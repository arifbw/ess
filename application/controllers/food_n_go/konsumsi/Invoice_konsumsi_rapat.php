<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_konsumsi_rapat extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }
    
    function get_request(){
        $response=[];
        $nomor = str_replace('/','-',$this->input->post('detail_no_pemesanan'));
        $filename = "invoice-$nomor.pdf";
        $this->create_pdf($this->input->post());
        if( is_file(APPPATH.'../uploads/rekap/konsumsi/invoice/'.$filename) ){
            $link = base_url('uploads/rekap/konsumsi/invoice/'.$filename);
            $status = true;
            $data = $link;
        } else{
            $status = false;
            $data = '#';
        }
        $response['status']=$status;
        $response['data']=$data;
        
        echo json_encode($response);
    }
    
    function create_pdf($data){
        $this->load->helper("tanggal_helper");
        require_once('./asset/html2pdf/html2pdf.class.php');
        ob_start();
        $nomor = str_replace('/','-',$data['detail_no_pemesanan']);
        $this->load->view('food_n_go/konsumsi/invoice_konsumsi_rapat', $data);
        $html = ob_get_contents();
        
        $pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8', array(5, 5, 5, 5));
        $pdf->pdf->SetDisplayMode('fullpage');
        ob_end_clean();
        $pdf->writeHTML($html);
        $pdf->Output(APPPATH."../uploads/rekap/konsumsi/invoice/invoice-$nomor.pdf", 'F');
    }
}