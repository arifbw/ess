		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul; ?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php
				if (!empty($success)) {
				?>
					<div class="alert alert-success alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<?php echo $success; ?>
					</div>
				<?php
				}
				if (!empty($warning)) {
				?>
					<div class="alert alert-danger alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<?php echo $warning; ?>
					</div>
				<?php
				}

				if ($akses["lihat"]) {
				?>

					<div class="row">
						<form role="form" action="" id="filter_tahun" method="post">
							<input type="hidden" name="aksi" value="filter_tahun" />
							<div class='col-lg-1'>Tahun</div>
							<div class='col-lg-2'>
								<select class="form-control select2" name="tahun" id="tahun" onchange='this.form.submit()'> // onchange="refresh_table_serverside()"
									<option value='all'>Semua Tahun</option>
									<?php for ($i = 2022; $i <= date('Y'); $i++) {
										echo "<option value='" . $i . "' " . ($i == $tahun ? "selected" : "") . ">" . $i . "</option>";
									} ?>
									value="<?php echo $link; ?>"
								</select>
								<noscript><input type="submit" value="Submit"></noscript>
							</div>
						</form>
					</div>

					<div class="row">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_manajemen_poin">
							<thead>
								<tr>
									<th class='text-center'>No Pokok</th>
									<th class='text-center'>Nama</th>
									<th class='text-center'>Poin</th>
									<!-- <th class='text-center'>Status</th> -->
									<?php
									if ($akses["riwayat"]) {
										echo "<th class='text-center'>Aksi</th>";
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?php
								for ($i = 0; $i < count($daftar_manajemen_poin); $i++) {
									if ($i % 2 == 0) {
										$class = "even";
									} else {
										$class = "odd";
									}

									echo "<tr class='$class'>";
									echo "<td class='text-center' style='width: 120px;'>" . $daftar_manajemen_poin[$i]["np"] . "</td>";
									echo "<td>" . $daftar_manajemen_poin[$i]["nama"] . "</td>";
									echo "<td class='text-center' style='width: 120px;'>" . $daftar_manajemen_poin[$i]["poin"] . "</td>";
									if ($akses["riwayat"]) {
										echo "<td class='text-center'>";
										if ($akses["riwayat"]) {
											echo "<a href='" . base_url($url_riwayat) . "/" . $daftar_manajemen_poin[$i]["tahun"] . "/" . $daftar_manajemen_poin[$i]["np"] . "/" . $daftar_manajemen_poin[$i]["nama"] . "' class='btn btn-primary btn-xs'>Riwayat</a> ";
										}
										echo "</td>";
									}
									echo "</tr>";
								}
								?>
							</tbody>
						</table>
						<!-- /.table-responsive -->
					</div>
				<?php
				}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
