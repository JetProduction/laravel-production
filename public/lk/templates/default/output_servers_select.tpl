
<div class="lk-server-select" id="lk-server-select-<?php echo $this->increment?>" onclick="lk.selectServer(this, <?php echo $this->increment?>)">
	<b>Сервер <?php echo $this->server_serverInfo['name']?></b>
	<span style="float: right; margin-right: 5px;">
		<?php echo '<span>' . $this->status['name'] . '</span>' . ($this->end_time ? ' <span style="font-size: 12px">(Закончится ' . date('d.m.Y', $this->end_time) . ')</span>' : ' <span style="font-size: 12px"></span>')?>
	</span>
</div>