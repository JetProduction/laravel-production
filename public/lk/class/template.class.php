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

	class Template {

		private $tplPath;
		private $tplFormat = '.tpl';
		private $data = array();
		public $global_data;
		private $tplCode = array(
			'patterns' 		=> array(
				'/{(\w+)}/i'
			),
			
			'replaces' 		=> array(
				'<?php echo$this->$1?>'
			)
		);

		public function __construct( $tplPath, &$global_data ) {
			$this->tplPath = $tplPath;
			
			if ( !file_exists($this->getPath()) ) {
				die('Ошибка: шаблон <b>'. $this->getPath() .'</b> не найден!');
			}
			
			$this->global_data = &$global_data;
		}

		public function set( $name, $value ) {
			if ( $name != 'data' || !is_array($value) ) {
				$this->data[$name] = $value;
			} else {
				$this->data = $value;
			}
		}
		
		public function set_global( $name, $value ) {
			$this->global_data[$name] = $value;
		}

		public function delete( $name ) {
			unset($this->data[$name]);
		}

		public function __get( $name ) {
			if ( isset($this->data[$name]) ) {
				return $this->data[$name];
			} else if ( isset($this->global_data[$name]) ) {
				return $this->global_data[$name];
			} else {
				return false;
			}
		}
		
		public function getPath() {
			return $this->tplPath . $this->tplFormat;
		}

		public function getCrudeContent() {
			ob_start();
			include($this->getPath());
			return ob_get_clean();
		}
		
		public function getContent() {
			$content = $this->getCrudeContent();
			echo $this->complile($content);
		}
		
		public function compile( $content ) {
			$content = preg_replace($this->tplCode['patterns'], $this->tplCode['replaces'], $text);
			echo $content;
		}
		
		public function display() {
			echo $this->getCrudeContent();
		}
}


?>