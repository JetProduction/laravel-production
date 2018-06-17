

function _req(path, args, ddos) {
	
	this.path = path;
	this.args = args;
	this.ddos = ddos;
	this.ddos_time = 0;
	this.ddos_maxCount = 3;
	this.ddos_count = 0;
	
	this.send_post = function(params, after, antiddos) {
		
		if ( this.ddos != false && antiddos == null /*&& this.ddos_count ++ == this.ddos_maxCount*/ ) {
			var time = new Date().getTime();
			var time_ = time - this.ddos_time;
			
			if ( time_ < this.ddos ) {
				return after({status: "error", message: "Пожалуйста, подождите "+ ((this.ddos - time_) / 1000 + 1).toFixed(0) +" секунд..."});
			} else this.ddos_time = time;
			this.ddos_count = 0;
		}
		
		xmlhttp = this.getXmlHttp();
		
		xmlhttp.open('POST', this.path, true);
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		
		xmlhttp.onreadystatechange = function() {
			if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 )
				if ( xmlhttp.responseText )
				{
					if ( xmlhttp.responseText.charAt(0) == '{' ) {
						//alert(xmlhttp.responseText);
						after(JSON.parse(xmlhttp.responseText));
					} else {
						after({status: "error", message: xmlhttp.responseText});
					}
				}
		};
		
		var p = '';
		for (var key in params) {
			p += key + '=' + params[key] + '&';
		}
		for (var key in args) {
			p += key + '=' + args[key] + '&';
		}
		xmlhttp.send(p.slice(0,-1));
	};
	
	this.upload = function( file, params, progress, load ) {
		
		var xhr = this.getXmlHttp();
		var formData = new FormData();
		
		formData.append("file", file);
		for (var key in params) {
			formData.append(key, params[key]);
		}
		for (var key in args) {
			formData.append(key, args[key]);
		}
		
		xhr.upload.onprogress = function(event) {
			progress(event);
		}

		xhr.onload = xhr.onerror = function() {
			load(this);
		};

		xhr.open('POST', this.path, true);
		xhr.send(formData);
	};
	
	this.getXmlHttp = function() {
		var xmlhttp;
		
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				xmlhttp = false;
			}
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
	}
};