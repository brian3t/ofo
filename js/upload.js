// upload form javacript
function closeWindow(filepath)
{
	var formId = document.upload.fid.value;
	var fileType = document.upload.filetype.value;
	var controlName = document.upload.control_name.value;
	if (formId != "") {
		window.opener.setFilePath(filepath, fileType, controlName, formId);
	} else {
		window.opener.setFilePath(filepath, fileType, controlName);
	}
	if (document.upload.image_large_generated && document.upload.image_large_generated.value == 1) {
		var fileName = document.upload.filename.value;
		window.opener.setFilePath("images/products/large/" + fileName, "product_large", controlName);
	}
	if (document.upload.image_small_generated && document.upload.image_small_generated.value == 1) {
		var fileName = document.upload.filename.value;
		window.opener.setFilePath("images/products/small/" + fileName, "product_small", controlName);
	}
	if (document.upload.image_tiny_generated && document.upload.image_tiny_generated.value == 1) {
		var fileName = document.upload.filename.value;
		window.opener.setFilePath("images/products/tiny/" + fileName, "product_tiny", controlName);
	}
	window.opener.focus();
	window.close();
}