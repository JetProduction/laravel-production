
<div class="lk-block-ban" align="left">
	<button class="lk-button-1" style="float: right" onclick="lk.unban(<?php echo $this->increment?>, this)">Разбаниться за <?php echo $this->lk->getUnbanPrice($this->increment) . ' ' . $this->cfg['cur'][1]?></button>
	<b style="font-size: 18px"><?php echo $this->cfg['server'][$this->increment]['name']?></b> <span style="font-size: 15px; color: #1A1A1A">(Забанил <?php echo $this->info['name']?> <?php echo date('d/m/Y в H:i:s', $this->info['time'])?>)</span>
	
	<br/><span style="color: #676767">Причина: <?php echo $this->info['reason']?></span>
</div>