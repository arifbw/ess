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
				if(@$akses["tambah"]) { ?>
                <!-- <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>					
                    Dalam rangka <i>physical distancing</i> selama masa pandemi Covid-19, maka kapasitas kendaraan tersedia untuk Innova 4 orang, Hiace 8 orang, Elf 11 orang, Bus 20 orang.
                </div> -->
                
		        <div class="row">
		            <a class="btn btn-outline btn-primary btn-xs" href="<?= base_url('food_n_go/kendaraan/data_pemesanan')?>" title="kembali"><i class="fa fa-arrow-circle-left fa-fw"></i> Kembali</a>
		        </div>
		        <div class="panel-body">
		            <form role="form" action="<?php echo base_url(); ?>food_n_go/kendaraan/data_pemesanan/action_insert_data_pemesanan" id="formulir_tambah" method="post">
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Nama Pemesan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input id="input-np-karyawan" type="text" class="form-control" style="width: 100%;" placeholder="Harus diisi" title="" value="<?= $this->session->userdata('no_pokok').' - '.$this->session->userdata('nama')?>" readonly>
		                        </div>
                                <input type="hidden" name="insert_np_karyawan" value="<?= $this->session->userdata('no_pokok')?>">
                                <input type="hidden" name="insert_nama" value="<?= $this->session->userdata('nama')?>">
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Telp/HP Pemesan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="text" class="form-control" name='no_hp' style="width: 100%;" placeholder="Harus diisi" title="Sertakan No. HP yang bisa dihubungi" required>
		                        </div>
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>No Ext Pemesan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="text" class="form-control" name='no_ext_pemesan' style="width: 100%;" placeholder="Harus diisi" title="Sertakan No. Telp Kantor yang bisa dihubungi" required>
		                        </div>
		                    </div>
		                </div>
                        <br>
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Unit Pemesan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <select class="form-control select2" id="kode_unit_pemesan" name="kode_unit_pemesan" onchange="get_approval();" style="width: 100%;" required>
                                        <option value="" selected disabled>-- Pilih Unit --</option>
		                                <?php foreach ($arr_pemesan as $value) 
										{
											/*21 01 2022 Tri Wibowo 7648, validasi ratting di hilangkan request mas eka pak firman
											  <option value='<?php echo $value['kode_unit']?>' <?= count_belum_rating($value['kode_unit'])>0?'disabled':''?>><?php echo $value['nama_unit'].(count_belum_rating($value['kode_unit'])>0?' (Belum input penilaian sebelumnya)':'')?></option>
											*/
										?>                                       

									   <option value='<?php echo $value['kode_unit']?>'><?php echo $value['nama_unit']?></option>
									   
									   
                                        <?php } ?>
		                            </select>
                                    <input type="hidden" name="nama_unit_pemesan" id="nama_unit_pemesan">
		                        </div>
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Pilih Approver</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <select class="form-control select2" id="verified_by" name="verified_by" style="width: 100%;" required>
                                        <option value="" selected disabled>-- Pilih Approver --</option>
		                            </select>
		                        </div>
		                    </div>
		                </div>
                        <br>
                        <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Nama PIC</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <select class="form-control select2" id="np_karyawan_pic" name="np_karyawan_pic" style="width: 100%;" required>
                                        <?php foreach ($np_pic as $value) { ?>
                                        <option value='<?php echo $value->no_pokok.' - '.$value->nama?>'><?php echo $value->no_pokok.' - '.$value->nama?></option>
                                        <?php } 
                                        if($this->session->userdata('grup')==4){
                                            echo '<option value="'.$this->session->userdata('no_pokok').' - '.$this->session->userdata('nama').'" selected>'.$this->session->userdata('no_pokok').' - '.$this->session->userdata('nama').'</option>';
                                        }
                                        ?>
		                            </select>
									<button type="button" id="btn-pic-is-pemesan">Sama dengan Pemesan</button>
		                        </div>
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Telp/HP PIC</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="text" class="form-control" name="no_hp_pic" style="width: 100%;" placeholder="Harus diisi" title="Sertakan No. HP yang bisa dihubungi" required>
		                        </div>
		                    </div>
		                </div>
                        
		                <!-- <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>No Ext PIC</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="text" class="form-control" name="no_ext_pic" style="width: 100%;" placeholder="Harus diisi" title="Sertakan No. Telp Kantor yang bisa dihubungi" required>
		                        </div>
		                    </div>
		                </div> -->

		                <!--<div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Tujuan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="text" class="form-control" name='tujuan' style="width: 100%;" placeholder="Harus diisi" required>
		                        </div>
		                    </div>
		                </div>-->
                        <br>
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Unit Pemroses</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <select class="form-control select2" name="unit_pemroses" style="width: 100%;" required>
                                        <option value="Jakarta">Unit Kendaraan Jakarta</option>
                                        <option value="Karawang">Unit Kendaraan Karawang</option>
		                            </select>
		                        </div>
		                    </div>
		                </div>
                        
		                <!-- <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Jenis Kendaraan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <select class="form-control select2" name="jenis_kendaraan_request" style="width: 100%;" required>
                                        <?php foreach($jenis_kendaraan as $row){?>
                                        <option value="<?= str_replace('PERURI ','',str_replace('SEWA ','',str_replace('KENDARAAN ','',$row->nama)))?>"><?= str_replace('PERURI ','',str_replace('SEWA ','',str_replace('KENDARAAN ','',$row->nama)))?></option>
                                        <?php } ?>
		                            </select>
		                        </div>
		                    </div>
		                </div> -->
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Kota Penjemputan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <select class="form-control select2" id="kode_kota_asal" name="kode_kota_asal" onchange="fill_name_kota()" style="width: 100%;" required>
                                        <option value="" selected disabled>-- Pilih Kota --</option>
		                                <?php foreach ($arr_kota as $value) { ?>
                                        <option value='<?php echo $value['kode_wilayah']?>'><?php echo $value['kota'].', '.str_replace('Prop. ','',$value['prov'])?></option>
                                        <?php } ?>
		                            </select>
                                    <input type="hidden" name="nama_kota_asal" id="nama_kota_asal">
		                        </div>
		                    </div>
		                </div>

		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Lokasi Penjemputan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="text" class="form-control" name='lokasi_jemput' style="width: 100%;" placeholder="Harus diisi" required>
		                        </div>
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Jumlah Penumpang</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="number" class="form-control" name='jumlah_penumpang' style="width: 100%;" placeholder="Harus diisi" required>
		                        </div>
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Waktu Berangkat</label>
		                        </div>
		                        <div class="col-lg-4">
		                            <input type="text" class="form-control" name="tanggal_berangkat" id="insert_tanggal_berangkat" placeholder="Tanggal harus diisi" required>
		                        </div>
		                        <div class="col-lg-3">
		                            <input type="text" class="form-control datetimepicker5" name="jam" id="insert_jam" placeholder="Jam harus diisi" required>
		                        </div>
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Keterangan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <textarea class="form-control" name='keterangan' style="width: 100%;" placeholder="Optional"></textarea>
		                        </div>
		                    </div>
		                </div>
                        
		                <!--<div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>PP?</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="checkbox" name='is_pp'>
		                        </div>
		                    </div>
		                </div>
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Menginap?</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <input type="checkbox" name='is_inap' id="is_inap" onchange="show_hide_inap(this)">
		                        </div>
		                    </div>
		                </div>-->
                        
		                <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Tipe Perjalanan</label>
		                        </div>
		                        <div class="col-lg-7">
		                            <select class="form-control select2" id="ket_list" name="ket_list" onchange="show_hide_inap_list()" style="width: 100%;" required>
                                        <option value="1">Sekali jalan</option>
                                        <option value="2">PP</option>
                                        <option value="3">Menginap</option>
		                            </select>
		                        </div>
		                    </div>
		                </div>
                        
                        <div id="div_inap_tanggal" style="display: none;">
                        <div class="row">
		                    <div class="form-group">
		                        <div class="col-lg-2">
		                            <label>Range Tanggal</label>
		                        </div>
		                        <div class="col-lg-3">
		                            <input type="text" class="form-control" name="tanggal_awal" id="insert_tanggal_awal" placeholder="Tanggal start">
		                        </div>
                                <div class="col-lg-1">
                                    <label>sampai</label>
                                </div>
		                        <div class="col-lg-3">
		                            <input type="text" class="form-control" name="tanggal_akhir" id="insert_tanggal_akhir" placeholder="Tanggal end">
		                        </div>
		                    </div>
		                </div>
                        </div>
                        
                        <div class="col-lg-3" style="margin-top: 50px;">
                            <section class="panel">
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
                        <div class="row">
                            <div class="col-lg-9">
                                <section class="panel">
                                    <div class="form-horizontal scrollable-form" xml_error_string style="margin-top: -20px">
                                        <input type="hidden" id="maxIndexTable" />
                                        <table class="table table-striped table-hover table-bordered" id="editable-sample" width="100%">
                                            <thead style="position:relative">
                                                <tr>
                                                    <th style="width:40%;">Kota tujuan</th>
                                                    <th style="width:50%;">Lokasi tujuan</th>
                                                    <th style="width:auto;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodyTable">
                                            </tbody>
                                        </table>
                                    </div>
                                </section>
                            </div>
                        </div>
                        
		                <div class="row">
		                    <div class="col-lg-9 text-right">
		                        <input type="submit" name="submit" value="submit" class="btn btn-primary">
		                    </div>
		                </div>
		            </form>
		        </div>
		        <?php } ?>
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
		            $('.fixed-form').css({
		                'padding-right': scrollWidth
		            });
		        }).resize();
		        $('.select2').select2();
		        $('.datetimepicker5').datetimepicker({
		            format: 'HH:mm'
		        });

		        $('#insert_tanggal_berangkat').datetimepicker({
		            format: 'DD-MM-Y',
		            //minDate: '<?php //echo date('Y/m/d');?>'
		        });

		        $('#insert_tanggal_awal').datetimepicker({
		            format: 'DD-MM-Y',
		            minDate: '<?php echo date('Y/m/d');?>'
		        });

		        $('#insert_tanggal_akhir').datetimepicker({
		            format: 'DD-MM-Y',
		            minDate: '<?php echo date('Y/m/d');?>'
		        });
                
                /* start repeating */
		        $('#maxIndexTable').val(0);

		        $('#addNewRowTable').click(addNewRow);

		        $('#addNewRowTable').click();

		    });

		    function addNewRow() {
		        var lastIndexTable = Number($('#maxIndexTable').val());
		        var baris = Number($('#tambah_baris').val());

		        var i;
		        for (i = 0; i < baris; i++) {
		            lastIndexTable = lastIndexTable + 1;
		            var newRow = "<tr id=\"tableRow" + lastIndexTable + "\">" +
		                "	<td><select class=\"form-control select2 input-sm no_pokok\" id=\"no_pokok" + lastIndexTable + "\" name=\"kode_kota_tujuan[]\" required><?= $list_kota ?></select></td>		" +
                        
		                "	<td><input type=\"text\" class=\"form-control input-sm keterangan_tujuan\" id=\"keteranganTujuan" + lastIndexTable + "\" name=\"keterangan_tujuan[]\" required></td>" +
                        
		                "	<td><button class=\"btn btn-danger btn-sm\" type=\"button\" onclick=\"deleteRow('tableRow" + lastIndexTable + "')\">		" +
		                " 		<i class=\"fa fa-trash-o\"/> </button></td>	        																" +
		                "</tr>";
		            $('#bodyTable').append(newRow);
		            //$('#jamMulai' + lastIndexTable).addClass('datetimepicker5');
		            $('.select2').select2();
		        }
		        $('#maxIndexTable').val(lastIndexTable);
		    }

		    function deleteRow(tag) {
		        $('#' + tag).remove();
		    }

		    function change_tgl(id) {
		        var tgl_dws = $('#tgl_dws' + id).val();
		        $('#tgl_mulai' + id).val(tgl_dws);
		        $('#tgl_selesai' + id).val(tgl_dws);
		    }

		    function selectHeaderCopy() {
		        var coloumnHeader = $('#sel1').val();
		        //reset
		        $('#rowCopyValue').empty();
		        if (coloumnHeader == 'NP') {
		            $('#rowCopyValue').append('<select class=\"form-control select2 input-sm\" id=\"copyValue\"><?= $list_kota ?></select>');
		            $('.select2').select2();
		        } else if (coloumnHeader == 'NP Approver') {
		            $('#rowCopyValue').append('<select class=\"form-control select2 input-sm\" id=\"copyValue\"><?= @$list_apv ?></select>');
		            $('.select2').select2();
		        } else if (coloumnHeader == 'Tanggal Mulai' || coloumnHeader == 'Tanggal Selesai' || coloumnHeader == 'Tertanggal') {
		            $('#rowCopyValue').append('<input type=\"date\" class=\"form-control input-sm\" id=\"copyValue\">');
		        } else if (coloumnHeader == 'Jam Mulai' || coloumnHeader == 'Jam Selesai') {
		            $('#rowCopyValue').append('<input type=\"time\" class=\"form-control datetimepicker5 input-sm\" id=\"copyValue\">');
		        } else if (coloumnHeader == 'Generate Karyawan') {
		            $('#rowCopyValue').append('<select class=\"form-control select2 input-sm\" id=\"copyValue\" style=\"max-width:300px;\"><?= $list_unit_kerja ?></select>');
		            $('.select2').select2();
		        }

		        if (coloumnHeader == 'Generate Karyawan') {
		            $("#tombol_aksi").text('Generate');
		            $("#tombol_aksi").attr("onClick", "generateKaryawan()");
		        } else {
		            $("#tombol_aksi").text('Copy');
		            $("#tombol_aksi").attr("onClick", "copyToRow()");
		        }
		    }
		    selectHeaderCopy();

		    function generateKaryawan() {
		        $('#bodyTable').empty();

		        var kode_unit = $('#copyValue').val();
		        $.ajax({
		            type: "POST",
		            dataType: "html",
		            url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getKaryawanUnitKerjaLembur');?>",
		            data: "vkode_unit=" + kode_unit,
		            success: function(msg) {
		                if (msg != '') {
		                    var arr_karyawan = JSON.parse(msg);
		                    for (var i = 0; i < arr_karyawan.length; i++) {
		                        addNewRow();
		                        //$("#no_pokok"+$("#maxIndexTable").val()).attr("onchange","");
		                        $("#no_pokok" + $("#maxIndexTable").val()).val(arr_karyawan[i]["no_pokok"]).trigger("change");
		                        $("#np_approver" + $("#maxIndexTable").val()).val(arr_karyawan[i]["np_atasan"]).trigger("change");
		                        $("#no_pokok" + $("#maxIndexTable").val()).attr("onchange", "getPilihanAtasanLembur(" + $("#maxIndexTable").val() + ")");
		                    }
		                } else {
		                    alert('Karyawan tidak ditemukan!');
		                }
		            }
		        });
		    }

		    function copyToRow() {
		        var coloumnHeader = $('#sel1').val();
		        var copyValue = $('#copyValue').val();

		        if (coloumnHeader == 'NP') {
		            $('.no_pokok').each(function(index, item) {
		                $("#" + this.id).val(copyValue).trigger("change");
		            });
		        } else if (coloumnHeader == 'NP Approver') {
		            $('.np_approver').each(function(index, item) {
		                $("#" + this.id).val(copyValue).trigger("change");
		                //this.value = copyValue;
		            });
		        } else if (coloumnHeader == 'Tertanggal') {
		            $('.tgl_dws').each(function(index, item) {
		                this.value = copyValue;
		                $("#" + this.id).val(copyValue).trigger("change");
		            });
		            $('.tgl_mulai').each(function(index, item) {
		                this.value = copyValue;
		            });
		            $('.tgl_selesai').each(function(index, item) {
		                this.value = copyValue;
		            });
		        } else if (coloumnHeader == 'Tanggal Mulai') {
		            $('.tgl_mulai').each(function(index, item) {
		                this.value = copyValue;
		            });
		        } else if (coloumnHeader == 'Tanggal Selesai') {
		            $('.tgl_selesai').each(function(index, item) {
		                this.value = copyValue;
		            });
		        } else if (coloumnHeader == 'Jam Mulai') {
		            $('.jam_mulai').each(function(index, item) {
		                this.value = copyValue;
		            });
		        } else if (coloumnHeader == 'Jam Selesai') {
		            $('.keterangan_tujuan').each(function(index, item) {
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
		        	$('.keterangan_tujuan').each(function(index,item){
		        		if(item.value.trim() == ''){
		        			this.value = copyValue;
		        		}
		        	});
		        }*/
		    }

		    function getNama(id) {
		        var no_pokok = $('#no_pokok' + id).val();

		        $.ajax({
		            type: "POST",
		            dataType: "html",
		            url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getNama');?>",
		            data: "vno_pokok=" + no_pokok,
		            success: function(msg) {
		                if (msg == '') {
		                    alert('Silahkan isi No. Pokok Dengan Benar.');
		                } else {
		                    $('#nama' + id).val(msg);
		                }
		            }
		        });
		    }

		    function getAtasanLembur(id) {
		        //alert("asd");
		        var no_pokok = $('#no_pokok' + id).val();
		        $.ajax({
		            type: "POST",
		            dataType: "html",
		            url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getAtasanLembur');?>",
		            data: "vnp_karyawan=" + no_pokok,
		            success: function(msg) {
		                if (msg != '') {
		                    //console.log(msg);
		                    //$("select[id=np_approver"+id+"] option[value="+msg+"]").attr('selected','selected');
		                    $("#np_approver" + id).val(msg).trigger("change");
		                } else if ($("#np_approver" + id).children().length > 0) {
		                    $("#np_approver" + id).get(0).selectedIndex = 0;
		                    $("#np_approver" + id).trigger("change");
		                } else {
		                    alert('Atasan tidak ditemukan!');
		                }
		            }
		        });
		    }

		    function getPilihanAtasanLembur(id) {
		        //alert("asd");
		        var no_pokok = $('#no_pokok' + id).val();

		        //2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
		        var periode = $('#tgl_dws' + id).val();

		        $("#np_approver" + id).empty();

		        $.ajax({
		            type: "POST",
		            dataType: "html",
		            url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getPilihanAtasanLembur');?>",

		            //2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
		            data: "vnp_karyawan=" + no_pokok + "#" + periode,
		            success: function(msg) {
		                if (msg != '') {
		                    //console.log(msg);
		                    var arr_atasan = JSON.parse(msg);
		                    for (var i = 0; i < arr_atasan.length; i++) {
		                        $("#np_approver" + id).append($("<option></option>").attr("value", arr_atasan[i]["no_pokok"]).text(arr_atasan[i]["no_pokok"] + " - " + arr_atasan[i]["nama"]));
		                    }
		                    $('.select2').select2();
		                    getAtasanLembur(id);
		                } else {
		                    alert('Atasan tidak ditemukan!');
		                }
		            }
		        });
		    }

		    function simpan() {
		        var totalRow = Number($('#maxIndexTable').val());
		        jsonObj = [];
		        var i;
		        for (i = 1; i <= totalRow; i++) {
		            item = {}
		            item["no_pokok"] = $('#no_pokok' + i).val();
		            item["np_approver"] = $('#np_approver' + i).val();
		            item["tgl_dws"] = $('#tgl_dws' + i).val();
		            item["tgl_mulai"] = $('#tgl_mulai' + i).val();
		            item["jamMulai"] = $('#jamMulai' + i).val();
		            item["tgl_selesai"] = $('#tgl_selesai' + i).val();
		            item["keteranganTujuan"] = $('#keteranganTujuan' + i).val();
		            item["ket"] = $('#ket' + i).val();

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
            
            function get_approval(){
                var kode_unit_pemesan = $('#kode_unit_pemesan').children("option:selected").val();
                var nama_unit_pemesan = $('#kode_unit_pemesan').children("option:selected").text();
                $('#nama_unit_pemesan').val(nama_unit_pemesan);
                
                $("#verified_by").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/get_apv');?>",
		            type: "POST",
		            dataType: "json",
		            data: {kode_unit: kode_unit_pemesan},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#verified_by").append($("<option></option>").attr("value", response[i]["no_pokok"] + " - " + response[i]["nama"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
                            if(response[i]["kode_unit"].substring(0, 3)==kode_unit_pemesan.substring(0, 3) && response[i]["as_default"]==1){
                                $("#verified_by").val(response[i]["no_pokok"] + " - " + response[i]["nama"]);
                                $("#verified_by").trigger('change');
                            }
                        }
                        $('.select2').select2();
		            }
		        });
            }
            
            function get_pic(){
                var kode_unit_pemesan = $('#kode_unit_pemesan').children("option:selected").val();
                
                $("#np_karyawan_pic").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/get_pic');?>",
		            type: "POST",
		            dataType: "json",
		            data: {kode_unit: kode_unit_pemesan},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#np_karyawan_pic").append($("<option></option>").attr("value", response[i]["no_pokok"] + " - " + response[i]["nama"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
                        }
                        $('.select2').select2();
		            }
		        });
            }
            
            function show_hide_inap(input){
                if(input.checked==true){
                    $('#insert_tanggal_awal').prop('required',true);
                    $('#insert_tanggal_akhir').prop('required',true);
                    $('#div_inap_tanggal').show();
                } else{
                    $('#insert_tanggal_awal').prop('required',false);
                    $('#insert_tanggal_akhir').prop('required',false);
                    $('#div_inap_tanggal').hide();
                }
            }
            
            function show_hide_inap_list(){
                var ket_list = $('#ket_list').children("option:selected").val();
                
                if(ket_list != 1){
                    $('#insert_tanggal_awal').prop('required',true);
                    $('#insert_tanggal_akhir').prop('required',true);
                    $('#div_inap_tanggal').show();
                } else{
                    $('#insert_tanggal_awal').prop('required',false);
                    $('#insert_tanggal_akhir').prop('required',false);
                    $('#div_inap_tanggal').hide();
                }
            }
            
            $("#insert_tanggal_awal").on("dp.change", function (e) {
                var newDate =$('#insert_tanggal_awal').val();
                $('#insert_tanggal_akhir').val(newDate);
                $('#insert_tanggal_akhir').data("DateTimePicker").minDate(newDate);
            });
            
            $("#insert_tanggal_berangkat").on("dp.change", function (e) {
                var newDate =$('#insert_tanggal_berangkat').val();
                if(newDate==''){
                    alert('Tanggal berangkat belum diisi');
                } else{
                    $('#insert_tanggal_awal').val(newDate);
                    $('#insert_tanggal_awal').data("DateTimePicker").minDate(newDate);
                    $('#insert_tanggal_akhir').val(newDate);
                    $('#insert_tanggal_akhir').data("DateTimePicker").minDate(newDate);
                }
            });
            
            function fill_name_kota(){
                var kode_kota_asal = $('#kode_kota_asal').children("option:selected").val();
                var nama_kota_asal = $('#kode_kota_asal').children("option:selected").text();
                $('#nama_kota_asal').val(nama_kota_asal);
            }

			$('#btn-pic-is-pemesan').on('click', function(e){
				e.preventDefault();
				$('#np_karyawan_pic').val( $('#input-np-karyawan').val() ).trigger('change');
				$('input[name="no_hp_pic"]').val( $('input[name="no_hp"]').val() ).trigger('change');
				// $('input[name="no_ext_pic"]').val( $('input[name="no_ext_pemesan"]').val() ).trigger('change');
			});
		</script>
		<script>
			var np_pic = <?= json_encode($np_pic)?>;
			$('#kode_unit_pemesan').on('change', function(e){
				e.preventDefault();
				var e_select = $('#np_karyawan_pic');
				var kode_unit = $(this).val();
				var pic_filtered = np_pic.filter(o=>{ return o.kode_unit == kode_unit; });
				e_select.empty();
				e_select.append(new Option('<?= $this->session->userdata('no_pokok')?>' + ' - ' + '<?= $this->session->userdata('nama')?>', '<?= $this->session->userdata('no_pokok')?>' + ' - ' + '<?= $this->session->userdata('nama')?>'));
				for (const i of pic_filtered) {
					e_select.append(new Option(`${i.no_pokok} - ${i.nama}`, `${i.no_pokok} - ${i.nama}`));
				}
			});
		</script>
