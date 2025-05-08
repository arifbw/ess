<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Portal_to_ess_sppd_lndn extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('perjalanan_dinas/M_portal_to_ess_sppd_lndn');
		$this->folder_sppd_lndn	= dirname($_SERVER["SCRIPT_FILENAME"]) . "/outbound_portal/sppd_LNDN/";
		$this->load->helper(array('tanggal_helper', 'karyawan_helper'));
	}

	public function index()
	{
		redirect(base_url('dashboard'));
	}

	function get_files()
	{
		$msc = microtime(true);
		$ignored = array('.', '..', '.svn', '.htaccess');

		$data_files = array();
		echo '<br>Scanning dir ' . $this->folder_sppd_lndn . ' ...<br><br>';
		echo 'Start : ' . date('Y-m-d H:i:s') . '<br>';
		if (is_dir($this->folder_sppd_lndn)) {
			foreach (scandir($this->folder_sppd_lndn) as $file) {
				if (in_array($file, $ignored)) continue;
				$data_files = [
					'nama_file' => $file,
					'size' => filesize($this->folder_sppd_lndn . $file),
					'last_modified' => date('Y-m-d H:i:s', filemtime($this->folder_sppd_lndn . $file)),
					'baris_data' => $this->count_rows($this->folder_sppd_lndn . $file)
				];

				$this->M_portal_to_ess_sppd_lndn->check_name_then_insert_data($file, $data_files);
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
		$get_proses_is_noll = $this->M_portal_to_ess_sppd_lndn->get_proses_is_nol()->result();

		foreach ($get_proses_is_noll as $row) {
			if (is_file($this->folder_sppd_lndn . $row->nama_file)) {
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

		$rows = explode("\n", trim(file_get_contents($this->folder_sppd_lndn . $file)));

		$num_rows = 0;
		$count_inserted = 0;

		//parsing data di file .txt
		foreach ($rows as $row) {
			if (!empty(trim($row))) {
				$num_rows += 1;
				if ($num_rows > 1) {
					$pisah = explode("\t", trim($row));
					// if (validateDate(@$pisah[5]) == true) {
					// 	$personel_number	= erp_master_data_by_np(@$pisah[2], @$pisah[5])['personnel_number'];
					// 	$kode_unit 			= erp_master_data_by_np(@$pisah[2], @$pisah[5])['kode_unit'];
					// } else {
					$personel_number	= mst_karyawan_by_np(@$pisah[2])['personnel_number'];
					$kode_unit 			= mst_karyawan_by_np(@$pisah[2])['kode_unit'];
					// }

					if ($personel_number != NULL && trim($personel_number) != '') {
						//need to be check !!!
						$insert_data = array(
							'id_member_sppd'	=> @$pisah[0],
							'id_sppd'           => @$pisah[1],
							'np_karyawan'       => @$pisah[2],
							'nama'              => @$pisah[3],
							'perihal'  	        => @$pisah[4],
							'tipe_perjalanan'   => @$pisah[5],
							'tujuan'   			=> @$pisah[6],
							'tgl_berangkat'     => @$pisah[7],
							'tgl_pulang'        => @$pisah[8],
							'tgl_selesai'       => @$pisah[9],
							'no_surat'       	=> @$pisah[10],
							// 'justifikasi'       => @$pisah[11],
							'id_surat' 			=> @$pisah[12],
							'hotel' 			=> @$pisah[13],
							'jenis_transportasi' => @$pisah[14],
							'jenis_fasilitas' 	=> @$pisah[15],
							'biaya' 			=> @$pisah[16],
							'biayaus' 			=> @$pisah[17],
							'nama_jabatan'		=> @$pisah[18],
							'pangkat' 			=> @$pisah[19],
							'unit' 				=> @$pisah[20],
							'kode_unit'         => $kode_unit,
							'jumlah_hari'       => @$pisah[21],
							'waktudari'       	=> @$pisah[22],
							'waktusampai'       => @$pisah[23],
							'bintang'       	=> @$pisah[24],
							'jenistransportasi' => @$pisah[25],
							'tipe_kamar'       	=> @$pisah[26],
							'catatan'       	=> @$pisah[27]
						);

						$data_where = [
							'id_member_sppd' => @$pisah[0],
							'id_sppd' => @$pisah[1],
							'np_karyawan' => @$pisah[2],
							'jenis_fasilitas' => @$pisah[15],
						];

						if ($this->M_portal_to_ess_sppd_lndn->check_id_then_insert_data($data_where, $insert_data) == true) {
							$count_inserted += 1;
						}

						// $tanggal_awal	= 	@$pisah[6];
						// $tanggal_akhir	=	@$pisah[7];
						// $np_karyawan	= 	@$pisah[2];

						// $tanggal_proses=$tanggal_awal;
						// while (strtotime($tanggal_proses) <= strtotime($tanggal_akhir))
						// {
						// 	//update id_sppd_lndn
						// 	$this->load->model('perjalanan_dinas/m_sppd_lndn');
						// 	$get_sppd_lndn['np_karyawan'] 	= $np_karyawan;
						// 	$get_sppd_lndn['tgl_dws'] 		= $tanggal_proses;
						// 	$this->m_sppd_lndn->insert_to_cico($get_sppd_lndn);

						// 	$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));//looping tambah 1 date
						// }
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

		$this->M_portal_to_ess_sppd_lndn->update_files($file, $update_file);
	}

	public function get_sppd_lndn()
	{
		$this->get_files();
		$this->get_data();
	}
}
