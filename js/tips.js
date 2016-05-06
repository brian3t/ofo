
function mouseX(evt) {
	if (evt.pageX) {
		return evt.pageX;
	} else if (evt.clientX) {
		return evt.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
	} else {
		return null;
	}
}

function mouseY(evt) {
	if (evt.pageY) {
		return evt.pageY;
	} else if (evt.clientY) {
		return evt.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
	} else {
		return null;
	}
}

function showTip(event, text)	{
	if (text) {
		var tip_cloud = document.getElementById("tip_cloud");
		if (!tip_cloud) {
			var tip_cloud     = document.createElement('div');
			tip_cloud.id      = "tip_cloud";
			tip_cloud.style.position = "absolute";
			tip_cloud.onclick = hideTip;
			document.body.appendChild(tip_cloud);
		}
		var leftPos = mouseX(event) + 15;
		var topPos  = mouseY(event) + 15;
		tip_cloud.style.left    = leftPos;
		tip_cloud.style.top     = topPos;
		tip_cloud.style.display = "block";
		tip_cloud.innerHTML     = text;		
	}
}

function hideTip()	{
	var tip_cloud = document.getElementById("tip_cloud");
	if (tip_cloud) {
		tip_cloud.style.display = "none";
	}
}