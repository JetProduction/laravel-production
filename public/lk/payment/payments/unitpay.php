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
	
	$up = $cfg['payments'][ID_UP - 1];
	
	if ( !$up['enable'] ) {
		die('Off');
	} 
	
	$payment = new payment_up;
	
	
	$request = $_GET;
	
	if ( $up['check_ip'] ) {
		if ( !in_array($_SERVER["REMOTE_ADDR"], $up['allow_ip']) ) {
			die('IP address Error');
		}
	}
	
	if ( empty($request['method'])
		|| empty($request['params'])
		|| !is_array($request['params'])
	)
	{
		die($payment->getResponseError('Invalid request'));
	}

	$method = $request['method'];
	$params = $request['params'];
	
	//echo $payment->getMd5Sign($params, $up['key']);
	
	if ( $params['signature'] != $payment->getSha256SignatureByMethodAndParams($method, $params, $up['key']) )
	{
		die($payment->getResponseError('Incorrect digital signature'));
	}
	
	$db = new PDO('mysql:host='.$cfg['db']['host'].';dbname='.$cfg['db']['name'], $cfg['db']['user'], $cfg['db']['pass']);
	$db->query("SET NAMES '".$cfg['db']['charset']."'");
	
	if ( $db->query("SELECT COUNT(0) FROM `".$cfg['db']['trans']."` WHERE `status` = 'success' AND `payid` = ". $params['unitpayId'])->fetchColumn() )
	{
		die($payment->getResponseError('РџР»Р°С‚РµР¶ СѓР¶Рµ СЃРѕРІРµСЂС€РµРЅ!'));
	}
		
	if ( $method == 'check' )
	{
		if ( $db->query("SELECT COUNT(0) FROM `".$cfg['db']['trans']."` WHERE `status` = 'process' AND `payid` = ". $params['unitpayId'])->fetchColumn() )
		{
			die($payment->getResponseError('Error!'));
		}
	
		if ( !$db->query("INSERT INTO `".$cfg['db']['trans']."` VALUES (NULL, ". $params['unitpayId'] .", ". $params['account'] .", ". round($params['sum']) .", 'process', '". $params['orderCurrency'] ."', ". time() .")") )
		{
			die($payment->getResponseError('Dont connect to DB!'));
		}
		
		die($payment->getResponseSuccess('CHECK is successful'));
	}
	
	if ( $method == 'pay' )
	{
		$db->query("UPDATE `".$cfg['db']['trans']."` SET `status` = 'success' WHERE `status` = 'process' AND `payid` = ". $params['unitpayId']);
		$db->query("UPDATE `{$cfg['db']['users']}` SET `{$cfg['db']['money']}` = {$cfg['db']['money']} + ".round($params['sum'])." WHERE `{$cfg['db']['userid']}` = {$params['account']}");
			
		die($payment->getResponseSuccess('PAY is successful'));
	}
?>