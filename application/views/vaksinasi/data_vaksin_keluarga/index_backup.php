<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
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
			<div class="col-lg-12 text-right">
				<?php
				if( @$akses["export"] ){
					// echo '<button class="btn btn-success btn-md" onclick="">Export</button>&nbsp;';
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

<link rel="stylesheet" href="<?= base_url('asset/select2/4.0.13/select2.min.css')?>" />
<script src="<?= base_url('asset/jquery-validate/1.19.2/jquery.validate.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js')?>"></script>
<script src="<?= base_url('asset/select2/4.0.13/select2.min.js')?>"></script>
<script type="text/javascript">
	var table;
	var allProv, allKlinik;
	var allWilayah = [];
	$(document).ready(function() {
		tableServerside();
		getAllProv();
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
                ubah_email: {
                    required: true
                },
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
		table = $('#tabel_vaksin').DataTable({ 
			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database"
			},
			"destroy": true,
			"stateSave": true,
			"processing": true,
			"serverSide": true,
			"ordering": false,
			"ajax": {				
				"url"	: "<?php echo site_url("vaksinasi/data_vaksin_keluarga/tabel_data")?>",					 
				"type"	: "POST"
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
						if( 2021 - parseInt(moment(row.tanggal_lahir).format('YYYY')) > 17 ){
							if(row.dibatalkan_admin==='1'){
								const status = $('<span/>', {
									html: 'Dibatalkan Admin',
									class: 'label label-danger'
								})
								label += status.prop('outerHTML');
							} else{
								if(row.created_at!=null){
									if(row.status_vaksin==='1'){
										const status = $('<span/>', {
											html: 'Sudah',
											class: 'label label-success'
										})
										label += status.prop('outerHTML');
									} else{
										const status = $('<span/>', {
											html: 'Belum',
											class: 'label label-warning'
										})
										label += status.prop('outerHTML');
									}
								} else{
									const status = $('<span/>', {
										html: 'Belum Input',
										class: 'label label-default'
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
						if(jQuery.inArray(sessionGroup, ['4','5']) !== -1){
							if( row.created_at===null && (2021 - parseInt(moment(row.tanggal_lahir).format('YYYY'))) > 17 ){
								const input = $('<button/>', {
									html: 'Input',
									class: 'btn btn-primary btn-sm',
									onclick: `input(${JSON.stringify(row)})`,
								})
								btn += input.prop('outerHTML');
							} else{
								btn += '-';
							}
						} else if(jQuery.inArray(sessionGroup, ['10']) !== -1){

						}

						return btn;
					}
				}
			],
		});
	};

	const doUbah = () => {
		var r = confirm("Perhatian!\nAnda hanya bisa submit satu kali. Pastikan isian Anda sudah benar.\nLanjutkan?");
  		if (r == true) {
			$('#formulir-ubah').submit();
  		}
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
				$('#btn-submit-ubah').hide();
			}
        }).catch(function(xhr, status, error){
            $('#btn-submit-ubah').prop('disabled',false);
            $('#alert-formulir-ubah').html(xhr.responseText);
        })
	}

	const input = (data) => {
		let fields = ['np_karyawan','nama_karyawan','tipe_keluarga','nama_lengkap','usia','tanggal_lahir'];
		$.each(data, function(key, value){
			if(fields.includes(key)){
				$(`#ubah_${key}`).val(value);
			}
		});
		let usia = data.usia!==null ? data.usia : ( parseInt(2021) - parseInt(moment(data.tanggal_lahir).format('YYYY')));
		$(`#ubah_usia`).val(usia);
		$('#modal-ubah').modal('show');
	}

	const getAllProv = async () => {
		await $.ajax({
			type: "POST",
			url: `<?= base_url('vaksinasi/data_vaksin_keluarga/get_all_prov')?>`,
			data: {},
			dataType: 'json',
		}).then(function(response){
			allProv = response.data;
			for (const item of allProv) {
				$( "#ubah_alamat_kode_prov" ).append(new Option(item.nama_prov, item.kode_wilayah_prov));
			}
			$( "#ubah_alamat_kode_prov" ).trigger('change');
			$('#ubah_alamat_nama_prov').val($('#ubah_alamat_kode_prov').find(':selected').text());
		}).catch(function(xhr, status, error){
			console.log(xhr.responseText);
		})
	}

	const getWilayahByProv = async () => {
		let kode = $('#ubah_alamat_kode_prov').val();
		let nama = $('#ubah_alamat_kode_prov').find(':selected').text();
		let cari = await _.find(allWilayah, function(o) { return o.params.kode_prov === `${kode}`; });
		if( typeof cari === 'undefined' ){
			await $.ajax({
				type: "POST",
				url: `<?= base_url('vaksinasi/data_vaksin_keluarga/get_all_wilayah')?>`,
				data: {kode: kode, nama: nama},
				dataType: 'json',
			}).then(function(response){
				allWilayah.push(response);
			}).catch(function(xhr, status, error){
				console.log(xhr.responseText);
			})
		} else
			console.log(`Data ${nama} Exist`);
	}

	const setSelectKab = async () => {
		$( "#ubah_alamat_kode_kab" ).html('');
		let kode_prov = $('#ubah_alamat_kode_prov').val();
		let data = await _.find(allWilayah, function(o) { return o.params.kode_prov === `${kode_prov}`; }).data;
		let group = _.uniqBy(data, 'kode_wilayah_kab');
		for (const item of group) {
			$( "#ubah_alamat_kode_kab" ).append(new Option(item.nama_kab, item.kode_wilayah_kab));
		}
		$( "#ubah_alamat_kode_kab" ).trigger('change');
		$('#ubah_alamat_nama_kab').val($('#ubah_alamat_kode_kab').find(':selected').text());
	}

	const setSelectKec = async () => {
		$( "#ubah_alamat_kode_kec" ).html('');
		let kode_prov = $('#ubah_alamat_kode_prov').val();
		let kode_kab = $('#ubah_alamat_kode_kab').val();
		let data = await _.find(allWilayah, function(o) { return o.params.kode_prov === `${kode_prov}`; }).data;
		let filter = await _.filter(data, function(o) { return o.kode_wilayah_kab === `${kode_kab}`; });
		let group = _.uniqBy(filter, 'kode_wilayah_kec');
		for (const item of group) {
			$( "#ubah_alamat_kode_kec" ).append(new Option(item.nama_kec, item.kode_wilayah_kec));
		}
		$( "#ubah_alamat_kode_kec" ).trigger('change');
		$('#ubah_alamat_nama_kec').val($('#ubah_alamat_kode_kec').find(':selected').text());
	}

	const setSelectKel = async () => {
		$( "#ubah_alamat_kode_kel" ).html('');
		let kode_prov = $('#ubah_alamat_kode_prov').val();
		let kode_kab = $('#ubah_alamat_kode_kab').val();
		let kode_kec = $('#ubah_alamat_kode_kec').val();
		let data = await _.find(allWilayah, function(o) { return o.params.kode_prov === `${kode_prov}`; }).data;
		let filterKab = await _.filter(data, function(o) { return o.kode_wilayah_kab === `${kode_kab}`; });
		let filterKec = await _.filter(filterKab, function(o) { return o.kode_wilayah_kec === `${kode_kec}`; });
		let group = _.uniqBy(filterKec, 'kode_wilayah_kel');
		for (const item of group) {
			$( "#ubah_alamat_kode_kel" ).append(new Option(item.nama_kel, item.kode_wilayah_kel));
		}
		$('#ubah_alamat_nama_kel').val($('#ubah_alamat_kode_kel').find(':selected').text());
	}

	const renderAllSelect = async () => {
		$( "#ubah_alamat_kode_kab" ).html('');
		$( "#ubah_alamat_kode_kec" ).html('');
		$( "#ubah_alamat_kode_kel" ).html('');
		await getWilayahByProv();
		await setSelectKab();
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

	$("input[name=ubah_status_vaksin]").on('change', function(){
		let vall = this.value;
		if( vall!=='1' ){
			$("#form-klinik").show();
			$("#ubah_mst_klinik_id").prop('required',true);
		} else{
			$("#form-klinik").hide();
			$("#ubah_mst_klinik_id").prop('required',false);
		}
	})

	const getAlamatKlinik = () => {
		let id = $("#ubah_mst_klinik_id").val();
		let data = _.find(allKlinik, function(o) { return parseInt(o.id) === parseInt(id); });
		$("#alamat_klinik").text(`${data.alamat}, ${data.kelurahan}, ${data.kecamatan}, ${data.kabupaten}, ${data.provinsi}, kode pos ${data.kode_pos}`);
	}

	const getAllKlinik = async () => {
		await $.ajax({
			type: "POST",
			url: `<?= base_url('vaksinasi/data_vaksin_keluarga/get_all_klinik')?>`,
			data: {},
			dataType: 'json',
		}).then(function(response){
			allKlinik = response.data;
			for (const item of _.filter(allKlinik, function(o) { return o.jabodetabek === `1`; })) {
				$( "#ubah_mst_klinik_id" ).append(new Option(item.nama_outlet, item.id));
			}
			
			$( "#ubah_mst_klinik_id" ).trigger('change');
		}).catch(function(xhr, status, error){
			console.log(xhr.responseText);
		})
	}
</script>

