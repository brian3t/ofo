// search form javacript
function clearFields()
{
	if (document.search_form.search_string) {
		document.search_form.search_string.value = "";
	}
	if (document.search_form.s_tit) {
		document.search_form.s_tit.checked = true;
	}
	if (document.search_form.s_sds) {
		document.search_form.s_sds.checked = false;
	}
	if (document.search_form.s_fds) {
		document.search_form.s_fds.checked = false;
	}
	var prQty = document.search_form.pq.value;
	for(var i = 0; i <= prQty; i++) {
		if(document.search_form.elements["pv_" + i]) {
			document.search_form.elements["pv_" + i].value = "";
		}
	}
	var fQty = document.search_form.fq.value;
	for(var i = 0; i <= fQty; i++) {
		if(document.search_form.elements["fv_" + i]) {
			document.search_form.elements["fv_" + i].value = "";
		}
	}
	if (document.search_form.search_category_id) {
		document.search_form.search_category_id.selectedIndex = 0;
	}
	if (document.search_form.manf) {
		document.search_form.manf.selectedIndex = 0;
	}
	if (document.search_form.lprice) {
		document.search_form.lprice.value = "";
	}
	if (document.search_form.hprice) {
		document.search_form.hprice.value = "";
	}
	if (document.search_form.lweight) {
		document.search_form.lweight.value = "";
	}
	if (document.search_form.hweight) {
		document.search_form.hweight.value = "";
	}
}
