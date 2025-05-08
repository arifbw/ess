<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $judul ?></h1>
            </div>
        </div>

        <?php if(!empty($this->session->flashdata('success'))): ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('success');?>
            </div>
        <?php endif; ?>
        <?php if(!empty($this->session->flashdata('warning'))): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('warning');?>
            </div>
        <?php endif; ?>
        
        <?php if(@$akses["tambah"]): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Tambah</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="panel-body">
                                <form role="form" action="<?= base_url(); ?>master_data/plafon/listrik/action_insert" id="formulir_tambah" method="post" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>NP Karyawan</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" name="np_karyawan" id="np_karyawan" placeholder="NP" onchange="findDataKaryawan();" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>Nama Karyawan</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" name="nama_karyawan" id="nama_karyawan" placeholder="Nama Karyawan" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>Nama Jabatan</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" name="nama_jabatan" id="nama_jabatan" placeholder="Nama Jabatan" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>Alamat</label>
                                        </div>
                                        <div class="col-lg-7">                                                      
                                            <input class="form-control" name="alamat" id="alamat" placeholder="Masukkan Alamat" required>
                                        </div>                                                      
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>No Kontrol</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" type="text" name="no_kontrol" id="no_kontrol" placeholder="Masukkan No Kontrol" required>
                                        </div>														
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>Plafon</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" type="number" name="plafon" id="plafon" placeholder="Masukkan Jumlah">
                                        </div>                                                      
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-lg-9 text-right">
                                            <button type="button" class="btn btn-default" id="btn-cancel-form">Cancel</button>
                                            <button type="submit" class="btn btn-primary" id="btn-submit-form">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($this->akses["lihat"]): ?>
            <div class="row table-responsive">
                <div class="col-lg-12">	
                    <table width="100%" class="table table-striped table-bordered table-hover" id="data-table">
                        <thead>
                            <tr>
                                <th class="text-center no-sort" style="max-width: 5%">No</th>
                                <th class="text-center">Karyawan</th>
                                <th class="text-center no-sort">Alamat</th>
                                <th class="text-center no-sort">No Kontrol</th>
                                <th class="text-center no-sort">Plafon</th>
                                <th class="text-center no-sort">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>						
            </div>

            <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="label_modal_batal">Status <?= @$judul ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="get-approve"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>


        <?php if(@$akses["hapus"]): ?>
            <div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title text-danger" id="title-inactive">
                                <b>Hapus <?= $judul ?></b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </h4>
                        </div>

                        <div class="modal-body">
                            <h4 id="message-inactive"></h4>
                        </div>
                        <div class="modal-footer">
                            <a href="" id="inactive-action" class="btn btn-danger">Ya, Hapus</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= base_url('asset/select2/select2.min.js')?>"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url()?>asset/lodash.js/4.17.21/lodash.min.js"></script>
<script type="text/javascript">
    var table;
    var allKaryawan = <?= json_encode($array_daftar_karyawan->result_array())?>;
    $(document).ready(function() {
        $('.select2').select2();
        tableServerside();
    });

    const tableServerside = async () => {
		table = $('#data-table').DataTable({
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
				"url"	: "<?php echo site_url("master_data/plafon/listrik/tabel_listrik")?>",					 
				"type"	: "POST",
				"data"	: {}
			},
			"columnDefs": [
				{ 
					"targets": 'no-sort',
					"orderable": false
				},
			],
			columns: [
				{
					render: function (data, type, row, meta) {
                 		return meta.row + meta.settings._iDisplayStart + 1;
                	} 
				},
				{
					data: 'nama_karyawan',
					name: 'nama_karyawan',
				},
				{
					data: 'alamat',
					name: 'alamat',
				},
				{
					data: 'no_kontrol',
					name: 'no_kontrol',
				},
				{
					render: (data, type, row) => {
                        if(row.plafon!==null)
						    return 'Rp ' + parseInt(row.plafon).toLocaleString();
                        else if(row.ket==='at cost')
						    return 'at cost';
                        else
                            return '';
					}
				},
				{
					render: (data, type, row) => {
						let label='';
                        
						const edit = $('<button/>', {
							html: 'Edit',
							class: 'btn btn-warning btn-xs detail_button',
							onclick: `edit(${JSON.stringify(row)})`
						})
						label += edit.prop('outerHTML');
                        label +=' ';
						const hapus = $('<button/>', {
							html: 'Hapus',
							class: 'btn btn-danger btn-xs detail_button',
							onclick: `hapus(${JSON.stringify(row)})`
						})
						label += hapus.prop('outerHTML');
                        
						return label;
					}
				}
			],
		});
	};

    const edit = async (data) => {
		$('#btn-tambah').html('Edit data');
		$('#np_karyawan').prop('disabled',true);
		$('#no_kontrol').prop('disabled',true);
		if($("#collapseOne").is(":visible")){
			console.log('Form opened');
		} else{
			$('#btn-tambah').trigger('click');
		}
		
		let fields = ['np_karyawan', 'nama_karyawan', 'nama_jabatan', 'alamat', 'no_kontrol', 'plafon'];
		for (const i of fields) {
			$('#formulir_tambah').find(`[name=${i}]`).val(`${(data[i]!=null ? data[i]:'')}`);
		}
        // $('#np_karyawan').trigger('change');

		if($('#formulir_tambah').find('[name=id]').length){
			$('#formulir_tambah').find(`[name=id]`).val(`${data.id}`);
		} else{
			$('<input>').attr({
				type: 'hidden',
				name: 'id',
				value: `${data.id}`
			}).appendTo('#formulir_tambah');
		}
		$("html, body").animate({ scrollTop: 0 }, "slow");
	}

    const hapus = async (data) =>{
		let result = confirm("Perhatian\nData yang telah dihapus tidak dapat dikembalikan.\nLanjutkan?");
		if (result) {
			$.ajax({
				type: "POST",
				url: `<?= base_url('master_data/plafon/listrik/hapus')?>`,
				data: {id: data.id},
				dataType: 'json',
			}).then(function(response){
				console.log(response.message);
				table.draw(false);
			}).catch(function(xhr, status, error){
				console.log(xhr.responseText);
				table.draw(false);
			})
		}
	}

    $("#btn-cancel-form").on('click', function(e){
		$('#btn-tambah').html('Tambah');
		$('#btn-tambah').trigger('click');
		document.getElementById("formulir_tambah").reset();
        // $('#np_karyawan').trigger('change');
		if($('#formulir_tambah').find('[name=id]').length){
			$('#formulir_tambah').find(`[name=id]`).remove();
		}
		$('#np_karyawan').prop('disabled',false);
		$('#no_kontrol').prop('disabled',false);
	});

    const findDataKaryawan = async ()=>{
		let np_karyawan = $("#np_karyawan").val();
        let findData = await _.find(allKaryawan, function(o) { return o.no_pokok === `${np_karyawan.trim()}`; });
        if (typeof findData!='undefined'){
            $("#nama_karyawan").val(findData.nama);
            $("#nama_jabatan").val(findData.nama_jabatan);
        } else{
            $("#nama_karyawan").val('');
            $("#nama_jabatan").val('');
            alert('NP tidak ada di Master Data, silakan lengkapi Nama dan Jabatan');
        }
    }
</script>