<link href="<?= base_url('asset/select2/select2.min.css') ?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>asset/daterangepicker/daterangepicker.css" />

<div id="page-wrapper">
    <div class="container-fluid">
        <?php if (@$akses["tambah"]) : ?>
            <div class="row">
                <div class="row ">
                    <div class="col-lg-9">
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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Tambah <?= $judul ?></a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <form role="form" action="<?= base_url(); ?>poin_reward/contribution/action_insert_mycontribution" id="formulir_tambah" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <input type="hidden" id='edit_id' name='edit_id'>
                                    <div class="col-lg-2">
                                        <label>NP karyawan</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control select2" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
                                            <?php foreach ($array_daftar_karyawan->result_array() as $value) : ?>
                                                <option value='<?= $value['no_pokok'] ?>'><?= $value['no_pokok'] . " " . $value['nama'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Perihal</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" name="perihal" id="perihal" placeholder="Masukkan perihal" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Jenis Dokumen</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-control select2" name="jenis_dokumen_id" id="jenis_dokumen_id" style="width: 100%" required>
                                            <?php foreach ($ref_jenis_dokumen as $value) : ?>
                                                <option value="<?= $value['id'] ?>"><?= $value['nama'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Tanggal dokumen</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input type="date" class="form-control" name="tanggal_dokumen" id="tanggal_dokumen" placeholder="Masukkan tanggal dokumen" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label>Dokumen</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <input class="form-control" type="file" name="dokumen" id="dokumen" accept="application/pdf,image/*" required>
                                        <small class="form-text text-danger">Dokumen PDF/JPG Max 2MB</small><!-- <strong> (wajib diisi)</strong> -->
                                    </div>
                                    <div class="col-lg-3" id="edit_dokumen"></div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-9 text-right">
                                        <input type="submit" name="submit" value="Submit" class="btn btn-submit-form btn-primary mr-2 btn-submit-form">
                                        <button type="button" class="btn btn-secondary btn-cancel-form" onclick="cancel()">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php if ($this->session->userdata('grup') != '5'): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" id="btn-import">Import <?= $judul ?></a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                            <div class="panel-body">
                                <form role="form" action="<?= base_url(); ?>poin_reward/contribution/import_excel" id="formulir_import" method="post" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                            <label for="dokumen">File Excel</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" type="file" name="dokumen" id="dokumen" accept=".xls,.xlsx" required>
                                            <small class="form-text text-danger">Dokumen Excel Max 2MB</small><!-- <strong> (wajib diisi)</strong> -->
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-9 text-right">
                                            <input type="submit" name="submit" value="Submit" class="btn btn-primary mr-2 btn-submit-import">
                                            <a href="<?= base_url('poin_reward/contribution/create_template'); ?>" class="btn btn-info">Unduh Template</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if ($this->session->userdata('grup') == 30) : ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div style="width:100%;" class="form-group">
                            <label>Satuan kerja</label>
                            <select class="form-control select2" id="satuan_kerja">
                                <option value="all">Semua</option>
                                <?php foreach ($ref_satuan_kerja as $item): ?>
                                    <option value="<?= $item['kode_unit']; ?>"><?= $item['nama_unit']; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div style="width:100%;" class="form-group">
                            <label>Karyawan</label>
                            <select class="form-control select2" id="karyawan" onchange="refresh_table_serverside()">
                                <option value="all">Semua</option>
                                <?php foreach ($ref_karyawan as $item): ?>
                                    <option value="<?= $item['no_pokok']; ?>"><?= $item['no_pokok']; ?> - <?= $item['nama']; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <div class="row">
                <div class="col-lg-8">
                    <div style="width:100%;" class="form-group">
                        <label>Tanggal</label>
                        <input name="date_range" id="date_range" class="form-control datepicker" onchange="refresh_table_serverside()">
                        <input type="hidden" id="start_date">
                        <input type="hidden" id="end_date">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div style="width:100%;" class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="status" onchange="refresh_table_serverside()">
                            <option value="all">Semua</option>
                            <option value="0">Proses</option>
                            <option value="1">Disetujui</option>
                            <option value="2">Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($this->akses["lihat"]) : ?>
            <div class="row table-responsive">
                <table width="100%" class="table table-striped table-bordered table-hover" id="daftar_mycontribution">
                    <thead>
                        <tr>
                            <th class="text-center no-sort" style="max-width: 5%">No</th>
                            <th class="text-center">Pegawai</th> <!-- style="max-width: 15%" -->
                            <th class="text-center no-sort">Perihal</th>
                            <th class="text-center no-sort">Tanggal dokumen</th>
                            <th class="text-center no-sort">Jenis dokumen</th>
                            <th class="text-center no-sort">Status</th>
                            <th class="text-center" style="min-width: 165px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
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
                            <h4 class="modal-title text-danger" id="title-inactive">
                                <b>Hapus <?= $judul ?></b>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </h4>
                        </div>

                        <div class="modal-body">
                            <h4 id="message-inactive"></h4>
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
<script type="text/javascript">
    var all_atasan_1_np = [],
        all_atasan_1_jabatan = [],
        all_atasan_2_np = [],
        all_atasan_2_jabatan = [];
    $('#multi_select').select2({
        closeOnSelect: false
    });
    var startDate = moment().startOf('month');
    var endDate = moment().endOf('month');
    $(document).ready(function() {
        $('.datetimepicker5').datetimepicker({
            format: 'HH:mm'
        });

        $("#formulir_tambah").on('submit', function(e) {
            Swal.fire({
                title: "Loding...",
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            })
            $("#btn-cancel-form").prop('disabled', true);
            $("#btn-submit-form").prop('disabled', true);
        });
        $("#formulir_import").on('submit', function(e) {
            Swal.fire({
                title: "Loding...",
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            })
            $("#btn-submit-import").prop('disabled', true);
        });

        $('#date_range').daterangepicker({
            startDate,
            endDate,
            locale: {
                format: 'DD-MM-YYYY'
            }
        }, function(start, end) {
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        })
        $('#satuan_kerja').on('change', function(e) {
            refresh_table_serverside()
            $.get(`<?= base_url('poin_reward/contribution/get_karyawan_by_satuan_kerja'); ?>/${$(this).val()}`, function(res) {
                const select = $('#karyawan');
                select.empty();
                select.append($('<option></option>')
                    .val('all')
                    .text(`Semua`))
                JSON.parse(res).map(function(item) {
                    var $option = $('<option></option>')
                        .val(item.no_pokok)
                        .text(`${item.no_pokok} - ${item.nama}`);

                    select.append($option);
                });
            })
        })
        $('.select2').select2();
        $('#np_karyawan').select2({
            placeholder: "Nomor Pokok Karyawan"
        });

        $('.tanggal').datetimepicker({
            format: 'Y-MM-D'
        });

        $('#daftar_mycontribution').DataTable().destroy();
        table_serverside();
    });

    function refresh_table_serverside() {
        $('#daftar_mycontribution').DataTable().destroy();
        table_serverside();
    }

    function table_serverside() {
        var table;

        table = $('#daftar_mycontribution').DataTable({

            "iDisplayLength": 10,
            "language": {
                "url": "<?= base_url('asset/datatables/Indonesian.json'); ?>",
                "sEmptyTable": "Tidak ada data di database",
                "emptyTable": "Tidak ada data di database"
            },

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?= site_url("poin_reward/contribution/tabel_mycontribution") ?>",
                "data": {
                    status: $("#status").val(),
                    satuan_kerja: $("#satuan_kerja").val(),
                    start_date: $("#start_date").val(),
                    end_date: $("#end_date").val(),
                    karyawan: $("#karyawan").val()
                },
                "type": "POST",
            },

            //Set column definition initialisation properties.
            "columnDefs": [{
                    "targets": 'no-sort', //first column / numbering column
                    "orderable": false, //set not orderable
                },
                {
                    "targets": [0],
                    "className": "text-center"
                }
            ],

        });

    };
</script>

<script>
    const edit = async (data) => {
        $('#btn-tambah').html('Edit My Contribution');
        if ($("#collapseOne").is(":visible")) {
            console.log('Already shown');
        } else {
            $('#btn-tambah').trigger('click');
        }

        $('#dokumen').removeAttr('required')

        let fields = [
            'np_karyawan',
            'perihal',
            'tanggal_dokumen',
            'jenis_dokumen_id',
        ];
        $('#formulir_tambah').find(`[name=edit_id]`).val($(data).data('id'));
        for (const i of fields) {
            $(`#${i}`).val($(data).data(`${i}`));
        }

        $('#np_karyawan').trigger('change');
        $('#jenis_dokumen_id').trigger('change');
        $('#edit_dokumen').html('<label class="text-danger"><b>Upload Ulang Jika Ingin Mengganti File</b></label>');

        if ($('#formulir_tambah').find('[name=edit_id]').length) {} else {
            $('<input>').attr({
                type: 'hidden',
                name: 'edit_id',
                value: $(data).data('id')
            }).appendTo('#formulir_tambah');
        }
        $("html, body").animate({
            scrollTop: 0
        }, "slow");
    }

    const cancel = () => {
        $('#btn-tambah').html('Tambah My Contribution');

        $('#dokumen').attr('required')
        let fields = [
            'np_karyawan',
            'perihal',
            'tanggal_dokumen',
            'jenis_dokumen_id',
        ];
        for (const i of fields) {
            $(`#${i}`).val('');
        }

        $('#formulir_tambah').find(`[name=edit_id]`).val('');

        $('#np_karyawan').trigger('change');
        $('#jenis_dokumen').trigger('change');

        $('#edit_dokumen').html('');

        if ($("#collapseOne").is(":visible")) {
            $('#btn-tambah').trigger('click');
        }

        $("html, body").animate({
            scrollTop: 0
        }, "slow");
    }


    $(document).on('click', '.detail_button', function(e) {
        e.preventDefault();
        $("#modal_detail").modal('show');
        $.post('<?php echo site_url("poin_reward/contribution/view_detail") ?>', {
                id_: $(this).attr('data-id')
            },
            function(e) {
                $(".get-approve").html(e);
            }
        );
    });

    $(document).on('click', '.hapus', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('<?= site_url("poin_reward/contribution/hapus") ?>/' + $(this).data('id') + '/' + $(this).data('np'),
                    function(get) {
                        ret = JSON.parse(get);
                        if (ret.status == true) {
                            Swal.fire(
                                ret.msg,
                                '',
                                'success'
                            );
                            refresh_table_serverside();
                        } else {
                            Swal.fire(
                                ret.msg,
                                '',
                                'error'
                            );
                        }
                    }
                );
            }
        });
    });
</script>