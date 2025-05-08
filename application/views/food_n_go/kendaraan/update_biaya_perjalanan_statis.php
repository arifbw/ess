<style>
    td{
        vertical-align: top;
    }
    
    .input-int{
        text-align: right;
    }
    
    .row-space{
        padding-top: 10px;
    }
</style>
<?php $disabled = ($row->pesanan_selesai==1?'disabled':'');?>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="label_modal_detail">Update Biaya Perjalanan</h4>
    </div>
    <div class="modal-body">
        <div class="get-approve">
            
            <form method="post" action="<?= base_url('food_n_go/kendaraan/konfirmasi_pemesanan/save_update_biaya_statis')?>" id="form-update-biaya">
                <input type="hidden" name="kode" id="kode" value="<?= @$row->kode?>">
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Tol <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control input-int isInt" name="biaya_tol" id="biaya_tol" placeholder="Angka dalam rupiah" value="<?= @$row->biaya_tol!=null?$row->biaya_tol:0?>" onkeyup="count_total()" required>
                        </div>
                    </div>
                </div>
                
                <div class="row row-space">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Parkir <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control input-int isInt" name="biaya_parkir" id="biaya_parkir" placeholder="Angka dalam rupiah" value="<?= @$row->biaya_parkir!=null?$row->biaya_parkir:0?>" onkeyup="count_total()" required>
                        </div>
                    </div>
                </div>
                
                <div class="row row-space">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Jenis BBM <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <select class="form-control" name="id_mst_bbm" id="id_mst_bbm" onchange="change_attr_bbm();">
                                <?php foreach($bbm as $r){?>
                                <option value="<?= $r->id?>" data-nama_mst_bbm="<?= $r->nama?>" data-harga_bbm_per_liter="<?= $r->harga?>" <?= $r->id==$row->id_mst_bbm?'selected':''?>><?= $r->nama?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="nama_mst_bbm" id="nama_mst_bbm" value="<?= @$row->nama_mst_bbm?>">
                        </div>
                    </div>
                </div>
                
                <div class="row row-space">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Harga per Liter <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control input-int isInt" name="harga_bbm_per_liter" id="harga_bbm_per_liter" placeholder="Angka dalam rupiah" value="<?= @$row->harga_bbm_per_liter!=null?$row->harga_bbm_per_liter:0?>" required readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row row-space">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Liter BBM <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control input-int isFloat" name="jumlah_liter_bbm" id="jumlah_liter_bbm" placeholder="Angka dalam rupiah" value="<?= @$row->jumlah_liter_bbm!=null?$row->jumlah_liter_bbm:0?>" onkeyup="count_total()" required>
                        </div>
                    </div>
                </div>
                
                <div class="row row-space">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Lain-lain <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control input-int isInt" name="biaya_lainnya" id="biaya_lainnya" placeholder="Isi dengan 0 jika tidak ada" value="<?= @$row->biaya_lainnya!=null?$row->biaya_lainnya:0?>" onkeyup="count_total()" required>
                        </div>
                    </div>
                </div>
                
                <div class="row row-space">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Keterangan Biaya Lain-lain</label>
                        </div>
                        <div class="col-lg-8">
                            <textarea class="form-control" name="ket_lainnya" id="ket_lainnya" placeholder="Harus diisi jika ada biaya lain-lain" <?= @$row->biaya_lainnya>0?'':'disabled'?> ><?= @$row->ket_lainnya?></textarea>
                        </div>
                    </div>
                </div>
                
                <hr>
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label><b>Total</b></label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control input-int isFloat" name="biaya_total" id="biaya_total" placeholder="" value="<?= @$row->biaya_total!=null?$row->biaya_total:0?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row row-space">
                    <div class="col-lg-12 text-center" id="message"></div>
                    <div class="col-lg-12 text-right">
                        <button type="submit" id="btn-biaya" class="btn btn-block btn-success">Submit</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script>    
    
    $(document).ready(function() {
        
    })
    
    function change_attr_bbm(){        
        // attribute bbm
        var id_mst_bbm = $('#id_mst_bbm').children("option:selected").val();
        var nama_mst_bbm = $('#id_mst_bbm').children("option:selected").data('nama_mst_bbm');
        var harga_bbm_per_liter = $('#id_mst_bbm').children("option:selected").data('harga_bbm_per_liter');
        
        $('#id_mst_bbm').val(id_mst_bbm);
        $('#nama_mst_bbm').val(nama_mst_bbm);
        $('#harga_bbm_per_liter').val(harga_bbm_per_liter);
        
        count_total();
    }
    
    function count_total(){
        var biaya_total, biaya_bbm;
        
        var biaya_tol = check_value('biaya_tol');
        var biaya_parkir = check_value('biaya_parkir');
        var harga_bbm_per_liter = check_value('harga_bbm_per_liter');
        var jumlah_liter_bbm = check_value('jumlah_liter_bbm');
        var biaya_lainnya = check_value('biaya_lainnya');
        
        biaya_total = biaya_tol + biaya_parkir + biaya_lainnya + (harga_bbm_per_liter * jumlah_liter_bbm);
        $('#biaya_total').val(biaya_total);
    }
    
    function check_value(id){
        var value_return;
        if($('#'+id).val()!=''){
            value_return = parseFloat($('#'+id).val());
        } else{
            console
            value_return = parseInt(0);
        }
        
        if(id=='biaya_lainnya'){
            if($('#'+id).val()>0){
                $('#ket_lainnya').prop('required',true);
                $('#ket_lainnya').prop('disabled',false);
            } else{
                $('#ket_lainnya').val('');
                $('#ket_lainnya').prop('required',false);
                $('#ket_lainnya').prop('disabled',true);
            }
        }
        return value_return;
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
            },
            error: function(){
                $('#message').html('Gagal terhubung ke server');
                $('#btn-biaya').prop('disabled',false);
                setTimeout(function(){ $('#message').html(''); }, 3000);
            }
         });
        
        return false;
    })
    
    $('.isFloat').keypress(function(event) {
        if( event.which ==8){
          return;  
        } else if ((event.which != 46 || $(this).val().indexOf('.') != -1 ) && (event.which < 47 || event.which > 59) && event.key != '-' ){
            event.preventDefault();
            if ((event.which == 46) && ($(this).indexOf('.') != -1) ) {
                event.preventDefault();
            }
        }
    });
    
    $(".isInt").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 107, 16, 187]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
             // Allow: Ctrl+V
            (e.keyCode == 86 && e.ctrlKey === true) ||
            // Allow: Ctrl+z
            (e.keyCode == 90 && e.ctrlKey === true) ||
             // Allow: Ctrl+C
            (e.keyCode == 67 && e.ctrlKey === true) ||
             // Allow: Ctrl+X
            (e.keyCode == 88 && e.ctrlKey === true) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
             // let it happen, don't do anything
             return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
</script>