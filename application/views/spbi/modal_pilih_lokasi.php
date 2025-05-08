<div class="modal fade" id="modal-lokasi-ttd" tabindex="-1" role="dialog" aria-labelledby="label-modal-lokasi-ttd" aria-hidden="true" data-toggle="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="label-modal-lokasi-ttd">Cetak SPBI</h4>
            </div>
            <form role="form" action="#" id="form-lokasi-ttd" method="post" onsubmit="return false;">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 alert alert-success">
                        <input type="hidden" name="uuid">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label>Pilih Lokasi Penandatangan</label>
                                    <select class="form-control" name="pilih_lokasi_ttd" required>
                                        <option value="Jakarta">Jakarta</option>
                                        <option value="Karawang">Karawang</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                <button type="button" class="btn btn-danger" onclick="cetak_pdf();">Cetak Pdf</button>
            </div>
            </form>
        </div>
    </div>
</div>