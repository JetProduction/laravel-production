<?php
/*

		
		ИНФОРМАЦИЯ:
			Личный Кабинет v1.4.5 UTF-8
			Автор: Fleynaro(faicraft)
			Сайт http://fleynaro.ru/
			Группа ВК: http://vk.com/fleynaro_prods
		
		ОГРАНИЧЕНИЯ:
			Запрещено использовать весь код или его части в сторонних скриптах без разрешения автора!
			Запрещено любым способом распространять данный PHP файл на других ресурсах, кроме Вашего проекта!
		
		ВНИМАНИЕ!
			Если данный файл не является конфигурационным, то при отсутствии у Вас знаний и навыков
			программирования на языке PHP любое Ваше здесь изменение может привести к нестабильной работе Личного Кабинета.
			Редактируйте данный код только при указании тех. поддержки, оказываемой автором данного скрипта!
		
		НОВАЯ ВЕРСИЯ
			Данная версия v1.4.5 очень многое что поменяла и добавила в Личном кабинете. Рекомендуется устанавливать новую версию ЛК с нуля.
		
		По любым вопросам обращайтесь к автору данного кода http://vk.com/fleynaro или в группу ВК http://vk.com/fleynaro_prods
		
	
*/

	session_start();
	
	define ( 'ROOT_LK_DIR', dirname ( __FILE__ ) );
	
	include(ROOT_LK_DIR . '/config.php');
	include(ROOT_LK_DIR . '/class/language.class.php');
	include(ROOT_LK_DIR . '/class/_user.class.php');
	include(ROOT_LK_DIR . '/class/lk.class.php');
	
	$lk = new lk( $config_lk );
	
	$json_permission = '{
		"status": "error",
		"message": "'. $lk->lang->error('dontHaveRight') .'"
	}';
	
	$json = '{
		"status": "error",
		"message": "'. $lk->lang->error('error') .'"
	}';
	
	if ( !$lk->isKeyCSRF( $lk->getReq('key') ) ) {
		die( '{
			"status": "error",
			"message": "'. $lk->lang->error('incorrectKey') .'"
		}' );
	}
	
	if ( $lk->cfg['anti_ddos'] != false && $lk->getReq('type') != 'showSkins' )
	{
		$time = time();
		$time_ = isset($_SESSION['lk_antiddos']) ? $_SESSION['lk_antiddos'] : 0;
		
		if ( $time - $time_ < $lk->cfg['anti_ddos'] ) {
			die('{
				"status": "error",
				"message": "'. $lk->lang->system('pleaseWait', array($lk->cfg['anti_ddos'] - ($time - $time_) + 1)) .'"
			}');
		} else
			$_SESSION['lk_antiddos'] = $time;
	}
	
	switch ( $lk->getReq('type') )
	{
		case 'upload': {
			if ( is_uploaded_file($_FILES['file']['tmp_name']) )
			{
				$size = getimagesize($_FILES['file']['tmp_name']);
				
				if ( $_FILES['file']['size'] <= $lk->cfg['skin']['max_size_mb'] * 1024 * 1024 && $_FILES['file']['type'] == 'image/png' ) 
				{
					$path_server = '';
					$right = false;
					$serverid = (int)$lk->getReq('serverid');
					
					if ( $lk->cfg['skin']['multi_enable'] ) {
						if ( !$lk->isNumIn($serverid, 0, count($lk->cfg['server'])) ) {
							die($json);
						}
					
						$path_server = 'server_'. $serverid .'/';
						if ( $lk->cfg['skin']['status'] ) $right = $lk->cfg['status'][$lk->user['status'][$serverid][0]]['right'];
					}
						
					if ( $lk->getReq('type_upload') == 1 )
					{	
						$path = ROOT_LK_DIR . $lk->cfg['skin']['path_to_skin'] . $path_server . $lk->user['name'] . '.png';
						
						if ( $size[0] == 64 && ($size[1] == 32 || $size[1] == 64) )
						{
							if ( !$lk->isHaveRight( 'upload_skin' ) || ($right != false && !$right['upload_skin']) ) die( $json_permission );
							
							if ( move_uploaded_file($_FILES['file']['tmp_name'], $path) )
							{
								$json = '{
									"status": "success",
									"message": "'. $lk->lang->success('loadedSkin') .'"
								}';
								$lk->logWrite($lk->lang->log('loadSkin'));
								
								if ( $lk->cfg['skin']['cache'] ) {
									$lk->deleteCacheSkins( $lk->user['name'], $path_server );
								}
							} else
								$json = '{
									"status": "error",
									"message": "'. $lk->lang->error('loadedSkin') .'"
								}';
						} else if ( !($size[0] % 256) && !($size[1] % 128) /*$size[0] == 256 && $size[1]== 128 || $size[0] == 1024 && $size[1] == 512*/)
						{
							if ( !$lk->isHaveRight( 'upload_hd_skin' ) || ($right != false && !$right['upload_hd_skin']) ) die( $json_permission );
							
							if ( move_uploaded_file($_FILES['file']['tmp_name'], $path) )
							{
								$json = '{
									"status": "success",
									"message": "'. $lk->lang->success('loadedSkinHD') .'"
								}';
								$lk->logWrite($lk->lang->log('loadSkinHD'));
								
								if ( $lk->cfg['skin']['cache'] ) {
									$lk->deleteCacheSkins( $lk->user['name'], $path_server );
								}
							}
						} else
							$json = '{
								"status": "error",
								"message": "'. $lk->lang->error('incorrectSkinSize') .'"
							}';
					} else {
						$path = ROOT_LK_DIR . $lk->cfg['skin']['path_to_cloak'] . $path_server . $lk->user['name'] . '.png';
						
						if ( $size[0] == 64 && $size[1] == 32 || $size[0] == 22 && $size[1] == 17) 
						{
							if ( !$lk->isHaveRight( 'upload_cloak' ) || ($right != false && !$right['upload_cloak']) ) die( $json_permission );
							
							if ( move_uploaded_file($_FILES['file']['tmp_name'], $path) )
							{
								$json = '{
									"status": "success",
									"message": "'. $lk->lang->success('loadedCloak') .'"
								}';
								$lk->logWrite($lk->lang->log('loadCloak'));
								
								if ( $lk->cfg['skin']['cache'] ) {
									$lk->deleteCacheSkins( $lk->user['name'], $path_server );
								}
							}
						} else if ( !($size[0] % 256) && !($size[1] % 128) /*$size[0] == 512 && $size[1] == 256 || $size[0] == 1024 && $size[1] == 512*/)
						{
							if ( !$lk->isHaveRight( 'upload_hd_cloak' ) || ($right != false && !$right['upload_hd_cloak']) ) die( $json_permission );
							
							if ( move_uploaded_file($_FILES['file']['tmp_name'], $path) )
							{
								$json = '{
									"status": "success",
									"message": "'. $lk->lang->success('loadedCloakHD') .'"
								}';
								$lk->logWrite($lk->lang->log('loadCloakHD'));
								
								if ( $lk->cfg['skin']['cache'] ) {
									$lk->deleteCacheSkins( $lk->user['name'], $path_server );
								}
							}
						} else
							$json = '{
								"status": "error",
								"message": "'. $lk->lang->error('incorrectCloakSize') .'"
							}';
					}
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('incorrectFormat') .'"
					}';
			}
			break;
		}
		
		case 'delete': {
		
			$path_server = '';
			$serverid = (int)$lk->getReq('serverid');
					
			if ( $lk->cfg['skin']['multi_enable'] && $lk->isNumIn($serverid, 0, count($lk->cfg['server'])) ) {
				$path_server = 'server_'. $serverid .'/';
			}
						
			$path = ROOT_LK_DIR . $lk->cfg['skin']['path_to_' . ($lk->getReq('type_delete') == '1' ? 'skin' : 'cloak')] . $path_server . $lk->user['name'] . '.png';
			
			if ( file_exists($path) ) {
				unlink($path);
				
				if ( $lk->cfg['skin']['cache'] ) {
					$lk->deleteCacheSkins( $lk->user['name'], $path_server );
				}
				
				$json = '{
					"status": "success"
				}';
				$lk->logWrite($lk->lang->log('removeSkin'));
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('hasNotFile') .'"
				}';
			
			break;
		}

		
		case 'buy_status': {
			
			$serverid = (int)$lk->getReq('serverid');
			$statusid = (int)$lk->getReq('statusid');
			$time_day = (int)$lk->getReq('time_day');
			
			if ( is_numeric($serverid) && is_numeric($statusid) && is_numeric($time_day) )
			{
				$status_info = $lk->cfg['status'][$statusid];
				
				if ( $lk->cfg['server'][$serverid]['enable'] && $status_info['enable'] && $lk->cfg['status'][$lk->user['status'][$serverid][0]]['right']['buy_status'] && $lk->cfg['server'][$serverid]['right']['buy_status'] )
				{
					if ( $lk->cfg['server'][$serverid]['status'][$statusid] ) {
						if ( $status_info['set_days'] && $time_day > 0 ) {
							$price = round($lk->getPriceStatus($statusid) / $status_info['buy_days'] * $time_day);
						} else {
							$time_day = $status_info['buy_days'];
							$price = $lk->getPriceStatus($statusid);
						}
						
						if ( $lk->hasMoney($price) )
						{
							$_db = $lk->getServerDB($serverid);
							
							$lk->setStatus($_db, $serverid, $lk->user['name'], $statusid);
							$lk->setStatusUser($lk->user['id'], $serverid, $statusid, time() + $time_day * 86400, $lk->user['prefix'][$serverid], $lk->user['unban_count'][$serverid]);
							$lk->give_money(-$price);
							
							$entity_info = $lk->cfg['server'][$serverid]['entity'];
							if ( $entity_info != false ) {
								$entity = $lk->getEntity( $_db, $serverid, $lk->user['name'] );
								
								if ( !isset( $entity['id'] ) ) {
									$lk->setEntity( $_db, $serverid, $lk->user['name'], $entity_info['type'], $entity_info['default'] );
								}
							}
							
							$json = '{
								"status": "success"
							}';
							
							$lk->logWrite($lk->lang->log('buyStatus', array($status_info['name'], $time_day)));
						}
					} else
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('notBuyStatus') .'"
						}';
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('serverOff') .'"
					}';
			}
			
			break;
		}
		
		case 'extend_status': {
			
			$serverid = (int)$lk->getReq('serverid');
			$time_day = (int)$lk->getReq('time_day');
			
			if ( is_numeric($serverid) && is_numeric($time_day) && $lk->cfg['server'][$serverid]['enable'] && $lk->cfg['server'][$serverid]['right']['extend_status'] )
			{
				$status = $lk->user['status'][$serverid];
				
				if ( $status[0] > 0 )
				{
					$status_info = $lk->cfg['status'][$status[0]];
					
					if ( $status_info['set_days'] && $time_day > 0 ) {
						$price = round($lk->getPriceStatus($status[0]) / $status_info['buy_days'] * $time_day);
					} else {
						$time_day = $status_info['buy_days'];
						$price = $lk->getPriceStatus($status[0]);
					}
					
					if ( $lk->hasMoney($price) )
					{
						$lk->setStatusUser($lk->user['id'], $serverid, $status[0], $status[1] + $time_day * 86400, $lk->user['prefix'][$serverid], $lk->user['unban_count'][$serverid]);
						$lk->give_money(-$price);
						
						$json = '{
							"status": "success"
						}';
						
						$lk->logWrite($lk->lang->log('extendStatus', array($status_info['name'], $time_day)));
					} else
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('money') .'"
						}';
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('statusHasnotBought') .'"
					}';
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('notExtendStatus') .'"
				}';
			
			break;
		}
		
		case 'set_prefix': {
			$serverid = (int)$lk->getReq('serverid');
			$color_prefix = (int)$lk->getReq('color_prefix');
			$color_nickname = (int)$lk->getReq('color_nickname');
			$color_message = (int)$lk->getReq('color_message');
			$name_prefix = $lk->getReq('name_prefix');
			
			if ( !$lk->cfg['server'][$serverid]['enable']
				|| !$lk->isNumIn($color_prefix, 0, 15)
				|| !$lk->isNumIn($color_nickname, 0, 15)
				|| !$lk->isNumIn($color_message, 0, 15)
				|| !preg_match('/^[a-z0-9]{'. $lk->cfg['prefix']['prefix_min_len'] .','. $lk->cfg['prefix']['prefix_max_len'] .'}$/si', $name_prefix)
				|| preg_match('/('. $lk->cfg['prefix']['prefix_ban'] .')/si', $name_prefix)
				|| (!$lk->cfg['status'][$lk->user['status'][$serverid][0]]['right']['set_prefix'] && !$lk->user['right']['set_prefix'])
				|| !$lk->cfg['server'][$serverid]['right']['set_prefix']
			)
			{
				die('{
					"status": "error",
					"message": "'. $lk->lang->error('incorrectPrefix') .'"
				}');
			}
			
			$colors = array('f', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e');
			$format = $lk->cfg['prefix']['prefix_format'];
			$prefix = $format[0] . '&'.$colors[$color_prefix] . $name_prefix . $format[1] . '&'.$colors[$color_nickname];
			$suffix = $format[2] . '&'.$colors[$color_message] . $format[3];
			
			$lk->setPrefix( $lk->getServerDB($serverid), $serverid, $lk->user['name'], $prefix, $suffix );
			$lk->setStatusUser($lk->user['id'], $serverid, $lk->user['status'][$serverid][0], $lk->user['status'][$serverid][1], Array($color_prefix, $name_prefix, $color_nickname, $color_message), $lk->user['unban_count'][$serverid]);
			
			$json = '{
				"status": "success"
			}';
			$lk->logWrite($lk->lang->log('setPrefix', array($prefix, $suffix)));
			
			break;
		}
		
		case 'exchange_iconomy': {
			if ( !$lk->cfg['exchange']['iconomy']['enable'] ) die( $json );
			
			$serverid = $lk->cfg['exchange']['iconomy']['ic_money_viem'] ? $lk->cfg['exchange']['iconomy']['default_server'] : (int)$lk->getReq('serverid');
			$value = (int)$lk->getReq('value');
			
			if ( !$lk->cfg['server'][$serverid]['enable'] || !$lk->isNumIn($value, 1, 10000000) || !$lk->cfg['server'][$serverid]['right']['exchange'] || $lk->user['icmoney'][$serverid] == false ) {
				die( '{
					"status": "error",
					"message": "'. $lk->lang->error('incorrectExchange') .'"
				}' );
			}
			
			if ( $lk->getReq('type_exchange') == '0' )
			{
				$price = ceil($value * $lk->cfg['exchange']['iconomy']['price_cur']);
				
				if ( $price > $lk->user['money'] ) {
					$price = $lk->user['money'];
				}
				$value = floor($price / $lk->cfg['exchange']['iconomy']['price_cur']);
				if ( $value > 0 ) {
					$lk->give_money_ic( !$lk->cfg['exchange']['iconomy']['ic_money_viem'] ? $lk->dbs[$serverid] : $lk->db, $serverid, $lk->user['name'], $value );
					$lk->give_money(-$price);
					$lk->logWrite($lk->lang->log('exchange0', array($price.$lk->cfg['cur'][1], $value)));
				} else
					die('{
						"status": "error",
						"message": "'. $lk->lang->error('money') .'"
					}');
					
				$money = -$price;
				$icmoney = $value;
			} else
			{
				$price = ceil($value * $lk->cfg['exchange']['iconomy']['price_u_cur']);
				$_db = !$lk->cfg['exchange']['iconomy']['ic_money_viem'] ? $lk->dbs[$serverid] : $lk->db;
				$moneyIC = $lk->get_money_ic( $_db, $serverid, $lk->user['name'] );
				
				if ( $price > $moneyIC ) {
					$price = $moneyIC;
				}
				$value = floor($price / $lk->cfg['exchange']['iconomy']['price_u_cur']);
				if ( $value > 0 )
				{
					$lk->give_money_ic( $_db, $serverid, $lk->user['name'], -$price );
					$lk->give_money($value);
					$lk->logWrite($lk->lang->log('exchange1', array($price, $value.$lk->cfg['cur'][1])));
				} else
					die('{
						"status": "error",
						"message": "'. $lk->lang->error('money') .'"
					}');
					
				$money = $value;
				$icmoney = -$price;
			}
			
			$json = '{
				"status": "success",
				"money": '.$money.',
				"icmoney": '.$icmoney .'
			}';
			
			break;
		}
		
		case 'warp': {
			if ( !$lk->cfg['other']['warp']['enable'] ) die( $json );
			
			$cfg_warp = $lk->cfg['other']['warp'];
			$serverid = !$cfg_warp['list_warps'] ? (int)$lk->getReq('serverid') : $cfg_warp['default_server'];
			$public = (int)$lk->getReq('pub');
			$pos_x = (float)$lk->getReq('x');
			$pos_y = (float)$lk->getReq('y');
			$pos_z = (float)$lk->getReq('z');
			$id = (int)$lk->getReq('id');
			$name = $lk->getReq('name');
			$message = $lk->getReq('msg');
			
			if ( !$lk->cfg['server'][$serverid]['enable']
				|| !$lk->isNumIn($pos_x, -100000, 100000)
				|| !$lk->isNumIn($pos_y, -100000, 100000)
				|| !$lk->isNumIn($pos_z, -100000, 100000)
				|| !is_numeric($id)
				|| !preg_match('/^[a-z 0-9]{'. $cfg_warp['len_name'][0] .','. $cfg_warp['len_name'][1] .'}$/i', $name)
				|| !preg_match('/^[a-z 0-9 \% \_ \. \! \? \- \* \= \+]{'. $cfg_warp['len_message'][0] .','. $cfg_warp['len_message'][1] .'}$/i', $message)
				|| (!$lk->cfg['status'][$lk->user['status'][$serverid][0]]['right']['create_warp'] && !$lk->user['right']['create_warp'])
				|| !$lk->cfg['server'][$serverid]['right']['warp']
			)
			{
				die('{
					"status": "error",
					"message": "'. $lk->lang->error('incorrectWarp') .'"
				}');
			}
			
			$_db = !$cfg_warp['list_warps'] ? $lk->dbs[$serverid] : $lk->db;
			$count_of_warps = $lk->getCountWarps( $_db, $serverid, $lk->user['name'] );
			
			if ( $count_of_warps < $cfg_warp['max_warps'] )
			{
				if ( $lk->getCountWarpsByName( $_db, $serverid, $name, $id ) == 0 )
				{
					if ( $id == -1 && $cfg_warp['paid']['enable'] && $count_of_warps >= $cfg_warp['paid']['free_count'] ) {
						if ( $lk->hasMoney($cfg_warp['paid']['price']) ) {
							if ( $lk->setWarp( $_db, $serverid, $lk->user['name'], Array($name, $id, $cfg_warp['world'], $pos_x, $pos_y, $pos_z, $public, $message) ) ) {
								$lk->give_money(-$cfg_warp['paid']['price']);
								$json = '{
									"status": "success",
									"price": '.$cfg_warp['paid']['price'].'
								}';
								$lk->logWrite($lk->lang->log('createWarpPay', array($name,  $cfg_warp['paid']['price'].$lk->cfg['cur'][1])));
							} else
								$json = '{
									"status": "error",
									"message": "'. $lk->lang->error('warpHasnotCreated') .'"
								}';
						} else {
							die('{
								"status": "error",
								"message": "'. $lk->lang->error('money') .'"
							}');
						}
					} else {
						if ( $lk->setWarp( $_db, $serverid, $lk->user['name'], Array($name, $id, $cfg_warp['world'], $pos_x, $pos_y, $pos_z, $public, $message) ) ) {
							$json = '{
								"status": "success",
								"price": 0
							}';
							$lk->logWrite($lk->lang->log('editWarp', array($name)));
						} else
							$json = '{
								"status": "error",
								"message": "'. $lk->lang->error('warpHasnotEdited') .'"
							}';
					}
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('occupiedName') .'"
					}';
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('maxWarp', array($cfg_warp['max_warps'])) .'"
				}';
			
			break;
		}
		
		case 'deletewarp': {
			if ( !$lk->cfg['other']['warp']['enable'] ) die( $json );
			
			$serverid = !$lk->cfg['other']['warp']['list_warps'] ? (int)$lk->getReq('serverid') : $lk->cfg['other']['warp']['default_server'];
			$id = (int)$lk->getReq('id');
			
			if ( $lk->cfg['server'][$serverid]['enable'] && is_numeric($id) ) {
				
				$_db = !$lk->cfg['other']['warp']['list_warps'] ? $lk->dbs[$serverid] : $lk->db;
				
				if ( $lk->isWarpId( $_db, $serverid, $id, " AND `creator` = '". $lk->user['name'] . "'" ) )
				{
					$lk->deleteWarpById( $_db, $serverid, $id );
					$json = '{
						"status": "success"
					}';
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('notYourWarp') .'"
					}';
				
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('serverOff') .'"
				}';
			
			break;
		}
		
		case 'unban': {
			if ( !$lk->cfg['other']['unban']['enable'] ) die( $json );
			$cfg_unban = $lk->cfg['other']['unban'];
			
			$serverid = !$cfg_unban['unban_all'] ? (int)$lk->getReq('serverid') : $cfg_unban['default_server'];
			
			if ( $lk->cfg['server'][$serverid]['enable'] && $lk->cfg['server'][$serverid]['right']['unban'] )
			{
				$price = $lk->getUnbanPrice($serverid);
				
				if ( $lk->hasMoney($price) )
				{
					if ( $cfg_unban['unban_all'] ) {
						$ban = $lk->user['ban'][0];
						$_db = $lk->db;
					} else {
						$ban = $lk->user['ban'][$serverid];
						$_db = $lk->dbs[$serverid];
					}
					
					if ( $ban !== false )
					{
						$lk->unban( $_db, $serverid, $lk->user['name'] );
						$lk->give_money(-$price);
						$lk->setStatusUser($lk->user['id'], $serverid, $lk->user['status'][$serverid][0], $lk->user['status'][$serverid][1], $lk->user['prefix'][$serverid], ++ $lk->user['unban_count'][$serverid]);
						
						$json = '{
							"status": "success",
							"money": '.$price.'
						}';
						$lk->logWrite($lk->logWrite($lk->lang->log('unban', array($price . $lk->cfg['cur'][1]))));
					} else
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('hasnotBan') .'"
						}';
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('money') .'"
					}';
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('serverOff') .'"
				}';
			
			break;
		}
		
		case 'vaucher': {
			$vaucher_cfg = $lk->cfg['other']['vaucher'];
			if ( !$vaucher_cfg['enable'] ) die( $json );
			
			$name = $lk->getReq('name');
			
			if ( preg_match('/^\w{'. $vaucher_cfg['len'][0] .','. $vaucher_cfg['len'][1] .'}$/si', $name) ) {
				
				$vaucher = $lk->getVaucher( $name );
				
				if ( $vaucher['id'] ) {
					$lk->deleteVaucher( $vaucher['id'] );
					
					if ( empty($vaucher['eval']) ) $vaucher['eval'] = $vaucher_cfg['eval'];
					
					$evals = explode('/', $vaucher['eval']);
					for ( $i = 0, $Max = count($evals); $i < $Max; $i ++ ) {
						eval( '$lk->' . $evals[$i] . ';' );
					}
					
					$json = '{
						"status": "success",
						"message": "'. $vaucher['message'] .'"
					}';
					$lk->logWrite($lk->logWrite($lk->lang->log('vaucher', array($name, $vaucher['id'], $vaucher['eval']))));
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('notVaucher') .'"
					}';
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('incorrectVaucher') .'"
				}';
			
			break;
		}
		
		case 'buyright': {
			if ( !$lk->cfg['rights']['enable'] ) die( $json );
			
			$right_id = (int)$lk->getReq('right_id');
			
			if ( $lk->isNumIn($right_id, 0, count($lk->cfg['rights']['right']) - 1) ) {
				
				$right = $lk->cfg['rights']['right'][$right_id];
				
				if ( $right[4] && $lk->hasMoney($right[3]) ) {
					$lk->setRight( $lk->user['id'], $right[2], true );
					$lk->give_money(-$right[3]);
					
					$json = '{
						"status": "success",
						"money": '. $right[3] .'
					}';
					$lk->logWrite($lk->lang->log('buyRight', array($right[0], $right_id, $right[3])));
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('money') .'"
					}';
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('notRight') .'"
				}';
			
			break;
		}
		
		case 'pexright': {
			if ( !$lk->cfg['right_pex']['enable'] ) die( $json );
			
			$serverid = !$lk->cfg['right_pex']['all_server'] ? (int)$lk->getReq('serverid') : $lk->cfg['right_pex']['default_server'];
			$right_id = (int)$lk->getReq('right_id');
			
			if ( $lk->cfg['server'][$serverid]['enable'] && $lk->cfg['server'][$serverid]['right']['buy_right'] )
			{
				if ( $lk->isNumIn($right_id, 0, count($lk->cfg['right_pex']['rights']) - 1) ) {
					
					$right = $lk->cfg['right_pex']['rights'][$right_id];
					
					if ( ($right[0] == $serverid || $right[0] == -1) && $lk->hasMoney($right[4]) ) {
						$_db = !$lk->cfg['right_pex']['all_server'] ? $lk->dbs[$serverid] : $lk->db;
						
						if ( !$lk->hasPexRight( $_db, $serverid, $right_id ) ) {
							$lk->givePexRight( $_db, $serverid, $right_id );
							$lk->give_money(-$right[4]);
							
							$json = '{
								"status": "success",
								"name": "'. $right[2] .'",
								"money": '. $right[4] .'
							}';
							$lk->logWrite($lk->lang->log('buyPexRight', array($right[2], $right_id, $right[4], $right[5])));
						} else
							$json = '{
								"status": "error",
								"message": "'. $lk->lang->error('hasPexRight') .'"
							}';
					} else
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('money') .'"
						}';
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('notRight') .'"
					}';
			} else
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('serverOff') .'"
				}';
			
			break;
		}
		
		case 'showSkins': {
			
			$page = $lk->getReq('page');
			$skins = scandir(ROOT_LK_DIR . '/' . $lk->cfg['skin']['catalog']);
			
			if ( $page < ceil(count($skins) / $lk->cfg['skin']['max_skins']) ) {
				$json = '{
					"status": "success",
					"skins":[';
				for ( $i = $page * $lk->cfg['skin']['max_skins'] + 2, $Max = count($skins), $Max2 = $i + $lk->cfg['skin']['max_skins']; $i < $Max && $i < $Max2; $i ++ ) {
					$json .= '
						{
							"name": "'.$skins[$i].'",
							"time": '.filemtime(ROOT_LK_DIR . '/' . $lk->cfg['skin']['catalog'] . '/' . $skins[$i]).'
						},
					';
				}
				
				$json .= '
						{"skin": "", "time": ""}
					]
				}';
			} else {
				$json = '{
					"status": "error"
				}';
			}
			
			break;
		}
		
		case 'setSkin': {
			
			$skin_name = $lk->getReq('name');
			$serverid = (int)$lk->getReq('serverid');
			$path_skin = ROOT_LK_DIR . '/' . $lk->cfg['skin']['catalog'] . '/' . $skin_name . '.png';
			
			if ( preg_match('/^[a-z0-9\_\-]{0,30}$/si', $skin_name) && file_exists($path_skin) ) {
				
				$path_server = '';
				$right = false;
				
				if ( $lk->cfg['skin']['multi_enable'] ) {
					if ( !$lk->isNumIn($serverid, 0, count($lk->cfg['server'])) ) {
						die($json);
					}
					
					$path_server = 'server_'. $serverid .'/';
					if ( $lk->cfg['skin']['status'] ) $right = $lk->cfg['status'][$lk->user['status'][$serverid][0]]['right'];
				}
				
				$path = ROOT_LK_DIR . $lk->cfg['skin']['path_to_skin'] . $path_server . $lk->user['name'] . '.png';
				$size = getimagesize($path_skin);
				
				if ( $size[2] == 3 ) {
					if ( $size[0] == 64 && ($size[1] == 32 || $size[1] == 64) ) 
					{
						if ( !$lk->isHaveRight( 'upload_skin' ) || ($right != false && !$right['upload_skin']) ) die( $json_permission );
						
						if ( copy($path_skin, $path) )
						{
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('loadedSkin') .'"
							}';
							$lk->logWrite($lk->lang->log('loadSkin'));
							
							if ( $lk->cfg['skin']['cache'] ) {
								$lk->deleteCacheSkins( $lk->user['name'], $path_server );
							}
						} else
							$json = '{
								"status": "error",
								"message": "'. $lk->lang->error('loadedSkin') .'"
							}';
					} else if ( !($size[0] % 256) && !($size[1] % 128) )
					{
						if ( !$lk->isHaveRight( 'upload_hd_skin' ) || ($right != false && !$right['upload_hd_skin']) ) die( $json_permission );
						
						if ( copy($path_skin, $path) )
						{
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('loadedSkinHD') .'"
							}';
							$lk->logWrite($lk->lang->log('loadSkinHD'));
							
							if ( $lk->cfg['skin']['cache'] ) {
								$lk->deleteCacheSkins( $lk->user['name'], $path_server );
							}
						}
					} else
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('incorrectSkinSize') .'"
						}';
				} else
					$json = '{
						"status": "error",
						"message": "'. $lk->lang->error('incorrectFormat') .'"
					}';
			}
			
			break;
		}
	}
	
	echo $json;
?>