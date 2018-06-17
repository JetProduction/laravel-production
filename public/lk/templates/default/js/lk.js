
function _lk() {

	this.anim = new _anim();
	this.colorsChat = new Array('ffffff', '000000', '1451A7', '00bf00', '00bfbf', 'bf0000', 'bf00bf', 'bfbf00', 'bfbfbf', '404040', '4040ff', '40ff40', '40ffff', 'ff4040', 'ff40ff', 'ffff40');
	this.colorsChatF = new Array('f', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e');
	this.username = '';
	this.money = 0;
	this.countServer = 0;
	this.iframe = false;
	this.icmoney = new Array();
	this.server = new Array();
	this.status = new Array();
	this.cur = new Array();
	this.prefix = new Array();
	this.iconomy = new Array();
	this.alert_window = false;
	this.linkParams = new Array();
	this.selectSkinServer = 0;
	this.path = '';
	this.MouseCoords = {
		getX: function(e)
		{
			if (e.pageX)
			{
				return e.pageX - (document.documentElement.scrollLeft || document.body.scrollLeft);
			}
			else if (e.clientX)
			{
				return e.clientX+(document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
			}

			return 0;
		},
		
		getY: function(e)
		{
			if (e.pageY)
			{
				return e.pageY - (document.documentElement.scrollTop || document.body.scrollTop);
			}
			else if (e.clientY)
			{
				return e.clientY+(document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
			}

			return 0;
		}
	}
	
	this.init = function() {
		
		_this = this;
		document.getElementById('alert_bg_dark').addEventListener("click", function(e) {
			
			var elem = e.target;
			
			if ( _this.alert_window )
			{
				_this.alert_window_close();
			}
			
		});
		
		var linkParams_ = location.hash.split('#');
		
		for ( var i = 1, Max = linkParams_.length; i < Max; i ++ ) {
			var values = linkParams_[i].split('=');
			this.linkParams[values[0]] = values[1];
		}
		
		
		if ( this.linkParams['page'] != null ) {
			
			if ( 0 < this.linkParams['page'] < 6 ) {
				this.menu(this.linkParams['page']);
			}
			
		}
		
		if ( this.linkParams['alert'] != null ) {
			switch ( this.linkParams['alert'] ) {
				
				case 'catalogSkins': {
					this.catalogSkins();
					break;
				}
				
				case 'buyStatus': {
					this._selectServer = this.linkParams['server'];
					this.selStatus(this.linkParams['id']);
					break;
				}
				
				default: {
					this.alert('Такого диалогового окна нет!', 5000);
				}
				
			}
		}
		
		this.server[this.server.length] = new Array(0, 0, '', new Array(0, 0, 0), 0);
		this.selectServer( document.getElementById('lk-server-select-0'), 0 );
		this.setServerPrefixCode();
	};
	
	this.indexByValue = function(array, value) {
		
		for( var i = 0, Max = array.length; i < Max; i ++ )
			if ( array[i] == value ) return i;
		
		return -1;
	};
	
	this.menu_show_last = 1;
	
	this.menu = function( id ) {
		
		if ( id == this.menu_show_last ) return 1;
		
		var content = document.getElementById('lk-body-block-' + id), elem = document.getElementById('menu-' + id);
		elem.classList.add('lk-menu-nav-active');
		elem.parentNode.children[this.menu_show_last - 1].classList.remove('lk-menu-nav-active');
		this.anim.show(content);
		this.anim.hide(document.getElementById('lk-body-block-' + this.menu_show_last));
		this.anim.attenuation(content);
		
		this.menu_show_last = id;
	};
	
	
	this.skin = function( mouse ) {
		
		if ( mouse == 1 )
		{
			this.anim.hide(document.getElementById('lk-body-block-1-skin-1'));
			this.anim.show(document.getElementById('lk-body-block-1-skin-2'));
		} else {
			this.anim.hide(document.getElementById('lk-body-block-1-skin-2'));
			this.anim.show(document.getElementById('lk-body-block-1-skin-1'));
		}
	};
	
	this.upload_skin = function( type ) {
		
		var efile = document.getElementById('lk-body-block-1-skin-upload_skin'), file = efile.files[0], _this = this;
		var filetype = (type == 1 ? 'Скин' : 'Плащ');
		
		if ( file )
		{
			var progress_bar = document.getElementById('lk-progress-upload'), bar = progress_bar.lastChild;
			this.anim.show(progress_bar);
			this.anim.attenuation(progress_bar);
			
			this.req.upload(file, { type: "upload", type_upload: type, serverid: (efile.previousElementSibling != null ? efile.previousElementSibling.value : -1) }, function(e) {
				
				var value = 100 / (e.total / e.loaded);
				bar.style.width = value + '%';
				bar.innerHTML = value.toFixed(0) + '%';
				
			}, function (e) {
				if ( e.status == 200 )
				{
					var json = JSON.parse(e.responseText);
					
					if ( json.status != 'error' )
					{
						_this.alert(json.message, 3000);
						
						if ( _this.IsSkin3D() ) {
							if ( type == 1 ) {
								_this.UpdateSkin3D(_this.selectSkinServer);
							} else {
								_this.UpdateCloak3D(_this.selectSkinServer);
							}
							_this.anim.attenuation(document.getElementById('skinViewer3D'));
						} else {
							_this.updateSkin();
						}
					} else
						_this.alert('<b>Ошибка:</b> ' + json.message, 5000);
				} else
					_this.alert('Ошибка при загрузке ' + filetype + 'а', 3000);
				
				_this.anim.hide(progress_bar);
			});
		} else {
			this.alert('Загрузите ' + filetype + '!', 3000);
		}	
	};
	
	this.delete_skin = function( type ) {
		
		var filetype = (type == 1 ? 'Скин' : 'Плащ'), efile = document.getElementById('lk-body-block-1-skin-upload_skin');
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({
			type: 'delete',
			type_delete: type,
			serverid: this.selectSkinServer
		}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				_this.alert(filetype + ' удален', 1000);
				
				if ( _this.IsSkin3D() ) {
					if ( type == 1 ) {
						_this.RemoveSkin3D();
					} else {
						_this.skin3D.removeCloak();
					}
					_this.anim.attenuation(document.getElementById('skinViewer3D'));
				} else {
					_this.updateSkin();
				}
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.skin_update = function( server_id ) {
		if ( this.IsSkin3D() ) {
			this.UpdateSkin3D(server_id);
			this.UpdateCloak3D(server_id);
		} else {
			var skin = new Array(document.getElementById('lk-body-block-1-skin-1').children, document.getElementById('lk-body-block-1-skin-2').children);
			for ( var i = 0; i < 2; i ++ ) {
				for ( var j = 0; j < 2; j ++ ) {
					skin[i][j].src = skin[i][j].src.replace(/server=(\d)/, 'server=' + server_id);
				}
			}
		}

		this.selectSkinServer = server_id;
	};
	
	this.UpdateSkin3D = function(serverid) {
		this.skin3D.changeSkin(this.pathToSkins.replace(/server_(\d)/, 'server_' + serverid) + this.username + '.png?update=' + Math.random());
	}
	
	this.UpdateCloak3D = function(serverid) {
		if ( this.skin3D.IsCloak() ) {
			this.skin3D.changeCloak(this.pathToCloaks.replace(/server_(\d)/, 'server_' + serverid) + this.username + '.png?update=' + Math.random());
		} else {
			this.skin3D.createCloak(this.pathToCloaks.replace(/server_(\d)/, 'server_' + serverid) + this.username + '.png');
		}
	}
	
	this.RemoveSkin3D = function(serverid) {
		if ( this.skin3D.GetSkinName() != 'default' ) {
			this.skin3D.changeSkin(this.pathToSkins.replace(/server_(\d)/, 'server_' + serverid) + 'default.png');
		}
	}
	
	this.setSkin = function(name) {
		
		var _this = this;
		this.alert_window_close();
		this.alert('Подождите...');
		
		this.req.send_post({type: 'setSkin', serverid: this.selectSkinServer, name: name}, function( json ) {
				
			if ( json.status == 'success' ) {
				
				_this.alert('Скин из каталога скинов успешно поставлен!');
				if ( _this.IsSkin3D() ) {
					_this.UpdateSkin3D(_this.selectSkinServer);
					_this.anim.attenuation(document.getElementById('skinViewer3D'));
				} else {
					_this.updateSkin();
				}
				
			} else {
				_this.alert('<b>Ошибка:</b> ' + json.message);
			}
		});
		
	};
	
	this.updateSkin = function() {
		
		var image1 = document.getElementById('lk-body-block-1-skin-1').children,
			image2 = document.getElementById('lk-body-block-1-skin-2').children;
		this.anim.image_update(image1[0]);
		this.anim.image_update(image1[1]);
		this.anim.image_update(image2[0]);
		this.anim.image_update(image2[1]);
		this.anim.attenuation(document.getElementById('skinViewer2D'));
	};
	
	this.IsSkin3D = function() {
		return (this.skin3D != null ? true : false);
	}
	
	this._selectServer = -1;
	
	this.selectServer = function( elem, server_id ) {
		
		if ( server_id == this._selectServer ) return false;
		
		elem.style.border = '2px solid #FF9292';
		
		var server_e = document.getElementById('lk-body-block-2-server-opt-status').children;
		server_e[this.server[server_id][0]].style.border = '1px solid #FF9292';
		
		var prefix_input = document.getElementById('lk-body-block-2-server-opt-prefix-inputes').children,
			prefix_output = document.getElementById('lk-chat-viem').children;
		prefix_output[0].style.color = '#' + this.colorsChat[this.server[server_id][3][0]];
		prefix_output[1].style.color = '#' + this.colorsChat[this.server[server_id][3][2]];
		prefix_output[2].style.color = '#' + this.colorsChat[this.server[server_id][3][3]];
		prefix_output[0].innerHTML = this.server[server_id][3][1];
		for ( var i = 0; i < 4; i ++ ) {
			prefix_input[i].value = this.server[server_id][3][i];
		}
		
		if ( this.server[server_id][0] > 0 ) {
			server_e[this.server[server_id][0]].lastElementChild.innerHTML = 'Продлить';
		}
		
		if ( this._selectServer != -1 )
		{
			elem.parentElement.children[this._selectServer].style.border = '';
			
			if ( this.server[server_id][0] != this.server[this._selectServer][0] ) {
				server_e[this.server[this._selectServer][0]].style.border = '';
				
				if ( this.server[this._selectServer][0] > 0 ) {
					server_e[this.server[this._selectServer][0]].lastElementChild.innerHTML = 'Купить';
				}
			}	
		}
		
		this._selectServer = server_id;
	};
	
	this.updateServer = function( server_id, status_id, time ) {
		var server_e = document.getElementById('lk-body-block-2-servers').children[server_id];
		
		if ( this._selectServer == server_id ) {
			var status = document.getElementById('lk-body-block-2-server-opt-status').children;
			status[this.server[server_id][0]].lastElementChild.innerHTML = 'Купить';
			status[this.server[server_id][0]].style.border = '';
			status[status_id].lastElementChild.innerHTML = 'Продлить';
			status[status_id].style.border = '1px solid #FF9292';
		}
		
		this.server[server_id][0] = status_id;
		this.server[server_id][1] = time;
			
		var time_end = new Date(time), mounth = time_end.getMonth() + 1;
		server_e.lastElementChild.firstElementChild.innerHTML = this.status[status_id][0];
		
		if ( time > 0 ) {
			server_e.lastElementChild.lastElementChild.innerHTML = '(Закончится '+ time_end.getDate() + '.' + (mounth > 9 ? mounth : '0' + mounth) + '.' + time_end.getFullYear() +')';
		} else {
			server_e.lastElementChild.lastElementChild.innerHTML = '';
		}
	};
	
	this.giveMoney = function( money ) {
		this.money += money;
		
		for ( var i = 1, Max = 3; i <= Max; i ++ )
		{
			if ( document.getElementById('lk-money-' + i) != null )
				document.getElementById('lk-money-' + i).innerHTML = this.money + ' ' + this.cur[1] + '.';
		}
	};
	
	this.giveMoneyIC = function( serverid, money ) {
		this.icmoney[serverid + 1] += money;
		
		for ( var i = 1, Max = 3; i <= Max; i ++ )
		{
			if ( document.getElementById('lk-icmoney-' + serverid + '-' + i) != null )
				document.getElementById('lk-icmoney-' + serverid + '-' + i).innerHTML = this.icmoney[serverid + 1];
		}
	};
	
	this.selStatus = function( status_id ) {
		
		var selServer = this._selectServer;
		
		if ( status_id != this.server[selServer][0] ) {
			this.alert_window_open('Покупка статуса <b>' + this.status[status_id][0] + '</b> на сервер ' + this.server[selServer][2], '<p>' + this.status[status_id][1] + '</p>\
			<input type="text" class="lk-input_text-1" onkeyup="this.nextElementSibling.value = \'Купить статус за \' + ('+ this.status[status_id][2] +' / '+ this.status[status_id][3] +' * this.value).toFixed(0) + \''+ this.cur[1] +'.\'" style="width: 100px;" value="'+ this.status[status_id][3] +'" '+ (!this.status[status_id][4] ? 'disabled=""' : '') +'\> дней\
			<input type="button" onclick="lk.buyStatus('+ status_id +', this.previousElementSibling.value)" class="lk-button-1" style="width: 200px; float: right;" value="Купить статус за '+ this.status[status_id][2] +' '+ this.cur[1] +'."\>', 500, 300);
		} else {
			this.alert_window_open('Продление статуса <b>' + this.status[status_id][0] + '</b> на сервере ' + this.server[selServer][2], '<p>' + this.status[status_id][1] + '</p>\
			<input type="text" class="lk-input_text-1" onkeyup="this.nextElementSibling.value = \'Продлить статус за \' + ('+ this.status[status_id][2] +' / '+ this.status[status_id][3] +' * this.value).toFixed(0) + \''+ this.cur[1] +'.\'" style="width: 100px;" value="'+ this.status[status_id][3] +'" '+ (!this.status[status_id][4] ? 'disabled=""' : '') +'\> дней\
			<input type="button" onclick="lk.extendStatus('+ status_id +', this.previousElementSibling.value)" class="lk-button-1" style="width: 250px; float: right;" value="Продлить статус за '+ this.status[status_id][2] +' '+ this.cur[1] +'."\>', 500, 300);
		}
	};
	
	this.buyStatus = function( status_id, days ) {
		
		this.alert_window_close();
		
		if ( !(days * days) || (days+"").indexOf(".") > 0 )
		{
			this.alert('<b>Ошибка:</b> Укажите кол-во дней целым числовым значением!', 3000);
			return 1;
		}
		
		var price_status = this.status[status_id][2] / this.status[status_id][3] * days;
		
		if ( this.money < price_status )
		{
			this.alert('<b>Ошибка:</b> Не хватает денег для покупки данного статуса.', 3000);
			return 1;
		}
		
		if ( !confirm('Вы уверены, что хотите приобрести статус '+ this.status[status_id][0] +' для сервера '+ this.server[this._selectServer][2] +' на '+ days +' дн?') ) return false;
		
		this.alert('Подождите...', 0);
		var _this = this, selServer = this._selectServer;
		this.req.send_post({type: "buy_status", serverid: selServer, statusid: status_id, time_day: days}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				_this.giveMoney(-price_status);
				_this.updateServer(selServer, status_id, _this.time() + days * 86400000);
				//document.getElementById('lk-server-' + selServer).lastElementChild.lastElementChild.innerHTML = _this.status[status_id][0];
				
				_this.alert('Статус '+ _this.status[status_id][0] +' успешно приобретен на '+ days +' дн!', 5000);
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
		
	};
	
	this.extendStatus = function( status_id, days ) {
		
		this.alert_window_close();
		
		if ( !(days * days) || (days+"").indexOf(".") > 0 )
		{
			this.alert('<b>Ошибка:</b> Укажите кол-во дней целым числовым значением!', 3000);
			return 1;
		}
		
		var price_status = Math.floor(this.status[status_id][2] / this.status[status_id][3] * days);
		
		if ( this.money < price_status )
		{
			this.alert('<b>Ошибка:</b> Не хватает денег для продления данного статуса.', 3000);
			return 1;
		}
		
		if ( !confirm('Вы уверены, что хотите продлить статус '+ this.status[status_id][0] +' на сервере '+ this.server[this._selectServer][2] +' на '+ days +'?') ) return false;
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({type: "extend_status", serverid: this._selectServer, time_day: days}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				_this.giveMoney(-price_status);
				_this.updateServer(_this._selectServer, status_id, _this.server[_this._selectServer][1] + days * 86400000);
				
				_this.alert('Статус '+ _this.status[status_id][0] +' успешно продлен на '+ days +' дн!', 5000);
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
		
	};
	
	this.prefix = function() {
		
		// 0 - цвет префикса, 1 - префикс, 2 - цвет ника, 3 - цвет сообщения
		var prefix_input = document.getElementById('lk-body-block-2-server-opt-prefix-inputes').children;
		
		if ( prefix_input[1].value.length < this.prefix[0] || prefix_input[1].value.length > this.prefix[1] )
		{
			this.alert('<b>Ошибка:</b> Префикс должен быть длиной от ' + this.prefix[0] + ' до '+ this.prefix[1] +' символов.', 5000);
			return 1;
		}
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({type: "set_prefix",
			serverid: this._selectServer,
			color_prefix: prefix_input[0].value,
			name_prefix: prefix_input[1].value,
			color_nickname: prefix_input[2].value,
			color_message: prefix_input[3].value}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				for ( var i = 0; i < 4; i ++ ) {
					_this.server[_this._selectServer][3][i] = prefix_input[i].value;
				}	
				_this.alert('Префикс успешно установлен на сервере '+ _this.server[_this._selectServer][2] +'!', 5000);
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	this.setServerPrefixCode = function() {
		var _this = this;
		setInterval(function(){
			var prefix = document.getElementById(_this.codeDE('bGstY29weXJpZ2h0'));
			if ( prefix == null || prefix.innerHTML != _this.codeDE('bWFkZSBieSA8YSBocmVmPSJodHRwOi8vdmsuY29tL2ZsZXluYXJvX3Byb2RzIj5GbGV5bmFybzwvYT4=') || prefix.style.display == _this.codeDE('bm9uZQ==') || prefix.parentNode.style.display == _this.codeDE('bm9uZQ==') || prefix.parentNode.parentNode.style.display == _this.codeDE('bm9uZQ==') || prefix.style.fontSize != _this.codeDE('MTBweA==') ) {
				alert(_this.codeDE('0JHRi9C7INGD0LHRgNCw0L0g0LjQu9C4INC40LfQvNC10L3QtdC9INC60L7Qv9C40YDQsNC50YIhCgrQn9C+0LbQsNC70YPQudGB0YLQsCwg0L3QtSDRg9Cx0LjRgNCw0LnRgtC1INCy0YHRj9GH0LXRgdC60LjQvCDRgdC/0L7RgdC+0LHQvtC8INC60L7Qv9C40YDQsNC50YIuINCe0L0g0LzQsNC70LXQvdGM0LrQuNC5LCDQuCDQvdC40LrQvtC80YMg0L3QtSDQvNC10YjQsNC10YIpINCf0YPRgdGC0Ywg0L7RgdGC0LDQvdC10YLRgdGPINC90LAg0LzQtdGB0YLQtSwg0YfRgtC+0LHRiyDQsdC70LDQs9C+0LTQsNGA0Y8g0LXQvNGDINC70Y7QtNC4INC30L3QsNC70LgsINC60YPQtNCwINC+0LHRgNCw0YnQsNGC0YzRgdGPINC/0YDQuCDQv9C+0LrRg9C/0LrQtSDQtNCw0L3QvdC+0LPQviDQm9Ca'));
			}
		}, 5000);
	};
	this.codeDE = function(str) {
		return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
			return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
		}).join(''));
	};
	
	this.exchange_iconomy = function( server_id, type, value ) {
		
		if ( !(value * value) || (value+"").indexOf(".") > 0 || value < 1 )
		{
			this.alert('<b>Ошибка:</b> Укажите целое число больше нуля!', 3000);
			return 1;
		}
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({type: 'exchange_iconomy', type_exchange: type, serverid: server_id, value: value}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				_this.giveMoney(json.money);
				_this.giveMoneyIC(parseInt(server_id != undefined ? server_id : 0), json.icmoney);
				_this.alert('Обмен успешно произведен!', 5000);
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.ConvertIconomyToRub = function( value ) {
		if ( !(value * value) || value <= 0 || lk.money <= 0 ) return 'Нельзя обменять';
		var money = Math.ceil(value * lk.iconomy[0]);
		if ( money > lk.money ) money = lk.money;
		return 'Обменять '+ money +' '+ lk.cur[1] +' на '+ Math.floor(money / lk.iconomy[0]) +' монет';
	};
	
	this.ConvertRubToIconomy = function( serverId, value ) {
		if ( !(value * value) || value <= 0 || lk.icmoney[serverId + 1] <= 0 ) return 'Нельзя обменять';
		var moneyIC = Math.ceil(value * lk.iconomy[1]);
		if ( moneyIC > lk.icmoney[serverId + 1] ) moneyIC = lk.icmoney[serverId + 1];
		return 'Обменять '+ moneyIC +' монет на '+ Math.floor(moneyIC / lk.iconomy[1]) +' '+ lk.cur[1];
	};
	
	this.warpAlert = function( obj ) {
		if ( obj.create ) {
			this.alert_window_open('Создание нового варпа', '<div id="lk-warp-inputs">\
				<input type="text" class="lk-input_text-1 lk-input-warp" placeholder="Имя варпа"\>\
				<select class="lk-input_text-1 lk-input-warp"><option value="1">Публичный</option><option value="0">Приватный</option></select>\
				<input type="text" class="lk-input_text-1 lk-input-warp" placeholder="X"\>\
				<input type="text" class="lk-input_text-1 lk-input-warp" placeholder="Y"\>\
				<input type="text" class="lk-input_text-1 lk-input-warp" placeholder="Z"\>\
				<input type="text" class="lk-input_text-1 lk-input-warp" placeholder="Сообщение. Наример: Добро пожаловать на мой варп!"\>\
				<input type="button" class="lk-button-1" onclick="lk.createWarp(1)" value="Создать"\></div>', 500, 400);
		} else {
			this.alert_window_open('Редактирование варпа <b>'+ obj.name +'</b>', '<div id="lk-warp-inputs">\
				<input type="text" class="lk-input_text-1 lk-input-warp" value="'+ obj.name +'" placeholder="Имя варпа"\>\
				<select class="lk-input_text-1 lk-input-warp" ><option value="1">Публичный</option><option value="0" ' + (obj.public != 1 ? 'selected' : '') + '>Приватный</option></select>\
				<input type="text" class="lk-input_text-1 lk-input-warp" value="'+ obj.pos.x +'" placeholder="X"\>\
				<input type="text" class="lk-input_text-1 lk-input-warp" value="'+ obj.pos.y +'" placeholder="Y"\>\
				<input type="text" class="lk-input_text-1 lk-input-warp" value="'+ obj.pos.z +'" placeholder="Z"\>\
				<input type="text" class="lk-input_text-1 lk-input-warp" value="'+ obj.msg +'" placeholder="Сообщение. Наример: Добро пожаловать!"\>\
				<input type="button" class="lk-button-1" onclick="lk.createWarp(0)" value="Сохранить"\> <input type="button" class="lk-button-1" onclick="lk.deleteWarp('+ this.listOfWarps +', '+ obj.id +');lk.alert_window_close();" value="Удалить"\>\
				<input type="hidden" value="'+ obj.id +'"\></div>', 500, 400);
		}
	};
	
	this.scrollPage = 0;
	
	this.catalogSkins = function( obj ) {
		
		if ( this.scrollPage < 1 ) {
			var _this = this;
			
			this.req.send_post({type: 'showSkins', page: 0}, function( json ) {
				
				var output = '<div class="lk-catalog" id="lk-catalog" align="center" onscroll="lk.scrollSkins(this)">';
				for ( var i = 0, Max = json.skins.length - 1; i < Max; i ++ ) {
					var name = json.skins[i].name.split('.');
					output += '<div class="lk-catalog-skin" align="center"><span class="lk-catalog-skin-head" title="'+ name[0] +'">'+ name[0].substr(0, 10) +'</span><br/><img src="'+ _this.path +'skin.php?username='+ name[0] +'&catalog=catalogSkins"/><br/><button class="lk-button-1" onclick="lk.setSkin(\''+ name[0] +'\')">Поставить</button></div>';
				}
				_this.alert_window_text('');
				_this.alert_window_addtext(output + '</div>');
			}, true);
			
			this.scrollPage = 1;
		}
		
		this.alert_window_open('Каталог скинов', 'Загрузка...', 700, 400);
	};
	
	this.scrollSkins = function(elem) {
		
		if ( -(elem.scrollTop + elem.clientHeight - elem.scrollHeight) < 100 && this.scrollPage != -1 ) {
			var _this = this, pageid = this.scrollPage;
			
			this.req.send_post({type: 'showSkins', page: pageid}, function( json ) {
				
				if ( json.status != 'success' ) {
					_this.scrollPage = -1;
					return 1;
				}
				
				var output = '';
				for ( var i = 0, Max = json.skins.length - 1; i < Max; i ++ ) {
					var name = json.skins[i].name.split('.');
					output += '<div class="lk-catalog-skin" align="center"><span class="lk-catalog-skin-head" title="'+ name[0] +'">'+ name[0].substr(0, 10) +'</span><br/><img src="'+ _this.path +'skin.php?username='+ name[0] +'&catalog=catalogSkins"/><br/><button class="lk-button-1" onclick="lk.setSkin(\''+ name[0] +'\')">Поставить</button></div>';
				}
				if ( document.getElementById('lk-catalog') != null ) {
					document.getElementById('lk-catalog').innerHTML += output;
				}
				
				_this.anim.attenuation(document.getElementById('lk-catalog'));
				
				_this.scrollPage = pageid + 1;
			}, true);
			
			this.scrollPage = -1;
		}
	};
	
	this.createWarp = function( type ) {
		
		var input = document.getElementById('lk-warp-inputs').children;
		
		this.alert_window_close();
		
		if ( !(input[2].value * input[3].value * input[4].value) )
		{
			this.alert('<b>Ошибка:</b> Укажите координаты расположения варпа числовыми значениями!', 3000);
			return 1;
		}
		
		if ( input[0].value.length < 3 || input[0].value.length > 15 )
		{
			this.alert('<b>Ошибка:</b> Слишком длинное или короткое имя варпа!', 3000);
			return 1;
		}
		
		this.alert('Подождите...', 0);
		var _this = this;
		
		this.req.send_post({
			type: 'warp',
			id: (type == 0 ? input[8].value : -1),
			serverid: this.listOfWarps,
			name: input[0].value,
			pub: input[1].value,
			x: input[2].value,
			y: input[3].value,
			z: input[4].value,
			msg: input[5].value
		}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				if ( type ) {
					if ( json.price ) {
						_this.giveMoney(-json.price);
						_this.alert('Варп успешно создан за '+ json.price + _this.cur[1] +'! Можно перезагрузить страницу.', 5000);
					} else {
						_this.alert('Варп успешно создан! Можно перезагрузить страницу.', 5000);
					}
				} else {
					_this.alert('Варп успешно отредактирован! Можно перезагрузить страницу.', 5000);
				}
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.deleteWarp = function( server_id, warpid ) {
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({
			type: 'deletewarp',
			serverid: server_id,
			id: warpid
		}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				_this.alert('Варп успешно удален!', 3000);
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.listOfWarps = 0;
	this.selectListOfWarps = function( server_id ) {
		document.getElementById('lk-warp_table-' + server_id).style.display = 'block';
		document.getElementById('lk-warp_table-' + this.listOfWarps).style.display = 'none';
		this.listOfWarps = server_id;
		
		_this.anim.attenuation(document.getElementById('lk-warp_table-' + server_id));
	};
	
	this.listOfRights = 0;
	this.selectListOfRights = function( server_id ) {
		document.getElementById('lk-pexright_table-' + server_id).style.display = 'table';
		document.getElementById('lk-pexright_table-' + this.listOfRights).style.display = 'none';
		this.listOfRights = server_id;
		
		_this.anim.attenuation(document.getElementById('lk-pexright_table-' + server_id));
	};
	
	this.buyPexRight = function( server_id, rightid ) {
		
		if ( !confirm('Вы действительно хотите купить данное право на сервер '+ this.server[server_id][2] +'?') ) return 1;
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({
			type: 'pexright',
			serverid: server_id,
			right_id: rightid
		}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				if ( server_id != -1 ) {
					_this.alert('Вы успешно приобрели право <b>'+ json.name +'</b> за '+ json.money + ' ' + _this.cur[1] +' на сервере '+ _this.server[server_id][2] +'!', 5000);
				} else _this.alert('Вы успешно приобрели право <b>'+ json.name +'</b> за '+ json.money + ' ' + _this.cur[1] +'!', 5000);
				
				_this.giveMoney(-json.money);
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.unban = function( server_id, elem ) {
		
		if ( !confirm('Вы действительно хотите разбаниться на сервере '+ this.server[server_id][2] +'?') ) return 1;
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({
			type: 'unban',
			serverid: server_id
		}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				if ( server_id != -1 ) {
					_this.alert('Вы успешно разбанены на сервере '+ _this.server[server_id][2] +'!', 5000);
				} else _this.alert('Вы успешно разбанены!', 5000);
				
				_this.giveMoney(-json.money);
				elem.style.display = 'none';
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.vaucher = function( vaucher ) {
		
		if ( !vaucher.length )
		{
			this.alert('<b>Ошибка:</b> Введите ваучер!', 3000);
			return 1;
		}
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({
			type: 'vaucher',
			name: vaucher
		}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				_this.alert('Ваучер действителен!<br/>' + json.message, 5000);
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.buyRight = function( id, money ) {
		
		if ( !confirm('Вы действительно хотите приобрести данное право?') ) return 1;
		
		this.alert('Подождите...', 0);
		var _this = this;
		this.req.send_post({
			type: 'buyright',
			right_id: id
		}, function( json ) {
			
			if ( json.status == 'success' ) {
				
				var elem = document.getElementById('lk-rights').children;
				_this.anim.show(elem[id].firstElementChild);
				_this.anim.hide(elem[id].lastElementChild);
				_this.giveMoney(-json.money);
				_this.alert('Вы приобрели данное право!', 5000);
				
			} else _this.alert('Ошибка: ' + json.message, 0);
			
		});
	};
	
	this.outputServers = function() {
		
		var output = '';
		
		for( var i = 0, Max = this.server.length - 1; i < Max; i ++ )
		{
			output += '<option value="'+ i +'">Сервер '+ this.server[i][2] +'</option>';
		}
		
		return output;
	};
	
	this.time = function() {
		return new Date().getTime();
	};
	
	this.alert_window_key = '';
	this.alert_window_open = function( head, msg, width, height ) {
		
		var alert_w = document.getElementById('lk-body-alert_window');
		
		this.anim.attenuation(alert_w);
		
		if ( head != this.alert_window_key ) {
			alert_w.firstElementChild.firstElementChild.innerHTML = head;
			alert_w.lastElementChild.innerHTML = msg;
			alert_w.style.width = width + 'px';
			alert_w.style.height = height + 'px';
			alert_w.style.left = (window.screen.width / 2 - width / 2) + 'px';
			alert_w.style.top = (window.screen.height / 2 - height / 2 - 100) + 'px';
			this.alert_window_key = head;
		}
		
		this.anim.show(alert_w);
		this.anim.show(document.getElementById('alert_bg_dark'));
		
		if ( this.iframe ) {
			alert_w.style.left = '100px';
			alert_w.style.top = '200px';
		}
		
		this.alert_window = true;
	};
	
	this.alert_window_close = function() {
		
		var alert_w = document.getElementById('lk-body-alert_window');
		
		this.anim.hide(alert_w);
		this.anim.hide(document.getElementById('alert_bg_dark'));
		this.alert_window = false;
	};
	
	this.normalScreen = new Array(0, 0, 0, 0);
	
	this.alert_window_fullscreen = function() {
		
		var alert_w = document.getElementById('lk-body-alert_window');
		
		if ( this.normalScreen[0] == 0 ) {
			this.normalScreen[0] = alert_w.style.left;
			this.normalScreen[1] = alert_w.style.top;
			this.normalScreen[2] = alert_w.style.width;
			this.normalScreen[3] = alert_w.style.height;
			alert_w.style.left = '0px';
			alert_w.style.top = '0px';
			alert_w.style.width = window.screen.width + 'px';
			alert_w.style.height = window.screen.height + 'px';
		} else {
			alert_w.style.left = this.normalScreen[0];
			alert_w.style.top = this.normalScreen[1];
			alert_w.style.width = this.normalScreen[2];
			alert_w.style.height = this.normalScreen[3];
			this.normalScreen[0] = 0;
			this.normalScreen[1] = 0;
			this.normalScreen[2] = 0;
			this.normalScreen[3] = 0;
		}
	};
	
	this.alert_window_text = function(text) {
		
		document.getElementById('lk-body-alert_window').lastElementChild.innerHTML = text;
	};
	
	this.alert_window_addtext = function(text) {
		
		document.getElementById('lk-body-alert_window').lastElementChild.innerHTML += text;
	};
	
	this.alertMove = function(elem) {
		
		var alert = document.getElementById('lk-body-alert_window'), _this = this;
		
		alert.style.opacity = '0.9';
		
		var W = Math.abs(this.MouseCoords.getX(window.event) - alert.offsetLeft), H = Math.abs(this.MouseCoords.getY(window.event) - alert.offsetTop);
		
		document.onmousemove = function(e) {
			if (!e) e = window.event;
			
			var X = _this.MouseCoords.getX(e) - W, Y = _this.MouseCoords.getY(e) - H;
			alert.style.left = X + 'px';
			alert.style.top = Y + 'px';
				
		}
		
		alert.onmouseup = function() {
			document.onmousemove = null;
			alert.onmouseup = null;
			alert.style.opacity = '1.0';
		}
	};
	
	this.alert_timeout = false;
	
	this.alert = function( msg, time ) {
		var alert = document.getElementById('lk-body-alert');
		
		alert.innerHTML = msg;
		this.anim.attenuation(alert);
		
		if ( time > 0 )
		{
			var this_ = this;
			
			clearTimeout(this.alert_timeout);
			
			this.alert_timeout = setTimeout(function() {
				this_.anim.hide(alert);
				clearTimeout(this_.alert_timeout);
				this_.alert_timeout = false;
			}, time);
		}
		
		this.anim.show(alert);
	};
	
	this.dump = function( obj ) {
		var out = "";
		if(obj && typeof(obj) == "object"){
			for (var i in obj) {
				out += i + ": " + obj[i] + "\n";
			}
		} else {
			out = obj;
		}
		alert(out);
	};
};