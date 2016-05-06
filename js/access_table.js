var MAX_LEVELS = 8;

function enableTableCells(cell_name, cell_ids) {	
	for(var level_number = 0;  level_number < MAX_LEVELS;  level_number++) {
		var level_value  = Math.pow(2, level_number);
		onAllClick(cell_name, cell_ids,  level_value, 1);
		for(var i =0; i<cell_ids['t'].length; i++) {
			onTypeClick(cell_name, cell_ids, cell_ids['t'][i], level_value, 1);
		}
	}
}
function onAllClick(cell_name, cell_ids,  level_value, check) {
	var cell_for_all = document.record[cell_name + "_all_" +  level_value];
	if (cell_for_all) {
		for(var i =0; i<cell_ids['s'].length; i++) {
			var cell = document.record[cell_name + "_s_" + cell_ids['s'][i] + "_" + level_value];
			if (cell) {
				if (cell_for_all.checked) {
					cell.disabled=true;
					if (check) {
						cell.checked=true;
					}
				} else {
					cell.disabled=false;
				}
			}
		}
		
		for(var i =0; i<cell_ids['t'].length; i++) {
			var cell = document.record[cell_name + "_t_" + cell_ids['t'][i] + "_" + level_value];
			if (cell) {
				if (cell_for_all.checked) {
					cell.disabled=true;
					if (check) {
						cell.checked=true;
					}
				} else {
					cell.disabled=false;
					if (cell_ids[cell_ids['t'][i]] && cell.checked) {
						for(var j =0; j<cell_ids[cell_ids['t'][i]].length; j++) {
							var cell = document.record[cell_name + "_s_" + cell_ids[cell_ids['t'][i]][j] + "_" + level_value];
							if (cell) {								
								cell.disabled=true;
								if (check) {
									cell.checked=true;
								}
							}
						}
					}
				}
			}
		}
	}
}

function onTypeClick(cell_name, cell_ids, type_id, level_value, check) {
	var cell_for_all = document.record[cell_name + "_t_" +  type_id + "_"+  level_value];
	if (cell_for_all && cell_ids[type_id]) {	
		for(var i =0; i<cell_ids[type_id].length; i++) {
			var cell = document.record[cell_name + "_s_" + cell_ids[type_id][i] + "_" + level_value];
			if (cell) {
				if (cell_for_all.checked) {
					cell.disabled=true;
					if (check) {
						cell.checked=true;
					}
				} else {
					cell.disabled=false;
				}
			}
		}
	}
}