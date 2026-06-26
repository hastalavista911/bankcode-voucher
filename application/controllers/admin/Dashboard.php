<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Voucher_model', 'Releaselog_model']);
    }

    public function index()
    {
        $data['stock_summary']   = $this->Voucher_model->get_stock_summary();
        $data['today_count']     = $this->Releaselog_model->get_today_count();
        $data['recent_releases'] = $this->Releaselog_model->get_recent(10);
        $data['title']           = 'Dashboard';

        $this->view('admin/dashboard/index', $data);
    }
}
