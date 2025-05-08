<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link href="<?= base_url('asset/daterangepicker/daterangepicker.css')?>" rel="stylesheet" />

<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
		</div>

		<?php if( count($perizinan_belum_approve_atasan)>0 ){?>
		<div class="row">
			<div class="alert alert-warning">
				Permohonan yang <b>belum diapprove</b> Atasan:
				<?php
				foreach ($perizinan_belum_approve_atasan as $key) {
					$tanggal_izin = $key->start_date!=null ? tanggal_indonesia($key->start_date) : tanggal_indonesia($key->end_date);
					$kalimat = "- <b>{$key->np_karyawan} {$key->nama}</b>, {$key->nama_izin}, Tanggal <b>{$tanggal_izin}</b>. Belum diapprove ";
					if( $key->approval_1_status==null ){
						$kalimat .= "<b>{$key->approval_1_np} {$key->approval_1_nama}</b>, ";
					}
					if( $key->approval_2_np!=null && $key->approval_2_status==null ){
						$kalimat .= "<b>{$key->approval_2_np} {$key->approval_2_nama}</b>";
					}
					echo "<br><span class='detail_button' data-toggle='modal' data-target='#modal_detail' data-id='".$key->id."' data-tgl='".($key->start_date!=null ? $key->start_date: $key->end_date)."'>{$kalimat}</span>";
				}
				?>
			</div>
		</div>
		<?php } ?>

		<?php if(!empty($this->session->flashdata('success'))){ ?>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('success');?>
		</div>
		<?php
		}
		if(!empty($this->session->flashdata('warning'))){ ?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('warning');?>
		</div>
		<?php
		}
		if(@$akses["tambah"]){ ?>
		<div class="row">						
			<div class="panel panel-default">
				<div class="panel-heading" id="tambah-panel-color-izin">
					<h4 class="panel-title">
						<a href="javascript:;">Tambah <?php echo $judul;?></a>
					</h4>
				</div>
				<div id="collapseOne" class="panel-collapse show">
					<div class="panel-body">
						<form role="form" action="<?php echo base_url(); ?>perizinan/tambah_permohonan_perizinan/action_insert_perizinan" id="formulir_tambah" method="post">												
							<div class="row">
								<div class="form-group">
									<div class="col-lg-2">
										<label>NP Karyawan</label>
									</div>
									<div class="col-lg-7">
										<select class="form-control select2" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
											<option value=''></option>
											<?php 
											foreach ($array_daftar_karyawan->result_array() as $value) { ?>
												<option value='<?php echo $value['no_pokok']?>'><?php echo $value['no_pokok']." ".$value['nama']?></option>
											<?php 
											}

											foreach( $array_daftar_outsource as $value ){
												echo '<option value="'.$value['np_karyawan'].'">'.$value['np_karyawan'].' '.$value['nama'].'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</div>
									
							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Jenis Izin</label>
									</div>
									<div class="col-lg-7">
										<select class="form-control" name="absence_type" onchange="take_action_date(); show_approval();" id="add_absence_type" required>
											<option value="">-- Pilih jenis perizinan --</option>
											<?php foreach($jenis_izin as $row){?>
											<option value="<?= $row->kode_pamlek.'|'.$row->kode_erp?>"><?= $row->nama?></option>
											<?php } ?>
										</select>
									</div>														
								</div>
							</div>
									
							<?php
								$bulan_lalu = $data_tanggal	= date('Y-m-d',strtotime('-1 months',strtotime(date('Y-m-d'))));
								$sudah_cutoff = sudah_cutoff($bulan_lalu);
								
								if($sudah_cutoff)
								{										
									$min = date('Y-m')."-01";
								}else
								{
									$min = '';
								}
								
							?>
							<div id="form-start-date" style="display: block;">
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Start Date</label>
										</div>
										<div class="col-lg-7">
												<input type="text" class="form-control" name="start_date" id="start_date" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Start Time</label>
										</div>
										<div class="col-lg-7">
												<input type="text" class="form-control datetimepicker5" name="start_time" id="start_time" required>
										</div>
									</div>
								</div>
							</div>
									
							<div class="row">
								<div class="form-group">
									<div class="col-lg-2">
										<label>End Date</label>
									</div>
									<div class="col-lg-7">
											<input type="text" class="form-control" name="end_date" id="end_date" required>
									</div>														
								</div>
							</div>
									
							<div class="row">
								<div class="form-group">
									<div class="col-lg-2">
										<label>End Time</label>
									</div>
									<div class="col-lg-7">
											<input type="text" class="form-control datetimepicker5" name="end_time" required>
									</div>
								</div>
							</div>
									
							<div id="atasan_1" style="display:none;">
								<div class="form-group row">
									<div class="col-lg-2">
										<label>NP Atasan 1</label>								
									</div>
									<div class="col-lg-7">
										<input class="form-control" name="approval_1_np" id="approval_1_np" value="" onchange="getNamaAtasan1()" min="4" required>
									</div>								
								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label></label>
									</div>
									<div class="col-lg-7">														
										<input class="form-control" name="approval_1_input" id="approval_1_input" value="" readonly required>											
									</div>														
								</div>
									
								<div class="form-group row">
									<div class="col-lg-2">
										<label></label>
									</div>
									<div class="col-lg-7">														
										<input class="form-control" name="approval_1_input_jabatan" id="approval_1_input_jabatan" readonly required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>												
									</div>														
								</div>
							</div>

							<div id="atasan_2" style="display:none;">
								<div class="form-group row">
									<div class="col-lg-2">
										<label>NP Atasan 2</label>								
									</div>
									<div class="col-lg-7">
										<input class="form-control" name="approval_2_np" id="approval_2_np" value="" onchange="getNamaAtasan2()" min="4" required>
									</div>								
								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label></label>
									</div>
									<div class="col-lg-7">														
										<input class="form-control" name="approval_2_input" id="approval_2_input" value="" readonly required>											
									</div>														
								</div>
									
								<div class="form-group row">
									<div class="col-lg-2">
										<label></label>
									</div>
									<div class="col-lg-7">														
										<input class="form-control" name="approval_2_input_jabatan" id="approval_2_input_jabatan" readonly required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>												
									</div>														
								</div>
							</div>
									
							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Pos Yang Dilewati</label>
									</div>
									<div class="col-lg-7">
										<select class="form-control select2" name="pos[]" multiple id="add_pos" required style="width:100%">
											<?php foreach($pos as $row){?>
											<option value="<?= $row->id ?>"><?= $row->nama ?></option>
											<?php } ?>
										</select>
									</div>														
								</div>
							</div>
									
							<!-- tambahan untuk alasan, 2021-03-10 -->
							<div class="form-group row">
								<div class="col-lg-2">
									<label>Alasan</label>
								</div>
								<div class="col-lg-7">														
									<input class="form-control" name="alasan" id="alasan" required>
								</div>														
							</div>
							<!-- END tambahan untuk alasan, 2021-03-10 -->

							<div class="row">
								<div class="col-lg-9 text-right">
									<input type="submit" name="submit" value="submit" class="btn btn-primary">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
		<?php } ?>			
		
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>

<script src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js')?>"></script>

<script type="text/javascript">
	var all_atasan_1_np=[], all_atasan_1_jabatan=[], all_atasan_2_np=[], all_atasan_2_jabatan=[];
	var ref_jenis_izin = <?= json_encode($jenis_izin)?>;
	var mst_alasan_sipk = [];
	$('#multi_select').select2({
		closeOnSelect: false
		//minimumResultsForSearch: 20
	});

	$(document).ready(function() {
		get_mst_alasan_sipk();
		$('.datetimepicker5').datetimepicker({
			format: 'HH:mm'
		});

		$('.select2').select2();
		
		$('#start_date').datetimepicker({
			format: 'D-M-Y',
			<?php if(@$min){?>
			minDate : '<?php echo $min;?>'
			<?php } ?>
		});
		
		$('#end_date').datetimepicker({
			format: 'D-M-Y'
		});
		
		$("#start_date").on("dp.change", function (e) {
			var oldDate = new Date(e.date);
			var newDate = new Date(e.date);
			newDate.setDate(oldDate.getDate());

			$('#end_date').data("DateTimePicker").minDate(newDate); 
			
			var start_date = $('#start_date').val();;
			$('#end_date').val(start_date);
		});
		
		$("#form_absence_type").hide();
	});
</script>

<script>
	function getNama(){
		var np_karyawan = $('#np_karyawan').val();
		
		$.ajax({
			type: "POST",
			dataType: "html",
			url: "<?php echo base_url('perizinan/tambah_permohonan_perizinan/ajax_getNama');?>",
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
	
	function listNp(){
		$.ajax({
			type: "POST",
			dataType: "html",
			url: "<?php echo base_url('perizinan/tambah_permohonan_perizinan/ajax_getListNp');?>",
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
	$(document).on('click','.detail_button',function(e){
		e.preventDefault();
		$("#modal_detail").modal('show');
		$.post('<?php echo site_url("perizinan/tambah_permohonan_perizinan/view_detail") ?>',
			{id_perizinan:$(this).attr('data-id'), tgl:$(this).attr('data-tgl')},
			function(e){
				$(".get-approve").html(e);
			}
		);
	});
	
	function take_action_date(){
		var add_absence_type = $('#add_absence_type').val();
		if(add_absence_type=='0|2001|5000'){
			$('#form-start-date').css('display', 'none');
			$('#start_date').prop('required', false);
			$('#start_time').prop('required', false);
			$('#start_date').val('');
			$('#start_time').val('');
		} else{
			$('#form-start-date').css('display', 'block');
			$('#start_date').prop('required', true);
			$('#start_time').prop('required', true);
		}
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

	function show_approval(){
		let insert_np_karyawan = $('#np_karyawan').find(':selected').val();
		let insert_absence_type = $('#add_absence_type').val();
		let value = insert_absence_type;

		if (value=='G|2001|5050' || value=='0|2001|5000') {
			$('#approval_2_np').prop('required', false);
			$('#approval_2_input').prop('required', false);
			$('#approval_2_input_jabatan').prop('required', false);

			$('#atasan_2').css('display', 'none');
			$('#atasan_1').css('display', '');
		} else if (value=='H|2001|5030' || value=='D|2001|5040' || value=='C|2001|5040' || value=='F|2001|5020') {
			$('#approval_2_np').prop('required', true);
			$('#approval_2_input').prop('required', true);
			$('#approval_2_input_jabatan').prop('required', true);

			$('#atasan_1').css('display', '');
			$('#atasan_2').css('display', '');
		} else if (value=='E|2001|5010') {
			$('#approval_2_np').prop('required', true);
			$('#approval_2_input').prop('required', true);
			$('#approval_2_input_jabatan').prop('required', true);

			$('#atasan_1').css('display', '');
			$('#atasan_2').css('display', '');
		} else if(value=='SIPK|2001|5030') {
			$('#approval_2_np').prop('required', false);
			$('#approval_2_input').prop('required', false);
			$('#approval_2_input_jabatan').prop('required', false);

			$('#atasan_2').hide();
			$('#atasan_1').show();
		} else {
			$('#approval_2_np').prop('required', false);
			$('#approval_2_input').prop('required', false);
			$('#approval_2_input_jabatan').prop('required', false);

			$('#atasan_1').css('display', 'none');
			$('#atasan_2').css('display', 'none');
		}
	}
	
	function fill_jabatan(input,id){
		if (id.indexOf("1") >= 0){
			let index_of = all_atasan_1_np.indexOf(input.value);
			$('#'+id).val(all_atasan_1_jabatan[index_of]);
		} else if (id.indexOf("2") >= 0){
			let index_of = all_atasan_2_np.indexOf(input.value);
			$('#'+id).val(all_atasan_2_jabatan[index_of]);
		}
	}

	function getNamaAtasan1(){
		var approval_2_np = $('#approval_2_np').val();
		var np_atasan = $('#approval_1_np').val();
		if( np_atasan == " " ) {
			$('#approval_1_np').val('');
			$('#approval_1_input').val('');
			$('#approval_1_input_jabatan').val('');
		} else if( np_atasan!==approval_2_np ){
			if (np_atasan.length>3) {
				var np_karyawan = $('#np_karyawan').val();
				var insert_absence_type = $('#add_absence_type').val();

				$.ajax({
					type: "POST",
					dataType: "JSON",
					url: "tambah_permohonan_perizinan/ajax_getNama_approval/1",
					data: {
						"np_aprover":np_atasan, 
						"np_karyawan":np_karyawan, 
						"izin":insert_absence_type
					},
					success: function(msg){
						console.log(msg);
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
		} else{
			alert ('NP Atasan 1 tidak boleh sama dengan Atasan 2');
			$('#approval_1_np').val('');
			$('#approval_1_input').val('');
			$('#approval_1_input_jabatan').val('');
		}
	} 	

	function getNamaAtasan2(){
		var approval_1_np = $('#approval_1_np').val();
		var np_atasan = $('#approval_2_np').val();
		if( np_atasan == " " ) {
			$('#approval_2_np').val('');
			$('#approval_2_input').val('');
			$('#approval_2_input_jabatan').val('');
		} else if( np_atasan!==approval_1_np ){
			if (np_atasan.length>3) {
				var np_karyawan = $('#np_karyawan').val();
				var insert_absence_type = $('#add_absence_type').val();

				$.ajax({
					type: "POST",
					dataType: "JSON",
					url: "tambah_permohonan_perizinan/ajax_getNama_approval/2",
					data: {"np_aprover":np_atasan, "np_karyawan":np_karyawan, "izin":insert_absence_type},
					success: function(msg){
						if(msg.status == false){
							alert (msg.message);
							$('#approval_2_np').val('');
							$('#approval_2_input').val('');
							$('#approval_2_input_jabatan').val('');
						}else{
							$('#approval_2_input').val(msg.data.nama);
							$('#approval_2_input_jabatan').val(msg.data.jabatan);
						}													  
					}
				});
			} else if (np_atasan.length<4) {
				$('#approval_2_input').val('');
				$('#approval_2_input_jabatan').val('');
			}
		} else{
			alert ('NP Atasan 2 tidak boleh sama dengan Atasan 1');
			$('#approval_2_np').val('');
			$('#approval_2_input').val('');
			$('#approval_2_input_jabatan').val('');
		}
	}
	
	// tambahan untuk date range, 2021-02-24
	$('input[name="dates"]').daterangepicker({
		locale: {
			format: 'DD-MM-YYYY'
		}
	});
	// END: tambahan untuk date range, 2021-02-24

	// untuk ubah background izin, 2022-05-31
	$('#add_absence_type').on('change', (e)=>{
		let parent = $('#tambah-panel-color-izin').parent().closest('div');
		if( e.target.value!=='' ){
			let explode = e.target.value.split('|');
			let find = _.find(ref_jenis_izin, (o)=>{ return o.kode_pamlek==explode[0] && o.kode_erp==`${explode[1]}|${explode[2]}` });
			if( typeof find!='undefined' ) {
				$('#tambah-panel-color-izin').css({"background-color": find.theme_color, opacity: 0.75, color: `${find.font_color}`});
				parent.css({"border-color": `${find.theme_color!=='#fff' ? find.theme_color:'#ddd'}`});
			} else {
				$('#tambah-panel-color-izin').css({"background-color": "#fff", color: '#333333'});
				parent.css({"border-color": "#ddd"});
			}
		} else{
			$('#tambah-panel-color-izin').css({"background-color": "#fff", color: '#333333'});
			parent.css({"border-color": "#ddd"});
		}

		if( e.target.value == 'SIPK|2001|5030' ) alasan_set_to_option();
		else alasan_set_to_input();
	});
	// END: untuk ubah background izin, 2022-05-31

	const get_mst_alasan_sipk = async()=>{
		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: "<?php echo base_url('perizinan/tambah_permohonan_perizinan/get_mst_alasan_sipk');?>",
			data: {},
			success: function(res){
				mst_alasan_sipk = res;
			}
		});
	}

	const alasan_set_to_option = ()=>{
		let html_select = `<select class="form-control" name="alasan" style="width: 100%;" required>`;
		for (const i of mst_alasan_sipk) {
			html_select += `<option value="${i.alasan}">${i.alasan}</option>`;
		}
		html_select += `</select>`;
		$('#formulir_tambah').find('[name=alasan]').replaceWith(html_select);
	}

	const alasan_set_to_input = ()=>{
		let html_input = `<input class="form-control" name="alasan" id="alasan" required>`;
		$('#formulir_tambah').find('[name=alasan]').replaceWith(html_input);
	}
</script>

