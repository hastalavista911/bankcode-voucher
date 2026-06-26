<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    protected $table = 'products';

    public function get_all($filters = [])
    {
        if (!empty($filters['is_active']) && $filters['is_active'] !== 'all') {
            $this->db->where('is_active', (int) $filters['is_active']);
        }
        return $this->db->order_by('created_at', 'DESC')
                        ->get($this->table)
                        ->result();
    }

    public function get_paginated($limit, $offset, $filters = [])
    {
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', (int) $filters['is_active']);
        }
        return $this->db->order_by('created_at', 'DESC')
                        ->limit($limit, $offset)
                        ->get($this->table)
                        ->result();
    }

    public function count_all($filters = [])
    {
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', (int) $filters['is_active']);
        }
        return $this->db->count_all_results($this->table);
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function get_by_code($product_code)
    {
        return $this->db->get_where($this->table, ['product_code' => $product_code])->row();
    }

    public function get_active()
    {
        return $this->db->get_where($this->table, ['is_active' => 1])->result();
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
        $product = $this->get_by_id($id);
        if (!$product) return FALSE;
        return $this->db->where('id', $id)
                        ->update($this->table, ['is_active' => $product->is_active ? 0 : 1]);
    }
}
