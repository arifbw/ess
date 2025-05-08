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
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					if($akses["tambah"]){
				?>

						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_tambah;?>">
										<div class="panel-body">
											<form role="form" action="" id="formulir_tambah" method="post">
												<input type="hidden" name="aksi" value="tambah"/>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Kelompok Modul</label>
														</div>
														<div class="col-lg-7">
															<div class="form-group">
																<select class="form-control" name="kelompok_modul">
																	<option value=''>--- Pilih Kelompok Modul ---</option>
																	<?php
																		for($i=0;$i<count($daftar_kelompok_modul);$i++){
																			echo "<option value='".$daftar_kelompok_modul[$i]["id"]."'>".$daftar_kelompok_modul[$i]["nama"]."</option>";
																		}
																	?>
																</select>
															</div>
														</div>
														<div id="warning_kelompok_modul" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Nama <?php echo $judul;?></label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="nama" value="<?php echo $nama;?>" placeholder="Nama <?php echo $judul;?>">
														</div>
														<div id="warning_nama" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>URL</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="url" value="<?php echo $url;?>" placeholder="URL">
														</div>
														<div id="warning_url" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Icon</label>
														</div>
														<div class="col-lg-7">
															<input type='hidden' id='icon' name='icon' value=''/>
															<i id='gambar_icon'></i>
															<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_icon' onclick='set_modal("tambah");return false;'>Pilih Icon</button>
														</div>
														<div id="warning_icon" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Status</label>
														</div>
														<div class="col-lg-7">
															<div class="radio">
																<label>
																	<?php
																		if(strcmp($status,"1")==0){
																			$checked="checked='checked'";
																		}
																		else{
																			$checked="";
																		}
																	?>
																	<input type="radio" name="status" id="status_tambah_aktif" value="aktif" <?php echo $checked;?>>Aktif
																</label>
																<label>
																	<?php
																		if(strcmp($status,"0")==0){
																			$checked="checked='checked'";
																		}
																		else{
																			$checked="";
																		}
																	?>
																	<input type="radio" name="status" id="status_tambah_non_aktif" value="non aktif" <?php echo $checked;?>>Non Aktif
																</label>
															</div>
														</div>
														<div id="warning_status" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12 text-center">
														<button type="submit" class="btn btn-primary" onclick="return cek_simpan_tambah()">Simpan</button>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- /.col-lg-12 -->
						</div>
						<!-- /.row -->
				<?php
					}
					
					if($this->akses["lihat"]){
				?>

						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_modul">
								<thead>
									<tr>
										<th class='text-center' style='max-width:100px;'>Kelompok Modul</th>
										<th class='text-center'>Nama</th>
										<th class='text-center'>URL</th>
										<th class='text-center'>Icon</th>
										<th class='text-center'>Status</th>
										<?php
											if($akses["ubah"] or $akses["lihat log"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_modul);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td>".$daftar_modul[$i]["nama_kelompok_modul"]."</td>";
												echo "<td>".$daftar_modul[$i]["nama"]."</td>";
												echo "<td><a href='".base_url($daftar_modul[$i]["url"])."'>".$daftar_modul[$i]["url"]."</a></td>";
												echo "<td class='text-center'><i class='fa ".$daftar_modul[$i]["icon"]." fa-fw'></i></td>";
												echo "<td class='text-center'>";
													if((int)$daftar_modul[$i]["status"]==1){
														echo "Aktif";
													}
													else if((int)$daftar_modul[$i]["status"]==0){
														echo "Non Aktif";
													}
												echo "</td>";
												if($akses["ubah"] or $akses["lihat log"]){
													echo "<td class='text-center'>";
														if($akses["ubah"]){
															echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button> ";
														}
														if($akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_modul[$i]["nama_kelompok_modul"]." - ".$daftar_modul[$i]["nama"]."\",".$daftar_modul[$i]["id"].")'>Lihat Log</button>";
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
					
					if($akses["ubah"]){
				?>
				
						<!-- Modal -->
						<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_ubah" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="aksi" value="ubah"/>
											<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Kelompok Modul</label>
														</div>
														<div class="col-lg-5">
															<div class="form-group">
																<input type="hidden" name="kelompok_modul" value="">
																<select class="form-control" name="kelompok_modul_ubah">
																	<option value=''>--- Pilih Kelompok Modul ---</option>
																	<?php
																		for($i=0;$i<count($daftar_kelompok_modul);$i++){
																			echo "<option value='".$daftar_kelompok_modul[$i]["id"]."'>".$daftar_kelompok_modul[$i]["nama"]."</option>";
																		}
																	?>
																</select>
															</div>
														</div>
														<div id="warning_kelompok_modul_ubah" class="col-lg-4 text-danger"></div>
													</div>
												</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Nama <?php echo $judul;?></label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="nama" value="">
														<input class="form-control" name="nama_ubah" value="" placeholder="Nama <?php echo $judul;?>">
													</div>
													<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>URL</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="url" value="">
														<input class="form-control" name="url_ubah" value="" placeholder="URL">
													</div>
													<div id="warning_url_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Icon</label>
													</div>
													<div class="col-lg-5">
														<input type='hidden' id='icon_awal' name='icon' value=''/>
														<input type='hidden' id='icon_ubah' name='icon_ubah' value=''/>
														<i id='gambar_icon_ubah'></i>
														<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_icon' onclick='set_modal("ubah");return false;'>Pilih Icon</button>
													</div>
													<div id="warning_icon_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Status</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="status" value="">
														<div class="radio">
															<label>
																<input type="radio" name="status_ubah" id="status_ubah_aktif" value="aktif">Aktif
															</label>
															<label>
																<input type="radio" name="status_ubah" id="status_ubah_non_aktif" value="non aktif">Non Aktif
															</label>
														</div>
													</div>
													<div id="warning_status_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary" onclick="return cek_simpan_ubah()">Simpan</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
						
						<!-- Modal -->
						<div class="modal fade" id="modal_icon" tabindex="-1" role="dialog" aria-labelledby="label_modal_icon" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="label_modal_icon">Pilih Icon</h4>
									</div>
									<div class="modal-body"  style="height:300px;overflow-y:scroll;">
										<?php
											$fonts = array("fa-glass", "fa-music", "fa-search", "fa-envelope-o", "fa-heart", "fa-star", "fa-star-o", "fa-user", "fa-film", "fa-th-large", "fa-th", "fa-th-list", "fa-check", "fa-remove", "fa-search-plus", "fa-search-minus", "fa-power-off", "fa-signal", "fa-gear", "fa-trash-o", "fa-home", "fa-file-o", "fa-clock-o", "fa-road", "fa-download", "fa-arrow-circle-o-down", "fa-arrow-circle-o-up", "fa-inbox", "fa-play-circle-o", "fa-rotate-right", "fa-refresh", "fa-list-alt", "fa-lock", "fa-flag", "fa-headphones", "fa-volume-off", "fa-volume-down", "fa-volume-up", "fa-qrcode", "fa-barcode", "fa-tag", "fa-tags", "fa-book", "fa-bookmark", "fa-print", "fa-camera", "fa-font", "fa-bold", "fa-italic", "fa-text-height", "fa-text-width", "fa-align-left", "fa-align-center", "fa-align-right", "fa-align-justify", "fa-list", "fa-dedent", "fa-indent", "fa-video-camera", "fa-photo", "fa-pencil", "fa-map-marker", "fa-adjust", "fa-tint", "fa-edit", "fa-share-square-o", "fa-check-square-o", "fa-arrows", "fa-step-backward", "fa-fast-backward", "fa-backward", "fa-play", "fa-pause", "fa-stop", "fa-forward", "fa-fast-forward", "fa-step-forward", "fa-eject", "fa-chevron-left", "fa-chevron-right", "fa-plus-circle", "fa-minus-circle", "fa-times-circle", "fa-check-circle", "fa-question-circle", "fa-info-circle", "fa-crosshairs", "fa-times-circle-o", "fa-check-circle-o", "fa-ban", "fa-arrow-left", "fa-arrow-right", "fa-arrow-up", "fa-arrow-down", "fa-mail-forward", "fa-expand", "fa-compress", "fa-plus", "fa-minus", "fa-asterisk", "fa-exclamation-circle", "fa-gift", "fa-leaf", "fa-fire", "fa-eye", "fa-eye-slash", "fa-warning", "fa-plane", "fa-calendar", "fa-random", "fa-comment", "fa-magnet", "fa-chevron-up", "fa-chevron-down", "fa-retweet", "fa-shopping-cart", "fa-folder", "fa-folder-open", "fa-arrows-v", "fa-arrows-h", "fa-bar-chart-o", "fa-twitter-square", "fa-facebook-square", "fa-camera-retro", "fa-key", "fa-gears", "fa-comments", "fa-thumbs-o-up", "fa-thumbs-o-down", "fa-star-half", "fa-heart-o", "fa-sign-out", "fa-linkedin-square", "fa-thumb-tack", "fa-external-link", "fa-sign-in", "fa-trophy", "fa-github-square", "fa-upload", "fa-lemon-o", "fa-phone", "fa-square-o", "fa-bookmark-o", "fa-phone-square", "fa-twitter", "fa-facebook-f", "fa-github", "fa-unlock", "fa-credit-card", "fa-feed", "fa-hdd-o", "fa-bullhorn", "fa-bell", "fa-certificate", "fa-hand-o-right", "fa-hand-o-left", "fa-hand-o-up", "fa-hand-o-down", "fa-arrow-circle-left", "fa-arrow-circle-right", "fa-arrow-circle-up", "fa-arrow-circle-down", "fa-globe", "fa-wrench", "fa-tasks", "fa-filter", "fa-briefcase", "fa-arrows-alt", "fa-group", "fa-chain", "fa-cloud", "fa-flask", "fa-cut", "fa-copy", "fa-paperclip", "fa-save", "fa-square", "fa-navicon", "fa-list-ul", "fa-list-ol", "fa-strikethrough", "fa-underline", "fa-table", "fa-magic", "fa-truck", "fa-pinterest", "fa-pinterest-square", "fa-google-plus-square", "fa-google-plus", "fa-money", "fa-caret-down", "fa-caret-up", "fa-caret-left", "fa-caret-right", "fa-columns", "fa-unsorted", "fa-sort-down", "fa-sort-up", "fa-envelope", "fa-linkedin", "fa-rotate-left", "fa-legal", "fa-dashboard", "fa-comment-o", "fa-comments-o", "fa-flash", "fa-sitemap", "fa-umbrella", "fa-paste", "fa-lightbulb-o", "fa-exchange", "fa-cloud-download", "fa-cloud-upload", "fa-user-md", "fa-stethoscope", "fa-suitcase", "fa-bell-o", "fa-coffee", "fa-cutlery", "fa-file-text-o", "fa-building-o", "fa-hospital-o", "fa-ambulance", "fa-medkit", "fa-fighter-jet", "fa-beer", "fa-h-square", "fa-plus-square", "fa-angle-double-left", "fa-angle-double-right", "fa-angle-double-up", "fa-angle-double-down", "fa-angle-left", "fa-angle-right", "fa-angle-up", "fa-angle-down", "fa-desktop", "fa-laptop", "fa-tablet", "fa-mobile-phone", "fa-circle-o", "fa-quote-left", "fa-quote-right", "fa-spinner", "fa-circle", "fa-mail-reply", "fa-github-alt", "fa-folder-o", "fa-folder-open-o", "fa-smile-o", "fa-frown-o", "fa-meh-o", "fa-gamepad", "fa-keyboard-o", "fa-flag-o", "fa-flag-checkered", "fa-terminal", "fa-code", "fa-mail-reply-all", "fa-star-half-empty", "fa-location-arrow", "fa-crop", "fa-code-fork", "fa-unlink", "fa-question", "fa-info", "fa-exclamation", "fa-superscript", "fa-subscript", "fa-eraser", "fa-puzzle-piece", "fa-microphone", "fa-microphone-slash", "fa-shield", "fa-calendar-o", "fa-fire-extinguisher", "fa-rocket", "fa-maxcdn", "fa-chevron-circle-left", "fa-chevron-circle-right", "fa-chevron-circle-up", "fa-chevron-circle-down", "fa-html5", "fa-css3", "fa-anchor", "fa-unlock-alt", "fa-bullseye", "fa-ellipsis-h", "fa-ellipsis-v", "fa-rss-square", "fa-play-circle", "fa-ticket", "fa-minus-square", "fa-minus-square-o", "fa-level-up", "fa-level-down", "fa-check-square", "fa-pencil-square", "fa-external-link-square", "fa-share-square", "fa-compass", "fa-toggle-down", "fa-toggle-up", "fa-toggle-right", "fa-euro", "fa-gbp", "fa-dollar", "fa-rupee", "fa-cny", "fa-ruble", "fa-won", "fa-bitcoin", "fa-file", "fa-file-text", "fa-sort-alpha-asc", "fa-sort-alpha-desc", "fa-sort-amount-asc", "fa-sort-amount-desc", "fa-sort-numeric-asc", "fa-sort-numeric-desc", "fa-thumbs-up", "fa-thumbs-down", "fa-youtube-square", "fa-youtube", "fa-xing", "fa-xing-square", "fa-youtube-play", "fa-dropbox", "fa-stack-overflow", "fa-instagram", "fa-flickr", "fa-adn", "fa-bitbucket", "fa-bitbucket-square", "fa-tumblr", "fa-tumblr-square", "fa-long-arrow-down", "fa-long-arrow-up", "fa-long-arrow-left", "fa-long-arrow-right", "fa-apple", "fa-windows", "fa-android", "fa-linux", "fa-dribbble", "fa-skype", "fa-foursquare", "fa-trello", "fa-female", "fa-male", "fa-gittip", "fa-sun-o", "fa-moon-o", "fa-archive", "fa-bug", "fa-vk", "fa-weibo", "fa-renren", "fa-pagelines", "fa-stack-exchange", "fa-arrow-circle-o-right", "fa-arrow-circle-o-left", "fa-toggle-left", "fa-dot-circle-o", "fa-wheelchair", "fa-vimeo-square", "fa-turkish-lira", "fa-plus-square-o", "fa-space-shuttle", "fa-slack", "fa-envelope-square", "fa-wordpress", "fa-openid", "fa-institution", "fa-mortar-board", "fa-yahoo", "fa-google", "fa-reddit", "fa-reddit-square", "fa-stumbleupon-circle", "fa-stumbleupon", "fa-delicious", "fa-digg", "fa-pied-piper-pp", "fa-pied-piper-alt", "fa-drupal", "fa-joomla", "fa-language", "fa-fax", "fa-building", "fa-child", "fa-paw", "fa-spoon", "fa-cube", "fa-cubes", "fa-behance", "fa-behance-square", "fa-steam", "fa-steam-square", "fa-recycle", "fa-automobile", "fa-cab", "fa-tree", "fa-spotify", "fa-deviantart", "fa-soundcloud", "fa-database", "fa-file-pdf-o", "fa-file-word-o", "fa-file-excel-o", "fa-file-powerpoint-o", "fa-file-photo-o", "fa-file-zip-o", "fa-file-sound-o", "fa-file-movie-o", "fa-file-code-o", "fa-vine", "fa-codepen", "fa-jsfiddle", "fa-life-bouy", "fa-circle-o-notch", "fa-ra", "fa-ge", "fa-git-square", "fa-git", "fa-y-combinator-square", "fa-tencent-weibo", "fa-qq", "fa-wechat", "fa-send", "fa-send-o", "fa-history", "fa-circle-thin", "fa-header", "fa-paragraph", "fa-sliders", "fa-share-alt", "fa-share-alt-square", "fa-bomb", "fa-soccer-ball-o", "fa-tty", "fa-binoculars", "fa-plug", "fa-slideshare", "fa-twitch", "fa-yelp", "fa-newspaper-o", "fa-wifi", "fa-calculator", "fa-paypal", "fa-google-wallet", "fa-cc-visa", "fa-cc-mastercard", "fa-cc-discover", "fa-cc-amex", "fa-cc-paypal", "fa-cc-stripe", "fa-bell-slash", "fa-bell-slash-o", "fa-trash", "fa-copyright", "fa-at", "fa-eyedropper", "fa-paint-brush", "fa-birthday-cake", "fa-area-chart", "fa-pie-chart", "fa-line-chart", "fa-lastfm", "fa-lastfm-square", "fa-toggle-off", "fa-toggle-on", "fa-bicycle", "fa-bus", "fa-ioxhost", "fa-angellist", "fa-cc", "fa-shekel", "fa-meanpath", "fa-buysellads", "fa-connectdevelop", "fa-dashcube", "fa-forumbee", "fa-leanpub", "fa-sellsy", "fa-shirtsinbulk", "fa-simplybuilt", "fa-skyatlas", "fa-cart-plus", "fa-cart-arrow-down", "fa-diamond", "fa-ship", "fa-user-secret", "fa-motorcycle", "fa-street-view", "fa-heartbeat", "fa-venus", "fa-mars", "fa-mercury", "fa-intersex", "fa-transgender-alt", "fa-venus-double", "fa-mars-double", "fa-venus-mars", "fa-mars-stroke", "fa-mars-stroke-v", "fa-mars-stroke-h", "fa-neuter", "fa-genderless", "fa-facebook-official", "fa-pinterest-p", "fa-whatsapp", "fa-server", "fa-user-plus", "fa-user-times", "fa-hotel", "fa-viacoin", "fa-train", "fa-subway", "fa-medium", "fa-yc", "fa-optin-monster", "fa-opencart", "fa-expeditedssl", "fa-battery-4", "fa-battery-3", "fa-battery-2", "fa-battery-1", "fa-battery-0", "fa-mouse-pointer", "fa-i-cursor", "fa-object-group", "fa-object-ungroup", "fa-sticky-note", "fa-sticky-note-o", "fa-cc-jcb", "fa-cc-diners-club", "fa-clone", "fa-balance-scale", "fa-hourglass-o", "fa-hourglass-1", "fa-hourglass-2", "fa-hourglass-3", "fa-hourglass", "fa-hand-grab-o", "fa-hand-stop-o", "fa-hand-scissors-o", "fa-hand-lizard-o", "fa-hand-spock-o", "fa-hand-pointer-o", "fa-hand-peace-o", "fa-trademark", "fa-registered", "fa-creative-commons", "fa-gg", "fa-gg-circle", "fa-tripadvisor", "fa-odnoklassniki", "fa-odnoklassniki-square", "fa-get-pocket", "fa-wikipedia-w", "fa-safari", "fa-chrome", "fa-firefox", "fa-opera", "fa-internet-explorer", "fa-tv", "fa-contao", "fa-500px", "fa-amazon", "fa-calendar-plus-o", "fa-calendar-minus-o", "fa-calendar-times-o", "fa-calendar-check-o", "fa-industry", "fa-map-pin", "fa-map-signs", "fa-map-o", "fa-map", "fa-commenting", "fa-commenting-o", "fa-houzz", "fa-vimeo", "fa-black-tie", "fa-fonticons", "fa-reddit-alien", "fa-edge", "fa-credit-card-alt", "fa-codiepie", "fa-modx", "fa-fort-awesome", "fa-usb", "fa-product-hunt", "fa-mixcloud", "fa-scribd", "fa-pause-circle", "fa-pause-circle-o", "fa-stop-circle", "fa-stop-circle-o", "fa-shopping-bag", "fa-shopping-basket", "fa-hashtag", "fa-bluetooth", "fa-bluetooth-b", "fa-percent", "fa-gitlab", "fa-wpbeginner", "fa-wpforms", "fa-envira", "fa-universal-access", "fa-wheelchair-alt", "fa-question-circle-o", "fa-blind", "fa-audio-description", "fa-volume-control-phone", "fa-braille", "fa-assistive-listening-systems", "fa-asl-interpreting", "fa-deafness", "fa-glide", "fa-glide-g", "fa-signing", "fa-low-vision", "fa-viadeo", "fa-viadeo-square", "fa-snapchat", "fa-snapchat-ghost", "fa-snapchat-square", "fa-pied-piper", "fa-first-order", "fa-yoast", "fa-themeisle", "fa-google-plus-circle", "fa-fa");

											sort($fonts);
											
											foreach($fonts as $font){
												echo "<label title='$font'><input type='radio' name='pilih_icon' id='icon_$font' name='$font' value='$font' onchange='icon_pilihan(this)'><i class='fa $font fa-fw'></i></label> ";
											}
											echo "<input type='hidden' id='pilihan_icon'/>";
											echo "<input type='hidden' id='modal_aksi'/>";
										?>
									</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-primary" onclick="pilih_icon()" data-dismiss="modal">Pilih</button>
										<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
									</div>
								
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->