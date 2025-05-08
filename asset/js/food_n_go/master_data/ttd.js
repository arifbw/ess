function set_kiri_nama(){
    var val_kiri_nama = $('#kiri_np').find(':selected').text();
    if(val_kiri_nama!=''){
        var explode_kiri_nama = val_kiri_nama.split(' - ')[1];
    } else{
        var explode_kiri_nama = '';
    }
    $('#kiri_nama').val(explode_kiri_nama);
}

function set_kanan_nama(){
    var val_kanan_nama = $('#kanan_np').find(':selected').text();
    if(val_kanan_nama!=''){
        var explode_kanan_nama = val_kanan_nama.split(' - ')[1];
    } else{
        var explode_kanan_nama = '';
    }
    $('#kanan_nama').val(explode_kanan_nama);
}