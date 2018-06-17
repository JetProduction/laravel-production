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
		
		
		
		По любым вопросам обращайтесь к автору данного кода http://vk.com/fleynaro
		
	
*/

	define ( 'ROOT_LK_DIR', dirname ( __FILE__ ) );
	
	include(ROOT_LK_DIR . '/config.php');
	include(ROOT_LK_DIR . '/class/language.class.php');
	include(ROOT_LK_DIR . '/class/_user.class.php');
	include(ROOT_LK_DIR . '/class/lk.class.php');
	
	$lk = new lk( $config_lk, true );
	
	$cms = $lk->cfg['cms'];
	$time = time();
	
	for( $i = 0, $Max = count($lk->cfg['server']); $i < $Max; $i ++ ) {
		$_db = $lk->getServerDB( $i );
		$serv_info = $lk->cfg['server'][$i];
		$users = $lk->db->query("SELECT * FROM {$cms['t_users']} WHERE `server_{$i}` != ''")->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ( $users as $row ) {
			$lk->user = $row;
			$lk->user['id'] = $lk->user[ $cms['c_userid'] ];
			$lk->user['name'] = $lk->user[ $cms['c_name'] ];
			$server = explode('_', $row['server_' . $i]);
			$status = explode('/', $server[0]);
			
			if ( $status[0] > 0 && $status[1] <= $time ) {
				$lk->setStatusUser( $lk->user['id'], $i, 0, 0, array(0, 0, 0, 0), $server[2] );
				$lk->deletePrefix( $_db, $i, $lk->user['name'] );
				$lk->setStatus( $_db, $i, $lk->user['name'], 0 );
				$lk->logWrite($lk->lang->log('removeGroupByCron', array($lk->cfg['status'][$status[0]]['name'])));
			}
		}
		
		$_db->query("DELETE l.*,p.* FROM lk_pexrights l INNER JOIN {$serv_info['tables'][TABLE_PERMISSION]} p ON l.pexRightId = p.id WHERE time != -1 AND time < " . $lk->time);
	}
?>