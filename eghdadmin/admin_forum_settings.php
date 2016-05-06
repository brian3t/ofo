<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_settings.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");

	include_once("./admin_common.php");

	check_admin_security("forum");

	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$validation_types = 
		array( 
			array(2, FOR_ALL_USERS_MSG), array(1, UNREGISTERED_USER_ONLY_MSG), array(0, NOT_USED_MSG)
		);

	$show_topic_information = 
		array( 
			array(1, ALWAYS_AT_THE_TOP_MSG), array(0, BEGIN_MESSAGES_LIST_MSG)
		);

	$show_reply_form = 
		array( 
			array(1, ABOVE_MESSAGES_LIST_MSG), array(2, BELOW_MESSAGES_LIST_MSG)
		);

	$messages_order = 
		array( 
			array(1, ASC_MSG), array(2, DESC_MSG)
		);

	$show_user_info = 
		array( 
			array(1, SHOW_USER_INFO_LEFT_MSG), array(2, SHOW_USER_INFO_TOP_MSG)
		);

	$yes_no = 
		array( 
			array(1, YES_MSG), array(0, NO_MSG)
		);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_forum_settings.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_forum_settings_href", "admin_forum_settings.php");
	$t->set_var("admin_forum_help_href", "admin_forum_help.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");

	$r = new VA_Record($table_prefix . "global_settings");

	// set up html form parameters
	$r->add_radio("use_random_image", TEXT, $validation_types);
	$r->add_radio("topic_information", TEXT, $show_topic_information);
	$r->add_radio("reply_form", TEXT, $show_reply_form);
	$r->add_radio("sort_messages", TEXT, $messages_order);
	$r->add_radio("user_info", TEXT, $show_user_info);
	$r->add_radio("allow_bbcode", INTEGER, $yes_no);
	$r->add_textbox("user_no_image", TEXT);

	// attachments settings
	$r->add_textbox("attachments_dir", TEXT);
	$r->add_textbox("attachments_users_mask", TEXT);

	// icons settings
	$r->add_checkbox("icons_enable", INTEGER);
	$r->add_textbox("icons_cols", INTEGER, ICONS_FORM_COLUMNS_MSG);
	$r->add_textbox("icons_limit", INTEGER, ICONS_FORM_LIMIT_MSG);

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
	if(!strlen($return_page)) $return_page = "admin.php";
	$errors = "";

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		if (!function_exists('imagecreate') && (($r->get_value("use_random_image") == 2) || ($r->get_value("use_random_image") == 1 ))) {	
		  $errors .= RANDOM_IMAGE_VALIDATION_ERROR_MSG;
			$r->set_value("use_random_image",0);
		} 

		if(!strlen($errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='forum'";
			$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			foreach($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql("forum", TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get order_info settings
	{
		foreach($r->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql("forum", TEXT) . " AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$r->set_value($key, get_db_value($sql));
		}
	}

	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	if(strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}
	else
	{
		$t->set_var("errors", "");
	}
	
	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>