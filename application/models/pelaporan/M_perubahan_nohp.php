<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_perubahan_nohp extends CI_Model {

    var $table = 'laporan_perubahan_nohp';
    var $column_order = array(null, 'np_karyawan','nama_karyawan', 'nohp_lama', 'nohp_baru', 'np_atasan', 'nama_atasan', 'keterangan', 'status_approve'); // Kolom untuk pengurutan
    var $column_search = array('np_karyawan', 'nama_karyawan', 'nohp_lama', 'nohp_baru', 'np_atasan', 'nama_atasan', 'keterangan', 'status_approve'); // Kolom yang bisa dicari
    var $order = array('id' => 'asc'); // Default pengurutan

    public function __construct() {
        parent::__construct();
    }

    private function _get_datatables_query() {
        $this->db->from($this->table);
        $i = 0;

         // Tambahkan kondisi where untuk membatasi hasil berdasarkan np_karyawan
         $this->db->where('np_karyawan', $_SESSION['no_pokok']);
    
        if (isset($_POST['search']) && !empty($_POST['search']['value'])) { // Periksa apakah ada pencarian yang dilakukan
            foreach ($this->column_search as $item) { // Loop kolom yang bisa dicari
                if ($i === 0) { // Pencarian pertama
                    $this->db->group_start(); // (group_start) Membuka kurung untuk kondisi OR
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
    
                if (count($this->column_search) - 1 == $i) { // Pencarian terakhir
                    $this->db->group_end(); // (group_end) Menutup kurung untuk kondisi OR
                }
    
                $i++;
            }
        }
    
        if (isset($_POST['order'])) { // Pengurutan berdasarkan kolom yang dipilih
            $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        } else if (isset($this->order)) { // Default pengurutan
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    
        if (isset($_POST['length']) && $_POST['length'] != -1) { // Periksa apakah ada batasan jumlah data yang diminta
            $this->db->limit($_POST['length'], $_POST['start']);
        }
    }

    public function insert_data($data) {
        // Lakukan validasi data sebelum menyimpan ke database (jika diperlukan)
        
        // Lakukan penyisipan data ke dalam tabel 'laporan_perubahan_nohp'
        $this->db->insert('laporan_perubahan_nohp', $data);

        // Periksa apakah penyisipan berhasil
        if ($this->db->affected_rows() > 0) {
            return true; // Jika berhasil disimpan
        } else {
            return false; // Jika gagal disimpan
        }
    }
    
    public function delete_data($id) {
        // Menghapus data berdasarkan ID
        $this->db->where('id', $id);
        $this->db->delete('laporan_perubahan_nohp');

        // Mengembalikan TRUE jika data berhasil dihapus, dan FALSE jika tidak
        return $this->db->affected_rows() > 0;
    }
    
    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) // Limit data
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
}
