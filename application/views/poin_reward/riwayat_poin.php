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
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_riwayat_poin">
							<thead>
								<tr>
									<th class='text-center'>Sumber</th>
									<th class='text-center'>Nama</th>
									<th class='text-center'>Tipe</th>
									<th class='text-center'>Poin</th>
									<th class='text-center'>Poin Awal</th>
									<th class='text-center'>Poin Hasil</th>
									<th class='text-center'>Dibuat pada</th>
								</tr>
							</thead>
							<tbody>
								<?php
								for ($i = 0; $i < count($daftar_riwayat_poin); $i++) {
									if ($i % 2 == 0) {
										$class = "even";
									} else {
										$class = "odd";
									}

									echo "<tr class='$class'>";
									echo "<td class='text-center' style='width: 120px;'>" . $daftar_riwayat_poin[$i]["sumber"] . "</td>";
									echo "<td>" . $daftar_riwayat_poin[$i]["nama"] . "</td>";
									echo "<td class='text-center' style='width: 120px;'>" . $daftar_riwayat_poin[$i]["tipe"] . "</td>";
									echo "<td class='text-center' style='width: 120px;'>" . $daftar_riwayat_poin[$i]["poin"] . "</td>";
									echo "<td class='text-center' style='width: 120px;'>" . $daftar_riwayat_poin[$i]["poin_awal"] . "</td>";
									echo "<td class='text-center' style='width: 120px;'>" . $daftar_riwayat_poin[$i]["poin_hasil"] . "</td>";
									echo "<td class='text-center'>" . datetime_indo($daftar_riwayat_poin[$i]["created_at"]) . "</td>";
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
