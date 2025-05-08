<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_jadwal_operasional extends CI_Model {

	var $table = 'ess_pemesanan_kendaraan';
	var $column_order = array(); //set column field database for datatable orderable	
	var $column_search = array('np_karyawan','nama','tujuan','nomor_pemesanan'); //set column field database for datatable orderable	
	var $order = array('np_karyawan' => 'asc','tanggal_peminjaman' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query($tampil_tanggal, $unit)
	{
        $this->db->select('a.id, a.nomor_pemesanan, a.nama_mst_kendaraan, a.nama_mst_driver, a.nama_mst_bbm, a.jumlah_liter_bbm, a.total_harga_bbm, a.biaya_lainnya, a.biaya_tol, a.biaya_parkir, a.biaya_total, a.nama_unit_pemesan, a.nama_pic, a.no_hp_pic, a.tanggal_berangkat, a.tanggal_awal, a.tanggal_akhir, a.jam, a.lokasi_jemput, a.nama_kota_asal, GROUP_CONCAT(b.keterangan_tujuan SEPARATOR "<br>") as tujuannya, a.is_inap, a.is_pp')
            ->where(['a.tanggal_berangkat'=>$tampil_tanggal, 'a.status_persetujuan_admin'=>1, 'a.is_canceled_by_admin!='=>'1']);
            //->where('a.id_mst_bbm IS NOT NULL',null,false);
        if($unit!='semua'){
            $this->db->where('a.unit_pemroses', $unit);
        }
        
		$this->db->join('ess_pemesanan_kendaraan_kota b','a.id=b.id_pemesanan_kendaraan','LEFT');
        $this->db->group_by('a.id');
        $this->db->order_by('a.id_mst_kendaraan, a.jam');
		$this->db->from($this->table.' a');
        
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($tampil_tanggal, $unit)
	{
		$this->_get_datatables_query($tampil_tanggal, $unit);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($tampil_tanggal, $unit)
	{
		$this->_get_datatables_query($tampil_tanggal, $unit);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($tampil_tanggal, $unit) {
        $this->_get_datatables_query($tampil_tanggal, $unit);
        return $this->db->count_all_results();
	}
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */