
<tr>
	<td width="20%">
		<b><?php echo $this->info['name']?></b>
	</td>
	<td width="15%">
		<?php echo $this->info['publicAll'] ? 'Публичный' : 'Приватный'?>
	</td>
	<td width="10%" title="Посещения">
		<?php echo $this->info['visits']?>
	</td>
	<td width="40%">
		<?php echo $this->info['welcomeMessage']?>
	</td>
	<td width="15%">
		<button class="lk-button-1" onclick="lk.warpAlert({create: false, <?php echo 'server: '. $this->info['serverId'] .', id: '. $this->info['id'] .', name: \'' . $this->info['name'] . '\', public: ' . $this->info['publicAll'] . ', msg: \'' . $this->info['welcomeMessage'] . '\', pos: {x: '. $this->info['x'] .', y: '. $this->info['y'] .', z:'. $this->info['z'] .'}'?>})">Редактировать</button>
	</td>
</tr>