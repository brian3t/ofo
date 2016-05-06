<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_tax_rate.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("tax_rates");

	$eol = get_eol();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_tax_rate.html");
	$t->set_var("admin_tax_rate_href", "admin_tax_rate.php");
	$t->set_var("admin_tax_rates_href", "admin_tax_rates.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", TAX_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$r = new VA_Record($table_prefix . "tax_rates");
	$r->return_page = "admin_tax_rates.php";

	// load data to listbox
	$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states ORDER BY state_name ", array(array("0", ANY_MSG)));
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ", array(array("", "---".SELECT_COUNTRY_MSG."---")));

	$r->add_where("tax_id", INTEGER);
	$r->change_property("tax_id", USE_IN_INSERT, true);

	$r->add_checkbox("is_default", INTEGER);
	$r->add_textbox("tax_name", TEXT, TAX_NAME_MSG);
	$r->change_property("tax_name", REQUIRED, true);
	$r->add_select("state_id", INTEGER, $states, STATE_FIELD);
	$r->add_select("country_id", INTEGER, $countries, COUNTRY_FIELD);
	$r->change_property("country_id", REQUIRED, true);

	$r->add_textbox("tax_percent", FLOAT, TAX_PERCENT_MSG);
	$r->change_property("tax_percent", REQUIRED, true);
	$r->add_textbox("shipping_tax_percent", FLOAT, SHIPPING_TAX_PERCENT_MSG);

	$r->add_hidden("page", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);

	$r->get_form_values();

	$tax_rates_items = array();
	$sql  = " SELECT it.item_type_id, it.item_type_name, tri.tax_percent ";
	$sql .= " FROM (" . $table_prefix . "item_types it ";
	$sql .= " LEFT JOIN " . $table_prefix . "tax_rates_items tri ON (it.item_type_id=tri.item_type_id ";
	$sql .= " AND tri.tax_id=" . $db->tosql($r->get_value("tax_id"), INTEGER) . ")) ";
	$db->query($sql);
	while ($db->next_record()) {
		$item_type_id = $db->f("item_type_id");
		$item_type_name = get_translation($db->f("item_type_name"));
		$item_tax_percent = $db->f("tax_percent");
		$tax_rates_items[] = array($item_type_id, $item_type_name, $item_tax_percent);
	}
                                                                  
	$operation = get_param("operation");
	$tax_id = get_param("tax_id");

	if(strlen($operation))
	{
		$return_page = $r->get_return_url();
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $tax_id)
		{
			$r->delete_record();

			$sql = " DELETE FROM " . $table_prefix . "tax_rates_items WHERE tax_id=" . $db->tosql($tax_id, INTEGER);
			$db->query($sql);

			header("Location: " . $return_page);
			exit;
		}

		$r->validate();


		/*
		if(!$r->is_empty("country_id")) {
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "tax_rates ";
			$sql .= " WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER);
			if ($r->is_empty("state_id")) {
				$sql .= " AND ( state_id IS NULL OR  state_id=0 )";
			} else {
				$sql .= " AND state_id=" . $db->tosql($r->get_value("state_id"), INTEGER);
			}
			$sql .= $r->is_empty("tax_id") ? "" : " AND tax_id<>" . $db->tosql($tax_id, INTEGER);
			if(get_db_value($sql)) {
				$r->errors .= TAX_EXISTS_MSG ." <b>" . get_array_value($r->get_value("country_id"), $countries) . "</b>";
				if (!$r->is_empty("state_id")) {
					$r->errors .= " <b>(" . get_array_value($r->get_value("state_id"), $states) . ")</b>";
				}
			}
		}//*/

		for ($i = 0; $i < sizeof($tax_rates_items); $i++) {
			list ($item_type_id, $item_type_name, $item_tax_percent) = $tax_rates_items[$i]; 
			$item_tax_percent = get_param("item_tax_percent_" . $item_type_id);
			$tax_rates_items[$i][2] = $item_tax_percent;
			if ($item_tax_percent && !is_numeric($item_tax_percent)) {
				$error_message = str_replace("{field_name}", $item_type_name, INCORRECT_VALUE_MESSAGE);
				$r->errors .= $error_message . "<br>" . $eol;
			}
		}

		if(!strlen($r->errors))
		{
			if (strlen($r->get_value("tax_id"))) {
				$r->update_record();
			} else {
				$sql = "SELECT MAX(tax_id) FROM " . $table_prefix . "tax_rates ";
				$tax_id = get_db_value($sql);
				$r->set_value("tax_id", ($tax_id + 1));
				$r->insert_record();
			}

			// update item types tax percents
			$sql = " DELETE FROM " . $table_prefix . "tax_rates_items WHERE tax_id=" . $db->tosql($tax_id, INTEGER);
			$db->query($sql);
			for ($i = 0; $i < sizeof($tax_rates_items); $i++) {
				list ($item_type_id, $item_type_name, $item_tax_percent) = $tax_rates_items[$i]; 
				$sql  = " INSERT INTO " . $table_prefix . "tax_rates_items (tax_id, item_type_id, tax_percent) VALUES (";
				$sql .= $db->tosql($r->get_value("tax_id"), INTEGER) . ", ";
				$sql .= $db->tosql($item_type_id, INTEGER) . ", ";
				$sql .= $db->tosql($item_tax_percent, FLOAT) . ") ";
				$db->query($sql);
			}

			if ($r->get_value("is_default") == 1) {
				$sql  = " UPDATE " . $table_prefix . "tax_rates SET is_default=0 ";
				$sql .= " WHERE is_default=1 ";
				$sql .= " AND (country_id<>" . $db->tosql($r->get_value("country_id"), INTEGER);
				$sql .= " OR state_id<>" . $db->tosql($r->get_value("state_id"), INTEGER) . ") ";
				$db->query($sql);

				$sql  = " UPDATE " . $table_prefix . "tax_rates SET is_default=1 ";
				$sql .= " WHERE is_default=0 ";
				$sql .= " AND country_id=" . $db->tosql($r->get_value("country_id"), INTEGER);
				$sql .= " AND state_id=" . $db->tosql($r->get_value("state_id"), INTEGER);
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	} else if(strlen($r->get_value("tax_id"))) {
		$r->get_db_values();
	} else { 
		// new item (set default values)
	}


	$r->set_form_parameters();
	for ($i = 0; $i < sizeof($tax_rates_items); $i++) {
		list ($item_type_id, $item_type_name, $item_tax_percent) = $tax_rates_items[$i]; 
		$t->set_var("item_type_id", $item_type_id);
		$t->set_var("item_type_name", $item_type_name);
		$t->set_var("item_tax_percent", $item_tax_percent);
		$t->parse("tax_rates_items", true);
	}
	
	if(strlen($tax_id))	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}
	else
	{
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}


	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_tax_rates_href", "admin_tax_rates.php");
	$t->pparse("main");

?>
