<div class="modal-header">
    <h3 class="modal-title" id="modal-title-approval"></h3>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-10">
            <table>
                <tr>
                    <th>Alamat</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a><?= $header['alamat'] ?></a></b></td>
                </tr>
                <tr>
                    <th>No. Kontrol</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a><?= $header['no_kontrol'] ?></a></b></td>
                </tr>
                <tr>
                    <th>Bulan Pemakaian</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a id="bulan-pemakaian"></a></b></td>
                </tr>
                <tr>
                    <th>Bulan Pembayaran</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a id="bulan-pembayaran"></a></b></td>
                </tr>
                <tr>
                    <th>Tagihan</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a id="tagihan"></a></b></td>
                </tr>
                <tr>
                    <th>Admin</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a id="admin"></a></b></td>
                </tr>
                <tr>
                    <th>Plafon</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a id="plafon"></a></b></td>
                </tr>
                <tr>
                    <th>Beban Pegawai</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a id="beban-pegawai"></a></b></td>
                </tr>
                <tr>
                    <th>Beban Perusahaan</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a id="beban-perusahaan"></a></b></td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a><?= $header['keterangan'] ?></a></b></td>
                </tr>
                <tr>
                    <th>Dibuat Tanggal</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a><?= $header['created_at'] ?></a></b></td>
                </tr>
                <?php if($header['submit_date']!=null){?>
                <tr>
                    <th>Disubmit Tanggal</th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                    <td><b><a><?= $header['submit_date'] ?></a></b></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <br>

    <div class="alert alert-<?= @$approval_warna ?>">
        <strong><a class="text-<?= @$approval_warna ?>"><?= @$header['approval_atasan_np'].' | '.@$header['approval_atasan_nama'] ?></a></strong><br>
        <p><?= @$approval_status ?></p>
        <?php if (@$header['approval_status']=='2') { ?>
        <p style="margin-top: 0">Alasan : <?= @$header['approval_atasan_alasan'] ?></p>
        <?php } ?>
    </div>

    <?php if (@$header['approval_status']=='3' || @$header['approval_status']=='4' || @$header['approval_status']=='5') { ?>
    <div class="alert alert-<?= @$sdm_warna ?>">
        <strong><a class="text-<?= @$sdm_warna ?>">Verifikasi Persetujuan SDM</a></strong><br>
        <p><?= @$sdm_status ?></p>
        <?php if (@$header['approval_status']=='4') { ?>
        <p style="margin-top: 0">Alasan : <?= @$header['alasan_sdm'] ?></p>
        <?php } ?>
    </div>
    <?php } ?>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-cancel-approval">Close</button>
</div>

<script>
    var judul = '<?= $header['judul']?>';
    var header_pemakaian_bulan = '<?= $header['pemakaian_bulan']?>';
    var header_pembayaran_bulan = '<?= $header['pembayaran_bulan']?>';
    var tagihan = '<?= $header['tagihan']?>';
    var admin = '<?= $header['biaya_admin']?>';
    var plafon = '<?= $header['plafon']?>';
    var beban_pegawai = '<?= $header['beban_pegawai']?>';
    var beban_perusahaan = '<?= $header['beban_perusahaan']?>';
	
    $(document).ready(function() {
		
		$('#modal-title-approval').html(`${judul}`);
		$('#bulan-pemakaian').html(`${moment(header_pemakaian_bulan).format('MMMM YYYY')}`);
		$('#bulan-pembayaran').html(`${moment(header_pembayaran_bulan).format('MMMM YYYY')}`);
		$('#tagihan').html(`Rp ${parseInt(tagihan).toLocaleString()}`);
		$('#admin').html(`Rp ${parseInt(admin).toLocaleString()}`);
		$('#plafon').html(`Rp ${parseInt(plafon).toLocaleString()}`);
		$('#beban-pegawai').html(`Rp ${parseInt(beban_pegawai).toLocaleString()}`);
		$('#beban-perusahaan').html(`Rp ${parseInt(beban_perusahaan).toLocaleString()}`);
	});
</script>