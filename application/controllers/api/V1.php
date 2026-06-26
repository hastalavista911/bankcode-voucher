<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class V1 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Apikey_model', 'Item_model', 'Voucher_model', 'Releaselog_model']);
        // Pastikan response selalu JSON
        header('Content-Type: application/json');
    }

    // POST /api/v1/release-voucher
    public function release_voucher()
    {
        if ($this->input->method() !== 'post') {
            return $this->_error(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
        }

        // 1. Validasi API Key dari header
        $plain_key = $this->input->get_request_header('X-API-Key', TRUE);
        if (!$plain_key) {
            return $this->_error(401, 'API key missing', 'INVALID_API_KEY');
        }

        $api_key = $this->Apikey_model->validate($plain_key);
        if (!$api_key) {
            return $this->_error(401, 'Invalid or inactive API key', 'INVALID_API_KEY');
        }

        // 2. Parse request body JSON
        $body = json_decode($this->input->raw_input_stream, TRUE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_error(400, 'Invalid JSON body', 'VALIDATION_ERROR');
        }

        $order_id  = isset($body['order_id'])  ? trim($body['order_id'])  : '';
        $item_code = isset($body['item_code']) ? trim($body['item_code']) : '';

        if (empty($order_id) || empty($item_code)) {
            return $this->_error(400, 'order_id and item_code are required', 'VALIDATION_ERROR');
        }

        // 3. Idempotency check — order_id sudah pernah diproses?
        $existing_log = $this->Releaselog_model->find_by_order_id($order_id);
        if ($existing_log) {
            $response_data = json_decode($existing_log->response_payload, TRUE);
            return $this->_success($response_data['data']);
        }

        // 4. Validasi item
        $item = $this->Item_model->get_by_code($item_code);
        if (!$item || !$item->is_active) {
            return $this->_error(404, 'Item not found or inactive', 'INVALID_PRODUCT');
        }

        // 5. Cek stok awal (non-locking, cepat)
        if ($this->Voucher_model->count_available_by_item($item->id) === 0) {
            return $this->_error(422, 'Insufficient stock', 'OUT_OF_STOCK');
        }

        // 6. Transaksi atomik: lock → release
        $this->db->trans_begin();
        try {
            $voucher = $this->Voucher_model->lock_one_available($item->id);

            if (!$voucher) {
                $this->db->trans_rollback();
                return $this->_error(422, 'Insufficient stock', 'OUT_OF_STOCK');
            }

            $this->Voucher_model->set_locked($voucher->id);

            $data = [
                'order_id'      => $order_id,
                'item_code'     => $item->item_code,
                'item_name'     => $item->item_name,
                'product_code'  => $item->product_code,
                'voucher_code'  => $voucher->voucher_code,
                'serial_number' => $voucher->serial_number,
                'expired_date'  => $voucher->expired_date,
            ];

            $request_payload  = json_encode(['order_id' => $order_id, 'item_code' => $item_code]);
            $response_payload = json_encode(['success' => TRUE, 'message' => 'Voucher released successfully', 'data' => $data]);

            $this->Releaselog_model->insert([
                'order_id'         => $order_id,
                'api_key_id'       => $api_key->id,
                'item_id'          => $item->id,
                'voucher_id'       => $voucher->id,
                'request_payload'  => $request_payload,
                'response_payload' => $response_payload,
            ]);

            $this->Voucher_model->set_released($voucher->id, $order_id);

            $this->db->trans_commit();

            return $this->_success($data);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'release_voucher error: ' . $e->getMessage());
            return $this->_error(500, 'Internal server error', 'SERVER_ERROR');
        }
    }

    private function _success($data)
    {
        http_response_code(200);
        echo json_encode([
            'success' => TRUE,
            'message' => 'Voucher released successfully',
            'data'    => $data,
        ]);
    }

    private function _error($http_code, $message, $error_code)
    {
        http_response_code($http_code);
        echo json_encode([
            'success'    => FALSE,
            'message'    => $message,
            'error_code' => $error_code,
        ]);
    }
}
