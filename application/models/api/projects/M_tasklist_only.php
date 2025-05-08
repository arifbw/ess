<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tasklist_only extends CI_Model {
    
    private $data=array();
    public function get_parent($params){
        /*
        $params = [parent_id=>value, project_id=>value]
        */
        
        if( $params['parent_id']!=null ){
            $get_row = $this->db->select('id,project_id,task_name,parent_id')->where('id', $params['parent_id'])->get('ess_project_tasklists')->row();
            array_unshift($this->data, $get_row->task_name);
            $this->get_parent(['parent_id'=>$get_row->parent_id, 'project_id'=>$get_row->project_id]);
        } else{
            $get_row = $this->db->select('id,project_name')->where('id', $params['project_id'])->get('ess_projects')->row();
            array_unshift($this->data, $get_row->project_name);
        }
    }
    
    public function get_data(){
        return $this->data;
    }
    
    public function reset_data(){
        $this->data=array();
    }
    
    
    
}