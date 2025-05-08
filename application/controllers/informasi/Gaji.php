<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Gaji extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'informasi/';
			$this->folder_model = 'informasi/';
			$this->folder_controller = 'informasi/';
			
			$this->load->helper("tanggal_helper");
			
			$this->akses = array();
						
			$this->load->model($this->folder_model."m_gaji");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Gaji";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index()
		{			
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."gaji";
								
			$this->load->view('template',$this->data);
		}
		
		public function ajax_get_gaji($id_payslip_karyawan="")
		{
			//02 02 2022, Tri Wibowo 7648, id di look di erp_payslip_karyawan harusnya hanya bisa di buka oleh np nya
			$ambil_user 	= $this->db->query("SELECT np_karyawan FROM erp_payslip_karyawan WHERE id='$id_payslip_karyawan'")->row_array();
			$np_karyawan 	= $ambil_user['np_karyawan'];
			$np 			= $_SESSION["no_pokok"];
			
			if($np_karyawan==$np)
			{
				//do nothing
			}else
			{	
				die();
			}
			//end of 02 02 2022, Tri Wibowo 7648
			
			if(!empty($id_payslip_karyawan)){
				$np 	= $_SESSION["no_pokok"];
				$gaji	= $this->m_gaji->select_payment_by_np($id_payslip_karyawan);
				
				$data["gaji"] = array();
				
				$data["total"]["Pendapatan"] = 0;
				$data["total"]["Potongan"] = 0;
				
				//7648 Tri Wibowo 22 04 2020, slip ditambah wfh
				//KETERANGAN
				$data['gaji']['tampil_keterangan'] = '0';
				
				$ambil_payment_date	= $this->m_gaji->select_payment_date_by_id_payslip($id_payslip_karyawan);		
				$payment_date		= $ambil_payment_date['payment_date'];
				
				$pisah	=explode("-",$payment_date);
				$tahun	=$pisah[0];
				$bulan	=$pisah[1];
				$tanggal=$pisah[2];
								
				//jika merupakan gaji bulanan
				if($tanggal=='25')
				{
					//kurangi satu bulan
					$date_sebelum = date('Y-m-d', strtotime($payment_date . '-1 month'));
					$ambil_wfh	= $this->m_gaji->select_wfh_by_date($np,$date_sebelum);	
					
					if($ambil_wfh>0)
					{
						$data['gaji']['tampil_keterangan'] = '1';
						$bulan_sebelum = bulan($date_sebelum);
						$data["gaji"]["wfh"][0] = array("$bulan_sebelum",$ambil_wfh);
					}
					
				}				
				
				foreach($gaji as $rincian){
					if(!isset($data["gaji"][$rincian["jenis"]])){
						$data["gaji"][$rincian["jenis"]] = array();
					}
					array_push($data["gaji"][$rincian["jenis"]],array($rincian["nama_slip"],$rincian["amount"]));
					
					if(in_array($rincian["jenis"],array("Pendapatan","Potongan"))){
						$data["total"][$rincian["jenis"]] += (int)$rincian["amount"];
					}
				}
				
				if(isset($data["gaji"]["Pendapatan"]) and isset($data["gaji"]["Potongan"])){
					$num_rows = max(count($data["gaji"]["Pendapatan"]),count($data["gaji"]["Potongan"]));
				}
				else if(isset($data["gaji"]["Pendapatan"]) and !isset($data["gaji"]["Potongan"])){
					$num_rows = count($data["gaji"]["Pendapatan"]);
				}
				else if(!isset($data["gaji"]["Pendapatan"]) and isset($data["gaji"]["Potongan"])){
					$num_rows = count($data["gaji"]["Potongan"]);
				}
				
				if(isset($data["gaji"]["Pendapatan"])){
					while(count($data["gaji"]["Pendapatan"])<$num_rows){
						$data["gaji"]["Pendapatan"][count($data["gaji"]["Pendapatan"])] = array("","");
					}
				}
				
				if(isset($data["gaji"]["Potongan"])){
					while(count($data["gaji"]["Potongan"])<$num_rows){
						$data["gaji"]["Potongan"][count($data["gaji"]["Potongan"])] = array("","");
					}
				}
				
				$this->load->view($this->folder_view."rincian_gaji",$data);
			}
		}
		
		public function tabel_gaji()
		{		
			$this->load->model($this->folder_model."m_tabel_gaji");
			
			$np 	= $_SESSION["no_pokok"];

			$list = $this->m_tabel_gaji->get_datatables($np);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;			
				$row[] = $tampil->nama_payslip;
			
				$row[] = "<button class='btn btn-primary btn-xs lihat_button' data-toggle='modal' data-target='#modal_lihat' data-id_payslip_karyawan='$tampil->id_payslip_karyawan' onclick='tampil_rincian(this)'>Lihat Informasi Pembayaran</button>";
								
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_tabel_gaji->count_all($np),
						"recordsFiltered" => $this->m_tabel_gaji->count_filtered($np),
						"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
	}
	
	/* End of file payslip.php */
	/* Location: ./application/controllers/informasi/payslip.php */