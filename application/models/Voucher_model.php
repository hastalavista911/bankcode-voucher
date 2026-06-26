<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher_model extends CI_Model
{
    protected $table = 'vouchers';

    public function get_paginated($limit, $offset, $filters = [])
    {
        $this->_apply_filters($filters);
        return $this->db->select('v.*, i.item_name, i.item_code, p.product_name, p.product_code')
                        ->from('vouchers v')
                        ->join('items i',    'i.id = v.item_id')
                        ->join('products p', 'p.id = i.product_id')
                        ->order_by('ISNULL(v.expired_date)', 'ASC')
                        ->order_by('v.expired_date', 'ASC')
                        ->limit($limit, $offset)
                        ->get()->result();
    }

    public function count_all($filters = [])
    {
        $this->_apply_filters($filters);
        $this->db->from('vouchers v');
        $this->db->join('items i', 'i.id = v.item_id');
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        return $this->db->select('v.*, i.item_name, i.item_code, p.product_name, p.product_code')
                        ->from('vouchers v')
                        ->join('items i',    'i.id = v.item_id')
                        ->join('products p', 'p.id = i.product_id')
                        ->where('v.id', $id)
                        ->get()->row();
    }

    // Ambil 1 voucher available untuk item — dipakai dalam transaksi atomik
    public function lock_one_available($item_id)
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE status = 'available' AND item_id = ?
                LIMIT 1 FOR UPDATE";
        return $this->db->query($sql, [$item_id])->row();
    }

    public function set_locked($id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, ['status' => 'locked']);
    }

    public function set_released($id, $order_id)
    {
        return $this->db->where('id', $id)
                        ->update($this->table, [
                            'status'      => 'released',
                            'order_id'    => $order_id,
                            'released_at' => date('Y-m-d H:i:s'),
                        ]);
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    public function get_existing_codes(array $codes)
    {
        if (empty($codes)) return [];
        $rows = $this->db->select('voucher_code')
                         ->where_in('voucher_code', $codes)
                         ->get($this->table)
                         ->result_array();
        return array_column($rows, 'voucher_code');
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    // Statistik stok per item untuk dashboard
    public function get_stock_summary()
    {
        $sql = "SELECT i.item_name, i.item_code, p.product_name, p.product_code,
                       SUM(v.status = 'available') AS available,
                       SUM(v.status = 'locked')    AS locked,
                       SUM(v.status = 'released')  AS released
                FROM items i
                JOIN products p ON p.id = i.product_id
                LEFT JOIN vouchers v ON v.item_id = i.id
                WHERE i.is_active = 1 AND p.is_active = 1
                GROUP BY i.id
                ORDER BY p.product_name, i.item_name";
        return $this->db->query($sql)->result();
    }

    public function count_available_by_item($item_id)
    {
        return $this->db->where(['item_id' => $item_id, 'status' => 'available'])
                        ->count_all_results($this->table);
    }

    public function get_for_export($filters = [])
    {
        $this->_apply_filters($filters);
        return $this->db->select('v.*, i.item_name, i.item_code, p.product_name, p.product_code')
                        ->from('vouchers v')
                        ->join('items i',    'i.id = v.item_id')
                        ->join('products p', 'p.id = i.product_id')
                        ->order_by('v.id', 'ASC')
                        ->get()->result();
    }

    private function _apply_filters($filters)
    {
        if (!empty($filters['product_id'])) {
            $this->db->where('i.product_id', $filters['product_id']);
        }
        if (!empty($filters['item_id'])) {
            $this->db->where('v.item_id', $filters['item_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('v.status', $filters['status']);
        }
    }
}
