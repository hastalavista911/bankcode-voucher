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

        $this->pagination->initialize(array(
            'base_url'             => site_url('admin/vouchers'),
            'total_rows'           => $total,
            'per_page'             => $this->per_page,
            'use_page_numbers'     => TRUE,
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

        $this->Voucher_model->insert(array(
            'item_id'       => (int) $this->input->post('item_id'),
            'voucher_code'  => $this->input->post('voucher_code', TRUE),
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

        $item_id = (int) $this->input->post('item_id');
        if (!$item_id) {
            $this->session->set_flashdata('error', 'Pilih item terlebih dahulu.');
            return redirect('admin/vouchers/create');
        }

        $handle = fopen($file['tmp_name'], 'r');
        fgetcsv($handle); // skip header

        $batch = array();
        $now   = date('Y-m-d H:i:s');

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (empty(trim($row[0]))) continue;
            $batch[] = array(
                'item_id'       => $item_id,
                'voucher_code'  => trim($row[0]),
                'serial_number' => isset($row[1]) ? trim($row[1]) ?: NULL : NULL,
                'price'         => isset($row[2]) ? (float) trim($row[2]) : 0,
                'expired_date'  => isset($row[3]) ? trim($row[3]) ?: NULL : NULL,
                'status'        => 'available',
                'created_at'    => $now,
                'updated_at'    => $now,
            );
        }
        fclose($handle);

        if (empty($batch)) {
            $this->session->set_flashdata('error', 'Tidak ada data valid di CSV.');
            return redirect('admin/vouchers/create');
        }

        $this->Voucher_model->insert_batch($batch);
        $this->session->set_flashdata('success', count($batch) . ' voucher berhasil diimport.');
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
