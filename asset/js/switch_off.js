function hasilSwitchOff(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			location.replace(window.location.href.replace("#",""));
		}
	}
}

function processAjaxSwitchOff(report,params){
	if(report=="switch_off"){
		url = "login/user_switch_off/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="switch_off"){
			req.onreadystatechange = hasilSwitchOff;
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
			if(report=="switch_off"){
				req.onreadystatechange = hasilSwitchOff;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}

function switch_off(){
	processAjaxSwitchOff("switch_off","");
}
