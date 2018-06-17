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

	
	require( realpath( '../config.php' ) );
	require( realpath( '../payment.class.php' ) );
	
	$rk = $cfg['payments'][ID_RK - 1];
	
	if ( !$rk['enable'] ) {
		die('Off');
	} 
	
	$payment = new payment_rk;
	
	
	$sum = $_REQUEST["OutSum"];
	$inv_id = $_REQUEST["InvId"];
	$user_id = $_REQUEST["shp_userid"];
	$rk_sign = $_REQUEST["SignatureValue"];
	
	if ( $payment->getSign( $sum, $inv_id, $rk['pass2'], $user_id ) != $rk_sign )
	{
		die( "bad sign\n" );
	}
	
	$db = new PDO('mysql:host='.$cfg['db']['host'].';dbname='.$cfg['db']['name'], $cfg['db']['user'], $cfg['db']['pass']);
	$db->query("SET NAMES '".$cfg['db']['charset']."'");
	
	if ( $db->query("SELECT COUNT(0) FROM `".$cfg['db']['trans']."` WHERE `status` = 'success' AND `payid` = ". $inv_id)->fetchColumn() )
	{
		die( "error" );
	}
	
	if ( $db->query("INSERT INTO `".$cfg['db']['trans']."` VALUES (NULL, ". $inv_id .", ". $user_id .", ". round($sum) .", 'success', 'RUB', ". time() .")") )
	{
		$db->query("UPDATE `{$cfg['db']['users']}` SET `{$cfg['db']['money']}` = {$cfg['db']['money']} + ".round($sum)." WHERE `{$cfg['db']['userid']}` = {$user_id}");
		echo "OK" . $inv_id . "\n";
	}
		else die( "error" );
?>