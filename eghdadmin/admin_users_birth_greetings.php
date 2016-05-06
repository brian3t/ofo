<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_users_birth_greetings.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit (300);
	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/parameters.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	// Database Initialize
	$db = new VA_SQL();
	$db->DBType      = $db_type;
	$db->DBDatabase  = $db_name;
	$db->DBHost      = $db_host;
	$db->DBPort      = $db_port;
	$db->DBUser      = $db_user;
	$db->DBPassword  = $db_password;
	$db->DBPersistent= $db_persistent;

	check_admin_security("site_users");

	$types = array();
	$sql = " SELECT type_id, type_name FROM " . $table_prefix . "user_types WHERE is_active=1 ";
	$db->query($sql);
	while ($db->next_record()) {
		$type_id = $db->f("type_id");
		$type_name = $db->f("type_name");
		$types[$type_id] = $type_name;
	}


	$eol = get_eol();
	$current_date = va_time();
	// initiliaze template if it doesn't exists
	if (!isset($t)) {
		$t = new VA_Template($settings["admin_templates_dir"]);
	}

	$birth_errors = ""; $birth_messages = "";
	$users_births = 0;
	$emails_sent = 0; $emails_errors = 0;
	$sms_sent = 0; $sms_errors = 0;

	foreach ($types as $type_id => $type_name) {
		$setting_type = "user_profile_" . $type_id;

		$user_profile = array();
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		if ($multisites_version) {
			$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
			$sql .= "ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$user_profile[$db->f("setting_name")] = $db->f("setting_value");
		}

		$user_birth_mail_greetings = get_setting_value($user_profile, "user_birth_mail_greetings", 0);
		$user_birth_sms_greetings = get_setting_value($user_profile, "user_birth_sms_greetings", 0);

		if ($user_birth_mail_greetings || $user_birth_sms_greetings) {
			$sql  = " SELECT * FROM " . $table_prefix . "users ";
			$sql .= " WHERE is_approved=1 ";
			$sql .= " AND user_type_id=" . $db->tosql($type_id, INTEGER);
			$sql .= " AND birth_month=" . $db->tosql($current_date[MONTH], INTEGER);
			$sql .= " AND birth_day=" . $db->tosql($current_date[DAY], INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$users_births++;
				$is_sms_allowed = $db->f("is_sms_allowed");
				$email = $db->f("email");
				$cell_phone = $db->f("cell_phone");
				$registration_date = $db->f("registration_date", DATETIME);
				$registration_date_string = va_date($datetime_show_format, $registration_date);
				$t->set_vars($db->Record);
				$t->set_var("registration_date", $registration_date_string);

				if ($user_birth_mail_greetings && $email)
				{
					$user_subject = get_setting_value($user_profile, "user_birth_subject", "");
					$user_message = get_setting_value($user_profile, "user_birth_message", "");
					$user_subject = get_translation($user_subject);
					$user_message = get_translation($user_message);
			  
					$t->set_block("user_subject", $user_subject);
					$t->set_block("user_message", $user_message);
					$t->parse("user_subject", false);
					$t->parse("user_message", false);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($user_profile, "user_birth_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($user_profile, "user_birth_mail_cc");
					$email_headers["bcc"] = get_setting_value($user_profile, "user_birth_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($user_profile, "user_birth_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($user_profile, "user_birth_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($user_profile, "user_birth_message_type");

					$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
					$email_sent = va_mail($email, $t->get_var("user_subject"), $user_message, $email_headers);
					if ($email_sent) {
						$emails_sent++;
					} else {
						$emails_errors++;
					}
				}		 

				if ($user_birth_sms_greetings && $is_sms_allowed) 
				{
					$user_sms_recipient  = get_setting_value($user_profile, "user_birth_sms_recipient", $cell_phone);
					$user_sms_originator = get_setting_value($user_profile, "user_birth_sms_originator", "");
					$user_sms_message    = get_setting_value($user_profile, "user_birth_sms_message", "");

					$t->set_block("user_sms_recipient",  $user_sms_recipient);
					$t->set_block("user_sms_originator", $user_sms_originator);
					$t->set_block("user_sms_message",    $user_sms_message);

					$t->parse("user_sms_recipient", false);
					$t->parse("user_sms_originator", false);
					$t->parse("user_sms_message", false);

					$user_sms_recipient = $t->get_var("user_sms_recipient");
					$message_sent = sms_send($user_sms_recipient, $t->get_var("user_sms_message"), $t->get_var("user_sms_originator"));
					if ($email_sent) {
						$sms_sent++;
					} else if ($user_sms_recipient) {
						$sms_errors++;
					}
				}		 
			}
		}
	}

	$birth_messages .= str_replace("{users_births}", $users_births, BIRTH_USERS_CELEBRATING_MSG);
	if ($emails_sent) {
		$birth_messages .= str_replace("{emails_sent}", $emails_sent, BIRTH_EMAILS_SENT_MSG);
	} 
	if ($sms_sent) {
		$birth_messages .= str_replace("{sms_sent}", $sms_sent, BIRTH_SMS_SENT_MSG);
	} 
	if ($emails_errors) {
		$birth_errors  = str_replace("{emails_errors}", $emails_errors, BIRTH_ERRORS_OCCURED_MSG);"$emails_errors email errors occurred.<br>";
	} 
	if ($sms_errors) {
		$birth_errors .= str_replace("{sms_errors}", $sms_errors, BIRTH_SMS_ERRORS_MSG);
	} 

?>