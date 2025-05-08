<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_vaksin_keluarga extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'vaksinasi/data_vaksin_keluarga/';
		$this->folder_model = 'vaksinasi/data_vaksin_keluarga/';
		$this->folder_controller = 'vaksinasi/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
					
		$this->load->model($this->folder_model."M_data_vaksin_keluarga");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Data Vaksin Keluarga";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index() {
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."index";
		$this->load->view('template',$this->data);
	}	
	
	public function tabel_data(){
		$params = [];
		$params['grup'] = $_SESSION['grup'];
		$params['jenis'] = $this->input->post('jenis');
		switch ($_SESSION['grup']) {
			case "5":
				$params['list_np'] = [$_SESSION['no_pokok']];
			  	break;
			case "4":
				$get_np = $this->db->select('GROUP_CONCAT(no_pokok) as np')->where_in('kode_unit', array_column($_SESSION['list_pengadministrasi'], 'kode_unit'))->get('mst_karyawan')->row();
				$list_np = explode(',', $get_np->np);
				$params['list_np'] = $list_np;
			  	break;
			case "12":
				$params['list_np'] = [];
			  	break;
			default:
				$params['list_np'] = [];
		}

		$list = $this->M_data_vaksin_keluarga->get_datatables($params);
		$data = array();
		$no = $_POST['start'];

		foreach ($list as $tampil) {
			$no++;
			$row = $tampil;
			$row->no = $no;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_data_vaksin_keluarga->count_all($params),
			"recordsFiltered" => $this->M_data_vaksin_keluarga->count_filtered($params),
			"data" => $data
		);
		
		echo json_encode($output);
	}

	function get_all_prov(){
		$get = $this->db->select('provinsi.kode_wilayah as kode_wilayah_prov, provinsi.nama as nama_prov')
			->where('kode_wilayah !=', '350000')
			->from('provinsi')
			->order_by('provinsi.kode_wilayah')
			->get()->result();
		echo json_encode([
			'message'=>'All provinsi Indonesia',
			'data'=>$get
		]);
	}

	function get_all_wilayah(){
		$kode = $this->input->post('kode',true);
		$nama = $this->input->post('nama',true);
		$get = $this->db->select('kabupaten.kode_wilayah as kode_wilayah_kab, kabupaten.nama as nama_kab
		, kecamatan.kode_wilayah as kode_wilayah_kec, kecamatan.nama as nama_kec
		, kelurahan.kode_wilayah as kode_wilayah_kel, kelurahan.nama as nama_kel')
			->from('kelurahan')
			->join('kecamatan', 'kecamatan.kode_wilayah=kelurahan.kode_kec')
			->join('kabupaten', 'kabupaten.kode_wilayah=kecamatan.kode_kab')
			->where('kabupaten.kode_prop', $kode)
			->order_by('kabupaten.kode_wilayah, kecamatan.kode_wilayah, kelurahan.kode_wilayah')
			->get()->result();
		echo json_encode([
			'params'=>[
				'kode_prov'=>$kode,
				'nama_prov'=>$nama
			],
			'data'=>$get
		]);
	}

	function get_all_klinik(){
		$get = $this->db->select('*')
			->from('mst_klinik')
			->get()->result();
		echo json_encode([
			'message'=>'Data klinik',
			'data'=>$get
		]);
	}

	function save(){
		$input=[];
		$response = [];
		foreach( $this->input->post() as $key=>$value){
			$field = str_replace('ubah_','',$key);
			if( !in_array($field, ['alamat_kode_prov','alamat_kode_kab','alamat_kode_kec','alamat_kode_kel','mst_klinik_id','tempat_lahir_keluarga','undefined']) ){
				if($field=='status_kawin'){
					$input[$field] = (trim($value)!='' ? strtoupper(trim($value)) : null);
				} else{
					$input[$field] = (trim($value)!='' ? trim($value) : null);
				}
			}
		}

		if( $this->input->post('ubah_status_vaksin')=='2' )
			$input['mst_klinik_id'] = $this->input->post('ubah_mst_klinik_id');
		else
			$input['mst_klinik_id'] = null;

		$cek = $this->db
			->where('np_karyawan', $input['np_karyawan'])
			->where('tipe_keluarga', $input['tipe_keluarga'])
			->where('nama_lengkap', $input['nama_lengkap'])
			->get('data_vaksin_keluarga');
		if( $cek->num_rows() >0 ){
			$input['updated_at'] = date('Y-m-d H:i:s');
			$input['updated_by_np'] = $_SESSION['no_pokok'];
			$this->db->where('uuid', $cek->row()->uuid)->update('data_vaksin_keluarga', $input);
			$response['status'] = true;
			$response['message'] = '<div class="alert alert-success alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										Data berhasil diupdate.
									</div>';
		} else{
			$input['uuid'] = $this->uuid->v4();
			$input['created_at'] = date('Y-m-d H:i:s');
			$input['created_by_np'] = $_SESSION['no_pokok'];
			$this->db->insert('data_vaksin_keluarga', $input);

			$response['status'] = true;
			$response['message'] = '<div class="alert alert-success alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										Data berhasil ditambahkan.
									</div>';
		}
		echo json_encode($response);
	}

	function export(){
        $filename = "Data-Member-VGR.xlsx";
		$jenis = @$this->input->get('jenis') ? $this->input->get('jenis'):'all';
        $this->load->library('phpexcel');

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Data-Member-VGR.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-vaksinasi/template-export-data-vaksin-keluarga.xlsx');

        $this->db->select('ess_kesehatan_keluarga_tertanggung.np_karyawan
		, ess_kesehatan_keluarga_tertanggung.tipe_keluarga
		, ess_kesehatan_keluarga_tertanggung.nama_lengkap
		, ess_kesehatan_keluarga_tertanggung.tempat_lahir_keluarga
		, ess_kesehatan_keluarga_tertanggung.tanggal_lahir
		, ess_kesehatan_keluarga_tertanggung.jenis_kelamin
		, data_vaksin_keluarga.nik
		, data_vaksin_keluarga.no_hp
		, data_vaksin_keluarga.email
		, data_vaksin_keluarga.status_kawin
		, data_vaksin_keluarga.alamat as alamat_sesuai_nik
		, data_vaksin_keluarga.status_vaksin
		, data_vaksin_keluarga.created_at
		, data_vaksin_keluarga.updated_at
		, data_vaksin_keluarga.mst_klinik_id
		, mst_klinik.kode_klinik
		, mst_klinik.alamat as lokasi_klinik')
			->from('ess_kesehatan_keluarga_tertanggung')
			->join('data_vaksin_keluarga', 'data_vaksin_keluarga.np_karyawan=ess_kesehatan_keluarga_tertanggung.np_karyawan AND data_vaksin_keluarga.tipe_keluarga=ess_kesehatan_keluarga_tertanggung.tipe_keluarga AND data_vaksin_keluarga.nama_lengkap=ess_kesehatan_keluarga_tertanggung.nama_lengkap AND data_vaksin_keluarga.tanggal_lahir=ess_kesehatan_keluarga_tertanggung.tanggal_lahir', 'LEFT')
			->join('mst_klinik', 'mst_klinik.id=data_vaksin_keluarga.mst_klinik_id', 'LEFT')
			->where('ess_kesehatan_keluarga_tertanggung.status_tanggungan','Ditanggung')
			// ->where('ess_kesehatan_keluarga_tertanggung.no_urut >',0)
			->where("2021 - YEAR(ess_kesehatan_keluarga_tertanggung.tanggal_lahir) >",17);
		if( $jenis!='all' ){
			switch ($jenis) {
				case "1":
					$this->db->where('data_vaksin_keluarga.created_at is not null', null, false);
						break;
				case "2":
					$this->db->where('data_vaksin_keluarga.created_at is null', null, false);
						break;
				default:
						
			}
		}
		$get = $this->db->get();

        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 2;
        $no = 1;

        foreach($get->result() as $row){
			if($row->tipe_keluarga=='Pasangan'){
				if($row->jenis_kelamin=='P')
					$hubungan = 'Istri';
				else if($row->jenis_kelamin=='L')
					$hubungan = 'Suami';
				else
					$hubungan = '';
			} else{
				$hubungan = $row->tipe_keluarga;
			}

			if($row->jenis_kelamin=='P')
				$jk = 'PEREMPUAN';
			else if($row->jenis_kelamin=='L')
				$jk = 'LAKI - LAKI';
			else
				$jk = '';

            $excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->np_karyawan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, 'Peserta', PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $hubungan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $row->nik, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $row->no_hp, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $row->email, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $row->nama_lengkap, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, $row->tempat_lahir_keluarga, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValue('J'.$awal, $row->tanggal_lahir);
            $excel->getActiveSheet()->setCellValueExplicit('K'.$awal, $jk, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('L'.$awal, $row->status_kawin, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('M'.$awal, $row->alamat_sesuai_nik, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('N'.$awal, $row->kode_klinik, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('O'.$awal, $row->lokasi_klinik, PHPExcel_Cell_DataType::TYPE_STRING);

			if($row->updated_at!=null)
				$excel->getActiveSheet()->setCellValue('Q'.$awal, date('Y-m-d', strtotime($row->updated_at)));
			else if($row->created_at!=null)
				$excel->getActiveSheet()->setCellValue('Q'.$awal, date('Y-m-d', strtotime($row->created_at)));

			if($row->status_vaksin=='1')
				$excel->getActiveSheet()->setCellValue('R'.$awal, 'SUDAH');
			else if($row->status_vaksin=='2')
				$excel->getActiveSheet()->setCellValue('R'.$awal, 'BELUM');
			else
				$excel->getActiveSheet()->setCellValue('R'.$awal, '');

            $no++;
            $awal++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        $objWriter->save('php://output');
        // $objWriter->save(APPPATH.'../uploads/rekap/transportasi/'.$filename, 'F');
    }
}