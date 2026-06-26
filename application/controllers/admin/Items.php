<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Items extends Admin_Controller
{
    protected $per_page = 20;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Item_model', 'Product_model']);
        $this->load->library('pagination');
    }

    public function index()
    {
        $filters = [
            'product_id' => $this->input->get('product_id', TRUE),
            'is_active'  => $this->input->get('is_active', TRUE),
        ];
        $total  = $this->Item_model->count_all($filters);
        $page   = (int) ($this->input->get('page') ?: 1);
        $offset = ($page - 1) * $this->per_page;

        $this->pagination->initialize(array(
            'base_url'             => site_url('admin/items'),
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

        $data['items']      = $this->Item_model->get_paginated($this->per_page, $offset, $filters);
        $data['pagination'] = $this->pagination->create_links();
        $data['products']   = $this->Product_model->get_all();
        $data['total']      = $total;
        $data['filters']    = $filters;
        $data['title']      = 'Manajemen Item / SKU';

        $this->view('admin/items/index', $data);
    }

    public function create()
    {
        $data['products'] = $this->Product_model->get_active();
        $data['title']    = 'Tambah Item';
        $this->view('admin/items/form', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('product_id', 'Produk',     'required|integer');
        $this->form_validation->set_rules('item_code',  'Kode Item',  'required|trim');
        $this->form_validation->set_rules('item_name',  'Nama Item',  'required|trim');

        if (!$this->form_validation->run()) {
            $data['products'] = $this->Product_model->get_active();
            $data['title']    = 'Tambah Item';
            return $this->view('admin/items/form', $data);
        }

        $this->Item_model->insert(array(
            'product_id' => (int) $this->input->post('product_id'),
            'item_code'  => strtoupper($this->input->post('item_code', TRUE)),
            'item_name'  => $this->input->post('item_name', TRUE),
        ));

        $this->session->set_flashdata('success', 'Item berhasil ditambahkan.');
        redirect('admin/items');
    }

    public function edit($id)
    {
        $item = $this->Item_model->get_by_id($id);
        if (!$item) show_404();

        $data['item']     = $item;
        $data['products'] = $this->Product_model->get_active();
        $data['title']    = 'Edit Item';
        $this->view('admin/items/form', $data);
    }

    public function update($id)
    {
        $item = $this->Item_model->get_by_id($id);
        if (!$item) show_404();

        $this->form_validation->set_rules('item_name', 'Nama Item', 'required|trim');

        if (!$this->form_validation->run()) {
            $data['item']     = $item;
            $data['products'] = $this->Product_model->get_active();
            $data['title']    = 'Edit Item';
            return $this->view('admin/items/form', $data);
        }

        $this->Item_model->update($id, array(
            'item_name' => $this->input->post('item_name', TRUE),
        ));

        $this->session->set_flashdata('success', 'Item berhasil diperbarui.');
        redirect('admin/items');
    }

    public function toggle($id)
    {
        $item = $this->Item_model->get_by_id($id);
        if (!$item) show_404();

        $this->Item_model->toggle_active($id);
        $this->session->set_flashdata('success', 'Status item berhasil diubah.');
        redirect('admin/items');
    }
}
