<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_filter_property.php                                ***
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
	include_once("./admin_common.php");

	check_admin_security("filters");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_filter_property.html");

	$t->set_var("admin_filter_href",     "admin_filter.php");
	$t->set_var("admin_filters_href",    "admin_filters.php");
	$t->set_var("admin_filter_property_href",   "admin_filter_property.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", OPTION_MSG, CONFIRM_DELETE_MSG));
                              
	$filter_id = get_param("filter_id");
	$property_id = get_param("property_id");

	$sql  = " SELECT filter_name FROM " . $table_prefix . "filters ";
	$sql .= " WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$t->set_var("filter_name", get_translation($db->f("filter_name")));
	} else {
		header("Location: admin_filters.php");
		exit;
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
			array("IMAGEUPLOAD",  IMAGEUPLOAD_MSG)
			);

	$property_types = 
		array(			
			array("", ""),  
			array("manufacturer",  PROD_MANUFACTURER_MSG),
			array("product_type",  PROD_TYPE_MSG),
			array("product_price", PRODUCT_PRICE_MSG),
			array("product_option",PRODUCT_OPTION_MSG),
			array("product_specification",PRODUCT_SPECIFICATION_MSG),
			array("custom",        CUSTOM_OPTION_MSG)
			);

	// set up html form parameters
	$r = new VA_Record($table_prefix . "filters_properties");
	$r->add_where("property_id", INTEGER);
	$r->change_property("property_id", USE_IN_INSERT, true);
	$r->add_hidden("filter_id", INTEGER);
	$r->change_property("filter_id", USE_IN_INSERT, true);
	$r->add_textbox("property_order", INTEGER, OPTION_ORDER_MSG);
	$r->parameters["property_order"][REQUIRED] = true;
	$r->add_textbox("property_name", TEXT, OPTION_NAME_MSG);
	$r->parameters["property_name"][REQUIRED] = true;
	$r->add_select("property_type", TEXT, $property_types, OPTION_TYPE_MSG);
	$r->parameters["property_type"][REQUIRED] = true;
	$r->add_textbox("property_value", TEXT, OPTION_VALUE_MSG);

	$r->add_textbox("filter_from_sql", TEXT);
	$r->add_textbox("filter_join_sql", TEXT);
	$r->add_textbox("filter_where_sql", TEXT);
	$r->add_textbox("list_group_fields", TEXT);
	$r->add_textbox("list_group_where", TEXT);
	$r->add_textbox("list_table", TEXT);
	$r->add_textbox("list_field_id", TEXT);
	$r->add_textbox("list_field_title", TEXT);
	$r->add_textbox("list_field_total", TEXT);
	$r->add_textbox("list_sql", TEXT);

	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("page", TEXT);
	$r->return_page = "admin_filter_properties.php";

	$r->get_form_values();

	$ipv = new VA_Record($table_prefix . "filters_properties_values", "properties");
	$ipv->add_where("value_id", INTEGER);
	$ipv->add_hidden("property_id", INTEGER);
	$ipv->change_property("property_id", USE_IN_INSERT, true);
	$ipv->add_textbox("value_order", INTEGER, ADMIN_ORDER_MSG);
	$ipv->add_textbox("list_value_id", TEXT, VALUE_ID_MSG);
	$ipv->add_textbox("list_value_title", TEXT, TITLE_MSG);
	$ipv->change_property("list_value_title", REQUIRED, true);
	$ipv->add_textbox("filter_where_sql", TEXT);
	
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
			$db->query("DELETE FROM " . $table_prefix . "filters_properties WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "filters_properties_values WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}
		$property_type = $r->get_value("property_type");	
		if ($property_type == "product_option" || $property_type == "product_specification") {
			$r->change_property("property_value", REQUIRED, true);
			if ($property_type == "product_option") {
				$r->change_property("property_value", CONTROL_DESC, PRODUCT_OPTION_NAME_MSG);
			} else {
				$r->change_property("property_value", CONTROL_DESC, PRODUCT_SPECIFICATION_NAME_MSG);
			}
		} else if ($property_type == "product_price") {
			$eg->set_values("list_value_id", ""); // we don't need to save this field for price
			$ipv->change_property("filter_where_sql", REQUIRED, true);
		} else if ($property_type == "manufacturer" || $property_type == "product_type") {
			//list_value_id or filter_where_sql required for both types
		}

		$is_valid = $r->validate();
		$is_valid = ($eg->validate() && $is_valid); 

		if($is_valid)
		{
			populate_filter_fields();
			if(strlen($property_id))
			{
				$r->update_record();
				update_filter_fields();
				$eg->set_values("property_id", $property_id);
				$eg->update_all($number_properties);
			}
			else
			{
				$db->query("SELECT MAX(property_id) FROM " . $table_prefix . "filters_properties");
				$db->next_record();
				$property_id = $db->f(0) + 1;
				$r->set_value("property_id", $property_id);
				$r->set_value("filter_id", $filter_id);
				$r->insert_record();
				update_filter_fields();
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
		$eg->change_property("value_id", USE_IN_SELECT, true);
		$eg->change_property("value_id", USE_IN_WHERE, false);
		$eg->change_property("property_id", USE_IN_WHERE, true);
		$eg->change_property("property_id", USE_IN_SELECT, true);
		$number_properties = $eg->get_db_values();
		if($number_properties == 0)
			$number_properties = 5;
	}
	else if($more_properties)
	{
		$number_properties += 5;
	}
	else // set default values
	{
		$sql  = " SELECT MAX(property_order) FROM " . $table_prefix . "filters_properties ";
		$sql .= " WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
		$property_order = get_db_value($sql);
		$property_order = ($property_order) ? ($property_order + 1) : 1;
		$r->set_value("property_order", $property_order);

		$number_properties = 5;
	}

	$t->set_var("number_properties", $number_properties);

	$eg->set_parameters_all($number_properties);
	$r->set_parameters();

	if(strlen($property_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}
	$property_type = $r->get_value("property_type");
	if ($property_type == "custom") {
		$t->set_var("custom_options_block_style", "display: block;");	
	} else {
		$t->set_var("custom_options_block_style", "display: none;");	
	}
	if ($property_type == "product_option" || $property_type == "product_specification") {
		if ($property_type == "product_option") {
			$t->set_var("property_value_name", PRODUCT_OPTION_NAME_MSG);	
		} else {
			$t->set_var("property_value_name", PRODUCT_SPECIFICATION_NAME_MSG);	
		}
		$t->set_var("property_value_block_style", "display: block;");	
	} else {
		$t->set_var("property_value_block_style", "display: none;");	
	}


	$tabs = array("general" => ADMIN_GENERAL_MSG);
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

function populate_filter_fields()
{
	global $r, $table_prefix;
	$property_type = $r->get_value("property_type");	
	if ($property_type == "manufacturer")  {
		$r->set_value("filter_from_sql", "");
		$r->set_value("filter_join_sql", "");
		$r->set_value("filter_where_sql", " i.manufacturer_id={value_id} ");
		$r->set_value("list_group_fields", "i.manufacturer_id");
		$r->set_value("list_group_where", "");
		$r->set_value("list_table", $table_prefix . "manufacturers");
		$r->set_value("list_field_id", "manufacturer_id");
		$r->set_value("list_field_title", "manufacturer_name");
		$r->set_value("list_field_total", "");
		$r->set_value("list_sql", "");
	} else if ($property_type == "product_type") {
		$r->set_value("filter_from_sql", "");
		$r->set_value("filter_join_sql", "");
		$r->set_value("filter_where_sql", " i.item_type_id={value_id} ");
		$r->set_value("list_group_fields", "i.item_type_id");
		$r->set_value("list_group_where", "");
		$r->set_value("list_table", $table_prefix . "item_types");
		$r->set_value("list_field_id", "item_type_id");
		$r->set_value("list_field_title", "item_type_name");
		$r->set_value("list_field_total", "");
		$r->set_value("list_sql", "");
	} else if ($property_type == "product_price") {
		$r->set_value("filter_from_sql", "");
		$r->set_value("filter_join_sql", "");
		$r->set_value("filter_where_sql", "");
		$r->set_value("list_group_fields", "");
		$r->set_value("list_group_where", "");
		$r->set_value("list_table", "");
		$r->set_value("list_field_id", "");
		$r->set_value("list_field_title", "");
		$r->set_value("list_field_total", "");
		$r->set_value("list_sql", "");
	} else if ($property_type == "product_option") {
		$r->set_value("filter_from_sql", "");
		$r->set_value("filter_join_sql", "");
		$r->set_value("filter_where_sql", "");
		$r->set_value("list_group_fields", "");
		$r->set_value("list_group_where", "");
		$r->set_value("list_table", "");
		$r->set_value("list_field_id", "");
		$r->set_value("list_field_title", "");
		$r->set_value("list_field_total", "");
		$r->set_value("list_sql", "");
	}
}

function update_filter_fields()
{
	global $r, $db, $table_prefix;
	$property_type = $r->get_value("property_type");	
	$property_value = $r->get_value("property_value");	
	if ($property_type == "product_option" && $property_value) {
		$pi = $r->get_value("property_id");
		$filter_from_sql = "((";

		//$filter_join_sql  = " LEFT JOIN " . $table_prefix . "items_properties fip_".$pi." ON (i.item_id = fip_".$pi.".item_id OR i.item_type_id = fip_".$pi.".item_type_id)) ";
		$filter_join_sql  = " LEFT JOIN " . $table_prefix . "items_properties fip_".$pi." ON i.item_id = fip_".$pi.".item_id) ";
		$filter_join_sql .= " LEFT JOIN " . $table_prefix . "items_properties_values fipv_".$pi." ON fipv_".$pi.".property_id= fip_".$pi.".property_id) ";
		$filter_where_sql  = " fip_".$pi.".property_name=" . $db->tosql($property_value, TEXT);
		$filter_where_sql .= " AND (fip_".$pi.".property_description='{value_id}' ";
		$filter_where_sql .= " OR fipv_".$pi.".property_value='{value_id}') ";
		$list_group_fields = "fip_".$pi.".property_description, fipv_".$pi.".property_value";
		$list_group_where = " fip_".$pi.".property_name=" . $db->tosql($property_value, TEXT);

		$r->set_value("filter_from_sql", $filter_from_sql);
		$r->set_value("filter_join_sql", $filter_join_sql);
		$r->set_value("filter_where_sql", $filter_where_sql);
		$r->set_value("list_group_fields", $list_group_fields);
		$r->set_value("list_group_where", $list_group_where);
		$r->set_value("list_table", "");
		$r->set_value("list_field_id", "");
		$r->set_value("list_field_title", "");
		$r->set_value("list_field_total", "");
		$r->set_value("list_sql", "");
		$r->update_record();
	} else if ($property_type == "product_specification" && $property_value) {
		$pi = $r->get_value("property_id");
		$filter_from_sql = "(";

		$filter_where_sql  = " ff_".$pi.".feature_name=" . $db->tosql($property_value, TEXT);
		$filter_where_sql .= " AND ff_".$pi.".feature_value='{value_id}' ";
		$filter_join_sql  = " LEFT JOIN " . $table_prefix . "features ff_".$pi." ON i.item_id = ff_".$pi.".item_id) ";
		$list_group_fields = "ff_".$pi.".feature_value";
		$list_group_where = " ff_".$pi.".feature_name=" . $db->tosql($property_value, TEXT);

		$r->set_value("filter_from_sql", $filter_from_sql);
		$r->set_value("filter_join_sql", $filter_join_sql);
		$r->set_value("filter_where_sql", $filter_where_sql);
		$r->set_value("list_group_fields", $list_group_fields);
		$r->set_value("list_group_where", $list_group_where);
		$r->set_value("list_table", "");
		$r->set_value("list_field_id", "");
		$r->set_value("list_field_title", "");
		$r->set_value("list_field_total", "");
		$r->set_value("list_sql", "");
		$r->update_record();
	}

}

?>