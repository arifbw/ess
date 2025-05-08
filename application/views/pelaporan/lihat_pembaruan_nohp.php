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

        <?php if (@$akses["tambah"]) : ?>
            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Tambah <?= $judul ?></a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <form role="form" action="<?= base_url(); ?>pelaporan/pembaruan_nohp/action_insert" id="formulir_tambah" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>NP Karyawan *</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <!-- Input NP Karyawan -->
                                        <select class="form-control" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
                                            <option></option>
                                            <?php foreach ($array_daftar_karyawan->result_array() as $value) : ?>
                                                <option value='<?= $value['no_pokok'] ?>'><?= $value['no_pokok'] . " " . $value['nama'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input class="form-control" type="hidden" name="nama_karyawan" id="nama_karyawan" required readonly>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>No HP Lama *</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <!-- Input nohp Lama -->
                                        <input class="form-control" type="nohp" name="nohp_lama" id="nohp_lama" placeholder="Masukkan nohp Lama" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>No HP Baru *</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <!-- Input nohp Baru -->
                                        <input class="form-control" type="nohp" name="nohp_baru" id="nohp_baru" placeholder="Masukkan nohp Baru" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>NP Atasan *</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <!-- Input NP Atasan -->
                                        <input class="form-control" name="np_atasan" value="" id="np_atasan" value="" onchange="getNamaAtasan()" min="4"><small class="form-text text-muted">Atasan Langsung Minimal Kepala Seksi</small><strong> (wajib diisi)</strong>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Nama Atasan *</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <!-- Input Nama Atasan -->
                                        <input class="form-control" type="text" name="nama_atasan" value="" id="nama_atasan" value="" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Jabatan Atasan *</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="text" name="jabatan_atasan" value="" id="jabatan_atasan" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Keterangan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <!-- Input Keterangan -->
                                        <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Masukkan Keterangan"></textarea>
                                    </div>
                                </div>

                                <!-- Input untuk Status Approve dengan nilai default 'Menunggu Persetujuan' -->
                                <input type="hidden" name="status_approve" value="Menunggu Persetujuan">

                                <div class="row">
                                    <div class="col-lg-9 text-right">
                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <?php if ($this->akses["lihat"]) : ?>
            <div class="form-group">
                <div class="row table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="laporan_perubahan_nohp_table">
                        <thead>
                            <tr>
                                <th class="text-center" style="max-width: 5%">No</th>
                                <th class="text-center">Nama Karyawan</th>
                                <th class="text-center">No HP Lama</th>
                                <th class="text-center">No HP Baru</th>
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


        <?php if (@$akses["hapus"]) : ?>
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
                            <a href="" id="inactive-action" class="btn btn-danger">Ya, Hapus</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
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
                var nohpLama = button.closest('tr').find('td:eq(2)').text(); // Mengambil teks dari kolom nohp lama
                var nohpBaru = button.closest('tr').find('td:eq(3)').text(); // Mengambil teks dari kolom nohp baru
                var modal = $(this);

                modal.find('#message-inactive').html('<b>nohp Lama:</b> ' + nohpLama + '<br><b>nohp Baru:</b> ' + nohpBaru);


                modal.find('#inactive-action').on('click', function(e) {
                    e.preventDefault(); // Mencegah aksi default dari link

                    $.post('<?= base_url() ?>pelaporan/pembaruan_nohp/delete_data/' + idToDelete, function(response) {
                        if (response === 'success') {
                            alert('Data berhasil dihapus');
                            window.location.reload(); // Memuat ulang halaman setelah penghapusan berhasil
                        } else {
                            alert('Gagal menghapus data');
                        }
                    });
                });
            });
      


        $('#laporan_perubahan_nohp_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo base_url('pelaporan/pembaruan_nohp/tabel_laporan_perubahan_nohp'); ?>",
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
                }, // Kolom nohp_lama
                {
                    "data": "3"
                }, // Kolom nohp_baru
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
                url: "pembaruan_nohp/ajax_getNama_approval",
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