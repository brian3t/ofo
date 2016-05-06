<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_config.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$is_admin_path = true; // use admin path to the root of the web folder
	$root_folder_path = "../";
	$tracking_ignore = true; // if it set to true ignoring statistics for such pages

	define("WHERE_DB_FIELD",   1);
	define("USUAL_DB_FIELD",   2);
	define("FOREIGN_DB_FIELD", 3);
	define("HIDE_DB_FIELD",    4);
	define("RELATED_DB_FIELD", 5);
	define("CUSTOM_FIELD",     6);

	$site_id  = 1;
	/**
	 * Compare two versions in string format. Returns 1 (if first is bigger), 2 (if second), or 0 if equal
	 *
	 * @param string $version1
	 * @param string $version2
	 * @return integer
	 */	
	 
	 
	function update_rating($table_name, $column_name, $review_id)
	{
		global $db;
		global $table_prefix;

		$sql = "SELECT " . $column_name . " FROM " . $table_name . " WHERE review_id=" . $db->tosql($review_id, INTEGER);		
		$column_id = get_db_value($sql);

		$sql = " SELECT COUNT(*) FROM " . $table_name . " WHERE approved=1 AND rating <> 0 AND " . $column_name . "=" . $db->tosql($column_id, INTEGER);
		$total_rating_votes = get_db_value($sql);

		$sql = " SELECT SUM(rating) FROM " . $table_name . " WHERE approved=1 AND rating <> 0 AND " . $column_name . "=" . $db->tosql($column_id, INTEGER);
		$total_rating_sum = get_db_value($sql);
		if(!strlen($total_rating_sum)) $total_rating_sum = 0;

		$average_rating = $total_rating_votes ? $total_rating_sum / $total_rating_votes : 0;

		if ($column_name == "item_id") {
			$sql  = " UPDATE " . $table_prefix . "items ";
			$sql .= " SET votes=" . $total_rating_votes . ", points=" . $total_rating_sum . ", ";
			$sql .= " rating=" . $average_rating;
			$sql .= " WHERE item_id=" . $db->tosql($column_id, INTEGER);
			$db->query($sql);
		} else {
			$sql  = " UPDATE " . $table_prefix . "articles ";
			$sql .= " SET total_votes=" . $total_rating_votes . ", total_points=" . $total_rating_sum . ", ";
			$sql .= " rating=" . $average_rating;
			$sql .= " WHERE article_id=" . $db->tosql($column_id, INTEGER);
			$db->query($sql);
		}
	}
	 
	 
	function delete_tickets($support_id)
	{
		global $db, $table_prefix;
		
		// delete attachments if available
		$sql = "SELECT file_path FROM " . $table_prefix . "support_attachments WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$file_path = $db->f("file_path");
			@unlink($file_path);
		}
		
		$db->query("DELETE FROM " . $table_prefix . "support_attachments WHERE support_id=" . $db->tosql($support_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "support_messages WHERE support_id=" . $db->tosql($support_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "support WHERE support_id=" . $db->tosql($support_id, INTEGER));
	}
	 
 	function comp_vers($version1, $version2)
	{
		$first_numbers = explode(".", $version1);
		$second_numbers = explode(".", $version2); 

		if (count($first_numbers) > count($second_numbers)) {
			for ($i = 0; isset($first_numbers[$i]); $i++) {
				if (!isset($second_numbers[$i])) $second_numbers[$i] = "0";
			}
		} else {
			for ($i = 0; isset($second_numbers[$i]); $i++) {
				if (!isset($first_numbers[$i])) $first_numbers[$i] = "0";
			}
		}
			
		foreach ($first_numbers as $key => $value) {
			if ($first_numbers[$key] > $second_numbers[$key]) {
				return 1;
			} elseif ($first_numbers[$key] < $second_numbers[$key]) {
				return 2;
			}
		}
	
		return 0;
	}

	/**
	 * Return array with permissions for the Privilege Group of currently logged administrator
	 *
	 * @param void
	 * @return array
	 */	
	function get_permissions() 
	{
		global $db, $table_prefix;

		$permissions = array();
		$privilege_id = get_session("session_admin_privilege_id");
		$sql  = " SELECT block_name, permission FROM " . $table_prefix . "admin_privileges_settings ";
		$sql .= " WHERE privilege_id=" . $db->tosql($privilege_id, INTEGER, true, false);
		$db->query($sql);
		while($db->next_record()) {
			$block_name = $db->f("block_name");
			$permissions[$block_name] = $db->f("permission");
		}
		
		return $permissions;
	}

	/**
	 * Delete users with ids separated by comma
	 *
	 * @param string $user_ids
	 * @return void
	 */	
	function delete_users($users_ids) 
	{
		global $db, $table_prefix;
		$db->query("DELETE FROM " . $table_prefix . "users WHERE user_id IN (" . $db->tosql($users_ids, TEXT, false) . ")");
	}

	/**
	 * Return folder name of administrative scripts
	 *
	 * @param void
	 * @return string
	 */	
	function get_admin_dir()
	{
		$admin_folder = "";
		$request_uri = get_request_uri();
		$request_uri = preg_replace("/\/+/", "/", $request_uri);
		if (strpos($request_uri,"?")){
			$request_uri = substr($request_uri,0,strpos($request_uri,"?"));
		}
		$slash_position = strrpos($request_uri, "/");
		
		if ($slash_position !== false) {
			$request_path = substr($request_uri, 0, $slash_position);
			$slash_position = strrpos($request_path, "/");
			if ($slash_position !== false) {
				$admin_folder = substr($request_path, $slash_position + 1);
			}
		}
		if (strlen($admin_folder)) {
			$admin_folder .= "/";
		} else {
			$admin_folder = "admin/";
		}
		
		return $admin_folder;
	}


function parse_admin_tabs($tabs, $current_tab, $tabs_in_row = 10)
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