<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Penggajian extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_controller = 'osdm/';
			
			$this->akses = array();
			
			$this->load->model("informasi/m_gaji");
			$this->load->model($this->folder_model."m_penggajian");
		
			$this->load->helper("tanggal_helper");
			
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Penggajian";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->data['url'] = $this->m_setting->ambil_url_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			$this->data["akses"] = $this->akses;
			
			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			array_push($this->data['js_sources'],"osdm/penggajian");
			
			izin($this->akses["akses"]);
		}
		
		public function index()
		{			
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."penggajian";

			$this->load->view('template',$this->data);
		}
		
		public function tabel_gaji()
		{
			$list = $this->m_penggajian->get_datatables();

			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;			
				$row[] = $tampil->nama_payslip;
				$row[] = tanggal($tampil->payment_date);
				$row[] = $tampil->penerima;
				$row[] = $tampil->dengan_slip;
				$row[] = tanggal_waktu($tampil->start_display);
			
				if($this->akses["lihat pembayaran"]){
					$row[] = "<a class='btn btn-primary btn-xs lihat_button' href='".base_url($this->data['url'])."/lihat_pembayaran/$tampil->id'>Lihat Pembayaran</a>";
				}
				else{
					$row[] = "";
				}
								
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_penggajian->count_all(),
						"recordsFiltered" => $this->m_penggajian->count_filtered(),
						"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function tabel_rincian_gaji($id_header)
		{
			$this->load->model($this->folder_model."m_penggajian_rincian");
			$list = $this->m_penggajian_rincian->get_datatables($id_header);

			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;			
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->kode_unit." - ".$tampil->nama_unit;
				$row[] = $tampil->kode_jabatan." - ".$tampil->nama_jabatan;
				$row[] = $tampil->nama_payslip;
			
				if($this->akses["lihat pembayaran"] and (bool)$tampil->with_payslip){
					$row[] = "<button class='btn btn-primary btn-xs lihat_button' onclick='cetak(\"$tampil->np_karyawan\")'>Cetak</button>";
				}
				else{
					$row[] = "";
				} 

				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_penggajian_rincian->count_all($id_header),
						"recordsFiltered" => $this->m_penggajian_rincian->count_filtered($id_header),
						"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function lihat_pembayaran($id_header){
			// Report all errors
            //error_reporting(E_ALL);

            // Display errors in output
            //ini_set('display_errors', 1);
			
			if(!empty($this->input->post())){
				if(!empty($this->input->post("np_karyawan"))){
					$this->request_cetak($id_header,$this->input->post("np_karyawan"));
				}
				else if(!empty($this->input->post("aksi"))){
					if(strcmp($this->input->post("aksi"),"ubah")==0){
						$ubah = $this->ubah_header($id_header,$this->input->post("waktu_publikasi"),$this->input->post("pesan_baris_1"),$this->input->post("pesan_baris_2"));
						
						if($ubah["status"]){
							$this->data['success'] = "Perubahan Data Header Slip Gaji berhasil dilakukan.";
						}
						else{
							$this->data['warning'] = $ubah['error_info'];
						}
					}
				}
			}
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."lihat_penggajian";
			
			$this->data['panel_ubah'] = "off";
			$this->data['panel_cetak'] = "off";
			
			$this->data["id_header"] = $id_header;
			$this->data["header_penggajian"] = $this->m_penggajian->get_data_penggajian($id_header);
			
			$this->load->view('template',$this->data);
		}
		
		private function ubah_header($id_header,$waktu_publikasi,$pesan_baris_1,$pesan_baris_2){
			$return = array("status" => false, "error_info" => "");
			
			$set = array('start_display'=>$waktu_publikasi,'pesan_1'=>$pesan_baris_1,'pesan_2'=>$pesan_baris_2);
			
			$arr_data_lama = $this->m_penggajian->get_data_penggajian($id_header);
			
			$log_data_lama = "";
				
			foreach($arr_data_lama as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
			
			$this->m_penggajian->ubah($set,$id_header);
			
			if($this->m_penggajian->cek_hasil_ubah($id_header,$waktu_publikasi,$pesan_baris_1,$pesan_baris_2)){
				$return["status"] = true;
				
				$log_data_baru = "";
				foreach($set as $key => $value){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
				
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $arr_data_lama["id"],
					"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Perubahan Data Header Slip Gaji <b>Gagal</b> Dilakukan.";
			}
			
			return $return;
		}
		
		public function cetak_slip(){
	        ob_start();
			
			$gaji = array();
			$ct = array();
			$cb = array();
			$hc = array();
			
			$result_gaji = array();
			$result_ct = array();
			$result_cb = array();
			$result_hc = array();
			
			$result_gaji = $this->m_penggajian->get_slip_request();
			$result_ct = $this->m_penggajian->get_cuti_tahunan();
			$result_cb = $this->m_penggajian->get_cuti_besar();
			$result_hc = $this->m_penggajian->get_hutang_cuti();
			
			$id_header = 0;

			foreach($result_gaji as $rincian){
				if(!isset($gaji[$rincian["np_karyawan"]])){
					$id_header = $rincian["id_payslip_header"];
					$gaji[$rincian["np_karyawan"]]["nama_pembayaran"] = $rincian["nama_pembayaran"];
					$gaji[$rincian["np_karyawan"]]["nama_karyawan"] = $rincian["nama_karyawan"];
					$gaji[$rincian["np_karyawan"]]["kode_unit"] = $rincian["kode_unit"];
					$gaji[$rincian["np_karyawan"]]["nama_unit"] = $rincian["nama_unit"];
					$gaji[$rincian["np_karyawan"]]["nama_jabatan"] = $rincian["nama_jabatan"];
					$gaji[$rincian["np_karyawan"]]["total"]["Pendapatan"] = 0;
					$gaji[$rincian["np_karyawan"]]["total"]["Potongan"] = 0;
				}
				if(!isset($gaji[$rincian["np_karyawan"]][$rincian["jenis"]])){
					$gaji[$rincian["np_karyawan"]][$rincian["jenis"]] = array();
				}

				array_push($gaji[$rincian["np_karyawan"]][$rincian["jenis"]],array($rincian["nama_slip"],$rincian["amount"]));
				if(in_array($rincian["jenis"],array("Pendapatan","Potongan"))){
					$gaji[$rincian["np_karyawan"]]["total"][$rincian["jenis"]] += (int)$rincian["amount"];
				}
			}
			
			foreach($result_ct as $cuti){
				$ct[$cuti["np_karyawan"]][$cuti["tahun"]]["sisa"] = $cuti["sisa"];
				$ct[$cuti["np_karyawan"]][$cuti["tahun"]]["berlaku"] = $cuti["berlaku"];
			}
			
			foreach($result_cb as $cuti){
				$cb[$cuti["np_karyawan"]][$cuti["tahun"]]["sisa_bulan"] = $cuti["sisa_bulan"];
				$cb[$cuti["np_karyawan"]][$cuti["tahun"]]["sisa_hari"] = $cuti["sisa_hari"];
				$cb[$cuti["np_karyawan"]][$cuti["tahun"]]["berlaku"] = $cuti["berlaku"];
			}
			
			foreach($result_hc as $cuti){
				$hc[$cuti["no_pokok"]] = $cuti["hutang"];
			}
			
			$this->load->library("fpdf_lib");
	        
			$slip_width = 210;
			$slip_height = 140;
			$slip_per_page = 2;
			
			$paper_width = $slip_width;
			$paper_height = $slip_per_page*$slip_height;
			
			$orientation = "P";
			if($paper_width>$paper_height){
				$orientation = "L";
			}

			$pdf = new $this->fpdf_lib($orientation,"mm",array($paper_width,$paper_height));
			
			$margin["left"] = 5;
			$margin["top"] = 20;
			$margin["right"] = 5;
			$margin["bottom"] = 5;
		
			$pdf->SetMargins($margin["left"], $margin["top"], $margin["right"]);
			$pdf->SetAutoPageBreak(true,$margin["bottom"]);
			$w_area = (int)$pdf->get_width()-(int)$pdf->get_margin("l")-(int)$pdf->get_margin("r");
			$h_area = (int)$pdf->get_height()-(int)$pdf->get_margin("t")-(int)$pdf->get_margin("b");
			
			$urutan = 1;
			
			$line = 0;
			
			$w_header = array(18,3,120);
			$w_header[3] = $w_area-array_sum($w_header);
			$h_header = 5;
			
			$pesan = $this->m_penggajian->get_pesan_slip($id_header);
			
			$arr_pesan = array();
			
			$w_pesan = $w_area;
			$h_pesan = 5;
			
			$jarak_atas_pesan = 8;
			$jarak_bawah_pesan = 8;
			
			$w_body = array(45,20,6,45,20);
			$h_body = 5;
			
			$h_generate = 4;
			
			$line_length_cuti=array_sum($w_body);
			
			$x_cuti_start = array_sum($w_body)+11;
			$x_cuti_end = $w_area+(int)$pdf->get_margin("l");
			
			$w_cuti[0] = 11;
			$w_cuti[1] = 6;
			$w_cuti[2] = 11;
			$w_cuti[3] = $w_cuti[1];
			$w_cuti[4] = $x_cuti_end - $x_cuti_start - array_sum($w_cuti);
			
			foreach ($gaji as $np => $penggajian) {
				if($urutan%2==1){
					$pdf->AddPage();
				}
				else{
					$pdf->SetY($slip_height+$margin["top"]);
				}
				
				$pdf->SetFont('Arial','B',14);
				$pdf->Cell($w_area,9,"SLIP PEMBAYARAN ".strtoupper($penggajian["nama_pembayaran"]),0,1,"C");
				
				$pdf->SetY($pdf->GetY()+3);
				
				$pdf->SetFont('Arial','',9);				
				$pdf->Cell($w_header[0],$h_header,"Unit Kerja",$line,0,"L");
				$pdf->Cell($w_header[1],$h_header,":",$line,0,"L");
				$pdf->Cell($w_header[2],$h_header,$penggajian["kode_unit"]." - ".preg_replace("/–/","-",$penggajian["nama_unit"]),$line,0,"L");
				$pdf->Cell($w_header[3],$h_header,"No. Urut : ".$urutan,$line,1,"R");
				$urutan++;
				
				$pdf->Cell($w_header[0],$h_header,"Karyawan",$line,0,"L");
				$pdf->Cell($w_header[1],$h_header,":",$line,0,"L");
				$pdf->Cell($w_header[2]+$w_header[3],$h_header,$np." - ".$penggajian["nama_karyawan"]." (".$penggajian["nama_jabatan"].")",$line,1,"L");
				
				$pdf->Ln($jarak_atas_pesan);
				$pdf->Cell($w_pesan,$h_pesan,$pesan["pesan_1"],$line,1,"C");
				$pdf->Cell($w_pesan,$h_pesan,$pesan["pesan_2"],$line,1,"C");
				$pdf->Ln($jarak_bawah_pesan);
			
				$y_data = $pdf->GetY();
				
				$pdf->Line($pdf->GetX(),$pdf->GetY(),$pdf->GetX()+$line_length_cuti,$pdf->GetY());

				$pdf->SetFont('Arial','B',9);
				$pdf->Cell($w_body[0]+$w_body[1],$h_body,"PENDAPATAN",$line,0,"C");
				$pdf->Cell($w_body[2],$h_body,"",$line,0,"C");
				$pdf->Cell($w_body[3]+$w_body[4],$h_body,"POTONGAN",$line,1,"C");
				
				$pdf->Line($pdf->GetX(),$pdf->GetY(),$pdf->GetX()+$line_length_cuti,$pdf->GetY());
				
				if(isset($penggajian["Pendapatan"]) and isset($penggajian["Potongan"])){
					$num_rows = max(count($penggajian["Pendapatan"]),count($penggajian["Potongan"]));
				}
				else if(isset($penggajian["Pendapatan"]) and !isset($penggajian["Potongan"])){
					$num_rows = count($penggajian["Pendapatan"]);
				}
				else if(!isset($penggajian["Pendapatan"]) and isset($penggajian["Potongan"])){
					$num_rows = count($penggajian["Potongan"]);
				}
				
				$pdf->SetFont('Arial','',9);
				
				for($i=0;$i<$num_rows;$i++){
					if(isset($penggajian["Pendapatan"][$i])){
						$text = $penggajian["Pendapatan"][$i][0];
					}
					else{
						$text = "";
					}
					$pdf->Cell($w_body[0],$h_body,$text,$line,0,"L");
					
					if(isset($penggajian["Pendapatan"][$i])){
						$text = number_format($penggajian["Pendapatan"][$i][1],0,"",".");
					}
					else{
						$text = "";
					}
					$pdf->Cell($w_body[1],$h_body,$text,$line,0,"R");
					
					$pdf->Cell($w_body[2],$h_body,"",$line,0,"R");
					
					if(isset($penggajian["Potongan"][$i])){
						$text = $penggajian["Potongan"][$i][0];
					}
					else{
						$text = "";
					}
					$pdf->Cell($w_body[3],$h_body,$text,$line,0,"L");
					
					if(isset($penggajian["Potongan"][$i])){
						$text = number_format($penggajian["Potongan"][$i][1],0,"",".");
					}
					else{
						$text = "";
					}
					$pdf->Cell($w_body[4],$h_body,$text,$line,1,"R");
				}
				
				$pdf->Cell($w_body[0],$h_body,"Total Pendapatan",$line,0,"L");
				$pdf->Cell($w_body[1],$h_body,number_format($penggajian["total"]["Pendapatan"],0,"","."),$line,0,"R");
				$pdf->Cell($w_body[2],$h_body,"",$line,0,"R");
				$pdf->Cell($w_body[3],$h_body,"Total Potongan",$line,0,"L");
				$pdf->Cell($w_body[4],$h_body,number_format($penggajian["total"]["Potongan"],0,"","."),$line,1,"R");
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->Line($pdf->GetX(),$pdf->GetY(),$pdf->GetX()+$line_length_cuti,$pdf->GetY());
				
				if(isset($penggajian["Penghasilan"][0])){
					$text = $penggajian["Penghasilan"][0][0];
				}
				else{
					$text = "xxxxxxxxxxxx";
				}
				$pdf->Cell($w_body[0]+$w_body[1],$h_body,$text,$line,0,"L");
				
				$pdf->Cell($w_body[2],$h_body,"",$line,0,"L");
				
				if(isset($penggajian["Penghasilan"][0])){
					$text = number_format($penggajian["Penghasilan"][0][1],0,"",".");
				}
				else{
					$text = "";
				}
				$pdf->Cell($w_body[3]+$w_body[4],$h_body,$text,$line,1,"R");
				
				$pdf->Line($pdf->GetX(),$pdf->GetY(),$pdf->GetX()+$line_length_cuti,$pdf->GetY());
				
				$pdf->SetY($y_data);
				$pdf->SetX($x_cuti_start);
				
				if(isset($ct[$np])){
					$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());

					$pdf->SetFont('Arial','B',9);
					$pdf->Cell(array_sum($w_cuti),$h_body,"CUTI TAHUNAN",$line,1,"C");
					
					$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());

					$pdf->SetFont('Arial','',9);

					$pdf->SetX($x_cuti_start);
					$pdf->Cell($w_cuti[0],$h_body,"Tahun",$line,0,"C");
					$pdf->Cell($w_cuti[1]+$w_cuti[2]+$w_cuti[3],$h_body,"Sisa Hak",$line,0,"C");
					$pdf->Cell($w_cuti[4],$h_body,"Sampai Dengan",$line,1,"C");

					foreach($ct[$np] as $tahun=>$cuti_tahunan){
						$pdf->SetX($x_cuti_start);
						$pdf->Cell($w_cuti[0],$h_body,$tahun,$line,0,"C");
						$pdf->Cell($w_cuti[1],$h_body,"",$line,0,"R");
						$pdf->Cell($w_cuti[2],$h_body,$cuti_tahunan["sisa"]." hari",$line,0,"R");
						$pdf->Cell($w_cuti[3],$h_body,"",$line,0,"R");
						$pdf->Cell($w_cuti[4],$h_body,$cuti_tahunan["berlaku"],$line,1,"C");
					}
					$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());
					
				}
				
				if(isset($cb[$np])){

					$pdf->SetFont('Arial','B',9);
					$pdf->SetX($x_cuti_start);
					$pdf->Cell(array_sum($w_cuti),$h_body,"CUTI BESAR",$line,1,"C");
					
					$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());

					$pdf->SetFont('Arial','',9);

					$pdf->SetX($x_cuti_start);
					$pdf->Cell($w_cuti[0],$h_body,"Tahun",$line,0,"C");
					$pdf->Cell($w_cuti[1]+$w_cuti[2]+$w_cuti[3],$h_body,"Sisa Hak",$line,0,"C");
					$pdf->Cell($w_cuti[4],$h_body,"Sampai Dengan",$line,1,"C");

					foreach($cb[$np] as $tahun=>$cuti_besar){
						$sisa = "";
						if((int)$cuti_besar["sisa_bulan"]>0){
							$sisa .= (int)$cuti_besar["sisa_bulan"]. " bulan";
						}
						if((int)$cuti_besar["sisa_hari"]>0){
							if(!empty($sisa)){
								$sisa .= " ";
							}
							$sisa .= (int)$cuti_besar["sisa_hari"]. " hari";
						}
						$pdf->SetX($x_cuti_start);
						$pdf->Cell($w_cuti[0],$h_body,$tahun,$line,0,"C");
						$pdf->Cell($w_cuti[1]+$w_cuti[2]+$w_cuti[3],$h_body,$sisa,$line,0,"R");
						$pdf->Cell($w_cuti[4],$h_body,$cuti_besar["berlaku"],$line,1,"C");
					}
					$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());
				}
				
				if(isset($hc[$np])){
					$pdf->SetFont('Arial','',9);
					$pdf->SetX($x_cuti_start);
					$pdf->Cell(array_sum($w_cuti),$h_body,"Hutang Cuti : $hc[$np]",$line,1,"L");
					
					$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());
				}
						
					//7648 Tri Wibowo 22 04 2020, slip ditambah wfh
					//KETERANGAN
					//$data['gaji']['tampil_keterangan'] = '0';
				
					$ambil_payment_date	= $this->m_penggajian->select_payment_date_by_id_payslip_header($id_header);		
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
							$bulan_sebelum = bulan($date_sebelum);
														
							$pdf->SetFont('Arial','B',9);
							$pdf->SetX($x_cuti_start);
							$pdf->Cell(array_sum($w_cuti),$h_body,"WFH",$line,1,"C");
							
							$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());

							$pdf->SetFont('Arial','',9);

							$pdf->SetX($x_cuti_start);
							$pdf->Cell($w_cuti[0],$h_body,"Bulan",$line,0,"C");
							$pdf->Cell($w_cuti[1]+$w_cuti[2]+$w_cuti[3],$h_body,"Hari",$line,1,"C");
							
							$pdf->SetX($x_cuti_start);
							$pdf->Cell($w_cuti[0],$h_body,$bulan_sebelum,$line,0,"C");						
							$pdf->Cell($w_cuti[1]+$w_cuti[2]+$w_cuti[3],$h_body,$ambil_wfh,$line,1,"C");
							
							$pdf->Line($x_cuti_start,$pdf->GetY(),$x_cuti_end,$pdf->GetY());
							
						}
						
					}			


					
					
				
				
				$pdf->SetY((($urutan%2)+1)*$slip_height-3*$h_generate);
				$pdf->SetX($x_cuti_start);
				$pdf->SetFont('Arial','I',6);
				$pdf->Cell(array_sum($w_cuti),$h_generate,"dihasilkan dari sistem pada ".tanggal_waktu(date("Y-m-d H:i:s")),$line,1,"R");
				
				if($urutan%2==1){
					$pdf->Line(0,$slip_height,$paper_height,$slip_height);					
					$pdf->SetX(0);
				}
			}
			
	        $pdf->Output();
			$this->m_penggajian->update_request_cetak();
		}
	
		public function semua_karyawan($id_header){
			$arr_result = $this->m_penggajian->np_karyawan_penggajian($id_header);
			$list_np = "";
			for($i=0;$i<count($arr_result);$i++){
				$list_np .= $arr_result[$i]["np_karyawan"];
				if($i<count($arr_result)-1){
					$list_np .= ",";
				}
			}
			echo $list_np;
		}
		
		private function request_cetak($id_header,$list){
			$list_np = array();
			
			$list_np = explode(",",$list);
			
			$this->load->model("outbound_sap/m_outbound_payslip");
			
			$arr_insert_data = array();
			
			foreach($list_np as $np){
				$id_payslip_karyawan = $this->m_outbound_payslip->get_id_payment_karyawan($id_header,$np)[0]["id"];
				$insert_data = array(
									"id_payslip_karyawan" => $id_payslip_karyawan,
									"id_requester" => $_SESSION["id_pengguna"],
									"status" => "REQUEST",
									"waktu_permintaan" => date("Y-m-d H:i:s")
								);
				array_push($arr_insert_data,$insert_data);
			}
			//$this->m_penggajian->tambah_request_cetak($id_payslip_karyawan, $_SESSION["no_pokok"],"REQUEST");
			$this->m_penggajian->tambah_request_cetak($arr_insert_data);
			echo "<script>window.open(\"".base_url($this->data['url'])."/cetak_slip\")</script>";
		}
		
		public function pilih_karyawan(){
			//list($kontrak_kerja_terpilih,$unit_kerja_terpilih) = explode("=",$pilihan);
			if(isset($_POST["kontrak_kerja_terpilih"])){
				$kontrak_kerja_terpilih = $_POST["kontrak_kerja_terpilih"];
			}
			else{
				$kontrak_kerja_terpilih = "";
			}
			
			if(isset($_POST["unit_kerja_terpilih"])){
				$unit_kerja_terpilih = $_POST["unit_kerja_terpilih"];
			}
			else{
				$unit_kerja_terpilih = "";
			}
			
			$this->load->model("master_data/m_satuan_kerja");
			$this->data["daftar_satuan_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja();
			
			$this->load->model("master_data/m_karyawan");
			$this->data["daftar_kontrak_kerja"] = $this->m_karyawan->daftar_kontrak_kerja();
			
			if(!empty($kontrak_kerja_terpilih)){
				$this->data["kontrak_kerja_terpilih"] = explode(",",$kontrak_kerja_terpilih);
			}
			else{
				$this->data["kontrak_kerja_terpilih"] = array();
			}
			
			if(!empty($unit_kerja_terpilih)){
				$this->data["unit_kerja_terpilih"] = explode(",",$unit_kerja_terpilih);
			}
			else{
				$this->data["unit_kerja_terpilih"] = array();
			}
			
			$this->data["karyawan_terpilih"] = array();
			if(!empty($this->data["kontrak_kerja_terpilih"]) and !empty($this->data["unit_kerja_terpilih"])){
				$arr_kontrak_kerja=explode(",",$kontrak_kerja_terpilih);
				$arr_kode_unit=explode(",",$unit_kerja_terpilih);
				$this->data["karyawan_terpilih"] = $this->m_karyawan->filter_karyawan($arr_kontrak_kerja,$arr_kode_unit);
			}
			
			$this->load->view($this->folder_view."ajax/penggajian",$this->data);
		}
		
		public function filter_karyawan(){
			if(isset($_POST["kontrak_kerja_terpilih"])){
				$kontrak_kerja_terpilih = $_POST["kontrak_kerja_terpilih"];
			}
			else{
				$kontrak_kerja_terpilih = "";
			}
			$arr_kontrak_kerja=explode(",",$kontrak_kerja_terpilih);
			
			if(isset($_POST["unit_kerja_terpilih"])){
				$unit_kerja_terpilih = $_POST["unit_kerja_terpilih"];
			}
			else{
				$unit_kerja_terpilih = "";
			}
			$arr_kode_unit=explode(",",$unit_kerja_terpilih);
			
			$this->load->model("master_data/m_karyawan");
			$this->data["karyawan_terpilih"] = $this->m_karyawan->filter_karyawan($arr_kontrak_kerja,$arr_kode_unit);
			
			$this->load->view($this->folder_view."ajax/penggajian",$this->data);
		}
	}
	
	/* End of file penggajian.php */
	/* Location: ./application/controllers/osdm/penggajian.php */	