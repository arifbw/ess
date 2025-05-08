
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Komentar</h1>
                <div id="postingan-content">
                </div>
                <table id="comments_table" class="display">
                    <thead>
                        <tr>
                            <th>Komentar :</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <a href="<?php echo site_url('monitoring_socnet/home'); ?>" class="btn btn-default">Kembali</a>
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

<script>
    function renderCommentRow(comment, level = 0) {
        let commentHtml = '';
        let repliesHtml = '';
<script>
    function renderCommentRow(comment, level = 0) {
        let commentHtml = '';
        let repliesHtml = '';

        let marginLeft = level * 50 + 'px';
        let marginLeft = level * 50 + 'px';

        commentHtml += `<div style="margin-left: ${marginLeft};">`;
        commentHtml += `<span style="font-size: 1em;">${comment.comment}</span>`;
        let toggleAction = comment.hidden_at ? 'Tampilkan' : 'Sembunyikan';
        let toggleUrl = comment.hidden_at ? '<?= site_url('monitoring_socnet/home/unhide_comment/') ?>' + comment.id : '<?= site_url('monitoring_socnet/home/hide_comment/') ?>' + comment.id;
        commentHtml += `<div style="margin-left: ${marginLeft};">`;
        commentHtml += `<span style="font-size: 1em;">${comment.comment}</span>`;
        let toggleAction = comment.hidden_at ? 'Tampilkan' : 'Sembunyikan';
        let toggleUrl = comment.hidden_at ? '<?= site_url('monitoring_socnet/home/unhide_comment/') ?>' + comment.id : '<?= site_url('monitoring_socnet/home/hide_comment/') ?>' + comment.id;

        commentHtml += `<br><span style="font-size: 0.8em; color: #666;">
            <i class="fa fa-calendar" aria-hidden="true"></i> ${formatDate(comment.created_at)} | ${comment.np} |
            <a href="#" onclick="confirmAction('${toggleAction} komentar ini?', '${toggleUrl}'); return false;">${toggleAction}</a>
        </span>`;
        commentHtml += `<br><span style="font-size: 0.8em; color: #666;">
            <i class="fa fa-calendar" aria-hidden="true"></i> ${formatDate(comment.created_at)} | ${comment.np} |
            <a href="#" onclick="confirmAction('${toggleAction} komentar ini?', '${toggleUrl}'); return false;">${toggleAction}</a>
        </span>`;

        commentHtml += `</div>`;
        commentHtml += `</div>`;

        return {
            commentHtml: commentHtml,
            id: comment.id
        };
    }

    function displayPostContent(content, np, nama, created_at, commentCount) {
        document.getElementById('postingan-content').innerHTML = `
            <span style="font-size: 1.25em;">${content}</span><br>
            <span style="font-size: 0.8em; color: #666;"><i class="fa fa-calendar" aria-hidden="true"></i> ${formatDate(created_at)} 
            | <strong>${np}</strong></span> <br>
            <span style="font-size: 0.8em; color: #666;">Menampilkan ${commentCount} komentar</span>
        `;
    }

    $(document).ready(function() {
        $('#comments_table').DataTable({
            "processing": true,
            "paging": false,
            "state": true,
            "ordering": false,
            "ajax": {
                "url": "<?= site_url('monitoring_socnet/home/get_comments/'.$post_id); ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    var allComments = json.data;
                    var topLevelComments = allComments.filter(comment => !allComments.some(c => c.id === comment.comment_id));
                    var dataRows = [];
                    
                    // Assume the first comment has post details (adjust as necessary)
                    if (allComments.length > 0) {
                        displayPostContent(
                            allComments[0].content,
                            allComments[0].np,
                            allComments[0].nama,
                            allComments[0].created_at,
                            json.recordsTotal
                        );
                    }

                    function processComment(comment, level) {
                        dataRows.push(renderCommentRow(comment, level));
                        const replies = allComments.filter(c => c.comment_id === comment.id);
                        replies.forEach(reply => processComment(reply, level + 1));
                    }
                    function processComment(comment, level) {
                        dataRows.push(renderCommentRow(comment, level));
                        const replies = allComments.filter(c => c.comment_id === comment.id);
                        replies.forEach(reply => processComment(reply, level + 1));
                    }

                    topLevelComments.forEach(comment => processComment(comment, 0));
                    topLevelComments.forEach(comment => processComment(comment, 0));

                    return dataRows;
                }
            },
            "columns": [
                { "data": "commentHtml", "render": function(data, type, row) { return data; } },
                { "data": null, "render": function(data, type, row) {
                    return '';
                } }
            ],
            "dom": '<"top"f>rt<"bottom"p><"clear">'
        });
    });
                    return dataRows;
                }
            },
            "columns": [
                { "data": "commentHtml", "render": function(data, type, row) { return data; } },
                { "data": null, "render": function(data, type, row) {
                    return '';
                } }
            ],
            "dom": '<"top"f>rt<"bottom"p><"clear">'
        });
    });

    function formatDate(dateString) {
        var date = new Date(dateString);
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        var hour = date.getHours().toString().padStart(2, '0');
        var minute = date.getMinutes().toString().padStart(2, '0');
    function formatDate(dateString) {
        var date = new Date(dateString);
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        var hour = date.getHours().toString().padStart(2, '0');
        var minute = date.getMinutes().toString().padStart(2, '0');

        var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return day + ' ' + monthNames[month - 1] + ' ' + year + ' ' + hour + ':' + minute;
    }
        var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return day + ' ' + monthNames[month - 1] + ' ' + year + ' ' + hour + ':' + minute;
    }

    function confirmAction(message, url) {
        Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        $('#comments_table').DataTable().ajax.reload();
                        Swal.fire(
                            'Sukses!',
                            'Aksi telah dilakukan.',
                            'success'
                        );
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan: ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
