<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sdm extends CI_Controller {

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
        $this->load->helper('jwt');
        $this->load->library('jwt');
    }

    /**
     * GET /api/v1/sdm/org-tree
     * Ambil Struktur Organisasi (Org-Tree) & Vacancy Alert
     *
     * Authorization:
     *   - role_id=1 (Administrator) / role_id=3 (Eksekutif): optional ?polda_id= query
     *   - role_id=2 (Operator Polda): locked to JWT polda_id
     */
    public function org_tree_get()
    {
        // ── 1. AUTH ──
        $payload = get_jwt_payload($this);
        if (!$payload) {
            $this->output->set_status_header(401);
            echo json_encode(array(
                "message" => "Token tidak ditemukan",
                "status" => 401,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 2. ROLE & JURISDICTION ──
        $role_id = isset($payload['role_id']) ? (int) $payload['role_id'] : 0;
        $jwt_polda_id = isset($payload['polda_id']) ? (int) $payload['polda_id'] : 0;

        if ($role_id == 2) {
            // Operator Polda: locked to JWT polda_id
            $target_polda_id = $jwt_polda_id;
        } else if ($role_id == 1 || $role_id == 3) {
            // Admin / Eksekutif: optional ?polda_id= query param
            $query_polda = $this->input->get('polda_id');
            $target_polda_id = ($query_polda !== null && $query_polda !== '')
                ? (int) $query_polda
                : null; // null = all polda
        } else {
            $this->output->set_status_header(403);
            echo json_encode(array(
                "message" => "Akses ditolak",
                "status" => 403,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 3. SQL: LEFT JOIN with polda filter in ON clause ──
        if ($target_polda_id !== null) {
            $sql = "SELECT j.jabatan_id, j.nama_jabatan, j.formasi_ideal, j.parent_id, "
                 . "COUNT(p.personil_id) as jumlah_riil "
                 . "FROM tbl_jabatan j "
                 . "LEFT JOIN tbl_personil p ON j.jabatan_id = p.jabatan_id "
                 . "  AND p.polda_id = " . (int) $target_polda_id . " "
                 . "GROUP BY j.jabatan_id "
                 . "ORDER BY j.jabatan_id";
        } else {
            $sql = "SELECT j.jabatan_id, j.nama_jabatan, j.formasi_ideal, j.parent_id, "
                 . "COUNT(p.personil_id) as jumlah_riil "
                 . "FROM tbl_jabatan j "
                 . "LEFT JOIN tbl_personil p ON j.jabatan_id = p.jabatan_id "
                 . "GROUP BY j.jabatan_id "
                 . "ORDER BY j.jabatan_id";
        }

        $query = $this->db->query($sql);
        $rows = $query->result_array();

        if (empty($rows)) {
            $this->output->set_status_header(200);
            echo json_encode(array(
                "message" => "Data organisasi berhasil diambil",
                "status" => 200,
                "data" => array(
                    "struktur" => array()
                )
            ));
            return;
        }

        // ── 4. BUILD TREE ──
        $tree = $this->_build_tree($rows, null);

        // ── 5. SUCCESS ──
        $this->output->set_status_header(200);
        echo json_encode(array(
            "message" => "Data organisasi berhasil diambil",
            "status" => 200,
            "data" => array(
                "struktur" => $tree
            )
        ));
    }

    /**
     * GET /api/v1/sdm/personil
     * Tarik Daftar Personel (Desentralisasi)
     *
     * Authorization:
     *   - role_id=1 (Administrator) / role_id=3 (Eksekutif): optional ?polda_id=, ?polres_id=, ?search=, ?status=
     *   - role_id=2 (Operator Polda): locked to JWT polda_id
     */
    public function personil_get()
    {
        // ── 1. AUTH ──
        $payload = get_jwt_payload($this);
        if (!$payload) {
            $this->output->set_status_header(401);
            echo json_encode(array(
                "message" => "Token tidak ditemukan",
                "status" => 401,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 2. ROLE & JURISDICTION ──
        $role_id = isset($payload['role_id']) ? (int) $payload['role_id'] : 0;
        $jwt_polda_id = isset($payload['polda_id']) ? (int) $payload['polda_id'] : 0;

        if ($role_id == 2) {
            // Operator Polda: locked to JWT polda_id
            $this->db->where('p.polda_id', $jwt_polda_id);
        } else if ($role_id == 1 || $role_id == 3) {
            // Admin / Eksekutif: optional ?polda_id= query param
            $query_polda = $this->input->get('polda_id');
            if ($query_polda !== null && $query_polda !== '') {
                $this->db->where('p.polda_id', (int) $query_polda);
            }
        } else {
            $this->output->set_status_header(403);
            echo json_encode(array(
                "message" => "Akses ditolak",
                "status" => 403,
                "data" => new stdClass()
            ));
            return;
        }

        // ── 3. QUERY: SELECT + 4 LEFT JOINs ──
        $this->db->select("
            p.personil_id,
            p.nrp,
            p.nama_lengkap,
            p.status_aktif,
            p.polda_id,
            p.polres_id,
            pkt.nama_pangkat,
            jbt.nama_jabatan,
            prs.nama_polres
        ")
        ->from('tbl_personil p')
        ->join('tbl_pangkat pkt', 'p.pangkat_id = pkt.pangkat_id', 'left')
        ->join('tbl_jabatan jbt', 'p.jabatan_id = jbt.jabatan_id', 'left')
        ->join('tbl_polres prs', 'p.polres_id = prs.polres_id', 'left');

        // ── 4. DYNAMIC FILTERS (GET params) ──

        // ?search= (nama_lengkap OR nrp)
        $search = $this->input->get('search');
        if ($search !== null && $search !== '') {
            $this->db->group_start()
                ->like('p.nama_lengkap', $search)
                ->or_like('p.nrp', $search)
                ->group_end();
        }

        // ?polres_id= (int)
        $polres_id = $this->input->get('polres_id');
        if ($polres_id !== null && $polres_id !== '') {
            $this->db->where('p.polres_id', (int) $polres_id);
        }

        // ?status= (enum: Aktif, Mutasi, Pensiun)
        $status = $this->input->get('status');
        if ($status !== null && $status !== '') {
            $valid = array('Aktif', 'Mutasi', 'Pensiun');
            if (in_array($status, $valid)) {
                $this->db->where('p.status_aktif', $status);
            } else {
                $this->output->set_status_header(400);
                echo json_encode(array(
                    "message" => "Parameter status tidak valid. Gunakan: Aktif, Mutasi, atau Pensiun.",
                    "status" => 400,
                    "data" => new stdClass()
                ));
                return;
            }
        }

        // ── 5. ORDER & EXECUTE ──
        $this->db->order_by('p.nrp', 'ASC');
        $query = $this->db->get();
        $rows = $query->result_array();

        // ── 6. TYPE CAST relational IDs (Flutter compatibility) ──
        foreach ($rows as &$row) {
            $row['polres_id'] = $row['polres_id'] !== null ? (int) $row['polres_id'] : null;
            $row['polda_id'] = (int) $row['polda_id'];
        }
        unset($row);

        // ── 7. SUCCESS ──
        $this->output->set_status_header(200);
        echo json_encode(array(
            "message" => "Daftar personel berhasil dimuat.",
            "status" => 200,
            "data" => $rows
        ));
    }

    /**
     * Recursive tree builder from flat query result
     *
     * @param array $items  Flat result array from SQL
     * @param int|null $parent_id
     * @return array
     */
    private function _build_tree($items, $parent_id)
    {
        $branch = array();
        foreach ($items as $item) {
            $item_parent = ($item['parent_id'] !== null) ? (int) $item['parent_id'] : null;
            if ($item_parent !== $parent_id) {
                continue;
            }

            $jumlah_riil = (int) $item['jumlah_riil'];
            $formasi_ideal = (int) $item['formasi_ideal'];

            $node = array(
                "jabatan_id"       => (int) $item['jabatan_id'],
                "nama_jabatan"     => $item['nama_jabatan'],
                "formasi_ideal"    => $formasi_ideal,
                "jumlah_riil"      => $jumlah_riil,
                "is_vacancy_alert" => ($jumlah_riil < $formasi_ideal),
                "bawahan"          => $this->_build_tree($items, (int) $item['jabatan_id'])
            );

            $branch[] = $node;
        }
        return $branch;
    }
}
