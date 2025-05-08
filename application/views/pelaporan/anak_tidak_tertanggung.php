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
        <?php if(@$regulasi): ?>
            <div class="alert alert-info">
                <p><?= $regulasi ?></p>
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
                            <form role="form" action="<?= base_url(); ?>pelaporan/anak_tidak_tertanggung/action_insert_anak_tidak_tertanggung" id="formulir_tambah" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <input type='hidden' id='edit_id' name='edit_id'>
                                    <div class="col-lg-2">
                                        <label>NP Karyawan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
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
                                        <label>DATA ANAK</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Nama</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="text" name="nama_anak" id="nama_anak" placeholder="Masukkan Nama Anak" required>
                                    </div>														
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Jenis Kelamin</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="jenis_kelamin_anak" id="jenis_kelamin_anak" style="width: 100%" required>
                                            <option></option>
                                            <option value="L">Laki-Laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>														
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Tempat Lahir</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="text" name="tempat_lahir_anak" id="tempat_lahir_anak" placeholder="Masukkan Tempat Lahir Anak" required>
                                    </div>														
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Tanggal Lahir</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control tanggal" type="text" name="tanggal_lahir_anak" id="tanggal_lahir_anak" placeholder="Masukkan Tanggal Lahir Anak" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Anak Ke</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="number" name="anak_ke" id="anak_ke" placeholder="Masukkan Anak Nomor Berapa" required>
                                    </div>											
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Alasan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select name="alasan" id="alasan" class="form-control select2" data-placeholder="Pilih Alasan" style="width: 100%" required="">
                                            <option value="">Pilih Alasan</option>
                                            <option value="Tidak Sekolah">Tidak Sekolah</option>
                                            <option value="Sudah Lulus Kuliah">Sudah Lulus Kuliah</option>
                                            <option value="Sudah Bekerja">Sudah Bekerja</option>
                                            <option value="Sudah Menikah">Sudah Menikah</option>
                                            <option value="Meninggal Dunia">Meninggal Dunia</option>                
                                        </select>
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
                    <table width="100%" class="table table-striped table-bordered table-hover" id="daftar_kelahiran_anak">
                        <thead>
                            <tr>
                                <th class="text-center no-sort" style="max-width: 5%">No</th>
                                <th class="text-center">Pegawai</th>  <!-- style="max-width: 15%" -->
                                <th class="text-center no-sort">Nama Anak</th>
                                <th class="text-center no-sort">Tanggal Lahir Anak</th>
                                <th class="text-center no-sort">Anak Ke</th>
                                <th class="text-center no-sort">Alasan</th>
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
                                <b>Hapus Laporan kelahiran_anak</b>
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
        $('#jenis_kelamin_anak').select2({
            placeholder: "Jenis Kelamin"
        });
        $('.tanggal').datetimepicker({
            format: 'Y-MM-D'
        });

        $('#daftar_kelahiran_anak').DataTable().destroy();				
        table_serverside();
    });

    function refresh_table_serverside() {
        $('#daftar_kelahiran_anak').DataTable().destroy();				
        table_serverside();
    }

    function table_serverside() {
        var table;

        table = $('#daftar_kelahiran_anak').DataTable({ 
            
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
                "url": "<?= site_url("pelaporan/anak_tidak_tertanggung/tabel_anak_tidak_tertanggung/")?>",
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
        
        let fields = ['np_karyawan', 'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak', 'anak_ke', 'jenis_kelamin_anak', 'alasan', 'approval_1_np', 'keterangan'];
        for (const i of fields) {
            $(`#${i}`).val($(data).data(`${i}`));
        }

        $('#np_karyawan').trigger('change');
        $('#jenis_kelamin_anak').trigger('change');
        $('#alasan').trigger('change');
        $('#approval_1_np').trigger('change');

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
        $.post('<?php echo site_url("pelaporan/anak_tidak_tertanggung/view_detail") ?>',
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
                $.get('<?= site_url("pelaporan/anak_tidak_tertanggung/hapus") ?>/'+$(this).data('id')+'/'+$(this).data('np'),
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