<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_comment_api extends CI_Model {
    
    function get_comments($tasklist_id){
        return $this->db->where('tasklist_id',$tasklist_id)->get('ess_project_tasklist_comments')->result_array();
    }
    
    function get_project_comments($project_id){
        return $this->db->where('project_id',$project_id)->get('ess_project_comments')->result_array();
    }
}