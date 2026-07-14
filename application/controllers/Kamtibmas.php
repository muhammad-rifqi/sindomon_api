<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kamtibmas extends CI_Controller {

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

        $this->load->helper('url');
        $this->load->library('session');
        $this->load->helper('uuid');
        $this->load->helper('string');
        $this->load->library('jwt');
    }

    /**
     * POST /api/v1/kamtibmas/laporan
     * Input Laporan SITKAMTIBMAS [Multipart]
     *
     * Authorization: Operator Polda (role_id=3)
     * Content-Type: multipart/form-data
     *
     * Form fields:
     *   - deskripsi_kejadian (string, required)
     *   - level_kritis (enum: Aman|Waspada|Darurat, required)
     *   - foto_tkp (file: jpg/png, max 500KB, required)
     */
    public function laporan()
    {
        // ── 1. AUTH: JWT Bearer Token ──
        $headers = $this->input->request_headers();
        if (!isset($headers['Authorization'])) {
            $this->output->set_status_header(401);
            echo json_encode(array(
                "message" => "Token tidak ditemukan",
                "status" => 401,
                "data" => new stdClass()
            ));
            return;
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);
        $payload = $this->jwt->decode($token);

        if (!$payload) {
            $this->output->set_status_header(401);
            echo json_encode(array(
                "message" => "Token tidak valid atau kadaluarsa",
                "status" => 401,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 2. ROLE CHECK: Operator Polda only (role_id == 3) ──
        $role_id = isset($payload['role_id']) ? (int) $payload['role_id'] : 0;
        if ($role_id !== 3) {
            $this->output->set_status_header(403);
            echo json_encode(array(
                "message" => "Akses ditolak. Hanya Operator Polda yang dapat mengirim laporan",
                "status" => 403,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 3. VALIDATE FORM FIELDS ──
        $deskripsi_kejadian = $this->input->post('deskripsi_kejadian');
        $level_kritis = $this->input->post('level_kritis');

        if ($deskripsi_kejadian === null || $deskripsi_kejadian === '') {
            $this->output->set_status_header(400);
            echo json_encode(array(
                "message" => "deskripsi_kejadian wajib diisi",
                "status" => 400,
                "data" => new stdClass()
            ));
            return;
        }

        $allowed_levels = array('Aman', 'Waspada', 'Darurat');
        if (!in_array($level_kritis, $allowed_levels, true)) {
            $this->output->set_status_header(400);
            echo json_encode(array(
                "message" => "level_kritis harus salah satu dari: Aman, Waspada, Darurat",
                "status" => 400,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 4. FILE UPLOAD: foto_tkp ──
        $upload_dir = './uploads/sitkamtibmas/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $config = array();
        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 500; // KB
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('foto_tkp')) {
            $this->output->set_status_header(400);
            echo json_encode(array(
                "message" => "Gagal upload foto: " . $this->upload->display_errors('', ''),
                "status" => 400,
                "data" => new stdClass()
            ));
            return;
        }

        $upload_data = $this->upload->data();
        $foto_tkp_url = 'uploads/sitkamtibmas/' . $upload_data['file_name'];

        // ── 5. AUTO-INJECT polda_id FROM JWT ──
        $polda_id = isset($payload['polda_id']) ? (int) $payload['polda_id'] : null;
        if ($polda_id === null) {
            // Rollback: delete uploaded file
            @unlink($upload_data['full_path']);
            $this->output->set_status_header(500);
            echo json_encode(array(
                "message" => "polda_id tidak ditemukan dalam token",
                "status" => 500,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 6. GENERATE UUID ──
        $sitkamtibmas_id = generate_uuid4();

        // ── 7. INSERT INTO tbl_sitkamtibmas ──
        $sql = "INSERT INTO tbl_sitkamtibmas (sitkamtibmas_id, polda_id, deskripsi_kejadian, level_kritis, foto_tkp_url, created_at) "
             . "VALUES ("
             . "'" . $this->db->escape_str($sitkamtibmas_id) . "', "
             . "'" . $this->db->escape_str($polda_id) . "', "
             . "'" . $this->db->escape_str($deskripsi_kejadian) . "', "
             . "'" . $this->db->escape_str($level_kritis) . "', "
             . "'" . $this->db->escape_str($foto_tkp_url) . "', "
             . "NOW()"
             . ")";

        $insert = $this->db->query($sql);

        if (!$insert) {
            // Rollback: delete uploaded file
            @unlink($upload_data['full_path']);
            $this->output->set_status_header(500);
            echo json_encode(array(
                "message" => "Gagal menyimpan laporan",
                "status" => 500,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 8. WEBSOCKET TRIGGER: Darurat → Command Center Alert ──
        if ($level_kritis === 'Darurat') {
            // TODO: Fire WebSocket payload to Message Broker
            // to trigger Red Blinking Node on Command Center Map
        }

        // ── 9. SUCCESS RESPONSE: HTTP 201 Created ──
        $this->output->set_status_header(201);
        echo json_encode(array(
            "message" => "Laporan SITKAMTIBMAS berhasil dikirim",
            "status" => 201,
            "data" => array(
                "sitkamtibmas_id" => $sitkamtibmas_id
            )
        ));
    }
}
