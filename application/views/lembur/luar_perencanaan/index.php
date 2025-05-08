<link href="<?= base_url() ?>asset/daterangepicker-master/daterangepicker3.css" rel="stylesheet" />
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
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul; ?></a>
						</h4>
					</div>

                    <div id="collapseOne" class="panel-collapse collapse">
						<div class="panel-body">
                            <div class="col-12">
                                <form role="form" action="<?= base_url('lembur/luar_perencanaan/import_excel') ?>" id="form-tambah" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="alert alert-info">
                                                Silakan <a href="<?= base_url('asset/Template_import_lembur_diluar_perencanaan.xlsx')?>" download="Template_import_lembur_diluar_perencanaan-<?= date('YmdHis')?>.xlsx" target="_blank">Download file template</a>, kemudian upload pada form di bawah ini.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-2 col-md-4 col-sm-12">
                                            <label for="input-file">Upload File Excel</label>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <input type="file" class="form-control" name="file_lembur" id="input-file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary mt-2" type="submit">Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		<?php }
		if ($this->akses["lihat"]) { ?>

			<div class="row">
				<!-- info -->
				<div class="col-lg-3 col-md-6 col-sm-12">
					<div class="form-group">
						<label>Rentang Tanggal</label>
						<input type="text" class="form-control" id="dates">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-3 col-md-6 col-sm-12">
					<div class="form-group">
						<button id="btnExcel" class="btn btn-success mb-2">Export Excel</button>
					</div>
				</div>
			</div>
			
			<div class="row mt-2">
				<div class="col-lg-12">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_lembur">
						<thead>
							<tr>
								<th class='text-center' style="width: 10%;">NP</th>
								<th class='text-center' style="width: 15%;">Nama</th>
								<th class='text-center' style="width: 15%;">Unit Kerja</th>
								<th class='text-center' style="width: 10%;">Tanggal</th>
								<th class='text-center' style="width: 10%;">Jumlah Jam Lembur</th>
								<th class='text-center' style="width: 15%;">Jenis Lembur</th>
								<th class='text-center' style="width: 15%;">Premi Lembur</th>
								<th class='text-center' style="width: 10%;">Aksi</th>
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
<script src="<?= base_url('asset/daterangepicker-master/daterangepicker3.js')?>"></script>
<script>
	var BASE_URL = '<?= base_url()?>';
    var lembur_table;
	var startDate = moment().startOf("month").format("YYYY-MM-DD");
    var endDate = moment().endOf("month").format("YYYY-MM-DD");

    $(()=>{
		init_dates();
    });

	function init_dates() {
        $("#dates").daterangepicker(
            {
                startDate: moment().startOf("month"),
                endDate: moment().endOf("month"),
            },
            function (start, end) {
                startDate = start.format("YYYY-MM-DD");
                endDate = end.format("YYYY-MM-DD");
            }
        );
    };

	$("#dates").on("change", (e) => {
        load_table();
        return;
    });

    const load_table = ()=>{
		if(typeof lembur_table!='undefined') lembur_table.draw();
		else{
			lembur_table = $('#tabel_lembur').DataTable({
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
					"url": "<?= base_url("lembur/luar_perencanaan/get_data/") ?>",
					"type": "POST",
					"data": function(d){
						d['start_date'] = startDate;
						d['end_date'] = endDate;
					}
				},
				columns: [
					{
						data: 'np',
					}, {
						data: 'nama',
					}, {
						data: 'nama_unit',
					}, {
						data: 'tanggal',
						render: (data, type, row) => {
							if(data==null) return '';
							else return moment(data).format('DD MMM YYYY');
						}
					}, {
						data: 'jumlah_jam_lembur',
					}, {
						data: 'jenis_lembur',
					}, {
						data: 'premi_lembur',
						render: (data, type, row) => {
							if(data!=null){
								return new Intl.NumberFormat("id-ID", {
									style: "currency",
									currency: "IDR",
									minimumFractionDigits: 0,
									maximumFractionDigits: 0
								}).format(data);
							} else{
								return '';
							}
						}
					}, {
						data: 'id',
						render: (id, type, row) => {
							<?php if (@$akses["hapus"]){?>
							const button_delete = $('<button>', {
								html: 'Hapus',
								class: 'btn btn-danger btn-delete',
								type: `button`,
								'data-id': id,
								'data-toggle': 'tooltip',
								'data-placement': 'top',
								title: 'Hapus'
							});
							<?php }?>

							return $('<div>', {
								class: 'btn-group',
								html: () => {
									let arr = [];
									<?php if (@$akses["hapus"]){?>
									arr.push(button_delete);
									<?php }?>
									
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

	$('#btnExcel').on('click', () => {
		window.open(`${BASE_URL}lembur/luar_perencanaan/export_excel/${startDate}/${endDate}`);
	});

	<?php if(@$akses["hapus"]):?>
	$('#tabel_lembur').on('click', '.btn-delete', function(e){
		Swal.fire({
			title: 'Hapus lembur?',
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
					url: '<?= base_url('lembur/luar_perencanaan/hapus')?>',
					type: 'POST',
					data: data,
					dataType: 'json',
					processData: false,
					contentType: false,
					beforeSend: () => {
						$('#tabel_lembur').LoadingOverlay('show');
					},
				}).then((res) => {
					$('#tabel_lembur').LoadingOverlay('hide', true);
					if(res.status==true) {
						lembur_table.draw();
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
						lembur_table.draw();
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

<?php if ($akses["tambah"]) { ?>
<script src="<?= base_url('asset/js/lembur/import_diluar_perencanaan.js?q='.random_string()) ?>"></script>
<?php }?>