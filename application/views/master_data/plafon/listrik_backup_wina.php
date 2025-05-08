<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css')?>" />

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
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?= $judul ?></a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <form role="form" action="<?= base_url(); ?>master_data/plafon/listrik" id="formulir_tambah" method="post" enctype="multipart/form-data">
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

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Alamat</label>
                                    </div>
                                    <div class="col-lg-7">                                                      
                                        <input class="form-control" name="alamat" id="alamat" placeholder="Masukkan Alamat" required>
                                    </div>                                                      
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>No Kontrol</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="number" name="no_kontrol" id="no_kontrol" placeholder="Masukkan No Kontrol" required>
                                    </div>														
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Plafon</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="number" name="plafon" id="plafon" placeholder="Masukkan Jumlah" required>
                                    </div>                                                      
                                </div>

                                <div class="row">
                                    <div class="col-lg-9 text-right">
                                        <input type="submit" name="submit" value="Simpan" class="btn btn-primary">
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
                    <table width="100%" class="table table-striped table-bordered table-hover" id="daftar_anak_tertanggung">
                        <thead>
                            <tr>
                                <th class="text-center no-sort" style="max-width: 5%">No</th>
                                <th class="text-center">Karyawan</th>  <!-- style="max-width: 15%" -->
                                <th class="text-center no-sort">Alamat</th>
                                <th class="text-center no-sort">No Kontrol</th>
                                <th class="text-center no-sort">Plafon</th>
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
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>
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
            format: 'D-M-Y'
        });

        $('#daftar_anak_tertanggung').DataTable().destroy();				
        table_serverside();
    });

    function refresh_table_serverside() {
        $('#daftar_anak_tertanggung').DataTable().destroy();				
        table_serverside();
    }

    function table_serverside() {
        var table;

        table = $('#daftar_anak_tertanggung').DataTable({ 
            
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
                "url": "<?= site_url("master_data/plafon/listrik")?>",
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
	function getNama(){
		var np_karyawan = $('#np_karyawan').val();

		$.ajax({
            type: "POST",
            dataType: "html",
            url: "<?php echo base_url('perizinan/permohonan_perizinan/ajax_getNama');?>",
            data: "vnp_karyawan="+np_karyawan,
            success: function(msg){
                if(msg == ''){
                    alert ('Silahkan isi No. Pokok Dengan Benar.');
                    $('#np_karyawan').val('');
                    $('#nama').text('');
                    $("#form_absence_type").hide();
                }else{							 
                    $('#nama').text(msg);
                    $("#form_absence_type").show();
                }													  
            }
		});       
	} 
</script>

<script>
    function listNp(){			
        $.ajax({
            type: "POST",
            dataType: "html",
            url: "<?php echo base_url('perizinan/permohonan_perizinan/ajax_getListNp');?>",
            success: function(msg){
                if(msg == ''){
                    alert ('Silahkan isi No. Pokok Dengan Benar.');
                    $('#list_np').text('');
                }else{							 
                    $('#list_np').text(msg);
                }													  
            }
        });       
    } 
</script>

<script>
    $(document).on( "click", '.status_button',function(e) {
        var status_np_karyawan = $(this).data('np-karyawan');
        var status_nama = $(this).data('nama');
        var status_created_at = $(this).data('created-at');
        var status_start = $(this).data('start-date');
        var status_end = $(this).data('end-date');
        var status_approval_1_nama = $(this).data('approval-1-nama');
        var status_approval_1_status = $(this).data('approval-1-status');
        var status_approval_1_alasan = $(this).data('approval-1-alasan');
        var status_approval_2_nama = $(this).data('approval-2-nama');
        var status_approval_2_status = $(this).data('approval-2-status');
        var status_approval_2_alasan = $(this).data('approval-2-alasan');
        var status_pamlek = $(this).data('pamlek');
        var batal_waktu = $(this).data('batal-waktu');
        var batal_alasan = $(this).data('batal-alasan');
        var batal_np = $(this).data('batal-np');

        $('#approver_2').hide();
        $('#batal').hide();
        if (status_pamlek != 'G') {
            $('#approver_2').show();
        }
        if (batal_np != '' && batal_np != null) {
            $('#batal').show();
        }

        $('#approver_1').removeClass('alert-info');
        $('#approver_1').removeClass('alert-danger');
        $('#approver_2').removeClass('alert-info');
        $('#approver_2').removeClass('alert-danger');
        $('#status_approval_1_nama').removeClass('text-primary');
        $('#status_approval_1_nama').removeClass('text-danger');
        $('#status_approval_2_nama').removeClass('text-primary');
        $('#status_approval_2_nama').removeClass('text-danger');

        $('#status_approval_1_alasan').css('display', 'none');
        if (status_approval_1_status.includes("TIDAK")==true) {
            $('#approver_1').addClass('alert-danger');
            $('#status_approval_1_nama').addClass('text-danger');
            $('#status_approval_1_alasan').css('display', '');
            $("#status_approval_1_alasan").text('Alasan : '+status_approval_1_alasan);
        } else {
            $('#approver_1').addClass('alert-info');
            $('#status_approval_1_nama').addClass('text-primary');
        }
        // console.log(status_approval_1_status.includes("TIDAK"));
        $('#status_approval_2_alasan').css('display', 'none');
        if (status_approval_2_status.includes("TIDAK")==true) {
            $('#approver_2').addClass('alert-danger');
            $('#status_approval_2_nama').addClass('text-danger');
            $('#status_approval_2_alasan').css('display', '');
            $("#status_approval_2_alasan").text('Alasan : '+status_approval_2_alasan);
        } else {
            $('#approver_2').addClass('alert-info');
            $('#status_approval_2_nama').addClass('text-primary');
        }

        $("#status_np_karyawan").text(status_np_karyawan);					
        $("#status_nama").text(status_nama);
        $("#status_created_at").text(status_created_at);	
        $("#status_start").text(status_start);
        $("#status_end").text(status_end);
        $("#status_approval_1_nama").text(status_approval_1_nama);				
        $("#status_approval_1_status").text(status_approval_1_status);
        $("#status_approval_2_nama").text(status_approval_2_nama);
        $("#status_approval_2_status").text(status_approval_2_status);
        $("#status_batal_np").text(batal_np);
        $("#status_batal_alasan").text(batal_alasan);
        $("#status_batal_waktu").text(batal_waktu);
    });
</script>

<script>
    $(document).on('click','.detail_button',function(e){
        e.preventDefault();
        $("#modal_detail").modal('show');
        $.post('<?php echo site_url("pelaporan/anak_tertanggung/view_detail") ?>',
            {id_:$(this).attr('data-id')},
            function(e){
                $(".get-approve").html(e);
            }
        );
    });

    $(document).on( "click", '.edit_button',function(e) {
        var edit_id = $(this).data('id');
        var edit_np_karyawan = $(this).data('np-karyawan');
        var edit_absence_type = $(this).data('absence-type');
        var edit_start_date = $(this).data('start-date');
        var edit_start_time = $(this).data('start-time');
        var edit_end_date = $(this).data('end-date');
        var edit_end_time = $(this).data('end-time');
            
        $("#edit_id").val(edit_id);
        $("#edit_start_date").val(edit_start_date);	
        $("#edit_start_time").val(edit_start_time);
        $("#edit_end_date").val(edit_end_date);				
        $("#edit_end_time").val(edit_end_time);
        
        document.getElementById("edit_np_karyawan").value = edit_np_karyawan;
        document.getElementById("edit_absence_type").value = edit_absence_type;
        take_action_date();
    });
    
    function hapus(id, np) {
        var url = "<?php echo site_url('pelaporan/anak_tertanggung/hapus') ?>/";
        $('#inactive-action').prop('href', url+id+'/'+np);
        $('#message-inactive').text('Apakah anda yakin ingin menghapus laporan pendidikan ini ?');
        $('#modal-inactive').modal('show');
    }
    
    function get_approval(){
        let insert_np_karyawan = $('#np_karyawan').find(':selected').val();
        let insert_absence_type = $('#add_absence_type').val();
        $('#approval_1_np').find('option').remove();
        $('#approval_2_np').find('option').remove();
        all_atasan_1_np=[];
        all_atasan_1_jabatan=[];
        all_atasan_2_np=[];
        all_atasan_2_jabatan=[];
        
        $.ajax({
            url: "<?php echo base_url('perizinan/filter_approval/get_approval');?>",
            type: "POST",
            dataType: "json",
            data: {np:insert_np_karyawan, absence_type:insert_absence_type},
            success: function(response){
                if(response.data.atasan_1.length>0){
                    $('#approval_1_jabatan').val(response.data.atasan_1[0].nama_jabatan);
                    $.each(response.data.atasan_1, function(i, item) {
                        all_atasan_1_np.push(response.data.atasan_1[i].no_pokok);
                        all_atasan_1_jabatan.push(response.data.atasan_1[i].nama_jabatan);
                        $('#approval_1_np').append(`<option value="`+response.data.atasan_1[i].no_pokok+`">`
                            +response.data.atasan_1[i].no_pokok+` - `+response.data.atasan_1[i].nama+
                        `</option>`);
                    });
                }
                if(response.data.atasan_2.length>0){
                    $('#approval_2_jabatan').val(response.data.atasan_2[0].nama_jabatan);
                    $.each(response.data.atasan_2, function(i, item) {
                        all_atasan_2_np.push(response.data.atasan_2[i].no_pokok);
                        all_atasan_2_jabatan.push(response.data.atasan_2[i].nama_jabatan);
                        $('#approval_2_np').append(`<option value="`+response.data.atasan_2[i].no_pokok+`">`
                            +response.data.atasan_2[i].no_pokok+` - `+response.data.atasan_2[i].nama+
                        `</option>`);
                    });
                }
            },
            error: function(e){
                console.log(e);
            }
        });
    }

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
    

    $('input[name="dates"]').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
</script>