<style>
    th {
        text-align: left;
    }
</style>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
    </head>
    <body>
        <div id="container">
            <h4 style="text-align: center;">DEPARTEMEN HRO - SEKSI REMUNERASI & HRIS</h4>
            <h4 style="text-align: center;"><?= strtoupper($title) ?></h4>
            <div id="body">
                <table>
                    <tr>
                        <th>I.</th>
                        <th colspan="2">DATA PEGAWAI</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>1.&nbsp;&nbsp;Nomor Pokok</td>
                        <td>:</td>
                        <td><?= $data['np_karyawan'] ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2.&nbsp;&nbsp;Nama</td>
                        <td>:</td>
                        <td><?= $data['nama_karyawan'] ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>3.&nbsp;&nbsp;Jabatan</td>
                        <td>:</td>
                        <td><?= $data['nama_jabatan'] ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>4.&nbsp;&nbsp;Unit Kerja</td>
                        <td>:</td>
                        <td><?= $data['nama_unit'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:5px"></td>
                    </tr>
                    <tr>
                        <th>II.</th>
                        <th colspan="2">DATA ANAK</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>1.&nbsp;&nbsp;Nama</td>
                        <td>:</td>
                        <td><?= $data['nama_anak'] ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2.&nbsp;&nbsp;Jenis Kelamin</td>
                        <td>:</td>
                        <td><?= ($data['jenis_kelamin_anak']=='L')?'Laki-Laki':'Perempuan'; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>3.&nbsp;&nbsp;Tempat Lahir</td>
                        <td>:</td>
                        <td><?= $data['tempat_lahir_anak'] ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>4.&nbsp;&nbsp;Tanggal Lahir</td>
                        <td>:</td>
                        <td><?= tanggal_indonesia($data['tanggal_lahir_anak']) ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>5.&nbsp;&nbsp;Anak Ke</td>
                        <td>:</td>
                        <td><?= $data['anak_ke'] ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>6.&nbsp;&nbsp;Alasan</td>
                        <td>:</td>
                        <td><?= $data['alasan'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding:5px"></td>
                    </tr>
                    <tr>
                        <th>III.</th>
                        <th colspan="2">KETERANGAN</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>1.&nbsp;&nbsp;Keterangan</td>
                        <td>:</td>
                        <td><?= $data['keterangan'] ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2.&nbsp;&nbsp;Dibuat Tanggal</td>
                        <td>:</td>
                        <td><?= datetime_indo($data['created_at']) ?></td>
                    </tr>
                </table>
            </div>

            <br>

            <table style="font-size: 12px;border: 1px solid black;border-collapse: collapse;" width="100%">
                <tr>
                    <td style="border: 1px solid black;padding:5px;">
                        <b>
                            <?= $data['approval_nama'] ?>
                        </b>
                    </td>
                    <td style="border: 1px solid black;padding:5px">
                        <p><?= $approval_status ?></p>
                        <?php if ($data['approval_status']=='2'): ?>
                            <p>Alasan : <b><?= $data['approval_alasan'] ?></b></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($data['approval_status']=='3' || $data['approval_status']=='4' || $data['approval_status']=='5'): ?>
                    <tr>
                        <td style="border: 1px solid black;padding:5px">
                            <b>
                                VERIFIKASI PERSETUJUAN SDM
                            </b>
                        </td>
                        <td style="border: 1px solid black;padding:5px">
                            <p><?= $sdm_status ?></p>
                            <?php if ($data['approval_status']=='4'): ?>
                                <p>Alasan : <b><?= $data['sdm_alasan'] ?></b></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($data['approval_status']=='5'): ?>
                    <tr>
                        <td style="border: 1px solid black;padding: 5px">
                            <b>
                                VERIFIKASI SUBMIT ERP SDM
                            </b>
                        </td>
                        <td style="border: 1px solid black;padding: 5px">
                            <p><?= $submit_status ?></p>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </body>
</html>