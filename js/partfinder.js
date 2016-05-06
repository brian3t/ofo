// Part Finder Functions

function addOption(selectId, txt, val) {
	var objOption = new Option(txt, val);
	document.getElementById(selectId).options.add(objOption);
}

function disableEnterKey(e) {
	 var key;

	 if(window.event)
		  key = window.event.keyCode;     //IE
	 else
		  key = e.which;     //firefox

	 if(key == 13) {
		 document.getElementById("crossReferenceSubmit").click();
	 }else{
		  return true;}
}

function getResults(comp) {
	$('#currentCross').html('<div class="application loading"><img src="images/loading.gif" /></div>');
	xajax_getcross(comp);
}
