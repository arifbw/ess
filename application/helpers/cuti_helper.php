<?php
function status_cuti($params){
    $return = '';
    /*
    $params = [
        'status_1'=>value,
        'status_2'=>value,
        'approval_2'=>value,
        'approval_sdm'=>value
    ]
    */
    if($params['approval_2']!=null){
        if($params['status_1']=='1'){
            if($params['status_2']=='1'){
                if($params['approval_sdm']=='1'){
                    $return = 'Disetujui SDM';
                } else if($params['approval_sdm']=='2'){
                    $return = 'Ditolak SDM';
                } else if($params['approval_sdm']=='0'){
                    $return = 'Menunggu SDM';
                }
            } else if($params['status_2']=='2'){
                $return = 'Ditolak Atasan 2';
            } else if($params['status_2']=='3'){
                $return = 'Dibatalkan Pengguna';
            } else{
                $return = 'Menunggu Atasan 2';
            }
        } else if($params['status_1']=='2'){
            $return = 'Ditolak Atasan 1';
        } else if($params['status_1']=='3'){
            $return = 'Dibatalkan Pengguna';
        } else{
            $return = 'Menunggu Atasan 1';
        }
    } else{
        if($params['status_1']=='1'){
            if($params['approval_sdm']=='1'){
                $return = 'Disetujui SDM';
            } else if($params['approval_sdm']=='2'){
                $return = 'Ditolak SDM';
            } else if($params['approval_sdm']=='0'){
                $return = 'Menunggu SDM';
            }
        } else if($params['status_1']=='2'){
            $return = 'Ditolak Atasan 1';
        } else if($params['status_1']=='3'){
            $return = 'Dibatalkan Pengguna';
        } else{
            $return = 'Menunggu Atasan 1';
        }
    }
    
    return $return;
}

function status_cuti_web($params){
    $return = '';
    /*
    $params = [
        'status_1'=>value,
        'status_2'=>value,
        'approval_2'=>value,
        'approval_sdm'=>value,
        'approval_1_date'=>value,
        'approval_2_date'=>value,
    ]
    */
    if( $params['status_1']=='1' ){
        $approval_1_status 	= "Cuti Telah Disetujui pada {$params['approval_1_date']}.";
    } else if( $params['status_1']=='2' ){
        $approval_1_status 	= "Cuti TIDAK Disetujui pada {$params['approval_1_date']}.";
    } else if( $params['status_1']=='3' ){
        $approval_1_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada {$params['approval_1_date']}.";
    } else if( $params['status_1']==''||$params['status_1']=='0' ){
        $params['status_1']=='0';
        $approval_1_status 	= "Cuti BELUM disetujui."; 
    }
    
    if( $params['status_2']=='1' ){
        $approval_1_status 	= "Cuti Telah Disetujui pada {$params['approval_2_date']}.";
    } else if( $params['status_2']=='2' ){
        $approval_1_status 	= "Cuti TIDAK Disetujui pada {$params['approval_2_date']}.";
    } else if( $params['status_2']=='3' ){
        $approval_1_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada {$params['approval_2_date']}.";
    } else if( $params['status_2']==''||$params['status_2']=='0' ){
        $params['status_2']=='0';
        $approval_1_status 	= "Cuti BELUM disetujui."; 
    }

    if(($params['status_1']=='' || $params['status_1']=='0' || $params['status_1'] == null) && ($params['status_2']!='2' || $params['status_2']!='1')) {
        $text = 'Menunggu Atasan 1';
    } 
    if(($params['status_1']=='1') && ($params['status_2']!='2' || $params['status_2']!='1')) {
        if($params['approval_2']==null || $params['approval_2']=='') //jika tidak ada atasan 2
        {
            $text = 'Disetujui atasan 1';
        }else //jika ada atasan 2
        {
            $btn_text = 'Disetujui Atasan 1, Menunggu Atasan 2';
        }
    }
    if(($params['status_1']=='2') && ($params['status_2']!='2' || $params['status_2']!='1')) //ditolak atasan 1
    {
        $text = 'Ditolak Atasan 1';
    }
    if($params['status_2']=='1') //disetujui atasan  2
    {
        $text = 'Disetujui Atasan 2';
        
        if($params['status_1']=='0') //jika paralel atasan 2 belum approve
        {
            $text = 'Disetujui Atasan 2, Menunggu Atasan 1';
        }
    }
    if($params['status_2']=='2') //ditolak atasan 2
    {
        $text = 'Ditolak Atasan 2';
    }
        
    if($params['status_1']=='3' || $params['status_2']=='3') //dibatalkan
    {
        $text = 'dibatalkan';
    }

    if($params['approval_sdm']==1) {
        $text = 'Disetujui Oleh SDM';
    }else if($params['approval_sdm']==2) {
        $text = 'Ditolak Oleh SDM';
    }
    $return = $text;
    return $return;
}