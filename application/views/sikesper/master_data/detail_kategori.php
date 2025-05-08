
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_ubah">Sub Kategori <?php echo ucwords($jenis) ?> - <?php echo $nama_kategori ?></h4>
							</div>
							<div class="modal-body">
								<?php foreach ($kategori_obat as $obat) { ?>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-5">
											<label>No. Kode</label>
										</div>
										<div class="col-lg-7">
											<label><b><?= $obat['nama_kategori'] ?></b></label>
										</div>
										<div id="warning_kode_ubah" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<?php } ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
							</div>
						</div>