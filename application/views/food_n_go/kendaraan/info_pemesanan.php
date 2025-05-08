<?php
    if($_SESSION["grup"]==13){
        $status = status_pemesanan_admin([
            'tanggal_berangkat'=>$row->tanggal_berangkat,
            'verified'=>$row->verified,
            'status_persetujuan_admin'=>$row->status_persetujuan_admin,
            'id_mst_kendaraan'=>$row->id_mst_kendaraan,
            'id_mst_driver'=>$row->id_mst_driver,
            'pesanan_selesai'=>$row->pesanan_selesai,
            'rating_driver'=>$row->rating_driver,
            'is_canceled_by_admin'=>$row->is_canceled_by_admin
        ]);
    } else{
        $status = status_pemesanan([
            'tanggal_berangkat'=>$row->tanggal_berangkat,
            'verified'=>$row->verified,
            'status_persetujuan_admin'=>$row->status_persetujuan_admin,
            'id_mst_kendaraan'=>$row->id_mst_kendaraan,
            'id_mst_driver'=>$row->id_mst_driver,
            'pesanan_selesai'=>$row->pesanan_selesai,
            'rating_driver'=>$row->rating_driver,
            'is_canceled_by_admin'=>$row->is_canceled_by_admin
        ]);
    }
?>
<link rel="stylesheet" href="<?= base_url('asset/star-rating-svg-master/src/css/star-rating-svg.css')?>">
<table>
    <tbody>
        <tr>
            <td><b>Nomor Pemesanan</b></td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><b><?= $row->nomor_pemesanan;?></b></a></td>
        </tr>
        <tr>
            <td>Diajukan pada</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= hari_tanggal($row->created);?></a></td>
        </tr>
        <tr>
            <td>Pemesan a.n</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= '<b>'.@$row->np_karyawan.'</b> - '.@$row->nama?></a></td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->no_hp_pemesan?></a></td>
        </tr>
        <tr>
            <td>No. Ext Pemesan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->no_ext_pemesan?></a></td>
        </tr>
        <tr>
            <td>Unit Pemesan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->nama_unit_pemesan?></a></td>
        </tr>
        <tr>
            <td>PIC</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= '<b>'.@$row->np_karyawan_pic.'</b> - '.@$row->nama_pic?></a></td>
        </tr>
        <tr>
            <td>No. HP PIC</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->no_hp_pic?></a></td>
        </tr>
        <!-- <tr>
            <td>No. Ext PIC</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->no_ext_pic?></a></td>
        </tr> -->
        
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        
        <tr>
            <td>Unit Pemroses</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->unit_pemroses!=null ? 'Unit Kendaraan '.$row->unit_pemroses:''?></a></td>
        </tr>
        <!-- <tr>
            <td><b>Jenis Kendaraan</b></td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><b><?= @$row->nama_mst_kendaraan!=null ? $row->nama_mst_kendaraan:$row->jenis_kendaraan_request?></b></a></td>
        </tr> -->
        <tr>
            <td>Kota penjemputan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->nama_kota_asal?></a></td>
        </tr>
        <tr>
            <td>Lokasi penjemputan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= @$row->lokasi_jemput?></a></td>
        </tr>
        <tr>
            <td>Tujuan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= get_pemesanan_tujuan_small($row->id)?></a></td>
        </tr>
        <tr>
            <td><b>Jumlah Penumpang</b></td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><b><?= @$row->jumlah_penumpang?></b></a></td>
        </tr>
        <tr>
            <td><b>Waktu Berangkat</b></td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><b><?= hari_tanggal($row->tanggal_berangkat).' @'.$row->jam;?></b></a></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><?= @$row->keterangan?></td>
        </tr>
        <tr>
            <td>Tipe Perjalanan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= tipe_perjalanan([
                        'tanggal_awal'=>$row->tanggal_awal,
                        'tanggal_akhir'=>$row->tanggal_akhir,
                        'is_inap'=>$row->is_inap,
                        'is_pp'=>$row->is_pp
                    ])?></a></td>
        </tr>
        
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        
        <?php if(@$class_name!='Data_pemesanan'):?>
        <tr>
            <td>Approver</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= '<b>'.@$row->verified_by_np.'</b> - '.@$row->verified_by_nama?></a></td>
        </tr>
        <?php endif ?>
        <?php if($row->verified_date!=null && @$class_name!='Data_pemesanan'){?>
        <tr>
            <td>Tanggal approval</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= $row->verified_date?></a></td>
        </tr>
        <?php } ?>
        <tr>
            <td>Status</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><?= $status?></td>
        </tr>
        <?php if(strpos($status, 'Ditolak Pengelola') !== false){?>
        <tr>
            <td></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><?= $row->catatan_admin_dafasum?></td>
        </tr>
        <?php } else if(strpos($status, 'Ditolak atasan') !== false){?>
        <tr>
            <td></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><?= $row->catatan_atasan?></td>
        </tr>
        <?php } else if(strpos($status, 'Jalan') !== false || strpos($status, 'nilai') !== false || strpos($status, 'Selesai') !== false){?>
        <tr>
            <td>Driver</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= $row->nama_mst_driver?></a></td>
        </tr>
        <tr>
            <td>No. HP Driver</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= get_row_mst_driver($row->id_mst_driver,'no_hp')?></a></td>
        </tr>
        <tr>
            <td>Kendaraan</td>
            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
            <td><a><?= $row->nama_mst_kendaraan?></a></td>
        </tr>
        <?php } if($row->rating_driver!=null){?>
        <tr>
            <td></td>
            <td></td>
            <td>
                <input type="hidden" name="rating_driver" id="rating_driver" value="<?= @$row->rating_driver!=null?$row->rating_driver:0?>">
                <div class="my-rating-8"></div>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<script src="<?= base_url('asset/star-rating-svg-master/src/jquery.star-rating-svg.js')?>"></script>
<?php if($row->rating_driver!=null){?>
<script>
    var rate_val = $('#rating_driver').val();
    $(".my-rating-8").starRating({
        readOnly: true,
        useFullStars: true,
        activeColor: 'cornflowerblue',
        initialRating: rate_val,
    });
</script>
<?php } ?>