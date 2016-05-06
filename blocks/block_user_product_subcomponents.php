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
	$allow_subcomponents_selection = get_setting_value($product_settings, "allow_subcomponents_selection", 0);
	if (!$item_id || !$allow_subcomponents_selection) {
		header("Location: " . get_custom_friendly_url("user_products.php"));
		exit;
	}

	$show_property_style = get_setting_value($product_settings, "show_component_property_style", 0);
	$use_on_list_default = get_setting_value($product_settings, "component_on_list_default", 0);
	$use_on_details_default = get_setting_value($product_settings, "component_on_details_default", 0);
	$use_on_table_default = get_setting_value($product_settings, "component_on_table_default", 0);
	$use_on_grid_default = get_setting_value($product_settings, "component_on_grid_default", 0);
	$use_on_second_default = get_setting_value($product_settings, "component_on_second_default", 0);
	$show_use_on_list = get_setting_value($product_settings, "show_component_on_list", 0);
	$show_use_on_details = get_setting_value($product_settings, "show_component_on_details", 0);
	$show_use_on_table = get_setting_value($product_settings, "show_component_on_table", 0);
	$show_use_on_grid = get_setting_value($product_settings, "show_component_on_grid", 0);
	$show_use_on_second = get_setting_value($product_settings, "show_component_on_second", 0);

	$show_control_style = get_setting_value($product_settings, "show_component_control_style", 0);
	$show_start_html = get_setting_value($product_settings, "show_component_start_html", 0);
	$show_middle_html = get_setting_value($product_settings, "show_component_middle_html", 0);
	$show_before_control_html = get_setting_value($product_settings, "show_component_before_control_html", 0);
	$show_after_control_html = get_setting_value($product_settings, "show_component_after_control_html", 0);
	$show_end_html = get_setting_value($product_settings, "show_component_end_html", 0);

	$show_control_code = get_setting_value($product_settings, "show_component_control_code", 0);
	$show_onchange_code = get_setting_value($product_settings, "show_component_onchange_code", 0);
	$show_onclick_code = get_setting_value($product_settings, "show_component_onclick_code", 0);

	$show_component_trade_price = get_setting_value($product_settings, "show_component_trade_price", 0);

  $t->set_file("block_body","block_user_product_subcomponents.html");

	$t->set_var("user_home_href",  	   get_custom_friendly_url("user_home.php"));
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));
	$t->set_var("user_product_select_href",  get_custom_friendly_url("user_product_select.php"));
	$t->set_var("user_product_options_href", get_custom_friendly_url("user_product_options.php"));
	$t->set_var("user_product_subcomponent_href",  get_custom_friendly_url("user_product_subcomponent.php"));
	$t->set_var("user_product_subcomponents_href", get_custom_friendly_url("user_product_subcomponents.php"));

	$t->set_var("item_id", $item_id);
	$t->set_var("item_name", htmlspecialchars($item_name));

	$confirm_delete_message = str_replace("{record_name}", SUBCOMPONENT_SELECTION_MSG, CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete_message", str_replace("'", "\\'", $confirm_delete_message));

	$item_id = get_param("item_id");
	if(!strlen($item_id)) $item_id= "0";
	$property_id = get_param("property_id");

	$controls = 
		array(			
			array("", ""),  
			array("CHECKBOXLIST", "Checkboxes List"),
			array("LISTBOX",      "ListBox"),
			array("RADIOBUTTON",  "Radio Buttons"),
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
	$r->add_textbox("property_order", INTEGER, COMPONENT_ORDER_MSG);
	$r->parameters["property_order"][REQUIRED] = true;
	$r->add_textbox("property_name", TEXT, NAME_MSG);
	$r->parameters["property_name"][REQUIRED] = true;
	$r->add_select("control_type", TEXT, $controls, COMPONENT_CONTROL_MSG);
	$r->parameters["control_type"][REQUIRED] = true;
	if ($show_property_style) {
		$r->add_textbox("property_style", TEXT);
	}
	$r->add_checkbox("use_on_list", INTEGER);
	$r->add_checkbox("use_on_details", INTEGER);
	$r->add_checkbox("use_on_table", INTEGER);
	$r->add_checkbox("use_on_grid", INTEGER);
	$r->add_checkbox("use_on_second", INTEGER);
	$r->add_checkbox("use_on_checkout", INTEGER);

	$r->change_property("use_on_list", SHOW, $show_use_on_list);
	$r->change_property("use_on_details", SHOW, $show_use_on_details);
	$r->change_property("use_on_table", SHOW, $show_use_on_table);
	$r->change_property("use_on_grid", SHOW, $show_use_on_grid);
	$r->change_property("use_on_second", SHOW, $show_use_on_second);
	$r->add_checkbox("required", INTEGER);

	if ($show_control_style) {
		$r->add_textbox("control_style", TEXT);
	}
	if ($show_start_html) {
		$r->add_textbox("start_html", TEXT);
	}
	if ($show_middle_html) {
		$r->add_textbox("middle_html", TEXT);
	}
	if ($show_before_control_html) {
		$r->add_textbox("before_control_html", TEXT);
	}
	if ($show_after_control_html) {
		$r->add_textbox("after_control_html", TEXT);
	}
	if ($show_end_html) {
		$r->add_textbox("end_html", TEXT);
	}

	if ($show_control_code) {
		$r->add_textbox("control_code", TEXT);
	}
	if ($show_onchange_code) {
		$r->add_textbox("onchange_code", TEXT);
	}
	if ($show_onclick_code) {
		$r->add_textbox("onclick_code", TEXT);
	}

	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("page", TEXT);
	$r->return_page = get_custom_friendly_url("user_product_options.php");

	$r->get_form_values();

	if (!$show_use_on_list) { $r->set_value("use_on_list", $use_on_list_default); }
	if (!$show_use_on_details) { $r->set_value("use_on_details", $use_on_details_default); }
	if (!$show_use_on_table) { $r->set_value("use_on_table", $use_on_table_default); }
	if (!$show_use_on_grid) { $r->set_value("use_on_grid", $use_on_grid_default); }
	if (!$show_use_on_second) { $r->set_value("use_on_second", $use_on_second_default); }

	$ipv = new VA_Record($table_prefix . "items_properties_values", "properties");
	$ipv->add_where("item_property_id", INTEGER);
	$ipv->add_hidden("property_id", INTEGER);
	$ipv->change_property("property_id", USE_IN_INSERT, true);
	$ipv->add_textbox("sub_item_id", TEXT, SUBCOMP_ID_MSG);
	$ipv->change_property("sub_item_id", REQUIRED, true);
	$ipv->change_property("sub_item_id", BEFORE_SHOW, "show_basic_price");
	$ipv->change_property("sub_item_id", AFTER_VALIDATE, "check_sub_item_id");

	$ipv->add_textbox("value_order", TEXT, "Order");
	$ipv->add_textbox("property_value", TEXT, NAME_MSG);
	$ipv->change_property("property_value", REQUIRED, true);
	$ipv->add_textbox("quantity", NUMBER, QUANTITY_MSG);
	$ipv->add_textbox("additional_price", NUMBER, OVERRIDE_PRICE_MSG);
	if ($show_component_trade_price) {
		$ipv->add_textbox("trade_additional_price", NUMBER, PROD_TRADE_PRICE_MSG);
		$t->parse("trade_additional_price_title", false);
	}
	$ipv->add_checkbox("hide_value", INTEGER);
	$ipv->add_checkbox("is_default_value", INTEGER);
	
	$more_properties = get_param("more_properties");
	$number_properties = get_param("number_properties");

	$eg = new VA_EditGrid($ipv, "properties");
	$eg->get_form_values($number_properties);

	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	$return_page = $r->get_return_url();

	if(strlen($operation) && !$more_properties)
	{
		$tab = "general";
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $property_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "items_properties WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "items_properties_values WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();
		$is_valid = ($eg->validate() && $is_valid); 

		if($is_valid)
		{
			$r->set_value("property_type_id", 3);
			$r->set_value("use_on_checkout", 0);
			if(strlen($property_id))
			{
				$r->update_record();
				$eg->set_values("property_id", $property_id);
				$eg->update_all($number_properties);
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
				$eg->set_values("property_id", $property_id);
				$eg->insert_all($number_properties);
			}
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($property_id) && !$more_properties)
	{
		$r->get_db_values();
		$eg->set_value("property_id", $property_id);
		$eg->change_property("item_property_id", USE_IN_SELECT, true);
		$eg->change_property("item_property_id", USE_IN_WHERE, false);
		$eg->change_property("property_id", USE_IN_WHERE, true);
		$eg->change_property("property_id", USE_IN_SELECT, true);
		$number_properties = $eg->get_db_values();
		if ($number_properties == 0) {
			$number_properties = 5;
		}
	}
	else if($more_properties)
	{
		$number_properties += 5;
	}
	else // set default values
	{
		$sql  = " SELECT MAX(property_order) FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$property_order = get_db_value($sql);
		$property_order = ($property_order) ? ($property_order + 1) : 1;
		$r->set_value("property_order", $property_order);
		if ($use_on_list_default) { $r->set_value("use_on_list", 1); }
		if ($use_on_details_default) { $r->set_value("use_on_details", 1); }
		if ($use_on_table_default) { $r->set_value("use_on_table", 1); }
		if ($use_on_grid_default) { $r->set_value("use_on_grid", 1); }
		if ($use_on_second_default) { $r->set_value("use_on_second", 1); }

		$number_properties = 5;
	}

	$t->set_var("number_properties", $number_properties);

	$eg->set_parameters_all($number_properties);
	$r->set_parameters();

	$tabs_parse["general"] = true;
	if ($show_use_on_list || $show_use_on_details || $show_use_on_table || $show_use_on_grid || $show_use_on_second) {
		$t->parse("show_component_block", false);	
	}
	if ($show_control_style || $show_start_html || $show_middle_html || $show_before_control_html || $show_after_control_html || $show_end_html) {
		$tabs_parse["html"] = true;
		$t->parse("apperance_block", false);	
	}
	if ($show_control_code || $show_onchange_code || $show_onclick_code) {
		$tabs_parse["js"] = true;
		$t->parse("js_block", false);	
	}

	if (strlen($property_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_BUTTON);
		$t->set_var("delete", "");	
	}

	// set styles for tabs
	$tabs = array("general" => EDIT_SUBCOMP_SELECTION_MSG, "html" => COMPONENT_APPEARANCE_MSG, "js" => JAVASCRIPT_SETTINGS_MSG);
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);
		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		if (isset($tabs_parse[$tab_name]) && $tabs_parse[$tab_name]) {
			$t->parse("tabs", true);
		}
	}
	$t->set_var("tab", $tab);

	$t->parse("block_body", false);
	$t->parse($block_name, true);
	
	function show_basic_price() 
	{
		global $t, $eg, $db, $table_prefix;
		$sub_item_id = $eg->record->get_value("sub_item_id"); 

		$t->set_var("basic_price", "");
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
				$t->set_var("basic_price", number_format($price, 2, 	".", ""));
			}
		}
  }


	function check_sub_item_id() 
	{
		global $eg, $db, $table_prefix;
		$sub_item_id = $eg->record->get_value("sub_item_id");
		$sql  = " SELECT item_id FROM " . $table_prefix . "items ";
		$sql .= " WHERE item_id=" . $db->tosql($sub_item_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		$db->query($sql);
		if (!$db->next_record()) {
			$eg->record->errors = "Product with ID <b>'" . $sub_item_id . "'</b> doesn't exists in the database.<br>";
		}
	}


?>