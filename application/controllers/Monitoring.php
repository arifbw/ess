<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Monitoring extends CI_Controller {
		private $data = array();
		public function __construct(){
			parent::__construct();
			
			$this->load->model("m_setting");
			$this->load->model("M_monitoring","monitoring");

			if(empty($this->session->userdata("username"))){
				redirect(base_url($this->m_setting->ambil_url_modul("login")));
			}
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");

			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
            
            $this->nama_db = $this->db->database;
			
			$this->data['is_with_sidebar'] = true;
		}
        
        function index(){
            redirect(base_url('monitoring/harian'));
        }

		public function harian($tahun_bulan=null){
			coming_soon();
            //$tahun_bulan = $this->input->post('filter_tahun_bulan', true);
            if(@$tahun_bulan){
                $tahun_bulan_tampil = $tahun_bulan;
                $lastday = date('t',strtotime("$tahun_bulan-01"));
                $first_date = date("$tahun_bulan-01");
                
                $last_date_str = "$tahun_bulan-$lastday";
                $last_date = date('Y-m-d', strtotime($last_date_str));
            } else{
                $tahun_bulan_tampil = date('Y-m');
                $first_date = date("Y-m-01");
                $last_date = date("Y-m-t");
            }
            
            $all_array_fix = [];
            
            //get bulan
            $arr_tahun_bulan = $this->monitoring->get_tahun_bulan();
            
            //get daily process
            $get_mst_proses = $this->monitoring->get_mst_proses('daily')->result();
            foreach($get_mst_proses as $row){
                
                $date_to_process = $first_date;
                if($row->in_out=='1'){
                    $tabel_proses = 'ess_status_proses_input';
                } else if($row->in_out=='0'){
                    $tabel_proses = 'ess_status_proses_output';
                } else{
                    continue;
                }
                
                while($date_to_process<=$last_date){
                    //set default
                    $cal_start = $date_to_process;
                    $cal_title = $row->nama_file;
                    $cal_backgroundColor = '#f56954';
                    $cal_borderColor = '#f56954';
                    $desc = "Nama file: $row->nama_file";
                    
                    //search row in table
                    $get_proses_rows = $this->monitoring->get_from_id_and_date($tabel_proses, $row->id, $date_to_process);
                    if($get_proses_rows->num_rows()>0){
                        $desc .= "<br>Waktu proses:";
                        $data_proses = $get_proses_rows->result();
                        foreach($data_proses as $row_proses){
                            $desc .= "<br>$row_proses->waktu,";
                        }
                        $cal_backgroundColor = '#00a65a';
                        $cal_borderColor = '#00a65a';
                    }
                    
                    $all_array_fix[] = [
                        'title'=>$cal_title,
                        'start'=>$cal_start,
                        'backgroundColor'=>$cal_backgroundColor,
                        'borderColor'=>$cal_borderColor,
                        'description'=>$desc
                    ];
                    
                    //date ++
                    $date_to_process = date('Y-m-d', strtotime($date_to_process . "+1 days"));
                }
            }
            //echo json_encode($all_array_fix); exit();

			$this->data['content'] = 'monitoring_harian';
            $this->data['daily'] = $all_array_fix;
            //$this->data['tabel_bulanan'] = $get_mst_proses_monthly;
            $this->data['tahun_bulan'] = $arr_tahun_bulan;
            $this->data['tahun_bulan_tampil'] = $tahun_bulan_tampil;
            
			//$this->data["navigasi_menu"] = menu_helper();
			//$this->data['is_tnav'] = true;
            
			$this->load->view('template',$this->data);
		}

		public function bulanan($tahun_bulan=null){
			coming_soon();
            //$tahun_bulan = $this->input->post('filter_tahun_bulan', true);
            if(@$tahun_bulan){
                $tahun_bulan_tampil = $tahun_bulan;
                $first_date = date("$tahun_bulan-01");
                $last_date = date("$tahun_bulan-t");
            } else{
                $tahun_bulan_tampil = date('Y-m');
                $first_date = date("Y-m-01");
                $last_date = date("Y-m-t");
            }
            
            //get bulan
            $arr_tahun_bulan = $this->monitoring->get_tahun_bulan();
            
            //get monthly process
            $get_mst_proses_monthly = $this->monitoring->get_monthly_proses($tahun_bulan_tampil)->result();

			$this->data['content'] = 'monitoring_bulanan';
            //$this->data['daily'] = $all_array_fix;
            $this->data['tabel_bulanan'] = $get_mst_proses_monthly;
            $this->data['tahun_bulan'] = $arr_tahun_bulan;
            $this->data['tahun_bulan_tampil'] = $tahun_bulan_tampil;
            
			//$this->data["navigasi_menu"] = menu_helper();
			//$this->data['is_tnav'] = true;
            
			$this->load->view('template',$this->data);
		}
	}
	