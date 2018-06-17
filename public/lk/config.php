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

	
	$config_lk = array(
	
		/*_________ПОДКЛЮЧЕНИЕ К БД_________*/
		'db' =>	array (
		
			'host'	=> 'localhost',
			'name'	=> 'laravel',
			'user'	=> 'root',
			'pass'	=> '',
			'char'	=> 'cp1251'
			//utf8 или cp1251
		),
		
		/*_________НАСТРОЙКА СОВМЕСТИМОСТИ С ДВИЖКОМ_________*/
		'cms' => array (
            //Laravel Framework
            'cms_id'		=> 1,
            't_users' 		=> 'dle_users',
            'c_userid'		=> 'id',
            'c_group'		=> 'group',
            'c_name'		=> 'name',
            'c_money'		=> 'money'

			/*// DLE
			'cms_id'		=> 1,
			't_users' 		=> 'dle_users',
			'c_userid'		=> 'id',
			'c_group'		=> 'group',
			'c_name'		=> 'name',
			'c_money'		=> 'money'

			
			/*
				//WebMCR
				'cms_id'		=> 2,
				't_users' 		=> 'accounts',
				'c_userid'		=> 'id',
				'c_group'		=> 'group',
				'c_name'		=> 'login',
				'c_money'		=> 'money'
				
				//XenFORO
				'cms_id'		=> 3,
				't_users' 		=> 'xf_user',
				'c_userid' 		=> 'user_id',
				'c_group'		=> 'user_group_id',
				'c_name' 		=> 'username',
				'c_money' 		=> 'money'
				
				//WordPress
				'cms_id'		=> 4,
				't_users' 		=> 'wp_users',
				'c_userid' 		=> 'ID',
				'c_group'		=> 'user_status',
				'c_name' 		=> 'user_login',
				'c_money' 		=> 'money'
				
				//AuthMe
				'cms_id'		=> 5,
				't_users' 		=> 'authme',
				'c_userid' 		=> 'id',
				'c_group'		=> 'group',
				'c_name' 		=> 'username',
				'c_money' 		=> 'money'
			*/
		),
		
		/*_________НАСТРОЙКА ЛК_________*/
		//КОДИРОВКА ВАШЕГО САЙТА. utf-8 или windows-1251
		'charset'	=>	'utf-8',
		
		//Языковой пакет
		'language'	=>	'lang_RU.php',
		
		//Шаблон ЛК
		'template'	=>	'default',
		
		//Выбор шаблона через GET запрос ?tpl=[Имя папки с шаблоном] (Полезно, если Вы хотите предоставить пользователю выбор шаблона ЛК)
		'selectTpl'	=>	true,
		
		//Тип отображения ЛК. auto - определяется автоматически(v1.4.5+), 	'путь/' - путь от корня до папки ЛК, например "lk/" или "modules/lk/",		false - выключить, 	true - включить
		'insite'	=>	'auto',//оставьте auto
		
		//Встраивание в шаблон через тег iframe.
		'iframe'	=>	false,
		
		//Ширина ЛК
		'width'		=>	'590px',
		
		
		//Валюта в ЛК
		'cur'		=> 	array('RUB', 'руб', 'рублей', 'рубля', 'рубль', 'р'),
		//Стоимость 1 единицы валюты ЛК
		'cur_price'	=>	1, //RUB/USD. По умолчанию курс такой: 1 рубль ЛК = 1 RUB
		
		//Антиддос. Сильно не защитит, но нагрузку снизит. Указывать время в сек, которое придется ждать между запросами. false - выкл.
		'anti_ddos'	=>	3,
		
		/*_________СКИНЫ И ПЛАЩИ_________*/
		'skin'		=> array(
				//Мультисерверная загрузка/просмотр скинов и плащей. При этом в папке upload/skins должны быть папки server_0, server_1, server_2 и т.д в зависимости от кол-ва серверов, куда будут загружаться скины
				'multi_enable'	=> false,
				//При значении false можно загружать HD скины, например, на любой сервер, если хотя бы на одном из серверов есть статус Premium или куплено право на загрузку HD скина. true - отключает эту возможность.
				'status'		=> false,
				//Путь до скинов
				'path_to_skin'	=> '/upload/skins/',
				//Путь до плащей
				'path_to_cloak'	=> '/upload/cloaks/',
				//Включить 3D скины. Если браузер клиента не имеет такой возможности, то будет показан 2D скин(если 2D скины включены)
				'skin3D'		=> true,
				//Включить 2D скины. Если вы планируете использовать ТОЛЬКО 3D скины, то в предыдущем параметре поставьте true, а здесь false. В идеале лучше включить и 2D, и 3D просмотр.
				'skin2D'		=> true,
				//Увеличение скинов
				'zoom_k'		=> 1,
				//Макс. размер загружаемого скина
				'max_size_mb'	=> 3,
				//Кеш скинов
				'cache'			=> false,
				
				//КАТАЛОГ СКИНОВ. Путь до папки со скинами. false - выключить
				'catalog'		=> 'upload/catalogSkins',
				//Макс. кол-во скинов на 1 страницу (Выгруженные за раз)
				'max_skins'		=> 10
		),
		
		/*_________ПРАВА_________*/
		'rights'		=> array(
				//Включить возможность покупки прав
				'enable'		=> true,
				//Права
				'right'			=> array(
					
					//    Возможность           Название права       колонка           цена    включить
					array('загрузить HD скин', 	'upload_hd_skin',	'lk_hdskin',		30,		true),
					array('загрузить плащ', 	'upload_cloak',		'lk_cloak',			25,		true),
					array('загрузить HD плащ', 	'upload_hd_cloak',	'lk_hdcloak',		35,		true),
					array('установить префикс', 'set_prefix',		'lk_prefix',		50,		true),
					array('создать варп', 		'create_warp',		'lk_warp',			100,	true)
					
				)
		),
		
		/*_________ПРЕФИКС_________*/
		'prefix'	=> array(
				
				//Минимальная длина префикса
				'prefix_min_len'	=> 3,
				//Максимальная длина префикса
				'prefix_max_len'	=> 10,
				//Текст сообщения в предосмотре префикса
				'prefix_text'		=> 'Привет, ребята!',
				//Запрещенные префиксы через |
				'prefix_ban'		=> 'a.*d.*m.*i.*n|h.*e.*l.*p.*e.*r|m.*o.*d.*e.*r',
				//Брать префикс прямо из таблицы permissions, а не кеш из колонки server_n в таблице с юзерами
				'prefix_perm'		=> true,
				//Формат префикса
				'prefix_format'		=> array(
					
					'&0[ ', /* ПРЕФИКС */  ' &0] ', 	'', /* НИК */ '' 	/* СООБЩЕНИЕ */
					
					//Например: &0[ &2Admin &0] &6Fleynaro&3: hello, world!
				)
			
		),
		
		/*_________ДРУГОЕ_________*/
		'other'	=> array(
				
				//MyWarp  	v2.6.5 без UUID
				'warp'	=>	array(
					//Вкл/Выкл систему варпов в ЛК
					'enable'		=>	true,
					//Платное создание варпов
					'paid'	=>		array(
						'enable'	=>	true,	//Вкл/Выкл ПЛАТНОЕ создание варпов
						'price'		=>	25,		//Цена создания платного варпа в рублях
						'free_count'=>	1		//Сколько можно создать бесплатных варпов. 0 - ни сколько
					),
					//Мин и Макс длина названия варпа
					'len_name'		=>	array(3, 10),
					//Мин и Макс длина сообщения приветствия при тп на варп
					'len_message'	=>	array(5, 30),
					//Макс. число варпов
					'max_warps'		=>	5,
					//Название мира
					'world'			=>	'world',
					//Если таблица с варпами находится в общей БД(где сайт), то true
					'list_warps'	=> 	false,
					//ID сервера по дефолту, из которого будет браться информация из указанной там таблицы с варпами. Параметр обязателен, если в предыдущем стоит true
					'default_server'=> 	0,
					
				),
				
				'unban'	=>	array(
					
					//Вкл/Выкл систему разбана в ЛК
					'enable'		=>	true,
					//Начальная цена разбана в ЛК
					'price'			=>	100,
					//На сколько дороже будет начальная цена разбана при след. разбане
					'price_next'	=>	50,
					//UUID - true/false. Чтобы uuid было активно, сервер в конфиге должен содержать параметр uuid со значением true.
					'uuid'			=> 	false,
					//Как использовется система разбанов: false - бан на каждом сервере по отдельности, true - бан на всех серверах.
					'unban_all'		=>	false,
					//ID сервера по дефолту, из которого будет браться информация из указанной там таблицы banlist. Параметр обязателен, если в предыдущем стоит true
					'default_server'=> 	0,
					//Инфо таблицы с банами. По умолчанию настроено на UltraBans MC v1.4.7
					'table'			=>	array(
						//Колонка с ником забаненного
						'name'		=>	'name',
						//Колонка с ником того, кто забанил(админ)
						'admin'		=>	'admin',
						//Колонка с временем, когда забанили
						'time'		=>	'time',
						//Колонка с причиной бана
						'reason'	=>	'reason'
					)
				),
				
				'vaucher' =>	array(
					
					//Вкл/Выкл систему ваучеров
					'enable'		=>	true,
					//Длина ваучера. Мин и Макс
					'len'			=>	array(3, 10)
				),
				
				'top' =>	array(
					
					//Вкл/Выкл отображение иконок топов
					'enable'		=>	true,
					
					//Описание
					'desc'			=>	'Голосуя, вы получаете рубли!',
					
					//Топы
					'tops'			=>	array(
						
						//Название, 	Ссылка на страницу с сервером в Топе,			Ссылка на картинку топа
						array('McRate', 'http://mcrate.su/rate/', 'http://mcrate.su/bmini.png'),
						array('TopCraft', 'http://topcraft.ru/servers/', 'http://img-fotki.yandex.ru/get/6727/63518323.1/0_be533_32c33617_S.png'),
						array('McTop', 'http://mctop.pro/vote/0-vote.html', 'http://mctop.pro/banners/6.png')
						
					)
				)
			
		),
		
		/*_________ОБМЕНЫ_________*/
		'exchange'	=> array(
				
				//iConomy 		до v7.0 включительно
				'iconomy'	=>	array(
					//Вкл/Выкл обмен валюты iConomy в ЛК 
					'enable'		=>	true,
					//Сколько стоит 1 единица валюты iconomy в валюте ЛК(руб). 0 - выкл. обмен
					'price_cur'		=>	0.001,//или 1/1000
					//Сколько стоит 1 единица вашей валюты в монетах iConomy. 0 - выкл. обмен
					'price_u_cur'	=> 	5,
					//Если у Вас одна таблица iConomy для всех серверов, находящиеся на сайте, то ставьте здесь true
					'ic_money_viem'	=>	false,
					//ID сервера по дефолту, из которого будет браться информация из указанной там таблицы iConomy. Параметр обязателен, если 	'ic_money_viem'	=>	true
					'default_server'=> 	0
				)
			
		),
		
		/*_________ПЛАТЕЖНАЯ СИСТЕМА_________*/
		'payment'	=> array(
				//Если вы собираетесь использовать платежную ситему в магазине, то вам необходимо настроить также конфиг файл payment/config.php
				
				//Включить ли платежную система в ЛК?
				'enable'	=>	true,
				//Платежная ситема. 1 - InterKassa,  2 - UnitiPay,  3 - RoboKassa
				'type'	=>	3,
				//Путь к файлу payment.php
				'path'			=> 'payment/payment.php',
				//Укажите валюту (RUB|USD) или оставьте пустое поле
				'cur'			=> 'RUB', //Для робокассы не RUB, а RUR нужно указывать
				//Имя валюты (руб.|$)
				'curname'		=> 'руб.',
				//Описание
				'desc'			=> 'payment!',
				//Метод отправки данных файлу payment.php: GET или POST
				'method'		=> 'GET',
				 //GET запросы с индексами. Настраивать ничего не нужно.
				'params'		=> array (
			
					array ( //INTERKASSA
						'sum' => 'ik_am',
						'desc' => 'ik_desc',
						'user' => 'ik_pm_no',
						'cur' => 'ik_cur'
					),
					
					array ( //UNITIPAY
						'sum' => 'up_sum',
						'desc' => 'up_desc',
						'user' => 'up_account',
						'cur' => 'up_currency'
					),
					
					array ( //ROBOKASSA
						'sum' => 'rk_OutSum',
						'desc' => 'rk_InvDesc',
						'user' => 'rk_shp_userid',
						'cur' => 'rk_OutSumCurrency'
					),
					
					array ( //FREEKASSA
						'sum' => 'fk_oa',
						'desc' => '',
						'user' => 'fk_us_id',
						'cur' => ''
					)
				)	
			
		),
		
		/*_________ЛОГ ФАЙЛ_________*/
		'log'	=> array(
				
				//Включить лог систему в ЛК
				'enable'	=>	true,
				//Путь до файла, куда будет производиться запись логов
				'path'		=>	'/upload/secret/log.txt',
				//Макс. размер лог файла в кБ, после достижения которого лог будет очищаться
				'clear'		=>	1024
			
		),
		
		/*_________UUID_________*/
		'uuid'	=> array(
				
				//Генерировать стандартным методом. Если false, то будет брать UUID из колонки в таблице с пользователями или указанной внизу таблице
				'generate'	=>	true,
				//Черточки у UUID (true: 86a3fe83-9cc5-3b97-8309-e518da73e725  или  false: 86a3fe839cc53b978309e518da73e725)
				'underline'	=>	true,
				//Имя колонки UUID в    таблице с пользователями/указанной внизу таблице
				'column'	=>	'uuid',
				//Таблица
				'table'		=>	array(
					
					'enable'		=>	false,		// Вкл\Выкл взятие UUID из другой таблицы, а не из users(или dle_users, accounts и т.д)
					'tablename'		=>	'userUUID',	//Имя таблицы с UUID пользователей
					'c_username'	=>	'username',	//Имя колонки с никами/ID пользователей
					'byfind'		=>	'name'		//Искать UUID в таблице по нику(name) или ID(id)
				)
			
		),
		
		/*_________СЕРВЕРА. МУЛЬТИСЕРВЕРНОСТЬ_________*/
		'server'	=> array( //Для отключения мультисерверности поставьте первому серверу параметру name значение All
						
				//Сервер Classic
				array
				(
					//Вкл/Выкл данный сервер в ЛК
					'enable'	=> true,
					//Наименование сервера
					'name'		=>	'Classic',
					//UUID 		1.7.9 +
					'uuid'		=> 	true,
					//Добавлять записи при покупке статусов
					'entity'	=>	array('type' => 1, 'default' => -1),
					//Данные для подключения к БД. false - использовать общую БД.
					'db'		=>	array (
						
						'host'	=> 'localhost',
						'name'	=> 'lk',
						'user'	=> 'root',
						'pass'	=> '',
						'char'	=> 'cp1251'
						
					),
					//Таблицы
					'tables'	=> array( 'permissions', 'permissions_entity', 'permissions_inheritance', 'iconomy', 'warptable', 'banlist' ),
					//Статусы(Вкл/Выкл)  Первый true - первый статус.
					'status'	=> array( true, true, true, true, true, true ),
					//Возможности
					'right'		=>	array (
						
						'buy_status'	=>	true,	//Покупка статуса на данном сервере
						'set_prefix'	=>	true,	//Установка префикса на данном сервере
						'buy_right'		=>	true,	//Покупка pex прав
						'extend_status'	=>	true,	//Продление статуса на данном сервере
						'exchange'		=>	true,	//Обмен валютой на данном сервере
						'warp'			=>	true,	//Создать/Редактировать варпы на данном сервере
						'unban'			=>	true	//Разбан на данном сервере
						
					)
				),
				
				//Сервер HiTech
				array
				(
					//Вкл/Выкл данный сервер в ЛК
					'enable'	=> true,
					//Наименование сервера
					'name'		=>	'HiTech',
					//UUID		1.7.9 +
					'uuid'		=> 	true,
					//Добавлять записи при покупке статусов
					'entity'	=>	array('type' => 1, 'default' => -1),
					//Данные для подключения к БД. false - использовать общую БД.
					'db'		=>	false,
					//Таблицы
					'tables'	=> array( 'permissions', 'permissions_entity', 'permissions_inheritance', 'iconomy', 'warptable', 'banlist' ),
					//Статусы(Вкл/Выкл)  Первый true - первый статус.
					'status'	=> array( true, true, true, true, true, true ),
					//Возможности
					'right'		=>	array (
						
						'buy_status'	=>	true,	//Покупка статуса на данном сервере
						'set_prefix'	=>	true,	//Установка префикса на данном сервере
						'buy_right'		=>	true,	//Покупка pex прав
						'extend_status'	=>	true,	//Продление статуса на данном сервере
						'exchange'		=>	true,	//Обмен валютой на данном сервере
						'warp'			=>	true,	//Создать/Редактировать варпы на данном сервере
						'unban'			=>	true	//Разбан на данном сервере
						
					)
				),
				
				//Сервер Magic
				array
				(
					//Вкл/Выкл данный сервер в ЛК
					'enable'	=> true,
					//Наименование сервера
					'name'		=>	'Magic',
					//UUID		1.7.9 +
					'uuid'		=> 	true,
					//Добавлять записи при покупке статусов
					'entity'	=>	array('type' => 1, 'default' => -1),
					//Данные для подключения к БД. false - использовать общую БД.
					'db'		=>	false,
					//Таблицы
					'tables'	=> array( 'permissions', 'permissions_entity', 'permissions_inheritance', 'iconomy3', 'warptable3', 'banlist3' ),
					//Статусы(Вкл/Выкл)  Первый true - первый статус.
					'status'	=> array( true, true, true, true, true, true ),
					//Возможности
					'right'		=>	array (
						
						'buy_status'	=>	true,	//Покупка статуса на данном сервере
						'set_prefix'	=>	true,	//Установка префикса на данном сервере
						'buy_right'		=>	true,	//Покупка pex прав
						'extend_status'	=>	true,	//Продление статуса на данном сервере
						'exchange'		=>	true,	//Обмен валютой на данном сервере
						'warp'			=>	true,	//Создать/Редактировать варпы на данном сервере
						'unban'			=>	true	//Разбан на данном сервере
						
					)
				)
		
		),
		
		/*_________СТАТУСЫ_________*/
		'status'	=> array(
						
				//Группа ИГРОКИ
				array
				(
					//Вкл/Выкл статус
					'enable'	=> true,
					//Наименование статуса
					'name' 		=>	'Игрок',
					//Описание статуса
					'desc'		=>	'Группа "игрок" выдается всем игрокам, принявших участие в проекте.',
					//Наименование статуса в пермишенс
					'name_pex' 	=>	'default',
					//Возможности группы
					'right'		=> 	array (
										
						'upload_skin'		=>	true,	//Загрузка скина
						'upload_hd_skin'	=>	false,	//Загрузка HD скина
						'upload_cloak'		=> 	false,	//Загрузка плаща
						'upload_hd_cloak'	=> 	false,	//Загрузка HD плаща
						'set_prefix'		=> 	false,	//Установка префикса
						'buy_status'		=> 	true,	//Покупка статусов
						'create_warp'		=> 	false,	//Создание варпа
						'buy_unban'			=>	true	//Покупка разбана
										
					),
					//Покупка осуществляется на (в днях)
					'buy_days'	=>	0,
					//Возможность указать кол-во дней
					'set_days'	=> false,
					//Цена
					'price'		=>	0,
					//Скидка в процентах
					'discount'	=>	0
				),
				
				//Группа VIP
				array
				(
					//Вкл/Выкл статус
					'enable'	=> true,
					//Наименование статуса
					'name' 		=>	'VIP игрок',
					//Описание статуса
					'desc'		=>	'Группа "ВИП" покупается и дает больше возможностей и привилегий по сравнению с обычной группой "Игрок"',
					//Наименование статуса в пермишенс
					'name_pex' 	=>	'vip',
					//Возможности группы
					'right'		=> 	array (
										
						'upload_skin'		=>	true,	//Загрузка скина
						'upload_hd_skin'	=>	false,	//Загрузка HD скина
						'upload_cloak'		=> 	true,	//Загрузка плаща
						'upload_hd_cloak'	=> 	false,	//Загрузка HD плаща
						'set_prefix'		=> 	false,	//Установка префикса
						'buy_status'		=> 	true,	//Покупка статусов
						'create_warp'		=> 	false,	//Создание варпа
						'buy_unban'			=>	true	//Покупка разбана
										
					),
					//Покупка осуществляется на (в днях)
					'buy_days'	=>	30,
					//Возможность указать кол-во дней
					'set_days'	=> false,
					//Цена
					'price'		=>	150,
					//Скидка в процентах
					'discount'	=>	25
				),
				
				//Группа Premium
				array
				(
					//Вкл/Выкл статус
					'enable'	=> true,
					//Наименование статуса
					'name' 		=>	'Premium игрок',
					//Описание статуса
					'desc'		=>	'Группа "Premium" является высшей привелегией для игрока.',
					//Наименование статуса в пермишенс
					'name_pex' 	=>	'premium',
					//Возможности группы
					'right'		=> 	array (
										
						'upload_skin'		=>	true,	//Загрузка скина
						'upload_hd_skin'	=>	true,	//Загрузка HD скина
						'upload_cloak'		=> 	true,	//Загрузка плаща
						'upload_hd_cloak'	=> 	true,	//Загрузка HD плаща
						'set_prefix'		=> 	true,	//Установка префикса
						'buy_status'		=> 	true,	//Покупка статусов
						'create_warp'		=> 	true,	//Создание варпа
						'buy_unban'			=>	true	//Покупка разбана
										
					),
					//Покупка осуществляется на (в днях)
					'buy_days'	=>	30,
					//Возможность указать кол-во дней
					'set_days'	=> false,
					//Цена
					'price'		=>	350,
					//Скидка в процентах
					'discount'	=>	0
				)
						
		),
		
		
		/*_________ПРАВА PEX_________*/
		'right_pex'	=>	array(
				
				//Включить покупку прав permissions
				'enable'			=>	true,
				//Если у Вас одна таблица permissions для всех серверов, которая находится на сайте, то ставьте здесь true
				'all_server'		=>	false,
				//ID сервера по дефолту, из которого будет браться информация из указанной там таблицы permissions. Параметр обязателен, если в предыдщем стоит true
				'default_server'	=> 	0,
				
				//Права permissions
				'rights'	=>	array(
						
						//ID сервера(-1  - все сервера),	Право pex, 	Название,	Описание,	Цена,   Срок действия в часах
						array( -1,	'essentials.fly', 'Полет', 'Позволяет летать на сервере.',	100, 	24 * 30 ),
						array( -1,	'essentials.feed', 'Утоление голода', 'Позволяет утолить свой голод.',	80, 	5 ),
						array( 1,	'essentials.repair', 'Починка предметов', 'Позволяет полностью починить предмет.',	90, 	-1 )
				
				)
		),
		
		
		/*_________АДМИН ПАНЕЛЬ_________*/
		'admin'	=> array(
				
				//Включить админ панель admin.php
				'enable'	=>	true,
				
				//Включить админ панель в ЛК
				'lk_search'	=>	true,
				
				//ID групп, которым разрешена админ панель в ЛК(id группы зависит от движка)
				'groups'	=>	array(1),
				
				//Авторизация в админ-консоле
				'login'		=>	array(
						//Пароль
						'123456',
						//Диапозон ip адресов, для которых разрешен вход. false - выключить.
						array('127.0.0.1', '77.43.212.64', '41.42.252.91'),
						//Задержка в секундах для входа в админ-консоль
						1
				),
				
				'pathToAdminCMD'	=>	'admin/admin_cmd.php'
		)
	
	);

	define('TABLE_PERMISSION', 0);
	define('TABLE_PERMISSION_ENTITY', 1);
	define('TABLE_PERMISSION_INHERITANCE', 2);
	define('TABLE_ICONOMY', 3);
	define('TABLE_WARPS', 4);
	define('TABLE_BANLIST', 5);

?>