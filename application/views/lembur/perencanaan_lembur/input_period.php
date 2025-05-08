<link href="<?= base_url() ?>asset/select2/select2.min.css" rel="stylesheet" />
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
		
		if (@$akses["tambah"] || (@$perencanaan && @$akses["ubah"])) { ?>
			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
						Form Perencanaan Lembur
					</div>
                    <div class="panel-body">
                        <div class="col-12">
                            <form role="form" action="<?= base_url(); ?>lembur/perencanaan_lembur/simpan_data_perencanaan" id="form-tambah" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                <?php if(@$perencanaan):?>
                                <input type="hidden" name="uuid" value="<?= $perencanaan->uuid?>" required>
                                <?php endif?>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-3">
                                            <label>Unit Kerja *</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" name="kode_unit" id="kode_unit" style="width: 100%;" required>
                                                <?php foreach($sto as $row):?>
                                                <option value="<?= $row->object_abbreviation?>" <?= $row->object_abbreviation==@$perencanaan->kode_unit ? 'selected':''?>><?= $row->object_name?></option>
                                                <?php endforeach?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div id="upload-container">
                                        <div class="row upload-form">
                                            <div class="form-group">
                                                <div class="col-lg-3">
                                                    <label>NDE (file pdf) <?= @$perencanaan ? '' : '*' ?></label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <div class="row">
                                                        <div class="col-lg-10" style="margin-top: 5px; margin-bottom: 5px;">
                                                            <input type="file" class="form-control me-2" name="evidence[]" accept="application/pdf" <?= @$perencanaan ? '' : 'required' ?>>
                                                        </div>
                                                        <div class="col" style="margin-top: 7px; margin-bottom: 3px;">
                                                            <!-- <button type="button" class="btn btn-sm        btn-danger" onclick="hapusKolom(this)"><i class="fa fa-trash"></i></button> -->
                                                            <button type="button" class="btn btn-sm btn-primary" onclick="tambahKolom(this)"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>           
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-3">
                                            <label>Periode *</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control" id="date-period" required>
                                                <option value="">-- Pilih periode --</option>
                                                <?php foreach($periode_lembur as $row):?>
                                                    <option value="<?= $row['periode']?>" <?= $row['start_date']<=$current_date && $row['end_date']>=$current_date ? 'selected':''?>><?= $row['periode']?></option>
                                                <?php endforeach?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
    
                                <?php if(@$perencanaan && @$akses["ubah"]){
                                } else{?>
                                <br><br>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-3">
                                            <label>Import Melalui Excel</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <button type="button" class="btn btn-default" id="btn-modal-import">Upload Excel</button>
                                            <!-- <input type="file" class="form-control" id="choose-excel" style="width: 100%;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                            <span>
                                                Silakan <a href="<?= base_url('asset/Template_import_perencanaan_lembur.xlsx')?>" download="Template_import_perencanaan_lembur-<?= date('YmdHis')?>.xlsx" target="_blank">Download file template</a>.
                                            </span> -->
                                        </div>
                                    </div>
                                </div>
                                <?php }?>
                                <br>
    
                                <div class="div-list row">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <table class="table table-bordered table-responsive" id="list-table">
                                                <thead>
                                                    <tr>
                                                        <td style="width: 15%;">Tanggal</td>
                                                        <td style="width: 20%;">NP</td>
                                                        <td style="width: 10%;">Jumlah Karyawan</td>
                                                        <td style="width: 15%;">Jam Lembur (per karyawan)</td>
                                                        <td style="width: 10%;">Jenis Hari</td>
                                                        <td style="width: 10%;">Jenis Lembur</td>
                                                        <td style="width: 15%;">Alasan Lembur</td>
                                                        <td style="width: 5%;"></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="date" class="form-control tanggal" required></td>
                                                        <td>
                                                            <select class="form-control list_np" name="" id="" style="width: 100%;" required></select>
                                                        </td>
                                                        <td><input type="number" class="form-control jumlah_karyawan" required></td>
                                                        <td><input type="number" class="form-control jam_lembur" required></td>
                                                        <td>
                                                            <select class="form-control jenis_hari" required>
                                                                <option value="kerja">Hari Kerja</option>
                                                                <option value="libur">Hari Libur</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control mst_kategori_lembur_id" required>
                                                                <option value="awal">Lembur Awal</option>
                                                                <option value="akhir">Lembur Akhir</option>
                                                            </select>
                                                        </td>
                                                        <td><textarea rows="1" class="form-control alasan_lembur" required></textarea></td>
                                                        <td><button class="btn btn-danger" type="button"><i class="fa fa-trash"></i></button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <button type="button" class="add-list"><i class="fa fa-plus"></i> Tambah baris</button>
                                        </div>
                                    </div>
                                </div>
                                <br>
    
                                <a class="btn btn-default mt-2" href="<?= base_url('lembur/perencanaan_lembur')?>">Kembali</a>
                                <button class="btn btn-primary mt-2 btn-check-input" type="button">Simpan</button>
                                        
                            </form>
                        </div>
                    </div>
				</div>
			</div>

            <!-- modal warning -->
            <div class="modal fade" id="modal-warning" tabindex="-1" role="dialog" aria-labelledby="label-modal-warning" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Perhatian</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row mt-2">
                                <div class="col-lg-12">
                                    <p>Beberapa isian tidak akan diproses oleh sistem.</p>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-lg-12">
                                    <table width="100%" class="table table-striped table-bordered table-hover" id="tabel-warning">
                                        <thead>
                                            <tr>
                                                <th class='text-center' style="width: 20%;">NP</th>
                                                <th class='text-center' style="width: 20%;">Tanggal</th>
                                                <th class='text-center' style="width: 20%;">Jam Lembur</th>
                                                <th class='text-center' style="width: 40%;">Alasan</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-default">Periksa Isian</button>
                            <button type="button" class="btn btn-primary btn-force-submit">Tetap Simpan</button>
                        </div>
                    </div>
                </div>
            </div>

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

            <!-- modal error -->
            <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="label-modal-error" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Informasi</h4>
                        </div>
                        <div class="modal-body">
                            <p>
                                <div class="alert alert-success">
                                    Data berhasil disimpan, namun ada beberapa data yang gagal disimpan.
                                </div>
                            </p>
                            <p>Berikut sebagian data yang gagal disimpan beserta alasannya:</p>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="tabel-error">
                                <thead>
                                    <tr>
                                        <th class='text-center' style="width: 15%;">Tanggal</th>
                                        <th class='text-center' style="width: 15%;">NP</th>
                                        <th class='text-center' style="width: 15%;">Jam Lembur</th>
                                        <th class='text-center' style="width: 15%;">Jenis Hari</th>
                                        <th class='text-center' style="width: 20%;">Alasan Lembur</th>
                                        <th class='text-center' style="width: 20%;">Alasan Gagal Simpan</th>
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
</div>

<script src="<?= base_url() ?>asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= base_url() ?>asset/select2/select2.min.js"></script>
<script src="<?= base_url()?>asset/js/uuidv4.js"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/daterangepicker-master/daterangepicker3.js')?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js')?>"></script>
<script src="<?= base_url('asset/xlsx/0.16.9/xlsx.full.min.js')?>"></script>
<script>
    const uploadContainer = document.getElementById('upload-container');

    // Fungsi untuk menghapus kolom
    function hapusKolom(button) {
        const uploadForm = button.closest('.upload-form');
        uploadForm.remove();
    }

    // Fungsi untuk menambah kolom
    function tambahKolom(button) {
        const newUploadForm = document.createElement('div');
        // newUploadForm.className = 'row upload-form mt-2';
        newUploadForm.innerHTML = `
            <div>
                <div id="upload-container">
                    <div class="row upload-form">
                        <div class="form-group">
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-10" style="margin-top: 5px; margin-bottom: 5px;">
                                        <input type="file" class="form-control" name="evidence[]" accept="application/pdf" <?= @$perencanaan ? '' : 'required' ?>>
                                    </div>
                                    <div class="col" style="margin-top: 7px; margin-bottom: 3px;">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="tambahKolom(this)"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusKolom(this)"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
        `;
        uploadContainer.appendChild(newUploadForm);
    }
</script>
<script>
    var BASE_URL = '<?= base_url()?>';
    <?php if(@$perencanaan && @$akses["ubah"]):?>
    var perencanaan = <?= json_encode($perencanaan)?>;
    <?php endif?>
    <?php if(@$perencanaan_detail && @$akses["ubah"]):?>
    var perencanaan_detail = <?= json_encode($perencanaan_detail)?>;
    var temp_last_id = null;
    <?php endif?>

    var list_table = $('#list-table');
    var temp_array_of_id = [];
    var mst_karyawan = <?= json_encode($mst_karyawan)?>;
    var temp_mst_karyawan = [];
    var periode_lembur = <?= json_encode($periode_lembur)?>;
    var kategori_lembur = <?= json_encode($kategori_lembur)?>;

    var barisOk = [];
    var barisNotOk = [];

    var startDate = (typeof perencanaan!='undefined' ? moment(perencanaan.tanggal_mulai).format("YYYY-MM-DD") : moment().startOf("week").format("YYYY-MM-DD"));
    var endDate = (typeof perencanaan!='undefined' ? moment(perencanaan.tanggal_selesai).format("YYYY-MM-DD") : moment().endOf("week").format("YYYY-MM-DD"));


    $(()=>{
        list_table.find('tbody').empty();
        $('.select2').select2();
        $("#kode_unit").trigger('change');
        $("#date-period").trigger('change');

        if(typeof perencanaan_detail!='undefined'){
            load_existing_detail();
        }
    });

    // kode unit
    $("#kode_unit").on("change", (e) => {
        temp_mst_karyawan = mst_karyawan.filter(o=>{ return o.kode_unit.startsWith(e.target.value.replace(/0+$/, '')); });
        let tbody = list_table.find('tbody');
        for (const i of temp_array_of_id) {
            let select_np = tbody.find(`#tr-${i}`).find('.list_np');
            select_np.empty();
            for (const i of temp_mst_karyawan) {
                select_np.append(new Option(`${i.no_pokok} - ${i.nama}`, i.no_pokok));
            }
            select_np.select2({
                multiple: true
            });
            select_np.val('').trigger('change');
        }

        checkPeriodAndUnit();
        return;
    });
    // END kode unit
    
    // tanggal periode
    $("#date-period").on("change", (e) => {
        let find_match = periode_lembur.find(o=>{ return o.periode==e.target.value; });
        if(typeof find_match!='undefined'){
            startDate = find_match.start_date;
            endDate = find_match.end_date;
        }

        let tbody = list_table.find('tbody');
        for (const i of temp_array_of_id) {
            let tr = tbody.find(`#tr-${i}`);
            tr.find('.tanggal').trigger('change');
        }

        checkPeriodAndUnit();
        return;
    });
    // END tanggal periode

    $('.add-list').on('click', (e)=>{
        let tbody = list_table.find('tbody');
        let id = uuidv4();
        if( typeof temp_last_id!='undefined' && temp_last_id!=null ) id = temp_last_id;
        else if( typeof temp_last_excel_id!='undefined' && temp_last_excel_id!=null ) id = temp_last_excel_id;
        // let id = (typeof temp_last_id!='undefined' && temp_last_id!=null ? temp_last_id : uuidv4());
        temp_array_of_id.push(id);
        let tr = $('<tr>', {
            id: `tr-${id}`
        });

        let find_row = null;
        if(typeof perencanaan_detail!='undefined'){
            let find_detail = perencanaan_detail.find(o=>{ return o.id==id; });
            if(typeof find_detail!='undefined') find_row = find_detail;
        } else if(typeof tempDataExcel!='undefined'){
            let find_detail = tempDataExcel.find(o=>{ return o.id==id; });
            if(typeof find_detail!='undefined') find_row = find_detail;
        }
        
        // tanggal
        tr.append($('<td>', {
            html: `<input type="date" class="form-control tanggal" required>`
        }));
        if(find_row != null){
            setTimeout(() => {
                tbody.find(`#tr-${id}`).find('.tanggal').val(find_row.tanggal).trigger('change');
            }, 100);
        }

        // np
        tr.append($('<td>', {
            html: `<select class="form-control list_np" name="" id="" style="width: 100%;" required></select>`
        }));
        setTimeout(() => {
            let select_np = tbody.find(`#tr-${id}`).find('.list_np');
            select_np.empty();
            for (const i of temp_mst_karyawan) {
                select_np.append(new Option(`${i.no_pokok} - ${i.nama}`, i.no_pokok));
            }
            select_np.select2({
                multiple: true
            });
            if(find_row != null){
                let np = [null,''].includes(find_row.list_np)===false ? find_row.list_np.split(',') : [];
                select_np.val(np).trigger('change');
            } else {
                select_np.val('').trigger('change');
            }
        }, 100);

        // jumlah
        tr.append($('<td>', {
            html: `<input type="number" class="form-control jumlah_karyawan" readonly required>`
        }));

        // jam
        tr.append($('<td>', {
            html: `<input type="number" class="form-control jam_lembur" min="0" required>`
        }));
        if(find_row != null){
            setTimeout(() => {
                tbody.find(`#tr-${id}`).find('.jam_lembur').val(find_row.jam_lembur).trigger('change');
            }, 100);
        }

        // jenis hari
        tr.append($('<td>', {
            html: `<select class="form-control jenis_hari" required>
                        <option value="kerja">Hari Kerja</option>
                        <option value="libur">Hari Libur</option>
                    </select>`
        }));
        if(find_row != null){
            setTimeout(() => {
                tbody.find(`#tr-${id}`).find('.jenis_hari').val(find_row.jenis_hari).trigger('change');
            }, 100);
        }

        // jenis lembur
        tr.append($('<td>', {
            html: `<select class="form-control mst_kategori_lembur_id" required>
                        <option value="awal">Lembur Awal</option>
                        <option value="akhir">Lembur Akhir</option>
                    </select>`
        }));
        setTimeout(() => {
            let select_jenis_lembur = tbody.find(`#tr-${id}`).find('.mst_kategori_lembur_id');
            select_jenis_lembur.empty();
            for (const i of kategori_lembur) {
                select_jenis_lembur.append(new Option(`${i.kategori_lembur}`, i.id));
            }
            
            if(find_row != null){
                tbody.find(`#tr-${id}`).find('.mst_kategori_lembur_id').val(find_row.mst_kategori_lembur_id).trigger('change');
            }
        }, 100);

        // alasan
        tr.append($('<td>', {
            html: `<textarea rows="1" class="form-control alasan_lembur" required></textarea>`
        }));
        if(find_row != null){
            setTimeout(() => {
                tbody.find(`#tr-${id}`).find('.alasan_lembur').val(find_row.alasan_lembur).trigger('change');
            }, 100);
        }

        // aksi
        tr.append($('<td>', {
            html: `<button class="btn btn-danger delete-list" data-id="${id}" type="button"><i class="fa fa-trash" data-id="${id}"></i></button>`
        }));
        tbody.append(tr.prop('outerHTML'));

        if( typeof temp_last_id!='undefined' ) temp_last_id = null;
        if( typeof temp_last_excel_id!='undefined' ) temp_last_excel_id = null;
        return;
    });

    // list table action
    list_table.on('click', '.delete-list', (e)=>{
        list_table.find(`#tr-${e.target.dataset.id}`).remove();
        temp_array_of_id = temp_array_of_id.filter(o=>{ return o!=e.target.dataset.id; });
        return;
    });

    list_table.on('change', '.tanggal', function(e){
        if(e.target.value >= startDate && e.target.value <= endDate ) {
            $(this).css('border-color','');
        } else {
            $(this).css('border-color','red');
        };
        return;
    });

    list_table.on('change', '.list_np', function(e){
        count_jumlah_karyawan();
        return;
    });

    function count_jumlah_karyawan(){
        let tbody = list_table.find('tbody');
        for (const i of temp_array_of_id) {
            let select_np = tbody.find(`#tr-${i}`).find('.list_np');
            let jumlah_np = tbody.find(`#tr-${i}`).find('.jumlah_karyawan');
            jumlah_np.val(select_np.val()!=null ? select_np.val().length:0);
        }
        return;
    }
    // END list table action

    $('.btn-check-input').on('click', (e)=>{
        e.preventDefault();
        let checkAllInput = validateAllInput();
        if(checkAllInput.barisNotOk.length){
            showWarningModal();
        } else{
            $('#form-tambah').submit();
        }
        return;
    });

    $('#form-tambah').on('submit', function(e){
        let data = new FormData(this);
        let detail = getDataListFinal();
        data.append('tanggal_mulai', startDate);
        data.append('tanggal_selesai', endDate);
        data.append('detail', JSON.stringify(detail));
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: () => {
                $('#form-tambah').LoadingOverlay('show');
            },
        }).then((res) => {
            $('#form-tambah').LoadingOverlay('hide', true)
            if(res.status==true) {
                Swal.fire({
                    title: '',
                    text: res.message,
                    icon: 'success',
                    allowOutsideClick: false,
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                }).then(()=>{
                    location.href = '<?= base_url('lembur/perencanaan_lembur')?>';
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
        })
    });

    function get_data_list(){
        var data = [];
        let tbody = list_table.find('tbody');
        for (const i of temp_array_of_id) {
            let tr = tbody.find(`#tr-${i}`);
            let row = {
                'id': i,
                'tanggal': tr.find('.tanggal').val(),
                'list_np': tr.find('.list_np').val().join(','),
                'jumlah_karyawan': tr.find('.jumlah_karyawan').val(),
                'jam_lembur': tr.find('.jam_lembur').val(),
                'jenis_hari': tr.find('.jenis_hari').val(),
                'mst_kategori_lembur_id': tr.find('.mst_kategori_lembur_id').val(),
                'alasan_lembur': tr.find('.alasan_lembur').val(),
            };
            data.push(row);
        }
        return data;
    }

    <?php if(@$perencanaan_detail && @$akses["ubah"]):?>
    const load_existing_detail = ()=>{
        for (const i of perencanaan_detail) {
            temp_last_id = i.id;
            $('.add-list').trigger('click');
        }
        return;
    }
    <?php endif?>

    <?php if(@$akses["tambah"]):?>
    $('#btn-modal-import').on('click', (e)=>{
        $('#modal-import').modal('show');
    });
    <?php endif?>
</script>
<script src="<?= base_url('asset/js/lembur/validasi_input_perencanaan_lembur.js?q='.random_string())?>"></script>
<script src="<?= base_url('asset/js/lembur/import_perencanaan_excel.js?q='.random_string())?>"></script>