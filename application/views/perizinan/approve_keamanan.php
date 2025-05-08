<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

<form role="form" action="<?php echo base_url(); ?>perizinan/persetujuan_keamanan/save_approve/" id="formulir_approve" method="post" onsubmit="return false;">
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
			&nbsp;&nbsp;<a title="Edit" data-no="<?= $no?>" data-created="<?= $aman->created?>" data-pos="<?= $aman->pos?>" data-nama_pos="<?= $aman->nama_pos?>" data-waktu="<?= $aman->waktu?>" data-posisi="<?= $aman->posisi?>" onclick="edit_approval(this)"><i class="fa fa-pencil"></i></a>
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
				<input type="text" value='<?php echo date('Y-m-d H:i:s')?>' class="form-control datetimepicker5" style="width: 100%;" name="waktu" placeholder="Masukkan Waktu Keluar/Masuk" required>
				<small>Check Kembali Tanggal dan Jam</small>
			</div>
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
			<!-- <input type="submit" name="submit" id='persetujuan_button' value="Approve" class="click btn btn-block btn-success"> -->
			
			<button type="button" id="persetujuan_button" class="btn btn-block btn-success" onclick="save_approve_ajax()">Approve</button>
			<button type="button" id="btn-update" class="btn btn-block btn-success" style="display: none;" onclick="update_approval()">Update</button>
		</div>
	</div>
</form>


<!-- 10 05 2021 - Tri Wibowo - Matiin akses luar
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>" integrity="sha512-he8U4ic6kf3kustvJfiERUpojM8barHoz0WYpAUDWQVn61efpm3aVAD8RWL8OloaDDzMZ1gZiubF9OSdYBqHfQ==" crossorigin="anonymous"></script>
-->

	<script src="<?php echo base_url('asset/daterangepicker-master')?>/daterangepicker2.css" integrity="sha512-he8U4ic6kf3kustvJfiERUpojM8barHoz0WYpAUDWQVn61efpm3aVAD8RWL8OloaDDzMZ1gZiubF9OSdYBqHfQ==" crossorigin="anonymous"></script>

<script type="text/javascript">
	var temp_pos_list = <?= json_encode($pos)?>;
	$(document).ready(function() {
		
		//11 03 2022, 7648 Tri Wibowo, Jika sudah klik tombol approve maka disable button, untuk mengantisipasi banyak data
		$("#formulir_approve").submit(function (e) {
			//disable the submit button
			$("#persetujuan_button").attr("disabled", true);
		});
		//end of 11 03 2022, 7648 Tri Wibowo

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
	async function edit_approval(data){
		var id_perizinan = $('input[name=id_perizinan]').val();
		var tgl = $('input[name=tgl]').val();
		var created = data.dataset.created;
		var pos = data.dataset.pos;
		var nama_pos = data.dataset.nama_pos;
		var waktu = data.dataset.waktu;
		var posisi = data.dataset.posisi;
		var no = data.dataset.no;
		
		await cek_id_pos(pos, nama_pos)
		
		$('#div-cancel-edit').show();
		$('#keterangan-tambahan').html(`<i>(Edit data no. ${no})</i>`);
		
		$('#pilih_pos').val(pos).trigger('change');
		$('input[name=waktu]').val(waktu).trigger('change');
		$('#pilih_posisi').val(posisi).trigger('change');
		$('#created').val(created).trigger('change');
		
		$('#persetujuan_button').hide();
		$('#btn-update').show();
	}

	async function cek_id_pos(pos, nama_pos){
		let find = _.find(temp_pos_list, o=>{ return o.id==pos;});
		if( typeof find=='undefined' ){
			temp_pos_list.push({id: pos, kode_pos: `P${pos}`, nama: nama_pos, no_pokok: null, status: '1', ket: '0'});
		}
		await reset_option();
		let a = [];
		$("#pilih_pos option").each(function(){
			a.push($(this).val());
		});
		if(a.includes(pos)===false) $('#pilih_pos').append(new Option(nama_pos, pos));
	}
	
	async function cancel_edit(){
		await reset_option();
		$('#pilih_pos').val('').trigger('change');
		$('input[name=waktu]').val('').trigger('change');
		$('#pilih_posisi').val('').trigger('change');
		$('#created').val('').trigger('change');
		$('#div-cancel-edit').hide();
		$('#keterangan-tambahan').html('<i>(Input data baru)</i>');
		
		$('#persetujuan_button').show();
		$('#btn-update').hide();
		$("#persetujuan_button").attr("disabled", false);
	}

	async function reset_option(){
		let filter = await _.filter(temp_pos_list, o=>{ return o.ket=='1'; });
		$('#pilih_pos').html('');
		$('#pilih_pos').append(new Option('Pilih Pos', ''));
		for (const i of filter) {
			$('#pilih_pos').append(new Option(i.nama, i.id));
		}
	}
	
	function update_approval(){
		var id_perizinan = $('input[name=id_perizinan]').val();
		var tgl = $('input[name=tgl]').val();
		
		var created = $('#created').val();
		var pos = $('#pilih_pos').val();
		var nama_pos = $('#pilih_pos').find(':selected').text().trim();
		var posisi = $('#pilih_posisi').val();
		var waktu = $('input[name=waktu]').val();
		
		$.ajax({
			type: "POST",
			url: '<?= base_url('perizinan/persetujuan_keamanan/update_approval')?>',
			data: { id_perizinan: id_perizinan, tgl: tgl, created: created, pos: pos, nama_pos: nama_pos, posisi: posisi, waktu: waktu },
			dataType: 'json',
		}).then(function(response){
			// reset_alert_detail(response);
			// $('#alert-detail').html(response.new_text);
			// $('#start_date_real').text(response.start_date_realisasi);
			// $('#end_date_real').text(response.end_date_realisasi);
			// if(response.kode_pamlek!='0' && response.kode_pamlek!='G'){
			// 	$('#text-start-date').html(`<b>${moment(response.start_date_real, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
			// }
			// $('#text-end-date').html(`<b>${moment(response.end_date_real, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
			$('#btn-cancel-edit').trigger('click');
			Swal.fire('',response.message,'success').then(function() {
				$('#modal_persetujuan').modal('hide');
			});
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

	/** ubah approval ke ajax */
	function collect_data(){
		let data = {};
		let array = $('#formulir_approve').serializeArray();
		for (const i of array) {
			data[i.name] = i.value;
		}
		return data;
	}

	async function save_approve_ajax(){
		let data = await collect_data();
		if( data.pos=='' ){
			alert('Pos belum diisi');
		} else if( data.posisi=='' ){
			alert('Posisi belum diisi');
		} else if( data.waktu=='' ){
			alert('Jam dan tanggal belum diisi');
		} else{
			$("#persetujuan_button").attr("disabled", true);
			$.ajax({
				type: "POST",
				url: '<?= base_url('perizinan/persetujuan_keamanan/save_approve_ajax')?>',
				data: data,
				dataType: 'json',
			}).then(function(response){
				// if( response.status==true ){
				// 	$('#pilih_pos').val('').trigger('change');
				// 	$('#pilih_posisi').val('').trigger('change');
				// 	$('input[name=waktu]').val('').trigger('change');
				// 	reset_alert_detail(response);
				// } else{
				// 	alert(response.message);
				// }
				// $("#persetujuan_button").attr("disabled", false);

				var type = response.status==true ? 'success' : 'error';
				Swal.fire('',response.message,type).then(function() {
					$('#modal_persetujuan').modal('hide');
				});
			}).catch(function(xhr, status, error){
				alert(xhr.responseText);
				$("#persetujuan_button").attr("disabled", false);
			})
		}
	}

	async function reset_alert_detail(data){
		$('#alert-detail').html('');
		let ordered = await _.orderBy(data.new_pengamanan_posisi, ['waktu'],['asc']);
		let filtered1 = await _.filter(data.new_pengamanan_posisi, o=>{ return o.status=='1';});
		let new_text = '';
		let no = 1;
		for (const i of ordered) {
			new_text += '<strong>';
			if( i.status=='1' ) new_text += `<a>${no}. ${i.nama_pos} | ${i.nama_approver} (${i.np_approver}) | ${i.posisi} Pada ${i.waktu}</a>`;
			else new_text += `<a><strike>${no}. ${i.nama_pos} | ${i.nama_approver} (${i.np_approver}) | ${i.posisi} Pada ${i.waktu}</strike></a>`;
			new_text += `
						&nbsp;&nbsp;<a title="Edit" data-no="${no}" data-created="${i.created}" data-pos="${i.pos}" data-nama_pos="${i.nama_pos}" data-waktu="${i.waktu}" data-posisi="${i.posisi}" onclick="edit_approval(this)"><i class="fa fa-pencil"></i></a>`;
			if(i.status=='1'){
				new_text += `&nbsp;&nbsp;<a title="Hapus" data-created="${i.created}" onclick="hapus_approval(this)"><i class="fa fa-trash"></i></a>`;
			}
			new_text += `</strong><br>`;
			no++;
		}
		$('#alert-detail').html(new_text);

		let first = _.minBy(filtered1, 'waktu');
		if(typeof first.waktu!='undefined'){
			if( ['0','G'].includes(data.kode_pamlek)===false ){
				$('#start_date_real').html(`<b>${moment(first.waktu, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
			} else{
				$('#start_date_real').html('');
			}
		} else{
			$('#start_date_real').html('');
		}

		let last = _.maxBy(filtered1, 'waktu');
		if(typeof last.waktu!='undefined'){
			$('#end_date_real').html(`<b>${moment(last.waktu, 'YYYY-MM-DD HH:mm').format('DD MMMM YYYY HH:mm')}</b>`);
		} else{
			$('#end_date_real').html('');
		}
	}
</script>