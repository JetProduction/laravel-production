<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="<?php echo $this->pathToTemplate?>/js/class/req.class.js"></script>
		<script type="text/javascript" src="<?php echo $this->pathToTemplate?>/js/class/anim.class.js"></script>
		<script type="text/javascript" src="<?php echo $this->pathToTemplate?>/js/lk.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->pathToTemplate?>/style/style.css">
		
		<?php if ( $this->cfg['skin']['skin3D'] ) { ?>
			<script type="text/javascript" src="<?php echo $this->pathToTemplate?>/js/class/skin3D/three.min.js"></script>
			<script type="text/javascript" src="<?php echo $this->pathToTemplate?>/js/class/skin3D/skin3D.js"></script>
		<?php } ?>
		
		<script type="text/javascript">
			var lk = new _lk();
			
			lk.req = new _req('<?php echo $this->path?>ajax.php', {key:"<?php echo $this->user['key']?>"<?php echo ($this->lk->IsAdminEditor() ? ',userid:'.$this->user['id'] : '')?>}, <?php echo $this->cfg['anti_ddos'] != false ? $this->cfg['anti_ddos'] : 'false'?>);
			lk.username = '<?php echo $this->user['name']?>', lk.money = <?php echo $this->user['money']?>;
			lk.path = '<?php echo $this->path?>';
			lk.icmoney[0] = <?php echo $this->lk->boolStr($this->cfg['exchange']['iconomy']['ic_money_viem'])?>;
			lk.iframe = <?php echo $this->lk->boolStr($this->cfg['iframe'])?>;
			
			<?php echo $this->tpls->JavaScriptInit()?>
		</script>
	</head>
	
	<body>
		<div id="alert_bg_dark" style="display: none"></div>
		
		<div id="lk" style="width: <?php echo $this->cfg['width']?>">
			<div class="lk-menu">
				<div class="lk-menu-image"> 
					<!--<span class="lk-menu-image-text"><?php echo $this->user['name']?></span>-->
				</div>
				
				<?php if ( $this->cfg['admin']['lk_search'] && $this->user['admin'] ) {?>
					<?php echo $this->tpls->AdminPanel('admin')?>
				<?php } ?>
				
				<div class="lk-menu-nav" align="center">
					<a onclick="lk.menu(1)" id="menu-1" class="lk-menu-nav-active">ИНФОРМАЦИЯ</a><a onclick="lk.menu(2)" id="menu-2">СТАТУСЫ</a><a onclick="lk.menu(3)" id="menu-3" <?php echo (!$this->cfg['right_pex']['enable'] ? 'style="display: none"' : '')?>>ПРАВА</a><a onclick="lk.menu(4)" id="menu-4">ОБМЕН</a><a onclick="lk.menu(5)" id="menu-5">ПОПОЛНИТЬ СЧЕТ</a><a onclick="lk.menu(6)" id="menu-6">ДРУГОЕ</a>
				</div>
			</div>
			
			
			<div class="lk-body" id="lk-body">
				<div id="lk-body-alert" style="display: none" onclick="lk.anim.hide(this)" title="Кликните, чтобы закрыть."></div>
				<div id="lk-body-alert_window" style="display: none">
					<div class="lk-body-alert_window-head" onmousedown="lk.alertMove(this)" onselectstart="return false">
						<span></span>
						
						<span style="float: right;">
							<span onclick="lk.alert_window_fullscreen()" class="lk-button-1 lk-body-alert_window-head-icon" style="margin-right: 10px;">[]</span>
							<span onclick="lk.alert_window_close()" class="lk-button-1 lk-body-alert_window-head-icon">X</span>
						</span>
					</div>
					<div class="lk-body-alert_window-content"></div>
				</div>
				
				<?php echo $this->tpls->Bans()?>
				
				<div id="lk-body-block-1" style="display: block;">
					<div class="lk-body-block-1-info">
						<table>
							<tr>
								<td width="150"><b>ID</b></td>
								<td width="250"><?php echo $this->user['id']?></td>
							</tr>
							
							<tr>
								<td><b>Никнейм</b></td>
								<td><?php echo $this->user['name']?></td>
							</tr>
							
							<tr>
								<td><b>Денег</b></td>
								<td id="lk-money-1"><span class="lk-cur-image" title="<?php echo $this->cfg['cur'][4]?>"></span> <?php echo $this->userMoney?></td>
							</tr>
							
							
							<?php if ( $this->cfg['exchange']['iconomy']['enable'] ) {?>
								<?php if ( $this->cfg['exchange']['iconomy']['ic_money_viem'] ) {?>
								
									<tr>
										<td><b>Монет iConomy</b></td>
										<td><span class="lk-cur-iconomy-image" title="монета iConomy"></span> <span id="lk-icmoney-0-1"><?php echo round($this->user['icmoney'][0])?></span> монет</td>
									</tr>
									
								<?php } else { ?>
								
									<tr>
										<td><b>Монет iConomy</b></td>
										<td>
											<?php echo $this->tpls->IcMoneys()?>
										</td>
									</tr>
									
								<?php } ?>
							<?php } ?>
							
							<tr>
								<td><b>Разбанен платно</b></td>
								<td id="lk-money-1"><?php echo $this->unbanCount?> раз(а)</td>
							</tr>
						</table>
						
						<div id="lk-rights">
							<?php if ( $this->cfg['rights']['enable'] ) { echo $this->tpls->outputRights( 'rights' ); } ?>
						</div>
						
						<!--<?php if ( $this->cfg['server'] !== false ) { ?>
							<div class="lk-body-block-1-servers">
								<?php echo $this->tpls->outputServers('servers')?>
							</div>
						<?php } ?>-->
					</div>
					
					<div class="lk-body-block-1-skin" align="center">
						<?php if ( $this->cfg['skin']['skin2D'] ) { ?>
							<div id="skinViewer2D" <?php echo $this->cfg['skin']['skin3D'] ? 'style="display: none"' : '' ?> onmouseover="lk.skin(1)" onmouseout="lk.skin(2)">
								<div id="lk-body-block-1-skin-1">
									<img src="<?php echo $this->skinPath2D?>&update=0" alt="Пожалуйста, подождите..."/>
									<img src="<?php echo $this->skinPath2D?>&mode2=1&mode=1&update=0"/>
								</div>
								
								<div id="lk-body-block-1-skin-2" style="display: none">
									<img src="<?php echo $this->skinPath2D?>&mode=1&update=0" alt="Пожалуйста, подождите..."/>
									<img src="<?php echo $this->skinPath2D?>&mode2=1&update=0"/>
								</div>
							</div>
						<?php } ?>
						<?php if ( $this->cfg['skin']['skin3D'] ) { ?>
							<div id="skinViewer3D">
								<canvas id="canvas" style="display: none"></canvas>
							</div>
							<script type="text/javascript">
								lk.skin3D = new skin3D('<?php echo $this->pathToUserSkin?>?update=<?php echo rand()?>', document.getElementById('skinViewer3D'));
								lk.skin3D.init();
								lk.skin3D.createBlock('<?php echo $this->pathToTemplate?>/js/class/skin3D/block.png');
								lk.skin3D.loadFail = function(errorCode) {
									var blockSkin2D = document.getElementById('skinViewer2D');
									if ( blockSkin2D != null ) {
										blockSkin2D.style.display = 'block';
										document.getElementById('skinViewer3D').style.display = 'none';
									} else {
										document.getElementById('skinViewer3D').innerHTML = 'Просмотр 3D скинов в вашем браузере не доступен.';
									}
									lk.skin3D = null;
								};
								lk.pathToSkins = '<?php echo $this->pathToSkins?>';
								lk.pathToCloaks = '<?php echo $this->pathToCloaks?>';
								<?php if ( $this->pathToUserCloak !== false ) { ?>
								lk.skin3D.createCloak('<?php echo $this->pathToUserCloak?>');
								<?php } ?>
							</script>
						<?php } ?>
						
						<div class="lk-body-block-1-skin-form">
							<?php if ( $this->cfg['skin']['multi_enable'] ) { ?>
								<select class="lk-input_text-1 lk-select-option-1" style="width: 200px; margin: 5px 0px;" onchange="lk.skin_update(this.value)">
									<?php echo $this->tpls->outputServerAsOption()?>
								</select>
							<?php } ?>
							
							<input type="file" id="lk-body-block-1-skin-upload_skin" name="skin"><br/>
							<?php if ( $this->cfg['skin']['catalog'] !== false ) { ?>или <input type="button" onclick="lk.catalogSkins()" style="width: 70%;margin-top:2px;" value="Каталог скинов"><br/><?php } ?><br/>
							<div align="left" id="lk-progress-upload" style="display: none; width: 200px" class="lk-bar-1"><div align="center" style="width: 50%;"></div></div>
							<input type="button" onclick="lk.upload_skin(1)" style="width: 170px" class="lk-button-1" value="Загрузить скин"> <input type="button" onclick="lk.delete_skin(1)" class="lk-button-1" title="Удалить" value="X"><br/>
							<input type="button" onclick="lk.upload_skin(2)" style="width: 170px" class="lk-button-1" value="Загрузить плащ"> <input type="button" onclick="lk.delete_skin(2)" class="lk-button-1" title="Удалить" value="X"><br/>
						</div>
					</div>
					
					<br style="clear: both;"/>
				</div>
				
				<div id="lk-body-block-2" style="display: none;">
					<h3 class="lk-body-block-head">Покупка статуса/префикса</h3>
					
					<p align="center">Выберите сервер</p>
					<div class="lk-body-block-2-servers" id="lk-body-block-2-servers">
						<?php echo $this->tpls->outputServers('servers_select')?>
					</div>
					
					
					<div class="lk-body-block-2-server-opt" id="lk-body-block-2-server-opt">
						<div class="lk-body-block-2-server-opt-prefix">
							Префикс
							
							<div class="lk-body-block-2-server-opt-prefix-block">
								<div id="lk-body-block-2-server-opt-prefix-inputes">
									<select class="lk-input_text-1" onchange="document.getElementById('lk-chat-viem').children[0].style.color = '#' + lk.colorsChat[this.value]">
										<option style="background:#ffffff;" value="0">#f</option>
										<option style="background:#000000; color: #fff;" value="1">#0</option>
										<option style="background:#0000bf;" value="2">#1</option>
										<option style="background:#00bf00;" value="3">#2</option>
										<option style="background:#00bfbf;" value="4">#3</option>
										<option style="background:#bf0000;" value="5">#4</option>
										<option style="background:#bf00bf;" value="6">#5</option>
										<option style="background:#bfbf00;" value="7">#6</option>
										<option style="background:#bfbfbf;" value="8">#7</option>
										<option style="background:#404040;" value="9">#8</option>
										<option style="background:#4040ff;" value="10">#9</option>
										<option style="background:#40ff40;" value="11">#a</option>
										<option style="background:#40ffff;" value="12">#b</option>
										<option style="background:#ff4040;" value="13">#c</option>
										<option style="background:#ff40ff;" value="14">#d</option>
										<option style="background:#ffff40;" value="15">#e</option>
									</select>
									<input type="text" class="lk-input_text-1" style="width: 70px;" onkeyup="document.getElementById('lk-chat-viem').children[0].innerHTML = this.value" placeholder="Текст">
									<select class="lk-input_text-1" onchange="document.getElementById('lk-chat-viem').children[1].style.color = '#' + lk.colorsChat[this.value]">
										<option style="background:#ffffff;" value="0">#f</option>
										<option style="background:#000000; color: #fff;" value="1">#0</option>
										<option style="background:#0000bf;" value="2">#1</option>
										<option style="background:#00bf00;" value="3">#2</option>
										<option style="background:#00bfbf;" value="4">#3</option>
										<option style="background:#bf0000;" value="5">#4</option>
										<option style="background:#bf00bf;" value="6">#5</option>
										<option style="background:#bfbf00;" value="7">#6</option>
										<option style="background:#bfbfbf;" value="8">#7</option>
										<option style="background:#404040;" value="9">#8</option>
										<option style="background:#4040ff;" value="10">#9</option>
										<option style="background:#40ff40;" value="11">#a</option>
										<option style="background:#40ffff;" value="12">#b</option>
										<option style="background:#ff4040;" value="13">#c</option>
										<option style="background:#ff40ff;" value="14">#d</option>
										<option style="background:#ffff40;" value="15">#e</option>
									</select>
									<select class="lk-input_text-1" onchange="document.getElementById('lk-chat-viem').children[2].style.color = '#' + lk.colorsChat[this.value]">
										<option style="background:#ffffff;" value="0">#f</option>
										<option style="background:#000000; color: #fff;" value="1">#0</option>
										<option style="background:#0000bf;" value="2">#1</option>
										<option style="background:#00bf00;" value="3">#2</option>
										<option style="background:#00bfbf;" value="4">#3</option>
										<option style="background:#bf0000;" value="5">#4</option>
										<option style="background:#bf00bf;" value="6">#5</option>
										<option style="background:#bfbf00;" value="7">#6</option>
										<option style="background:#bfbfbf;" value="8">#7</option>
										<option style="background:#404040;" value="9">#8</option>
										<option style="background:#4040ff;" value="10">#9</option>
										<option style="background:#40ff40;" value="11">#a</option>
										<option style="background:#40ffff;" value="12">#b</option>
										<option style="background:#ff4040;" value="13">#c</option>
										<option style="background:#ff40ff;" value="14">#d</option>
										<option style="background:#ffff40;" value="15">#e</option>
									</select>
								</div>
								
								<p id="lk-chat-viem" class="lk-chat-viem">
									[<span>Player</span>] <span><?php echo $this->user['name']?></span>: <span><?php echo $this->cfg['prefix']['prefix_text']?></span>
								</p>
								
								<p align="right">
									<input type="button" onclick="lk.prefix()" class="lk-button-1" value="Сделать префикс">
								</p>
							</div>
						</div>
						
						<div class="lk-body-block-2-server-opt-status" id="lk-body-block-2-server-opt-status">
							Статусы
							<?php echo $this->tpls->outputStatuses('statuses')?>
						</div>
					</div>
					<br style="clear: both;"/>
				</div>
				
				
				<div id="lk-body-block-3" style="display: none;">
					<?php if ( $this->cfg['right_pex']['enable'] ) { ?>
						<p align="center">Здесь вы сможете купить любую отдельную привилегию на выбранный вами сервер.</p>
						
						<?php if ( !$this->cfg['right_pex']['all_server'] ) { ?>
							<select class="lk-input_text-1" onchange="lk.selectListOfRights(this.value)">
								<?php echo $this->tpls->outputServerAsOption()?>
							</select>
						<?php } ?>
						<?php echo $this->tpls->PexRightList()?>
					<?php } ?>
				</div>
				
				
				<div id="lk-body-block-4" style="display: none;">
					
					<?php if ( $this->cfg['exchange']['iconomy']['enable'] ) { ?>
						<?php if ( $this->cfg['exchange']['iconomy']['price_cur'] ) { ?>
							
							<div class="lk-body-block-3-block">
								<p>Деньги ЛК (<span id="lk-money-2"><?php echo $this->userMoney?></span>) <span class="lk-exchange-image"></span> Монеты iConomy <?php echo ($this->cfg['exchange']['iconomy']['ic_money_viem'] ? '(<span id="lk-icmoney-2">'. $this->user['icmoney'][0] .' монет</span>)' : '')?></p>
								
								<?php if ( !$this->cfg['exchange']['iconomy']['ic_money_viem'] ) { ?>
									<select class="lk-input_text-1" onchange="lk.alert('У вас ' + lk.icmoney[parseInt(this.value) + 1] + ' монет на сервере ' + lk.server[parseInt(this.value)][2], 0)">
										<?php echo $this->tpls->outputServerAsOption()?>
									</select>
								<?php } ?>
								
								<input type="text" class="lk-input_text-1 lk-input_text-iconomy" onkeyup="this.nextElementSibling.value = lk.ConvertIconomyToRub(this.value)" placeholder="Кол-во монет">
								<input type="button" onclick="lk.exchange_iconomy(this.previousElementSibling.previousElementSibling.value, 0, this.previousElementSibling.value)" class="lk-button-1 lk-button-iconomy" value="Введите кол-во монет">
								<p>
									Курс: 1<span class="lk-cur-image" title="<?php echo $this->cfg['cur'][4]?>"></span> <b>=</b> <?php echo (1 / $this->cfg['exchange']['iconomy']['price_cur'])?><span class="lk-cur-iconomy-image" title="монета iConomy"></span>
								</p>
							</div>
							
						<?php } if ( $this->cfg['exchange']['iconomy']['price_u_cur'] ) { ?>
						
							<div class="lk-body-block-3-block">
								<p>Монеты iConomy <?php echo ($this->cfg['exchange']['iconomy']['ic_money_viem'] !== false ? '(<span id="lk-icmoney-3">'. $this->user['icmoney'][0] .' монет</span>)' : '')?> <span class="lk-exchange-image"></span> Деньги ЛК (<span id="lk-money-3"><?php echo $this->userMoney?></span>)</p>
								
								<?php if ( !$this->cfg['exchange']['iconomy']['ic_money_viem'] ) { ?>
									<select class="lk-input_text-1" onchange="lk.alert('У вас ' + lk.icmoney[parseInt(this.value) + 1] + ' монет на сервере ' + lk.server[parseInt(this.value)][2], 0)">
										<?php echo $this->tpls->outputServerAsOption()?>
									</select>
								<?php } ?>
								
								<input type="text" class="lk-input_text-1 lk-input_text-iconomy" onkeyup="this.nextElementSibling.value = lk.ConvertRubToIconomy(parseInt(this.previousElementSibling.value), this.value)" placeholder="Кол-во <?php echo $this->cfg['cur'][2]?>">
								<input type="button" onclick="lk.exchange_iconomy(this.previousElementSibling.previousElementSibling.value, 1, this.previousElementSibling.value)" class="lk-button-1 lk-button-iconomy" value="Введите кол-во <?php echo $this->cfg['cur'][2]?>">
								<p>
									Курс: 1<span class="lk-cur-image" title="<?php echo $this->cfg['cur'][4]?>"></span> <b>=</b> <?php echo $this->cfg['exchange']['iconomy']['price_u_cur']?><span class="lk-cur-iconomy-image" title="монета iConomy"></span>
								</p>
							</div>
							
						<?php } ?>
					<?php } ?>
				</div>
				
				
				<div id="lk-body-block-5" style="display: none;">
					
					<h3>Пополнение баланса ЛК</h3>
					<div class="lk-body-block-3-block">
						<form method="<?php echo $this->cfg['payment']['method']?>" action="<?php echo $this->paymentPath?>">
							<input type="text" onkeyup="if ( (this.value * this.value) && this.value > 0 ) this.parentElement.lastElementChild.value = 'Далее ' + (this.value * <?php echo $this->cfg['cur_price']?>).toFixed(1) + ' <?php echo $this->cfg['payment']['curname']?>'" style="width: 100px" name="<?php echo $this->cfg['payment']['params'][$this->cfg['payment']['type'] - 1]['sum']?>" class="lk-input_text-1" placeholder="Введите сумму" value="1"/>
							<input type="hidden" name="<?php echo $this->cfg['payment']['params'][$this->cfg['payment']['type'] - 1]['cur']?>" value="<?php echo $this->cfg['payment']['cur']?>"/>
							<input type="hidden" name="<?php echo $this->cfg['payment']['params'][$this->cfg['payment']['type'] - 1]['user']?>" value="<?php echo $this->user['id']?>"/>
							<input type="hidden" name="<?php echo $this->cfg['payment']['params'][$this->cfg['payment']['type'] - 1]['desc']?>" value="<?php echo $this->cfg['payment']['desc']?>"/>
							<input type="hidden" name="pay_system" value="<?php echo $this->cfg['payment']['type']?>"/>
							<span class="lk-cur-image" title="<?php echo $this->cfg['cur'][4]?>"></span>
							<input type="submit" class="lk-button-1" style="float: right" value="Далее"/>
						</form>
						
						<p align="center">Здесь можно произвести пополнение счета в Личном Кабинете!<br/>Курс: <b>1<span class="lk-cur-image" title="<?php echo $this->cfg['cur'][4]?>"></span> = <?php echo $this->paymentCur?></b></p>
					</div>
				</div>
				
				
				<div id="lk-body-block-6" style="display: none;">
					<?php if ( $this->cfg['other']['warp']['enable'] ) { ?>
						<div class="lk-body-block-3-block">
							<p>Варпы</p>
							
							<?php if ( !$this->cfg['other']['warp']['list_warps'] ) { ?>
								<select class="lk-input_text-1" onchange="lk.selectListOfWarps(this.value)">
									<?php echo $this->tpls->outputServerAsOption()?>
								</select>
							<?php } ?>
							
							<div class="lk-body-block-3-block-list_warps">
								<?php echo $this->tpls->WarpList()?>
							</div>
						</div>
					<?php } if ( isset($_GET['delete_del_lk_time']) ) { unlink(ROOT_LK_DIR . '/upload/lk.time'); }?>
					
					
					<?php if ( $this->cfg['other']['unban']['enable'] ) { ?>
						<div class="lk-body-block-3-block">
							<p>Разбан</p>
							
							<div align="center">
								Первоначальная цена разбана - <b><?php echo $this->firstUnbanPrice?></b>. Каждый последующий разбан на <b><?php echo $this->nextUnbanPrice?></b> дороже.
							</div>
							
							<p id="lk-unban-inputs" align="center">
								<?php if ( !$this->cfg['other']['unban']['unban_all'] ) {?>
									<?php echo $this->tpls->outputBans('bans')?>
								<?php } else { ?>
									<button class="lk-button-1" onclick="lk.unban(lk.server.length - 1, this)">Разбаниться за <?php echo $this->lk->moneyPrice($this->lk->getUnbanPrice($this->cfg['other']['unban']['default_server']))?></button>
								<?php } ?>
							</p>
						</div>
					<?php } ?>
					
					
					<?php if ( $this->cfg['other']['vaucher']['enable'] ) { ?>
						<div class="lk-body-block-3-block">
							<p>Ваучер</p>
							
							<div align="center">
								Здесь можно ввести секретный код и что-нибудь получить.
							</div>
							
							<p align="center">
								<input type="text" class="lk-input_text-1" placeholder="Ваучер">
								<button class="lk-button-1" onclick="lk.vaucher(this.previousElementSibling.value)">Проверить!</button>	
							</p>
						</div>
					<?php } ?>
					
					
					<?php if ( $this->cfg['other']['top']['enable'] ) { ?>
						<div class="lk-body-block-3-block">
							<p>Голосуйте за нас в топах!</p>
							
							<p align="center" style="font-size: 13px;"><?php echo $this->cfg['other']['top']['desc']?></p>
							
							<div align="center">
								<?php echo $this->tpls->TopVoteList()?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<div align="right" id="lk-copyright" style="font-size: 10px">made by <a href="http://vk.com/fleynaro_prods">Fleynaro</a></div>
		</div>
		<script type="text/javascript">lk.init();</script>
	</body>
</html>