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
	$lk_messages = array(
		
		//Сообщения, появляющиеся после успешной какой-либо операции
		'success'		=>	array(
			
			'loadedSkin'			=>	"Скин загружен.",
			'loadedSkinHD'			=>	"HD скин загружен.",
			'loadedCloak'			=>	"Плащ загружен.",
			'loadedCloakHD'			=>	"HD плащ загружен.",
			'setGroup'				=>	"Игроку %1% успешно установлена группа %2%",
			'setBan'				=>	"Игрок %1% успешно забанен на %2%дн по причине %3%",
			'setVaucher'			=>	"Ваучер %1% с кодом %2% и с сообщением %3% успешно добавлен",
			'giveMoney'				=>	"Игроку %1% успешно выданы %2%",
			'giveMoneyIC'			=>	"Игроку %1% успешно выданы %2% монет",
			'deleteGroup'			=>	"Игрок успешно удален из группы",
			'deleteBan'				=>	"Игрок успешно забанен"
			
		),
		
		//Системные сообщения
		'system'		=>	array(
			
			'notMsg' 				=>	"*** не найдено сообщение ***",
			'pleaseWait'			=>	"Пожалуйста, подождите %1% секунд..."
			
		),
		
		//Ошибки
		'errors'		=>	array(
			
			'error'					=>	"Произошла ошибка",
			'dontHaveRight'			=>	"У вас нет прав для совершения данного действия",
			'incorrectKey'			=> 	"Неверный ключ",
			'money'					=>	"Не хватает денег",
			'loadedSkin'			=>	"Скин не загружен",
			'incorrectSkinSize'		=>	"Неверные размеры скина",
			'incorrectCloakSize'	=>	"Неверные размеры плаща",
			'incorrectFormat'		=>	"Неверный формат загружаемого файла. Нужен формат .png",
			'hasNotFile'			=>	"Файл отсутствует",
			'notBuyStatus'			=>	"На этот сервер данный статус купить нельзя",
			'serverOff'				=>	"Сервер выключен, или у Вас нет прав",
			'statusHasnotBought'	=>	"Статус не куплен",
			'notExtendStatus'		=>	"Продление статуса на данном сервере невозможно",
			'incorrectPrefix'		=>	"Неверный формат префикса, запрещенный префикс или у вас нет прав для установки префикса",
			'incorrectExchange'		=>	"Число слишком большое, или у вас нет прав производить обмен на данном сервере",
			'incorrectWarp'			=>	"Неверные введенные данные для создания варпа, или у вас нет прав для создания варпа",
			'warpHasnotCreated'		=>	"Варп не создался",
			'warpHasnotEdited'		=>	"Варп не создался/отредактировался",
			'occupiedName'			=>	"Данное имя уже занято. Придумайте другое",
			'maxWarp'				=>	"Можно создать максимум %1% варпов",
			'notYourWarp'			=>	"Не ваш варп",
			'hasnotBan'				=>	"Вы не забанены",
			'notVaucher'			=>	"Такого ваучера нет",
			'incorrectVaucher'		=>	"Ваучер введен некорректно",
			'notRight'				=>	"Несуществующее право",
			'hasPexRight'			=>	"Данное право у вас уже имеется",
			'needAuth'				=>	"Необходима авторизация",
			'tplHasnotFound'		=>	"Шаблон не найден",
			'playerNotFound'		=>	"Игрок не найден",
			'undefinedCMD'			=>	"Команда не распознана",
			'undefinedTypeCMD'		=>	"Не указан ти команды(get, give, set, delete)",
			'incorrectCMD'			=>	"Команда введена некорректно",
			'incorrectPass'			=>	"Неверный пароль",
			'undefinedIP'			=>	"Ваш ip не попадает в указанный диапазон ip адресов пользователей, которым разрешен вход в админ-панель"
			
		),
		
		//HTML код
		'html'		=>	array(
		
			'banAllServer'			=>	'<div id="lk-body-alert" style="background: #FFADAD;border: 1px solid #EC5C5C;" onclick="lk.anim.hide(this)" title="Кликните, чтобы закрыть.">Вы бали забанены на сервере %1% по при причине: <b>%2%</b></div>',
			'banServer'				=>	'<div id="lk-body-alert" style="background: #FFADAD;border: 1px solid #EC5C5C;" onclick="lk.anim.hide(this)" title="Кликните, чтобы закрыть.">Вы бали забанены на сервере по при причине: <b>%1%</b></div>',
			'icMoney'				=>	'<div><b>%1%</b> <span class="lk-cur-iconomy-image" title="монета iConomy"></span> <span id="lk-icmoney-%2%-1">%3%</span> монет</div>',
			'pexRightList'			=>	'<table class="lk-pexright" id="lk-pexright_table-%1%" %2%>%3%</table>',
			'pexRightListNot'		=>	'<div id="lk-pexright_table-%1%" %2% align="center">На данном сервере нельзя покупать права.</div>',
			'warpBegin'				=>	'<div id="lk-warp_table-%1%" %2%>',
			'warpOutput'			=>	'<table class="lk-warp">%1%</table>',
			'warpHasnot'			=>	'<p align="center">Нет варпов на данном сервере. Вы можете создать их.</p>',
			'warpCreatePay'			=>	'<p align="center"><button class="lk-button-1" onclick="lk.warpAlert({create: true})">Создать варп за %1%</button></p>',
			'warpCreateFree'		=>	'<p align="center"><button class="lk-button-1" onclick="lk.warpAlert({create: true})">Создать варп</button></p>',
			'topVote'				=>	'<a target="_blank" href="%1%"><img src="%2%" alt="%3%" title="%3%"></a> ',
			'serverOption'			=>	'<option value="%1%">Сервер %2%</option>',
			'always'				=>	'Навсегда',
			'alwaysRight'			=>	'Данное право приобретено навсегда',
			'cutYear'				=>	'г',
			'cutDay'				=>	'д',
			'cutHour'				=>	'ч',
			'untilTime'				=>	'До %1%',
			'alwaysBought'			=>	'Куплено навсегда',
			'notBan'				=>	'Вы не забанены ни на одном сервере',
			'getPlayerInfo_servers'	=>	'<b>Сервера:</b><br/>',
			'getPlayerInfo'			=>	'<br/><br/><b>Ник:</b> %1%<br/><b>Денег:</b> %2%<br/>%3%',
			'getPlayerInfo_server'	=>	'%1%: %2% (До %3%) | Префикс: %4% | Разбанен: %5% раз(а) | iConomy: %6% монет | Забанен: %7%<br/><br/>'
		),
		
		//Лог
		'log'		=>	array(
			
			'loadSkin'				=>	'загрузил скин',
			'loadSkinHD'			=>	'загрузил HD скин',
			'loadCloak'				=>	'загрузил плащ',
			'loadCloakHD'			=>	'загрузил HD плащ',
			'removeSkin'			=>	'удалил скин/плащ',
			'buyStatus'				=>	'купил статус %1% на %2% дней',
			'extendStatus'			=>	'продлил статус %1% на %2% дней',
			'setPrefix'				=>	'установил префикс %1%%2%',
			'exchange0'				=>	'совершил обмен: %1% => %2% IC монет',
			'exchange1'				=>	'совершил обмен: %1% IC монет => %2%',
			'createWarpPay'			=>	'платно создал варп %1% за %2%',
			'editWarp'				=>	'создал/отредактировал варп %1%',
			'unban'					=>	'разбанился за %1%',
			'vaucher'				=>	'ввел ваучер %1% ID:%2% и получил %3%',
			'buyRight'				=>	'купил право "%1%" ID:%2% за %3%руб',
			'buyPexRight'			=>	'купил pex право "%1%" ID:%2% за %3%руб на срок %4% часов',
			'sendCMD'				=>	'отправил команду %1%',
			'removeGroupByCron'		=>	'был убран из группы "%1%" cron системой'
			
		),
	
	);
?>