<?php
if (strcmp($function, "salin") == 0) {
	echo $id . "|" . $nama . "|" . $konten . "|" . $link . "|" . $poin . "|" . $durasi_baca . "|" . $start_date . "|" . $end_date . "|" . $status;
} else if (strcmp($function, "ubah") == 0) {
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Nama $judul</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='id_manajemen_survey' value='$id'/>";
	echo "<input class='form-control' type='hidden' name='nama' value='$nama'/>";
	echo "<input class='form-control' name='nama_ubah' value='$nama' placeholder='Nama $judul'>";
	echo "</div>";
	echo "<div id='warning_nama_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Konten</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='konten' value='$konten'/>";
	echo "<input class='form-control' name='konten_ubah' value='$konten' placeholder='Konten'/>";
	echo "</div>";
	echo "<div id='warning_konten_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Link</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='link' value='$link'/>";
	echo "<input class='form-control' name='link_ubah' value='$link' placeholder='Link'/>";
	echo "</div>";
	echo "<div id='warning_link_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Poin</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='poin' value='$poin'/>";
	echo "<input class='form-control' name='poin_ubah' value='$poin' placeholder='Poin'/>";
	echo "</div>";
	echo "<div id='warning_poin_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Durasi Baca (Detik)</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='durasi_baca' value='$durasi_baca'/>";
	echo "<input class='form-control' name='durasi_baca_ubah' value='$durasi_baca' placeholder='Durasi Baca'/>";
	echo "</div>";
	echo "<div id='warning_durasi_baca_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Tgl Awal</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='start_date' value='$start_date'/>";
	echo "<input type='date' class='form-control' name='start_date_ubah' value='$start_date' placeholder='Tgl Awal'/>";
	echo "</div>";
	echo "<div id='warning_start_date_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Tgl Akhir</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='end_date' value='$end_date'/>";
	echo "<input type='date' class='form-control' name='end_date_ubah' value='$end_date' placeholder='Tgl Akhir'/>";
	echo "</div>";
	echo "<div id='warning_end_date_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Upload Gambar <code>(jpg|png|jpeg)</code></label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<input class='form-control' type='hidden' name='gambar' value='$gambar'/>";
	echo "<input type='file' class='form-control' accept='.png,.jpg,.jpeg' name='gambar_ubah' id='gambar_ubah' value='$gambar'/>";
	echo "</div>";
	echo "<div id='warning_gambar_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='form-group'>";
	echo "<div class='row'>";
	echo	"<div class='col-lg-4'>";
	echo		"<label>NP Tergabung</label>";
	echo	"</div>";
	echo	"<div class='col-lg-5'>";
	echo		"<select id='np_tergabung' name='np_tergabung[]' multiple style='width: 100%;' class='form-control'>";
	foreach ($ref_karyawan as $item):
		echo "<option value='" . $item['no_pokok'] . "'" . isset($np_tergabung) && in_array($item['no_pokok'], explode(',', $np_tergabung)) ? 'selected' : null . ">" . $item['no_pokok'] . "-" . $item['nama'] . "</option>";
	endforeach;
	echo		"</select>";
	echo	"</div>";
	echo	"<div id='warning_np_tergabung' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div class='form-group'>";
	echo "<div class='col-lg-4'>";
	echo "<label>Status</label>";
	echo "</div>";
	echo "<div class='col-lg-5'>";
	echo "<label class='radio-inline'>";
	echo "<input type='hidden' name='status' value='$status'>";
	if (strcmp($status, "1") == 0) {
		$checked = "checked='checked'";
	} else {
		$checked = "";
	}

	echo "<input type='radio' name='status_ubah' id='status_tambah_aktif' value='aktif' $checked>Aktif ";
	echo "</label>";
	echo "<label class='radio-inline'>";
	if (strcmp($status, "0") == 0) {
		$checked = "checked='checked'";
	} else {
		$checked = "";
	}
	echo "<input type='radio' name='status_ubah' id='status_tambah_non_aktif' value='non aktif' $checked>Non Aktif";
	echo "</label>";
	echo "</div>";
	echo "<div id='warning_status_ubah' class='col-lg-3 text-danger'></div>";
	echo "</div>";
	echo "</div>";
}
	/* End of file isi_menu.php */
	/* Location: ./application/controllers/administrator/ajax/isi_menu.php */
