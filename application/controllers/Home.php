<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
	private $data = array();
	public function __construct()
	{
		parent::__construct();

		$this->load->model("m_setting");
		$this->load->model("M_dashboard", "dashboard");
		$this->load->model("master_data/m_hari_libur");
		$this->load->model("informasi/m_data_rekap_keterlambatan");

		if (empty($this->session->userdata("username"))) {
			redirect(base_url($this->m_setting->ambil_url_modul("login")));
		}

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("fungsi_helper");
		$this->load->helper("string");

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->nama_db = $this->db->database;

		$this->data['cutoff_erp_tanggal'] = $this->m_setting->ambil_pengaturan('cutoff_erp_tanggal');

		$awal_libur = date_add(date_create(date_add(date_create(date("Y-m-d")), date_interval_create_from_date_string("-1 months"))->format("Y-m-t")), date_interval_create_from_date_string("1 day"))->format("Y-m-d");

		if ((int)date("j") <= (int)$this->data['cutoff_erp_tanggal']) {
			$awal_libur = date_add(date_create($awal_libur), date_interval_create_from_date_string("-1 months"))->format("Y-m-d");
		}

		$akhir_libur = date("Y-m-t");

		$this->data["hari_libur"] = $this->m_hari_libur->daftar_hari_libur_periode($awal_libur, $akhir_libur);

		$this->data['is_with_sidebar'] = true;
	}

	public function index()
	{
		// var_dump($this->session->userdata());
		// die;		
		//coming_soon();
		//$this->output->enable_profiler(TRUE);
		$np = $this->session->userdata('no_pokok');
		$this->session->set_userdata('tampil_np_karyawan', $np);

		//ambil tahun bulan tabel yang tersedia
		$array_tahun_bulan = array();

		$query = $this->db->query("SELECT table_name as table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like 'ess_cico_%' group by table_name");

		foreach ($query->result_array() as $data) {
			$bulan = substr($data['table_name'], -2);
			$tahun = substr($data['table_name'], 9, 4);

			$tahun_bulan = $tahun . "-" . $bulan;

			$array_tahun_bulan[] = $tahun_bulan;
			$this->session->set_userdata('tampil_tahun_bulan', $tahun_bulan);
		}

		//02 06 2022, Tri Wibowo, ambil dari dropdown bukan tahun bulan sekarang, karena nanti error kalau tabel nya belum terbentuk
		$dropdown_tahun_bulan = $tahun . "_" . $bulan;
		//end off 02 06 2022, Tri Wibowo

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
		} else {
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
		}

		$this->data['array_tahun_bulan'] = $array_tahun_bulan;

		$checkDateKehadiran = str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan'));
		//			if($this->dashboard->checkDateKehadiran(str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan')))){
		//				$checkDateKehadiran = 'tidak ada';
		//			}

		//$this->data['hari_libur'] = $this->m_hari_libur->getTotalCuti_where('both', $value, $checkDateKehadiran);
		$value = array($this->session->userdata('tampil_np_karyawan'), $this->session->userdata('tampil_tahun_bulan'));

		//REKAP TELAT
		$bulan = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		$bulan_str = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$tahun_periode = $tahun;
		if ($tahun_periode == date('Y'))
			$hitung = date('n');
		else
			$hitung = count($bulan);

		for ($i = 0; $i < $hitung; $i++) {
			$periode = $tahun_periode . '_' . $bulan[$i];

			//02 06 2022, Tri Wibowo, ambil dari dropdown bukan tahun bulan sekarang, karena nanti error kalau tabel nya belum terbentuk
			//$list[$i] = $this->m_data_rekap_keterlambatan->hitung_rekap_keterlambatan($value,$periode);$dropdown_tahun_bulan
			$list[$i] = $this->m_data_rekap_keterlambatan->hitung_rekap_keterlambatan($value, $dropdown_tahun_bulan);
			//end off 02 06 2022, Tri Wibowo
		}

		$return_telat = 0;
		for ($i = 0; $i < $hitung; $i++) {
			$return_telat = $list[$i] + $return_telat;
		}
		$count_penindakan = $this->db->where('tahun', substr($tahun_periode, 0, 4))->where('np_karyawan', $value[0])->get('ess_penindakan')->num_rows();
		$return_telat = $return_telat - ($count_penindakan * 13);

		$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
		$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
		$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
		$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
		$this->data['total_ab'] = $this->dashboard->getABKaryawan_where($this->session->userdata('tampil_np_karyawan'), $value[1]);
		$this->data['total_terlambat'] = $return_telat;

		$this->data['daftar_kehadiran'] = $this->dashboard->getKehadiranNeedApproval_where($this->session->userdata('tampil_np_karyawan'), $checkDateKehadiran)->result_array();
		$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
		$this->data['daftar_pelatihan'] = $this->dashboard->getPelatihanNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
		$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();

		// perubahan grafik kehadiran
		if ($_SESSION["grup"] == 3) { // Admin SDM - Remunerasi
			$filter_kehadiran_bulan = $this->db->select("CONCAT(
				SUBSTRING_INDEX(SUBSTRING_INDEX(TABLE_NAME, '_', -2), '_', 1),
				'-',
				SUBSTRING_INDEX(TABLE_NAME, '_', -1)
			  ) AS extracted_month")
				->from('information_schema.TABLES')
				->where('TABLE_SCHEMA', $this->db->database)
				->like('TABLE_NAME', 'ess_cico_', 'AFTER')
				->order_by('TABLE_NAME', 'DESC')
				->get()->result();
			$this->data['filter_kehadiran_bulan'] = $filter_kehadiran_bulan;

			// hutang cuti
			$hutang_cuti = $this->db
				->where('hutang.deleted_at IS NULL', null, false)
				->where('hutang.hutang >', 0)
				->where("hutang.no_pokok IN (SELECT np_karyawan FROM `cuti_cubes_jatah` WHERE tanggal_kadaluarsa >= NOW())", null, false)
				->get('cuti_hutang hutang')->num_rows();
			$this->data['hutang_cuti'] = $hutang_cuti;
		} else {
			$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
		}
		// END perubahan grafik kehadiran

		//07 07 2022 - Tri Wibowo 7648 - Fix : Perizinan Ditolak Atasan, But still show in notif atasan
		// $perizinan = $this->db->select('a.*, (CASE WHEN start_date is not null then start_date else end_date end) as dws_tanggal')->where('((a.approval_1_np="'.$np.'" and a.approval_1_status is null) OR (a.approval_2_np="'.$np.'" and a.approval_2_status is null))')->get('ess_request_perizinan a');			
		$perizinan = $this->db->select('a.*, (CASE WHEN start_date is not null then start_date else end_date end) as dws_tanggal')->where('(((a.approval_1_np="' . $np . '" and a.approval_1_status is null) OR (a.approval_2_np="' . $np . '" and a.approval_2_status is null)) AND (a.date_batal IS null OR a.date_batal="") )')->get('ess_request_perizinan a');
		$perizinan_np = $this->db->select('a.*, (CASE WHEN start_date is not null then start_date else end_date end) as dws_tanggal')
			->where('a.np_karyawan', $this->session->userdata('tampil_np_karyawan'))
			->where('a.np_batal IS NULL', null, false)
			->group_start()
			->where('a.approval_1_status is null', null, false)
			->or_where('a.approval_2_status is null', null, false)
			->or_where('a.approval_pengamanan_posisi is null', null, false)
			->group_end()
			->get('ess_request_perizinan a');
		//end of 07 07 2022 - Tri Wibowo 7648

		$this->data['lembur_belum_diapprove'] = $this->dashboard->getLemburNotYetApproved($this->session->userdata('tampil_np_karyawan'))->result_array();
		$this->data['kehadiran_belum_diapprove'] = $this->dashboard->getKehadiranNotYetApproved($this->session->userdata('tampil_np_karyawan'),  $checkDateKehadiran)->result_array();
		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$this->data['list_pengadministrasi'] = $_SESSION["list_pengadministrasi"];
		}

		$select_kendaraan = 'nama_kota_asal,lokasi_jemput,no_hp_pemesan,np_karyawan,nama,tanggal_berangkat';
		if ($_SESSION["grup"] == 13) {
			$kendaraan = $this->db->select($select_kendaraan)->where(['a.verified' => 1, 'a.tanggal_berangkat>=' => date('Y-m-d')])->where('a.status_persetujuan_admin is null', null, false)->get('ess_pemesanan_kendaraan a');

			$penilaian = [];
		} else {
			$kendaraan = $this->db->select($select_kendaraan)->where(['a.verified_by_np' => $np, 'a.verified' => 0, 'a.tanggal_berangkat>=' => date('Y-m-d')])->get('ess_pemesanan_kendaraan a');

			// $penilaian = $this->db->select('nama_kota_asal,lokasi_jemput,tanggal_berangkat,nama_mst_driver,jam')->where("(status_persetujuan_admin=1 and id_mst_driver is not null AND rating_driver is null and (np_karyawan='$np' OR np_karyawan_pic='$np') AND is_canceled_by_admin!='1')")->order_by('tanggal_berangkat', 'DESC')->get('ess_pemesanan_kendaraan')->result_array();
			$penilaian = [];
		}

		$jabatan = $this->db->select('*')
			->from('mst_karyawan')
			->where('no_pokok', $np)
			->get()
			->row();

		$this->data['jabatan'] = $jabatan;

		$kode_unit = substr($jabatan->kode_unit, 0, 2);

		$notif_job_tender = $this->db->select('a.*, c.no_pokok, c.nama AS nama_karyawan, c.nama_jabatan_singkat AS nama_jabatan_karyawan, d.nama_jabatan')
			->from('ijt_apply a')
			// ->join('usr_pengguna b', 'b.no_pokok = a.np', 'left')
			->join('mst_karyawan c', 'a.np = c.no_pokok', 'left')
			->join('ijt_data d', 'd.id = a.job_id', 'left')
			->where('a.deleted_at', null)
			->like('c.kode_unit', $kode_unit, 'after')
			->order_by('a.created_at', 'DESC')
			->get()
			->result();	

		$this->data['notif_job_tender'] = $notif_job_tender;

		// undangan agenda job tender
		$notif_undangan_job_tender = [];
		if($this->session->userdata('grup')=='5'){
			$this->db->select('ev.kegiatan, ev.tanggal, ev.tempat, job.id AS job_id, job.kode_jabatan, job.kode_unit, job.nama_jabatan, job.deskripsi, job.start_date, job.end_date');
			$this->db->from('ijt_event_undangan undangan');
			$this->db->join('ijt_event ev', 'ev.id = undangan.event_id AND ev.deleted_at IS NULL AND ev.tanggal >= NOW()', 'inner', false);
			$this->db->join('ijt_data job', 'job.id = ev.job_id AND job.deleted_at IS NULL', 'inner');
			$this->db->join('ijt_apply apply', "apply.job_id = job.id AND apply.np = '{$np}' AND apply.deleted_at IS NULL", 'inner');
			$this->db->join('ijt_verval verval', 'verval.apply_id = apply.id AND verval.is_verval = "1" AND verval.jenis_verval = "administrasi" AND verval.deleted_at IS NULL', 'inner');
			$this->db->where('undangan.deleted_at IS NULL', null, false);
			$this->db->where('undangan.np', $np);
			
			$query = $this->db->get();
			$notif_undangan_job_tender = $query->result();
		}
		$this->data['notif_undangan_job_tender'] = $notif_undangan_job_tender;
		// END undangan agenda job tender

		if ($_SESSION["grup"] == 14) {
			// , 'a.tanggal_pemesanan>='=>date('Y-m-d')
			$makan_lembur = $this->db->select('*')->where(['a.verified' => '1', 'a.tanggal_pemesanan>=' => date('Y-m-d')])->get('ess_pemesanan_makan_lembur a')->result_array();

			$konsumsi_rapat = $this->db->select('*')->where(['a.verified' => '1', 'a.tanggal_pemesanan>=' => date('Y-m-d')])->get('ess_pemesanan_konsumsi_rapat a')->result_array();
		} else {
			$makan_lembur = $this->db->select('*')->where(['a.np_atasan' => $np, 'a.tanggal_pemesanan>=' => date('Y-m-d')])->where('a.verified is null')->get('ess_pemesanan_makan_lembur a')->result_array();

			$konsumsi_rapat = $this->db->select('*')->where(['a.np_atasan' => $np, 'a.tanggal_pemesanan>=' => date('Y-m-d')])->where('a.verified is null')->get('ess_pemesanan_konsumsi_rapat a')->result_array();
		}
		$this->data['daftar_perizinan'] = $perizinan->result_array();
		$this->data['perizinan_belum_approve'] = $perizinan_np->result_array();
		$this->data['daftar_kendaraan'] = $kendaraan->result_array();
		$this->data['daftar_penilaian'] = $penilaian;

		$this->data['daftar_makan_lembur'] = $makan_lembur;
		$this->data['daftar_konsumsi_rapat'] = $konsumsi_rapat;
		$this->data['agenda_slide'] = $this->slideAgenda();

		# pelaporan
		$all_pelaporan = [];
		$array_pelaporan = [
			['variable' => 'persetujuan_pendidikan', 'table' => 'ess_laporan_pendidikan', 'url_persetujuan' => 'pelaporan/persetujuan/pendidikan', 'url_verifikasi' => 'pelaporan/verifikasi/pendidikan'],
			['variable' => 'persetujuan_pindah_agama', 'table' => 'ess_laporan_pindah_agama', 'url_persetujuan' => 'pelaporan/persetujuan/pindah_agama', 'url_verifikasi' => 'pelaporan/verifikasi/pindah_agama'],
			['variable' => 'persetujuan_pernikahan', 'table' => 'ess_laporan_pernikahan', 'url_persetujuan' => 'pelaporan/persetujuan/pernikahan', 'url_verifikasi' => 'pelaporan/verifikasi/pernikahan'],
			['variable' => 'persetujuan_kelahiran_anak', 'table' => 'ess_laporan_kelahiran_anak', 'url_persetujuan' => 'pelaporan/persetujuan/kelahiran_anak', 'url_verifikasi' => 'pelaporan/verifikasi/kelahiran_anak'],
			['variable' => 'persetujuan_anak_tertanggung', 'table' => 'ess_laporan_anak_tertanggung', 'url_persetujuan' => 'pelaporan/persetujuan/anak_tertanggung', 'url_verifikasi' => 'pelaporan/verifikasi/anak_tertanggung'],
			['variable' => 'persetujuan_anak_usia', 'table' => 'ess_laporan_anak_usia', 'url_persetujuan' => 'pelaporan/persetujuan/anak_usia', 'url_verifikasi' => 'pelaporan/verifikasi/anak_usia'],
			['variable' => 'persetujuan_anak_tidak_tertanggung', 'table' => 'ess_laporan_anak_tidak_tertanggung', 'url_persetujuan' => 'pelaporan/persetujuan/anak_tidak_tertanggung', 'url_verifikasi' => 'pelaporan/verifikasi/anak_tidak_tertanggung'],
			['variable' => 'persetujuan_suami_tertanggung', 'table' => 'ess_laporan_suami_tertanggung', 'url_persetujuan' => 'pelaporan/persetujuan/suami_tertanggung', 'url_verifikasi' => 'pelaporan/verifikasi/suami_tertanggung'],
			['variable' => 'persetujuan_pindah_alamat', 'table' => 'ess_laporan_pindah_alamat', 'url_persetujuan' => 'pelaporan/persetujuan/pindah_alamat', 'url_verifikasi' => 'pelaporan/verifikasi/pindah_alamat'],
			['variable' => 'persetujuan_perceraian', 'table' => 'ess_laporan_perceraian', 'url_persetujuan' => 'pelaporan/persetujuan/perceraian', 'url_verifikasi' => 'pelaporan/verifikasi/perceraian']
		];

		foreach ($array_pelaporan as $row) {
			if ($_SESSION["grup"] == 5) {
				$all_pelaporan[] = [
					'data' => $this->db->where('approval_np', $_SESSION['no_pokok'])->where('approval_status', '0')->where('deleted_at', null)->get($row['table'])->result_array(),
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => base_url($row['url_persetujuan'])
				];
			} else if ($_SESSION["grup"] == 21) {
				$all_pelaporan[] = [
					'data' => $this->db->where('approval_status', '1')->where('deleted_at', null)->get($row['table'])->result_array(),
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => base_url($row['url_verifikasi'])
				];
			} else if ($_SESSION["grup"] == 9) {
				$all_pelaporan[] = [
					'data' => $this->db->where('approval_status', '3')->where('deleted_at', null)->get($row['table'])->result_array(),
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => base_url($row['url_verifikasi'])
				];
			} else {
				$all_pelaporan[] = [
					'data' => [],
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => 'javascript:;'
				];
			}
		}
		$this->data['all_pelaporan'] = $all_pelaporan;
		# END: pelaporan

		# pelaporan ditolak
		$all_pelaporan_tolak = [];
		$array_pelaporan_tolak = [
			['variable' => 'laporan_pendidikan', 'table' => 'ess_laporan_pendidikan', 'url_pelaporan' => 'pelaporan/pendidikan', 'url_verifikasi' => 'pelaporan/verifikasi/pendidikan'],
			['variable' => 'laporan_pindah_agama', 'table' => 'ess_laporan_pindah_agama', 'url_pelaporan' => 'pelaporan/pindah_agama', 'url_verifikasi' => 'pelaporan/verifikasi/pindah_agama'],
			['variable' => 'laporan_pernikahan', 'table' => 'ess_laporan_pernikahan', 'url_pelaporan' => 'pelaporan/pernikahan', 'url_verifikasi' => 'pelaporan/verifikasi/pernikahan'],
			['variable' => 'laporan_kelahiran_anak', 'table' => 'ess_laporan_kelahiran_anak', 'url_pelaporan' => 'pelaporan/kelahiran_anak', 'url_verifikasi' => 'pelaporan/verifikasi/kelahiran_anak'],
			['variable' => 'laporan_anak_tertanggung', 'table' => 'ess_laporan_anak_tertanggung', 'url_pelaporan' => 'pelaporan/anak_tertanggung', 'url_verifikasi' => 'pelaporan/verifikasi/anak_tertanggung'],
			['variable' => 'laporan_anak_usia', 'table' => 'ess_laporan_anak_usia', 'url_pelaporan' => 'pelaporan/anak_usia', 'url_verifikasi' => 'pelaporan/verifikasi/anak_usia'],
			['variable' => 'laporan_anak_tidak_tertanggung', 'table' => 'ess_laporan_anak_tidak_tertanggung', 'url_pelaporan' => 'pelaporan/anak_tidak_tertanggung', 'url_verifikasi' => 'pelaporan/verifikasi/anak_tidak_tertanggung'],
			['variable' => 'laporan_suami_tertanggung', 'table' => 'ess_laporan_suami_tertanggung', 'url_pelaporan' => 'pelaporan/suami_tertanggung', 'url_verifikasi' => 'pelaporan/verifikasi/suami_tertanggung'],
			['variable' => 'laporan_pindah_alamat', 'table' => 'ess_laporan_pindah_alamat', 'url_pelaporan' => 'pelaporan/pindah_alamat', 'url_verifikasi' => 'pelaporan/verifikasi/pindah_alamat'],
			['variable' => 'laporan_perceraian', 'table' => 'ess_laporan_perceraian', 'url_pelaporan' => 'pelaporan/perceraian', 'url_verifikasi' => 'pelaporan/verifikasi/perceraian']
		];

		foreach ($array_pelaporan_tolak as $row) {
			if ($_SESSION["grup"] == 5) {
				$all_pelaporan_tolak[] = [
					'data' => $this->db->where('np_karyawan', $_SESSION['no_pokok'])->where('approval_status in ("2","4","6")')->where('deleted_at', null)->get($row['table'])->result_array(),
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => base_url($row['url_pelaporan'])
				];
			} else {
				$all_pelaporan_tolak[] = [
					'data' => [],
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => 'javascript:;'
				];
			}
		}
		$this->data['all_pelaporan_tolak'] = $all_pelaporan_tolak;
		# END: pelaporan ditolak

		# faskar
		$all_faskar = [];
		$array_faskar = [
			['variable' => 'persetujuan_listrik', 'table' => 'ess_faskar_listrik_header', 'url_persetujuan' => 'faskar/persetujuan/listrik/header', 'url_verifikasi' => 'faskar/verifikasi/listrik/header'],
			['variable' => 'persetujuan_air', 'table' => 'ess_faskar_pam_header', 'url_persetujuan' => 'faskar/persetujuan/pam/header', 'url_verifikasi' => 'faskar/verifikasi/pam/header'],
			['variable' => 'persetujuan_tv_kabel', 'table' => 'ess_faskar_tv_header', 'url_persetujuan' => 'faskar/persetujuan/tv/header', 'url_verifikasi' => 'faskar/verifikasi/tv/header'],
			['variable' => 'persetujuan_internet', 'table' => 'ess_faskar_internet_header', 'url_persetujuan' => 'faskar/persetujuan/internet/header', 'url_verifikasi' => 'faskar/verifikasi/internet/header'],
			['variable' => 'persetujuan_pulsa', 'table' => 'ess_faskar_ponsel_header', 'url_persetujuan' => 'faskar/persetujuan/ponsel/header', 'url_verifikasi' => 'faskar/verifikasi/ponsel/header']
		];

		foreach ($array_faskar as $row) {
			if ($_SESSION["grup"] == 5) {
				$all_faskar[] = [
					'data' => $this->db->where('approval_atasan_np', $_SESSION['no_pokok'])->where('approval_status', '0')->get($row['table'])->result_array(),
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => base_url($row['url_persetujuan'])
				];
			} else if ($_SESSION["grup"] == 19) {
				$all_faskar[] = [
					'data' => $this->db->where('approval_status', '1')->get($row['table'])->result_array(),
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => base_url($row['url_verifikasi'])
				];
			} else {
				$all_faskar[] = [
					'data' => [],
					'title' => ucfirst(str_replace('_', ' ', $row['variable'])),
					'url' => 'javascript:;'
				];
			}
		}
		$this->data['all_faskar'] = $all_faskar;
		# END: faskar
		if ($this->input->get('dismiss_notif')) {
			$dismiss_notif = $this->input->get('dismiss_notif'); // menangkap nilai 'status' dari URL
		} else {
			$dismiss_notif = true;
		}
		$dismiss_notif == 'true' ? $dismiss_notif = true : $dismiss_notif = false;

		// Ambil bulan aktif berdasarkan cut off tanggal 10
		if (date('d') > 10) {
			// Jika tanggal sekarang lebih dari 10, maka bulan ini aktif
			$active_month = date('m');
			$active_year = date('Y');
		} else {
			// Jika tanggal sekarang kurang dari 10, ambil bulan lalu
			$active_month = date('m', strtotime('-1 month'));
			$active_year = date('Y', strtotime('-1 month'));
		}

		if ($dismiss_notif) {
			// Tentukan rentang tanggal dari tanggal 11 bulan aktif hingga tanggal 10 bulan berikutnya
			$start_date_cutoff = date("$active_year-$active_month-11");
			$next_month_cutoff = date('Y-m-10', strtotime('+1 month', strtotime("$active_year-$active_month-01")));

			// Fungsi untuk memfilter data berdasarkan rentang tanggal
			function filterDataByDateRange($data, $date_field, $start_date_cutoff, $next_month_cutoff)
			{
				$filtered_data = [];
				foreach ($data as $key => $value) {
					$date_value = $value[$date_field];
					if (strtotime($date_value) >= strtotime($start_date_cutoff) && strtotime($date_value) <= strtotime($next_month_cutoff)) {
						$filtered_data[] = $value;
					}
				}
				return $filtered_data;
			}

			// Penerapan filter pada setiap data
			if ($this->data['daftar_perizinan']) {
				$this->data['daftar_perizinan'] = filterDataByDateRange($this->data['daftar_perizinan'], 'dws_tanggal', $start_date_cutoff, $next_month_cutoff);
			}

			if ($this->data['daftar_kehadiran']) {
				$this->data['daftar_kehadiran'] = filterDataByDateRange($this->data['daftar_kehadiran'], 'dws_tanggal', $start_date_cutoff, $next_month_cutoff);
			}

			if ($this->data['daftar_lembur']) {
				$this->data['daftar_lembur'] = filterDataByDateRange($this->data['daftar_lembur'], 'tgl_dws', $start_date_cutoff, $next_month_cutoff);
			}

			if ($this->data['daftar_cuti']) {
				$daftar_cuti = $this->data['daftar_cuti'];
				$daftar_cuti_filtered = [];

				foreach ($daftar_cuti as $value) {
					$start_date = $value['start_date'];
					$end_date = $value['end_date'];

					// Jika start_date atau end_date berada dalam rentang yang diinginkan
					if ((strtotime($start_date) >= strtotime($start_date_cutoff) && strtotime($start_date) <= strtotime($next_month_cutoff)) ||
						(strtotime($end_date) >= strtotime($start_date_cutoff) && strtotime($end_date) <= strtotime($next_month_cutoff))
					) {
						$daftar_cuti_filtered[] = $value;
					}
				}

				$this->data['daftar_cuti'] = $daftar_cuti_filtered;
			}

			if ($this->data['daftar_makan_lembur']) {
				$this->data['daftar_makan_lembur'] = filterDataByDateRange($this->data['daftar_makan_lembur'], 'tanggal_pemesanan', $start_date_cutoff, $next_month_cutoff);
			}

			if ($this->data['daftar_konsumsi_rapat']) {
				$this->data['daftar_konsumsi_rapat'] = filterDataByDateRange($this->data['daftar_konsumsi_rapat'], 'tanggal_pemesanan', $start_date_cutoff, $next_month_cutoff);
			}

			if ($this->data['daftar_kendaraan']) {
				$this->data['daftar_kendaraan'] = filterDataByDateRange($this->data['daftar_kendaraan'], 'tanggal_berangkat', $start_date_cutoff, $next_month_cutoff);
			}

			if ($this->data['daftar_penilaian']) {
				$this->data['daftar_penilaian'] = filterDataByDateRange($this->data['daftar_penilaian'], 'tanggal_berangkat', $start_date_cutoff, $next_month_cutoff);
			}

			if ($this->data['all_pelaporan']) {
				$all_pelaporan = $this->data['all_pelaporan'];
				$all_pelaporan_filtered = [];

				foreach ($all_pelaporan as $array) {
					if (!empty($array['data'])) {
						$data = $array['data'];
						$data_filtered = filterDataByDateRange($data, 'created_at', $start_date_cutoff, $next_month_cutoff);

						// Hasil filter
						$array['data'] = $data_filtered;
						$all_pelaporan_filtered[] = $array;
					}
				}

				$this->data['all_pelaporan'] = $all_pelaporan_filtered;
			}

			if ($this->data['all_faskar']) {
				$all_faskar = $this->data['all_faskar'];
				$all_faskar_filtered = [];

				foreach ($all_faskar as $array) {
					if (!empty($array['data'])) {
						$data = $array['data'];
						$data_filtered = filterDataByDateRange($data, 'submit_date', $start_date_cutoff, $next_month_cutoff);

						// Hasil filter
						$array['data'] = $data_filtered;
						$all_faskar_filtered[] = $array;
					}
				}

				$this->data['all_faskar'] = $all_faskar_filtered;
			}
		}

		$this->data['button_dismiss'] = $dismiss_notif;
		$this->data['content'] = 'dashboard';
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['is_tnav'] = true;
		$this->load->view('template', $this->data);
	}

	public function index_new()
	{
		coming_soon();
		//$this->output->enable_profiler(TRUE);
		$np = $this->session->userdata('no_pokok');
		$this->session->set_userdata('tampil_np_karyawan', $np);

		//ambil tahun bulan tabel yang tersedia
		$array_tahun_bulan = array();

		$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like 'ess_cico_%' group by table_name");
		foreach ($query->result_array() as $data) {
			$bulan = substr($data['table_name'], -2);
			$tahun = substr($data['table_name'], 9, 4);

			$tahun_bulan = $tahun . "-" . $bulan;

			$array_tahun_bulan[] = $tahun_bulan;
			$this->session->set_userdata('tampil_tahun_bulan', $tahun_bulan);
		}

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
		} else {
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
		}

		$this->data['array_tahun_bulan'] = $array_tahun_bulan;

		$checkDateKehadiran = str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan'));
		//			if($this->dashboard->checkDateKehadiran(str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan')))){
		//				$checkDateKehadiran = 'tidak ada';
		//			}

		//$this->data['hari_libur'] = $this->m_hari_libur->getTotalCuti_where('both', $value, $checkDateKehadiran);

		$value = array($this->session->userdata('tampil_np_karyawan'), $this->session->userdata('tampil_tahun_bulan'));
		$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
		$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
		$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
		$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
		$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
		$this->data['daftar_kehadiran'] = $this->dashboard->getKehadiranNeedApproval_where($this->session->userdata('tampil_np_karyawan'), $checkDateKehadiran)->result_array();
		$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
		$this->data['daftar_pelatihan'] = $this->dashboard->getPelatihanNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
		$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$this->data['list_pengadministrasi'] = $_SESSION["list_pengadministrasi"];
		}

		$this->data['content'] = 'dashboard';
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['is_tnav'] = true;
		$this->load->view('template', $this->data);
	}

	public function semua()
	{
		$np = $this->session->userdata('no_pokok');
		$this->session->unset_userdata('tampil_np_karyawan');
		$this->session->unset_userdata('tampil_tahun_bulan');

		//ambil tahun bulan tabel yang tersedia
		$array_tahun_bulan = array();
		$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like '%ess_cico_%';");
		foreach ($query->result_array() as $data) {
			$bulan = substr($data['table_name'], -2);
			$tahun = substr($data['table_name'], 9, 4);

			$tahun_bulan = $tahun . "-" . $bulan;

			$array_tahun_bulan[] = $tahun_bulan;
		}

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
		} else {
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
		}

		//REKAP TELAT
		$bulan = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		$bulan_str = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$tahun_periode = $tahun;
		if ($tahun_periode == date('Y'))
			$hitung = date('n');
		else
			$hitung = count($bulan);

		for ($i = 0; $i < $hitung; $i++) {
			$periode = $tahun_periode . '_' . $bulan[$i];
			$list[$i] = $this->m_data_rekap_keterlambatan->hitung_rekap_keterlambatan($value, $periode);
		}

		$return_telat = 0;
		for ($i = 0; $i < $hitung; $i++) {
			$return_telat = $list[$i] + $return_telat;
		}
		$count_penindakan = $this->db->where('tahun', substr($tahun_periode, 0, 4))->where('np_karyawan', $value[0])->get('ess_penindakan')->num_rows();
		$return_telat = $return_telat - ($count_penindakan * 13);


		$this->data['array_tahun_bulan'] = $array_tahun_bulan;
		$this->data['total_cuti'] = $this->dashboard->getTotalCuti();
		$this->data['total_lembur'] = $this->dashboard->getTotalLembur();
		$this->data['total_izin'] = $this->dashboard->getTotalIzin();
		$this->data['total_dinas'] = $this->dashboard->getTotalDinas();
		$this->data['total_ab'] = $this->dashboard->getTotalAB();
		$this->data['total_terlambat'] = $return_telat;
		$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval()->result_array();
		$this->data['daftar_pelatihan'] = $this->dashboard->getPelatihanNeedApproval()->result_array();
		$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval()->result_array();
		$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran($checkDateIzin);
		$this->data['content'] = 'dashboard';
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['is_tnav'] = true;
		$this->load->view('template', $this->data);
	}

	public function filter_np_karyawan($np_karyawan = null)
	{
		$np = $this->session->userdata('no_pokok');
		if (empty($np_karyawan)) {
			$this->session->unset_userdata('tampil_np_karyawan');
			if (!empty($this->session->userdata('tampil_tahun_bulan'))) {
				redirect(base_url('home/filter_tahun_bulan/') . $this->session->userdata('tampil_tahun_bulan'));
			} else {
				redirect(base_url('home/semua'));
			}
		}
		$this->session->set_userdata('tampil_np_karyawan', $np_karyawan);

		//ambil tahun bulan tabel yang tersedia
		$array_tahun_bulan = array();
		$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like '%ess_cico_%' group by table_name");
		foreach ($query->result_array() as $data) {
			$bulan = substr($data['table_name'], -2);
			$tahun = substr($data['table_name'], 9, 4);

			$tahun_bulan = $tahun . "-" . $bulan;

			$array_tahun_bulan[] = $tahun_bulan;
			if (empty($this->session->userdata('tampil_tahun_bulan'))) {
				$this->session->set_userdata('tampil_tahun_bulan', $tahun_bulan);
			}
		}

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
		} else {
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
		}

		$this->data['array_tahun_bulan'] = $array_tahun_bulan;
		$checkDateKehadiran = str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan'));
		if ($this->dashboard->checkDateKehadiran(str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan')))) {
			$checkDateKehadiran = 'tidak ada';
		}

		//REKAP TELAT
		$value = array($np_karyawan, $this->session->userdata('tampil_tahun_bulan'));
		$bulan = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		$tahun_periode = $tahun;
		if ($tahun_periode == date('Y'))
			$hitung = date('n');
		else
			$hitung = count($bulan);

		for ($i = 0; $i < $hitung; $i++) {
			$periode = $tahun_periode . '_' . $bulan[$i];
			$list[$i] = $this->m_data_rekap_keterlambatan->hitung_rekap_keterlambatan($value, $periode);
		}

		$return_telat = 0;
		for ($i = 0; $i < $hitung; $i++) {
			$return_telat = $list[$i] + $return_telat;
			// $return_telat = $val + $return_telat;
		}
		$count_penindakan = $this->db->where('tahun', substr($tahun_periode, 0, 4))->where('np_karyawan', $value[0])->get('ess_penindakan')->num_rows();
		$return_telat = ($return_telat - ($count_penindakan * 13));

		$this->data['total_terlambat'] = $return_telat;

		if (empty($this->session->userdata('tampil_tahun_bulan'))) {
			$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('np', $np_karyawan, $checkDateKehadiran);
			$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('np', $np_karyawan, $checkDateKehadiran);
			$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('np', $np_karyawan, $checkDateKehadiran);
			$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('np', $np_karyawan, $checkDateKehadiran);
			$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('np', $np_karyawan, $checkDateKehadiran);
			$this->data['total_ab'] = $this->dashboard->getABKaryawan_where($np_karyawan, $checkDateKehadiran);
		} else {
			$value = array($np_karyawan, $this->session->userdata('tampil_tahun_bulan'));
			$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
			$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
			$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
			$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
			$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
			$this->data['total_ab'] = $this->dashboard->getABKaryawan_where($np_karyawan, $checkDateKehadiran);
		}

		$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($np_karyawan)->result_array();
		$this->data['daftar_pelatihan'] = $this->dashboard->getPelatihanNeedApproval_where($np_karyawan)->result_array();
		$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval_where($np_karyawan)->result_array();
		$this->data['content'] = 'dashboard';
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['is_tnav'] = true;
		$this->load->view('template', $this->data);
	}

	public function filter_tahun_bulan($tahun_bulan = null)
	{
		$np = $this->session->userdata('no_pokok');
		if (empty($tahun_bulan)) {
			$this->session->unset_userdata('tampil_tahun_bulan');
			if (!empty($this->session->userdata('tampil_np_karyawan'))) {
				redirect(base_url('home/filter_np_karyawan/') . $this->session->userdata('tampil_np_karyawan'));
			} else {
				redirect(base_url('home/semua'));
			}
		}
		$this->session->set_userdata('tampil_tahun_bulan', $tahun_bulan);

		//ambil tahun bulan tabel yang tersedia
		$array_tahun_bulan = array();

		//$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like 'ess_cico_%';"); # query yg lama
		$query = $this->db->select('table_name')->where('table_schema', $this->nama_db)->like('table_name', 'ess_cico_', 'after')->get('information_schema.tables'); # heru PDS ubah jadi query builder, 2021-01-05 @10:24

		foreach ($query->result_array() as $data) {
			$bulan = substr($data['table_name'], -2);
			$tahun = substr($data['table_name'], 9, 4);

			$tahunbulan = $tahun . "-" . $bulan;

			$array_tahun_bulan[] = $tahunbulan;
			if (empty($this->session->userdata('tampil_tahun_bulan'))) {
				$this->session->set_userdata('tampil_tahun_bulan', $tahunbulan);
			}
		}

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
		} else {
			$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
		}

		$this->data['array_tahun_bulan'] = $array_tahun_bulan;
		$checkDateKehadiran = str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan'));
		if ($this->dashboard->checkDateKehadiran(str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan')))) {
			$checkDateKehadiran = 'tidak ada';
		}

		//REKAP TELAT
		$value = array($this->session->userdata('tampil_np_karyawan'), $tahun_bulan);
		$bulan = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		$tahun_periode = $tahun;
		if ($tahun_periode == date('Y'))
			$hitung = date('n');
		else
			$hitung = count($bulan);

		for ($i = 0; $i < $hitung; $i++) {
			$periode = $tahun_periode . '_' . $bulan[$i];
			$list[$i] = $this->m_data_rekap_keterlambatan->hitung_rekap_keterlambatan($value, $periode);
		}

		$return_telat = 0;
		for ($i = 0; $i < $hitung; $i++) {
			$return_telat = $list[$i] + $return_telat;
			// $return_telat = $val + $return_telat;
		}
		$count_penindakan = $this->db->where('tahun', substr($tahun_periode, 0, 4))->where('np_karyawan', $value[0])->get('ess_penindakan')->num_rows();
		$return_telat = ($return_telat - ($count_penindakan * 13));

		$this->data['total_terlambat'] = $return_telat;
		if (empty($this->session->userdata('tampil_np_karyawan'))) {
			$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('date', $tahun_bulan, $checkDateKehadiran);
			$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('date', $tahun_bulan, $checkDateKehadiran);
			$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('date', $tahun_bulan, $checkDateKehadiran);
			$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('date', $tahun_bulan, $checkDateKehadiran);
			$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('date', $tahun_bulan, $checkDateKehadiran);
			$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval()->result_array();
			$this->data['daftar_pelatihan'] = $this->dashboard->getPelatihanNeedApproval()->result_array();
			$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval()->result_array();
			$this->data['total_ab'] = $this->dashboard->getABKaryawan_where($this->session->userdata('tampil_np_karyawan'), $tahun_bulan);
		} else {
			$value = array($this->session->userdata('tampil_np_karyawan'), $tahun_bulan);
			$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
			$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
			$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
			$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
			$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
			$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
			$this->data['daftar_pelatihan'] = $this->dashboard->getPelatihanNeedApproval_wheredate($this->session->userdata('tampil_np_karyawan'), $tahun_bulan)->result_array();
			$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
			$this->data['total_ab'] = $this->dashboard->getABKaryawan_where($this->session->userdata('tampil_np_karyawan'), $tahun_bulan);
		}

		$this->data['content'] = 'dashboard';
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['is_tnav'] = true;
		$this->load->view('template', $this->data);
	}

	private function slideAgenda()
	{
		$np_login = $this->session->userdata('no_pokok');
		$datefrom = date('Y/m/d', strtotime("+30 days"));

		$agenda = $this->db->select('a.id, a.agenda, a.image, a.tanggal, a.is_berkala, a.next_date')
			->from('v_ess_agenda a')
			// ->group_start()
			// ->where(['a.tanggal >=' => date('Y/m/d'), 'a.tanggal <=' => $datefrom])
			// ->or_where(['a.next_date >=' => date('Y/m/d'), 'a.next_date <=' => $datefrom])
			// ->group_end()
			->group_start()
			->where('a.tanggal', date('Y-m-d'))
			->or_where('a.next_date', date('Y-m-d'))
			->group_end()
			->where('a.status', 1)
			->group_start()
			->where('np_tergabung is null', null, false)
			->or_where('np_tergabung', 'all')
			->or_where("FIND_IN_SET('{$np_login}', np_tergabung)", null, false)
			->group_end()
			->order_by('a.waktu_mulai', 'ASC')
			->get()->result();

		return $agenda;
	}

	// filter grafik kehadiran remunerasi
	function get_filter_kehadiran_unit()
	{
		$bulan = $this->input->post('bulan', true);
		$value = array($this->session->userdata('tampil_np_karyawan'), $bulan);
		$filter_kehadiran_unit = $this->dashboard->filter_kehadiran_unit('both', $value, date('Y_m', strtotime($bulan)));
		echo json_encode([
			'status' => true,
			'data' => $filter_kehadiran_unit
		]);
	}

	function get_data_kehadiran_by_unit()
	{
		$bulan = $this->input->post('bulan', true);
		$unit = $this->input->post('unit', true);
		$value = array($this->session->userdata('tampil_np_karyawan'), $bulan);
		$data_kehadiran = $this->dashboard->getGrafikKehadiran_where_by_unit('both', $value, date('Y_m', strtotime($bulan)), $unit);
		echo json_encode([
			'status' => true,
			'data' => $data_kehadiran
		]);
	}
}
