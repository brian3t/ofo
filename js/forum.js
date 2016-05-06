// javacript for editing forum

function editMessage(messageId)
{
	var origMes = document.getElementById("original_message_" + messageId);
	var messBlock = document.getElementById("message_block_" + messageId);
	var messEdit = document.getElementById("message_edit_" + messageId);
	var editForm = document.getElementById("message_edit_block");
	if (origMes && messBlock && messBlock && editForm) {
		cancelMessageEdit();
		var editFormHTML = editForm.innerHTML;
		editForm.innerHTML = "";
		messBlock.style.display = "none";
		messEdit.innerHTML = editFormHTML;
		messEdit.style.display = "block";
		document.message_edit.text_edit.value = origMes.value;
		document.message_edit.message_id.value = messageId;
	}

}


function cancelMessageEdit()
{
	var messageId = document.message_edit.message_id.value;
	var messBlock = document.getElementById("message_block_" + messageId);
	var messEdit = document.getElementById("message_edit_" + messageId);
	var editForm = document.getElementById("message_edit_block");
	if (messageId != "" && messBlock && messBlock && editForm) {
		document.message_edit.message_id.value = "";
		var editFormHTML = messEdit.innerHTML;
		messEdit.innerHTML = "";
		messEdit.style.display = "none";
		editForm.innerHTML = editFormHTML;
		messBlock.style.display = "block";
	}

	
}