// attachments javascript

function attachFiles(formObj)
{
	var attachURL = formObj.attachments_url.value;
	var paramNumber = 0;
	if (formObj.dep_id) {
		var depId = formObj.dep_id.options[formObj.dep_id.selectedIndex].value;
		if (depId != "") {
			paramNumber++;
			attachURL += "?dep_id=" + depId;
		}
	}
	if (formObj.forum_id && formObj.forum_id.value != "") {
		paramNumber++;
		attachURL += (paramNumber == 1) ? "?" : "&";
		attachURL += "forum_id=" + formObj.forum_id.value;
	}
	if (formObj.thread_id && formObj.thread_id.value != "") {
		paramNumber++;
		attachURL += (paramNumber == 1) ? "?" : "&";
		attachURL += "thread_id=" + formObj.thread_id.value;
	}
	if (formObj.support_id && formObj.support_id.value != "") {
		paramNumber++;
		attachURL += (paramNumber == 1) ? "?" : "&";
		attachURL += "support_id=" + formObj.support_id.value;
	}
	if (formObj.vc && formObj.vc.value != "") {
		paramNumber++;
		attachURL += (paramNumber == 1) ? "?" : "&";
		attachURL += "vc=" + formObj.vc.value;
	}
	var attachWin = window.open (attachURL, 'attachWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400');
	attachWin.focus();
}

function updateAttachments(attachmentsFiles)
{
	var ab = document.getElementById("attachmentsBlock");
	var af = document.getElementById("attachedFiles");
	if (attachmentsFiles == "") {
		ab.style.display = "none";
	} else {
		af.innerHTML = attachmentsFiles;
		ab.style.display = "block";
	}

}

