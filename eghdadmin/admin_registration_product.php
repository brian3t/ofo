<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_product.php                           ***
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

	check_admin_security("edit_reg_products");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_registration_product.html");
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registration_products_href", "admin_registration_products.php");	
	$t->set_var("admin_registration_product_href", "admin_registration_product.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", PRODUCT_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$category_id = get_param("category_id");
	$item_id = get_param("item_id");

	$r = new VA_Record($table_prefix . "registration_items");

	$r->add_where("item_id", INTEGER);
	$r->add_hidden("category_id", INTEGER);
	$r->change_property("category_id", USE_IN_INSERT, false);
	$r->change_property("category_id", USE_IN_UPDATE, false);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->add_textbox("item_order", INTEGER, PROD_ORDER_MSG);
	$r->change_property("item_order", REQUIRED, true);
	$r->add_textbox("item_code", TEXT, PROD_CODE_MSG);
	$r->add_textbox("item_name", TEXT, PROD_NAME_MSG);
	$r->change_property("item_name", REQUIRED, true);
	
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->get_form_values();
	
	$return_page = "admin_registration_products.php";
	$r->return_page = $return_page;
	
	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "registration_categories", "tree");
	$tree->show($category_id);
	
	if (strlen($item_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("save", false);		
		$t->parse("delete", false);		
	} else {
		$t->set_var("save_button", ADD_BUTTON);
		$t->parse("save", false);
		$t->set_var("delete", "");
	}
	
	$r->set_event(AFTER_VALIDATE, "set_product_values");
	$r->set_event(AFTER_DELETE, "delete_product_values");
	$r->set_event(BEFORE_DEFAULT, "set_product_default");
	$r->set_event(AFTER_INSERT, "save_product");
	
	$r->process();

	$t->pparse("main");
	
	function set_product_values()
	{
		global $r, $table_prefix, $db;
				
		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());
	
	}
	
	function delete_product_values()
	{
		global $r, $table_prefix, $db;
				
		$sql  = " DELETE FROM " . $table_prefix . "registration_items_assigned ";
		$sql .= " WHERE item_id=" . $db->tosql($r->get_value("item_id"), INTEGER);
		
		$sql  = " DELETE FROM " . $table_prefix . "registration_list ";
		$sql .= " WHERE item_id=" . $db->tosql($r->get_value("item_id"), INTEGER);
		$db->query($sql);
	}
	
	function set_product_default()
	{
		global $r, $table_prefix, $db, $category_id;
		$sql  = " SELECT MAX(item_order) FROM " . $table_prefix . "registration_items i, ";
		$sql .= $table_prefix . "registration_items_assigned ic ";
		$sql .= " WHERE i.item_id=ic.item_id ";
		$sql .= " AND ic.category_id=" . $db->tosql($category_id, INTEGER);	
		$item_order = get_db_value($sql);
		$item_order++;
		$r->set_value("show_for_user", 1);
		$r->set_value("item_order", $item_order);
	}
	function save_product()
	{
		global $r, $db, $db_type, $table_prefix;
		if ($db_type == "mysql") {
			$item_id = get_db_value(" SELECT LAST_INSERT_ID() ");
		} else if ($db_type == "access") {
			$item_id = get_db_value(" SELECT @@IDENTITY ");
		} else if ($db_type == "db2") {
			$item_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "registration_items FROM " . $table_prefix . "registration_items");
		}

		$sql  = " INSERT INTO " . $table_prefix . "registration_items_assigned ";
		$sql .= " (item_id, category_id) VALUES (";
		$sql .= $db->tosql($item_id, INTEGER) . ", ";
		$sql .= $db->tosql($r->get_value("category_id"), INTEGER, true, false) . ")";
		$db->query($sql);
	}
	
?>