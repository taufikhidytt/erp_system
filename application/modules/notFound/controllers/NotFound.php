<?php
defined('BASEPATH') or exit('No direct script access allowed');

class NotFound extends MX_Controller
{
    public function index()
    {
        $data['title'] = 'Not Found';
        $data['Heading'] = 'Not Found';
        $this->load->view('notFound/index', $data);
    }
}
