
function changeTab(newTabName, formName)
{
	var formObj = "";
	if (formName) {
		formObj = document.forms[formName];
	} else {
		formObj = document.record;
	}
	var currentTabName = formObj.tab.value;

	if (currentTabName != newTabName) {
		currentTabTD = document.getElementById("td_tab_" + currentTabName);
		newTabTD = document.getElementById("td_tab_" + newTabName);
		currentTab = document.getElementById("tab_" + currentTabName);
		newTab = document.getElementById("tab_" + newTabName);

		if (currentTabTD) {
			currentTabTD.className = "adminTab";
			newTabTD.className = "adminTabActive";
		}
		currentTab.className = "adminTab";
		newTab.className = "adminTabActive";

		currentData = document.getElementById("data_" + currentTabName);
		newData = document.getElementById("data_" + newTabName);

   	currentData.style.display = "none";
   	newData.style.display = "block";

		formObj.tab.value = newTabName;
	}
}

function changeProductTab(formObj, newTabName)
{
	var currentTabName = formObj.tab.value;
	if (currentTabName != newTabName) {
		var idNames = new Array("_tab", "_td_tab", "_a_tab");
		for (var i = 0; i < idNames.length; i++) {
			var idName = idNames[i];
			currentTab = document.getElementById(currentTabName + idName);
			newTab = document.getElementById(newTabName + idName);
			if (currentTab) {
				currentTab.className = "tab";
			}
			if (newTab) {
				newTab.className = "tabActive";
			}
		}
		
		currentData = document.getElementById(currentTabName + "_data");
		newData = document.getElementById(newTabName + "_data" );

		if (currentData) {
			currentData.style.display = "none";
		}
		if (newData) {
			newData.style.display = "block";
		}

		formObj.tab.value = newTabName;
	}
}
