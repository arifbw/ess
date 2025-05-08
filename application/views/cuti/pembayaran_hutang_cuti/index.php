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
		if ($this->akses["lihat"]) { ?>
            <div class="row mt-2">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="form-group">
                        <label for="filter-unit">Unit</label>
                        <select placeholder="Filter Unit" id="filter-unit" class="form-control" style="width: 100%;">
                            <option value="00000">-- Semua Unit --</option>
                            <?php foreach($sto as $row):?>
                            <option value="<?= $row->kode_unit?>"><?= $row->nama_unit?></option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>
            </div>

			<div class="row mt-2">
                <div class="col-lg-12">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_hutang">
                        <thead>
                            <tr>
                                <th class='text-center' style="width: 5%;">No</th>
                                <th class='text-center' style="width: 15%;">Nama</th>
                                <th class='text-center' style="width: 15%;">Unit Kerja</th>
                                <th class='text-center' style="width: 10%;">Hutang Cuti</th>
                                <th class='text-center' style="width: 15%;">Sisa Cuti Besar</th>
                                <th class='text-center' style="width: 20%;">Kuota Cuti Tahunan</th>
                                <th class='text-center' style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
			</div>

            <div class="modal fade" id="modal-histori" tabindex="-1" role="dialog" aria-labelledby="label-modal-histori" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Histori Pembayaran Hutang</h4>
                        </div>
                        <div class="modal-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="tabel_histori">
                                <thead>
                                    <tr>
                                        <th class='text-center' style="width: 20%;">Hutang Awal</th>
                                        <th class='text-center' style="width: 20%;">Pembayaran</th>
                                        <th class='text-center' style="width: 20%;">Bayar Dari</th>
                                        <th class='text-center' style="width: 20%;">Sisa Hutang</th>
                                        <th class='text-center' style="width: 20%;">Tanggal</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-default">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
		<?php } ?>
	</div>

    <?php if ($this->akses["ubah"]) { ?>
    <div class="modal fade" id="modal-bayar" tabindex="-1" role="dialog" aria-labelledby="label-modal-bayar" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Pembayaran Hutang</h4>
                </div>
                <div class="modal-body">
                    <form role="form" action="<?= base_url('cuti/pembayaran_hutang_cuti/simpan_bayar') ?>" id="form-bayar" method="post" onsubmit="return false;">
                        <input type="hidden" name="no_pokok">
                        <div class="form-group">
                            <label for="hutang">Sisa Hutang</label>
                            <input type="number" class="form-control" id="hutang" name="hutang" style="width: 100%;" readonly>
                        </div>
                        <div class="form-group">
                            <label for="pembayaran">Bayar</label>
                            <input type="number" class="form-control" id="pembayaran" name="pembayaran" style="width: 100%;" required>
                        </div>
                        <div class="form-group">
                            <label for="bayar_dari_mst_cuti_id">Bayar dari kuota mana</label>
                            <select class="form-control" id="bayar_dari_mst_cuti_id" name="bayar_dari_mst_cuti_id" required>
                                <option value="">-- Pilih --</option>
                                <option value="1">Cuti Tahunan</option>
                                <option value="2">Cuti Besar</option>
                            </select>
                        </div>
                        <div class="form-group" id="div-confirm-cubes"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Batal</button>
                    <button type="submit" form="form-bayar" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script src="<?= base_url() ?>asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js')?>"></script>
<?php if ($this->akses["ubah"]) { ?>
<script src="<?= base_url('asset/js/cuti/pembayaran_hutang_cuti.js?q='.random_string())?>"></script> 
<?php } ?>
<script>
    var BASE_URL = '<?= base_url()?>';
    var hutang_table;
    $(()=>{
        $("#filter-unit").select2().trigger('change');
    });

    $('#filter-unit').on('change', (e)=>{
        load_table();
    });

    const load_table = ()=>{
        if(typeof hutang_table!='undefined') hutang_table.draw(false);
        else {
            hutang_table = $('#tabel_ess_hutang').DataTable({
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
                // "ordering": false,
                "ajax": {
                    "url": "<?= base_url("cuti/pembayaran_hutang_cuti/get_data/") ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.unit= $('#filter-unit').val();
                    }
                },
                columns: [
                    {
                        data: 'no',
                    }, {
                        data: 'no_pokok',
                        render: (data, type, row) => {
                            return `${row.no_pokok} - ${row.nama}`;
                        }
                    }, {
                        data: 'nama_unit',
                    }, {
                        data: 'hutang',
                    }, {
                        data: 'no_pokok',
                        render: (data, type, row) => {
                            return `${row.sisa_bulan} bulan, ${row.sisa_hari} hari`;
                        }
                    }, {
                        data: 'absence_quota',
                        render: (data, type, row) => {
                            let absence_quota = [];
                            for (const i of data) {
                                absence_quota.push(moment(i.start_date).format('YYYY') + ` : `+ (parseInt(i.number) - parseInt(i.deduction)) + ` (masa aktif cuti `+ moment(i.deduction_from).format('DD MMM YYYY') + ` s/d `+ moment(i.deduction_to).format('DD MMM YYYY') +`)`);
                            }
                            return absence_quota.join('<br><br>');
                        }
                    }, {
                        data: 'no_pokok',
                        render: (data, type, row) => {
                            const button_detail = $('<button>', {
                                html: 'History',
                                class: 'btn btn-default btn-histori',
                                type: 'button',
                                'data-np': data,
                                'data-toggle': 'tooltip',
                                'data-placement': 'top',
                                title: 'History'
                            });
    
                            <?php if (@$akses["ubah"]){?>
                            const button_edit = $('<button>', {
                                html: 'Pembayaran',
                                class: 'btn btn-primary btn-update',
                                type: 'button',
                                'data-np': data,
                                'data-toggle': 'tooltip',
                                'data-placement': 'top',
                                title: 'Pembayaran'
                            });
                            <?php } ?>
    
                            return $('<div>', {
                                class: 'btn-group',
                                html: () => {
                                    let arr = [];
                                    arr.push(button_detail);
                                    
                                    <?php if (@$akses["ubah"]){?>
                                    arr.push(button_edit);
                                    <?php } ?>
                                    
                                    return arr;
                                }
                            }).prop('outerHTML');
                        }
                    }
                ],
                columnDefs: [
                    { orderable: false, targets: [0, -1] },
                    { orderable: true, targets: [1, 2, 3, 4] },
                ],
                drawCallback: function() {
                    
                }
            });
        }
    }

    // get data histori
    var modal_histori = $('#modal-histori');
    var tabel_histori;
    $('#tabel_ess_hutang').on('click', '.btn-histori', function(e){
        e.preventDefault();
        
        let data = new FormData();
        data.append('np', e.target.dataset.np)
        $.ajax({
            url: `${BASE_URL}cuti/pembayaran_hutang_cuti/histori`,
            type: 'POST',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: () => {
                modal_histori.modal('show');
                $('#tabel_histori').LoadingOverlay('show');
            },
        }).then((res) => {
            $('#tabel_histori').LoadingOverlay('hide', true);
            load_table_histori(res.data);
        }).fail((error) => {
            $('#tabel_histori').LoadingOverlay('hide', true);
            alert('Internal Server Error');
        });
    });

    const load_table_histori = (data)=>{
        tabel_histori = $('#tabel_histori').DataTable({
            "iDisplayLength": 10,
            "language": {
                "url": "<?= base_url('asset/datatables/Indonesian.json'); ?>",
                "sEmptyTable": "Tidak ada data di database",
                "processing": "Sedang memuat data pengajuan lembur",
                "emptyTable": "Tidak ada data di database"
            },
            "destroy": true,
            "stateSave": true,
            "responsive": true,
            "processing": true,
            "serverSide": false,
            "ordering": false,
            "data": data,
            columns: [
                {
                    data: 'hutang_awal',
                }, {
                    data: 'pembayaran',
                }, {
                    data: 'bayar_dari_mst_cuti_id',
                    render: (data) => {
                        switch (data) {
                            case '1':
                                return 'Cuti Tahunan';
                                break;
                            case '2':
                                return 'Cuti Besar';
                                break;
                            default:
                                return '';
                                break;
                        }
                    }
                }, {
                    data: 'sisa_hutang',
                }, {
                    data: 'created_at',
                }
            ]
        });
    }

    modal_histori.on('hidden.bs.modal', function (e) {
        load_table();
    });
</script>