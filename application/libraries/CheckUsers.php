<?php

class checkUsers
{
    protected $ci;

    function __construct()
    {
        $this->ci = &get_instance();
    }

    function users_login()
    {
        $id_users = $this->ci->session->userdata('id');
        $data = $this->ci->db->query("SELECT ERP_USER_ID, ERP_GROUP_ID, ERP_USER_NAME, ADMIN_FLAG, TEMPLATE_FLAG, PROTECT_FLAG, VIEW_FLAG, PRINT_FLAG, START_DATE, END_DATE, TITLE, ERP_USER_DESC, EMAIL_ID, DIVISI_ID, NOTE, CREATED_BY, CREATED_DATE, LAST_UPDATE_BY, LAST_UPDATE_DATE FROM erp_user WHERE erp_user_id = $id_users")->row();
        return $data;
    }
}
