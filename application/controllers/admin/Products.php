<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller
{
    protected $per_page = 15;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->library('pagination');
    }

    public function index()
    {
        $filters = ['is_active' => $this->input->get('is_active', TRUE)];
        $total   = $this->Product_model->count_all($filters);
        $page    = (int) ($this->input->get('page') ?: 1);
        $offset  = ($page - 1) * $this->per_page;

        $this->pagination->initialize([
            'base_url'    => site_url('admin/products'),
            'total_rows'  => $total,
            'per_page'    => $this->per_page,
            'uri_segment' => 0,
            'use_page_numbers' => TRUE,
            'query_string_segment' => 'page',
            'full_tag_open'  => '<ul class="pagination mb-0">',
            'full_tag_close' => '</ul>',
            'first_tag_open'  => '<li class="page-item">',
            'first_tag_close' => '</li>',
            'last_tag_open'   => '<li class="page-item">',
            'last_tag_close'  => '</li>',
            'next_tag_open'   => '<li class="page-item">',
            'next_tag_close'  => '</li>',
            'prev_tag_open'   => '<li class="page-item">',
            'prev_tag_close'  => '</li>',
            'num_tag_open'    => '<li class="page-item">',
            'num_tag_close'   => '</li>',
            'cur_tag_open'    => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close'   => '</a></li>',
            'first_link'  => '&laquo;',
            'last_link'   => '&raquo;',
            'next_link'   => '&rsaquo;',
            'prev_link'   => '&lsaquo;',
            'attributes'  => ['class' => 'page-link'],
        ]);

        $data['products']   = $this->Product_model->get_paginated($this->per_page, $offset, $filters);
        $data['pagination'] = $this->pagination->create_links();
        $data['total']      = $total;
        $data['filters']    = $filters;
        $data['title']      = 'Manajemen Produk';

        $this->view('admin/products/index', $data);
    }

    public function create()
    {
        $data['title']   = 'Tambah Produk';
        $data['product'] = NULL;
        $this->view('admin/products/form', $data);
    }

    public function store()
    {
        $this->_set_rules();

        if (!$this->form_validation->run()) {
            $data['title']   = 'Tambah Produk';
            $data['product'] = NULL;
            return $this->view('admin/products/form', $data);
        }

        $this->Product_model->insert([
            'product_code' => strtoupper($this->input->post('product_code', TRUE)),
            'product_name' => $this->input->post('product_name', TRUE),
            'provider'     => $this->input->post('provider', TRUE),
            'is_active'    => 1,
        ]);

        $this->session->set_flashdata('success', 'Produk berhasil ditambahkan.');
        redirect('admin/products');
    }

    public function edit($id)
    {
        $product = $this->Product_model->get_by_id($id);
        if (!$product) show_404();

        $data['title']   = 'Edit Produk';
        $data['product'] = $product;
        $this->view('admin/products/form', $data);
    }

    public function update($id)
    {
        $product = $this->Product_model->get_by_id($id);
        if (!$product) show_404();

        $this->_set_rules($id);

        if (!$this->form_validation->run()) {
            $data['title']   = 'Edit Produk';
            $data['product'] = $product;
            return $this->view('admin/products/form', $data);
        }

        $this->Product_model->update($id, [
            'product_code' => strtoupper($this->input->post('product_code', TRUE)),
            'product_name' => $this->input->post('product_name', TRUE),
            'provider'     => $this->input->post('provider', TRUE),
        ]);

        $this->session->set_flashdata('success', 'Produk berhasil diupdate.');
        redirect('admin/products');
    }

    public function toggle($id)
    {
        $this->Product_model->toggle_active($id);
        $this->session->set_flashdata('success', 'Status produk berhasil diubah.');
        redirect('admin/products');
    }

    private function _set_rules($exclude_id = NULL)
    {
        $unique_rule = 'is_unique[products.product_code]';
        if ($exclude_id) {
            $unique_rule = "is_unique[products.product_code.id.{$exclude_id}]";
        }

        $this->form_validation->set_rules('product_code', 'Kode Produk', "required|trim|alpha_dash|{$unique_rule}");
        $this->form_validation->set_rules('product_name', 'Nama Produk', 'required|trim');
        $this->form_validation->set_rules('provider',     'Provider',    'required|trim');
    }
}
