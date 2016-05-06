<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_property.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path."messages/".$language_code."/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_categories");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_property.html");

	$t->set_var("admin_href", $admin_site_url . "admin.php");
	$t->set_var("admin_property_href", "admin_property.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_item_types_href", "admin_item_types.php");
	$t->set_var("admin_item_type_href", "admin_item_type.php");
	
	$t->set_var("admin_product_href", "admin_product.php");
	$t->set_var("admin_properties_href", "admin_properties.php");
	$t->set_var("admin_files_select_href", "admin_files_select.php");

	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", OPTION_MSG, CONFIRM_DELETE_MSG));
	$t->set_var("HIDE_OPTION_VALUE_JS", str_replace("'", "\\'", HIDE_OPTION_VALUE_DESC));
	$t->set_var("ACTIVATE_CONTROL_CHECKBOX_JS", str_replace("'", "\\'", ACTIVATE_CONTROL_CHECKBOX_MSG));
	$t->set_var("CHECK_STOCK_USE_JS", str_replace("'", "\\'", CHECK_STOCK_USE_MSG));
	$t->set_var("OPTION_PERCENTAGE_PRICE_JS", str_replace("'", "\\'", OPTION_PERCENTAGE_PRICE_DESC));
	
	$item_id = get_param("item_id");
	if(!strlen($item_id)) $item_id= "0";
	$item_type_id = get_param("item_type_id");
	if(!strlen($item_type_id)) $item_type_id = "0";
	$category_id = get_param("category_id");
	if(!strlen($category_id)) $category_id = "0";
	$property_id = get_param("property_id");

	$t->set_var("item_id", htmlspecialchars($item_id));
	$t->set_var("item_type_id", htmlspecialchars($item_type_id));

	$parent_properties = array();
	$downloadable_files = 0;
	if ($item_type_id > 0) {
		$sql  = " SELECT item_type_name FROM " . $table_prefix . "item_types ";
		$sql .= " WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$t->set_var("item_type_name", get_translation($db->f("item_type_name")));

			// get parent options
			$sql  = " SELECT property_id, property_name FROM " . $table_prefix . "items_properties ";
			$sql .= " WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER);
			$sql .= " AND (property_type_id=1 OR property_type_id=3) ";
			if ($property_id) {
				$sql .= " AND property_id<>" . $db->tosql($property_id, INTEGER);
			}
			$sql .= " ORDER BY property_order ";
			$parent_properties = get_db_values($sql, array(array("", "")));
		} else {
			die(str_replace("{item_type_id}", $item_id, PROD_TYPE_ID_NO_LONGER_EXISTS_MSG));
		}

		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_files ";
		$sql .= " WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER);
		$downloadable_files = get_db_value($sql);
	} else {
		$sql  = " SELECT item_type_id, item_name FROM " . $table_prefix . "items ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$db_type_id = $db->f("item_type_id");
			$t->set_var("item_name", get_translation($db->f("item_name")));

			// get parent options
			$sql  = " SELECT property_id, property_name FROM " . $table_prefix . "items_properties ";
			$sql .= " WHERE (item_id=" . $db->tosql($item_id, INTEGER);
			$sql .= " OR item_type_id=" . $db->tosql($db_type_id, INTEGER) . ") ";
			$sql .= " AND (property_type_id=1 OR property_type_id=3) ";
			if ($property_id) {
				$sql .= " AND property_id<>" . $db->tosql($property_id, INTEGER);
			}
			$sql .= " ORDER BY property_order ";
			$parent_properties = get_db_values($sql, array(array("", "")));
		} else {
			die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));
		}

		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_files ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$downloadable_files = get_db_value($sql);

		$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
		$tree->show($category_id);
	}

	$controls = 
		array(			
			array("", ""),  
			array("CHECKBOXLIST", CHECKBOXLIST_MSG),
			array("LABEL",        LABEL_MSG),
			array("LISTBOX",      LISTBOX_MSG),
			array("RADIOBUTTON",  RADIOBUTTON_MSG),
			array("TEXTAREA",     TEXTAREA_MSG),
			array("TEXTBOX",      TEXTBOX_MSG),
			array("TEXTBOXLIST",  TEXTBOXLIST_MSG),
			array("IMAGEUPLOAD",  IMAGEUPLOAD_MSG)
			);

	$usage_types = 
		array(			
			array("1", AUTO_ADD_TO_ALL_PRODS_MSG),
			array("2", SELECT_OPTION_AND_VALUES_MSG),
			array("3", SELECT_OPTION_ALL_VALUES_MSG),
		);

	$prices_types = 
		array(			
			array("0", NONE_MSG),
			array("1", SINGLE_TOTAL_PRICE_MSG),
			array("2", FOR_SPECIFIED_CONTOL_MSG),
			array("3", FOR_SPECIFIED_LETTER_MSG),
			array("4", FOR_SPECIFIED_NONSPACE_MSG),
		);

	$free_prices_types = 
		array(			
			array("0", NONE_MSG),
			array("1", SUMMARY_DISCOUNT_MSG),
			array("2", FREE_CHARGE_CONTROLS_MSG),
			array("3", FREE_CHARGE_LETTERS_MSG),
			array("4", FREE_CHARGE_NONSPACE_MSG),
		);

	$limit_types = 
		array(			
			array("0", NONE_MSG),
			array("1", MAX_LETTERS_ALL_CONTROLS_MSG),
			array("2", MAX_LETTERS_EACH_CONTROL_MSG),
			array("3", MAX_NONSPACE_ALL_CONTROLS_MSG),
			array("4", MAX_NONSPACE_EACH_CONTROL_MSG),
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
	$r->add_textbox("property_name", TEXT, OPTION_NAME_MSG);
	$r->parameters["property_name"][REQUIRED] = true;
	$r->add_select("usage_type", INTEGER, $usage_types, ASSIGN_OPTION_MSG);
	$r->parameters["usage_type"][REQUIRED] = true;
	$r->add_select("control_type", TEXT, $controls, OPTION_CONTROL_MSG);
	$r->parameters["control_type"][REQUIRED] = true;
	$r->add_select("parent_property_id", INTEGER, $parent_properties, PARENT_OPTION_MSG);
	$r->add_select("parent_value_id", INTEGER, "", PARENT_OPTION_VALUE_MSG);

	$r->add_select("property_price_type", INTEGER, $prices_types, PRICE_TYPE_MSG);
	$r->add_textbox("additional_price", NUMBER, PRICE_MSG);
	$r->add_textbox("trade_additional_price", NUMBER, PROD_TRADE_PRICE_MSG);

	$r->add_select("free_price_type", INTEGER, $free_prices_types, DISCOUNT_PRICE_TYPE_MSG);
	$r->add_textbox("free_price_amount", NUMBER, DISCOUNT_AMOUNT_MSG);

	$r->add_select("max_limit_type", INTEGER, $limit_types, MAX_LIMIT_TYPE_MSG);
	$r->add_textbox("max_limit_length", INTEGER, MAX_LIMIT_LENGTH_MSG);

	$r->add_textbox("property_description", TEXT, OPTION_TEXT_MSG);
	$r->add_textbox("property_style", TEXT);
	$r->add_textbox("control_style", TEXT);
	$r->add_checkbox("use_on_list", INTEGER);
	$r->add_checkbox("use_on_details", INTEGER);
	$r->add_checkbox("use_on_table", INTEGER);
	$r->add_checkbox("use_on_grid", INTEGER);
	$r->add_checkbox("use_on_second", INTEGER);
	$r->add_checkbox("use_on_checkout", INTEGER);
	$r->add_checkbox("required", INTEGER);
	$r->add_textbox("start_html", TEXT);
	$r->add_textbox("middle_html", TEXT);
	$r->add_textbox("before_control_html", TEXT);
	$r->add_textbox("after_control_html", TEXT);
	$r->add_textbox("end_html", TEXT);
	$r->add_textbox("control_code", TEXT);
	$r->add_textbox("onchange_code", TEXT);
	$r->add_textbox("onclick_code", TEXT);
	$r->add_hidden("category_id", INTEGER);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("page", TEXT);
	$r->return_page = "admin_properties.php";

	$r->get_form_values();

	$ipv = new VA_Record($table_prefix . "items_properties_values", "properties");
	$ipv->add_where("item_property_id", INTEGER);
	$ipv->add_hidden("property_id", INTEGER);
	$ipv->change_property("property_id", USE_IN_INSERT, true);

	$ipv->add_textbox("property_value", TEXT, DESCRIPTION_MSG);
	$ipv->add_textbox("value_order", INTEGER, SORT_ORDER_MSG);
	$ipv->add_textbox("item_code", TEXT, PROD_CODE_MSG);
	$ipv->add_textbox("manufacturer_code", TEXT, MANUFACTURER_CODE_MSG);
	$ipv->parameters["property_value"][REQUIRED] = true;
	$ipv->change_property("property_value", REQUIRED, true);
	$ipv->add_textbox("additional_price", NUMBER, SELLING_PRICE_MSG);
	$ipv->add_textbox("trade_additional_price", NUMBER, TRADE_SELLING_PRICE_MSG);
	$ipv->add_textbox("percentage_price", NUMBER, PERCENTAGE_PRICE_MSG);
	$ipv->add_textbox("buying_price", NUMBER, PROD_BUYING_PRICE_MSG);
	$ipv->add_textbox("additional_weight", NUMBER, WEIGHT_MSG);
	$ipv->add_textbox("stock_level", INTEGER, QUANTITY_MSG);
	$ipv->add_checkbox("use_stock_level", INTEGER);
	$ipv->add_checkbox("hide_out_of_stock", INTEGER);
	$ipv->add_textbox("download_files_ids", TEXT);
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

	if (!$item_type_id) {
		$r->change_property("usage_type", SHOW, false);
		$r->set_value("usage_type", 1);
	}
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
			if ($r->get_value("free_price_type") != 1 && !$r->is_empty("free_price_amount")) {
				$r->set_value("free_price_amount", intval($r->get_value("free_price_amount")));
			}
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
				$r->set_value("item_type_id", $item_type_id);
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
		if ($r->get_value("free_price_type") != 1 && !$r->is_empty("free_price_amount")) {
			$r->set_value("free_price_amount", intval($r->get_value("free_price_amount")));
		}
		if ($r->get_value("additional_price") == 0) {
			$r->set_value("additional_price", "");
		}
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
		if ($item_type_id > 0) {
			$sql .= " WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER);
		} else {
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		}
		$property_order = get_db_value($sql);
		$property_order = ($property_order) ? ($property_order + 1) : 1;
		$r->set_value("property_order", $property_order);
		$r->set_value("use_on_list", 1);
		$r->set_value("use_on_details", 1);
		$r->set_value("use_on_table", 1);
		$r->set_value("use_on_grid", 1);
		$r->set_value("usage_type", 1);

		$number_properties = 5;
	}
	$t->set_var("number_properties", $number_properties);


	$parent_values = array(array("", ""));
	if (is_array($parent_properties) && sizeof($parent_properties) > 1) {
		for ($p = 0; $p < sizeof($parent_properties); $p++) {
			$parent_id = $parent_properties[$p][0];
			if ($parent_id) {
				$t->set_var("property_id", $parent_id);
				$t->parse("parent_options", true);
				$sql  = " SELECT item_property_id, property_value FROM " . $table_prefix . "items_properties_values ";
				$sql .= " WHERE property_id=" . $db->tosql($parent_id, INTEGER);
				$db->query($sql);
				while ($db->next_record()) {
					$list_id = $db->f("item_property_id");
					$list_value = $db->f("property_value");
					$t->set_var("value_id", $list_id);
					$t->set_var("value_title", htmlspecialchars($list_value));
					$t->parse("options_values", true);
					if ($r->get_value("parent_property_id") == $parent_id) {
						$parent_values[] = array($list_id, $list_value);
					}
				}
			}
		}
	}

	$r->change_property("parent_value_id", VALUES_LIST, $parent_values);
	$eg->set_parameters_all($number_properties);
	$r->set_parameters();

	if (is_array($parent_properties) && sizeof($parent_properties) > 1) {
		if (is_array($parent_values) && sizeof($parent_values) > 1) {
			$t->set_var("parent_value_style", "display: block;");
		} else {
			$t->set_var("parent_value_style", "display: none;");
		}
		$t->parse("parent_property_block", false);
	}

	if ($item_type_id > 0) {
		$t->parse("type_path");
	} else {
		$t->parse("product_path");
	}

	if(strlen($property_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_BUTTON);
		$t->set_var("delete", "");	
	}

	$tabs = array("general" => ADMIN_GENERAL_MSG, "html" => OPTIONS_APPEARANCE_MSG, "js" => JAVASCRIPT_SETTINGS_MSG);
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
		$t->parse("tabs", $tab_title);
	}
	$t->set_var("tab", $tab);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

function check_value_order()
{
	global $eg;
	$value_order = $eg->record->get_value("value_order");
	if (!$value_order) {
		$eg->record->set_value("value_order", 1);
	}
}

	function check_downloads_ids()
	{
		global $eg, $r, $t, $db, $table_prefix, $downloadable_files;
		$t->set_var("selected_files", "");

		$download_files_ids = $eg->record->get_value("download_files_ids");
		$sql  = " SELECT * FROM " . $table_prefix . "items_files ";
		$sql .= " WHERE file_id IN (" . $db->tosql($download_files_ids, INTEGERS_LIST) . ")" ;
		$db->query($sql);
		while ($db->next_record()) {
			$file_id = $db->f("file_id");
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

		if ($downloadable_files) {
			$t->parse("select_file_link", false);
		} else {
			$t->set_var("select_file_link", "");
		}

	}

?>