<?php

	
	if(isset($gaji["Pendapatan"]) and isset($gaji["Potongan"])){
		$num_rows = max(count($gaji["Pendapatan"]),count($gaji["Potongan"]));
	}
	else if(isset($gaji["Pendapatan"]) and !isset($gaji["Potongan"])){
		$num_rows = count($gaji["Pendapatan"]);
	}
	else if(!isset($gaji["Pendapatan"]) and isset($gaji["Potongan"])){
		$num_rows = count($gaji["Potongan"]);
	}
	
	echo "<div class='row'>";
		echo "<div class='col-lg-6'>";
			echo "<div class='row'>";
				echo "<div class='col-lg-12'>";
					echo "<b>PENDAPATAN</b>";
				echo "</div>";
			echo "</div>";
			for($i=0;$i<count($gaji["Pendapatan"]);$i++){
				echo "<div class='row'>";
					echo "<div class='col-lg-8'>";
						echo $gaji["Pendapatan"][$i][0];
					echo "</div>";
				
					echo "<div class='col-lg-4' align='right'>";
						echo number_format($gaji["Pendapatan"][$i][1],0,"",".");
					echo "</div>";
				echo "</div>";
			}
		echo "</div>";
		echo "<div class='col-lg-6'>";
			echo "<div class='row'>";
				echo "<div class='col-lg-12'>";
					echo "<b>POTONGAN</b>";
				echo "</div>";
			echo "</div>";
			for($i=0;$i<count($gaji["Potongan"]);$i++){
				echo "<div class='row'>";
					echo "<div class='col-lg-8'>";
						echo $gaji["Potongan"][$i][0];
					echo "</div>";
				
					echo "<div class='col-lg-4' align='right'>";
						echo number_format($gaji["Potongan"][$i][1],0,"",".");
					echo "</div>";
				echo "</div>";
			}
		echo "</div>";
	echo "</div>";
	/* for($i=0;$i<$num_rows;$i++){
		echo "<div class='row'>";
			echo "<div class='col-lg-4'>";
				if(isset($gaji["Pendapatan"])){
					echo $gaji["Pendapatan"][$i][0];
				}
			echo "</div>";
			echo "<div class='col-lg-2' align='right'>";
				if(!empty($gaji["Pendapatan"][$i][1])){
					echo number_format($gaji["Pendapatan"][$i][1],0,"",".");
				}
			echo "</div>";
			echo "<div class='col-lg-4'>";
				if(isset($gaji["Potongan"])){
					echo $gaji["Potongan"][$i][0];
				}
			echo "</div>";
			echo "<div class='col-lg-2' align='right'>";
				if(!empty($gaji["Potongan"][$i][1])){
					echo number_format($gaji["Potongan"][$i][1],0,"",".");
				}
			echo "</div>";
		echo "</div>";
	} */
	echo "<div class='row'>";
		echo "<div class='col-lg-6'>";
			echo "<hr>";
		echo "</div>";
		echo "<div class='col-lg-6'>";
			echo "<hr>";
		echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
		echo "<div class='col-lg-4'>";
			echo "<b>Total Pendapatan</b>";
		echo "</div>";
		echo "<div class='col-lg-2' align='right'>";
			echo "<b>".number_format($total["Pendapatan"],0,"",".")."</b>";
		echo "</div>";
		echo "<div class='col-lg-4'>";
			echo "<b>Total Potongan</b>";
		echo "</div>";
		echo "<div class='col-lg-2' align='right'>";
			echo "<b>".number_format($total["Potongan"],0,"",".")."</b>";
		echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<hr>";
		echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
		echo "<div class='col-lg-6'>";
			echo "<b>".$gaji["Penghasilan"][0][0]."</b>";
		echo "</div>";
		echo "<div class='col-lg-6' align='right'>";
			echo "<b>".number_format($gaji["Penghasilan"][0][1],0,"",".")."</b>";
		echo "</div>";
	echo "</div>";
	
	if($gaji['tampil_keterangan']=='1')
	{
		echo "<hr>";
		echo "<div class='row'>";
		echo "<div class='col-lg-6'>";
			echo "<div class='row'>";
				echo "<div class='col-lg-12'>";
					echo "<b>WFH</b>";
				echo "</div>";
			echo "</div>";
			for($i=0;$i<count($gaji["wfh"]);$i++){
				echo "<div class='row'>";
					echo "<div class='col-lg-8'>";
						echo $gaji["wfh"][$i][0];
					echo "</div>";
				
					echo "<div class='col-lg-4' align='right'>";
						echo number_format($gaji["wfh"][$i][1],0,"",".")." hari";
					echo "</div>";
				echo "</div>";
			}
		echo "</div>";
		echo "</div>";
	}
	