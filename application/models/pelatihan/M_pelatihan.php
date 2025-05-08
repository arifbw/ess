<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class M_pelatihan extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
    
	function check_double_cuti($np_karyawan,$start_date,$end_date)
	{
		
		$query = $this->db->query("SELECT * 
									FROM 
										ess_cuti 
									WHERE 
										np_karyawan='$np_karyawan' AND 
										(
											(status_1!=2 AND status_1!=3) OR 
											(status_2!=2 AND status_2!=3)
										) AND
										(
											(start_date<='$start_date' AND end_date>='$start_date') OR 
											(start_date<='$end_date' AND end_date>='$end_date')
										)AND
										((status_1=0 OR status_1=1) AND (status_2=0 OR status_2=1)) /*yang tidak ditolak/dibatalkan*/
										AND
										(approval_sdm=0 OR approval_sdm=1) /*yang tidak ditolak sdm*/
										")->row_array();
		
		
		return $query;
			
	}
	
	//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
    function select_daftar_karyawan()
	{
		/*
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{			
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{	
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
			{
				$var='';
			}
			
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
		{
			$var 	= $_SESSION["no_pokok"];
			
		}else
		{
			$var = '';				
		}	
			
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('mst_karyawan.kode_unit', $var);								
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('mst_karyawan.no_pokok', $var);	
		}else
		{
		}			
		
		$data = $this->db->get();
		
		return $data;
		*/
		
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_master))
		{
			$tabel_master = 'erp_master_data';
		}
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_master_kemarin))
		{
			$tabel_master_kemarin = 'erp_master_data';
		}
					
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			$var = "'".implode("','",$var)."'";
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;
		
		$where='';
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$where = "kode_unit IN ($var)";
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
		{
			$where = "np_karyawan IN ('$var')";
		}
		
		if($where!='')
		{
			$where = "WHERE ".$where;
		}
			
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama FROM 
									(SELECT a.np_karyawan, a.nama FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan) c where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan");
		
		return $data;	
	}

	public function get_atasan($kode_unit){
		$data = $this->db->select("no_pokok, nama_jabatan")
						 ->from("mst_karyawan a")
						 ->where("a.kode_unit_poh",$kode_unit)
						 ->like("a.kode_jabatan_poh","00","before")
						 ->get();
		//echo $this->db->last_query();
		$atasan["np"] = "";
		$atasan["nama_jabatan"] = "";
		$atasan["is_poh"] = false;
		
		if($data->num_rows()==1){
			$atasan["np"] = $data->result_array()[0]["no_pokok"];
			$atasan["nama_jabatan"] = $data->result_array()[0]["nama_jabatan"];
			$atasan["is_poh"] = true;
		}
		else if($data->num_rows()==0){
			$data = $this->db->select("no_pokok, nama_jabatan")
							 ->from("mst_karyawan a")
							 ->where("a.kode_unit",$kode_unit)
							 ->like("a.kode_jabatan","00","before")
							 ->get();
			//echo $this->db->last_query();
			
			if($data->num_rows()==1){
				$atasan["np"] = $data->result_array()[0]["no_pokok"];
				$atasan["nama_jabatan"] = $data->result_array()[0]["nama_jabatan"];
				$atasan["is_poh"] = false;
			}
		}
		return $atasan;
	}
	
	function select_absence_quota_by_np($np)
	{
		$this->db->select('*');
		$this->db->from('erp_absence_quota');	
		$this->db->where('np_karyawan',$np);
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_absence_quota_by_array_of_np($array_of_np)
	{
		if($array_of_np!=[]){
			$this->db->select('np_karyawan, start_date, deduction_from, deduction_to, number, deduction');
			$this->db->from('erp_absence_quota');	
			$this->db->where_in('np_karyawan',$array_of_np);
			$data = $this->db->get()->result();
			return $data;
		} else return [];
	}
	
	function select_cubes_by_np($np)
	{
		$this->db->select('*');
		$this->db->from('cuti_cubes_jatah');	
		$this->db->where('np_karyawan',$np);
		$this->db->where('tanggal_kadaluarsa >=', date("Y-m-d"));
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_hutang_by_np($np){
		$this->db->select('*');
		$this->db->from('cuti_hutang');	
		$this->db->where('no_pokok',$np);

		$data = $this->db->get();
		return $data;
	}
	
	function select_mst_cuti()
	{
		$this->db->select('*');
		$this->db->from('mst_cuti');
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_np_by_kode_unit($list_kode_unit)
	{
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		$this->db->where_in('kode_unit', $list_kode_unit);
		$this->db->order_by('no_pokok','ASC');
		$data = $this->db->get();
		
		return $data;
	}
	
	function insert_pelatihan($data)
	{
		$this->db->select('id');
		$this->db->from('ess_diklat_kebutuhan_pelatihan');
		$this->db->order_by('id', 'DESC'); // Mengurutkan data berdasarkan ID secara menurun
		$this->db->limit(1); // Hanya ambil satu data terakhir
		$id = $this->db->get()->row_array()['id'];

		$data = array(
				'id'						=> $id + 1,
				'np_karyawan'				=> $data['np_karyawan'],	
				'nama'						=> $data['nama'],	
				'nama_jabatan'				=> $data['nama_jabatan'],	
				'kode_unit'					=> $data['kode_unit'],	
				'nama_unit'					=> $data['nama_unit'],	
				'id_kategori_pelatihan'		=> $data['id_kategori_pelatihan'],				
				'kode_kategori_pelatihan'	=> $data['kode_kategori_pelatihan'],				
				'nama_kategori_pelatihan'	=> $data['nama_kategori_pelatihan'],	
				'id_pelatihan'				=> $data['id_pelatihan'],				
				'kode_pelatihan'			=> $data['kode_pelatihan'],				
				'pelatihan'					=> $data['pelatihan'],	
				// 'tanggal_pelatihan'			=> $data['tanggal_pelatihan'],				
				'skala_prioritas'			=> $data['skala_prioritas'],
				'vendor'					=> $data['vendor'],
				'approval_1'				=> $data['approval_1'],
				'approval_1_jabatan'		=> $data['approval_1_jabatan'],
				'status_1'					=> $data['status_1'],
				'approval_2'				=> $data['approval_2'],					
				'approval_2_jabatan'		=> $data['approval_2_jabatan'],
				'status_2'					=> $data['status_2'],						
				'created_at'				=> date('Y-m-d H:i:s'),
				'updated_by'				=> $_SESSION["no_pokok"],
			);
		
		$this->db->insert('ess_diklat_kebutuhan_pelatihan', $data); 

		if($this->db->affected_rows() > 0)
		{			
			return $this->db->insert_id(); 
		}else
		{
			return "0";
		}
	}

	function update_pelatihan($data_update)
	{
		$id 						= $data_update['id'];
		$skala_prioritas 			= $data_update['skala_prioritas'];
		
		$data = array(               
				'skala_prioritas' 	=> $skala_prioritas,
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $this->session->userdata('no_pokok')
            );
	
		$this->db->where('id',$id);	
		$this->db->update('ess_diklat_kebutuhan_pelatihan', $data); 
		
		
		if($this->db->affected_rows() > 0)
		{			
			return '1'; 
		}else
		{
			return '0';
		}
		
	}
	
	/*
	function check_cuti_tahunan($np_karyawan)
	{		
		$absence_quota_type='91';
		
		$this->db->select('sum(number)');
		$this->db->select('sum(deduction)');
		$this->db->from('erp_absence_quota');
		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->where('absence_quota_type', $absence_quota_type);
		$this->db->where('deduction_from<=', date('yyyy-dd-mm'));
		$this->db->where('deduction_to<=', date('yyyy-dd-mm'));
	
		$this->db->order_by("start_date", "ASC");	
		$query = $this->db->get();
		
		return $query->row_array();
	}
	*/
	
	function select_cuti_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function batal_pelatihan($id)
	{
		$data = array(				
				'status_1'			=> '3',
				'status_2'			=> '3',
				'approval_1_date'	=> date('Y-m-d H:i:s'),
				'approval_2_date'	=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $this->session->userdata('no_pokok')
        );
		$this->db->where('id', $id);
		$this->db->update('ess_diklat_kebutuhan_pelatihan', $data); 
		
		if($this->db->affected_rows() > 0)
		{			
			return "1"; 
		}else
		{
			return "0";
		}
	}

	function get_absence($id)
	{
		$this->db->where('kode_erp', $id);
		$tipe = $this->db->get('mst_cuti')->row(); 
		
		return $tipe->uraian;
	}

	function get_category()
	{
		$this->db->select('*');
		$this->db->from('mst_diklat_kategori_pelatihan');	
		
		$data = $this->db->get()->result_array();
		
		return $data;
	}

	function mst_kategori_pelatihan_by_id_pelatihan($id)
	{
		$this->db->select('*');
		$this->db->from('mst_diklat_pelatihan');	
		$this->db->where('id', $id);
		$data = $this->db->get()->row_array();

		$this->db->select('*');
		$this->db->from('mst_diklat_kategori_pelatihan');	
		$this->db->where('id', $data['id_kategori_pelatihan']);
		$data = $this->db->get()->row_array();
		
		return $data;
	}

	function mst_kategori_pelatihan_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('mst_diklat_kategori_pelatihan');	
		$this->db->where('id', $id);
		$data = $this->db->get()->row_array();
		
		return $data;
	}

	function get_pelatihan_all()
	{
		$this->db->select('*');
		$this->db->from('mst_diklat_pelatihan');	
		$data = $this->db->get()->result_array();
		
		return $data;
	}

	function get_pelatihan($val_kategori)
	{
		if ($val_kategori == 'Semua'){
			$this->db->select('*');
			$this->db->from('mst_diklat_pelatihan');	
			$data = $this->db->get()->result_array();	
		} else {
			$this->db->select('*');
			$this->db->from('mst_diklat_pelatihan');	
			$this->db->where('id_kategori_pelatihan', $val_kategori);
			$data = $this->db->get()->result_array();
		}
		
		return $data;
	}

	function mst_pelatihan_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('mst_diklat_pelatihan');	
		$this->db->where('id', $id);
		$data = $this->db->get()->row_array();
		
		return $data;
	}

	function get_skala_prioritas($np)
	{
		$sql = "SELECT skala_prioritas 
        FROM ess_diklat_kebutuhan_pelatihan 
        WHERE (
            (status_1 = '0' AND status_2 = '0') 
            OR (status_1 = '1' AND status_2 = '1') 
            OR (status_1 = '0' AND status_2 = '1') 
            OR (status_1 = '1' AND status_2 = '0')
        ) 
        AND np_karyawan = '".$np."'";

		$query = $this->db->query($sql);

		$data = $query->result_array();
		
		if (!$data){
			$data = [
				['skala_prioritas' => 1],
				['skala_prioritas' => 2],
				['skala_prioritas' => 3],
				['skala_prioritas' => 4],
				['skala_prioritas' => 5]
			];
			
		}

		return $data;
	}

	function get_last_kode_pelatihan_kategori_lainnya($np)
	{
		$this->db->select('kode_pelatihan');
		$this->db->from('ess_diklat_kebutuhan_pelatihan');	
		$this->db->where('nama_kategori_pelatihan', 'Lainnya');
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$data = $this->db->get()->row_array();
		
		if (!$data){
			$data['kode_pelatihan'] = 'ETC000';
		}

		return $data;
	}

	public function import($file_path)
	{
		// require_once __DIR__ . '/../../../vendor/autoload.php';
		$spreadsheet = IOFactory::load($file_path);
		$sheet = $spreadsheet->getActiveSheet();
		$data = $sheet->toArray();
		array_shift($data);
		$data = array_filter($data, function($row) {
			return !empty($row[1]); // Hanya menyimpan array dengan index 1 yang tidak kosong
		});

		return $data;
	}

	public function insert_batch($data, $data_karyawan)
	{	
		$array_of_np = array_column($data, 0);
		$this->db->select('no_pokok, nama, nama_jabatan, kode_unit, nama_unit');
		$this->db->from('mst_karyawan');	
		$this->db->where_in('no_pokok',$array_of_np);
		$karyawan = $this->db->get()->result_array();

		$this->db->select('id');
		$this->db->from('ess_diklat_kebutuhan_pelatihan');
		$this->db->order_by('id', 'DESC'); // Mengurutkan data berdasarkan ID secara menurun
		$this->db->limit(1); // Hanya ambil satu data terakhir
		$id = $this->db->get()->row_array()['id'];

		$this->db->select('*');
		$this->db->from('mst_diklat_kategori_pelatihan');	
		$allKategori = $this->db->get()->result_array();

		$this->db->select('*');
		$this->db->from('mst_diklat_pelatihan');	
		$allPelatihan = $this->db->get()->result_array();

		$this->db->select('kode_pelatihan');
		$this->db->from('ess_diklat_kebutuhan_pelatihan');	
		$this->db->where('nama_kategori_pelatihan', 'Lainnya');
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$lainnya = $this->db->get()->row_array();
		
		if (!$lainnya){
			$lainnya['kode_pelatihan'] = 'ETC000';
		}
		
		$target = substr($lainnya['kode_pelatihan'], 0, -3);
		$kategoriLainnya = array_filter($data, function ($item) use ($target) {
			return $item[1] === $target;
		});
		
		$i = 1;
		foreach ($kategoriLainnya as $key => $index) {
			$number = (int)substr($lainnya['kode_pelatihan'], -3) + $i;
			$new_kode = str_pad($number, 3, "0", STR_PAD_LEFT);		
			$data[$key][2] = substr($lainnya['kode_pelatihan'], 0, -3) . $new_kode;
			$i++;
		};

		// Menggabungkan kedua array
		foreach ($data as &$dataItem) {
			$data_search3 = array_filter($karyawan, function($karyawanItem) use ($dataItem) {
				return $karyawanItem['no_pokok'] === "".$dataItem[0];
			});

			$data_search = array_filter($allKategori, function($kategoriItem) use ($dataItem) {
				return $kategoriItem['kode_kategori_pelatihan'] === $dataItem[1];
			});

			$data_search2 = array_filter($allPelatihan, function($pelatihanItem) use ($dataItem) {
				return $pelatihanItem['kode_pelatihan'] === $dataItem[2];
			});
			
			// Ambil nilai pertama dari hasil filter (karena id unik)
			$data_search = array_values($data_search);
			$data_search2 = array_values($data_search2);
			$data_search3 = array_values($data_search3);

			// Menambahkan nilai ke data siswa jika ada
			if (count($data_search) > 0) {
				$dataItem['id_kategori_pelatihan'] = $data_search[0]['id'];
				$dataItem['nama_kategori_pelatihan'] = $data_search[0]['nama_kategori_pelatihan'];
			} else {
				$dataItem['id_kategori_pelatihan'] = null; // Jika tidak ada nilai
				$dataItem['nama_kategori_pelatihan'] = null;
			}

			if (count($data_search2) > 0) {
				$dataItem['id_pelatihan'] = $data_search2[0]['id'];
			} else {
				$dataItem['id_pelatihan'] = null;
			}

			if (count($data_search3) > 0) {
				$dataItem['nama'] = $data_search3[0]['nama'];
				$dataItem['nama_jabatan'] = $data_search3[0]['nama_jabatan'];
				$dataItem['kode_unit'] = $data_search3[0]['kode_unit'];
				$dataItem['nama_unit'] = $data_search3[0]['nama_unit'];
			} else {
				$dataItem['nama'] = null;
				$dataItem['nama_jabatan'] = null;
				$dataItem['kode_unit'] = null;
				$dataItem['nama_unit'] = null;
			}
		}

		foreach ($data as $key => $row) {
			$insert_data[] = [
				'id' => $id + 1 + $key,
				'np_karyawan' => $row[0],
				'nama' => $row['nama'],
				'nama_jabatan' => $row['nama_jabatan'],
				'kode_unit' => $row['kode_unit'],
				'nama_unit' => $row['nama_unit'],
				'id_kategori_pelatihan' => $row['id_kategori_pelatihan'],
				'kode_kategori_pelatihan' => $row[1],
				'nama_kategori_pelatihan' => $row['nama_kategori_pelatihan'],
				'id_pelatihan' => $row['id_pelatihan'],
				'kode_pelatihan' => $row[2],
				'pelatihan' => $row[3],
				'tanggal_pelatihan' => null,
				'vendor' => $row[5],
				'skala_prioritas' => $row[4],
				'approval_1' => $row['np_atasan_1'],
				'approval_1_jabatan' => $row['nama_jabatan_atasan_1'],
				'status_1' => '0',
				'approval_alasan_1' => null,
				'approval_1_date' => null,
				'approval_2' => $row['np_atasan_2'],
				'approval_2_jabatan' => $row['nama_jabatan_atasan_2'],
				'status_2' => '0',
				'approval_alasan_2' => null,
				'approval_2_date' => null,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => null,
				'updated_by' => $_SESSION["no_pokok"]
			];
		}

		if (!empty($insert_data)) {
			$this->db->insert_batch('ess_diklat_kebutuhan_pelatihan', $insert_data);
		}

		$this->db->trans_complete();

		return [
			'status' => $this->db->trans_status(),
			'inserted_count' => count($insert_data)
		];
	}

	function select_karyawan_by_array_of_np($array_of_np)
	{
		if($array_of_np!=[]){
			$this->db->select('no_pokok, nama, nama_jabatan, kode_unit, nama_unit');
			$this->db->from('mst_karyawan');	
			$this->db->where_in('no_pokok',$array_of_np);
			$data = $this->db->get()->result_array();
			return $data;
		} else return [];
	}

	function select_nama_karyawan_np($np)
	{
		$this->db->select('nama');
		$this->db->from('mst_karyawan');	
		$this->db->where('no_pokok',$np);
		$data = $this->db->get()->row_array();
		return $data;
	}
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */