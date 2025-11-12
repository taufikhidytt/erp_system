<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('user_agent');
    }

    public function switch($language = 'english')
    {
        // Validasi bahasa
        $available_languages = ['english', 'indonesian'];
        if (in_array($language, $available_languages)) {
            // Menyimpan bahasa ke session
            $this->session->set_userdata('site_lang', $language);
        }
        // Redirect kembali ke halaman sebelumnya
        redirect($this->agent->referrer());
    }
}
