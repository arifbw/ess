<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Resize_gambar extends CI_Controller {
		
		public function __construct(){
			parent::__construct();
		}

		public function index(){
			ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

			$folder_source = "file/kehadiran/";
			//$folder_dest = "assets/media/a/";
			$folder_dest = "file/kehadiran/";

			// File and new size
			$arr_files = scandir($folder_source);
			$count_files = count($arr_files);

			for($i=0;$i<$count_files;$i++){ echo __LINE__." $i<br>";
				if(strlen($arr_files[$i])>2){
					$filename = $arr_files[$i];
					$filename_src = $folder_source.$filename;
					$filename_dest = $folder_dest.$filename;

					list($width, $height) = getimagesize($filename_src);
					
					if(!empty($width) and !empty($height)){ echo __LINE__." $i $filename_dest<br>";
						//rename($filename_src,$filename_src."_temp");
						$width_resize = 0;
						$height_resize = 0;
						
						$rasio_height = 1;
						$rasio_width = 1;
						
						$landscape["width"] = 800;
						$landscape["height"] = 600;
						
						$potrait["width"] = 600;
						$potrait["height"] = 800;
						
						$square["width"] = 600;
						$square["height"] = 600;

						$rasio_height = 1;
						$rasio_width = 1;
						$format = "";
						
						if($width>$height){
							$format = "landscape";
							
							if($width>$landscape["width"] or $height>$landscape["height"]){
								$rasio_width = $width/$landscape["width"];
								$rasio_height = $height/$landscape["height"];
							}
						}
						else if($width<$height){
							$format = "potrait";
							
							if($width>$potrait["width"] or $height>$potrait["height"]){
								$rasio_width = $width/$potrait["width"];
								$rasio_height = $height/$potrait["height"];
							}
						}
						else{
							$format = "square";
							
							if($width>$square["width"] or $height>$square["height"]){
								$rasio_width = $width/$square["width"];
								$rasio_height = $height/$square["height"];
							}
						}
						
						$rasio = min($rasio_width,$rasio_height);

						if($rasio>1){
							$width_resize = (int)($width/$rasio);
							$height_resize = (int)($height/$rasio);
							

							// Content type
							//header('Content-Type: image/jpeg');

							// Load
							$thumb = imagecreatetruecolor($width_resize, $height_resize);

							if(strcmp(strtolower(substr($filename,-3)),"jpg")==0 or strcmp(strtolower(substr($filename,-4)),"jpeg")==0){
								$source = imagecreatefromjpeg($filename_src);
								
								// Resize
								imagecopyresized($thumb, $source, 0, 0, 0, 0, $width_resize, $height_resize, $width, $height);

								// Output
								imagejpeg($thumb,$filename_dest);
							}
							else if(strcmp(strtolower(substr($filename,-3)),"png")==0){
								$source = imagecreatefrompng($filename_src);
								
								// Resize
								imagecopyresized($thumb, $source, 0, 0, 0, 0, $width_resize, $height_resize, $width, $height);

								// Output
								imagepng($thumb,$filename_dest);
							}
						}
					}
				}
			}
		}
	}
	