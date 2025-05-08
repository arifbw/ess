<div class="modal-header">
    <h3 class="modal-title" id="modal-title-ajukan"></h3>
</div>
<form role="form" id="form-ajukan" method="post" onsubmit="return false;">
<div class="modal-body">
    <div class="form-group row">
        <div class="col-lg-12">
            <div id="alert-form-ajukan"></div>
        </div>
    </div>
    
    <input type="hidden" name="id" id="header_id" value="<?= $header['id']?>">

    <div class="form-group row">
        <div class="col-lg-3">
            <label>NP Atasan <span style="color: red">*</span></label>
        </div>
        <div class="col-lg-9">
            <input class="form-control" name="approval_atasan_np" id="approval_atasan_np" onchange="getNamaAtasan1()" required>
            <small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>
        </div>														
    </div>
    
    <div class="form-group row">
        <div class="col-lg-3">
            <label>Nama Atasan</label>
        </div>
        <div class="col-lg-9">
            <input class="form-control" name="approval_atasan_nama" id="approval_atasan_nama" readonly>
        </div>														
    </div>
    
    <div class="form-group row">
        <div class="col-lg-3">
            <label>Jabatan Atasan</label>
        </div>
        <div class="col-lg-9">
            <input class="form-control" name="approval_atasan_jabatan" id="approval_atasan_jabatan" readonly>
        </div>														
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-cancel-ajukan">Close</button>
    <button type="button" class="btn btn-primary" id="btn-submit-ajukan" onclick="doSubmit()">Ajukan</button>
</div>
</form>

<script src="<?= base_url()?>asset/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script>
    var header_pemakaian_bulan = '<?= $header['pemakaian_bulan']?>';
	var header_lokasi = '<?= $header['lokasi']?>';
    $(document).ready(function() {
		$('#modal-title-ajukan').html(`Ajukan ke Atasan: Pemakaian Listrik ${header_lokasi} Bulan ${moment(header_pemakaian_bulan).format('MMMM YYYY')}`);

        $('#form-ajukan').validate({
            rules: {
                id: {
                    required: true
                }, 
                approval_atasan_np: {
                    required: true
                }, 
                approval_atasan_nama: {
                    required: true
                },
                approval_atasan_jabatan: {
                    required: true
                }
            },
            submitHandler: function (form) {
                save()
            }
        });
	});

    function getNamaAtasan1() {
        var np_atasan = $('#approval_atasan_np').val();
        var np_karyawan = '<?= $_SESSION["no_pokok"]?>';
        if (np_atasan.length>3) {
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?= base_url('faskar/atasan/ajax_getNama_approval')?>",
                data: {np_aprover: np_atasan, np_karyawan: np_karyawan},
                success: function(msg){
                    if(msg.status == false){
                        alert (msg.message);
                        $('#approval_atasan_np').val('');
                        $('#approval_atasan_nama').val('');
                        $('#approval_atasan_jabatan').val('');
                    }else{							 
                        $('#approval_atasan_nama').val(msg.data.nama);
                        $('#approval_atasan_jabatan').val(msg.data.jabatan);
                    }													  
                }
            });
        } else if (np_atasan.length<4) {
            $('#approval_atasan_nama').val('');
            $('#approval_atasan_jabatan').val('');
        }
    }

    function doSubmit(){
        $('#form-ajukan').submit();
    }

    function save(){
        let allData = {};
        $("#form-ajukan input").each(function(){
            allData[$(this).attr('name')] = this.value;
        });

        let result = confirm("Pastikan isian Anda sudah benar.\nLanjutkan?");
        if (result) {
            $.ajax({
                type: "POST",
                url: `<?= base_url('faskar/listrik/header/save_ajukan')?>`,
                data: allData,
                dataType: 'JSON',
            }).then(function(response){
                $('#btn-submit-ajukan').remove();
                $('#alert-form-ajukan').html('<div class="alert alert-info"><b>'+response.message+'</b></div>');
                table.draw(false);
            }).catch(function(xhr, status, error){
                console.log(xhr.responseText);
                table.draw(false);
            })
        }
    }
</script>