<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Polda extends CI_Controller {

    public function __construct() {
        parent::__construct();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With");
        header("Access-Control-Allow-Credentials: false");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        $this->config->load('jwt');
        $this->load->helper('jwt');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->helper('uuid');
        $this->load->helper('string');
    }

    public function get()
    {
        $headers = $this->input->request_headers();
        if(isset($headers['Authorization'])){
            $authorization = $headers['Authorization'];
            $payload = jwt_decode($authorization);
             if ($payload === false) {
                echo json_encode("Unauthorize");
             } else {
                $data = $this->db->query("select * from tbl_polda")->result_array();
                $rows = array();
                for($i=0;$i<count($data);$i++){
                    $rows[] = array(
                        "id" => $data[0]['id'],
                        "nama_polda" => $data[0]['nama_polda'],
                        "latitude" => $data[0]['latitude'],
                        "longitude" => $data[0]['longitude'],
                        "created_at" => $data[0]['created_at'],
                        "polres" => $this->db->query("select * from tbl_polres where polda_id = '".$data[0]['id']."'")->result_array(),
                    );
                }
                echo json_encode(array("message"=> "success", "status" => 200 , "data" => $rows));
             }
        }else{
            echo json_encode("Unauthorize");
        }   
    }
}