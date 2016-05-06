function create_event_listener(object){
	is_addEventListener=false;
	is_attachEvent=false;
	try {
		if (addEventListener){
			is_addEventListener=true;
		}
	} catch (e) {}
	try {
		if (attachEvent){
			is_attachEvent=true;
		}
	} catch (e) {}
	if (is_addEventListener){
		document.forms[object.formname].addEventListener("click", eval(object.object_name+'_event_click'), false);
	} else if (is_attachEvent){
		document.forms[object.formname].attachEvent("onclick", eval(object.object_name+'_event_click'));
	}
}

function select()
{
	this.last_id = 0;
	this.name_prefix = '';
	this.formname = '';
	this.object_name = '';
	this.after_select_action = '';
	this.final_action = '';

	this.get_id = get_id;
	this.change_box_Status = change_box_Status;
	this.valid_box = valid_box;

	is_addEventListener=false;
	is_attachEvent=false;
	try {
		if (addEventListener){
			is_addEventListener=true;
		}
	} catch (e) {}
	try {
		if (attachEvent){
			is_attachEvent=true;
		}
	} catch (e) {}
	
	if (is_addEventListener){
		this.create_event_click = create_event_click_dom2;
	}else if (is_attachEvent){
		this.create_event_click = create_event_click_ie;
	}else{
		this.create_event_click = create_event_click_empty;
	}

} 

function get_id(Element_obj){
	var current_id = parseInt(Element_obj.name.substring(this.name_prefix.length, Element_obj.name.length));
	if (current_id == 0 || isNaN(current_id)) {current_id=1;}
	return current_id;
}

function change_box_Status(last_id, current_id){
	if (last_id == current_id){return;}
	var new_stasus = document.forms[this.formname].elements[this.name_prefix+current_id].checked;
	if (last_id > current_id){
		var start_id=current_id;
		var finish_id=last_id;
	}else{
		var finish_id=current_id;
		var start_id=last_id;
	}
	if (start_id == 0) {start_id=1;}
	for (var i = start_id; i <= finish_id; i++) {
		document.forms[this.formname].elements[this.name_prefix+i].checked = new_stasus;
	}
}

function valid_box(Element_obj){
	var valid_value = false;
	try {
		if (this.name_prefix == Element_obj.name.substring(0, this.name_prefix.length)){
			valid_value = true;
		}else{
			valid_value = false;
		}
	} catch (e) {}
	return valid_value;
}

function create_event_click_dom2(){
	document.writeln('<script language= "JavaScript" type= "text/javascript">');
	document.writeln('<!--');
	document.writeln('function '+this.object_name+'_event_click(event){');
	document.writeln('if ('+this.object_name+'.valid_box(event.target)) {');
	document.writeln('if (event.shiftKey) {');
	document.writeln(this.object_name+'.change_box_Status('+this.object_name+'.last_id, '+this.object_name+'.get_id(event.target));');
	document.writeln(this.object_name+'.last_id = '+this.object_name+'.get_id(event.target);');
	document.writeln(this.after_select_action);
	document.writeln('}');
	document.writeln(this.object_name+'.last_id = '+this.object_name+'.get_id(event.target);');
	document.writeln(this.final_action);
	document.writeln('}');
	document.writeln('}');
	document.writeln('//-->');
	document.writeln('</script>');

}

function create_event_click_ie(){
	document.writeln('<script language= "JavaScript" type= "text/javascript">');
	document.writeln('<!--');
	document.writeln('function '+this.object_name+'_event_click(){');
	document.writeln('if ('+this.object_name+'.valid_box(event.srcElement)) {');
	document.writeln('if (event.shiftKey) {');
	document.writeln(this.object_name+'.change_box_Status('+this.object_name+'.last_id, '+this.object_name+'.get_id(event.srcElement));');
	document.writeln(this.object_name+'.last_id = '+this.object_name+'.get_id(event.srcElement);');
	document.writeln(this.after_select_action);
	document.writeln('}');
	document.writeln(this.object_name+'.last_id = '+this.object_name+'.get_id(event.srcElement);');
	document.writeln(this.final_action);
	document.writeln('}');
	document.writeln('}');
	document.writeln('//-->');
	document.writeln('</script>');
}

function create_event_click_empty(){
}