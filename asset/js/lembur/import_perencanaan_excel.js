var tempDataExcel = [];
var temp_last_excel_id = null;
$('#choose-excel').on('change', function(e){
    $('#form-tambah').LoadingOverlay('show');
    handleFileSelect(e);
});

function handleFileSelect(event) {
    let tbody = list_table.find('tbody');
    tbody.empty();
    tempDataExcel = [];
    temp_array_of_id = [];

    var files = event.target.files;
    var file = files[0];

    var reader = new FileReader();
    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        // Assuming the first sheet here; adjust as needed
        var firstSheetName = workbook.SheetNames[0];
        var worksheet = workbook.Sheets[firstSheetName];

        // Adjust the range to start from row 6 to skip headers or any other content before the data
        var range = XLSX.utils.decode_range(worksheet['!ref']); // Decodes the range of the worksheet
        range.s.r = 5; // Setting the start row as 7 (0-indexed, hence 6)

        var jsonData = [];
        for(var R = range.s.r + 1; R <= range.e.r; ++R) { // Start from row 7
            var rowObject = { id: uuidv4() };
            for(var C = range.s.c; C <= range.e.c; ++C) {
                var cellAddress = {c:C, r:R};
                var cellRef = XLSX.utils.encode_cell(cellAddress);
                var cell = worksheet[cellRef];
                var headerRef = XLSX.utils.encode_cell({c:C, r:range.s.r});
                var header = worksheet[headerRef];
                if(cell && header) {
                    var headerValue = header.v;
                    if(cell.t == 'd') { // If the cell contains a date
                        rowObject[headerValue] = cell.v.toISOString().slice(0, 10); // Format date as YYYY-MM-DD
                    } else {
                        // Use `w` for formatted text (includes commas etc.) if available, else `v` for raw value
                        var cellValue = (cell.t == 's' || cell.t == 'str' || !cell.w) ? cell.v : cell.w; 
                        rowObject[headerValue] = cellValue;
                    }
                }
            }
            if(Object.keys(rowObject).length > 0) jsonData.push(rowObject);
        }

        // console.log(jsonData);
        tempDataExcel = jsonData.map(o => {
            const { jenis_lembur, ...rest } = o;
            return { mst_kategori_lembur_id: jenis_lembur, ...rest };
        });
        for (const i of tempDataExcel) {
            if( (typeof i.tanggal!='undefined' && i.tanggal.trim()!='') && (typeof i.list_np!='undefined' && i.list_np.trim()!='') ){
                temp_last_excel_id = i.id;
                $('.add-list').trigger('click');
            }
        }
    };

    reader.onerror = function(event) {
        console.error("File could not be read! Code " + event.target.error.code);
    };

    reader.readAsBinaryString(file);
    $('#form-tambah').LoadingOverlay('hide', true);
}

$('#form-import').on('submit', function(e){
    e.preventDefault(); // Menghentikan pengiriman form standar

    let data = new FormData(this);
    data.append('kode_unit', $('#form-tambah').find('[name="kode_unit"]').val());
    data.append('tanggal_mulai', startDate);
    data.append('tanggal_selesai', endDate);
    if ($('#form-tambah').find('[name="uuid"]').length) {
        data.append('uuid', $('#form-tambah').find('[name="uuid"]').val());
    }

    // Loop melalui semua input dengan nama "evidence[]"
    $('#form-tambah').find('[name="evidence[]"]').each(function(index, element) {
        $.each(element.files, function(i, file) {
            data.append('evidence[]', file); // Menambahkan setiap file ke FormData
        });
    });

    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#form-import').LoadingOverlay('show');
        },
    }).then((res) => {
        $('#form-import').LoadingOverlay('hide', true);
        if (res.status == true) {
            if (res.data_error.length) {
                $('#modal-import').modal('hide');
                $('#modal-error').modal('show');
                laod_table_error(res.data_error);
            } else {
                Swal.fire({
                    title: '',
                    text: res.message,
                    icon: 'success',
                    allowOutsideClick: false,
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.href = `${BASE_URL}lembur/perencanaan_lembur`;
                });
            }
        } else {
            Swal.fire({
                title: '',
                text: res.message,
                icon: 'error',
                allowOutsideClick: false,
                showCancelButton: false,
                confirmButtonText: 'OK'
            });
        }
    });
});

$('#modal-error').on('hidden.bs.modal', function () {
    location.href = `${BASE_URL}lembur/perencanaan_lembur`;
});

const laod_table_error = (data)=>{
    $('#tabel-error').DataTable({
        iDisplayLength: 10,
        stateSave: true,
        responsive: true,
        processing: true,
        serverSide: false,
        ordering: false,
        data: data,
        columns: [
            {
                data: 'tanggal'
            }, {
                data: 'list_np',
            }, {
                data: 'jam_lembur',
            }, {
                data: 'jenis_hari',
            }, {
                data: 'alasan_lembur',
            }, {
                data: 'reason',
            }
        ]
    });
}