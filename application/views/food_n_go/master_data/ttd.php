		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul;?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php
					if(!empty($success)){
				?>
						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $success;?>
						</div>
				<?php
					} if(@$this->session->flashdata('success')){
                        echo '<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							'.$this->session->flashdata('success').'
						</div>';
                    }
					if(!empty($warning)){
				?>
						<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $warning;?>
						</div>
				<?php
					}
					if(@$akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					if(@$akses["tambah"]){
				?>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Input <?php echo $judul;?></a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse <?php echo $panel_tambah;?>">
										<div class="panel-body">
											<form role="form" action="<?= base_url('food_n_go/master_data/ttd/simpan')?>" id="formulir_tambah" method="post">
                                                <div class="col-lg-6 col-md-12 col-sm-12">
                                                    <div class="row">
                                                        <label>Kiri (Mengetahui)</label>
                                                        <input type="hidden" name="kiri_type" value="kiri">
                                                        <input type="hidden" name="kiri_keterangan" value="Mengetahui">
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-lg-3">
                                                                <label>Nama Unit</label>
                                                            </div>
                                                            <div class="col-lg-9">
                                                                <input type="text" class="form-control" name="kiri_nama_unit" placeholder="Nama Unit" value="<?= @$existing_kiri->nama_unit?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-lg-3">
                                                                <label>Nama</label>
                                                            </div>
                                                            <div class="col-lg-9">
                                                                <select class="form-control select2" id="kiri_np" name="kiri_np" onchange="set_kiri_nama()" style="width: 100%;" required>
                                                                    <option>-- Pilih --</option>
                                                                    <?php foreach($mst_karyawan as $r){?>
                                                                    <option value="<?= $r['no_pokok']?>" <?= @$existing_kiri->np==$r['no_pokok']?'selected':''?>><?= $r['no_pokok'].' - '.$r['nama']?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <input type="hidden" id="kiri_nama" name="kiri_nama" value="<?= @$existing_kiri->nama?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-lg-3">
                                                                <label>Jabatan</label>
                                                            </div>
                                                            <div class="col-lg-9">
                                                                <input type="text" class="form-control" name="kiri_jabatan" placeholder="Jabatan" value="<?= @$existing_kiri->jabatan?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6 col-md-12 col-sm-12">
                                                    <div class="row">
                                                        <label>Kanan</label>
                                                        <input type="hidden" name="kanan_type" value="kanan">
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-lg-3">
                                                                <label>Nama Unit</label>
                                                            </div>
                                                            <div class="col-lg-9">
                                                                <input type="text" class="form-control" name="kanan_nama_unit" placeholder="Nama Unit" value="<?= @$existing_kanan->nama_unit?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-lg-3">
                                                                <label>Nama</label>
                                                            </div>
                                                            <div class="col-lg-9">
                                                                <select class="form-control select2" id="kanan_np" name="kanan_np" onchange="set_kanan_nama()" style="width: 100%;" required>
                                                                    <?php foreach($mst_karyawan as $r){?>
                                                                    <option value="<?= $r['no_pokok']?>" <?= @$existing_kanan->np==$r['no_pokok']?'selected':''?>><?= $r['no_pokok'].' - '.$r['nama']?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <input type="hidden" id="kanan_nama" name="kanan_nama" value="<?= @$existing_kanan->nama?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-lg-3">
                                                                <label>Jabatan</label>
                                                            </div>
                                                            <div class="col-lg-9">
                                                                <input type="text" class="form-control" name="kanan_jabatan" placeholder="Jabatan" value="<?= @$existing_kanan->jabatan?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-lg-12 text-center">
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </div>
                                                </div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
				<?php
					}
					
					if(@$this->akses["lihat"]){
				?>
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ttd">
								<thead>
									<tr>
										<th class='text-center'>#</th>
										<th class='text-center'>Nama Unit</th>
										<th class='text-center'>Nama</th>
										<th class='text-center'>Jabatan</th>
										<th class='text-center'>Posisi Ttd</th>
										<th class='text-center'>Status</th>
										<th class='text-center'>Timestamp</th>
									</tr>
								</thead>
								<tbody>
                                    <?php $no=0; foreach($histori_ttd as $row){
                                        $no++;
                                    ?>
                                    <tr>
                                        <td><?= $no?></td>
                                        <td><?= $row->nama_unit?></td>
                                        <td><?= $row->nama?></td>
                                        <td><?= $row->jabatan?></td>
                                        <td><?= $row->type?></td>
                                        <td><?= $row->status=='1'?'Aktif':'Tidak aktif'?></td>
                                        <td>
                                            <?php
                                            $time='Created: '.$row->created;
                                            if($row->updated!=null){
                                                $time.='<br>Updated: '.$row->updated;
                                            }
                                            if($row->deleted!=null){
                                                $time.='<br>Deleted: '.$row->deleted;
                                            }
                                            echo $time;
                                            ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
								</tbody>
							</table>
						</div>
				
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
        <script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
        <script>
            $('.select2').select2();
        </script>