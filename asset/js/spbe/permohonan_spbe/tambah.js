var all_karyawan_temp = [];
$(()=>{
    $('#formulir_tambah').find('[name=np_karyawan]').trigger('change');
    $('#formulir_tambah').find('[name=pilih_pengawal]').trigger('change');
});

$('.multi_select').select2({
    closeOnSelect: false
});

$('.select2').select2({
    closeOnSelect: true
});

$(".btn-add").on('click', function(e){
    $( ".group-input:last" ).clone(true).appendTo( "#all-input" );
    $( ".group-input:last" ).find('input, select').not("input[type=date]").val('').trigger('change');
});

$(".btn-rm").on('click', function(e){
    if( $('.group-input').length > 1 ) $(this).closest('.group-input').remove();
});

$('#formulir_tambah').find('[name=np_karyawan]').on('change', function(e){
    let find = _.find(daftar_karyawan_temp, (o)=>{ return o.no_pokok==this.value; });
    if(typeof find!='undefined'){
        $('#formulir_tambah').find('[name=nama]').val(find.nama);
        $('#formulir_tambah').find('[name=nama_jabatan]').val(find.nama_jabatan);
        $('#formulir_tambah').find('[name=kode_unit]').val(find.kode_unit);
        $('#formulir_tambah').find('[name=nama_unit]').val(find.nama_unit);
    } else{
        $('#formulir_tambah').find('[name=nama]').val('');
        $('#formulir_tambah').find('[name=nama_jabatan]').val('');
        $('#formulir_tambah').find('[name=kode_unit]').val('');
        $('#formulir_tambah').find('[name=nama_unit]').val('');
    }
    setTimeout(() => {
        show_list_atasan();
        show_list_kasek();
    }, 500); 
});

const show_list_kasek = async()=>{
    $('#formulir_tambah').find('[name=approval_kasek_np]').html('');
    let data;
    let find = await _.find(daftar_kasek_temp, (o)=>{ return o.np_karyawan==$('#formulir_tambah').find('[name=np_karyawan]').val();});
    if( typeof find!='undefined' ){
        data = find.list_kasek;
    } else{
        data = await get_kasek();
    }
    
    $('#formulir_tambah').find('[name=approval_kasek_np]').append(new Option(`-- Pilih Kasek --`, ''));
    for (const i of data) {
        $('#formulir_tambah').find('[name=approval_kasek_np]').append(new Option(`${i.no_pokok} - ${i.nama}`, i.no_pokok));
    }
    $('#formulir_tambah').find('[name=approval_kasek_np]').trigger('change');
}

const show_list_atasan = async()=>{
    $('#formulir_tambah').find('[name=approval_atasan_np]').html('');
    let data;
    let find = await _.find(daftar_atasan_temp, (o)=>{ return o.np_karyawan==$('#formulir_tambah').find('[name=np_karyawan]').val();});
    if( typeof find!='undefined' ){
        data = find.list_atasan;
    } else{
        data = await get_atasan();
    }
    
    $('#formulir_tambah').find('[name=approval_atasan_np]').append(new Option(`-- Pilih Atasan --`, ''));
    for (const i of data) {
        $('#formulir_tambah').find('[name=approval_atasan_np]').append(new Option(`${i.no_pokok} - ${i.nama}`, i.no_pokok));
    }
    $('#formulir_tambah').find('[name=approval_atasan_np]').trigger('change');
}

const get_atasan = async()=>{
    let temp = [];
    let np_karyawan = $('#formulir_tambah').find('[name=np_karyawan]').val();
    let kode_unit = $('#formulir_tambah').find('[name=kode_unit]').val();
    await $.ajax({
        type: "POST",
        url: `${BASE_URL}spbe/permohonan_spbe/get_atasan`,
        data: {np_karyawan: np_karyawan, kode_unit: kode_unit},
    }).then( (response)=>{
        temp = response;
        daftar_atasan_temp.push({np_karyawan: np_karyawan, list_atasan: response});
    }).catch((xhr, textStatus, errorThrown)=>{
        Swal.fire('',xhr.responseText,'error');
    })
    return temp;
}

const get_kasek = async()=>{
    let temp = [];
    let np_karyawan = $('#formulir_tambah').find('[name=np_karyawan]').val();
    let kode_unit = $('#formulir_tambah').find('[name=kode_unit]').val();
    await $.ajax({
        type: "POST",
        url: `${BASE_URL}spbe/permohonan_spbe/get_kasek`,
        data: {np_karyawan: np_karyawan, kode_unit: kode_unit},
    }).then( (response)=>{
        temp = response;
        daftar_kasek_temp.push({np_karyawan: np_karyawan, list_kasek: response});
    }).catch((xhr, textStatus, errorThrown)=>{
        Swal.fire('',xhr.responseText,'error');
    })
    return temp;
}

$('#formulir_tambah').find('[name=approval_kasek_np]').on('change', function(e){
    let find = _.find(daftar_kasek_temp, (o)=>{ return o.np_karyawan==$('#formulir_tambah').find('[name=np_karyawan]').val();});
    let kasek = _.find(find.list_kasek, (o)=>{ return o.no_pokok==this.value;});
    if(typeof kasek!='undefined'){
        $('#formulir_tambah').find('[name=approval_kasek_nama]').val(kasek.nama);
        $('#formulir_tambah').find('[name=approval_kasek_jabatan]').val(kasek.nama_jabatan);
        $('#formulir_tambah').find('[name=approval_kasek_kode_unit]').val(kasek.kode_unit);
        $('#formulir_tambah').find('[name=approval_kasek_nama_unit]').val(kasek.nama_unit);
        $('#formulir_tambah').find('[name=approval_kasek_nama_unit_singkat]').val(kasek.nama_unit_singkat);
    } else{
        $('#formulir_tambah').find('[name=approval_kasek_nama]').val('');
        $('#formulir_tambah').find('[name=approval_kasek_jabatan]').val('');
        $('#formulir_tambah').find('[name=approval_kasek_kode_unit]').val('');
        $('#formulir_tambah').find('[name=approval_kasek_nama_unit]').val('');
        $('#formulir_tambah').find('[name=approval_kasek_nama_unit_singkat]').val('');
    }
});

$('#formulir_tambah').find('[name=approval_atasan_np]').on('change', function(e){
    let find = _.find(daftar_atasan_temp, (o)=>{ return o.np_karyawan==$('#formulir_tambah').find('[name=np_karyawan]').val();});
    let atasan = _.find(find.list_atasan, (o)=>{ return o.no_pokok==this.value;});
    if(typeof atasan!='undefined'){
        $('#formulir_tambah').find('[name=approval_atasan_nama]').val(atasan.nama);
        $('#formulir_tambah').find('[name=approval_atasan_jabatan]').val(atasan.nama_jabatan);
        $('#formulir_tambah').find('[name=approval_atasan_kode_unit]').val(atasan.kode_unit);
        $('#formulir_tambah').find('[name=approval_atasan_nama_unit]').val(atasan.nama_unit);
        $('#formulir_tambah').find('[name=approval_atasan_nama_unit_singkat]').val(atasan.nama_unit_singkat);
    } else{
        $('#formulir_tambah').find('[name=approval_atasan_nama]').val('');
        $('#formulir_tambah').find('[name=approval_atasan_jabatan]').val('');
        $('#formulir_tambah').find('[name=approval_atasan_kode_unit]').val('');
        $('#formulir_tambah').find('[name=approval_atasan_nama_unit]').val('');
        $('#formulir_tambah').find('[name=approval_atasan_nama_unit_singkat]').val('');
    }
});

$('#formulir_tambah').find('[name=pilih_pengawal]').on('change', function(e){
    if( $(this).is(":checked") ){
        var pilih_pengawal = $(this).val();
    }

    if( ['2','3'].includes(pilih_pengawal) == true ){
        $('#inputan-penyegel').show();
        $('#formulir_tambah').find('[name=konfirmasi_pengguna_np]').focus();
        if( pilih_pengawal=='3' ){
            $('#inputan-nama-pembawa').show();
            $('#inputan-nama-pembawa').find('[name=nama_pembawa_barang]').prop('required',true);
        } else {
            $('#inputan-nama-pembawa').hide();
            $('#inputan-nama-pembawa').find('[name=nama_pembawa_barang]').prop('required',false);
        }
        show_list_all_karyawan();
    } else{
        $('#inputan-penyegel').hide();
        $('#inputan-nama-pembawa').hide();
        $('#inputan-nama-pembawa').find('[name=nama_pembawa_barang]').prop('required',false);
    }
});

const show_list_all_karyawan = async()=>{
    $('#formulir_tambah').find('[name=konfirmasi_pengguna_np]').html('');
    if( all_karyawan_temp.length===0 ){
        all_karyawan_temp = await get_all_karyawan();
    }
    
    for (const i of all_karyawan_temp) {
        $('#formulir_tambah').find('[name=konfirmasi_pengguna_np]').append(new Option(`${i.no_pokok} - ${i.nama}`, i.no_pokok));
    }
    $('#formulir_tambah').find('[name=konfirmasi_pengguna_np]').val($('#formulir_tambah').find('[name=np_karyawan]').val());
    $('#formulir_tambah').find('[name=konfirmasi_pengguna_np]').trigger('change');
}

const get_all_karyawan = async()=>{
    Swal.fire({
        text: "Mengambil data karyawan...",
        allowOutsideClick: false,
        showConfirmButton: false
    });
    let temp = await $.ajax({
        type: "POST",
        url: `${BASE_URL}spbe/spbe_proses/all_karyawan`,
        data: {},
    });
    Swal.close();
    return temp;
}

$('#formulir_tambah').find('[name=konfirmasi_pengguna_np]').on('change', function(e){
    let find = _.find(all_karyawan_temp, (o)=>{ return o.no_pokok==e.target.value;});
    if(typeof find!='undefined'){
        $('#formulir_tambah').find('[name=konfirmasi_pengguna_nama]').val(find.nama);
        $('#formulir_tambah').find('[name=konfirmasi_pengguna_jabatan]').val(find.nama_jabatan);
    } else{
        $('#formulir_tambah').find('[name=konfirmasi_pengguna_nama]').val('');
        $('#formulir_tambah').find('[name=konfirmasi_pengguna_jabatan]').val('');
    }
});

$('#formulir_tambah').find('[name=barang_kembali]').on('change', function(e){
    if( $(this).is(":checked") ){
        var barang_kembali = $(this).val();
    }

    if( barang_kembali=='1' ){
        $('#inputan-pos-masuk').show();
        $('#formulir_tambah').find('.pos_masuk').focus();
    } else{
        $('#inputan-pos-masuk').hide();
    }
});

const collect_data = async()=>{
    let data = {};
    for (const i of $('#formulir_tambah').find('.input-detail')) {
        if( ['pilih_pengawal','barang_kembali'].includes(i.name) == false ) data[i.name] = i.value;
    }
    data['pilih_pengawal'] = $('#formulir_tambah').find('[name=pilih_pengawal]:checked').val();
    data['barang_kembali'] = $('#formulir_tambah').find('[name=barang_kembali]:checked').val();
    return data;
}

$("#btn-cancel").on('click', function(e){
    Swal.fire({
        title: '',
        text: "Isian akan dibatalkan?",
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = `${BASE_URL}spbe/permohonan_spbe`;
        }
    })
});

$('#formulir_tambah').on('submit', function(){
    Swal.fire({
        title: 'Konfirmasi',
        text: "Sebelum submit pastikan isian Anda sudah benar",
        icon: 'warning',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                text: "Data sedang diproses...",
                allowOutsideClick: false,
                showConfirmButton: false
            })
            submit_process();
        }
    })
})

const submit_process = async()=>{
    let data = await collect_data();
    let barang = [];
    for (let i = 0; i < $( ".group-input" ).length; i++) {
        const groupInput = $($( ".group-input" )[i]);

        const row = {
            jumlah: groupInput.find('.jumlah').val(),
            nama_barang: groupInput.find('.nama_barang').val(),
            keterangan: groupInput.find('.keterangan').val(),
        }
        barang.push(row);
    }

    let pos_keluar = [];
    let pos_keluar_id = $('#formulir_tambah').find('.pos_keluar').val();
    for (const i of pos_keluar_id) {
        let find_lengkap = await _.find(daftar_pos_temp, (o)=>{ return o.id==i; });
        pos_keluar.push({
            id: find_lengkap.id,
            kode_pos: find_lengkap.kode_pos,
            nama: find_lengkap.nama
        })
    }
    data['pos_keluar'] = pos_keluar;
    data['pos_keluar_id'] = pos_keluar_id;

    let pos_masuk = [];
    let pos_masuk_id = $('#formulir_tambah').find('.pos_masuk').val();
    for (const i of pos_masuk_id) {
        let find_lengkap = await _.find(daftar_pos_temp, (o)=>{ return o.id==i; });
        pos_masuk.push({
            id: find_lengkap.id,
            kode_pos: find_lengkap.kode_pos,
            nama: find_lengkap.nama
        })
    }
    data['pos_masuk'] = pos_masuk;
    data['pos_masuk_id'] = pos_masuk_id;

    data['barang'] = barang;

    let formData = new FormData();
    let jumlah_files = $("#jumlah_files").val();

    for (let i = 0; i < jumlah_files; i++) {
        let file = $('input[name="upload_file[]"]')[i].files[0];
        formData.append('upload_file[]', file);
    }
    console.log(formData);

    $.ajax({
        type: "POST",
        url: `${BASE_URL}spbe/permohonan_spbe/simpan`,
        data: data,
    }).then( (response)=>{
        if(response.status==true) var type = 'success';
        else var type = 'error';
        
        submit_files(formData, response.new_id);
        // Swal.fire('',response.message,type).then(function() {
        //     window.location = `${BASE_URL}spbe/permohonan_spbe`;
        // });
    }).catch((xhr, textStatus, errorThrown)=>{
        Swal.fire('',xhr.responseText,'error');
    })
}

function submit_files(formData, new_id) {
    $.ajax({
        type: "POST",
        url: `${BASE_URL}spbe/upload_files/simpan/` + new_id,
        data: formData,
        processData: false,
        contentType: false,
    }).then((response) => {
        if (response.status == true) {
            var type = 'success';
        } else {
            var type = 'error';
        }
        
        Swal.fire('', response.message, type).then(function() {
            window.location = `${BASE_URL}spbe/permohonan_spbe`;
        });
    }).catch((xhr, textStatus, errorThrown) => {
        Swal.fire('', xhr.responseText, 'error');
    });
}