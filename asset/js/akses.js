function hasilSetSessionGrup(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			location.replace(window.location.href);
		}
	}
}

function processAjaxSession(report,params){
	if(report=="set_session_grup"){
		url = "login/set_session_grup/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="set_session_grup"){
			req.onreadystatechange = hasilSetSessionGrup;
		}
		
		try {
			req.open("GET", url, true);
		}
		catch (e) {
			alert(e);
		}
		req.send(null);
	}
	else if (window.ActiveXObject) { // IE
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) {
			if(report=="set_session_grup"){
				req.onreadystatechange = hasilSetSessionGrup;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}

function set_session(){
	processAjaxSession("set_session_grup",document.getElementById("set_session_grup").value);
}