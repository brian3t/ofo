<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_bom_settings.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ("../messages/".$language_code."/cart_messages.php");

	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$param_site_id = get_session("session_site_id");

	// get current bom settings
	$bom = array();
	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE (setting_type='bom' OR setting_type='bom_column') ";
	$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
	$sql .= " ORDER BY site_id ASC ";
	$db->query($sql);
	while ($db->next_record()) {
		$bom[$db->f("setting_name")] = $db->f("setting_value");
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_orders_bom_settings.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_orders_bom_settings_href", "admin_orders_bom_settings.php");
	$t->set_var("admin_orders_bom_column_href", "admin_orders_bom_column.php");

	$t->set_var("admin_href", "admin.php");

	$r = new VA_Record($table_prefix . "global_settings");

	// set columns parameters
	$r->add_checkbox("show_item_name", INTEGER);
	$r->add_checkbox("show_manufacturer_code", INTEGER);
	$r->add_checkbox("show_item_code", INTEGER);
	$r->add_checkbox("show_selling_price", INTEGER);
	$r->add_checkbox("show_buying_price", INTEGER);
	$r->add_checkbox("show_quantity", INTEGER);
	$r->add_checkbox("show_selling_total", INTEGER);
	$r->add_checkbox("show_buying_total", INTEGER);

	$r->add_textbox("item_name_order", INTEGER);
	$r->add_textbox("manufacturer_code_order", INTEGER);
	$r->add_textbox("item_code_order", INTEGER);
	$r->add_textbox("selling_price_order", INTEGER);
	$r->add_textbox("buying_price_order", INTEGER);
	$r->add_textbox("quantity_order", INTEGER);
	$r->add_textbox("selling_total_order", INTEGER);
	$r->add_textbox("buying_total_order", INTEGER);

	$r->get_form_values();

	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "predefined_columns"; }	
	$return_page = get_param("rp");
	if (!strlen($return_page)) $return_page = "admin.php";
	$errors = "";

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$r->validate();

		if (!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='bom' ";
			$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= "'bom', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get bom settings
	{
		foreach ($r->parameters as $key => $value) {
			$r->set_value($key, get_setting_value($bom, $key, ""));
		}
	}

	$source_types = array(
		"1" => PRODUCT_OPTION_MSG,
		"2" => PROD_SPECIFICATION_MSG,
	);

	$columns_number = 0;
	foreach ($bom as $name => $value) {
		if (preg_match("/^column_id_(\d+)$/", $name, $matches)) {
			$columns_number++;
			$column_id = $matches[1];
			$column_name = get_translation(get_setting_value($bom, "column_name_".$column_id));
			$column_show = get_setting_value($bom, "column_show_".$column_id);
			$column_order = get_setting_value($bom, "column_order_".$column_id);
			$source_type = get_setting_value($bom, "source_type_".$column_id);
			$source_name = get_setting_value($bom, "source_name_".$column_id);
			if ($column_show == 1) {
				$column_show = YES_MSG;
			} else {
				$column_show = NO_MSG;
			}

			$t->set_var("column_id",   $column_id);
			$t->set_var("column_name", $column_name);
			$t->set_var("column_show", $column_show);
			$t->set_var("column_order", $column_order);
			$t->set_var("source_type", get_setting_value($source_types, $source_type));

			$t->parse("columns", true);
		}
	}
	if ($columns_number) {
		$t->parse("name_columns", false);
	} else {
		$t->parse("no_columns", false);
	}

	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	$tabs = array("predefined_columns" => PREDEFINED_COLUMNS_MSG, "custom_columns" => CUSTOM_COLUMNS_MSG);
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

	$t->set_var("admin_href", "admin.php");
	
	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	$t->pparse("main");

?>