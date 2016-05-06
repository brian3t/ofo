<?php

function support_block($block_name) 
{
	global $t, $db, $db_type, $table_prefix;
	global $settings, $datetime_show_format, $currency, $site_id;

	$t->set_file("block_body", "block_support.html");
	$errors = false;
	
	$user_id = get_session("session_user_id");
	$section_name = "All";
	$sections = array(array(0,"All"));
	
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='support'";
	if (isset($site_id)) {
		$sql .= " AND ( site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
		$sql .= " ORDER BY site_id ASC";
	} else {
		$sql .= " AND site_id=1";		
	}
	$db->query($sql);
	while ($db->next_record()) {
		$support_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$eol = get_eol();
	$submit_tickets = intval(get_setting_value($support_settings, "submit_tickets", 0));
	$use_random_image = intval(get_setting_value($support_settings, "use_random_image", 0));
	$attachments_users_allowed = get_setting_value($support_settings, "attachments_users_allowed", 0);

	if ($submit_tickets == 1) {
		check_user_session();
	}

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_user_ticket = get_setting_value($settings, "secure_user_ticket", 0);
	$secure_user_tickets = get_setting_value($settings, "secure_user_tickets", 0);
	if ($secure_user_ticket) {
		$support_url = $secure_url . get_custom_friendly_url("support.php");
		$support_messages_url = $secure_url . get_custom_friendly_url("support_messages.php");
		$user_support_attachments_url = $secure_url . get_custom_friendly_url("user_support_attachments.php");
	} else {
		$support_url = $site_url . get_custom_friendly_url("support.php");
		$support_messages_url = $site_url . get_custom_friendly_url("support_messages.php");
		$user_support_attachments_url = $site_url . get_custom_friendly_url("user_support_attachments.php");
	}
	if ($secure_user_tickets) {
		$user_support_url = $secure_url . get_custom_friendly_url("user_support.php");
	} else {
		$user_support_url = $site_url . get_custom_friendly_url("user_support.php");
	}
	$user_home_url = $site_url . get_custom_friendly_url("user_home.php");

	if (($use_random_image == 2) || ($use_random_image == 1 && !strlen(get_session("session_user_id")))) { 
		$use_validation = true;
	} else {
		$use_validation = false;
	}
	
	
	
	// prepare custom options
	$pp = array(); $pn = 0;
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "support_custom_properties ";
	if ($user_id) {
		$sql .= " WHERE property_show IN (1,3) ";
	} else {
		$sql .= " WHERE property_show IN (1,2) ";
	}
	if (isset($site_id)) {
		$sql .= " AND site_id=" . $db->tosql($site_id, INTEGER, true, false);
	} else {
		$sql .= " AND site_id=1 ";
	}
	$sql .= " ORDER BY property_order, property_id ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$pp[$pn]["property_id"] = $db->f("property_id");
			$pp[$pn]["property_order"] = $db->f("property_order");
			$pp[$pn]["property_name"] = $db->f("property_name");
			$pp[$pn]["property_description"] = $db->f("property_description");
			$pp[$pn]["default_value"] = $db->f("default_value");
			$pp[$pn]["property_style"] = $db->f("property_style");
			$pp[$pn]["section_id"] = $db->f("section_id");
			$pp[$pn]["control_type"] = $db->f("control_type");
			$pp[$pn]["control_style"] = $db->f("control_style");
			$pp[$pn]["control_code"] = $db->f("control_code");
			$pp[$pn]["onchange_code"] = $db->f("onchange_code");
			$pp[$pn]["onclick_code"] = $db->f("onclick_code");
			$pp[$pn]["required"] = $db->f("required");
			$pp[$pn]["before_name_html"] = $db->f("before_name_html");
			$pp[$pn]["after_name_html"] = $db->f("after_name_html");
			$pp[$pn]["before_control_html"] = $db->f("before_control_html");
			$pp[$pn]["after_control_html"] = $db->f("after_control_html");
			$pp[$pn]["validation_regexp"] = $db->f("validation_regexp");
			$pp[$pn]["regexp_error"] = $db->f("regexp_error");
			$pp[$pn]["options_values_sql"] = $db->f("options_values_sql");

			$pn++;
		} while ($db->next_record());
	}
	
	$t->set_var("site_url", $settings["site_url"]);

	$provide_info_message = str_replace("{button_name}", SUPPORT_REQUEST_BUTTON, PROVIDE_INFO_MSG);
	$t->set_var("PROVIDE_INFO_MSG", $provide_info_message);

	$t->set_var("support_href", $support_url);
	$t->set_var("user_home_href", $user_home_url);
	$t->set_var("user_support_href", $user_support_url);
	$t->set_var("user_support_attachments_url", $user_support_attachments_url);
	$t->set_var("rnd", va_timestamp());

	$r = new VA_Record($table_prefix . "support", "support");

	$sql  = " SELECT d.dep_id, d.short_title ";
	if (isset($site_id)) {
		$sql .= " FROM (" . $table_prefix . "support_departments d ";
		$sql .= " LEFT JOIN " . $table_prefix . "support_departments_sites ds  ON (ds.dep_id=d.dep_id AND d.sites_all=0))";	
		$sql .= " WHERE d.show_for_user=1 AND d.sites_all=1 OR ds.site_id=" . $db->tosql($site_id, INTEGER, true, false);		
	} else {
		$sql .= " FROM " . $table_prefix . "support_departments d ";
		$sql .= " WHERE d.show_for_user=1 AND d.sites_all=1";		
	}
	$support_deps = get_db_values($sql , array(array("", SUPPORT_SELECT_DEP_MSG)));
	$number_of_deps = count($support_deps) - 1;
	if ($number_of_deps<1) {
		$t->set_var($block_name, 'No support department is availiable');
		return false;
	}
	$sql  = " SELECT p.product_id, p.product_name ";
	$sql .= " FROM " . $table_prefix . "support_products AS p ";
	if (isset($site_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "support_products_sites ps  ON ps.product_id=p.product_id";	
		$sql .= " WHERE p.show_for_user=1 AND p.sites_all=1 OR ps.site_id=" . $db->tosql($site_id, INTEGER, true, false);
		$sql .= " GROUP BY  p.product_id, p.product_name ";			
	} else {
		$sql .= " WHERE p.show_for_user=1 AND p.sites_all=1";		
	}
	$support_products = get_db_values($sql, array(array("", SUPPORT_SELECT_PROD_MSG)));
	$number_of_products = count($support_products) - 1;	
	
	$support_types = get_db_values("SELECT * FROM " . $table_prefix . "support_types WHERE show_for_user=1", array(array("", SELECT_TYPE_MSG)));

	$r->add_where("support_id", INTEGER);
	$r->add_textbox("site_id", INTEGER);
	$r->change_property("site_id", USE_SQL_NULL, false);
	$r->add_textbox("user_id", INTEGER);
	$r->add_textbox("affiliate_code", TEXT);
	$r->change_property("affiliate_code", USE_SQL_NULL, false);
	$r->add_textbox("user_name", TEXT, SUPPORT_USER_NAME_FIELD);
	$r->change_property("user_name", TRIM, true);
	$r->change_property("user_name", REQUIRED, true);
	$r->add_textbox("user_email", TEXT, SUPPORT_USER_EMAIL_FIELD);
	$r->change_property("user_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->change_property("user_email", TRIM, true);
	$r->change_property("user_email", REQUIRED, true);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("identifier", TEXT);
	$r->add_textbox("environment", TEXT);
	$r->add_select("dep_id", INTEGER, $support_deps, SUPPORT_DEPARTMENT_FIELD);
	$r->change_property("dep_id", REQUIRED, true);
	if ($number_of_deps < 2) {
		$r->parameters["dep_id"][SHOW] = false;
	} 
	$r->add_select("support_product_id", INTEGER, $support_products, SUPPORT_PRODUCT_FIELD);
	$r->change_property("support_product_id", REQUIRED, true);
	if ($number_of_products < 2) {
		$r->parameters["support_product_id"][SHOW] = false;
	} 
	$r->add_select("support_type_id", INTEGER, $support_types, SUPPORT_TYPE_FIELD);
	$r->change_property("support_type_id", REQUIRED, true);
	$r->add_textbox("summary", TEXT, SUPPORT_SUMMARY_FIELD);
	$r->change_property("summary", TRIM, true);
	$r->change_property("summary", REQUIRED, true);
	$r->add_textbox("description", TEXT, SUPPORT_DESCRIPTION_FIELD);
	$r->change_property("description", TRIM, true);
	$r->change_property("description", REQUIRED, true);
	$r->add_textbox("support_status_id", INTEGER);
	$r->add_textbox("support_priority_id", INTEGER);
	$r->add_textbox("admin_id_assign_to", INTEGER);
	$r->add_textbox("admin_id_assign_by", INTEGER);
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_SQL_NULL, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_SQL_NULL, false);
	$r->add_textbox("date_added", DATETIME);
	$r->add_textbox("date_modified", DATETIME);
	$r->add_textbox("validation_number", TEXT, VALIDATION_CODE_FIELD);
	$r->change_property("validation_number", USE_IN_INSERT, false);
	$r->change_property("validation_number", USE_IN_UPDATE, false);
	$r->change_property("validation_number", USE_IN_SELECT, false);
	if ($use_validation) {
		$r->change_property("validation_number", REQUIRED, true);
		$r->change_property("validation_number", SHOW, true);
	} else {
		$r->change_property("validation_number", REQUIRED, false);
		$r->change_property("validation_number", SHOW, false);
	}
	
	foreach ($pp as $id => $pp_row) {
		$control_type = $pp_row["control_type"];
		$param_name = "pp_" . $pp_row["property_id"];
		$param_title = $pp_row["property_name"];

		if ($control_type == "CHECKBOXLIST") {
			$r->add_checkboxlist($param_name, TEXT, "", $param_title);
		} elseif ($control_type == "RADIOBUTTON") {
			$r->add_radio($param_name, TEXT, "", $param_title);
		} elseif ($control_type == "LISTBOX") {
			$r->add_select($param_name, TEXT, "", $param_title);
		} else {
			$r->add_textbox($param_name, TEXT, $param_title);
		}
		if ($control_type == "CHECKBOXLIST" || $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") {
			if ($pp_row["options_values_sql"]) {
				$sql = $pp_row["options_values_sql"];
			} else {
				$sql  = " SELECT property_value_id, property_value FROM " . $table_prefix . "support_custom_values ";
				$sql .= " WHERE property_id=" . $db->tosql($pp_row["property_id"], INTEGER) . " AND hide_value=0";
				$sql .= " ORDER BY property_value_id ";
			}
			$r->change_property($param_name, VALUES_LIST, get_db_values($sql, ""));
		}
		if ($pp_row["required"] == 1) {
			$r->change_property($param_name, REQUIRED, true);
		}
		if ($pp_row["validation_regexp"]) {
			$r->change_property($param_name, REGEXP_MASK, $pp_row["validation_regexp"]);
			if ($pp_row["regexp_error"]) {
				$r->change_property($param_name, REGEXP_ERROR, $pp_row["regexp_error"]);
			}
		}
		$r->change_property($param_name, USE_IN_SELECT, false);
		$r->change_property($param_name, USE_IN_INSERT, false);
		$r->change_property($param_name, USE_IN_UPDATE, false);
	}
	
	$user_name_class = "normal"; 
	$user_email_class = "normal"; 
	$dep_class = "normal"; 
	$product_class = "normal"; 
	$type_class = "normal"; 
	$summary_class = "normal"; 
	$description_class = "normal"; 	
	$validation_class = "normal"; 

	$action = get_param("action");
	$rnd = get_param("rnd");
	$filter = get_param("filter");
	$remote_address = get_ip();

	$session_rnd = get_session("session_rnd");

	if ($action && $rnd != $session_rnd)
	{
		set_session("session_rnd", $rnd);

		$r->get_form_values();
		$r->set_value("affiliate_code", get_session("session_af"));

		if ($number_of_deps == 1) {
			$sql = " SELECT dep_id FROM " . $table_prefix . "support_departments WHERE show_for_user=1 ";
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("dep_id", $db->f("dep_id"));	
			} else {
				$sql = " SELECT dep_id FROM " . $table_prefix . "support_departments ";
				$db->query($sql);
				if ($db->next_record()) {
					$r->set_value("dep_id", $db->f("dep_id"));	
				}
			}
		}
		if ($number_of_products == 1) {
			$sql = " SELECT product_id FROM " . $table_prefix . "support_products WHERE show_for_user=1 ";
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("support_product_id", $db->f("product_id"));
			}
		} elseif ($number_of_products == 0) {
			$r->set_value("support_product_id", 0);
		}	

		if ($r->is_empty("user_name")) {
			$user_name_class = "error";
		}
		if ($r->is_empty("user_email")) {
			$user_email_class = "error";
		}
		if ($number_of_deps > 1 && $r->is_empty("dep_id")) {
			$dep_class = "error";
		}
		if ($number_of_products > 1 && $r->is_empty("support_product_id")) {
			$product_class = "error";
		}
		if ($r->is_empty("support_type_id")) {
			$type_class = "error";
		}
		if ($r->is_empty("summary")) {
			$summary_class = "error";
		}
		if ($r->is_empty("description")) {
			$description_class = "error";
		}
		
		foreach ($pp as $id => $pp_row) {
			$param_name = "pp_" . $pp_row["property_id"];
			if ($r->is_empty($param_name) && $pp_row["required"] == 1) {
				$pp[$id]["property_class"] = "error";
			}
		}

		$r->validate();

		if ($use_validation) {
			if ($r->is_empty("validation_number")) {
				$validation_class = "error"; 
			} else {
				$validated_number = check_image_validation($r->get_value("validation_number"));
				if (!$validated_number) {
					$validation_class = "error"; 
					$r->errors .= str_replace("{field_name}", VALIDATION_CODE_FIELD, VALIDATION_MESSAGE);
				} elseif ($r->errors) {
					// saved validated number for following submits	
					set_session("session_validation_number", $validated_number);
				}
			} 
		}

		if (strlen($r->errors)) {
			$errors = true;
			set_session("session_rnd", "");
		}

		if (!$errors)
		{
			$user_id = strlen(get_session("session_user_id")) ? get_session("session_user_id") : 0;
			$user_email = trim($r->get_value("user_email"));

			// get status for new message
			$sql = " SELECT status_id,status_name,status_caption FROM " . $table_prefix . "support_statuses WHERE is_user_new=1 ";
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("support_status_id", $db->f("status_id"));	
				$status_name = $db->f("status_name");
				$status_caption = $db->f("status_caption");
			} else {
				$status_name = NEW_MSG;
				$status_caption = NEW_MSG;
				$r->set_value("support_status_id", 0);	
			}

			// get priority for new message
			$priority_id = 0;
			$sql  = " SELECT sp.priority_id, sup.priority_expiry ";
			$sql .= " FROM " . $table_prefix . "support_priorities sp, " . $table_prefix . "support_users_priorities sup ";
			$sql .= " WHERE sp.priority_id=sup.priority_id ";
			if ($user_id > 0) {
				$sql .= " AND (user_id=" . $db->tosql($user_id, INTEGER);
				$sql .= " OR user_email=" . $db->tosql($user_email, TEXT) . ")";
			} else {
				$sql .= " AND user_email=" . $db->tosql($user_email, TEXT);
			}
			$db->query($sql);
			if ($db->next_record()) {
				$priority_id = $db->f("priority_id");	
				$current_ts = va_timestamp();
				$priority_expiry = $db->f("priority_expiry", DATETIME);
				if (is_array($priority_expiry)) {
					$priority_expiry_ts = va_timestamp($priority_expiry); 
					if ($current_ts > $priority_expiry_ts) {
						// user rank expired
						$priority_id = 0;
					}
				}
			} 
			if (!$priority_id) {
				$sql  = " SELECT priority_id FROM " . $table_prefix . "support_priorities WHERE is_default=1 ";
				$db->query($sql);
				if ($db->next_record()) {
					$priority_id = $db->f("priority_id");	
				}
			}

			$date_added = va_time();
			
			if (isset($site_id)) {
				$r->set_value("site_id", $site_id);
			} else {
				$r->set_value("site_id", 1);
			}
			$r->set_value("user_id", $user_id);
			$r->set_value("date_added", $date_added);
			$r->set_value("date_modified", va_time());
			$r->set_value("remote_address", $remote_address);
			$r->set_value("admin_id_assign_to", 0);
			$r->set_value("admin_id_assign_by", 0);
			if (get_session("session_admin_id")) {
				$r->set_value("admin_id_added_by", get_session("session_admin_id"));
			} else {
				$r->set_value("admin_id_added_by", 0);
			}
			$r->set_value("admin_id_modified_by", 0);
			$r->set_value("support_priority_id", $priority_id);
			
			if ($db_type == "postgre") {
				$support_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "support') ");
				$r->change_property("support_id", USE_IN_INSERT, true);
				$r->set_value("support_id", $support_id);
			}
			if ($r->insert_record())
			{	
				if ($db_type == "mysql") {
					$support_id = get_db_value(" SELECT LAST_INSERT_ID() ");
				} elseif ($db_type == "access") {
					$support_id = get_db_value(" SELECT @@IDENTITY ");
				} elseif ($db_type == "db2") {
					$support_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "support FROM " . $table_prefix . "support");
				}
				$r->set_value("support_id", $support_id);
				$vc = md5($support_id . $date_added[3].$date_added[4].$date_added[5]);
				$ticket_url = $support_messages_url . "?support_id=" . $support_id . "&vc=" . $vc;

				update_support_properties($pp,$r,$support_id);

				// update attachments
				$sql  = " UPDATE " . $table_prefix . "support_attachments ";
				$sql .= " SET support_id=" . $db->tosql($support_id, INTEGER);
				$sql .= " , attachment_status=1 ";
				$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
				$sql .= " AND support_id=0 ";
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);

				// check attachments
				$attachments = array();
				if ($user_id) {
					$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "support_attachments ";
					$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
					$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
					$sql .= " AND message_id=0 ";
					$sql .= " AND attachment_status=1 ";
					$db->query($sql);
					while ($db->next_record()) {
						$filename = $db->f("file_name");
						$filepath = $db->f("file_path");
						$attachments[] = array($filename, $filepath);
					}
				}

				// send email notification to admin
				if ($support_settings["admin_notification"])
				{
					$t->set_block("admin_subject", $support_settings["admin_subject"]);
					$t->set_block("admin_message", $support_settings["admin_message"]);

					$date_added_string = va_date($datetime_show_format, $date_added);
					$t->set_var("request_added", $date_added_string);
					$t->set_var("message_added", $date_added_string);
					$t->set_var("date_added", $date_added_string);
					$t->set_var("date_modified", $date_added_string);
					$t->set_var("vc", $vc);
					$t->set_var("support_id", $support_id);
					$t->set_var("support_url", $ticket_url);
					$t->set_var("ticket_url", $ticket_url);
					$t->set_var("user_id", $user_id);
					
					$t->set_var("product", get_array_value($r->get_value("support_product_id"), $support_products));
					$t->set_var("type", get_array_value($r->get_value("support_type_id"), $support_types));
					$t->set_var("status", $status_name);
					$t->set_var("status_name", $status_name);
					$t->set_var("status_caption", $status_caption);
					$t->set_var("priority", "Normal");
			  
					$mail_to = get_setting_value($support_settings, "admin_email", $settings["admin_email"]);
					$mail_to = str_replace(";", ",", $mail_to);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($support_settings, "admin_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($support_settings, "cc_emails");
					$email_headers["bcc"] = get_setting_value($support_settings, "admin_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($support_settings, "admin_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($support_settings, "admin_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($support_settings, "admin_message_type");

					$support_properties = array();
					$sql  = " SELECT sp.property_id, scp.property_name, sp.property_value,  scp.control_type";
					$sql .= " FROM (" . $table_prefix . "support_properties sp ";
					$sql .= " INNER JOIN " . $table_prefix . "support_custom_properties scp ON sp.property_id=scp.property_id)";
					$sql .= " WHERE sp.support_id=" . $db->tosql($support_id, INTEGER);
					$sql .= " ORDER BY sp.property_id ";
					$db->query($sql);
					if ($db->next_record()){
						$dbd = new VA_SQL();
						$dbd->DBType = $db->DBType;
						$dbd->DBDatabase = $db->DBDatabase;
						$dbd->DBHost = $db->DBHost;
						$dbd->DBPort = $db->DBPort;
						$dbd->DBUser = $db->DBUser;
						$dbd->DBPassword = $db->DBPassword;
						$dbd->DBPersistent = $db->DBPersistent;
						do {
							$property_id   = $db->f("property_id");
							$property_name = $db->f("property_name");
							$property_value = $db->f("property_value");
							$property_price = $db->f("property_price");
							$control_type = $db->f("control_type");
							// check value description
							if (($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && is_numeric($property_value)) {
								$sql  = " SELECT property_value FROM " . $table_prefix . "support_custom_values ";
								$sql .= " WHERE property_value_id=" . $db->tosql($property_value, INTEGER);
								$dbd->query($sql);
								if ($dbd->next_record()) {
									$property_value = get_translation($dbd->f("property_value"));
								}
							}
							if (isset($support_properties[$property_id])) {
								$support_properties[$property_id]["value"] .= "; " . $property_value;
							} else {
								$support_properties[$property_id] = array(
									"name" => $property_name, "value" => $property_value,
								);
							}
						} while ($db->next_record());
					}
					
					if (count($pp) > 0) {
						foreach ($support_properties as $property_id => $property_values) {
							$property_name = $property_values["name"];
							$property_value = $property_values["value"];

							$t->set_var("field_name_" . $property_id, $property_name);
							$t->set_var("field_value_" . $property_id, $property_value);
							$t->set_var("field_" . $property_id, $property_value);
							$t->set_var("property_name", $property_name);
							$t->set_var("property_value", $property_value);
						}
					}
					
					$t->set_var("summary", $r->get_value("summary"));
					$t->set_var("description", $r->get_value("description"));
					$t->set_var("message_text", $r->get_value("description"));
					$t->set_var("user_name", $r->get_value("user_name"));
					$t->set_var("user_email", $r->get_value("user_email"));
					$t->set_var("remote_address", $r->get_value("remote_address"));
					$t->set_var("identifier", $r->get_value("identifier"));
					$t->set_var("environment", $r->get_value("environment"));
					$t->parse("admin_subject", false);
					if ($email_headers["mail_type"]) {
						$t->set_var("summary", htmlspecialchars($r->get_value("summary")));
						$t->set_var("description", nl2br(htmlspecialchars($r->get_value("description"))));
						$t->set_var("message_text", nl2br(htmlspecialchars($r->get_value("description"))));
						$t->set_var("user_name", htmlspecialchars($r->get_value("user_name")));
						$t->set_var("user_email", htmlspecialchars($r->get_value("user_email")));
						$t->set_var("remote_address", htmlspecialchars($r->get_value("remote_address")));
						$t->set_var("identifier", htmlspecialchars($r->get_value("identifier")));
						$t->set_var("environment", htmlspecialchars($r->get_value("environment")));
					}
					$t->parse("admin_message", false);

					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					$admin_message = get_translation($admin_message);
					va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers, $attachments);
				}

				$r->empty_values();
				if (strlen(get_session("session_user_id"))) {
					$r->set_value("user_name", get_session("session_user_name"));
					$r->set_value("user_email", get_session("session_user_email"));
				}
				
			}
			else
			{
				$errors = true;
				if (!strlen($r->errors)) {
					$t->parse("db_error", false);
				}
				set_session("session_rnd", "");
			}
		}
	} elseif (strlen(get_session("session_user_id"))) {
		$r->set_value("user_name", get_session("session_user_name"));
		$r->set_value("user_email", get_session("session_user_email"));
	}

	$t->set_var("user_name_class", $user_name_class);
	$t->set_var("user_email_class", $user_email_class);
	$t->set_var("dep_class", $dep_class);
	$t->set_var("product_class", $product_class);
	$t->set_var("type_class", $type_class);
	$t->set_var("summary_class", $summary_class);
	$t->set_var("description_class", $description_class);	
	$t->set_var("validation_class", $validation_class);

	foreach ($pp as $id => $pp_row) {
		$param_name = "pp_" . $pp_row["property_id"];
		if ($r->parameter_exists($param_name)) {
			$r->change_property($param_name, SHOW, false);
		}
	}

	$r->set_parameters();

	if ($errors) {
		$t->parse("support_errors", false);
	}

	if (!$errors && $action) {
		$t->parse("support_thanks", false);
	}

	$intro_text = get_translation(get_setting_value($support_settings, "intro_text", ""));
	$intro_text = get_currency_message($intro_text, $currency);
	if ($intro_text) {
		$t->set_var("intro_text", $intro_text);
		$t->parse("intro_block", false);
	}

	$properties_ids = "";
	foreach ($sections as $section_id => $section_name) {
		$t->set_var("profile_section", "");
		$t->set_var("profile_properties", "");
		$section_properties = 0;

		// show custom options
		if (sizeof($pp) > 0)
		{
			for ($pn = 0; $pn < sizeof($pp); $pn++) {
				$section_properties++;
				$property_id = $pp[$pn]["property_id"];
				$param_name = "pp_" . $property_id;
				$property_order  = $pp[$pn]["property_order"];
				$property_name_initial = $pp[$pn]["property_name"];
				$property_name = get_translation($property_name_initial);
				$property_description = $pp[$pn]["property_description"];
				$default_value = $pp[$pn]["default_value"];
				$property_style = $pp[$pn]["property_style"];
				$control_type = $pp[$pn]["control_type"];
				$control_style = $pp[$pn]["control_style"];
				$property_required = $pp[$pn]["required"];
				$before_name_html = $pp[$pn]["before_name_html"];
				$after_name_html = $pp[$pn]["after_name_html"];
				$before_control_html = $pp[$pn]["before_control_html"];
				$after_control_html = $pp[$pn]["after_control_html"];
				$onchange_code = $pp[$pn]["onchange_code"];
				$onclick_code = $pp[$pn]["onclick_code"];
				$control_code = $pp[$pn]["control_code"];
				$validation_regexp = $pp[$pn]["validation_regexp"];
				$regexp_error = $pp[$pn]["regexp_error"];
				$options_values_sql = $pp[$pn]["options_values_sql"];
				if (isset($pp[$pn]["property_class"])){
					$property_class = $pp[$pn]["property_class"];
				} else {
					$property_class = "normal";
				}

				if (strlen($properties_ids)) { $properties_ids .= ","; }
				$properties_ids .= $property_id;

				$property_control  = "";
				$property_control .= "<input type=\"hidden\" name=\"pp_name_" . $property_id . "\"";
				$property_control .= " value=\"" . strip_tags($property_name) . "\">";
				$property_control .= "<input type=\"hidden\" name=\"pp_required_" . $property_id . "\"";
				$property_control .= " value=\"" . intval($property_required) . "\">";
				$property_control .= "<input type=\"hidden\" name=\"pp_control_" . $property_id . "\"";
				$property_control .= " value=\"" . strtoupper($control_type) . "\">";

				if ($options_values_sql) {
					$sql = $options_values_sql;
				} else {
					$sql  = " SELECT * FROM " . $table_prefix . "support_custom_values ";
					$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER) . " AND hide_value=0";
					$sql .= " ORDER BY property_value_id ";
				}
				if (strtoupper($control_type) == "LISTBOX") 
				{
					$selected_value = $r->get_value($param_name);
					$properties_values = "<option value=\"\">" . SELECT_MSG . " " . $property_name . "</option>" . $eol;
					$db->query($sql);
					while ($db->next_record())
					{
						if ($options_values_sql) {
							$property_value_id = $db->f(0);
							$property_value = get_translation($db->f(1));
						} else {
							$property_value_id = $db->f("property_value_id");
							$property_value = get_translation($db->f("property_value"));
						}
						$is_default_value = $db->f("is_default_value");
						$property_selected  = "";
						if (strlen($action) || $user_id) {
							if ($selected_value == $property_value_id) {
								$property_selected  = "selected ";
							}
						} elseif ($is_default_value) {
							$property_selected  = "selected ";
						}

						$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($property_value_id) . "\">";
						$properties_values .= htmlspecialchars($property_value);
						$properties_values .= "</option>" . $eol;
					}
					$property_control .= $before_control_html;
					$property_control .= "<select name=\"pp_" . $property_id . "\" ";
					if ($onchange_code) { $property_control .= " onChange=\"" . $onchange_code. "\""; }
					if ($onclick_code) { $property_control .= " onClick=\"" . $onclick_code . "\""; }
					if ($control_code) { $property_control .= " " . $control_code . " "; }
					if ($control_style) { $property_control .= " style=\"" . $control_style . "\""; }
					$property_control .= ">" . $properties_values . "</select>";
					$property_control .= $after_control_html;
				} 
				elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") 
				{
					$is_radio = (strtoupper($control_type) == "RADIOBUTTON");
					$selected_value = array();
					if ($is_radio) {
						$selected_value[] = $r->get_value($param_name);
					} else {
						$selected_value = $r->get_value($param_name);
					}

					$input_type = $is_radio ? "radio" : "checkbox";
					$property_control .= "<span";
					if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
					$property_control .= ">";
					$value_number = 0;
					$db->query($sql);
					while ($db->next_record())
					{
						$value_number++;
						if ($options_values_sql) {
							$property_value_id = $db->f(0);
							$property_value = get_translation($db->f(1));
						} else {
							$property_value_id = $db->f("property_value_id");
							$property_value = get_translation($db->f("property_value"));
						}
						$is_default_value = $db->f("is_default_value");
						$property_checked = "";
						$property_control .= $before_control_html;
						if (strlen($action) || $user_id) {
							if (is_array($selected_value) && in_array($property_value_id, $selected_value)) {
								$property_checked = "checked ";
							}
						} elseif ($is_default_value) {
							$property_checked = "checked ";
						}

						$control_name = ($is_radio) ? ("pp_".$property_id) : ("pp_".$property_id."_".$value_number);
						$property_control .= "<input type=\"" . $input_type . "\" name=\"" . $control_name . "\" ". $property_checked;
						$property_control .= "value=\"" . htmlspecialchars($property_value_id) . "\" ";
						if ($onclick_code) {
							$control_onclick_code = str_replace("{option_value}", $property_value, $onclick_code);
							$property_control .= " onClick=\"" . $control_onclick_code. "\"";
						}
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">";
						$property_control .= $property_value;
						$property_control .= $after_control_html;
					}
					$property_control .= "</span>";
					if (!$is_radio) {
						$property_control .= "<input type=\"hidden\" name=\"pp_".$property_id."\" value=\"".$value_number."\">";
					}
				} 
				elseif (strtoupper($control_type) == "TEXTBOX") 
				{
					if (strlen($action) || $user_id) {
						$control_value = $r->get_value($param_name);
					} else {
						$control_value = $default_value;
					}
					$property_control .= $before_control_html;
					$property_control .= "<input type=\"text\" name=\"pp_" . $property_id . "\"";
					if ($control_style) { $property_control .= " style=\"" . $control_style . "\""; }
					if ($onclick_code) { $property_control .= " onClick=\"" . $onclick_code . "\""; }
					if ($onchange_code) { $property_control .= " onChange=\"" . $onchange_code . "\""; }
					if ($control_code) { $property_control .= " " . $control_code . " "; }
					$property_control .= " value=\"". htmlspecialchars($control_value) . "\">";
					$property_control .= $after_control_html;
				} 
				elseif (strtoupper($control_type) == "TEXTAREA") 
				{
					if (strlen($action) || $user_id) {
						$control_value = $r->get_value($param_name);
					} else {
						$control_value = $default_value;
					}
					$property_control .= $before_control_html;
					$property_control .= "<textarea name=\"pp_" . $property_id . "\"";
					if ($control_style) { $property_control .= " style=\"" . $control_style . "\""; }
					if ($onclick_code) { $property_control .= " onClick=\"" . $onclick_code . "\""; }
					if ($onchange_code) { $property_control .= " onChange=\"" . $onchange_code . "\""; }
					if ($control_code) { $property_control .= " " . $control_code . " "; }
					$property_control .= ">". htmlspecialchars($control_value) ."</textarea>";
					$property_control .= $after_control_html;
				} 
				else 
				{
					$property_control .= $before_control_html;
					if ($property_required) {
						$property_control .= "<input type=\"hidden\" name=\"pp_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
					}
					$property_control .= "<span";
					if ($control_style) { $property_control .= " style=\"" . $control_style . "\""; }
					if ($onclick_code) { $property_control .= " onClick=\"" . $onclick_code . "\""; }
					if ($onchange_code) { $property_control .= " onChange=\"" . $onchange_code . "\""; }
					if ($control_code) { $property_control .= " " . $control_code . " "; }
					$property_control .= ">" . get_translation($default_value) . "</span>";
					$property_control .= $after_control_html;
				}

				$t->set_var("property_id", $property_id);
				$t->set_var("property_name", $before_name_html . $property_name . $after_name_html);
				$t->set_var("property_style", $property_style);
				$t->set_var("property_class", $property_class);
				$t->set_var("property_control", $property_control);
				if ($property_required) {
					$t->set_var("property_required", "*");
				} else {
					$t->set_var("property_required", "");
				}

				$t->parse("profile_properties", true);
			}

			$t->set_var("properties_ids", $properties_ids);
		}
	}

	// check attachments
	$attachments_files = "";
	if ($attachments_users_allowed && $user_id) 
	{
		$sql  = " SELECT attachment_id, file_name, file_path, date_added ";
		$sql .= " FROM " . $table_prefix . "support_attachments ";
		$sql .= " WHERE support_id=0 ";
		$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
		$sql .= " AND message_id=0 ";
		$sql .= " AND attachment_status=0 ";
		$db->query($sql);
		while ($db->next_record()) {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$date_added = $db->f("date_added", DATETIME);
			$attachment_vc = md5($attachment_id . $date_added[3].$date_added[4].$date_added[5]);
			$filesize = filesize($filepath);
			if ($attachments_files) { $attachments_files .= "; "; }
			$attachments_files .= "<a href=\"support_attachment.php?atid=" . $attachment_id . "&vc=" . $attachment_vc . "\" target=\"_blank\">" . $filename . "</a> (" . get_nice_bytes($filesize) . ")";
		}
		if ($attachments_files) {
			$t->set_var("attached_files", $attachments_files);
			$t->set_var("attachments_class", "display: block;");
		} else {
			$t->set_var("attachments_class", "display: none;");
		}
		$t->parse("attachments_block", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

function update_support_properties($pp,$r,$support_id)
{
	global $db, $table_prefix;

	foreach ($pp as $id => $data) {
		$property_id =$data["property_id"];
		$param_name = "pp_" . $property_id;
		$values = array();
		if ($r->get_property_value($param_name, CONTROL_TYPE) == CHECKBOXLIST) {
			$values = $r->get_value($param_name);
		} else {
			$values[] = $r->get_value($param_name);
		}
		if (is_array($values)) {
			for ($i = 0; $i < sizeof($values); $i++) {
				$property_value = $values[$i];
				if (strlen($property_value)) {
					$sql  = " INSERT INTO " . $table_prefix . "support_properties ";
					$sql .= " (support_id, property_id, property_value) VALUES (";
					$sql .= $db->tosql($support_id, INTEGER) . ", ";
					$sql .= $db->tosql($property_id, INTEGER) . ", ";
					$sql .= $db->tosql($property_value, TEXT) . ") ";
					$db->query($sql);
				}
			}
		}
	}
}

?>