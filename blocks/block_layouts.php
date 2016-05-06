<?php

function layouts($block_name, $block_prefix = "", $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $site_id, $table_prefix, $settings, $page_settings, $current_page;

	$layout_id = get_setting_value($settings, "layout_id", "");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$layouts_selection = get_setting_value($page_settings, "layouts_selection", 1);

	if ($block_name) {
	  $t->set_file("block_body", "block_layouts.html");
	}

	$remove_parameters = array();
	if ($friendly_urls && $page_friendly_url) {
		$current_page = $page_friendly_url . $friendly_extension;
		$query_string = transfer_params($page_friendly_params, true);
	} else {
		$query_string = transfer_params("", true);
	}
	$t->set_var("current_href", $current_page);

	$sql  = " SELECT l.layout_id, l.layout_name, l.user_layout_name ";
	$sql .= " FROM (" . $table_prefix . "layouts l ";
	if (isset($site_id))  {
		$sql .= " LEFT JOIN " . $table_prefix . "layouts_sites AS ls ON ls.layout_id=l.layout_id) ";
	} else {
		$sql .= " ) ";
	}
	$sql .= " WHERE l.show_for_user=1 ";
	if (isset($site_id))  {
		$sql .= " AND (l.sites_all=1 OR ls.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
	} else {
		$sql .= " AND l.sites_all=1 ";					
	}
	$sql .= " GROUP BY l.layout_id, l.layout_name, l.user_layout_name ";
	$sql .= " ORDER BY l.layout_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$row_layout_id = $db->f("layout_id");
		$layout_name = get_translation($db->f("layout_name"));
		$user_layout_name = get_translation($db->f("user_layout_name"));
		if (strlen($user_layout_name)) { $layout_name = $user_layout_name; }

		$layout_selected = ($layout_id == $row_layout_id) ? "selected" : "";
		$t->set_var("layout_selected", $layout_selected);
		$t->set_var("layout_id", $row_layout_id);
		$t->set_var("layout_name", $layout_name);

		$layout_query = $query_string;
		if($layout_query) {
			$layout_query .= "&";
		} else {
			$layout_query .= "?";
		}
 		$layout_query .= "set_layout_id=" . $row_layout_id; 
		$layout_url = $current_page . $layout_query;
		$t->set_var("layout_url", $layout_url);

		$t->parse($block_prefix . "layouts", true);
		$t->parse($block_prefix . "layouts_options", true);
	}

	if ($layouts_selection == 2) {
		$t->sparse($block_prefix . "layouts_select", false);
	} else {
		$t->sparse($block_prefix . "layouts_list", false);
	}

	if ($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>