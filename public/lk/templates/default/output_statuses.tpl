
<div class="lk-status" id="lk-status-<?php echo $this->increment?>">
	Статус <i><?php echo $this->status_info['name']?></i>
	<?php echo $this->status_info['discount'] > 0 ? '<br/><span class="lk-discount"> Скидка '. $this->status_info['discount'] .'% (<span style="text-decoration: line-through">'. $this->status_info['price'] . ' ' . $this->cfg['cur'][1].'</span>)</span>' : ''?>
	<?php if ( $this->increment ) { ?>
		<button onclick="lk.selStatus(<?php echo $this->increment?>)" style="float: right; position: relative; bottom: 7px;" class="lk-button-1">Купить</button>
	<?php } ?>
</div>