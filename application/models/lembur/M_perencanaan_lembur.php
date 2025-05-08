<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_perencanaan_lembur extends CI_Model {

    var $table = 'ess_perencanaan_lembur';
    var $detail = 'ess_perencanaan_lembur_detail';
    var $kategori = 'mst_kategori_lembur';
    var $ess_sto = 'ess_sto';
    var $mst_karyawan = 'mst_karyawan';
    var $perencanaan_evidence = 'ess_perencanaan_lembur_evidence';

    public function __construct(){
        parent::__construct();
        $this->table_schema = $this->db->database;
    }

    function get_perencanaan($where, $select = null){
        if($select) $this->db->select($select);
        $this->db->where($where);
        return $this->db->get($this->table);
    }

    function get_perencanaan_join_sto($where, $select = null){
        $this->db->select("rencana.*, sto.object_name");
        $this->db->where($where);
        $this->db->join("{$this->ess_sto} sto", 'sto.object_abbreviation = rencana.kode_unit', 'LEFT');
        return $this->db->get("{$this->table} rencana");
    }

    function insert_perencanaan($data){
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    function update_perencanaan($data, $where){
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update($this->table, $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    function get_detail($where, $select = null){
        if($select) $this->db->select($select);
        $this->db->where($where);
        return $this->db->get($this->detail);
    }

    function get_detail_join_karyawan($where, $select = null){
        $this->db->select("detail.*, kry.nama, kry.no_pokok");
        $this->db->where($where);
        $this->db->join("{$this->mst_karyawan} kry", "FIND_IN_SET(kry.no_pokok, detail.list_np)", 'LEFT');
        return $this->db->get("{$this->detail} detail");
    }

    function insert_detail($data){
        $this->db->trans_start();
        $this->db->insert($this->detail, $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    function insert_multiple_detail($data){
        $this->db->trans_start();
        $this->db->insert_batch($this->detail, $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    function update_detail($data, $where){
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update($this->detail, $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    function hard_delete_detail($where){
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->delete($this->detail);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    function update_nama_jenis_lembur($id = null){
        if($id){
            return $this->db->query("UPDATE {$this->detail} detail
            INNER JOIN {$this->kategori} ref ON ref.id = detail.mst_kategori_lembur_id
            SET detail.jenis_lembur = ref.kategori_lembur
            WHERE detail.id = '{$id}'");
        } else{
            return $this->db->query("UPDATE {$this->detail} detail
            INNER JOIN {$this->kategori} ref ON ref.id = detail.mst_kategori_lembur_id
            SET detail.jenis_lembur = ref.kategori_lembur
            WHERE detail.mst_kategori_lembur_id IS NOT NULL AND detail.jenis_lembur IS NULL");
        }
    }

    function filter_periode(){
        $this->db->select('tanggal_mulai, tanggal_selesai');
        $this->db->where('deleted_at is null', null, false);
        $this->db->group_by('tanggal_mulai, tanggal_selesai');
        $this->db->order_by('tanggal_mulai, tanggal_selesai');
        return $this->db->get($this->table)->result();
    }

    // Fungsi untuk menyimpan evidence ke dalam tabel ess_perencanaan_lembur_evidence
    function insert_batch_perencanaan_evidence($data) {
        $this->db->trans_start();
        $this->db->insert_batch($this->perencanaan_evidence, $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

	public function hapus_perencanaan_lembur($id) {
        // Start transaction
        $this->db->trans_start();
        
        // Soft delete in ess_perencanaan_lembur table
        $this->db->where('id', $id);
        $this->db->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
        
        // Retrieve filenames from ess_perencanaan_lembur_evidence before deletion
        $this->db->select('evidence_file'); // Use the correct column name
        $this->db->where('perencanaan_lembur_id', $id);
        $query = $this->db->get($this->detail);
        $files = $query->result();
        
        // Soft delete in ess_perencanaan_lembur_evidence table
        $this->db->where('perencanaan_lembur_id', $id);
        $this->db->update($this->detail, ['deleted_at' => date('Y-m-d H:i:s')]);
        
        // Complete transaction
        $this->db->trans_complete();
        
        // If transaction was successful, delete files from the directory
        if ($this->db->trans_status() === true) {
            foreach ($files as $file) {
                $file_path = FCPATH . 'uploads/perencanaan_lembur/' . $file->evidence_file; // Use the correct column name
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
