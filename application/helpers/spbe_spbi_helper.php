<?php
function spbi_status($data=[]){
	$text = '';
    if( $data['approval_atasan_status'] == null ){
        $text = 'Menunggu Persetujuan Atasan';
    } else if( $data['approval_atasan_status'] == '2' ){
        $text = 'Ditolak Atasan';
    } else if( $data['pengecek1_status'] == '2' || $data['konfirmasi_pengguna'] == '2' || $data['danposko_status'] == '2' ){
        if( $data['pengecek1_status'] == '2' ) $text = 'Ditolak Petugas Pamsiknilmat';
        else if( $data['konfirmasi_pengguna'] == '2' ) $text = 'Ditolak Pemohon';
        else if( $data['danposko_status'] == '2' ) $text = 'Ditolak Komandan Posko';
    } else if( $data['pengecek1_status'] == null ){
        $text = 'Menunggu Petugas Pamsiknilmat';
    } 
    else if( $data['konfirmasi_pengguna'] == null ){
        $text = 'Menunggu Konfirmasi Pemohon';
    } 
    else if( $data['danposko_status'] == null ){
        $text = 'Menunggu Persetujuan Komandan Posko';
    } 
    else if( $data['approval_pengamanan_keluar'] == null ){
        $text = 'Menunggu Persetujuan Admin Pamsiknilmat';
    } else{
        $text = $data['kondisi_barang_keluar']=='2' ? 'Barang Keluar Sebagian' : 'Pengeluaran Selesai';
        if( $data['approval_pengamanan_masuk'] == null ){
            $text .= '\n(Menunggu Barang Masuk)';
        } else{
            $text = 'Barang Telah Selesai Masuk';
            if( $data['konfirmasi_pembawa_status'] == null ){
                $text .= '\n(Pembawa Barang Belum Konfirmasi)';
            }
        } 
    }
    return $text;
}

function spbe_status($data=[]){
	$text = '';
    if( $data['approval_atasan_status'] == null ){
        $text = 'Menunggu Persetujuan Atasan';
    } else if( $data['approval_atasan_status'] == '2' ){
        $text = 'Ditolak Atasan';
    } else if( $data['pengecek1_status'] == '2' || $data['konfirmasi_pengguna'] == '2' || $data['danposko_status'] == '2' ){
        if( $data['pengecek1_status'] == '2' ) $text = 'Ditolak Petugas Pamsiknilmat';
        else if( $data['konfirmasi_pengguna'] == '2' ) $text = 'Ditolak Pemohon';
        else if( $data['danposko_status'] == '2' ) $text = 'Ditolak Komandan Posko';
    } else if( $data['pengecek1_status'] == null ){
        $text = 'Menunggu Petugas Pamsiknilmat';
    } 
    else if( $data['konfirmasi_pengguna'] == null ){
        $text = 'Menunggu Konfirmasi Pemohon';
    } 
    else if( $data['danposko_status'] == null ){
        $text = 'Menunggu Persetujuan Komandan Posko';
    } 
    else if( $data['approval_pengamanan_keluar'] == null ){
        $text = 'Menunggu Persetujuan Admin Pamsiknilmat';
    } else{
        $text = $data['kondisi_barang_keluar']=='2' ? 'Barang Keluar Sebagian' : 'Pengeluaran Selesai';
		if( $data['barang_kembali'] == '1' ){
			if( $data['approval_pengamanan_masuk'] == null ){
				$text .= '\n(Menunggu Barang Masuk)';
			} else{
				$text = 'Barang Telah Selesai Masuk';
                if( $data['konfirmasi_pembawa_status'] == null ){
                    $text .= '\n(Pembawa Barang Belum Konfirmasi)';
                }
			} 
		}
    }
    return $text;
}