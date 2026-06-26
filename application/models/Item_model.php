<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_model extends CI_Model
{
    protected $table = 'items';

    public function get_paginated($limit, $offset, $filters = [])
    {
        $this->_apply_filters($filters);
        return $this->db->select('i.*, p.product_name, p.product_code')
                        ->from('items i')
                        ->join('products p', 'p.id = i.product_id')
                        ->order_by('p.product_name, i.item_name')
                        ->limit($limit, $offset)
                        ->get()->result();
    }

    public function count_all($filters = [])
    {
        $this->_apply_filters($filters);
        $this->db->from('items i');
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        return $this->db->select('i.*, p.product_name, p.product_code')
                        ->from('items i')
                        ->join('products p', 'p.id = i.product_id')
                        ->where('i.id', $id)
                        ->get()->row();
    }

    public function get_by_code($code)
    {
        return $this->db->select('i.*, p.product_name, p.product_code, p.provider')
                        ->from('items i')
                        ->join('products p', 'p.id = i.product_id')
                        ->where('i.item_code', $code)
                        ->get()->row();
    }

    // Semua item aktif untuk dropdown (dilengkapi info produk)
    public function get_active()
    {
        return $this->db->select('i.*, p.product_name, p.product_code')
                        ->from('items i')
                        ->join('products p', 'p.id = i.product_id')
                        ->where(['i.is_active' => 1, 'p.is_active' => 1])
                        ->order_by('p.product_name, i.item_name')
                        ->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function toggle_active($id)
    {
        $this->db->set('is_active', 'IF(is_active=1, 0, 1)', FALSE);
        return $this->db->where('id', $id)->update($this->table);
    }

    private function _apply_filters($filters)
    {
        if (!empty($filters['product_id'])) {
            $this->db->where('i.product_id', $filters['product_id']);
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('i.is_active', $filters['is_active']);
        }
    }
}
