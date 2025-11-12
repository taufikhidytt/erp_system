<?php defined('BASEPATH') or exit('No direct script access allowed');

class Back_Controller extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        //check cache validation
        $this->refreshCache();
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
}
