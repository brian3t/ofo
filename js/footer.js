// footer javacript
function openPopup(pageUrl, width, height)
{
	var scrollbars = "yes";
	var popupWin = window.open (pageUrl, 'popupWin', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=' + scrollbars + ',resizable=yes,width=' + width + ',height=' + height);
	popupWin.focus();
	return false;
}

