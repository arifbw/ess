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
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log(\"".substr($judul,strpos($judul," : ")+strlen(" : "))."\",\"".$id_menu."\")'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
				?>

				<div class="row">
					<input type='hidden' id='id_menu' value='<?php echo $id_menu;?>'/>
					<div id='pengaturan_menu'></div>
				</div>
			</div>
		

			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->