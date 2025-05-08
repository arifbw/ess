<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url()?>asset/daterangepicker/daterangepicker.css" />

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $judul ?></h1>
            </div>
        </div>

        <?php if(!empty($this->session->flashdata('success'))): ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('success');?>
            </div>
        <?php endif; ?>
        <?php if(!empty($this->session->flashdata('warning'))): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('warning');?>
            </div>
        <?php endif; ?>
        <?php if(@$akses["tambah"]): ?>
            <div class="row">						
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Tambah <?= $judul ?></a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <form role="form" action="<?= base_url(); ?>pelaporan/pindah_alamat/action_insert_pindah_alamat" id="formulir_tambah" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <input type='hidden' id='edit_id' name='edit_id'>
                                    <div class="col-lg-2">
                                        <label>NP Karyawan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control select2" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
                                            <option></option>
                                            <?php foreach ($array_daftar_karyawan->result_array() as $value): ?>
                                                <option value='<?= $value['no_pokok'] ?>'><?= $value['no_pokok']." ".$value['nama'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <div class="col-lg-9">
                                        <label>ALAMAT LAMA</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Jenis Alamat</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="jenis_alamat_lama" id="jenis_alamat_lama" style="width: 100%;" required>
                                            <option></option>
                                            <option value="ktp">KTP</option>
                                            <option value="domisili">Domisili</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Alamat</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <textarea class="form-control" name="alamat_lama" id="alamat_lama" placeholder="Masukkan Alamat Lama" rows="3" required></textarea>
                                    </div>														
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Provinsi</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_provinsi_lama" id="kode_provinsi_lama" style="width: 100%;" required>
                                            <option></option>
                                            <?php foreach($provinsi as $row): ?>
                                                <option value="<?= $row->kode_wilayah ?>"><?= $row->nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kabupaten/Kota</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_kabupaten_lama" id="kode_kabupaten_lama" style="width: 100%;" required>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kecamatan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_kecamatan_lama" id="kode_kecamatan_lama" style="width: 100%;" required>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kelurahan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_kelurahan_lama" id="kode_kelurahan_lama" style="width: 100%;" required>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kode Pos</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="text" name="kode_pos_lama" id="kode_pos_lama" placeholder="Masukkan Kode Pos Lama" required>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <div class="col-lg-9">
                                        <label>ALAMAT BARU</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Jenis Alamat</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="jenis_alamat_baru" id="jenis_alamat_baru" style="width: 100%;" required>
                                            <option></option>
                                            <option value="ktp">KTP</option>
                                            <option value="domisili">Domisili</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Alamat</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <textarea class="form-control" name="alamat_baru" id="alamat_baru" placeholder="Masukkan Alamat Baru" rows="3" required></textarea>
                                    </div>														
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Provinsi</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_provinsi_baru" id="kode_provinsi_baru" style="width: 100%;" required>
                                            <option></option>
                                            <?php foreach($provinsi as $row): ?>
                                                <option value="<?= $row->kode_wilayah ?>"><?= $row->nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kabupaten/Kota</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_kabupaten_baru" id="kode_kabupaten_baru" style="width: 100%;" required>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kecamatan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_kecamatan_baru" id="kode_kecamatan_baru" style="width: 100%;" required>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kelurahan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="kode_kelurahan_baru" id="kode_kelurahan_baru" style="width: 100%;" required>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Kode Pos</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="text" name="kode_pos_baru" id="kode_pos_baru" placeholder="Masukkan Kode Pos Baru" required>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <div class="col-lg-9">
                                        <label>LAMPIRAN</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>KTP</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <input class="form-control" type="text" name="no_ktp" id="no_ktp" placeholder="Masukkan Nomor Dokumen" required>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control" type="file" name="ktp" id="ktp" accept="application/pdf,image/*" required><small class="form-text text-danger">Dokumen PDF/JPG Max 2MB</small><!-- <strong> (wajib diisi)</strong> -->
                                    </div>
                                    <div class="col-lg-3" id="edit_ktp">
									</div>
                                </div>

                                <hr>

                                <div id="atasan_1">
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>NP Atasan</label>								
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" name="approval_1_np" id="approval_1_np" value="" onchange="getNamaAtasan1()" min="4" placeholder="Masukkan Nomor Pokok Atasan" required><small class="form-text text-danger">Atasan Langsung Minimal Kepala Seksi</small><strong class="form-text text-danger"> (wajib diisi)</strong>
                                        </div>								
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>Nama Atasan</label>
                                        </div>
                                        <div class="col-lg-7">														
                                            <input class="form-control" type="text" name="approval_1_input" id="approval_1_input" value="" readonly required>
                                        </div>														
                                    </div>
                                    
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>Jabatan Atasan</label>
                                        </div>
                                        <div class="col-lg-7">														
                                            <input class="form-control" type="text" name="approval_1_input_jabatan" id="approval_1_input_jabatan" readonly required>	
                                        </div>														
                                    </div>
                                </div>
                                    
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Keterangan</label>
                                    </div>
                                    <div class="col-lg-7">														
                                        <input class="form-control" name="keterangan" id="keterangan" placeholder="Masukkan Keterangan" required>
                                    </div>														
                                </div>

                                <div class="row">
                                    <div class="col-lg-9 text-right">
                                        <input type="submit" name="submit" value="Submit Atasan" class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($this->akses["lihat"]): ?>
            <div class="form-group">	
                <div class="row table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="daftar_pindah_alamat">
                        <thead>
                            <tr>
                                <th class="text-center no-sort" style="max-width: 5%">No</th>
                                <th class="text-center">Pegawai</th>  <!-- style="max-width: 15%" -->
                                <th class="text-center no-sort">Alamat Lama</th>
                                <th class="text-center no-sort">Alamat Baru</th>
                                <th class="text-center no-sort" style="max-width: 15%">Keterangan</th>
                                <th class="text-center no-sort">Status</th>
                                <th class="text-center no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        </tbody>
                    </table>
                </div>						
            </div>

            <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="label_modal_batal">Status <?= @$judul ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="get-approve"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>


        <?php if(@$akses["hapus"]): ?>
            <div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title text-danger" id="title-inactive">
                                <b>Hapus <?= $judul ?></b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </h4>
                        </div>

                        <div class="modal-body">
                            <h4 id="message-inactive"></h4>
                        </div>
                        <div class="modal-footer">
                            <a href="" id="inactive-action" class="btn btn-danger">Ya, Hapus</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= base_url('asset/select2/select2.min.js')?>"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url()?>asset/daterangepicker/daterangepicker.min.js"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js') ?>"></script>
<script type="text/javascript">
    var all_atasan_1_np=[], all_atasan_1_jabatan=[], all_atasan_2_np=[], all_atasan_2_jabatan=[];
    $('#multi_select').select2({
        closeOnSelect: false
	});
    $(document).ready(function() {
        $('.datetimepicker5').datetimepicker({
            format: 'HH:mm'
        });
        $('.select2').select2();
        $('#np_karyawan').select2({
            placeholder: "Nomor Pokok Karyawan"
        });

        $('#kode_provinsi_lama').select2({
            placeholder: "Provinsi"
        });
        $('#kode_kabupaten_lama').select2({
            placeholder: "Kabupaten/Kota"
        });
        $('#kode_kecamatan_lama').select2({
            placeholder: "Kecamatan"
        });
        $('#kode_kelurahan_lama').select2({
            placeholder: "Kelurahan"
        });

        $('#kode_provinsi_baru').select2({
            placeholder: "Provinsi"
        });
        $('#kode_kabupaten_baru').select2({
            placeholder: "Kabupaten/Kota"
        });
        $('#kode_kecamatan_baru').select2({
            placeholder: "Kecamatan"
        });
        $('#kode_kelurahan_baru').select2({
            placeholder: "Kelurahan"
        });

        $('.tanggal').datetimepicker({
            format: 'Y-MM-D'
        });

        $('#daftar_pindah_alamat').DataTable().destroy();				
        table_serverside();
    });

    function refresh_table_serverside() {
        $('#daftar_pindah_alamat').DataTable().destroy();				
        table_serverside();
    }

    function table_serverside() {
        var table;

        table = $('#daftar_pindah_alamat').DataTable({ 
            
            "iDisplayLength": 10,
            "language": {
                "url": "<?= base_url('asset/datatables/Indonesian.json');?>",
                "sEmptyTable": "Tidak ada data di database",
                "emptyTable": "Tidak ada data di database"
            },
            
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?= site_url("pelaporan/pindah_alamat/tabel_pindah_alamat/")?>",
                "type": "POST",
            },

            //Set column definition initialisation properties.
            "columnDefs": [
                { 
                    "targets": 'no-sort', //first column / numbering column
                    "orderable": false, //set not orderable
                },
                {
                    "targets": [0],
                    "className": "text-center"
                }
            ],

        });

    };
</script>

<script>
    const edit = async (data) => {
        $('#btn-tambah').html('Edit data');
        if($("#collapseOne").is(":visible")){
            console.log('Already shown');
        } else{
            $('#btn-tambah').trigger('click');
        }

        console.log($(data).data('kode_kabupaten_lama'));
        
        let fields = [
            'np_karyawan',
            'alamat_lama',
            'kode_provinsi_lama',
            'kode_pos_lama',
            'alamat_baru',
            'kode_provinsi_baru',
            'kode_pos_baru',
            'no_ktp',
            'approval_1_np',
            'keterangan'
        ];
        for (const i of fields) {
            $(`#${i}`).val($(data).data(`${i}`));
        }

        $('#np_karyawan').trigger('change');
        $('#kode_provinsi_lama').trigger('change');
        $('#kode_provinsi_baru').trigger('change');
        setTimeout(function() {$('#kode_kabupaten_lama').val($(data).data('kode_kabupaten_lama')).change()},500);
        setTimeout(function() {$('#kode_kecamatan_lama').val($(data).data('kode_kecamatan_lama')).change()},1000);
        setTimeout(function() {$('#kode_kelurahan_lama').val($(data).data('kode_kelurahan_lama')).change()},1500);

        setTimeout(function() {$('#kode_kabupaten_baru').val($(data).data('kode_kabupaten_baru')).change()},500);
        setTimeout(function() {$('#kode_kecamatan_baru').val($(data).data('kode_kecamatan_baru')).change()},1000);
        setTimeout(function() {$('#kode_kelurahan_baru').val($(data).data('kode_kelurahan_baru')).change()},1500);
        $('#approval_1_np').trigger('change');
        $('#ktp').prop('required',false);
        $('#edit_ktp').html('<label class="text-danger"><b>Upload Ulang Jika Ingin Mengganti File</b></label>');

        if($('#formulir_tambah').find('[name=edit_id]').length){
            $('#formulir_tambah').find(`[name=edit_id]`).val($(data).data('id'));
        } else{
            $('<input>').attr({
                type: 'hidden',
                name: 'edit_id',
                value: $(data).data('id')
            }).appendTo('#formulir_tambah');
        }
        $("html, body").animate({ scrollTop: 0 }, "slow");
    }

    $(document).on('click','.detail_button',function(e){
        e.preventDefault();
        $("#modal_detail").modal('show');
        $.post('<?php echo site_url("pelaporan/pindah_alamat/view_detail") ?>',
            {id_:$(this).attr('data-id')},
            function(e){
                $(".get-approve").html(e);
            }
        );
    });

    function getNamaAtasan1() {
        var np_atasan = $('#approval_1_np').val();
        if (np_atasan.length>3) {
            var np_karyawan = $('#np_karyawan').val();
            var insert_absence_type = $('#add_absence_type').val();

            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "pendidikan/ajax_getNama_approval",
                data: {"np_aprover":np_atasan, "np_karyawan":np_karyawan},
                success: function(msg){
                    if(msg.status == false){
                        alert (msg.message);
                        $('#approval_1_np').val('');
                        $('#approval_1_input').val('');
                        $('#approval_1_input_jabatan').val('');
                    }else{							 
                        $('#approval_1_input').val(msg.data.nama);
                        $('#approval_1_input_jabatan').val(msg.data.jabatan);
                    }													  
                }
            });
        } else if (np_atasan.length<4) {
            $('#approval_1_input').val('');
            $('#approval_1_input_jabatan').val('');
        }
    }

    $(document).on('click','.hapus',function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('<?= site_url("pelaporan/pindah_alamat/hapus") ?>/'+$(this).data('id')+'/'+$(this).data('np'),
                    function(get){
                        ret = JSON.parse(get);
                        if (ret.status==true) {
                            Swal.fire(
                                ret.msg,
                                '',
                                'success'
                            );
                            refresh_table_serverside();
                        } else {
                            Swal.fire(
                                ret.msg,
                                '',
                                'error'
                            );
                        }
                    }
                );
            }
        });
    });
</script>

<script>
$('#kode_provinsi_lama').change(function() {
    var prop = this.value;
    get_kab(prop,'kode_kabupaten_lama');
});
$('#kode_kabupaten_lama').change(function() {
    var prop = this.value;
    get_kec(prop,'kode_kecamatan_lama');
});
$('#kode_kecamatan_lama').change(function() {
    var prop = this.value;
    get_kel(prop,'kode_kelurahan_lama');
});

$('#kode_provinsi_baru').change(function() {
    var prop = this.value;
    get_kab(prop,'kode_kabupaten_baru');
});
$('#kode_kabupaten_baru').change(function() {
    var prop = this.value;
    get_kec(prop,'kode_kecamatan_baru');
});
$('#kode_kecamatan_baru').change(function() {
    var prop = this.value;
    get_kel(prop,'kode_kelurahan_baru');
});

function get_kab(prop, selector) {
    $('#'+selector).html('');
    $.ajax({
        type: 'GET',
        url: '<?= base_url('pelaporan/pindah_alamat/get_kab') ?>',
        dataType: 'JSON',
        data: {
        kode: prop,
        },
        success: function(res) {
        $('#'+selector).html(res);
        },
    });
    $('#'+selector).select2({
        placeholder: "Pilih Kabupaten"
    });
    return false
}

function get_kec(prop, selector) {
    $('#'+selector).html('');
    $.ajax({
        type: 'GET',
        url: '<?= base_url('pelaporan/pindah_alamat/get_kec') ?>',
        dataType: 'JSON',
        data: {
        kode: prop,
        },
        success: function(res) {
        $('#'+selector).html(res);
        },
    });
    $('#'+selector).select2({
        placeholder: "Pilih Kecamatan"
    });
    return false
}

function get_kel(prop, selector) {
    $('#'+selector).html('');
    $.ajax({
        type: 'GET',
        url: '<?= base_url('pelaporan/pindah_alamat/get_kel') ?>',
        dataType: 'JSON',
        data: {
        kode: prop,
        },
        success: function(res) {
        $('#'+selector).html(res);
        },
    });
    $('#'+selector).select2({
        placeholder: "Pilih Kelurahan"
    });
    return false
}
</script>