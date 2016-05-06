<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_bom_column.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");

	check_admin_security("sales_orders");

	// get current bom_column settings
	$bom_columns = array();
	$sql  = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= "WHERE setting_type='bom_column' ";
	if ($multisites_version) {
		$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
		$sql .= "ORDER BY site_id DESC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$bom_columns[$db->f("setting_name")] = $db->f("setting_value");
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_orders_bom_column.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_orders_bom_column_href", "admin_orders_bom_column.php");
	$t->set_var("admin_orders_bom_settings_href", "admin_orders_bom_settings.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", OPTION_MSG, CONFIRM_DELETE_MSG));	
	$types = 
		array(			
			array("", ""),
			array("1", PRODUCT_OPTION_MSG),
			array("2", PROD_SPECIFICATION_MSG),
		);

	$show_options = 
		array(			
			array("1", YES_MSG),
			array("0", NO_MSG)
		);

	// set up html form parameters
	$r = new VA_Record($table_prefix . "global_settings");
	$r->add_where("column_id", INTEGER);
	$r->add_textbox("column_order", INTEGER, COLUMN_ORDER_MSG);
	$r->change_property("column_order", REQUIRED, true);
	$r->add_textbox("column_name", TEXT, COLUMN_NAME_MSG);
	$r->change_property("column_name", REQUIRED, true);
	$r->add_select("source_type", INTEGER, $types, SOURCE_TYPE_MSG);
	$r->change_property("source_type", REQUIRED, true);
	$r->add_textbox("source_name", TEXT, SOURCE_NAME_MSG);
	$r->change_property("source_name", REQUIRED, true);
	$r->change_property("source_name", TRIM, true);
	$r->add_radio("column_show", INTEGER, $show_options);
	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ",null);
		$r->add_select("site_id", INTEGER, $sites, ADMIN_SITE_MSG);
		$r->change_property("site_id", REQUIRED, true);
	} else {
		$r->add_textbox("site_id", INTEGER);
	}

	$r->get_form_values();
	if (!$sitelist) {
		$r->set_value("site_id", 1);
	}
	
	$column_id = get_param("column_id");
	$operation = get_param("operation");
	$return_page = "admin_orders_bom_settings.php?tab=custom_columns";

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $column_id)
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='bom_column' ";
			$sql .= " AND (setting_name=" . $db->tosql("column_id_" . $column_id, TEXT);
			$sql .= " OR setting_name=" . $db->tosql("column_show_" . $column_id, TEXT);
			$sql .= " OR setting_name=" . $db->tosql("column_order_" . $column_id, TEXT);
			$sql .= " OR setting_name=" . $db->tosql("column_name_" . $column_id, TEXT);
			$sql .= " OR setting_name=" . $db->tosql("source_type_" . $column_id, TEXT);
			$sql .= " OR setting_name=" . $db->tosql("source_name_" . $column_id, TEXT);
			$sql .= " OR setting_name=" . $db->tosql("site_id_" . $column_id, TEXT) . ") ";
			$db->query($sql);

			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();

		if ($is_valid)
		{
			// delete current settings and add new updated
			if (strlen($column_id)) {
				$sql  = " DELETE FROM " . $table_prefix . "global_settings ";
				$sql .= " WHERE setting_type='bom_column' ";
				$sql .= " AND (setting_name=" . $db->tosql("column_id_" . $column_id, TEXT);
				$sql .= " OR setting_name=" . $db->tosql("column_show_" . $column_id, TEXT);
				$sql .= " OR setting_name=" . $db->tosql("column_order_" . $column_id, TEXT);
				$sql .= " OR setting_name=" . $db->tosql("column_name_" . $column_id, TEXT);
				$sql .= " OR setting_name=" . $db->tosql("source_type_" . $column_id, TEXT);
				$sql .= " OR setting_name=" . $db->tosql("source_name_" . $column_id, TEXT);
				$sql .= " OR setting_name=" . $db->tosql("site_id_" . $column_id, TEXT) . ") ";
				$db->query($sql);
			} else {
				// check for max column_id
				$column_id = 1;
				foreach ($bom_columns as $name => $value) {
					if (preg_match("/^column_id_(\d+)$/", $name, $matches)) {
						$bom_column_id = $matches[1];
						if ($bom_column_id >= $column_id) {
							$column_id = $bom_column_id + 1;
						}
					}
				}
			}
			$r->set_value("column_id", $column_id);

			// insert new data
			$column_site_id = $r->get_value("site_id");
			foreach ($r->parameters as $key => $value) {
				if ($multisites_version) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= "'bom_column', '" . $key."_".$column_id . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
					$sql .= $db->tosql($column_site_id, INTEGER) . ") ";
				} else {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= "'bom_column', '" . $key."_".$column_id . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ", 1)";					
				}
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	} elseif (strlen($column_id)) {
		foreach ($r->parameters as $key => $value) {
			$r->set_value($key, get_setting_value($bom_columns, $key."_".$column_id));
		}
	} else { // set default values
		// check for max column_id
		$column_order = 1;
		foreach ($bom_columns as $name => $value) {
			if (preg_match("/^column_order_(\d+)$/", $name, $matches)) {
				$bom_column_order = get_setting_value($bom_columns, $matches[0]);
				if ($bom_column_order >= $column_order) {
					$column_order = $bom_column_order + 1;
				}
			}
		}
		$r->set_value("column_order", $column_order);
	}

	$r->set_parameters();

	if (strlen($r->get_value("column_id")))	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}
	else
	{
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}

	if ($sitelist) {
		$t->parse("sitelist");
	}
	
	$t->pparse("main");

?>