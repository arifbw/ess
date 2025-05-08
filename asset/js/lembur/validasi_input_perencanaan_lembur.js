function getAllSelectedNp() {
    let allNp = [];
    $('#list-table .list_np').each(function() {
        let selectedNp = $(this).val();
        if (selectedNp) {
            allNp = allNp.concat(selectedNp);
        }
    });
    let uniqueNp = [...new Set(allNp)];
    return uniqueNp;
}

function validateJumlahJam() {
    let uniqueNp = getAllSelectedNp();
    let allJumlahJam = {}

    uniqueNp.forEach(np => {
        let findSelect = $('#list-table .list_np').find(`option:selected:contains('${np}')`);
        for (const i of findSelect) {
            var parentTr = $(i).closest('tr');
            
            if(typeof allJumlahJam[`${np}`]=='undefined'){
                allJumlahJam[`${np}`] = {};
                var jumlahJamHariKerja = 0;
                var jumlahJamHariLibur = 0;
                var arrayOfTrId = [];
            }
            
            if(parentTr.find('.jenis_hari').val()=='kerja') jumlahJamHariKerja += parseFloat(parentTr.find('.jam_lembur').val()) || 0;
            else if(parentTr.find('.jenis_hari').val()=='libur') jumlahJamHariLibur += parseFloat(parentTr.find('.jam_lembur').val()) || 0;

            arrayOfTrId.push(parentTr.attr('id'));
            allJumlahJam[`${np}`] = { jumlahJamHariKerja, jumlahJamHariLibur, arrayOfTrId };
        }

    });
    return allJumlahJam;
}

function checkPeriodAndUnit(){
    let data = new FormData();
    data.append('kode_unit', $("#kode_unit").val());
    data.append('tanggal_mulai', startDate);
    data.append('tanggal_selesai', endDate);
    if(typeof perencanaan!='undefined') data.append('perencanaan_id', perencanaan.id);
    $.ajax({
        url: `${BASE_URL}lembur/perencanaan_lembur/cek_perencanaan`,
        type: 'POST',
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            
        },
    }).then((res) => {
        if(res.status===false){
            Swal.fire({
                title: '',
                text: res.message,
                icon: 'info',
                allowOutsideClick: false,
                showCancelButton: false,
                confirmButtonText: 'OK'
            }).then(()=>{
                
            });
        }
    });
}

function validateAllInput(){
    barisOk = [];
    barisNotOk = [];
    let tbody = list_table.find('tbody');
    for (const i of temp_array_of_id) {
        let tr = tbody.find(`#tr-${i}`);
        let list_np = tr.find('.list_np').val();
        let tanggal = tr.find('.tanggal').val();
        let jenis_hari = tr.find('.jenis_hari').val();
        let mst_kategori_lembur_id = tr.find('.mst_kategori_lembur_id').val();
        let jam_lembur = parseFloat(tr.find('.jam_lembur').val()) || 0;
        for (const np of list_np) {
            if(tanggal >= startDate && tanggal <= endDate){
                let findNpTanggal = barisOk.find(o=>{ return o.tanggal==tanggal && o.list_np==np && o.mst_kategori_lembur_id==mst_kategori_lembur_id; });
                if(typeof findNpTanggal!='undefined'){
                    barisNotOk.push({
                        'id': i,
                        'tanggal': tanggal,
                        'list_np': np,
                        'jumlah_karyawan': 1,
                        'jam_lembur': jam_lembur,
                        'jenis_hari': jenis_hari,
                        'mst_kategori_lembur_id': mst_kategori_lembur_id,
                        'alasan_lembur': tr.find('.alasan_lembur').val(),
                        'reason': 'Data dobel'
                    });
                } else{
                    if(jenis_hari=='libur'){
                        if(jam_lembur <= 12){
                            barisOk.push({
                                'id': i,
                                'tanggal': tanggal,
                                'list_np': np,
                                'jumlah_karyawan': 1,
                                'jam_lembur': jam_lembur,
                                'jenis_hari': jenis_hari,
                                'mst_kategori_lembur_id': mst_kategori_lembur_id,
                                'alasan_lembur': tr.find('.alasan_lembur').val()
                            });
                        } else{
                            barisNotOk.push({
                                'id': i,
                                'tanggal': tanggal,
                                'list_np': np,
                                'jumlah_karyawan': 1,
                                'jam_lembur': jam_lembur,
                                'jenis_hari': jenis_hari,
                                'mst_kategori_lembur_id': mst_kategori_lembur_id,
                                'alasan_lembur': tr.find('.alasan_lembur').val(),
                                'reason': 'Jumlah jam melebihi'
                            });
                        }
                    } else{
                        const sumByNp = barisOk.filter(o=>{ return o.jenis_hari=='kerja'; }).reduce((acc, item) => {
                            if (acc[item.list_np]) {
                                acc[item.list_np] += item.jam_lembur;
                            } else {
                                acc[item.list_np] = item.jam_lembur;
                            }
                            return acc;
                        }, {});

                        if((sumByNp[np] || 0) + jam_lembur <= 18){
                            barisOk.push({
                                'id': i,
                                'tanggal': tanggal,
                                'list_np': np,
                                'jumlah_karyawan': 1,
                                'jam_lembur': jam_lembur,
                                'jenis_hari': jenis_hari,
                                'mst_kategori_lembur_id': mst_kategori_lembur_id,
                                'alasan_lembur': tr.find('.alasan_lembur').val()
                            });
                        } else{
                            barisNotOk.push({
                                'id': i,
                                'tanggal': tanggal,
                                'list_np': np,
                                'jumlah_karyawan': 1,
                                'jam_lembur': jam_lembur,
                                'jenis_hari': jenis_hari,
                                'mst_kategori_lembur_id': mst_kategori_lembur_id,
                                'alasan_lembur': tr.find('.alasan_lembur').val(),
                                'reason': 'Jumlah jam melebihi'
                            });
                        }
                    }
                }
            } else{
                barisNotOk.push({
                    'id': i,
                    'tanggal': tanggal,
                    'list_np': np,
                    'jumlah_karyawan': 1,
                    'jam_lembur': jam_lembur,
                    'jenis_hari': jenis_hari,
                    'mst_kategori_lembur_id': mst_kategori_lembur_id,
                    'alasan_lembur': tr.find('.alasan_lembur').val(),
                    'reason': 'Tanggal tidak sesuai periode'
                });
            }
        }
    }
    return { barisOk, barisNotOk };
}

function showWarningModal(){
    $('#modal-warning').modal('show');
    $('#tabel-warning').DataTable({
        "iDisplayLength": 10,
        "destroy": true,
        "stateSave": true,
        "responsive": true,
        "processing": true,
        "serverSide": false,
        "ordering": false,
        "data": barisNotOk,
        columns: [
            {
                data: 'list_np',
                render: function(data, type, row){
                    let findNp = mst_karyawan.find(o=>{ return o.no_pokok==data; });
                    if(typeof findNp!='undefined') return `${data} - ${findNp.nama}`;
                    else return data;
                }
            }, {
                data: 'tanggal',
            }, {
                data: 'jam_lembur',
            }, {
                data: 'reason',
            }
        ],
    });
}

$('.btn-force-submit').on('click', (e)=>{
    e.preventDefault();
    $('#modal-warning').modal('hide');
    $('#form-tambah').submit();
});

function getDataListFinal(){
    const grouped = barisOk.reduce((acc, item) => {
        // Create a unique key for each group
        const key = `${item.id}-${item.tanggal}-${item.jam_lembur}-${item.jenis_hari}-${item.mst_kategori_lembur_id}-${item.alasan_lembur}`;
        if (!acc[key]) {
            acc[key] = {...item, list_np: [item.list_np]};
        } else {
            acc[key].list_np.push(item.list_np);
        }
        return acc;
    }, {});
    
    // Convert the object back into an array and adjust the np field
    const finalArray = Object.values(grouped).map(item => ({
        ...item,
        list_np: item.list_np.join(','),
        jumlah_karyawan: item.list_np.length
    }));
    
    return finalArray;
}