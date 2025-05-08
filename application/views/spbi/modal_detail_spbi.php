<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="label-modal-detail" aria-hidden="true" data-toggle="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="label-modal-detail">Detail</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table>
                            <tr>
                                <td>Nomor</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="nomor_surat"></td>
                            </tr>
                            <tr>
                                <td>NP</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="np_karyawan"></td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="nama"></td>
                            </tr>
                            <tr>
                                <td>Unit Kerja</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="nama_unit"></td>
                            </tr>
                            <tr>
                                <td>Milik</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="milik"></td>
                            </tr>
                            <tr>
                                <td>Maksud</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="maksud"></td>
                            </tr>
                            <tr>
                                <td>Dikirim ke</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="dikirim_ke"></td>
                            </tr>
                            <tr>
                                <td>Keluar Tanggal</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="keluar_tanggal"></td>
                            </tr>
                            <tr>
                                <td>Pos Keluar yang Dilewati</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="pos_keluar"></td>
                            </tr>
                            <tr>
                                <td>Pos Masuk yang Dilewati</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="pos_masuk"></td>
                            </tr>
                            <tr>
                                <td>NP Atasan</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="approval_atasan_np"></td>
                            </tr>
                            <tr>
                                <td>Nama Atasan</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="approval_atasan_nama"></td>
                            </tr>
                            <tr>
                                <td>Jabatan Atasan</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="approval_atasan_jabatan"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <th style="width: 25%;">Jumlah</th>
                                <th style="width: 50%;">Nama Barang</th>
                                <th style="width: 25%;">Keterangan</th>
                            </thead>
                            <tbody class="barang"></tbody>
                        </table>
                    </div>
                </div>
                
                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <label>Pemeriksaan Keluar Perusahaan</label>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Approval Atasan</strong><br>
                            <p>NP: <span class="approval_atasan_np"></span></p>
                            <p>Nama: <span class="approval_atasan_nama"></span></p>
                            <p class="approval_atasan_status"></p>
                            <p class="approval_atasan_keterangan"></p>
                            <p class="approval_atasan_at"></p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Pengecek</strong><br>
                            <p>NP: <span class="pengecek1_np"></span></p>
                            <p>Nama: <span class="pengecek1_nama"></span></p>
                            <p class="pengecek1_status"></p>
                            <p class="pengecek1_keterangan"></p>
                            <p class="pengecek1_at"></p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Pengawas/Pengawal/Penyegel</strong><br>
                            <p>NP: <span class="konfirmasi_pengguna_np"></span></p>
                            <p>Nama: <span class="konfirmasi_pengguna_nama"></span></p>
                            <p class="konfirmasi_pengguna"></p>
                            <p class="konfirmasi_pengguna_keterangan"></p>
                            <p class="konfirmasi_pengguna_at"></p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>DAN/WADAN Posko</strong><br>
                            <p>NP: <span class="danposko_np"></span></p>
                            <p>Nama: <span class="danposko_nama"></span></p>
                            <p class="danposko_status"></p>
                            <p class="danposko_keterangan"></p>
                            <p class="danposko_at"></p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Pos Keluar</strong><br>
                            <table style="width: 100%;" class="approval_pengamanan_keluar"></table>
                        </div>
                    </div>
                </div>
                
                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <label>Pemeriksaan waktu tiba di lokasi tujuan</label>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Pengecek (Pos Masuk)</strong><br>
                            <table style="width: 100%;" class="approval_pengamanan_masuk"></table>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Pembawa Barang</strong><br>
                            <p>NP: <span class="konfirmasi_pengguna_np"></span></p>
                            <p>Nama: <span class="konfirmasi_pengguna_nama"></span></p>
                            <p class="konfirmasi_pembawa_status"></p>
                            <p class="konfirmasi_pembawa_keterangan"></p>
                            <p class="konfirmasi_pembawa_at"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>