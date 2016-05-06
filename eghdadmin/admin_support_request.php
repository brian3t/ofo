<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_request.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");

	$eol = get_eol();
	$session_admin_id = get_session("session_admin_id");

 	$t = new VA_Template($settings["admin_templates_dir"]);
 	$t->set_file("main","admin_support_request.html");
	$t->set_var("admin_support_request_href", "admin_support_request.php");
	$t->set_var("admin_support_attachments_url", "admin_support_attachments.php");

	$admin_support_url = new VA_URL("admin_support.php", false);
	$admin_support_url->add_parameter("s_w", REQUEST, "s_w");
	$admin_support_url->add_parameter("s_s", REQUEST, "s_s");
	$admin_support_url->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_support_url->add_parameter("sort_dir", REQUEST, "sort_dir");
	$return_page = $admin_support_url->get_url();
	$t->set_var("admin_support_url", $return_page);
	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_TICKET_MSG, CONFIRM_DELETE_MSG));

	$permissions = get_permissions();
	$allow_close = get_setting_value($permissions, "support_ticket_close", 0); 
	$operation = get_param("operation");
	$support_id = get_param("support_id");
	$send_mail = get_param("send_mail");
	$rp = get_param("rp");
	$return_page = (strlen($rp) && $operation != "delete") ? $rp : $return_page;

	// prepare custom options 
	$user_id = get_param("user_id");
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
			$pp[$pn]["payment_id"] = $db->f("payment_id");
			$pp[$pn]["property_description"] = $db->f("property_description");
			$pp[$pn]["default_value"] = $db->f("default_value");
			$pp[$pn]["property_style"] = $db->f("property_style");
			$pp[$pn]["section_id"] = $db->f("property_type");
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
	
	$r = new VA_Record($table_prefix . "support");
	$r->return_page = $return_page;

	$r->add_where("support_id", INTEGER);

	$r->add_hidden("s_w", TEXT);
	$r->add_hidden("s_s", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);

	$r->add_textbox("site_id", INTEGER);
	$r->change_property("site_id", USE_SQL_NULL, false);
	$r->add_textbox("user_id", INTEGER, CUSTOMER_ID_MSG);
	$r->add_textbox("user_name", TEXT, USER_NAME_MSG);
	$r->change_property("user_name", REQUIRED, true);
	$r->add_textbox("user_email", TEXT, CUSTOMER_EMAIL_MSG);
	$r->change_property("user_email", REQUIRED, true);
	$r->add_textbox("mail_cc", TEXT);
	$r->add_textbox("mail_bcc", TEXT);
	$r->add_textbox("identifier", TEXT);
	$r->add_textbox("environment", TEXT);
	$r->add_textbox("remote_address", TEXT);

	//Departments and assigned users
	$deps_users = "";
	$sql  = " SELECT sud.dep_id, sud.admin_id, a.admin_name ";
	$sql .= " FROM (" . $table_prefix . "support_users_departments sud ";
	$sql .= " INNER JOIN " . $table_prefix . "admins a ON sud.admin_id=a.admin_id) ";
	$sql .= " ORDER BY a.admin_name";
	$db->query($sql);
	if ($db->next_record()) {
		$i = 0;
		do {
			$deps_users .= "arrDepsUsers[" . $i . "] = Array(" . $db->f("dep_id") . ", " . $db->f("admin_id") . ", " . $db->tosql($db->f("admin_name"), TEXT) . ");" . $eol;
			$i++;
		} while ($db->next_record());
	}
	$t->set_var("deps_users", $deps_users);

	$number_of_products = get_db_value("SELECT COUNT(*) FROM " . $table_prefix . "support_products");
	$support_products = get_db_values("SELECT product_id, product_name FROM " . $table_prefix . "support_products", array(array("", SUPPORT_SELECT_PROD_MSG)));
	$r->add_select("support_product_id", INTEGER, $support_products, PRODUCT_MSG);
	$r->change_property("support_product_id", REQUIRED, true);
	if ($number_of_products < 2) {
		$r->parameters["support_product_id"][SHOW] = false;
		$only_product_id = get_db_value("SELECT product_id FROM " . $table_prefix . "support_products");
	}

	$admins = get_db_values("SELECT admin_id, admin_name FROM " . $table_prefix . "admins ", array(array("", SELECT_RESPONSIBLE_MSG)));
	$r->add_select("admin_id_assign_to", INTEGER, $admins);
	$r->change_property("admin_id_assign_to", USE_SQL_NULL, false);
	
	$sql  = " SELECT sd.dep_id, sd.short_title ";
	$sql .= " FROM (" . $table_prefix . "support_departments sd ";
	$sql .= " INNER JOIN " . $table_prefix . "support_users_departments sud ON sud.dep_id=sd.dep_id) ";
	$sql .= " WHERE sud.admin_id=" . $db->tosql($session_admin_id, INTEGER);
	$support_deps = get_db_values($sql, array(array("", SUPPORT_SELECT_DEP_MSG)));
	$number_of_deps = sizeof($support_deps) - 1;
	$r->add_select("dep_id", INTEGER, $support_deps, SUPPORT_DEPARTMENT_FIELD);
	$r->change_property("dep_id", REQUIRED, true);
	if ($number_of_deps < 2) {
		$only_dep_id = get_db_value("SELECT dep_id FROM " . $table_prefix . "support_departments");
	}

	$support_types = get_db_values("SELECT type_id, type_name FROM " . $table_prefix . "support_types", array(array("", SELECT_TYPE_MSG)));
	$r->add_select("support_type_id", INTEGER, $support_types, TYPE_MSG);
	$r->change_property("support_type_id", REQUIRED, true);

	$sql = " SELECT status_id, status_name, is_internal FROM " . $table_prefix . "support_statuses ";
	if (!$allow_close) {
		$sql .= " WHERE (is_closed IS NULL OR is_closed<>1)";
	}
	$sql .= " ORDER BY status_name ASC ";
	$support_statuses = array(array("", SUPPORT_SELECT_STATUS_MSG));
	$db->query($sql);
	while ($db->next_record()) {
		if ($db->f("is_internal") == "1") {
			$support_statuses[] = array($db->f("status_id"), $db->f("status_name") . " (Internal)");
		}
		else {
			$support_statuses[] = array($db->f("status_id"), $db->f("status_name"));
		}
	};
	$r->add_select("support_status_id", INTEGER, $support_statuses, STATUS_MSG);
	$r->change_property("support_status_id", REQUIRED, true);

	$support_priorities = get_db_values("SELECT priority_id, priority_name FROM " . $table_prefix . "support_priorities", array(array("", PRIORITY_MSG)));
	$default_priority = get_db_value("SELECT priority_id FROM " . $table_prefix . "support_priorities WHERE is_default=1 ");
	$r->add_select("support_priority_id", INTEGER, $support_priorities, PRIORITY_MSG);
	$r->change_property("support_priority_id", REQUIRED, true);

	$r->add_textbox("summary", TEXT, SUPPORT_SUMMARY_COLUMN);
	$r->change_property("summary", REQUIRED, true);
	$r->add_textbox("description", TEXT, DESCRIPTION_MSG);
	$r->change_property("description", REQUIRED, true);

	// editing information
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->change_property("admin_id_added_by", USE_SQL_NULL, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_IN_INSERT, false);
	$r->change_property("admin_id_modified_by", USE_SQL_NULL, false);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);

	foreach($pp as $id => $pp_row) {

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
		if ($control_type == "TEXTAREA" || $control_type == "TEXTBOX" || $control_type == "LABEL") {
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
	
	$r->get_form_values();
	$t->set_var("cur_admin_id", intval($r->get_value("admin_id_assign_to")));

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $support_id)
		{
			if (isset($permissions["support_ticket_edit"]) && $permissions["support_ticket_edit"] == 1) 
			{
				// delete attachments if available
				delete_tickets($support_id);
				header("Location: " . $return_page);
				exit;
			} else {
				$r->errors = REMOVE_TICKET_NOT_ALLOWED_MSG;
			}
		}

		if ($number_of_products == 1) {
			$r->set_value("support_product_id", $only_product_id);
		} elseif ($number_of_products < 1) {
			$r->set_value("support_product_id", 0);
		}

		$is_valid = $r->validate();
		
		if ($is_valid)
		{
			if ($multisites_version) {
				$r->set_value("site_id", $site_id);
			} else {
				$r->set_value("site_id", 1);
			}
			$r->set_value("admin_id_added_by", get_session("session_admin_id"));
			$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
			$r->set_value("date_added", va_time());
			$r->set_value("date_modified", va_time());
			
			if ($r->is_empty("user_id")) { 
				$r->set_value("user_id", 0);
				$sql  = " SELECT user_id FROM " . $table_prefix . "users ";
				$sql .= " WHERE email=" . $db->tosql($r->get_value("user_email"), TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					$r->set_value("user_id", $db->f("user_id"));
				}
			}
			if ($r->is_empty("admin_id_assign_to")) { 
				$r->set_value("admin_id_assign_to", 0);
			}
			
			$record_updated = false;
			if (strlen($r->get_value("support_id"))) {
				if (isset($permissions["support_ticket_edit"]) && $permissions["support_ticket_edit"] == 1) {
					$record_updated = $r->update_record();
				} else {
					$r->errors = UPDATE_TICKET_NOT_ALLOWED_MSG;
				}
			} else {
				if (isset($permissions["support_ticket_new"]) && $permissions["support_ticket_new"] == 1) {
					// check new ticket ID for postgre
					if ($db_type == "postgre") {
						$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "support') ";
						$support_id = get_db_value($sql);
						$r->set_value("support_id", $support_id);
						$r->change_property("support_id", USE_IN_INSERT, true);
					}
					$record_updated = $r->insert_record();
					if ($record_updated)
					{
						// check new ticket ID for MySQL, Access, DB2
						if ($db_type == "mysql") {
							$sql = " SELECT LAST_INSERT_ID() ";
							$support_id = get_db_value($sql);
						} else if ($db_type == "access") {
							$sql = " SELECT @@IDENTITY ";
							$support_id = get_db_value($sql);
						}  else if ($db_type == "db2") {
							$support_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "support FROM " . $table_prefix . "support ");
						}

						// check attachments
						$attachments = array();
						$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "support_attachments ";
						$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
						$sql .= " AND support_id=0 ";
						$sql .= " AND message_id=0 ";
						$sql .= " AND attachment_status=0 ";
						$db->query($sql);
						while ($db->next_record()) {
							$filename = $db->f("file_name");
							$filepath = $db->f("file_path");
							$attachments[] = array($filename, $filepath);
						}
          
						$sql  = " UPDATE " . $table_prefix . "support_attachments ";
						$sql .= " SET support_id=" . $db->tosql($support_id, INTEGER);
						$sql .= " , attachment_status=1 ";
						$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
						$sql .= " AND message_id=0 ";
						$sql .= " AND support_id=0 ";
						$sql .= " AND attachment_status=0 ";
						$db->query($sql);

						$support_settings = array();
						$sql = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='support'";
						if ($multisites_version) {
							$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
							$sql .= "ORDER BY site_id ASC ";
						}
						$db->query($sql);
						while ($db->next_record()) {
							$support_settings[$db->f("setting_name")] = $db->f("setting_value");
						}
						$outgoing_account = get_db_value("SELECT outgoing_account FROM " . $table_prefix . "support_departments WHERE dep_id = " . $db->tosql($r->get_value("dep_id"), INTEGER));
						
						$sql  = " SELECT s.support_id , s.user_id, s.user_name, s.remote_address, s.identifier, ";
						$sql .= " s.environment, p.product_name, st.type_name, s.summary, s.description, ";
						$sql .= " ss.status_name, ss.status_id, sp.priority_name, s.date_added, s.date_modified, ";
						$sql .= " aa.admin_name as assign_to, s.date_viewed ";
						$sql .= " FROM (((((" . $table_prefix . "support s ";
						$sql .= " LEFT JOIN " . $table_prefix . "support_products p ON p.product_id=s.support_product_id) ";
						$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id=s.support_status_id) ";
						$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id=s.support_type_id) ";
						$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id=s.support_priority_id) ";
						$sql .= " LEFT JOIN " . $table_prefix . "admins aa ON aa.admin_id=s.admin_id_assign_to) ";
						$sql .= " WHERE s.support_id=" . $db->tosql($support_id, INTEGER);
						$db->query($sql);
						if ($db->next_record())
						{
							$support_id = $db->f("support_id");
							$t->set_var("support_id", $support_id);
							$t->set_var("user_id", $db->f("user_id"));
							$t->set_var("user_name", $db->f("user_name"));
							$t->set_var("identifier", htmlspecialchars($db->f("identifier")));
							$t->set_var("environment", htmlspecialchars($db->f("environment")));
							$t->set_var("remote_address", $db->f("remote_address"));
							$t->set_var("product", $db->f("product_name"));					
							$t->set_var("assign_to", $db->f("assign_to"));
							$t->set_var("type", get_translation($db->f("type_name")));
							$current_status = strlen($db->f("status_name")) ? $db->f("status_name") : NEW_MSG;
							$t->set_var("current_status", $current_status);
							$priority = strlen($db->f("priority_name")) ? $db->f("priority_name") : "Normal";
							$t->set_var("priority", $priority);
							
							$date_added = $db->f("date_added", DATETIME);
							$request_added_string = va_date($datetime_show_format, $date_added);
							$t->set_var("request_added", $request_added_string);
							$t->set_var("message_added", $request_added_string);
										
							$date_modified = $db->f("date_modified", DATETIME);
							$date_modified_string = va_date($datetime_show_format, $date_modified);
							$t->set_var("date_modified", $date_modified_string);
					
							$summary = $db->f("summary");
							$t->set_var("summary", htmlspecialchars($summary));
							$description = $db->f("description");
							$t->set_var("description", $description);
							$t->set_var("message_text", $description);
							
							$vc = md5($support_id . $date_added[3].$date_added[4].$date_added[5]);
							$support_url = $settings["site_url"] . "support_messages.php?support_id=" . $support_id . "&vc=" . $vc;
							$t->set_var("vc", $vc);
							$t->set_var("support_url", $support_url);						
						}
						
						// send email notification to admin
						if ($support_settings["admin_notification"] && $r->get_value("admin_id_assign_to")) 
						{
							$t->set_block("admin_subject", $support_settings["admin_subject"]);
							$t->set_block("admin_message", $support_settings["admin_message"]);

							$admin_email = get_db_value("SELECT email FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql($r->get_value("admin_id_assign_to"), INTEGER));
		
							if (strlen($outgoing_account)) {
								$mail_from = $outgoing_account;
							} else {
								$mail_from = get_setting_value($support_settings, "admin_mail_from", $settings["admin_email"]);
							}
							$email_headers = array();
							$email_headers["from"] = $mail_from;
							$email_headers["cc"] = get_setting_value($support_settings, "cc_emails");
							$email_headers["bcc"] = get_setting_value($support_settings, "admin_mail_bcc");
							$email_headers["reply_to"] = get_setting_value($support_settings, "admin_mail_reply_to");
							$email_headers["return_path"] = get_setting_value($support_settings, "admin_mail_return_path");
							$email_headers["mail_type"] = get_setting_value($support_settings, "admin_message_type");
/**/
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
											$property_value = $dbd->f("property_value");
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
							
							foreach ($support_properties as $property_id => $property_values) {
								$property_name = $property_values["name"];
								$property_value = $property_values["value"];

								$t->set_var("field_name_" . $property_id, $property_name);
								$t->set_var("field_value_" . $property_id, $property_value);
								$t->set_var("field_" . $property_id, $property_value);
								$t->set_var("property_name", $property_name);
								$t->set_var("property_value", $property_value);
							} 
/**/							
							$t->parse("admin_subject", false);
							$t->parse("admin_message", false);

							$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
							va_mail($admin_email, $t->get_var("admin_subject"), $admin_message, $email_headers, $attachments);
						}
		
						// send email notification to user
						if ($send_mail)
						{
							$t->set_block("user_subject", $support_settings["user_subject"]);
							$t->set_block("user_message", $support_settings["user_message"]);

							if (strlen($outgoing_account)) {
								$mail_from = $outgoing_account;
							} else {
								$mail_from = get_setting_value($support_settings, "user_mail_from", $settings["admin_email"]);
							}
							$user_email = $r->get_value("user_email");
							$email_headers = array();
							$email_headers["from"] = $mail_from;
							$email_headers["cc"] = get_setting_value($support_settings, "user_mail_cc");
							$email_headers["bcc"] = get_setting_value($support_settings, "user_mail_bcc");
							$email_headers["reply_to"] = get_setting_value($support_settings, "user_mail_reply_to");
							$email_headers["return_path"] = get_setting_value($support_settings, "user_mail_return_path");
							$email_headers["mail_type"] = get_setting_value($support_settings, "user_message_type");
/**/
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
											$property_value = $dbd->f("property_value");
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
							
							foreach ($support_properties as $property_id => $property_values) {
								$property_name = $property_values["name"];
								$property_value = $property_values["value"];

								$t->set_var("field_name_" . $property_id, $property_name);
								$t->set_var("field_value_" . $property_id, $property_value);
								$t->set_var("field_" . $property_id, $property_value);
								$t->set_var("property_name", $property_name);
								$t->set_var("property_value", $property_value);
							} 
/**/
							$t->parse("user_subject", false);
							$t->parse("user_message", false);

							$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
							va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers, $attachments);
						}
					}
				} else {
					$r->errors = CREATE_TICKET_NOT_ALLOWED_MSG;
				}
			}
			
			update_support_properties($pp,$r,$support_id);

			if ($record_updated) {
				header("Location: " . $return_page);
				exit;
			}
		}
	} elseif (strlen($r->get_value("support_id"))) {
		$r->get_db_values();
		$t->set_var("cur_admin_id", intval($r->get_value("admin_id_assign_to")));
		if ($r->get_value("user_id") == 0) { 
			$r->set_value("user_id", "");
		}
		$t->set_var("send_mail", "");
	}
	else // new item (set default values)
	{
		if (isset($only_dep_id) && strlen($only_dep_id)) {
			$r->set_value("dep_id", $only_dep_id);
		}
		$r->set_value("support_priority_id", $default_priority);
		$t->parse("send_mail", false);
	}

	foreach($pp as $id => $pp_row) {
		$param_name = "pp_" . $pp_row["property_id"];
		if ($r->parameter_exists($param_name)) {
			$r->change_property($param_name, SHOW, false);
		}
	}
	
	$r->set_parameters();

	// begin custom options
	$properties_ids = "";
	if (sizeof($pp) > 0) 
	{
		if (!strlen($operation) && strlen($support_id)){
			get_additional_data();
		}
		for ($pn = 0; $pn < sizeof($pp); $pn++) {
				if ( 1 == 1) {
					//$section_properties++;
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
						$sql .= " WHERE property_id=" . $property_id . " AND hide_value=0";
						$sql .= " ORDER BY property_value_id ";
					}
					if (strtoupper($control_type) == "LISTBOX") {
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
							
							if ($selected_value == $property_value_id) {
								$property_selected  = "selected ";
							}
        
							$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($property_value_id) . "\">";
							$properties_values .= htmlspecialchars($property_value);
							$properties_values .= "</option>" . $eol;
						}
						$property_control .= $before_control_html;
						$property_control .= "<select name=\"pp_" . $property_id . "\" ";
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code. "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						$property_control .= ">" . $properties_values . "</select>";
						$property_control .= $after_control_html;
						
					} elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") {
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
							if (is_array($selected_value) && in_array($property_value_id, $selected_value)) {
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
					} elseif (strtoupper($control_type) == "TEXTBOX") {
						if (strlen($operation)) {
							$control_value = $r->get_value($param_name);
						} else {
							if (strlen($support_id)){
								$control_value = $r->get_value($param_name);
							} else {
								$control_value = $default_value;
							}
						}
						$property_control .= $before_control_html;
						$property_control .= "<input class=\"field\" type=\"text\" name=\"pp_" . $property_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= " value=\"". htmlspecialchars($control_value) . "\">";
						$property_control .= $after_control_html;
					} elseif (strtoupper($control_type) == "TEXTAREA") {
						if (strlen($operation)) {
							$control_value = $r->get_value($param_name);
						} else {
							//$control_value = $default_value;
							$control_value = get_db_value(" SELECT property_value FROM ". $table_prefix . "support_properties WHERE property_id=" .$db->tosql($property_id,INTEGER));
						}
						$property_control .= $before_control_html;
						$property_control .= "<textarea  class=\"field\" name=\"pp_" . $property_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">". htmlspecialchars($control_value) ."</textarea>";
						$property_control .= $after_control_html;
					} else {
							$property_control .= $before_control_html;
							if ($property_required) {
								if (!strlen($property_description)){
									$property_description = $default_value;
								}
								$property_control .= "<input type=\"hidden\" name=\"pp_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
							}
							$property_control .= "<span";
							if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
							if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
							if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
							if ($control_code) {	$property_control .= " " . $control_code . " "; }
							$property_control .= ">" . get_translation($default_value) . "</span>";
							$property_control .= $after_control_html;
							$custom_options[$property_id] = array($property_order, $property_name_initial, $default_value);
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
					
					$t->parse("support_properties", true);

				}
			}

		$t->set_var("properties_ids", $properties_ids);
	}
	// end custom options

	
	if (strlen($support_id)) {
		if (isset($permissions["support_ticket_edit"]) && $permissions["support_ticket_edit"] == 1) {
			$t->parse("update_button", false);
			$t->parse("delete", false);	
		}
	} else {
		if (isset($permissions["support_ticket_new"]) && $permissions["support_ticket_new"] == 1) {
			$t->parse("add_button", false);
		}
	}

	// check attachments
	if (!$support_id) {
		$attachments_files = "";
		$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "support_attachments ";
		$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$sql .= " AND support_id=0 ";
		$sql .= " AND message_id=0 ";
		$sql .= " AND attachment_status=0 ";
		$db->query($sql);
		while ($db->next_record()) {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$is_file_exists = file_exists($filepath);
			if (!$is_file_exists && file_exists("../" . $filepath)) {
				$filepath = "../" . $filepath;
			}
			$filesize = filesize($filepath);
			if ($attachments_files) { $attachments_files .= "; "; }
			$attachments_files .= "<a href=\"admin_support_attachment.php?atid=" .$attachment_id. "\" target=\"_blank\">" . $filename . "</a> (" . get_nice_bytes($filesize) . ")";
		}
		if ($attachments_files) {
			$t->set_var("attached_files", $attachments_files);
			$t->set_var("attachments_class", "display: block;");
		} else {
			$t->set_var("attachments_class", "display: none;");
		}
		$t->parse("attachments_block", false);
	}


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");
	
	function update_support_properties($pp,$r,$support_id)
	{
		global $db, $table_prefix;

		$sql  = " DELETE FROM " . $table_prefix . "support_properties ";
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$db->query($sql);
		
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

	function get_additional_data()
	{
		global $r, $pp, $db, $table_prefix, $support_id, $control_type;
  
		$orders_properties = array();
		$sql  = " SELECT op.property_id, op.property_value ";
		$sql .= " ,op.support_property_id";
		$sql .= " FROM (" . $table_prefix . "support_properties op ";
		$sql .= " INNER JOIN " . $table_prefix . "support_custom_properties ocp ON op.property_id=ocp.property_id)";
		$sql .= " WHERE op.support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " ORDER BY op.property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_id   = $db->f("property_id");
			$property_value = $db->f("property_value");
			$param_name = "pp_" . $property_id;

			if ($r->parameter_exists($param_name)) {
				if(($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && !is_numeric($property_value)) {
					$property_value = explode(";", $property_value);
				} else {
					$property_value = array($property_value);
				}
				for ($op = 0; $op < sizeof($property_value); $op++) {
					$option_value = $property_value[$op];
					$orders_properties[$property_id][] = array(
						"value" => $option_value
					);
				}
			}
		}

		foreach($orders_properties as $property_id => $property_values) {
			$param_name = "pp_" . $property_id;
			foreach ($property_values as $option_id => $option_data) {
				//$control_type = $option_data["control"];
				$option_value = $option_data["value"];
				// check value from the description
				if(($control_type == "CHECKBOXLIST" || $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && !is_numeric($option_value)) {
					$sql  = " SELECT property_value_id FROM " . $table_prefix . "support_custom_values ";
					$sql .= " WHERE property_value=" . $db->tosql(trim($option_value), TEXT);
					$db->query($sql);
					if ($db->next_record()) {
						$option_value = $db->f("property_value_id");
					}
				}
				$r->set_value($param_name, $option_value);
			}
		}
	}
	
?>