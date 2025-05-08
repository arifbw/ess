
				<div class="row">
					<div class="col-md-10">
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
								<td>Start Date Input</td>
								<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><a><?= $start_date_input ?></a></td>
							</tr>
							<tr>
								<td>End Date Input</td>
								<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><a><?= $end_date_input ?></a></td>
							</tr>
							<tr>
								<td><b>Start Date Realisasi</b></td>
								<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><a class="text-danger"><b><?= $start_date ?></b></a></td>
							</tr>
							<tr>
								<td><b>End Date Realisasi</b></td>
								<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><a class="text-danger"><b><?= $end_date ?></b></a></td>
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
					</div>
				</div>
				<br>

				<?php if ($status_1!='2') { ?>
					<div class="alert alert-info">
						<strong><a class="text-info"><?= $status_approval_1_nama ?></a></strong><br>
						<p><?= $status_approval_1_status ?></p>
					</div>
				<?php } else { ?>
					<div class="alert alert-danger">
						<strong><a class="text-danger"><?= $status_approval_1_nama ?></a></strong><br>
						<p><?= $status_approval_1_status ?></p>
						<p style="margin-top: 0">Alasan : <?= $status_approval_1_keterangan ?></p>
					</div>
				<?php } ?>
					
				<?php if ($approval_2!='' && $approval_2!='0' && $approval_2!=null) { ?>
					<?php if ($status_2!='2') { ?>
					<div class="alert alert-info">
						<strong><a class="text-info"><?= $status_approval_2_nama ?></a></strong><br>
						<p><?= $status_approval_2_status ?></p>
					</div>
					<?php } else { ?>
					<div class="alert alert-danger">
						<strong><a class="text-danger"><?= $status_approval_2_nama ?></a></strong><br>
						<p><?= $status_approval_2_status ?></p>
						<p style="margin-top: 0">Alasan : <?= $status_approval_2_keterangan ?></p>
					</div>
					<?php } ?>
				<?php } ?>

				<?php if ($np_batal_apr!='' && $np_batal_apr!='0' && $np_batal_apr!=null) { ?>
					<div class="alert alert-danger">
						<strong><a class="text-danger"><?= $waktu_batal ?></a></strong><br>
						<p style="margin-top: 0">Alasan : <?= $alasan_batal ?></p>
					</div>
				<?php } ?>

				<?php if (count($pengamanan) > 0) { ?>
				<div class="alert alert-info">
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


			<script type="text/javascript">
			function close_modal_batal(obj){
				$('#batal').modal('hide');
			}
			function form_alasan(obj){
                var selectBox = obj;
                var selected = selectBox.options[selectBox.selectedIndex].value;
                var textarea = document.getElementById("form-alasan");

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