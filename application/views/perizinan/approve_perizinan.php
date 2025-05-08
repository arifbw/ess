				<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
        		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

				<form role="form" action="<?php echo base_url(); ?>perizinan/persetujuan_perizinan/save_approve/" id="formulir_tambah" method="post">	
				
					<table>
						<tr>
							<td>NP Pemohon</td>
							<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
							<td><a><?= $no_pokok ?></a></td>
						</tr>
						<tr>
							<td>Nama Pemohon</td>
							<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
							<td><a><?= $nama_pegawai ?></a></td>
						</tr>
						<tr>
							<td>Start Date</td>
							<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
							<td><a><?= $start_date ?></a></td>
						</tr>
						<tr>
							<td>End Date</td>
							<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
							<td><a><?= $end_date ?></a></td>
						</tr>
						<tr>
							<td>Dibuat Tanggal</td>
							<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
							<td><a><?= $tgl ?></a></td>
						</tr>
                        
                        <!-- tambahan untuk alasan, 2021-03-10 -->
                        <tr>
                            <td>Alasan</td>
                            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                            <td><a><?= $alasan ?></a></td>
                        </tr>
                        <!-- END tambahan untuk alasan, 2021-03-10 -->
					</table>
					
					<br>
					
					<?php if (count($pengamanan) > 0) { ?>
					<div class="alert alert-info" id="alert-detail">
						<?php $no=1; foreach ($pengamanan as $aman) { ?>
						<strong>
                            <?php if($aman->status=='1'){?>
                            <a><?= $no ?>. <?= $aman->nama_pos ?> | <?= $aman->nama_approver ?> (<?= $aman->np_approver ?>) | <?= ucwords($aman->posisi) ?> Pada <?= $aman->waktu ?></a>
                            <?php } else{?>
                            <a><strike><?= $no ?>. <?= $aman->nama_pos ?> | <?= $aman->nama_approver ?> (<?= $aman->np_approver ?>) | <?= ucwords($aman->posisi) ?> Pada <?= $aman->waktu ?></strike></a>
                            <?php }?>
                        </strong>
                        <br>
						<?php $no++;} ?>
					</div>
					<?php } ?>

					<div class="alert alert-info">
						<strong><a><?= $status_approval_1_nama ?></a></strong><br>
						<?php if ((($status_1==null || $status_1=='0') && $approval_1==$_SESSION["no_pokok"]) || $_SESSION["grup"]=='15') { ?>
						<br>
						<select class="form-control select2" name='status_1' data-id="1" onchange="form_alasan(this)" style="width : 100%" required>
							<option value=''>Berikan Persetujuan</option>
							<option value='1'>Setuju</option>
							<option value='2'>Tidak Setuju</option>
						</select>
						<div id="form-alasan-1" style="display: none;">
							<b>Alasan Tidak Disetujui</b>
							<br>
							<textarea rows="2" class="form-control" name='alasan_1'></textarea>
						</div>
						<?php } else { ?>
						<p><?= $status_approval_1_status ?></p>
						<?php } ?>
					</div>
					
					<?php if ($approval_2!=null) { ?>
					<div class="alert alert-info">
						<strong><a><?= $status_approval_2_nama ?></a></strong><br>
						<?php if ((($status_2==null || $status_2=='0') && $approval_2==$_SESSION["no_pokok"]) || $_SESSION["grup"]=='15') { ?>
						<br>
						<select class="form-control select2" name='status_2' data-id="2" onchange="form_alasan(this)" style="width : 100%" required>
							<option value=''>Berikan Persetujuan</option>
							<option value='1'>Setuju</option>
							<option value='2'>Tidak Setuju</option>
						</select>
						<div id="form-alasan-2" style="display: none;">
							<b>Alasan Tidak Disetujui</b>
							<br>
							<textarea rows="2" class="form-control" name='alasan_2'></textarea>
						</div>
						<?php } else { ?>
						<p><?= $status_approval_2_status ?></p>
						<?php } ?>
					</div>
					<?php } ?>

					<div class="row">
						<div class="col-lg-12 text-right">
							<input type="hidden" name="id_perizinan" value="<?= $id_ ?>">
							<input type="hidden" name="tgl" value="<?= $date ?>">
							<input type="hidden" name="persetujuan_id" id="persetujuan_id">
							<input type="submit" name="submit" id='persetujuan_button' value="Submit" class="btn btn-block btn-success">
						</div>
					</div>
				</form>


			<script type="text/javascript">
			function form_alasan(obj){
                var selectBox = obj;
                var selected = selectBox.options[selectBox.selectedIndex].value;
                var id = $(obj).attr('data-id');
                var textarea = document.getElementById("form-alasan-"+id);

                if(selected === '2'){
                    textarea.style.display = "block";
                }
                else{
                    textarea.style.display = "none";
                }
            }
            $('.datetimepicker5').datetimepicker({
                format: 'YYYY-MM-DD HH:mm'
            });
            $('.select2').select2();
			</script>