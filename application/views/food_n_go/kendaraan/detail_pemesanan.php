<style>
    td{
        vertical-align: top;
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="label_modal_detail">Detail Pemesanan Kendaraan</h4>
    </div>
    <div class="modal-body">
        <div class="get-approve">
            <?php $this->load->view('food_n_go/kendaraan/info_pemesanan', $data); ?>
        </div>
    </div>
</div>
