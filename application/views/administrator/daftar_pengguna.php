<?php
	echo "<table width='100%' class='table table-striped table-bordered table-hover' id='tabel_daftar_pengguna'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th class='text-center'>No Pokok</th>";
				echo "<th class='text-center'>Nama</th>";
				echo "<th class='text-center'>Kode Kerja</th>";
				echo "<th class='text-center'>Unit Kerja</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		for($i=0;$i<count($daftar_pengguna);$i++){
			echo "<tr>";
				echo "<td>".$daftar_pengguna[$i]["no_pokok"]."</td>";
				echo "<td>".$daftar_pengguna[$i]["nama"]."</td>";
				echo "<td>".$daftar_pengguna[$i]["kode_unit"]."</td>";
				echo "<td>".$daftar_pengguna[$i]["nama_unit"]."</td>";
			echo "</tr>";
		}
		echo "</tbody>";
	echo "</table>";
?>