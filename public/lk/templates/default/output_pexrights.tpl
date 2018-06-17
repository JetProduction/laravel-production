
<tr>
	<td width="20%" align="center">
		<b title="<?php echo $this->info[1]?>"><?php echo $this->info[2]?></b>
	</td>
	
	<td width="35%">
		<?php echo $this->info[3]?>
	</td>
	
	<td width="15%" align="center" title="<?php echo $this->untilTime?>">
		<?php echo $this->time?>
	</td>
	
	<td width="30%" align="center">
		<?php if ( !$this->info['right_have'] ) {?>
			<button class="lk-button-1" onclick="lk.buyPexRight(<?php echo $this->serverId?>, <?php echo $this->increment?>)">Купить за <?php echo $this->info[4] . ' ' . $this->cfg['cur'][1]?></button>
		<?php } else { ?>
			<b>Есть</b><br/><span class="lk-pexright-duration"><?php echo $this->info['duration']?></span>
		<?php } ?>
	</td>
</tr>