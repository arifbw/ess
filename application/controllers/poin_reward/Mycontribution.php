<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MyContribution extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $meta = meta_data();
        foreach ($meta as $key => $value) {
            $this->data[$key] = $value;
        }

        $this->folder_view = 'poin_reward/';
        $this->folder_model = 'poin_reward/';
        $this->folder_controller = 'poin_reward/';

        $this->load->helper("cutoff_helper");
        $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
        $this->load->helper("reference_helper");

        $this->data["is_with_sidebar"] = true;

        $this->data['judul'] = "My Contribution";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);
        // $this->akses = array('persetujuan' => true, 'lihat' => true, 'hapus' => true, 'tambah' => true);
        $this->nama_db = $this->db->database;
        izin($this->akses["akses"]);
    }

    public function index()
    {
        $this->load->model($this->folder_model . "M_mycontribution");

        $array_daftar_karyawan    = $this->M_mycontribution->select_daftar_karyawan();

        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view . "mycontribution";
        $this->data['array_daftar_karyawan'] = $array_daftar_karyawan;
        $this->data['provinsi'] = $this->db->get('provinsi')->result();
        $this->data['ref_jenis_dokumen'] = $this->db->get('ref_jenis_dokumen_contribution')->result_array();
        $this->data['ref_satuan_kerja'] = $this->db->distinct()->select('kode_unit,nama_unit')->where('kode_unit!=', null)->where('kode_unit!=', '')->get('my_contribution')->result_array();
        $this->data['ref_karyawan'] = $this->db->select('nama_karyawan as nama,np_karyawan as no_pokok')->distinct()->get('my_contribution')->result_array();
        // print_r($this->db->get('ref_jenis_dokumen_contribution')->result());
        // die;
        $this->load->view('template', $this->data);
    }

    public function get_karyawan_by_satuan_kerja($kode_unit = 'all')
    {
        if ($kode_unit !== 'all') {
            $this->db->where('kode_unit', $kode_unit);
        }
        $res = $this->db->select('nama_karyawan as nama,np_karyawan as no_pokok')->distinct()->get('my_contribution')->result_array();

        echo json_encode($res);
    }

    public function tabel_mycontribution()
    {
        $this->load->model($this->folder_model . "M_tabel_mycontribution");
        if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
            $var = array();
            $list_pengadministrasi = $_SESSION["list_pengadministrasi"];
            foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
                array_push($var, $data['kode_unit']);
            }
        } else if ($_SESSION["grup"] == 5) { //jika Pengguna
            $var     = $_SESSION["no_pokok"];
        } else {
            $var = 1;
        }

        $list     = $this->M_tabel_mycontribution->get_datatables($var);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $tampil) {
            $no++;
            $get_status = trim($tampil->status_verifikasi);

            $row = array();
            $row[] = $no;
            $row[] = $tampil->np_karyawan . ' - ' . $tampil->nama_karyawan;
            $row[] = $tampil->perihal;
            $row[] = $tampil->tanggal_dokumen;
            $row[] = $tampil->jenis_dokumen;

            //DETAIL
            if ($get_status == '1') {
                $btn_warna        = 'btn-success';
                $btn_text        = 'Disetujui';
            } else if ($get_status == '2') {
                $btn_warna        = 'btn-danger';
                $btn_text        = 'Ditolak';
            } else if ($get_status == '0' || $get_status == null) {
                $btn_warna        = 'btn-default';
                $btn_text        = 'Proses';
            }

            $row[] = "<span class='btn btn-xs $btn_warna'" . ">$btn_text</span>";

            if ($tampil->status_verifikasi == null || $tampil->status_verifikasi == '0') {
                $np_hapus = $tampil->np_karyawan;

                if ($this->akses) {
                    $edit['id']    = trim($tampil->id);
                    $edit['np_karyawan'] = trim($tampil->np_karyawan);
                    $edit['perihal'] = trim($tampil->perihal);
                    $edit['tanggal_dokumen'] = trim($tampil->tanggal_dokumen);
                    $edit['jenis_dokumen_id'] = trim($tampil->jenis_dokumen_id);
                    $edit['dokumen'] = trim($tampil->dokumen);
                    `<div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-secondary">Left</button>
                    <button type="button" class="btn btn-secondary">Middle</button>
                    <button type="button" class="btn btn-secondary">Right</button>
                  </div>`;

                    $aksi = "<div class='btn-group' role='group'><button data-toggle='tooltip' data-placement='top' title='Detail' class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . "><i class='fa fa-eye' aria-hidden='true'></i></button>";

                    if (@$this->akses['tambah']) {
                        $aksi .= "<button data-toggle='tooltip' data-placement='top' title='Edit' class='btn btn-warning btn-xs'";
                        foreach ($edit as $key => $value) {
                            $aksi .= 'data-' . $key . '="' . $value . '"';
                        }
                        $aksi .= "onclick='edit(this)''><i class='fa fa-pencil' aria-hidden='true'></i></button>";
                    }
                    if (@$this->akses['hapus']) {
                        $aksi .= ` <button data-toggle='tooltip' data-placement='top' title='Hapus' data-toggle="tooltip" data-placement="top" title="Tooltip on top" class="btn btn-danger btn-xs hapus" data-id="' . $tampil->id . '" data-np="' . $np_hapus . '"><i class='fa fa-trash-o' aria-hidden='true'></i></button>`;
                    }
                    $aksi .= '</div>';
                } else
                    $aksi = '';
            } else {
                $aksi = "<div class='btn-group' role='group'><button data-toggle='tooltip' data-placement='top' title='Detail' class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . "><i class='fa fa-eye' aria-hidden='true'></i></button>";
            }

            $row[] = $aksi;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_tabel_mycontribution->count_all($var),
            "recordsFiltered" => $this->M_tabel_mycontribution->count_filtered($var),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    function action_insert_mycontribution()
    {
        $this->load->model($this->folder_model . "M_mycontribution");
        $this->load->helper('form');
        $this->load->library('form_validation');

        if ($this->akses["tambah"]) {
            $fail = array();
            $error = "";

            $this->form_validation->set_rules('np_karyawan', 'NP karyawan', 'required');
            $this->form_validation->set_rules('jenis_dokumen_id', 'Jenis dokumen', 'required');
            $this->form_validation->set_rules('tanggal_dokumen', 'Tanggal dokumen', 'required');
            // $this->form_validation->set_rules('dokumen', 'Dokumen', 'required');
            $this->form_validation->set_rules('perihal', 'Perihal', 'required');

            $start_date            = date('Y-m-d');
            $end_date            = date('Y-m-d');
            $tahun_bulan         = $start_date != null ? str_replace('-', '_', substr("$start_date", 0, 7)) : str_replace('-', '_', substr("$end_date", 0, 7));

            if (($this->input->post('edit_id', true) == '' && $_FILES['dokumen']['tmp_name'] == '') || $this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('warning', 'Data Belum Lengkap');
                $this->index();
                // print_r($this->form_validation->error_array());
            } else {
                $submit = $this->input->post('submit');
                if ($submit) {
                    $this->load->library('upload');

                    if ($_FILES['dokumen']['tmp_name'] != '') {
                        //Surat Keterangan
                        $config['upload_path'] = './uploads/mycontribution/dokumen';
                        $config['allowed_types'] = 'pdf|jpg|png|jpeg';
                        $config['max_size']    = '2148';
                        $files = $_FILES;

                        $this->upload->initialize($config);

                        if ($files['dokumen']['name']) {
                            $this->load->helper("file");
                            if ($this->upload->do_upload('dokumen')) {
                                $up = $this->upload->data();
                                $dokumen = $up['file_name'];
                            } else {
                                $error = $this->upload->display_errors();
                            }
                        } else {
                            $error = "File Dokumen Tidak Ditemukan";
                        }
                    }

                    if ($error == "") {
                        $np_karyawan = $this->input->post('np_karyawan', true);
                        $ref_dokumen = $this->db->where('id', $this->input->post('jenis_dokumen_id'))->get('ref_jenis_dokumen_contribution')->row_array();
                        $ref_karyawan = $this->db->where('no_pokok', $np_karyawan)->get('mst_karyawan')->row_array();
                        $data_insert = [
                            'np_karyawan' => $np_karyawan,
                            'nama_karyawan' => erp_master_data_by_np($np_karyawan, $start_date)['nama'],
                            'perihal' => $this->input->post('perihal'),
                            'jenis_dokumen' => $ref_dokumen['nama'],
                            'jenis_dokumen_id' => $this->input->post('jenis_dokumen_id'),
                            'tanggal_dokumen' => $this->input->post('tanggal_dokumen'),
                            'tanggal_submit' => date('Y-m-d H:i:s'),
                            'kode_unit' => $ref_karyawan['kode_unit'],
                            'nama_unit' => $ref_karyawan['nama_unit'],
                        ];

                        if (@$dokumen)
                            $data_insert['dokumen'] = $dokumen;

                        if ($this->input->post('edit_id', true) != '') {
                            $data_lama = $this->db->where('id', $this->input->post('edit_id', true))->get('my_contribution')->row();

                            $data_insert['updated_at'] = date('Y-m-d H:i:s');
                            $data_insert['updated_by_np'] = $_SESSION['no_pokok'];
                            $data_insert['updated_by_nama'] = $_SESSION['nama'];
                            $this->db->set($data_insert)->where('id', $this->input->post('edit_id', true))->update('my_contribution');
                        } else {
                            $data_insert['created_at'] = date('Y-m-d H:i:s');
                            $data_insert['created_by_np'] = $_SESSION['no_pokok'];
                            $data_insert['created_by_nama'] = $_SESSION['nama'];
                            $this->db->set($data_insert)->insert('my_contribution');
                        }

                        if ($this->db->affected_rows() > 0) {
                            if ($this->input->post('edit_id', true) != '') {
                                if (@$dokumen)
                                    unlink('./uploads/mycontribution/dokumen/' . $data_lama->dokumen);

                                $this->session->set_flashdata('success', "Berhasil Update My Contribution");
                            } else {
                                $this->session->set_flashdata('success', "Berhasil Tambah My Contribution");
                            }
                        } else {
                            $this->session->set_flashdata('warning', "Gagal Update My Contribution");
                        }
                        redirect(base_url($this->folder_controller . 'mycontribution'));
                    } else {
                        $this->session->set_flashdata('warning', "Terjadi Kesalahan Upload , $error");
                        redirect(base_url($this->folder_controller . 'mycontribution'));
                    }
                } else {
                    $this->session->set_flashdata('warning', "Terjadi Kesalahan Input Data");
                    redirect(base_url($this->folder_controller . 'mycontribution'));
                }
            }
        } else {
            $this->session->set_flashdata('warning', "Anda Tidak Memiliki Hak Akses");
            redirect(base_url($this->folder_controller . 'mycontribution'));
        }
    }

    public function hapus($id = null, $np = null)
    {
        $this->load->model($this->folder_model . "M_mycontribution");
        if (@$id != null && @$np != null) {
            $get = $this->M_mycontribution->ambil_by_id($id, 'my_contribution');
            $this->db->where('id', $id)->set(array('deleted_at' => date('Y-m-d H:i:s'), 'deleted_by_np' => $_SESSION['no_pokok'], 'deleted_by_nama' => $_SESSION['nama']))->update("my_contribution");

            if ($this->db->affected_rows() > 0) {
                $return["status"] = true;
                if (@$get['dokumen'])
                    unlink('./uploads/mycontribution/dokumen/' . $get['dokumen']);
                $log_data_lama = "";
                foreach ($get as $key => $value) {
                    if (strcmp($key, "id") != 0) {
                        if (!empty($log_data_lama)) {
                            $log_data_lama .= "<br>";
                        }
                        $log_data_lama .= "$key = $value";
                    }
                }

                $log = array(
                    "id_pengguna" => $this->session->userdata("id_pengguna"),
                    "id_modul" => $this->data['id_modul'],
                    "id_target" => $get["id"],
                    "deskripsi" => "hapus " . strtolower(preg_replace("/_/", " ", __CLASS__)),
                    "kondisi_lama" => $log_data_lama,
                    "kondisi_baru" => '',
                    "alamat_ip" => $this->data["ip_address"],
                    "waktu" => date("Y-m-d H:i:s")
                );
                $this->m_log->tambah($log);

                $return['status'] = true;
                $return['msg'] = 'My Contribution Berhasil Dihapus';
            } else {
                $return['status'] = false;
                $return['msg'] = 'My Contribution Gagal Dihapus';
            }
        } else {
            $return['status'] = false;
            $return['msg'] = 'My Contribution Gagal Dihapus';
        }

        echo json_encode($return);
    }

    public function view_detail()
    {
        $id = $this->input->post('id_');
        $tabel = 'my_contribution';

        $lap = $this->db->select("*")->where('id', $id)->get($tabel . ' a')->row_array();
        $data['detail'] = $lap;

        //DETAIL
        if ($lap['status_verifikasi'] == '1') {
            $data['status_verifikasi'] = "My Contrbution <b>TELAH DISETUJUI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
            $data['approval_warna'] = 'success';
        } else if ($lap['status_verifikasi'] == '2') {
            $data['status_verifikasi'] = "My Contrbution <b>TIDAK DISETUJI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
            $data['approval_warna'] = 'danger';
        } else if ($lap['status_verifikasi'] == '0' || $lap['status_verifikasi'] == null) {
            $data['status_verifikasi'] = "Proses";
            $data['approval_warna'] = 'info';
        }
        $data["judul"] = ucwords(preg_replace("/_/", " ", __CLASS__));

        $this->load->view($this->folder_view . "detail_mycontribution", $data);
    }

    public function import_excel()
    {
        $this->load->model($this->folder_model . "M_mycontribution");;
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('upload');

        $this->form_validation->set_rules('dokumen', 'Dokumen', 'required');

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048; // 2MB

        $this->upload->initialize($config);

        $data_karyawan = [
            'no_pokok' => $_SESSION['no_pokok'],
            'nama' => $_SESSION['nama']
        ];

        $file_ext = pathinfo($_FILES['dokumen']['name'], PATHINFO_EXTENSION);
        if (isset($_FILES['dokumen']['name']) && $_FILES['dokumen']['name'] != '' && ($file_ext == 'xls'
            || $file_ext == 'xlsx')) {
            $file = $_FILES['dokumen']['tmp_name'];

            $data = $this->M_mycontribution->import($file);
            $res = $this->M_mycontribution->insert_batch($data, $data_karyawan);

            $this->session->set_flashdata('success', "Data berhasil diimport ke database " . $res['inserted_count'] . " data gagal " . $res['invalid_count'] . ".");
        } else {
            $this->session->set_flashdata('warning', "File harus berupa excel (.xls atau .xlsx)");
        }
        redirect(base_url($this->folder_controller . 'mycontribution'));
    }

    public function create_template()
    {
        $ref_jenis_dokumen = $this->db->select('nama')->get('ref_jenis_dokumen_contribution')->result_array();
        foreach ($ref_jenis_dokumen as $key => $dokumen) {
            $ref_jenis_dokumen[$key] = array_merge(['no' => $key + 1], $dokumen);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $newSheet = $spreadsheet->createSheet();
        $sheet->setTitle('Template import contribution');
        $newSheet->setTitle('Referensi Jenis Dokumen');

        $headers = [
            ["NP Karyawan", ""],
            ["Perihal", ""],
            ["Nama Jenis Dokumen", "(* Ambil dari sheet referensi jenis dokumen"],
            ["Tanggal Dokumen", "(* Gunakan format YYYY-MM-DD"],
            ["Url Dokumen", ""]
        ];

        $sheet->getStyle('D:D')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

        $headers_new_sheet = ['No', 'Nama Jenis Dokumen'];

        for ($i = 1; $i <= count($headers); $i++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($columnLetter)->setWidth(40);

            $sheet->getStyle($columnLetter . '1')->getFont()->setBold(true);
            $sheet->getStyle($columnLetter . '1')->getAlignment()->setWrapText(true);


            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

            $headerText = $richText->createTextRun($headers[$i - 1][0]);
            $headerText->getFont()->setBold(true);

            if (!empty($headers[$i - 1][1])) {
                $richText->createText("\n");
                $noteText = $richText->createTextRun($headers[$i - 1][1]);
                $noteText->getFont()->setBold(false);
            }

            $sheet->getStyle($columnLetter . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnLetter . '1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getCell($columnLetter . '1')->setValue($richText);
        }

        $newSheet->fromArray($headers_new_sheet, null, 'A1');
        $newSheet->getColumnDimension('A')->setAutoSize(true);
        $newSheet->getColumnDimension('B')->setAutoSize(true);
        $newSheet->fromArray($ref_jenis_dokumen, NULL, 'A2');
        $newSheet->getStyle('A1')->getFont()->setBold(true);
        $newSheet->getStyle('B1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
