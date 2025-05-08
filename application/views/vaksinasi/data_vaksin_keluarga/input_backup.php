<div id="alert-formulir-ubah"></div>
<form role="form" id="formulir-ubah" method="post" onsubmit="return false;">

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>NP Karyawan</label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_np_karyawan" id="ubah_np_karyawan" placeholder="NP Karyawan" readonly required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Nama Karyawan</label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_nama_karyawan" id="ubah_nama_karyawan" placeholder="Nama Karyawan" readonly required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Tipe Keluarga</label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_tipe_keluarga" id="ubah_tipe_keluarga" placeholder="Tipe Keluarga" readonly required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Nama Lengkap</label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_nama_lengkap" id="ubah_nama_lengkap" placeholder="Nama Lengkap" readonly required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Tanggal lahir</label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_tanggal_lahir" id="ubah_tanggal_lahir" placeholder="Tanggal lahir" readonly required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Usia per 2021</label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_usia" id="ubah_usia" placeholder="Usia per 2021" readonly required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>NIK <code>*</code></label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_nik" id="ubah_nik" placeholder="NIK" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Email <code>*</code></label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_email" id="ubah_email" placeholder="Email" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>No. HP <code>*</code></label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control" name="ubah_no_hp" id="ubah_no_hp" placeholder="No. HP" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Status Perkawinan <code>*</code></label>
            </div>
            <div class="col-lg-8">
                <select class="form-control" name="ubah_status_kawin" id="ubah_status_kawin" placeholder="Status Perkawinan" required>
                    <option value="BELUM KAWIN">BELUM KAWIN</option>
                    <option value="KAWIN">KAWIN</option>
                    <option value="CERAI MATI">CERAI MATI</option>
                    <option value="CERAI HIDUP">CERAI HIDUP</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Alamat lengkap sesuai KTP <code>*</code></label>
            </div>
            <div class="col-lg-8">
                <textarea style="max-width: 100%;" class="form-control" name="ubah_alamat" id="ubah_alamat" placeholder="Alamat lengkap sesuai KTP" required></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-lg-4">
                <label>Status Vaksin <code>*</code></label>
            </div>
            <div class="col-lg-8">
                <!-- <div class="col-6"> -->
                    <div class="checkbox">
                        <label><input style="width: auto;" type="radio" class="form-control" name="ubah_status_vaksin" value="1" required> Sudah</label>
                    </div>
                <!-- </div>
                <div class="col-6"> -->
                    <div class="checkbox">
                        <label><input style="width: auto;" type="radio" class="form-control" name="ubah_status_vaksin" value="2" required> Belum</label>
                    </div>
                <!-- </div> -->
            </div>
        </div>
    </div>

    <div id="form-klinik" style="display: none;">
        <div class="row">
            <div class="form-group">
                <div class="col-lg-4">
                    <label>Provinsi <code>*</code></label>
                </div>
                <div class="col-lg-8">
                    <select class="form-control" name="ubah_alamat_kode_prov" id="ubah_alamat_kode_prov" placeholder="Provinsi" required></select>
                </div>
                <input type="hidden" name="ubah_alamat_nama_prov" id="ubah_alamat_nama_prov" value="">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-lg-4">
                    <label>Kabupaten <code>*</code></label>
                </div>
                <div class="col-lg-8">
                    <select class="form-control" name="ubah_alamat_kode_kab" id="ubah_alamat_kode_kab" placeholder="Kabupaten" required></select>
                </div>
                <input type="hidden" name="ubah_alamat_nama_kab" id="ubah_alamat_nama_kab" value="">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-lg-4">
                    <label>Kecamatan <code>*</code></label>
                </div>
                <div class="col-lg-8">
                    <select class="form-control" name="ubah_alamat_kode_kec" id="ubah_alamat_kode_kec" placeholder="Kecamatan" required></select>
                </div>
                <input type="hidden" name="ubah_alamat_nama_kec" id="ubah_alamat_nama_kec" value="">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-lg-4">
                    <label>Kelurahan <code>*</code></label>
                </div>
                <div class="col-lg-8">
                    <select class="form-control" name="ubah_alamat_kode_kel" id="ubah_alamat_kode_kel" placeholder="Kelurahan" required></select>
                </div>
                <input type="hidden" name="ubah_alamat_nama_kel" id="ubah_alamat_nama_kel" value="">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-lg-4">
                    <label>Klinik <code>*</code></label>
                </div>
                <div class="col-lg-8">
                    <select style="width: 100%;" class="form-control" name="ubah_mst_klinik_id" id="ubah_mst_klinik_id" onchange="getAlamatKlinik()" placeholder="Klinik"></select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-lg-4">
                    <label>Alamat Klinik</label>
                </div>
                <div class="col-lg-8">
                    <textarea style="max-width: 100%;" class="form-control" id="alamat_klinik" disabled></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 text-center">
            <button type="button" class="btn btn-primary" id="btn-submit-ubah" onclick="doUbah()">Simpan</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close" id="btn-close">
                Close
            </button>
        </div>
    </div>
</form>