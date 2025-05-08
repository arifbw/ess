<div class="modal fade" id="modal-approval" tabindex="-1" role="dialog" aria-labelledby="label-modal-approval" aria-hidden="true" data-toggle="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="label-modal-approval">Pemeriksaan waktu kembali ke perusahaan: Pembawa Barang</h4>
            </div>
            <form role="form" action="#" id="form-approval" method="post" onsubmit="return false;">
            <div class="modal-body">
                <!-- <div class="row">
                    <div class="col-lg-12">
                        <table>
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
                
                <hr> -->
                <div class="row">
                    <div class="col-12 alert alert-success">
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Konfirmasi</label>
                                    <select class="form-control" name="konfirmasi_pembawa_status" required>
                                        <option value="1">Setujui</option>
                                        <option value="2">Tolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row div-keterangan" style="display: none;">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Alasan</label>
                                    <input type="text" class="form-control" name="konfirmasi_pembawa_keterangan">
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Tanggal</label>
                                    <input type="date" class="form-control" name="konfirmasi_pembawa_tanggal" value="<?= date('Y-m-d')?>" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Jam</label>
                                    <input type="time" class="form-control" name="konfirmasi_pembawa_jam" value="<?= date('H:i')?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>