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

	include( realpath( 'config.php' ) );
	
	foreach ( $cfg['req'] as $key => $val )
	{
		$_REQ[$key] = isset($_REQUEST[$val[0]]) ? $_REQUEST[$val[0]] : 0;
	}
	
	if ( ( !$cfg['use_key'] || $_REQ['key'] == $cfg['key'] ) && ( is_numeric($_REQ['system']) && $_REQ['system'] > 0 && $_REQ['system'] <= 4 ) )
	{
		
		require( realpath( 'payment.class.php' ) );
		
		if ( !$cfg['payments'][$_REQ['system'] - 1]['enable'] ) die('Ошибка: данная пл. система не принимается!');
		
		switch ( $_REQ['system'] )
		{
			
			case ID_IK:	//INTERKASSA
			{
				$payment = new payment_ik;
				$_REQUEST['ik_co_id'] = $cfg['payments'][ID_IK - 1]['id'];
				header('Location: https://sci.interkassa.com/' . $payment->create_req($_REQUEST, $cfg['payments'][ID_IK - 1]['prefix']));
				break;
			}
			
			case ID_UP:	//UNITIPAY
			{
				$payment = new payment_up;
				header('Location: http://unitpay.ru/pay/' . $cfg['payments'][ID_UP - 1]['id'] . $payment->create_req($_REQUEST, $cfg['payments'][ID_UP - 1]['prefix']));
				break;
			}
			
			case ID_RK:	//ROBOKASSA
			{
				$payment = new payment_rk;
				$params = $_REQUEST;
				$params['rk_MrchLogin'] = $cfg['payments'][ID_RK - 1]['login'];
				$params['rk_Culture'] = 'ru';
				if ( $cfg['payments'][ID_RK - 1]['test'] ) $params['rk_isTest'] = 1;
				$params['rk_SignatureValue'] = md5($params['rk_MrchLogin'] . ":" . $params['rk_OutSum'] . ":0:". $params['rk_OutSumCurrency'] .":" . $cfg['payments'][ID_RK - 1]['pass1'] . ":shp_userid=" . $params['rk_shp_userid']);
				header('Location: '.('https://merchant.roboxchange.com/Index.aspx') . $payment->create_req($params, $cfg['payments'][ID_RK - 1]['prefix']));
				break;
			}
			
			case ID_FK:	//FREEKASSA
			{
				$payment = new payment_fk;
				
				$params = $_REQUEST;
				$params['fk_lang'] = 'ru';
				$params['fk_m'] = $cfg['payments'][ID_FK - 1]['id'];
				
				$db = new PDO('mysql:host='.$cfg['db']['host'].';dbname='.$cfg['db']['name'], $cfg['db']['user'], $cfg['db']['pass']);
				$db->query("SET NAMES '".$cfg['db']['charset']."'");
				
				$userid = $params['fk_us_id'];
				$trans = $db->query("SELECT id,userid FROM ".$cfg['db']['trans']." WHERE userid=" . $userid . " LIMIT 1")->fetch(PDO::FETCH_ASSOC);
				if ( isset($trans['id']) ) {
					$db->query("UPDATE `".$cfg['db']['trans']."` SET sum=". round($params['fk_oa']) .",date=". time() ." WHERE id = " . $trans['id']);
					$params['fk_o'] = $trans['id'];
				} else {
					$stmt = $db->query("INSERT INTO `".$cfg['db']['trans']."` VALUES (NULL, 0, ". $userid .", ". round($params['fk_oa']) .", 'process', 'RUB', ". time() .")");
					$params['fk_o'] = $db->lastInsertId();
				}
				//echo 'last id = ' . $params['fk_o'];
				$params['fk_s'] = md5($params['fk_m'].':'.$params['fk_oa'].':'.$cfg['payments'][ID_FK - 1]['secret'].':'.$params['fk_o']);
				
				header('Location: http://www.free-kassa.ru/merchant/cash.php' . $payment->create_req($params, $cfg['payments'][ID_FK - 1]['prefix']));
				break;
			}
		}
		
	}
?>