<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->load->model('server/Server_model', 'server');
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
                //dimatikan dulu karena sudah dipindahkan ke submit_signin untuk kebutuhan API
                // $this->signin();
                redirect('auth');
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
                            $this->auth->insertLogSignin($data);

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
            'group',
            'nama',
            'admin_flag',
            'template_flag',
            'protect_flag',
            'view_flag',
            'print_flag',
            'name_ub',
            'logo',
            'logged_in',
            'setup',
            'id_access_users',
            'site_lang',
        ];
        $this->session->unset_userdata($session);
        $this->session->set_flashdata('toastSuccess', 'You have successfully logged out!');
        redirect('auth');
    }

    public function check_username()
    {
        $username = $this->input->post('username');
        $query = $this->server->get_user_servers($username);
        if ($query->num_rows() > 0) {
            $data = [];
            foreach ($query->result() as $row) {
                $db_encrypt = base64url_encode($this->encrypt->encode($row->DB_NAME));
                $data[$db_encrypt] = [
                    'name' => strtoupper($row->DB_NAME),
                    'default' => $row->PRIMARY_FLAG == 'Y' ? true : false
                ];
            }
            return sendSuccess($data,'Username found');
        } else {
            return sendError('Username not found');
        }
    }

    public function submit_signin(){
        try {
            $username   = $this->input->post('username');
            $db_encrypt = $this->input->post('server');
            $db_name    = $this->encrypt->decode(base64url_decode($db_encrypt));
            $password   = $this->input->post('password');

            $userServer = $this->server->getDataServer($username, $db_name);
            if(!$userServer || $userServer->num_rows() == 0){
                sendError('username or password wrong!');die();
            }

            $dataServer = $userServer->row();

            $session = [
                'hostname'  => $dataServer->HOSTNAME,
                'port'      => $dataServer->PORT,
                'db'        => $dataServer->DB_NAME,
            ];
            $this->session->set_userdata($session);
            connNewDatabase();
            $query = $this->auth->getData($username);

            if(!$query || $query->num_rows() == 0){
                sendError('username or password wrong!');die();
            }

            $data = $query->row();
            if ('*' . strtoupper(sha1(sha1($password, true))) != $data->PASSWORD) {
                sendError('username or password wrong!');die();
            }
            if ($data->END_DATE && $data->END_DATE <= date('Y-m-d H:i:s')) {
                sendError('account not active, please contact administrator!');die();
            }

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
            $this->auth->insertLogSignin($data);

            sendSuccess([
                'redirect' => site_url('dashboard')
            ],'congratulations, you have successfully logged in!, please wait while we redirect you...');die();
        } catch (mysqli_sql_exception $err) {
            return sendError('Error Server', $err->getMessage());
        }
    }
}
