<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Biodata extends CI_Controller {
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
					
			$this->load->model($this->folder_model."m_keluarga_tertanggung");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Biodata";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index() {
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."keluarga_tertanggung";

			$this->data['panel_tambah'] = "in";

			$this->data["tanggal"] = "";
			$this->data["deskripsi"] = "";
			$this->data["panel_tambah"] = "";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
				
				$this->load->model("master_data/m_karyawan");
				$this->load->model("master_data/m_satuan_kerja");
				
				if($this->akses["pilih seluruh karyawan"]){
					$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
					$this->data["daftar_akses_unit"] = $this->m_satuan_kerja->daftar_satuan_kerja();
				}
				else{				
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
					$this->data["daftar_akses_unit"] = array(array("kode_unit"=>$this->session->userdata("kode_unit"),"nama_unit"=>$this->session->userdata("nama_unit")));
				}

				// var_dump($this->data["daftar_akses_karyawan"]);exit;
						
				$js_header_script = "<script>
					$(document).ready(function() {
						$('.select2').select2();
					});
				</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel($np=0, $unit=0){
			
			$list = $this->m_keluarga_tertanggung->get_datatable($np, $unit);
					
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				
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
				$row[] = $tampil->no_pokok.' - '.$tampil->nama.'<br><small>('.$tampil->nama_unit_singkat.')</small>';
				$row[] = $tampil->tempat_lahir.'<br><small>'.tanggal_indonesia($tampil->tanggal_lahir).'</small>';
				$row[] = $tampil->usia;
				//$row[] = tanggal_indonesia($tampil->start_date);
				$row[] = $tampil->jenis_kelamin;
				//$row[] = $tampil->bpjs_id;
				$row[] = $tampil->bpjs_kesehatan;
				$row[] = $tampil->class_bpjs!=null ? $tampil->class_bpjs:'I';
				$row[] = $tampil->kelas;
				$row[] = "<a class='btn btn-".(($tampil->jumlah > 0) ? 'primary' : 'danger')." btn-xs detail-keluarga' data-toggle='modal' data-target='#modal_detail' data-no='".$tampil->no_pokok."'>".$tampil->jumlah." Keluarga</a>";
					
				$data[] = $row;
			}

			$output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_keluarga_tertanggung->count_all($np, $unit),
                "recordsFiltered" => $this->m_keluarga_tertanggung->count_filtered($np, $unit),
                "data" => $data
            );
            
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
		
		public function getDetailKeluarga() {
			$np = $this->input->post('np');
			$karyawan = $this->m_keluarga_tertanggung->detailKaryawan($np);
			$keluarga = $this->m_keluarga_tertanggung->detail_keluarga($np);
			
			/*if($karyawan){
				$display = $this->load->view('sikesper/detail_keluarga_tertanggung', [
					'data' => $karyawan,
					'keluarga' => $keluarga
				], TRUE);

				echo json_encode(['response' => $display, 'status' => 200]);
			}*/
            
            $this->load->view('sikesper/detail_keluarga_tertanggung', [
                'data' => $karyawan,
                'keluarga' => $keluarga
            ]);
		}
        
        function ambil_karyawan(){
            try {
                if($this->akses["pilih seluruh karyawan"]){
                	$return='<option value="all">Semua karyawan</option>';
                } else {
                	$return='';
                }
                $kode_unit = $this->input->post('unit',true);

                if($this->akses["pilih seluruh karyawan"]){
					// $get = $this->m_karyawan->daftar_karyawan();
	                $get = $this->db->select('no_pokok,nama')
	                    ->where('kode_unit',$kode_unit)
	                    ->get('mst_karyawan')
	                    ->result();
	                foreach($get as $row){
	                    $return .= '<option value="'.$row->no_pokok.'">'.$row->nama.'</option>';
	                }
				}
				else{			
					$return .= '<option value="'.$this->session->userdata("no_pokok").'">'.$this->session->userdata("no_pokok").' - '.$this->session->userdata("nama").'</option>';	
				}

                echo $return;
            } catch(Exception $e){
                echo '';
            }
        }
	}
	
	/* End of file skep.php */
	/* Location: ./application/controllers/informasi/skep.php */
