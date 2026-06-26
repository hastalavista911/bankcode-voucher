<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
    }

    public function index()
    {
        redirect('admin/login');
    }

    public function login()
    {
        if ($this->session->userdata('admin_logged_in')) {
            redirect('admin/dashboard');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run()) {
                $username = $this->input->post('username', TRUE);
                $password = $this->input->post('password');

                $admin = $this->Admin_model->get_by_username($username);

                if ($admin && $this->Admin_model->verify_password($password, $admin->password)) {
                    $this->session->set_userdata([
                        'admin_logged_in' => TRUE,
                        'admin_id'        => $admin->id,
                        'admin_username'  => $admin->username,
                        'admin_name'      => $admin->full_name,
                    ]);
                    redirect('admin/dashboard');
                } else {
                    $this->session->set_flashdata('error', 'Username atau password salah.');
                }
            }
        }

        $this->load->view('admin/auth/login');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('admin/login');
    }
}
