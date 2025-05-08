<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class All_activities extends CI_Controller {
    public function __construct(){
        parent::__construct();
        
        $meta = meta_data();
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }
        
        $this->folder_view = 'tasklist/';
        $this->folder_model = 'tasklist/';
        $this->folder_controller = 'tasklist/';
        
        $this->akses = array();
        
        $this->data["is_with_sidebar"] = true;
        
        $this->data['judul'] = "Tasklist";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);
        
        izin($this->akses["akses"]);
    }
    
    function index(){
        /*header('Content-Type: application/json');
        echo json_encode([
            'status'=>true,
            'message'=>'Development...'
        ]);
        exit;*/
        
        # get projects
        $get = $this->db
            ->where( 'deleted_at is null',null,false )
            ->group_start()
                ->where('as_member >',0)
                ->or_where('created_by_np',$_SESSION["no_pokok"])
            ->group_end()
            ->order_by('created_at','DESC')
            ->get("(SELECT id,kode,project_name,description,created_at,created_by_np,created_by_nama,project_type_id,deleted_at, (SELECT COUNT(ess_project_members.kode) FROM ess_project_members WHERE project_id=ess_projects.id AND ess_project_members.np='".$_SESSION["no_pokok"]."') AS as_member FROM ess_projects) combine")->result();
        
        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."all_activities";
        
        $this->data["projects"] = $get;
        
        $this->load->view('template',$this->data);
    }
    
    function show_detail(){
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        switch ($type) {
            case "pro":
                $row = $this->db->select('id,kode,project_name as name, "Project" as task_type, description, created_by_nama, created_at')->where('id',$id)->get('ess_projects')->row();
                $jenis = 'Project';
                break;
            default:
                $row = $this->db->select('id,kode,task_name as name, task_type, description, created_by_nama, created_at, progress, evidence, uploaded_at')->where('id',$id)->get('ess_project_tasklists')->row();
                $jenis = $row->task_type=='dir' ? 'Tasklist':'Activity';
        }
        $this->load->view('tasklist/detail',[
            'data'=>$row,
            'jenis'=>$jenis
        ]);
    }
}