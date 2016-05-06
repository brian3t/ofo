
	function checkListFieldsAll(name)
	{
		if(list_fields_names) {
			for (var n = 0; n < list_fields_names.length; n++) {
				var name = list_fields_names[n];	
				checkListFields(name);		
			}		
		}
	}
	
	function checkListFields(name)
	{		
		checkListFieldsByTickName(name+'_all', name);
	}
	
	function checkListFieldsByTickName(tickname, name)
	{		
		if(document.record[tickname]) {
			var ticksAll = document.record[tickname].checked;
			if (ticksAll) {
				document.record['available_'+name].disabled = true;
				document.record['selected_'+name].disabled = true;
				document.record['add_'+name].disabled = true;
				document.record['remove_'+name].disabled = true;
			} else {
				document.record['available_'+name].disabled = false;
				document.record['selected_'+name].disabled = false;
				document.record['add_'+name].disabled = false;
				document.record['remove_'+name].disabled = false;
			}
		}
	}	
		
	function selectListFieldsAll()
	{
		if (list_fields_names) {
			for (var n = 0; n < list_fields_names.length; n++) {
				var name = list_fields_names[n];
				selectListFields(name);	
			}
		}		
	}
	
	function selectListFields(name)
	{
		if (document.record['selected_'+name]) {
			var totalOptions = document.record['selected_'+name].length;
			var return_value = "";
			for (var i = 0; i < totalOptions; i++) {
				document.record['selected_'+name].options[i].selected = true;
				if(i > 0) return_value += ",";
				return_value += document.record['selected_'+name].options[i].value;
			}
			document.record[name].value = return_value;
		}
	}

	function addListFields(name)
	{
		var formObj = document.record;
		if (formObj['available_'+name]) {
			var totalOptions = formObj['available_'+name].length;
			for(var i = totalOptions - 1; i >= 0; i--) {
				if (formObj['available_'+name].options[i].selected == true) {
					formObj['selected_'+name].options[formObj['selected_'+name].length] = new Option(formObj['available_'+name].options[i].text, formObj['available_'+name].options[i].value);
					formObj['available_'+name].options[i] = null;
				}
			}
		}
	}

	function removeListFields(name)
	{
		var formObj = document.record;
		var totalOptions = formObj['selected_'+name].length;
		for (var i = totalOptions - 1; i >= 0; i--) {
			if (formObj['selected_'+name].options[i].selected == true) {
				formObj['available_'+name].options[formObj['available_'+name].length] = new Option(formObj['selected_'+name].options[i].text, formObj['selected_'+name].options[i].value);
				formObj['selected_'+name].options[i] = null;
			}
		}
	}