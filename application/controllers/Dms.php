<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dms extends CI_Controller {

    public function __construct() {
        parent::__construct();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
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
     * POST /api/v1/dms/surat
     * Kirim Surat Dinas Baru [Multipart]
     *
     * Authorization: Super Admin (role_id=2) or Operator Polda (role_id=3)
     * Content-Type: multipart/form-data
     *
     * Form fields:
     *   - nomor_surat        (string, required)
     *   - judul_surat         (string, required)
     *   - penerima_polda_id   (integer, optional — null/0 = Mabes Polri)
     *   - file_dokumen        (file, required — .pdf or .docx only)
     */
    public function surat()
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

        // ── 2. ROLE CHECK: Super Admin (2) or Operator Polda (3) ──
        $role_id = isset($payload['role_id']) ? (int) $payload['role_id'] : 0;
        if ($role_id != 2 && $role_id != 3) {
            $this->output->set_status_header(403);
            echo json_encode(array(
                "message" => "Akses ditolak. Hanya Super Admin atau Operator Polda yang dapat mengirim surat",
                "status" => 403,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 3. VALIDATE FORM FIELDS ──
        $nomor_surat = $this->input->post('nomor_surat');
        $judul_surat = $this->input->post('judul_surat');

        if ($nomor_surat === null || $nomor_surat === '') {
            $this->output->set_status_header(400);
            echo json_encode(array(
                "message" => "nomor_surat wajib diisi",
                "status" => 400,
                "data" => new stdClass()
            ));
            return;
        }

        if ($judul_surat === null || $judul_surat === '') {
            $this->output->set_status_header(400);
            echo json_encode(array(
                "message" => "judul_surat wajib diisi",
                "status" => 400,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 4. HANDLE penerima_polda_id (Optional — null/0 = Mabes Polri) ──
        $raw_penerima = $this->input->post('penerima_polda_id');
        $penerima_polda_id = null;

        if ($raw_penerima !== null && $raw_penerima !== '' && (int) $raw_penerima > 0) {
            $penerima_polda_id = (int) $raw_penerima;
        }

        // ── 5. FILE UPLOAD: file_dokumen (pdf/docx only) ──
        $upload_dir = dirname(FCPATH) . '/uploads/dms/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $config = array();
        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|docx';
        $config['max_size']      = 2048; // KB ~ 2MB
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_dokumen')) {
            $upload_error = $this->upload->display_errors('', '');
            $is_filetype_error = (
                stripos($upload_error, 'filetype') !== false ||
                stripos($upload_error, 'allowed') !== false
            );

            if ($is_filetype_error) {
                $this->output->set_status_header(415);
                echo json_encode(array(
                    "message" => "Format file tidak didukung. Harap unggah PDF atau Docx.",
                    "status" => 415,
                    "data" => new stdClass()
                ));
            } else {
                $this->output->set_status_header(400);
                echo json_encode(array(
                    "message" => "Gagal upload dokumen: " . $upload_error,
                    "status" => 400,
                    "data" => new stdClass()
                ));
            }
            return;
        }

        $upload_data = $this->upload->data();
        $file_pdf_url = 'uploads/dms/' . $upload_data['file_name'];

        // ── 6. AUTO-INJECT pengirim_polda_id FROM JWT ──
        $pengirim_polda_id = isset($payload['polda_id']) ? (int) $payload['polda_id'] : null;

        // ── 7. GENERATE UUID ──
        $surat_id = generate_uuid4();

        // ── 8. INSERT INTO tbl_dms_surat ──
        $sql = "INSERT INTO tbl_dms_surat (surat_id, pengirim_polda_id, penerima_polda_id, judul_surat, nomor_surat, file_pdf_url, status_tracking, created_at) "
             . "VALUES ("
             . "'" . $this->db->escape_str($surat_id) . "', "
             . ($pengirim_polda_id !== null ? "'" . $this->db->escape_str($pengirim_polda_id) . "'" : "NULL") . ", "
             . ($penerima_polda_id !== null ? "'" . $this->db->escape_str($penerima_polda_id) . "'" : "NULL") . ", "
             . "'" . $this->db->escape_str($judul_surat) . "', "
             . "'" . $this->db->escape_str($nomor_surat) . "', "
             . "'" . $this->db->escape_str($file_pdf_url) . "', "
             . "'Terkirim', "
             . "NOW()"
             . ")";

        $insert = $this->db->query($sql);

        if (!$insert) {
            // Rollback: delete uploaded file
            @unlink($upload_data['full_path']);
            $this->output->set_status_header(500);
            echo json_encode(array(
                "message" => "Gagal menyimpan surat dinas",
                "status" => 500,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 9. SUCCESS RESPONSE: HTTP 201 Created ──
        $this->output->set_status_header(201);
        echo json_encode(array(
            "message" => "Surat dinas berhasil dikirim",
            "status" => 201,
            "data" => array(
                "surat_id" => $surat_id
            )
        ));
    }

    /**
     * GET /api/v1/dms/surat?tipe=inbox|outbox
     * Tarik Kotak Masuk (Inbox) & Kotak Keluar (Outbox)
     *
     * Authorization: Eksekutif (role_id=2) or Operator Polda (role_id=3)
     * Query params:
     *   - tipe  (string, required) — "inbox" or "outbox"
     */
    public function inbox_outbox()
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

        // ── 2. ROLE CHECK: Eksekutif (2) or Operator Polda (3) ──
        $role_id = isset($payload['role_id']) ? (int) $payload['role_id'] : 0;
        if ($role_id != 2 && $role_id != 3) {
            $this->output->set_status_header(403);
            echo json_encode(array(
                "message" => "Akses ditolak. Hanya Eksekutif atau Operator Polda yang dapat mengakses surat",
                "status" => 403,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 3. VALIDATE ?tipe= PARAM ──
        $tipe = $this->input->get('tipe');
        if ($tipe !== 'inbox' && $tipe !== 'outbox') {
            $this->output->set_status_header(400);
            echo json_encode(array(
                "message" => "Parameter tipe harus 'inbox' atau 'outbox'",
                "status" => 400,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 4. EXTRACT polda_id FROM JWT (Mabes = null/0) ──
        $polda_id = isset($payload['polda_id']) ? (int) $payload['polda_id'] : 0;
        $is_mabes = ($polda_id === 0);

        // ── 5. BUILD QUERY ──
        if ($tipe === 'inbox') {
            // Inbox: surat yang ditujukan ke user ini
            $select_cols = "s.surat_id, s.nomor_surat, s.judul_surat, "
                         . "COALESCE(p.nama_polda, 'Mabes Polri') AS pengirim, "
                         . "s.status_tracking, s.created_at";
            $join_table = "tbl_polda p ON s.pengirim_polda_id = p.id";

            if ($is_mabes) {
                $where = "s.penerima_polda_id IS NULL OR s.penerima_polda_id = 0";
            } else {
                $where = "s.penerima_polda_id = '" . $this->db->escape_str($polda_id) . "'";
            }
        } else {
            // Outbox: surat yang dikirim oleh user ini
            $select_cols = "s.surat_id, s.nomor_surat, s.judul_surat, "
                         . "COALESCE(p.nama_polda, 'Mabes Polri') AS penerima, "
                         . "s.status_tracking, s.created_at";
            $join_table = "tbl_polda p ON s.penerima_polda_id = p.id";

            if ($is_mabes) {
                $where = "s.pengirim_polda_id IS NULL OR s.pengirim_polda_id = 0";
            } else {
                $where = "s.pengirim_polda_id = '" . $this->db->escape_str($polda_id) . "'";
            }
        }

        $sql = "SELECT {$select_cols} "
             . "FROM tbl_dms_surat s "
             . "LEFT JOIN {$join_table} "
             . "WHERE ({$where}) "
             . "ORDER BY s.created_at DESC";

        $query = $this->db->query($sql);
        $results = $query->result_array();

        // ── 6. SUCCESS RESPONSE: HTTP 200 ──
        $this->output->set_status_header(200);
        echo json_encode(array(
            "message" => "Data surat berhasil diambil",
            "status" => 200,
            "data" => $results
        ));
    }
}
