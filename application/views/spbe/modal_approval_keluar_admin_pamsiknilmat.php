<div class="modal fade" id="modal-approval" tabindex="-1" role="dialog" aria-labelledby="label-modal-approval" aria-hidden="true" data-toggle="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="label-modal-approval">Pemeriksaan waktu keluar perusahaan: Admin Pamsiknilmat</h4>
            </div>
            <form role="form" action="#" id="form-approval" method="post" onsubmit="return false;">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 alert alert-warning">
                        <div class="">
                            <strong>Histori</strong><br>
                            <table style="width: 100%;" class="approval_pengamanan_keluar"></table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 alert alert-danger">
                        <input type="hidden" name="ess_permohonan_spbe_id">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Pos *</label>
                                    <select class="form-control" name="pos_id"></select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="pos_nama">
                        <input type="hidden" name="posisi" value="keluar">

                        <!-- <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Posisi *</label>
                                    <select class="form-control" name="posisi">
                                        <option value="keluar">Keluar</option>
                                        <option value="masuk">Masuk</option>
                                    </select>
                                </div>
                            </div>
                        </div> -->

                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Tanggal *</label>
                                    <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d')?>" required>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Jam *</label>
                                    <input type="time" class="form-control" name="jam" value="<?= date('H:i')?>" required>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Keterangan</label>
                                    <textarea class="form-control" name="keterangan" style="max-width: 100%"></textarea>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Kondisi Barang</label>
                                    <div class="radio">
                                        <label>
                                            <input class="" type="radio" name="kondisi_barang_keluar" value="1" checked>Lengkap (Barang sesuai kuantitas yang terinput)
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input class="" type="radio" name="kondisi_barang_keluar" value="2">Keluar Parsial
                                        </label>
                                    </div>
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