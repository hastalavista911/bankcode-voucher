<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vouchers extends Admin_Controller
{
    protected $per_page = 20;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Voucher_model', 'Item_model', 'Product_model']);
        $this->load->library('pagination');
    }

    public function index()
    {
        $filters = array(
            'product_id' => $this->input->get('product_id', TRUE),
            'item_id'    => $this->input->get('item_id', TRUE),
            'status'     => $this->input->get('status', TRUE),
        );
        $total  = $this->Voucher_model->count_all($filters);
        $page   = (int) ($this->input->get('page') ?: 1);
        $offset = ($page - 1) * $this->per_page;

        $filter_qs = http_build_query(array_filter($filters));
        $this->pagination->initialize(array(
            'base_url'             => site_url('admin/vouchers') . ($filter_qs ? '?' . $filter_qs : ''),
            'total_rows'           => $total,
            'per_page'             => $this->per_page,
            'use_page_numbers'     => TRUE,
            'page_query_string'    => TRUE,
            'query_string_segment' => 'page',
            'full_tag_open'        => '<ul class="pagination mb-0">',
            'full_tag_close'       => '</ul>',
            'num_tag_open'         => '<li class="page-item">',
            'num_tag_close'        => '</li>',
            'cur_tag_open'         => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close'        => '</a></li>',
            'next_tag_open'        => '<li class="page-item">',
            'next_tag_close'       => '</li>',
            'prev_tag_open'        => '<li class="page-item">',
            'prev_tag_close'       => '</li>',
            'attributes'           => array('class' => 'page-link'),
        ));

        $data['vouchers']   = $this->Voucher_model->get_paginated($this->per_page, $offset, $filters);
        $data['pagination'] = $this->pagination->create_links();
        $data['products']   = $this->Product_model->get_all();
        $data['items']      = $this->Item_model->get_active();
        $data['total']      = $total;
        $data['offset']     = $offset;
        $data['filters']    = $filters;
        $data['title']      = 'Manajemen Voucher';

        $this->view('admin/vouchers/index', $data);
    }

    public function create()
    {
        $data['items']          = $this->Item_model->get_active();
        $data['generated_code'] = generate_voucher_code('RF');
        $data['title']          = 'Tambah Voucher';
        $this->view('admin/vouchers/form', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('item_id',      'Item/SKU',     'required|integer');
        $this->form_validation->set_rules('voucher_code', 'Kode Voucher', 'required|trim');
        $this->form_validation->set_rules('price',        'Harga',        'required|numeric|greater_than_equal_to[0]');

        if (!$this->form_validation->run()) {
            $data['items']          = $this->Item_model->get_active();
            $data['generated_code'] = generate_voucher_code('RF');
            $data['title']          = 'Tambah Voucher';
            return $this->view('admin/vouchers/form', $data);
        }

        $voucher_code = $this->input->post('voucher_code', TRUE);

        $existing = $this->Voucher_model->get_existing_codes([$voucher_code]);
        if (!empty($existing)) {
            $this->session->set_flashdata('error', 'Kode voucher <strong>' . esc($voucher_code) . '</strong> sudah ada di database.');
            $data['items']          = $this->Item_model->get_active();
            $data['generated_code'] = generate_voucher_code('RF');
            $data['title']          = 'Tambah Voucher';
            return $this->view('admin/vouchers/form', $data);
        }

        $this->Voucher_model->insert(array(
            'item_id'       => (int) $this->input->post('item_id'),
            'voucher_code'  => $voucher_code,
            'serial_number' => $this->input->post('serial_number', TRUE) ?: NULL,
            'price'         => (float) $this->input->post('price'),
            'expired_date'  => $this->input->post('expired_date', TRUE) ?: NULL,
            'status'        => 'available',
        ));

        $this->session->set_flashdata('success', 'Voucher berhasil ditambahkan.');
        redirect('admin/vouchers');
    }

    public function import()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/vouchers/create');
        }

        $file = $_FILES['csv_file'] ?? NULL;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'File CSV tidak valid.');
            return redirect('admin/vouchers/create');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            $this->session->set_flashdata('error', 'File harus berekstensi .csv');
            return redirect('admin/vouchers/create');
        }

        // Build map item_code => item_id (sekali query)
        $item_map = array();
        foreach ($this->Item_model->get_active() as $it) {
            $item_map[strtoupper($it->item_code)] = $it->id;
        }

        $handle = fopen($file['tmp_name'], 'r');
        fgetcsv($handle); // skip header

        $candidates      = array();
        $seen_in_file    = array();
        $dup_in_file     = array();
        $invalid_rows    = array();
        $now             = date('Y-m-d H:i:s');
        $line            = 1; // baris header sudah dilewati

        while (($row = fgetcsv($handle)) !== FALSE) {
            $line++;
            $item_code    = isset($row[0]) ? strtoupper(trim($row[0])) : '';
            $voucher_code = isset($row[1]) ? trim($row[1]) : '';

            if ($item_code === '' || $voucher_code === '') {
                $invalid_rows[] = 'Baris ' . $line . ': item_code atau voucher_code kosong';
                continue;
            }

            if (!isset($item_map[$item_code])) {
                $invalid_rows[] = 'Baris ' . $line . ': item_code <strong>' . esc($item_code) . '</strong> tidak dikenali';
                continue;
            }

            // Duplikat dalam file yang sama
            if (isset($seen_in_file[$voucher_code])) {
                $dup_in_file[] = $voucher_code;
                continue;
            }
            $seen_in_file[$voucher_code] = true;

            $candidates[] = array(
                'item_id'       => $item_map[$item_code],
                'voucher_code'  => $voucher_code,
                'serial_number' => isset($row[2]) ? trim($row[2]) ?: NULL : NULL,
                'price'         => isset($row[3]) ? (float) trim($row[3]) : 0,
                'expired_date'  => isset($row[4]) ? trim($row[4]) ?: NULL : NULL,
                'status'        => 'available',
                'created_at'    => $now,
                'updated_at'    => $now,
            );
        }
        fclose($handle);

        // Tolak seluruh file jika ada baris tidak valid
        if (!empty($invalid_rows)) {
            $msg = 'CSV tidak valid, import dibatalkan. Perbaiki CSV terlebih dahulu:<br><ul>';
            foreach ($invalid_rows as $err) {
                $msg .= '<li>' . $err . '</li>';
            }
            $msg .= '</ul>';
            $this->session->set_flashdata('error', $msg);
            return redirect('admin/vouchers/create');
        }

        // Tolak seluruh file jika ada duplikat dalam file itu sendiri
        if (!empty($dup_in_file)) {
            $msg = 'CSV tidak valid, import dibatalkan. Terdapat kode voucher duplikat di dalam file:<br><ul>';
            foreach (array_unique($dup_in_file) as $code) {
                $msg .= '<li><code>' . esc($code) . '</code></li>';
            }
            $msg .= '</ul>';
            $this->session->set_flashdata('error', $msg);
            return redirect('admin/vouchers/create');
        }

        if (empty($candidates)) {
            $this->session->set_flashdata('error', 'Tidak ada data di CSV.');
            return redirect('admin/vouchers/create');
        }

        // Tolak seluruh file jika ada duplikat terhadap DB
        $codes_to_check = array_column($candidates, 'voucher_code');
        $existing       = $this->Voucher_model->get_existing_codes($codes_to_check);

        if (!empty($existing)) {
            $msg = 'CSV tidak valid, import dibatalkan. Kode voucher berikut sudah ada di database:<br><ul>';
            foreach ($existing as $code) {
                $msg .= '<li><code>' . esc($code) . '</code></li>';
            }
            $msg .= '</ul>Perbaiki CSV lalu coba lagi.';
            $this->session->set_flashdata('error', $msg);
            return redirect('admin/vouchers/create');
        }

        $this->Voucher_model->insert_batch($candidates);

        $this->session->set_flashdata('success', count($candidates) . ' voucher berhasil diimport.');
        redirect('admin/vouchers');
    }

    public function export()
    {
        $filters = array(
            'product_id' => $this->input->get('product_id', TRUE),
            'item_id'    => $this->input->get('item_id', TRUE),
            'status'     => $this->input->get('status', TRUE),
        );

        $vouchers = $this->Voucher_model->get_for_export($filters);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="vouchers_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Product Code', 'Item Code', 'Item Name', 'Voucher Code', 'Serial Number', 'Price', 'Expired Date', 'Status', 'Order ID', 'Released At', 'Created At']);

        foreach ($vouchers as $v) {
            fputcsv($out, [
                $v->id, $v->product_code, $v->item_code, $v->item_name,
                $v->voucher_code, $v->serial_number, $v->price,
                $v->expired_date, $v->status, $v->order_id, $v->released_at, $v->created_at,
            ]);
        }
        fclose($out);
        exit;
    }

    public function template()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="template_import_voucher.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['item_code', 'voucher_code', 'serial_number', 'price', 'expired_date']);
        fputcsv($out, ['RF25000', 'RF-XXXX-1234-YYYY', 'SN-001', '25000', '2026-12-31']);
        fputcsv($out, ['RF25000', 'RF-AAAA-5678-BBBB', '',        '25000', '2026-12-31']);
        fputcsv($out, ['RF50000', 'RF-CCCC-9012-DDDD', 'SN-003',  '50000', '2026-12-31']);
        fclose($out);
        exit;
    }

    public function delete($id)
    {
        $voucher = $this->Voucher_model->get_by_id($id);
        if (!$voucher) show_404();

        if ($voucher->status !== 'available') {
            $this->session->set_flashdata('error', 'Voucher tidak dapat dihapus karena status bukan available.');
            return redirect('admin/vouchers');
        }

        $this->Voucher_model->delete($id);
        $this->session->set_flashdata('success', 'Voucher berhasil dihapus.');
        redirect('admin/vouchers');
    }
}
