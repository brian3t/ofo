<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  tabs_functions.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


function parse_tabs($tabs, $current_tab, $tabs_in_row = 10)
{
	global $t;
	$tab_row = 0; $tab_number = 0; $active_tab = false;

	foreach ($tabs as $tab_name => $tab_info) {
		$tab_title = $tab_info["title"];
		$tab_show = isset($tab_info["show"]) ? $tab_info["show"] : true;
		if ($tab_show) {
			$tab_number++;
			$t->set_var("tab_id", "tab_" . $tab_name);
			$t->set_var("tab_name", $tab_name);
			$t->set_var("tab_title", $tab_title);
			if ($tab_name == $current_tab) {
				$active_tab = true;
				$t->set_var("tab_class", "adminTabActive");
				$t->set_var($tab_name . "_style", "display: block;");
			} else {
				$t->set_var("tab_class", "adminTab");
				$t->set_var($tab_name . "_style", "display: none;");
			}
			$t->parse("tabs", true);
			if ($tab_number % $tabs_in_row == 0) {
				$tab_row++;
				$t->set_var("row_id", "tab_row_" . $tab_row);
				if ($active_tab) {
					$t->rparse("tabs_rows", true);
				} else {
					$t->parse("tabs_rows", true);
				}
				$t->set_var("tabs", "");
			}
		} else {
			// hide all related blocks in case if tab hidden
			$t->set_var($tab_name . "_style", "display: none;");
		}
	}
	if ($tab_number % $tabs_in_row != 0) {
		$tab_row++;
		$t->set_var("row_id", "tab_row_" . $tab_row);
		if ($active_tab) {
			$t->rparse("tabs_rows", true);
		} else {
			$t->parse("tabs_rows", true);
		}
	}
	$t->set_var("current_tab", $current_tab);
	$t->set_var("tab", $current_tab);
}

?>