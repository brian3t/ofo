var MAX_LEVELS = 5;

function enableTableCells(cell_name, cell_ids) {	
	for(var level_number = 0;  level_number < MAX_LEVELS;  level_number++) {
		var level_value  = Math.pow(2, level_number);
		var cell_for_all = document.record[cell_name + "_all_" +  level_value];
		if (cell_for_all && cell_for_all.checked) {	
			for(var i =0; i<cell_ids.length; i++) {
				var cell = document.record[cell_name + "_" + cell_ids[i] + "_" + level_value];
				cell.disabled=true;
			}
		}
	}
}
function onAllClick(cell_name, cell_ids,  level_value) {
	var cell_for_all = document.record[cell_name + "_all_" +  level_value];
	if (cell_for_all) {	
		for(var i =0; i<cell_ids.length; i++) {
			var cell = document.record[cell_name + "_" + cell_ids[i] + "_" + level_value];
			if (cell_for_all.checked) {
				cell.disabled=true;
			} else {
				cell.disabled=false;
			}
		}
	}
}
function markTableCell(cell_name, cell_ids, subscription_id, level_value, checked) {
	var cell_for_all = document.record[cell_name + "_type_" +  subscription_id];
	if (!checked && cell_for_all.checked) {
		cell_for_all.checked = checked;
	}
	var cell_for_all = document.record[cell_name + "_level_" +  level_value];
	if (!checked && cell_for_all.checked) {
		cell_for_all.checked = checked;
	}
	
	var cell_for_all = document.record[cell_name + "_every"];
	if (!checked && cell_for_all.checked) {
		cell_for_all.checked = checked;
	}
	
}
function markTableCells(cell_name, cell_ids, style, value) {
	if (style == "vert") {
		var cell_for_all = document.record[cell_name + "_level_" +  value];
		for(var i =0; i<cell_ids.length; i++) {
			var cell = document.record[cell_name + "_" + cell_ids[i] + "_" + value];
			if (cell_for_all.checked) {
				cell.checked=true;
			} else {
				cell.checked=false;
			}
		}
	} else if (style == "horz") {
		var cell_for_all = document.record[cell_name + "_type_" +  value];
		for(var level_number = 0;  level_number < MAX_LEVELS;  level_number++) {
			var level_value  = Math.pow(2, level_number);		
			var cell = document.record[cell_name + "_" + value + "_" + level_value];
			if (cell) {
				if (cell_for_all.checked) {
					cell.checked=true;
				} else {
					cell.checked=false;
				}
				if (value == "all") {
					cell.onclick();
				}
			}
		}
	} else if (style == "every") {
		var cell_for_all = document.record[cell_name + "_every"];
		for(var level_number = 0;  level_number < MAX_LEVELS;  level_number++) {
			var level_value  = Math.pow(2, level_number);
			for(var i =0; i<cell_ids.length; i++) {
				var cell = document.record[cell_name + "_" + cell_ids[i] + "_" + level_value];
				if (cell) {
					if (cell_for_all.checked) {
						cell.checked=true;
					} else {
						cell.checked=false;
					}
				}
			}
			
		}
	}
}
