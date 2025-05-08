<link rel="stylesheet" type="text/css" href="<?= base_url('asset/select2/select2.min.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') ?>" />

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul; ?></h1>
			</div>
		</div>

        <div class="row">
            <!-- filter -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="filter-bulan">Bulan</label>
                    <select placeholder="Filter Bulan" id="filter-bulan" class="form-control">
                        <?php foreach($bulan as $row):?>
                            <option value="<?= $row->bulan?>"><?= date('F Y', strtotime($row->bulan))?></option>
                        <?php endforeach?>
                    </select>
                </div>
			</div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="filter-cuti">Status Cuti</label>
                    <select placeholder="Filter Cuti" id="filter-cuti" class="form-control" style="width: 100%;">
                        <option value="">-- Tampilkan Semua --</option>
                        <option value="1">Sudah Mengajukan Cuti</option>
                        <option value="2">Belum Mengajukan Cuti</option>
                    </select>
                </div>
			</div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <h4>Data Kehadiran</h4>
                <table width="100%" class="table table-striped table-bordered table-hover" id="kehadiran-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th class="text-center" style="width: 20%;">NP - Nama</th>
                            <th class="text-center" style="width: 20%;">Unit Kerja</th>
                            <th class="text-center" style="width: 20%;">Tanggal Cuti Bersama</th>
                            <th class="text-center" style="width: 20%;">Tap Masuk/Pulang</th>
                            <th class="text-center" style="width: 15%;">Status Cuti</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

	</div>
</div>

<script src="<?= base_url('asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js') ?>"></script>
<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js') ?>"></script>

<script>
    var kehadiran_table;
    $(()=>{
        load_table();
    });

    $('#filter-bulan, #filter-cuti').on('change', (e)=>{
        load_table();
    });

    const load_table = ()=>{
        if(typeof kehadiran_table!='undefined') kehadiran_table.draw();
        else {
            kehadiran_table = $('#kehadiran-table').DataTable({
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
                "ajax": {
                    "url": "<?= base_url("kehadiran/kehadiran_cuti_bersama/get_data_kehadiran/") ?>",
                    "data": function(e){
                        e.bulan = $('#filter-bulan').val();
                        e.status = $('#filter-cuti').val();
                    },
                    "type": "POST"
                },
                columns: [
                    {
                        data: 'no',
                    }, {
                        data: 'np_karyawan',
                        render: (data, type, row) => {
                            return `${row.np_karyawan} - ${row.nama}`;
                        }
                    }, {
                        data: 'nama_unit',
                    }, {
                        data: 'dws_tanggal',
                        render: (data, type, row) => {
                            return moment(data).format('DD MMM YYYY') + ` (${row.deskripsi})`;
                        }
                    }, {
                        data: 'id',
                        render: (data, type, row) => {
                            let str = '';
                            if(row.dws_in_fix==null && row.dws_out_fix==null){
                                str = '-';
                            } else{
                                if(row.dws_in_fix!=null){
                                    str += `Datang: ${row.dws_in_fix} <br>`;
                                }
                                if(row.dws_out_fix!=null){
                                    str += `Pulang: ${row.dws_out_fix}`;
                                }
                            }
                            return str;
                        }
                    }, {
                        data: 'id_cuti',
                        render: (data, type, row) => {
                            if([null,'null',''].includes(data)) return 'Belum Mengajukan Cuti';
                            else return 'Sudah Mengajukan Cuti';
                        }
                    }
                ],
                drawCallback: function() {
                    
                }
            });
        }
    }
</script>