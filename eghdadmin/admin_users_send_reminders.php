<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_users_send_reminders.php                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit(300);
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
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
	
	$weekdays_values = array(
		array(1, MONDAY),
		array(2, TUESDAY),
		array(4, WEDNESDAY),
		array(8, THURSDAY),
		array(16, FRIDAY),
		array(32, SATURDAY),
		array(64, SUNDAY)
	);
	
	$types = array();
	$sql = " SELECT type_id, type_name FROM " . $table_prefix . "user_types WHERE is_active=1 ";
	$db->query($sql);
	while ($db->next_record()) {
		$type_id = $db->f("type_id");
		$type_name = $db->f("type_name");
		$types[$type_id] = $type_name;
	}

	$eol = get_eol();
	$today_date = va_time();
	$today_ts = va_timestamp();
	$weekday_index = date("w", $today_ts);
	if ($weekday_index > 0) {
		$weekday_number = $weekdays_values[($weekday_index - 1)][0];
	} else {
		$weekday_number = 64;
	}
	
	// initiliaze template if it doesn't exists
	if (!isset($t)) {
		$t = new VA_Template($settings["admin_templates_dir"]);
	}

	$reminders_errors = ""; $reminders_messages = "";
	$users_reminders = 0;
	$emails_sent = 0; $emails_errors = 0;
	$sms_sent = 0; $sms_errors = 0;

	foreach ($types as $type_id => $type_name) 
	{
		$setting_type = "user_profile_" . $type_id;
		$user_profile = array();
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		$db->query($sql);
		while ($db->next_record()) {
			$user_profile[$db->f("setting_name")] = $db->f("setting_value");
		}

		$user_reminder_mail = get_setting_value($user_profile, "user_reminder_mail", 0);
		$user_reminder_sms = get_setting_value($user_profile, "user_reminder_sms", 0);

		if ($user_reminder_mail || $user_reminder_sms) 
		{
			$sql = "SELECT * FROM " . $table_prefix . "reminders r LEFT JOIN va_users u ON (u.user_id=r.user_id)";
			$sql .= " WHERE (r.reminder_year=" . $db->tosql($today_date[YEAR], INTEGER)." OR r.reminder_year=0) ";
			$sql .= " AND (r.reminder_month=" . $db->tosql($today_date[MONTH], INTEGER)." OR r.reminder_month=0)";
			$sql .= " AND (r.reminder_day=" . $db->tosql($today_date[DAY], INTEGER)." OR r.reminder_day=0) ";
			$sql .= " AND (r.reminder_weekdays&" . $db->tosql($weekday_number, INTEGER)." OR r.reminder_weekdays=0)";
			$sql .= " AND (r.start_date<=". $db->tosql($today_date, DATE)  ." OR r.start_date IS NULL)";
			$sql .= " AND (r.end_date>=". $db->tosql($today_date, DATE) ." OR r.end_date IS NULL)";
			$db->query($sql);
			while ($db->next_record()) {
				$users_reminders++;
				$is_sms_allowed = $db->f("is_sms_allowed");
				$email = $db->f("email");
				$cell_phone = $db->f("cell_phone");
				$registration_date = $db->f("registration_date", DATETIME);
				$registration_date_string = va_date($datetime_show_format, $registration_date);
				$t->set_vars($db->Record);
				$t->set_var("registration_date", $registration_date_string);

				if ($user_reminder_mail && $email)
				{
					$user_subject = get_setting_value($user_profile, "user_reminder_subject", "");
					$user_message = get_setting_value($user_profile, "user_reminder_message", "");
					$user_subject = get_translation($user_subject);
					$user_message = get_translation($user_message);
					$t->set_block("user_subject", $user_subject);
					$t->set_block("user_message", $user_message);
					
					$t->parse("user_subject", false);
					$t->parse("user_message", false);

					$email_headers = array();
					$email_headers["from"] = get_setting_value($user_profile, "user_reminder_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($user_profile, "user_reminder_mail_cc");
					$email_headers["bcc"] = get_setting_value($user_profile, "user_reminder_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($user_profile, "user_reminder_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($user_profile, "user_reminder_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($user_profile, "user_reminder_message_type");

					$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
					
					$email_sent = va_mail($email, $t->get_var("user_subject"), $user_message, $email_headers);
					if ($email_sent) {
						$emails_sent++;
					} else {
						$emails_errors++;
					}
				}		 

				if ($user_reminder_sms && $is_sms_allowed) 
				{
					$user_sms_recipient  = get_setting_value($user_profile, "user_reminder_sms_recipient", $cell_phone);
					$user_sms_originator = get_setting_value($user_profile, "user_reminder_sms_originator", "");
					$user_sms_message    = get_setting_value($user_profile, "user_reminder_sms_message", "");

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
					} elseif ($user_sms_recipient) {
						$sms_errors++;
					}
				}		 
			}
		}
	}

	$reminders_messages .= $users_reminders . " " . REMINDERS_AVAIL_TODAY_MSG . "<br>";
	if ($emails_sent) {
		$reminders_messages .= $emails_sent . " " . REMINDERS_SENT_MSG . "<br>";
	} 
	if ($sms_sent) {
		$reminders_messages .= $sms_sent . " " . REMINDERS_SMS_SENT_MSG . "<br>";
	} 
	if ($emails_errors) {
		$reminders_errors  = $emails_sent . " " . EMAIL_ERRORS_OCCURED_MSG . "<br>";
	} 
	if ($sms_errors) {
		$reminders_errors .= $sms_sent . " " . SMS_ERRORS_OCCURED_MSG . "<br>";
	} 

?>