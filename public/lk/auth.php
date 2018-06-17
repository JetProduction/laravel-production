<?php
/*

		
		ИНФОРМАЦИЯ:
			Личный Кабинет v1.4.5 WINDOWS-1251
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

	//По умолчанию настроено на AuthMe
	
	$link = 'http://site.ru/lk/index.php'; //Куда должно перебросить после успешной авторизации. Указывайте ссылку на ЛК.
	$max_len_login = 30;		//Длина логина
	$max_len_pass = 20;			//Длина пароя
	$column_pass = 'password';	//Имя колонки с паролями
	$hashtype = 'MD5';			//Тип шифрования: MD5, SHA1, SHA256
	
	$message = '';
	
	if ( isset( $_POST['input'] ) ) {
		$username = isset($_POST['username']) ? $_POST['username'] : false;
		$password = isset($_POST['password']) ? $_POST['password'] : false;
		
		if ( $username && $password ) {
			if ( empty( $username{$max_len_login} ) && empty( $password{$max_len_pass} ) ) {
				$sth = $this->db->prepare("SELECT {$this->cfg['cms']['c_userid']},{$column_pass} FROM {$this->cfg['cms']['t_users']} WHERE {$this->cfg['cms']['c_name']} = :name");
				$sth->bindParam(':name', $username, PDO::PARAM_STR);
				$sth->execute();
				$userinfo = $sth->fetch(PDO::FETCH_NUM);
				
				if ( $userinfo[0] ) {
					$getpass = explode('$', $userinfo[1]);
					$pass = pass_hash( $hashtype, $password, $getpass[2] );
					if ( $getpass[3] == $pass ) {
						$_SESSION['lk_user_id'] = $userinfo[0];
						header( 'Location: ' . $link );
					} else {
						$message = 'Неверный логин или пароль.';
					}
				} else {
					$message = 'Неверный логин или пароль.';
				}
			} else {
				$message = 'Длинный логин или пароль.';
			}
		} else {
			$message = 'Введите логин или пароль.';
		}
	}
	
	function pass_hash( $type, $password, $salt = '' ) {
		
		switch ( $type ) {
			
			case 'MD5': {
				return md5($password);
			}
			
			case 'SHA1': {
				return sha1($password);
			}
			
			case 'SHA256': {
				return hash('sha256', hash('sha256', $password) . $salt);
			}
		}
		
		return '';
	}
?>

<div class="auth">
	<h2 align="center">Sign in</h2>

	<div class="alert"><?php echo $message?></div>

	<form method="POST">
		<span class="desc">Логин:</span><br/>
		<input type="text" name="username" placeholder="Введите ваш никнейм" class="input"/><br/><br/>
		<span class="desc">Пароль:</span><br/>
		<input type="password" name="password" placeholder="Введите ваш пароль" class="input"/><br/><br/>
		<center>
			<input type="submit" name="input" class="button" value="Sign in"/>
		</center>
	</form>
</div>

<style>
	
	body {
		font-family: tahoma;
	}
	
	.auth {
		width: 200px;
		margin: 0 auto;
		padding: 15px;
		border: 2px solid #E47D7D;
		border-radius: 5px;
	}
	
	.alert {
		font-size: 13px;
		color: #C95A5A;
	}
	
	.input {
		outline: 0;
		width: 200px;
		padding: 10px;
		border: 1px solid #D4D4D4;
	}
	
	.desc {
		
	}
	
	.button {
		padding: 5px;
	}
	
</style>