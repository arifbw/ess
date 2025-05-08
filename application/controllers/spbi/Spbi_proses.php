<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spbi_proses extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}
	
	public function detail_spbi($id=null){
        $data = [];
        if($id) $data = $this->db->where('id',$id)->get('ess_permohonan_spbi')->row_array();

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
        $spbi_id = $this->input->get('spbi_id');
        $data = $this->db->where('ess_permohonan_spbi_id',$spbi_id)->where('deleted_at IS NULL',null,false)->get('ess_permohonan_spbi_barang')->result_array();

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
            $data = $this->db->where('uuid',$uuid)->get('ess_permohonan_spbi')->row_array();

            if( $data['id']!=null ){
                require_once('./asset/html2pdf/html2pdf.class.php');
                $this->load->helper('tanggal');
                // $this->load->library('ciqrcode');

                // if (!file_exists(FCPATH. 'generates/spbi/qrcode/' . $data['uuid'] . '.png')) {
                //     $params = [];
                //     $params['data'] = $data['uuid'];
                //     $params['level'] = 'L';
                //     $params['size'] = 10;
                //     $params['savename'] = FCPATH. 'generates/spbi/qrcode/' . $data['uuid'] . '.png';
                //     $this->ciqrcode->generate($params);
                // }
                
                $pos_keluar = $this->db->where('deleted_at IS NULL',null,false)->where('posisi','keluar')->where('ess_permohonan_spbi_id',$data['id'])->order_by('tanggal','DESC')->order_by('jam','DESC')->order_by('updated_at','DESC')->order_by('created_at','DESC')->limit(1)->get('ess_permohonan_spbi_pos')->row_array();
                $data['last_keluar'] = $pos_keluar;

                $pos_masuk = $this->db->where('deleted_at IS NULL',null,false)->where('posisi','masuk')->where('ess_permohonan_spbi_id',$data['id'])->order_by('tanggal','DESC')->order_by('jam','DESC')->order_by('updated_at','DESC')->order_by('created_at','DESC')->limit(1)->get('ess_permohonan_spbi_pos')->row_array();
                $data['last_masuk'] = $pos_masuk;
                
                $data['lokasi_ttd'] = $this->input->get('lokasi') ?: 'Jakarta';

                $kondisi_barang = [];
                if( $pos_masuk['id']!=null ){
                    $kondisi_barang = $this->db->select('ess_permohonan_spbi_barang.nama_barang, ess_permohonan_spbi_barang.jumlah, ess_permohonan_spbi_kondisi_barang.kondisi, ess_permohonan_spbi_kondisi_barang.keterangan')->where('ess_permohonan_spbi_kondisi_barang.ess_permohonan_spbi_pos_id',$pos_masuk['id'])->from('ess_permohonan_spbi_kondisi_barang')->join('ess_permohonan_spbi_barang','ess_permohonan_spbi_kondisi_barang.ess_permohonan_spbi_barang_id=ess_permohonan_spbi_barang.id')->get()->result_array();
                }
                $data['last_kondisi_barang'] = $kondisi_barang;
                
                ob_start();
                $this->load->view('spbi/export_pdf', $data);
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
            $this->load->view('spbi/export_pdf_blank', []);
            $html = ob_get_contents();
            
            $pdf = new HTML2PDF('P', 'F4', 'en', true, 'UTF-8', array(5, 5, 5, 5));
            $pdf->pdf->SetDisplayMode('fullpage');
            ob_end_clean();
            $pdf->writeHTML($html);
            $pdf->Output("print-spbi-blank.pdf", 'P');
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
		$this->db->where('id',$id)->update('ess_permohonan_spbi',$data_insert);
		if($this->db->affected_rows()>0){
			$status = true;
			$message = 'Pembatalan SPBI berhasil dilakukan';
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