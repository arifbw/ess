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
                    <td>3.&nbsp;&nbsp;Perihal</td>
                    <td>:</td>
                    <td><?= $data['perihal'] ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>3.&nbsp;&nbsp;Jenis Dokumen</td>
                    <td>:</td>
                    <td><?= $data['jenis_dokumen'] ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>3.&nbsp;&nbsp;Tanggal Dokumen</td>
                    <td>:</td>
                    <td><?= $data['tanggal_dokumen'] ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="padding:5px"></td>
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
                    <p><?= $status_verifikasi ?></p>
                    <?php if ($data['status_verifikasi'] == '2'): ?>
                        <p>Alasan : <b><?= $data['approval_alasan'] ?></b></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>