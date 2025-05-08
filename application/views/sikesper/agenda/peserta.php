<div class="row">
	<div class="col-md-12 text-right" style="margin-bottom: 2% !important;">
		<a href="<?= base_url('sikesper/agenda/report_peserta_agenda/'.$agenda); ?>" target="_blank" class="btn btn-primary btn-m">Report Peserta</a>
	</div>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-striped" id="peserta" style="width: 100%;">
				<thead>
					<tr>
						<td>#</td>
						<td>No Pokok</td>
						<td>Nama Peserta</td>
						<td>Tanggal Daftar</td>
                        <?= $_SESSION['grup']==12 ?'<td><input type="checkbox" class="check-all" id="check-all" onclick="check_all();"> Verifikasi Kehadiran</td>':'<td></td>'?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$start = 1;
						foreach($data_peserta as $val){ 
					?>
					<tr>
						<td><?= $start++; ?></td>
						<td><?= $val->np_karyawan; ?></td>
						<td><?= $val->nama; ?></td>
						<td><?= date('d/m/Y H:i:s', strtotime($val->daftar_at)); ?></td>
                        <?php if($_SESSION['grup']==12){?>
                        <td>
                            <input type="checkbox" class="check-row" id="check-row-<?= $val->id?>" data-np="<?= $val->np_karyawan?>" onclick="check_row();" <?= $val->verifikasi_hadir==1?'checked':''?>>
                        </td>
                        <?php } else {
                            echo '<td></td>';
                        }?>
					</tr>
					<?php } ?>
				</tbody>	
			</table>
            <input type="hidden" name="id_agenda" id="id_agenda" value="<?= $agenda?>">
		</div>
	</div>
    <div class="col-md-12 text-center">
        <div id="div-response-message"></div><br>
        <button class="btn btn-success" id="btn-verifikasi" onclick="simpan()">Simpan</button>
    </div>
</div>

<script type="text/javascript">
	var all_peserta = [], id_agenda;
	$(document).ready(function() {
		$('#peserta').DataTable({
            destroy: true,
            ordering: false,
            drawCallback: function() {
                console.log('table loaded!');
                check_row();
            }
        });
	});
    
    function check_all(){
        if($('#check-all').is(":checked")) {
            $(".check-row").prop( "checked", true );
        } else {
            $(".check-row").prop( "checked", false );
        }
    }
    
    function check_row(){
        if($('.check-row:not(:checked)').length>0){
            $('#check-all').prop('checked',false);
        } else{
            $('#check-all').prop('checked',true);
        }
    }
    
    function simpan(){
        $('#btn-verifikasi').prop('disabled',true);
        $('#btn-verifikasi').html('Memproses...');
        
        all_peserta = $('input:checkbox:checked.check-row').map(function () {
            return this.dataset.np;
        }).get();
        id_agenda = $('#id_agenda').val();
        
        $.ajax({
            url: "<?= base_url('sikesper/agenda/verifikasi_hadir') ?>",
            type: "POST",
            dataType: "json",
            data: {kry: all_peserta, agenda: id_agenda},
            success: function(response) {
                return response;
            },
            error: function(jqXHR){
                console.log(jqXHR.responseText);
            }
        }).then(function(response) {
            $('#div-response-message').html(response.message);
            $('#btn-verifikasi').prop('disabled',false);
            $('#btn-verifikasi').html('Simpan');
            
            setTimeout(function(){
                $('#div-response-message').html('');
            }, 2000);
            
        }).catch((error) => {
            console.error(error);
        });
    }
</script>