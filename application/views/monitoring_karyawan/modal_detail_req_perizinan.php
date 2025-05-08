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
                                <td>Jenis Izin</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="jenis_izin"></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="tanggal"></td>
                            </tr>
                            <tr>
                                <td>Pos yang Dilewati</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td class="pos"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <label>Approval</label>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Atasan 1</strong><br>
                            <p>NP: <span class="approval_1_np"></span></p>
                            <p>Nama: <span class="approval_1_nama"></span></p>
                            <p class="approval_1_status"></p>
                            <p class="approval_1_keterangan"></p>
                        </div>
                    </div>
                    <div class="col-lg-12" id="div-atasan-2">
                        <div class="alert alert-warning">
                            <strong>Atasan 2</strong><br>
                            <p>NP: <span class="approval_2_np"></span></p>
                            <p>Nama: <span class="approval_2_nama"></span></p>
                            <p class="approval_2_status"></p>
                            <p class="approval_2_keterangan"></p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>Pos Pengamanan</strong><br>
                            <table style="width: 100%;" class="approval_pengamanan_posisi"></table>
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