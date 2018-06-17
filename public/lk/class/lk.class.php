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


	class lk extends _user {
		
		public $lang;
		public $db;
		public $dbs = array();
		public $user = array();
		public $cfg = array();
		public $req = array();
		public $path;
		public $time;
		
		function __construct( $config_lk, $user__ = false )
		{
			$this->time = time();
			$this->cfg = $config_lk;
			$this->lang = new Language(ROOT_LK_DIR. '/lang/' .$this->cfg['language']);
			
			$this->DB_connect();
			if ( !$this->cfg['exchange']['iconomy']['ic_money_viem'] || !$this->cfg['other']['unban']['enable'] || !$this->cfg['other']['warp']['list_warps'] || !$this->cfg['other']['right_pex']['all_server'] ) {
				$this->connectTo();
			}
			
			$this->path = '';
			if ( $this->cfg['insite'] !== false ) {
				$insite = true;
				if ( $this->cfg['insite'] == 'auto' ) {
					$arrCurrentRelativePath = explode("/", dirname($_SERVER['SCRIPT_NAME']));
					$arrConstantAbsolutePath = explode("/", str_replace('\\', '/', ROOT_LK_DIR));
					if ( $arrCurrentRelativePath[count($arrCurrentRelativePath) - 1] == $arrConstantAbsolutePath[count($arrConstantAbsolutePath) - 1] ) {
						$insite = false;
					}
				} else if ( $this->cfg['insite'] !== true ) {
					$this->path = $this->cfg['insite'];
					$insite = false;
				}
				if ( $insite ) {
					$this->path = str_replace('\\', '/', substr(ROOT_LK_DIR, strlen($_SERVER['DOCUMENT_ROOT']) + 1)) . '/';
				}
			}
			
			$this->req_filter( $_REQUEST );
			
			if ( $this->cfg['selectTpl'] && $this->getReq('tpl') !== false ) {
				
				$tpl_name = $this->getReq('tpl');
				
				if ( preg_match('/^[a-z0-9_]{0,30}$/si', $tpl_name) && file_exists(ROOT_LK_DIR . '/templates/' . $tpl_name) ) {
					
					$this->cfg['template'] = $tpl_name;
					
				} else {
					die ( $this->lang->error('tplHasnotFound') );
				}
			}
			
			if ( !$user__ )
			{	
				$userid_ses = $this->getSessionID( $this->cfg['cms']['cms_id'] );
				
				if ( $userid_ses != -1 ) {
					$this->init($this->init_user( $userid_ses ));
				} else {
					die($this->lang->error('needAuth'));
				}
			}
			
			header("Content-type: text/html; charset=".$this->cfg['charset']);
		}
		
		public function init( $userinfo )
		{
			$this->user = $userinfo;
			$this->user['id'] 		= $this->user[ $this->cfg['cms']['c_userid'] ];
			$this->user['name'] 	= $this->user[ $this->cfg['cms']['c_name'] ];
			$this->user['group'] 	= $this->user[ $this->cfg['cms']['c_group'] ];
			$this->user['money'] 	= $this->user[ $this->cfg['cms']['c_money'] ];
			$this->user['right'] 	= array();
			for( $i = 0, $Max = count($this->cfg['rights']['right']); $i < $Max; $i ++ ) {
				$right = $this->cfg['rights']['right'][$i];
				if ( $right[4] ) {
					$this->user['right'][$right[1]] = (bool)$this->user[ $right[2] ];
				} else {
					$this->user['right'][$right[1]] = false;
				}
			}
			
			if ( !isset($this->user['admin']) ) {
				$this->user['admin'] = false;
				
				if ( $this->cfg['admin']['enable'] ) {
					foreach ( $this->cfg['admin']['groups'] as $val ) {
						if ( $val == $this->user['group'] ) {
							$this->makeAdmin();
							
							if ( $this->getReq('userid') !== false ) {
								$userid = (int)$this->getReq('userid');
								
								if ( is_numeric($userid) && $userid >= 0 ) {
									if ( $userid != $this->user['id'] ) {
										$userinfo_ = $this->init_user($userid);
										if ( $userinfo_ ) {
											$userinfo_['admin'] = true;
											$userinfo_['administration'] = true;
											return $this->init($userinfo_);
										}
									}
								}
							}
						}
					}
				}
			}
			
			if ( $this->cfg['exchange']['iconomy']['enable'] ) {
				$this->user['icmoney'] = Array(0);
				
				if ( $this->cfg['exchange']['iconomy']['ic_money_viem'] ) {
					$def_server = $this->cfg['exchange']['iconomy']['default_server'];
					
					$this->user['icmoney'][0] = $this->get_money_ic( $this->db, $def_server, $this->user['name'] );
					
					if ( $this->user['icmoney'][0] === false ) {
						$this->user['icmoney'][0] = 0;
						
						$sth = $this->db->prepare("INSERT INTO `{$this->cfg['server'][$def_server]['tables'][TABLE_ICONOMY]}` VALUES(NULL, :name, 0.0, 0)");
						$sth->bindParam(':name', $this->user['name'], PDO::PARAM_STR);
						$sth->execute();
					}
				} else {
					for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
						if ( $this->cfg['server'][$i]['right']['exchange'] ) {
							$this->user['icmoney'][$i] = $this->get_money_ic( $this->dbs[$i], $i, $this->user['name'] );
							
							if ( $this->user['icmoney'][$i] === false ) {
								$this->user['icmoney'][$i] = 0;
								
								$sth = $this->dbs[$i]->prepare("INSERT INTO `{$this->cfg['server'][$i]['tables'][TABLE_ICONOMY]}` VALUES(NULL, :name, 0.0, 0)");
								$sth->bindParam(':name', $this->user['name'], PDO::PARAM_STR);
								$sth->execute();
							}
						} else {
							$this->user['icmoney'][$i] = false;
						}
					}
				}
			}
			
			
			if ( $this->cfg['other']['unban']['enable'] ) {
				$this->user['ban'] = Array();
				
				if ( $this->cfg['other']['unban']['unban_all'] ) {
					$this->user['ban'][0] = $this->getBan( $this->db, $this->cfg['other']['unban']['default_server'], $this->user['name'] );
				} else {
					for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
						if ( $this->cfg['server'][$i]['right']['unban'] ) {
							$this->user['ban'][$i] = $this->getBan( $this->dbs[$i], $i, $this->user['name'] );
						} else {
							$this->user['ban'][$i] = Array('name' => false, 'reason' => false, 'admin' => false, 'time' => false);
						}
					}
				}
			}
			
			
			if ( $this->cfg['other']['warp']['enable'] ) {
				$this->user['warp'] = Array();
				
				if ( $this->cfg['other']['warp']['list_warps'] ) {
					$this->user['warp'][0] = $this->listWarps( $this->db, $this->cfg['other']['warp']['default_server'], $this->user['name'] );
				} else {
					for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
						if ( $this->cfg['server'][$i]['right']['warp'] ) {
							$this->user['warp'][$i] = $this->listWarps( $this->dbs[$i], $i, $this->user['name'] );
						} else {
							$this->user['warp'][$i] = false;
						}
					}
				}
			}
			
			
			if ( $this->cfg['right_pex']['enable'] ) {
				$this->user['right_pex'] = Array();
				
				if ( $this->cfg['right_pex']['all_server'] ) {
					$this->user['right_pex'][0] = $this->pexRightList( $this->db, $this->cfg['right_pex']['default_server'] );
				} else {
					for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
						if ( $this->cfg['server'][$i]['right']['buy_right'] ) {
							$this->user['right_pex'][$i] = $this->pexRightList( $this->dbs[$i], $i );
						} else {
							$this->user['right_pex'][$i] = false;
						}
					}
				}
			}
			
			
			if ( !isset($_SESSION['lk_csrf_key']) )
				$_SESSION['lk_csrf_key'] = $this->generateKeyCSRF();
			$this->user['key'] = $_SESSION['lk_csrf_key'];
			
			
			for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
				$info = $this->getInfo($i);
				
				if ( $info[0] != 0 )
				{
					$this->user['status'][$i] = explode('/', $info[0]);
					$this->user['prefix'][$i] = explode('/', $info[1]);
					$this->user['unban_count'][$i] = (int)$info[2];
				} else {
					$this->user['status'][$i] = Array(0, 0);
					$this->user['prefix'][$i] = Array(1, 'Player', 1, 1);
					$this->user['unban_count'][$i] = 0;
				}
				
				if ( $this->cfg['prefix']['prefix_perm'] ) {
					$prefix = $this->getPrefix( $this->getServerDB( $i ), $i, $this->user['name'] );
					if ( count($prefix) ) {
						$count = 0;
						$form = $this->cfg['prefix']['prefix_format'];
						$form[0] = str_replace('/', '\\', preg_replace('/(\[|\]|\(|\))/i', "/$1", $form[0]));
						$form[1] = str_replace('/', '\\', preg_replace('/(\[|\]|\(|\))/i', "/$1", $form[1]));
						$eprefix = preg_replace('/.*'.$form[0].'\s*&(\w)\s*(\w+)\s*'.$form[1].'.*&(\w)/i', "$1/$2/$3", $prefix[0], -1, $count);
						if ( $count > 0 ) {
							$eprefix .= preg_replace('/&(\w)/i', "/$1", $prefix[1], -1, $count);
							if ( $count > 0 && ($info[0] == 0 || $info[1] != $eprefix) ) {
								$this->user['prefix'][$i] = explode('/', $eprefix);
								$colors = array('f' => 0, 0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10, 'a' => 11, 'b' => 12, 'c' => 13, 'd' => 14, 'e' => 15);
								$this->user['prefix'][$i][0] = $colors[$this->user['prefix'][$i][0]];
								$this->user['prefix'][$i][2] = $colors[$this->user['prefix'][$i][2]];
								$this->user['prefix'][$i][3] = $colors[$this->user['prefix'][$i][3]];
							}
						}
					}
				}
			}
		}
		
		public function connectTo()
		{
			for( $i = 0, $Max = count($this->cfg['server']); $i < $Max; $i ++ ) {
				$this->dbs[$i] = $this->getServerDB( $i );
			}
		}
		
		private function DB_connect()
		{
			$this->db = @new PDO('mysql:host='.$this->cfg['db']['host'].';dbname='.$this->cfg['db']['name'], $this->cfg['db']['user'], $this->cfg['db']['pass']);
			$this->db->query("SET NAMES '".$this->cfg['db']['char']."'");
		}
		
		public function DB_server_connect( $server_id )
		{
			$_db = @new PDO('mysql:host='.$this->cfg['server'][$server_id]['db']['host'].';dbname='.$this->cfg['server'][$server_id]['db']['name'], $this->cfg['server'][$server_id]['db']['user'], $this->cfg['server'][$server_id]['db']['pass']);
			$_db->query("SET NAMES '".$this->cfg['server'][$server_id]['db']['char']."'");
			return $_db;
		}
		
		public function getServerDB( $server_id )
		{
			if ( $this->cfg['server'][$server_id]['db'] != false ) {
				if ( isset($this->dbs[$server_id]) ) {
					return $this->dbs[$server_id];
				} else {
					return $this->DB_server_connect( $server_id );
				}
			} else {
				return $this->db;
			}
		}
		
		private function req_filter( $_REQ )
		{
			foreach( $_REQ as $key=>$val )
			{
				if ( !is_array( $val ) )
				{
					$this->req[$key] = strip_tags( $val );
				} else {
					$this->req[$key] = array();
					
					foreach( $val as $key2=>$val2 )
					{
						$this->req[$key][$key2] = strip_tags( $val2 );
					}	
				}		
			}
		}
		
		public function getReq( $index )
		{
			return (isset( $this->req[ $index ] ) ? $this->req[ $index ] : false);
		}
		
		public function isMultiServers()
		{
			return ($this->cfg['server'][0]['name'] != 'all');
		}
		
		public function isUUIDServer( $server_id )
		{
			return ($this->cfg['server'][$server_id]['uuid']);
		}
		
		public function isNum( $num )
		{
			return ((int)$num + '' == $num);
		}
		
		public function isNumIn( $num, $min, $max )
		{
			return ( is_numeric($num) && $num >= $min && $num <= $max );
		}
		
		public function logWrite( $message )
		{
			$log = $this->cfg['log'];
			
			if ( $log['enable'] )
			{
				$path = ROOT_LK_DIR . $log['path'];
				
				if ( filesize($path) >= $log['clear'] * 1024 )
				{
					$handle = fopen($path, "w");
					fclose($handle);
				}
				
				$handle = fopen($path, "a");
				fwrite($handle, '['.date('d.m.Y в G:i:s', $this->time).'] '. $this->user['name'] .' '.$message."\n");
				fclose($handle);
			}
		}
		
		public function addVaucher( $vaucher, $eval, $message )
		{
			$sth = $this->db->prepare("INSERT INTO `lk_vauchers` VALUES(NULL, :vaucher, :eval, :message)");
			$sth->bindParam(':vaucher', $vaucher, PDO::PARAM_STR);
			$sth->bindParam(':eval', $eval, PDO::PARAM_STR);
			$sth->bindParam(':message', $message, PDO::PARAM_STR);
			return $sth->execute();
		}
		
		public function deleteVaucher( $id )
		{
			$sth = $this->db->prepare("DELETE FROM `lk_vauchers` WHERE `id` = :id");
			$sth->bindParam(':id', $id, PDO::PARAM_INT);
			return $sth->execute();
		}
		
		public function getVaucher( $vaucher )
		{
			$sth = $this->db->prepare("SELECT * FROM `lk_vauchers` WHERE `name` = :vaucher");
			$sth->bindParam(':vaucher', $vaucher, PDO::PARAM_STR);
			$sth->execute();
			return $sth->fetch(PDO::FETCH_ASSOC);
		}
		
		public function getPriceStatus( $status_id )
		{
			$status = $this->cfg['status'][$status_id];
			return $status['price'] - ($status['price'] / 100 * $status['discount']);
		}
		
		public function content()
		{
			$tpl_path = ROOT_LK_DIR . '/templates/' . $this->cfg['template'] . '/';
			
			$tpls = new Tpl($this, $tpl_path);
			echo $tpls->copyright();
			
			$template = new Template($tpl_path . 'main', $tpls->global_data);
			//TPL переменные
			$template->set_global('lk', $this);
			$template->set_global('user', $this->user);
			$template->set_global('cfg', $this->cfg);
			$template->set_global('tpls', $tpls);
			
			$template->set_global('path', $this->path);
			$template->set_global('pathToTemplate', $this->path . 'templates/' . $this->cfg['template']);
			$template->set_global('pathToSkins', $this->path . substr($this->cfg['skin']['path_to_skin'], 1) . ($this->cfg['skin']['multi_enable'] ? 'server_0/' : ''));
			$template->set_global('pathToCloaks', $this->path . substr($this->cfg['skin']['path_to_cloak'], 1) . ($this->cfg['skin']['multi_enable'] ? 'server_0/' : ''));
			$template->set_global('pathToUserSkin', $template->pathToSkins . ($this->hasSkin($this->user['name'], 0) ? $this->user['name'] : 'default') . '.png');
			$template->set_global('pathToUserCloak', $this->hasCloak($this->user['name'], 0) ? $template->pathToCloaks . $this->user['name'] . '.png' : false);
			$template->set_global('userMoney',  $this->moneyPrice($this->user['money']));
			$template->set_global('skinPath2D', $this->path . 'skin.php?' . ($this->cfg['skin']['multi_enable'] ? 'server=0&' : '') . 'username=' . $this->user['name']);
			$template->set_global('unbanCount', $this->getCountUnbanAll());
			$template->set_global('firstUnbanPrice', $this->moneyPrice($this->cfg['other']['unban']['price']));
			$template->set_global('nextUnbanPrice', $this->moneyPrice($this->cfg['other']['unban']['price_next']));
			$template->set_global('paymentPath', $this->path . $this->cfg['payment']['path']);
			$template->set_global('paymentCur', $this->cfg['cur_price'] . ' ' . $this->cfg['payment']['curname']);
			//$template->set_global('', );
			
			$template->display();
		}
		
		public function moneyPrice( $price )
		{
			return $price . ' ' . $this->cfg['cur'][1];
		}
		
		public function fullMoneyPrice( $price )
		{
			if ( $price % 100 > 4 && $price % 100 < 20 ) {
				return $price . ' ' . $this->cfg['cur'][2];
			} else {
				$num = $price % 10;
				if ( $num == 1 ) {
					return $price . ' ' . $this->cfg['cur'][4];
				} else if ( $num > 1 && $num < 5 ) {
					return $price . ' ' . $this->cfg['cur'][3];
				} else {
					return $price . ' ' . $this->cfg['cur'][2];
				}
			}
		}
		
		public function boolStr( $var )
		{
			return ($var ? 'true' : 'false');
		}
	}


?>