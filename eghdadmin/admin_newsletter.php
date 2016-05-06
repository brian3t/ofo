<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_newsletter.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");
	
	check_admin_security("newsletter");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_newsletter.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	$html_editor = get_setting_value($settings, "html_editor", 1);
	$t->set_var("html_editor", 			$html_editor);		
	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_newsletters_href", "admin_newsletter.php");
	$t->set_var("admin_newsletter_href",  "admin_newsletter.php");
	$t->set_var("datetime_format", join("", $datetime_edit_format));
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", NEWSLETTER_MSG, CONFIRM_DELETE_MSG));
	$mail_types =
		array(
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);
	
	$r = new VA_Record($table_prefix . "newsletters");
	$return_page = "admin_newsletters.php";
	
	$r->add_where("newsletter_id", INTEGER);
	
	$r->add_checkbox("is_active", INTEGER);
	$r->add_textbox("newsletter_date", DATETIME, DATE_MSG);
	$r->change_property("newsletter_date", VALUE_MASK, $datetime_edit_format);
	$r->change_property("newsletter_date", REQUIRED, true);
	$r->add_radio("mail_type", INTEGER, $mail_types, TYPE_MSG);
	$r->add_textbox("mail_from", TEXT, EMAIL_FROM_MSG);
	$r->add_textbox("mail_reply_to", TEXT, EMAIL_REPLY_TO_MSG);
	$r->add_textbox("mail_return_path", TEXT, EMAIL_RETURN_PATH_MSG);
	
	$r->add_textbox("newsletter_subject", TEXT, EMAIL_SUBJECT_MSG);
	$r->change_property("newsletter_subject", REQUIRED, true);
	$r->add_textbox("newsletter_body", TEXT, EMAIL_SUBJECT_MSG);
	
	$r->add_textbox("added_by", INTEGER);
	$r->change_property("added_by", USE_IN_UPDATE, false);
	$r->add_textbox("added_date", DATETIME);
	$r->change_property("added_date", USE_IN_UPDATE, false);
	
	$r->add_textbox("edited_by", INTEGER);
	$r->add_textbox("edited_date", DATETIME);
	
	$r->add_textbox("emails_left", INTEGER);
	$r->change_property("emails_left", USE_IN_UPDATE, false);
	$r->add_textbox("emails_sent", INTEGER);
	$r->change_property("emails_sent", USE_IN_UPDATE, false);
	$r->add_textbox("is_sent", INTEGER);
	$r->change_property("is_sent", USE_IN_UPDATE, false);
	$r->add_textbox("is_prepared", INTEGER);
	
	$r->add_checkbox("subscribed_recipients", TEXT);
	$r->add_checkbox("users_recipients", TEXT);
	$r->add_checkbox("orders_recipients", TEXT);
	$r->add_checkbox("admins_recipients", TEXT);
	
	$r->add_textbox("custom_recipients", TEXT, EMAIL_SUBJECT_MSG);
	$r->add_textbox("custom_recipients_file", TEXT, EMAIL_SUBJECT_MSG);
	$r->change_property("custom_recipients", USE_IN_SELECT, false);
	$r->change_property("custom_recipients_file", USE_IN_SELECT, false);
	$r->change_property("custom_recipients", USE_IN_UPDATE, false);
	$r->change_property("custom_recipients_file", USE_IN_UPDATE, false);
	$r->change_property("custom_recipients", USE_IN_INSERT, false);
	$r->change_property("custom_recipients_file", USE_IN_INSERT, false);
	
	$r->get_form_parameters();
	$custom_recipients = $r->get_value("custom_recipients");
	$custom_emails_check_error = false;
	$operation = get_param("operation");
	$newsletter_id = get_param("newsletter_id");
	
	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $newsletter_id)
		{
			$r->delete_record();
			$sql  = " DELETE FROM " . $table_prefix . "newsletters_emails ";
			$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
			$db->query($sql);
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "load")
		{
			$errors = "";
	
			if (isset($_FILES)) {
				$tmp_name = $_FILES["custom_recipients_file"]["tmp_name"];
				$filesize = $_FILES["custom_recipients_file"]["size"];
				$upload_error = isset($_FILES["custom_recipients_file"]["error"]) ? $_FILES["custom_recipients_file"]["error"] : "";
			} else {
				$tmp_name = $HTTP_POST_FILES["custom_recipients_file"]["tmp_name"];
				$filesize = $HTTP_POST_FILES["custom_recipients_file"]["size"];
				$upload_error = isset($HTTP_POST_FILES["custom_recipients_file"]["error"]) ? $HTTP_POST_FILES["custom_recipients_file"]["error"] : "";
			}

			if ($upload_error == 0) {
				$handle = fopen($tmp_name, "r");
	
				while (!feof($handle)) {
					$buffer = fgets($handle, 256);
					$custom_recipients .= $buffer ;
				}
				fclose($handle);
			}
			$custom_recipients_array = check_custom_recipients($custom_recipients);
			$custom_recipients = implode("\n", $custom_recipients_array)."\n";
	
		}
	
	
		if ($r->get_value("subscribed_recipients") == "1") {
			$r->set_value("subscribed_recipients", "all");
		}
	
		$users_recipients = "";
		$user_types_total = get_param("user_types_total");
		for ($i = 1; $i <= $user_types_total; $i++) {
			$user_type = get_param("user_type_" . $i);
			if (strlen($user_type)) {
				if (strlen($users_recipients)) { $users_recipients .= ","; }
				$users_recipients .= $user_type;
			}
		}
		$r->set_value("users_recipients", $users_recipients);
	
		$orders_recipients = "";
		$order_statuses_total = get_param("order_statuses_total");
		for ($i = 1; $i <= $order_statuses_total; $i++) {
			$order_status = get_param("order_status_" . $i);
			if (strlen($order_status)) {
				if (strlen($orders_recipients)) { $orders_recipients .= ","; }
				$orders_recipients .= $order_status;
			}
		}
		$r->set_value("orders_recipients", $orders_recipients);
	
		$admins_recipients = "";
		$privileges_total = get_param("privileges_total");
		for ($i = 1; $i <= $privileges_total; $i++) {
			$privilege_id = get_param("privilege_" . $i);
			if (strlen($privilege_id)) {
				if (strlen($admins_recipients)) { $admins_recipients .= ","; }
				$admins_recipients .= $privilege_id;
			}
		}
		$r->set_value("admins_recipients", $admins_recipients);

		if ($operation != "load") {
			$r->validate();
		}
	
		if (!strlen($r->errors) && $operation != "load")
		{
			$custom_recipients_array = check_custom_recipients($custom_recipients);
			if (!$custom_emails_check_error) {
				if (strlen($newsletter_id)) {
					$r->set_value("edited_by",   get_session("session_admin_id"));
					$r->set_value("edited_date", va_time());
					$r->set_value("is_prepared", 0);
					$r->update_record();
	
					$sql  = " DELETE FROM " . $table_prefix . "newsletters_emails ";
					$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
					$db->query($sql);
				} else {
					$r->set_value("added_by",   get_session("session_admin_id"));
					$r->set_value("added_date", va_time());
					$r->set_value("edited_by",   get_session("session_admin_id"));
					$r->set_value("edited_date", va_time());
					$r->set_value("emails_left", 0);
					$r->set_value("emails_sent", 0);
					$r->set_value("is_sent", 0);
					$r->set_value("is_prepared", 0);
					if ($db_type == "postgre") {
						$newsletter_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "newsletters') ");
						$r->change_property("newsletter_id", USE_IN_INSERT, true);
						$r->set_value("newsletter_id", $newsletter_id);
					}
					$r->insert_record();
					if ($db_type == "mysql") {
						$newsletter_id = get_db_value(" SELECT LAST_INSERT_ID() ");
						$r->set_value("newsletter_id", $newsletter_id);
					} elseif ($db_type == "access") {
						$newsletter_id = get_db_value(" SELECT @@IDENTITY ");
						$r->set_value("newsletter_id", $newsletter_id);
					} elseif ($db_type == "db2") {
						$newsletter_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "newsletters FROM " . $table_prefix . "newsletters");
						$r->set_value("newsletter_id", $newsletter_id);
					}
				}
	
				foreach ($custom_recipients_array as $i => $email) {
					$custom_recipients_array[$i] = "($newsletter_id,".$db->tosql($email,TEXT).",1)";
				}
				if (count($custom_recipients_array) > 0) {
					$sql  = "INSERT INTO  " . $table_prefix . "newsletters_emails (newsletter_id,user_email,is_custom) VALUES ";
					$sql .= implode(",", $custom_recipients_array);
					$db->query($sql);
				}
				header ("Location: " . $return_page);
				exit;
			} else {
				$t->parse("check_emails_errors_block");
			}
		}
	
		
	}
	elseif (strlen($newsletter_id))
	{
		$r->get_db_values();
		if ($r->get_value("subscribed_recipients") == "all") {
			$r->set_value("subscribed_recipients", 1);
		}
		if ($r->get_value("is_sent") == "1") {
			$r->errors = NEWSLETTER_SENT_MSG;
		}
		$sql = "SELECT user_email FROM " . $table_prefix . "newsletters_emails ";
		$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
		$sql .= " AND is_custom = 1";
		$db->query($sql);
		while ($db->next_record()){
			$custom_recipients .= $db->f(0)."\n";
		}
		$r->set_value("custom_recipients", $custom_recipients);
	}
	else // new record (set default values)
	{
		$r->set_value("is_active", 1);
		$r->set_value("newsletter_date", va_time());
		$r->set_value("mail_type", 1);
	}
	
	$r->set_value("custom_recipients", $custom_recipients);
	
	$users_recipients = explode(",", $r->get_value("users_recipients"));
	$orders_recipients = explode(",", $r->get_value("orders_recipients"));
	$admins_recipients = explode(",", $r->get_value("admins_recipients"));
	
		
	$index = 0;
	$sql = "SELECT * FROM " . $table_prefix . "user_types ";
	$db->query($sql);
	while ($db->next_record()) {
		$index++;
		$type_id = $db->f("type_id");
		$type_name = get_translation($db->f("type_name"));
		$user_type_checked = in_array($type_id, $users_recipients) ? " checked " : "";
		$t->set_var("index", $index);
		$t->set_var("type_id", $type_id);
		$t->set_var("user_type_checked", $user_type_checked);
		$t->set_var("type_name", $type_name);
		$t->parse("user_types", true);
	}
	$t->set_var("user_types_total", $index);
	
	$index = 0;
	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$db->query($sql);
	while ($db->next_record()) {
		$index++;
		$status_id = $db->f("status_id");
		$status_name = get_translation($db->f("status_name"));
		$order_status_checked = in_array($status_id, $orders_recipients) ? " checked " : "";
		$t->set_var("index", $index);
		$t->set_var("status_id", $status_id);
		$t->set_var("order_status_checked", $order_status_checked);
		$t->set_var("status_name", $status_name);
		$t->parse("order_statuses_cols", true);
		if ($index % 4 == 0) {
			$t->parse("order_statuses_rows", true);
			$t->set_var("order_statuses_cols", "");
		}
	}
	if ($index % 4 != 0) {
		$t->parse("order_statuses_rows", true);
	}
	$t->set_var("order_statuses_total", $index);
	
	$index = 0;
	$sql  = " SELECT privilege_id, privilege_name FROM " . $table_prefix . "admin_privileges ";
	$db->query($sql);
	while ($db->next_record()) {
		$index++;
		$privilege_id = $db->f("privilege_id");
		$privilege_name = get_translation($db->f("privilege_name"));
		$privilege_checked = in_array($privilege_id, $admins_recipients) ? " checked " : "";
		$t->set_var("index", $index);
		$t->set_var("privilege_id", $privilege_id);
		$t->set_var("privilege_checked", $privilege_checked);
		$t->set_var("privilege_name", $privilege_name);
		$t->parse("admin_types", true);
	}
	$t->set_var("privileges_total", $index);
	
	$r->set_form_parameters();
	
	if (strlen($newsletter_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);
	} else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");
	}
	
	$t->pparse("main");
	
	function check_custom_recipients($custom_recipients) 
	{
		global $custom_emails_check_error;

		$custom_recipients_array = split ("\n", $custom_recipients);
		foreach ($custom_recipients_array as $i => $email) {
			$email = trim($email);
			$lentgth = strlen($email);
			if ($lentgth > 0) {
				if ($lentgth > 6 && preg_match(EMAIL_REGEXP,$email)) {
					$custom_recipients_array[$i] = $email;
				} else {
					$custom_emails_check_error = true;
				}
			} else {
				unset($custom_recipients_array[$i]);
			}
		}
		$custom_recipients_array = array_unique($custom_recipients_array);
		return  $custom_recipients_array;
	}

?>