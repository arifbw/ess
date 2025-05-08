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
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
							echo "<div class='chart' id='diagram_sto'></div>";
						echo "</div>";
					}
					
					if($this->akses["lihat"]){
						echo "<script>";
							/* echo "var config = ";
								echo "{";
									echo "container: '#custom-colored',";
									echo "nodeAlign: 'BOTTOM',";
									echo "connectors: {";
										echo "type: 'step'";
									echo "},";
									echo "node: {";
										echo "HTMLclass: 'nodeExample1'";
									echo "}";
								echo "},";
								echo "ceo = ";
									echo "{";
										echo "text: ";
											echo "{";
												echo "name: 'Mark Hill',";
												echo "title: 'Chief executive officer',";
												echo "contact: 'Tel: 01 213 123 134',";
											echo "},";
										//image: "../headshots/2.jpg"
									echo "},";

								echo "cto = ";
									echo "{";
										echo "parent: ceo,";
										echo "HTMLclass: 'light-gray',";
										echo "text:{";
											echo "name: 'Joe Linux',";
											echo "title: 'Chief Technology Officer',";
										echo "},";
										//image: "../headshots/1.jpg"
									echo "},";
									
								echo "cbo = {";
									echo "parent: ceo,";
									echo "childrenDropLevel: 2,";
									echo "HTMLclass: 'blue',";
									echo "text:{";
										echo "name: 'Linda May',";
										echo "title: 'Chief Business Officer',";
									echo "},";
									//image: "../headshots/5.jpg"
								echo "},";
										
								echo "cdo = {";
									echo "parent: ceo,";
									echo "HTMLclass: 'gray',";
									echo "text:{";
										echo "name: 'John Green',";
										echo "title: 'Chief accounting officer',";
										echo "contact: 'Tel: 01 213 123 134',";
									echo "},";
									//image: "../headshots/6.jpg"
								echo "},";
								
								echo "cio = {";
									echo "parent: cto,";
									echo "HTMLclass: 'light-gray',";
									echo "text:{";
										echo "name: 'Ron Blomquist',";
										echo "title: 'Chief Information Security Officer'";
									echo "},",
									//image: "../headshots/8.jpg"
								echo "},",
								
								echo "ciso = {";
									echo "parent: cto,";
									echo "HTMLclass: 'light-gray',";
									echo "text:{";
										echo "name: 'Michael Rubin',";
										echo "title: 'Chief Innovation Officer',";
										echo "contact: 'we@aregreat.com'";
									echo "},";
									//image: "../headshots/9.jpg"
								echo "},"; */
									/* cio2 = {
									parent: cdo,
									HTMLclass: 'gray',
									text:{
									name: "Erica Reel",
									title: "Chief Customer Officer"
									},
									link: {
									href: "http://www.google.com"
									},
									image: "../headshots/10.jpg"
									},
									ciso2 = {
									parent: cbo,
									HTMLclass: 'blue',
									text:{
									name: "Alice Lopez",
									title: "Chief Communications Officer"
									},
									image: "../headshots/7.jpg"
									},
									ciso3 = {
									parent: cbo,
									HTMLclass: 'blue',
									text:{
									name: "Mary Johnson",
									title: "Chief Brand Officer"
									},
									image: "../headshots/4.jpg"
									},
									ciso4 = {
									parent: cbo,
									HTMLclass: 'blue',
									text:{
									name: "Kirk Douglas",
									title: "Chief Business Development Officer"
									},
									image: "../headshots/11.jpg"
									},*/

									echo "chart_config = [
									config,
									ceo,cto,cbo,
									cdo,cio,ciso,
									cio2,ciso2,ciso3,ciso4
									];";
							echo "new Treant( chart_config );";
						echo "</script>";
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
	