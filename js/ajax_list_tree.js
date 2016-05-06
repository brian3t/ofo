function processBranch(branch_id, tree_name, response_url, page_number) {
	var div_id       = tree_name + "_branch_" + branch_id;
	var title_div_id = tree_name + "_branch_title_" + branch_id;
	var div          = document.getElementById(div_id);
	var title_div    = document.getElementById(title_div_id);
	
	if (page_number) {
		if (title_div) {
			removeClassName(title_div, "closed");
			removeClassName(title_div, "opened");
			addClassName(title_div, "processed");
		}
		loadAjax(div_id,  response_url+'&branch_id='+branch_id + "&page_number=" + page_number, loadedBranch, new Array(tree_name, branch_id));		
	} else {
		if (hasClassName(title_div, "closed")) {
			removeClassName(title_div, "closed");
			addClassName(title_div, "processed");
			loadAjax(div_id,  response_url+'&branch_id='+branch_id, loadedBranch, new Array(tree_name, branch_id));
		} else if (hasClassName(title_div, "opened")) {
			removeClassName(title_div, "opened");
			addClassName(title_div, "closed");
			div.style.display = 'none';
		} else {
			div.innerHTML = 'Processing...';
			div.style.display = 'block';
		}
	}
}	

function loadedBranch(response_text, params) {
	var tree_name = params[0];
	var branch_id = params[1];
	
	var div_id       = tree_name + "_branch_" + branch_id;
	var title_div_id = tree_name + "_branch_title_" + branch_id;
	var div          = document.getElementById(div_id);
	var title_div    = document.getElementById(title_div_id);

	div.style.display = 'block';
	if (title_div) {
		removeClassName(title_div, "processed");
		addClassName(title_div, "opened");
	}
	markSelectedLeafes(tree_name);
}

function processLeaf(id, text, tree_name) {
	if(document.getElementById(tree_name + '_action_object_id')) {
		var title_div_id = tree_name + "_leaf_title_" + id;
		var action_object_id = document.getElementById(tree_name + '_action_object_id').value;
		var action_object_type = document.getElementById(tree_name + '_action_object_type').value;
		if (action_object_type = 'ul') {
			var child_id =  action_object_id + '_' + id;
			var list = document.getElementById(action_object_id);
			if (hasClassNameByID(title_div_id, "plus")) {
				removeClassNameByID(title_div_id, "plus");
				addClassNameByID(title_div_id, "minus");
				var child = document.createElement('li');
				child.id  = child_id;
				child.className = "tree_leaf";
				child.innerHTML = addLeafFormat(id, text, tree_name, action_object_id);
				list.appendChild(child);
			} else {		
				removeClassNameByID(title_div_id, "minus");
				addClassNameByID(title_div_id, "plus");
				if (list.hasChildNodes()) {
					list.removeChild(document.getElementById(child_id));
				}
			}
		}
	}
}

function markSelectedLeafes(tree_name) {
	if(document.getElementById(tree_name + '_action_object_id')) {
		var action_object_id = document.getElementById(tree_name + '_action_object_id').value;
		var action_object_type = document.getElementById(tree_name + '_action_object_type').value;
		
		if (action_object_type = 'ul') {
			var list = document.getElementById(action_object_id);			
			if (list.hasChildNodes()) {
				var children = list.childNodes; 
				for (var i=0; i<children.length; i++){
					if (children[i].id) {
						var id = children[i].id.substring(action_object_id.length+1);						
						var title_div_id = tree_name + "_leaf_title_" + id;				
						removeClassNameByID(title_div_id, "plus");
						addClassNameByID(title_div_id, "minus");							
					}					
				}				
			}		
		}
	}
}

function removeListItem(id, tree_name, action_object_id) {
	var title_div_id = tree_name + "_leaf_title_" + id;
	var child_id = action_object_id + '_' + id;
	var list = document.getElementById(action_object_id);
	removeClassNameByID(title_div_id, "minus");
	addClassNameByID(title_div_id, "plus");
	if (list.hasChildNodes()) {					
		list.removeChild(document.getElementById(child_id));
	}
	return false;
}

function saveListItems(field_name, tree_name, action_object_id) {
	var target = document.getElementsByName(field_name)[0];
	var list = document.getElementById(action_object_id);
	if (list.hasChildNodes()) {
		var children    = list.childNodes;
		var numchildren = children.length;   
		target.value = "";	
		for(var i = 0; i <numchildren; i++) {
			if (children[i].id) {
				var id = children[i].id.substring(action_object_id.length+1);
				if(target.value.length > 0) target.value += ",";
				target.value += id;			
			}
		}   
	} else {
		target.value = "";
	}
	return false;
}

var selectedListItem = new Array();
var ctrlPressed  = 0;
var altPressed   = 0;
var shiftPressed = 0;
	

function mouseDown(e) {
	ctrlPressed  = 0;
	altPressed   = 0;
	shiftPressed = 0;
	if (parseInt(navigator.appVersion)>3) {

		var evt = navigator.appName=="Netscape" ? e:event;

		if (navigator.appName=="Netscape" && parseInt(navigator.appVersion)==4) {
			// NETSCAPE 4 CODE
			var mString =(e.modifiers+32).toString(2).substring(3,6);
			shiftPressed=(mString.charAt(0)=="1");
			ctrlPressed =(mString.charAt(1)=="1");
			altPressed  =(mString.charAt(2)=="1");
		} else {
			// NEWER BROWSERS [CROSS-PLATFORM]
			shiftPressed=evt.shiftKey;
			altPressed  =evt.altKey;
			ctrlPressed =evt.ctrlKey;
		}
	}
	if (altPressed || shiftPressed || ctrlPressed) {
		return false;
	}
}

function selectListItem(id, tree_name, action_object_id) {
	var current_index = -1;
	var child_id   = action_object_id + '_' + id;
	var child      = document.getElementById(child_id);
	var child_node = nextElement(nextNode(child.childNodes[0]));
	if (ctrlPressed) {		
		if (selectedListItem) {
			for(var j=0; j<selectedListItem.length; j++) {
				var prev_child_id = selectedListItem[j];
				if (prev_child_id) {
					if (prev_child_id == id) {
						current_index = j;
					}
				}
			}
			if (current_index == -1) {
				addClassName(child_node, "selected");
				selectedListItem[selectedListItem.length] = id;
			} else {
				removeClassName(child_node, "selected");
				selectedListItem[current_index] = false;
			}
		}		
	} else {
		if (selectedListItem) {
			for(var j=0; j<selectedListItem.length; j++) {
				var prev_child_id = action_object_id + '_' + selectedListItem[j];
				if (prev_child_id) {
					var prev_child = document.getElementById(prev_child_id );
					if (prev_child) {						
						var prev_child_node = nextElement(nextNode(prev_child.childNodes[0]));
						removeClassName(prev_child_node, "selected");
						if (prev_child_id == child_id) {
							current_index = j;
						}
					}
				}
			}
		}
		if (current_index == -1 ) {
			addClassName(child_node, "selected");
			selectedListItem = new Array(id);
		} else {
			removeClassName(child_node, "selected");
			selectedListItem = new Array();
		}
	}
	return false;
}

function moveSelectedListItem(direction, tree_name, action_object_id) {
	var ids = Array();
	for(var j=0; j<selectedListItem.length; j++) {
		var id = selectedListItem[j];
		if (id) {
			ids[id] = 1;
		}
	}
	
	if (ids.length) {
		var list = document.getElementById(action_object_id);
		var moved_ids = Array();
		if (list.hasChildNodes()) {
			var children = list.childNodes;    		
			if (direction > 0) {
				for (var i=children.length - 1; i>=0; i--) {
					var c1 = nextNode(children[i]);
					var id = c1.id.substring(action_object_id.length+1);
					if (ids[id] == 1) {
						var c2;
						ids[id] = 0;
						moved_ids[id] = 1;
						c2 = nextElement(c1);
						if(c2 && c2.id) {
							var c2_id = c2.id.substring(action_object_id.length+1);
							if (!(moved_ids[c2_id] == 1)) {
				        		var c3 = document.createElement("li");
				        		c3.id = c1.id;
				        		c3.innerHTML = c1.innerHTML;
				        		c3.className = "tree_leaf";
				        		var c4 = document.createElement("li");
				        		c4.id = c2.id;
				        		c4.className = "tree_leaf";
				        		c4.innerHTML = c2.innerHTML;
				        		list.appendChild(c3);
				        		list.appendChild(c4);
				        		list.replaceChild(c4, c1);
				        		list.replaceChild(c3, c2);
							}
			        	}
					}
				}
			} else {
				for (var i =0; i<children.length; i++) {
					var c1 = nextNode(children[i]);
					var id = c1.id.substring(action_object_id.length+1);
					if (ids[id] == 1) {
						var c2;
						ids[id] = 0;
						moved_ids[id] = 1;
						c2 = prevElement(c1);
						if(c2 && c2.id) {
							var c2_id = c2.id.substring(action_object_id.length+1);
							if (!(moved_ids[c2_id] == 1)) {
				        		var c3 = document.createElement("li");
				        		c3.id = c1.id;
				        		c3.innerHTML = c1.innerHTML;
				        		c3.className = "tree_leaf";
				        		var c4 = document.createElement("li");
				        		c4.id = c2.id;
				        		c4.className = "tree_leaf";
				        		c4.innerHTML = c2.innerHTML;
				        		list.appendChild(c3);
				        		list.appendChild(c4);
				        		list.replaceChild(c4, c1);
				        		list.replaceChild(c3, c2);
							}
			        	}
					}
				}
			}
		}
	}

	return false;
	
}