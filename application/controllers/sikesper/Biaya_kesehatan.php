<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Biaya_kesehatan extends CI_Controller {
		public function __construct(){
			parent::__construct();
	        $this->load->library('Excel_reader');
			$this->load->library('phpexcel');
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/';
			$this->folder_model = 'sikesper/';
			$this->folder_controller = 'sikesper/';
			
			$this->akses = array();
					
			$this->load->helper("tanggal_helper");
					
			$this->load->model($this->folder_model."m_biaya_kesehatan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Biaya Kesehatan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index() {
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."biaya_kesehatan";

			$this->data['panel_tambah'] = "in";

			$this->data["tanggal"] = "";
			$this->data["deskripsi"] = "";
			$this->data["panel_tambah"] = "";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
				
				$this->load->model("master_data/m_karyawan");
				
				
				if($this->akses["pilih seluruh karyawan"]){
					$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
				}
				else{				
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
				}
                
                $this->data["list_tahun"] = $this->db->select('YEAR(tgl_berobat) as tahun')->group_by('tahun')->get('ess_biaya_kesehatan')->result();
						
				$js_header_script = "<script>
					$(document).ready(function() {
						$('.select2').select2();
					});
				</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel($np=null, $vendor=null, $filter_status, $tahun){
			
			$list = $this->m_biaya_kesehatan->get_datatable($np, @$vendor, $filter_status, $tahun);
					
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				switch ($tampil->status) {
                    case "Accounted":
                        $status = "Disetujui";
                        break;
                    case "To be accounted":
                        $status = "Dalam proses";
                        break;
                    default:
                        $status = "";
                }
				// START OF ENCRYPT NAMA FILE*/
				$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321";
				$rand_chars = "";
				$length_rand_chars=rand(8,16);

				while(strlen($rand_chars)<$length_rand_chars){
					$rand_chars .= substr($chars,rand(0,strlen($chars)),1);
				}

				$id_txt = rand(1,10000)."|"."get_id"."|".$tampil->id."|".date('Y-m-d H:i:s')."|".$rand_chars;
				$encrypted_txt_id = $this->encrypt_decrypt('encrypt', $id_txt);
				// END OF ENCRYPT NAMA FILE*/
				
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->bill_no;
				$row[] = $tampil->np_karyawan.' - '.$tampil->nama_pegawai.'<br><small>('.$tampil->nama_unit_singkat.')</small>';
				$row[] = $tampil->nama_vendor;
				$row[] = tanggal_indonesia($tampil->tgl_berobat);
				//$row[] = $tampil->status;
				$row[] = $status;
				$row[] = $tampil->deskripsi_periksa;
				$row[] = $tampil->total_hari;
				$row[] = rupiah($tampil->total_beban_karyawan, '1');
				$row[] = rupiah($tampil->total_tanggungan_karyawan, '1');
				$row[] = rupiah($tampil->total_tanggungan_perusahaan, '1');
				$row[] = "<a class='btn btn-primary btn-xs detail-periksa' data-toggle='modal' data-target='#modal_detail' data-tgl='".($tampil->tgl_berobat)."' data-bill='".$tampil->bill_no."' data-no='".$tampil->np_karyawan."'>Lihat</a>";
					
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_biaya_kesehatan->count_all($np, @$vendor, $filter_status, $tahun),
						"recordsFiltered" => $this->m_biaya_kesehatan->count_filtered($np, @$vendor, $filter_status, $tahun),
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
		}
		
		private function encrypt_decrypt($action, $string){
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$secret_key = 'zxfajaSsd1fjDwASjA12SAGSHga3yus'.date('Ymd');
			$secret_iv = 'zxASsadkmjku4jLOIh2jfGda5'.date('Ymd');
			// hash
			$key = hash('sha256', $secret_key);
			
			// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
			if ( $action == 'encrypt' ) {
				$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
				$output = base64_encode($output);
			  } else if( $action == 'decrypt' ) {
					$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
					
					$pisah 				= explode('|',$output);
					$datetime_request 	= $pisah[3];
					/*
					$datetime_expired 	= date('Y-m-d H:i:s',strtotime('+10 seconds',strtotime($datetime_request))); 

					$datetime_now		= date('Y-m-d H:i:s');
					
					if($datetime_now > $datetime_expired || !$datetime_request){
						$output = false;
					}	
					*/
				}
			return $output;
		}
		
		public function getDetailPeriksa()
		{
			$bill = $this->input->post('bill');
			$np = $this->input->post('np');
			$tgl = $this->input->post('tgl');
			$karyawan = $this->m_biaya_kesehatan->detailKaryawan($np);
			$riwayat = $this->m_biaya_kesehatan->riwayatPemeriksaan($np, $bill, $tgl);

			if($karyawan){
				$display = $this->load->view('sikesper/detail_biaya_kesehatan', [
					'data' => $karyawan,
					'riwayat' => $riwayat
				], TRUE);

				echo json_encode(['response' => $display, 'status' => 200]);
			}

		}
        
        function sum_tagihan_perusahaan(){
            $np = $this->input->post('np',true);
            $vendor = $this->input->post('vendor',true);
            $filter_status = $this->input->post('status',true);
            $tahun = $this->input->post('tahun',true);
            $sum_total = 0;
            $teks = '';
            $array_keluarga = [];
            
            try {
                $this->db->select("a.np_karyawan, a.nama_pegawai, a.nama_pasien, SUM(tanggungan_perusahaan) as total_tanggungan_perusahaan, (SELECT tipe_keluarga FROM ess_kesehatan_keluarga_tertanggung WHERE np_karyawan=a.np_karyawan AND nama_lengkap=a.nama_pasien) as tipe");
                $this->db->from('ess_biaya_kesehatan a');
                $this->db->join("mst_karyawan b", "a.np_karyawan=b.no_pokok");
                
                /*if($np!=0 && $_SESSION["grup"]!=5) {
                    $this->db->where("np_karyawan", $np);
                } else if ($_SESSION["grup"]==5) {
                    $this->db->where("np_karyawan", $_SESSION['no_pokok']);
                }*/
                
                if($np!='all') {
                    $this->db->where("a.np_karyawan", $np);
                }

                if($vendor=='1') {
                    $this->db->where("nama_vendor", "Reimbursement");
                } else if($vendor=='0') {
                    $this->db->where("nama_vendor != 'Reimbursement'");
                }

                if($filter_status=='1') {
                    $this->db->where("status", "Accounted");
                } else if($filter_status=='2') {
                    $this->db->where("status", "To be accounted");
                }
                
                if($tahun!='all') {
                    $this->db->where("YEAR(tgl_berobat)", $tahun);
                }
                
                $this->db->group_by('a.np_karyawan, a.nama_pasien');
                $get = $this->db->get()->result();
                
                if(count($get)>0){
                    foreach($get as $row){
                        $sum_total += $row->total_tanggungan_perusahaan;
                        $tipe_keluarga = $row->tipe!=null ? $row->tipe:'Karyawan';
                        if(@$array_keluarga[$tipe_keluarga]){
                            $array_keluarga[$tipe_keluarga] += $row->total_tanggungan_perusahaan;
                        } else{
                            $array_keluarga[$tipe_keluarga] = $row->total_tanggungan_perusahaan;
                        }
                    }
                    
                    $teks .= '<br><table class="col-4 pull-right">';
                    foreach($array_keluarga as $key=>$value){
                        $teks .= '<tr><td class="pull-left">'.$key.'</td><td>: Rp&nbsp;</td><td class="pull-right">'.str_replace('Rp. ','',rupiah($value,'1')).'</td></tr>';
                    }
                    $teks .= '</table>';
                }
                
                echo json_encode(['status'=>true, 'value'=>rupiah($sum_total,'1'), 'teks'=>$teks]);
            } catch(Exception $e){
                echo json_encode(['status'=>false, 'value'=>rupiah(0,'1'), 'teks'=>$teks]);
            }
        }
	}
	
	/* End of file skep.php */
	/* Location: ./application/controllers/informasi/skep.php */
