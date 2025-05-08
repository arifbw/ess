<link href="<?= base_url('asset/select2/select2.min.css') ?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>asset/daterangepicker/daterangepicker.css" />

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $judul ?></h1>
            </div>
        </div>

        <?php if (!empty($this->session->flashdata('success'))) : ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($this->session->flashdata('warning'))) : ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('warning'); ?>
            </div>
        <?php endif; ?>
        <?php if (@$regulasi) : ?>
            <div class="alert alert-info">
                <p><?= $regulasi ?></p>
            </div>
        <?php endif; ?>

        <?php if ($this->akses["CRUD"]) : ?>
            <div class="form-group">
                <div class="row table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="laporan_perubahan_email_table">
                        <thead>
                            <tr>
                                <th class="text-center" style="max-width: 5%">No</th>
                                <th class="text-center">Nama Karyawan</th>
                                <th class="text-center">NP Karyawan</th>
                                <th class="text-center">Email Lama</th>
                                <th class="text-center">Email Baru</th>
                                <th class="text-center">NP Atasan</th>
                                <th class="text-center">Nama Atasan</th>
                                <th class="text-center" style="max-width: 15%">Keterangan</th>
                                <th class="text-center" style="max-width: 15%">status</th>
                                <th class="text-center" style="max-width: 15%">#</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="label_modal_batal">Status <?= @$judul ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="get-approve"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>


        <?php if (@$akses["CRUD"]) : ?>
            <div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger" id="title-inactive">
                                <b>Hapus <?= $judul ?></b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </h5>
                        </div>

                        <div class="modal-body">
                            <h5 id="message-inactive"></h5>
                        </div>
                        <div class="modal-footer">
                            <a href="" id="inactive-action" class="btn btn-success">Approve</a>
                            <a href="" id="inactive-action_1" class="btn btn-danger">Tolak</a>
                            
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url() ?>asset/daterangepicker/daterangepicker.min.js"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js') ?>"></script>

<script>
    $(document).ready(function() {
    
            $('#modal-inactive').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button yang memicu modal
                var idToDelete = button.data('id'); // Mendapatkan ID data yang dipilih
                console.log(idToDelete)
                var modal = $(this);
                var button = $(event.relatedTarget); // Button yang memicu modal
                var nama_karyawan = button.closest('tr').find('td:eq(1)').text(); 
                var np_karyawan = button.closest('tr').find('td:eq(2)').text(); 
                var emailLama = button.closest('tr').find('td:eq(3)').text(); // Mengambil teks dari kolom email lama
                var emailBaru = button.closest('tr').find('td:eq(4)').text(); // Mengambil teks dari kolom email baru
                var modal = $(this);

                modal.find('#message-inactive').html('<b>Nama:</b> ' + nama_karyawan +'<br><b>NP Karyawan:</b> ' + np_karyawan +'<br><b>Email Lama:</b> ' + emailLama + '<br><b>Email Baru:</b> ' + emailBaru);


                modal.find('#inactive-action').on('click', function(e) {
                    e.preventDefault(); // Mencegah aksi default dari link

                    $.post('<?= base_url() ?>pelaporan/persetujuan/pembaruan_email/approve/' + idToDelete, function(response) {
                        if (response === 'success') {
                           
                            window.location.reload(); // Memuat ulang halaman setelah penghapusan berhasil
                        } else {
                            alert('Gagal Approve');
                        }
                    });
                });

                modal.find('#inactive-action_1').on('click', function(e) {
                    e.preventDefault(); // Mencegah aksi default dari link

                    $.post('<?= base_url() ?>pelaporan/persetujuan/pembaruan_email/tolak/' + idToDelete, function(response) {
                        if (response === 'success') {
                            
                            window.location.reload(); // Memuat ulang halaman setelah penghapusan berhasil
                        } else {
                            alert('Gagal menolak');
                        }
                    });
                });
            });
      


        $('#laporan_perubahan_email_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo base_url('pelaporan/persetujuan/pembaruan_email/tabel_laporan_perubahan_email'); ?>",
                "type": "POST"
            },
            "columns": [{
                    "data": "0"
                }, // Untuk nomor urut
                {
                    "data": "1"
                }, // Kolom np_karyawan
                {
                    "data": "2"
                }, // Kolom email_lama
                {
                    "data": "3"
                }, // Kolom email_baru
                {
                    "data": "4"
                }, // Kolom np_atasan
                {
                    "data": "5"
                }, // Kolom nama_atasan
                {
                    "data": "6"
                }, // Kolom keterangan
                {
                    "data": "7"
                }, // Kolom status
                {
                    "data": "8"
                } ,
                {
                    "data": "9"
                } // Kolom cancel
            ]
        });
    });

    function getNamaAtasan() {
        var np_atasan = $('#np_atasan').val();
        if (np_atasan.length > 3) {
            var np_karyawan = $('#np_karyawan').val();
            var insert_absence_type = $('#add_absence_type').val();

            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "pembaruan_email/ajax_getNama_approval",
                data: {
                    "np_aprover": np_atasan,
                    "np_karyawan": np_karyawan
                },
                success: function(msg) {
                    if (msg.status == false) {
                        alert(msg.message);
                        $('#nama_atasan').val('');
                        $('#jabatan_atasan').val('');
                        $('#np_atasan').val('');
                    } else {
                        $('#nama_atasan').val(msg.data.nama);
                        $('#jabatan_atasan').val(msg.data.jabatan);
                    }
                }
            });
        } else if (np_atasan.length < 4) {
            $('#approval_1_input').val('');
            $('#approval_1_input_jabatan').val('');
        }
    }

    $('#np_karyawan').change(function() {
        var selectedValue = $(this).val(); // Mendapatkan nilai yang dipilih dari dropdown
        var selectedText = $(this).find('option:selected').text(); // Mendapatkan teks yang dipilih dari dropdown

        // Mengambil teks setelah tanda spasi pertama
        var textAfterSpace = selectedText.substring(selectedText.indexOf(' ') + 1);

        $('#nama_karyawan').val(textAfterSpace); // Mengisi nilai input 'nama_karyawan' dengan teks yang dipilih dari dropdown
    });
</script>