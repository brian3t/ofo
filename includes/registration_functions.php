<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  registration_functions.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	function send_product_registration_emails($registration_id, $is_approved, $just_placed = false) {
		global $t, $table_prefix, $db;
		
		get_all_product_registration_variables($registration_id);
				
		$registration_settings = array();
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type=" . $db->tosql("registration", TEXT);
		if (isset($site_id)) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			$sql .= " ORDER BY site_id ASC ";
		} else {
			$sql .= " AND site_id=1 ";
		}
		$db->query($sql);
		while($db->next_record()) {
			$registration_settings[$db->f("setting_name")] = $db->f("setting_value");
		}
		
		if ($just_placed) {	
			format_product_registration_emails("placed_");
		}
		if ($is_approved) {
			format_product_registration_emails("approved_");
		} elseif (!$just_placed) {
			format_product_registration_emails("declined_");
		}
	}
	
	function format_product_registration_emails($type) {
		global $t, $registration_settings, $settings;
		if (get_setting_value($registration_settings, $type . "admin_notification", 0)) {
			$t->set_block("admin_subject", get_setting_value($registration_settings, $type . "admin_subject"));
			$t->set_block("admin_message", get_setting_value($registration_settings, $type . "admin_message"));
			$t->parse("admin_subject", false);
			$t->parse("admin_message", false);
			$mail_type = get_setting_value($registration_settings, $type . "admin_message_type");
			$mail_to   = get_setting_value($registration_settings, $type . "admin_email", $settings["admin_email"]);
			$mail_to   = str_replace(";", ",", $mail_to);
			$email_headers = array();
			$email_headers["from"]        = get_setting_value($registration_settings, $type . "admin_mail_from", get_setting_value($settings, "admin_email", ""));
			$email_headers["cc"]          = get_setting_value($registration_settings, $type . "admin_mail_cc");
			$email_headers["bcc"]         = get_setting_value($registration_settings, $type . "admin_mail_bcc");
			$email_headers["reply_to"]    = get_setting_value($registration_settings, $type . "admin_mail_reply_to");
			$email_headers["return_path"] = get_setting_value($registration_settings, $type . "admin_mail_return_path");
			$email_headers["mail_type"] = $mail_type;				
			$admin_message = $t->get_var("admin_message");
			if ($mail_type) {
				$admin_message = nl2br(htmlspecialchars($admin_message));
			}
			$admin_message = str_replace("\r", "", 	$admin_message);
			va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
		}
		if (get_setting_value($registration_settings, $type . "user_notification", 0)) {
			$mail_to   = $t->get_var("user_email");
			if ($mail_to) {
				$t->set_block("user_subject", get_setting_value($registration_settings, $type . "user_subject"));
				$t->set_block("user_message", get_setting_value($registration_settings, $type . "user_message"));
				$t->parse("user_subject", false);
				$t->parse("user_message", false);
				$mail_type = get_setting_value($registration_settings, $type . "user_message_type");				
				$email_headers = array();
				$email_headers["from"]        = get_setting_value($registration_settings, $type . "user_mail_from", get_setting_value($settings, "user_email", ""));
				$email_headers["cc"]          = get_setting_value($registration_settings, $type . "user_mail_cc");
				$email_headers["bcc"]         = get_setting_value($registration_settings, $type . "user_mail_bcc");
				$email_headers["reply_to"]    = get_setting_value($registration_settings, $type . "user_mail_reply_to");
				$email_headers["return_path"] = get_setting_value($registration_settings, $type . "user_mail_return_path");
				$email_headers["mail_type"] = $mail_type;					
				$user_message = $t->get_var("user_message");
				if ($mail_type) {
					$user_message = nl2br(htmlspecialchars($user_message));
				}
				$user_message = str_replace("\r", "", 	$user_message);
				va_mail($mail_to, $t->get_var("user_subject"), $user_message, $email_headers);
			}
		}
	}
	
	function get_all_product_registration_variables($registration_id) {
		global $t, $table_prefix, $db, $datetime_show_format;
		
		$dbd = new VA_SQL();
		$dbd->DBType       = $db->DBType;
		$dbd->DBDatabase   = $db->DBDatabase;
		$dbd->DBUser       = $db->DBUser;
		$dbd->DBPassword   = $db->DBPassword;
		$dbd->DBHost       = $db->DBHost;
		$dbd->DBPort       = $db->DBPort;
		$dbd->DBPersistent = $db->DBPersistent;
	
		$sql  = " SELECT reg.*, c.category_name, it.item_name AS item_id_name, u.name, u.first_name, u.last_name, u.email ";
		$sql .= " FROM (((" . $table_prefix . "registration_list reg ";
		$sql .= " LEFT JOIN " . $table_prefix . "registration_categories c ON c.category_id = reg.category_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "registration_items it ON it.item_id = reg.item_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id = reg.user_id) ";
		$sql .= " WHERE registration_id=" . $db->tosql($registration_id, INTEGER, true, false);
		$db->query($sql);
		if (!$db->next_record()) {
			return false;
		}
		$t->set_var("registration_id", $registration_id);
				
		$user_id     = $db->f("user_id");
		$t->set_var("user_id", $user_id);
		$user_name   = $db->f("name");
		if(!strlen($user_name)) {
			$user_name = $db->f("first_name") . " " . $db->f("last_name");
		}
		$t->set_var("user_name", $user_name);
		$t->set_var("user_email", $db->f("email"));
		
		$is_approved = $db->f("is_approved");
		if ($is_approved) {
			$t->set_var("is_approved", IS_APPROVED_MSG);
		} else {
			$t->set_var("is_approved", NOT_APPROVED_MSG);
		}
		
		$category_id = $db->f("category_id");
		$t->set_var("category_id", $category_id);
		if ($category_id) {
			$t->set_var("category_name", get_translation($db->f("category_name")));
		} else {
			$t->set_var("category_name", TOP_CATEGORY_MSG);
		}
		$t->set_var("item_id_name",    get_translation($db->f("item_id_name")));
		$t->set_var("item_code",       $db->f("item_code"));	
		$t->set_var("item_name",       $db->f("item_name"));
		$t->set_var("serial_number",   $db->f("serial_number"));
		$t->set_var("invoice_number",  $db->f("invoice_number"));
		$t->set_var("store_name",      $db->f("store_name"));
		$t->set_var("purchased_day",   $db->f("purchased_day"));
		$t->set_var("purchased_month", $db->f("purchased_month"));
		$t->set_var("purchased_year",  $db->f("purchased_year"));
		$date_added    = va_date($datetime_show_format, $db->f("date_added", DATETIME));
		$date_modified = va_date($datetime_show_format, $db->f("date_modified", DATETIME));
		$t->set_var("date_added",      $date_added);
		$t->set_var("date_modified",   $date_modified);

		$custom_properties = array();
		$sql  = " SELECT op.property_id, ocp.property_name, op.property_value, ";
		$sql .= " ocp.control_type ";
		$sql .= " FROM (" . $table_prefix . "registration_properties op ";
		$sql .= " INNER JOIN " . $table_prefix . "registration_custom_properties ocp ON op.property_id=ocp.property_id)";
		$sql .= " WHERE op.registration_id=" . $db->tosql($registration_id, INTEGER);
		$sql .= " ORDER BY ocp.property_order, op.property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_id    = $db->f("property_id");
			$property_name  = $db->f("property_name");
			$property_value = $db->f("property_value");
			$control_type   = $db->f("control_type");
			if(($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && is_numeric($property_value)) {
				$sql  = " SELECT property_value FROM " . $table_prefix . "registration_custom_values ";
				$sql .= " WHERE property_value_id=" . $db->tosql($property_value, INTEGER);
				$dbd->query($sql);
				if ($dbd->next_record()) {
					$property_value = $dbd->f("property_value");
				}
			}
			if (isset($custom_properties[$property_id])) {
				$custom_properties[$property_id]["value"] .= "; " . $property_value;
			} else {
				$custom_properties[$property_id] = array(
					"name" => $property_name, "value" => $property_value,
				);
			}
		}
		if ($custom_properties) {
			$t->sparse("custom_properties_title", true);			
			foreach ($custom_properties as $property_id => $property_values) {
				$property_name = $property_values["name"];
				$property_value = $property_values["value"];
				$t->set_var("property_id", $property_id);
				$t->set_var("property_name", $property_name);
				$t->set_var("property_value", $property_value);
				$t->set_var("field_" . $property_id, $property_value);
				$t->sparse("custom_properties", true);
			}
		}
		return true;
	}
	
	function show_custom_properties($pp, $action = "", $properties_block_name = "custom_properties") {
		global $user_id;
		global $t, $r, $table_prefix, $eol, $db;
		
		if (!$action) $action = get_param("action");
		$properties_ids = "";
		if (sizeof($pp) > 0){
			for ($pn = 0; $pn < sizeof($pp); $pn++) {
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
					$sql  = " SELECT * FROM " . $table_prefix . "registration_custom_values ";
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

				$t->parse($properties_block_name, true);
			}
		}
		
		return $properties_ids;
	}
?>