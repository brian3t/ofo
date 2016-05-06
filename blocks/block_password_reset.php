<?php

function password_reset_form($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $datetime_show_format, $date_show_format ;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_password_reset.html");
	$t->set_var("reset_password_href", "reset_password.php");
	$t->set_var("user_login_href",     "user_login.php");

	$em = get_param("em");
	$rc = get_param("rc");
	$operation = get_param("operation");
	$errors = "";
	$password_updated = false;

	if (strlen($em) && strlen($rc)) {
		$sql  = " SELECT u.user_id, u.reset_password_date ";
		$sql .= " FROM (";
		if (isset($site_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "users u";
		$sql .=	" LEFT JOIN " . $table_prefix . "user_types ut ON ut.type_id=u.user_type_id) ";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites s ON s.type_id = ut.type_id) ";		
		}
		$sql .= " WHERE u.email=" . $db->tosql($em, TEXT);
		$sql .= " AND u.reset_password_code=" . $db->tosql($rc, TEXT);
		if (isset($site_id)) {
			$sql .= " AND (ut.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true. false) . ") ";
		} else {
			$sql .= " AND ut.sites_all=1 ";
		}
		$db->query($sql);
		if ($db->next_record()) {
			$user_id = $db->f("user_id");
			$expiry_date_db = $db->f("reset_password_date", DATETIME);
			if(is_array($expiry_date_db)) {
				$expiry_date = va_date($date_show_format, $expiry_date_db);
				$expiry_date_ts = mktime ($expiry_date_db[HOUR], $expiry_date_db[MINUTE], $expiry_date_db[SECOND], $expiry_date_db[MONTH], $expiry_date_db[DAY], $expiry_date_db[YEAR]);
				$current_date_ts = va_timestamp();
				if($current_date_ts > $expiry_date_ts) {
					$errors = RESET_PASSWORD_EXPIRY_MSG;
				}
			} else {
				$errors = RESET_PASSWORD_EXPIRY_MSG;
			}
		} else {
			$errors = RESET_PASSWORD_PARAMS_MSG;
		}
	} else {
		$errors = RESET_PASSWORD_REQUIRE_MSG;
	}

	$r = new VA_Record($table_prefix . "users");

	$r->add_where("user_id", INTEGER);
	$r->add_textbox("modified_date", DATETIME);
	$r->add_textbox("password", TEXT, NEW_PASS_FIELD);
	$r->change_property("password", REQUIRED, true);
	$r->change_property("password", MIN_LENGTH, 5);
	$r->add_textbox("confirm", TEXT, CONFIRM_PASS_FIELD);
	$r->change_property("confirm", USE_IN_UPDATE, false);
	$r->change_property("password", MATCHED, "confirm");
	$r->add_textbox("reset_password_code", TEXT);
	$r->add_textbox("reset_password_date", DATETIME);

	$r->add_hidden("em", TEXT);
	$r->add_hidden("rc", TEXT);

	$r->get_form_values();

	if (strlen($errors)) {
		$r->errors = $errors;
	} else if (strlen($operation)) {
		if($operation == "cancel")
		{            
			header("Location: " . $return_page);
			exit;      
		}            
		             
		$r->validate();

		if(!strlen($r->errors))
		{
			$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
			if ($password_encrypt) {
				$r->set_value("password", md5($r->get_value("password")));
			}
			$r->set_value("user_id", $user_id);
			$r->set_value("modified_date", va_time());
			$r->set_value("reset_password_date", va_time());
			$r->update_record();

			$password_updated = true;
		}
	}

	$r->set_parameters();

	if (strlen($errors)) {
		$t->set_var("password_block", "");
		$t->set_var("confirm_block", "");
	} else if ($password_updated) {
		$t->set_var("password_block", "");
		$t->set_var("confirm_block", "");
		$t->parse("password_updated", false);
	} else {
		$t->parse("buttons", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}
?>