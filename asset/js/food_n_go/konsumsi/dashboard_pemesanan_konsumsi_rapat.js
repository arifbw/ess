var tableRapat,tableLembur;
$(document).ready(function() {
    loadTableRapat();
    loadTableLembur();
});

const loadTableRapat = (startDate=null, endDate=null)=>{
    let date_ob = new Date();
    let date = ("0" + date_ob.getDate()).slice(-2);
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    let year = date_ob.getFullYear();
    let currentDate = year+'-'+month+'-'+date;
    if(startDate===undefined)
        startDate = currentDate;
    if(endDate===undefined)
        endDate = currentDate;
    tableRapat = $(`#tabel-data-rapat`).DataTable({
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
            url: "dashboard_pemesanan_konsumsi_rapat/get_data_table_rapat",
            type: "POST",
            dataType: "json",
            data: {start_date: startDate, end_date: endDate}
        },
        initComplete: function(){
            $(`#tabel-data-rapat_length`).html(`<h4>Rapat</h4>`)
        }
    });
}

const loadTableLembur = (startDate=null, endDate=null)=>{
    let date_ob = new Date();
    let date = ("0" + date_ob.getDate()).slice(-2);
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    let year = date_ob.getFullYear();
    let currentDate = year+'-'+month+'-'+date;
    if(startDate===undefined)
        startDate = currentDate;
    if(endDate===undefined)
        endDate = currentDate;
    tableLembur = $(`#tabel-data-lembur`).DataTable({
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
            url: "dashboard_pemesanan_konsumsi_rapat/get_data_table_lembur",
            type: "POST",
            dataType: "json",
            data: {start_date: startDate, end_date: endDate}
        },
        initComplete: function(){
            $(`#tabel-data-lembur_length`).html(`<h4>Lembur</h4>`)
        }
    });
}