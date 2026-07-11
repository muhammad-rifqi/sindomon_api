<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->helper('uuid');
        $this->load->helper('string');
    }

    public function index()
    {
        // indexing
    }

     public function get()
    {

        //$headers = $this->input->request_headers();
        //$content_type = $headers['Authorization'] ?? null;

        $data = $this->db->query("select * from tbl_role")->result_array();
        echo json_encode(array("message"=> "success", "status" => 200 , "data" => $data));
    }

    public function post()
    {
        $rows = json_decode($this->input->raw_input_stream, true);
        $data = $this->db->query("insert into tbl_role(roles,created_at)values('".$rows['role']."','".date('Y-m-d : H:i:s')."')");
        if($data){
            echo json_encode(array("message"=> "success", "status" => 200 , "data" => $rows));
        }else{
            echo json_encode(array("message"=> "failed", "status" => 400 , "data" => []));
        }
    }

    public function put()
    {
        echo "PUT";
    }

    public function delete()
    {
        echo "DELETE";
    }



}