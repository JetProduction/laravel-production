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
	
	define ( 'ROOT_LK_DIR', dirname ( __FILE__ ) . '/..' );
	
	include(ROOT_LK_DIR . '/config.php');
	include(ROOT_LK_DIR . '/class/language.class.php');
	include(ROOT_LK_DIR . '/class/_user.class.php');
	include(ROOT_LK_DIR . '/class/lk.class.php');
	
	$lk = new lk( $config_lk );
	
	$cmd_text = $lk->getReq('cmd');
	
	if ( $lk->user['admin'] == false ) {
		die('Error: you are not logged in as admin.');
	}
	
	$json = '{
		"status": "error",
		"type": 1,
		"message": "'. $lk->lang->error('error') .'"
	}';
	
	if ( preg_match('/^[a-z 0-9 \-\'\.\_\,\$\!\?\:\"\>\<\{\}]{3,100}$/si', $cmd_text) ) {
		
		$cmd = explode(' ', $cmd_text);
		
		$c = 0;
		if ( $cmd[0] == '-p' ) $c ++;
		
		$lk->logWrite($lk->lang->log('sendCMD', array($cmd_text)));
		
		switch ( $cmd[$c] )
		{
			case 'g':
			case 'get': {
				
				switch ( $cmd[$c + 1] )
				{
					case 'info': {
						
						$username = $cmd[$c + 2];
						
						$userinfo = $lk->init_user_byname( $username );
						
						if ( $userinfo != null ) {
							$serverinfo = $lk->lang->html('getPlayerInfo_servers');
							
							for( $i = 0, $Max = count($lk->cfg['server']); $i < $Max; $i ++ ) {
								if ( $lk->cfg['server'][$i]['enable'] ) {
									$s = explode("_", $userinfo['server_' . $i]);
									$a = explode("/", $s[0]);
									$b = $lk->getBan( $lk->getServerDB( $i ), $i, $username );
									$serverinfo .= $lk->lang->html('getPlayerInfo_server', array($lk->cfg['server'][$i]['name'], $lk->cfg['status'][$a[0]]['name'], date('d/m/Y H:i:s', $a[1]), $s[1], $s[2], ($lk->cfg['server'][$i]['right']['exchange'] ? $lk->get_money_ic( $lk->getServerDB( $i ), $i, $username ) : 'Неизвестно'), ($b ? 'Да' : 'Нет')));
								}
							}
							
							$json = '{
								"status": "success",
								"type": 1,
								"message": "'. $lk->lang->html('getPlayerInfo', array($username, $userinfo[$lk->cfg['cms']['c_money']] . ' ' . $lk->cfg['cur'][1], $serverinfo)) .'"
							}';
						} else
							$json = '{
								"status": "error",
								"type": 1,
								"message": "'. $lk->lang->error('playerNotFound') .'"
							}';
						break;
					}
					
					default: {
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('undefinedCMD') .'"
						}';
					}
				}
				
				break;
			}
			
			
			
			case 's':
			case 'set': {
				
				switch ( $cmd[$c + 1] )
				{
					
					case 'group': {
						$serverid = $cmd[$c + 2];
						$username = $cmd[$c + 3];
						$statusid = $cmd[$c + 4];
						$time_day = $cmd[$c + 5];
						
						$userinfo = $lk->init_user_byname( $username );
						
						if ( $userinfo != null ) {
							$lk->init($userinfo);	
							$_db = $lk->getServerDB($serverid);
							
							$lk->setStatus($_db, $serverid, $username, $statusid);
							$lk->setStatusUser($lk->user['id'], $serverid, $statusid, time() + $time_day * 86400, $lk->user['prefix'][$serverid], $lk->user['unban_count'][$serverid]);
							
							$entity_info = $lk->cfg['server'][$serverid]['entity'];
							if ( $entity_info != false ) {
								$entity = $lk->getEntity( $_db, $serverid, $lk->user['name'] );
								
								if ( !isset( $entity['id'] ) ) {
									$lk->setEntity( $_db, $serverid, $lk->user['name'], $entity_info['type'], $entity_info['default'] );
								}
							}
							
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('setGroup', array($username, $lk->user['name'])) .'"
							}';
						} else
							$json = '{
								"status": "error",
								"type": 1,
								"message": "'. $lk->lang->error('playerNotFound') .'"
							}';
						break;
					}
					
					case 'ban': {
						$serverid 	= $cmd[$c + 2];
						$username 	= $cmd[$c + 3];
						$reason 	= $cmd[$c + 4];
						$time_day 	= $cmd[$c + 5];
						$admin 		= isset($cmd[$c + 6]) ? $cmd[$c + 6] : false;
						
						if ( $lk->init_user_byname( $username ) != null )
						{
							$sth = $lk->getServerDB($serverid)->prepare("INSERT INTO `{$lk->cfg['server'][$serverid]['tables'][TABLE_BANLIST]}` SET
									{$lk->cfg['other']['unban']['table']['name']} = :name,
									{$lk->cfg['other']['unban']['table']['admin']} = :admin,
									{$lk->cfg['other']['unban']['table']['reason']} = :reason,
									{$lk->cfg['other']['unban']['table']['time']} = :time
							");
							
							$sth->bindParam(':name', $username, PDO::PARAM_STR);
							$reason = str_replace('_', ' ', trim($reason, "'"));
							$sth->bindParam(':reason', $reason, PDO::PARAM_STR);
							$time = time() + $time_day * 86400;
							$sth->bindParam(':time', $time, PDO::PARAM_INT);
							$admin = ($admin != false ? $admin : 'admin');
							$sth->bindParam(':admin', $admin, PDO::PARAM_STR);
							$sth->execute();
							
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('setBan', array($username, $time_day, $reason)) .'"
							}';
						} else
							$json = '{
								"status": "error",
								"type": 1,
								"message": "'. $lk->lang->error('playerNotFound') .'"
							}';
						break;
					}
					
					case 'vaucher': {
						$message = str_replace('_', ' ', $cmd[$c + 4]);
						$eval = str_replace('}', ')', str_replace('{', '(', $cmd[$c + 3]));
						
						$lk->addVaucher( $cmd[$c + 2], $eval, $message);
						$json = '{
							"status": "success",
							"message": "'. $lk->lang->success('setVaucher', array($cmd[$c + 2], str_replace('"', "'", $eval), $message)) .'"
						}';
						
						break;
					}
					
					default: {
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('undefinedCMD') .'"
						}';
					}
				}
				
				break;
			}
			
			
			
			case 'give': {
				
				switch ( $cmd[$c + 1] )
				{
					
					case 'money': {
						$username 	= $cmd[$c + 2];
						$money 		= $cmd[$c + 3];
						
						$userinfo = $lk->init_user_byname( $username );
						
						if ( $userinfo != null ) {
							
							$lk->init($userinfo);
							$lk->give_money($money);
							
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('giveMoney', array($username, $money . ' ' . $lk->cfg['cur'][1])) .'"
							}';
						}
						break;
					}
					
					case 'icmoney': {
						
						$serverid 	= $cmd[$c + 2];
						$username 	= $cmd[$c + 3];
						$icmoney 	= $cmd[$c + 4];
						
						if ( $lk->init_user_byname( $username ) != null ) {
							
							$lk->give_money_ic($lk->getServerDB($serverid), $serverid, $username, $icmoney);
							
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('giveMoneyIC', array($username, $icmoney)) .'"
							}';
						}
						break;
					}
					
					default: {
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('undefinedCMD') .'"
						}';
					}
				}
				break;
			}
			
			
			
			case 'd':
			case 'del':
			case 'delete': {
				
				switch ( $cmd[$c + 1] )
				{
					
					case 'group': {
						$serverid 	= $cmd[$c + 2];
						$username 	= $cmd[$c + 3];
						
						$userinfo = $lk->init_user_byname( $username );
						
						if ( $userinfo != null ) {
							
							$lk->init($userinfo);
							$lk->setStatus($lk->getServerDB($serverid), $serverid, $username, 0);
							$lk->setStatusUser($lk->user['id'], $serverid, 0, 0, $lk->user['prefix'][$serverid], $lk->user['unban_count'][$serverid]);
							
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('deleteGroup') .'"
							}';
						}
						break;
					}
					
					case 'ban': {
						$serverid 	= $cmd[$c + 2];
						$username 	= $cmd[$c + 3];
						
						if ( $lk->init_user_byname( $username ) != null ) {
							
							$lk->unban($lk->getServerDB($serverid), $serverid, $username);
							
							$json = '{
								"status": "success",
								"message": "'. $lk->lang->success('deleteBan') .'"
							}';
						}
						break;
					}
					
					default: {
						$json = '{
							"status": "error",
							"message": "'. $lk->lang->error('undefinedCMD') .'"
						}';
					}
				}
				break;
			}
			
			default: {
				$json = '{
					"status": "error",
					"message": "'. $lk->lang->error('undefinedTypeCMD') .'"
				}';
			}
		}
	} else
		$json = '{
			"status": "error",
			"type": 1,
			"message": "'. $lk->lang->error('incorrectCMD') .'"
		}';
	
	echo $json;
?>