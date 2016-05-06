var userAgent = navigator.userAgent.toLowerCase();
var isIE = ((userAgent.indexOf("msie") != -1) && (userAgent.indexOf("opera") == -1) && (userAgent.indexOf("webtv") == -1));
var popupImages = new Array();

var indicatorIcon = "images/icons/indicator.gif";
var closeIcon = "images/icons/close.gif";
var siteURL = "";

function openImage(a, width, height)
{
	// when action is linked to element like <a href='/sample.jpg' title='sample' onclick='popupImage(this)'>view me</a>
	var image_href  = a.href;
	var image_title = a.title;
	
	// when action is linked to element like document.getElementById('sample').onmouseover = popupImage;
	if (!image_href) {
		var image_href  = this.href;
		var image_title = this.title;	
	}
	
	if (!image_href)
		return false;
		
	var scrollbars = "no";
	// add margins to image size
	if (width > 0 && height > 0) {
		width += 30; height += 30;
	}
	// check available sizes
	var availableHeight = window.screen.availHeight - 60;
	var availableWidth = window.screen.availWidth - 20;
	if (isNaN(availableHeight)) { availableHeight = 520; } 
	if (isNaN(availableWidth)) { availableWidth = 760; } 
	if (height > availableHeight || height == 0) { 
		height = availableHeight;
		scrollbars = "yes"; 
	}
	if (width > availableWidth || width == 0) {
		width = availableWidth;
		scrollbars = "yes";
	}
	var openImageWin = window.open (image_href, 'openImageWin', 'left=0,top=0,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=' + scrollbars + ',resizable=yes,width=' + width + ',height=' + height);
	openImageWin.focus();
	return false;
}


function popupImage(a, globalURL){
	if (globalURL) {
		siteURL = globalURL;
	} else {
		siteURL = "";
	}
	// when action is linked to element like <a href='/sample.jpg' title='sample' onclick='popupImage(this)'>view me</a>
	var image_href  = a.href;
	var image_title = a.title;
	
	// when action is linked to element like document.getElementById('sample').onmouseover = popupImage;
	if (!image_href) {
		var image_href  = this.href;
		var image_title = this.title;	
	}
	
	if (!image_href)
		return false;
	
	var black_cloud = document.getElementById("black_cloud");
	if (!black_cloud) {
		var black_cloud     = document.createElement('div');			
		black_cloud.id      = "black_cloud";
		black_cloud.onclick = hideBlack;
		black_cloud.style.position = "absolute";
		black_cloud.style.zIndex   = "3500";
		black_cloud.style.top      = "0px";
		black_cloud.style.left     = "0px";		
		black_cloud.style.backgroundColor = "black";
		black_cloud.style.opacity    = "0.9";
		black_cloud.style.mozOpacity = "0.9";
		black_cloud.style.filter     = "alpha(opacity=90)";
		fullScreen(black_cloud);
		document.body.appendChild(black_cloud);
	} else {
		black_cloud.style.visibility = "visible";	
	}
	hideSelectBoxes('black_cloud');
	hideFlash();
		
	var black_cloud_inner = document.getElementById("black_cloud_inner");
	if (!black_cloud_inner) {
		var black_cloud_inner            = document.createElement('div');			
		black_cloud_inner.id             = "black_cloud_inner";
		black_cloud_inner.style.position = "absolute";
		black_cloud_inner.style.zIndex   = "4000";
		black_cloud_inner.style.padding  = "5px";
		black_cloud_inner.style.backgroundColor = "white";
		document.body.appendChild(black_cloud_inner);		
	} else {
		black_cloud_inner.style.visibility = "visible";	
	}
	black_cloud_inner.style.border = "none";
	black_cloud_inner.innerHTML = "<img src='" + siteURL + indicatorIcon + "' alt='loading' />";
	centerScreen(black_cloud_inner, 10, 10);
	
	if (popupImages[image_href]) {
		loadedImage(popupImages[image_href]);		
	} else {
		var img = document.createElement('img');
		img.alt    = image_title;
		img.onload = loadedImage;
		img.src    = image_href;
		popupImages[image_href] = img;		
	}
	
	return false;
}
function loadedImage(img){
	if (img) {
		var image_href   = img.src;
		var image_title  = img.alt;	
		var image_width  = img.width;
		var image_height = img.height;
	} 
	
	if (!image_href) {
		var image_href   = this.src;
		var image_title  = this.alt;	
		var image_width  = this.width;
		var image_height = this.height;
	}
	
	var black_cloud_inner = document.getElementById("black_cloud_inner");
	black_cloud_inner.innerHTML  = "";
	centerScreen(black_cloud_inner, image_width, image_height);
	black_cloud_inner.innerHTML  = "<div align='right'><a href='#' onClick='hideBlack(); return false;'><img style='border:none;' src='" + siteURL + closeIcon +"' alt='close'></a></div>";
	black_cloud_inner.innerHTML += "<br/><center><img src='" + image_href + "' alt='" + image_title + "' /></center>";	
	black_cloud_inner.innerHTML += "<br/><div align='center'>";
	black_cloud_inner.innerHTML += image_title + "</div>";	
}

function hideBlack(){
	var black_cloud = document.getElementById("black_cloud");
	var black_cloud_inner = document.getElementById("black_cloud_inner");
	showSelectBoxes('black_cloud');
	showFlash();
	document.body.removeChild(black_cloud);
	document.body.removeChild(black_cloud_inner);
	return false;
}
function centerScreen(black_cloud_inner, width, height){
	scrolls =  getScroll();
	
	var window_width = 0;
	if (window.innerWidth && (window_width==0 || window_width>window.innerWidth))
		window_width = window.innerWidth;
	if (document.body.offsetWidth && (window_width==0 || window_width>document.body.offsetWidth))
		window_width = document.body.offsetWidth;
	if (document.documentElement.offsetWidth && (window_width==0 || window_width>document.documentElement.offsetWidth))
		window_width = document.documentElement.offsetWidth;
	
	if ((window_width-width)/2 + scrolls[0] > 0)
		black_cloud_inner.style.left = Math.round((window_width-width)/2 + scrolls[0]) + "px";
	else
		black_cloud_inner.style.left = "0px";
		
	var window_height = 0;
	if (window.innerHeight && (window_height==0 || window_height>window.innerHeight))
		window_height = window.innerHeight;
	if (document.body.offsetHeight && (window_height==0 || window_height>document.body.offsetHeight))
		window_height = document.body.offsetHeight;
	if (document.documentElement.offsetHeight && (window_height==0 || window_height>document.documentElement.offsetHeight))
		window_height = document.documentElement.offsetHeight;
	
	if ((window_height-height)/2 + scrolls[1] > 0)
		black_cloud_inner.style.top = Math.round((window_height-height)/2 + scrolls[1]) + "px";
	else
		black_cloud_inner.style.top = "0px";
}

function fullScreen(black_cloud){
	scrolls = getPageSizeWithScroll();
	if (window.screen.width > scrolls[0])
		black_cloud.style.width  = window.screen.width + "px";	
	else
		black_cloud.style.width  = scrolls[0] + "px";
	if (window.screen.height > scrolls[1])
		black_cloud.style.height = window.screen.height + "px";
	else
		black_cloud.style.height = scrolls[1] + "px";
}

/* From menu.js */
function getPageSizeWithScroll() {
	var xWithScroll = 0; var yWithScroll = 0; 
	if (window.innerHeight && window.scrollMaxY) { // Firefox         
		yWithScroll = window.innerHeight + window.scrollMaxY;         
		xWithScroll = window.innerWidth + window.scrollMaxX;     
	} else if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac         
		yWithScroll = document.body.scrollHeight;         
		xWithScroll = document.body.scrollWidth;     
	} else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari         
		yWithScroll = document.body.offsetHeight;         
		xWithScroll = document.body.offsetWidth;       
	}     
	var arrayPageSizeWithScroll = new Array(xWithScroll,yWithScroll);    
	return arrayPageSizeWithScroll; 
} 

function getScroll() {
	var w = window.pageXOffset ||
		document.body.scrollLeft ||
		document.documentElement.scrollLeft;
	var h = window.pageYOffset ||
		document.body.scrollTop ||
		document.documentElement.scrollTop;
	var arrayScroll = new Array(w, h);    
	return arrayScroll;
}

function showSelectBoxes(objId) {
	if (isIE) {
		var obj = document.getElementById(objId);
		var selects = obj.overlapObjects;
		if (selects && selects.length > 0) {
			for (var i = 0; i < selects.length; i++) {
				selects[i].style.visibility = "visible";
			}
		}
		obj.overlapObjects = null;
	}
}

function hideSelectBoxes(objId, objExclude) {
	if (isIE) {
		var obj = document.getElementById(objId);

		var x = findPosX(obj, false);
		var y = findPosY(obj, false);
		var w = obj.offsetWidth;
		var h = obj.offsetHeight;

		if (!obj.overlapObjects) {
			obj.overlapObjects = new Array();
		}

		selects = document.getElementsByTagName("select");
		for (i = 0; i != selects.length; i++) {

			var selectObj = selects[i];
			var objName = selectObj.name;
			var isExclude = false;
			if (objExclude && objExclude.length) {
				for (var ex = 0; ex < objExclude.length; ex++) {
					if (objName == objExclude[ex]) {
						isExclude = true;
					}
				}
			}
			if (isExclude == true || selectObj.style.visibility == "hidden") {
				continue;
			}

			var ox = findPosX(selectObj, false);
			var oy = findPosY(selectObj, false);
			var ow = selectObj.offsetWidth;
			var oh = selectObj.offsetHeight;

			if (ox > (x + w) || (ox + ow) < x) {
				continue;
			}
			if (oy > (y + h) || (oy + oh) < y) {
				continue;
			}
			obj.overlapObjects[obj.overlapObjects.length] = selectObj;

			selects[i].style.visibility = "hidden";
		}
	}
}

function hideFlash() {
	flash = document.getElementsByTagName('object');
	for (var i = 0; i < flash.length; i++) {
		flash[i].style.visibility = 'hidden';
	}
}

function showFlash() {
	flash = document.getElementsByTagName('object');
	for (var i = 0; i < flash.length; i++) { 
		flash[i].style.visibility = 'visible';
	}
}

var rolloverImages = new Array();

function rolloverImage(imageId, imageSrc, imageName, superId, superSrc)
{
	if (!rolloverImages[imageId]) {
		rolloverImages[imageId] = new Image();
		rolloverImages[imageId].src = imageSrc;
	}
	document.images[imageName].src = rolloverImages[imageId].src;
	if (rolloverImages[imageId].height) {
		document.images[imageName].height = rolloverImages[imageId].height;
	}
	if (rolloverImages[imageId].width) {
		document.images[imageName].width = rolloverImages[imageId].width;
	}
	if (superId) {
		var superObj = document.getElementById(superId);
		if (superObj) {
			if (superSrc != "") {
				superObj.href = superSrc;
				superObj.style.display = "inline";
			} else {
				superObj.style.display = "none";
			}
		}
	}
}
