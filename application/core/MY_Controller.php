<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Base controller untuk semua halaman admin — cek session di satu tempat
class Admin_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('admin_logged_in')) {
            redirect('admin/login');
        }
    }

    protected function view($view, $data = [])
    {
        $data['admin_name'] = $this->session->userdata('admin_name');
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view($view, $data);
        $this->load->view('admin/layout/footer', $data);
    }
}
