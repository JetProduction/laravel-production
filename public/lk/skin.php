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

	define ( 'ROOT_LK_DIR', dirname ( __FILE__ ) );
	
	include( ROOT_LK_DIR . '/config.php' );
	
	if ( isset($_GET['username']) )
		$username = strip_tags($_GET['username']); else $username = 'default';
		
	if ( isset($_GET['server']) )
		$server = 'server_' . $_GET['server'] . '/'; else $server = '';
	
	if ( !isset( $_GET['mode'] ) ) $_GET['mode'] = 0;
	if ( !isset( $_GET['mode2'] ) ) $_GET['mode2'] = 0;
	
	if ( !isset($_GET['catalog']) ) {
		$path_to_skin = ROOT_LK_DIR . $config_lk['skin']['path_to_skin'] . $server. $username . '.png';
		$path_to_cloak = ROOT_LK_DIR . $config_lk['skin']['path_to_cloak'] . $server. $username . '.png';
	} else {
		if ( preg_match('/^[a-z0-9\_\/\.\-]{0,50}$/si', $_GET['catalog']) ) {
			
			if ( $_GET['catalog'] == 'catalogSkins' ) {
				$path_to_skin = ROOT_LK_DIR . '/' . $config_lk['skin']['catalog'] . '/' . $username . '.png';
				$path_to_cloak = '';
			} else {
				$path_to_skin = ROOT_LK_DIR . $_GET['catalog'] . $username . '.png';
				$path_to_cloak = '';
			}
		}
		
		$server = 'catalogSkins_';
	}
	
	if ( !file_exists($path_to_skin) ) {
		$path_to_skin = ROOT_LK_DIR . $config_lk['skin']['path_to_skin'] . $server . 'default.png';
	}
	
	if ( !file_exists($path_to_cloak) ) {
		$path_to_cloak = false;
	}
	
	if ( $config_lk['skin']['cache'] ) {
		$cachefile = ROOT_LK_DIR . $config_lk['skin']['path_to_skin'] . $server. $username . '_cache1' . $_GET['mode'] . '2' . $_GET['mode2'] . '.png';
		
		if ( file_exists($cachefile) ) {
			header('Content-type: image/png');
			echo file_get_contents($cachefile);
			exit();
		}
	}
	
	if ( $path_to_skin != false ) $skin = imagecreatefrompng($path_to_skin);
	if ( $path_to_cloak != false ) $cloak = imagecreatefrompng($path_to_cloak);
	
	$skin_size = getimagesize($path_to_skin);
	$h = $skin_size[0];
	$w = $skin_size[1];
	
	if ( $path_to_cloak ) {
		$cloak_size = getimagesize($path_to_cloak);
		$h2 = $cloak_size[0];
		$w2 = $cloak_size[1];
		
		$ratio = $h / 64;
		$ratio2 = $h2 / 64;
		
		if ( $h2 == 22 ) {
			$ratio2 = 1;
		} else {
			$ratio2 = $h2 / 64;
		}
		
		if ( $ratio > $ratio2 ) {
			$ration = $ratio;
		} else {
			$ration = $ratio2;
		}
	} else {
		$ratio = $h / 64;
		$ration = $ratio;
		$ratio2 = 0;
	}
	
	$preview = imagecreatetruecolor(16 * $ration, 32 * $ration);
	$alpha = imagecolorallocatealpha($preview, 255, 255, 255, 127);
	imagefill($preview, 0, 0, $alpha);
	
	if ( !$_GET['mode2'] )
	{
		if ( !$_GET['mode'] )
		{
			//РїР»Р°С‰
			if ( $path_to_cloak != false )
			{
				imagecopyresampled($preview, $cloak, 3 * $ration, 8 * $ration, 12 * $ratio2, 1 * $ratio2, 10 * $ration, 16 * $ration, 10 * $ratio2, 16 * $ratio2);
			}
			
			//РіРѕР»РѕРІР°
			imagecopyresampled($preview, $skin, 4 * $ration, 0 * $ration, 8 * $ratio, 8 * $ratio, 8 * $ration, 8 * $ration, 8 * $ratio, 8 * $ratio);
			
			//РіРѕР»РѕРІР°
			//С‚РµР»Рѕ
			imagecopyresampled($preview, $skin, 4 * $ration, 8 * $ration, 20 * $ratio, 20 * $ratio, 8 * $ration, 12 * $ration, 8 * $ratio, 12 * $ratio);
			//СЂСѓРєРё
			imagecopyresampled($preview, $skin, 0 * $ration, 8 * $ration, 44 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			_imageflip($preview, $skin, 12 * $ration, 8 * $ration, 44 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			//РЅРѕРіРё
			imagecopyresampled($preview, $skin, 4 * $ration, 20 * $ration, 4 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			_imageflip($preview, $skin, 8 * $ration, 20 * $ration, 4 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
		} else {
		
			//РіРѕР»РѕРІР°
			imagecopyresampled($preview, $skin, 4 * $ration, 0 * $ration, 24 * $ratio, 8 * $ratio, 8 * $ration, 8 * $ration, 8 * $ratio, 8 * $ratio);
			//С‚РµР»Рѕ
			imagecopyresampled($preview, $skin, 4 * $ration, 8 * $ration, 32 * $ratio, 20 * $ratio, 8 * $ration, 12 * $ration, 8 * $ratio, 12 * $ratio);
			//СЂСѓРєРё
			imagecopyresampled($preview, $skin, 12 * $ration, 8 * $ration, 52 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			_imageflip($preview, $skin, 0 * $ration, 8 * $ration, 52 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			//РЅРѕРіРё
			imagecopyresampled($preview, $skin, 8 * $ration, 20 * $ration, 12 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			_imageflip($preview, $skin, 4 * $ration, 20 * $ration, 12 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			
			//РїР»Р°С‰
			if ( $path_to_cloak != false )
			{
				imagecopyresampled($preview, $cloak, 3 * $ration, 8 * $ration, 1 * $ratio2, 1 * $ratio2, 10 * $ration, 16 * $ration, 10 * $ratio2, 16 * $ratio2);
			}
		}
	} else {
		
		if ( !$_GET['mode'] )
		{
			
			//РіРѕР»РѕРІР°
			imagecopyresampled($preview, $skin, 4 * $ration, 0 * $ration, 0 * $ratio, 8 * $ratio, 8 * $ration, 8 * $ration, 8 * $ratio, 8 * $ratio);
			//СЂСѓРєР°
			imagecopyresampled($preview, $skin, 6 * $ration, 8 * $ration, 40 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			//РЅРѕРіР°
			imagecopyresampled($preview, $skin, 6 * $ration, 20 * $ration, 0 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			
			//РїР»Р°С‰
			if ( $path_to_cloak != false )
			{
				imagecopyresampled($preview, $cloak, 5 * $ration, 8 * $ration, 0 * $ratio2, 0 * $ratio2, 1 * $ration, 16 * $ration, 1 * $ratio2, 16 * $ratio2);
			}
		} else {
			
			//РіРѕР»РѕРІР°
			imagecopyresampled($preview, $skin, 4 * $ration, 0 * $ration, 16 * $ratio, 8 * $ratio, 8 * $ration, 8 * $ration, 8 * $ratio, 8 * $ratio);
			//СЂСѓРєР°
			imagecopyresampled($preview, $skin, 6 * $ration, 8 * $ration, 40 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			//РЅРѕРіР°
			imagecopyresampled($preview, $skin, 6 * $ration, 20 * $ration, 8 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
			
			//РїР»Р°С‰
			if ( $path_to_cloak != false )
			{
				imagecopyresampled($preview, $cloak, 10 * $ration, 8 * $ration, 11 * $ratio2, 0 * $ratio2, 1 * $ration, 16 * $ration, 1 * $ratio2, 16 * $ratio2);
			}
		}
	}
	
	
	$fullsize = imagecreatetruecolor(90 * $config_lk['skin']['zoom_k'], 180 * $config_lk['skin']['zoom_k']);
	
	imagesavealpha($fullsize, true);
	$alpha = imagecolorallocatealpha($fullsize, 255, 255, 255, 127);
	imagefill($fullsize, 0, 0, $alpha);
	
	imagecopyresized($fullsize, $preview, 0, 0, 0, 0, imagesx($fullsize), imagesy($fullsize), imagesx($preview), imagesy($preview));
	
	ob_start();
	header('Content-type: image/png');
	imagepng($fullsize);
	imagedestroy($preview);
	imagedestroy($fullsize);
	imagedestroy($skin);
	if ( $path_to_cloak != false ) imagedestroy($cloak);
	
	if ( $config_lk['skin']['cache'] ) {
		file_put_contents(ROOT_LK_DIR . $config_lk['skin']['path_to_skin'] . $server. $username . '_cache1' . $_GET['mode'] . '2' . $_GET['mode2'] . '.png', ob_get_contents());
	}
	
	function _imageflip(&$result, &$img, $rx = 0, $ry = 0, $x = 0, $y = 0, $size_x2 = null, $size_y2 = null, $size_x = null, $size_y = null)
	{
		if ( $size_x < 1 ) $size_x = imagesx($img);
		if ( $size_y < 1 ) $size_y = imagesy($img);
	 
		imagecopyresampled($result, $img, $rx, $ry, ($x + $size_x - 1), $y, $size_x2, $size_y2, -$size_x, $size_y);
	}
?>