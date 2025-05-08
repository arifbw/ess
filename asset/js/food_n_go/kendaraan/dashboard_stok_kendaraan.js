var table;
$(document).ready(function() {
    loadTable('jakarta');
    loadTable('karawang');
	loadTable_status();
});

const loadTable = (lokasi, startDate=null, endDate=null)=>{
    let date_ob = new Date();
    let date = ("0" + date_ob.getDate()).slice(-2);
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    let year = date_ob.getFullYear();
    let currentDate = year+'-'+month+'-'+date;
    if(startDate===undefined)
        startDate = currentDate;
    if(endDate===undefined)
        endDate = currentDate;
    table = $(`#tabel-data-${lokasi}`).DataTable({
        destroy: true,
        processing: true,
        serverSide: false,
        pageLength: 15,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: false,
        autoWidth: false,
        ajax: {
            url: "dashboard_stok_kendaraan/get_data_table",
            type: "POST",
            dataType: "json",
            data: {start_date: startDate, end_date: endDate, lokasi: lokasi}
        },
        initComplete: function(){
            $(`#tabel-data-${lokasi}_length`).html(`<h4>${lokasi.toUpperCase()}</h4>`)
        }
    });
}

const loadTable_status = (lokasi, startDate=null, endDate=null)=>{
    let date_ob = new Date();
    let date = ("0" + date_ob.getDate()).slice(-2);
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    let year = date_ob.getFullYear();
    let currentDate = year+'-'+month+'-'+date;
    if(startDate===undefined)
        startDate = currentDate;
    if(endDate===undefined)
        endDate = currentDate;
    table = $(`#tabel-data-status`).DataTable({
        destroy: true,
        processing: true,
        serverSide: false,
        pageLength: 5,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: false,
        autoWidth: false,
        ajax: {
            url: "dashboard_stok_kendaraan/get_data_table_status",
            type: "POST",
            dataType: "json",
            data: {start_date: startDate, end_date: endDate, lokasi: lokasi}
        },
        initComplete: function(){
            $(`#tabel-data-${lokasi}_length`).html(`<h4>${lokasi.toUpperCase()}</h4>`)
        }
    });
}