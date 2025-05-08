$('#form-tambah').on('submit', function(e){
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#form-tambah').LoadingOverlay('show');
        },
    }).then((res) => {
        $('#form-tambah').LoadingOverlay('hide', true);
        $('#form-tambah')[0].reset();
        load_table();
    }).fail((error) => {
        $('#form-tambah').LoadingOverlay('hide', true);
        alert('Internal Server Error');
    });
});