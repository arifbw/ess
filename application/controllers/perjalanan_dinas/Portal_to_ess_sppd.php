<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Portal_to_ess_sppd extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('perjalanan_dinas/M_portal_to_ess_sppd');
		$this->folder_sppd	= dirname($_SERVER["SCRIPT_FILENAME"]) . "/outbound_portal/sppd/";
		$this->load->helper(array('tanggal_helper', 'karyawan_helper'));
	}

	public function index()
	{
		//redirect(base_url('dashboard'));
	}

	function get_files()
	{
		$msc = microtime(true);
		$ignored = array('.', '..', '.svn', '.htaccess');

		$data_files = array();
		echo '<br>Scanning dir ' . $this->folder_sppd . ' ...<br><br>';
		echo 'Start : ' . date('Y-m-d H:i:s') . '<br>';
		if (is_dir($this->folder_sppd)) {
			foreach (scandir($this->folder_sppd) as $file) {
				if (in_array($file, $ignored)) continue;
				$data_files = [
					'nama_file' => $file,
					'size' => filesize($this->folder_sppd . $file),
					'last_modified' => date('Y-m-d H:i:s', filemtime($this->folder_sppd . $file)),
					'baris_data' => $this->count_rows($this->folder_sppd . $file)
				];

				$this->M_portal_to_ess_sppd->check_name_then_insert_data($file, $data_files);
			}
			$msc = microtime(true) - $msc;
			echo "Done. Execution time: $msc seconds.<br>Inserted to database.";
		} else {
			echo 'Dir not found!';
		}
	}

	function count_rows($file_name)
	{
		$rows = explode("\n", trim(file_get_contents($file_name)));
		return count($rows) - 1;
	}

	function get_data($date_input = null)
	{
		$msc = microtime(true);
		echo '<br>Scanning files...<br>';
		echo 'Start : ' . date('Y-m-d H:i:s') . '<br><br>';

		set_time_limit('0');

		//get files that process=0
		$get_proses_is_noll = $this->M_portal_to_ess_sppd->get_proses_is_nol()->result();

		foreach ($get_proses_is_noll as $row) {
			if (is_file($this->folder_sppd . $row->nama_file)) {
				$this->read_process($row->nama_file);
			}
		}
		$msc = microtime(true) - $msc;

		echo "Done. Execution time: $msc seconds.<br>Inserted to database.";

		//insert ke tabel 'ess_status_proses_input', id proses = 7
		$this->db->insert('ess_status_proses_input', ['id_proses' => 7, 'waktu' => date('Y-m-d H:i:s')]);
	}

	function read_process($file)
	{
		//echo "<br>".$file."<br><br>";

		$rows = explode("\n", trim(file_get_contents($this->folder_sppd . $file)));

		$num_rows = 0;
		$count_inserted = 0;

		//parsing data di file .txt
		foreach ($rows as $row) {
			if (!empty(trim($row))) {
				$num_rows += 1;
				if ($num_rows > 1) {
					$pisah = explode("\t", trim($row));
					if (validateDate(@$pisah[5]) == true) {
						$nama_karyawan 		= erp_master_data_by_np(@$pisah[2], @$pisah[5])['nama'];
						$personel_number	= erp_master_data_by_np(@$pisah[2], @$pisah[5])['personnel_number'];
						$nama_jabatan		= erp_master_data_by_np(@$pisah[2], @$pisah[5])['nama_jabatan'];
						$kode_unit 			= erp_master_data_by_np(@$pisah[2], @$pisah[5])['kode_unit'];
						$nama_unit 			= erp_master_data_by_np(@$pisah[2], @$pisah[5])['nama_unit'];
					} else {
						$nama_karyawan 		= mst_karyawan_by_np(@$pisah[2])['nama'];
						$personel_number	= mst_karyawan_by_np(@$pisah[2])['personnel_number'];
						$nama_jabatan		= mst_karyawan_by_np(@$pisah[2])['nama_jabatan'];
						$kode_unit 			= mst_karyawan_by_np(@$pisah[2])['kode_unit'];
						$nama_unit 			= mst_karyawan_by_np(@$pisah[2])['nama_unit'];
					}

					if ($personel_number != NULL && trim($personel_number) != '') {
						//need to be check !!!
						$insert_data = array(
							'id_member'         => @$pisah[0],
							'id_sppd'           => @$pisah[1],
							'np_karyawan'       => @$pisah[2],
							'personel_number'   => $personel_number,
							'nama'              => $nama_karyawan,
							'nama_jabatan'      => $nama_jabatan,
							'kode_unit'         => $kode_unit,
							'nama_unit'         => $nama_unit,
							'perihal'  	        => @$pisah[3],
							'tipe_perjalanan'   => @$pisah[4],
							'tujuan'   			=> @$pisah[5],
							'tgl_berangkat'     => @$pisah[6],
							'tgl_pulang'        => @$pisah[7],
							'tgl_selesai'       => @$pisah[8],
							'no_surat'       	=> @$pisah[9],
							'justifikasi'       => @$pisah[10],
							'jumlah_hari'       => @$pisah[11],
							'waktudari'       	=> @$pisah[12],
							'waktusampai'       => @$pisah[13],
							'hotel'       		=> @$pisah[14],
							'bintang'       	=> @$pisah[15],
							'jenistransportasi' => @$pisah[16],
							'tipe_kamar'       	=> @$pisah[17],
							'catatan'       	=> @$pisah[18]
						);

						$data_where = [
							'np_karyawan' => @$pisah[2],
							'id_sppd' => @$pisah[1]
						];

						if ($this->M_portal_to_ess_sppd->check_id_then_insert_data($data_where, $insert_data) == true) {
							$count_inserted += 1;
						}

						$tanggal_awal	= 	@$pisah[6];
						$tanggal_akhir	=	@$pisah[7];
						$np_karyawan	= 	@$pisah[2];

						$tanggal_proses = $tanggal_awal;
						while (strtotime($tanggal_proses) <= strtotime($tanggal_akhir)) {
							//update id_sppd
							$this->load->model('perjalanan_dinas/m_sppd');
							$get_sppd['np_karyawan'] 	= $np_karyawan;
							$get_sppd['tgl_dws'] 		= $tanggal_proses;
							$this->m_sppd->insert_to_cico($get_sppd);


							$tanggal_proses = date("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses))); //looping tambah 1 date
						}
					}
				}
			}
		}
		//$proses = ($count_inserted==($num_rows - 1) ? '1':'0');
		$update_file = array(
			'proses'			=> 1,
			'baris_data'        => $num_rows - 1,
			'waktu_proses'		=> date('Y-m-d H:i:s')
		);

		$this->M_portal_to_ess_sppd->update_files($file, $update_file);
	}

	public function get_sppd()
	{
		$this->get_files();
		$this->get_data();
	}
}
