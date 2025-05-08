<?php
function menu($id_parent = null, $li_active, $li_open, $id_ref_bidang = null) {
    $CI =& get_instance();
    //$role = $CI->session->userdata('role');
    $parent = $CI->db->select('order_number, task_name, id as id_parent, task_type')
        ->where(['np'=>'7648', 'start_date'=>'2020-11-16'])
        ->where('parent_id', $id_parent)
        ->from('ess_project_tasklists')
        ->order_by('order_number','ASC')
        ->order_by('id','ASC')
        ->get();
    $get_parent = $parent->result();
    echo '<ul class="nav-main" id="myMenu">';
        foreach ($get_parent as $r_parent) {
//            if ($id_parent == null) {
                $child = $CI->db->select('*')
                    ->where(['parent_id'=>$r_parent->id_parent])
                    ->from('ess_project_tasklists')
                    ->get();
                if ($child->num_rows() > 0) {
                    echo '<li><a data-toggle="nav-submenu" href="#"><span>'.$r_parent->task_name.' ('.$r_parent->task_type.')'.'</span></a>';

                }else {
                    echo '<li><a ><span class="sidebar-mini-hide">'.$r_parent->task_name.' ('.$r_parent->task_type.')'.'</span></a>';
                }
//            } else {
//                $child = $CI->db->select('*')
//                    ->where(['parent_id'=>$r_parent->id_parent])
//                    ->from('ess_project_tasklists')
//                    ->order_by('order_number','asc')
//                    ->order_by('id','ASC')
//                    ->get();
//
//                if ($child->num_rows() > 0) {
//                    echo '<li ><a class="nav-submenu zoom" data-toggle="nav-submenu" href="#"><span class="sidebar-mini-hide">'.$r_parent->task_name.' ('.$r_parent->task_type.')'.'</span></a>';
//
//                }else {
//                    echo '<li><a href="#"><span class="sidebar-mini-hide">'.$r_parent->task_name.' ('.$r_parent->task_type.')'.'</span></a>';
//                }
//            }
            menu($r_parent->id_parent, $li_active, $li_open, null);
        }
        echo '</li>';
    echo '</ul>';
}