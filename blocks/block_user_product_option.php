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
	$allow_options = get_setting_value($product_settings, "allow_options", 0);
	if (!$item_id || !$allow_options) {
		header("Location: " . get_custom_friendly_url("user_products.php"));
		exit;
	}

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_files ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$downloadable_files = get_db_value($sql);

	$show_property_style = get_setting_value($product_settings, "show_option_property_style", 0);
	$use_on_list_default = get_setting_value($product_settings, "option_on_list_default", 0);
	$use_on_details_default = get_setting_value($product_settings, "option_on_details_default", 0);
	$use_on_table_default = get_setting_value($product_settings, "option_on_table_default", 0);
	$use_on_grid_default = get_setting_value($product_settings, "option_on_grid_default", 0);
	$use_on_second_default = get_setting_value($product_settings, "option_on_second_default", 0);
	$use_on_checkout_default = get_setting_value($product_settings, "option_on_checkout_default", 0);
	$show_use_on_list = get_setting_value($product_settings, "show_option_on_list", 0);
	$show_use_on_details = get_setting_value($product_settings, "show_option_on_details", 0);
	$show_use_on_table = get_setting_value($product_settings, "show_option_on_table", 0);
	$show_use_on_grid = get_setting_value($product_settings, "show_option_on_grid", 0);
	$show_use_on_second = get_setting_value($product_settings, "show_option_on_second", 0);
	$show_use_on_checkout = get_setting_value($product_settings, "show_option_on_checkout", 0);

	$show_control_style = get_setting_value($product_settings, "show_option_control_style", 0);
	$show_start_html = get_setting_value($product_settings, "show_option_start_html", 0);
	$show_middle_html = get_setting_value($product_settings, "show_option_middle_html", 0);
	$show_before_control_html = get_setting_value($product_settings, "show_option_before_control_html", 0);
	$show_after_control_html = get_setting_value($product_settings, "show_option_after_control_html", 0);
	$show_end_html = get_setting_value($product_settings, "show_option_end_html", 0);

	$show_control_code = get_setting_value($product_settings, "show_option_control_code", 0);
	$show_onchange_code = get_setting_value($product_settings, "show_option_onchange_code", 0);
	$show_onclick_code = get_setting_value($product_settings, "show_option_onclick_code", 0);

	$show_option_values = get_setting_value($product_settings, "show_option_values", 0);
	$show_option_value_prices = get_setting_value($product_settings, "show_option_value_prices", 0);
	$show_option_value_trade_prices = get_setting_value($product_settings, "show_option_value_trade_prices", 0);
	$show_option_value_weight = get_setting_value($product_settings, "show_option_value_weight", 0);
	$show_option_value_levels = get_setting_value($product_settings, "show_option_value_levels", 0);
	$show_option_value_downloads = get_setting_value($product_settings, "show_option_value_downloads", 0);

  $t->set_file("block_body","block_user_product_option.html");

	$t->set_var("user_home_href",  	   get_custom_friendly_url("user_home.php"));
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));
	$t->set_var("user_product_option_href",  get_custom_friendly_url("user_product_option.php"));
	$t->set_var("user_product_options_href", get_custom_friendly_url("user_product_options.php"));
	$t->set_var("user_files_select_href", get_custom_friendly_url("user_files_select.php"));

	$t->set_var("OPTION_PERCENTAGE_PRICE_DESC", str_replace("'", "\\'", OPTION_PERCENTAGE_PRICE_DESC));
	$t->set_var("ACTIVATE_CONTROL_CHECKBOX_MSG", str_replace("'", "\\'", ACTIVATE_CONTROL_CHECKBOX_MSG));
	$t->set_var("HIDE_OPTION_VALUE_DESC", str_replace("'", "\\'", HIDE_OPTION_VALUE_DESC));

	$t->set_var("item_id", $item_id);
	$t->set_var("item_name", htmlspecialchars($item_name));
	
	$property_id = get_param("property_id");

	$controls = 
		array(			
			array("", ""),  
			array("CHECKBOXLIST", "Checkboxes List"),
			array("LABEL",        "Label"),
			array("LISTBOX",      "ListBox"),
			array("RADIOBUTTON",  "Radio Buttons"),
			array("TEXTAREA",     "TextArea"),
			array("TEXTBOX",      "TextBox"),
			array("IMAGEUPLOAD",  "Image Upload")
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
	$r->add_textbox("property_order", INTEGER, OPTION_ORDER_MSG);
	$r->parameters["property_order"][REQUIRED] = true;
	$r->add_textbox("property_name", TEXT, NAME_MSG);
	$r->parameters["property_name"][REQUIRED] = true;
	$r->add_select("control_type", TEXT, $controls, OPTION_CONTROL_MSG);
	$r->parameters["control_type"][REQUIRED] = true;
	$r->add_textbox("property_description", TEXT, OPTION_TEXT_MSG);
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
	$r->change_property("use_on_grid", SHOW, $show_use_on_grid);
	$r->change_property("use_on_table", SHOW, $show_use_on_table);
	$r->change_property("use_on_second", SHOW, $show_use_on_second);
	$r->change_property("use_on_checkout", SHOW, $show_use_on_checkout);

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
	if (!$show_use_on_checkout) { $r->set_value("use_on_checkout", $use_on_checkout_default); }

	$ipv = new VA_Record($table_prefix . "items_properties_values", "properties");
	$ipv->add_where("item_property_id", INTEGER);
	$ipv->change_property("item_property_id", BEFORE_SHOW, "check_item_property_id");
	$ipv->add_hidden("property_id", INTEGER);
	$ipv->change_property("property_id", USE_IN_INSERT, true);
	$ipv->add_textbox("property_value", TEXT, DESCRIPTION_MSG);
	$ipv->parameters["property_value"][REQUIRED] = true;
	$ipv->change_property("property_value", REQUIRED, true);
	$ipv->add_textbox("value_order", INTEGER, "Order");
	$ipv->add_textbox("item_code", TEXT, PROD_CODE_MSG);
	$ipv->add_textbox("manufacturer_code", TEXT, MANUFACTURER_CODE_MSG);
	if ($show_option_value_prices) {
		$ipv->add_textbox("additional_price", NUMBER, SELLING_MSG);
		if ($show_option_value_trade_prices) {
			$ipv->add_textbox("trade_additional_price", NUMBER, PROD_TRADE_PRICE_MSG);
		}
		$ipv->add_textbox("percentage_price", NUMBER, PERCENTAGE_MSG);
		$ipv->add_textbox("buying_price", NUMBER, BUYING_MSG);
		$ipv->add_hidden("values_prices", TEXT);
	}
	if ($show_option_value_weight) {
		$ipv->add_textbox("additional_weight", NUMBER, PROD_WEIGHT_MSG);
		$ipv->add_hidden("values_weight", TEXT);
	}
	if ($show_option_value_levels) {
		$ipv->add_textbox("stock_level", INTEGER, QTY_MSG);
		$ipv->add_checkbox("use_stock_level", INTEGER);
		$ipv->add_checkbox("hide_out_of_stock", INTEGER);
		$ipv->add_hidden("values_levels", TEXT);
	}
	if ($show_option_value_weight || $show_option_value_levels) {
		$ipv->add_hidden("values_levels_weight", TEXT);
	}
	if ($show_option_value_downloads) {
		$ipv->add_textbox("download_files_ids", TEXT);
		$ipv->add_hidden("values_downloads", TEXT);
	}
	$ipv->add_checkbox("hide_value", INTEGER);
	$ipv->add_checkbox("is_default_value", INTEGER);
	
	$more_properties = get_param("more_properties");
	$number_properties = get_param("number_properties");

	$eg = new VA_EditGrid($ipv, "properties");
	$eg->get_form_values($number_properties);
	$eg->set_event(BEFORE_INSERT, "check_value_order");
	$eg->set_event(BEFORE_UPDATE, "check_value_order");
	$eg->set_event(BEFORE_SHOW, "check_downloads_ids");

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
			$r->set_value("property_type_id", 1);
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
	} else if($more_properties) {
		$number_properties += 5;
	} else { // set default values
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
		if ($use_on_checkout_default) { $r->set_value("use_on_checkout", 1); }

		$number_properties = 5;
	}

	$t->set_var("number_properties", $number_properties);

	$eg->set_parameters_all($number_properties);
	$r->set_parameters();

	$tabs_parse["general"] = true;
	if ($show_use_on_list || $show_use_on_details || $show_use_on_table || $show_use_on_grid || $show_use_on_second || $show_use_on_checkout) {
		$t->parse("show_option_block", false);	
	}
	if ($show_control_style || $show_start_html || $show_middle_html || $show_before_control_html || $show_after_control_html || $show_end_html) {
		$tabs_parse["html"] = true;
		$t->parse("apperance_block", false);	
	}
	if ($show_control_code || $show_onchange_code || $show_onclick_code) {
		$tabs_parse["js"] = true;
		$t->parse("js_block", false);	
	}
	if ($show_option_values) {
		if ($show_option_value_prices) {
			$t->parse("values_prices_title", false);	
		}
		if ($show_option_value_levels || $show_option_value_weight) {
			$levels_and_weight = "";
			if ($show_option_value_levels) {
				$levels_and_weight = STOCK_LEVEL_MSG;
			}
			if ($show_option_value_levels && $show_option_value_weight) {
				$levels_and_weight .= " & ";
			}
			if ($show_option_value_weight) {
				$levels_and_weight .= PROD_WEIGHT_MSG;
			}
			$t->set_var("levels_and_weight", $levels_and_weight);	
			$t->parse("values_levels_weight_title", false);	
		}
		if ($show_option_value_downloads) {
			$t->parse("values_downloads_title", false);	
		}
		$t->parse("options_values_block", false);	
	}
	

	if(strlen($property_id))	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}
	else
	{
		$t->set_var("save_button", ADD_BUTTON);
		$t->set_var("delete", "");	
	}

	// set styles for tabs
	$tabs = array("general" => EDIT_OPTION_MSG, "html" => OPTIONS_APPEARANCE_MSG, "js" => JAVASCRIPT_SETTINGS_MSG);
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

	function check_item_property_id()
	{
		global $eg, $ipv;

		if ($eg->record->get_value("item_property_id")) {
			$eg->record->change_property("item_property_id", SHOW, true);
		} else {
			$eg->record->change_property("item_property_id", SHOW, false);
		}
	}

	function check_value_order()
	{
		global $eg, $t, $db, $table_prefix, $item_id;

		$value_order = $eg->record->get_value("value_order");
		if (!$value_order) {
			$eg->record->set_value("value_order", 1);
		}
		// check for allowed ids
		$download_files_ids = $eg->record->get_value("download_files_ids");
		if ($download_files_ids) {
			$ids = array();
			$sql  = " SELECT file_id FROM " . $table_prefix . "items_files ";
			$sql .= " WHERE file_id IN (" . $db->tosql($download_files_ids, INTEGERS_LIST) . ")" ;
			$sql .= " AND item_id=" . $db->tosql($item_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$file_id = $db->f("file_id");
				$ids[] = $file_id;
			}
			$eg->record->set_value("download_files_ids", implode(",", $ids));
		}

	}

	function check_downloads_ids()
	{
		global $eg, $t, $db, $table_prefix, $downloadable_files, $item_id;
		$t->set_var("selected_files", "");

		$download_files_ids = $eg->record->get_value("download_files_ids");

		if ($download_files_ids) {
			$ids = array();
			$sql  = " SELECT * FROM " . $table_prefix . "items_files ";
			$sql .= " WHERE file_id IN (" . $db->tosql($download_files_ids, INTEGERS_LIST) . ")" ;
			$sql .= " AND item_id=" . $db->tosql($item_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$file_id = $db->f("file_id");
				$ids[] = $file_id;
				$file_title = $db->f("download_title");
				if (!$file_title) {
					$file_title = basename($db->f("download_path"));
				}
	    
				$t->set_var("file_id", $file_id);
				$t->set_var("file_title", $file_title);
				$t->set_var("file_title_js", str_replace("\"", "&quot;", $file_title));
			  
				$t->parse("selected_files", true);
				$t->parse("selected_files_js", true);
			}
			$eg->record->set_value("download_files_ids", implode(",", $ids));
		}

		if ($downloadable_files) {
			$t->parse("select_file_link", false);
		} else {
			$t->set_var("select_file_link", "");
		}

	}



?>