function openFilesWindow(windowUrl, formName, fieldName)
{
	var queryString = "";
	var questionMark = windowUrl.indexOf("?")
	if (questionMark != -1) {
		queryString = windowUrl.substring(questionMark);
	}
  if (formName != "") {
		queryString += ((queryString == "") ? "?" : "&");
		queryString += "form_name=" + formName;
	}
  if (fieldName != "") {
		queryString += ((queryString == "") ? "?" : "&");
		queryString += "field_name=" + fieldName;
	}
	var filesWin = window.open (windowUrl + queryString, 'filesWin', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=600,height=500');
	filesWin.focus();
}

function selectFile(fileId, fileTitle)
{
	if (window.opener) {
		var formName  = document.files_list.form_name.value;
		var fieldName  = document.files_list.field_name.value;
		window.opener.setFile(fileId, fileTitle, formName, fieldName);
		window.opener.focus();
	}
	window.close();
}

function closeFilesWindow()
{
	window.opener.focus();
	window.close();
}

function setFile(fileId, fileTitle, formName, fieldName)
{
	var idsControl = document.forms[formName].elements[fieldName];
	if (idsControl) {
		var ids = idsControl.value;
		var id = ""; var fileNew = true;
		if (ids != "") {
			var idsArray = ids.split(",");
			for (f = 0; f < idsArray.length; f++) {
				id = idsArray[f];
				if (id == fileId) {
					fileNew = false;
				}
			}
		}
		if (fileNew)  {
			files[fileId] = fileTitle;
			if (ids != "") { ids += ","; }
			ids += fileId;
			idsControl.value = ids;
			generateFilesList(formName, fieldName);
		}
	}
}

function removeFile(fileId, formName, fieldName)
{
	var idsControl = document.forms[formName].elements[fieldName];
	if (idsControl && idsControl.value != "") {
		var idsArray = idsControl.value.split(",");
		var newIds = "";
		for (f = 0; f < idsArray.length; f++) {
			var id = idsArray[f];
			if (id != fileId) {
				if (newIds != "") { newIds += ","; }
				newIds += id;
			}
		}
		idsControl.value = newIds;
	}
	generateFilesList(formName, fieldName);
}

function generateFilesList(formName, fieldName)
{
	var ids = document.forms[formName].elements[fieldName].value;
	var blockControl = document.getElementById("block_" + fieldName);
	blockControl.innerHTML = "";

	if (ids != "") {
		var idsArray = ids.split(",");
		for (var f = 0; f < idsArray.length; f++) {
			var id = idsArray[f];
			var fileTitle = files[id];
			var fileInfo = "<b>" + fileTitle + "</b>";
			fileInfo += " - <a href=\"#\" onClick=\"removeFile('" + id + "', '" + formName + "', '" + fieldName + "'); return false;\">" + removeButton + "</a><br>";
			if (blockControl.insertAdjacentHTML) {
				blockControl.insertAdjacentHTML("beforeEnd", fileInfo);
			} else {
				blockControl.innerHTML += fileInfo;
			}
		}
	}
}
