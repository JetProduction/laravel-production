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
	
	if ( !isset($_SESSION['lk_admin']) or $_SESSION['lk_admin'] !== true ) {
		die('not access');
	}
	
	define ( 'ROOT_LK_ADMIN_DIR', dirname ( __FILE__ ) );
	
	include(ROOT_LK_ADMIN_DIR . '/../config.php');
	
	$db = @new PDO('mysql:host='.$config_lk['db']['host'].';dbname='.$config_lk['db']['name'], $config_lk['db']['user'], $config_lk['db']['pass']);
	$db->query("SET NAMES '".$config_lk['db']['char']."'");
	
	if ( !isset($_POST['nickname']) ) 
		die('{
			"status": "error",
			"message": "Нет запроса"
		}');
	$playerName = $_POST['nickname'];
	
	if ( !preg_match('/^[a-z 0-9_]{3,30}$/i', $playerName) ) 
		die('{
			"status": "error",
			"message": "Некорректный никнейм"
		}');
	
	$c_name = $config_lk['cms']['c_name'];
	$c_id = $config_lk['cms']['c_userid'];
	$playerArray = $db->query("SELECT {$c_name},{$c_id} FROM {$config_lk['cms']['t_users']} WHERE {$c_name} LIKE '%{$playerName}%' LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
	
	$json = '{
		"status": "success",
		"players":[
	';
	foreach ( $playerArray as $row ) {
		$json .= '{"name": "'. $row[$c_name] .'", "userid": '. $row[$c_id] .'},';
	}
	$json .= '
			{"name": "", "userid": 0}
		]}
	';
	echo $json;
?>