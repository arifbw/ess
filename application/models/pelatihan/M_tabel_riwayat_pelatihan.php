<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_riwayat_pelatihan extends CI_Model {

    var $table = 'ess_sppd';
    var $column_order = array(null, 'perihal', 'tgl_berangkat', 'tgl_pulang', 'tgl_selesai', 'no_surat'); // Kolom yang bisa diurutkan
    var $column_search = array('np_karyawan', 'nama', 'perihal', 'tgl_berangkat'); // Kolom yang bisa dicari
    var $order = array("tgl_berangkat" => "desc", "np_karyawan" => "asc");

    public function __construct(){
        parent::__construct();
    }

    private function _get_datatables_query($npk='', $filter='all')
    { 
        $this->db->from($this->table);
        $this->db->where('YEAR(tgl_berangkat) >=', 2024);
        $this->db->where('catatan', 'Perjalanan Dinas Tugas Belajar');
        
        // Menambahkan filter untuk np_karyawan yang bernilai 1234
        $this->db->where('np_karyawan', $npk);

        // Melakukan pengecekan jika ada tambahan filter berdasarkan bulan atau kondisi lainnya
        // if ($month != 0) {
        //     $this->db->where("DATE_FORMAT(tgl_berangkat,'%Y-%m')", $month);
        // }

        // Proses pencarian tambahan (jika ada)
        $i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if (isset($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
                $i++;
            }
        }

        // Menentukan urutan hasil query
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    private function _get_datatables_query2($npk='', $filter='all')
    { 
        $this->db->from($this->table);
        $this->db->where("YEAR(tgl_berangkat) < 2024");
		$this->db->not_like('perihal', 'rapat');
        
        // Menambahkan filter untuk np_karyawan yang bernilai 1234
        $this->db->where('np_karyawan', $npk);

        // Melakukan pengecekan jika ada tambahan filter berdasarkan bulan atau kondisi lainnya
        // if ($month != 0) {
        //     $this->db->where("DATE_FORMAT(tgl_berangkat,'%Y-%m')", $month);
        // }

        // Proses pencarian tambahan (jika ada)
        $i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if (isset($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
                $i++;
            }
        }

        // Menentukan urutan hasil query
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }


    function get_datatables($month=null,$filter='all')
    {
        $this->_get_datatables_query($month,$filter);
        
        if($_POST['length'] != -1)
        // $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get()->result();

        $this->_get_datatables_query2($month,$filter);
        
        if($_POST['length'] != -1)
        // $this->db->limit($_POST['length'], $_POST['start']);
        $query2 = $this->db->get()->result();

        $combined_results = array_merge($query, $query2);
		
		if(isset($_POST['order'])) // here order processing
		{
			usort($combined_results, function ($a, $b) {
				if ($_POST['order']['0']['column'] == 3){
					if ($_POST['order']['0']['dir'] == 'asc'){
						return strcasecmp($a->nama, $b->nama);
					} else {
						return strcasecmp($b->nama, $a->nama);
					}
				} else if ($_POST['order']['0']['column'] == 4){
					if ($_POST['order']['0']['dir'] == 'asc'){
						return strcasecmp($a->perihal, $b->perihal);
					} else {
						return strcasecmp($b->perihal, $a->perihal);
					}
				}  else if ($_POST['order']['0']['column'] == 5){
					if (strtotime($a->tgl_berangkat) == strtotime($b->tgl_berangkat)) {
						return 0;
					}
					if ($_POST['order']['0']['dir'] == 'asc'){
						return (strtotime($a->tgl_berangkat) > strtotime($b->tgl_berangkat)) ? -1 : 1;
					} else {
						return (strtotime($a->tgl_berangkat) < strtotime($b->tgl_berangkat)) ? -1 : 1;
					}
				}
			});
		} 

		return $combined_results;
    }

    function count_filtered($month=null,$filter='all')
    {
        $this->_get_datatables_query($month,$filter);
        $query = $this->db->get();

        $this->_get_datatables_query2($month,$filter);
        $query2 = $this->db->get();

        $combined_results = array_merge($query->result_array(), $query2->result_array());
		return count($combined_results);
    }

    public function count_all($month=null, $filter='all') {
        $this->db->from($this->table);
        // Hapus filter np_karyawan = '7776' jika tidak diperlukan
        // Tambahkan logika filter bulan jika diperlukan, misalnya:
        // if ($month != null && $month != 0) {
        //     $this->db->where("DATE_FORMAT(tgl_berangkat,'%Y-%m')", $month);
        // }
        $query = $this->db->get();
        return $query->num_rows();
    }
    

}

