<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembayaran_hutang_cuti extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'cuti/';
		$this->folder_model = 'cuti/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';

		$this->akses = array();

		$this->load->helper("karyawan_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("cutoff_helper");
		$this->load->helper("string");

		$this->load->model($this->folder_model . "/M_pembayaran_hutang_cuti_new");
		$this->load->model("cuti/M_permohonan_cuti", 'm_permohonan_cuti');

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

    public function index() {
		$this->data['judul'] = "Pembayaran Hutang Cuti";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "pembayaran_hutang_cuti/index"; 

        // $sto = $this->db->where('object_type', 'O')->get('ess_sto')->result();
		// $this->data['sto'] = $sto;
        $sto = $this->db->select('kode_unit, nama_unit')->group_by('kode_unit, nama_unit')->get('mst_karyawan')->result();
		$this->data['sto'] = $sto;
        
        $this->load->view('template', $this->data);
	}

    public function get_data() {
		$list 	= $this->M_pembayaran_hutang_cuti_new->get_datatables();
		$no = $_POST['start'];

        $array_of_np = array_map(function($person) {
            return $person->no_pokok;
        }, $list);
        $absence_quota = $this->m_permohonan_cuti->select_absence_quota_by_array_of_np($array_of_np);

		$i = 0;
		foreach ($list as $key=>$val) {
			$no++;
            $no_pokok = $val->no_pokok;
            $list[$key]->no = $no;
            $list[$key]->absence_quota = array_values(array_filter($absence_quota, function($e) use ($no_pokok){
                return $e->np_karyawan == $no_pokok;
            }));
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_pembayaran_hutang_cuti_new->count_all(),
			"recordsFiltered" => $this->M_pembayaran_hutang_cuti_new->count_filtered(),
			"data" => $list,
		);
		echo json_encode($output);
	}

    function histori(){
        $np = $this->input->post('np', true);
        $data = $this->M_pembayaran_hutang_cuti_new->get_histori($np);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => 'OK',
                'data' => $data
            ]));
    }

    function form_bayar(){
        $np = $this->input->post('np', true);
        $data = $this->M_pembayaran_hutang_cuti_new->get_by_np($np);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => true,
                'message' => 'OK',
                'data' => $data
            ]));
    }

    function simpan_bayar(){
        $status = false;
        $message = 'Pembayaran melebihi hutang';
        $np = $this->input->post('no_pokok', true);
        $bayar_dari_mst_cuti_id = $this->input->post('bayar_dari_mst_cuti_id', true);
        $data = $this->M_pembayaran_hutang_cuti_new->get_by_np($np);
        if($data){
            $hutang = $data->hutang;
            $pembayaran = $this->input->post('pembayaran', true);
            if($pembayaran > $hutang){
                $status = false;
                $message = 'Pembayaran melebihi hutang';
            } else{
                // validasi sisa kuota cuti
                $sisa_kuota = 0;
                if($bayar_dari_mst_cuti_id==1){
                    $get = $this->db->select('SUM(kuota.number) AS total_number, SUM(kuota.deduction) AS total_deduction')
                        ->where('kuota.np_karyawan', $np)
                        ->where("kuota.deduction_to >= DATE(NOW())", null,false)
                        ->where("kuota.deduction < kuota.number", null,false)
                        ->get('erp_absence_quota kuota')->row();
                    if($get){
                        $sisa_kuota += ($get->total_number - $get->total_deduction);
                        if($sisa_kuota >= $pembayaran){
                            // lanjut
                        } else{
                            return $this->output
                                ->set_content_type('application/json')
                                ->set_status_header(200)
                                ->set_output(json_encode([
                                    'status' => false,
                                    'message' => "Sisa kuota Cuti Tahunan tidak mencukupi"
                                ]));
                            exit;
                        }
                    } else{
                        return $this->output
                            ->set_content_type('application/json')
                            ->set_status_header(200)
                            ->set_output(json_encode([
                                'status' => false,
                                'message' => 'Tidak ada sisa kuota Cuti Tahunan'
                            ]));
                        exit;
                    }
                } else if($bayar_dari_mst_cuti_id==2){
                    $get = $this->db->select('cubes.sisa_bulan, cubes.sisa_hari')
                        ->where('cubes.np_karyawan', $np)
                        ->where("cubes.tanggal_kadaluarsa >= DATE(NOW())", null,false)
                        ->order_by('cubes.tahun','ASC')
                        ->order_by('cubes.tanggal_kadaluarsa','ASC')
                        ->get('cuti_cubes_jatah cubes')->row();
                    if($get){
                        $sisa_bulan = $get->sisa_bulan;
                        $sisa_hari = $get->sisa_hari;
                        // $sisa_kuota += ($sisa_bulan * 22 + $sisa_hari);
                        if($sisa_hari >= $pembayaran){
                            // lanjut
                        } else{
                            if(@$this->input->post('is_convert_bulan_to_hari')=='1' && $sisa_bulan>0){
                                $sisa_kuota += (1 * 22 + $sisa_hari);
                                $sisa_bulan = $get->sisa_bulan - 1;
                                $sisa_hari = $sisa_kuota;
                                if($sisa_hari >= $pembayaran){
                                    // lanjut
                                } else{
                                    return $this->output
                                        ->set_content_type('application/json')
                                        ->set_status_header(200)
                                        ->set_output(json_encode([
                                            'status' => false,
                                            'message' => "Sisa kuota Cuti Besar tidak mencukupi"
                                        ]));
                                    exit;
                                }
                            } else{
                                return $this->output
                                    ->set_content_type('application/json')
                                    ->set_status_header(200)
                                    ->set_output(json_encode([
                                        'status' => false,
                                        'message' => "Sisa kuota Cuti Besar tidak mencukupi"
                                    ]));
                                exit;
                            }
                        }
                    } else{
                        return $this->output
                            ->set_content_type('application/json')
                            ->set_status_header(200)
                            ->set_output(json_encode([
                                'status' => false,
                                'message' => 'Tidak ada sisa kuota Cuti Besar'
                            ]));
                        exit;
                    }
                } else{
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(200)
                        ->set_output(json_encode([
                            'status' => false,
                            'message' => 'Bayar dari Master Cuti belum dipilih'
                        ]));
                    exit;
                }
                // END validasi sisa kuota cuti

                $this->db->trans_start();
                $this->M_pembayaran_hutang_cuti_new->insert_bayar([
                    'id'=>$this->uuid->v4(),
                    'no_pokok'=>$np,
                    'hutang_awal'=>$data->hutang,
                    'pembayaran'=>$pembayaran,
                    'sisa_hutang'=>$data->hutang - $pembayaran,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'created_by'=>$this->session->userdata('no_pokok') ?: null,
                    'bayar_dari_mst_cuti_id'=>$bayar_dari_mst_cuti_id
                ]);

                if($this->db->trans_status()==true){
                    // update sisa hutang
                    $this->M_pembayaran_hutang_cuti_new->update_hutang([
                        'hutang'=>$data->hutang - $pembayaran,
                        'updated_at'=>date('Y-m-d H:i:s')
                    ], ['no_pokok'=>$np]);

                    // update ke tabel cubes/tahunan
                    if($bayar_dari_mst_cuti_id==1){
                        $all_row_tahunan = $this->db->select('kuota.id, kuota.number, kuota.deduction')
                            ->where('kuota.np_karyawan', $np)
                            ->where("kuota.deduction_to >= DATE(NOW())", null,false)
                            ->where("kuota.deduction < kuota.number", null,false)
                            ->get('erp_absence_quota kuota')->result();
                        foreach ($all_row_tahunan as $key => $value) {
                            $sisa_awal = (int)($value->number - $value->deduction);
                            $fix_bayar = min([$sisa_awal, (int)$pembayaran]);
                            $sisa_akhir = $sisa_awal - (int)$fix_bayar;
                            $this->db->where('id',$value->id)->update('erp_absence_quota', ['deduction'=>((int)$value->number - (int)$sisa_akhir)]);
                            $pembayaran -= $fix_bayar;
                        }
                    } else if($bayar_dari_mst_cuti_id==2){
                        $get = $this->db->select('cubes.id, cubes.pakai_hari, cubes.sisa_bulan, cubes.sisa_hari')
                            ->where('cubes.np_karyawan', $np)
                            ->where("cubes.tanggal_kadaluarsa >= DATE(NOW())", null,false)
                            ->order_by('cubes.tahun','ASC')
                            ->order_by('cubes.tanggal_kadaluarsa','ASC')
                            ->get('cuti_cubes_jatah cubes')->row();
                        $sisa_bulan = $get->sisa_bulan;
                        $pakai_hari = (int)$get->pakai_hari + (int)$pembayaran;
                        $sisa_hari = (int)$get->sisa_hari - (int)$pembayaran;
                        if(@$this->input->post('is_convert_bulan_to_hari')=='1' && $sisa_bulan>0){
                            $sisa_bulan = $get->sisa_bulan - 1;
                            $sisa_hari = (1 * 22 + (int)$get->sisa_hari) - (int)$pembayaran;
                        }
                        $this->db->where('id',$get->id)->update('cuti_cubes_jatah', ['pakai_hari'=>$pakai_hari, 'sisa_hari'=>$sisa_hari, 'sisa_bulan'=>$sisa_bulan]);
                    }
                    
                    $this->db->trans_complete();
                    $status = true;
                    $message = 'Pembayaran telah disimpan';
                } else{
                    $this->db->trans_rollback();
                    $status = false;
                    $message = 'Pembayaran gagal disimpan';
                }
            }
        } else{
            $status = false;
            $message = 'Data tidak ditemukan';
        }
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => $status,
                'message' => $message
            ]));
    }

    function cek_sisa_kuota(){
        $status = false;
        $data = [];
        $content = '';
        $message = '';
        $bayar_dari_mst_cuti_id = $this->input->post('bayar_dari_mst_cuti_id', true);
        $np = $this->input->post('no_pokok', true);
        $pembayaran = $this->input->post('pembayaran', true)!='' ? $this->input->post('pembayaran', true) : 0;
        if($bayar_dari_mst_cuti_id=='1'){
            $status = true;
            $data = $this->input->post();
            $sisa_kuota = 0;
            $get = $this->db->select('SUM(kuota.number) AS total_number, SUM(kuota.deduction) AS total_deduction')
                ->where('kuota.np_karyawan', $np)
                ->where("kuota.deduction_to >= DATE(NOW())", null,false)
                ->where("kuota.deduction < kuota.number", null,false)
                ->get('erp_absence_quota kuota')->row();
            if($get){
                $sisa_kuota += ($get->total_number - $get->total_deduction);
                if($sisa_kuota >= $pembayaran){
                    $content = "Sisa kuota Cuti Tahunan: {$sisa_kuota}";
                    $data = $get;
                } else{
                    $content = "Sisa kuota Cuti Tahunan: {$sisa_kuota}
                    <br>
                    Sisa kuota Cuti tahunan tidak mencukupi
                    ";
                    $data = $get;
                }
            } else{
                $content = "Tidak ada sisa kuota Cuti Tahunan";
                $data = $get;
            }
        } else if($bayar_dari_mst_cuti_id=='2'){
            $status = true;
            $data = $this->input->post();
            $get = $this->db->select('cubes.sisa_bulan, cubes.sisa_hari')
                ->where('cubes.np_karyawan', $np)
                ->where("cubes.tanggal_kadaluarsa >= DATE(NOW())", null,false)
                ->order_by('cubes.tahun','ASC')
                ->order_by('cubes.tanggal_kadaluarsa','ASC')
                ->get('cuti_cubes_jatah cubes')->row();
            if($get){
                $sisa_bulan = $get->sisa_bulan;
                $sisa_hari = $get->sisa_hari;
                $sisa_kuota += ($sisa_bulan * 22 + $sisa_hari);
                $text_sisa_hari = '';
                if($sisa_hari < $pembayaran){
                    $text_sisa_hari = 'Sisa Kuota Hari tidak mencukupi';
                    if($sisa_bulan>0) $text_sisa_hari .= ', apakah akan konversi sisa Kuota Bulan ke Hari?';
                }
                $content = "<p>
                                    Rencana bayar: {$pembayaran}
                                    <br>
                                    Sisa kuota bulan: {$sisa_bulan}
                                    <br>
                                    Sisa kuota hari: {$sisa_hari}
                                    <br>
                                    {$text_sisa_hari}
                                </p>";
                if($text_sisa_hari!='' && $sisa_bulan>0){
                    $content .= "<input type=\"checkbox\" class=\"\" id=\"is_convert_bulan_to_hari\" name=\"is_convert_bulan_to_hari\" value=\"1\">
                                <label for=\"is_convert_bulan_to_hari\">Konversi Sisa Kuota Bulan ke Hari</label>";
                }
                $data = $get;
            } else{
                $content = "Tidak ada sisa kuota Cuti Besar";
                $data = $get;
            }
        }
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'content' => $content
            ]));
    }
}