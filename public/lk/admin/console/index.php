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

	session_start();
	
	define ( 'ROOT_LK_DIR', dirname ( __FILE__ ) . '/../..' );
	
	include(ROOT_LK_DIR . '/config.php');
	include(ROOT_LK_DIR . '/class/language.class.php');
	include(ROOT_LK_DIR . '/class/_user.class.php');
	include(ROOT_LK_DIR . '/class/lk.class.php');
	
	$lk = new lk( $config_lk );
	$admin_cfg = $lk->cfg['admin']['login'];
	sleep($admin_cfg[2]);
	
	if ( $lk->user['admin'] == false && (!isset($_GET['pass']) || $_GET['pass'] != $admin_cfg[0]) ) die($lk->lang->error('incorrectPass'));
	
	if ( $lk->user['admin'] == false ) {
		$lk->makeAdmin();
	}
	
	$ip = ($admin_cfg[1] == false ? true : false);
	
	if ( !$ip ) {
		foreach ( $admin_cfg[1] as $val ) {
			if ( $_SERVER['REMOTE_ADDR'] == $val ) {
				$ip = true;
				break;
			}
		}
		if ( !$ip ) die($lk->lang->error('undefinedIP'));
	}
?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="req.class.js"></script>
	</head>

	<body>
		<div class="body">
			<h3 align="center">Админ-консоль Личного Кабинета</h3>
			<div id="log">
				<div>Добро пожаловать в админ-панель! Для просмотра всех команд, введите <b>-c help</b>.</div>
				<div>Управление клавиатурой: <b>enter</b> - отправить команду, <b>вверх/вниз</b> - история введенных команд</div>
				<div>Примеры ввода команд: <b>give money dima 500</b>; <b>delete ban 1 Vasya1</b>; <b>set group 0 sanya565 1 30</b></div>
			</div>
			<input type="text" id="cmd" placeholder="Введите команду, например -c help"/><input type="button" class="input_send" onclick="sendCMD()" value="Отправить"/>
		</div>
	</body>
</html>

<style>
	.body {
		margin: 0 auto;
		width: 600px;
		font-family: calibri;
	}
	
	#log {
		padding: 15px;
		background: #171717;
		font-size: 14px;
		color: #C9C9C9;
		height: 300px;
		overflow: auto;
	}
	
	#log div {
		margin: 3px 0px;
	}
	
	#cmd {
		outline: 0;
		height: 30px;
		padding: 5px;
		width: 500px;
		border: none;
		background: #171717;
		color: #fff;
	}
	
	.input_send {
		width: 100px;
		height: 30px;
		border: none;
		outline: 0;
		background: #424242;
		color: #fff;
		cursor: pointer;
	}
</style>

<script type="text/javascript">
	var cmds = '<br/><br/>\
		<b>-c [Команда]</b> - команда, обрабатываемая только браузером. Она не отправляется серверу. Например -c help.<br/>\
		<b>-p [Тип команды] [Команда]</b> - команда, которая отправляется серверу на обработку. Например: -p give money sashok567 50. Указаетль -p в отличии от -c указывать не обязательно.<br/><br/>\
		Всего 4 типа команд: <b>get</b>(g) - что-то узнать/получить, <b>set</b>(s) - что-то кому-то установить(группу), <b>give</b> - что-то кому-то дать(деньги), <b>delete</b>(d, del) - что-то у кого-то удалить(бан)<br/><br/>\
		<p style="color: #97AA97">Если команда принимает значение в виде последовательности символов(например словосочетание или предложение), то пробелы там обязательно заменять нижней чертой(_). Например: you_have_got_140_rub, rule_3.5</p><br/>\
		<p style="color: #DADAD0"><b>-p get info [Ник]</b> - информация об игроке.<br/>\
		<b>-p get auth [Ник]</b> - авторизоваться в аккаунте пользователя(Только для движков DLE, WebMCR).<br/>\
		<b>-p give money [Ник] [Сумма]</b> - дать деньги игроку.<br/>\
		<b>-p give icmoney [ID сервера] [Ник] [Сумма]</b> - дать iConomy монеты игроку.<br/>\
		<b>-p set group [ID сервера] [Ник] [ID группы] [Время в днях]</b> - установить группу(статус) игроку.<br/>\
		<b>-p set ban [ID сервера] [Ник] [Причина] [Кол-во дней] [Забанил(по умолчанию admin)]</b> - забанить игрока.<br/>\
		<b>-p set vaucher [Ваучер] [Функции] [Сообщение]</b> - установить ваучер. Подробнее внизу.<br/>\
		<b>-p delete group [ID сервера] [Ник]</b> - удалить игрока из группы.<br/>\
		<b>-p delete ban [ID сервера] [Ник]</b> - разбанить игрока</p><br/>\
		<p style="color: #DCE8FF">ID сервера - это порядковый номер сервера в конфиге. Если сервер в конфиге стоит первым, его ID равен нулю. Соответственно, следующий после него сервер имеет ID на один больше, то есть 1.</p>\
		<p style="color: #AAA997">Ваучер - это промокод, при вводе которого в ЛК выдается заранее указанный в нем приз(деньги, статус, префикс, разбан или даже какой-нибудь предмет.)<br/>Чтобы добавить ваучер через консоль,\
		необходимо придумать сначала ему код, описание и то, что будет делаться после его ввода(выдаваться деньги, например).<br/>Команда добавления ваучера такая: <b>-p set vaucher [Ваучер] [Функции] [Сообщение]</b><br/>\
		<b>[Ваучер]</b> - код ваучера. Например: xDjfJF45<br/><b>[Функции]</b> - функции, то есть действия, которые будут выполняться после ввода действительного кода. Если их несколько в одном ваучере, то каждая функция отделяется наклонной чертой(/). Вот список некоторых функций:<br/><br/>\
		<b><i>give_money{[Деньги]}</i></b> - дать деньги игроку. Например: -p set vaucher 244lk351 give_money{140} you_have_got_140_rub!.<br/><b><i>give_moneyIC{[ID сервера],[Деньги]}</i></b> - дать iConomy монеты на указанный сервер.\
		Например: -p set vaucher a2DD5135 give_moneyIC{1,550} you_have_got_550_mon.<br/><b><i>addBlockToShop{"[Таблица]",[ID магазина],"[ID предмета(123, 5:2)]",[Кол-во]}</i></b> - дать игроку предмет на склад магазина(только магазин от Fleynaro). Например: -p set vaucher bLoc3k44 addBlockToShop{"GetItem",1,"5:3",64} you_have_got_block_of_wood<br/>\
		<b><i>Status{[ID сервера],[ID статуса],[Время в днях]}</i></b> - дать статус игроку на указанный сервер. Например: -p set vaucher 244lk351 Status{0,2,30} you_have_got_Premium_for_30_days!<br/><b><i>Prefix{[ID сервера],[ID цвета префикса],"[Текст префикса]",[ID цвета ника],[ID цвета сообщения]}</i></b> - установить префикс игроку. ID цветов указан ниже. Например:\
		-p set vaucher aBa23k35s1 Prefix{1,3,"VIP",8,2} you_have_got_prefix!<br/><br/><b>[Сообщение]</b> - сообщение на английском, выводиоме после ввода кода. Пробелы обязательно заменять нижней чертой. Например: You_have_got_150_RUB!</p>\
		<p style="color: #A5ADBE">ID цветов Java в ЛК в порядке возрастания(Всего их 16, где "f" - это цвет, ID которого 0, а у "e" - ID 15): <b>f, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, a, b, c, d, e</b></p>\
		<p style="color: #C4B1B1">Вы можете также отправить команду и через GET запрос в адресной строке. &cmd=give money Fleynaro 100</p>\
	';
	
	var history = new Array(), history_count = 0, his = 0;
	
	var req = new _req('../admin_cmd.php', 'key=0');
	
	var _cmd = document.getElementById('cmd');
	
	_cmd.onkeydown = function(e) {
		
		switch ( e.keyCode )
		{
			case 13: {
				sendCMD();
				break;
			}
			
			case 40: {
				if ( his < history_count - 1 ) {
					_cmd.value = history[++ his];
				}
				break;
			}
			
			case 38: {
				if ( his > 0 ) {
					_cmd.value = history[-- his];
				}
				break;
			}
		}
		
	};
	
	function sendCMD() {
		
		var cmd_text = _cmd.value;
		var cmd = cmd_text.split(' ');
		_cmd.value = '';
		history[history_count ++] = cmd_text;
		his = history_count;
		
		if ( cmd[0] == '-c' ) {
			
			switch ( cmd[1] )
			{
				case 'help': {
					addMsg(cmds);
					break;
				}
				
				default: {
					addMsg('JS: Команда не распознана!');
				}
			}
			
		} else {
			addMsg('Команда <b>' + cmd_text + '</b> отправлена.');
			req.send_post({cmd: cmd_text}, function( json ) {
			
				if ( json.status == 'success' ) {
					
					addMsg( json.message )
					
				} else {
					if ( json.type == 1 ) {
						addMsg( '<span style="color: #ff0000">'+ json.message +'</span>' );
					} else addMsg('JS: Команда не распознана!');
				}
			});
		}
	}
	
	function addMsg( msg ) {
		var e = document.getElementById('log');
		e.innerHTML += '<div>'+ msg +'</div>';
		e.scrollTop = 999;
	}
	
	function send( cmd ) {
		if ( !confirm('Вы действительно хотите отправить команду: '+ cmd +'?') ) return 1;
		_cmd.value = cmd;
		sendCMD();
	}
	
	<?php
		if ( isset($_GET['cmd']) ) {
			echo '
				send("'. $_GET['cmd'] .'");
			';
		}
	?>
</script>