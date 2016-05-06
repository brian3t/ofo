<?php
include_once($root_folder_path . "includes/forums_functions.php");
	
function forum_topic_new($block_name, $forum_id)
{
	global $t, $db, $site_id, $db_type, $r, $table_prefix;
	global $settings, $page_settings;
	global $date_show_format, $datetime_show_format;

	if (!VA_Forums::check_permissions($forum_id, POST_TOPICS_PERM)) {
		return;
	}

	$eol = get_eol();
	$errors = false; 
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='forum'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$forum_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$user_id = strlen(get_session("session_user_id")) ? get_session("session_user_id") : 0;
	$user_type_id = get_session("session_user_type_id");
	$use_random_image = get_setting_value($forum_settings, "use_random_image", 0);
	$icons_enable = get_setting_value($forum_settings, "icons_enable", 0); 
	$icons_cols = get_setting_value($forum_settings, "icons_cols", 4); 
	$icons_limit = get_setting_value($forum_settings, "icons_limit", 16); 

	if (($use_random_image == 2) || ($use_random_image == 1 && !$user_id)) { 
		$use_validation = true;
	} else {
		$use_validation = false;
	}

	$t->set_file("block_body", "block_forum_topic_new.html");

	$t->set_var("site_url", $settings["site_url"]);

	$provide_info_message = str_replace("{button_name}", ADD_TOPIC_BUTTON, PROVIDE_INFO_MSG);
	$t->set_var("PROVIDE_INFO_MSG", $provide_info_message);

	$forum_url = new VA_URL("forum.php");
	$forum_url->add_parameter("u", REQUEST, "u");
	$forum_url->add_parameter("forum_id", REQUEST, "forum_id");

	$t->set_var("forum_topic_new_href", "forum_topic_new.php");
	$t->set_var("user_forum_attachments_url", "user_forum_attachments.php");
	$t->set_var("icon_select_href", "icon_select.php");
	$t->set_var("rnd", va_timestamp());

	$r = new VA_Record($table_prefix . "forum");

	$recommended = 
		array( 
			array(1, "Yes"), array(0, "No")
			);

	$r->add_where("thread_id", INTEGER);
	$r->add_hidden("u", INTEGER);
	$r->add_textbox("user_id", INTEGER);
	$r->add_textbox("priority_id", INTEGER);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("user_name", TEXT, TOPIC_NICKNAME_FIELD);
	$r->change_property("user_name", TRIM, true);
	$r->change_property("user_name", REQUIRED, true);
	$r->change_property("user_name", REGEXP_MASK, NICKNAME_REGEXP);
	$r->change_property("user_name", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("user_email", TEXT, TOPIC_EMAIL_FIELD);
	$r->change_property("user_email", TRIM, true);
	$r->change_property("user_email", REQUIRED, true);
	$r->add_textbox("topic", TEXT, TOPIC_NAME_FIELD);
	$r->change_property("topic", TRIM, true);
	$r->change_property("topic", REQUIRED, true);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->add_textbox("description", TEXT, TOPIC_MESSAGE_FIELD);
	$r->change_property("description", TRIM, true);
	$r->change_property("description", REQUIRED, true);
	$r->add_checkbox("email_notification", INTEGER);
	$r->add_textbox("views", INTEGER);
	$r->change_property("views", USE_IN_UPDATE, false);
	$r->add_textbox("replies", INTEGER);
	$r->change_property("replies", USE_IN_UPDATE, false);
	$r->add_textbox("date_added", DATETIME);
	$r->add_textbox("date_modified", DATETIME);	
	$r->add_textbox("thread_updated", DATETIME);
	$r->add_textbox("forum_id", INTEGER);
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
	$topic_class = "normal"; 
	$description_class = "normal"; 
	$validation_class = "normal"; 

	$operation = get_param("operation");
	$rnd = get_param("rnd");
	$filter = get_param("filter");
	$remote_address = get_ip();
	$return_page = $forum_url->get_url();
	$session_rnd = get_session("session_rnd");
	  
	$r->get_form_values();
	if ($user_id) {	
		$user_info = get_session("session_user_info");
		$user_nickname = get_setting_value($user_info, "nickname", "");
		$user_email = get_setting_value($user_info, "email", "");
		if (strlen($user_nickname)) {
			$r->set_value("user_name", $user_nickname);
			$r->change_property("user_name", SHOW, false);
		}
		if (strlen($user_email)) {
			$r->set_value("user_email", $user_email);
			$r->change_property("user_email", SHOW, false);
		}
	}

	$button = get_param("button");
	if($operation && $rnd != $session_rnd)
	{
		set_session("session_rnd", $rnd);	  

		if($r->is_empty("user_email") || !preg_match(EMAIL_REGEXP, $r->get_value("user_email"))) {
			$user_email_class = "error"; 
		}
		if($r->is_empty("user_name") || !preg_match(NICKNAME_REGEXP, $r->get_value("user_name"))) {
			$user_name_class = "error"; 
		}
		if($r->is_empty("topic")) {
			$topic_class = "error"; 
		}
		if($r->is_empty("description")) {
			$description_class = "error"; 
		}

		$r->validate();
		if ($r->errors) {
			$errors = true;
		}

		if ($use_validation) { 
			$validated_number = check_image_validation($r->get_value("validation_number"));
			if(!$validated_number) {
				$validation_class = "error"; $errors = true;
				$r->errors .= str_replace("{field_name}", VALIDATION_CODE_FIELD, VALIDATION_MESSAGE);
			} else if ($errors || $button == PREVIEW_BUTTON) {
				// saved validated number for following submits	
				set_session("session_validation_number", $validated_number);
			}
		}
		
		if(!$errors) {
			if (check_black_ip()) {
				$r->errors .= BLACK_IP_MSG; $errors = true;
			} else if (check_banned_content($r->get_value("description"))) {
				$r->errors .= BANNED_CONTENT_MSG; $errors = true;
			}
		}
		
		if ($errors) {
			set_session("session_rnd", "");
		}

		if (!$errors && $button == PREVIEW_BUTTON) {
			$description = get_param("description");
			$t->set_var("description", process_message($description, $icons_enable));
			$t->parse("topic_preview", false);
		} else if(!$errors) {
			set_friendly_url();
			if ($db_type == "postgre") {
				$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "forum') ";
				$thread_id = get_db_value($sql);
				$r->set_value("thread_id", $thread_id);
				$r->change_property("thread_id", USE_IN_INSERT, true);
			}
			$date_added = va_time();

			$sql  = " SELECT priority_id FROM " . $table_prefix . "forum_priorities WHERE is_default=1 ";
			$db->query($sql);
			if($db->next_record()) {
				$priority_id = $db->f("priority_id");	
			} else {
				$priority_id = 0;
			}

			$user_id = strlen(get_session("session_user_id")) ? get_session("session_user_id") : 0;

			$r->set_value("priority_id", $priority_id);
			$r->set_value("user_id", $user_id);
			$r->set_value("date_added", $date_added);
			$r->set_value("date_modified", $date_added);
			$r->set_value("thread_updated", $date_added);
			$r->set_value("remote_address", $remote_address);
			$r->set_value("forum_id", $forum_id);
			$r->set_value("views", 0);
			$r->set_value("replies", 0);
			if($r->insert_record())
			{
				if ($db_type == "mysql") {
					$sql = " SELECT LAST_INSERT_ID() ";
					$thread_id = get_db_value($sql);
					$r->set_value("thread_id", $thread_id);
				} else if ($db_type == "access") {
					$sql = " SELECT @@IDENTITY ";
					$thread_id = get_db_value($sql);
					$r->set_value("thread_id", $thread_id);
				} else if ($db_type == "db2") {
					$thread_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "forum FROM " . $table_prefix . "forum");
					$r->set_value("thread_id", $thread_id);
				}
				$sql  = " UPDATE " . $table_prefix . "forum_list ";
				$sql .= " SET last_post_added=" . $db->tosql($date_added, DATETIME) . ", ";
				$sql .= " last_post_user_id=" . $db->tosql($user_id, INTEGER) . ", ";
				$sql .= " last_post_thread_id=" . $db->tosql($thread_id, INTEGER) . ", ";
				$sql .= " last_post_admin_id=0, last_post_message_id=0, threads_number=threads_number+1";
				$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
				$db->query($sql);

				// update attachments
				$sql  = " UPDATE " . $table_prefix . "forum_attachments ";
				$sql .= " SET thread_id=" . $db->tosql($thread_id, INTEGER);
				$sql .= " , attachment_status=1 ";
				$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
				if (!$user_id) {
					$sql .= " AND session_id=" . $db->tosql(session_id(), TEXT);
				}
				$sql .= " AND forum_id=" . $db->tosql($forum_id, INTEGER);
				$sql .= " AND thread_id=0 ";
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);

				// check attachments
				$attachments = array();								
				$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "forum_attachments ";
				$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
				$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
				if (!$user_id) {
					$sql .= " AND session_id=" . $db->tosql(session_id(), TEXT);
				}
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=1 ";
				$db->query($sql);
				while ($db->next_record()) {
					$filename = $db->f("file_name");
					$filepath = $db->f("file_path");
					$attachments[] = array($filename, $filepath);
				}

				setcookie("cookie_forum_nick", $r->get_value("user_name"), va_timestamp() + 3600 * 24 * 366);  
				setcookie("cookie_forum_email", $r->get_value("user_email"), va_timestamp() + 3600 * 24 * 366);

				// send email notification to admin
				if(isset($forum_settings["admin_notification"]))
				{
					$t->set_block("admin_subject", $forum_settings["admin_subject"]);
					$t->set_block("admin_message", $forum_settings["admin_message"]);
					$user_name = $r->get_value("user_name");
					$user_email = $r->get_value("user_email");
					$topic = $r->get_value("topic");
					$description = $r->get_value("description");
					$date_added_string = va_date($datetime_show_format, $date_added);

					// set variables for email
					$r->set_parameters();
					$t->set_var("date_added", $date_added_string);
					$t->set_var("date_updated", $date_added_string);
					$t->set_var("thread_added", $date_added_string);
					$t->set_var("thread_modified", $date_added_string);
					$t->set_var("thread_updated", $date_added_string);
					$t->set_var("message_added", $date_added_string);

					$t->set_var("nickname", $r->get_value("user_name"));
					$t->set_var("thread_user_name", $r->get_value("user_name"));
					$t->set_var("message_user_name", $r->get_value("user_name"));

					$t->set_var("message_remote_address", $r->get_value("remote_address"));
					$t->set_var("thread_remote_address", $r->get_value("remote_address"));

					$t->set_var("thread_user_email", $r->get_value("user_email"));
					$t->set_var("message_user_email", $r->get_value("user_email"));

					$t->set_var("topic_description", $r->get_value("description"));
					$t->set_var("topic_message", $r->get_value("description"));
					$t->set_var("message", $r->get_value("description"));
					$t->set_var("message_text", $r->get_value("description"));
			  
					$t->set_var("user_name", $user_name);
					$t->set_var("user_email", $user_email);
					$t->set_var("topic", $topic);
					$t->set_var("description", $description);
					$t->parse("admin_subject", false);
					$mail_type = get_setting_value($forum_settings, "admin_message_type");
					if ($mail_type) {
						$t->set_var("user_name", htmlspecialchars($user_name));
						$t->set_var("user_email", htmlspecialchars($user_email));
						$t->set_var("topic", htmlspecialchars($topic));
						$t->set_var("description", nl2br(htmlspecialchars($description)));
					}
					$t->parse("admin_message", false);

					$mail_to = get_setting_value($forum_settings, "admin_email", $settings["admin_email"]);
					$mail_to = str_replace(";", ",", $mail_to);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($forum_settings, "admin_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($forum_settings, "cc_emails");
					$email_headers["bcc"] = get_setting_value($forum_settings, "admin_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($forum_settings, "admin_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($forum_settings, "admin_mail_return_path");
					$email_headers["mail_type"] = $mail_type;

					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers, $attachments);
				}

				$r->empty_values();
				
				header("Location: " . $return_page);
				exit;

			}
			else
			{
				$errors = true;
				$r->errors = DATABASE_ERROR_MSG;
				set_session("session_rnd", "");
			}
		}
	} else if (!$operation) {
		$r->set_value("user_name", get_cookie("cookie_forum_nick"));
		$r->set_value("user_email", get_cookie("cookie_forum_email"));
	}

	$t->set_var("user_name_class", $user_name_class);
	$t->set_var("user_email_class", $user_email_class);
	$t->set_var("topic_class", $topic_class);
	$t->set_var("description_class", $description_class);
	$t->set_var("validation_class", $validation_class);

	$r->set_parameters();

	if(!$errors && $operation && $button == ADD_TOPIC_BUTTON) {
		$t->parse("forum_thanks", false);
	}

	// check attachments
	$attachments_files = "";
	if (VA_Forums::check_permissions($forum_id, POST_ATTACHMENTS_PERM)) {
		$sql  = " SELECT attachment_id, file_name, file_path, date_added ";
		$sql .= " FROM " . $table_prefix . "forum_attachments ";
		$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
		if (!$user_id) {
			$sql .= " AND session_id=" . $db->tosql(session_id(), TEXT);
		}
		$sql .= " AND thread_id=0 ";
		$sql .= " AND message_id=0 ";
		$sql .= " AND attachment_status=0 ";
		$db->query($sql);
		while ($db->next_record()) {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$date_added = $db->f("date_added", DATETIME);
			$attachment_vc = md5($attachment_id . $date_added[3].$date_added[4].$date_added[5]);
			$filesize = filesize($filepath);
			if ($attachments_files) { $attachments_files .= "; "; }
			$attachments_files .= "<a href=\"forum_attachment.php?atid=" .$attachment_id. "&vc=".$attachment_vc."\" target=\"_blank\">" . $filename . "</a> (" . get_nice_bytes($filesize) . ")";
		}
		if ($attachments_files) {
			$t->set_var("attached_files", $attachments_files);
			$t->set_var("attachments_class", "display: block;");
		} else {
			$t->set_var("attachments_class", "display: none;");
		}
		$t->parse("attachments_block", false);
	}

	// parse icons
	if ($icons_enable) {
		parse_icons("icons", $icons_cols, $icons_limit);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>