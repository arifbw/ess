<div class="row">						
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse">
										<div class="panel-body">
											
												<form role="form" action="<?php echo base_url(); ?>perizinan/data_perizinan/action_insert_perizinan" id="formulir_tambah" method="post">												
																								
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Karyawan</label>
															</div>
															<div class="col-lg-7">
																 <!--<input class="form-control" name="np_karyawan" id="np_karyawan" onChange="getNama()" required>-->
                                                                <select class="form-control select2" onchange="get_approval()" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
                                                                    <option value=''></option>
                                                                    <?php 
																	foreach ($array_daftar_karyawan->result_array() as $value) {		
																	?>
																		<option value='<?php echo $value['no_pokok']?>'><?php echo $value['no_pokok']." ".$value['nama']?></option>								
																		
																	<?php 
																	}
																	?>
                                                                </select>
															</div>	
															<!--
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>	
															-->
														</div>
													</div>
													
													<!--<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label></label>
															</div>
															<div class="col-lg-7">
																 <textarea class="form-control" name="nama" id="nama" rows="5" readonly required></textarea>
															</div>														
														</div>
													</div>-->
													
													<div class="row" id="">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jenis Izin</label>
															</div>
															<div class="col-lg-7">
																<select class="form-control" name="absence_type" onchange="take_action_date(); get_approval()" id="add_absence_type" required>
                                                                    <option value="">-- Pilih jenis perizinan --</option>
                                                                    <?php foreach($jenis_izin as $row){?>
                                                                    <option value="<?= $row->kode_pamlek.'|'.$row->kode_erp?>"><?= $row->nama?></option>
                                                                    <?php } ?>
																</select>
															</div>														
														</div>
													</div>
													
													<?php
														$bulan_lalu = $data_tanggal	= date('Y-m-d',strtotime('-1 months',strtotime(date('Y-m-d'))));
														$sudah_cutoff = sudah_cutoff($bulan_lalu);
														
														if($sudah_cutoff)
														{										
															$min = date('Y-m')."-01";
														}else
														{
															$min = '';
														}
														
													?>
													<div id="form-start-date" style="display: block;">
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Start Date</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="start_date" id="start_date" required>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Start Time</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control datetimepicker5" name="start_time" id="start_time" required>
															</div>
														</div>
													</div></div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>End Date</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="end_date" id="end_date" required>
															</div>														
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>End Time</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control datetimepicker5" name="end_time" required>
															</div>
														</div>
													</div>
                                                    
                                                    <div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Atasan 1</label>								
															</div>
															<div class="col-lg-7">
                                                                <select class="form-control select2" name="approval_1_np" id="approval_1_np" style="width: 100%" onchange="fill_jabatan(this,'approval_1_jabatan')"></select>
															</div>								
														</div>
													</div>
                                                    
                                                    <div class="row">
														<div class="form-group">
															<div class="col-lg-2"></div>
															<div class="col-lg-7">
																<input class="form-control" name="approval_1_jabatan" id="approval_1_jabatan" readonly>
															</div>								
														</div>
													</div>
                                                    
                                                    <div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Atasan 2</label>								
															</div>
                                                                <select class="form-control select2" name="approval_2_np" id="approval_2_np" style="width: 100%" onchange="fill_jabatan(this,'approval_2_jabatan')"></select>
															</div>								
														</div>
													</div>
                                                    
                                                    <div class="row">
														<div class="form-group">
															<div class="col-lg-2"></div>
															<div class="col-lg-7">
																<input class="form-control" name="approval_2_jabatan" id="approval_2_jabatan" readonly>
															</div>								
														</div>
													</div>

													<div class="form-group row">
														<div class="col-lg-2">
															<label>NP Atasan 1</label>								
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="approval_1_np" id="approval_1_np" value="" onChange="getNamaAtasan1()" required>
														</div>								
													</div>

													<div class="form-group row">
														<div class="col-lg-2">
															<label></label>
														</div>
														<div class="col-lg-7">														
															<input class="form-control" name="approval_1_input" id="approval_1_input" value="" readonly required>											
														</div>														
													</div>
													
													<div class="form-group row">
														<div class="col-lg-2">
															<label></label>
														</div>
														<div class="col-lg-7">														
															<input class="form-control" name="approval_1_input_jabatan" id="approval_1_input_jabatan" required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>												
														</div>														
													</div>

													<div class="form-group row">
														<div class="col-lg-2">
															<label>NP Atasan 2</label>								
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="approval_2_np" id="approval_2_np" value="" onChange="getNamaAtasan2()" required>
														</div>								
													</div>

													<div class="form-group row">
														<div class="col-lg-2">
															<label></label>
														</div>
														<div class="col-lg-7">														
															<input class="form-control" name="approval_2_input" id="approval_2_input" value="" readonly required>											
														</div>														
													</div>
													
													<div class="form-group row">
														<div class="col-lg-2">
															<label></label>
														</div>
														<div class="col-lg-7">														
															<input class="form-control" name="approval_2_input_jabatan" id="approval_2_input_jabatan" required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>												
														</div>														
													</div>
                                                    
													<div class="row">
														<div class="col-lg-9 text-right">
															<input type="submit" name="submit" value="submit" class="btn btn-primary">
														</div>
													</div>
												</form>
											
											
									</div>
								</div>
							</div>
							<!-- /.col-lg-12 -->
						</div>
						<!-- /.row -->
						
						<!-- Modal NP -->
						<div class="modal fade" id="modal_np" tabindex="-1" role="dialog" aria-labelledby="label_modal_np" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_np">Daftar List NP <?php echo $judul;?></h4>
										</div>
										<div class="modal-body" align='center'>		
											<textarea name='list_np' id='list_np' rows="10" cols="50" readonly></textarea>
										</div>										
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->