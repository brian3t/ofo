<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_category.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once($root_folder_path . "includes/sites_table.php");
	include_once($root_folder_path . "includes/access_table.php");
	include_once("./admin_common.php");
		
	check_admin_security("ads");
	
 	$t = new VA_Template($settings["admin_templates_dir"]);
 	$t->set_file("main", "admin_ads_category.html");

	$t->set_var("admin_ads_category_href", "admin_ads_category.php");
	$t->set_var("admin_select_href",   "admin_select.php");
	$t->set_var("admin_upload_href",   "admin_upload.php");
	$t->set_var("admin_ads_href",      "admin_ads.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CATEGORY_MSG, CONFIRM_DELETE_MSG));

	$html_editor = get_setting_value($settings, "html_editor", 1);
	$t->set_var("html_editor", $html_editor);
	
	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path  = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$category_id = get_param("category_id");
	$parent_category_id = get_param("parent_category_id");
	if (!$parent_category_id) { $parent_category_id = 0; }
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	$return_page = "admin_ads.php?category_id=" . $parent_category_id;
		
	$r = new VA_Record($table_prefix . "ads_categories");
	$r->return_page = $return_page;
	if (get_param("apply")) {
		$r->redirect = false;
	}
	$r->add_where("category_id", INTEGER);
	$r->add_textbox("category_order", INTEGER, CATEGORY_ORDER_MSG);
	$r->change_property("category_order", REQUIRED, true);
	$r->add_textbox("total_views", INTEGER);
	$r->change_property("total_views", USE_IN_INSERT, false);
	$r->change_property("total_views", USE_IN_UPDATE, false);
	$r->add_textbox("category_name", TEXT, CATEGORY_NAME_MSG);
	$r->change_property("category_name", REQUIRED, true);
	$r->change_property("category_name", MAX_LENGTH, 255);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("publish_price", NUMBER, ADS_PUBLISH_PRICE_MSG);

	$r->add_textbox("image_small", TEXT);
	$r->add_textbox("image_large", TEXT);
	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);

	$r->add_textbox("parent_category_id", INTEGER);
	$r->change_property("parent_category_id", USE_IN_UPDATE, false);
	$r->add_textbox("parent_category_id", INTEGER);
	
	$r->add_textbox("category_path", TEXT);
	$r->change_property("category_path", USE_IN_UPDATE, false);

	$r->add_textbox("language_code", TEXT, LANGUAGE_CODE_MSG);
	$r->change_property("language_code", USE_SQL_NULL, false);
	$r->add_textbox("total_ads", INTEGER);
	$r->change_property("total_ads", USE_IN_INSERT, true);
	$r->add_textbox("total_subcategories", INTEGER);
	$r->change_property("total_subcategories", USE_IN_INSERT, true);

	$r->add_checkbox("sites_all", INTEGER);	
	$r->add_textbox("access_level", INTEGER);
	$r->add_textbox("guest_access_level", INTEGER);
	
	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", "Ads");
	if ($category_id) {
		$tree->show($category_id);
		$action_title = EDIT_CATEGORY_MSG;
	} else {
		$tree->show($parent_category_id);
		$action_title = ADD_CATEGORY_MSG;
	}
	$t->set_var("action_title", $action_title);
	
	if ($category_id) {
		$t->set_var("live_href", $root_folder_path . "ads.php?category_id=" . $category_id);
		$t->parse("view_live");
	}
	
	$access_table = new VA_Access_Table($settings["admin_templates_dir"], "access_table.html");
	$access_table->set_access_levels(
		array(
			1 => array(VIEW_MSG, VIEW_CATEGORY_IN_THE_LIST_MSG), 
			2 => array(ACCESS_LIST_MSG, ACCESS_CATEGORY_DETAILS_AND_ITEMS_LIST_MSG),
			4 => array(ACCESS_DETAILS_MSG, ACCESS_CATEGORY_ITEMS_DETAILS_MSG),
			8 => array(POST_MSG, ALLOW_TO_POST_NEW_ITEMS_TO_CATEGORY_MSG)
		)
	);
	$access_table->set_tables("ads_categories", "ads_categories_types",  "ads_categories_subscriptions", "category_id", "category_path", $category_id);
	
	$sites_table = new VA_Sites_Table($settings["admin_templates_dir"], "sites_table.html");
	$sites_table->set_tables("ads_categories", "ads_categories_sites", "category_id", "category_path", $category_id);

	$r->set_event(BEFORE_INSERT,  "before_insert_category");
	$r->set_event(BEFORE_UPDATE,  "before_update_category");
	$r->set_event(AFTER_VALIDATE, "after_validate_category");
	$r->set_event(AFTER_INSERT,   "after_insert_category");
	$r->set_event(AFTER_UPDATE,   "after_update_category");
	$r->set_event(AFTER_DELETE,   "delete_category");
	$r->set_event(AFTER_DEFAULT,  "default_category");
	$r->process();
	
	$sites_table->parse("sites_table", $r->get_value("sites_all"));
	$has_any_subscriptions = $access_table->parse("subscriptions_table", $r->get_value("access_level"), $r->get_value("guest_access_level"));
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	if (strlen($category_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);
	} else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");
	}
		
	$tabs = array(
		"general"       => array( "title" => EDIT_CATEGORY_MSG),
		"sites"         => array( "title" => ADMIN_SITES_MSG, "show" => $sitelist),
		"subscriptions" => array( "title" => ACCESS_LEVELS_MSG, "show" => $has_any_subscriptions)
	);
	parse_admin_tabs($tabs, $tab);
	
	$t->pparse("main");
	
	function before_insert_category() {
		global $r, $table_prefix;
		$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "");
		set_friendly_url();
		$r->set_value("category_path", $tree->get_path($r->get_value("parent_category_id")));
		$r->set_value("total_ads", 0);
		$r->set_value("total_subcategories", 0);
		$category_id = get_db_value("SELECT MAX(category_id) FROM " . $table_prefix . "ads_categories") + 1;
		$r->set_value("category_id", $category_id);
		return true;
	}
	
	function before_update_category() {
		set_friendly_url();		
		return true;
	}
	
	function after_validate_category() {
		global $r, $access_table;
		$r->set_value("access_level", $access_table->all_selected_access_level);
		$r->set_value("guest_access_level", $access_table->guest_selected_access_level);
	}
	
	function after_update_category() {
		global $r, $access_table, $sites_table;
		$category_id = $r->get_value("category_id"); 
		$access_table->save_values($category_id, get_param("save_nested_subscriptions"));
		$sites_table->save_values($category_id, $r->get_value("sites_all"), get_param("save_nested_sites"));		
	}
	
	function after_insert_category() {
		global $r, $table_prefix, $db;
		$sql  = " UPDATE " . $table_prefix . "ads_categories ";
		$sql .= " SET total_subcategories = total_subcategories + 1 ";
		$sql .= " WHERE category_id = " . $db->tosql($r->get_value("parent_category_id"), INTEGER);
		after_update_category();
	}
	
	function delete_category() {
		global $r, $table_prefix, $db;
		$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "");

		$category_id   = $r->get_value("category_id");
		$category_path = $tree->get_path($category_id);
		if ($category_path != "0,") {
			$sql  = " SELECT category_id FROM " . $table_prefix . "ads_categories ";
			$sql .= " WHERE category_id = " . $db->tosql($category_id, INTEGER);
			$sql .= " OR category_path LIKE '" . $db->tosql($category_path, TEXT, false) . "%'";
			$db->query($sql);
			while ($db->next_record()) {
				$categories[] = $db->f("category_id");
			}
			if (is_array($categories)) {
				$sql  = " DELETE FROM " . $table_prefix . "ads_assigned ";
				$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
				$db->query($sql);
				
				$sql  = " DELETE FROM " . $table_prefix . "ads_categories_sites ";
				$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
				$db->query($sql);
				
				$sql  = " DELETE FROM " . $table_prefix . "ads_categories_types ";
				$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
				$db->query($sql);
				
				$sql  = " DELETE FROM " . $table_prefix . "ads_categories_subscriptions ";
				$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
				$db->query($sql);
				
				$sql  = " DELETE FROM " . $table_prefix . "ads_categories ";
				$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
				$db->query($sql);
			}
			
			if ($parent_category_id) {
				$db->query("UPDATE " . $table_prefix . "ads_categories SET total_subcategories = total_subcategories - 1 WHERE category_id = " . $db->tosql($parent_category_id, INTEGER));
			}
			
			$db->query("DELETE FROM " . $table_prefix . "ads_categories_assigned WHERE category_id=" . $db->tosql($category_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "ads_categories_sites WHERE category_id=" . $db->tosql($category_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "ads_categories_types WHERE category_id=" . $db->tosql($category_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "ads_categories_subscriptions WHERE category_id=" . $db->tosql($category_id, INTEGER));
		}
	}
	
	function default_category() {
		global $r, $parent_category_id, $table_prefix, $db;
		$category_order = get_db_value("SELECT MAX(category_order) FROM " . $table_prefix . "ads_categories WHERE parent_category_id=" . $db->tosql($parent_category_id, INTEGER));
		$category_order++;
		$r->set_value("category_order", $category_order);
		$r->set_value("parent_category_id", $parent_category_id);
		$r->set_value("access_level", 255);
		$r->set_value("guest_access_level", 255);
		$r->set_value("sites_all", 1);		
	}
?>