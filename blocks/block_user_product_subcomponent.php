<?php

	check_user_security("access_products");

	$item_id = get_param("item_id");
	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= " AND user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$item_name = get_translation($db->f("item_name"));
	} else {
		$item_id = "";
	}

	// get product settings
	$setting_type = "user_product_" . get_session("session_user_type_id");
	$product_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$product_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	$allow_subcomponents = get_setting_value($product_settings, "allow_subcomponents", 0);
	$show_component_trade_price = get_setting_value($product_settings, "show_component_trade_price", 0);
	if (!$item_id || !$allow_subcomponents) {
		header("Location: " . get_custom_friendly_url("user_products.php"));
		exit;
	}

	$t->set_file("block_body","block_user_product_subcomponent.html");

	$t->set_var("user_home_href",  	   get_custom_friendly_url("user_home.php"));
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));
	$t->set_var("user_product_select_href",  get_custom_friendly_url("user_product_select.php"));
	$t->set_var("user_product_options_href", get_custom_friendly_url("user_product_options.php"));
	$t->set_var("user_product_subcomponent_href", get_custom_friendly_url("user_product_subcomponent.php"));

	$t->set_var("item_id", $item_id);
	$t->set_var("item_name", htmlspecialchars($item_name));

	$property_id = get_param("property_id");

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
	$r->add_textbox("property_name", TEXT, NAME_MSG);
	$r->parameters["property_name"][REQUIRED] = true;
	$r->add_textbox("sub_item_id", INTEGER, SUBCOMP_ID_MSG);
	$r->parameters["sub_item_id"][REQUIRED] = true;
	$r->add_textbox("quantity", INTEGER, QUANTITY_MSG);
	$r->add_textbox("additional_price", FLOAT, SUBCOMP_PRICE_MSG);
	if ($show_component_trade_price) {
		$r->add_textbox("trade_additional_price", FLOAT, "Subcomponent Trade Price");
	}
	$r->add_hidden("control_type", TEXT);
	$r->change_property("control_type", USE_SQL_NULL, false);
	$r->add_textbox("use_on_list", INTEGER);
	$r->add_textbox("use_on_details", INTEGER);
	$r->add_textbox("use_on_second", INTEGER);
	$r->add_textbox("use_on_checkout", INTEGER);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("page", TEXT);
	$r->return_page = get_custom_friendly_url("user_product_options.php");

	$r->get_form_values();


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
			$sql  = " SELECT item_id FROM " . $table_prefix . "items ";
			$sql .= " WHERE item_id=" . $db->tosql($sub_item_id, INTEGER);
			$sql .= " AND user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
			$db->query($sql);
			if (!$db->next_record()) {
				$r->errors = "Product with ID <b>'" . $sub_item_id . "'</b> doesn't exists in the database.";
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
			if (strlen($property_id))
			{
				$r->update_record();
			}
			else
			{
				$db->query("SELECT MAX(property_id) FROM " . $table_prefix . "items_properties");
				$db->next_record();
				$property_id = $db->f(0) + 1;
				$r->set_value("property_id", $property_id);
				$r->set_value("item_id", $item_id);
				$r->set_value("item_type_id", 0);
				$r->insert_record();
			}
			header("Location: " . $return_page);
			exit;
		}
	} elseif (strlen($property_id)) {
		$r->get_db_values();
	} else { // set default values

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
			if ($is_sales) {
				$price = $sales_price;
			}
			$t->set_var("basic_price", "&nbsp;&ndash;&nbsp;" . number_format($price, 2, 	".", ""));
		}
	}

	if (strlen($property_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_BUTTON);
		$t->set_var("delete", "");	
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>