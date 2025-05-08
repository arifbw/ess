<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permohonan_perizinan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'perizinan/';
		$this->folder_model = 'perizinan/';
		$this->folder_controller = 'perizinan/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		$this->load->helper("perizinan_helper");

		#$this->load->model($this->folder_model."m_permohonan_cuti");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Permohonan Perizinan";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}

	public function index()
	{
		/** data perizinan belum diapprove atasan */
		$perizinan_belum_approve_atasan = $this->db->select('a.id, a.np_karyawan, a.nama, a.info_type, a.absence_type, a.kode_pamlek, a.start_date, a.end_date, a.approval_1_np, a.approval_1_nama, a.approval_1_status, a.approval_2_np, a.approval_2_nama, a.approval_2_status, a.id_perizinan, a.approval_pengamanan_posisi, b.nama AS nama_izin')
			->from('ess_request_perizinan a')
			->join('mst_perizinan b', "CONCAT(b.kode_pamlek,'|',b.kode_erp) = CONCAT(a.kode_pamlek,'|',a.info_type,'|',a.absence_type)", 'LEFT')
			->where('a.np_karyawan', $_SESSION["no_pokok"])
			->where('a.date_batal IS NULL', null, false)
			->where("(
							CASE 
								WHEN a.approval_2_np IS NOT NULL THEN (a.approval_1_np!='' AND a.approval_1_np IS NOT NULL AND a.approval_2_np!='' AND a.approval_2_np IS NOT NULL)
								WHEN a.approval_2_np IS NULL THEN (a.approval_1_np!='' AND a.approval_1_np IS NOT NULL)
							END
						)")
			->where("(
							CASE 
								WHEN a.approval_2_np IS NOT NULL THEN (a.approval_1_status!='2' OR a.approval_2_status!='2')
								WHEN a.approval_2_np IS NULL THEN a.approval_1_status!='2'
							END
						)")
			->where("(
							CASE 
								WHEN a.approval_2_np IS NOT NULL THEN (a.approval_1_status IS NULL OR a.approval_2_status IS NULL)
								WHEN a.approval_2_np IS NULL THEN a.approval_1_status IS NULL
							END
						)")
			->get()->result();

		$this->data["perizinan_belum_approve_atasan"] = $perizinan_belum_approve_atasan;
		// print_r($this->data["perizinan_belum_approve_atasan"]);
		//$this->output->enable_profiler(true);
		$this->load->model($this->folder_model . "M_permohonan_perizinan");
		//echo __FILE__ . __LINE__;die(var_dump($this->akses));
		$izin = $this->M_permohonan_perizinan->get_mst_perizinan()->result();
		$pos = $this->M_permohonan_perizinan->get_mst_pos()->result();
		$array_daftar_karyawan	= $this->M_permohonan_perizinan->select_daftar_karyawan();

		# ambil karyawan outsource
		$this->db->select('np_karyawan, nama');
		if ($_SESSION['grup'] == '5') {
			$this->db->where("SUBSTR(kode_unit,1,2)", substr($_SESSION['kode_unit'], 0, 2));
		} else if ($_SESSION['grup'] == '4') {
			$list_pengadministrasi = [];
			foreach ($_SESSION['list_pengadministrasi'] as $row) {
				$list_pengadministrasi[] = substr($row['kode_unit'], 0, 2);
			}
			if ($list_pengadministrasi != [])
				$this->db->where_in("SUBSTR(kode_unit,1,2)", $list_pengadministrasi);
			else
				$this->db->where("SUBSTR(kode_unit,1,2)", null);
		}
		$this->db->where("DATE_FORMAT(NOW(), '%Y-%m-%d') <= DATE_ADD(end_date, INTERVAL 1 MONTH)");
		$outsource = $this->db->get('ess_karyawan_outsource')->result_array();
		$this->data['array_daftar_outsource'] 	= $outsource;
		# END: ambil karyawan outsource

		$this->data["akses"] 					= $this->akses;
		$this->data["navigasi_menu"] 			= menu_helper();
		// $this->data['content'] 					= $this->folder_view."permohonan_perizinan";
		$this->data['content'] 					= $this->folder_view . "tabel_permohonan_perizinan";
		$this->data['jenis_izin']               = $izin;
		$this->data['pos']               		= $pos;
		//$this->data['select_mst_cuti']= $this->m_permohonan_cuti->select_mst_cuti();
		$this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
		$this->data['array_tahun_bulan'] 		= $this->M_permohonan_perizinan->get_tabel_perizinan_from_schema()->result();

		$this->load->view('template', $this->data);
	}

	public function permohonan_perizinan()
	{
		
		$jenis = array();

		$filter_bulan   = $this->uri->segment(4);
		if($this->uri->segment(5)==1){
                
			$jenis[] = '0';
		}
		if($this->uri->segment(6)==1){ //izin dinas pendidikan / non pendidikan
			$jenis[] = 'C';
		}
		if($this->uri->segment(7)==1){
			$jenis[] = 'E';
		}
		if($this->uri->segment(8)==1){
			$jenis[] = 'F';
		}
		if($this->uri->segment(9)==1){
			$jenis[] = 'G';
		}
		if($this->uri->segment(10)==1){
			$jenis[] = 'H';
		}
		if($this->uri->segment(11)==1){
			$jenis[] = 'SIPK';
		}

		$target_table   = $filter_bulan;
		$this->load->model($this->folder_model . "M_tabel_permohonan_perizinan");

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$var 	= $_SESSION["no_pokok"];
		} else {
			$var = 1;
		}

		# tambahan untuk filter date range, 2021-02-24
		$date_range = $this->input->post('date_range', true);
		$explode_date_range = explode(' - ', $date_range);
		$startDate = date('Y-m-d', strtotime($explode_date_range[0]));
		$endDate = date('Y-m-d', strtotime($explode_date_range[1]));
		$params = [];
		$params['startDate'] = $startDate;
		$params['endDate'] = $endDate;
		# END tambahan untuk filter date range, 2021-02-24

		$list 	= $this->M_tabel_permohonan_perizinan->get_datatables($var, $params, @$jenis);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $tampil) {
			$no++;
			$absence_type = $tampil->kode_pamlek . '|' . $tampil->info_type . '|' . $tampil->absence_type;
			$row = array();
			$row[] = $no;
			$row[] = $tampil->np_karyawan . '<br>' . $tampil->nama;
			$row[] = get_perizinan_name($tampil->kode_pamlek)->nama;

			if ($tampil->start_date) {
				$row[] = tanggal_indonesia($tampil->start_date) . '<br>' . $tampil->start_time . '<br><span class="text-primary"><b>machine : ' . $tampil->machine_id_start . '</b></span>';
			} else {
				$row[] = '';
			}

			if ($tampil->end_date) {
				$row[] = tanggal_indonesia($tampil->end_date) . '<br>' . $tampil->end_time . '<br><span class="text-primary"><b>machine : ' . $tampil->machine_id_end . '</b></span>';
			} else {
				$row[] = '';
			}

			$row[] = implode("<br>", array_column($this->db->where("id in ('" . (implode("','", json_decode($tampil->pos))) . "')")->get('mst_pos')->result_array(), 'nama'));

			# action hapus dikomen, 2021-03-04 heru/bowo
			//if ($this->akses["hapus"]) {
			//cutoff ERP
			if ($tampil->start_date) {
				$tanggal_check = $tampil->start_date;
			} else {
				$tanggal_check = $tampil->end_date;
			}

			$sudah_cutoff = sudah_cutoff($tanggal_check);

			if ($sudah_cutoff) { //jika sudah lewat masa cutoff
				$row[] = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . " data-tgl=" . ($tampil->start_date != null ? $tampil->start_date : $tampil->end_date) . "'>Sudah Cutoff Data</button>";

				$aksi = "<button class='btn btn-primary btn-xs' data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
			} else {
				$np_karyawan	= trim($tampil->np_karyawan);
				$nama			= trim($tampil->nama);
				$kode_pamlek	= trim($tampil->kode_pamlek);
				$created_at		= trim($tampil->created_at);
				$start_date		= trim(tanggal_indonesia($tampil->start_date) . ' ' . $tampil->start_time);
				$end_date		= trim(tanggal_indonesia($tampil->end_date) . ' ' . $tampil->end_time);
				$approval_1		= trim($tampil->approval_1_np);
				$approval_2		= trim($tampil->approval_2_np);
				$status_1		= trim($tampil->approval_1_status);
				$approval_1_alasan		= trim($tampil->approval_1_keterangan);
				$status_2		= trim($tampil->approval_2_status);
				$approval_2_alasan		= trim($tampil->approval_2_keterangan);
				$approval_1_date = trim($tampil->approval_1_updated_at);
				$approval_2_date = trim($tampil->approval_2_updated_at);
				$np_batal_apr	= trim($tampil->np_batal);
				if ($np_batal_apr != '' && $np_batal_apr != null) {
					$waktu_batal	= 'Izin Telah Dibatalkan pada ' . trim($tampil->date_batal);
					$alasan_batal	= 'Alasan : ' . trim($tampil->alasan_batal);
					$np_batal		= $np_batal_apr . " | " . nama_karyawan_by_np($np_batal_apr);
				} else {
					$waktu_batal	= '';
					$alasan_batal	= '';
					$np_batal		= '';
				}

				if ($status_1 == '1') {
					$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Izin Telah Disetujui pada $approval_1_date.";
				} else if ($status_1 == '2') {
					$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Izin TIDAK disetujui pada $approval_1_date.";
				} else if ($status_1 == '3') {
					$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_1_date.";
				} else if ($status_1 == '' || $status_1 == '0' || $status_1 == null) {
					$status_1 = '0';
					$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Izin BELUM disetujui.";
				}

				if ($status_2 == '1') {
					$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Izin Telah Disetujui pada $approval_2_date.";
				} else if ($status_2 == '2') {
					$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Izin TIDAK disetujui pada $approval_2_date.";
				} else if ($status_2 == '3') {
					$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_2_date.";
				} else if ($status_2 == '' || $status_2 == '0' || $status_2 == null) {
					$status_2 = '0';
					$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Izin BELUM disetujui.";
				}

				$btn_warna		= 'btn-default';
				$btn_text		= 'Menunggu Persetujuan';
				$btn_disabled 	= '';

				if (($status_1 == '' || $status_1 == null) && ($status_2 != '2' || $status_2 != '1')) { //menunggu atasan 1
					$btn_warna		= 'btn-warning';
					$btn_text		= 'Menunggu Atasan 1';
				}

				if (($status_1 == '1') && ($status_2 != '2' || $status_2 != '1')) { //disetujui atasan 1
					if ($tampil->approval_2_np == null || $tampil->approval_2_np == '') { //jika tidak ada atasan 2
						$btn_warna		= 'btn-success';
						$btn_text		= 'Disetujui Atasan 1';
						$btn_disabled 	= '';
					} else { //jika ada atasan 2
						$btn_warna		= 'btn-warning';
						$btn_text		= 'Disetujui Atasan 1, Menunggu Atasan 2';
						$btn_disabled 	= 'disabled';
					}
				}

				if (($status_1 == '2') && ($status_2 != '2' || $status_2 != '1')) { //ditolak atasan 1
					$btn_warna		= 'btn-danger';
					$btn_text		= 'Ditolak Atasan 1';
					$btn_disabled 	= 'disabled';
				}

				if ($status_2 == '1') { //disetujui atasan  2
					$btn_warna		= 'btn-success';
					$btn_text		= 'Disetujui Atasan 2';
					$btn_disabled 	= 'disabled';

					if ($status_1 == '0' || $status_1 == null) { //jika paralel atasan 2 belum approve
						$btn_warna		= 'btn-warning';
						$btn_text		= 'Disetujui Atasan 2, Menunggu Atasan 1';
						$btn_disabled 	= 'disabled';
					}
				}

				if ($status_2 == '2') { //ditolak atasan 2
					$btn_warna		= 'btn-danger';
					$btn_text		= 'Ditolak Atasan 2';
					$btn_disabled 	= 'disabled';
				}

				if ($tampil->date_batal != null) { //dibatalkan
					$btn_warna		= 'btn-danger';
					$btn_text		= 'Dibatalkan keamanan';
					$btn_disabled 	= 'disabled';
				}

				$row[] = "<button class='btn $btn_warna btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . " data-tgl=" . ($tampil->start_date != null ? $tampil->start_date : $tampil->end_date) . ">$btn_text</button>";

				if ($tampil->date_batal == null) {
					if (($tampil->approval_1_status == null && $tampil->approval_2_status == null && $tampil->approval_pengamanan_np == null)) {
						$np_hapus = $tampil->np_karyawan;
						$tanggal_hapus = ($tampil->start_date != NULL ? $tampil->start_date : $tampil->end_date);
						$time_hapus = ($tampil->start_time != NULL ? $tampil->start_time : $tampil->end_time);
						$date_time_hapus = date('Y-m-d H:i:s', strtotime($tanggal_hapus . ' ' . $time_hapus));

						# Admin Pamsiknilmat (7) dan Admin Pamsiknilmat Masterdata (15) tidak ada tombol hapus, 2021-03-16 Heru
						if (!in_array($_SESSION["grup"], [15]) && $this->akses["hapus"])
							$aksi = '<button class="btn btn-danger" onclick="hapus(\'' . $tampil->id . '\',\'' . $np_hapus . '\',\'' . $tampil->end_date . '\',\'' . $tampil->start_date . '\')">Hapus</button>';
						else
							$aksi = '';
					} else {
						/*$aksi = "<button class='btn btn-default status_button' data-toggle='modal' data-target='#modal_status'
							data-np-karyawan='$np_karyawan'
							data-nama='$nama'
							data-pamlek='$kode_pamlek'
							data-created-at='$created_at'
							data-start-date='$start_date'
							data-end-date='$end_date'
							data-approval-1-nama='$approval_1_nama'
							data-approval-2-nama='$approval_2_nama'
							data-approval-1-status='$approval_1_status'
							data-approval-1-alasan='$approval_1_alasan'
							data-approval-2-status='$approval_2_status'
							data-approval-2-alasan='$approval_2_alasan'
							data-batal-alasan='$alasan_batal'
							data-batal-np='$np_batal'
							data-batal-waktu='$waktu_batal'>Detail</button>";*/
						$aksi = "<button class='btn btn-default detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . " data-tgl=" . ($tampil->start_date != null ? $tampil->start_date : $tampil->end_date) . ">Detail</button>";
					}
				} else {
					/*$aksi = "<button class='btn btn-default status_button' data-toggle='modal' data-target='#modal_status'
						data-np-karyawan='$np_karyawan'
						data-nama='$nama'
						data-pamlek='$kode_pamlek'
						data-created-at='$created_at'
						data-start-date='$start_date'
						data-end-date='$end_date'
						data-approval-1-nama='$approval_1_nama'
						data-approval-2-nama='$approval_2_nama'
						data-approval-1-status='$approval_1_status'
						data-approval-1-alasan='$approval_1_alasan'
						data-approval-2-status='$approval_2_status'
						data-approval-2-alasan='$approval_2_alasan'
						data-batal-alasan='$alasan_batal'
						data-batal-np='$np_batal'
						data-batal-waktu='$waktu_batal'>Detail</button>";*/

					$aksi = "<button class='btn btn-default detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . " data-tgl=" . ($tampil->start_date != null ? $tampil->start_date : $tampil->end_date) . ">Detail</button>";
				}
			}

			$pengamanan_posisi = json_decode($tampil->approval_pengamanan_posisi);

if (is_array($pengamanan_posisi) && count($pengamanan_posisi) > 0) {
    $last_pengamanan = $pengamanan_posisi[(count($pengamanan_posisi) - 1)];
    $btn_warna = 'btn-warning';
    $btn_text = ucwords($last_pengamanan->posisi);
    $btn_disabled = '';
} else {
    $btn_warna = 'btn-danger';
    $btn_text = 'Belum Keluar/Masuk';
    $btn_disabled = '';
}

$row[] = "<button class='btn " . $btn_warna . " btn-xs'>$btn_text</button>";
			/*}
                else {
                    $aksi = "";
                }*/

			$row[] = $aksi;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_permohonan_perizinan->count_all($var, $params, @$jenis),
			"recordsFiltered" => $this->M_tabel_permohonan_perizinan->count_filtered($var, $params, @$jenis),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function view_detail()
	{
		$id_ = $this->input->post('id_perizinan');
		$tgl = $this->input->post('tgl');
		$bulan = substr($tgl, 0, 4) . '_' . substr($tgl, 5, 2);
		// $tabel = 'ess_perizinan_'.$bulan;
		$tabel = 'ess_request_perizinan';
		$izin = $this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->where('id', $id_)->get($tabel . ' a')->row_array();

		$data["id_"] = $izin["id"];
		$data["no_pokok"] = $izin["np_karyawan"];
		$data["nama_pegawai"] = $izin["nama"];

		if ($izin["start_date"] != null)
			$data["start_date"] = tanggal_indonesia($izin["start_date"]) . ' ' . $izin["start_time"];
		else
			$data["start_date"] = '';

		if ($izin["start_date_input"] != null)
			$data["start_date_input"] = tanggal_indonesia(date('Y-m-d', strtotime($izin["start_date_input"]))) . ' ' . date('H:i:s', strtotime($izin["start_date_input"]));
		else
			$data["start_date_input"] = '';

		$data["end_date"] = tanggal_indonesia($izin["end_date"]) . ' ' . $izin["end_time"];
		$data["end_date_input"] = tanggal_indonesia(date('Y-m-d', strtotime($izin["end_date_input"]))) . ' ' . date('H:i:s', strtotime($izin["end_date_input"]));
		$data["tgl"] = $izin["created_at"];
		$data["date"] = $izin["ordere"];
		$data["kode_pamlek"] = $izin["kode_pamlek"];

		# tambahan untuk alasan, 2021-03-10
		$data["alasan"] = $izin['alasan'];

		$approval_1		= trim($izin['approval_1_np']);
		$approval_2		= trim($izin['approval_2_np']);
		$status_1		= trim($izin['approval_1_status']);
		$status_2		= trim($izin['approval_2_status']);
		$approval_1_date = trim($izin['approval_1_updated_at']);
		$approval_2_date = trim($izin['approval_2_updated_at']);

		if ($status_1 == '1') {
			$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
			$approval_1_status 	= "Izin Telah Disetujui pada $approval_1_date.";
		} else if ($status_1 == '2') {
			$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
			$approval_1_status 	= "Izin TIDAK disetujui pada $approval_1_date.";
		} else if ($status_1 == '3') {
			$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
			$approval_1_status 	= "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_1_date.";
		} else if ($status_1 == '' || $status_1 == '0' || $status_1 == null) {
			$status_1 = '0';
			$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
			$approval_1_status 	= "Izin BELUM disetujui.";
		}

		if ($status_2 == '1') {
			$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
			$approval_2_status 	= "Izin Telah Disetujui pada $approval_2_date.";
		} else if ($status_2 == '2') {
			$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
			$approval_2_status 	= "Izin TIDAK disetujui pada $approval_2_date.";
		} else if ($status_2 == '' || $status_2 == '0' || $status_2 == null) {
			$status_2 = '0';
			$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
			$approval_2_status 	= "Izin BELUM disetujui.";
		}

		$np_batal_apr = trim($izin['np_batal']);
		if ($np_batal_apr != '' && $np_batal_apr != '0' && $np_batal_apr != null) {
			$data["waktu_batal"] = 'Izin telah dibatalkan keamanan pada ' . trim($izin['date_batal']);
			$data["alasan_batal"] = trim($izin['alasan_batal']);
			$data["np_batal"] = $np_batal_apr . " | " . nama_karyawan_by_np($np_batal_apr);
		} else {
			$data["waktu_batal"] = '';
			$data["alasan_batal"] = '';
			$data["np_batal"] = '';
		}
		$data["np_batal_apr"] = $np_batal_apr;

		$arr_pos = json_decode($izin["pos"]);
		$pos = $this->db->where_in('id', $arr_pos)->get('mst_pos')->result();
		$data["pos"] = $pos;

		$data["status_1"] = $status_1;
		$data["status_2"] = $status_2;
		$data["approval_1"] = $approval_1;
		$data["approval_2"] = $approval_2;
		$data["status_approval_1_nama"] = $approval_1_nama;
		$data["status_approval_1_status"] = $approval_1_status;
		$data["status_approval_1_keterangan"] = $izin['approval_1_keterangan'];
		$data["status_approval_2_nama"] = $approval_2_nama;
		$data["status_approval_2_status"] = $approval_2_status;
		$data["status_approval_2_keterangan"] = $izin['approval_2_keterangan'];
		$data["pengamanan"] = ($izin["approval_pengamanan_posisi"] == null) ? array() : json_decode($izin["approval_pengamanan_posisi"]);
		$data["judul"] = ucwords(preg_replace("/_/", " ", __CLASS__));
		$this->load->view($this->folder_view . "detail", $data);
	}

	//Fungsi untuk mengambil data dalam file .txt yang ada di outbound_portal/sppd
	public function get_data($last_date = null)
	{
		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');

		//$this->output->enable_profiler(TRUE);

		echo "Proses ambil data dari pamlek";
		echo "<br>mulai " . date('Y-m-d H:i:s') . "<br>";

		//ambil data di database setting
		$this->load->model($this->folder_model . "M_sppd");
		$setting	= $this->M_sppd->setting();

		$pamlek_url	= dirname($_SERVER["SCRIPT_FILENAME"]) . $setting['url'];
		$pamlek_max	= $setting['max_files_hapus'];
		// $pamlek_max	= $setting['max_files'];

		if (@$last_date) {
			$file = 'biaya-sppd-' . $last_date . '.txt';
			if ($last_date == date('Y-m-d') && is_file($pamlek_url . $file)) {
				$this->read_process($pamlek_url, $file);
			} else {
				$data_error['modul'] 		= "perizinan/sppd/get_data";
				$data_error['error'] 		= "Gagal konek ke Server Pamlek";
				$data_error['status'] 		= "0";
				$data_error['created_at'] 	= date("Y-m-d H:i:s");
				$data_error['created_by'] 	= "scheduler";

				$this->M_sppd->insert_error($data_error);
				echo "<br>status = " . $data_error['error'] . ", " . $data_error['modul'];
			}
		} else {
			//ambil data mana saja yang belum di proses
			$result = $this->M_sppd->select_pamlek_files_limit($pamlek_max);

			$arr_registered_pamlek_files = array();
			foreach ($result->result_array() as $data) {
				array_push($arr_registered_pamlek_files, $data['nama_file']);
			}

			//check server pamlek menyala
			if (is_dir($pamlek_url)) {
				//scan file .txt dalam server ftp pamlek 
				$arr_scan_pamlek_files = scandir($pamlek_url);

				$pamlek_files = array();
				foreach ($arr_scan_pamlek_files as $file) {
					if (in_array($file, $arr_registered_pamlek_files)) {
						array_push($pamlek_files, $file);
					}
				}

				foreach ($pamlek_files as $file) {
					$this->read_process($pamlek_url, $file);
				}
			} else {
				$data_error['modul'] 		= "perizinan/sppd/get_data";
				$data_error['error'] 		= "Gagal konek ke Server Pamlek";
				$data_error['status'] 		= "0";
				$data_error['created_at'] 	= date("Y-m-d H:i:s");
				$data_error['created_by'] 	= "scheduler";

				$this->M_sppd->insert_error($data_error);
				echo "<br>status = " . $data_error['error'] . ", " . $data_error['modul'];
			}
		}

		echo "<br>selesai " . date('Y-m-d H:i:s');
	}

	function read_process($pamlek_url, $file)
	{
		echo "<br>" . $file . "<br><br>";

		$rows = explode("\n", trim(file_get_contents($pamlek_url . $file)));

		$i = 1;
		$banyak_data = 0;

		//parsing data di file .txt
		$array_insert_data = array();
		foreach ($rows as $row) {
			if (!empty(trim($row))) {

				$banyak_data++;
				$pisah = explode("\t", trim($row));

				$insert_data = array(
					'id_sppd'       	=> $pisah[0],
					'id_user'           => $pisah[1],
					'kode_sto' 			=> $pisah[2],
					'jenis_fasilitas'	=> $pisah[3],
					'biaya'			    => $pisah[4],
					'catatan'   		=> @$pisah[6],
					'tgl_pulang'		=> @$pisah[7]
				);

				$this->M_sppd->cek_id_then_insert_data($pisah[0], $insert_data);
			}
		}

		$update_file = array(
			'proses'			=> '1',
			'baris_data' 		=> $banyak_data,
			'waktu_proses'		=> date('Y-m-d H:i:s')
		);

		$this->M_sppd->update_files($file, $update_file);
	}

	public function ajax_getListNp()
	{
		$this->load->model($this->folder_model . "M_permohonan_perizinan");
		$tampil = '';

		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
		{
			$list_kode_unit = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{
				array_push($list_kode_unit, $data['kode_unit']);
			}

			$np_list = array();
			$np_list = $this->M_permohonan_perizinan->select_np_by_kode_unit($list_kode_unit);

			foreach ($np_list->result_array() as $np) {
				if ($tampil) {
					$tampil = $tampil . "" . $np['no_pokok'] . " | " . $np['nama'] . "\n";
				} else {
					$tampil = $np['no_pokok'] . " | " . $np['nama'] . "\n";
				}
			}
		} else
			if ($_SESSION["grup"] == 5) //jika Pengguna
		{
			$np 	= $_SESSION["no_pokok"];
			$tampil	= $np . " | " . nama_karyawan_by_np($np) . "\n";
		} else {
			$tampil = "Anda Memiliki Hak untuk semua nomer pokok Karyawan";
		}



		echo $tampil;
	}

	public function ajax_getNama()
	{
		$np_karyawan	= $this->input->post('vnp_karyawan');

		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
		{
			$temp = '';
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{
				if (kode_unit_by_np($np_karyawan) == $data['kode_unit']) //check apakah ada disalah satu unit 
				{
					$temp = $np_karyawan;
				}
			}

			$np_karyawan	= $temp;
		} else
			if ($_SESSION["grup"] == 5) //jika Pengguna
		{
			if ($np_karyawan == $_SESSION["no_pokok"]) {
				$np_karyawan	= $this->input->post('vnp_karyawan');
			} else {
				$np_karyawan	= '';
			}
		}

		$nama 			= nama_karyawan_by_np($np_karyawan);

		echo $nama;
	}

	function action_insert_perizinan()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required');
		$this->form_validation->set_rules('absence_type', 'Tipe izin', 'required');
		if ($this->input->post('absence_type') != '0|2001|5000') {
			$this->form_validation->set_rules('start_date', 'Tanggal mulai izin', 'required');
			$this->form_validation->set_rules('start_time', 'Waktu mulai izin', 'required');
		}
		$this->form_validation->set_rules('end_date', 'Tanggal akhir izin', 'required');
		$this->form_validation->set_rules('end_time', 'Waktu akhir izin', 'required');
		$this->form_validation->set_rules('approval_1_np', 'Approver 1', 'required|exact_length[4]|alpha_numeric');
		$this->form_validation->set_rules('approval_1_input', 'Approver 1', 'required');
		$this->form_validation->set_rules('approval_1_input_jabatan', 'Approver 1', 'required');

		if ($this->input->post('absence_type') == 'H|2001|5030' || $this->input->post('absence_type') == 'D|2001|5040' || $this->input->post('absence_type') == 'C|2001|5040' || $this->input->post('absence_type') == 'F|2001|5020' || $this->input->post('absence_type') == 'E|2001|5010') {
			$this->form_validation->set_rules('approval_2_np', 'Approver 2', 'required|exact_length[4]|alpha_numeric');
			$this->form_validation->set_rules('approval_2_input', 'Approver 2', 'required');
			$this->form_validation->set_rules('approval_2_input_jabatan', 'Approver 2', 'required');
		}

		$this->form_validation->set_rules('pos[]', 'Pos', 'required');
		$this->form_validation->set_rules('alasan', 'Alasan', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('warning', "Data Belum Lengkap <br>" . validation_errors());
			redirect(base_url('perizinan/permohonan_perizinan'));
		} else {
			$data_insert = [];
			$submit = $this->input->post('submit');
			if ($submit) {
				$absence_type = $this->input->post('absence_type');
				$explode = explode('|', $absence_type);
				$info_type = $explode[1];
				$absence_type = $explode[2];
				$kode_pamlek = $explode[0];

				//                gak usah cek kehadiran
				//                if($kode_pamlek=='0'){ 
				//                    $this->action_insert_perizinan_sidt($this->input->post()); exit();
				//                }

				$np_karyawan		= $this->input->post('np_karyawan');
				$start_date			= @$this->input->post('start_date') ? date('Y-m-d', strtotime($this->input->post('start_date'))) : null;
				$start_time			= @$this->input->post('start_time') ? $this->input->post('start_time') : null;
				$end_date			= date('Y-m-d', strtotime($this->input->post('end_date')));
				$end_time			= $this->input->post('end_time');
				$approval_1_np		= $this->input->post('approval_1_np');
				$approval_1_nama	= $this->input->post('approval_1_input');
				$approval_1_jabatan	= $this->input->post('approval_1_input_jabatan');
				$approval_2_np		= (@$this->input->post('approval_2_np') != '') ? $this->input->post('approval_2_np') : null;
				$approval_2_nama	= (@$this->input->post('approval_2_input') != '') ? $this->input->post('approval_2_input') : null;
				$approval_2_jabatan	= (@$this->input->post('approval_2_input_jabatan') != '') ? $this->input->post('approval_2_input_jabatan') : null;
				$pos				= json_encode($this->input->post('pos'));

				$start_date_time = ($kode_pamlek == '0' ? null : date('Y-m-d H:i', strtotime($start_date . ' ' . $start_time)));
				$end_date_time = date('Y-m-d H:i', strtotime($end_date . ' ' . $end_time));
				$tahun_bulan     = $start_date != null ? str_replace('-', '_', substr("$start_date", 0, 7)) : str_replace('-', '_', substr("$end_date", 0, 7));

				$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
				$personel_number	= erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'];
				$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
				$kode_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'];
				$nama_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'];

				$this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$tahun_bulan LIKE ess_perizinan");

				$data_insert = [
					'np_karyawan' => $np_karyawan,
					'nama' => $nama_karyawan,
					'personel_number' => $personel_number,
					'nama_jabatan' => $nama_jabatan,
					'kode_unit' => $kode_unit,
					'nama_unit' => $nama_unit,
					'info_type' => $info_type,
					'absence_type' => $absence_type,
					'kode_pamlek' => $kode_pamlek,
					'start_date' => $start_date,
					'start_time' => $start_time,
					'end_date' => $end_date,
					'end_time' => $end_time,
					'start_date_input' => $start_date_time,
					'end_date_input' => $end_date_time,
					'approval_1_np' => $approval_1_np,
					'approval_1_nama' => $approval_1_nama,
					'approval_1_jabatan' => $approval_1_jabatan,
					'approval_2_np' => $approval_2_np,
					'approval_2_nama' => $approval_2_nama,
					'approval_2_jabatan' => $approval_2_jabatan,
					'end_time' => $end_time,
					'machine_id_start' => 'ess',
					'machine_id_end' => 'ess',
					'pos' => $pos,
					'created_at' => date('Y-m-d H:i:s'),
					'created_by' => $_SESSION['no_pokok'],
					'alasan' => trim($this->input->post('alasan', true)) # tambahan untuk alasan, 2021-03-10
				];

				//echo '<br>under maintenance. <br>';
				//echo json_encode($data_insert); exit();
				#$cek_izin = $this->db->where('np_karyawan="'.$data_insert['np_karyawan'].'" AND approval_1_status is not null AND (approval_2_np is null OR approval_2_status is not null) ')->get('ess_request_perizinan');
				# 2021-04-01, heru ganti query jadi ini
				$cek_izin = $this->db
					->where('np_karyawan', $data_insert['np_karyawan'])
					->where('date_batal IS NULL', null, false)
					->where("(
	                                CASE 
	                                    WHEN approval_2_np IS NOT NULL THEN (approval_1_status IS NULL OR approval_2_status IS NULL)
	                                    WHEN approval_2_np IS NULL THEN approval_1_status IS NULL
	                                END
	                            )")
					->get('ess_request_perizinan');

				if ($np_karyawan == '' || $np_karyawan == null) {
					$this->session->set_flashdata('warning', "NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				} else if (($start_date == '' || $start_date == null) && $kode_pamlek != '0') {
					$this->session->set_flashdata('warning', "Start date tidak boleh kosong.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				} else if ($end_date == '' || $end_date == null) {
					$this->session->set_flashdata('warning', "End date tidak boleh kosong.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				} else if ($start_date_time >= $end_date_time) {
					$this->session->set_flashdata('warning', "Tanggal Akhir Perizinan harus lebih besar dari Tanggal Mulai Perizinan.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				} else if ($cek_izin->num_rows() > 0) {
					# 2021-04-01, tambahan untuk filter izin yg belum diapprove
					$array_pending = [];
					$count_pending = 0;
					foreach ($cek_izin->result() as $row) {
						$tanggal_pending = $row->start_date != null ? tanggal_indonesia($row->start_date) . ' - ' : '';
						$tanggal_pending .= $row->end_date != null ? tanggal_indonesia($row->end_date) : '';
						if ($row->approval_1_status == '2' || $row->approval_2_status == '2') {
							# do nothing
						} else {
							if ($row->approval_2_np != null) {
								if ($row->approval_1_status == null) {
									$array_pending[] = "<br>Tanggal {$tanggal_pending} Belum Diapprove Atasan 1.";
									$count_pending++;
								} else {
									if ($row->approval_1_status == '1' && $row->approval_2_status == null) {
										$array_pending[] = "<br>Tanggal {$tanggal_pending} Belum Diapprove Atasan 2.";
										$count_pending++;
									}
								}
							} else {
								if ($row->approval_1_status == null) {
									$array_pending[] = "<br>Tanggal {$tanggal_pending} Belum Diapprove Atasan 1.";
									$count_pending++;
								}
							}
						}
					}

					if ($count_pending > 0) {
						$this->session->set_flashdata('warning', "Permohonan Perizinan Terakhir Belum Diapprove Oleh Atasan/Keamanan." . implode(" ", $array_pending));
						redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
					}
				}

				//old $cek = $this->db->where(['np_karyawan'=>$nama_karyawan, 'start_date'=>$start_date, 'end_date'=>$end_date, 'start_time'=>$start_time, 'end_time'=>$end_time, 'kode_pamlek'=>$kode_pamlek])->get("ess_perizinan_$tahun_bulan");
				$start_date_time_validasi = ($start_date_time == null ? $end_date_time : $start_date_time);
				$cek = $this->db->query("SELECT * 
	                FROM ess_perizinan_$tahun_bulan
	                WHERE np_karyawan='$np_karyawan' 
	                AND (
	                    ('$start_date_time_validasi' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
	                    OR ('$end_date_time' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
	                    OR (DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time_validasi' AND '$end_date_time')
	                    OR (DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time_validasi' AND '$end_date_time')
	                ) ");

				//15 04 2021, Tri Wibowo 7648, Dimatikan Sementara Karena Masih salah, contoh row 108 di excel google drive
				//if($cek->num_rows()>0){
				if (1 == 2) {
					$this->session->set_flashdata('warning', "Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time sudah ada.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				} else {
					// $this->db->insert("ess_perizinan_$tahun_bulan", $data_insert);
					$this->db->insert("ess_request_perizinan", $data_insert);

					/*$parameter_perizinan = [
	                        'id_row_baru'=>$this->db->insert_id(),
	                        'np_karyawan'=>$np_karyawan,
	                        'date_start'=>$start_date,
	                        'date_end'=>$end_date
	                    ];
	                    $this->update_cico($parameter_perizinan);*/

					$this->session->set_flashdata('success', "Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time berhasil ditambahkan.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				}

				echo json_encode($data_insert);
			} else {
				$this->session->set_flashdata('warning', "Terjadi Kesalahan");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			}
		}
	}

	function action_insert_perizinan_ajax()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required');
		$this->form_validation->set_rules('absence_type', 'Tipe izin', 'required');
		if ($this->input->post('absence_type') != '0|2001|5000') {
			$this->form_validation->set_rules('start_date', 'Tanggal mulai izin', 'required');
			$this->form_validation->set_rules('start_time', 'Waktu mulai izin', 'required');
		}
		$this->form_validation->set_rules('end_date', 'Tanggal akhir izin', 'required');
		$this->form_validation->set_rules('end_time', 'Waktu akhir izin', 'required');
		$this->form_validation->set_rules('approval_1_np', 'Approver 1', 'required|exact_length[4]|alpha_numeric');
		$this->form_validation->set_rules('approval_1_input', 'Approver 1', 'required');
		$this->form_validation->set_rules('approval_1_input_jabatan', 'Approver 1', 'required');

		if ($this->input->post('absence_type') == 'H|2001|5030' || $this->input->post('absence_type') == 'D|2001|5040' || $this->input->post('absence_type') == 'C|2001|5040' || $this->input->post('absence_type') == 'F|2001|5020' || $this->input->post('absence_type') == 'E|2001|5010') {
			$this->form_validation->set_rules('approval_2_np', 'Approver 2', 'required|exact_length[4]|alpha_numeric');
			$this->form_validation->set_rules('approval_2_input', 'Approver 2', 'required');
			$this->form_validation->set_rules('approval_2_input_jabatan', 'Approver 2', 'required');
		}

		$this->form_validation->set_rules('pos[]', 'Pos', 'required');
		$this->form_validation->set_rules('alasan', 'Alasan', 'required');

		$status = false;
		$message = '';

		if (@$this->input->post('start_date', true) == NULL) {
			$startDate = @$this->input->post('end_date', true);
		} else {
			$startDate = @$this->input->post('start_date', true);
		}

		if ($this->form_validation->run() == FALSE) {
			$status = false;
			$message = "Data Belum Lengkap <br>" . validation_errors();
			//robi j971 20-nov-2023 hide backdate
			// } else if( $startDate < date('d-m-Y') || @$this->input->post('end_date',true) < date('d-m-Y') ){
			// 	$status = false;
			// if( $startDate < date('d-m-Y') ) $message = "Start Date tidak bisa back date.";
			// else if( @$this->input->post('end_date',true) < date('d-m-Y') ) $message = "End Date tidak bisa back date.";
		} else {
			$data_insert = [];
			// $submit = $this->input->post('submit');
			// if($submit){
			$absence_type = $this->input->post('absence_type');
			$explode = explode('|', $absence_type);
			$info_type = $explode[1];
			$absence_type = $explode[2];
			$kode_pamlek = $explode[0];

			$np_karyawan		= $this->input->post('np_karyawan');
			$start_date			= @$this->input->post('start_date') ? date('Y-m-d', strtotime($this->input->post('start_date'))) : null;
			$start_time			= @$this->input->post('start_time') ? $this->input->post('start_time') : null;
			$end_date			= date('Y-m-d', strtotime($this->input->post('end_date')));
			$end_time			= $this->input->post('end_time');
			$approval_1_np		= $this->input->post('approval_1_np');
			$approval_1_nama	= $this->input->post('approval_1_input');
			$approval_1_jabatan	= $this->input->post('approval_1_input_jabatan');
			$approval_2_np		= (@$this->input->post('approval_2_np') != '') ? $this->input->post('approval_2_np') : null;
			$approval_2_nama	= (@$this->input->post('approval_2_input') != '') ? $this->input->post('approval_2_input') : null;
			$approval_2_jabatan	= (@$this->input->post('approval_2_input_jabatan') != '') ? $this->input->post('approval_2_input_jabatan') : null;
			$pos				= json_encode($this->input->post('pos'));

			$start_date_time = ($kode_pamlek == '0' ? null : date('Y-m-d H:i', strtotime($start_date . ' ' . $start_time)));
			$end_date_time = date('Y-m-d H:i', strtotime($end_date . ' ' . $end_time));
			$tahun_bulan     = $start_date != null ? str_replace('-', '_', substr("$start_date", 0, 7)) : str_replace('-', '_', substr("$end_date", 0, 7));

			$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
			$personel_number	= erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'];
			$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
			$kode_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'];
			$nama_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'];

			$this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$tahun_bulan LIKE ess_perizinan");

			$data_insert = [
				'np_karyawan' => $np_karyawan,
				'nama' => $nama_karyawan,
				'personel_number' => $personel_number,
				'nama_jabatan' => $nama_jabatan,
				'kode_unit' => $kode_unit,
				'nama_unit' => $nama_unit,
				'info_type' => $info_type,
				'absence_type' => $absence_type,
				'kode_pamlek' => $kode_pamlek,
				'start_date' => $start_date,
				'start_time' => $start_time,
				'end_date' => $end_date,
				'end_time' => $end_time,
				'start_date_input' => $start_date_time,
				'end_date_input' => $end_date_time,
				'approval_1_np' => $approval_1_np,
				'approval_1_nama' => $approval_1_nama,
				'approval_1_jabatan' => $approval_1_jabatan,
				'approval_2_np' => $approval_2_np,
				'approval_2_nama' => $approval_2_nama,
				'approval_2_jabatan' => $approval_2_jabatan,
				'end_time' => $end_time,
				'machine_id_start' => 'ess',
				'machine_id_end' => 'ess',
				'pos' => $pos,
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => $_SESSION['no_pokok'],
				'alasan' => trim($this->input->post('alasan', true)) # tambahan untuk alasan, 2021-03-10
			];

			$cek_izin = $this->db
				->where('np_karyawan', $data_insert['np_karyawan'])
				->where('date_batal IS NULL', null, false)
				->where("(
	                                CASE 
	                                    WHEN approval_2_np IS NOT NULL THEN (approval_1_status IS NULL OR approval_2_status IS NULL)
	                                    WHEN approval_2_np IS NULL THEN approval_1_status IS NULL
	                                END
	                            )")
				->get('ess_request_perizinan');

			if ($np_karyawan == '' || $np_karyawan == null) {
				$message = "NP Karyawan <b>$np_karyawan</b> tidak ditemukan.";
				echo json_encode([
					'status' => $status,
					'message' => $message
				]);
				exit;
			} else if (($start_date == '' || $start_date == null) && $kode_pamlek != '0') {
				$message = "Start date tidak boleh kosong.";
				echo json_encode([
					'status' => $status,
					'message' => $message
				]);
				exit;
			} else if ($end_date == '' || $end_date == null) {
				$message = "End date tidak boleh kosong.";
				echo json_encode([
					'status' => $status,
					'message' => $message
				]);
				exit;
			} else if ($start_date_time >= $end_date_time) {
				$message = "Tanggal Akhir Perizinan harus lebih besar dari Tanggal Mulai Perizinan.";
				echo json_encode([
					'status' => $status,
					'message' => $message
				]);
				exit;
			} else if ($cek_izin->num_rows() > 0) {
				# 2021-04-01, tambahan untuk filter izin yg belum diapprove
				$array_pending = [];
				$count_pending = 0;
				foreach ($cek_izin->result() as $row) {
					$tanggal_pending = $row->start_date != null ? tanggal_indonesia($row->start_date) . ' - ' : '';
					$tanggal_pending .= $row->end_date != null ? tanggal_indonesia($row->end_date) : '';
					if ($row->approval_1_status == '2' || $row->approval_2_status == '2') {
						# do nothing
					} else {
						if ($row->approval_2_np != null) {
							if ($row->approval_1_status == null) {
								$array_pending[] = "<br>Tanggal {$tanggal_pending} Belum Diapprove Atasan 1.";
								$count_pending++;
							} else {
								if ($row->approval_1_status == '1' && $row->approval_2_status == null) {
									$array_pending[] = "<br>Tanggal {$tanggal_pending} Belum Diapprove Atasan 2.";
									$count_pending++;
								}
							}
						} else {
							if ($row->approval_1_status == null) {
								$array_pending[] = "<br>Tanggal {$tanggal_pending} Belum Diapprove Atasan 1.";
								$count_pending++;
							}
						}
					}
				}

				if ($count_pending > 0) {
					$message = "Permohonan Perizinan Terakhir Belum Diapprove Oleh Atasan/Keamanan." . implode(" ", $array_pending);
					echo json_encode([
						'status' => $status,
						'message' => $message
					]);
					exit;
				}
			}

			$start_date_time_validasi = ($start_date_time == null ? $end_date_time : $start_date_time);
			$cek = $this->db->query("SELECT * 
	                FROM ess_perizinan_$tahun_bulan
	                WHERE np_karyawan='$np_karyawan' 
	                AND (
	                    ('$start_date_time_validasi' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
	                    OR ('$end_date_time' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
	                    OR (DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time_validasi' AND '$end_date_time')
	                    OR (DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time_validasi' AND '$end_date_time')
	                ) ");

			if (1 == 2) {
				$message = "Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time sudah ada.";
			} else {
				$this->db->insert("ess_request_perizinan", $data_insert);
				$status = true;
				$message = "Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time berhasil ditambahkan.";
			}
			// } else{
			//     $message = "Terjadi Kesalahan";
			// }
		}
		echo json_encode([
			'status' => $status,
			'message' => $message
		]);
	}

	function action_insert_perizinan_sidt($data)
	{
		/*$this->load->helper('form');
			$this->load->library('form_validation');
			
        	$this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required');
	    	$this->form_validation->set_rules('absence_type', 'Tipe izin', 'required');
	    	$this->form_validation->set_rules('start_date', 'Tanggal mulai izin', 'required');
	    	$this->form_validation->set_rules('start_time', 'Waktu mulai izin', 'required');
	    	$this->form_validation->set_rules('end_date', 'Tanggal akhir izin', 'required');
	    	$this->form_validation->set_rules('end_time', 'Waktu akhir izin', 'required');
	    	$this->form_validation->set_rules('approval_1_np', 'Approver 1', 'required');
	    	$this->form_validation->set_rules('approval_1_input', 'Approver 1', 'required');
	    	$this->form_validation->set_rules('approval_1_input_jabatan', 'Approver 1', 'required');

	    	if ($this->input->post('absence_type')=='H|2001|5030' || $this->input->post('absence_type')=='D|2001|5040' || $this->input->post('absence_type')=='C|2001|5040' || $this->input->post('absence_type')=='F|2001|5020' || $this->input->post('absence_type')=='E|2001|5010') {
		    	$this->form_validation->set_rules('approval_2_np', 'Approver 2', 'required');
		    	$this->form_validation->set_rules('approval_2_input', 'Approver 2', 'required');
		    	$this->form_validation->set_rules('approval_2_input_jabatan', 'Approver 2', 'required');
	    	}

	    	$this->form_validation->set_rules('pos', 'Pos', 'required');
	    	$this->form_validation->set_rules('alasan', 'Alasan', 'required');

        	if ($this->form_validation->run() == FALSE) {
	        	$this->session->set_flashdata('warning', 'Data Belum Lengkap');
	            redirect(base_url('perizinan/permohonan_perizinan'));
        	} else {*/
		$data_insert = [];
		$submit = $data['submit'];
		if ($submit) {
			$absence_type		= $data['absence_type'];
			$explode = explode('|', $absence_type);
			$info_type = $explode[1];
			$absence_type = $explode[2];
			$kode_pamlek = $explode[0];

			$np_karyawan		= $data['np_karyawan'];

			$cek_izin = $this->db
				->where('np_karyawan', $data_insert['np_karyawan'])
				->where('date_batal IS NULL', null, false)
				->where("(
	                                CASE 
	                                    WHEN approval_2_np IS NOT NULL THEN (approval_1_status IS NULL OR approval_2_status IS NULL)
	                                    WHEN approval_2_np IS NULL THEN approval_1_status IS NULL
	                                END
	                            )")
				->get('ess_request_perizinan');

			if ($np_karyawan == '' || $np_karyawan == null) {
				$this->session->set_flashdata('warning', "NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($data['end_date'] == '' || $data['end_date'] == null) {
				$this->session->set_flashdata('warning', "End date tidak boleh kosong.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($data['absence_type'] == '' || $data['absence_type'] == null) {
				$this->session->set_flashdata('warning', "Jenis perizinan tidak boleh kosong.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($cek_izin->num_rows() > 0) {
				# 2021-04-01, tambahan untuk filter izin yg belum diapprove
				$count_pending = 0;
				foreach ($cek_izin->result() as $row) {
					if ($row->approval_1_status == '2' || $row->approval_2_status == '2') {
						# do nothing
						continue;
					} else {
						if ($row->approval_2_np != null) {
							if ($row->approval_1_status == null)
								$count_pending++;
							else {
								if ($row->approval_1_status == '1' && $row->approval_2_status == null)
									$count_pending++;
							}
						} else {
							if ($row->approval_1_status == null)
								$count_pending++;
						}
					}
				}

				if ($count_pending > 0) {
					$this->session->set_flashdata('warning', "Permohonan Perizinan Terakhir Belum Diapprove Oleh Atasan/Keamanan.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				}
			}

			//$start_date			= date('Y-m-d',strtotime($this->input->post('start_date')));
			//$start_time			= $this->input->post('start_time');
			$end_date			= date('Y-m-d', strtotime($data['end_date']));
			$end_time			= $data['end_time'];


			/*$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $end_date)['nama'];
					$personel_number	= erp_master_data_by_np($np_karyawan, $end_date)['personnel_number'];
					$nama_jabatan		= erp_master_data_by_np($np_karyawan, $end_date)['nama_jabatan'];
					$kode_unit 			= erp_master_data_by_np($np_karyawan, $end_date)['kode_unit'];
					$nama_unit 			= erp_master_data_by_np($np_karyawan, $end_date)['nama_unit'];*/



			$tahun_bulan     	= str_replace('-', '_', substr("$end_date", 0, 7));

			$start_date			= date('Y-m-d', strtotime($data['start_date']));
			$start_time			= $data['start_time'];
			$end_date			= date('Y-m-d', strtotime($data['end_date']));
			$end_time			= $data['end_time'];
			$end_date_time 		= date('Y-m-d H:i', strtotime($end_date . ' ' . $end_time));
			$approval_1_np		= $data['approval_1_np'];
			$approval_1_nama	= $data['approval_1_input'];
			$approval_1_jabatan	= $data['approval_1_input_jabatan'];
			$approval_2_np		= (@$data['approval_2_np'] != '') ? $data['approval_2_np'] : null;
			$approval_2_nama	= (@$data['approval_2_input'] != '') ? $data['approval_2_input'] : null;
			$approval_2_jabatan	= (@$data['approval_2_input_jabatan'] != '') ? $data['approval_2_input_jabatan'] : null;
			$pos				= json_encode($data['pos']);

			$start_date_time = date('Y-m-d H:i', strtotime($start_date . ' ' . $start_time));
			$end_date_time = date('Y-m-d H:i', strtotime($end_date . ' ' . $end_time));
			// $tahun_bulan     = str_replace('-','_',substr("$start_date", 0, 7));

			$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
			$personel_number	= erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'];
			$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
			$kode_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'];
			$nama_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'];

			$data_insert = [
				'np_karyawan' => $np_karyawan,
				'nama' => $nama_karyawan,
				'personel_number' => $personel_number,
				'nama_jabatan' => $nama_jabatan,
				'kode_unit' => $kode_unit,
				'nama_unit' => $nama_unit,
				'info_type' => $info_type,
				'absence_type' => $absence_type,
				'kode_pamlek' => $kode_pamlek,
				// 'start_date'=>$start_date,
				// 'start_time'=>$start_time,
				'end_date' => $end_date,
				'end_time' => $end_time,
				// 'start_date_input'=>$start_date_time,
				'end_date_input' => $end_date_time,
				'approval_1_np' => $approval_1_np,
				'approval_1_nama' => $approval_1_nama,
				'approval_2_np' => $approval_2_np,
				'approval_2_nama' => $approval_2_nama,
				'end_time' => $end_time,
				'machine_id_start' => 'ess',
				'machine_id_end' => 'ess',
				'pos' => $pos,
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => $_SESSION['no_pokok'],
				'alasan' => trim($this->input->post('alasan', true)) # tambahan untuk alasan, 2021-03-10
			];


			// Dikomen wina perizinan digital
			/*$data_insert = [
	                    'np_karyawan'=>$np_karyawan,
	                    'nama'=>$nama_karyawan,
	                    'personel_number'=>$personel_number,
	                    'nama_jabatan'=>$nama_jabatan,
	                    'kode_unit'=>$kode_unit,
	                    'nama_unit'=>$nama_unit,
	                    'info_type'=>$info_type,
	                    'absence_type'=>$absence_type,
	                    'kode_pamlek'=>$kode_pamlek,
	                    //'start_date'=>$start_date,
	                    //'start_time'=>$start_time,
	                    'end_date'=>$end_date,
	                    'end_time'=>$end_time
	                ];*/


			// Dikomen wina perizinan digital
			// $this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$tahun_bulan LIKE ess_perizinan");

			//cek table exist
			// Dikomen wina perizinan digital
			/*$get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
	                if($get_table->num_rows()==0){
	                    $this->session->set_flashdata('warning',"Data kehadiran belum tersedia.");
						redirect(base_url($this->folder_controller.'permohonan_perizinan'));
	                }
	                */

			$cek_perizinan = $this->db->where(['np_karyawan' => $np_karyawan, 'end_date' => $end_date, 'kode_pamlek' => $kode_pamlek])->get("ess_request_perizinan");
			$cek_cico = $this->db->select("(CASE WHEN dws_in_fix is not null then dws_in_fix ELSE dws_in END) as dws_in_time,(CASE WHEN dws_out_fix is not null then dws_out_fix ELSE dws_out END) as dws_out_time")->where(['np_karyawan' => $np_karyawan, 'dws_tanggal' => $end_date])->get("ess_cico_$tahun_bulan");

			if ($cek_perizinan->num_rows() > 0) {
				$this->session->set_flashdata('warning', "Data perizinan SIDT dengan nama $nama_karyawan, pada tanggal $end_date sudah ada.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($cek_cico->num_rows() == 0) {
				$this->session->set_flashdata('warning', "Data kehadiran belum tersedia.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else {
				$cek_jam = $this->db->select("MIN(start_time) AS min_start_time")->where(['np_karyawan' => $np_karyawan, 'kode_pamlek!=' => '0', 'start_date' => $end_date])->get("ess_request_perizinan")->row();

				if (@$cek_jam->min_start_time != NULL) {
					if (date('H:i:s', strtotime($end_time)) > $cek_jam->min_start_time) {
						$this->session->set_flashdata('warning', "Waktu SIDT tidak boleh melebihi waktu perizinan lain.");
						redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
					}
				} else if ($cek_cico->num_rows() > 0) {
					if ((date('H:i:s', strtotime($end_time)) < $cek_cico->row()->dws_in_time) || (date('H:i:s', strtotime($end_time)) > $cek_cico->row()->dws_out_time)) {
						$this->session->set_flashdata('warning', "Waktu SIDT harus di antara DWS IN dan DWS OUT.");
						redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
					}
				}

				$this->db->insert("ess_request_perizinan", $data_insert);

				// Dikomen wina perizinan digital
				/*$parameter_perizinan = [
	                        'id_row_baru'=>$this->db->insert_id(),
	                        'np_karyawan'=>$np_karyawan,
	                        //'date_start'=>$start_date,
	                        'date_end'=>$end_date
	                    ];
	                    $this->update_cico_sidt($parameter_perizinan);*/

				$this->session->set_flashdata('success', "Data perizinan SIDT dengan nama $nama_karyawan, pada tanggal $end_date $end_time berhasil ditambahkan.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			}

			echo json_encode($data_insert);
		} else {
			$this->session->set_flashdata('warning', "Terjadi Kesalahan");
			redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
		}
		// }
	}

	function action_insert_perizinan_sidt_ajax($data)
	{
		$data_insert = [];
		$submit = $data['submit'];
		if ($submit) {
			$absence_type		= $data['absence_type'];
			$explode = explode('|', $absence_type);
			$info_type = $explode[1];
			$absence_type = $explode[2];
			$kode_pamlek = $explode[0];

			$np_karyawan		= $data['np_karyawan'];

			$cek_izin = $this->db
				->where('np_karyawan', $data_insert['np_karyawan'])
				->where('date_batal IS NULL', null, false)
				->where("(
								CASE 
									WHEN approval_2_np IS NOT NULL THEN (approval_1_status IS NULL OR approval_2_status IS NULL)
									WHEN approval_2_np IS NULL THEN approval_1_status IS NULL
								END
							)")
				->get('ess_request_perizinan');

			if ($np_karyawan == '' || $np_karyawan == null) {
				$this->session->set_flashdata('warning', "NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($data['end_date'] == '' || $data['end_date'] == null) {
				$this->session->set_flashdata('warning', "End date tidak boleh kosong.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($data['absence_type'] == '' || $data['absence_type'] == null) {
				$this->session->set_flashdata('warning', "Jenis perizinan tidak boleh kosong.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($cek_izin->num_rows() > 0) {
				# 2021-04-01, tambahan untuk filter izin yg belum diapprove
				$count_pending = 0;
				foreach ($cek_izin->result() as $row) {
					if ($row->approval_1_status == '2' || $row->approval_2_status == '2') {
						# do nothing
						continue;
					} else {
						if ($row->approval_2_np != null) {
							if ($row->approval_1_status == null)
								$count_pending++;
							else {
								if ($row->approval_1_status == '1' && $row->approval_2_status == null)
									$count_pending++;
							}
						} else {
							if ($row->approval_1_status == null)
								$count_pending++;
						}
					}
				}

				if ($count_pending > 0) {
					$this->session->set_flashdata('warning', "Permohonan Perizinan Terakhir Belum Diapprove Oleh Atasan/Keamanan.");
					redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
				}
			}

			$end_date			= date('Y-m-d', strtotime($data['end_date']));
			$end_time			= $data['end_time'];

			$tahun_bulan     	= str_replace('-', '_', substr("$end_date", 0, 7));

			$start_date			= date('Y-m-d', strtotime($data['start_date']));
			$start_time			= $data['start_time'];
			$end_date			= date('Y-m-d', strtotime($data['end_date']));
			$end_time			= $data['end_time'];
			$end_date_time 		= date('Y-m-d H:i', strtotime($end_date . ' ' . $end_time));
			$approval_1_np		= $data['approval_1_np'];
			$approval_1_nama	= $data['approval_1_input'];
			$approval_1_jabatan	= $data['approval_1_input_jabatan'];
			$approval_2_np		= (@$data['approval_2_np'] != '') ? $data['approval_2_np'] : null;
			$approval_2_nama	= (@$data['approval_2_input'] != '') ? $data['approval_2_input'] : null;
			$approval_2_jabatan	= (@$data['approval_2_input_jabatan'] != '') ? $data['approval_2_input_jabatan'] : null;
			$pos				= json_encode($data['pos']);

			$start_date_time = date('Y-m-d H:i', strtotime($start_date . ' ' . $start_time));
			$end_date_time = date('Y-m-d H:i', strtotime($end_date . ' ' . $end_time));

			$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
			$personel_number	= erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'];
			$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
			$kode_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'];
			$nama_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'];

			$data_insert = [
				'np_karyawan' => $np_karyawan,
				'nama' => $nama_karyawan,
				'personel_number' => $personel_number,
				'nama_jabatan' => $nama_jabatan,
				'kode_unit' => $kode_unit,
				'nama_unit' => $nama_unit,
				'info_type' => $info_type,
				'absence_type' => $absence_type,
				'kode_pamlek' => $kode_pamlek,
				'end_date' => $end_date,
				'end_time' => $end_time,
				'end_date_input' => $end_date_time,
				'approval_1_np' => $approval_1_np,
				'approval_1_nama' => $approval_1_nama,
				'approval_2_np' => $approval_2_np,
				'approval_2_nama' => $approval_2_nama,
				'end_time' => $end_time,
				'machine_id_start' => 'ess',
				'machine_id_end' => 'ess',
				'pos' => $pos,
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => $_SESSION['no_pokok'],
				'alasan' => trim($this->input->post('alasan', true)) # tambahan untuk alasan, 2021-03-10
			];

			$cek_perizinan = $this->db->where(['np_karyawan' => $np_karyawan, 'end_date' => $end_date, 'kode_pamlek' => $kode_pamlek])->get("ess_request_perizinan");
			$cek_cico = $this->db->select("(CASE WHEN dws_in_fix is not null then dws_in_fix ELSE dws_in END) as dws_in_time,(CASE WHEN dws_out_fix is not null then dws_out_fix ELSE dws_out END) as dws_out_time")->where(['np_karyawan' => $np_karyawan, 'dws_tanggal' => $end_date])->get("ess_cico_$tahun_bulan");

			if ($cek_perizinan->num_rows() > 0) {
				$this->session->set_flashdata('warning', "Data perizinan SIDT dengan nama $nama_karyawan, pada tanggal $end_date sudah ada.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else if ($cek_cico->num_rows() == 0) {
				$this->session->set_flashdata('warning', "Data kehadiran belum tersedia.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			} else {
				$cek_jam = $this->db->select("MIN(start_time) AS min_start_time")->where(['np_karyawan' => $np_karyawan, 'kode_pamlek!=' => '0', 'start_date' => $end_date])->get("ess_request_perizinan")->row();

				if (@$cek_jam->min_start_time != NULL) {
					if (date('H:i:s', strtotime($end_time)) > $cek_jam->min_start_time) {
						$this->session->set_flashdata('warning', "Waktu SIDT tidak boleh melebihi waktu perizinan lain.");
						redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
					}
				} else if ($cek_cico->num_rows() > 0) {
					if ((date('H:i:s', strtotime($end_time)) < $cek_cico->row()->dws_in_time) || (date('H:i:s', strtotime($end_time)) > $cek_cico->row()->dws_out_time)) {
						$this->session->set_flashdata('warning', "Waktu SIDT harus di antara DWS IN dan DWS OUT.");
						redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
					}
				}

				$this->db->insert("ess_request_perizinan", $data_insert);

				$this->session->set_flashdata('success', "Data perizinan SIDT dengan nama $nama_karyawan, pada tanggal $end_date $end_time berhasil ditambahkan.");
				redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
			}

			echo json_encode($data_insert);
		} else {
			$this->session->set_flashdata('warning', "Terjadi Kesalahan");
			redirect(base_url($this->folder_controller . 'permohonan_perizinan'));
		}
	}

	public function get_NP($np_atasan, $izin)
	{
		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$np_karyawan = $this->input->post('np_karyawan');
			$kode_unit = array(kode_unit_by_np($np_karyawan));
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$np_karyawan = $_SESSION["no_pokok"];
			$kode_unit = array($_SESSION["kode_unit"]);
		} else {
			$np_karyawan = $this->input->post('np_karyawan');
			$kode_unit = array(kode_unit_by_np($np_karyawan));
		}

		$return = [
			'status' => false,
			'data' => [],
			'message' => 'Silahkan isi No. Pokok Atasan ' . $atasan . ' Dengan Benar',
		];

		if ($np_atasan == $np_karyawan) {
			$return['message'] = 'No. Pokok Approver Tidak Valid';
		} else {
			$this->load->model('m_approval');
			if ($atasan == '1') {
				if ($izin != 'E|2001|5010') {
					$list = $this->m_approval->list_atasan_minimal_kasek($kode_unit, $np_karyawan);
					$return['message'] = 'Approval 1 Perizinan Minimal Kasek';
				} else {
					$list = $this->m_approval->list_atasan_minimal_kadep($kode_unit, $np_karyawan);
					$return['message'] = 'Perizinan Izin Probadi Tanpa Potongan Approval 1 Minimal Kadep';
				}
			} else if ($atasan == '2') {
				if ($izin != 'E|2001|5010') {
					$list = $this->m_approval->list_atasan_minimal_kadep($kode_unit, $np_karyawan);
					$return['message'] = 'Approval 2 Perizinan Minimal Kadep';
				} else {
					// echo $atasan;exit;
					$list = $this->m_approval->list_atasan_minimal_kadiv($kode_unit, $np_karyawan);
					$return['message'] = 'Perizinan Izin Probadi Tanpa Potongan Approval 2 Minimal Kadiv';
				}
			}

			$list_np = array_column($list, 'no_pokok');
			if (in_array($np_atasan, $list_np)) {
				$key = array_search($np_atasan, $list_np);
				$data['nama'] = $list[$key]['nama'];
				$data['nama_jabatan'] = $list[$key]['nama_jabatan'];
			}

			if (@$data) {
				$return = [
					'status' => true,
					'data' => [
						'nama' => $data['nama'],
						'jabatan' => $data['nama_jabatan']
					]
				];
			}
		}

		return json_encode($return);
	}

	public function hapus($id = null, $np = null, $tanggal_end = null, $tanggal_start = null)
	{
		$this->load->model($this->folder_model . "M_permohonan_perizinan");
		$request = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_request_perizinan');
		//echo 'under maintenance.'; exit();
		if (@$id != null && @$np != null && @$tanggal_start != null && @$tanggal_end != null) {
			/*$tanggal_proses = $tanggal_start;
                while($tanggal_proses <= $tanggal_end){
                    $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
                    $cek = $this->db->query("SELECT TABLE_NAME FROM information_schema.`TABLES` WHERE table_schema='$this->nama_db' AND TABLE_NAME='ess_cico_$tahun_bulan'");
                    
                    if($cek->num_rows()>0){
                        $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $np)->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                        
                        if($get_cico->num_rows()>0){
                            $str_fix = '';
                            $row = $get_cico->row_array();
                            
                            //str awal diambil dari id_perizinan di cico
                            $str_awal = $row['id_perizinan'];
                            //convert str_awal to array_awal
                            $arr_awal = explode(',', $str_awal);

                            //concat dari id tabel perizinan
                            $str_datang = $id;

                            if (($key = array_search($str_datang, $arr_awal)) !== false) {
                                unset($arr_awal[$key]);
                                $arr_awal = array_values($arr_awal);
                            }

                            //convert arr_awal to str
                            $str_awal = implode(',', $arr_awal);
                            $str_fix = trim($str_awal,',');

                            $this->db->where('id', $row['id'])->update('ess_cico_'.$tahun_bulan, ['id_perizinan'=>$str_fix]);
                        }
                    }
                    
                    $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
                }*/

			// cek status approval
			if (($request['approval_1_status'] == null && $request['approval_2_status'] == null && $request['approval_pengamanan_np'] == null)) {
				// continue
			} else{
				$this->session->set_flashdata('warning', 'Perizinan sudah disetujui dan tidak bisa dihapus.');
				redirect(base_url('perizinan/permohonan_perizinan'));
			}

			// $tahun_bulan_perizinan = str_replace('-','_',substr("$tanggal_start", 0, 7));
			$tahun_bulan_perizinan = 'request';
			// $get = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_perizinan_'.$tahun_bulan_perizinan);
			$get = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_request_perizinan');


			// 05 04 2021, Tri Wibowo 7648, Hapus nya di request_perizinan
			//if($this->db->where('id', $id)->delete("ess_perizinan_$tahun_bulan_perizinan")){
			if ($this->db->where('id', $id)->delete("ess_request_perizinan")) {
				$return["status"] = true;

				$log_data_lama = "";
				foreach ($get as $key => $value) {
					if (strcmp($key, "id") != 0) {
						if (!empty($log_data_lama)) {
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $get["id"],
					"deskripsi" => "hapus " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => '',
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
				$this->session->set_flashdata('success', 'Perizinan berhasil dihapus.');
			}
		} else if (@$id != null && @$np != null && @$tanggal_end != null && (@$tanggal_start == NULL || @$tangal_start == '')) {
			// cek status approval
			if (($request['approval_1_status'] == null && $request['approval_2_status'] == null && $request['approval_pengamanan_np'] == null)) {
				// continue
			} else{
				$this->session->set_flashdata('warning', 'Perizinan sudah disetujui dan tidak bisa dihapus.');
				redirect(base_url('perizinan/permohonan_perizinan'));
			}
			
			$tanggal_proses = $tanggal_end;
			//while($tanggal_proses <= $tanggal_end){
			$tahun_bulan = str_replace('-', '_', substr("$tanggal_proses", 0, 7));
			$cek = $this->db->query("SELECT TABLE_NAME FROM information_schema.`TABLES` WHERE table_schema='$this->nama_db' AND TABLE_NAME='ess_cico_$tahun_bulan'");

			if ($cek->num_rows() > 0) {
				$get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $np)->where('dws_tanggal', $tanggal_proses)->get('ess_cico_' . $tahun_bulan);

				if ($get_cico->num_rows() > 0) {
					$str_fix = '';
					$row = $get_cico->row_array();

					//str awal diambil dari id_perizinan di cico
					$str_awal = $row['id_perizinan'];
					//convert str_awal to array_awal
					$arr_awal = explode(',', $str_awal);

					//concat dari id tabel perizinan
					$str_datang = $id;

					if (($key = array_search($str_datang, $arr_awal)) !== false) {
						unset($arr_awal[$key]);
						$arr_awal = array_values($arr_awal);
					}

					//convert arr_awal to str
					$str_awal = implode(',', $arr_awal);
					$str_fix = trim($str_awal, ',');

					$this->db->where('id', $row['id'])->update('ess_cico_' . $tahun_bulan, ['id_perizinan' => $str_fix]);
				}
			}

			//$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
			//}

			// $tahun_bulan_perizinan = str_replace('-','_',substr("$tanggal_end", 0, 7));
			$tahun_bulan_perizinan = 'request';
			// $get = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_perizinan_'.$tahun_bulan_perizinan);
			$get = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_request_perizinan');
			//if($this->db->where('id', $id)->delete("ess_perizinan_$tahun_bulan_perizinan")){
			if ($this->db->where('id', $id)->delete("ess_request_perizinan")) {
				$return["status"] = true;

				$log_data_lama = "";
				foreach ($get as $key => $value) {
					if (strcmp($key, "id") != 0) {
						if (!empty($log_data_lama)) {
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $get["id"],
					"deskripsi" => "hapus " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => '',
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
				$this->session->set_flashdata('success', 'Perizinan berhasil dihapus.');
			}
		} else {
			$this->session->set_flashdata('warning', 'Data Perizinan <b>Gagal</b> Dihapus.');
		}
		redirect(base_url('perizinan/permohonan_perizinan'));
	}

	public function hapus_ajax()
	{
		$id = $this->input->post('id', true);
		$np = $this->input->post('np', true);
		$tanggal_start = $this->input->post('tanggal_start', true);
		$tanggal_end = $this->input->post('tanggal_end', true);
		$this->load->model($this->folder_model . "M_permohonan_perizinan");
		$status = false;
		$message = '';
		$request = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_request_perizinan');
		if (($request['approval_1_status'] == null && $request['approval_2_status'] == null && $request['approval_pengamanan_np'] == null && $request['id_perizinan'] == null)) {
			// continue
		} else{
			echo json_encode([
				'status' => false,
				'message' => 'Perizinan sudah disetujui dan tidak bisa dihapus.'
			]); exit;
		}

		if (@$id != null && @$np != null && @$tanggal_start != null && @$tanggal_end != null) {
			$tahun_bulan_perizinan = 'request';
			$get = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_request_perizinan');
			if ($this->db->where('id', $id)->delete("ess_request_perizinan")) {
				$status = true;

				$log_data_lama = "";
				foreach ($get as $key => $value) {
					if (strcmp($key, "id") != 0) {
						if (!empty($log_data_lama)) {
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $get["id"],
					"deskripsi" => "hapus " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => '',
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
				$message = 'Perizinan berhasil dihapus.';
			}
		} else if (@$id != null && @$np != null && @$tanggal_end != null && (@$tanggal_start == NULL || @$tangal_start == '')) {
			$tanggal_proses = $tanggal_end;
			$tahun_bulan = str_replace('-', '_', substr("$tanggal_proses", 0, 7));
			$cek = $this->db->query("SELECT TABLE_NAME FROM information_schema.`TABLES` WHERE table_schema='$this->nama_db' AND TABLE_NAME='ess_cico_$tahun_bulan'");

			if ($cek->num_rows() > 0) {
				$get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $np)->where('dws_tanggal', $tanggal_proses)->get('ess_cico_' . $tahun_bulan);

				if ($get_cico->num_rows() > 0) {
					$str_fix = '';
					$row = $get_cico->row_array();

					//str awal diambil dari id_perizinan di cico
					$str_awal = $row['id_perizinan'];
					//convert str_awal to array_awal
					$arr_awal = explode(',', $str_awal);

					//concat dari id tabel perizinan
					$str_datang = $id;

					if (($key = array_search($str_datang, $arr_awal)) !== false) {
						unset($arr_awal[$key]);
						$arr_awal = array_values($arr_awal);
					}

					//convert arr_awal to str
					$str_awal = implode(',', $arr_awal);
					$str_fix = trim($str_awal, ',');

					$this->db->where('id', $row['id'])->update('ess_cico_' . $tahun_bulan, ['id_perizinan' => $str_fix]);
				}
			}

			$tahun_bulan_perizinan = 'request';
			$get = $this->M_permohonan_perizinan->ambil_perizinan_id($id, 'ess_request_perizinan');
			if ($this->db->where('id', $id)->delete("ess_request_perizinan")) {
				$status = true;

				$log_data_lama = "";
				foreach ($get as $key => $value) {
					if (strcmp($key, "id") != 0) {
						if (!empty($log_data_lama)) {
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $get["id"],
					"deskripsi" => "hapus " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => '',
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
				$message = 'Perizinan berhasil dihapus.';
			}
		} else {
			$status = false;
			$message = 'Data Perizinan Gagal Dihapus.';
		}
		echo json_encode([
			'status' => $status,
			'message' => $message
		]);
	}

	function update_cico($data_lempar)
	{
		$tanggal_proses = $data_lempar['date_start'];

		while ($tanggal_proses <= $data_lempar['date_end']) {
			$tahun_bulan = str_replace('-', '_', substr("$tanggal_proses", 0, 7));

			//cek table exist
			$get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_' . $tahun_bulan)->get('information_schema.`TABLES`');
			if ($get_table->num_rows() > 0) {
				//get cico
				$get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_' . $tahun_bulan);

				if ($get_cico->num_rows() > 0) {
					$data_to_process = array();
					$row = $get_cico->row_array();

					$data_to_process = [
						'id' => $row['id'],
						'id_perizinan' => $row['id_perizinan'],
						'tahun_bulan' => str_replace('-', '_', substr($row['dws_tanggal'], 0, 7)),
						'id_row_baru' => $data_lempar['id_row_baru']
					];
					$this->process_update_cico($data_to_process);
				}
			}

			$tanggal_proses = date("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
		}
	}

	function update_cico_sidt($data_lempar)
	{
		$tanggal_proses = $data_lempar['date_end'];

		//while($tanggal_proses <= $data_lempar['date_end']){
		$tahun_bulan = str_replace('-', '_', substr("$tanggal_proses", 0, 7));

		//cek table exist
		$get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_' . $tahun_bulan)->get('information_schema.`TABLES`');
		if ($get_table->num_rows() > 0) {
			//get cico
			$get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_' . $tahun_bulan);

			if ($get_cico->num_rows() > 0) {
				$data_to_process = array();
				$row = $get_cico->row_array();

				$data_to_process = [
					'id' => $row['id'],
					'id_perizinan' => $row['id_perizinan'],
					'tahun_bulan' => str_replace('-', '_', substr($row['dws_tanggal'], 0, 7)),
					'id_row_baru' => $data_lempar['id_row_baru']
				];
				$this->process_update_cico($data_to_process);
			}
		}

		//$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
		//}
	}

	function process_update_cico($data_lempar)
	{
		//$data_cico = $get_cico->row();
		$str_fix = '';
		$new_element = [];

		//str awal diambil dari id_perizinan di cico
		$str_awal = $data_lempar['id_perizinan'];
		//convert str_awal to array_awal
		$arr_awal = explode(',', $str_awal);

		//concat dari id tabel perizinan
		$str_datang = $data_lempar['id_row_baru'];
		//convert str_datang to array_datang
		$arr_datang = explode(',', $str_datang);

		//found elements of arr_datang where not in arr_awal
		$new_elements = array_diff($arr_datang, $arr_awal);

		foreach ($new_elements as $value) {
			//push new element to arr_awal
			$arr_awal[] = $value;
		}

		//convert arr_awal to str
		$str_awal = implode(',', $arr_awal);
		$str_fix = trim($str_awal, ',');

		$this->db->where('id', $data_lempar['id'])->update('ess_cico_' . $data_lempar['tahun_bulan'], ['id_perizinan' => $str_fix]);
	}

	public function ajax_getNama_approval($atasan = null)
	{
		$np_atasan = $this->input->post('np_aprover');
		$izin = $this->input->post('izin');

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$np_karyawan = $this->input->post('np_karyawan');
			$kode_unit = array(kode_unit_by_np($np_karyawan));
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$np_karyawan = $_SESSION["no_pokok"];
			$kode_unit = array($_SESSION["kode_unit"]);
		} else {
			$np_karyawan = $this->input->post('np_karyawan');
			$kode_unit = array(kode_unit_by_np($np_karyawan));
		}

		$return = [
			'status' => false,
			'data' => [],
			'message' => 'Silahkan isi No. Pokok Atasan ' . $atasan . ' Dengan Benar',
		];

		if ($np_atasan == $np_karyawan) {
			$return['message'] = 'No. Pokok Approver Tidak Valid';
		} else {
			$this->load->model('m_approval');

			/*
                2021-03-19, bowo/wina: kembalikan ke awal
                */

			if ($atasan == '1') {
				if ($izin == 'SIPK|2001|5030') {
					$list = $this->m_approval->list_atasan_minimal_kaun($kode_unit, $np_karyawan);
					$return['message'] = 'Approval 1 Perizinan Minimal Kaun';
				} else if ($izin != 'E|2001|5010') {
					$list = $this->m_approval->list_atasan_minimal_kasek($kode_unit, $np_karyawan);
					$return['message'] = 'Approval 1 Perizinan Minimal Kasek';
				} else {
					$list = $this->m_approval->list_atasan_minimal_kadep($kode_unit, $np_karyawan);
					$return['message'] = 'Perizinan Izin Probadi Tanpa Potongan Approval 1 Minimal Kadep';
				}
			} else if ($atasan == '2') {
				if ($izin != 'E|2001|5010') {
					$list = $this->m_approval->list_atasan_minimal_kadep($kode_unit, $np_karyawan);
					$return['message'] = 'Approval 2 Perizinan Minimal Kadep';
				} else {
					// echo $atasan;exit;
					$list = $this->m_approval->list_atasan_minimal_kadiv($kode_unit, $np_karyawan);
					$return['message'] = 'Perizinan Izin Probadi Tanpa Potongan Approval 2 Minimal Kadiv';
				}
			}

			# 2021-03-17 bowo/heru membuka list approval minimal kaun di semua unit
			/*if ($atasan=='1') {
                    $list = $this->m_approval->list_atasan_minimal_kaun_all_unit($kode_unit, $np_karyawan);
                    $return['message'] = 'No. Pokok Atasan 1 Minimal Kaun';
				} else if ($atasan=='2') {
                    $list = $this->m_approval->list_atasan_minimal_kaun_all_unit($kode_unit, $np_karyawan);
                    $return['message'] = 'No. Pokok Atasan 2 Minimal Kaun';
				}*/

			$list_np = array_column($list, 'no_pokok');
			if (in_array($np_atasan, $list_np)) {
				$key = array_search($np_atasan, $list_np);
				$data['nama'] = $list[$key]['nama'];
				$data['nama_jabatan'] = $list[$key]['nama_jabatan'];
			}

			if (@$data) {
				$return = [
					'status' => true,
					'data' => [
						'nama' => $data['nama'],
						'jabatan' => $data['nama_jabatan']
					]
				];
			}
		}

		echo json_encode($return);
	}

	public function cetak()
	{

		$this->load->library('phpexcel');
		$this->load->model($this->folder_model . "M_tabel_permohonan_perizinan");
		$set['np_karyawan'] = $this->input->post('np_karyawan');
		$jenis = array();
		$filter_bulan   = $this->input->post('bulan');
		if ($this->input->post('izin_D') == 1)
			$jenis[] = 'D';
		if ($this->input->post('izin_E') == 1)
			$jenis[] = 'E';
		if ($this->input->post('izin_F') == 1)
			$jenis[] = 'F';
		if ($this->input->post('izin_G') == 1)
			$jenis[] = 'G';
		if ($this->input->post('izin_H') == 1)
			$jenis[] = 'H';
		if ($this->input->post('izin_TM') == 1)
			$jenis[] = 'TM';
		if ($this->input->post('izin_TK') == 1)
			$jenis[] = 'TK';
		if ($this->input->post('izin_0') == 1)
			$jenis[] = '0';

		$target_table   = 'ess_request_perizinan';
		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
		} else if ($_SESSION["grup"] == 5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;
		$get_data = $this->M_tabel_permohonan_perizinan->_get_excel($var, $target_table, $set, @$jenis);

		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=permohonan_perizinan.xlsx");
		header('Cache-Control: max-age=0');

		$excel = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $excel->load('./asset/Template_permohonan_perizinan.xlsx');

		$excel->setActiveSheetIndex(0);
		$kolom 	= 2;
		$awal 	= 4;
		$no = 1;

		foreach ($get_data as $tampil) {
			$absence_type = $tampil->kode_pamlek . '|' . $tampil->info_type . '|' . $tampil->absence_type;
			$excel->getActiveSheet()->setCellValueExplicit('A' . $awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('B' . $awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('C' . $awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('D' . $awal, get_perizinan_name($tampil->kode_pamlek)->nama, PHPExcel_Cell_DataType::TYPE_STRING);

			if ($tampil->start_date)
				$excel->getActiveSheet()->setCellValueExplicit('E' . $awal, tanggal_indonesia($tampil->start_date) . ' ' . $tampil->start_time, PHPExcel_Cell_DataType::TYPE_STRING);
			else
				$excel->getActiveSheet()->setCellValueExplicit('E' . $awal, '', PHPExcel_Cell_DataType::TYPE_STRING);

			if ($tampil->end_date)
				$excel->getActiveSheet()->setCellValueExplicit('F' . $awal, tanggal_indonesia($tampil->end_date) . ' ' . $tampil->end_time, PHPExcel_Cell_DataType::TYPE_STRING);
			else
				$excel->getActiveSheet()->setCellValueExplicit('F' . $awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
			$awal += 1;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->setIncludeCharts(TRUE);
		$objWriter->setPreCalculateFormulas(TRUE);
		PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
		$objWriter->save('php://output');
		exit();
	}

	function get_mst_alasan_sipk()
	{
		$data = $this->db->where('status', 1)->get('mst_sipk_alasan')->result();
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */
