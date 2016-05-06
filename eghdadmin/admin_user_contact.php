<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_contact.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("users_groups");

	$type_id = get_param("type_id");
	$setting_type = "user_contact_" . $type_id;
	$sql = " SELECT type_name FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$type_name = get_translation($db->f("type_name"));
	} else {
		header ("Location: admin_user_types.php");
		exit;
	}

	$tab = get_param("tab");
	if (!$tab) { $tab = "contact"; }

	$validation_types = 
		array( 
			array(2, FOR_ALL_USERS_MSG), array(1, UNREGISTERED_USER_ONLY_MSG), array(0, NOT_USED_MSG)
		);

	$message_types =
		array(
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$html_editors =
		array(
			array(1, WYSIWYG_HTML_EDITOR_MSG),
			array(0, TEXTAREA_EDITOR_MSG)
		);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_user_contact.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href",              "admin.php");
	$t->set_var("admin_users_href",        "admin_users.php");
	$t->set_var("admin_user_types_href",   "admin_user_types.php");
	$t->set_var("admin_user_type_href",    "admin_user_type.php");
	$t->set_var("admin_user_contact_href", "admin_user_contact.php");
	$t->set_var("admin_user_contact_help_href", "admin_user_contact_help.php");
	$t->set_var("admin_email_help_href",   "admin_email_help.php");
	$t->set_var("user_home_href",          "user_home.php");
	$t->set_var("type_id",   $type_id);
	$t->set_var("type_name", $type_name);
	$t->set_var("HIDE_ADD_BUTTON_MSG", str_replace("{ADD_TO_CART_MSG}", ADD_TO_CART_MSG, HIDE_ADD_BUTTON_MSG));

	$r = new VA_Record($table_prefix . "global_settings");

	$r->add_radio("use_random_image", TEXT, $validation_types);

	// email notification settings
	$r->add_checkbox("admin_notification", INTEGER);
	$r->add_textbox("admin_email", TEXT);
	$r->add_textbox("admin_mail_from", TEXT);
	$r->add_textbox("cc_emails", TEXT);
	$r->add_textbox("admin_mail_bcc", TEXT);
	$r->add_textbox("admin_mail_reply_to", TEXT);
	$r->add_textbox("admin_mail_return_path", TEXT);
	$r->add_textbox("admin_subject", TEXT);
	$r->add_radio("admin_message_type", TEXT, $message_types);
	$r->add_textbox("admin_message", TEXT);

	$r->add_checkbox("user_notification", INTEGER);
	$r->add_textbox("user_mail_from", TEXT);
	$r->add_textbox("user_mail_cc", TEXT);
	$r->add_textbox("user_mail_bcc", TEXT);
	$r->add_textbox("user_mail_reply_to", TEXT);
	$r->add_textbox("user_mail_return_path", TEXT);
	$r->add_textbox("user_subject", TEXT);
	$r->add_radio("user_message_type", TEXT, $message_types);
	$r->add_textbox("user_message", TEXT);

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if (!strlen($return_page)) {
		$return_page = "admin_user_types.php";
	}
	$t->set_var("rp", htmlspecialchars($return_page));

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$r->validate();

		if (!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			foreach($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get user_product settings
	{
		foreach($r->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT) . " AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$r->set_value($key, get_db_value($sql));
		}
	}

	$r->set_parameters();
	$t->set_var("type_id", htmlspecialchars($type_id));

	// set styles for tabs
	$tabs = array("contact" => CONTACT_FORM_SETTINGS_MSG);
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);
		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		$t->parse("tabs", $tab_title);
	}
	$t->set_var("tab", $tab);

	// multisites
	if ($sitelist) {
		$sites_all = 0;
		$sql = " SELECT sites_all FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$sites_all = $db->f("sites_all");
		}
		
		$sql  = " SELECT s.site_id, s.site_name FROM " . $table_prefix . "sites AS s ";
		if (!$sites_all) {
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites AS t ON s.site_id=t.site_id ";
			$sql .= " WHERE t.type_id=" . $db->tosql($type_id, INTEGER);	
		}
		$sql .= " ORDER BY s.site_id ";
		$sites   = get_db_values($sql, "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>