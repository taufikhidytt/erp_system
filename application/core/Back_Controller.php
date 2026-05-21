<?php defined('BASEPATH') or exit('No direct script access allowed');

class Back_Controller extends MX_Controller
{
    public $access = [];
    public $version=[
        'inline-editor' => '1.1'
    ];
    public function __construct()
    {
        parent::__construct();
         $this->load->vars(['version' => $this->version]);

        //check cache validation
        $this->refreshCache();
        $this->checkAccessUser();
    }

    /**
     * On browser back button hit
     */
    private function refreshCache()
    {
        // any valid date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // always modified right now
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        // HTTP/1.1
        header("Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0");
        // HTTP/1.0
        header("Pragma: no-cache");
    }

    public function sendSuccess($result = null, $message)
    {
        $response = [
            'success' => TRUE,
            'result' => $result,
            'message' => $message,
        ];

        echo json_encode($response);
    }

    public function sendWarning($message)
    {
        $response = [
            'success' => FALSE,
            'result' => 'warning',
            'message' => $message,
        ];

        echo json_encode($response);
    }

    public function sendError($error, $errorMessages = [])
    {
        $response = [
            'success' => FALSE,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['result'] = $errorMessages;
        }

        echo json_encode($response);
    }

    public function _remap($method, $params = array())
    {
        if (!method_exists($this, $method)) {
            $this->output->set_status_header('404');
            $this->load->view('notFound/index');
            return;
        }

        return call_user_func_array(array($this, $method), $params);
    }

    /**
     * cara pakai 
     * di view gunakan $access contoh $access['view']
     * di controller gunakan $this->access contoh $this->access['view']
     * di helper/library gunakan
     *      $ci = &get_instance();
     *      $ci->load->get_var('access')
     * di model gunakan $this->load->get_var('access')
     */
    private function checkAccessUser(){
        $this->access = get_access();
        $this->load->vars(['access' => $this->access]);
        $current_url = $this->uri->segment(1);
        $p1          = $this->uri->segment(2);
        if(!in_array($current_url,['dashboard','api'])){

            // pengecekan akses view
            if (empty($this->access) || (isset($this->access['view']) && $this->access['view'] === false)) {
                $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
                redirect('dashboard');
            }
            // pengecekan akses add/insert
            else if ($p1 == 'add' && (empty($this->access) || (isset($this->access['insert']) && $this->access['insert'] === false))) {
                $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
                redirect('dashboard');
            }
            //pengeceka akses update ketika submit
            else if ($p1 == 'detail' && (empty($this->access) || (isset($this->access['update']) && $this->access['update'] === false))) {
                $id_post = $this->input->post('id');
                if($id_post){
                    $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
                    redirect($current_url);
                }
            }
        }
    }
}
