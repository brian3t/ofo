<?php
include_once($root_folder_path . "includes/forums_functions.php");
	
function forum_show_topic($block_name, $forum_id, $page_friendly_url = "", $page_friendly_params = array())
{

	global $t, $r, $db, $site_id, $db_type, $table_prefix;
	global $datetime_show_format, $settings;
	global $html_title, $meta_description, $meta_keywords;

	$eol = get_eol();
	$user_id = get_session("session_user_id");
	if (!$user_id) $user_id = 0;
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$session_rnd = get_session("session_rnd");
	$action = get_param("action");
	$operation = get_param("operation");
	$rnd = get_param("rnd");
	$button = get_param("button");
	$thread_id = get_param("thread_id");
	$action = get_param("action");
	$category_id = get_param("category_id");
	$message_text = get_param("message_text");

	if ($operation == "update" && $user_id && $rnd != $session_rnd) {
		set_session("session_rnd", $rnd);
		$message_id = get_param("message_id");
		$text_edit = get_param("text_edit");
		$date_modified = va_time();
		
		if (strval($message_id) == "0") {
			// update topic information
			$sql  = " UPDATE " . $table_prefix . "forum ";
			$sql .= " SET description=" . $db->tosql($text_edit, TEXT);
			$sql .= " , date_modified=" . $db->tosql($date_modified, DATETIME);
			$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
			$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
			$sql .= " AND (admin_id_modified_by IS NULL OR admin_id_modified_by=0) ";
			$db->query($sql);
		} else if ($message_id) {
			// update message
			$sql  = " UPDATE " . $table_prefix . "forum_messages ";
			$sql .= " SET message_text=" . $db->tosql($text_edit, TEXT);
			$sql .= " , date_modified=" . $db->tosql($date_modified, DATETIME);
			$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
			$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER);
			$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
			$sql .= " AND (admin_id_modified_by IS NULL OR admin_id_modified_by=0) ";
			$db->query($sql);
		}
	}


	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$remove_parameters = $page_friendly_params;
		$forum_topic_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
		$remove_parameters = array();
		$forum_topic_page = get_custom_friendly_url("forum_topic.php");
	}

	$t->set_file("block_body","block_forum_topic.html");
	$t->set_var("site_url", $settings["site_url"]);

	$forum_thread_url = new VA_URL(get_custom_friendly_url("forum_topic.php"));
	$forum_thread_url->add_parameter("u", REQUEST, "u");
	$forum_thread_url->add_parameter("thread_id", REQUEST, "thread_id");
	$forum_thread_url->add_parameter("page", REQUEST, "page");
	$return_page = $forum_thread_url->get_url();

	$t->set_var("user_home_href",    get_custom_friendly_url("user_home.php"));
	$t->set_var("forum_thread_href", get_custom_friendly_url("forum_topic.php"));
	$t->set_var("icon_select_href",  get_custom_friendly_url("icon_select.php"));
	$t->set_var("user_forum_attachments_url", "user_forum_attachments.php");
	$t->set_var("rnd", va_timestamp());
	$t->set_var("thread_id", $thread_id);
	
	$errors = "";

	if (!strlen($thread_id)) {
		$errors = TOPIC_MISS_ID_ERROR;
	}

	// get forum settings
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

	$use_random_image = get_setting_value($forum_settings, "use_random_image", 0); 
	if (($use_random_image == 2) || ($use_random_image == 1 && !$user_id)) { 
		$use_validation = true;
	} else {
		$use_validation = false;
	}
	$reply_form        = get_setting_value($forum_settings, "reply_form", 1); 
	$topic_information = get_setting_value($forum_settings, "topic_information", 1); 
	$sort_messages     = get_setting_value($forum_settings, "sort_messages", 1); 
	$user_images       = get_setting_value($forum_settings, "user_images", 1); 
	$forum_user_info   = get_setting_value($forum_settings, "user_info", 1);
	$user_no_image     = get_setting_value($forum_settings, "user_no_image", "");
	$allow_bbcode    = get_setting_value($forum_settings, "allow_bbcode", 0);
		
	$icons_enable    = get_setting_value($forum_settings, "icons_enable", 0); 
	$icons_cols      = get_setting_value($forum_settings, "icons_cols", 4); 
	$icons_limit     = get_setting_value($forum_settings, "icons_limit", 16);
	
	$forum_id = 0;

	$sql  = " SELECT f.admin_id_added_by, f.admin_id_modified_by, f.user_id, f.user_name, f.user_email,  ";
	$sql .= " f.remote_address, f.topic, f.description, f.email_notification, ";
	$sql .= " f.date_added, f.date_modified, f.thread_updated, f.views, f.replies, ";
	$sql .= " fl.forum_id ";
	$sql .= " FROM (" . $table_prefix . "forum f ";
	$sql .= " INNER JOIN " . $table_prefix . "forum_list fl ON f.forum_id=fl.forum_id) ";
	$sql .= " WHERE f.thread_id=" . $db->tosql($thread_id, INTEGER);
	$db->query($sql);
	if($db->next_record() && !strlen($errors))
	{
		$thread_admin_id = $db->f("admin_id_added_by");
		$thread_admin_id_modified_by = $db->f("admin_id_modified_by");

		$thread_admin_image = $db->f("admin_image");

		$thread_user_id = $db->f("user_id");
		$thread_user_name = $db->f("user_name");
		$thread_user_email = $db->f("user_email");
		$thread_user_image = $db->f("user_image");
		$date_updated = $db->f("thread_updated", DATETIME);
		$thread_date_added = $db->f("date_added", DATETIME);
		$thread_date_modified = $db->f("date_modified", DATETIME);

		$topic = $db->f("topic");
		$topic_description = $db->f("description");
		$email_notification = $db->f("email_notification");

		$views = intval($db->f("views"));
		$replies = intval($db->f("replies"));
		$thread_remote_address = $db->f("remote_address");

		$forum_id = $db->f("forum_id");

		$thread_user_name .= " (" . GUEST_MSG . ")";
		$thread_user_class = "forumGuest";
		$thread_personal_image = "";
		$t->set_var("user_id", $thread_user_id);
		$t->set_var("thread_remote_address", $thread_remote_address);
		// topic author
		$t->set_var("thread_user_name", htmlspecialchars($thread_user_name));
		$t->set_var("thread_user_class", "forumGuest");
		if ($thread_user_id) {
			$sql  = " SELECT login, nickname, email, personal_image FROM " . $table_prefix . "users ";
			$sql .= " WHERE user_id=" . $db->tosql($thread_user_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$thread_personal_image = $db->f("personal_image");
				if ($db->f("email")) { $thread_user_email = $db->f("email"); }
				$thread_user_name = $db->f("nickname");
				if (!strlen($thread_user_name)) { $thread_user_name = $db->f("login"); }
				$thread_user_class = "forumUser";
				$t->set_var("thread_user_class", "forumUser");
				$t->set_var("thread_user_name", htmlspecialchars($thread_user_name));
			}
		} else if ($thread_admin_id) {
			$sql  = " SELECT admin_name, nickname, email, personal_image FROM " . $table_prefix . "admins ";
			$sql .= " WHERE admin_id=" . $db->tosql($thread_admin_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$thread_personal_image = $db->f("personal_image");
				if ($db->f("email")) { $thread_user_email = $db->f("email"); }
				$thread_user_name = $db->f("nickname");
				if (!strlen($thread_user_name)) { $thread_user_name = $db->f("admin_name"); }
				$thread_user_class = "forumAdmin";
				$t->set_var("thread_user_class", "forumAdmin");
				$t->set_var("thread_user_name", htmlspecialchars($thread_user_name));
			}
		}
		$t->set_var("thread_user_email", htmlspecialchars($thread_user_email));
		if (!$thread_personal_image && $forum_user_info == 1) {
			$thread_personal_image = $user_no_image;
		}

		$date_updated_string = va_date($datetime_show_format, $date_updated);
		$t->set_var("thread_updated", $date_updated_string);

		$date_added_string = va_date($datetime_show_format, $thread_date_added);
		$thread_added_string = $date_added_string;
		$t->set_var("thread_added", $date_added_string);

		$html_title = strip_tags($topic);
		$t->set_var("topic", htmlspecialchars($topic));
		$last_message = $topic_description;
		$meta_description = $topic_description;
		$t->set_var("topic_description", nl2br(htmlspecialchars($topic_description)));

		$t->set_var("views", $views);
		$t->set_var("replies", $replies);

		$topic_viewed = get_session("session_topics_viewed");
		if (!isset($topic_viewed[$thread_id])) {
			$sql  = " UPDATE " . $table_prefix . "forum SET views=" . $db->tosql(($views + 1), INTEGER);
			$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
			$db->query($sql);

			$topic_viewed[$thread_id] = true;
			set_session("session_topics_viewed", $topic_viewed);
		}
		
	}
	else if(!strlen($errors))
	{
		$errors = TOPIC_WRONG_ID_ERROR;
	}

	if(strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("global_errors", false);
		$t->pparse("main", false);
		return;
	}

	$r = new VA_Record($table_prefix . "forum_messages");
	$r->add_where("message_id", INTEGER);
	$r->add_textbox("thread_id", INTEGER);
	$r->add_hidden("u", INTEGER);
	//$r->add_textbox("admin_id", INTEGER);
	$r->add_textbox("user_name", TEXT, TOPIC_NICKNAME_FIELD);
	$r->change_property("user_name", REQUIRED, true);
	$r->change_property("user_name", REGEXP_MASK, NICKNAME_REGEXP);
	$r->change_property("user_name", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("user_email", TEXT, TOPIC_EMAIL_FIELD);
	$r->change_property("user_email", REQUIRED, true);
	$r->change_property("user_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("message_text", TEXT, TOPIC_MESSAGE_FIELD);
	$r->change_property("message_text", REQUIRED, true);
	$r->change_property("message_text", TRIM, true);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("user_id", INTEGER);
	$r->change_property("user_id", USE_SQL_NULL, false);
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
	
	$r->get_form_values();
	// if user registered there is no necessity to submit his data
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

	// check if use allowed to post replies
	if (VA_Forums::check_permissions($forum_id, POST_REPLIES_PERM)) {

		if($action && $rnd != $session_rnd)
		{
  
			set_session("session_rnd", $rnd);
			$r->validate();
  
			if ($use_validation && !$r->is_empty("validation_number")) { 
				if(!check_image_validation($r->get_value("validation_number"))) {
					$r->errors .= str_replace("{field_name}", VALIDATION_CODE_FIELD, VALIDATION_MESSAGE);
				}
			}
  
			if(!$r->errors) {
				if (check_black_ip()) {
					$r->errors = BLACK_IP_MSG; 
				} else if (check_banned_content($r->get_value("message_text"))) {
					$r->errors = BANNED_CONTENT_MSG; 
				}
			}
  
			if(!strlen($r->errors) && $button == TOPIC_MESSAGE_BUTTON)
			{
				$date_updated = va_time();
				$remote_address = get_ip();
				$r->set_value("date_added", $date_updated);
				$r->set_value("remote_address", $remote_address);
				$r->set_value("user_id", $user_id);
				if ($db_type == "postgre") {
					$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "forum_messages') ";
					$message_id = get_db_value($sql);
					$r->set_value("message_id", $message_id);
					$r->change_property("message_id", USE_IN_INSERT, true);
				}
  
				//$r->set_value("admin_id", get_session("session_admin_id"));
  
				if($r->insert_record())
				{ 
					if ($db_type == "mysql") {
						$sql = " SELECT LAST_INSERT_ID() ";
						$message_id = get_db_value($sql);
					} else if ($db_type == "access") {
						$sql = " SELECT @@IDENTITY ";
						$message_id = get_db_value($sql);
					} else if ($db_type == "db2") {
						$message_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "forum_messages FROM " . $table_prefix . "forum_messages");
					}
					$r->set_value("message_id", $message_id);

          // update forum thread info
					$sql  = " UPDATE " . $table_prefix . "forum SET replies=replies+1 ";
					$sql .= " , thread_updated=" . $db->tosql($date_updated, DATETIME);
					$sql .= " , last_post_added=" . $db->tosql($date_updated, DATETIME);
					$sql .= " , last_post_user_id=" . $db->tosql($user_id, INTEGER);
					$sql .= " , last_post_admin_id=0";
					$sql .= " , last_post_message_id=" . $db->tosql($message_id, INTEGER);
					$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
					$db->query($sql);
  
					$sql  = " UPDATE " . $table_prefix . "forum_list SET messages_number=messages_number+1 ";
					$sql .= " , last_post_added=" . $db->tosql($date_updated, DATETIME);
					$sql .= " , last_post_user_id=" . $db->tosql($user_id, INTEGER);
					$sql .= " , last_post_admin_id=0";
					$sql .= " , last_post_thread_id=" . $db->tosql($thread_id, INTEGER);
					$sql .= " , last_post_message_id=" . $db->tosql($message_id, INTEGER);
					$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
					$db->query($sql);

					// update attachments
					$sql  = " UPDATE " . $table_prefix . "forum_attachments ";
					$sql .= " SET message_id=" . $db->tosql($message_id, INTEGER);
					$sql .= " , attachment_status=1 ";
					$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
					if (!$user_id) {
						$sql .= " AND session_id=" . $db->tosql(session_id(), TEXT);
					}
					$sql .= " AND forum_id=" . $db->tosql($forum_id, INTEGER);
					$sql .= " AND thread_id=" . $db->tosql($thread_id, INTEGER);
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
					$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER);
					$sql .= " AND attachment_status=1 ";
					$db->query($sql);
					while ($db->next_record()) {
						$filename = $db->f("file_name");
						$filepath = $db->f("file_path");
						$attachments[] = array($filename, $filepath);
					}
					
					setcookie("cookie_forum_nick", $r->get_value("user_name"), va_timestamp() + 3600 * 24 * 366);  
					setcookie("cookie_forum_email", $r->get_value("user_email"), va_timestamp() + 3600 * 24 * 366);  
  
					if($email_notification || (isset($forum_settings["admin_notification"]) && $forum_settings["admin_notification"]))
					{
						// prepare variables
						$date_updated_string = va_date($datetime_show_format, $date_updated);
				  
						// set variables for email
						$r->set_parameters();
						$t->set_var("date_added", $date_updated_string);
						$t->set_var("message_added", $date_updated_string);
						$t->set_var("thread_modified", $date_updated_string);
						$t->set_var("thread_updated", $date_updated_string);
  
						$msg_user_name = htmlspecialchars($r->get_value("user_name"));
						$msg_user_email = htmlspecialchars($r->get_value("user_email"));
						$t->set_var("added_by", $msg_user_name);
						$t->set_var("nickname", $msg_user_name);
						$t->set_var("message_added_by", $msg_user_name);
						$t->set_var("message_user_name", $msg_user_name);
						$t->set_var("message_nickname", $msg_user_name);
  
						$t->set_var("user_email", $msg_user_email);
						$t->set_var("message_user_email", $msg_user_email);
  
						$t->set_var("message_remote_address", $r->get_value("remote_address"));
						$t->set_var("message_user_ip", $r->get_value("remote_address"));
  
						$t->set_var("topic_description", $topic_description);
						$t->set_var("description", $topic_description);
						$t->set_var("topic_message", $topic_description);
  
						// send notification to site administrator
						if(isset($forum_settings["admin_notification"]) && $forum_settings["admin_notification"])
						{			  
							$t->set_block("admin_subject", $forum_settings["admin_subject"]);
							$t->set_block("admin_message", $forum_settings["admin_message"]);

							$t->set_var("thread_user_name", $thread_user_name);
							$t->set_var("thread_user_email", $thread_user_email);
							$t->set_var("topic", $topic);
							$t->set_var("description", $topic_description);
							$t->set_var("message", $r->get_value("message_text"));
							$t->parse("admin_subject", false);
							$mail_type = get_setting_value($forum_settings, "admin_message_type");
							if ($mail_type) {
								$t->set_var("thread_user_name", htmlspecialchars($thread_user_name));
								$t->set_var("thread_user_email", htmlspecialchars($thread_user_email));
								$t->set_var("topic", htmlspecialchars($topic));
								$t->set_var("description", nl2br(htmlspecialchars($topic_description)));
								$t->set_var("message", nl2br(htmlspecialchars($r->get_value("message_text"))));
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
						// send notification to topic owner
						if($email_notification)
						{			  
							$t->set_block("user_subject", $forum_settings["user_subject"]);
							$t->set_block("user_message", $forum_settings["user_message"]);

							$t->set_var("thread_user_name", $thread_user_name);
							$t->set_var("thread_user_email", $thread_user_email);
							$t->set_var("topic", $topic);
							$t->set_var("description", $topic_description);
							$t->set_var("message", $r->get_value("message_text"));
							$t->parse("user_subject", false);
							$mail_type = get_setting_value($forum_settings, "user_message_type");
							if ($mail_type) {
								$t->set_var("thread_user_name", htmlspecialchars($thread_user_name));
								$t->set_var("thread_user_email", htmlspecialchars($thread_user_email));
								$t->set_var("topic", htmlspecialchars($topic));
								$t->set_var("description", nl2br(htmlspecialchars($topic_description)));
								$t->set_var("message", nl2br(htmlspecialchars($r->get_value("message_text"))));
							}
							$t->parse("user_message", false);
  
							$email_headers = array();
							$email_headers["from"] = get_setting_value($forum_settings, "user_mail_from", $settings["admin_email"]);
							$email_headers["cc"] = get_setting_value($forum_settings, "user_mail_cc");
							$email_headers["bcc"] = get_setting_value($forum_settings, "user_mail_bcc");
							$email_headers["reply_to"] = get_setting_value($forum_settings, "user_mail_reply_to");
							$email_headers["return_path"] = get_setting_value($forum_settings, "user_mail_return_path");
							$email_headers["mail_type"] = $mail_type;
  
							$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
							va_mail($thread_user_email, $t->get_var("user_subject"), $user_message, $email_headers);
						}
  
					}
					
				}
  
				header("Location: " . $return_page);
				exit;
			} else if (!strlen($r->errors) && $button == PREVIEW_BUTTON) {
				$message_text = get_param("message_text");
				if ($allow_bbcode) {
					$message_text = process_bbcode($message_text, $icons_enable);
				} else {
					$message_text = process_message($message_text, $icons_enable);
				}				
				split_long_words($message_text);
				$t->set_var("message_text", $message_text);
				$t->parse("topic_preview", false);
			}
			else
			{
				//$errors .= "Please provide information in the sections with red, italicized headings, then click 'Submit'.<br>";	
				set_session("session_rnd", "");
			}
		} else if(strlen(get_session("session_user_id"))) { // new page (set default values)
			$r->set_value("user_name", get_session("session_user_name"));
			$r->set_value("user_email", get_session("session_user_email"));
		} else {
			$r->set_value("user_name", get_cookie("cookie_forum_nick"));
			$r->set_value("user_email", get_cookie("cookie_forum_email"));
		}
	}

	$t->set_var("thread_user_name", htmlspecialchars($thread_user_name));
	$t->set_var("thread_user_email", htmlspecialchars($thread_user_email));
	$t->set_var("topic", htmlspecialchars($topic));
	$t->set_var("description", nl2br(htmlspecialchars($topic_description)));

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $forum_topic_page);
	$n->set_parameters(true, true, false);

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "forum_messages WHERE thread_id=" . $db->tosql($thread_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$last_page = ceil($total_records / $records_per_page);
	$pages_number = 10;
	$page_number = $n->set_navigator("navigator", "page", CENTERED, $pages_number, $records_per_page, $total_records, false, $pass_parameters);

	$t->set_var("records", "");
	// show topic message as a first message in the thread for ASC order
	if ($sort_messages == 1 && $page_number <= 1 && !$topic_information) {
		parse_topic_message($thread_id, $thread_admin_id, $thread_admin_id_modified_by, $thread_user_id, $thread_user_class, $thread_user_name, $thread_user_email, $thread_personal_image, $thread_remote_address, $thread_date_added, $thread_date_modified, $topic_description, $forum_settings);
	}

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT fm.message_id,fm.admin_id, a.personal_image AS admin_image, ";
	$sql .= " fm.user_name,fm.user_email,fm.remote_address, u.personal_image AS user_image, ";
	$sql .= " fm.message_text, fm.date_added, fm.user_id, fm.date_modified, fm.admin_id_modified_by ";
	$sql .= " FROM ((" . $table_prefix . "forum_messages fm ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=fm.user_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id=fm.admin_id) ";
	$sql .= " WHERE fm.thread_id=" . $db->tosql($thread_id, INTEGER);
	if ($sort_messages == 1) {
		$sql .= " ORDER BY fm.date_added ASC ";
	} else {
		$sql .= " ORDER BY fm.date_added DESC ";
	}
	$db->query($sql);
	$messages = array(); $ms = 0;
	while ($db->next_record()) {
		$messages[$ms]["message_id"] = $db->f("message_id");
		$messages[$ms]["user_id"] = $db->f("user_id");
		$messages[$ms]["admin_id"] = $db->f("admin_id");
		$messages[$ms]["user_name"] = $db->f("user_name");
		$messages[$ms]["user_email"] = $db->f("user_email");
		$messages[$ms]["remote_address"] = $db->f("remote_address");
		$messages[$ms]["message_text"] = $db->f("message_text");
		$messages[$ms]["date_added"] = $db->f("date_added", DATETIME);
		$messages[$ms]["date_modified"] = $db->f("date_modified", DATETIME);
		$messages[$ms]["admin_id_modified_by"] = $db->f("admin_id_modified_by");
		$ms++;
	}

	if ($ms > 0) {
		for ($i = 0; $i < $ms; $i++) {
			$message_id = $messages[$i]["message_id"];
			$message_user_id = $messages[$i]["user_id"];
			$message_admin_id = $messages[$i]["admin_id"];
			$message_user_name = $messages[$i]["user_name"];
			$message_user_email = $messages[$i]["user_email"];
			$message_user_ip = $messages[$i]["remote_address"];
			$message_text = $messages[$i]["message_text"];
			$date_added = $messages[$i]["date_added"];
			$date_modified = $messages[$i]["date_modified"];
			$admin_id_modified_by = $messages[$i]["admin_id_modified_by"];

			$t->set_var("message_id", $message_id);
			$t->set_var("message_user_ip", $message_user_ip);

			$personal_image = "";
			$forum_user_class = "forumGuest";
			$message_user_name .= " (" . GUEST_MSG . ")";
			if ($message_user_id) {
				$sql  = " SELECT login, nickname, personal_image FROM " . $table_prefix . "users ";
				$sql .= " WHERE user_id=" . $db->tosql($message_user_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$message_user_name = $db->f("nickname");
					if (!strlen($message_user_name)) { $message_user_name = $db->f("login"); }
					if ($db->f("email")) { $message_user_email = $db->f("email"); }
					$forum_user_class = "forumUser";
					$personal_image = $db->f("personal_image");
				}
			} else if ($message_admin_id) {
				$sql  = " SELECT admin_name, nickname, personal_image FROM " . $table_prefix . "admins ";
				$sql .= " WHERE admin_id=" . $db->tosql($message_admin_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$message_user_name = $db->f("nickname");
					if (!strlen($message_user_name)) { $message_user_name = $db->f("admin_name"); }
					if ($db->f("email")) { $message_user_email = $db->f("email"); }
					$forum_user_class = "forumAdmin";
					$personal_image = $db->f("personal_image");
				}
			}
			$t->set_var("message_user_name", htmlspecialchars($message_user_name));
			$t->set_var("message_user_email", htmlspecialchars($message_user_email));
			$t->set_var("forum_user_class", $forum_user_class);
			if (strlen($user_id) && $message_user_id == $user_id && !$admin_id_modified_by) {
				$t->sparse("message_edit_link", false);
			} else {
				$t->set_var("message_edit_link", "");
			}

			if (is_array($date_modified)) {
				$t->set_var("date_modified", va_date($datetime_show_format, $date_modified));
				$t->sparse("message_modified", false);
			} else {
				$t->set_var("message_modified", "");
			}

			if($message_admin_id) {
				$t->sparse("admin_block", false);
				$t->set_var("user_block", "");
			} else {
				$t->sparse("user_block", false);
				$t->set_var("admin_block", "");
			}
			if (!$personal_image && $forum_user_info == 1) {
				$personal_image = $user_no_image;
			}
	
			if (strlen($personal_image)) {
				if (preg_match("/^http\:\/\//", $personal_image)) {
					$image_size = "";
				} else {
					$image_size = @GetImageSize($personal_image);
				}
				if (is_array($image_size)) {
					$t->set_var("image_size", $image_size[3]);
				} else {
					$t->set_var("image_size", "");
				}

				$t->set_var("personal_image", $personal_image);
				$t->sparse("user_image_block", false);
			} else {
				$t->set_var("user_image_block", "");
			}

			$original_message = $message_text;
			if ($allow_bbcode) {
				$message_text = process_bbcode($message_text, $icons_enable);
			} else {
				$message_text = process_message($message_text, $icons_enable);
			}
			split_long_words($message_text);
			$date_added_string = va_date($datetime_show_format, $date_added);
			$t->set_var("date_added", $date_added_string);

			forum_attachments($thread_id, $message_id, "message_attachments");

			if ($forum_user_info == 1) {
				$t->sparse("user_info_left", false);
			} else {
				$t->sparse("user_info_top", false);
			}

			$t->set_var("message_text", $message_text);
			$t->set_var("original_message", htmlspecialchars($original_message));
			$t->parse("records", true);
		} while($db->next_record());

	}

	// show topic message as a last message in the thread for DESC order
	if ($sort_messages != 1 && $page_number == $last_page && !$topic_information) {
		parse_topic_message($thread_id, $thread_admin_id, $thread_admin_id_modified_by, $thread_user_id, $thread_user_class, $thread_user_name, $thread_user_email, $thread_personal_image, $thread_remote_address, $thread_date_added, $thread_date_modified, $topic_description, $forum_settings);
	}


	$r->set_parameters();
	$t->set_var("page", $page_number);

	// parse icons
	if ($icons_enable) {
		parse_icons("icons", $icons_cols, $icons_limit);
	}

	if (VA_Forums::check_permissions($forum_id, POST_REPLIES_PERM)) {
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
			$sql .= " AND thread_id=" . $db->tosql($thread_id, INTEGER);
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
		$t->parse("message_form", false);
		if ($reply_form == 2) {
			$t->set_var("message_form_bottom", $t->get_var("message_form"));
			$t->set_var("message_form", "");
		}
	} else {
		$t->set_var("message_form", "");
	}
	if ($topic_information) {
		$original_topic = $topic_description;
		if ($allow_bbcode) {
			$topic_description = process_bbcode($topic_description, $icons_enable);
		} else {
			$topic_description = process_message($topic_description, $icons_enable);
		}
		split_long_words($topic_description);
		$t->set_var("topic_description", $topic_description);
		$t->set_var("original_topic", htmlspecialchars($original_topic));

		forum_attachments($thread_id, 0, "topic_attachments");

		if (strlen($user_id) && $thread_user_id == $user_id && !$thread_admin_id_modified_by) {
			$t->set_var("message_id", "0");
			$t->sparse("message_edit_link", false);
		} else {
			$t->set_var("message_edit_link", "");
		}
  
		$date_added_ts = va_timestamp($thread_date_added);
		$date_modified_ts = va_timestamp($thread_date_modified);
		if (is_array($thread_date_modified) && $date_added_ts != $date_modified_ts) {
			$t->set_var("date_modified", va_date($datetime_show_format, $thread_date_modified));
			$t->sparse("message_modified", false);
		} else {
			$t->set_var("message_modified", "");
		}

		$t->parse("topic_info", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

function forum_attachments($thread_id, $message_id, $attachments_block)
{
	global $t, $db, $table_prefix;

	$attachments_order = false;
	$forum_attachment_url = "forum_attachment.php";

	//-- check for attachments
	$attach_no = 0; $attachments_files = ""; 
	$sql  = " SELECT * FROM " . $table_prefix . "forum_attachments ";
	$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
	$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER);
	$sql .= " AND attachment_status=1 ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$attachment_id = $db->Record["attachment_id"];
			$attachment_date = $db->f("date_added", DATETIME);
			$file_name     = $db->Record["file_name"];
			$file_path     = $db->Record["file_path"];
			if (file_exists($file_path)) {
				$attach_no++;
				$size	         = get_nice_bytes(filesize($file_path));
				$attachment_vc = md5($attachment_id . $attachment_date[3].$attachment_date[4].$attachment_date[5]);
				if ($attachments_order) {
					$attachments_files .= $attach_no . ". ";
				}
				$attachments_files .= "<a target=\"_blank\" href=\"" . $forum_attachment_url . "?atid=" . $attachment_id . "&vc=" . $attachment_vc . "\">" . $file_name . "</a> (" . $size . ")&nbsp;&nbsp;";
			}
		} while ($db->next_record());
	}
	if ($attach_no > 0) {
		$t->set_var("attachments_files", $attachments_files);
		$t->parse($attachments_block,false);
	} else { 
		$t->set_var($attachments_block,"");
	}
}

function parse_topic_message($thread_id, $thread_admin_id, $thread_admin_id_modified_by, $thread_user_id, $thread_user_class, $thread_user_name, $thread_user_email, $thread_personal_image, $thread_remote_address, $thread_date_added, $thread_date_modified, $topic_description, $forum_settings)
{
	global $t, $datetime_show_format;

	$session_user_id = get_session("session_user_id");
	
	$forum_user_info = get_setting_value($forum_settings, "user_info", 1); 
	$icons_enable    = get_setting_value($forum_settings, "icons_enable", 1);
	$allow_bbcode    = get_setting_value($forum_settings, "allow_bbcode", 0);
	
	$forum_attachment_url = "forum_attachment.php";

	$t->set_var("message_user_name", htmlspecialchars($thread_user_name));
	$t->set_var("message_user_email", htmlspecialchars($thread_user_email));
	$t->set_var("message_user_ip", $thread_remote_address);
	$t->set_var("message_id", 0);

	if ($thread_user_id) {
		$t->sparse("user_block", false);
		$t->set_var("admin_block", "");
	} else {
		$t->sparse("admin_block", false);
		$t->set_var("user_block", "");
	}
	$t->set_var("forum_user_class", $thread_user_class);

	if ($thread_user_id == $session_user_id && !$thread_admin_id_modified_by) {
		$t->set_var("message_id", "0");
		$t->sparse("message_edit_link", false);
	} else {
		$t->set_var("message_edit_link", "");
	}

	$date_added_ts = va_timestamp($thread_date_added);
	$date_modified_ts = va_timestamp($thread_date_modified);
	if (is_array($thread_date_modified) && $date_added_ts != $date_modified_ts) {
		$t->set_var("date_modified", va_date($datetime_show_format, $thread_date_modified));
		$t->sparse("message_modified", false);
	} else {
		$t->set_var("message_modified", "");
	}


	if (strlen($thread_personal_image)) {
		if (preg_match("/^http\:\/\//", $thread_personal_image)) {
			$image_size = "";
		} else {
			$image_size = @GetImageSize($thread_personal_image);
		}
		if (is_array($image_size)) {
			$t->set_var("image_size", $image_size[3]);
		} else {
			$t->set_var("image_size", "");
		}

		$t->set_var("personal_image", $thread_personal_image);
		$t->set_var("forum_user_class", $thread_user_class);

		$t->sparse("user_image_block", false);
	} else {
		$t->set_var("user_image_block", "");
	}
	$t->set_var("date_added", va_date($datetime_show_format, $thread_date_added));

	if ($forum_user_info == 1) {
		$t->sparse("user_info_left", false);
	} else {
		$t->sparse("user_info_top", false);
	}

	forum_attachments($thread_id, 0, "message_attachments");

	$topic_original = $topic_description;
	if ($allow_bbcode) {
		$topic_description = process_bbcode($topic_description, $icons_enable);
	} else {
		$topic_description = process_message($topic_description, $icons_enable);
	}
	split_long_words($topic_description);

	$t->set_var("message_text", $topic_description);
	$t->set_var("original_message", htmlspecialchars($topic_original));
	$t->parse("records", true);

}
?>