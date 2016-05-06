// compare javacript
function compareItems()
{
	var checkedNumber = 0;
	var checkedItems = "";
	var formsNames = document.forms_names.form_name;
	for (var i = 0; i < formsNames.length; i++) {
		var formName = formsNames[i].value;
		if(document.forms[formName].compare.checked) {
				checkedNumber++;
				if(checkedNumber > 1) { checkedItems += ","; }
				checkedItems += document.forms[formName].compare.value;
		}
	}
	if (checkedNumber < 2) {
		alert(compareMinAllowed);
	} else if (checkedNumber > 5) {
		alert(compareMaxAllowed);
	} else {
		document.compare_form.items.value = checkedItems;
		document.compare_form.submit();
	}

	return false;
}

function compareRecentItems(formName)
{
	var checkedNumber = 0;
	var checkedItems = "";
	var recentForm = document.forms[formName];
	var compareItems = recentForm.compare;
	for (var i = 0; i < compareItems.length; i++) {
		if(recentForm.compare[i].checked) {
				checkedNumber++;
				if(checkedNumber > 1) { checkedItems += ","; }
				checkedItems += recentForm.compare[i].value;
		}
	}
	if (checkedNumber < 2) {
		alert(compareMinAllowed);
	} else if (checkedNumber > 5) {
		alert(compareMaxAllowed);
	} else {
		recentForm.items.value = checkedItems;
		recentForm.submit();
	}

	return false;
}