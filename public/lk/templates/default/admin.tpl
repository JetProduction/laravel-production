<div class="lk-block-admin">
	<div align="center">
		<?php if ( !$this->lk->IsAdminEditor() ) { ?>
			Вам доступна админпанель
		<?php } else { ?>
			Вы администрируете в личном кабинете игрока <b><?php echo $this->user['name']?></b> (<a href="<?php echo $this->path?>index.php">Вернуться</a>)
		<?php } ?>
		<br/>
		<input type="text" class="lk-input_text-1" style="width: 300px" onkeyup="searchPlayer(this.value)" placeholder="Введите id/ник игрока"/>
		<div id="lk-block-admin-playerMenu" align="left"></div>
	</div>
	
	<script type="text/javascript">
		lk.requestSearchPlayer = new _req('<?php echo $this->path?>admin/searchOfPlayers.php', 'key=0', false);
		
		function searchPlayer(name) {
			if ( name.length < 3 || name.length > 30 ) return;
			
			var playerList = document.getElementById('lk-block-admin-playerMenu');
			lk.requestSearchPlayer.send_post({
				nickname: name
			}, function( json ) {
				
				if ( json.status == 'success' ) {
					var list = '';
					for ( var i = 0, Max = json.players.length - 1; i < Max; i ++ ) {
						var player = json.players[i];
						list += '<a href="<?php echo $this->path?>index.php?userid='+ player.userid +'" target="_blank">'+ player.name +'</a>';
					}
					
					if ( list != '' ) {
						playerList.innerHTML = list;
					} else {
						playerList.innerHTML = 'Игроки не найдены';
					}
				} else {
					playerList.innerHTML = 'Ошибка: ' + json.message;
				}
				
			});
		}
		
	</script>
	
	<?php if ( $this->lk->IsAdminEditor() ) { ?>
		<div align="center">
			<div>
				<input type="button" onclick="lk.admin.showAdminPanel(1)" class="lk-button-1" value="Дать денег">
				<input type="button" onclick="lk.admin.showAdminPanel(2)" class="lk-button-1" value="Дать монет">
				<input type="button" onclick="lk.admin.showAdminPanel(3)" class="lk-button-1" value="Удалить статус">
				<input type="button" onclick="lk.admin.showAdminPanel(4)" class="lk-button-1" value="Забанить">
				<input type="button" onclick="lk.admin.showAdminPanel(5)" class="lk-button-1" value="Команда">
			</div>
			
			<div id="lk-adminPanels">
				<div>
					<input type="text" class="lk-input_text-1" placeholder="Введите кол-во денег"/>
					<input type="button" onclick="lk.admin.giveMoney(parseInt(this.previousElementSibling.value))" class="lk-button-1" value="Дать денег">
				</div>
				
				<div>
					<select class="lk-input_text-1 lk-select-option-1" style="width: 180px;">
						<?php echo $this->tpls->outputServerAsOption()?>
					</select>
					<input type="text" class="lk-input_text-1" placeholder="Введите кол-во монет"/>
					<input type="button" onclick="lk.admin.giveIC(parseInt(this.previousElementSibling.previousElementSibling.value), parseInt(this.previousElementSibling.value))" class="lk-button-1" value="Дать монеты">
				</div>
				
				<div>
					<select class="lk-input_text-1 lk-select-option-1" style="width: 180px;">
						<?php echo $this->tpls->outputServerAsOption()?>
					</select>
					<input type="button" onclick="lk.admin.removeStatus(parseInt(this.previousElementSibling.value))" class="lk-button-1" value="Удалить статус">
				</div>
				
				<div>
					<select class="lk-input_text-1 lk-select-option-1" style="width: 180px;">
						<?php echo $this->tpls->outputServerAsOption()?>
					</select>
					<input type="text" class="lk-input_text-1" placeholder="Причина"/>
					<input type="number" class="lk-input_text-1" style="width: 50px" value="30" placeholder="Дни"/>
					<input type="button" onclick="lk.admin.ban(parseInt(this.previousElementSibling.previousElementSibling.previousElementSibling.value), parseInt(this.previousElementSibling.value), this.previousElementSibling.previousElementSibling.value)" class="lk-button-1" value="Забанить">
				</div>
				
				<div>
					Список всех команд вы можете узнать в консоле ЛК, которая по умолчанию находится в lk/admin/console/index.php<br/>
					<input type="text" class="lk-input_text-1" style="width: 300px" placeholder="Введите команду"/>
					<input type="button" onclick="lk.admin.sendCMD(this.previousElementSibling.value)" class="lk-button-1" value="Ок">
				</div>
			</div>
		</div>
		
		<div id="lk-adminConsole-output" onclick="this.style.display = 'none'"></div>
		
		<script type="text/javascript">
			lk.adminPanelShownId = -1;
			lk.requestAdminCMD = new _req('<?php echo $this->path?><?php echo $this->cfg['admin']['pathToAdminCMD']?>', 'key=0', false);
			
			lk.admin = {
				
				showAdminPanel: function(id) {
					var panels = document.getElementById('lk-adminPanels').children;
					if ( lk.adminPanelShownId != -1 ) {
						panels[lk.adminPanelShownId - 1].style.display = 'none';
					}
					panels[id - 1].style.display = 'block';
					lk.adminPanelShownId = id;
				},
				
				giveMoney: function(money) {
					if ( !(money * money) || money <= 0 ) {
						return this.showMessage('<b>Ошибка:</b> введите число больше 0');
					}
					this.sendCMD('give money '+ lk.username +' '+ money, function() {
						lk.giveMoney(money);
					});
				},
				
				giveIC: function(server, money) {
					if ( !(money * money) || money <= 0 ) {
						return this.showMessage('<b>Ошибка:</b> введите число больше 0');
					}
					this.sendCMD('give icmoney '+ server +' '+ lk.username +' '+ money, function() {
						lk.giveMoneyIC(server, money);
					});
				},
				
				removeStatus: function(server) {
					this.sendCMD('delete group '+ server +' '+ lk.username, function() {
						lk.updateServer(server, 0, 0);
					});
				},
				
				ban: function(server, days, reason) {
					if ( !(days*days) || days <= 0 ) {
						return this.showMessage('<b>Ошибка:</b> укажите кол-во дней числом больше 0');
					}
					if ( reason.length > 50 || reason.length < 3 ) {
						return this.showMessage('<b>Ошибка:</b> причина должна быть от 3 до 50 символов');
					}
					this.sendCMD('set ban '+ server +' '+ lk.username +' '+ reason.replace(' ', '_') +' '+ days);
				},
				
				sendCMD: function(cmd, success) {
					this.showMessage('Пожалуйста, подождите. Выполняется команда...');
					
					var _this = this;
					lk.requestAdminCMD.send_post({
						cmd: cmd
					}, function( json ) {
						if ( json.status == 'success' ) {
							_this.showMessage(json.message);
							if ( success != null ) {
								success();
							}
						} else {
							_this.showMessage('<b>Ошибка:</b> ' + json.message);
						}
					});
				},
				
				showMessage: function(message) {
					var output = document.getElementById('lk-adminConsole-output');
					output.style.display = 'block';
					output.innerHTML = message;
				}
				
			}
		</script>
	<?php } ?>
</div>