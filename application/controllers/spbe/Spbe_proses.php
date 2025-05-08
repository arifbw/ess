<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spbe_proses extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}
	
	public function detail_spbe($id=null){
        $data = [];
        if($id) $data = $this->db->where('id',$id)->get('ess_permohonan_spbe')->row_array();
        $lampiran = $this->db->where('ess_permohonan_spbe_id',$id)->where('deleted_at IS NULL',null,false)->where('jenis_file','lampiran')->get('ess_permohonan_spbe_file')->result_array();
        foreach ($lampiran as $key => $value) {
            if(!is_file($value['path_file'])) $lampiran[$key]['path_file'] = null;
        }
        $data['lampiran'] = $lampiran;
        // $data['akses_lampiran'] = $this->db->where('id',$id)
        // ->group_start()
        // ->where('approval_kasek_np', $this->session->userdata('no_pokok'))
        // ->or_where('approval_atasan_np', $this->session->userdata('no_pokok'))
        // ->or_where('created_by', $this->session->userdata('no_pokok'))
        // ->group_end()
        // ->get('ess_permohonan_spbe')->row();

        $np_session = @$this->session->userdata('no_pokok') ?: null;
        $user_grup_session = @$this->session->userdata('grup') ?: null;
        if($np_session!=null && in_array($np_session, [$data['created_by'], $data['approval_kasek_np'], $data['approval_atasan_np']]) && $user_grup_session=='5') $data['akses_lampiran'] = true;
        else $data['akses_lampiran'] = false;
       
        if( $this->input->is_ajax_request() ){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else{
            return $data;
        }
	}

    public function approval(){
        $data = [];
        $id = $this->input->post('id');
        $data['akses_lampiran'] = $this->db->where('id',$id)
        ->group_start()
        ->where('approval_kasek_np', $this->session->userdata('no_pokok'))
        ->group_end()
        ->get('ess_permohonan_spbe')->row();
        
        if( $this->input->is_ajax_request() ){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else{
            return $data;
        }
    }

    public function all_karyawan(){
        $data = $this->db->select('no_pokok,nama,nama_jabatan')->get('mst_karyawan')->result_array();

        if( $this->input->is_ajax_request() ){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else{
            return $data;
        }
	}

    public function mst_pos(){
        $data = $this->db->where('status','1')->get('mst_pos')->result_array();

        if( $this->input->is_ajax_request() ){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else{
            return $data;
        }
	}

    public function get_data_barang(){
        $spbe_id = $this->input->get('spbe_id');
        $data = $this->db->where('ess_permohonan_spbe_id',$spbe_id)->where('deleted_at IS NULL',null,false)->get('ess_permohonan_spbe_barang')->result_array();

        if( $this->input->is_ajax_request() ){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else{
            return $data;
        }
	}

    public function export_pdf(){
        if( @$this->input->get('uuid') ){
            $uuid = $this->input->get('uuid');
            $data = $this->db->where('uuid',$uuid)->get('ess_permohonan_spbe')->row_array();

            if( $data['id']!=null ){
                require_once('./asset/html2pdf/html2pdf.class.php');
                $this->load->helper('tanggal');
                
                $pos_keluar = $this->db->where('deleted_at IS NULL',null,false)->where('posisi','keluar')->where('ess_permohonan_spbe_id',$data['id'])->order_by('tanggal','DESC')->order_by('jam','DESC')->order_by('updated_at','DESC')->order_by('created_at','DESC')->limit(1)->get('ess_permohonan_spbe_pos')->row_array();
                $data['last_keluar'] = $pos_keluar;

                $pos_masuk = $this->db->where('deleted_at IS NULL',null,false)->where('posisi','masuk')->where('ess_permohonan_spbe_id',$data['id'])->order_by('tanggal','DESC')->order_by('jam','DESC')->order_by('updated_at','DESC')->order_by('created_at','DESC')->limit(1)->get('ess_permohonan_spbe_pos')->row_array();
                $data['last_masuk'] = $pos_masuk;
                
                $data['lokasi_ttd'] = $this->input->get('lokasi') ?: 'Jakarta';

                $kondisi_barang = [];
                if( $pos_masuk['id']!=null ){
                    $kondisi_barang = $this->db->select('ess_permohonan_spbe_barang.nama_barang, ess_permohonan_spbe_barang.jumlah, ess_permohonan_spbe_kondisi_barang.kondisi, ess_permohonan_spbe_kondisi_barang.keterangan')->where('ess_permohonan_spbe_kondisi_barang.ess_permohonan_spbe_pos_id',$pos_masuk['id'])->from('ess_permohonan_spbe_kondisi_barang')->join('ess_permohonan_spbe_barang','ess_permohonan_spbe_kondisi_barang.ess_permohonan_spbe_barang_id=ess_permohonan_spbe_barang.id')->get()->result_array();
                }
                $data['last_kondisi_barang'] = $kondisi_barang;
                
                ob_start();
                $this->load->view('spbe/export_pdf', $data);
                $html = ob_get_contents();
                
                $pdf = new HTML2PDF('P', 'F4', 'en', true, 'UTF-8', array(5, 5, 5, 5));
                $pdf->pdf->SetDisplayMode('fullpage');
                ob_end_clean();
                $pdf->writeHTML($html);
                $pdf->Output(str_replace('/','-',$data['nomor_surat']).".pdf", 'P');
            } else{
                echo 'Kode tidak valid';
            }
        } else{
            require_once('./asset/html2pdf/html2pdf.class.php');
            
            ob_start();
            $this->load->view('spbe/export_pdf_blank', []);
            $html = ob_get_contents();
            
            $pdf = new HTML2PDF('P', 'F4', 'en', true, 'UTF-8', array(5, 5, 5, 5));
            $pdf->pdf->SetDisplayMode('fullpage');
            ob_end_clean();
            $pdf->writeHTML($html);
            $pdf->Output("print-spbe-blank.pdf", 'P');
        }
	}

	function batalkan(){
        $id = $this->input->post('id');
		$data_insert = [
			'canceled_at'=>date('Y-m-d H:i:s'),
			'canceled_np'=>$this->session->userdata('no_pokok'),
			'canceled_nama'=>$this->session->userdata('nama'),
			'canceled_group'=>$this->session->userdata('grup') ?: null,
		];
		$this->db->where('id',$id)->update('ess_permohonan_spbe',$data_insert);
		if($this->db->affected_rows()>0){
			$status = true;
			$message = 'Pembatalan SPBE berhasil dilakukan';
		} else{
			$status = false;
			$message = 'Gagal Melakukan Pembatalan';
		}

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode([
			'status'=>$status,
			'message'=>$message
		]);
	}

}