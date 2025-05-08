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
                                                                        <option>Alasan</option>
                                                                        <!-- <option>NP Approver</option> -->
                                                                        <!-- diminta mba zana diganti approver  -->
																		<!-- <?php
																			if(in_array($_SESSION["grup"],array(1,2,3,4))){
																				echo "<option>Generate Karyawan</option>";
																			}
																		?> -->
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
																	<th style="width:10%">No. Pokok</th>
																	<th style="width:6%">Tertanggal</th>
																	<th style="width:7%">Tanggal Mulai</th>
																	<th style="width:7%">Jam Mulai</th>
																	<th style="width:7%">Tanggal Selesai</th>
																	<th style="width:7%">Jam Selesai</th>
																	<th style="width:5%">Jam</th>
																	<th style="width:10%">Alasan</th>
																	<th style="width:10%">NP Approver</th>
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
      var last_approver_np = '<?= $last_approver_np?>';
      var new_last_approver_np;
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
							"	<td><select class=\"form-control select2 input-sm no_pokok\" onchange=\"getPilihanAtasanLembur("+ lastIndexTable +");\" id=\"no_pokok"+lastIndexTable+"\" name=\"no_pokok[]\" required><?= $list_np ?></select></td>		"+
							// "	<td><input type=\"text\" class=\"form-control input-sm nama\" id=\"nama"+lastIndexTable+"\" name=\"nama[]\" readonly=readonly></td>                 		"+
							"	<td><input type=\"date\" class=\"form-control input-sm tgl_dws\" onchange=\"getPilihanAtasanLembur("+ lastIndexTable +");change_tgl("+lastIndexTable+");\" id=\"tgl_dws"+lastIndexTable+"\" name=\"tgl_dws[]\" required></td>"       		+
							"	<td><input type=\"date\" class=\"form-control input-sm tgl_mulai\" id=\"tgl_mulai"+lastIndexTable+"\" name=\"tgl_mulai[]\" onChange=\"getWaktu('"+lastIndexTable+"')\" required></td>"       		+
							"	<td><input type=\"time\" class=\"form-control input-sm datetimepicker5 jam_mulai\" id=\"jamMulai"+lastIndexTable+"\" name=\"jam_mulai[]\" onChange=\"getWaktu('"+lastIndexTable+"')\" required></td>"       		+
							"	<td><input type=\"date\" class=\"form-control input-sm tgl_selesai\" id=\"tgl_selesai"+lastIndexTable+"\" name=\"tgl_selesai[]\" onChange=\"getWaktu('"+lastIndexTable+"')\" required></td>"       		+
							"	<td><input type=\"time\" class=\"form-control input-sm datetimepicker5 jam_selesai\" id=\"jamSelesai"+lastIndexTable+"\" name=\"jam_selesai[]\" onChange=\"getWaktu('"+lastIndexTable+"')\" required></td>"       	+
							"	<td><label class=\"jam\" id=\"jam"+lastIndexTable+"\"></label></td>"       	+
							"	<td><input type=\"text\" class=\"form-control input-sm alasan\" id=\"alasan"+lastIndexTable+"\" name=\"alasan[]\" required></td>"       		+
                            // "	<td><input type=\"text\" class=\"form-control input-sm np_approver\" id=\"np_approver"+lastIndexTable+"\" name=\"np_approver[]\" onChange=\"getNamaAtasan("+lastIndexTable+")\"  required></td>"       	+
							"	<td><select class=\"form-control select2 input-sm np_approver\" id=\"np_approver"+lastIndexTable+"\" name=\"np_approver[]\" required></select></td>		"+
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
				/*$('#rowCopyValue').append('<select class=\"form-control select2 input-sm\" id=\"copyValue\"><?= $list_apv ?></select>');
				$('.select2').select2();*/
                
                 
                // heru menambahkan ini, di-uncomment jika sudah mulai running ke prd
                if( typeof new_last_approver_np === "undefined" )
                    $('#rowCopyValue').append('<input type=\"text\" class=\"form-control input-sm\" id=\"copyValue\" value="'+last_approver_np+'">');
                else
				    $('#rowCopyValue').append('<input type=\"text\" class=\"form-control input-sm\" id=\"copyValue\" value="'+new_last_approver_np+'">');
                // END of tambahan
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
			} else if (coloumnHeader == 'Alasan'){
				$('#rowCopyValue').append('<input type=\"text\" class=\"form-control input-sm\" id=\"copyValue\">');
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
                        console.log(arr_karyawan);
						for(var i=0;i<arr_karyawan.length;i++){
							addNewRow();
							//$("#no_pokok"+$("#maxIndexTable").val()).attr("onChange","");
                            if($(".no_pokok option[value='"+arr_karyawan[i]["no_pokok"]+"']").length == 0){
                                $(".no_pokok").append('<option value="'+arr_karyawan[i]["no_pokok"]+'">'+arr_karyawan[i]["no_pokok"]+' - '+arr_karyawan[i]["nama"]+'</option>');
                            }
							$("#no_pokok"+$("#maxIndexTable").val()).val(arr_karyawan[i]["no_pokok"]).trigger("change");
							$("#np_approver"+$("#maxIndexTable").val()).val(arr_karyawan[i]["np_atasan"]).trigger("change");
							$("#no_pokok"+$("#maxIndexTable").val()).attr("onChange","getPilihanAtasanLembur("+$("#maxIndexTable").val()+")");
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
                getNamaAtasanAll();
                
				$('.np_approver').each(function(index,item){
					$("#"+this.id).val(copyValue).trigger("change");
					// $("#"+this.id).val(copyValue);
					this.value = copyValue;
				});
                
                new_last_approver_np = copyValue;
			}else if (coloumnHeader == 'Tertanggal'){
				$('.tgl_dws').each(function(index,item){
					this.value = copyValue;
					$("#"+this.id).val(copyValue).trigger("change");
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
					$("#"+this.id).val(copyValue).trigger("change");
				});
			}else if (coloumnHeader == 'Jam Selesai'){
				$('.jam_selesai').each(function(index,item){
					this.value = copyValue;
					$("#"+this.id).val(copyValue).trigger("change");
				});
			}else if (coloumnHeader == 'Alasan'){
				$('.alasan').each(function(index,item){
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
						alert ('Silakan isi No. Pokok Dengan Benar.');
					}else{							 
						$('#nama'+id).val(msg);
					}													  
				 }
			 });       
		}
		
		function getWaktu(id){
			var no_pokok = $('#no_pokok'+id).val();
			var tgl_mulai = $('#tgl_mulai'+id).val();
			var jam_mulai = $('#jamMulai'+id).val();
			var tgl_selesai = $('#tgl_selesai'+id).val();
			var jam_selesai = $('#jamSelesai'+id).val();
			
			if (no_pokok!="" && tgl_mulai!="" && jam_mulai!="" && tgl_selesai!="" && jam_selesai!="") {
				$.ajax({
	             type: "POST",
	             dataType: "html",
	             url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getWaktu');?>",
	             data: {vno_pokok:no_pokok, jam_mulai:jam_mulai, jam_selesai:jam_selesai, tgl_mulai:tgl_mulai, tgl_selesai:tgl_selesai},
					success: function(msg){
						if(msg == ''){
							alert ('Silakan isi No. Pokok dan Waktu Dengan Benar.');
						}else{
							$('#jam'+id).text(msg);
							getPilihanAtasanLembur(id);
						}													  
					 }
				});
			}
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
					}
					else if($("#np_approver"+id).children().length>0){
						$("#np_approver"+id).get(0).selectedIndex = 0;
						$("#np_approver"+id).trigger("change");
					}
					else{
						alert('Atasan tidak ditemukan!');
					}													  
				 }
			 });       
		}
		
		function getPilihanAtasanLembur(id){
			//alert("asd");
			var no_pokok = $('#no_pokok'+id).val();
			var tgl_mulai = $('#tgl_mulai'+id).val();
			var jam_mulai = $('#jamMulai'+id).val();
			var tgl_selesai = $('#tgl_selesai'+id).val();
			var jam_selesai = $('#jamSelesai'+id).val();
			
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			var periode = $('#tgl_dws'+id).val();
						
			$("#np_approver"+id).empty();
			
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getPilihanAtasanLembur');?>",
			 
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			//10-07-2021 - Wina menambah parameter perhitungan jam lembur
			 data: {vnp_karyawan:no_pokok+"#"+periode, vno_pokok:no_pokok, jam_mulai:jam_mulai, jam_selesai:jam_selesai, tgl_mulai:tgl_mulai, tgl_selesai:tgl_selesai},
				success: function(msg){
					if(msg != ''){
						//console.log(msg);
						var get_data = JSON.parse(msg);
						var arr_atasan = get_data.atasan;
						var kode_unit = get_data.kode_unit;
						for(var i=0;i<arr_atasan.length;i++){
							$("#np_approver"+id).append($("<option></option>").attr("value",arr_atasan[i]["no_pokok"]).text(arr_atasan[i]["no_pokok"]+" - "+arr_atasan[i]["nama"]));
							//10-07-2021 - Wina mengganti setting selected approval
							get_unit = arr_atasan[i]["kode_unit"];
							if (get_unit.substr(0, kode_unit.length) == kode_unit) {
								$("#np_approver"+id).val(arr_atasan[i]["no_pokok"]).trigger("change");
							}
						}
						$('.select2').select2();
						// getAtasanLembur(id);

					}
					else{
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
					item["alasan"]			=$('#alasan'+i).val();
					
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
					//	alert ('Silakan isi No. Pokok Dengan Benar.');
					}else{							 
						//$('#nama'+np).val(msg);
					}													  
				 }
			 });  */
			// console.log(vdata);
		}

        function getNamaAtasan(id){
			var np_karyawan = $('#np_approver'+id).val();
			
			$.ajax({
             type: "POST",
             dataType: "JSON",
             url: "<?php echo base_url('pilih_approval/ajax_getNama_approval');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg.status == false){
						alert ('Silakan isi NP Atasan Dengan Benar.');
						$('#np_approver'+id).val('');
					}else{							 
						//alert(' Nomor Pokok Approver Berhasil Ditemukan \n Nama : '+msg.data.nama+'\n Jabatan : '+msg.data.jabatan);
					}													  
				 }
			 });       
		}
	  
        function getNamaAtasanAll(){
			var coloumnHeader = $('#sel1').val();
			var np_karyawan = $('#copyValue').val();

			if (coloumnHeader == 'NP Approver'){
				$.ajax({
	             type: "POST",
	             dataType: "JSON",
	             url: "<?php echo base_url('pilih_approval/ajax_getNama_approval');?>",
	             data: "vnp_karyawan="+np_karyawan,
					success: function(msg){
						if(msg.status == false){
							alert ('Silakan isi No. Pokok Dengan Benar.');
							$('#copyValue').val('');
							$('.np_approver').val('');
						}else{							 
							//alert(' Nomor Pokok Approver Berhasil Ditemukan \n Nama : '+msg.data.nama+'\n Jabatan : '+msg.data.jabatan);
							$('.np_approver').each(function(index,item){
								this.value = np_karyawan;
							});
						}													  
					 }
				}); 
			}
		}
  </script>
