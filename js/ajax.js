function GetXmlHttpObject() {
	try { return new XMLHttpRequest(); }
	catch (e) {
		try { 
			return new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			return new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return null;
}

function loadAjax(divId, url, someFunction, someParams) {
	var divObj = document.getElementById(divId);
	if(divObj) {
		xmlHttp = GetXmlHttpObject();
		if (xmlHttp == null) {
			alert ("Your browser does not support AJAX!");
			return;
		}
		xmlHttp.onreadystatechange = function() { loadedAjax(someFunction, someParams, divObj); };  
		xmlHttp.open("GET", url, true);
		xmlHttp.setRequestHeader('Content-Type', 'application/ajax+html');
		xmlHttp.send(null);
	} else {
		alert("Can't initiliaze HTML object: " + divId);
	}
	return false;
}

function callAjax(url, someFunction, someParams) {
	if (someFunction) {
		xmlHttp = GetXmlHttpObject();
		if (xmlHttp == null) {
			alert ("Your browser does not support AJAX!");
			return;
		}
		xmlHttp.onreadystatechange = function() { loadedAjax(someFunction, someParams, ""); };  
		xmlHttp.open("GET", url, true);
		xmlHttp.setRequestHeader('Content-Type', 'application/ajax+html');
		xmlHttp.send(null);
	} else {
		alert("Function is not defined for AJAX call.");
	}
	return false;
}

function loadedAjax(someFunction, someParams, divObj) 
{
	if (xmlHttp.readyState == 4) {
		if (divObj) {
			divObj.innerHTML = xmlHttp.responseText;
		}
		if (someFunction) {
			someFunction(xmlHttp.responseText, someParams);
		}
	}
}

function nextNode(e) {
	return ((e && e.nodeType != 1) ? nextNode(e = e.nextSibling) : e);
}

function prevNode(e) {
	return ((e && e.nodeType != 1) ? prevNode(e = e.previousSibling) : e);
}

function nextElement(e) {
	return nextNode(e.nextSibling);
}

function prevElement(e) {
	return prevNode(e.previousSibling);
}



function addClassNameByID(obj_id, classname) {
	var obj    = document.getElementById(obj_id);
	if (obj) {
		addClassName(obj, classname);	
	}
	var obj    = document.getElementsByName(obj_id);
	if (obj) {
		for(var j=0; j<obj.length; j++) {
			addClassName(obj[j], classname);
		}
	}	
}

function addClassName(obj, classname) {
	obj.className += " " + classname;	
}

function removeClassNameByID(obj_id, classname) {
	var obj    = document.getElementById(obj_id);
	if (obj) {
		removeClassName(obj, classname);
	}
	var obj    = document.getElementsByName(obj_id);
	if (obj) {
		for(var j=0; j<obj.length; j++) {
			removeClassName(obj[j], classname);
		}
	}
}

function removeClassName(obj, classname) {
	var substr = obj.className.split(" ");
	if (substr.length) {
		obj.className = "";
		for (i = 0; i < substr.length; i++) {
			if (substr[i] !== classname) {
				obj.className += " " + substr[i];
			}
		}
	} else if (obj.className == classname){
		obj.className = "";
	}
}

function hasClassNameByID(obj_id, classname) {
	var obj    = document.getElementById(obj_id);
	if (obj) {
		return hasClassName(obj, classname);
	}
	var obj    = document.getElementsByName(obj_id);
	if (obj) {
		if (obj.length) {
			return hasClassName(obj[0], classname);
		} else {
			return hasClassName(obj, classname);
		}
	}
	return false;
}

function hasClassName(obj, classname) {
	var substr = obj.className.split(" ");
	if (substr.length) {
		for (i = 0; i < substr.length; i++) {
			if (substr[i] == classname) {
				return true;
			}
		}
	} else if (obj.className == classname){
		return true;
	}
	return false;
}

function formSerialize(form) {
	var serialized = "";
	for (i = 0; i<form.length; i++) {
		if(form[i].type) {
			if (form[i].type == "text" || form[i].type == "hidden" || form[i].type == "select-one") {
				serialized += form[i].name + "=" + form[i].value + "&";
			} else if (form[i].type == "checkbox") {
				if (form[i].checked) {
					serialized += form[i].name + "=" + form[i].value + "&";
				}
			}
		} else {
			serialized += form[i].name + "=" + form[i].value + "&";
		}
	}
	return serialized;	
}

function stringUnserialize(str) {
	var substr = str.split("&");
	var values = new Array();
	var j = 0;
	if (substr.length) {
		for (i = 0; i < substr.length; i++) {
			if (substr[i].length) {
				line = substr[i].split("=");
				if (line.length && line[0]){
					values[j] = new Array();
					values[j][0] = line[0];
					values[j][1] = line[1];
					j++;
				}
			}
		}
	} else {
		line = response.split("=");
		if (line.length && line[0]){
			values[j] = new Array();
			values[j][0] = line[0];
			values[j][1] = line[1];
			j++;
		}
	}
	return values;
}
