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
    
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-3">
                                            <label>NDE (file pdf) <?= @$perencanaan ? '':'*'?></label>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="file" class="form-control" name="evidence" id="" style="width: 100%;" accept="application/pdf" <?= @$perencanaan ? '':'required'?>>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-3">
                                            <label>Periode *</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" id="dates" required>
                                        </div>
                                    </div>
                                </div>
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
                                                            <select class="form-control jenis_lembur" required>
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
                                <button class="btn btn-primary mt-2" type="submit">Simpan</button>
                                        
                            </form>
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
<script>
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

    var startDate = (typeof perencanaan!='undefined' ? moment(perencanaan.tanggal_mulai).format("YYYY-MM-DD") : moment().startOf("week").format("YYYY-MM-DD"));
    var endDate = (typeof perencanaan!='undefined' ? moment(perencanaan.tanggal_selesai).format("YYYY-MM-DD") : moment().endOf("week").format("YYYY-MM-DD"));

    $(()=>{
        list_table.find('tbody').empty();
        $('.select2').select2();
        $("#kode_unit").trigger('change');
        init_dates();

        if(typeof perencanaan_detail!='undefined'){
            load_existing_detail();
        }
    });

    function init_dates() {
        $("#dates").daterangepicker(
            {
                startDate: (typeof perencanaan!='undefined' ? moment(perencanaan.tanggal_mulai) : moment().startOf("week")),
                endDate: (typeof perencanaan!='undefined' ? moment(perencanaan.tanggal_selesai) : moment().endOf("week")),
            },
            function (start, end) {
                startDate = start.format("YYYY-MM-DD");
                endDate = end.format("YYYY-MM-DD");
            }
        );
    };

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
        return;
    });
    // END kode unit
    
    // tanggal periode
    $("#dates").on("change", (e) => {
        let tbody = list_table.find('tbody');
        for (const i of temp_array_of_id) {
            let tr = tbody.find(`#tr-${i}`);
            tr.find('.tanggal').trigger('change');
        }
        return;
    });
    // END tanggal periode

    $('.add-list').on('click', (e)=>{
        let tbody = list_table.find('tbody');
        let id = (typeof temp_last_id!='undefined' && temp_last_id!=null ? temp_last_id : uuidv4());
        temp_array_of_id.push(id);
        let tr = $('<tr>', {
            id: `tr-${id}`
        });

        let find_row = null;
        if(typeof perencanaan_detail!='undefined'){
            let find_detail = perencanaan_detail.find(o=>{ return o.id==id; });
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
            } else select_np.val('').trigger('change');
        }, 100);

        // jumlah
        tr.append($('<td>', {
            html: `<input type="number" class="form-control jumlah_karyawan" readonly required>`
        }));

        // jam
        tr.append($('<td>', {
            html: `<input type="number" class="form-control jam_lembur" required>`
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
            html: `<select class="form-control jenis_lembur" required>
                        <option value="awal">Lembur Awal</option>
                        <option value="akhir">Lembur Akhir</option>
                    </select>`
        }));
        if(find_row != null){
            setTimeout(() => {
                tbody.find(`#tr-${id}`).find('.jenis_lembur').val(find_row.jenis_lembur).trigger('change');
            }, 100);
        }

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

    $('#form-tambah').on('submit', function(e){
        let data = new FormData(this);
        let detail = get_data_list();
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
            if(res.status==true) location.href = '<?= base_url('lembur/perencanaan_lembur')?>';
            else alert(res.message);
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
                'jenis_lembur': tr.find('.jenis_lembur').val(),
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
</script>