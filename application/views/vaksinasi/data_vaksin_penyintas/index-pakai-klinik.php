<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
		</div>

		<div class="alert alert-info">
			Batas submit tanggal <strong>9 Juli 2021 Pukul 17:00 WIB</strong><br>
			Tanggal PCR Negatif <strong>harus lebih dari 3 bulan</strong> dari waktu submit untuk melakukan pendaftaran vaksinasi
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
				<label>Pilih Karyawan</label>
				<select class="form-control" id="filter-karyawan" style="width: 30%;" onchange="showHideInput()">
					<option value="-">Pilih Karyawan</option>
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
		<div id="alert-formulir-ubah"></div>
		<br>
		
		<?php
			if($this->akses["lihat"]){
		?>
		<div class="row" id="div-input">
			<div class="col-lg-12">
				<?php $this->load->view('vaksinasi/data_vaksin_penyintas/input')?>
			</div>
		</div>
		<?php
			}
		?>

	</div>
</div>

<script src="<?= base_url('asset/jquery-validate/1.19.2/jquery.validate.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js')?>"></script>
<script type="text/javascript">
	var allData;
	var allKlinik;
	var sessionGroup = '<?= $_SESSION['grup']?>';
	var sessionNp = '<?= $_SESSION['no_pokok']?>';
	var listPengadministrasi = <?= json_encode($_SESSION['list_pengadministrasi'])?>;
	$(document).ready(function() {
		$('#div-input').hide();
		getAllKlinik();
		getAllData();

		$('#formulir-ubah').validate({
            rules: {
                ubah_tanggal_pcr_negatif: {
                    required: true
                }, 
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
                save();
				console.log('OK')
            }
        });
	});

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

	const getAllData = async () => {
		let mapped = await _.map(listPengadministrasi, 'kode_unit');
		await $.ajax({
			type: "POST",
			url: `<?= base_url('vaksinasi/get_data/data_penyintas')?>`,
			data: { np: sessionNp, group: sessionGroup, listPengadministrasi: mapped },
			dataType: 'json',
		}).then(function(response){
			allData = response;
			setFilterKaryawan();
		}).catch(function(xhr, status, error){
			console.log(xhr.responseText);
		})
	}

	const doUbah = () => {
		if( $('input[name=ubah_status_vaksin]:checked').val()!==undefined ){
			var r = confirm("Perhatian!\nAnda hanya bisa melakukan submit sebelum masa cut off pada 10 Juli 2021. Pastikan isian Anda sudah benar.\nLanjutkan?");
			if (r == true) {
				$('#formulir-ubah').submit();
			}
		} else
			alert('Status Vaksin harus diisi');
	}

	const save = async () => {
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
		await $.ajax({
            type: "POST",
            url: `<?= base_url('vaksinasi/data_vaksin_penyintas/save')?>`,
            data: allData,
            dataType: 'json',
        }).then(function(response){
            $('#btn-submit-ubah').prop('disabled',false);
            $('#alert-formulir-ubah').html(response.message);
			if(response.status===true){
				// console.log(response.data);
				changeData(response.data);
				setTimeout(function(){ $('#alert-formulir-ubah').html(''); }, 5000);
				$("#btn-close").trigger('click');
			}
        }).catch(function(xhr, status, error){
            $('#btn-submit-ubah').prop('disabled',false);
            $('#alert-formulir-ubah').html(xhr.responseText);
        })
	}

	const input = async () => {
		$("#formulir-ubah")[0].reset();
		$("#ubah_alamat_kode_prov").trigger('change');
		$("#form-klinik").hide();
		$("#ubah_mst_klinik_id").prop('required',false);
		let np = $('#filter-karyawan').val();
		let data = await _.find(allData.data, function(o) { return o.np_karyawan === `${np}`; });
		let fields = ['np_karyawan','nama_karyawan','tipe_keluarga','nama_lengkap','usia','tanggal_lahir','nik','email','no_hp','alamat','status_kawin','tempat_lahir_keluarga','tanggal_pcr_negatif'];
		$.each(data, function(key, value){
			if(fields.includes(key)){
				$(`#ubah_${key}`).val(value);
			}
		});
		let usia = data.usia!==null ? data.usia : ( parseInt(2021) - parseInt(moment(data.tanggal_lahir).format('YYYY')));
		$(`#ubah_usia`).val(usia);

		// status vaksin
		if(data.status_vaksin!==null){
			if(data.status_vaksin==='1')
				$(`#status_vaksin_value_1`).trigger('click');
			if(data.status_vaksin==='2'){
				$(`#status_vaksin_value_2`).trigger('click');
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
			}
		}
		// $('#modal-ubah').modal('show');
	}

	const setFilterKaryawan = async () =>{
		let data = allData.data;
		for (const item of data) {
			$('#filter-karyawan').append(new Option(item.nama_karyawan, item.np_karyawan));
		}
		if( sessionGroup==='5' )
			$('#filter-karyawan').val(sessionNp);
		$('#filter-karyawan').trigger('change');
	}

	const showHideInput = async () =>{
		let np = $('#filter-karyawan').val();
		let tanggalPcrNegatif = $('#ubah_tanggal_pcr_negatif').val();
		let add3Month = moment(tanggalPcrNegatif).add(3, 'M');
		
		if( np !=='-'){
			$('#div-input').show();
			await input();
			checkDate();
		}
		else
			$('#div-input').hide();
	}

	const checkDate = ()=>{
		let tanggalPcrNegatif = $('#ubah_tanggal_pcr_negatif').val();
		let add3Month = moment(tanggalPcrNegatif).add(3, 'M');
		let now = moment(Date.now()).format('YYYY-MM-DD');
		if( tanggalPcrNegatif!=='' ){
			if( moment(add3Month).format('YYYY-MM-DD') > now ){
				alert('Mohon maaf Anda belum layak vaksin');
				$('#btn-submit-ubah').hide();
			} else
				$('#btn-submit-ubah').show();
		} else
			$('#btn-submit-ubah').hide();
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
		if( vall!=='1' ){
			$("#form-klinik").show();
			$("#ubah_mst_klinik_id").prop('required',true);
		} else{
			$("#form-klinik").hide();
			$("#ubah_mst_klinik_id").prop('required',false);
		}
	})

	const getAlamatKlinik = async () => {
		let id = $("#ubah_mst_klinik_id").val();
		let data = await _.find(allKlinik, function(o) { return parseInt(o.id) === parseInt(id); });
		// console.log(data);
		if( typeof data !== undefined )
			$("#alamat_klinik").text(`${data.alamat}, ${data.kelurahan}, ${data.kecamatan}, ${data.kabupaten}, ${data.provinsi}, kode pos ${data.kode_pos}`);
	}

	$('#btn-close').on('click', function () {
		$("#formulir-ubah")[0].reset();
		$("#ubah_alamat_kode_prov").trigger('change');
		$("#form-klinik").hide();
		$("#ubah_mst_klinik_id").prop('required',false);
		$('#div-input').hide();
		$('#filter-karyawan').val('-');
		$('#filter-karyawan').trigger('change');
	})

	const changeData = async (data) =>{
		let np = $('#filter-karyawan').val();
		await _.dropWhile(allData.data, function(o) { return o.np_karyawan===`${np}`; });
		let i = await _.findIndex(allData.data, function(o) { return o.np_karyawan == `${np}`; });
		allData.data.splice(i,1);
		allData.data.push(data);
	}
</script>

