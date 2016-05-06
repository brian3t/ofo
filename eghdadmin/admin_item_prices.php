<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_item_prices.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("product_prices");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_item_prices.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_item_prices_href", "admin_item_prices.php");
	$t->set_var("admin_product_href", "admin_product.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");
	$item_id = get_param("item_id");
	$category_id = get_param("category_id");
	$feature_values = array();

	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_items_list.php?category_id=" . urlencode($category_id);
	$errors = "";

	$sql = " SELECT currency_code FROM " . $table_prefix . "currencies WHERE is_default=1 ";
	$default_currency_code = get_db_value($sql);
	$default_currency = get_currency($default_currency_code);

	$sql = "SELECT item_type_id, item_name, price, sales_price, is_sales FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$item_type_id = $db->f("item_type_id");
		$item_name = get_translation($db->f("item_name"));
		$initial_price = $db->f("price");
		$sales_price = $db->f("sales_price");
		$is_sales = $db->f("is_sales");
		if ($is_sales && $sales_price > 0) {
			$initial_price = $sales_price;
		}

		$t->set_var("initial_price", currency_format($initial_price, $default_currency));
		$t->set_var("item_name", htmlspecialchars($item_name));
	} else {
		header("Location: " . $return_page);
		exit;
	}

	// set up html form parameters
	$r = new VA_Record($table_prefix . "items_prices", "prices");
	$r->add_where("price_id", INTEGER);
	$r->add_hidden("item_id", INTEGER);
	$r->change_property("item_id", USE_IN_INSERT, true);

	$discount_actions = array(
		array("", ""),
		array(0, DONT_USE_PRICE_DISCOUNT_MSG),
		array(1, DONT_APPLY_DISCOUNT_PRICE_MSG),
		array(2, APPLY_DISCOUNT_PRICE_MSG)
	);


	$r->add_checkbox("is_active", INTEGER, ACTIVE_MSG);
	$r->add_textbox("min_quantity", INTEGER, MIN_QTY_MSG);
	$r->change_property("min_quantity", REQUIRED, true);
	$r->change_property("min_quantity", MIN_VALUE, 1);
	$r->change_property("min_quantity", BEFORE_VALIDATE, "check_item_quantity");
	$r->add_textbox("max_quantity", INTEGER, MAX_QTY_MSG);
	$r->change_property("max_quantity", REQUIRED, true);
	$r->change_property("max_quantity", MIN_VALUE, 1);
	$r->change_property("max_quantity", BEFORE_SHOW, "check_max_quantity");
	$r->add_textbox("price", NUMBER, INDIVIDUAL_PRICE_MSG);
	$r->parameters["price"][REQUIRED] = true;
	$r->add_textbox("properties_discount", NUMBER, OPTIONS_DISCOUNT_MSG);

	$user_types = get_db_values("SELECT type_id, type_name FROM " . $table_prefix . "user_types ", array(array("", ""), array("0", FOR_ALL_USERS_MSG)));
	$r->add_select("user_type_id", INTEGER, $user_types, USER_TYPE_MSG);
	
	if ($sitelist) {
		$error_colspan = 8;
		$total_colspan = 10;
		$sites = get_db_values("SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ", array(array("", ""), array("0", "All Sites")));
		$r->add_select("site_id", INTEGER, $sites, ADMIN_SITE_MSG);
		$r->change_property("site_id", USE_SQL_NULL, false);
	} else {
		$error_colspan = 7;
		$total_colspan = 9;
		$r->add_textbox("site_id", INTEGER);
		$r->change_property("site_id", SHOW, false);
		$r->change_property("site_id", USE_SQL_NULL, false);
	}
	$t->set_var("error_colspan", $error_colspan);
	$t->set_var("total_colspan", $total_colspan);

	$r->add_radio("discount_action", NUMBER, $discount_actions, DISCOUNT_SETTINGS_MSG);
	$r->parameters["discount_action"][REQUIRED] = true;

	$more_prices = get_param("more_prices");
	$number_prices = get_param("number_prices");

	$eg = new VA_EditGrid($r, "prices");
	$eg->get_form_values($number_prices);
	$eg->set_event(BEFORE_INSERT, "check_site_id");
	$eg->set_event(BEFORE_UPDATE, "check_site_id");

	if(strlen($operation) && !$more_prices)
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $item_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "items_prices WHERE item_id=" . $db->tosql($item_id, INTEGER));
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate();

		if($is_valid)
		{
			$eg->set_values("item_id", $item_id);
			$eg->update_all($number_prices);
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($item_id) && !$more_prices)
	{
		$eg->set_value("item_id", $item_id);
		$eg->change_property("price_id", USE_IN_SELECT, true);
		$eg->change_property("price_id", USE_IN_WHERE, false);
		$eg->change_property("item_id", USE_IN_WHERE, true);
		$eg->change_property("item_id", USE_IN_SELECT, true);
		$number_prices= $eg->get_db_values();
		if($number_prices == 0)
			$number_prices = 5;

	}
	else if($more_prices)
	{
		$number_prices += 5;
	}
	else
	{
		$number_prices = 5;
	}

	$t->set_var("number_prices", $number_prices);
	$eg->set_parameters_all($number_prices);

	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);

	$t->set_var("rp", htmlspecialchars($return_page));

	if ($sitelist) {
		$t->parse("site_column", false);
	}
	
	$t->pparse("main");

	function check_item_quantity()
	{
		global $eg, $r;
		$user_type_id = $eg->record->get_value("user_type_id");
		$min_quantity = $eg->record->get_value("min_quantity");
		$max_quantity = $eg->record->get_value("max_quantity");
		if (preg_match("/^[\+\s]+$/", $max_quantity)) {
			$eg->set_value("max_quantity", MAX_INTEGER);
		}
		if ($min_quantity) {
			$eg->record->change_property("max_quantity", MIN_VALUE, $min_quantity);
		} else {
			$eg->record->change_property("max_quantity", MIN_VALUE, 1);
		}
	}

	function check_max_quantity()
	{
		global $eg, $r;
		$max_quantity = $eg->get_value("max_quantity");
		if ($max_quantity == MAX_INTEGER) {
			$eg->set_value("max_quantity", "+");
		}
	}

	function check_site_id()
	{
		global $eg, $sitelist;
		if ($eg->record->is_empty("site_id")) {
			$eg->record->set_value("site_id", 0);
		}
		if ($eg->record->is_empty("user_type_id")) {
			$eg->record->set_value("user_type_id", 0);
		}
	}


?>