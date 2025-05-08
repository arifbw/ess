<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_monitoring_pelatihan extends CI_Model {

    var $table = 'ess_diklat_kebutuhan_pelatihan';
    var $column_order = array(null, 'np_karyawan', 'nama', 'pelatihan'); //set column field database for datatable orderable
    var $column_search = array('np_karyawan', 'nama', 'pelatihan'); //set column field database for datatable searchable 
    var $order = array("created_at"=> "desc",); // default order 

    public function __construct(){
        parent::__construct();
    }

    private function _get_datatables_query($month=null,$filter='all')
    { 
        // $this->db->select('mst_diklat_pelatihan.id');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.id');
        $this->db->select('mst_diklat_pelatihan.nama_pelatihan');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.np_karyawan');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.nama');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.nama_unit');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.id_pelatihan');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.pelatihan');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.tanggal_pelatihan');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.approval_1');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.approval_2');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.status_1');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.status_2');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.approval_1_date');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.approval_2_date');
        $this->db->select('ess_diklat_kebutuhan_pelatihan.created_at');
        $this->db->select('mst_diklat_pelatihan.nama_pelatihan');
        $this->db->from($this->table);    
        $this->db->join('mst_diklat_pelatihan', 'mst_diklat_pelatihan.id = ess_diklat_kebutuhan_pelatihan.id_pelatihan', 'left');
        
        if($_SESSION["grup"]==4 || $_SESSION["grup"]==5) //jika dia pengguna dan pengadministrasi unit kerja
        {
            $np = $_SESSION["no_pokok"];
            $this->db->where("(approval_1 = '$np' OR approval_2 = '$np')");
        }
        
        if ($month != 0) {
            $this->db->where("DATE_FORMAT(tanggal_pelatihan,'%Y-%m')", $month);
        }
        
        if($filter=='all') //filter all
        {    
            //do nothing
        }else
        if($filter=='0') //filter Menunggu Persetujuan
        {
            // $this->db->where("(
            //                     (
            //                         ((ess_diklat_kebutuhan_pelatihan.status_1='0' OR ess_diklat_kebutuhan_pelatihan.status_2='0') AND ess_diklat_kebutuhan_pelatihan.approval_1 is not null AND ess_diklat_kebutuhan_pelatihan.approval_1!='' AND ess_diklat_kebutuhan_pelatihan.approval_2 is not null AND ess_diklat_kebutuhan_pelatihan.approval_2!='') /*jika ada approval 1 dan approval 2*/
            //                         OR 
            //                         ((ess_diklat_kebutuhan_pelatihan.status_1='0') AND ess_diklat_kebutuhan_pelatihan.approval_2 is null AND ess_diklat_kebutuhan_pelatihan.approval_2='') /*jika hanya approval 1*/
            //                           ) 
            //                 )");
            $this->db->where("(ess_diklat_kebutuhan_pelatihan.status_1='0' AND ess_diklat_kebutuhan_pelatihan.status_2='0') OR 
                              (ess_diklat_kebutuhan_pelatihan.status_1='0' AND ess_diklat_kebutuhan_pelatihan.status_2!='2') OR
                              (ess_diklat_kebutuhan_pelatihan.status_1!='2' AND ess_diklat_kebutuhan_pelatihan.status_2='0')
                            ")
                            ->order_by('status_1', 'ASC')
                            ->order_by('status_2', 'ASC');
        }else
        if($filter=='1') //filter Disetujui Atasan
        {
            $this->db->where("(
                                (
                                    ((ess_diklat_kebutuhan_pelatihan.status_1='1' AND ess_diklat_kebutuhan_pelatihan.status_2='1') AND ess_diklat_kebutuhan_pelatihan.approval_1 is not null AND ess_diklat_kebutuhan_pelatihan.approval_1!='' AND ess_diklat_kebutuhan_pelatihan.approval_2 is not null AND ess_diklat_kebutuhan_pelatihan.approval_2!='') /*jika ada approval 1 dan approval 2*/
                                    OR 
                                    ((ess_diklat_kebutuhan_pelatihan.status_1='1') AND ess_diklat_kebutuhan_pelatihan.approval_2 is null AND ess_diklat_kebutuhan_pelatihan.approval_2='') /*jika hanya approval 1*/
                                ) 
                            )");
        }else
        if($filter=='2') //filter Ditolak Atasan
        {
            $this->db->where("(ess_diklat_kebutuhan_pelatihan.status_1='2' OR ess_diklat_kebutuhan_pelatihan.status_2='2') /*ditolak atasan*/");
        }else
        if($filter=='3')  //filter Dibatalkan Pemohon
        {
            $this->db->where("(ess_diklat_kebutuhan_pelatihan.status_1='3' OR ess_diklat_kebutuhan_pelatihan.status_2='3') /*dibatalkan pemohon*/");
        }

        $i = 0;

		
        // foreach ($this->column_search as $item) // loop column 
        // {
        //     if (isset($_POST['search']['value'])) {
        //         foreach ($this->column_search as $item) {
        //             if ($i === 0) {
        //                 $this->db->group_start();
        //                 $this->db->like($item, $_POST['search']['value']);
        //             } else {
        //                 $this->db->or_like($item, $_POST['search']['value']);
        //             }
        //             if (count($this->column_search) - 1 == $i) {
        //                 $this->db->group_end();
        //             }
        //             $i++;
        //         }
        //     }
        // }
		
        foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
        
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }




    function get_datatables($month=null,$filter='all')
    {
        $this->_get_datatables_query($month,$filter);
        
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($month=null,$filter='all')
    {
        $this->_get_datatables_query($month,$filter);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($month=null,$filter='all')
    {
        $this->db->from($this->table);
        $this->db->join('mst_diklat_pelatihan', 'mst_diklat_pelatihan.id = ess_diklat_kebutuhan_pelatihan.id_pelatihan', 'left');
        
        // if($_SESSION["grup"]==4 || $_SESSION["grup"]==5) //jika dia pengguna dan pengadministrasi unit kerja
        // {
        //     $np = $_SESSION["no_pokok"];
        //     $this->db->where("(approval_1 = '$np' OR approval_2 = '$np')");
        // }
        
        if(@$month!=0){
            $this->db->where("DATE_FORMAT(tanggal_pelatihan,'%Y-%m')", $month);
        }
        
        if($filter=='all') //filter all
        {    
            //do nothing
        }else
        if($filter=='0') //filter Menunggu Persetujuan
        {
            $this->db->where("(
                                (
                                    ((ess_diklat_kebutuhan_pelatihan.status_1='0' OR ess_diklat_kebutuhan_pelatihan.status_2='0') AND ess_diklat_kebutuhan_pelatihan.approval_1 is not null AND ess_diklat_kebutuhan_pelatihan.approval_1!='' AND ess_diklat_kebutuhan_pelatihan.approval_2 is not null AND ess_diklat_kebutuhan_pelatihan.approval_2!='') /*jika ada approval 1 dan approval 2*/
                                    OR 
                                    ((ess_diklat_kebutuhan_pelatihan.status_1='0') AND ess_diklat_kebutuhan_pelatihan.approval_2 is null AND ess_diklat_kebutuhan_pelatihan.approval_2='') /*jika hanya approval 1*/
                                ) 
                            )");
        }else
        if($filter=='1') //filter Disetujui Atasan
        {
            $this->db->where("(
                                (
                                    ((ess_diklat_kebutuhan_pelatihan.status_1='1' AND ess_diklat_kebutuhan_pelatihan.status_2='1') AND ess_diklat_kebutuhan_pelatihan.approval_1 is not null AND ess_diklat_kebutuhan_pelatihan.approval_1!='' AND ess_diklat_kebutuhan_pelatihan.approval_2 is not null AND ess_diklat_kebutuhan_pelatihan.approval_2!='') /*jika ada approval 1 dan approval 2*/
                                    OR 
                                    ((ess_diklat_kebutuhan_pelatihan.status_1='1') AND ess_diklat_kebutuhan_pelatihan.approval_2 is null AND ess_diklat_kebutuhan_pelatihan.approval_2='') /*jika hanya approval 1*/
                                ) 
                            )");
        }else
        if($filter=='2') //filter Ditolak Atasan
        {
            $this->db->where("(ess_diklat_kebutuhan_pelatihan.status_1='2' OR ess_diklat_kebutuhan_pelatihan.status_2='2') /*ditolak atasan*/");
        }else
        if($filter=='3')  //filter Dibatalkan Pemohon
        {
            $this->db->where("(ess_diklat_kebutuhan_pelatihan.status_1='3' OR ess_diklat_kebutuhan_pelatihan.status_2='3') /*dibatalkan pemohon*/");
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

}
