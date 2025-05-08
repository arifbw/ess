<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_payslip extends CI_Model {
	
	function insert_error($data){
		return  $this->db->insert('ess_error',$data);		
	}
	
	function setting(){
		$this->db->select('*');
		$this->db->from('pamlek_setting');		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	public function get_nama_offcycle($payment_date){
		return $this->db->select("GROUP_CONCAT(DISTINCT nama_slip) nama_pembayaran")
						->from("erp_payslip a")
						->join("mst_payslip b","a.wage_type=b.kode")
						->where("a.payment_date",$payment_date)
						->where("b.jenis","Pendapatan")
						->like("a.wage_type","4","after")
						->get()
						->result_array()[0]["nama_pembayaran"];
	}
	
	public function get_nama_offcycle_karyawan($payment_date){
		return $this->db->select("a.np_karyawan")
						->select("GROUP_CONCAT(DISTINCT nama_slip) nama_pembayaran")
						->from("erp_payslip a")
						->join("mst_payslip b","a.wage_type=b.kode")
						->where("a.payment_date",$payment_date)
						->where("b.jenis","Pendapatan")
						->like("a.wage_type","4","after")
						->group_by("a.np_karyawan")
						->get()
						->result_array();
	}
	
	public function set_nama_payslip($id_header,$nama_payslip){
		$this->db->where('id', $id_header);
		$this->db->update('erp_payslip_header', array("nama_payslip"=>$nama_payslip)); 
	}
	
	public function set_nama_payslip_karyawan_offcycle($id_karyawan,$nama_payslip){
		$this->db->where('id', $id_karyawan);
		$this->db->update('erp_payslip_karyawan', array("nama_payslip"=>$nama_payslip));//echo $this->db->last_query()."\n";
	}
	
	public function set_nama_payslip_karyawan_regular($id_header,$nama_payslip){
		$this->db->where('id_payslip_header', $id_header);
		$this->db->update('erp_payslip_karyawan', array("nama_payslip"=>$nama_payslip));//echo $this->db->last_query()."\n";
	}

	public function select_payslip_files()
	{
		$this->db->select('*');
		$this->db->from('erp_payslip_files');
		$this->db->order_by("nama_file", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	public function select_payslip_files_unproses()
	{
		$this->db->select('*');
		$this->db->from('erp_payslip_files');
		$this->db->where('proses','0');
		$this->db->order_by("nama_file", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	function insert_files($data)
	{
		return  $this->db->insert('erp_payslip_files',$data);	
	}
	
	function insert_data_batch($data)
	{
		//echo "<hr>";
		//echo "<br>in function ". __FUNCTION__ .", inserting ".count($data)." data<br>";
		//var_dump($data);
		//echo "<br>";
		$this->db->insert_batch('erp_payslip',$data);
		//echo $this->db->last_query()."<br><br>";
	}

	function update_files($nama_file,$data)
	{
		$this->db->where('nama_file', $nama_file);
		$this->db->update('erp_payslip_files', $data); 
	}
	
	function select_distinc_tapping_time_pamlek_data()
	{
		$this->db->distinct("tapping_time");
		$this->db->from('pamlek_data');
		
		$query = $this->db->get();
		return $query;		
	}
		
	function create_table_data($name)
	{	
		$this->db->query("CREATE TABLE $name like pamlek_data");
	}
	
	function truncate_table($name)
	{
		$this->db->from($name); 
		$this->db->truncate();
	}
	
	function alter_table($name)
	{
		$this->db->query("ALTER TABLE $name MODIFY id INT AUTO_INCREMENT PRIMARY KEY");
	}
	
	function copy_isi($name,$tahun_bulan)
	{
		$this->db->query("INSERT INTO $name 
		(no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file) 
		SELECT no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file FROM pamlek_data 
		WHERE tapping_time like '$tahun_bulan%'");	
	}
	
	function check_table_exist($name)
	{
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function get_id_payment_header($payment_date){
		return $this->db->select("id")
						->from("erp_payslip_header")
						->where("payment_date",$payment_date)
						->get()
						->result_array();
	}
	
	function insert_payment_header($payment_date,$pesan_1,$pesan_2){
		$this->db->insert("erp_payslip_header",array("payment_date"=>$payment_date,"pesan_1"=>$pesan_1,"pesan_2"=>$pesan_2));
	}
	
	function update_with_payslip($with_payslip){
		$this->db->where_in("id",$with_payslip);
		$this->db->update("erp_payslip_karyawan",array("with_payslip"=>true));
	}
	
	function get_id_payment_karyawan($id_header,$np){
		return $this->db->select("id")
						->from("erp_payslip_karyawan")
						->where("id_payslip_header",$id_header)
						->where("np_karyawan",$np)
						->get()
						->result_array();
	}
	
	function insert_payment_karyawan($id_header,$np){
		$this->db->insert("erp_payslip_karyawan",array("id_payslip_header"=>$id_header,"np_karyawan"=>$np,"with_payslip"=>false));
	}
	
	function get_wagetype_negatif(){
		return $this->db->select("GROUP_CONCAT(kode) wagetype")
						->from("mst_payslip")
						->where("is_minus_by_sap","1")
						->get()
						->result_array()[0]["wagetype"];
	}
	
	function update_proses_minus($flag,$wagetype_negatif){
		if(strcmp($flag,"N")==0){
			$this->db->where("proses_minus IS NULL");
		}
		else if(strcmp($flag,"Y")==0){
			$this->db->where("proses_minus", "N");
		}
		$this->db->where_in("wage_type", explode(",",$wagetype_negatif));
		$this->db->update("erp_payslip", array("proses_minus"=>$flag));	
	}
	
	function update_amount_wagetype_minus($payment_date){
		$this->db->set("amount","-1*amount",false)
				 ->where("payment_date",$payment_date)
				 ->where("proses_minus","N")
				 ->update("erp_payslip");
	}
	
	function update_karyawan_payslip($id_payslip_header,$tahun_bulan){
		$this->db->query("UPDATE erp_payslip_karyawan a LEFT JOIN erp_payslip_header b ON a.id_payslip_header = b.id LEFT JOIN erp_master_data_".$tahun_bulan." c ON a.np_karyawan = c.np_karyawan AND b.payment_date = c.tanggal_dws LEFT JOIN mst_karyawan d ON a.np_karyawan = d.no_pokok SET a.nama=IFNULL(c.nama,d.nama), a.kode_unit=IFNULL(c.kode_unit,d.kode_unit), a.nama_unit=IFNULL(c.nama_unit,d.nama_unit), a.kode_jabatan=IFNULL(c.kode_jabatan,d.kode_jabatan), a.nama_jabatan=IFNULL(c.nama_jabatan,d.nama_jabatan) WHERE a.id_payslip_header=$id_payslip_header AND (c.np_karyawan IS NOT NULL OR d.no_pokok IS NOT NULL)");
		echo "\n".$this->db->last_query()."\n";
	}
	
	function update_karyawan_payslip_non_aktif($id_payslip_header,$tahun_bulan){
		$this->db->query("UPDATE erp_payslip_karyawan a join erp_master_data_".$tahun_bulan." b ON a.np_karyawan=b.np_karyawan join (select c1.np_karyawan, max(c1.tanggal_dws) tanggal_dws from erp_master_data_".$tahun_bulan." c1 group by c1.np_karyawan) c ON b.np_karyawan=c.np_karyawan and b.tanggal_dws=c.tanggal_dws SET a.nama=b.nama, a.kode_unit=b.kode_unit, a.nama_unit=b.nama_unit, a.kode_jabatan=b.kode_jabatan, a.nama_jabatan=a.kode_jabatan WHERE a.nama=''");
		echo "\n".$this->db->last_query()."\n";
	}
	
	function encrypt($payment_date){
		$this->db->set("amount","AES_ENCRYPT(amount,md5(concat(payment_date,wage_type,parameter)))",false)
				 ->set("proses_encrypt","1")
				 ->where("payment_date",$payment_date)
				 ->where("proses_encrypt","0")
				 ->update("erp_payslip");
	}
	
	function set_display($id_payslip_header,$display_gaji){
		$this->db->set("start_display","concat(date_add(payment_date, interval $display_gaji day),' 00:00:00')",false)
				 ->where("id",$id_payslip_header)
				 ->where("start_display = ","'0000-00-00 00:00:00'",false)
				 ->update("erp_payslip_header");
		//echo $this->db->last_query()."<br><br>";
	}
	
	function hapus_rincian_payslip($id_payment_karyawan){
		$this->db->where("id_payslip_karyawan",$id_payment_karyawan)
				 ->delete("erp_payslip");
		echo $this->db->last_query()."<br>";
	}
	
	function reset_increment(){
		//$this->db->query("SET @rank:=0;UPDATE erp_payslip SET id=@rank:=@rank+1;");
		//$this->db->query("ALTER TABLE erp_payslip AUTO_INCREMENT = (SELECT MAX(id)+1 FROM erp_payslip);");
	}
	
	function truncate_rank_lembur(){
		$this->truncate_table("rank_lembur_karyawan");
		$this->truncate_table("rank_lembur_karyawan_tahunan");
		$this->truncate_table("rank_lembur_unit_kerja");
	}
	
	function generate_rank_lembur(){
		// 1. rank lembur karyawan
		$this->db->query("INSERT INTO rank_lembur_karyawan SELECT x5.payment_date, x5.nama_payslip, x5.np_karyawan, x5.nama, x5.kode_unit, x5.nama_unit,  x5.direktorat, x5.divisi, x5.departemen, x5.seksi, x5.unit, x5.persentase, x5.rank_nominal_perusahaan, x5.rank_nominal_direktorat, x5.rank_nominal_divisi, x5.rank_nominal_departemen, x5.rank_nominal_seksi, x5.rank_nominal_unit, x5.rank_persen_perusahaan, x5.rank_persen_direktorat, x5.rank_persen_divisi, x5.rank_persen_departemen, x5.rank_persen_seksi, @rank:= case when @kode_unit=x5.unit THEN @rank+1 when @kode_unit:=x5.unit then 1 end rank_persen_unit, NOW() waktu FROM (SELECT w5.* FROM (SELECT x4.*, @rank:= case when @kode_unit=x4.seksi THEN @rank+1 when @kode_unit:=x4.seksi then 1 end rank_persen_seksi FROM (SELECT w4.* FROM (SELECT x3.*, @rank:= case when @kode_unit=x3.departemen THEN @rank+1 when @kode_unit:=x3.departemen then 1 end rank_persen_departemen FROM (SELECT w3.* FROM (SELECT x2.*, @rank:= case when @kode_unit=x2.divisi THEN @rank+1 when @kode_unit:=x2.divisi then 1 end rank_persen_divisi FROM (SELECT w2.* FROM (SELECT x1.*, @rank:= case when @kode_unit=x1.direktorat THEN @rank+1 when @kode_unit:=x1.direktorat then 1 end rank_persen_direktorat FROM (SELECT w1.* FROM (SELECT x0.*, @rank:= case when @payment_date=x0.payment_date THEN @rank+1 when @payment_date:=x0.payment_date then 1 end rank_persen_perusahaan FROM (SELECT w0.* FROM (SELECT z5.*, @rank:= case when @kode_unit=z5.unit THEN @rank+1 when @kode_unit:=z5.unit then 1 end rank_nominal_unit FROM (SELECT y5.* FROM (SELECT z4.*, @rank:= case when @kode_unit=z4.seksi THEN @rank+1 when @kode_unit:=z4.seksi then 1 end rank_nominal_seksi FROM (SELECT y4.* FROM (SELECT z3.*, @rank:= case when @kode_unit=z3.departemen THEN @rank+1 when @kode_unit:=z3.departemen then 1 end rank_nominal_departemen FROM (SELECT y3.* FROM (SELECT z2.*, @rank:= case when @kode_unit=z2.divisi THEN @rank+1 when @kode_unit:=z2.divisi then 1 end rank_nominal_divisi FROM (SELECT y2.* FROM (SELECT z1.*, @rank:= case when @kode_unit=z1.direktorat THEN @rank+1 when @kode_unit:=z1.direktorat then 1 end rank_nominal_direktorat FROM (SELECT y1.* FROM (SELECT z0.*, @rank:= case when @payment_date=z0.payment_date THEN @rank+1 when @payment_date:=z0.payment_date then 1 end rank_nominal_perusahaan FROM (SELECT y0.* FROM (SELECT a.payment_date, a.nama_payslip, a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, RPAD(SUBSTRING(a.kode_unit,1,1),5,'0') direktorat, RPAD(SUBSTRING(a.kode_unit,1,2),5,'0') divisi, RPAD(SUBSTRING(a.kode_unit,1,3),5,'0') departemen, RPAD(SUBSTRING(a.kode_unit,1,4),5,'0') seksi, RPAD(a.kode_unit,5,'0') unit, a.gapok, IFNULL(b.lembur, 0) lembur, IFNULL(b.lembur, 0)/a.gapok*100 persentase FROM (SELECT a1.payment_date, a1.nama_payslip, a2.np_karyawan, a2.nama, a2.kode_unit, a2.nama_unit, a2.kode_jabatan, SUM(AES_DECRYPT(a3.amount, md5(concat(a3.payment_date, a3.wage_type, a3.parameter)))) gapok FROM erp_payslip_header a1 JOIN erp_payslip_karyawan a2 ON a1.id=a2.id_payslip_header LEFT JOIN erp_payslip a3 ON a2.id=a3.id_payslip_karyawan JOIN mst_payslip a4 ON a3.wage_type=a4.kode AND a4.nama_slip='Gaji Pokok', (SELECT @rank:=0, @kode_unit:='', @payment_date:='') var WHERE a1.nama_payslip LIKE 'Gaji%' ESCAPE '!' GROUP BY a1.nama_payslip, a2.np_karyawan) a LEFT JOIN (SELECT b1.nama_payslip, b2.np_karyawan, b2.kode_unit, SUM(AES_DECRYPT(b3.amount, md5(concat(b3.payment_date, b3.wage_type, b3.parameter)))) lembur FROM erp_payslip_header b1 JOIN erp_payslip_karyawan b2 ON b1.id=b2.id_payslip_header LEFT JOIN erp_payslip b3 ON b2.id=b3.id_payslip_karyawan JOIN mst_payslip b4 ON b3.wage_type=b4.kode AND b4.nama_slip in ('Uang Lembur','Koreksi Uang Lembur') WHERE b1.nama_payslip LIKE 'Gaji%' ESCAPE '!' GROUP BY b1.nama_payslip, b2.np_karyawan) b ON a.nama_payslip=b.nama_payslip AND a.np_karyawan=b.np_karyawan AND a.kode_unit=b.kode_unit) y0 ORDER BY y0.payment_date, y0.lembur DESC) z0) y1 ORDER BY y1.payment_date, y1.direktorat, y1.lembur DESC) z1) y2 ORDER BY y2.payment_date, y2.divisi, y2.lembur DESC) z2) y3 ORDER BY y3.payment_date, y3.departemen, y3.lembur DESC) z3) y4 ORDER BY y4.payment_date, y4.seksi, y4.lembur DESC) z4) y5 ORDER BY y5.payment_date, y5.unit, y5.lembur DESC) z5) w0 ORDER BY w0.payment_date, w0.lembur DESC) x0) w1 ORDER BY w1.payment_date, w1.direktorat, w1.persentase DESC) x1) w2 ORDER BY w2.payment_date, w2.divisi, w2.persentase DESC) x2) w3 ORDER BY w3.payment_date, w3.departemen, w3.persentase DESC) x3) w4 ORDER BY w4.payment_date, w4.seksi, w4.persentase DESC) x4) w5 ORDER BY w5.payment_date, w5.unit, w5.persentase DESC) x5");
		//echo $this->db->last_query();
		
		// 2. rank lembur karyawan tahunan
		$this->db->query("INSERT INTO rank_lembur_karyawan_tahunan SELECT x5.tahun, x5.np_karyawan, x5.nama, x5.kode_unit, x5.nama_unit,  x5.direktorat, x5.divisi, x5.departemen, x5.seksi, x5.unit, x5.persentase, x5.rank_nominal_perusahaan, x5.rank_nominal_direktorat, x5.rank_nominal_divisi, x5.rank_nominal_departemen, x5.rank_nominal_seksi, x5.rank_nominal_unit, x5.rank_persen_perusahaan, x5.rank_persen_direktorat, x5.rank_persen_divisi, x5.rank_persen_departemen, x5.rank_persen_seksi, @rank:= case when @kode_unit=x5.unit THEN @rank+1 when @kode_unit:=x5.unit then 1 end rank_persen_unit, NOW() waktu FROM (SELECT w5.* FROM (SELECT x4.*, @rank:= case when @kode_unit=x4.seksi THEN @rank+1 when @kode_unit:=x4.seksi then 1 end rank_persen_seksi FROM (SELECT w4.* FROM (SELECT x3.*, @rank:= case when @kode_unit=x3.departemen THEN @rank+1 when @kode_unit:=x3.departemen then 1 end rank_persen_departemen FROM (SELECT w3.* FROM (SELECT x2.*, @rank:= case when @kode_unit=x2.divisi THEN @rank+1 when @kode_unit:=x2.divisi then 1 end rank_persen_divisi FROM (SELECT w2.* FROM (SELECT x1.*, @rank:= case when @kode_unit=x1.direktorat THEN @rank+1 when @kode_unit:=x1.direktorat then 1 end rank_persen_direktorat FROM (SELECT w1.* FROM (SELECT x0.*, @rank:= case when @tahun=x0.tahun THEN @rank+1 when @tahun:=x0.tahun then 1 end rank_persen_perusahaan FROM (SELECT w0.* FROM (SELECT z5.*, @rank:= case when @kode_unit=z5.unit THEN @rank+1 when @kode_unit:=z5.unit then 1 end rank_nominal_unit FROM (SELECT y5.* FROM (SELECT z4.*, @rank:= case when @kode_unit=z4.seksi THEN @rank+1 when @kode_unit:=z4.seksi then 1 end rank_nominal_seksi FROM (SELECT y4.* FROM (SELECT z3.*, @rank:= case when @kode_unit=z3.departemen THEN @rank+1 when @kode_unit:=z3.departemen then 1 end rank_nominal_departemen FROM (SELECT y3.* FROM (SELECT z2.*, @rank:= case when @kode_unit=z2.divisi THEN @rank+1 when @kode_unit:=z2.divisi then 1 end rank_nominal_divisi FROM (SELECT y2.* FROM (SELECT z1.*, @rank:= case when @kode_unit=z1.direktorat THEN @rank+1 when @kode_unit:=z1.direktorat then 1 end rank_nominal_direktorat FROM (SELECT y1.* FROM (SELECT z0.*, @rank:= case when @tahun=z0.tahun THEN @rank+1 when @tahun:=z0.tahun then 1 end rank_nominal_perusahaan FROM (SELECT y0.* FROM (SELECT a.tahun, a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, RPAD(SUBSTRING(a.kode_unit,1,1),5,'0') direktorat, RPAD(SUBSTRING(a.kode_unit,1,2),5,'0') divisi, RPAD(SUBSTRING(a.kode_unit,1,3),5,'0') departemen, RPAD(SUBSTRING(a.kode_unit,1,4),5,'0') seksi, RPAD(a.kode_unit,5,'0') unit, a.gapok, IFNULL(b.lembur, 0) lembur, IFNULL(b.lembur, 0)/a.gapok*100 persentase FROM (SELECT date_format(a1.payment_date,'%Y') tahun, a2.np_karyawan, a2.nama, a2.kode_unit, a2.nama_unit, a2.kode_jabatan, SUM(AES_DECRYPT(a3.amount, md5(concat(a3.payment_date, a3.wage_type, a3.parameter)))) gapok FROM erp_payslip_header a1 JOIN erp_payslip_karyawan a2 ON a1.id=a2.id_payslip_header LEFT JOIN erp_payslip a3 ON a2.id=a3.id_payslip_karyawan JOIN mst_payslip a4 ON a3.wage_type=a4.kode AND a4.nama_slip='Gaji Pokok', (SELECT @rank:=0, @kode_unit:='', @tahun:='') var WHERE a1.nama_payslip LIKE 'Gaji%' ESCAPE '!' GROUP BY date_format(a1.payment_date,'%Y'), a2.np_karyawan) a LEFT JOIN (SELECT date_format(b1.payment_date,'%Y') tahun, b2.np_karyawan, b2.kode_unit, SUM(AES_DECRYPT(b3.amount, md5(concat(b3.payment_date, b3.wage_type, b3.parameter)))) lembur FROM erp_payslip_header b1 JOIN erp_payslip_karyawan b2 ON b1.id=b2.id_payslip_header LEFT JOIN erp_payslip b3 ON b2.id=b3.id_payslip_karyawan JOIN mst_payslip b4 ON b3.wage_type=b4.kode AND b4.nama_slip in ('Uang Lembur','Koreksi Uang Lembur') WHERE b1.nama_payslip LIKE 'Gaji%' ESCAPE '!' GROUP BY date_format(b1.payment_date,'%Y'), b2.np_karyawan) b ON a.tahun=b.tahun AND a.np_karyawan=b.np_karyawan AND a.kode_unit=b.kode_unit) y0 ORDER BY y0.tahun, y0.lembur DESC) z0) y1 ORDER BY y1.tahun, y1.direktorat, y1.lembur DESC) z1) y2 ORDER BY y2.tahun, y2.divisi, y2.lembur DESC) z2) y3 ORDER BY y3.tahun, y3.departemen, y3.lembur DESC) z3) y4 ORDER BY y4.tahun, y4.seksi, y4.lembur DESC) z4) y5 ORDER BY y5.tahun, y5.unit, y5.lembur DESC) z5) w0 ORDER BY w0.tahun, w0.lembur DESC) x0) w1 ORDER BY w1.tahun, w1.direktorat, w1.persentase DESC) x1) w2 ORDER BY w2.tahun, w2.divisi, w2.persentase DESC) x2) w3 ORDER BY w3.tahun, w3.departemen, w3.persentase DESC) x3) w4 ORDER BY w4.tahun, w4.seksi, w4.persentase DESC) x4) w5 ORDER BY w5.tahun, w5.unit, w5.persentase DESC) x5");
		
		// 3. rank lembur unit kerja
		$this->db->query("INSERT INTO rank_lembur_unit_kerja SELECT x5.payment_date, x5.nama_payslip, x5.kode_unit, x5.nama_unit, x5.level, x5.direktorat, x5.divisi, x5.departemen, x5.seksi, x5.unit, x5.persentase, x5.rank_nominal_perusahaan, x5.rank_nominal_direktorat, x5.rank_nominal_divisi, x5.rank_nominal_departemen, x5.rank_nominal_seksi, x5.rank_nominal_unit, x5.rank_persen_perusahaan, x5.rank_persen_direktorat, x5.rank_persen_divisi, x5.rank_persen_departemen, x5.rank_persen_seksi, @rank:= CASE WHEN @kode_unit=x5.unit THEN @rank+1 WHEN @kode_unit:=x5.unit THEN 1 END rank_persen_unit, NOW() waktu FROM (SELECT w5.* FROM (SELECT x4.*, @rank:= CASE WHEN @kode_unit=x4.seksi THEN @rank+1 WHEN @kode_unit:=x4.seksi THEN 1 END rank_persen_seksi FROM (SELECT w4.* FROM (SELECT x3.*, @rank:= CASE WHEN @kode_unit=x3.departemen THEN @rank+1 WHEN @kode_unit:=x3.departemen THEN 1 END rank_persen_departemen FROM (SELECT w3.* FROM (SELECT x2.*, @rank:= CASE WHEN @kode_unit=x2.divisi THEN @rank+1 WHEN @kode_unit:=x2.divisi THEN 1 END rank_persen_divisi FROM (SELECT w2.* FROM (SELECT x1.*, @rank:= CASE WHEN @kode_unit=x1.direktorat THEN @rank+1 WHEN @kode_unit:=x1.direktorat THEN 1 END rank_persen_direktorat FROM (SELECT w1.* FROM (SELECT x0.*, @rank:= CASE WHEN @level=x0.payment_date THEN @rank+1 WHEN @level:=x0.payment_date THEN 1 END rank_persen_perusahaan FROM (SELECT w0.* FROM (SELECT z5.*, @rank:= CASE WHEN @kode_unit=z5.unit THEN @rank+1 WHEN @kode_unit:=z5.unit THEN 1 END rank_nominal_unit FROM (SELECT y5.* FROM (SELECT z4.*, @rank:= CASE WHEN @kode_unit=z4.seksi THEN @rank+1 WHEN @kode_unit:=z4.seksi THEN 1 END rank_nominal_seksi FROM (SELECT y4.* FROM (SELECT z3.*, @rank:= CASE WHEN @kode_unit=z3.departemen THEN @rank+1 WHEN @kode_unit:=z3.departemen THEN 1 END rank_nominal_departemen FROM (SELECT y3.* FROM (SELECT z2.*, @rank:= CASE WHEN @kode_unit=z2.divisi THEN @rank+1 WHEN @kode_unit:=z2.divisi THEN 1 END rank_nominal_divisi FROM (SELECT y2.* FROM (SELECT z1.*, @rank:= CASE WHEN @kode_unit=z1.direktorat THEN @rank+1 WHEN @kode_unit:=z1.direktorat THEN 1 END rank_nominal_direktorat FROM (SELECT y1.* FROM (SELECT z0.*, @rank:= CASE WHEN @level=z0.level THEN @rank+1 WHEN @level:=z0.level THEN 1 END rank_nominal_perusahaan FROM (SELECT y0.* FROM (SELECT a.payment_date, a.nama_payslip, a.kode_unit, a.nama_unit, CASE WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$',''))=5 THEN '5 - Unit' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$',''))=4 THEN '4 - Seksi' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$',''))=3 THEN '3 - Departemen' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$',''))=2 THEN '2 - Divisi' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$',''))=1 THEN '1 - Direktorat' END level, RPAD(SUBSTRING(a.kode_unit,1,1),5,'0') direktorat, RPAD(SUBSTRING(a.kode_unit,1,2),5,'0') divisi, RPAD(SUBSTRING(a.kode_unit,1,3),5,'0') departemen, RPAD(SUBSTRING(a.kode_unit,1,4),5,'0') seksi, RPAD(a.kode_unit,5,'0') unit, a.gapok, IFNULL(b.lembur, 0) lembur, IFNULL(b.lembur, 0)/a.gapok*100 persentase FROM (SELECT a1.payment_date, a1.nama_payslip, a1.kode_unit, a1.nama_unit, SUM(a2.gapok) gapok FROM (SELECT DISTINCT a11.payment_date, a11.nama_payslip, a12.kode_unit, a12.nama_unit FROM erp_payslip_header a11 JOIN erp_payslip_karyawan a12 ON a11.id=a12.id_payslip_header WHERE a11.nama_payslip LIKE 'Gaji%' ESCAPE '!' AND a12.kode_unit != '') a1 LEFT JOIN (SELECT a21.payment_date, a21.nama_payslip, a22.kode_unit, a22.nama_unit, SUM(AES_DECRYPT(a23.amount, md5(concat(a23.payment_date, a23.wage_type, a23.parameter)))) gapok FROM erp_payslip_header a21 JOIN erp_payslip_karyawan a22 ON a21.id=a22.id_payslip_header LEFT JOIN erp_payslip a23 ON a22.id=a23.id_payslip_karyawan JOIN mst_payslip a24 ON a23.wage_type=a24.kode AND a24.nama_slip='Gaji Pokok', (SELECT @rank:=0, @kode_unit:='', @level:='') var WHERE a21.nama_payslip LIKE 'Gaji%' ESCAPE '!' AND a22.kode_unit != '' GROUP BY a21.nama_payslip, a22.kode_unit) a2 ON a1.payment_date=a2.payment_date AND a2.kode_unit LIKE CONCAT(REGEXP_REPLACE(a1.kode_unit,'0+$',''),'%') GROUP BY a1.payment_date, a1.nama_payslip, a1.kode_unit) a LEFT JOIN (SELECT b1.payment_date, b1.nama_payslip, b1.kode_unit, b1.nama_unit, SUM(b2.lembur) lembur FROM (SELECT DISTINCT b11.payment_date, b11.nama_payslip, b12.kode_unit, b12.nama_unit FROM erp_payslip_header b11 JOIN erp_payslip_karyawan b12 ON b11.id=b12.id_payslip_header WHERE b11.nama_payslip LIKE 'Gaji%' ESCAPE '!' AND b12.kode_unit != '') b1 LEFT JOIN (SELECT b21.payment_date, b22.kode_unit, SUM(AES_DECRYPT(b23.amount, md5(concat(b23.payment_date, b23.wage_type, b23.parameter)))) lembur FROM erp_payslip_header b21 JOIN erp_payslip_karyawan b22 ON b21.id=b22.id_payslip_header LEFT JOIN erp_payslip b23 ON b22.id=b23.id_payslip_karyawan JOIN mst_payslip b24 ON b23.wage_type=b24.kode AND b24.nama_slip IN ('Uang Lembur','Koreksi Uang Lembur') WHERE b21.nama_payslip LIKE 'Gaji%' ESCAPE '!' AND b22.kode_unit != '' GROUP BY b21.nama_payslip, b22.kode_unit) b2 ON b1.payment_date=b2.payment_date AND b2.kode_unit LIKE CONCAT(REGEXP_REPLACE(b1.kode_unit,'0+$',''),'%') GROUP BY b1.payment_date, b1.kode_unit) b ON a.payment_date=b.payment_date AND a.kode_unit=b.kode_unit) y0 ORDER BY y0.payment_date, y0.level, y0.lembur DESC) z0) y1 ORDER BY y1.payment_date, y1.level, y1.direktorat, y1.lembur DESC) z1) y2 ORDER BY y2.payment_date, y2.level, y2.divisi, y2.lembur DESC) z2) y3 ORDER BY y3.payment_date, y3.level, y3.departemen, y3.lembur DESC) z3) y4 ORDER BY y4.payment_date, y4.level, y4.seksi, y4.lembur DESC) z4) y5 ORDER BY y5.payment_date, y5.level, y5.unit, y5.lembur DESC) z5) w0 ORDER BY w0.payment_date, w0.level, w0.lembur DESC) x0) w1 ORDER BY w1.payment_date, w1.level, w1.direktorat, w1.persentase DESC) x1) w2 ORDER BY w2.payment_date, w2.level, w2.divisi, w2.persentase DESC) x2) w3 ORDER BY w3.payment_date, w3.level, w3.departemen, w3.persentase DESC) x3) w4 ORDER BY w4.payment_date, w4.level, w4.seksi, w4.persentase DESC) x4) w5 ORDER BY w5.payment_date, w5.level, w5.unit, w5.persentase DESC) x5");
	}
}
