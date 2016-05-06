// Javascript for emoticons

var textObj = "";

function storeCaret(textEl)
{
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

function insertAtCaret(text, textEl)
{	
	if (!textEl) {
		textEl = textObj;
	}
	if (textEl.createTextRange && textEl.caretPos)
	{
		var caretPos = textEl.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		//caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		textEl.focus();
	} else {
		textEl.value = textEl.value + text;
		textEl.focus();
	}
}

function openIconsWindow(pagename, textEl)
{
	textObj = textEl;
	var iconsWin = window.open(pagename, 'iconsWindow', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=400,height=300');
	iconsWin.focus();
}
