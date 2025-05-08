<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Input extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("karyawan_helper");
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/lembur/M_lembur_api","lembur");
        $this->load->model("lembur/m_pengajuan_lembur");
    }

    function index_post(){
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('np_approver'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP Approver harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tgl_dws'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tertanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tgl_mulai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal mulai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tgl_selesai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal selesai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jam_mulai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jam mulai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jam_selesai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jam selesai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jenis_lembur'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jenis lembur harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('alasan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Alasan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            }

            $status = false;
            $message = '';
            $data = [];
            $np = $this->post('np');
            $np_approver = $this->post('np_approver');
            $tgl_dws = $this->post('tgl_dws');
            $tgl_mulai = $this->post('tgl_mulai');
            $tgl_selesai = $this->post('tgl_selesai');
            $jam_mulai = $this->post('jam_mulai');
            $jam_selesai = $this->post('jam_selesai');
            $alasan = $this->post('jenis_lembur');
            $keterangan = $this->post('alasan');

            $kry = erp_master_data_by_np($np, $tgl_dws);
            $apv = erp_master_data_by_np($np_approver, $tgl_dws);

            $param = ["no_pokok" => $np, "nama" => $kry['nama'], "nama_jabatan" => $kry['nama_jabatan'], "nama_unit" => $kry['nama_unit'], "kode_unit" => $kry['kode_unit'], "approval_pimpinan_np" => $np_approver, "approval_pimpinan_nama" => $apv['nama'], "approval_pimpinan_nama_jabatan" => $apv['nama_jabatan'], "approval_pimpinan_nama_unit" => $apv['nama_unit'], "approval_pimpinan_kode_unit" => $apv['kode_unit'], "personel_number" => $kry['personnel_number'], "tgl_dws" => $tgl_dws, "tgl_mulai" => $tgl_mulai, "tgl_selesai" => $tgl_selesai, "jam_mulai" => $jam_mulai, "jam_selesai" => $jam_selesai, "alasan" => $alasan, "keterangan" => $keterangan, "created_at" => date('Y-m-d H:i:s'), "created_by" => $this->data_karyawan->np_karyawan];

            $get_date = [];
            $get_date['start_input'] = date('Y-m-d', strtotime($tgl_mulai)) . ' ' . date('H:i:s', strtotime($jam_mulai));
			$get_date['end_input'] = date('Y-m-d', strtotime($tgl_selesai)) . ' ' . date('H:i:s', strtotime($jam_selesai));
			$date_dws = date('Y-m-d', strtotime($tgl_dws));
			$plus1 = date('Y-m-d', strtotime($date_dws . "+1 days"));
			$minus1 = date('Y-m-d', strtotime($date_dws . "-1 days"));

            $get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($param);
			$cek_uniq_lembur = $this->m_pengajuan_lembur->cek_uniq_lembur($param, null, null, null);

            if (($get_date['start_input'] < $get_date['end_input'] || (($param['tgl_mulai'] != $param['tgl_dws'] || $param['tgl_mulai'] != $plus1 || $param['tgl_mulai'] != $minus1) && ($param['tgl_selesai'] != $param['tgl_dws'] || $param['tgl_selesai'] != $plus1 || $param['tgl_selesai'] != $minus1))) && $get_date['start_input'] <= $get_date['end_input'] && $cek_uniq_lembur['status'] == true){
				$get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($param);

				$param['waktu_mulai_fix'] = null;
				$param['waktu_selesai_fix'] = null;
				if ((bool)$get_jadwal != false && (bool)$this->m_pengajuan_lembur->cek_dws_lembur($param) == true) {
					if ($cek_uniq_lembur['message'] == 'Not Valid') {
						$param['waktu_mulai_fix'] = $get_date['start_input'];
						$param['waktu_selesai_fix'] = $get_date['end_input'];
						$param['time_type'] = $get_jadwal['time_type'];
						//echo 'a';
					} else if ($cek_uniq_lembur['message'] == 'Not DWS') {
						$param['waktu_mulai_fix'] = $param['waktu_mulai_fix'];
						$param['waktu_selesai_fix'] = $param['waktu_selesai_fix'];
						$param['time_type'] = null;
						//echo 'b';
					} else {
						$param['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
						$param['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
						$param['time_type'] = $get_jadwal['time_type'];
						//echo 'c';
					}

					//jika waktu mulai >= waktu selesai
					if ($param['waktu_mulai_fix'] >= $param['waktu_selesai_fix']) {
						$param['waktu_mulai_fix'] = null;
						$param['waktu_selesai_fix'] = null;
					}
				}

				//check apakah dia pelaksana atau kaun yang boleh lembur
				//return $boleh_lembur['status'],$boleh_lembur['grade_pangkat'],$boleh_lembur['grup_jabatan'],$boleh_lembur['keterangan_hari']
				$check_boleh_lembur = $this->m_pengajuan_lembur->check_boleh_lembur($param['no_pokok'], $param['tgl_dws']);

				if ($check_boleh_lembur == true) { // jika boleh lembur
					$checkPerencanaan = $this->db->where('FIND_IN_SET("' . $param['no_pokok'] . '", list_np)', NULL, FALSE)
						->where('tanggal', $param['tgl_dws'])->where('deleted_at IS NULL',null,false)->get('ess_perencanaan_lembur_detail')->row();

					if ($checkPerencanaan == null) {
						$nama = nama_karyawan_by_np($param['no_pokok']);
						$message = "Pengajuan Data Lembur " . $nama . " (" . $param['no_pokok'] . ") Pada " . $param['tgl_mulai'] . " " . $param['jam_mulai'] . " s/d " . $param['tgl_selesai'] . " " . $param['jam_selesai'] . " Tidak Sesuai Perencanaan.";
					} else {
						$dteStart = date_create(date('Y-m-d H:i', strtotime($param['tgl_mulai'] . ' ' . $param['jam_mulai'])));
						$dteEnd = date_create(date('Y-m-d H:i', strtotime($param['tgl_selesai'] . ' ' . $param['jam_selesai'])));

						$diff = date_diff($dteStart, $dteEnd);
						$hari = $diff->format("%a");
						$jam = $diff->format("%h");
						$menit = $diff->format("%i");
						if (($jam > 0 || $hari > 0) && $menit >= 45) $jam = $jam + 1;
						$total = ($hari * 24) + ($jam);

						if ($total > $checkPerencanaan->jam_lembur) {
							$nama = nama_karyawan_by_np($param['no_pokok']);
							$message = "Pengajuan Data Lembur " . $nama . " (" . $param['no_pokok'] . ") Pada " . $param['tgl_mulai'] . " " . $param['jam_mulai'] . " s/d " . $param['tgl_selesai'] . " " . $param['jam_selesai'] . " Tidak Sesuai Perencanaan.";
						} else {
							//insert ke db
                            $this->db->trans_start();
                            $this->db->insert('ess_lembur_transaksi', $param);
							if ($this->db->trans_status()==true) {
                                $id = $this->db->insert_id();
                                $this->db->trans_complete();
								$status = true;
                                $data = $this->db->where('id',$id)->get('ess_lembur_transaksi')->row();
								
                                $nama = nama_karyawan_by_np($param['no_pokok']);
								$message = "Pengajuan Data Lembur " . $nama . " (" . $param['no_pokok'] . ") Pada " . $param['tgl_mulai'] . " " . $param['jam_mulai'] . " s/d " . $param['tgl_selesai'] . " " . $param['jam_selesai'] . " Berhasil Ditambahkan.";
							} else {
                                $this->db->trans_rollback();
								$nama = nama_karyawan_by_np($param['no_pokok']);
								$message = "Pengajuan Data Lembur " . $nama . " (" . $param['no_pokok'] . ") Pada " . $param['tgl_mulai'] . " " . $param['jam_mulai'] . " s/d " . $param['tgl_selesai'] . " " . $param['jam_selesai'] . " Gagal Ditambahkan.";
							}
						}
					}
				} else {
					$nama = nama_karyawan_by_np($param['no_pokok']);
					$message = "Pengajuan Data Lembur " . $nama . " (" . $param['no_pokok'] . ") Pada " . $param['tgl_mulai'] . " " . $param['jam_mulai'] . " s/d " . $param['tgl_selesai'] . " " . $param['jam_selesai'] . " Gagal Ditambahkan. Grade Pangkat = " . $check_boleh_lembur['grade_pangkat'] . " dan Group Jabatan = " . $check_boleh_lembur['grup_jabatan'] . " pada hari " . $check_boleh_lembur['keterangan_hari'] . " tidak mendapat uang lembur.";
				}
			} else {
				$nama = nama_karyawan_by_np($param['no_pokok']);
				$message = "Pengajuan Data Lembur " . $nama . " (" . $param['no_pokok'] . ") Pada " . $param['tgl_mulai'] . " " . $param['jam_mulai'] . " s/d " . $param['tgl_selesai'] . " " . $param['jam_selesai'] . " Gagal Ditambahkan. " . $cek_uniq_lembur['message'] . "";
			}

            $this->response([
                'status'=>$status,
                'message'=>$message,
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}