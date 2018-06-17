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
	
	$fk = $cfg['payments'][ID_FK - 1];
	
	if ( !$fk['enable'] ) {
		die('Off');
	} 
	
	$payment = new payment_fk;
	$merchant_id = $cfg['payments'][ID_FK - 1]['id'];
	$merchant_secret = $cfg['payments'][ID_FK - 1]['secret2'];
	$ORDER_ID = $_REQUEST['MERCHANT_ORDER_ID'];
	$SUM = $_REQUEST['AMOUNT'];
	
	if ( !in_array($payment->getIP(), array('136.243.38.147', '136.243.38.149', '136.243.38.150', '136.243.38.151', '136.243.38.189', '88.198.88.98')) ) {
		die("hacking attempt!");
    }
	
	$sign = md5($merchant_id.':'.$SUM.':'.$merchant_secret.':'.$ORDER_ID);
	if ($sign != $_REQUEST['SIGN']) {
		die('wrong sign');
    }
	
	$db = new PDO('mysql:host='.$cfg['db']['host'].';dbname='.$cfg['db']['name'], $cfg['db']['user'], $cfg['db']['pass']);
	$db->query("SET NAMES '".$cfg['db']['charset']."'");
	
	if ( $db->query("SELECT COUNT(0) FROM `".$cfg['db']['trans']."` WHERE `status` = 'success' AND `id` = ". $ORDER_ID)->fetchColumn() )
	{
		die("the paymeny has already been completed");
	}
	
	
	$db->query("UPDATE `".$cfg['db']['trans']."` SET `status` = 'success' WHERE `status` = 'process' AND `id` = ". $ORDER_ID);
	$db->query("UPDATE `{$cfg['db']['users']}` SET `{$cfg['db']['money']}` = {$cfg['db']['money']} + ".round($SUM)." WHERE `{$cfg['db']['userid']}` = {$_REQUEST['us_id']}");
	die('YES');
?>