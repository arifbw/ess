<link rel="stylesheet" type="text/css" href="<?= base_url('asset/select2/select2.min.css') ?>" />
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul; ?></h1>
			</div>
		</div>
		
        <?php if (@$this->session->flashdata('success')) { ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('success'); ?>
			</div>
		<?php } else if (@$this->session->flashdata('warning')) { ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('warning'); ?>
			</div>
		<?php }
		if ($akses["lihat log"]) { ?>
			<div class='row text-right'>
				<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
				<br><br>
			</div>
		<?php }
		if ($akses["tambah"]) { ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a href="<?= base_url('lembur/perencanaan_lembur/input_perencanaan_lembur') ?>">Tambah <?php echo $judul; ?></a>
							</h4>
						</div>
					</div>
				</div>
			</div>
		<?php }
		if ($this->akses["lihat"]) { ?>
			<div class="row">
				<!-- filter -->
				<div class="col-lg-6">
					<div class="form-group">
						<label for="filter-periode">Periode</label>
						<select placeholder="Filter Periode" id="filter-periode" class="form-control" style="width: 100%;">
							<option value="">-- Semua Periode --</option>
							<?php foreach($filter_periode as $row):?>
							<option value="<?= "{$row->tanggal_mulai}|{$row->tanggal_selesai}"?>"><?= tanggal_indonesia($row->tanggal_mulai) .' - '. tanggal_indonesia($row->tanggal_selesai) ?></option>
							<?php endforeach?>
						</select>
					</div>
				</div>
			</div>
			
			<div class="row mt-2">
				<div class="col-lg-12">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_perencanaan_lembur">
						<thead>
							<tr>
								<th class='text-center' style="width: 5%;">No</th>
								<th class='text-center' style="width: 35%;">Unit Kerja</th>
								<th class='text-center' style="width: 25%;">Periode</th>
								<th class='text-center' style="width: 15%;">NDE</th>
								<th class='text-center' style="width: 20%;">Aksi</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<script src="<?= base_url() ?>asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js')?>"></script>
<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script>
    var lembur_table;
    $(()=>{
		$("#filter-periode").select2().trigger('change');
    });

	$("#filter-periode").on('change', (e)=>{
		load_table();
	});

    const load_table = ()=>{
		if(typeof lembur_table!='undefined') lembur_table.draw();
		else {
			lembur_table = $('#tabel_ess_perencanaan_lembur').DataTable({
				"iDisplayLength": 10,
				"language": {
					"url": "<?= base_url('asset/datatables/Indonesian.json'); ?>",
					"sEmptyTable": "Tidak ada data di database",
					"processing": "Sedang memuat data pengajuan lembur",
					"emptyTable": "Tidak ada data di database"
				},
				"stateSave": true,
				"responsive": true,
				"processing": true,
				"serverSide": true,
				"ordering": false,
	
				// Load data for the table's content from an Ajax source
				"ajax": {
					"url": "<?= base_url("lembur/perencanaan_lembur/get_data/") ?>",
					"data": function(e){
						e.periode = $("#filter-periode").val();
					},
					"type": "POST"
				},
				columns: [
					{
						data: 'no',
					}, {
						data: 'object_name',
					}, {
						data: 'id',
						render: (id, type, row) => {
							if(row.tanggal_mulai==null || row.tanggal_selesai==null) return '';
							else return moment(row.tanggal_mulai).format('DD MMM YYYY') + ' - ' + moment(row.tanggal_selesai).format('DD MMM YYYY');
						}
					}, {
						data: 'button_file',
					}, {
						data: 'id',
						render: (id, type, row) => {
							const button_detail = $('<a>', {
								html: 'Detail',
								class: 'btn btn-sm btn-default',
								href: '<?= base_url()?>' + `lembur/perencanaan_lembur/detail/${row.uuid}`,
								'data-id': id,
								'data-toggle': 'tooltip',
								'data-placement': 'top',
								title: 'Detail'
							});
	
							<?php if (@$akses["ubah"]){?>
							const button_edit = $('<a>', {
								html: 'Edit',
								class: 'btn btn-sm btn-primary',
								href: '<?= base_url()?>' + `lembur/perencanaan_lembur/edit/${row.uuid}`,
								'data-id': id,
								'data-toggle': 'tooltip',
								'data-placement': 'top',
								title: 'Edit'
							});
							<?php }
							if (@$akses["hapus"]){?>
	
							const button_delete = $('<button>', {
								html: 'Hapus',
								class: 'btn btn-sm btn-danger btn-delete',
								type: `button`,
								'data-id': id,
								'data-toggle': 'tooltip',
								'data-placement': 'top',
								title: 'Hapus'
							});
							<?php }?>

							const button_karyawan = $('<a>', {
								html: 'Daftar Karyawan',
								class: 'btn btn-sm btn-success',
								href: '<?= base_url()?>' + `lembur/perencanaan_lembur/download_excel_daftar_karyawan/${row.uuid}`,
								target: '_blank',
								'data-id': id,
								'data-toggle': 'tooltip',
								'data-placement': 'top',
								title: 'Daftar Karyawan'
							});
	
							return $('<div>', {
								class: 'btn-group',
								html: () => {
									let arr = [];
									arr.push(button_detail);
									
									<?php if (@$akses["ubah"]){?>
									// arr.push(button_edit);
									<?php }
									if (@$akses["hapus"]){?>
									arr.push(button_delete);
									<?php }?>

									arr.push(button_karyawan);
									
									return arr;
								}
							}).prop('outerHTML');
						}
					}
				],
				drawCallback: function() {
					
				}
			});
		}
    }

	<?php if(@$akses["hapus"]):?>
	$('#tabel_ess_perencanaan_lembur').on('click', '.btn-delete', function(e){
		Swal.fire({
			title: 'Hapus perencanaan akan menghapus karyawan yang ada di dalamnya',
			icon: 'warning',
			allowOutsideClick: false,
			reverseButtons: true,
			showCancelButton: true,
			confirmButtonText: 'Hapus',
			cancelButtonText: 'Tidak'
		}).then((result) => {
			if (result.isConfirmed) {
				let data = new FormData();
				data.append('id', e.target.dataset.id);
				$.ajax({
					url: '<?= base_url('lembur/perencanaan_lembur/hapus')?>',
					type: 'POST',
					data: data,
					dataType: 'json',
					processData: false,
					contentType: false,
					beforeSend: () => {
						$('#tabel_ess_perencanaan_lembur').LoadingOverlay('show');
					},
				}).then((res) => {
					$('#tabel_ess_perencanaan_lembur').LoadingOverlay('hide', true);
					lembur_table.draw();
					if(res.status==true) {
						Swal.fire({
							title: '',
							text: res.message,
							icon: 'success',
							allowOutsideClick: false,
							showCancelButton: false,
							confirmButtonText: 'OK'
						}).then(()=>{
							
						});
					} else {
						Swal.fire({
							title: '',
							text: res.message,
							icon: 'error',
							allowOutsideClick: false,
							showCancelButton: false,
							confirmButtonText: 'OK'
						}).then(()=>{
							
						});
					}
				});
			}
		})
	});
	<?php endif?>
</script>