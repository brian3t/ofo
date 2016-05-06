<?php

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_order_payment_form.html");

	$action   = get_param("action");
	$order_id = get_param("order_id");
	$vc = get_param("vc");
	$payment_error = get_param("payment_error");
	if (!strlen($order_id)) { $order_id = get_session("session_order_id"); }
	if (!strlen($vc)) { $vc = get_session("session_vc"); }

	$eol = get_eol();
	$referer = get_session("session_referer");
	$initial_ip = get_session("session_initial_ip");
	$cookie_ip = get_session("session_cookie_ip");
	$visit_number = get_session("session_visit_number");

	$order_errors = check_order($order_id, $vc);
	$payment_id = ""; $payment_info = ""; $error_message = "";
	if (!strlen($order_errors)) {
		$sql  = " SELECT ps.payment_id, ps.payment_info, o.error_message ";
		$sql .= " FROM " . $table_prefix . "orders o, " . $table_prefix . "payment_systems ps ";
		$sql .= " WHERE o.payment_id=ps.payment_id ";
		$sql .= " AND o.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			$payment_info = get_translation($db->f("payment_info"));
			$payment_info = get_currency_message($payment_info, $currency);
			$error_message = $db->f("error_message");
		}
	}

	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("referer", $referer);
	$t->set_var("referrer", $referer);
	$t->set_var("HTTP_REFERER", $referer);
	$t->set_var("initial_ip", $initial_ip);
	$t->set_var("cookie_ip", $cookie_ip);
	$t->set_var("visit_number", $visit_number);

	$t->set_var("credit_card_info_href", "credit_card_info.php");
	$t->set_var("cc_security_code_help_href", "cc_security_code_help.php");

	$t->set_var("vc", htmlspecialchars($vc));

	$cc_info = array();
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
	$cc_number_security = get_setting_value($cc_info, "cc_number_security", 0);
	$cc_code_security = get_setting_value($cc_info, "cc_code_security", 0);

	$r = new VA_Record($table_prefix . "orders");
	$r->errors = $order_errors;
	if ($payment_error == 1) {
		$r->errors .= $error_message;
	}

	$r->add_where("order_id", INTEGER);

	$r->add_textbox("cc_name", TEXT, CC_NAME_FIELD);
	$r->add_textbox("cc_first_name", TEXT, CC_FIRST_NAME_FIELD);
	$r->add_textbox("cc_last_name", TEXT, CC_LAST_NAME_FIELD);
	$r->add_textbox("cc_number", TEXT, CC_NUMBER_FIELD);
	$r->parameters["cc_number"][MIN_LENGTH] = 10;
	$r->add_textbox("cc_start_date", DATETIME, CC_START_DATE_FIELD);
	$r->change_property("cc_start_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_expiry_date", DATETIME, CC_EXPIRY_DATE_FIELD);
	$r->change_property("cc_expiry_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$credit_cards = get_db_values("SELECT credit_card_id, credit_card_name FROM " . $table_prefix . "credit_cards", array(array("", PLEASE_CHOOSE_MSG)));
	$r->add_select("cc_type", INTEGER, $credit_cards, CC_TYPE_FIELD);
	$issue_numbers = get_db_values("SELECT issue_number AS issue_value, issue_number AS issue_description FROM " . $table_prefix . "issue_numbers", array(array("", NOT_AVAILABLE_MSG)));
	$r->add_select("cc_issue_number", INTEGER, $issue_numbers, CC_ISSUE_NUMBER_FIELD);
	$r->add_textbox("cc_security_code", TEXT, CC_SECURITY_CODE_FIELD);
	$r->add_textbox("pay_without_cc", TEXT, PAY_WITHOUT_CC_FIELD);

	// 3D fields 
	$r->add_textbox("secure_3d_check", TEXT);
	$r->add_textbox("secure_3d_status", TEXT);
	$r->add_textbox("secure_3d_md", TEXT);
	$r->add_textbox("secure_3d_xid", TEXT);
	$r->add_textbox("secure_3d_eci", TEXT);
	$r->add_textbox("secure_3d_cavv", TEXT);

	$parameters_number = 0;
	for ($i = 0; $i < sizeof($cc_parameters); $i++)
	{            
		$show_param = "show_" . $cc_parameters[$i];
		if (isset($cc_info[$show_param]) && $cc_info[$show_param] == 1) {
			$parameters_number++;
			if ($cc_info[$cc_parameters[$i] . "_required"] == 1) {
				$r->parameters[$cc_parameters[$i]][REQUIRED] = true;
			}
		} else {
			$r->parameters[$cc_parameters[$i]][SHOW] = false;
		}
	}

	$r->get_form_values();
	$r->set_value("order_id", $order_id);

	// prepare custom options 
	$options_errors = "";
	$custom_options = array();
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "order_custom_properties ";
	$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$sql .= " AND property_type = 4 AND property_show IN (0,1) "; // show not hidden properties for all orders and web orders
	if (isset($site_id)) {
		$sql .= " AND site_id=" . $db->tosql($site_id, INTEGER, true, false);
	} else {
		$sql .= " AND site_id=1";
	}
	$sql .= " ORDER BY property_order, property_id ";
	$db->query($sql);
	if ($db->next_record()) {
		$order_properties = ""; $op_rows = array(); $rn = 0;
		do {
			$parameters_number++;
			$op_rows[$rn]["property_id"] = $db->f("property_id");
			$op_rows[$rn]["property_order"] = $db->f("property_order");
			$op_rows[$rn]["property_name"] = $db->f("property_name");
			$op_rows[$rn]["property_description"] = $db->f("property_description");
			$op_rows[$rn]["default_value"] = $db->f("default_value");
			//$op_rows[$rn]["property_type"] = $db->f("property_type");
			$op_rows[$rn]["property_style"] = $db->f("property_style");
			$op_rows[$rn]["control_type"] = $db->f("control_type");
			$op_rows[$rn]["control_style"] = $db->f("control_style");
			$op_rows[$rn]["required"] = $db->f("required");
			//$op_rows[$rn]["tax_free"] = $db->f("tax_free");
			$op_rows[$rn]["before_name_html"] = $db->f("before_name_html");
			$op_rows[$rn]["after_name_html"] = $db->f("after_name_html");
			$op_rows[$rn]["before_control_html"] = $db->f("before_control_html");
			$op_rows[$rn]["after_control_html"] = $db->f("after_control_html");
			$op_rows[$rn]["onchange_code"] = $db->f("onchange_code");
			$op_rows[$rn]["onclick_code"] = $db->f("onclick_code");
			$op_rows[$rn]["control_code"] = $db->f("control_code");
			$op_rows[$rn]["validation_regexp"] = $db->f("validation_regexp");
			$op_rows[$rn]["regexp_error"] = ($db->f("regexp_error")) ? get_translation($db->f("regexp_error")) : INCORRECT_VALUE_MESSAGE;

			$rn++;
		} while ($db->next_record());

		for ($rn = 0; $rn < sizeof($op_rows); $rn++) {
			$property_id = $op_rows[$rn]["property_id"];
			$property_order  = $op_rows[$rn]["property_order"];
			$property_name_initial = $op_rows[$rn]["property_name"];
			$property_name = get_translation($property_name_initial);
			$property_description = $op_rows[$rn]["property_description"];
			$default_value = $op_rows[$rn]["default_value"];
			//$property_type = $op_rows[$rn]["property_type"];
			$property_style = $op_rows[$rn]["property_style"];
			$control_type = $op_rows[$rn]["control_type"];
			$control_style = $op_rows[$rn]["control_style"];
			$property_required = $op_rows[$rn]["required"];
			//$property_tax_free = $op_rows[$rn]["tax_free"];
			$before_name_html = $op_rows[$rn]["before_name_html"];
			$after_name_html = $op_rows[$rn]["after_name_html"];
			$before_control_html = $op_rows[$rn]["before_control_html"];
			$after_control_html = $op_rows[$rn]["after_control_html"];
			$onchange_code = $op_rows[$rn]["onchange_code"];
			$onclick_code = $op_rows[$rn]["onclick_code"];
			$control_code = $op_rows[$rn]["control_code"];
			$validation_regexp = $op_rows[$rn]["validation_regexp"];
			$regexp_error = $op_rows[$rn]["regexp_error"];

			$property_control  = "";
			$property_control .= "<input type=\"hidden\" name=\"op_name_" . $property_id . "\"";
			$property_control .= " value=\"" . strip_tags($property_name) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_required_" . $property_id . "\"";
			$property_control .= " value=\"" . intval($property_required) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_control_" . $property_id . "\"";
			$property_control .= " value=\"" . strtoupper($control_type) . "\">";
			

			$sql  = " SELECT * FROM " . $table_prefix . "order_custom_values ";
			$sql .= " WHERE property_id=" . $property_id . " AND hide_value=0";
			$sql .= " ORDER BY property_value_id ";
			if (strtoupper($control_type) == "LISTBOX") {
				$selected_value = "";
				$selected_value_id = get_param("op_" . $property_id);
				$properties_values = "<option value=\"\">" . SELECT_MSG . " " . $property_name . "</option>" . $eol;
				$db->query($sql);
				while ($db->next_record())
				{
					$property_value_original = $db->f("property_value");
					$property_value = get_translation($property_value_original);
					$property_value_id = $db->f("property_value_id");
					$is_default_value = $db->f("is_default_value");
					$property_selected  = "";
					if (strlen($action)) {
						if ($selected_value_id == $property_value_id) {
							$property_selected  = "selected ";
							$selected_value = $property_value;
							$custom_options[$property_id][] = array(
								"order" => $property_order, "name" => $property_name_initial, 
								"value_id" => $property_value_id, "value" => $property_value_original,
							);
						}
					} elseif ($is_default_value) {
						$property_selected  = "selected ";
					} 

					$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($property_value_id) . "\">";
					$properties_values .= htmlspecialchars($property_value);
					$properties_values .= "</option>" . $eol;
				}
				$property_control .= $before_control_html;
				$property_control .= "<select name=\"op_" . $property_id . "\"";
				if ($onchange_code) {	$property_control .= $onchange_code; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				$property_control .= ">" . $properties_values . "</select>";
				$property_control .= $after_control_html;
			} elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") {
				$is_radio = (strtoupper($control_type) == "RADIOBUTTON");

				$selected_ids = array();
				if (strlen($action)) {
					if ($is_radio) {
						$selected_ids[] = get_param("op_" . $property_id);
					} else {
						$total_options = get_param("op_total_" . $property_id);
						for ($op = 1; $op <= $total_options; $op++) {
							$selected_ids[] = get_param("op_" . $property_id . "_" . $op);
						}
					}
				}

				$selected_values = "";
				$input_type = $is_radio ? "radio" : "checkbox";
				$property_control .= "<span";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				$property_control .= ">";
				$value_number = 0;
				$db->query($sql);
				while ($db->next_record())
				{
					$value_number++;
					$property_value_id = $db->f("property_value_id");
					$manufacturer_code = $db->f("manufacturer_code");
					$is_default_value = $db->f("is_default_value");
					$property_value_original = $db->f("property_value");
					$property_value = get_translation($property_value_original);
					$property_checked = "";
					$property_control .= $before_control_html;
					if (strlen($action)) {
						if (in_array($property_value_id, $selected_ids)) {
							$property_checked = "checked ";
							if (strlen($selected_values)) { $selected_values .= "; "; }
							$selected_values .= $property_value;
							$custom_options[$property_id][] = array(
								"order" => $property_order, "name" => $property_name_initial, 
								"value_id" => $property_value_id, "value" => $property_value_original,
							);
						}
					} elseif ($is_default_value) {
						$property_checked = "checked ";
					} 

					$control_name = ($is_radio) ? ("op_".$property_id) : ("op_".$property_id."_".$value_number);
					$property_control .= "<input type=\"" . $input_type . "\" name=\"" . $control_name . "\" ". $property_checked;
					$property_control .= "value=\"" . htmlspecialchars($property_value_id) . "\"";
					if ($onclick_code) {	
						$control_onclick_code = str_replace("{option_value}", $property_value, $control_onclick_code);
						$property_control .= " onClick=\"" . $control_onclick_code . "\"";
					}
					if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
					if ($control_code) {	$property_control .= " " . $control_code . " "; }
					$property_control .= ">";
					$property_control .= $property_value;
					$property_control .= $after_control_html;
				}
				$property_control .= "</span>";
				$property_control .= "<input type=\"hidden\" name=\"op_total_".$property_id."\" value=\"".$value_number."\">";
			} elseif (strtoupper($control_type) == "TEXTBOX") {
				if (strlen($action)) {
					$control_value = get_param("op_" . $property_id);
				} else {
					$control_value = $default_value;
				}
				$property_control .= $before_control_html;
				$property_control .= "<input type=\"text\" name=\"op_" . $property_id . "\"";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= " value=\"". htmlspecialchars($control_value) . "\">";
				$property_control .= $after_control_html;
				if (strlen($control_value)) {
					$custom_options[$property_id][] = array(
						"order" => $property_order, "name" => $property_name_initial, 
						"value_id" => "", "value" => $control_value,
					);
				}
			} elseif (strtoupper($control_type) == "TEXTAREA") {
				if (strlen($action)) {
					$control_value = get_param("op_" . $property_id);
				} else {
					$control_value = $default_value;
				}
				$property_control .= $before_control_html;
				$property_control .= "<textarea name=\"op_" . $property_id . "\"";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= ">". htmlspecialchars($control_value) ."</textarea>";
				$property_control .= $after_control_html;
				if (strlen($control_value)) {
					$custom_options[$property_id][] = array(
						"order" => $property_order, "name" => $property_name_initial, 
						"value_id" => "", "value" => $control_value,
					);
				}
			} else {
				$property_control .= $before_control_html;
				if ($property_required) {
					$property_control .= "<input type=\"hidden\" name=\"op_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
				}
				$property_control .= "<span";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= ">" . get_translation($default_value) . "</span>";
				$property_control .= $after_control_html;
				if (strlen($default_value)) {
					$custom_options[$property_id][] = array(
						"order" => $property_order, "name" => $property_name_initial, 
						"value_id" => "", "value" => $control_value,
					);
				}
			}

			$t->set_var("property_id", $property_id);
			$t->set_var("property_name", $before_name_html . $property_name . $after_name_html);
			$t->set_var("property_style", $property_style);
			$t->set_var("property_control", $property_control);
			if ($property_required) {
				$t->set_var("property_required", "*");
			} else {
				$t->set_var("property_required", "");
			}

			if (strlen($action) && $property_required && !isset($custom_options[$property_id])) {
				$property_message = str_replace("{field_name}", $property_name, REQUIRED_MESSAGE) . "<br>";
				$options_errors .= $property_message;
			}
			
			// check option with regexp
			$regexp_valid = true;
			if (strlen($action) && isset($custom_options[$property_id]) && strlen($validation_regexp)) {
				$validation_value = "";
				foreach ($custom_options[$property_id] as $value_id => $value_data) {
					if (strval($validation_value) != "") { $validation_value .= ","; }
					$validation_value .= $value_data["value"];
				}
				if(!preg_match($validation_regexp, $validation_value)) {
					$regexp_valid = false;
				}
			}

			if (!$regexp_valid) {
				$property_message = str_replace("{field_name}", $property_name, $regexp_error) . "<br>";
				$options_errors .= $property_message;
			}

			$t->parse("payment_properties", true);
		}

	}
	// and custom options

	$cc_start_year   = get_param("cc_start_year");
	$cc_start_month  = get_param("cc_start_month");
	$cc_expiry_year  = get_param("cc_expiry_year");
	$cc_expiry_month = get_param("cc_expiry_month");

	set_session("session_order_id", $order_id);
	set_session("session_vc", $vc);
	set_session("session_payment_id", $payment_id);
	$return_page = "order_confirmation.php";
	$items_text = "";

	if ($parameters_number == 0)
	{
		header("Location: " . $return_page);
		exit;
	}

	if (strlen($cc_start_year) && strlen($cc_start_month)) {
		$r->set_value("cc_start_date", array($cc_start_year, $cc_start_month, 1, 0, 0, 0));
	}

	if (strlen($cc_expiry_year) && strlen($cc_expiry_month)) {
		$r->set_value("cc_expiry_date", array($cc_expiry_year, $cc_expiry_month, 1, 0, 0, 0));
	}

	if (strlen($action))
	{
		if ($r->is_empty("order_id")) {
			$r->errors .= "Missing <b>Order number</b>.<br>";
		}

		$cc_number = $r->get_value("cc_number");
		if (strlen($cc_number) >= 10) {
			$ss = array("\\","^","\$",".","[","]","|","(",")","+","{","}");
			$rs = array("\\\\","\\^","\\\$","\\.","\\[","\\]","\\|","\\(","\\)","\\+","\\{","\\}");
			$cc_allowed_regexp = get_setting_value($cc_info, "cc_allowed", "");
			$cc_allowed_regexp = preg_replace("/\s/", "", $cc_allowed_regexp);
			if (strlen($cc_allowed_regexp)) {
				$cc_allowed_regexp = str_replace($ss, $rs, $cc_allowed_regexp);
				$cc_allowed_regexp = str_replace(array(",", ";", "*", "?"), array(")|(", ")|(", ".*", "."), $cc_allowed_regexp);
				$cc_allowed_regexp = "/^((" . $cc_allowed_regexp. "))$/i";
			}
			$cc_forbidden_regexp = get_setting_value($cc_info, "cc_forbidden", "");
			$cc_forbidden_regexp = preg_replace("/\s/", "", $cc_forbidden_regexp);
			if (strlen($cc_forbidden_regexp)) {
				$cc_forbidden_regexp = str_replace($ss, $rs, $cc_forbidden_regexp);
				$cc_forbidden_regexp = str_replace(array(",", ";", "*", "?"), array(")|(", ")|(", ".*", "."), $cc_forbidden_regexp);
				$cc_forbidden_regexp = "/^((" . $cc_forbidden_regexp. "))$/i";
			}
			if (strlen($cc_allowed_regexp) && !preg_match($cc_allowed_regexp, $cc_number)) {
				$r->errors = CC_NUMBER_ALLOWED_MSG . "<br>" . $eol;
			} elseif (strlen($cc_forbidden_regexp) && preg_match($cc_forbidden_regexp, $cc_number)) {
				$r->errors = CC_NUMBER_ALLOWED_MSG . "<br>" . $eol;
			} elseif (!check_cc_number($cc_number)) {
				$r->errors = CC_NUMBER_ERROR_MSG . "<br>" . $eol;
			}
		}

		$r->validate();
		$r->errors .= $options_errors;

		if (!strlen($r->errors))
		{
			$cc_number = clean_cc_number($cc_number);
			$cc_number_len = strlen($cc_number);
			$cc_security_code = $r->get_value("cc_security_code");
			$r->set_value("cc_number", $cc_number);
			set_session("session_cc_number", $cc_number);
			set_session("session_cc_code",   $cc_security_code);
			if ($cc_number_len > 6) {
				$cc_number_first = substr($cc_number, 0, 6);
			} else {
				$cc_number_first = $cc_number;
			}
			if ($cc_number_len > 4) {
				$cc_number_last = substr($cc_number, $cc_number_len - 4);
				if ($cc_info["cc_number_split"]) {
					$r->set_value("cc_number", substr($cc_number, 0, $cc_number_len - 4) . "****");
				}
			} else {
				$cc_number_last = $cc_number;
			}
			set_session("session_cc_number_first", $cc_number_first);
			set_session("session_cc_number_last", $cc_number_last);

			if ($cc_number_security == 0) {
				$r->set_value("cc_number", "");
			} elseif ($cc_number_security > 0) {
				$r->set_value("cc_number", va_encrypt($r->get_value("cc_number")));
			}

			if ($cc_code_security == 0) {
				$r->set_value("cc_security_code", "");
			} elseif ($cc_code_security > 0) {
				$r->set_value("cc_security_code", va_encrypt($cc_security_code));
			}

			if ($r->update_record())
			{
				// update order status
				$cc_order_status = 2;
				update_order_status($order_id, $cc_order_status, true, "", $status_error);

				$op = new VA_Record($table_prefix . "orders_properties");
				$op->add_textbox("order_id", INTEGER);
				$op->set_value("order_id", $order_id);
				$op->add_textbox("property_id", INTEGER);
				$op->add_textbox("property_order", INTEGER);
				$op->add_textbox("property_type", INTEGER);
				$op->add_textbox("property_name", TEXT);
				$op->add_textbox("property_value_id", INTEGER);
				$op->add_textbox("property_value", TEXT);
				$op->add_textbox("property_price", FLOAT);
				$op->add_textbox("property_weight", FLOAT);
				$op->add_textbox("tax_free", INTEGER);
				foreach ($custom_options as $property_id => $property_values) {
					// delete first all saved values
					$sql  = " DELETE FROM " . $table_prefix . "orders_properties ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$sql .= " AND property_id =" . $db->tosql($property_id, INTEGER);
					$db->query($sql);

          $property_full_desc = ""; 
					foreach ($property_values as $value_id => $value_data) {
						$property_order = $value_data["order"];
						$property_name = $value_data["name"];
						$property_value_id = $value_data["value_id"];
						$property_value = $value_data["value"];
						if ($property_full_desc) { $property_full_desc .= "; "; }
						$property_full_desc .= $property_value;

						$op->set_value("property_id", $property_id);
						$op->set_value("property_order", $property_order);
						$op->set_value("property_type", 4);
						$op->set_value("property_name", $property_name);
						$op->set_value("property_value_id", $property_value_id);
						$op->set_value("property_value", $property_value);
						$op->set_value("property_price", 0);
						$op->set_value("property_weight", 0);
						$op->set_value("tax_free", 0);
						$op->insert_record();
					}
					$t->set_var("field_name_" . $property_id, $property_name);
					$t->set_var("field_value_" . $property_id, $property_full_desc);
					$t->set_var("field_" . $property_id, $property_full_desc);
				}

				$admin_notification = get_setting_value($cc_info, "admin_notification", 0);
				$admin_sms = get_setting_value($cc_info, "admin_sms_notification", 0);

				if ($admin_notification || $admin_sms)
				{
					$admin_mail_type = get_setting_value($cc_info, "admin_message_type");
					$admin_message = get_setting_value($cc_info, "admin_message", "");
					$admin_sms_message    = get_setting_value($cc_info, "admin_sms_message", "");
					$items_text = "";
					// parse basket template
					if ($admin_notification && $admin_mail_type && strpos($admin_message, "{basket}") !== false)
					{
						$t->set_file("basket", "email_basket.html");
						$items_text = show_order_items($order_id, true, "");
						$t->parse("basket", false);
					}
					if (($admin_notification && !$admin_mail_type && strpos($admin_message, "{basket}") !== false) 
						|| ($admin_sms && !$items_text && strpos($admin_sms_message, "{basket}") !== false))
					{
						$t->set_file("basket", "email_basket.txt");
						$items_text = show_order_items($order_id, true, "");
						$t->parse("basket", false);
					}

					$sql = "SELECT * FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);
					$db->next_record();
					$t->set_vars($db->Record);

					$t->set_var("goods_total", currency_format($db->f("goods_total")));
					$t->set_var("shipping_cost", currency_format($db->f("shipping_cost")));
					$t->set_var("tax_percent", number_format($db->f("tax_percent"), 2) . "%");
					$order_placed_date = $db->f("order_placed_date", DATETIME);
					$order_placed_date_string = va_date($datetime_show_format, $order_placed_date);
					$t->set_var("order_placed_date", $order_placed_date_string);
					$company_id = $db->f("company_id");
					$state_id = $db->f("state_id");
					$country_id = $db->f("country_id");
					$delivery_company_id = $db->f("delivery_company_id");
					$delivery_state_id = $db->f("delivery_state_id");
					$delivery_country_id = $db->f("delivery_country_id");
					$company_select = get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($company_id, INTEGER));
					$delivery_company_select = get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($delivery_company_id, INTEGER));
					$state = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($state_id, INTEGER, true, false));
					$delivery_state = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($delivery_state_id, INTEGER, true, false));
					$country = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($country_id, INTEGER, true, false));
					$delivery_country = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($delivery_country_id, INTEGER, true, false));

					$t->set_var("company_select", $company_select);
					$t->set_var("state", $state);
					$t->set_var("country", $country);
					$t->set_var("delivery_company_select", $delivery_company_select);
					$t->set_var("delivery_state", $delivery_state);
					$t->set_var("delivery_country", $delivery_country);

					$t->set_var("cc_number", $cc_number);
					$t->set_var("cc_number_first", get_session("session_cc_number_first"));
					$t->set_var("cc_number_last", get_session("session_cc_number_last"));
					$t->set_var("cc_security_code", $cc_security_code);
					$cc_type = get_array_value($r->get_value("cc_type"), $credit_cards); 
					$t->set_var("cc_type", $cc_type);
					$cc_start = va_date(array("MM", " / ", "YYYY"), $r->get_value("cc_start_date"));
					$cc_expiry = va_date(array("MM", " / ", "YYYY"), $r->get_value("cc_expiry_date"));
					$t->set_var("cc_start_date", $cc_start);
					$t->set_var("cc_expiry_date", $cc_expiry);

					$t->set_block("payment_info", $payment_info);
					$t->parse("payment_info", false);
				}

				if ($cc_info["admin_notification"])
				{
					$admin_subject = get_setting_value($cc_info, "admin_subject", "");
					$admin_subject = get_translation($admin_subject);
					$admin_message = get_currency_message(get_translation($admin_message), $currency);
					// PGP enable
					$admin_notification_pgp = get_setting_value($cc_info, "admin_notification_pgp",   0);

					$t->set_block("admin_subject", $admin_subject);
					$t->set_block("admin_message", $admin_message);

					$mail_to = get_setting_value($cc_info, "admin_email", $settings["admin_email"]);
					$mail_to = str_replace(";", ",", $mail_to);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($cc_info, "admin_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($cc_info, "cc_emails");
					$email_headers["bcc"] = get_setting_value($cc_info, "admin_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($cc_info, "admin_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($cc_info, "admin_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($cc_info, "admin_message_type");

					$t->parse("admin_subject", false);
					$t->parse("admin_message", false);
					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					
					// PGP encryption			
					if ( $admin_notification_pgp && $admin_message) {	
						include_once ($root_folder_path . "includes/pgp_functions.php");
						if (pgp_test()) {
							$tmp_admin_emails = explode(',',$mail_to);
							foreach ($tmp_admin_emails AS $tmp_admin_email) {
								$admin_message = pgp_encrypt($admin_message, $tmp_admin_email);
								if ($admin_message){
									va_mail($tmp_admin_email, $t->get_var("admin_subject"), $admin_message, $email_headers);
								}
							}
						}
					} else {
						va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);		
					}					
				}		 

				if ($admin_sms) 
				{
					$admin_sms_recipient  = get_setting_value($cc_info, "admin_sms_recipient", "");
					$admin_sms_originator = get_setting_value($cc_info, "admin_sms_originator", "");

					$t->set_block("admin_sms_recipient",  $admin_sms_recipient);
					$t->set_block("admin_sms_originator", $admin_sms_originator);
					$t->set_block("admin_sms_message",    $admin_sms_message);

					$t->set_var("basket", $items_text);
					$t->set_var("items", $items_text);

					$t->parse("admin_sms_recipient", false);
					$t->parse("admin_sms_originator", false);
					$t->parse("admin_sms_message", false);

					sms_send($t->get_var("admin_sms_recipient"), $t->get_var("admin_sms_message"), $t->get_var("admin_sms_originator"));
				}		 

			}
			header("Location: " . $return_page);
			exit;
		}
	}
	else {
		// Prepopulate Name of Cardholder
		$db->query("SELECT name, first_name, last_name FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER));
		if ($db->next_record()) {
			$name = $db->f("name");
			$first_name = $db->f("first_name");
			$last_name = $db->f("last_name");
			@list($l_first_name, $l_last_name) = split(" ", $name, 2);
			if (!strlen($first_name)) {
				$first_name = $l_first_name;
			}
			if (!strlen($last_name)) {
				$last_name = $l_last_name;
			}
			if ($r->parameters["cc_name"][SHOW]) { 
				if (strlen($name)) {
					$r->set_value("cc_name", $name);
				} else {
					$r->set_value("cc_name", trim($first_name . " " . $last_name));
				}
			}
			if ($r->parameters["cc_first_name"][SHOW]) { 
				$r->set_value("cc_first_name", $first_name);
			}
			if ($r->parameters["cc_last_name"][SHOW]) { 
				$r->set_value("cc_last_name", $last_name);
			}
		}
	}
	
	/* if user can update his order details
	elseif ($order_id) // get user details from order
	{
		$sql = " SELECT * FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record())
		{
			for ($i = 0; $i < sizeof($cc_parameters); $i++) { 
				$r->set_value($cc_parameters[$i], $db->f($cc_parameters[$i]));
				$r->set_value("delivery_" . $cc_parameters[$i], $db->f("delivery_" . $cc_parameters[$i]));
			}
		}
	}*/

	if (trim($payment_info)) {
		$sql = "SELECT * FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		$db->next_record();
		$t->set_vars($db->Record);
		$t->set_block("payment_info", $payment_info);
		$t->parse("payment_info", false);
		$t->global_parse("payment_info_block", false, false, true);
	} else {
		$t->set_var("payment_info_block", "");
	}

	$current_date = va_time();
	$cc_start_years = get_db_values("SELECT start_year AS year_value, start_year AS year_description FROM " . $table_prefix . "cc_start_years", array(array("", YEAR_MSG)));
	if (sizeof($cc_start_years) < 2) {
		$cc_start_years = array(array("", YEAR_MSG));
		for($y = 7; $y >= 0; $y--) {
			$cc_start_years[] = array($current_date[YEAR] - $y, $current_date[YEAR] - $y);
		}
	}
	$cc_expiry_years = get_db_values("SELECT expiry_year AS year_value, expiry_year AS year_description FROM " . $table_prefix . "cc_expiry_years", array(array("", YEAR_MSG)));
	if (sizeof($cc_expiry_years) < 2) {
		$cc_expiry_years = array(array("", YEAR_MSG));
		for($y = 0; $y <= 7; $y++) {
			$cc_expiry_years[] = array($current_date[YEAR] + $y, $current_date[YEAR] + $y);
		}
	}
	set_options($cc_start_years, $cc_start_year, "cc_start_year");
	set_options($cc_expiry_years, $cc_expiry_year, "cc_expiry_year");

	$cc_months = array_merge (array(array("", MONTH_MSG)), $months);
	set_options($cc_months, $cc_start_month, "cc_start_month");
	set_options($cc_months, $cc_expiry_month, "cc_expiry_month");

	$r->set_parameters();

	$intro_text = trim($cc_info["intro_text"]);
	$intro_text = get_translation($intro_text);
	$intro_text = get_currency_message($intro_text, $currency);
	if ($intro_text) {
		$t->set_var("intro_text", $intro_text);
		$t->parse("intro_block", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>