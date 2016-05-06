function openWindow(pagename, filetype)
{
	var uploadWin = window.open (pagename + '?filetype=' + filetype, 'uploadWin', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=400,height=300');
	uploadWin.focus();
}

function setFilePath(filepath, filetype)
{

	if(filepath != "" && filetype == "personal_image")
	{
		document.user_profile.personal_image.value = filepath;
		document.user_profile.personal_image.focus();
	}
}

function setFileName(filename, filetype)
{
	if(filename != "" && filetype == "personal")
	{
		document.user_profile.personal_image.value = "images/users/" + filename;
		document.user_profile.personal_image.focus();
	}
}

function checkSame()
{
	var sameChecked = document.user_profile.same_as_personal.checked;
	if(sameChecked) {
		var fieldName = "";
		var fields = new Array("name", "first_name", "last_name", "company_id", "company_name", "email", 
			"address1", "address2", "city", "province", "address1", "state_code", "state_id", "zip", "country_code", "country_id",
			"phone", "daytime_phone", "evening_phone", "cell_phone", "fax");
		var orderForm = document.user_profile;
		for (var i = 0; i < fields.length; i++) {
			fieldName = fields[i];
			if (orderForm.elements[fieldName] && orderForm.elements["delivery_" + fieldName]) {
				orderForm.elements["delivery_" + fieldName].value = orderForm.elements[fieldName].value;
			}
		}
	}
}

function uncheckSame()
{
	if (document.user_profile.same_as_personal) {
		document.user_profile.same_as_personal.checked = false;
	}
}

function changeAffiliateCode()
{
	var siteURL = document.user_profile.site_url.value;
	var affiliateHelp = document.user_profile.affiliate_help.value;
	var affiliateCode = document.user_profile.affiliate_code.value;
	if (affiliateCode == "") {
		affiliateCode = "type_your_code_here";
	}
	var affiliateURL = siteURL + "?af=" + affiliateCode;
	affiliateHelp = affiliateHelp.replace("\{affiliate_url\}", affiliateURL);

	var affiliateHelpConrol = document.getElementById("affiliate_help_info");
	if (affiliateHelpConrol) {
		affiliateHelpConrol.innerHTML = affiliateHelp;
	}
}
