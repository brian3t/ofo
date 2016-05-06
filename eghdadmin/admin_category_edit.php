<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_category_edit.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once($root_folder_path . "includes/sites_table.php");
	include_once($root_folder_path . "includes/access_table.php");

	check_admin_security("products_categories");
	$permissions = get_permissions();
	$add_categories = get_setting_value($permissions, "add_categories", 0);
	$update_categories = get_setting_value($permissions, "update_categories", 0);
	$remove_categories = get_setting_value($permissions, "remove_categories", 0);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_category_edit.html");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CATEGORY_MSG, CONFIRM_DELETE_MSG));

	$html_editor = get_setting_value($settings, "html_editor", 1);
	$t->set_var("html_editor", $html_editor);		

	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");

	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_category_edit_href", "admin_category_edit.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path  = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$category_id = get_param("category_id");
	
	$r = new VA_Record($table_prefix . "categories");
	if (get_param("apply")) {
		$r->redirect = false;
	}
	$r->add_where("category_id", INTEGER);
	$r->add_checkbox("is_showing", INTEGER);
	$r->add_textbox("category_order", INTEGER, CATEGORY_ORDER_MSG);
	$r->change_property("category_order", REQUIRED, true);
	$r->add_textbox("total_views", INTEGER);
	$r->change_property("total_views", USE_IN_INSERT, false);
	$r->change_property("total_views", USE_IN_UPDATE, false);
	$r->add_textbox("category_name", TEXT, CATEGORY_NAME_MSG);
	$r->change_property("category_name", REQUIRED, true);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);
	$r->add_checkbox("show_sub_products", INTEGER);
	$r->add_checkbox("allowed_post_subcategories", INTEGER);
	$r->add_textbox("image", TEXT);
	$r->add_textbox("image_alt", TEXT);
	$r->add_textbox("image_large", TEXT);
	$r->add_textbox("image_large_alt", TEXT);

	//-- parent items
	$sql  = " SELECT * FROM " . $table_prefix . "categories ";
	$sql .= " ORDER BY category_path, category_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$list_id        = $db->f("category_id");
		$list_parent_id = $db->f("parent_category_id");
		$list_title     = get_translation($db->f("category_name"));
		
		$categories[$list_id]["category_name"] = $list_title;
		$categories[$list_id]["category_path"] = $db->f("category_path");
		$categories[$list_parent_id]["subs"][] = $list_id;
		$parent_categories[$list_id] = $list_parent_id;
	}

	$items = array();
	$items[] = array(0, "[Top]");
	build_category_list(0);

	$r->add_select("parent_category_id", INTEGER, $items, PARENT_CATEGORY_MSG);
	$r->change_property("parent_category_id", REQUIRED, true);

	$r->add_textbox("category_path", TEXT);

	// templates settings
	$r->add_textbox("list_template", TEXT);
	$r->add_textbox("details_template", TEXT);

	// meta data
	$r->add_textbox("meta_title", TEXT);
	$r->add_textbox("meta_keywords", TEXT);
	$r->add_textbox("meta_description", TEXT);

	// editing information
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);

	$google_base_product_types = get_db_values("SELECT type_id, type_name FROM " . $table_prefix . "google_base_types ORDER BY type_name", array(array(-1, NOT_EXPORTED_MSG), array(0, USE_GLOBAL_MSG)));
	$r->add_select("google_base_type_id", INTEGER, $google_base_product_types);
	
	$r->add_checkbox("sites_all", INTEGER);	
	$r->add_textbox("access_level", INTEGER);
	$r->add_textbox("guest_access_level", INTEGER);

	$r->get_form_values();
	
	if(!strlen($r->get_value("parent_category_id"))) $r->set_value("parent_category_id", "0");
	$parent_category_id = $r->get_value("parent_category_id");
	
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$return_page = "admin_items_list.php?category_id=" . $parent_category_id;
	
	$r->return_page = $return_page;

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($parent_category_id);

	$operation = get_param("operation");
	$return_page = "admin_items_list.php?category_id=" . $parent_category_id;

	$access_table = new VA_Access_Table($settings["admin_templates_dir"], "access_table.html");
	$access_table->set_access_levels(
		array(
			1 => array(VIEW_MSG, VIEW_CATEGORY_IN_THE_LIST_MSG), 
			2 => array(ACCESS_LIST_MSG, ACCESS_CATEGORY_DETAILS_AND_ITEMS_LIST_MSG),
			4 => array(ACCESS_DETAILS_MSG, ACCESS_CATEGORY_ITEMS_DETAILS_MSG),
			8 => array(POST_MSG, ALLOW_TO_POST_NEW_ITEMS_TO_CATEGORY_MSG)
		)
	);
	$access_table->set_tables("categories", "categories_user_types",  "categories_subscriptions", "category_id", "category_path", $category_id);
	
	$sites_table = new VA_Sites_Table($settings["admin_templates_dir"], "sites_table.html");
	$sites_table->set_tables("categories", "categories_sites", "category_id", "category_path", $category_id);
		
	$r->set_event(BEFORE_INSERT,  "before_insert_category");
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
		if ($update_categories) {
			$t->set_var("save_button", UPDATE_BUTTON);
			$t->parse("save", false);
		}
		if ($remove_categories) {
			$t->parse("delete", false);
		}
	} else {
		if ($add_categories) {
			$t->set_var("save_button", ADD_BUTTON);
			$t->parse("save", false);
		}
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
		$category_id = get_db_value("SELECT MAX(category_id) FROM " . $table_prefix . "categories") + 1;
		$r->set_value("category_id", $category_id);
		return true;
	}
	
	function after_validate_category() {
		global $r, $access_table, $table_prefix;
		
		$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "");
		set_friendly_url();
		$r->set_value("category_path", $tree->get_path($r->get_value("parent_category_id")));
		
		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());
		
		$r->set_value("access_level", $access_table->all_selected_access_level);
		$r->set_value("guest_access_level", $access_table->guest_selected_access_level);
	}
	
	function after_update_category() {
		global $r, $access_table, $sites_table, $table_prefix, $db, $settings;
		$category_id = $r->get_value("category_id");
		update_category_tree($category_id, $r->get_value("category_path"));
		$access_table->save_values($category_id, get_param("save_nested_subscriptions"));
		$sites_table->save_values($category_id, $r->get_value("sites_all"), get_param("save_nested_sites"));
		
		//nested products
		$save_products_sites        = get_param('save_products_sites');
		$save_nested_products_sites = get_param('save_nested_products_sites');
		$save_products_subscriptions = get_param('save_products_subscriptions');
		$save_nested_products_subscriptions = get_param('save_nested_products_subscriptions');
		
		$products_ids = array();
		if ($save_products_sites || $save_products_subscriptions) {
			$sql  = " SELECT item_id ";
			$sql .= " FROM " . $table_prefix . "items_categories ";
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$sql .= " GROUP BY item_id ";
			$db->query($sql);
			while ($db->next_record()) {
				$products_ids[] = $db->f('item_id');
			}
		}	
		
		$subproducts_ids = array();
		$nested_categories = array();
		if ($save_nested_products_sites || $save_nested_products_subscriptions) {
			$sql  = " SELECT category_id";
			$sql .= " FROM " . $table_prefix . 	"categories";
			$sql .= " WHERE category_path LIKE '%," . $db->tosql($category_id, INTEGER, false, false) . ",%'";
			$db->query($sql);
			while ($db->next_record()) {
				$nested_categories[] = $db->f("category_id");
			}
			
			if ($nested_categories) {
				$sql  = " SELECT item_id ";
				$sql .= " FROM " . $table_prefix . "items_categories ";
				$sql .= " WHERE category_id IN (" . $db->tosql($nested_categories, INTEGERS_LIST). ")" ;
				$sql .= " GROUP BY item_id ";
				$db->query($sql);
				while ($db->next_record()) {
					$subproducts_ids[] = $db->f('item_id');
				}
			}
		}
		
		if (($save_products_sites && $products_ids) || ($save_nested_products_sites && $subproducts_ids)) {
			$products_sites_table = new VA_Sites_Table($settings["admin_templates_dir"], "sites_table.html");
			$products_sites_table->set_tables("items", "items_sites", "item_id", false, 0);
			if($save_products_sites && $products_ids) {
				$products_sites_table->save_array_values($products_ids, $r->get_value("sites_all"));
			}
			if($save_nested_products_sites && $subproducts_ids) {
				$products_sites_table->save_array_values($subproducts_ids, $r->get_value("sites_all"));
			}
		}
		
		if (($save_products_subscriptions && $products_ids) || ($save_nested_products_subscriptions && $subproducts_ids)) {
			$products_access_table = new VA_Access_Table($settings["admin_templates_dir"], "access_table.html");
			$products_access_table->set_access_levels(
				array(
					VIEW_CATEGORIES_ITEMS_PERM => array(VIEW_MSG, VIEW_ITEM_IN_THE_LIST_MSG), 
					VIEW_ITEMS_PERM => array(ACCESS_DETAILS_MSG, ACCESS_ITEMS_DETAILS_MSG)
				)
			);
			$products_access_table->set_tables("items", "items_user_types",  "items_subscriptions", "item_id", false, 0);
	
			if($save_products_subscriptions && $products_ids) {
				$products_access_table->save_array_values($products_ids, $r->get_value("access_level"), $r->get_value("guest_access_level"));
			}
			if($save_nested_products_subscriptions && $subproducts_ids) {
				$products_access_table->save_array_values($subproducts_ids, $r->get_value("access_level"), $r->get_value("guest_access_level"));
			}
		}	
	}
	
	function after_insert_category() {
		global $r, $access_table, $sites_table;
		$category_id = $r->get_value("category_id"); 
		$access_table->save_values($category_id, get_param("save_nested_subscriptions"));
		$sites_table->save_values($category_id, $r->get_value("sites_all"), get_param("save_nested_sites"));	
	}
	
	function delete_category() {
		global $r, $table_prefix, $db;
		
		$category_id   = $r->get_value("category_id");
		if ($category_id) {
			delete_categories($category_id);
		}
	}
	
	function default_category() {
		global $r, $table_prefix, $db;
		
		$parent_category_id = $r->get_value("parent_category_id");		
		$category_order = get_db_value("SELECT MAX(category_order) FROM " . $table_prefix . "categories WHERE parent_category_id=" . $db->tosql($parent_category_id, INTEGER));
		$category_order++;
		$r->set_value("is_showing", 1);
		$r->set_value("category_order", $category_order);
		$r->set_value("parent_category_id", $parent_category_id);
		$r->set_value("access_level", 15);
		$r->set_value("guest_access_level", 7);
		$r->set_value("sites_all", 1);	
	}
	
	function spaces_level($level)
	{
		$spaces = "";
		for ($i =1; $i <= $level; $i++) {
			$spaces .= "--";
		}
		return $spaces . " ";
	}
	
	
	function update_category_tree($parent_category_id, $category_path)
	{
		global $db, $table_prefix, $categories, $parent_categories;
		
		if (isset($categories[$parent_category_id]["subs"])) {
			$category_path .= $parent_category_id . ",";	
			$subs = $categories[$parent_category_id]["subs"];
			for ($s = 0; $s < sizeof($subs); $s++) {
				$sub_id = $subs[$s];
				$sql  = " UPDATE " . $table_prefix . "categories SET ";
				$sql .= " category_path=" . $db->tosql($category_path, TEXT);
				$sql .= " WHERE category_id=" . $db->tosql($sub_id, INTEGER);
				$db->query($sql);

				if (isset($categories[$sub_id]["subs"])) {
					update_category_tree($sub_id, $category_path);
				}
			}
		}
	}
	
	function build_category_list($parent_id) 
	{
		global $t, $categories, $items;
		$subs = $categories[$parent_id]["subs"];
		for ($m = 0; $m < sizeof($subs); $m++) {
			$category_id = $subs[$m];
			$category_path = $categories[$category_id]["category_path"];
			$category_name = $categories[$category_id]["category_name"];
			$category_level = preg_replace("/\d/", "", $category_path);
			$spaces = spaces_level(strlen($category_level));
	
			$items[] = array($category_id, $spaces.$category_name);
	
			if (isset($categories[$category_id]["subs"])) {
				build_category_list($category_id);
			}
		}
	}
?>