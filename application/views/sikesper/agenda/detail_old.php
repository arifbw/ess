<form role="<?= $this->session->userdata('grup') == '5' ? 'pengguna' : 'admin' ?>" data-agenda="<?= $agenda; ?>" data-kry="<?= $this->session->userdata('no_pokok'); ?>" id="form-detail">
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Nama</label>
			</div>
			<div class="col-lg-7">
				<input class="form-control" name="agenda" value="<?= @$data_agenda->agenda; ?>" readonly />
			</div>
			<div id="warning_agenda" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Deskripsi</label>
			</div>
			<div class="col-lg-7">
                <textarea class="form-control" readonly><?= @$data_agenda->deskripsi; ?></textarea>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Waktu dan Tanggal</label>
			</div>
			<div class="col-lg-7">
				<div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" value="<?= !empty($data_agenda) ? date('m/d/Y', strtotime($data_agenda->tanggal)).' '.@$data_agenda->jam : ''; ?>" readonly />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
			</div>
			<div id="warning_tanggal" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Waktu Mulai</label>
			</div>
			<div class="col-lg-7">
				<div class='input-group date' id='waktu-mulai'>
                    <input type='text' class="form-control" value="<?= @$data_agenda->waktu_mulai; ?>" readonly />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
			</div>
			<div id="warning_mulai" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Waktu Selesai</label>
			</div>
			<div class="col-lg-7">
				<div class='input-group date' id='waktu-selesai'>
                    <input type='text' class="form-control" value="<?= @$data_agenda->waktu_selesai; ?>" readonly />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
			</div>
			<div id="warning_selesai" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Kuota</label>
			</div>
			<div class="col-lg-7">
				<input type="number" class="form-control" value="<?= @$data_agenda->kuota; ?>" readonly>
			</div>
			<div id="warning_kuota" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Nama Lokasi</label>
			</div>
			<div class="col-lg-7">
				<input class="form-control" type="text" value="<?= @$data_agenda->nama_lokasi; ?>" readonly />
			</div>
			<div id="warning_nama_lokasi" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Provinsi</label>
			</div>
			<div class="col-lg-7">
				<input class="form-control" type="text" value="<?= $data_agenda->nama_provinsi; ?>" readonly />
			</div>
			<div id="warning_provinsi" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Kabupaten</label>
			</div>
			<div class="col-lg-7">
				<input class="form-control" type="text" value="<?= $data_agenda->nama_kabupaten; ?>" readonly />
			</div>
			<div id="warning_kabupaten" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Alamat</label>
			</div>
			<div class="col-lg-7">
				<textarea class="form-control" name="alamat" readonly><?= @$data_agenda->alamat ?></textarea>
			</div>
			<div id="warning_alamat" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Longitude</label>
			</div>
			<div class="col-lg-10">
				<input id="tambah-longitude" class="form-control" type="text" value="<?= @$data_agenda->longitude; ?>" readonly />
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Latitude</label>
			</div>
			<div class="col-lg-10">
				<input id="tambah-latitude" class="form-control" type="text" value="<?= @$data_agenda->latitude; ?>" readonly />
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Status</label>
			</div>
			<div class="col-lg-7">
				<input class="form-control" type="text" value="<?= @$data_agenda->status == 1 ? 'Aktif' : 'Non Aktif' ?>" readonly>
			</div>
			<div id="warning_status" class="col-lg-3 text-danger"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-2">
				<label>Gambar</label>
			</div>
			<div class="col-lg-7">
				<img src="<?= base_url(); ?>uploads/images/sikesper/agenda/<?= $data_agenda->image; ?>" width="100" height="100" alt="Gambar Belum Diupload">
			</div>
			<div id="warning_image" class="col-lg-3 text-danger"></div>
		</div>
	</div>
</form>
