<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $judul; ?></h1>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="search-input">Pencarian :</label>
                        <input type="text" class="form-control" id="search-input" placeholder="Tulis Judul Postingan, lalu tekan enter">
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="date-range">Pilih Rentang Tanggal:</label>
                        <input type="text" class="form-control" id="date-range">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="privacy-filter">Filter Privasi:</label>
                        <select class="form-control" id="privacy-filter">
                            <option value="">Semua</option>
                            <option value="1">Publik</option>
                            <option value="2">Teman</option>
                            <option value="5">Hanya Saya</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="order-by">Urutkan Berdasarkan:</label>
                        <select class="form-control" id="order-by">
                            <option value="">Semua</option>
                            <option value="likes">Jumlah Like</option>
                            <option value="dislikes">Jumlah Dislike</option>
                            <option value="comments">Jumlah Komentar</option>
                            <option value="shares">Jumlah Share</option>
                            <option value="reports">Jumlah Report</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="order-direction">Urutan:</label>
                        <select class="form-control" id="order-direction">
                            <option value="DESC">Descending</option>
                            <option value="ASC">Ascending</option>
                        </select>
                    </div>
                </div>
                <hr>

                <table id="posts_table" class="display">
                    <thead>
                        <tr>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Detail Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="reportDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('asset/jquery.dataTables') ?>/1.13.6/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="<?php echo base_url('asset/sweetalert2@11') ?>/sweetalert2.min.css"/>
<script src="<?= base_url('asset/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('asset/sweetalert2@11') ?>/sweetalert2@11.js"></script>
<script src="<?php echo base_url('asset/jquery.dataTables') ?>/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />
<script src="<?= base_url()?>asset/moment.js/2.29.1/moment.min.js"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var today = moment().format('YYYY-MM-DD');
        var startOfYear = moment().startOf('year').format('YYYY-MM-DD');

        $('#date-range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' hingga ',
                cancelLabel: 'Batal',
                applyLabel: 'Terapkan',
                fromLabel: 'Mulai',
                toLabel: 'Selesai',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            },
            startDate: startOfYear,
            endDate: today
        });

        window.table = $('#posts_table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            ajax: {
                url: "<?= site_url('monitoring_socnet/home/get_posts') ?>",
                type: "GET",
                dataSrc: "data",
                data: function(d) {
                    d.search = $('#search-input').val();
                    var dateRange = $('#date-range').val().split(' hingga ');
                    d.start_date = dateRange[0];
                    d.end_date = dateRange[1];
                    d.privacy_filter = $('#privacy-filter').val();
                    d.order_by = $('#order-by').val();
                    d.order_direction = $('#order-direction').val();
                }
            },
            columns: [
                {
                    data: null,
                    render: function(data, type, row) {
                        var createdAt = new Date(row.created_at);
                        var formattedDate = createdAt.toLocaleString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        formattedDate = formattedDate.replace(/pukul /, '');
                        var apiUrl = "<?php echo $api_url; ?>";
                        var toggleAction = row.active == 1 ? 
                            '<?= site_url('monitoring_socnet/home/toggle_post/') ?>' + row.id : 
                            '<?= site_url('monitoring_socnet/home/toggle_post/') ?>' + row.id;
                        var toggleText = row.active == 1 ? 'Sembunyikan' : 'Tampilkan';
                        var toggleColor = row.active == 1 ? 'red' : 'green';
                        var toggleConfirm = row.active == 1 ? 'Sembunyikan post ini?' : 'Tampilkan post ini?';

                        var reportLink = '';
                        if (row.reports > 0) {
                            reportLink = '&nbsp;&nbsp;<a href="#" onclick="showReportModal(\'' + row.id + '\'); return false;" style="color: blue; text-decoration: none;">Lihat Laporan</a>';
                        }

                        return '<span style="font-size: 1.25em;">' + row.content + '</span>' +
                                '<br>' +
                                '<span style="font-size: 0.8em; color: #666;">' +
                                '(' + row.privacy_name + ') ' + row.nama + ' | ' + row.np + '<br>' +
                                '<i class="fa fa-calendar" aria-hidden="true"></i> ' + formattedDate +
                                ' <a href="#" onclick="confirmAction(\'' + toggleConfirm + '\', \'' + toggleAction + '\'); return false;" style="color: ' + toggleColor + '; text-decoration: none;">' + toggleText + '</a>' +
                                '&nbsp;|&nbsp;<a href="' + '<?= site_url('monitoring_socnet/home/comments/') ?>' + row.id + '" style="color: orange; text-decoration: none;">Lihat Komentar</a>' +
                                '</span><br>' +
                                '<span style="font-size: 0.8em; color: #666;">' +
                                '<i class="fa fa-thumbs-up" aria-hidden="true"></i> ' + row.likes + ' &nbsp;' +
                                '<i class="fa fa-thumbs-down" aria-hidden="true"></i> ' + row.dislikes + ' &nbsp;' +
                                '<i class="fa fa-comment" aria-hidden="true"></i> ' + row.comments + ' &nbsp;' +
                                '<i class="fa fa-share" aria-hidden="true"></i> ' + row.shares + ' &nbsp;' +
                                (row.reports > 0 ? '<i class="fa fa-flag" aria-hidden="true" style="color: red;"></i> ' + row.reports + reportLink : '') +
                                '<br>' +
                                '</span>';
                    }
                }
            ]
        });

        $('#search-input, #date-range, #privacy-filter, #order-by, #order-direction').on('change', function() {
            table.draw();
        });
    });

    function showReportModal(postId) {
        $.ajax({
            url: '<?= site_url('monitoring_socnet/home/get_reports/') ?>' + postId,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var reportDetails = '<ul>';
                
                var bulan = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                
                $.each(data, function(index, report) {
                    var createdAt = new Date(report.created_at);
                    
                    var day = createdAt.getDate();
                    var month = bulan[createdAt.getMonth()];
                    var year = createdAt.getFullYear();
                    var hours = String(createdAt.getHours()).padStart(2, '0');
                    var minutes = String(createdAt.getMinutes()).padStart(2, '0');

                    var formattedDate = day + ' ' + month + ' ' + year + ' ' + 'pukul ' + hours + ':' + minutes;

                    reportDetails += '<li>Dilaporkan oleh: ' + report.nama + ' pada tanggal ' + formattedDate + '</li>';
                });
                reportDetails += '</ul>';
                $('#reportDetails').html(reportDetails);
                $('#reportModal').modal('show');
            },
            error: function() {
                $('#reportDetails').html('<p>Error fetching report details.</p>');
                $('#reportModal').modal('show');
            }
        });
    }

    function confirmAction(message, url) {
        Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Aksi berhasil dilakukan.',
                            icon: 'success'
                        });
                        table.draw();
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
