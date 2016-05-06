<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_manual_category.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/friendly_functions.php");
	include_once ($root_folder_path . "includes/sites_table.php");
	include_once ($root_folder_path . "includes/access_table.php");	
	include_once ("./admin_common.php");

	check_admin_security("manual");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_manual_category.html");

	$t->set_var("admin_manual_href", "admin_manual.php");
	$t->set_var("admin_manual_category_href", "admin_manual_category.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CATEGORY_MSG, CONFIRM_DELETE_MSG));
	
	$html_editor = get_setting_value($settings, "html_editor", 1);
	$t->set_var("html_editor", $html_editor);	
	
	$return_page = "admin_manual.php";
	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	$category_id = get_param("category_id");

	$r = new VA_Record($table_prefix . "manuals_categories");
	$r->return_page = $return_page;
	if (get_param("apply")) {
		$r->redirect = false;
	}
		
	$r->add_where("category_id", INTEGER);
	$r->add_textbox("category_name", TEXT, CATEGORY_NAME_MSG);
	$r->change_property("category_name", REQUIRED, true);
	$r->change_property("category_name", MAX_LENGTH, 255);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	
	// Get orders of categories
	$orders = array();
	$sql = "SELECT * FROM ".$table_prefix."manuals_categories ";
	$sql .= " ORDER BY category_order";
	
	$db->query($sql);
	$i = 0;
	$order = 0;
	while ($db->next_record()) {
		$order = $db->f("category_order");
		$orders[] = array($order, ++$i);
	}
	if ($category_id == "") {
		$orders[] = array(++$order, ++$i);
	}
	
	$r->add_select("category_order", INTEGER, $orders, CATEGORY_ORDER_MSG);
	$r->change_property("category_order", REQUIRED, true);
	
	$r->add_textbox("meta_title", TEXT, META_TITLE_MSG);
	$r->change_property("meta_title", MAX_LENGTH, 255);
	
	$r->add_textbox("meta_keywords", TEXT, ADMIN_META_KEYWORD_MSG);
	$r->change_property("meta_keywords", MAX_LENGTH, 255);
	
	$r->add_textbox("meta_description", TEXT, META_DESCRIPTION_MSG);
	$r->change_property("meta_description", MAX_LENGTH, 255);
	
	$r->add_textbox("admin_id_added_by", INTEGER, ADMIN_ID_ADDED_BY_MSG);
	$r->change_property("admin_id_added_by", USE_IN_INSERT, true);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	
	$r->add_textbox("admin_id_modified_by", INTEGER, ADMIN_ID_MODIFIED_BY_MSG);
	$r->change_property("admin_id_modified_by", USE_IN_INSERT, true);
	$r->change_property("admin_id_modified_by", USE_IN_UPDATE, true);

	$r->add_textbox("date_added", DATETIME, MODIFICATION_DATE_MSG);
	$r->change_property("date_added", USE_IN_INSERT, true);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	
	$r->add_textbox("date_modified", DATETIME, MODIFICATION_DATE_MSG);
	$r->change_property("date_modified", USE_IN_INSERT, true);
	$r->change_property("date_modified", USE_IN_UPDATE, true);

	$r->add_checkbox("access_level", INTEGER);
	$r->add_checkbox("guest_access_level", INTEGER);
	$r->add_checkbox("sites_all", INTEGER);

	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);

	$r->set_event(AFTER_REQUEST, "set_manual_category_data");
	
	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($category_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "manuals_categories_sites ";
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}

	$r->set_event(BEFORE_SHOW,   "set_values_before_show");
	$r->set_event(AFTER_VALIDATE, "after_validate_category");
	$r->set_event(AFTER_INSERT,   "after_save_category");
	$r->set_event(AFTER_UPDATE,   "after_save_category");
	$r->set_event(BEFORE_DELETE, "before_delete_category");
	$r->set_event(AFTER_DEFAULT, "default_category");
		
	$access_table = new VA_Access_Table($settings["admin_templates_dir"], "access_table.html");
	$access_table->set_access_levels(
		array(
			1 => array(VIEW_MSG, VIEW_CATEGORY_IN_THE_LIST_MSG), 
			2 => array(ACCESS_LIST_MSG, ACCESS_CATEGORY_DETAILS_AND_ITEMS_LIST_MSG),
			4 => array(ACCESS_DETAILS_MSG, ACCESS_CATEGORY_ITEMS_DETAILS_MSG)
		)
	);
	$access_table->set_tables("manuals_categories", "manuals_categories_types",  "manuals_categories_subscriptions", "category_id", null, $category_id);
	
	$sites_table = new VA_Sites_Table($settings["admin_templates_dir"], "sites_table.html");
	$sites_table->set_tables("manuals_categories", "manuals_categories_sites", "category_id", null, $category_id);
	
	$r->process();
	
	$sites_table->parse("sites_table", $r->get_value("sites_all"));
	$has_any_subscriptions = $access_table->parse("subscriptions_table", $r->get_value("access_level"), $r->get_value("guest_access_level"));
			
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$tabs = array(
		"general"       => array( "title" => EDIT_CATEGORY_MSG),
		"sites"         => array( "title" => ADMIN_SITES_MSG, "show" => $sitelist),
		"subscriptions" => array( "title" => ACCESS_LEVELS_MSG, "show" => $has_any_subscriptions)
	);
	parse_admin_tabs($tabs, $tab);	


	$t->pparse("main");
	
	function after_validate_category() {
		global $r, $access_table, $table_prefix, $db;	
		
		$category_id = $r->get_value("category_id");
		if (!$category_id) {
			$category_id = get_db_value("SELECT MAX(category_id) FROM " . $table_prefix . "manuals_categories") + 1;
			$r->set_value("category_id", $category_id);
		}
		$admin_id = get_session("session_admin_id");
		$r->set_value("admin_id_modified_by", $admin_id);
		$r->set_value("admin_id_added_by", $admin_id);
		$r->set_value("date_modified", va_time());
		$r->set_value("date_added", va_time());
		$r->set_value("access_level", $access_table->all_selected_access_level);
		$r->set_value("guest_access_level", $access_table->guest_selected_access_level);
				
		$selected_order = $r->get_value("category_order");
		$saved_order = get_param("saved_category_order");
		
		if ($saved_order == 0 || $saved_order != $selected_order) {
			// Increase menu_order of selected item and all other, which have
			// menu_item_order greater than selected and the same parent_id
			if ($saved_order > $selected_order || $saved_order == 0) {
				$increase_order_sql  = " UPDATE " . $table_prefix . "manuals_categories ";
				$increase_order_sql .= " SET category_order = category_order + 1 ";
				$increase_order_sql .= " WHERE ";
				$increase_order_sql .= " category_order >=" . $db->tosql($selected_order, INTEGER);
				$r->set_value("category_order", $selected_order);
			} else {
				$increase_order_sql = "UPDATE " . $table_prefix . "manuals_categories ";
				$increase_order_sql .= "SET category_order = category_order + 2 ";
				$increase_order_sql .= "WHERE ";
				$increase_order_sql .= "category_order >" . $db->tosql($selected_order, INTEGER);
				$r->set_value("category_order", $selected_order + 1);
			}
			$db->query($increase_order_sql);
		}

	}
	
	function after_save_category() {
		global $r, $access_table, $sites_table;
		
		$category_id = $r->get_value("category_id");
		$access_table->save_values($category_id, false);
		$sites_table->save_values($category_id, $r->get_value("sites_all"), false);
	}
	
	/**
	 * Function remove manuals and its articles, before category removing
	 *
	 */
	function before_delete_category() {
		global $db, $category_id, $table_prefix;
		// Get manuals of the category
		$sql = "SELECT manual_id FROM ". $table_prefix."manuals_list WHERE category_id = " . $db->tosql($category_id, INTEGER);
		$db->query($sql);
		$manuals = array();
		if ($db->next_record()) {
			$where_arr = array();
			do {
				$manual_id = $db->f("manual_id");
				$where_arr[] = "manual_id = " . $db->tosql($manual_id, INTEGER);
			} while ($db->next_record());
			
			// Remove articles

			if (!empty($where_arr)) {
				$sql = "DELETE FROM " . $table_prefix . "manuals_articles WHERE " . implode(" AND ", $where_arr);
				$db->query($sql);
			}
		}
		// remove sites
		$db->query("DELETE FROM " . $table_prefix . "manuals_categories_sites WHERE category_id=" . $db->tosql($category_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "manuals_categories_types WHERE category_id=" . $db->tosql($category_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "manuals_categories_subscriptions WHERE category_id=" . $db->tosql($category_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "manuals_list WHERE category_id=" . $db->tosql($category_id, INTEGER));
		
	}
	
	/**
	 * Function calls before form showing.
	 * Assignes additional parameters
	 *
	 */
	function set_values_before_show() {
		global $r;
		global $t;

		$t->set_var("saved_category_order", $r->get_value("category_order"));
	}
	
	function default_category() {
		global $r;
		$r->set_value("access_level", 255);
		$r->set_value("guest_access_level", 255);
		$r->set_value("sites_all", 1);
	}

?>