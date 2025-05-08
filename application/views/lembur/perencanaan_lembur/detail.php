<link href="<?= base_url() ?>asset/select2/select2.min.css" rel="stylesheet" />
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
		
		if (@$akses["lihat"]) { ?>
			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
						Detail Perencanaan Lembur
					</div>
                    <div class="panel-body">
                        <div class="col-12">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>Unit Kerja</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <span id="kode_unit"><?= @$sto->object_name ?: ''?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>NDE (file pdf)</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-control" name="evidence" id="" style="width: 100%;" accept="application/pdf" required>
                                    </div>
                                </div>
                            </div> -->

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>Periode</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <span id="dates"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <?php if(@$akses['ubah']):?>
                                        <!-- <button class="btn btn-primary mt-2" type="button">Edit Perencanaan</button> -->
                                        <button class="btn btn-success mt-2 ml-2" type="button" id="btn-modal-import">Tambahkan Data melalui Excel</button>
                                        <?php endif?>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="div-list row">
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <table class="table table-bordered table-responsive" id="list-table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <td style="width: 10%;">Tanggal</td>
                                                    <td style="width: 15%;">NP</td>
                                                    <td style="width: 10%;">Jumlah Karyawan</td>
                                                    <td style="width: 10%;">Jam Lembur (per karyawan)</td>
                                                    <td style="width: 10%;">Jenis Hari</td>
                                                    <td style="width: 15%;">Jenis Lembur</td>
                                                    <td style="width: 15%;">Alasan Lembur</td>
                                                    <td style="width: 15%;">Aksi</td>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <a class="btn btn-default mt-2" href="<?= base_url('lembur/perencanaan_lembur')?>">Kembali</a>
                            <?php if(@$akses['ubah']):?>
                            <!-- <a class="btn btn-primary mt-2" href="<?= base_url('lembur/perencanaan_lembur/edit/' . $perencanaan->uuid)?>">Edit</a> -->
                            <?php endif?>
                        </div>
                    </div>
				</div>
			</div>
		<?php } ?>

        <?php if(@$akses['ubah']):?>
        <!-- modal import -->
        <div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="label-modal-import" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="<?= base_url('lembur/perencanaan_lembur/simpan_excel')?>" method="post" enctype="multipart/form-data" onsubmit="return false;" id="form-import">
                        <div class="modal-header">
                            <h4>Import Perencanaan Lembur</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row mt-2">
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>Pilih File Excel</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="file" name="excel_file" class="form-control" style="width: 100%;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                        <span>
                                            Silakan <a href="<?= base_url('asset/Template_import_perencanaan_lembur.xlsx')?>" download="Template_import_perencanaan_lembur-<?= date('YmdHis')?>.xlsx" target="_blank">Download file template</a>.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-default">Tutup</button>
                            <button type="submit" class="btn btn-primary btn-simpan-import">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- modal edit -->
        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="label-modal-edit" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="<?= base_url('lembur/perencanaan_lembur/update_detail')?>" method="post" enctype="multipart/form-data" onsubmit="return false;" id="form-edit">
                        <div class="modal-header">
                            <h4>Ubah Data Lembur</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row mt-2">
                                <input type="hidden" name="id" required>
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>NP</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" name="list_np" class="form-control" style="width: 100%;" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>Jam Lembur</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="number" name="jam_lembur" class="form-control" style="width: 100%;" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>Jenis Hari</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="jenis_hari" class="form-control" style="width: 100%;" required>
                                            <option value="kerja">Kerja</option>
                                            <option value="libur">Libur</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>Jenis Lembur</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="mst_kategori_lembur_id" class="form-control" style="width: 100%;" required>
                                            <?php foreach($kategori_lembur as $row):?>
                                            <option value="<?= $row->id?>"><?= $row->kategori_lembur?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-3">
                                        <label>Alasan Lembur</label>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea name="alasan_lembur" class="form-control" style="width: 100%;" rows="1" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-default">Tutup</button>
                            <button type="submit" class="btn btn-primary btn-simpan-edit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif?>
	</div>
</div>

<script src="<?= base_url() ?>asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= base_url() ?>asset/select2/select2.min.js"></script>
<script src="<?= base_url()?>asset/js/uuidv4.js"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js')?>"></script>
<script>
    var list_table;
    var mst_karyawan = <?= json_encode($mst_karyawan)?>;
    var perencanaan = <?= json_encode($perencanaan)?>;

    $(()=>{
        init_dates();
        load_table();
    });

    const init_dates = ()=> {
        if(perencanaan.tanggal_mulai==null || perencanaan.tanggal_selesai==null) $('#dates').html('');
        else $('#dates').html(moment(perencanaan.tanggal_mulai).format('DD MMM YYYY') + ' - ' + moment(perencanaan.tanggal_selesai).format('DD MMM YYYY'));
        return;
    };

    const load_table = ()=>{
        list_table = $('#list-table').DataTable({
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
				"url": "<?= base_url("lembur/perencanaan_lembur/get_data_karyawan") ?>",
				"type": "POST",
                "data": function(d){
                    d['id'] = perencanaan.id
                }
			},
            columns: [
                {
                    data: 'tanggal',
                    render: (data, type, row) => {
						if(data==null) return '';
						else return moment(data).format('DD MMM YYYY');
					}
                }, {
                    data: 'list_np',
					render: (data, type, row) => {
                        if([null,''].includes(data)===false){
                            let np = data.split(',');
                            let nama = mst_karyawan.filter(o=>{
                                return np.includes(o.no_pokok);
                            }).map(o=>{
                                return `${o.no_pokok} - ${o.nama}`;
                            });
                            return nama.join(';<br>');
                        } else return '';
					}
                }, {
                    data: 'jumlah_karyawan',
                }, {
                    data: 'jam_lembur',
                }, {
                    data: 'jenis_hari',
                }, {
                    data: 'jenis_lembur',
                }, {
                    data: 'alasan_lembur',
                }, {
                    data: 'id',
                    render: (id, type, row) => {
                        <?php if (@$akses["ubah"]){?>
                        const button_edit = $('<button>', {
                            html: 'Edit',
                            class: 'btn btn-sm btn-primary btn-edit',
                            type: 'button',
                            'data-id': JSON.stringify(row),
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

                        return $('<div>', {
                            class: 'btn-group',
                            html: () => {
                                let arr = [];
                                <?php if (@$akses["ubah"]){?>
                                arr.push(button_edit);
								<?php } if (@$akses["hapus"]){?>
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

    <?php if(@$akses["hapus"]):?>
	$('#list-table').on('click', '.btn-delete', function(e){
		Swal.fire({
			title: 'Hapus Lembur?',
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
					url: '<?= base_url('lembur/perencanaan_lembur/hapus_detail')?>',
					type: 'POST',
					data: data,
					dataType: 'json',
					processData: false,
					contentType: false,
					beforeSend: () => {
						$('#list-table').LoadingOverlay('show');
					},
				}).then((res) => {
					$('#list-table').LoadingOverlay('hide', true);
					list_table.draw();
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

    <?php if(@$akses["ubah"]):?>
    $('#btn-modal-import').on('click', (e)=>{
        $('#modal-import').modal('show');
    });

    $('#form-import').on('submit', function(e){
        let data = new FormData(this);
        data.append('kode_unit', perencanaan.kode_unit);
        data.append('tanggal_mulai', perencanaan.tanggal_mulai);
        data.append('tanggal_selesai', perencanaan.tanggal_selesai);
        data.append('uuid', perencanaan.uuid);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: () => {
                $('#form-import').LoadingOverlay('show');
            },
        }).then((res) => {
            $('#form-import').LoadingOverlay('hide', true);
            if(res.status==true) {
                Swal.fire({
                    title: '',
                    text: res.message,
                    icon: 'success',
                    allowOutsideClick: false,
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                }).then(()=>{
                    location.reload();
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
    });

    // ubah
    $('#list-table').on('click', '.btn-edit', function(e){
        let row = JSON.parse(e.target.dataset.id)
        let form = $('#form-edit');
        form.find('[name="id"]').val(row.id);
        form.find('[name="list_np"]').val(row.list_np);
        form.find('[name="jam_lembur"]').val(row.jam_lembur);
        form.find('[name="jenis_hari"]').val(row.jenis_hari).trigger('change');
        form.find('[name="mst_kategori_lembur_id"]').val(row.mst_kategori_lembur_id).trigger('change');
        form.find('[name="alasan_lembur"]').val(row.alasan_lembur);
        $('#modal-edit').modal('show');
	});

    $('#form-edit').on('submit', function(e){
        let data = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: () => {
                $('#form-edit').LoadingOverlay('show');
            },
        }).then((res) => {
            $('#form-edit').LoadingOverlay('hide', true);
            list_table.draw();
            if(res.status==true) {
                Swal.fire({
                    title: '',
                    text: res.message,
                    icon: 'success',
                    allowOutsideClick: false,
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                }).then(()=>{
                    $('#modal-edit').modal('hide');
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
    });
    <?php endif?>
</script>