<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Menu extends CI_Controller {
		public function __construct(){
			parent::__construct();

			// Report all errors
            //error_reporting(E_ALL);

            // Display errors in output
            //ini_set('display_errors', 1);

			
			/* $meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'pegawai/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			
			$this->load->model("m_pegawai");

			$this->data['success'] = "";
			$this->data['warning'] = ""; */
		}

		public function cari_menu(){
			foreach($_POST as $key=>$value){
				$$key = $value;
			}
			
			$this->load->model("m_menu");
			$id_grup = $this->session->userdata("grup");
		
			$daftar_menu_grup_pengguna = $this->m_menu->daftar_menu_grup_pengguna($id_grup);

			$hasil = "";
			$banyak = 0;
			for($i=0;$i<count($daftar_menu_grup_pengguna);$i++){
				$urutan = $this->m_menu->cari_urutan_menu($daftar_menu_grup_pengguna[$i]["id_master_menu"],$cari_menu);
				if(count($urutan)>0){				
					$arr_urutan = array();
					if(count($urutan)>0){
						for($j=0;$j<count($urutan);$j++){
							array_push($arr_urutan,$urutan[$j]["urutan"]);
						}
					}
					
					for($j=0;$j<count($arr_urutan);$j++){
						$strlen = strlen($arr_urutan[$j]);
						while($strlen>2){
							$strlen -= 2;
							array_push($arr_urutan,substr($arr_urutan[$j],0,$strlen));
						}
					}
					
					$arr_urutan = array_unique($arr_urutan);
					sort($arr_urutan);
					
					$arr_cari_menu = $this->m_menu->cari_menu($daftar_menu_grup_pengguna[$i]["id_master_menu"],$cari_menu,$arr_urutan);
					
					$arr_menu = array();
					
					if(count($arr_cari_menu)>0){
						//var_dump($arr_cari_menu);echo "<br>";
						$arr_cari_menu = $arr_cari_menu->result_array();
						for($j=0;$j<count($arr_cari_menu);$j++){
							$arr_menu[$arr_cari_menu[$j]["level"]] = preg_replace("/".$cari_menu."/","<b>".$cari_menu."</b>",$arr_cari_menu[$j]["nama"]);
							//echo $arr_menu[$arr_cari_menu[$j]["level"]]."===".$arr_cari_menu[$j]["level"]."===".count($arr_menu)."<br>";
							//var_dump($arr_menu);echo "<br>";
							if($arr_cari_menu[$j]["level"] < count($arr_menu)){
								$count_arr = count($arr_menu);
								for($k=1;$k<=$count_arr;$k++){
									//echo $k."xxx".$arr_cari_menu[$j]["level"];
									if($k>$arr_cari_menu[$j]["level"]){
										unset($arr_menu[$k]);//echo "xxx hapus";
									}
									//echo "<br>";
								}
							}
							//var_dump($arr_menu);echo "<br>";
							//echo "<br>";
							if(strcmp($arr_cari_menu[$j]["url"],"#")!=0){
								$hasil .= "<li><a href='".base_url($arr_cari_menu[$j]["url"])."'>".implode(" > " , $arr_menu)."</a></li>";
								$banyak++;
							}
						}
					}
					
				}
			}
			
			if(!empty($hasil)){
				$hasil = "<ul>".$hasil."</ul>";
			}
			else{
				$hasil = "<ul><li><a href='#'><i>menu yang dicari tidak ditemukan.</i></a></li></ul>";
			}
			$data["hasil"] = $hasil;
			$data["banyak"] = $banyak;
			echo json_encode($data);
				//var_dump($arr_cari_menu);
				//echo "<br><br>";
		}
	}
	