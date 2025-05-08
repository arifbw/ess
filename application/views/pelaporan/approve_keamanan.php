				<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
        		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

				<form role="form" action="<?php echo base_url(); ?>perizinan/persetujuan_keamanan/save_approve/" id="formulir_tambah" method="post">
					<div class="row">
						<div class="col-md-10">
							<table>
								<tr>
									<td>NP Pemohon</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a><?= $no_pokok ?></a></td>
								</tr>
								<tr>
									<td>Nama Pemohon</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a><?= $nama_pegawai ?></a></td>
								</tr>
								<tr>
									<td>Start Date Input</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a><?= $start_date_input ?></a></td>
								</tr>
								<tr>
									<td>End Date Input</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a><?= $end_date_input ?></a></td>
								</tr>
								<tr>
									<td><b>Start Date Realisasi</b></td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a class="text-danger"><b id="start_date_real"><?= $start_date ?></b></a></td>
								</tr>
								<tr>
									<td><b>End Date Realisasi</b></td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a class="text-danger"><b id="end_date_real"><?= $end_date ?></b></a></td>
								</tr>
								<!-- <tr>
									<td><b>Start Date Realisasi</b></td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a class="text-danger" id="text-start-date"><b><?= $start_date ?></b></a></td>
								</tr>
								<tr>
									<td><b>End Date Realisasi</b></td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a class="text-danger" id="text-end-date"><b><?= $end_date ?></b></a></td>
								</tr> -->
								<tr>
									<td>Dibuat Tanggal</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a><?= $tgl ?></a></td>
								</tr>
                                
                                <!-- tambahan untuk alasan, 2021-03-10 -->
								<tr>
									<td>Alasan</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a><?= $alasan ?></a></td>
								</tr>
                                <!-- END tambahan untuk alasan, 2021-03-10 -->
                                
							</table>
						</div>
						<div class="col-md-2">
							<table>
								<tr>
									<td><a data-toggle="modal" data-target="#modal_batal_izin" class="btn btn-block btn-danger"><i class="fa fa-times fa-md"></i> Batalkan Izin</a></td>
								</tr>
							</table>
						</div>
					</div>
					<br>

					<?php if ($status_1!='2') { ?>
					<div class="alert alert-info">
						<strong><a class="text-info"><?= $status_approval_1_nama ?></a></strong><br>
						<p><?= $status_approval_1_status ?></p>
					</div>
					<?php } else { ?>
					<div class="alert alert-danger">
						<strong><a class="text-danger"><?= $status_approval_1_nama ?></a></strong><br>
						<p><?= $status_approval_1_status ?></p>
						<p style="margin-top: 0">Alasan : <?= $status_approval_1_keterangan ?></p>
					</div>
					<?php } ?>
					
					<?php if ($approval_2!='' && $approval_2!='0' && $approval_2!=null) { ?>
					<?php if ($status_2!='2') { ?>
					<div class="alert alert-info">
						<strong><a class="text-info"><?= $status_approval_2_nama ?></a></strong><br>
						<p><?= $status_approval_2_status ?></p>
					</div>
					<?php } else { ?>
					<div class="alert alert-danger">
						<strong><a class="text-danger"><?= $status_approval_2_nama ?></a></strong><br>
						<p><?= $status_approval_2_status ?></p>
						<p style="margin-top: 0">Alasan : <?= $status_approval_2_keterangan ?></p>
					</div>
					<?php } ?>
					<?php } ?>

					<?php if (count($pengamanan) > 0) { ?>
					<div class="alert alert-info" id="alert-detail">
						<?php $no=1; foreach ($pengamanan as $aman) { ?>
						<strong>
                            <?php if($aman->status=='1'){?>
                            <a><?= $no ?>. <?= $aman->nama_pos ?> | <?= $aman->nama_approver ?> (<?= $aman->np_approver ?>) | <?= ucwords($aman->posisi) ?> Pada <?= $aman->waktu ?></a>
                            <?php } else{?>
                            <a><strike><?= $no ?>. <?= $aman->nama_pos ?> | <?= $aman->nama_approver ?> (<?= $aman->np_approver ?>) | <?= ucwords($aman->posisi) ?> Pada <?= $aman->waktu ?></strike></a>
                            <?php }?>
                            <?php if($aman->status=='1'){?>
                            &nbsp;&nbsp;<a title="Edit" data-no="<?= $no?>" data-created="<?= $aman->created?>" data-pos="<?= $aman->pos?>" data-waktu="<?= $aman->waktu?>" data-posisi="<?= $aman->posisi?>" onclick="edit_approval(this)"><i class="fa fa-pencil"></i></a>
                            &nbsp;&nbsp;<a title="Hapus" data-created="<?= $aman->created?>" onclick="hapus_approval(this)"><i class="fa fa-trash"></i></a>
                            <?php }?>
                        </strong>
                        <br>
						<?php $no++;} ?>
					</div>
					<?php } ?>
					
					<div class="alert alert-info">
						<strong><a>Approval Keamanan</a></strong>&nbsp;<small id="keterangan-tambahan"><i>(Input data baru)</i></small>
						<br>
						<div class="row">
                            <input type="hidden" id="created" value="">
							<div class="col-md-6">
								<select class="form-control select2" name="pos" id="pilih_pos" style="width: 100%;" required>
									<option value=''>Pilih Pos</option>
									<?php foreach($pos as $row){?>
                                    <option value="<?= $row->id ?>"><?= $row->nama ?></option>
                                    <?php } ?>
								</select>
							</div>
							<div class="col-md-6">
								<input type="text" class="form-control datetimepicker5" style="width: 100%;" name="waktu" placeholder="Masukkan Waktu Keluar/Masuk" required>
							</div>
							<!-- <div class="col-md-6">
								<select class="form-control select2" name='np_approver' style="width: 100%;" required>
									<option >Pilih NP</option>
									<option value='7648'>7648 - Tri Wibowo</option>
									<option value='7647'>7647 - Ofi</option>
								</select>
							</div> -->
							<div class="col-md-6">
								<select class="form-control select2" name="posisi" id="pilih_posisi" style="width: 100%;" required>
									<option value=''>Posisi</option>
									<option value='keluar'>Keluar</option>
									<option value='masuk'>Masuk</option>
								</select>
							</div>
                            
							<div class="col-md-6" id="div-cancel-edit" style="display: none;">
								<button class="btn btn-md btn-danger" id="btn-cancel-edit" onclick="cancel_edit()">
									Cancel
								</button>
							</div>
							
							<input type="hidden" name="date_range" value="<?= $date_range ?>">
							<input type="hidden" name="bulan_tahun" value="<?= $bulan_tahun ?>">
							<input type="hidden" name="get_pos" value="<?= $get_pos ?>">
							<input type="hidden" name="izin_0" value="<?= $izin_0 ?>">
							<input type="hidden" name="izin_C" value="<?= $izin_C ?>">
							<input type="hidden" name="izin_E" value="<?= $izin_E ?>">
							<input type="hidden" name="izin_F" value="<?= $izin_F ?>">
							<input type="hidden" name="izin_G" value="<?= $izin_G ?>">
							<input type="hidden" name="izin_H" value="<?= $izin_H ?>">
							<input type="hidden" name="izin_TM" value="<?= $izin_TM ?>">
							<input type="hidden" name="izin_TK" value="<?= $izin_TK ?>">
							<input type="hidden" name="id_perizinan" value="<?= $id_ ?>">
							<input type="hidden" name="tgl" value="<?= $date ?>">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 text-right">
							<input type="hidden" name="persetujuan_id" id="persetujuan_id">
							<input type="submit" name="submit" id='persetujuan_button' value="Approve" class="btn btn-block btn-success">
                            
                            <button type="button" id="btn-update" class="btn btn-block btn-success" style="display: none;" onclick="update_approval()">Update</button>
							 <!-- onclick="return checkRequired();" -->
						</div>
					</div>
				</form>


            <script src="<?= base_url()?>asset/moment.js/2.29.1/locale/id.min.js"></script>
			<script type="text/javascript">
			$(document).ready(function() {
				$('#id_perizinan_alasan').val('<?= $id_ ?>');
				$('#tgl_alasan').val('<?= $date ?>');
			});

			function close_modal_batal(obj){
				$('#modal_batal_izin').modal('hide');
			}
			function checkRequired(){
				pilih_pos = $('#pilih_pos').val();
				pilih_posisi = $('#pilih_posisi').val();
				if (pilih_pos=='' || pilih_posisi=='') {
					alert('Form Approval Harus Dilengkapi!'+pilih_pos+pilih_posisi);
					return false;
				}
				else {
					return true;
				}
			}
			function form_alasan(obj){
                var selectBox = obj;
                var selected = selectBox.options[selectBox.selectedIndex].value;
                var textarea = document.getElementById("form-alasan");

                if(selected === '2'){
                    textarea.style.display = "block";
                }
                else{
                    textarea.style.display = "none";
                }
            }
            $('.datetimepicker5').datetimepicker({
                format: 'YYYY-MM-DD HH:mm'
            });
            $('.select2').select2();
                
                function edit_approval(data){
                    var id_perizinan = $('input[name=id_perizinan]').val();
                    var tgl = $('input[name=tgl]').val();
                    var created = data.dataset.created;
                    var pos = data.dataset.pos;
                    var waktu = data.dataset.waktu;
                    var posisi = data.dataset.posisi;
                    var no = data.dataset.no;
                    
                    //console.log(id_perizinan);
                    //console.log(tgl);
                    
                    $('#div-cancel-edit').show();
                    $('#keterangan-tambahan').html(`<i>(Edit data no. ${no})</i>`);
                    
                    $('#pilih_pos').val(pos).trigger('change');
                    $('input[name=waktu]').val(waktu).trigger('change');
                    $('#pilih_posisi').val(posisi).trigger('change');
                    $('#created').val(created).trigger('change');
                    
                    $('#persetujuan_button').hide();
                    $('#btn-update').show();
                }
                
                function cancel_edit(){
                    $('#pilih_pos').val('').trigger('change');
                    $('input[name=waktu]').val('').trigger('change');
                    $('#pilih_posisi').val('').trigger('change');
                    $('#created').val('').trigger('change');
                    $('#div-cancel-edit').hide();
                    $('#keterangan-tambahan').html('<i>(Input data baru)</i>');
                    
                    $('#persetujuan_button').show();
                    $('#btn-update').hide();
                }
                
                function update_approval(){
                    var id_perizinan = $('input[name=id_perizinan]').val();
                    var tgl = $('input[name=tgl]').val();
                    
                    var created = $('#created').val();
                    var pos = $('#pilih_pos').val();
                    var nama_pos = $('#pilih_pos').find(':selected').text();
                    var posisi = $('#pilih_posisi').val();
                    var waktu = $('input[name=waktu]').val();
                    
                    /*console.log(pos);
                    console.log(nama_pos);
                    console.log(posisi);
                    console.log(waktu);*/
                    
                    $.ajax({
                        type: "POST",
                        url: '<?= base_url('perizinan/persetujuan_keamanan/update_approval')?>',
                        data: { id_perizinan: id_perizinan, tgl: tgl, created: created, pos: pos, nama_pos: nama_pos, posisi: posisi, waktu: waktu },
                        dataType: 'json',
                    }).then(function(response){
                        $('#alert-detail').html(response.new_text);
                        $('#start_date_real').text(response.start_date_realisasi);
                        $('#end_date_real').text(response.end_date_realisasi);
                        if(response.kode_pamlek!='0' && response.kode_pamlek!='G'){
                            $('#text-start-date').html(`<b>${moment(response.start_date_real, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
                        }
                        $('#text-end-date').html(`<b>${moment(response.end_date_real, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
                        //console.log(response);
                        $('#btn-cancel-edit').trigger('click');
                    }).catch(function(xhr, status, error){
                        $('#btn-cancel-edit').trigger('click');
                        alert(xhr.responseText);
                    })
                    
                }
                
                function hapus_approval(data){
                    var id_perizinan = $('input[name=id_perizinan]').val();
                    var tgl = $('input[name=tgl]').val();
                    var created = data.dataset.created;
                    var result = confirm("Hapus approval pada jam tersebut?");
                    if (result) {
                        $.ajax({
                            type: "POST",
                            url: '<?= base_url('perizinan/persetujuan_keamanan/hapus_approval')?>',
                            data: { id_perizinan: id_perizinan, tgl: tgl, created: created },
                            dataType: 'json',
                        }).then(function(response){
                            $('#alert-detail').html(response.new_text);
                        $('#start_date_real').text(response.start_date_realisasi);
                        $('#end_date_real').text(response.end_date_realisasi);
                            if(response.kode_pamlek!='0' && response.kode_pamlek!='G'){
                                $('#text-start-date').html(`<b>${moment(response.start_date_real, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
                            }
                            $('#text-end-date').html(`<b>${moment(response.end_date_real, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
                            //console.log(response);
                        }).catch(function(xhr, status, error){
                            alert(xhr.responseText);
                        })
                    }
                }
			</script>