<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_thread.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path."includes/navigator.php");
	include_once($root_folder_path."includes/record.php");
	include_once($root_folder_path."includes/icons_functions.php");

	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");
	include_once("./admin_common.php");

	check_admin_security("forum");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_forum_thread.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_thread_href", "admin_forum_thread.php");
	$t->set_var("admin_forum_message_href", "admin_forum_message.php");
	$t->set_var("admin_forum_topic_href", "admin_forum_topic.php");
	$t->set_var("admin_forum_attachments_url", "admin_forum_attachments.php");

	$t->set_var("icon_select_href", "../icon_select.php");

	$t->set_var("rnd", va_timestamp());

	$thread_id = get_param("thread_id");
	$operation = get_param("operation");
	$forum_id = "";
	$rnd = get_param("rnd");
	$message_id = get_param("message_id");
	$errors = "";

	$t->set_var("thread_id", htmlspecialchars($thread_id));

	$return_page = "admin_forum_thread.php?thread_id=" . $thread_id;

	$sql  = " SELECT f.user_name, f.user_email, f.remote_address, f.topic, f.description, f.email_notification, ";
	$sql .= " f.date_added, f.date_modified, f.thread_updated, ";
	$sql .= " f.views, f.replies, f.forum_id, fl.forum_name, fl.category_id, fc.category_name ";
	$sql .= " FROM " . $table_prefix . "forum f, " . $table_prefix . "forum_list fl, " . $table_prefix . "forum_categories fc ";
	$sql .= " WHERE thread_id = " . $db->tosql($thread_id, INTEGER);
	$sql .= " AND f.forum_id = fl.forum_id AND fl.category_id = fc.category_id ";
	$db->query($sql);
	if($db->next_record() && !strlen($errors)) {

		$topic = get_translation($db->f("topic"));
		$description = $db->f("description");
		$topic_user_name = $db->f("user_name");
		$topic_user_email = $db->f("user_email");
		$topic_user_ip = $db->f("remote_address");
		$date_added = va_date($datetime_show_format, $db->f("date_added", DATETIME));
		$date_modified = va_date($datetime_show_format, $db->f("date_modified", DATETIME));
		$thread_updated = va_date($datetime_show_format, $db->f("thread_updated", DATETIME));
		$topic_views = $db->f("views");
		$topic_replies = $db->f("replies");
		$email_notification = $db->f("email_notification");
		$forum_id = $db->f("forum_id");
		$forum_name = get_translation($db->f("forum_name"));
		$category_id = $db->f("category_id");
		$category_name = get_translation($db->f("category_name"));

		$t->set_var("topic_user_ip", $topic_user_ip);
		$t->set_var("topic_added", $date_added);
		$t->set_var("topic_modified", $date_modified);
		$t->set_var("thread_updated", $thread_updated);
		$t->set_var("views", $topic_views);
		$t->set_var("replies", $topic_replies);

		$last_message = $description;

		$t->set_var("forum_id", $forum_id);
		$t->set_var("current_forum", $forum_name);
		$t->set_var("category_id", $category_id);
		$t->set_var("current_category", $category_name);
	}	else {
		$t->set_var("errors_list", NO_THREADS_FOUND_MSG);
		$t->parse("global_errors", false);
		$t->set_var("topic_info", "");
		$t->set_var("topic_messages", "");
		$t->pparse("main", false);
		exit;
	}

	$forum_settings = array();
	$sql = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type = 'forum'";
	$db->query($sql);
	while($db->next_record()) {
		$forum_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$reply_form = get_setting_value($forum_settings, "reply_form", 1); 
	$topic_information = get_setting_value($forum_settings, "topic_information", 1); 
	$sort_messages = get_setting_value($forum_settings, "sort_messages", 1); 
	$user_images = get_setting_value($forum_settings, "user_images", 1); 
	$forum_user_info = get_setting_value($forum_settings, "user_info", 1); 
	$user_no_image = get_setting_value($forum_settings, "user_no_image", ""); 
	$icons_enable = get_setting_value($forum_settings, "icons_enable", 1); 
	$icons_cols = get_setting_value($forum_settings, "icons_cols", 4); 
	$icons_limit = get_setting_value($forum_settings, "icons_limit", 16); 

	$r = new VA_Record($table_prefix . "forum_messages");
	$r->add_where("message_id", INTEGER);
	$r->add_textbox("thread_id", INTEGER);
	$r->add_textbox("admin_id", INTEGER);
	$r->add_textbox("user_name", TEXT, NICKNAME_FIELD);
	$r->change_property("user_name", REQUIRED, true);
	$r->add_textbox("user_email", TEXT, EMAIL_FIELD);
	$r->change_property("user_email", REQUIRED, true);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("message_text", TEXT, MESSAGE_MSG);
	$r->change_property("message_text", REQUIRED, true);
	$r->add_textbox("date_added", DATETIME);

	$r->get_form_values();

	$session_rnd = get_session("session_rnd");
	$operation = get_param("operation");
	$rnd = get_param("rnd");

	if($operation && $rnd != $session_rnd) {
		$sql  = " SELECT admin_name, email FROM " . $table_prefix . "admins ";
		$sql .= " WHERE admin_id = " . $db->tosql(get_session("session_admin_id"), INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$r->set_value("user_name", $db->f("admin_name"));
			$r->set_value("user_email", $db->f("email"));
		}

		$is_valid = $r->validate();
		if($is_valid) {
			$date_updated = va_time();
			$remote_address = get_ip();
			$r->set_value("date_added", $date_updated);
			$r->set_value("remote_address", $remote_address);
			$r->set_value("admin_id", get_session("session_admin_id"));
			if ($db_type == "postgre") {
				$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "forum_messages') ";
				$new_message_id = get_db_value($sql);
				$r->set_value("message_id", $new_message_id);
				$r->change_property("message_id", USE_IN_INSERT, true);
			}

			if($r->insert_record()) { 
				if ($db_type == "mysql") {
					$sql = " SELECT LAST_INSERT_ID() ";
					$new_message_id = get_db_value($sql);
				} else if ($db_type == "access") {
					$sql = " SELECT @@IDENTITY ";
					$new_message_id = get_db_value($sql);
				} else if ($db_type == "db2") {
					$new_message_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "forum_messages FROM " . $table_prefix . "forum_messages");
				}
				$r->set_value("message_id", $new_message_id);

        // update forum topic info
				$sql  = " UPDATE " . $table_prefix . "forum SET replies = replies + 1, ";
				$sql .= " thread_updated= " . $db->tosql($date_updated, DATETIME);
				$sql .= " WHERE thread_id = " . $db->tosql($thread_id, INTEGER);
				$db->query($sql);

        // update forum info
				$sql  = " UPDATE " . $table_prefix . "forum_list SET messages_number = messages_number + 1, ";
				$sql .= " last_post_added = " . $db->tosql($date_updated, DATETIME);
				$sql .= " , last_post_thread_id = " . $db->tosql($thread_id, INTEGER);
				$sql .= " WHERE forum_id = " . $db->tosql($forum_id, INTEGER);
				$db->query($sql);

				// check attachments
				$attachments = array();
				$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "forum_attachments ";
				$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
				$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);
				while ($db->next_record()) {
					$filename = $db->f("file_name");
					$filepath = $db->f("file_path");
					$attachments[] = array($filename, $filepath);
				}
    
				$sql  = " UPDATE " . $table_prefix . "forum_attachments ";
				$sql .= " SET message_id=" . $db->tosql($new_message_id, INTEGER);
				$sql .= " , attachment_status=1 ";
				$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
				$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);

				setcookie("cookie_forum_nick", $r->get_value("user_name"), va_timestamp() + 3600 * 24 * 366);  
				setcookie("cookie_forum_email", $r->get_value("user_email"), va_timestamp() + 3600 * 24 * 366);  

				if($email_notification || (isset($forum_settings["admin_notification"]) && $forum_settings["admin_notification"])) {
					// Prepare variables
					$date_updated_string = va_date($datetime_show_format, $date_updated);
					$message_text = $r->get_value("message_text");
			  
					// Set variables for email
					$r->set_parameters();
					$t->set_var("date_added", $date_updated_string);
					$t->set_var("topic_modified", $date_modified);
					$t->set_var("thread_updated", $date_updated_string);

					$t->set_var("message_user_name", $r->get_value("user_name"));
					$t->set_var("message_user_email", $r->get_value("user_email"));
					$t->set_var("message_user_ip", $r->get_value("remote_address"));
					$t->set_var("message", $message_text);
					$t->set_var("topic_description", nl2br(htmlspecialchars($description)));

					// Send notification to site administrator
					if(isset($forum_settings["admin_notification"]) && $forum_settings["admin_notification"]) {			  
						$t->set_block("admin_subject", $forum_settings["admin_subject"]);
						$t->set_block("admin_message", $forum_settings["admin_message"]);

						$t->set_var("thread_user_name", $topic_user_name);
						$t->set_var("thread_user_email", $topic_user_email);
						$t->set_var("topic", $topic);
						$t->set_var("description", $description);
						$t->set_var("topic_description", $description);
						$t->set_var("message", $message_text);
						$t->parse("admin_subject", false);
						$mail_type = get_setting_value($forum_settings, "admin_message_type");
						if ($mail_type) {
							$t->set_var("topic_user_name", htmlspecialchars($topic_user_name));
							$t->set_var("topic_user_email", htmlspecialchars($topic_user_email));
							$t->set_var("topic", htmlspecialchars($topic));
							$t->set_var("description", nl2br(htmlspecialchars($description)));
							$t->set_var("topic_description", nl2br(htmlspecialchars($description)));
							$t->set_var("message", nl2br(htmlspecialchars($message_text)));
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

						$admin_message = str_replace("\r", "", $t->get_var("admin_message"));
						va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers, $attachments);
					}
					// Send notification to topic owner
					if($email_notification) {			  
						$t->set_block("user_subject", $forum_settings["user_subject"]);
						$t->set_block("user_message", $forum_settings["user_message"]);

						$t->set_var("thread_user_name", $topic_user_name);
						$t->set_var("thread_user_email", $topic_user_email);
						$t->set_var("topic", $topic);
						$t->set_var("description", $description);
						$t->set_var("topic_description", $description);
						$t->set_var("message", $message_text);
						$t->parse("user_subject", false);
						$mail_type = get_setting_value($forum_settings, "user_message_type");
						if ($mail_type) {
							$t->set_var("topic_user_name", htmlspecialchars($topic_user_name));
							$t->set_var("topic_user_email", htmlspecialchars($topic_user_email));
							$t->set_var("topic", htmlspecialchars($topic));
							$t->set_var("description", nl2br(htmlspecialchars($description)));
							$t->set_var("topic_description", nl2br(htmlspecialchars($description)));
							$t->set_var("message", nl2br(htmlspecialchars($message_text)));
						}
						$t->parse("user_message", false);

						$email_headers["from"] = get_setting_value($forum_settings, "user_mail_from", $settings["admin_email"]);
						$email_headers["cc"] = get_setting_value($forum_settings, "user_mail_cc");
						$email_headers["bcc"] = get_setting_value($forum_settings, "user_mail_bcc");
						$email_headers["reply_to"] = get_setting_value($forum_settings, "user_mail_reply_to");
						$email_headers["return_path"] = get_setting_value($forum_settings, "user_mail_return_path");
						$email_headers["mail_type"] = $mail_type;

						$user_message = str_replace("\r", "", $t->get_var("user_message"));
						va_mail($topic_user_email, $t->get_var("user_subject"), $user_message, $email_headers, $attachments);
					}
				}
			}

			header("Location: " . $return_page);
			exit;
		}
		else {
			set_session("session_rnd", "");
		}
	}
	// new (set default values)
	else {
		$r->set_value("user_name", get_cookie("cookie_forum_nick"));
		$r->set_value("user_email", get_cookie("cookie_forum_email"));
	}

	// prepare icons to replace in the text
	prepare_icons($icons, $icons_codes, $icons_tags);

	// parse icons
	if ($icons_enable) {
		parse_icons("icons", $icons_cols, $icons_limit);
	}

	$t->set_var("thread_user_name", htmlspecialchars($topic_user_name));
	$t->set_var("thread_user_email", htmlspecialchars($topic_user_email));
	$t->set_var("topic_user_name", htmlspecialchars($topic_user_name));
	$t->set_var("topic_user_email", htmlspecialchars($topic_user_email));
	$t->set_var("topic", htmlspecialchars($topic));
	$t->set_var("description", process_message($description, $icons_enable));
	$t->set_var("topic_description", process_message($description, $icons_enable));
	
	forum_attachments($thread_id, 0, "topic_attachments");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", TOPIC_NAME_COLUMN, CONFIRM_DELETE_MSG));

	$t->parse("topic_info", false);

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_forum_thread.php");

	// Set variables for navigator
	$sql = "SELECT COUNT(*) FROM " . $table_prefix . "forum_messages WHERE thread_id = " . $db->tosql($thread_id, INTEGER);
	$total_records = get_db_value($sql);

	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 7;

	$n->set_parameters(true, true, false);
	$page_number = $n->set_navigator("navigator", "mes_page", CENTERED, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;

	$sql  = " SELECT message_id, admin_id, user_name, user_email, remote_address, message_text, date_added ";
	$sql .= " FROM " . $table_prefix . "forum_messages  ";
	$sql .= " WHERE thread_id = " . $db->tosql($thread_id, INTEGER);
	$sql .= " ORDER BY date_added DESC ";
	$db->query($sql);
	if($db->next_record()) {
		$last_message = $db->f("message_text");
		do {
			$message_id = $db->f("message_id");
			$t->set_var("message_user_name", htmlspecialchars($db->f("user_name")));
			$t->set_var("message_user_email", htmlspecialchars($db->f("user_email")));
			$t->set_var("message_user_ip", $db->f("remote_address"));
			$t->set_var("message_id", $db->f("message_id"));

			if(strlen($db->f("admin_id"))) {
				$t->parse("admin_block", false);
				$t->set_var("user_block", "");
			}
			else {
				$t->parse("user_block", false);
				$t->set_var("admin_block", "");
			}

			$t->set_var("date_added", va_date($datetime_show_format, $db->f("date_added", DATETIME)));

			$message_text = process_message($db->f("message_text"), $icons_enable);
			split_long_words($message_text);

			forum_attachments($thread_id, $message_id, "message_attachments");

			$t->set_var("message_text", $message_text);
			$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", EMAIL_MESSAGE_MSG, CONFIRM_DELETE_MSG));

			$t->parse("records", true);
		} while($db->next_record());
	}
	else {
		$t->set_var("records", "");
	}

	// Set default message text for reply
	if(!strlen($operation)) {
		$last_message = ">" . str_replace("\n", "\n>", $last_message);
		$r->set_value("message_text", $last_message);
	}

	// check attachments
	$attachments_files = "";
	$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "forum_attachments ";
	$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
	$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
	$sql .= " AND message_id=0 ";
	$sql .= " AND attachment_status=0 ";
	$db->query($sql);
	while ($db->next_record()) {
		$attachment_id = $db->f("attachment_id");
		$filename = $db->f("file_name");
		$filepath = $db->f("file_path");
		$is_file_exists = file_exists($file_path);
		if (!$is_file_exists && file_exists("../" . $file_path)) {
			$file_path = "../" . $file_path;
		}
		$filesize = filesize($filepath);
		if ($attachments_files) { $attachments_files .= "; "; }
		$attachments_files .= "<a href=\"admin_forum_attachment.php?atid=" .$attachment_id. "\" target=\"_blank\">" . $filename . "</a> (" . get_nice_bytes($filesize) . ")";
	}
	if ($attachments_files) {
		$t->set_var("attached_files", $attachments_files);
		$t->set_var("attachments_class", "display: block;");
	} else {
		$t->set_var("attachments_class", "display: none;");
	}
	$t->parse("attachments_block", false);

	$r->set_parameters();
	$t->set_var("page", $page_number);
	$t->set_var("rp", urlencode($return_page));

	$t->parse("topic_messages", false);
	$t->pparse("main");


function forum_attachments($thread_id, $message_id, $attachments_block)
{
	global $t, $db, $table_prefix;

	// connection for attachemnts 
	$dba = new VA_SQL();
	$dba->DBType       = $db->DBType;
	$dba->DBDatabase   = $db->DBDatabase;
	$dba->DBUser       = $db->DBUser;
	$dba->DBPassword   = $db->DBPassword;
	$dba->DBHost       = $db->DBHost;
	$dba->DBPort       = $db->DBPort;
	$dba->DBPersistent = $db->DBPersistent;

	$attachments_order = false;
	$forum_attachment_url = "admin_forum_attachment.php";

	//-- check for attachments
	$attach_no = 0; $attachments_files = ""; 
	$sql  = " SELECT * FROM " . $table_prefix . "forum_attachments ";
	$sql .= " WHERE thread_id=" . $dba->tosql($thread_id, INTEGER);
	$sql .= " AND message_id=" . $dba->tosql($message_id, INTEGER);
	$sql .= " AND attachment_status=1 ";
	$dba->query($sql);
	if ($dba->next_record()) {
		do {
			$attachment_id = $dba->Record["attachment_id"];
			$attachment_date = $dba->f("date_added", DATETIME);
			$file_name     = $dba->Record["file_name"];
			$file_path     = $dba->Record["file_path"];
			$is_file_exists = file_exists($file_path);
			if (!$is_file_exists && file_exists("../" . $file_path)) {
				$file_path = "../" . $file_path;
			}
			if (file_exists($file_path)) {
				$attach_no++;
				$size	         = get_nice_bytes(filesize($file_path));
				$attachment_vc = md5($attachment_id . $attachment_date[3].$attachment_date[4].$attachment_date[5]);
				if ($attachments_order) {
					$attachments_files .= $attach_no . ". ";
				}
				$attachments_files .= "<a target=\"_blank\" href=\"" . $forum_attachment_url . "?atid=" . $attachment_id . "&vc=" . $attachment_vc . "\">" . $file_name . "</a> (" . $size . ")&nbsp;&nbsp;";
			}
		} while ($dba->next_record());
	}
	if ($attach_no > 0) {
		$t->set_var("attachments_files", $attachments_files);
		$t->parse($attachments_block, false);
	} else { 
		$t->set_var($attachments_block,"");
	}
}


?>