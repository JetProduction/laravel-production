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

	class Tpl {
		public $lk;
		public $tpl_path;
		public $global_data = array();
		
		public function __construct($lk, $tpl_path) {
			$this->lk = $lk;
			$this->tpl_path = $tpl_path;
		}
		
		public function JavaScriptInit()
		{
			$content = '';
			for( $i = 0, $Max = count($this->lk->cfg['server']); $i < $Max; $i ++ ) {
				$content .= 'lk.server[' . $i . '] = new Array(' . $this->lk->user['status'][$i][0] . ', ' . $this->lk->user['status'][$i][1] * 1000 . ', \'' . $this->lk->cfg['server'][$i]['name'] . '\', new Array(' . $this->lk->user['prefix'][$i][0] . ', \'' . $this->lk->user['prefix'][$i][1] . '\', ' . $this->lk->user['prefix'][$i][2] . ', ' . $this->lk->user['prefix'][$i][3] . '), '. round($this->lk->getUnbanPrice($i)) .');' . "\n";
				
				if ( $this->lk->cfg['server'][$i]['enable'] ) {
					$content .= 'lk.countServer ++;';
				}
			}
			
			for( $i = 0, $Max = count($this->lk->cfg['status']); $i < $Max; $i ++ ) {
				$content .= 'lk.status[' . $i . '] = new Array(\'' . $this->lk->cfg['status'][$i]['name'] . '\', \'' . $this->lk->cfg['status'][$i]['desc'] . '\', ' . round($this->lk->getPriceStatus($i)) . ', ' . $this->lk->cfg['status'][$i]['buy_days'] . ', ' . ($this->lk->cfg['status'][$i]['set_days'] ? 'true' : 'false') . ');' . "\n";
			}
		
			for( $i = 0, $Max = count($this->lk->cfg['cur']); $i < $Max; $i ++ ) {
				$content .= 'lk.cur[' . $i . '] = \''. $this->lk->cfg['cur'][$i] . '\';' . "\n";
			}
			
			if ( $this->lk->cfg['exchange']['iconomy']['enable'] ) {
				for( $i = 0, $Max = count($this->lk->user['icmoney']); $i < $Max; $i ++ ) {
					$content .= 'lk.icmoney[' . ($i + 1) . '] = '. ($this->lk->user['icmoney'][$i] != false ? $this->lk->user['icmoney'][$i] : 0) . ';' . "\n";
				}
			}
			
			$content .= 'lk.prefix[0] = '. $this->lk->cfg['prefix']['prefix_min_len'] .';' . "\n";
			$content .= 'lk.prefix[1] = '. $this->lk->cfg['prefix']['prefix_max_len'] .';' . "\n";
			$content .= 'lk.iconomy[0] = '. (($this->lk->cfg['exchange']['iconomy']['price_cur'] * 1000000) . '/1000000') .';' . "\n";
			$content .= 'lk.iconomy[1] = '. $this->lk->cfg['exchange']['iconomy']['price_u_cur'] .';' . "\n";
			return $content;
		}
		
		public function Bans()
		{
			if ( !$this->lk->cfg['other']['unban']['enable'] ) return '';
			$content = '';
			if ( !$this->lk->cfg['other']['unban']['unban_all'] ) {
				for( $i = 0, $Max = count($this->lk->user['ban']); $i < $Max; $i ++ ) {
					if ( $this->lk->user['ban'][$i] !== false ) {
						$content .= $this->lk->lang->html('banAllServer', array($this->lk->cfg['server'][$i]['name'], $this->lk->user['ban'][$i]['reason']));
					}
				}
			} else {
				if ( $this->lk->user['ban'][0] !== false ) {
					$content .= $this->lk->lang->html('banServer', array($this->lk->user['ban'][0]['reason']));
				}
			}
			return $content;
		}
		
		public function IcMoneys()
		{
			$content = '';
			for( $i = 0, $Max = count($this->lk->user['icmoney']); $i < $Max; $i ++ ) {
				if ( $this->lk->user['icmoney'][$i] !== false ) {
					echo $this->lk->lang->html('icMoney', array($this->lk->cfg['server'][$i]['name'], $i, round($this->lk->user['icmoney'][$i])));
				}
			}
			return $content;
		}
		
		public function PexRightList()
		{
			$content = '';
			for( $i = 0, $Max = count($this->lk->user['right_pex']); $i < $Max; $i ++ ) {
				if ( $this->lk->cfg['server'][$i]['right']['buy_right'] ) {
					$content .= $this->lk->lang->html('pexRightList', array($i, ($i > 0 ? 'style="display: none"' : ''), $this->outputPexRights($i, 'pexrights')));
				} else {
					$content .= $this->lk->lang->html('pexRightListNot', array($i, ($i > 0 ? 'style="display: none"' : '')));
				}
			}
			return $content;
		}
		
		public function WarpList()
		{
			$content = '';
			for( $i = 0, $Max = count($this->lk->user['warp']); $i < $Max; $i ++ ) {
				$content .= $this->lk->lang->html('warpBegin', array($i, ($i > 0 ? 'style="display: none"' : '')));
				
				if ( $this->lk->user['warp'][$i] !== false && count($this->lk->user['warp'][$i]) > 0 ) {
					$content .= $this->lk->lang->html('warpOutput', array($this->outputWarps($i, 'warps')));
				} else {
					$content .= $this->lk->lang->html('warpHasnot');
				}
				
				if ( $this->lk->cfg['other']['warp']['paid']['enable'] && $this->lk->getCountWarps($this->lk->getServerDB($i), $i, $this->lk->user['name']) >= $this->lk->cfg['other']['warp']['paid']['free_count'] ) {
					$content .= $this->lk->lang->html('warpCreatePay', array($this->lk->cfg['other']['warp']['paid']['price'].' '.$this->lk->cfg['cur'][1]));
				} else {
					$content .= $this->lk->lang->html('warpCreateFree');
				}
				
				$content .= '</div>';
			}
			return $content;
		}
		
		public function TopVoteList()
		{
			$content = '';
			for( $i = 0, $Max = count($this->lk->cfg['other']['top']['tops']); $i < $Max; $i ++ ) {
				$top = $this->lk->cfg['other']['top']['tops'][$i];
				$content .= $this->lk->lang->html('topVote', array($top[1], $top[2], $top[0]));
			}
			return $content;
		}
		
		public function outputServers( $filename )
		{
			if ( $this->lk->cfg['server'] === false ) return 0;
			
			$template = new Template($this->tpl_path. 'output_' . $filename, $this->global_data);
			$output = '';
			
			for( $i = 0, $Max = count($this->lk->cfg['server']); $i < $Max; $i ++ ) {
				if ( $this->lk->cfg['server'][$i]['enable'] )
				{
					$template->set('server_serverInfo', $this->lk->cfg['server'][$i]);
					$template->set('increment', $i);
					
					if ( $this->lk->user['server_' . $i] != '' )
					{
						$template->set('status', $this->lk->cfg['status'][$this->lk->user['status'][$i][0]]);
						$template->set('end_time', $this->lk->user['status'][$i][1]);
					} else {
						$template->set('status', $this->lk->cfg['status'][0]);
						$template->set('end_time', 0);
					}
					
					$output .= $template->getCrudeContent();
				}
			}

			return $output;
		}
		
		public function outputStatuses( $filename )
		{
			$template = new Template($this->tpl_path . 'output_' . $filename, $this->global_data);
			$output = '';
			
			for( $i = 0, $Max = count($this->lk->cfg['status']); $i < $Max; $i ++ ) {
				if ( $this->lk->cfg['status'][$i]['enable'] )
				{
					$template->set('increment', $i);
					$template->set('status_info', $this->lk->cfg['status'][$i]);
					$output .= $template->getCrudeContent();
				}
			}

			return $output;
		}
		
		public function outputServerAsOption()
		{
			$output = '';
			
			for( $i = 0, $Max = count($this->lk->cfg['server']); $i < $Max; $i ++ ) {
				if ( $this->lk->cfg['server'][$i]['enable'] )
				{
					$output .= $this->lk->lang->html('serverOption', array($i, $this->lk->cfg['server'][$i]['name']));
				}
			}

			return $output;
		}
		
		public function outputWarps( $serverid, $filename )
		{
			$template = new Template($this->tpl_path . 'output_' . $filename, $this->global_data);
			$output = '';
			
			foreach ( $this->lk->user['warp'][$serverid] as $row )
			{
				$row['serverId'] = $serverid;
				$row['welcomeMessage'] = str_replace("'", '*', $row['welcomeMessage']);
				$template->set('info', $row);
				$output .= $template->getCrudeContent();
			}

			return $output;
		}
		
		public function outputRights( $filename )
		{
			$template = new Template($this->tpl_path . 'output_' . $filename, $this->global_data);
			$output = '';
			$right = $this->lk->cfg['rights']['right'];
			
			for( $i = 0, $Max = count($right); $i < $Max; $i ++ ) {
				if ( $right[$i][4] )
				{
					$template->set('increment', $i);
					$template->set('info', $right[$i]);
					$output .= $template->getCrudeContent();
				}
			}

			return $output;
		}
		
		public function outputPexRights( $serverid, $filename )
		{
			$template = new Template($this->tpl_path . 'output_' . $filename, $this->global_data);
			$output = '';
			
			for( $i = 0, $Max = count($this->lk->cfg['right_pex']['rights']); $i < $Max; $i ++ ) {
				$info = $this->lk->cfg['right_pex']['rights'][$i];
				if ( $info[0] == $serverid || $info[0] == -1 ) {
					if ( $info[5] == -1 ) {
						$template->set('time', $this->lk->lang->html('always'));
						$template->set('untilTime', $this->lk->lang->html('alwaysRight'));
					} else if ( $info[5] ) {
						$year = 365 * 24;
						$years = floor($info[5] / $year);
						$days = floor($info[5] % $year / 24);
						$template->set('time', ($years != 0 ? $years . $this->lk->lang->html('cutYear') : '') . ' ' . ($days != 0 ? $days . $this->lk->lang->html('cutDay') : '') . ' ' . ($info[5] % 24 != 0 ? ($info[5] % 24) . $this->lk->lang->html('cutHour') : ''));
						$template->set('untilTime', $this->lk->lang->html('untilTime', array(date('d/m/Y H:00', $this->lk->time + $info[5] * 3600))));
					}
					$info['right_have'] = false;
					foreach ( $this->lk->user['right_pex'][$serverid] as $row )
					{
						if ( $row['rightId'] == $i ) {
							$info['right_have'] = true;
							if ( $row['time'] != -1 ) {
								$info['duration'] = $this->lk->lang->html('untilTime', array(date('d/m/Y H:00', $row['time'])));
							} else $info['duration'] = $this->lk->lang->html('alwaysBought');
							break;
						}
					}
					$template->set('increment', $i);
					$template->set('info', $info);
					$template->set('serverId', $serverid);
					$output .= $template->getCrudeContent();
				}
			}

			return $output;
		}
		
		public function outputBans( $filename )
		{
			$template = new Template($this->tpl_path . 'output_' . $filename, $this->global_data);
			$output = '';
			
			for( $i = 0, $Max = count($this->lk->user['ban']); $i < $Max; $i ++ ) {
				if ( $this->lk->user['ban'][$i]['reason'] != false ) {
					$template->set('increment', $i);
					$template->set('info', $this->lk->user['ban'][$i]);
					$output .= $template->getCrudeContent();
				}
			}
			
			if ( !$output ) {
				$output = $this->lk->lang->html('notBan');
			}

			return $output;
		}
		
		public function AdminPanel( $filename )
		{
			$template = new Template($this->tpl_path . $filename, $this->global_data);
			
			return $template->getCrudeContent();
		}
		
		public function copyright()
		{
			//Пожалуйста, не удаляйте копирайт. Он виден только при просмотре HTML кода Личного кабинета в браузере
			//Please do not remove the copyright. It can be shown for viewing HTML code of the Personal managment in a browser
			return "	
				<!--
					RU:
					Личный Кабинет для проектов Minecraft
					Автор данного Личного Кабинета: Fleynaro(faicraft)
					Сайт http://fleynaro.ru/
					Группа ВК: http://vk.com/fleynaro_prods
					
					Если Вас заинтересовал данный Личный Кабинет, Вы можете подробнее с ним ознакомиться здесь:
					http://rubukkit.org/threads/lichnyj-kabinet-v1-4-2-uuid-multiservernost-ajax-multicms.107544/
					
					EN:
					Personal managment for minecraft project and servers
					Author of the Personal managment: Fleynaro(old nick Faicraft)
					Site: http://fleynaro.ru/
					Group VK: http://vk.com/fleynaro_prods
					If you interest it you can known more here:
					http://rubukkit.org/threads/lichnyj-kabinet-v1-4-2-uuid-multiservernost-ajax-multicms.107544/
				-->
			";
		}
}


?>