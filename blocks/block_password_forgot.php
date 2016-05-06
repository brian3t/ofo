<?php

function password_forgot_form($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $datetime_show_format;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_password_forgot.html");
	$t->set_var("forgot_password_href", "forgot_password.php");

	$message_desc = "";
	$error_desc = "";
	$eol = get_eol();
	$email = get_param("email");

	if (strlen($email)) {
		if(preg_match(EMAIL_REGEXP, $email)) {
			$sql  = " SELECT u.* ";
			$sql .= " FROM (";
			if (isset($site_id)) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "users u";
			$sql .=	" LEFT JOIN " . $table_prefix . "user_types ut ON ut.type_id=u.user_type_id) ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites s ON s.type_id = ut.type_id) ";		
			}
			$sql .= " WHERE u.email=" . $db->tosql($email, TEXT);
			if (isset($site_id)) {
				$sql .= " AND (ut.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true. false) . ") ";
			} else {
				$sql .= " AND ut.sites_all=1 ";
			}
			$db->query($sql);
			if($db->next_record()) {
				$user_id = $db->f("user_id");
				$t->set_vars($db->Record);

				// prepare settings to send email
				$forgotten_password = array();
				$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
				$sql .= " WHERE setting_type='forgotten_password' ";
				if (isset($site_id)) {
					$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
					$sql .= " ORDER BY site_id ASC ";
				} else {
					$sql .= " AND site_id=1 ";
				}
				$db->query($sql);
				while($db->next_record()) {
					$forgotten_password[$db->f("setting_name")] = $db->f("setting_value");
				}

				$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
				// parse subject and body message
				if ($password_encrypt == 1) {
					$reset_time_limit = get_setting_value($forgotten_password, "md5_time_limit", 1440); // 1440 - 1 day
					$user_subject = get_setting_value($forgotten_password, "md5_subject", FORGOT_PASSWORD_MSG);
					$user_message = get_setting_value($forgotten_password, "md5_message", "{reset_password_url}");

					$reset_password_ts = va_timestamp() + ($reset_time_limit * 60); // max date when password can be reset
					srand ((double) microtime() * 1000000);
					$random_value = rand();
					$reset_password_code = substr(md5($reset_password_ts . $random_value), 0, 16);
					$reset_password_url = $settings["site_url"] . "reset_password.php?em=" . urlencode($email) . "&rc=" . $reset_password_code;
					$t->set_var("reset_password_code", $reset_password_code);
					$t->set_var("reset_password_date", va_date($datetime_show_format, $reset_password_ts));
					$t->set_var("reset_password_url", $reset_password_url);

					$sql  = " UPDATE " . $table_prefix . "users SET ";
					$sql .= " reset_password_code=" . $db->tosql($reset_password_code, TEXT) . ", ";
					$sql .= " reset_password_date=" . $db->tosql($reset_password_ts, DATETIME);
					$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
					$db->query($sql);
				} else {
					$user_subject = get_setting_value($forgotten_password, "user_subject", FORGOT_PASSWORD_MSG);
					$user_message = get_setting_value($forgotten_password, "user_message", "{password}");
				}
				$user_subject = get_translation($user_subject);
				$user_message = get_translation($user_message);
				$t->set_block("user_subject", $user_subject);
				$t->set_block("user_message", $user_message);
				$t->parse("user_subject", false);
				$t->parse("user_message", false);

				// prepare email fields
				$email_headers = array();
				$email_headers["from"] = get_setting_value($forgotten_password, "user_mail_from", $settings["admin_email"]);
				$email_headers["cc"] = get_setting_value($forgotten_password, "user_mail_cc");
				$email_headers["bcc"] = get_setting_value($forgotten_password, "user_mail_bcc");
				$email_headers["reply_to"] = get_setting_value($forgotten_password, "user_mail_reply_to");
				$email_headers["return_path"] = get_setting_value($forgotten_password, "user_mail_return_path");
				$email_headers["mail_type"] = get_setting_value($forgotten_password, "user_message_type");

				$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
				va_mail($email, $t->get_var("user_subject"), $user_message, $email_headers);

				$message_desc = FORGOT_EMAIL_SENT_MSG;
			} else {
				$error_desc = FORGOT_EMAIL_ERROR_MSG;
			}
		} else {
			$error_desc = INVALID_EMAIL_MSG;
		}
	}

	if ($message_desc) {
		$t->set_var("message_desc", $message_desc);
		$t->parse("forgot_message", false);
	}
	if ($error_desc) {
		$t->set_var("email", htmlspecialchars($email));
		$t->set_var("error_desc", $error_desc);
		$t->parse("forgot_error", false);
	} else {
		$t->set_var("email", "");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}
?>