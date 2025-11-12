<?php
class LanguageLoader
{
    public function initialize()
    {
        $CI = &get_instance();
        $CI->load->helper('language');

        // Mendapatkan bahasa dari session
        $lang = $CI->session->userdata('site_lang') ?? 'indonesian';
        $CI->lang->load('app', $lang);
    }
}
