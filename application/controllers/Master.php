<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

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
        $this->load->library('jwt');
    }

    public function polres_post()
    {
        $payload = get_jwt_payload($this);

        if ($payload === null || !isset($payload['role_id']) || $payload['role_id'] !== 1) {
            http_response_code(403);
            echo json_encode([
                'status' => 403,
                'message' => 'Akses ditolak. Anda tidak memiliki otoritas Super Admin.',
                'data' => (object)[]
            ]);
            return;
        }

        $input = json_decode($this->input->raw_input_stream, true);

        if (empty($input['nama_polres']) || empty($input['polda_id'])) {
            http_response_code(422);
            echo json_encode([
                'status' => 422,
                'message' => 'Validasi gagal. Field nama_polres dan polda_id wajib diisi.',
                'data' => (object)[]
            ]);
            return;
        }

        $nama_polres = trim($input['nama_polres']);
        $polda_id = (int) $input['polda_id'];

        $polda_exists = $this->db->get_where('tbl_polda', ['id' => $polda_id])->num_rows();

        if ($polda_exists === 0) {
            http_response_code(422);
            echo json_encode([
                'status' => 422,
                'message' => 'Validasi gagal. Induk Polda tidak ditemukan.',
                'data' => (object)[]
            ]);
            return;
        }

        $this->db->insert('tbl_polres', [
            'polda_id' => $polda_id,
            'nama_polres' => $nama_polres
        ]);

        $inserted_id = $this->db->insert_id();

        http_response_code(201);
        echo json_encode([
            'status' => 201,
            'message' => 'Data wilayah polres berhasil ditambahkan.',
            'data' => [
                'polres_id' => (int) $inserted_id
            ]
        ]);
    }
}
