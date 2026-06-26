<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Releaselog_model extends CI_Model
{
    protected $table = 'release_logs';

    // Cek idempotency — kembalikan log jika order_id sudah pernah diproses
    public function find_by_order_id($order_id)
    {
        return $this->db->get_where($this->table, ['order_id' => $order_id])->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_today_count()
    {
        return $this->db->where('DATE(created_at)', date('Y-m-d'))
                        ->count_all_results($this->table);
    }

    public function get_recent($limit = 10)
    {
        $this->db->select('rl.*, i.item_name, i.item_code, p.product_code, ak.partner_name, v.voucher_code');
        $this->db->from('release_logs rl');
        $this->db->join('items i',     'i.id  = rl.item_id');
        $this->db->join('products p',  'p.id  = i.product_id');
        $this->db->join('api_keys ak', 'ak.id = rl.api_key_id');
        $this->db->join('vouchers v',  'v.id  = rl.voucher_id');
        return $this->db->order_by('rl.created_at', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->result();
    }
}
