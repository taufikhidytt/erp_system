<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Mendapatkan bahasa dari session, default ke 'indonesian'
        $lang = $this->session->userdata('site_lang') ?? 'indonesian';
        $this->lang->load('app_lang', $lang);
        $this->config->set_item('language', $lang);
        $this->lang->load('form_validation', $lang);

        belum_login();
        verificationAndStatus();
        $this->load->model('Profile_model', 'profile');
    }
    public function index()
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('nama', '' . $this->lang->line('usersName') . '', 'required');
            $this->form_validation->set_rules('email', 'email', 'required|valid_email|callback_check_email');

            if ($this->input->post('password')) {
                $this->form_validation->set_rules('passKon', '' . $this->lang->line('confirmPassword') . '', 'required|matches[password]');
            }

            if ($this->input->post('passKon')) {
                $this->form_validation->set_rules('password', 'password', 'required');
                $this->form_validation->set_rules('passKon', '' . $this->lang->line('confirmPassword') . '', 'matches[password]');
            }

            $this->form_validation->set_rules('no_hp', 'no hp', 'required|numeric|max_length[14]');

            if ($this->form_validation->run() == false) {
                $data['title'] = $this->lang->line('profile');
                $data['heading'] = $this->lang->line('profile');
                $data['data'] = $this->profile->getData()->row();
                $this->template->load('template', 'profile/index', $data);
            } else {
                $post = $this->input->post();
                $post['id'] = $this->encrypt->decode($post['id']);

                $checkEmail = $this->db->query("SELECT email FROM tb_users WHERE email = '$post[email]' AND deleted_at IS NULL");

                // jika mengubah email
                if ($checkEmail->num_rows() == 0) {
                    // fungsi kirim email di sini

                    date_default_timezone_set("Asia/Jakarta");
                    $token = base64_encode(random_bytes(32));

                    $user_token = [
                        'email' => $post['email'],
                        'token' => $token,
                        'created_at' => time(),
                    ];

                    $this->db->insert('tb_users_token', $user_token);

                    sendEmail($post['nama'], $post['email'], $token, 'verify');

                    $post['status_verified_email'] = 'false';
                }

                if ($_FILES) {
                    date_default_timezone_set("Asia/Jakarta");
                    $config['upload_path']      = './assets/upload/photo-profile/';
                    $config['allowed_types']    = 'jpeg|jpg|png';
                    $config['max_size']         = '520';
                    $config['file_name']        = 'photo-profile-' . date('Y-m-d,H-i-s');

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if ($_FILES['photo']['name']) {
                        if ($this->upload->do_upload('photo')) {
                            $post['photo'] = $this->upload->data('file_name');
                            $this->profile->update($post);
                            if ($this->db->affected_rows() > 0) {
                                $this->session->set_flashdata('success', $this->lang->line('messageSuccess'));
                                return redirect('profile');
                            } else {
                                $this->session->set_flashdata('error', $this->lang->line('messageError'));
                                redirect('profile');
                            }
                        } else {
                            $this->session->set_flashdata('error', $this->lang->line('failUploadPhoto'));
                            redirect('profile');
                        }
                    } else {
                        $post['photo'] = null;
                        $this->profile->update($post);
                        if ($this->db->affected_rows() > 0) {
                            $this->session->set_flashdata('success', $this->lang->line('messageSuccess'));
                            return redirect('profile');
                        } else {
                            $this->session->set_flashdata('error', $this->lang->line('messageError'));
                            redirect('profile');
                        }
                    }
                }
            }
        } catch (Exception $err) {
            return sendError('Error Server', $err->getMessage());
        }
    }

    function check_email()
    {
        $post = $this->input->post();
        $post['id'] = $this->encrypt->decode($post['id']);
        $query = $this->db->query("SELECT * FROM tb_users WHERE email = '$post[email]' AND id != '$post[id]'");
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_email', '{field} ' . $this->lang->line('uniqEmail') . '');
            return false;
        } else {
            return true;
        }
    }
}
