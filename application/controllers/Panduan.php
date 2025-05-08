<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Panduan extends CI_Controller {
		
		public function __construct(){
			parent::__construct();
		}

		public function index(){
		
			$this->load->helper('download');
			
			
			if($_SESSION["grup"]==1) //jika superadmin
			{
				force_download('./asset/manual_book/MANUAL PERURI ALL ROLE.pdf', NULL);
			}else
			if($_SESSION["grup"]==2) //jika admin ti
			{
				force_download('./asset/manual_book/MANUAL PERURI ROLE ADMINISTRATOR IT.pdf', NULL);
			}else
			if($_SESSION["grup"]==3) //jika administrator sdm
			{
				force_download('./asset/manual_book/MANUAL PERURI ROLE ADMINISTRATOR SDM.pdf', NULL);
			}else			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				force_download('./asset/manual_book/MANUAL PERURI ROLE PENGADMINISTRASI UNIT KERJA.pdf', NULL);
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				force_download('./asset/manual_book/MANUAL PERURI ROLE PENGGUNA.pdf', NULL);
			}	
			if($_SESSION["grup"]==6) //jika pamlek
			{
				force_download('./asset/manual_book/MANUAL PERURI ROLE ADMINISTRATOR PAMLEK.pdf', NULL);
			}	
		}
	}
	