<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url('asset/rateYo/2.3.2/jquery.rateyo.min.css')?>">

<link rel="stylesheet" href="<?= base_url('asset/jQuery-Tree-Grid/css/jquery.treegrid.css')?>">
<style>
    i{
        cursor: pointer;
    }
    
    td{
        color: rgba(0,0,0,.54);
    }
    
    .table, .table-aa {
        width: 1500px !important;
        overflow-x: auto;
    }
    
    .td-name{
        width: 500px;
    }
    
    .td-desc{
        width: 300px;
    }
    
    .td-by{
        width: 250px;
    }
    
    .td-action, .td-date{
        width: 150px;
    }
    
    thead, tbody, tr, td, th { display: block; }
    
    tr:after {
        content: ' ';
        display: block;
        visibility: hidden;
        clear: both;
    }
    
    tbody {
        height: 500px;
        overflow-y: auto;
    }
    
    tbody td, thead th {
        float: left;
    }
    
    @media only screen and (max-width: 1400px) {
        #page-wrapper{
            width: 1400px !important;
        }
        
        .container-fluid{
            width: 1050px !important;
        }
        
        .table, .table-aa {
            width: 1050px !important;
            overflow-x: auto;
        }
        
        .td-name{
            width: 300px !important;
        }
        
        .td-desc{
            width: 200px !important;
        }
    }
    
    @media only screen and (max-width: 812px) {
        #page-wrapper{
            width: 812px !important;
        }
        
        .container-fluid{
            width: 750px !important;
        }
        
        .table, .table-aa {
            width: 700px !important;
            overflow-x: auto;
        }
        
        .td-name{
            width: 200px !important;
        }
        
        .td-desc, .td-action{
            width: 150px !important;
        }
        
        .td-by, .td-date{
            width: 100px;
        }
    }
</style>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo @$judul;?></h1>
            </div>
        </div>
        <div class="alert alert-dismissable" id="alert-message" style="display: none;"></div>
        <?php
        if(!empty($this->session->flashdata('success'))){ ?>
        <div class="alert alert-success alert-dismissable" id="alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('success');?>
        </div>
        <?php
        }
        if(!empty($this->session->flashdata('warning'))){ ?>
        <div class="alert alert-danger alert-dismissable" id="alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('warning');?>
        </div>
        <?php
        }
        if(@$akses["lihat log"]){
            echo "<div class='row text-right'>";
                echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
                echo "<br><br>";
            echo "</div>";
        }
        
        if(@$this->akses["lihat"]){ ?>	
        <div class="row">
            <table class="table table-hover tree table-aa">
                <thead>
                    <tr>
                        <th class="td-name">Name</th>
                        <th class="td-desc">Description</th>
                        <th class="td-action">&nbsp;</th>
                        <th class="td-by">Created By</th>
                        <th class="td-date">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <!--style="/*color: #337ab7; background-color: #f0f0f0;*/"-->
                    <?php 
                    function get_child($project_id, $parent_id){
                        $CI =& get_instance();
                            $get = $CI->db->select('a.id,a.project_id,a.task_type,a.task_name,a.description,a.parent_id,a.created_at,a.created_by_np,a.created_by_nama')
                                ->from('ess_project_tasklists a')
                                ->join('ess_project_tasklist_members b','a.id=b.tasklist_id AND b.np="'.$_SESSION["no_pokok"].'"')
                                ->where('a.deleted_at is null',null,false)
                                ->where('a.project_id',$project_id)
                                ->where('a.parent_id',$parent_id)
                                ->get()
                                ->result();
                        foreach($get as $x){
                            echo '<tr class="row-data treegrid-'.($x->task_type=='dir' ? 'task':'act').$x->id.' treegrid-parent-'.($parent_id==null ? "p$project_id":"task$parent_id").'" id="'.($x->task_type=='dir' ? 'task':'act').$x->id.'">
                                    <td class="td-name">'.$x->task_name.'</td>
                                    <td class="td-desc">'.$x->description.'</td>
                                    <td class="td-action text-right">
                                        <div id="action-'.($x->task_type=='dir' ? 'task':'act').$x->id.'" style="display: none;">
                                            <i class="fa fa-lg fa-user-plus"></i>
                                            &nbsp;&nbsp;&nbsp;
                                            <i data-id="'.$x->id.'" data-typee="'.$x->task_type.'" class="fa fa-lg fa-pencil" onclick="show_detail(this)"></i>
                                            &nbsp;&nbsp;&nbsp;
                                            <i class="fa fa-lg fa-trash"></i>
                                        </div>
                                    </td>
                                    <td class="td-by">'.$x->created_by_nama.'</td>
                                    <td class="td-date">'.$x->created_at.'</td>
                                </tr>';
                            if($x->task_type=='dir')
                                get_child($project_id, $x->id);
                        }
                    }
                    foreach($projects as $row){?>
                    <tr class="row-data treegrid-p<?= $row->id?>" id="p<?= $row->id?>">
                        <td class="td-name"><b><?= $row->project_name?></b></td>
                        <td class="td-desc"><?= $row->description?></td>
                        <td class="td-action text-right">
                            <div id="action-p<?= $row->id?>" style="display: none;">
                                <i class="fa fa-lg fa-user-plus"></i>
                                &nbsp;&nbsp;&nbsp;
                                <i data-id="<?= $row->id?>" data-typee="pro" class="fa fa-lg fa-pencil" onclick="show_detail(this)"></i>
                                &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-lg fa-trash"></i>
                            </div>
                        </td>
                        <td class="td-by"><?= $row->created_by_nama?></td>
                        <td class="td-date"><?= $row->created_at?></td>
                    </tr>
                    <?php 
                        echo get_child($row->id,null);
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
        
        <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_detail" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="modal-content-detail"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>

<script type="text/javascript" src="<?= base_url('asset/jQuery-Tree-Grid/js/jquery.treegrid.js')?>"></script>
		
<script type="text/javascript">
    $('.tree').treegrid();
    
    $('.row-data').hover(function() {
        let id = $(this).attr('id');
        $(`#action-${id}`).show();
    }, function(){
        let id = $(this).attr('id');
        $(`#action-${id}`).hide();
    });
    
    function show_detail(input){
        let url = '<?= base_url('tasklist/all_activities/show_detail?id=')?>'+input.dataset.id+`&type=${input.dataset.typee}`;

        $("#modal-content-detail").html('Loading...');
        $("#modal-detail").modal('show');
        $.get(url).done(function (data) {
            $("#modal-content-detail").html(data);
        });
    }
</script>
	