<style>
	blink {
  -webkit-animation: 2s linear infinite condemned_blink_effect; // for Android
  animation: 2s linear infinite condemned_blink_effect;
}
@-webkit-keyframes condemned_blink_effect { // for Android
  0% {
    visibility: hidden;
  }
  50% {
    visibility: hidden;
  }
  100% {
    visibility: visible;
  }
}
@keyframes condemned_blink_effect {
  0% {
    visibility: hidden;
  }
  50% {
    visibility: hidden;
  }
  100% {
    visibility: visible;
  }
}
</style>

<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">			
			<center>
			<?php if($menunggu_kehadiran=='0' AND $menunggu_cuti=='0' AND $menunggu_lembur=='0'){?>
				<strong><font color='#CC0000'>Anda Tidak Mempunyai<br>Notifikasi</font></strong>
			<?php }else{ ?>				
				<strong>Menunggu Persetujuan Anda</strong>
			<?php } ?>
			</center>
			
		</div>
		<!-- /.panel-heading -->
		<div class="panel-body">	
			<center>
			<strong>			
			<table>
			<?php if($menunggu_kehadiran!='0'){?>
				<tr>
					<td>	
						<blink><mark style='background-color: #00a1e4;'><font color='white'>Kehadiran</font></blink>						
					</td>
					<td>	
						<blink>&nbsp;:&nbsp;</blink>
					</td>
					<td>	
						<blink><?php echo $menunggu_kehadiran;?></blink>
					</td>
				</tr>
			<?php } ?>
			<?php if($menunggu_cuti!='0'){?>
				<tr>
					<td>	
						<blink><mark style='background-color: #00a1e4;'><font color='white'>Cuti</font></blink>		
					</td>
					<td>	
						<blink>&nbsp;:&nbsp;</blink>
					</td>
					<td>	
						<blink><?php echo $menunggu_cuti;?></blink>
					</td>
				</tr>
			<?php } ?>
			<?php if($menunggu_lembur!='0'){?>
				<tr>
					<td>	
						<blink><mark style='background-color: #00a1e4;'><font color='white'>Lembur</font></blink>		
					</td>
					<td>	
						<blink>&nbsp;:&nbsp;</blink>
					</td>
					<td>	
						<blink><?php echo $menunggu_lembur;?></blink>
					</td>
				</tr>
			<?php } ?>
			</table>
			</strong>
			</center>
		<!-- /.list-group -->			
		</div>
		<!-- /.panel-body -->
	</div>
		<!-- /.panel -->
</div>
<!-- /.col-lg-4 -->
		