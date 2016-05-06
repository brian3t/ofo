<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_component_single.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");

	check_admin_security("products_categories");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_component_single.html");

	$t->set_var("admin_component_single_href",   "admin_component_single.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_product_href",    "admin_product.php");
	$t->set_var("admin_properties_href", "admin_properties.php");
	$t->set_var("admin_product_select_href", "admin_product_select.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SUBCOMPONENT_MSG, CONFIRM_DELETE_MSG));
	
	$item_id = get_param("item_id");
	if (!strlen($item_id)) { $item_id= "0"; }
	$item_type_id = get_param("item_type_id");
	if (!strlen($item_type_id)) { $item_type_id = "0"; }
	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }
	$property_id = get_param("property_id");

	if ($item_type_id > 0) {
		$sql  = " SELECT item_type_name FROM " . $table_prefix . "item_types ";
		$sql .= " WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$t->set_var("item_type_name", get_translation($db->f("item_type_name")));
		} else {
			die(str_replace("{item_type_id}", $item_id, PROD_TYPE_ID_NO_LONGER_EXISTS_MSG));
		}
	} else {
		$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$t->set_var("item_name", get_translation($db->f("item_name")));
		} else {
			die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));
		}

		$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
		$tree->show($category_id);
	}

	$usage_types = 
		array(			
			array("1", AUTO_ADD_TO_ALL_PRODS_MSG),
			array("2", SELECT_OPTION_AND_VALUES_MSG),
			array("3", SELECT_OPTION_ALL_VALUES_MSG),
		);

	$quantity_actions = array(
		array(1, SUBCOMPONENT_QTY_MULTIPLY_MSG),
		array(2, SUBCOMPONENT_ADDED_ONCE_MSG),
	);

	// set up html form parameters
	$r = new VA_Record($table_prefix . "items_properties");
	$r->add_where("property_id", INTEGER);
	$r->change_property("property_id", USE_IN_INSERT, true);
	$r->add_textbox("property_type_id", INTEGER);
	$r->change_property("property_type_id", USE_IN_UPDATE, false);
	$r->add_hidden("item_id", INTEGER);
	$r->change_property("item_id", USE_IN_INSERT, true);
	$r->add_hidden("item_type_id", INTEGER);
	$r->change_property("item_type_id", USE_IN_INSERT, true);
	$r->add_textbox("property_order", INTEGER);
	$r->add_textbox("sub_item_id", INTEGER, SUBCOMP_ID_MSG);
	$r->parameters["sub_item_id"][REQUIRED] = true;
	$r->add_textbox("property_name", TEXT, SUBCOMP_NAME_MSG);
	$r->parameters["property_name"][REQUIRED] = true;
	$r->add_select("usage_type", INTEGER, $usage_types, ASSIGN_COMPONENT_MSG);
	$r->parameters["usage_type"][REQUIRED] = true;
	$r->add_textbox("quantity", INTEGER, QUANTITY_MSG);
	$r->add_radio("quantity_action", INTEGER, $quantity_actions, CART_QUANTITY_MSG);
	$r->add_textbox("additional_price", FLOAT, SUBCOMP_PRICE_MSG);
	$r->add_textbox("trade_additional_price", NUMBER, PROD_TRADE_PRICE_MSG);
	$r->add_hidden("control_type", TEXT);
	$r->change_property("control_type", USE_SQL_NULL, false);
	$r->add_textbox("use_on_list", INTEGER);
	$r->add_textbox("use_on_details", INTEGER);
	$r->add_textbox("use_on_second", INTEGER);
	$r->add_textbox("use_on_checkout", INTEGER);
	$r->add_hidden("category_id", INTEGER);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("page", TEXT);
	$r->return_page = "admin_properties.php";

	$r->get_form_values();


	if (!$item_type_id) {
		$r->change_property("usage_type", SHOW, false);
		$r->set_value("usage_type", 1);
	}
	$operation = get_param("operation");
	$return_page = $r->get_return_url();

	if (strlen($operation)) {
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $property_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "items_properties WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "items_properties_values WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();
		if ($is_valid) {
			$sub_item_id = $r->get_value("sub_item_id");
			$sql = "SELECT item_id FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($sub_item_id, INTEGER);
			$db->query($sql);
			if (!$db->next_record()) {
				$r->errors = str_replace("{item_id}", $sub_item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG);
				$is_valid = false;
			}
		}

		if ($is_valid)
		{
			$r->set_value("property_type_id", 2);
			$r->set_value("property_order", 1);
			$r->set_value("use_on_list", 1);
			$r->set_value("use_on_details", 1);
			$r->set_value("use_on_second", 1);
			$r->set_value("use_on_checkout", 1);
			if (strlen($property_id)) {
				$r->update_record();
			} else {
				$db->query("SELECT MAX(property_id) FROM " . $table_prefix . "items_properties");
				$db->next_record();
				$property_id = $db->f(0) + 1;
				$r->set_value("property_id", $property_id);
				$r->set_value("item_id", $item_id);
				$r->set_value("item_type_id", $item_type_id);
				$r->insert_record();
			}
			header("Location: " . $return_page);
			exit;
		}
	} elseif (strlen($property_id)) {
		$r->get_db_values();
	} else { // set default values
		$r->set_value("quantity", 1);
		$r->set_value("quantity_action", 1);
		$r->set_value("usage_type", 1);
	}

	$r->set_parameters();
	$sub_item_id = $r->get_value("sub_item_id");
	if ($sub_item_id) {
		$sql  = " SELECT price, is_sales, sales_price FROM " . $table_prefix . "items ";
		$sql .= " WHERE item_id=" . $db->tosql($sub_item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$price = $db->f("price");
			$is_sales = $db->f("is_sales");
			$sales_price = $db->f("sales_price");
			if ($is_sales && $sales_price > 0) {
				$price = $sales_price;
			}
			$t->set_var("basic_price", "&nbsp;&ndash;&nbsp;" . number_format($price, 2, 	".", ""));
		}
	}

	if ($item_type_id > 0) {
		$t->sparse("type_path");
	} else {
		$t->sparse("product_path");
	}

	if (strlen($property_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_SUBCOMPONENT_MSG);
		$t->set_var("delete", "");	
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>