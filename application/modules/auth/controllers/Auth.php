<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->load->library('user_agent');
    }

    public function index()
    {
        try {
            sudah_login();
            $session = [
                'hostname',
                'port',
                'db'
            ];
            $this->session->unset_userdata($session);

            $this->form_validation->set_rules('username', 'username', 'trim|required');
            $this->form_validation->set_rules('password', 'password', 'required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Sign In';
                $data['heading'] = 'Sign In';
                $this->load->view('signin', $data);
            } else {
                $this->signin();
            }
        } catch (Exception $err) {
            return sendError('Error Server', $err->getMessage());
        }
    }

    private function signin()
    {
        try {
            date_default_timezone_set('Asia/Jakarta');

            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $userServer = $this->auth->getDataServer($username);
            if ($userServer->num_rows() > 0) {
                $dataServer = $userServer->row();

                $session = [
                    'hostname'  => $dataServer->HOSTNAME,
                    'port'  => $dataServer->PORT,
                    'db'  => $dataServer->DB_NAME,
                ];
                $this->session->set_userdata($session);
                connNewDatabase();
                $query = $this->auth->getData($username);
                if ($query->num_rows() > 0) {
                    $data = $query->row();
                    if ($data->END_DATE >= date('Y-m-d H:i:s') or $data->END_DATE == NULL) {
                        if ('*' . strtoupper(sha1(sha1($password, true))) == $data->PASSWORD) {
                            $infoUB = $this->db->query("
                                    SELECT DISTINCT NAME, LOGO_FILENAME AS LOGO
                                    FROM setup a 
                                    JOIN address b ON a.address_id = b.address_id
                                ")->row();

                            $sessionUser = [
                                'id'            => $data->ERP_USER_ID,
                                'group'         => $data->ERP_GROUP_ID,
                                'nama'          => $data->ERP_USER_NAME,
                                'admin_flag'    => $data->ADMIN_FLAG,
                                'template_flag' => $data->TEMPLATE_FLAG,
                                'protect_flag'  => $data->PROTECT_FLAG,
                                'view_flag'     => $data->VIEW_FLAG,
                                'print_flag'    => $data->PRINT_FLAG,
                                'name_ub'       => $infoUB->NAME,
                                'logo'          => $infoUB->LOGO,
                                'logged_in'     => TRUE
                            ];

                            $this->session->set_userdata($sessionUser);

                            $this->session->set_flashdata('toastSuccess', 'congratulations, you have successfully logged in!');
                            redirect('dashboard');
                        } else {
                            $this->session->set_flashdata('toastWarning', 'username or password wrong!');
                            redirect('auth');
                        }
                    } else {
                        $this->session->set_flashdata('toastWarning', 'account not active, please contact administrator!');
                        redirect('auth');
                    }
                } else {
                    $this->session->set_flashdata('toastWarning', 'username or password wrong!');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('toastWarning', 'username or password wrong!');
                redirect('auth');
            }
        } catch (Exception $err) {
            return sendError('Error Server', $err->getMessage());
        }
    }

    public function logout()
    {
        $session = [
            'hostname',
            'port',
            'db',
            'id',
            'id_access_users',
            'site_lang',
        ];
        $this->session->unset_userdata($session);
        $this->session->set_flashdata('toastSuccess', 'You have successfully logged out!');
        redirect('auth');
    }
}
