<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ref_jenis_dokumen_contribution extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        try {
            $get = $this->db->get('ref_jenis_dokumen_contribution')->result();
            $this->response([
                'status' => true,
                'message' => 'Success',
                'data' => $get
            ], MY_Controller::HTTP_OK);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Not found',
                'data' => []
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
