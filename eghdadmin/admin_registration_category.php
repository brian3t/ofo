<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_category.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("edit_reg_categories");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_registration_category.html");
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registration_products_href", "admin_registration_products.php");
	$t->set_var("admin_registration_category_href", "admin_registration_category.php");	
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CATEGORY_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$category_id = get_param("category_id");

	$r = new VA_Record($table_prefix . "registration_categories");

	$r->add_where("category_id", INTEGER);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->add_textbox("category_order", INTEGER, CATEGORY_ORDER_MSG);
	$r->change_property("category_order", REQUIRED, true);
	$r->add_textbox("category_name", TEXT, CATEGORY_NAME_MSG);
	$r->change_property("category_name", REQUIRED, true);
	
	//-- parent items
	$sql  = " SELECT * FROM " . $table_prefix . "registration_categories ";
	$sql .= " ORDER BY category_path, category_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$list_id = $db->f("category_id");
		$list_parent_id = $db->f("parent_category_id");
		$list_title = get_translation($db->f("category_name"));

		$category_values = array(
			"category_name" => $list_title, "category_path" => $db->f("category_path")
		);
		$categories[$list_id] = $category_values;
		$categories[$list_parent_id]["subs"][] = $list_id;
		$parent_categories[$list_id] = $list_parent_id;
	}

	$items = array();
	$items[] = array(0, "[Top]");
	build_category_list(0);

	$r->add_select("parent_category_id", INTEGER, $items, PARENT_CATEGORY_MSG);
	$r->change_property("parent_category_id", REQUIRED, true);
	$r->add_textbox("category_path", TEXT);
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->get_form_values();

	if(!strlen($r->get_value("parent_category_id"))) {
		$r->set_value("parent_category_id", "0");
	}
	$parent_category_id = $r->get_value("parent_category_id");

	$return_page = "admin_registration_products.php?category_id=" . $parent_category_id;
	$r->return_page = $return_page;
	
	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "registration_categories", "tree");
	$tree->show($parent_category_id);
	
	if (strlen($category_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("save", false);		
		$t->parse("delete", false);		
	} else {
		$t->set_var("save_button", ADD_BUTTON);
		$t->parse("save", false);
		$t->set_var("delete", "");
	}
	
	$r->set_event(AFTER_VALIDATE, "set_category_values");
	$r->set_event(AFTER_DELETE, "delete_category_values");
	$r->set_event(BEFORE_DEFAULT, "set_category_default");
	$r->process();

	$t->pparse("main");
	
	function build_category_list($parent_id) 
	{
		global $t, $categories, $items;
		$subs = $categories[$parent_id]["subs"];
		for ($m = 0; $m < sizeof($subs); $m++) {
			$category_id = $subs[$m];
			$category_path = $categories[$category_id]["category_path"];
			$category_name = $categories[$category_id]["category_name"];
			$category_level = preg_replace("/\d/", "", $category_path);
			$spaces = "";
			if (strlen($category_level) >= 2) {
				$spaces = str_repeat("--", strlen($category_level) - 1);
			}
			$items[] = array($category_id, $spaces . $category_name);
	
			if (isset($categories[$category_id]["subs"])) {
				build_category_list($category_id);
			}
		}
	}
	
	function set_category_values()
	{
		global $r, $table_prefix, $db;
		
		$category_path = "";
		$category_id   = $r->get_value("category_id");
		$parent_category_id = $r->get_value("parent_category_id");
		if ($parent_category_id > 0) {
			$sql  = " SELECT category_path ";
			$sql .= " FROM " . $table_prefix . "registration_categories ";
			$sql .= " WHERE category_id=" . $db->tosql($r->get_value("parent_category_id"), INTEGER);
			$category_path = get_db_value($sql);
			if ($category_path) {
				$category_path .= $db->tosql($r->get_value("parent_category_id"), INTEGER) . ",";
			} else {
				$category_path = "0,";
			}
		} else {
			$category_path = "0,";
		}		
		
		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());
		$r->set_value("category_path", $category_path);
		$r->set_value("parent_category_id", $parent_category_id);
	}
	
	function delete_category_values()
	{
		global $r, $table_prefix, $db;
				
		$sql  = " DELETE FROM " . $table_prefix . "registration_items_assigned ";
		$sql .= " WHERE category_id=" . $db->tosql($r->get_value("category_id"), INTEGER);
		$db->query($sql);
	}
		
	function set_category_default()
	{
		global $r, $table_prefix, $db;
			
		$sql  = " SELECT MAX(category_order) ";
		$sql .= " FROM " . $table_prefix . "registration_categories ";
		$sql .= " WHERE parent_category_id=" . $db->tosql($r->get_value("parent_category_id"), INTEGER);
		$category_order = get_db_value($sql);
		$category_order++;
		$r->set_value("show_for_user", 1);
		$r->set_value("category_order", $category_order);
	}

?>