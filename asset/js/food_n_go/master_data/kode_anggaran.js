var table;
$(document).ready(function() {
    loadTable();
});

const loadTable = ()=>{
    table = $('#tabel_anggaran').DataTable({
        destroy: true,
        processing: true,
        serverSide: false,
        pageLength: 10,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: false,
        autoWidth: false,
        ajax: {
            url: "kode_anggaran/get_data_table",
            type: "POST",
            dataType: "json",
            data: {}
        }
    });
}

const saveNew=()=>{
    if(!navigator.onLine){
        alert('Anda sedang offline');
        return false;
    }
    $(`#btn-save`).prop('disabled',true);
    $.ajax({
        url: "kode_anggaran/save_new",
        type: 'POST',
        dataType: "JSON",
        data: {nama: $(`#nama`).val()}
    }).then(function(response){
        $(`#btn-save`).prop('disabled',false);
        if(response.status===true)
            alertSuccess(response.message);
        else
            alertError(response.message);
        table.ajax.reload();
    }).catch(function(xhr, status, error){
        $(`#btn-save`).prop('disabled',false);
        alertError(error);
    })
}

const changeStatus=(input)=>{
    if(!navigator.onLine){
        alert('Anda sedang offline');
        return false;
    }
    let id = input.dataset.ids;
    let newValue = input.value;
    $(`#status-${id}`).prop('disabled',true);
    $.ajax({
        url: "kode_anggaran/change_status",
        type: 'POST',
        dataType: "JSON",
        data: {id: id, newValue: newValue}
    }).then(function(response){
        $(`#status-${id}`).prop('disabled',false);
    }).catch(function(xhr, status, error){
        $(`#status-${id}`).prop('disabled',false);
        table.ajax.reload();
    })
}

const alertSuccess=(message)=>{
    $(`#text-danger`).hide();
    $(`#text-success`).show();
    $(`#text-success`).text(message);
    setTimeout(function(){ $(`#text-success`).hide(); }, 4000);
}

const alertError=(message)=>{
    $(`#text-success`).hide();
    $(`#text-danger`).show();
    $(`#text-danger`).text(message);
    setTimeout(function(){ $(`#text-danger`).hide(); }, 4000);
}