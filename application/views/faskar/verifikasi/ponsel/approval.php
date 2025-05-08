<div class="modal-header">
    <h3 class="modal-title" id="modal-title-approval"></h3>
</div>
<div class="modal-body">
    <input type="hidden" name="id" id="header_id" value="<?= $header['id']?>">
    <div class="row">
        <div class="form-group">
            <div class="col-lg-3">
                <label>Persetujuan <span style="color: red">*</span></label>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="approval_sdm" id="approval_sdm" onchange="showHideAlasan()" required>
                    <option value="">-- Pilih --</option>
                    <option value="3">Setuju</option>
                    <option value="4">Tolak</option>
                </select>
            </div>														
        </div>
    </div>
    <br>
    <div class="row" id="div-alasan" style="display: none;">
        <div class="form-group">
            <div class="col-lg-3">
                <label>Alasan <span style="color: red">*</span></label>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="alasan_sdm" id="alasan_sdm" style="max-width: 100%;"></textarea>
            </div>														
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-cancel-approval">Close</button>
    <button type="button" class="btn btn-primary" id="btn-submit-approval" onclick="save()">Approve</button>
</div>

<script>
    var header_pemakaian_bulan = '<?= $header['pemakaian_bulan']?>';
    $(document).ready(function() {
		$('#modal-title-approval').html(`Persetujuan Pemakaian Pulsa Bulan ${moment(header_pemakaian_bulan).format('MMMM YYYY')}`);
	});

    function showHideAlasan(){
        let approval = $('#approval_sdm').val();
        if(approval==='4'){
            $('#div-alasan').show();
            $("#alasan_sdm").prop('required',true);
        } else{
            $('#div-alasan').hide();
            $("#alasan_sdm").prop('required',false);
        }
    }

    function save(){
        let id = $('#header_id').val();
        let approval_sdm = $('#approval_sdm').val();
        let alasan_sdm = $('#alasan_sdm').val();
        if(approval_sdm===''){
            alert('Approval harus diisi');
            return false;
        } else{
            if(approval_sdm==='4'){
                if(alasan_sdm.trim()===''){
                    alert('Alasan harus diisi');
                    return false;
                }
            }
        }
        let result = confirm("Perhatian\nPersetujuan hanya bisa dilakukan satu kali.\nLanjutkan?");
		if (result) {
            $.ajax({
                type: "POST",
                url: `<?= base_url('faskar/verifikasi/ponsel/header/save_approval')?>`,
                data: {id: id, approval_sdm: approval_sdm, alasan_sdm: alasan_sdm},
                dataType: 'JSON',
            }).then(function(response){
                $('#btn-submit-approval').remove();
                table.draw(false);
            }).catch(function(xhr, status, error){
                console.log(xhr.responseText);
                table.draw(false);
            })
        }
    }
</script>