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
	class Language {
		public $messages = array();
		public $path;
		
		public function __construct( $path ) {
			$this->path = $path;
			if ( file_exists($path) ) {
				include($path);
				$this->messages = $lk_messages;
			} else {
				die('Error: language pack has not been in the lang folder!');
			}
		}
		
		public function success($name, $array = array()) {
			return $this->message('success', $name, $array);
		}
		
		public function system($name, $array = array()) {
			return $this->message('system', $name, $array);
		}
		
		public function error($name, $array = array()) {
			return $this->message('errors', $name, $array);
		}
		
		public function html($name, $array = array()) {
			return $this->message('html', $name, $array);
		}
		
		public function log($name, $array = array()) {
			return $this->message('log', $name, $array);
		}
		
		public function message($cat, $name, $array) {
			if ( isset($this->messages[$cat][$name]) ) {
				$message = $this->messages[$cat][$name];
				for ( $i = 0, $Max = count($array); $i < $Max; $i ++ ) {
					$message = str_replace('%'. ($i + 1) .'%', $array[$i], $message);
				}
				return $message;
			} else {
				return $this->messages['system']['notMsg'];
			}
		}
	}
?>