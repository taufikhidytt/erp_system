<?php

function sudah_login()
{
    $ci = &get_instance();
    $session = $ci->session->userdata('id');
    if ($session) {
        $ci->session->set_flashdata('warning', 'anda sudah login, jika ingin keluar silahkan cari tombol keluar pada profil!');
        redirect('dashboard');
    }
}

function belum_login()
{
    $ci = &get_instance();
    $session = $ci->session->userdata('id');
    if (!$session) {
        $ci->session->set_flashdata('toastWarning', 'anda belum login!! silahkan login telebih dahulu dengan username dan password anda!');
        redirect('auth');
    }
}

function rules()
{
    $ci = &get_instance();
    $session = $ci->session->userdata('group');
    $data = $ci->db->query("SELECT erp_menu_name, erp_menu.parent_id
        FROM erp_menu 
        JOIN erp_group_menu
        ON erp_group_menu.erp_menu_id = erp_menu.erp_menu_id
        WHERE active_flag = 'Y' 
        AND erp_group_id = $session
        AND erp_group_menu.view_flag = 'Y'
    ");

    $hasil = [];
    foreach ($data->result() as $dt) {
        if ($ci->uri->segment(1) != strtolower($dt->erp_menu_name)) {
            $hasil[] = false;
        } else {
            $hasil[] = true;
        }
    }

    if (count(array_filter($hasil)) == 0) {
        $ci->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
        redirect('dashboard');
    }
}

function sendSuccess($result = null, $message)
{
    $response = [
        'status' => 'success',
        'success' => TRUE,
        'result' => $result,
        'message' => $message,
    ];

    echo json_encode($response);
}

function sendWarning($message)
{
    $response = [
        'status' => 'warning',
        'success' => FALSE,
        'result' => 'warning',
        'message' => $message,
    ];

    echo json_encode($response);
}

function sendError($error, $errorMessages = [])
{
    $response = [
        'status' => 'error',
        'success' => FALSE,
        'message' => $error,
    ];

    if (!empty($errorMessages)) {
        $response['result'] = $errorMessages;
    }

    echo json_encode($response);
}

function debuging($data = null)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die();
}

function setVariableMysql()
{
    $ci = &get_instance();

    $setParamA = $ci->db->query("SELECT PROGRAM_CODE1, ERP_LOOKUP_VALUE_ID FROM erp_lookup_value WHERE PROGRAM_CODE1 IS NOT NULL AND PROGRAM_CODE1 <> '' AND PROGRAM_CODE1 NOT LIKE '% %' AND PROGRAM_CODE1 NOT LIKE '%-%'");

    foreach ($setParamA->result() as $spa) {
        $data[] = $ci->db->query("SELECT Erp_Lookup_Value_Id INTO @{$spa->PROGRAM_CODE1} FROM Erp_Lookup_Value WHERE Erp_Lookup_Value_Id = {$spa->ERP_LOOKUP_VALUE_ID}");
    }

    $setParamB = $ci->db->query("SELECT PROGRAM_CODE, ERP_LOOKUP_SET_ID FROM erp_lookup_set WHERE PROGRAM_CODE IS NOT NULL AND PROGRAM_CODE <> '' AND PROGRAM_CODE NOT LIKE '% %' AND PROGRAM_CODE NOT LIKE '%-%'");

    foreach ($setParamB->result() as $spb) {
        $ci->db->query("SELECT Erp_Lookup_Set_Id INTO @{$spb->PROGRAM_CODE} FROM Erp_Lookup_Set WHERE Erp_Lookup_Set_Id = {$spb->ERP_LOOKUP_SET_ID}");
    }

    $setParamC = $ci->db->query("SELECT COA_ID, PROGRAM_ACCOUNT FROM coa_setup");

    foreach ($setParamC->result() as $spc) {
        $ci->db->query("SELECT Coa_Id INTO @{$spc->PROGRAM_ACCOUNT} FROM Coa_Setup WHERE Program_Account = '{$spc->PROGRAM_ACCOUNT}'");
    }

    $ci->db->query("SET @SINKRON = 0;");
    $ci->db->query("SET TX_ISOLATION = 'READ-COMMITTED'");
    $ci->db->query("SET COMPLETION_TYPE = 0;");
    $ci->db->query("SET AUTOCOMMIT = 1;");
    $ci->db->query("SET @NO_MINUS_FLAG = FALSE;");
    $ci->db->query("SET @TAHUNAN = 0;");
    $ci->db->query("SET @STATUS_OK = 1;");
    $ci->db->query("SET @ARCHIVE = 0;");
    $ci->db->query("SET @STOK = 0.00;");
    $ci->db->query("SET @REPROSES_HPP = 1;");
    $ci->db->query("SET @PEMBULATAN_PPN = 0;");
    $ci->db->query("SET @BARIS = 5;");
}

function connNewDatabase()
{
    $ci = &get_instance();

    $dbConfig = [
        'dsn'      => '',
        'hostname' => $ci->session->userdata('hostname'),
        'username' => 'Fatra',
        'password' => '73fangfang',
        'database' => $ci->session->userdata('db'),
        'dbdriver' => 'mysqli',
        'port'     => $ci->session->userdata('port'),
        'dbprefix' => '',
        'pconnect' => FALSE,
        'db_debug' => (ENVIRONMENT !== 'production'),
        'cache_on' => FALSE,
        'cachedir' => '',
        'char_set' => 'utf8',
        'dbcollat' => 'utf8_general_ci',
        'swap_pre' => '',
        'encrypt'  => FALSE,
        'compress' => FALSE,
        'stricton' => FALSE,
        'failover' => array(),
        'save_queries' => TRUE
    ];

    $ci->db = $ci->load->database($dbConfig, TRUE);
}

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/=', '-_.'), '.');
}

function base64url_decode($data)
{
    return base64_decode(strtr($data, '-_.', '+/='));
}
