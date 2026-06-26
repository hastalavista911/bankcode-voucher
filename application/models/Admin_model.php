<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    protected $table = 'admins';

    public function get_by_username($username)
    {
        return $this->db->get_where($this->table, [
            'username'  => $username,
            'is_active' => 1,
        ])->row();
    }

    public function verify_password($plain, $hashed)
    {
        return password_verify($plain, $hashed);
    }
}
