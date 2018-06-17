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

	
	include( realpath( '../config.php' ) );
	include( realpath( '../payment.class.php' ) );
	
	$ik = $cfg['payments'][ID_IK - 1];
	
	if ( !$ik['enable'] ) {
		die('Off');
	} 
	
	$payment = new payment_ik;
	
	$ik_sign = $_REQUEST['ik_sign'];
	unset($_REQUEST['ik_sign']);
	
	if ( $payment->sign($_REQUEST, $_REQUEST['ik_pw_via'] == 'test_interkassa_test_xts' ? $ik['test_key'] : $ik['key']) == $ik_sign )
	{
		if ( $_REQUEST['ik_co_id'] != $ik['id'] ) die ('Ошибка. ID кассы не соответствует действительному.');
		
		$db = new PDO('mysql:host='.$cfg['db']['host'].';dbname='.$cfg['db']['name'], $cfg['db']['user'], $cfg['db']['pass']);
		$db->query("SET NAMES '".$cfg['db']['charset']."'");
		
		if ( $db->query("SELECT COUNT(0) FROM `".$cfg['db']['trans']."` WHERE `status` = 'success' AND `payid` = ". $_REQUEST['ik_inv_id'])->fetchColumn() )
		{
			die('Платеж уже совершен!');
		}
		
		if ( $_REQUEST['ik_inv_st'] == 'success' )
		{
			
			$db->query("INSERT INTO `".$cfg['db']['trans']."` VALUES (NULL, ". $_REQUEST['ik_inv_id'] .", ". $_REQUEST['ik_pm_no'] .", ". round($_REQUEST['ik_am']) .", 'success', '". $_REQUEST['ik_cur'] ."', ". time() .")");
			$db->query("UPDATE `{$cfg['db']['users']}` SET `{$cfg['db']['money']}` = {$cfg['db']['money']} + ".round($_REQUEST['ik_am'])." WHERE `{$cfg['db']['userid']}` = {$_REQUEST['ik_pm_no']}");
			echo 'OK';
			
		}
	} else echo 'Неверная цифровая подпись!';
?>