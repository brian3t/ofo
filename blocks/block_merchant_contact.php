<?php

function merchant_contact($block_name, $merchant_id, $merchant_type_id, $merchant_name, $merchant_email) {

	global $t, $db, $site_id, $table_prefix, $settings, $datetime_show_format, $current_page;
	
	$t->set_file("block_body", "block_merchant_contact.html");
	$errors = false;


	$contact_settings = array();
	$setting_type = "user_contact_" . $merchant_type_id;
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
		$contact_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$eol = get_eol();
	$use_random_image = get_setting_value($contact_settings, "use_random_image", 0);

	if (($use_random_image == 2) || ($use_random_image == 1 && !strlen(get_session("session_user_id")))) { 
		$use_validation = true;
	} else {
		$use_validation = false;
	}
	
	$t->set_var("site_url", $settings["site_url"]);

	$provide_info_message = str_replace("{button_name}", SEND_BUTTON, PROVIDE_INFO_MSG);
	$t->set_var("PROVIDE_INFO_MSG", $provide_info_message);

	$t->set_var("contact_href", $current_page);
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("rnd", va_timestamp());
	$t->set_var("user", htmlspecialchars($merchant_id));


	$r = new VA_Record($table_prefix . "support", "support");

	$r->add_textbox("user_name", TEXT);
	$r->change_property("user_name", TRIM, true);
	$r->add_textbox("user_email", TEXT, CONTACT_USER_EMAIL_FIELD);
	$r->change_property("user_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->change_property("user_email", TRIM, true);
	$r->add_textbox("summary", TEXT);
	$r->change_property("summary", TRIM, true);
	$r->add_textbox("description", TEXT);
	$r->change_property("description", TRIM, true);
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

	$user_name_class = "normal"; 
	$user_email_class = "normal"; 
	$summary_class = "normal"; 
	$description_class = "normal"; 	
	$validation_class = "normal"; 

	$operation = get_param("operation");
	$rnd = get_param("rnd");
	$filter = get_param("filter");
	$remote_address = get_ip();

	$session_rnd = get_session("session_rnd");

	if($operation && $rnd != $session_rnd)
	{
		set_session("session_rnd", $rnd);

		$r->get_form_values();

		if ($use_validation) { 
			if(!check_image_validation($r->get_value("validation_number"))) {
				$validation_class = "error"; $fill_error = true; $errors = true;
			}
		}

		if($r->is_empty("user_name")) {
			$user_name_class = "error"; $errors = true; 
		}
		if($r->is_empty("user_email")) {
			$user_email_class = "error"; $errors = true; 
		}
		if($r->is_empty("summary")) {
			$summary_class = "error"; $errors = true; 
		}
		if($r->is_empty("description")) {
			$description_class = "error"; $errors = true; 
		}

		if ($errors) {
			$t->parse("fill_error", false);
			set_session("session_rnd", "");
		} else {
			$r->validate();
			if (strlen($r->errors)) {
				$errors = true;
				set_session("session_rnd", "");
			}
		}

		if(!$errors)
		{
			$user_id = strlen(get_session("session_user_id")) ? get_session("session_user_id") : 0;
			$user_email = trim($r->get_value("user_email"));

			$request_sent = va_date($datetime_show_format, va_time());
			$t->set_var("request_sent", $request_sent);
			$t->set_var("remote_address", get_ip());
			$t->set_var("user_id", $user_id);
			$t->set_var("merchant_name", $merchant_name);
			$t->set_var("merchant_email", $merchant_email);
				
			// send email notification to admin
			if($contact_settings["admin_notification"])
			{
				$admin_subject = get_setting_value($contact_settings, "admin_subject", $r->get_value("summary"));
				$admin_message = get_setting_value($contact_settings, "admin_message", $r->get_value("description"));

				$t->set_block("admin_subject", $admin_subject);
				$t->set_block("admin_message", $admin_message);

				$mail_to = get_setting_value($contact_settings, "admin_email", $settings["admin_email"]);
				$mail_to = str_replace(";", ",", $mail_to);
				$email_headers = array();
				$email_headers["from"] = get_setting_value($contact_settings, "admin_mail_from", $settings["admin_email"]);
				$email_headers["cc"] = get_setting_value($contact_settings, "cc_emails");
				$email_headers["bcc"] = get_setting_value($contact_settings, "admin_mail_bcc");
				$email_headers["reply_to"] = get_setting_value($contact_settings, "admin_mail_reply_to");
				$email_headers["return_path"] = get_setting_value($contact_settings, "admin_mail_return_path");
				$email_headers["mail_type"] = get_setting_value($contact_settings, "admin_message_type");

				$t->set_var("summary", $r->get_value("summary"));
				$t->set_var("description", $r->get_value("description"));
				$t->set_var("message_text", $r->get_value("description"));
				$t->set_var("user_name", $r->get_value("user_name"));
				$t->set_var("user_email", $r->get_value("user_email"));
				$t->parse("admin_subject", false);
				if ($email_headers["mail_type"]) {
					$t->set_var("summary", htmlspecialchars($r->get_value("summary")));
					$t->set_var("description", nl2br(htmlspecialchars($r->get_value("description"))));
					$t->set_var("message_text", nl2br(htmlspecialchars($r->get_value("description"))));
					$t->set_var("user_name", htmlspecialchars($r->get_value("user_name")));
					$t->set_var("user_email", htmlspecialchars($r->get_value("user_email")));
				}
				$t->parse("admin_message", false);

				$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
				va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
			}

			// send email notification to merchant 
			if ($contact_settings["user_notification"])
			{
				$user_subject = get_setting_value($contact_settings, "user_subject", $r->get_value("summary"));
				$user_message = get_setting_value($contact_settings, "user_message", $r->get_value("description"));

				$t->set_block("user_subject", $user_subject);
				$t->set_block("user_message", $user_message);

				$email_headers = array();
				$email_headers["from"] = get_setting_value($contact_settings, "user_mail_from", $settings["admin_email"]);
				$email_headers["cc"] = get_setting_value($contact_settings, "user_mail_cc");
				$email_headers["bcc"] = get_setting_value($contact_settings, "user_mail_bcc");
				$email_headers["reply_to"] = get_setting_value($contact_settings, "user_mail_reply_to");
				$email_headers["return_path"] = get_setting_value($contact_settings, "user_mail_return_path");
				$email_headers["mail_type"] = get_setting_value($contact_settings, "user_message_type");

				$t->set_var("summary", $r->get_value("summary"));
				$t->set_var("description", $r->get_value("description"));
				$t->set_var("message_text", $r->get_value("description"));
				$t->set_var("user_name", $r->get_value("user_name"));
				$t->set_var("user_email", $r->get_value("user_email"));
				$t->parse("user_subject", false);
				if ($email_headers["mail_type"]) {
					$t->set_var("summary", htmlspecialchars($r->get_value("summary")));
					$t->set_var("description", nl2br(htmlspecialchars($r->get_value("description"))));
					$t->set_var("message_text", nl2br(htmlspecialchars($r->get_value("description"))));
					$t->set_var("user_name", htmlspecialchars($r->get_value("user_name")));
					$t->set_var("user_email", htmlspecialchars($r->get_value("user_email")));
				}
				$t->parse("user_message", false);

				$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
				va_mail($merchant_email, $t->get_var("user_subject"), $user_message, $email_headers);
			}

			$r->empty_values();
			if(strlen(get_session("session_user_id"))) {
				$r->set_value("user_name", get_session("session_user_name"));
				$r->set_value("user_email", get_session("session_user_email"));
			}
		}
	} else if(strlen(get_session("session_user_id"))) {
		$r->set_value("user_name", get_session("session_user_name"));
		$r->set_value("user_email", get_session("session_user_email"));
	}

	$t->set_var("user_name_class", $user_name_class);
	$t->set_var("user_email_class", $user_email_class);
	$t->set_var("summary_class", $summary_class);
	$t->set_var("description_class", $description_class);	
	$t->set_var("validation_class", $validation_class);

	$r->set_parameters();

	if($errors) {
		$t->parse("contact_errors", false);
	}

	if(!$errors && $operation) {
		$t->parse("contact_request_sent", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}
?>