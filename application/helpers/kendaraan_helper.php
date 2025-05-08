<?php

function get_pemesanan_tujuan($id_pemesanan=null) {
    $ci =& get_instance();
    $return = '';
    if(@$id_pemesanan){
        $get = $ci->db->select('id,kode_kota_tujuan,nama_kota_tujuan,keterangan_tujuan')
            ->where(['id_pemesanan_kendaraan'=>$id_pemesanan, 'status'=>1])
            ->get('ess_pemesanan_kendaraan_kota');
        foreach($get->result() as $row){
            $return .= '- '.$row->nama_kota_tujuan.' ('.$row->keterangan_tujuan.')<br>';
        }
    }
    return $return;
}

function get_pemesanan_tujuan_small($id_pemesanan=null) {
    $ci =& get_instance();
    $return = '';
    if(@$id_pemesanan){
        $get = $ci->db->select('id,kode_kota_tujuan,nama_kota_tujuan,keterangan_tujuan')
            ->where(['id_pemesanan_kendaraan'=>$id_pemesanan, 'status'=>1])
            ->get('ess_pemesanan_kendaraan_kota');
        foreach($get->result() as $row){
            $return .= '- '.$row->keterangan_tujuan.' <small>('.$row->nama_kota_tujuan.')</small><br>';
        }
    }
    return $return;
}

function __status_pemesanan($arr_params=null) {
    /*
    $arr_params=[
        tanggal_berangkat
        verified
        status_persetujuan_admin
        id_mst_kendaraan
        id_mst_driver
        pesanan_selesai
        rating_driver
        is_canceled_by_admin
    ]
    */
    $status = '';
    if(@$arr_params){
        if(@$arr_params['is_canceled_by_admin']=='1'){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Dibatalkan oleh Pengelola Transportasi</i>';
        }
        
        # tanggal berangkat sudah terlewat, belum diapprove atasan => Expired/Tidak direspon Atasan
        else if($arr_params['tanggal_berangkat']<date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak direspon Atasan</i>';
        }
        
        # tanggal berangkat belum terlewat, belum diapprove atasan => menunggu persetujuan
        else if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:grey;"><i class="fa fa-hourglass-half"></i> Menunggu persetujuan</i>';
        } 
        
        # approval atasan 2 => Ditolak atasan
        else if($arr_params['verified']==2){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Ditolak atasan</i>';
        } 
        
        # approval atasan 1
        else if($arr_params['verified']==1){
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Plotting kendaraan
            if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['status_persetujuan_admin'],[1,2]) && $arr_params['id_mst_driver']==null){
                $status = '<i style="color:grey;"><i class="fa fa-key"></i> Menunggu Persetujuan Pengelola Transportasi</i>'; //Plotting kendaraan
            } 
            
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Tidak direspon Admin
            else if($arr_params['tanggal_berangkat']<date('Y-m-d') && (!in_array($arr_params['status_persetujuan_admin'],[1,2]) || $arr_params['id_mst_driver']==null)){
                $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak direspon Pengelola Transportasi</i>'; //Tidak direspon Admin
            } 
            
            # status persetujuan admin 2  => ditolak admin
            else if($arr_params['status_persetujuan_admin']==2){
                $status = '<i style="color:red;"><i class="fa fa-times"></i> Ditolak Pengelola Transportasi</i>'; //Ditolak admin
            }
            
            # status persetujuan admin 1 => jalan
            else if($arr_params['status_persetujuan_admin']==1){
                if(@$arr_params['rating_driver']!=null && @$arr_params['rating_driver']>0){
                    $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Selesai</i>';
                } else if(@$arr_params['pesanan_selesai']==1){
                    $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Selesai</i>';
                } else{
                    $status = '<i style="color:green;"><i class="fa fa-car"></i> Jalan</i>';
                }
            }
        }
    }
    return $status;
}

function __status_pemesanan_admin($arr_params=null) {
    /*
    $arr_params=[
        tanggal_berangkat
        verified
        status_persetujuan_admin
        id_mst_kendaraan
        id_mst_driver
        pesanan_selesai
        rating_driver
        is_canceled_by_admin
    ]
    */
    $status = '';
    if(@$arr_params){
        if(@$arr_params['is_canceled_by_admin']=='1'){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Dibatalkan oleh Pengelola Transportasi</i>';
        }
        
        # tanggal berangkat sudah terlewat, belum diapprove atasan => Expired/Tidak direspon Atasan
        else if($arr_params['tanggal_berangkat']<date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak direspon Atasan</i>';
        }
        
        # tanggal berangkat belum terlewat, belum diapprove atasan => menunggu persetujuan
        else if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:grey;"><i class="fa fa-hourglass-half"></i> Menunggu persetujuan</i>';
        } 
        
        # approval atasan 2 => Ditolak atasan
        else if($arr_params['verified']==2){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Ditolak atasan</i>';
        } 
        
        # approval atasan 1
        else if($arr_params['verified']==1){
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Plotting kendaraan
            if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['status_persetujuan_admin'],[1,2]) && $arr_params['id_mst_driver']==null){
                $status = '<i style="color:grey;"><i class="fa fa-key"></i> Menunggu Persetujuan Pengelola Transportasi</i>'; //Plotting kendaraan
            } 
            
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Tidak direspon Admin
            else if($arr_params['tanggal_berangkat']<date('Y-m-d') && (!in_array($arr_params['status_persetujuan_admin'],[1,2]) || $arr_params['id_mst_driver']==null)){
                $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak direspon Pengelola Transportasi</i>'; //Tidak direspon Admin
            } 
            
            # status persetujuan admin 2  => ditolak admin
            else if($arr_params['status_persetujuan_admin']==2){
                $status = '<i style="color:red;"><i class="fa fa-times"></i> Ditolak Pengelola Transportasi</i>'; //Ditolak admin
            }
            
            # status persetujuan admin 1 => jalan
            else if($arr_params['status_persetujuan_admin']==1){
                if(@$arr_params['rating_driver']!=null && @$arr_params['rating_driver']>0 && @$arr_params['pesanan_selesai']!=1){
                    $status = '<i style="color:orange;"><i class="fa fa-bullseye"></i> Pemesan menilai driver</i>';
                } else if(@$arr_params['pesanan_selesai']==1){
                    $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Selesai</i>';
                } else{
                    $status = '<i style="color:green;"><i class="fa fa-car"></i> Jalan</i>';
                }
            }
        }
    }
    return $status;
}

function tipe_perjalanan($arr_params=null){
    /*
    $arr_params=[
        is_pp
        is_inap
        tanggal_awal
        tanggal_akhir
    ]
    */
    $status = '';
    if(@$arr_params){
        # is_inap 1 || is_pp 1
        if($arr_params['is_inap']==1 || $arr_params['is_pp']==1){
            if($arr_params['is_inap']==1){
                $status .= 'Menginap';
            } else if($arr_params['is_pp']==1){
                $status .= 'PP';
            }
            
            if($arr_params['tanggal_awal']!=null){
                $status .= ': '.hari_tanggal($arr_params['tanggal_awal']);
            }
            if($arr_params['tanggal_akhir']!=null){
                $status .= ' s/d '.hari_tanggal($arr_params['tanggal_akhir']);
            } 
            /* old: edited 2020-05-13 08:17
            else if($arr_params['tanggal_awal']!=null && $arr_params['tanggal_awal']!=$arr_params['tanggal_akhir']){
                $status .= ': '.hari_tanggal($arr_params['tanggal_awal']).' s/d '.hari_tanggal($arr_params['tanggal_akhir']);
            } else if($arr_params['tanggal_awal']!=null && $arr_params['tanggal_awal']==$arr_params['tanggal_akhir']){
                $status .= ': '.hari_tanggal($arr_params['tanggal_awal']);
            }*/
        } else{
            $status .= 'Sekali jalan';
        }
    }
    return $status;
}

function get_row_mst_driver($id,$select) {
    $ci =& get_instance();
    
    $ci->db->select($select);
    $ci->db->where('id',$id);
    $get = $ci->db->get('mst_driver')->row();
    
    return $get->$select;
}

function generate_nomor_pemesanan() { //created 2020-05-15
    $ci =& get_instance();
    $return = '';
    
    # get latest order: select id,nomor_pemesanan
    $latest = $ci->db->select('id, nomor_pemesanan')->where('YEAR(created)',date('Y'))->order_by('id','DESC')->limit(1)->get('ess_pemesanan_kendaraan');
    if($latest->num_rows()>0){
        $data = $latest->row();
        $explode = explode('/',$data->nomor_pemesanan); //isi array[0: urutan, 1:GO, 2:bulan, 3:tahun]
        $return = (int)$explode[0]+1 .'/GO/'. date('m/Y');
    } else{
        $return = '1/GO/'. date('m/Y');
    }
    
    return $return;
}

function count_belum_rating($kode_unit){
    $ci =& get_instance();
    $get = $ci->db->select('COUNT(id) as belum_rating')->where(['kode_unit_pemesan'=>$kode_unit, 'status_persetujuan_admin'=>1])->where('rating_driver is NULL',null,false)->get('ess_pemesanan_kendaraan')->row();
    return $get->belum_rating;
}

function tanggal_format($date=null){
    $result = '';
    if(@$date){
        $day = date('D', strtotime($date));
        switch ($day) {
            case "Sun":
                $result .= 'Minggu, ';
                break;
            case "Mon":
                $result .= 'Senin, ';
                break;
            case "Tue":
                $result .= 'Selasa, ';
                break;
            case "Wed":
                $result .= 'Rabu, ';
                break;
            case "Thu":
                $result .= 'Kamis, ';
                break;
            case "Fri":
                $result .= 'Jumat, ';
                break;
            case "Sat":
                $result .= 'Sabtu, ';
                break;
            default:
                $result .= '';
        }
        
        $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl   = substr($date, 8, 2);
        $result .= $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;
    }
    return $result;
}

function tanggal_format_noday($date=null){
    $result = '';
    if(@$date){        
        $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl   = substr($date, 8, 2);
        $result .= $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;
    }
    return $result;
}

function date_ym_to_bulan($date=null){
    $result = '';
    if(@$date){
        $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        
        $result .= $BulanIndo[(int)$bulan-1] . " ". $tahun;
    }
    return $result;
}

function check_tb_tahun($tb_name, $year=null){
    $CI =& get_instance();
    $tb = $tb_name;
    if(@$year){
        $count_tb_name = $CI->db->select('COUNT(*) as jumlah')->where(['table_schema'=>$CI->db->database, 'table_name'=>$tb_name.'_'.$year])->get('information_schema.tables')->row();
        if($count_tb_name->jumlah>0){
            $tb = $tb_name.'_'.$year;
        }
    }
    return $tb;
}

function status_pemesanan($arr_params=null) {
    /*
    $arr_params=[
        tanggal_berangkat
        verified
        status_persetujuan_admin
        id_mst_kendaraan
        id_mst_driver
        pesanan_selesai
        rating_driver
        is_canceled_by_admin
    ]
    */
    $status = '';
    if(@$arr_params){
        if(@$arr_params['is_canceled_by_admin']=='1'){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Dibatalkan oleh Pengelola Transportasi</i>';
        }
        
        # tanggal berangkat sudah terlewat, belum diapprove atasan => Expired/Tidak direspon Atasan
        else if($arr_params['tanggal_berangkat']<date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak Direspon Atasan</i>';
        }
        
        # tanggal berangkat belum terlewat, belum diapprove atasan => menunggu persetujuan
        else if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:grey;"><i class="fa fa-hourglass-half"></i> Menunggu Persetujuan</i>';
        } 
        
        # approval atasan 2 => Ditolak atasan
        else if($arr_params['verified']==2){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Ditolak Atasan</i>';
        } 
        
        # approval atasan 1
        else if(in_array($arr_params['verified'], [1])){
            $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Disetujui Atasan</i>';
            /*
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Plotting kendaraan
            if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['status_persetujuan_admin'],[1,2,3])){
                $status = '<i style="color:grey;"><i class="fa fa-key"></i> Disetujui Atasan</i>'; //Plotting kendaraan
            } 
            
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Tidak direspon Admin
            else if($arr_params['tanggal_berangkat']<date('Y-m-d') && !in_array($arr_params['status_persetujuan_admin'],[1,2,3]) ){
                $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak direspon Pengelola Transportasi</i>'; //Tidak direspon Admin
            } 
            
            # status persetujuan admin 2  => ditolak admin
            else if($arr_params['status_persetujuan_admin']==2){
                $status = '<i style="color:red;"><i class="fa fa-times"></i> Tidak Terlayani</i>'; //Ditolak admin
            }
            
            # status persetujuan admin 1 => jalan
            else if($arr_params['status_persetujuan_admin']==1){
                $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Terlayani</i>';
            }
            
            # status persetujuan admin 1 => jalan
            else if($arr_params['status_persetujuan_admin']==3){
                $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Terlayani - Gocorp Bluebird Reimburse</i>';
            }
            */
        }
    }
    return $status;
}

function status_pemesanan_admin($arr_params=null) {
    /*
    $arr_params=[
        tanggal_berangkat
        verified
        status_persetujuan_admin
        id_mst_kendaraan
        id_mst_driver
        pesanan_selesai
        rating_driver
        is_canceled_by_admin
    ]
    */
    $status = '';
    if(@$arr_params){
        if(@$arr_params['is_canceled_by_admin']=='1'){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Dibatalkan oleh Pengelola Transportasi</i>';
        }
        
        # tanggal berangkat sudah terlewat, belum diapprove atasan => Expired/Tidak direspon Atasan
        else if($arr_params['tanggal_berangkat']<date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak Direspon Atasan</i>';
        }
        
        # tanggal berangkat belum terlewat, belum diapprove atasan => menunggu persetujuan
        else if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['verified'],[1,2])){
            $status = '<i style="color:grey;"><i class="fa fa-hourglass-half"></i> Menunggu Persetujuan</i>';
        } 
        
        # approval atasan 2 => Ditolak atasan
        else if($arr_params['verified']==2){
            $status = '<i style="color:red;"><i class="fa fa-times"></i> Ditolak Atasan</i>';
        } 
        
        # approval atasan 1
        else if($arr_params['verified']==1){
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Plotting kendaraan
            if($arr_params['tanggal_berangkat']>=date('Y-m-d') && !in_array($arr_params['status_persetujuan_admin'],[1,2,3])){
                $status = '<i style="color:grey;"><i class="fa fa-key"></i> Menunggu Persetujuan Pengelola Transportasi</i>'; //Plotting kendaraan
            } 
            
            # atasan menyetujui, tanggal berangkat belum terlewat, kendaraan null => Tidak direspon Admin
            else if($arr_params['tanggal_berangkat']<date('Y-m-d') && !in_array($arr_params['status_persetujuan_admin'],[1,2,3]) ){
                $status = '<i style="color:red;"><i class="fa fa-hourglass-end"></i> Tidak Direspon Pengelola Transportasi</i>'; //Tidak direspon Admin
            } 
            
            # status persetujuan admin 2  => ditolak admin
            else if($arr_params['status_persetujuan_admin']==2){
                $status = '<i style="color:red;"><i class="fa fa-times"></i> Tidak Terlayani</i>'; //Ditolak admin
            }
            
            # status persetujuan admin 1 => jalan
            else if($arr_params['status_persetujuan_admin']==1){
                $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Terlayani</i>';
            }
            
            # status persetujuan admin 1 => jalan
            else if($arr_params['status_persetujuan_admin']==3){
                $status = '<i style="color:blue;"><i class="fa fa-hourglass-end"></i> Terlayani - Gocorp Bluebird Reimburse</i>';
            }
        }
    }
    return $status;
}

?>