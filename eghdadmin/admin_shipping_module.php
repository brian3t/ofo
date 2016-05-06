<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_shipping_module.php                                ***
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

	check_admin_security("shipping_methods");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_shipping_module.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_shipping_modules_href", "admin_shipping_modules.php");
	$t->set_var("admin_shipping_module_href",  "admin_shipping_module.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SHIPPING_MODULE_MSG, CONFIRM_DELETE_MSG));

	// set up html form parameters
	$r = new VA_Record($table_prefix . "shipping_modules");
	$r->add_where("shipping_module_id", INTEGER);
	$r->change_property("shipping_module_id", USE_IN_INSERT, true);
	$r->add_checkbox("is_active", INTEGER);
	$r->add_textbox("shipping_module_name", TEXT, SHIPPING_MODULE_NAME_MSG);
	$r->change_property("shipping_module_name", REQUIRED, true);
	$r->add_textbox("module_notes", TEXT, MODULE_NOTES_MSG);
	$r->add_textbox("cost_add_percent", FLOAT, COST_ADD_PERCENT_MSG);
	$r->add_checkbox("is_external", TEXT);
	$r->add_textbox("php_external_lib", TEXT, PHP_EXTERNAL_LIBRARY_MSG);
	$r->add_textbox("external_url", TEXT, EXTERNAL_URL_MSG);
	$r->add_textbox("tracking_url", TEXT, TRACKING_URL_MSG);
	$is_external = get_param("is_external");
	if ($is_external) {
		$r->change_property("php_external_lib", REQUIRED, true);
	}
	$r->get_form_values();

	$rp = new VA_Record($table_prefix . "shipping_modules_parameters", "parameters");
	$rp->add_where("parameter_id", INTEGER);
	$rp->change_property("parameter_id", USE_IN_ORDER, ORDER_ASC);
	$rp->add_hidden("shipping_module_id", INTEGER);
	$rp->change_property("shipping_module_id", USE_IN_INSERT, true);
	$rp->add_textbox("parameter_name", TEXT, PARAMETER_NAME_MSG);
	$rp->change_property("parameter_name", REQUIRED, true);
	$rp->add_textbox("parameter_source", TEXT, PARAMETER_SOURCE_MSG);
	$rp->add_checkbox("not_passed", INTEGER, NOT_PASSED_MSG);

	$shipping_module_id = get_param("shipping_module_id");

	$more_parameters = get_param("more_parameters");
	$number_parameters = get_param("number_parameters");

	$eg = new VA_EditGrid($rp, "parameters");
	$eg->get_form_values($number_parameters);

	$operation = get_param("operation");

	$return_page = "admin_shipping_modules.php";

	if (strlen($operation) && !$more_parameters)
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $shipping_module_id)
		{
			$shipping_type_ids = "";
			$sql = "SELECT shipping_type_id FROM " . $table_prefix . "shipping_types WHERE shipping_module_id = " . $db->tosql($shipping_module_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				if (strlen($shipping_type_ids)) { $shipping_type_ids .= ","; }
				$shipping_type_ids .= $db->f("shipping_type_id");
			}
			$db->query("DELETE FROM " . $table_prefix . "shipping_types_countries WHERE shipping_type_id IN (" . $db->tosql($shipping_type_ids, TEXT, false) . ")");
			$db->query("DELETE FROM " . $table_prefix . "shipping_types WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "shipping_modules_parameters WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "shipping_modules WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER));
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();
		$is_valid = ($eg->validate() && $is_valid);

		if ($is_valid)
		{
			if (strlen($shipping_module_id))
			{
				$r->update_record();
				$eg->set_values("shipping_module_id", $shipping_module_id);
				$eg->update_all($number_parameters);
			}
			else
			{
				$db->query("SELECT MAX(shipping_module_id) FROM " . $table_prefix . "shipping_modules");
				$db->next_record();
				$shipping_module_id = $db->f(0) + 1;
				$r->set_value("shipping_module_id", $shipping_module_id);
				$r->insert_record();
				$eg->set_values("shipping_module_id", $shipping_module_id);
				$eg->insert_all($number_parameters);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	elseif (strlen($shipping_module_id) && !$more_parameters)
	{
		$r->get_db_values();
		$eg->set_value("shipping_module_id", $shipping_module_id);
		$eg->change_property("parameter_id", USE_IN_SELECT, true);
		$eg->change_property("parameter_id", USE_IN_WHERE, false);
		$eg->change_property("shipping_module_id", USE_IN_WHERE, true);
		$eg->change_property("shipping_module_id", USE_IN_SELECT, true);
		$number_parameters = $eg->get_db_values();
		if ($number_parameters == 0) {
			$number_parameters = 5;
		}
	}
	elseif ($more_parameters)
	{
		$number_parameters += 5;
	}
	else
	{
		$number_parameters = 5;
	}

	$t->set_var("number_parameters", $number_parameters);
	$eg->set_parameters_all($number_parameters);
	$r->set_parameters();

	if (strlen($shipping_module_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);
	} else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");
	}

	$t->pparse("main");

?>