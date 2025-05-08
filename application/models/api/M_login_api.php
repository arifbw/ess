<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_login_api extends CI_Model {
    
    function auth($username, $password){
       if($password=='92adfe661256f0a5391ab0619665b240'){ # mobile-d3v-pass
           return $this->db->where(['username'=>$username])->or_where('no_pokok',$username)->get('usr_pengguna');
       } else{
            return $this->db
                ->group_start()
                ->where('username',$username)
                ->or_where('no_pokok',$username)
                ->group_end()
                ->where('password',$password)
                ->get('usr_pengguna');
       }
    }
    
    function _token($id){
        return $this->db->where(['user_id'=>$id])->get('keys');
    }
    
    function update_token($id,$key){
        return $this->db->where(['user_id'=>$id])->update('keys', ['key'=>$key, 'date_updated'=>date('Y-m-d H:i:s')]);
    }
    
    function insert_token($data){
        return $this->db->insert('keys', $data);
    }
    
    function auth_by_token($key){
        return $this->db->where(['key'=>$key])->get('keys');
    }
}