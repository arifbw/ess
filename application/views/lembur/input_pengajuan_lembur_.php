		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul;?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php if(!empty($success)) { ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $success;?>
				</div>
				<?php }
				if(!empty($warning)) { ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $warning;?>
				</div>
				<?php }
					/* if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					} */
				if($akses["tambah"]) { ?>
							<form name='form_action_tambah_lembur' action="<?php echo site_url('lembur/pengajuan_lembur/save_input_pengajuan_lembur') ?>" role="form" method="post" accept-charset="utf-8" action=''>
								<section id="main-content">
									<section class="wrapper">
										<!-- page start-->
										<!-- ofi start here  -->
										<div class="row">
											<div class="col-lg-3">
												<section class="panel">
													<!-- <select class="form-control select2" name="sel"><option>1</option><option>2</option></select> -->
													<!-- Awal form -->
													<div class="panel panel-default">
														<div class="panel-heading form-inline">
															<div class="form-group">
																<input type="number" class="form-control input-sm" id="tambah_baris" name="tambah_baris" onkeypress="return event.charCode >= 48" min="1" max="200" value="1" style="width:75px;">
															</div>
															<div class="form-group">
																<button type="button" class="btn btn-success btn-sm" id="addNewRowTable" >Add Row</button>
															</div>
														</div>
													</div>
												</section>
											</div>
											<div class="col-lg-7">
												<section class="panel">
													<div class="panel panel-default">
														<div class="panel-heading form-inline">
															<div class="form-group"> 
																<div class="dropdown">
																	<select class="form-control input-sm" id="sel1" name="sel1" onChange="selectHeaderCopy()">
																		<option>NP</option>
																		<option>Tertanggal</option>
																		<option>Tanggal Mulai</option>
																		<option>Jam Mulai</option>
																		<option>Tanggal Selesai</option>
																		<option>Jam Selesai</option>
																		<option>NP Approver</option>
																		<?php
																			if(in_array($_SESSION["grup"],array(1,2,3,4))){
																				echo "<option>Generate Karyawan</option>";
																			}
																		?>
																	</select>
																</div>
															</div>
															<div class="form-group" id="rowCopyValue"></div>
															<div class="form-group">
																<button type="button" class="btn btn-success btn-sm" id="tombol_aksi">Copy</button>
															</div>
														</div>
													</div>
												</section>
											</div>
											<div class="col-lg-2">
												<section class="panel">
													<div class="panel panel-default">
														<div class="panel-heading form-inline">
															<div class="form-group">
																<button type="submit" class="btn btn-info btn-sm">Save</button>
															</div>
														</div>
													</div>
												</section>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<section class="panel">
													<style>												
														.scrollable-form {
															height: 350px;
															overflow-y: scroll;
															table-layout: fixed;
															
															thead {
															    tr {
															      display: block;
															      position: relative;
															    }
															}
															tbody {
															   display: block;
															   overflow: auto;
															   width: 100%;
															}
														}
													</style>	

													<div class="form-horizontal scrollable-form" xml_error_string style="margin-top: -20px"> <!--  -->
														<input type="hidden" id="maxIndexTable" />
														<!-- table -->
														<table class="table table-striped table-hover table-bordered" id="editable-sample" width="100%">
															<thead style="position:relative">
																<tr>
																	<th style="width:15%">No. Pokok</th>
																	<th style="width:6%">Tertanggal</th>
																	<th style="width:7%">Tanggal Mulai</th>
																	<th style="width:7%">Jam Mulai</th>
																	<th style="width:10%">Tanggal Selesai</th>
																	<th style="width:10%">Jam Selesai</th>
																	<th style="width:15%">NP Approver</th>
																	<th style="width:5%">Aksi</th>
																</tr>
															</thead>
															<tbody id="bodyTable">
															</tbody>
														</table>
														<!-- end table -->				
													</div>
												<!-- akhir form -->
												</section>
							
											</div>
										</div>
										<!-- page end-->
									</section>
								</section>
							</form>
				<?php
					}
					
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->

		 <!-- END JAVASCRIPTS -->
  
  <script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
  <script src="<?= base_url('asset/js/moment.min.js')?>"></script>
  <script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
  <script type="text/javascript">
  		$(document).ready(function() {
  			$(window).on("load resize ", function() {
			  var scrollWidth = $('.scrollable-form').width() - $('.scrollable-form table').width();
			  $('.fixed-form').css({'padding-right':scrollWidth});
			}).resize();
			$('.select2').select2();
			$('.datetimepicker5').datetimepicker({
                format: 'HH:mm'
            });
		   	// get_id = $(e.target).attr("id");
		   	// console_log(get_id);
		    // $("#"+get_id).keypress(function() {
		    //     var dInput = $('input:text[name=dSuggest]').val();
		    //     console.log(dInput);
		    //     $(".dDimension:contains('" + dInput + "')").css("display","block");
		    // });
		});

		jQuery(document).ready(function() {
		   /* $('#editable-sample').DataTable({
				"paging":   false,
				"ordering": false,
				"info":     false
		   }); */

			$('#maxIndexTable').val(0);
			
			$('#addNewRowTable').click(addNewRow);
			
			$('#addNewRowTable').click();
			
		});
		 
		function addNewRow(){
			var lastIndexTable = Number($('#maxIndexTable').val());
			var baris = Number($('#tambah_baris').val());
						
			var i;
			for (i=0; i < baris; i++) {
				lastIndexTable = lastIndexTable + 1;
				var newRow ="<tr id=\"tableRow"+lastIndexTable+"\">"+
							// "	<td><input type=\"text\" class=\"form-control input-sm no_pokok\" id=\"no_pokok"+lastIndexTable+"\" name=\"no_pokok[]\" required onChange=\"getNama('"+lastIndexTable+"')\"></td>		"+
							"	<td><select class=\"form-control select2 input-sm no_pokok\" onchange=\"getAtasanLembur("+ lastIndexTable +")\" id=\"no_pokok"+lastIndexTable+"\" name=\"no_pokok[]\" required><?= $list_np ?></select></td>		"+
							// "	<td><input type=\"text\" class=\"form-control input-sm nama\" id=\"nama"+lastIndexTable+"\" name=\"nama[]\" readonly=readonly></td>                 		"+
							"	<td><input type=\"date\" class=\"form-control input-sm tgl_dws\" id=\"tgl_dws"+lastIndexTable+"\" name=\"tgl_dws[]\" onchange=\"change_tgl("+lastIndexTable+")\" required></td>"       		+
							"	<td><input type=\"date\" class=\"form-control input-sm tgl_mulai\" id=\"tgl_mulai"+lastIndexTable+"\" name=\"tgl_mulai[]\" required></td>"       		+
							"	<td><input type=\"time\" class=\"form-control input-sm datetimepicker5 jam_mulai\" id=\"jamMulai"+lastIndexTable+"\" name=\"jam_mulai[]\" required></td>"       		+
							"	<td><input type=\"date\" class=\"form-control input-sm tgl_selesai\" id=\"tgl_selesai"+lastIndexTable+"\" name=\"tgl_selesai[]\" required></td>"       		+
							"	<td><input type=\"time\" class=\"form-control input-sm datetimepicker5 jam_selesai\" id=\"jamSelesai"+lastIndexTable+"\" name=\"jam_selesai[]\" required></td>"       	+
							"	<td><select class=\"form-control select2 input-sm np_approver\" id=\"np_approver"+lastIndexTable+"\" name=\"np_approver[]\" required><?= $list_apv ?></select></td>		"+
							"	<td><button class=\"btn btn-danger btn-sm\" type=\"button\" onclick=\"deleteRow('tableRow"+lastIndexTable+"')\">		"+
							" 		<i class=\"fa fa-trash-o\"/> </button></td>	        																"+
							"</tr>";
				$('#bodyTable').append(newRow);	
				$('#jamMulai'+lastIndexTable).addClass('datetimepicker5');
				$('.select2').select2();
			}				
			$('#maxIndexTable').val(lastIndexTable);
		}
			
		function deleteRow(tag){
			$('#'+tag).remove();
		}

		function change_tgl(id){
			var tgl_dws = $('#tgl_dws'+id).val();
			$('#tgl_mulai'+id).val(tgl_dws);
			$('#tgl_selesai'+id).val(tgl_dws);
		}	
		
		function selectHeaderCopy(){
			var coloumnHeader = $('#sel1').val();
			//reset
			$('#rowCopyValue').empty();
			if (coloumnHeader == 'NP'){
				$('#rowCopyValue').append('<select class=\"form-control select2 input-sm\" id=\"copyValue\"><?= $list_np ?></select>');
				$('.select2').select2();
			}
			else if (coloumnHeader == 'NP Approver'){
				$('#rowCopyValue').append('<select class=\"form-control select2 input-sm\" id=\"copyValue\"><?= $list_apv ?></select>');
				$('.select2').select2();
			}
			else if (coloumnHeader == 'Tanggal Mulai' || coloumnHeader == 'Tanggal Selesai' || coloumnHeader == 'Tertanggal' ){
				$('#rowCopyValue').append('<input type=\"date\" class=\"form-control input-sm\" id=\"copyValue\">');
			}
			else if (coloumnHeader == 'Jam Mulai' || coloumnHeader == 'Jam Selesai' ){
				$('#rowCopyValue').append('<input type=\"time\" class=\"form-control datetimepicker5 input-sm\" id=\"copyValue\">');
			}
			else if (coloumnHeader == 'Generate Karyawan'){
				$('#rowCopyValue').append('<select class=\"form-control select2 input-sm\" id=\"copyValue\" style=\"max-width:300px;\"><?= $list_unit_kerja ?></select>');
				$('.select2').select2();
			}
			
			if(coloumnHeader == 'Generate Karyawan'){
				$("#tombol_aksi").text('Generate');
				$("#tombol_aksi").attr("onClick","generateKaryawan()");
			}
			else{
				$("#tombol_aksi").text('Copy');
				$("#tombol_aksi").attr("onClick","copyToRow()");
			}
		}
		selectHeaderCopy();
		
		function generateKaryawan(){
			$('#bodyTable').empty();
			
			var kode_unit = $('#copyValue').val();
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getKaryawanUnitKerjaLembur');?>",
             data: "vkode_unit="+kode_unit,
				success: function(msg){
					if(msg != ''){
						var arr_karyawan = JSON.parse(msg);
						for(var i=0;i<arr_karyawan.length;i++){
							addNewRow();
							$("#no_pokok"+$("#maxIndexTable").val()).attr("onChange","");
							$("#no_pokok"+$("#maxIndexTable").val()).val(arr_karyawan[i]["no_pokok"]).trigger("change");
							$("#np_approver"+$("#maxIndexTable").val()).val(arr_karyawan[i]["np_atasan"]).trigger("change");
							$("#no_pokok"+$("#maxIndexTable").val()).attr("onChange","getAtasanLembur()");
						}
					}
					else{							 
						alert('Karyawan tidak ditemukan!');
					}													  
				 }
			 });
		}

		function copyToRow(){
			var coloumnHeader = $('#sel1').val();
			var copyValue = $('#copyValue').val();

			if (coloumnHeader == 'NP'){
				$('.no_pokok').each(function(index,item){
					$("#"+this.id).val(copyValue).trigger("change");
				});
			}else if (coloumnHeader == 'NP Approver'){
				$('.np_approver').each(function(index,item){
					$("#"+this.id).val(copyValue).trigger("change");
					//this.value = copyValue;
				});
			}else if (coloumnHeader == 'Tertanggal'){
				$('.tgl_dws').each(function(index,item){
					this.value = copyValue;
				});
				$('.tgl_mulai').each(function(index,item){
					this.value = copyValue;
				});
				$('.tgl_selesai').each(function(index,item){
					this.value = copyValue;
				});
			}else if (coloumnHeader == 'Tanggal Mulai'){
				$('.tgl_mulai').each(function(index,item){
					this.value = copyValue;
				});
			}else if (coloumnHeader == 'Tanggal Selesai'){
				$('.tgl_selesai').each(function(index,item){
					this.value = copyValue;
				});
			}else if (coloumnHeader == 'Jam Mulai'){
				$('.jam_mulai').each(function(index,item){
					this.value = copyValue;
				});
			}else if (coloumnHeader == 'Jam Selesai'){
				$('.jam_selesai').each(function(index,item){
					this.value = copyValue;
				});
			}
			/*
			if (coloumnHeader == 'NP'){
				$('.no_pokok').each(function(index,item){
					if(item.value.trim() == ''){
						this.value = copyValue;
						getNama((item.id).replace("no_pokok",""));
					}
				});
			}else if (coloumnHeader == 'Tertanggal'){
				$('.tgl_dws').each(function(index,item){
					if(item.value.trim() == ''){
						this.value = copyValue;
					}
				});
			}else if (coloumnHeader == 'Tanggal Mulai'){
				$('.tgl_mulai').each(function(index,item){
					if(item.value.trim() == ''){
						this.value = copyValue;
					}
				});
			}else if (coloumnHeader == 'Tanggal Selesai'){
				$('.tgl_selesai').each(function(index,item){
					if(item.value.trim() == ''){
						this.value = copyValue;
					}
				});
			}else if (coloumnHeader == 'Jam Mulai'){
				$('.jam_mulai').each(function(index,item){
					if(item.value.trim() == ''){
						this.value = copyValue;
					}
				});
			}else if (coloumnHeader == 'Jam Selesai'){
				$('.jam_selesai').each(function(index,item){
					if(item.value.trim() == ''){
						this.value = copyValue;
					}
				});
			}*/
		}
		
		function getNama(id){
			var no_pokok = $('#no_pokok'+id).val();
			
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getNama');?>",
             data: "vno_pokok="+no_pokok,
				success: function(msg){
					if(msg == ''){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
					}else{							 
						$('#nama'+id).val(msg);
					}													  
				 }
			 });       
		} 
		
		function getAtasanLembur(id){
			//alert("asd");
			var no_pokok = $('#no_pokok'+id).val();
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getAtasanLembur');?>",
             data: "vnp_karyawan="+no_pokok,
				success: function(msg){
					if(msg != ''){
						//console.log(msg);
						//$("select[id=np_approver"+id+"] option[value="+msg+"]").attr('selected','selected');
						$("#np_approver"+id).val(msg).trigger("change");
					}else{							 
						alert('Atasan tidak ditemukan!');
					}													  
				 }
			 });       
		} 
		
		function simpan(){
			var totalRow = Number($('#maxIndexTable').val());
			jsonObj = [];
			var i;
				for (i=1; i <= totalRow; i++) {
					item ={}
					item["no_pokok"]		=$('#no_pokok'+i).val();
					item["np_approver"]		=$('#np_approver'+i).val();
					item["tgl_dws"]			=$('#tgl_dws'+i).val();
					item["tgl_mulai"]		=$('#tgl_mulai'+i).val();
					item["jamMulai"]		=$('#jamMulai'+i).val();
					item["tgl_selesai"]		=$('#tgl_selesai'+i).val();
					item["jamSelesai"]		=$('#jamSelesai'+i).val();
					item["ket"]				=$('#ket'+i).val();
					
					jsonObj.push(item);
				}
				
			//console.log(jsonObj);
			
			/*  $.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('lembur/pengajuan_lembur/insert_input_pengajuan_lembur');?>",
             data: "vdata="+jsonObj,
				success: function(msg){
					if(msg == ''){
					//	alert ('Silahkan isi No. Pokok Dengan Benar.');
					}else{							 
						//$('#nama'+np).val(msg);
					}													  
				 }
			 });  */
			// console.log(vdata);
		}
	  
  </script>
