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
        <?php if(!empty($this->session->flashdata('failed'))): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('failed');?>
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
                                <form role="form" action="<?= base_url(); ?>master_data/alasan_sipk/action_insert" id="formulir_tambah" method="post" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label>Alasan</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" name="alasan" id="alasan" placeholder="Alasan" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="form-group">
                                            <div class="col-lg-2">
                                                <label>Status</label>
                                            </div>
                                            <div class="col-lg-7">
                                                <label class="radio-inline">
                                                    <input type="radio" name="status" id="status_tambah_aktif" value="1">Aktif
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="status" id="status_tambah_non_aktif" value="0">Non Aktif
                                                </label>
                                            </div>
                                            <div id="warning_status" class="col-lg-3 text-danger"></div>
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
                                <th class="text-center">Alasan</th>
                                <th class="text-center no-sort">Status</th>
                                <th class="text-center no-sort">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>						
            </div>
        <?php endif ?>
    </div>
</div>

<script src="<?= base_url('asset/select2/select2.min.js')?>"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url()?>asset/lodash.js/4.17.21/lodash.min.js"></script>
<script type="text/javascript">
    var table;
    var data_alasan = <?= json_encode($alasan)?>;
    $(document).ready(function() {
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
			"stateSave": false,
			"processing": true,
			"serverSide": false,
			"ordering": false,
			"data": data_alasan,
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
					data: 'alasan',
					name: 'alasan',
				},
				{
					render: (data, type, row) => {
                        let status = ``;
                        switch (row.status) {
                            case '1':
                                status = 'Aktif';
                                break;
                            default:
                                status = 'Nonktif';
                                break;
                        }
                        return status;
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
                        
						return label;
					}
				}
			],
		});
	};

    const edit = async (data) => {
		$('#btn-tambah').html('Edit data');
		if($("#collapseOne").is(":visible")){
			console.log('Form opened');
		} else{
			$('#btn-tambah').trigger('click');
		}
		
		$('#formulir_tambah').find(`[name=alasan]`).val(data.alasan);
        switch (data.status) {
            case '1':
                $('#status_tambah_aktif').prop('checked',true);
                break;
            default:
                $('#status_tambah_non_aktif').prop('checked',true);
                break;
        }

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

    $("#btn-cancel-form").on('click', function(e){
		$('#btn-tambah').html('Tambah');
		$('#btn-tambah').trigger('click');
		document.getElementById("formulir_tambah").reset();
		if($('#formulir_tambah').find('[name=id]').length){
			$('#formulir_tambah').find(`[name=id]`).remove();
		}
	});
</script>