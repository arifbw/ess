<style>
    td{
        vertical-align: top;
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="label_modal_detail">Update Biaya Perjalanan</h4>
    </div>
    <div class="modal-body">
        <div class="get-approve">
            <a onclick="show_input()"><i id="fa-input" class="fa fa-chevron-down"></i> Input</a>
            <form method="post" action="<?= base_url('food_n_go/kendaraan/konfirmasi_pemesanan/save_update_biaya')?>" id="form-update-biaya" style="display: none;">
                <input type="hidden" name="id_pemesanan" id="id_pemesanan" value="<?= @$row->id?>">
                <input type="hidden" name="kode_pemesanan" id="kode_pemesanan" value="<?= @$row->kode?>">
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Item Pengeluaran <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="nama_pengeluaran" id="nama_pengeluaran" placeholder="Tol/parkir/dll" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Nominal <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="int" class="form-control" name="total_rp" id="total_rp" placeholder="Angka dalam rupiah" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12 text-center" id="message"></div>
                    <div class="col-lg-12 text-right">
                        <button type="submit" id="btn-biaya" class="btn btn-block btn-success">Submit</button>
                    </div>
                </div>
            </form>
            
            <br><hr><br>
            
            <h5>Detail</h5>
            <div class="row">
                <div class=" col-lg-12">
                    <table width="100%" class="table table-striped table-hover" id="tabel_biaya">
                        <thead>
                            <tr>
                                <th class='text-left no-sort'>Nama Item</th>
                                <th class='text-right no-sort'>Harga Satuan (Rp)</th>
                                <th class='text-right no-sort'>Total (Rp)</th>
                                <th class='text-center no-sort'>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        
        </div>
    </div>
</div>

<script>    
    var table_biaya;
    $(document).ready(function() {
        load_table_biaya();
    })
    
    function load_table_biaya() {
        var id_pemesanan = $('#id_pemesanan').val();
        table_biaya = $('#tabel_biaya').DataTable({
            "iDisplayLength": 10,
            "language": {
                "url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
                "sEmptyTable": "Tidak ada data di database",
                "emptyTable": "Tidak ada data di database"
            },
            "stateSave": true,
            "processing": true,
            "order": [],
            
            "ajax": {
                "url": "<?php echo site_url("food_n_go/kendaraan/konfirmasi_pemesanan/tabel_biaya/")?>" + id_pemesanan,
                "type": "POST"
            },
            
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }, {targets: [1,2], 'className':'text-right'}, {targets: [3], 'className':'text-center'} ],
        });
    }
    
    $('#form-update-biaya').on('submit', function(){
        $('#btn-biaya').prop('disabled',true);
        $('#message').html('Menyimpan...');
        
        var that = $(this),
            url = that.attr('action'),
            type = that.attr('method');
        $.ajax({
            url: url,
            type: type,
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function(response){
                $('#message').html(response.message);
                $('#btn-biaya').prop('disabled',false);
                setTimeout(function(){ $('#message').html(''); }, 3000);
                reset_value();
                table_biaya.ajax.reload();
            },
            error: function(){
                $('#message').html('Gagal terhubung ke server');
                $('#btn-biaya').prop('disabled',false);
                setTimeout(function(){ $('#message').html(''); }, 3000);
            }
         });
        
        return false;
    })
    
    function reset_value(){
        $('#nama_pengeluaran').val('');
        $('#total_rp').val('');
    }
    
    function show_input(){
        if ($('#form-update-biaya').css('display') == 'none'){
            $('#form-update-biaya').show();
            $('#fa-input').removeClass('fa-chevron-down');
            $('#fa-input').addClass('fa-chevron-up');
        } else{
            $('#form-update-biaya').hide();
            $('#fa-input').removeClass('fa-chevron-up');
            $('#fa-input').addClass('fa-chevron-down');
        }
    }
    
</script>