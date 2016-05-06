var userAgent = navigator.userAgent.toLowerCase();
var isIE = ((userAgent.indexOf("msie") != -1) && (userAgent.indexOf("opera") == -1) && (userAgent.indexOf("webtv") == -1));

var tid = new Array();
var lastMenu = new Array();

function show(menuName, menuType) 
{
	var actMenu = new Array();
	var subName = "";
	var subMenus = menuName.split("_");
	var addWidth = false; var addHeight = true;
	for (var m = 0; m < subMenus.length; m++) {
		if (m == 0) {
			subName = subMenus[m];
		} else {
			subName += "_" + subMenus[m];
			addWidth = true; var addHeight = false;
		}
		var parentMenuName = "m_" + subName;
		var subMenuName = "sm_" + subName;
		if (menuType == "2" || menuType == "secondary") {
			parentMenuName = "secondary_" + subName;
			subMenuName = "secondary_ddm_" + subName;
		}
		var parentMenu = document.getElementById(parentMenuName);
		var subMenu = document.getElementById(subMenuName);

		if (subMenu) {
			actMenu[subMenuName] = 1;
			subMenu.style.top = findPosY(parentMenu, addHeight) + "px";
			subMenu.style.left = findPosX(parentMenu, addWidth) + "px";
			subMenu.style.display='block';
			hideSelectBoxes(subMenuName);
			if (tid[subName]) {
				clearTimeout(tid[subName]);
				tid[subName] = "";
			}
		}
	}

	for (menuName in lastMenu) {
		if (!actMenu[menuName]) {
			var menuObj = document.getElementById(menuName);
			menuObj.style.display = "none";
			showSelectBoxes(menuName);
			if (menuObj && menuObj.style.display == "block") {
			}
		}
	}
	lastMenu = actMenu;

}

function hide(menuName, menuType)
{
	var subMenus = menuName.split("_");
	for (var m = 0; m < subMenus.length; m++) {
		if (m == 0) {
			subName = subMenus[m];
		} else {
			subName += "_" + subMenus[m];
		}
		tid[subName] = setTimeout("hideMenu('" + subName + "', '" + menuType + "')", 700);
	}
}

function hideMenu(menuName, menuType)
{
	var subMenuName = "sm_" + menuName;
	if (menuType == "2" || menuType == "secondary") {
		subMenuName = "secondary_ddm_" + menuName;
	}
	var subMenu = document.getElementById(subMenuName);
	if (subMenu) {
		subMenu.style.display='none';
		showSelectBoxes(subMenuName);
	}

}

function findPosX(obj, addWidth)
{
	var curleft = 0;
	if (addWidth) {
		curleft += obj.offsetWidth;
	}
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj, addHeight)
{
	var curtop = 0;
	if (addHeight) {
		curtop += obj.offsetHeight;
	}
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	} else if (obj.y) {
		curtop += obj.y;
	}
	return curtop;
}

function getPageSizeWithScroll()
{
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

function getScroll()
{
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

function popupBlock(linkName, blockName, imageName)
{                              	
	var linkObj = document.getElementById(linkName);
	var blockObj = document.getElementById(blockName);
	var imageObj = document.getElementById(imageName);

	if (blockObj.style.display == "none" || blockObj.style.display == "") {
		blockObj.style.left = findPosX(linkObj, 0) + "px";
		blockObj.style.top = findPosY(linkObj, 1) + "px";
		blockObj.style.display = "block";
		hideSelectBoxes(blockName, "");
		if (imageObj) {
			imageObj.src = "images/icons/minus_small.gif";
		}
	} else {
		blockObj.style.display = "none";
		showSelectBoxes(blockName);
		if (imageObj) {
			imageObj.src = "images/icons/plus_small.gif";
		}
	}
}
