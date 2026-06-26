<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apikey_model extends CI_Model
{
    protected $table = 'api_keys';

    // Validasi API key: input plain-text, bandingkan dengan SHA256 di DB
    public function validate($plain_key)
    {
        $hashed = hash('sha256', $plain_key);
        return $this->db->get_where($this->table, [
            'api_key'   => $hashed,
            'is_active' => 1,
        ])->row();
    }

    public function get_all()
    {
        return $this->db->order_by('created_at', 'DESC')
                        ->get($this->table)
                        ->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function insert($partner_name, $plain_key)
    {
        $this->db->insert($this->table, [
            'partner_name' => $partner_name,
            'api_key'      => hash('sha256', $plain_key),
        ]);
        return $this->db->insert_id();
    }

    public function toggle_active($id)
    {
        $key = $this->get_by_id($id);
        if (!$key) return FALSE;
        return $this->db->where('id', $id)
                        ->update($this->table, ['is_active' => $key->is_active ? 0 : 1]);
    }
}
