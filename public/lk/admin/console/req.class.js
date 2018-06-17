

function _req(path, args) {
	
	this.path = path;
	this.args = args;
	
	this.send_post = function(params, after) {
		
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
		
		xmlhttp.send(p + this.args);
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