<link rel="stylesheet" href="<?= base_url('asset/star-rating-svg-master/src/css/star-rating-svg.css')?>">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="btn-x">×</button>
        <h4 class="modal-title" id="label_modal_detail">Beri penilaian driver</h4>
    </div>
    <div class="modal-body">
        <div class="get-approve">
            
            <form method="post" action="<?= base_url('/food_n_go/kendaraan/data_pemesanan/save_rate')?>" id="form-rate">
                <div class="alert text-center">
                    <input type="hidden" name="kode" value="<?= @$row->kode?>">
                    <input type="hidden" name="rating_driver" id="rating_driver" value="<?= @$row->rating_driver!=null?$row->rating_driver:0?>">
                    <strong><a id=""><?= @$row->nama_mst_driver?></a></strong>
                    <br>
                    
                    <br>
                    <div class="my-rating-8"></div>
                    
                    <div>
                        <textarea class="form-control" placeholder="Catatan untuk driver" name="catatan_rating_driver" required><?= @$row->catatan_rating_driver?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center" id="message"></div>
                    <div class="col-lg-12 text-right">
                        <button type="submit" id="btn-rate" class="btn btn-block btn-success" disabled>Submit</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
<script src="<?= base_url('asset/star-rating-svg-master/src/jquery.star-rating-svg.js')?>"></script>
<script>
    var rate_val = $('#rating_driver').val();
    $(".my-rating-8").starRating({
        useFullStars: true,
        disableAfterRate: false,
        activeColor: 'cornflowerblue',
        initialRating: rate_val,
        callback: function(currentRating, $el){
            $('#rating_driver').val(currentRating);
            if(currentRating>0){
                $('#btn-rate').prop('disabled',false);
            } else{
                $('#btn-rate').prop('disabled',true);
            }
        }
    });
    
    $('#form-rate').on('submit', function(){
        $('#btn-rate').prop('disabled',true);
        $('#message').html('Menyimpan...');
        
        var that = $(this),
            url = that.attr('action'),
            type = that.attr('method');
        
        $.when(
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
                    //$('#btn-rate').prop('disabled',false);
                },
                error: function(){
                    $('#message').html('Gagal terhubung ke server');
                    $('#btn-rate').prop('disabled',false);
                }
            })
        ).done(function( x ) {
            setTimeout(function(){ 
                $('#message').html(''); 
                $('#btn-x').trigger('click');
            }, 3000);
        });
        
        return false;
    })
</script>