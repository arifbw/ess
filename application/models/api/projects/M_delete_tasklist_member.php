<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_delete_tasklist_member extends CI_Model {
    
    private $data=array();
    public function get_child($params){
        /*
        $params = [id=>value, task_type=>value]
        */
        
        if( $params['task_type']!='task' ){
            $this->data[] = $params['id'];
            $get_result = $this->db->select('id,task_type')->where('parent_id', $params['id'])->get('ess_project_tasklists')->result_array();
            foreach( $get_result as $x ){
                if($x['task_type']=='dir')
                    $this->get_child(['id'=>$x['id'], 'task_type'=>$x['task_type']]);
                else
                    $this->data[] = $x['id'];
            }
        } else{
            $this->data[] = $params['id'];
        }
    }
    
    public function get_data(){
        return $this->data;
    }
    
    public function reset_data(){
        $this->data=array();
    }
}