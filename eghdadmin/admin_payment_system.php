<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_payment_system.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("payment_systems");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_payment_system.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", PAYMENT_SYSTEMS_MSG, CONFIRM_DELETE_MSG));

	// set up html form parameters
	$r = new VA_Record($table_prefix . "payment_systems");
	$r->add_where("payment_id", INTEGER);
	$r->change_property("payment_id", USE_IN_INSERT, true);

	$r->add_checkbox("is_active", INTEGER);
	$r->add_checkbox("is_default", INTEGER);
	$r->add_checkbox("is_call_center", INTEGER);
	$r->add_textbox("payment_order", INTEGER, ADMIN_ORDER_MSG);
	$r->change_property("payment_order", REQUIRED, true);
	$r->add_textbox("payment_name", TEXT, PAYMENT_SYSTEM_NAME_MSG);
	$r->change_property("payment_name", REQUIRED, true);
	$r->add_textbox("user_payment_name", TEXT, PAYMENT_NAME_COLUMN);
	$r->add_textbox("processing_time", INTEGER, PROCESSING_TIME_MSG);

	// fast checkout fields
	$r->add_checkbox("fast_checkout_active", INTEGER, FAST_CHECKOUT_ACTIVE_MSG);
	$r->add_textbox("fast_checkout_image", TEXT, FAST_CHECKOUT_IMAGE_MSG);
	$r->add_textbox("fast_checkout_width", INTEGER, FAST_CHECKOUT_WIDTH_MSG);
	$r->add_textbox("fast_checkout_height", INTEGER, FAST_CHECKOUT_HEIGHT_MSG);
	$r->add_textbox("fast_checkout_alt", TEXT, FAST_CHECKOUT_ALT_MSG);

	//image settings
	$r->add_textbox("image_small", TEXT);
	$r->add_textbox("image_small_alt", TEXT);
	$r->add_textbox("image_large", TEXT);
	$r->add_textbox("image_large_alt", TEXT);


	$r->add_textbox("processing_fee", FLOAT, PROCESSING_FEE_MSG);
	$fee_types = array( array(1, PERCENTAGE_PER_ORDER_AMOUNT_MSG), array(2, AMOUNT_PER_ORDER_MSG) );
	$r->add_radio("fee_type", INTEGER, $fee_types, FREE_TYPE_MSG);
	$r->add_textbox("fee_min_goods", NUMBER, MINIMUM_GOODS_COST_MSG);
	$r->add_textbox("fee_max_goods", NUMBER, MAXIMUM_GOODS_COST_MSG);

	$recurring_methods = array(
		array(0, RECURRING_NOT_ALLOWED_MSG), array(1, RECURRING_AUTO_CREATE_MSG), array(2, RECURRING_AUTO_BILL_MSG)
	);
	$r->add_radio("recurring_method", INTEGER, $recurring_methods, RECURRING_METHOD_MSG);

	$r->add_textbox("payment_info", TEXT, PAYMENT_INFO_MSG);
	$r->add_textbox("payment_notes", TEXT, PAYMENT_NOTES_MSG);
	$r->add_textbox("payment_url", TEXT, PAYMENT_URL_MSG);
	$r->change_property("payment_url", REQUIRED, true);
	$methods = array(array("GET", "GET"), array("POST", "POST"));
	$r->add_radio("submit_method", TEXT, $methods, FORM_SUBMIT_METHOD_MSG);

	// advanced parameters
	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array(array("", "")));

	$failure_actions = array(
		array(0, GO_TO_FINAL_STEP_MSG),
		array(1, REDIRECT_BACK_PAYMENT_PAGE_MSG)
	);

	$r->add_checkbox("is_advanced", INTEGER);
	$r->add_textbox("advanced_url", TEXT, ADVANCED_URL_MSG);
	$r->add_textbox("advanced_php_lib", TEXT, ADVANCED_PHP_LIBRARY_MSG);
	$r->add_select("success_status_id", INTEGER, $order_statuses, SUCCESS_STATUS_MSG);
	$r->add_select("pending_status_id", INTEGER, $order_statuses, PENDING_STATUS_MSG);
	$r->add_select("failure_status_id", INTEGER, $order_statuses, FAILURE_STATUS_MSG);
	$r->add_radio("failure_action", INTEGER, $failure_actions, ON_FAILURE_ACTION_MSG);
	$r->add_textbox("capture_php_lib", TEXT);
	$r->add_textbox("refund_php_lib", TEXT);
	$r->add_textbox("void_php_lib", TEXT);
	$is_advanced = get_param("is_advanced");
	if ($is_advanced) {
		$r->change_property("advanced_url", REQUIRED, true);
		$r->change_property("advanced_php_lib", REQUIRED, true);
		$r->change_property("success_status_id", REQUIRED, true);
		$r->change_property("failure_status_id", REQUIRED, true);
	}

	$r->add_checkbox("user_types_all", INTEGER);
	$r->add_checkbox("sites_all", INTEGER);

	$r->get_form_values();

	$rp = new VA_Record($table_prefix . "payment_parameters", "parameters");
	$rp->add_where("parameter_id", INTEGER);
	$rp->add_hidden("payment_id", INTEGER);
	$rp->change_property("payment_id", USE_IN_INSERT, true);
	$rp->add_textbox("parameter_name", TEXT, PARAMETER_NAME_MSG);
	$rp->change_property("parameter_name", REQUIRED, true);

	$parameter_types = array(
		array("", ""),
		array("CONSTANT", ADMIN_CONSTANT_MSG),
		array("VARIABLE", ADMIN_VARIABLE_MSG)
	);


	$rp->add_select("parameter_type", TEXT, $parameter_types, PARAMETER_TYPE_MSG);
	$rp->change_property("parameter_type", REQUIRED, true);

	$rp->add_textbox("parameter_source", TEXT, PARAMETER_SOURCE_MSG);
	$rp->add_checkbox("not_passed", INTEGER, NOT_PASSED_MSG);

	$payment_id = get_param("payment_id");

	$more_parameters = get_param("more_parameters");
	$number_parameters = get_param("number_parameters");

	$eg = new VA_EditGrid($rp, "parameters");
	$eg->get_form_values($number_parameters);

	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	$return_page = "admin_payment_systems.php";

	$selected_user_types = array();
	if (strlen($operation)) {
		$user_types = get_param("user_types");
		if ($user_types) {
			$selected_user_types = split(",", $user_types);
		}
	} elseif ($payment_id) {
		$sql  = "SELECT user_type_id FROM " . $table_prefix . "payment_user_types ";
		$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$selected_user_types[] = $db->f("user_type_id");
		}
	}

	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($payment_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "payment_systems_sites ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}

	if (strlen($operation) && !$more_parameters)
	{
		$tab = "general";
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $payment_id)
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql("credit_card_info_" . $payment_id, TEXT);
			$sql .= " OR setting_type=" . $db->tosql("order_final_" . $payment_id, TEXT);
			$sql .= " OR setting_type=" . $db->tosql("recurring_" . $payment_id, TEXT);
			$db->query($sql);
			$db->query("DELETE FROM " . $table_prefix . "payment_user_types WHERE payment_id=" . $db->tosql($payment_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "payment_systems_sites WHERE payment_id=" . $db->tosql($payment_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "payment_parameters WHERE payment_id=" . $db->tosql($payment_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER));

			header("Location: " . $return_page);
			exit;
		}
		if ($r->get_value("processing_fee")) {
			$r->change_property("fee_type", REQUIRED, true);
		}
		$is_valid = $r->validate();
		$is_valid = ($eg->validate() && $is_valid);

		if ($is_valid)
		{
			if (!$sitelist) {
				$r->set_value("sites_all", 1);
			}
			if (strlen($payment_id))
			{
				$r->update_record();
				$eg->set_values("payment_id", $payment_id);
				$eg->update_all($number_parameters);
			}
			else
			{
				$db->query("SELECT MAX(payment_id) FROM " . $table_prefix . "payment_systems");
				$db->next_record();
				$payment_id = $db->f(0) + 1;
				$r->set_value("payment_id", $payment_id);
				$r->insert_record();
				$eg->set_values("payment_id", $payment_id);
				$eg->insert_all($number_parameters);
				// redirect to payment details page settings
				$return_page = "admin_credit_card_info.php?payment_id=" . urlencode($payment_id);
			}
			if ($r->get_value("is_default") == 1) {
				$sql = "UPDATE " . $table_prefix . "payment_systems SET is_default=0 WHERE payment_id<>" . $db->tosql($payment_id, INTEGER);
				$db->query($sql);
			}
			if ($r->get_value("is_advanced") == 1 && $r->get_value("is_call_center") == 1) {
				//different call center payment systems for different sites

				if ($multisites_version) {
					$sql  = " SELECT p.payment_id FROM " . $table_prefix . "payment_systems AS p ";
					$sql .= " LEFT JOIN " . $table_prefix . "payment_systems_sites AS s ON p.payment_id=s.payment_id";	
					$sql .= " WHERE p.payment_id<>".$db->tosql($payment_id, INTEGER)." AND p.is_call_center=1 ";					
					$sql_parts = array();	
					if ($r->get_value("sites_all")) {
						$sql_parts[] = " p.sites_all=1 ";	
					}
					if ($sitelist) {
						if (is_array($selected_sites)) {						
							for ($st = 0; $st < sizeof($selected_sites); $st++) {
								$sql_parts[] = " s.site_id=" . $db->tosql($selected_sites[$st], INTEGER, true, false);							
							}
						}
					}
					if ($sql_parts) {
						$sql .= " AND ( " . implode(" OR ",$sql_parts) . ") ";
					}								
					$db->query($sql);	
									
					$sql_parts = array();					
					if ($db->next_record())
					{
						do {
							$sql_parts[] = "payment_id=" . $db->tosql($db->f(0), INTEGER, true, false);
						} while ($db->next_record());			
					}
					if ($r->get_value("sites_all")) {
						$sql_parts[] = " sites_all=1 ";	
					}
					if ($sql_parts) {
						$sql  = " UPDATE " . $table_prefix . "payment_systems SET is_call_center=0 ";
						$sql .= " WHERE ( " . implode(" OR ",$sql_parts) . " ) ";
						$sql .= " AND payment_id<>" . $db->tosql($payment_id, INTEGER);	
						$db->query($sql);
					}		
				} else {
					$sql  = " UPDATE " . $table_prefix . "payment_systems SET is_call_center=0 ";
					$sql .= " WHERE payment_id<>".$db->tosql($payment_id, INTEGER);	
					$db->query($sql);
				}				
			}
			// update users types
			$db->query("DELETE FROM " . $table_prefix . "payment_user_types WHERE payment_id=" . $db->tosql($payment_id, INTEGER));
			for ($ut = 0; $ut < sizeof($selected_user_types); $ut++) {
				$type_id = $selected_user_types[$ut];
				if (strlen($type_id)) {
					$sql  = " INSERT INTO " . $table_prefix . "payment_user_types (payment_id, user_type_id) VALUES (";
					$sql .= $db->tosql($payment_id, INTEGER) . ", ";
					$sql .= $db->tosql($type_id, INTEGER) . ") ";
					$db->query($sql);
				}
			}
			// update sites
			if ($sitelist) {
				$db->query("DELETE FROM " . $table_prefix . "payment_systems_sites WHERE payment_id=" . $db->tosql($payment_id, INTEGER));
				for ($st = 0; $st < sizeof($selected_sites); $st++) {
					$site_id = $selected_sites[$st];
					if (strlen($site_id)) {
						$sql  = " INSERT INTO " . $table_prefix . "payment_systems_sites (payment_id, site_id) VALUES (";
						$sql .= $db->tosql($payment_id, INTEGER) . ", ";
						$sql .= $db->tosql($site_id, INTEGER) . ") ";
						$db->query($sql);
					}
				}
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	elseif (strlen($payment_id) && !$more_parameters)
	{
		$r->get_db_values();
		$eg->set_value("payment_id", $payment_id);
		$eg->change_property("parameter_id", USE_IN_SELECT, true);
		$eg->change_property("parameter_id", USE_IN_WHERE, false);
		$eg->change_property("payment_id", USE_IN_WHERE, true);
		$eg->change_property("payment_id", USE_IN_SELECT, true);
		$number_parameters = $eg->get_db_values();
		if ($number_parameters == 0)
			$number_parameters = 5;
	}
	elseif ($more_parameters)
	{
		$number_parameters += 5;
	}
	else
	{
		$sql = " SELECT MAX(payment_order) FROM " . $table_prefix . "payment_systems ";		
		$payment_order = get_db_value($sql);
		$r->set_value("payment_order", $payment_order + 1);
		$r->set_value("submit_method", "GET");
		$r->set_value("user_types_all", 1);
		$r->set_value("sites_all", 1);
		$number_parameters = 5;
	}

	$t->set_var("number_parameters", $number_parameters);

	$eg->set_parameters_all($number_parameters);
	$r->set_parameters();

	$user_types = array();
	$sql = " SELECT type_id, type_name FROM " . $table_prefix . "user_types ";
	$db->query($sql);
	while ($db->next_record())	{
		$type_id = $db->f("type_id");
		$type_name = get_translation($db->f("type_name"));
		$user_types[$type_id] = $type_name;
	}

	foreach($user_types as $type_id => $type_name) {
		$t->set_var("type_id", $type_id);
		$t->set_var("type_name", $type_name);
		if (in_array($type_id, $selected_user_types)) {
			$t->parse("selected_user_types", true);
		} else {
			$t->parse("available_user_types", true);
		}
	}

	if ($sitelist) {
		$sites = array();
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
		$db->query($sql);
		while ($db->next_record())	{
			$site_id   = $db->f("site_id");
			$site_name = get_translation($db->f("site_name"));
			$sites[$site_id] = $site_name;
			$t->set_var("site_id", $site_id);
			$t->set_var("site_name", $site_name);
			if (in_array($site_id, $selected_sites)) {
				$t->parse("selected_sites", true);
			} else {
				$t->parse("available_sites", true);
			}
		}
	}

	if (strlen($payment_id))	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);
	} else {
		$t->set_var("save_button", ADD_BUTTON);
		$t->set_var("delete", "");
	}

/*
	$tabs = array("general" => ADMIN_GENERAL_MSG, "fast_checkout" => FAST_CHECKOUT_MSG, "user_types" => USERS_TYPES_MSG);
	if ($sitelist) {
		$tabs["sites"] = ADMIN_SITES_MSG;
	}
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
	$t->set_var("tab", $tab);//*/
	
	// set styles for tabs
	$tabs = array(
		"general" => array("title" => ADMIN_GENERAL_MSG), 
		"images" => array("title" => IMAGES_MSG), 
		"fee" => array("title" => FEE_SETTINGS_MSG), 
		"fast_checkout" => array("title" => FAST_CHECKOUT_MSG), 
		"user_types" => array("title" => USERS_TYPES_MSG), 
		"sites" => array("title" => ADMIN_SITES_MSG, "show" => $sitelist),
	);

	parse_admin_tabs($tabs, $tab, 7);

	if ($sitelist) {
		$t->parse("sitelist");
	}

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_payment_systems_href", "admin_payment_systems.php");
	$t->set_var("admin_payment_system_href",  "admin_payment_system.php");
	$t->set_var("admin_payment_help_href",    "admin_payment_help.php");
	$t->set_var("admin_order_final_href",     "admin_order_final.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");

	$t->pparse("main");

?>