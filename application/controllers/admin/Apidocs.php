<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apidocs extends Admin_Controller
{
    public function index()
    {
        $data['title']    = 'API Documentation';
        $data['base_url'] = base_url();
        $this->view('admin/apidocs/index', $data);
    }
}
