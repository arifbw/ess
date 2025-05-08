<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Umum extends CI_Controller {
		public function __construct(){
			parent::__construct();
            $this->folder_view = 'food_n_go/kendaraan/';
            $this->load->helper('kendaraan');
		}
        
        function show_detail($id){
            //echo $id;
            $get_pesan = $this->db->where('id',$id)->get('ess_pemesanan_kendaraan')->row();
            $data=[
                'row'=>$get_pesan
            ];
            
            # update read status
            $this->db->where('id',$id)->update('ess_pemesanan_kendaraan',['is_read'=>1, 'last_read'=>date('Y-m-d H:i:s')]);
            $this->load->view($this->folder_view.'detail_pemesanan', $data);
        }
        
        function selesaikan_pesanan(){
            $response = [];
            $kode = $this->input->post('kode',true);
            
            $this->db->where('kode',$kode)->update('ess_pemesanan_kendaraan',['pesanan_selesai'=>1]);
            if($this->db->affected_rows()>0){
                $response['status'] = true;
                $response['message'] = 'Pesanan telah diselesaikan';
            } else{
                $response['status'] = false;
                $response['message'] = 'Gagal saat memproses';
            }
            
            echo json_encode($response);
        }
        
        function batalkan_pesanan(){
            $response = [];
            $kode = $this->input->post('kode',true);
            
            $this->db->where('kode',$kode)->update('ess_pemesanan_kendaraan',['is_canceled_by_admin'=>1, 'date_canceled_by_admin'=>date('Y-m-d H:i:s')]);
            if($this->db->affected_rows()>0){
                $response['status'] = true;
                $response['message'] = 'Pesanan telah dibatalkan';
            } else{
                $response['status'] = false;
                $response['message'] = 'Gagal saat memproses';
            }
            
            echo json_encode($response);
        }
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */