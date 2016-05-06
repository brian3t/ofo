<?php

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_order_data_preview.html");

	$referer = get_session("session_referer");
	$user_ip = get_ip();
	$initial_ip = get_session("session_initial_ip");
	$cookie_ip = get_session("session_cookie_ip");
	$visit_number = get_session("session_visit_number");

	$user_id = get_session("session_user_id");
	$user_type_id = get_session("session_user_type_id");

	$order_id = get_session("session_order_id");
	$vc = get_session("session_vc");
	if (!strlen($order_id)) { 
		$order_id =  get_param("order_id"); 
		set_session("session_order_id", $order_id);
	}
	if (!strlen($vc)) { 
		$vc = get_param("vc"); 
		set_session("session_vc", $vc);
	}
	
	$payment_id = ""; $payment_info = "";
	$order_errors = check_order($order_id, $vc);
	if (!$order_errors) {
		$sql  = " SELECT ps.payment_id, ps.payment_info ";
		$sql .= " FROM " . $table_prefix . "orders o, " . $table_prefix . "payment_systems ps ";
		$sql .= " WHERE o.payment_id=ps.payment_id ";
		$sql .= " AND o.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			$payment_info = get_translation($db->f("payment_info"));
			$payment_info = get_currency_message($payment_info, $currency);
		}
	}

	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='order_info'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$setting_type = "credit_card_info_" . $payment_id;
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$cc_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='order_confirmation'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_confirmation[$db->f("setting_name")] = $db->f("setting_value");
	}

	$confirmed_order_status = 3;

	$r = new VA_Record($table_prefix . "orders");
	$r->errors = $order_errors;

	$r->add_where("order_id", INTEGER);
	$r->set_value("order_id", $order_id);

	$r->add_textbox("is_confirmed", INTEGER);
	$r->change_property("is_confirmed", USE_IN_UPDATE, false);
	$r->add_textbox("error_message", TEXT);
	$r->add_textbox("pending_message", TEXT);
	$r->add_textbox("transaction_id", TEXT);
	$r->change_property("transaction_id", USE_IN_UPDATE, false);
	$r->add_textbox("authorization_code", TEXT);
	// AVS fields
	$r->add_textbox("avs_response_code", TEXT);
	$r->add_textbox("avs_message", TEXT);
	$r->add_textbox("avs_address_match", TEXT);
	$r->add_textbox("avs_zip_match", TEXT);
	$r->add_textbox("cvv2_match", TEXT);
	// 3D fields 
	$r->add_textbox("secure_3d_check", TEXT);
	$r->add_textbox("secure_3d_status", TEXT);
	$r->add_textbox("secure_3d_md", TEXT);
	$r->add_textbox("secure_3d_xid", TEXT);

	$action = get_param("action");
	$return_page = "order_final.php";
	$items_text = "";
	
	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("referer", $referer);
	$t->set_var("referrer", $referer);
	$t->set_var("HTTP_REFERER", $referer);
	$t->set_var("initial_ip", $initial_ip);
	$t->set_var("cookie_ip", $cookie_ip);
	$t->set_var("visit_number", $visit_number);

	if (strlen($action))
	{
		if ($r->is_empty("order_id")) {
			$r->errors .= "Missing <b>Order number</b>.<br>";
		}

		if (!strlen($r->errors))
		{
			$is_advanced = false;
			if (strlen($payment_id))
			{
				$db->query("SELECT * FROM " . $table_prefix . "payment_systems WHERE is_active=1 AND payment_id=" . $db->tosql($payment_id, INTEGER));
				if ($db->next_record()) {
					$is_advanced  = $db->f("is_advanced");
					$advanced_url = $db->f("advanced_url");
					$advanced_php_lib = $db->f("advanced_php_lib");
					$success_status_id = $db->f("success_status_id");
					$pending_status_id = $db->f("pending_status_id");
					$failure_status_id = $db->f("failure_status_id");
					$failure_action = $db->f("failure_action");
				}
			}

			$error_message = ""; $pending_message = ""; $transaction_id = "";
			if ($is_advanced && strlen($advanced_php_lib)) 
			{
				// get payment data
				$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
				get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables, "final");
				$payment_params = $payment_parameters;

				// flag to update order status when using foreign library
				$update_order_status = true; 
				// include payment module only if total order value greater than zero
				if ($variables["order_total"] > 0) {
					// use foreign php library to handle transaction
					$order_step = "confirmation";
					if (file_exists($advanced_php_lib)) {
						include_once ($advanced_php_lib);
					} else {
						$error_message = "Can't find appropriative php library: " . $advanced_php_lib;
					}
				}

				// update order data	
				$r->set_value("error_message", $error_message);
				$r->set_value("pending_message", $pending_message);
				if (strlen($transaction_id)) {
					$r->set_value("transaction_id", $transaction_id);
					$r->change_property("transaction_id", USE_IN_UPDATE, true);
				}
				if (!strlen($error_message) && !strlen($pending_message)) {
					$r->set_value("is_confirmed", 1);
					$r->change_property("is_confirmed", USE_IN_UPDATE, true);
				}
				$r->set_value("authorization_code", $variables["authorization_code"]);
				// set AVS data
				$r->set_value("avs_response_code", $variables["avs_response_code"]);
				$r->set_value("avs_message", $variables["avs_message"]);
				$r->set_value("avs_address_match", $variables["avs_address_match"]);
				$r->set_value("avs_zip_match", $variables["avs_zip_match"]);
				$r->set_value("cvv2_match", $variables["cvv2_match"]);
				// set 3D data
				$r->set_value("secure_3d_check", $variables["secure_3d_check"]);
				$r->set_value("secure_3d_status", $variables["secure_3d_status"]);
				$r->set_value("secure_3d_md", $variables["secure_3d_md"]);
				$r->set_value("secure_3d_xid", $variables["secure_3d_xid"]);

				$r->update_record();

				if ($update_order_status) {
					if (strlen($error_message)) {
						$order_status = $failure_status_id;
					} elseif (strlen($pending_message)) {
						$order_status = $pending_status_id; 
					} else {
						$order_status = $success_status_id; 
					}
			  
					// update order status for payment
					update_order_status($order_id, $order_status, true, "", $status_error);
				}
				$secure_3d_acsurl = get_setting_value($variables, "secure_3d_acsurl", "");
				if ($secure_3d_acsurl) {
					$secure_3d_pareq = get_setting_value($variables, "secure_3d_pareq", "");
					$secure_3d_md = get_setting_value($variables, "secure_3d_md", "");
					// prepare template for 3D
					$t->set_file("3d_payment", "payment.html");

					$t->set_var("payment_url", $secure_3d_acsurl);
					$t->set_var("submit_method", "POST");
		  
		  
					$t->set_var("parameter_name", "PaReq");
					$t->set_var("parameter_value", htmlspecialchars($secure_3d_pareq));
					$t->parse("parameters", true);
		  
					$t->set_var("parameter_name", "TermUrl");
					if ($settings["secure_url"]) {
						$t->set_var("parameter_value", $settings["secure_url"] . "order_final.php");
					} else {
						$t->set_var("parameter_value", $settings["site_url"] . "order_final.php");
					}
					$t->parse("parameters", true);
		  
					$t->set_var("parameter_name", "MD");
					$t->set_var("parameter_value", htmlspecialchars($secure_3d_md));
					$t->parse("parameters", true);
		  
					$goto_payment_message = str_replace("{payment_system}", "your bank", GOTO_PAYMENT_MSG);
					$goto_payment_message = str_replace("{button_name}", CONTINUE_BUTTON, $goto_payment_message);
					$t->set_var("GOTO_PAYMENT_MSG", $goto_payment_message);
		  
					$t->parse("submit_payment", false);
					$t->pparse("3d_payment", false);
					return;
				}
			} else {
				// set default order status 
				update_order_status($order_id, $confirmed_order_status, true, "", $status_error);
			}

			if (strlen($error_message) && $failure_action == 1) {
				header("Location: credit_card_info.php?payment_error=1");
				exit;
			} else {
				header("Location: " . $return_page);
				exit;
			}	
		}
	}

	$payment_properties = 0;
	if (!$order_errors) {
		$items_text = show_order_items($order_id, true, "order_confirmation");
	}

	$t->set_var("order_confirmation", "order_confirmation.php");
	$t->set_var("vc", htmlspecialchars($vc));

	$personal_number = 0;
	$delivery_number = 0;
	for ($i = 0; $i < sizeof($parameters); $i++)
	{                                    
		$personal_param = "show_" . $parameters[$i];
		$delivery_param = "show_delivery_" . $parameters[$i];
		$r->add_textbox($parameters[$i], TEXT);
		$r->add_textbox("delivery_" . $parameters[$i], TEXT);
		if (isset($order_info[$personal_param]) && $order_info[$personal_param] == 1) {
			$personal_number++;
		} else {
			$r->parameters[$parameters[$i]][SHOW] = false;
		}
		if (isset($order_info[$delivery_param]) && $order_info[$delivery_param] == 1) {
			$delivery_number++;
		} else {
			$r->parameters["delivery_" . $parameters[$i]][SHOW] = false;
		}
	}

	$r->add_textbox("cc_name", TEXT);
	$r->add_textbox("cc_first_name", TEXT);
	$r->add_textbox("cc_last_name", TEXT);
	$r->add_textbox("cc_number", TEXT);
	$r->add_textbox("cc_start_date", DATETIME);
	$r->change_property("cc_start_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_expiry_date", DATETIME);
	$r->change_property("cc_expiry_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_type", INTEGER);
	$r->add_textbox("cc_issue_number", INTEGER);
	$r->add_textbox("cc_security_code", TEXT);
	$r->add_textbox("pay_without_cc", TEXT);

	if (!$order_errors) {
		$r->get_db_values();
		$r->set_value("cc_number", get_session("session_cc_number"));
		$r->set_value("cc_security_code", get_session("session_cc_code"));
	}

	$cc_number = $r->get_value("cc_number");
	$cc_number = format_cc_number($cc_number, "-", true);
	$r->set_value("cc_number", $cc_number);
	$payment_number = 0;
	for ($i = 0; $i < sizeof($cc_parameters); $i++) { 
		$cc_param_name = $cc_parameters[$i];
		if (!isset($cc_info["show_" . $cc_param_name]) || $cc_info["show_" . $cc_param_name] != 1 || $r->is_empty($cc_param_name)) {
			$r->parameters[$cc_param_name][SHOW] = false;
		} else {
			$payment_number++;
		}
	}

	$r->set_value("company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("company_id"), INTEGER,true,false))));
	$r->set_value("state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER))));
	$r->set_value("country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER))));
	$r->set_value("delivery_company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("delivery_company_id"), INTEGER,true,false))));
	$r->set_value("delivery_state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER))));
	$r->set_value("delivery_country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER))));
	$r->set_value("cc_type", get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($r->get_value("cc_type"), INTEGER)));

	$r->set_parameters();

	if ($personal_number > 0 || $personal_properties) {
		$t->parse("personal", false);
	}

	if ($delivery_number > 0 || $delivery_properties) {
		$t->parse("delivery", false);
	}

	if (trim($payment_info)) {
		$payment_number++;
		$t->set_block("payment_info", $payment_info);
		$t->parse("payment_info", false);
		$t->global_parse("payment_info_block", false, false, true);
	} else {
		$t->set_var("payment_info_block", "");
	}

	if ($payment_number > 0 || $payment_properties) {
		$t->sparse("payment", false);
	}


	$intro_text = trim($order_confirmation["intro_text"]);
	$intro_text = get_translation($intro_text);
	$intro_text = get_currency_message($intro_text, $currency);
	if ($intro_text) {
		$t->set_var("intro_text", $intro_text);
		$t->parse("intro_block", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>