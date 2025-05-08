<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
		</div>

		<div class="alert alert-info">
			Sesuai kebijakaan yang berlaku vaksin keluarga hanya untuk usia tanggungan <strong>minimal 12 Tahun</strong>
		</div>

		<?php
			if( @$this->session->flashdata('success') ){
		?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('success');?>
				</div>
		<?php
			}
			if( @$this->session->flashdata('failed') ){
		?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('failed');?>
				</div>
		<?php
			} ?>
		
		<div class="row">
			<div class="col-lg-9 col-md-9 col-sm-12 text-left">
				<label>Filter</label>
				<select class="form-control" id="filter-jenis" style="width: 30%;" onchange="tableServerside()">
					<option value="all">Semua data</option>
					<option value="1">Sudah input</option>
					<option value="2">Belum input</option>
				</select>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 text-right">
				<?php
				if( @$akses["export"] ){
					echo '<button class="btn btn-success btn-md" onclick="exportExcel()">Export</button>&nbsp;';
				}
				
				if( @$akses["lihat log"] ){
					echo '<button class="btn btn-default btn-md" onclick="lihat_log()">Lihat Log</button>';
				}
				?>
			</div>
		</div>
		<br>
		
		<?php
			if($this->akses["lihat"]){
		?>
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">	
					<div class="row">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_vaksin">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>NP</th>	
									<th class='text-center'>Nama Karyawan</th>	
									<th class='text-center'>Tipe Keluarga</th>			
									<th class='text-center'>Nama Keluarga</th>
									<th class='text-center'>Tanggal Lahir</th>
									<th class='text-center'>Usia (per 2021)</th>
									<th class='text-center'>Status Vaksin</th>
									<th class='text-center no-sort'>Aksi</th>
								</tr>
							</thead>
						</table>
					</div>						
				</div>
			</div>
		</div>
		<?php
			}
		?>

		<?php if(@$akses['ubah']){?>
		<div class="modal fade" id="modal-ubah" aria-labelledby="label_modal_ubah" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="ubah-title"><strong>Insert Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">×</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
					  	<?php $this->load->view('vaksinasi/data_vaksin_keluarga/input')?>
		      		</div>
		    	</div>
		  	</div>
		</div>
		<?php } ?>

	</div>
</div>

<script src="<?= base_url()?>asset/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/moment.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/locale/id.min.js"></script>
<script src="<?= base_url()?>asset/lodash.js/4.17.21/lodash.min.js"></script>
<script type="text/javascript">
	var table;
	var allKlinik;
	$(document).ready(function() {
		tableServerside();
		getAllKlinik();

		$('#formulir-ubah').validate({
            rules: {
                ubah_np_karyawan: {
                    required: true
                }, 
                ubah_nama_karyawan: {
                    required: true
                }, 
                ubah_tipe_keluarga: {
                    required: true
                },
                ubah_nama_lengkap: {
                    required: true
                },
                ubah_nik: {
                    required: true
                },
                // ubah_email: {
                //     required: true
                // },
                ubah_no_hp: {
                    required: true
                },
                ubah_status_kawin: {
                    required: true
                },
                ubah_alamat: {
                    required: true
                },
                ubah_status_vaksin: {
                    required: true
                }
            },
            submitHandler: function (form) {
                save()
            }
        });
	});

	const tableServerside = async () => {
		let sessionGroup = '<?= $_SESSION['grup']?>';
		let jenis = $('#filter-jenis').val();
		table = $('#tabel_vaksin').DataTable({ 
			"iDisplayLength": 10,
			"language": {
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database",
				"sProcessing":   "Sedang memproses...",
				"sLengthMenu":   "Tampilkan _MENU_ entri",
				"sZeroRecords":  "Tidak ditemukan data yang sesuai",
				"sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
				"sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
				"sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
				"sInfoPostFix":  "",
				"sSearch":       "Cari NP:",
				"sUrl":          "",
				"oPaginate": {
					"sFirst":    "Pertama",
					"sPrevious": "Sebelumnya",
					"sNext":     "Selanjutnya",
					"sLast":     "Terakhir"
				}
			},
			"destroy": true,
			"stateSave": true,
			"processing": true,
			"serverSide": true,
			"ordering": false,
			"ajax": {				
				"url"	: "<?php echo site_url("vaksinasi/data_vaksin_keluarga/tabel_data")?>",					 
				"type"	: "POST",
				"data"	: {jenis: jenis}
			},
			"columnDefs": [
				{ 
					"targets": 'no-sort',
					"orderable": false
				},
			],
			columns: [
				{
					data: 'no',
					name: 'no',
				},
				{
					data: 'np_karyawan',
					name: 'np_karyawan',
				},
				{
					data: 'nama_karyawan',
					name: 'nama_karyawan',
				},
				{
					data: 'tipe_keluarga',
					name: 'tipe_keluarga',
				},
				{
					data: 'nama_lengkap',
					name: 'nama_lengkap',
				},
				{
					render: (data, type, row) => {
						return moment(row.tanggal_lahir).format('DD MMMM YYYY');
					}
				},
				{
					render: (data, type, row) => {
						let text='';
						if(row.usia!==null){
							text += row.usia;
						} else{
							text += 2021 - parseInt(moment(row.tanggal_lahir).format('YYYY'));
						}
						return text;
					}
				},
				{
					render: (data, type, row) => {
						let label='';
						if( 2021 - parseInt(moment(row.tanggal_lahir).format('YYYY')) >= 12 ){
							if(row.dibatalkan_admin==='1'){
								const status = $('<span/>', {
									html: 'Dibatalkan Admin',
									class: 'label label-danger'
								})
								label += status.prop('outerHTML');
							} else{
								if(row.created_at!=null){
									if(row.status_vaksin==='1'){
										if(row.tanggal_vaksin_2!==null){
											const status = $('<span/>', {
												html: 'Sudah Vaksin 2',
												class: 'label label-success'
											})
											label += status.prop('outerHTML');
										} else{
											const status = $('<span/>', {
												html: 'Sudah Vaksin 1',
												class: 'label label-success'
											})
											label += status.prop('outerHTML');
										}
									} else{
										const status = $('<span/>', {
											html: 'Belum Vaksin',
											class: 'label label-warning'
										})
										label += status.prop('outerHTML');
									}
								} else{
									const status = $('<span/>', {
										html: 'Belum Input',
										class: 'label label-danger'
									})
									label += status.prop('outerHTML');
								}
							}
						} else{
							label += '-';
						}
						return label;
					}
				},
				{
					render: (data, type, row) => {
						let btn='';
						let d = new Date();
						if(jQuery.inArray(sessionGroup, ['4','5']) !== -1){
							if( (2021 - parseInt(moment(row.tanggal_lahir).format('YYYY'))) >= 12 && ( moment(d).format('YYYY-MM-DD') < moment('2021-10-28').format('YYYY-MM-DD') ) ){
								const input = $('<button/>', {
									html: 'Input',
									class: 'btn btn-primary btn-sm',
									onclick: `input(${JSON.stringify(row)})`,
								})
								btn += input.prop('outerHTML');
							} else{
								btn += '-';
							}
						} else if(jQuery.inArray(sessionGroup, ['12']) !== -1){
							if( (2021 - parseInt(moment(row.tanggal_lahir).format('YYYY'))) >= 12 && ( moment(d).format('YYYY-MM-DD') < moment('2021-10-28').format('YYYY-MM-DD') ) ){
								const input = $('<button/>', {
									html: 'Input',
									class: 'btn btn-primary btn-sm',
									onclick: `input(${JSON.stringify(row)})`,
								})
								btn += input.prop('outerHTML');
							} else{
								btn += '-';
							}
						}

						return btn;
					}
				}
			],
		});
	};

	const doUbah = () => {
		let nik = $("#ubah_nik").val();
		let hp = $("#ubah_no_hp").val();
		if( $('input[name=ubah_status_vaksin]:checked').val()==undefined ){
			alert('Status Vaksin harus diisi');
		} else if( nik.length!==16 ){
			alert('NIK harus 16 digit');
		} else if( hp.length > 13 ){
			alert('Nomor HP max 13 digit');
		} else{
			var r = confirm("Perhatian!\nAnda hanya bisa melakukan submit sebelum masa cut off pada 30 September 2021. Pastikan isian Anda sudah benar.\nLanjutkan?");
			if (r == true) {
				$('#formulir-ubah').submit();
			}
		}

		/*if( $('input[name=ubah_status_vaksin]:checked').val()!==undefined ){
			var r = confirm("Perhatian!\nAnda hanya bisa melakukan submit sebelum masa cut off pada 30 September 2021. Pastikan isian Anda sudah benar.\nLanjutkan?");
			if (r == true) {
				$('#formulir-ubah').submit();
			}
		} else
			alert('Status Vaksin harus diisi');*/
	}

	const save = () => {
		let allData = {};
        $("#formulir-ubah input").each(function(){
			if( $(this).attr('type')=='radio' ){
				if( $(this).is(':checked') )
				allData[$(this).attr('name')] = this.value;
            } else{
				allData[$(this).attr('name')] = this.value;
            }
        });
        $("#formulir-ubah select").each(function(){
			allData[$(this).attr('name')] = this.value;
        });
        $("#formulir-ubah textarea").each(function(){
			allData[$(this).attr('name')] = this.value;
        });
		
		$('#alert-formulir-ubah').html('Menyimpan...');
		$('#btn-submit-ubah').prop('disabled',true);
		$.ajax({
            type: "POST",
            url: `<?= base_url('vaksinasi/data_vaksin_keluarga/save')?>`,
            data: allData,
            dataType: 'json',
        }).then(function(response){
            $('#btn-submit-ubah').prop('disabled',false);
            $('#alert-formulir-ubah').html(response.message);
			if(response.status===true){
				table.draw(false);
				setTimeout(function(){ $('#alert-formulir-ubah').html(''); }, 5000);
				$("#formulir-ubah")[0].reset();
				// $("#ubah_alamat_kode_prov" ).trigger('change');
				// $("#form-klinik").hide();
				// $("#ubah_mst_klinik_id").prop('required',false);
				$("#form-sudah").hide();
				$("#form-belum").hide();
				$("#ubah_alasan").prop('required', false);
				$("#ubah_tanggal_vaksin_1").prop('required', false);
				$("#ubah_lokasi_vaksin_1").prop('required', false);
				$("#btn-close").trigger('click');
			}
        }).catch(function(xhr, status, error){
            $('#btn-submit-ubah').prop('disabled',false);
            $('#alert-formulir-ubah').html(xhr.responseText);
        })
	}

	const input = async (data) => {
		// console.log(data);
		let fields = ['np_karyawan','nama_karyawan','tipe_keluarga','nama_lengkap','usia','tanggal_lahir','nik','no_hp','alamat','status_kawin','tempat_lahir_keluarga', 'tanggal_vaksin_1', 'tanggal_vaksin_2', 'alasan'];
		$.each(data, function(key, value){
			if(fields.includes(key)){
				$(`#ubah_${key}`).val(value);
			}
		});
		let usia = data.usia!==null ? data.usia : ( parseInt(2021) - parseInt(moment(data.tanggal_lahir).format('YYYY')));
		$(`#ubah_usia`).val(usia);

		// status vaksin
		if(data.status_vaksin!==null){
			if(data.status_vaksin==='1'){ // sudah vaksin
				$(`#status_vaksin_value_1`).trigger('click');
				if(data.tanggal_vaksin_1!==null) $('#ubah_tanggal_vaksin_1').val(data.tanggal_vaksin_1);
				if(data.lokasi_vaksin_1!==null) $('#ubah_lokasi_vaksin_1').val(data.lokasi_vaksin_1);
				if(data.tanggal_vaksin_2!==null) $('#ubah_tanggal_vaksin_2').val(data.tanggal_vaksin_2);
				if(data.lokasi_vaksin_2!==null) $('#ubah_lokasi_vaksin_2').val(data.lokasi_vaksin_2);
			}
			if(data.status_vaksin==='2'){ // belum vaksin
				if(data.alasan!==null) $('#ubah_alasan').val(data.alasan);

				$(`#status_vaksin_value_2`).trigger('click');
				/*
				if(data.provinsi!==null){
					await $('#ubah_alamat_kode_prov').val(data.provinsi);
					await $('#ubah_alamat_kode_prov').trigger('change');
				}

				if(data.kabupaten!==null){
					await $('#ubah_alamat_kode_kab').val(data.kabupaten);
					await $('#ubah_alamat_kode_kab').trigger('change');
				}

				if(data.kecamatan!==null){
					await $('#ubah_alamat_kode_kec').val(data.kecamatan);
					await $('#ubah_alamat_kode_kec').trigger('change');
				}

				if(data.kelurahan!==null){
					await $('#ubah_alamat_kode_kel').val(data.kelurahan);
					await $('#ubah_alamat_kode_kel').trigger('change');
				}

				if(data.mst_klinik_id!==null){
					await $('#ubah_mst_klinik_id').val(data.mst_klinik_id);
					await $('#ubah_mst_klinik_id').trigger('change');
				}
				*/
			}
		}
		$('#modal-ubah').modal('show');
	}

	const getAllProv = async () => {
		let source = await _.filter(allKlinik, function(o) { return o.jabodetabek === `1`; });
		let prov = _.uniqBy(source, 'provinsi');
		for (const item of prov) {
			$( "#ubah_alamat_kode_prov" ).append(new Option(item.provinsi, item.provinsi));
		}
		$( "#ubah_alamat_kode_prov" ).trigger('change');
	}

	const setSelectKab = async () => {
		$( "#ubah_alamat_kode_kab" ).html('');
		let prov = $('#ubah_alamat_kode_prov').val();
		let data = await _.filter(allKlinik, function(o) { return o.provinsi === `${prov}`; });
		let group = _.uniqBy(data, 'kabupaten');
		for (const item of group) {
			$( "#ubah_alamat_kode_kab" ).append(new Option(item.kabupaten, item.kabupaten));
		}
		$( "#ubah_alamat_kode_kab" ).trigger('change');
	}

	const setSelectKec = async () => {
		$( "#ubah_alamat_kode_kec" ).html('');
		let prov = $('#ubah_alamat_kode_prov').val();
		let kab = $('#ubah_alamat_kode_kab').val();
		let data = await _.filter(allKlinik, function(o) { return o.provinsi === `${prov}` && o.kabupaten === `${kab}`; });
		let group = _.uniqBy(data, 'kecamatan');
		for (const item of group) {
			$( "#ubah_alamat_kode_kec" ).append(new Option(item.kecamatan, item.kecamatan));
		}
		$( "#ubah_alamat_kode_kec" ).trigger('change');
	}

	const setSelectKel = async () => {
		$( "#ubah_alamat_kode_kel" ).html('');
		let prov = $('#ubah_alamat_kode_prov').val();
		let kab = $('#ubah_alamat_kode_kab').val();
		let kec = $('#ubah_alamat_kode_kec').val();
		let data = await _.filter(allKlinik, function(o) { return o.provinsi === `${prov}` && o.kabupaten === `${kab}` && o.kecamatan === `${kec}`; });
		let group = _.uniqBy(data, 'kelurahan');
		for (const item of group) {
			$( "#ubah_alamat_kode_kel" ).append(new Option(item.kelurahan, item.kelurahan));
		}
		$( "#ubah_alamat_kode_kel" ).trigger('change');
	}

	const setSelectKlinik = async () => {
		$( "#ubah_mst_klinik_id" ).html('');
		let prov = $('#ubah_alamat_kode_prov').val();
		let kab = $('#ubah_alamat_kode_kab').val();
		let kec = $('#ubah_alamat_kode_kec').val();
		let kel = $('#ubah_alamat_kode_kel').val();
		let data = await _.filter(allKlinik, function(o) { return o.provinsi === `${prov}` && o.kabupaten === `${kab}` && o.kecamatan === `${kec}` && o.kelurahan === `${kel}`; });
		for (const item of data) {
			$( "#ubah_mst_klinik_id" ).append(new Option(item.nama_outlet, item.id));
		}
		$( "#ubah_mst_klinik_id" ).trigger('change');
	}

	const renderAllSelect = async () => {
		$( "#ubah_alamat_kode_kab" ).html('');
		$( "#ubah_alamat_kode_kec" ).html('');
		$( "#ubah_alamat_kode_kel" ).html('');
		$( "#ubah_mst_klinik_id" ).html('');
		setSelectKab();
	}

	$("#ubah_alamat_kode_prov").on('change', function(event){
		renderAllSelect();
	})

	$("#ubah_alamat_kode_kab").on('change', function(event){
		setSelectKec();
	})

	$("#ubah_alamat_kode_kec").on('change', function(event){
		setSelectKel();
	})

	$("#ubah_alamat_kode_kel").on('change', function(event){
		setSelectKlinik();
	})

	$("input[name=ubah_status_vaksin]").on('change', function(){
		let vall = this.value;
		// if( vall!=='1' ){
		// 	$("#form-klinik").show();
		// 	$("#ubah_mst_klinik_id").prop('required',true);
		// } else{
		// 	$("#form-klinik").hide();
		// 	$("#ubah_mst_klinik_id").prop('required',false);
		// }

		if( vall=='1' ){ // sudah
			$("#form-sudah").show();
			$("#form-belum").hide();
			$("#ubah_alasan").prop('required', false);
			$("#ubah_tanggal_vaksin_1").prop('required', true);
			$("#ubah_lokasi_vaksin_1").prop('required', true);
		} else if( vall=='2' ){ // belum
			$("#form-sudah").hide();
			$("#form-belum").show();
			$("#ubah_alasan").prop('required', true);
			$("#ubah_tanggal_vaksin_1").prop('required', false);
			$("#ubah_lokasi_vaksin_1").prop('required', false);
		} else{
			$("#form-sudah").hide();
			$("#form-belum").hide();
			$("#ubah_alasan").prop('required', false);
			$("#ubah_tanggal_vaksin_1").prop('required', false);
			$("#ubah_lokasi_vaksin_1").prop('required', false);
		}
	})

	const getAlamatKlinik = async () => {
		let id = $("#ubah_mst_klinik_id").val();
		let data = await _.find(allKlinik, function(o) { return parseInt(o.id) === parseInt(id); });
		if( typeof data !== undefined )
			$("#alamat_klinik").text(`${data.alamat}, ${data.kelurahan}, ${data.kecamatan}, ${data.kabupaten}, ${data.provinsi}, kode pos ${data.kode_pos}`);
	}

	const getAllKlinik = async () => {
		await $.ajax({
			type: "POST",
			url: `<?= base_url('vaksinasi/get_data/get_all_klinik')?>`,
			data: {},
			dataType: 'json',
		}).then(function(response){
			allKlinik = response.data;
			getAllProv();
		}).catch(function(xhr, status, error){
			console.log(xhr.responseText);
		})
	}

	$('#modal-ubah').on('hidden.bs.modal', function () {
		$("#formulir-ubah")[0].reset();
		// $( "#ubah_alamat_kode_prov" ).trigger('change');
		// $("#form-klinik").hide();
		// $("#ubah_mst_klinik_id").prop('required',false);
		$("#form-sudah").hide();
		$("#form-belum").hide();
		$("#ubah_alasan").prop('required', false);
		$("#ubah_tanggal_vaksin_1").prop('required', false);
		$("#ubah_lokasi_vaksin_1").prop('required', false);
	})

	const exportExcel = () => {
		let url = '<?= base_url('vaksinasi/data_vaksin_keluarga/export')?>';
		let jenis = $('#filter-jenis').val();
		window.open(`${url}?jenis=${jenis}`, '_blank');
	}

	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}
</script>

