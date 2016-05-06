<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_topic.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path."includes/common.php");
	include_once ($root_folder_path."includes/record.php");
	include_once ($root_folder_path."includes/friendly_functions.php");
	include_once ($root_folder_path."includes/icons_functions.php");
	include_once ($root_folder_path."messages/".$language_code."/forum_messages.php");
	include_once("./admin_common.php");

	check_admin_security("forum");

	$forum_settings = array();
	$sql = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type = 'forum'";
	$db->query($sql);
	while($db->next_record()) {
		$forum_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$icons_enable = get_setting_value($forum_settings, "icons_enable", 1); 
	$icons_cols = get_setting_value($forum_settings, "icons_cols", 4); 
	$icons_limit = get_setting_value($forum_settings, "icons_limit", 16); 

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_forum_topic.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_thread_href", "admin_forum_thread.php");
	$t->set_var("admin_forum_topic_href", "admin_forum_topic.php");
	$t->set_var("admin_forum_attachments_url", "admin_forum_attachments.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", TOPIC_NAME_COLUMN, CONFIRM_DELETE_MSG));
	$t->set_var("icon_select_href", "../icon_select.php");

	$forums = array(array("", ""));
	$sql  = " SELECT fc.category_name, fl.forum_name, fl.forum_id ";
	$sql .= " FROM " . $table_prefix . "forum_list fl, " . $table_prefix . "forum_categories fc ";
	$sql .= " WHERE fl.category_id = fc.category_id ";
	$sql .= " ORDER BY fc.category_order, fl.forum_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$option_id = $db->f("forum_id");
		$option_name = get_translation($db->f("category_name")) . " > " . get_translation($db->f("forum_name"));
		$forums[] = array($option_id, $option_name);
	}

	$priorities = array(array("", ""));
	$default_priority_id = 0;
	$sql  = " SELECT priority_id, priority_name, is_default ";
	$sql .= " FROM " . $table_prefix . "forum_priorities ";
	$db->query($sql);
	while ($db->next_record()) {
		$priority_id = $db->f("priority_id");
		$priority_name = $db->f("priority_name");
		if ($db->f("is_default") == 1) {
			$default_priority_id = $priority_id;
		}
		$priorities[] = array($priority_id, $priority_name);
	}
	

	$r = new VA_Record($table_prefix . "forum");
	$r->add_where("thread_id", INTEGER);

	$r->add_select("forum_id", INTEGER, $forums, FORUM_TITLE);
	$r->add_select("priority_id", INTEGER, $priorities, PRIORITY_MSG);
	$r->change_property("priority_id", DEFAULT_VALUE, $default_priority_id);
	$r->add_textbox("user_id", INTEGER, USER_ID_MSG);
	$r->change_property("user_id", USE_SQL_NULL, false);
	$r->add_textbox("views", INTEGER);
	$r->change_property("views", USE_IN_INSERT, false);
	$r->change_property("views", USE_IN_UPDATE, false);
	$r->add_textbox("replies", INTEGER);
	$r->change_property("replies", USE_IN_UPDATE, false);
	$r->add_textbox("user_name", TEXT, NICKNAME_FIELD);
	$r->change_property("user_name", REQUIRED, true);
	$r->add_textbox("user_email", TEXT, EMAIL_FIELD);
	$r->change_property("user_email", REQUIRED, true);
	$r->change_property("user_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("topic", TEXT, TOPIC_NAME_FIELD);
	$r->change_property("topic", REQUIRED, true);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->change_property("friendly_url", TRIM, true);
	$r->add_textbox("description", TEXT, DESCRIPTION_MSG);
	$r->change_property("description", REQUIRED, true);
	$r->add_checkbox("email_notification", INTEGER);

	// editing information
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->change_property("date_modified", USE_IN_INSERT, false);
	$r->add_textbox("thread_updated", DATETIME);
	$r->change_property("thread_updated", USE_IN_UPDATE, false);

	$r->get_form_values();

	$operation = get_param("operation");
	$thread_id = get_param("thread_id");
	$rp = get_param("rp");

	if ($thread_id) {
		$sql  = " SELECT f.topic, f.forum_id, fl.forum_name, fl.category_id, fc.category_name ";
		$sql .= " FROM " . $table_prefix . "forum f, " . $table_prefix . "forum_list fl, " . $table_prefix . "forum_categories fc ";
		$sql .= " WHERE f.thread_id = " . $db->tosql($thread_id, INTEGER);
		$sql .= " AND f.forum_id = fl.forum_id AND fl.category_id = fc.category_id ";
		$db->query($sql);
		if ($db->next_record()) {
			$current_category_url  = "admin_forum.php?category_id=" . $db->f("category_id");
			$current_category_name = get_translation($db->f("category_name"));
			$current_forum_url  = "admin_forum.php?forum_id=" . $db->f("forum_id");
			$current_forum_name = get_translation($db->f("forum_name"));
			$current_topic_url  = "admin_forum_thread.php?thread_id=" . $thread_id;
			$current_topic_name = get_translation($db->f("topic"));

			$t->set_var("current_category_url",  $current_category_url);
			$t->set_var("current_category_name", $current_category_name);
			$t->set_var("current_forum_url",  $current_forum_url);
			$t->set_var("current_forum_name", $current_forum_name);
			$t->set_var("current_topic_url",  $current_topic_url);
			$t->set_var("current_topic_name", $current_topic_name);
			$t->parse("category_breadcrumb", false);
		}
	}
	$return_page = (strlen($rp) && $operation != "delete") ? $rp : "admin_forum_thread.php?thread_id=" . $thread_id;

	if(strlen($operation)) {
		if ($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		} else if ($operation == "delete" && $thread_id) {
			$sql = "SELECT forum_id, replies FROM ". $table_prefix . "forum WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$forum_id = $db->f("forum_id");
				$replies = intval($db->f("replies"));
			} else {
				header ("Location: " . $return_page);
				exit;
			}

			$db->query("DELETE FROM " . $table_prefix . "forum_messages WHERE thread_id = " . $db->tosql($thread_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "items_forum_topics WHERE thread_id = " . $db->tosql($thread_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "articles_forum_topics WHERE thread_id = " . $db->tosql($thread_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "forum WHERE thread_id = " . $db->tosql($thread_id, INTEGER));

			// update information when topic deleted
			$sql  = " SELECT thread_updated, user_id, admin_id_added_by, thread_id ";
			$sql .= " FROM " . $table_prefix . "forum ";
			$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
			$sql .= " ORDER BY thread_updated DESC ";
			$db->RecordsPerPage = 1; $db->PageNumber = 1;
			$db->query($sql);
			if ($db->next_record()) {
				$last_post_added = $db->f("thread_updated", DATETIME);
				$last_post_user_id = $db->f("user_id");
				$last_post_admin_id = $db->f("admin_id_added_by");
				$last_post_thread_id = $db->f("thread_id");
			} else {
				$last_post_added = ""; $last_post_thread_id = 0;
				$last_post_user_id = 0; $last_post_admin_id = 0;
			}

			$sql  = " UPDATE ". $table_prefix . "forum_list ";
			$sql .= " SET threads_number=threads_number-1, messages_number=messages_number-" . $db->tosql($replies, INTEGER) . ", ";
			$sql .= " last_post_added=" . $db->tosql($last_post_added, DATETIME) . ", ";
			$sql .= " last_post_user_id=" . $db->tosql($last_post_user_id, INTEGER) . ", ";
			$sql .= " last_post_admin_id=" . $db->tosql($last_post_admin_id, INTEGER) . ", ";
			$sql .= " last_post_thread_id=" . $db->tosql($last_post_thread_id, INTEGER) . ", ";
			$sql .= " last_post_message_id=0 ";
			$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
			$db->query($sql);

			// delete attachments
			$sql = "SELECT file_path FROM " . $table_prefix . "forum_attachments WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$file_path = $db->f("file_path");
				$is_file_exists = file_exists($file_path);
				if (!$is_file_exists && file_exists("../" . $file_path)) {
					$file_path = "../" . $file_path;
				}
				@unlink($file_path);
			}
	  
			$sql = "DELETE FROM " . $table_prefix . "forum_attachments WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
			$db->query($sql);

			header("Location: admin_forum.php?forum_id=" . $forum_id);
			exit;
		}

		$is_valid = $r->validate();

		if ($is_valid) {
			$remote_address = get_ip();
			$date_added = va_time();
			$r->set_value("date_modified", $date_added);
			$r->set_value("thread_updated", $date_added);
			$r->set_value("admin_id_added_by", get_session("session_admin_id"));
			$r->set_value("admin_id_modified_by", get_session("session_admin_id"));

			$new_forum_id = $r->get_value("forum_id");
			if (strlen($r->get_value("thread_id"))) {
				$sql = "SELECT forum_id, replies FROM ". $table_prefix . "forum WHERE thread_id = " . $db->tosql($thread_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$old_forum_id = $db->f("forum_id");
					$replies = intval($db->f("replies"));
				} else {
					header ("Location: " . $return_page);
					exit;
				}
				
				set_friendly_url();
				$r->update_record();

				// the topic moved to new location
				if ($new_forum_id != $old_forum_id) {
					// update information for old location
					$sql  = " SELECT thread_updated, user_id, admin_id_added_by, thread_id ";
					$sql .= " FROM " . $table_prefix . "forum ";
					$sql .= " WHERE forum_id=" . $db->tosql($old_forum_id, INTEGER);
					$sql .= " ORDER BY thread_updated DESC ";
					$db->RecordsPerPage = 1; $db->PageNumber = 1;
					$db->query($sql);
					if ($db->next_record()) {
						$last_post_added = $db->f("thread_updated", DATETIME);
						$last_post_user_id = $db->f("user_id");
						$last_post_admin_id = $db->f("admin_id_added_by");
						$last_post_thread_id = $db->f("thread_id");
					} else {
						$last_post_added = ""; $last_post_thread_id = 0;
						$last_post_user_id = 0; $last_post_admin_id = 0;
					}

					$sql  = " UPDATE ". $table_prefix . "forum_list ";
					$sql .= " SET threads_number=threads_number-1, messages_number=messages_number-" . $db->tosql($replies, INTEGER) . ", ";
					$sql .= " last_post_added=" . $db->tosql($last_post_added, DATETIME) . ", ";
					$sql .= " last_post_user_id=" . $db->tosql($last_post_user_id, INTEGER) . ", ";
					$sql .= " last_post_admin_id=" . $db->tosql($last_post_admin_id, INTEGER) . ", ";
					$sql .= " last_post_thread_id=" . $db->tosql($last_post_thread_id, INTEGER) . ", ";
					$sql .= " last_post_message_id=0 ";
					$sql .= " WHERE forum_id=" . $db->tosql($old_forum_id, INTEGER);
					$db->query($sql);

					// update information for new location
					$sql  = " SELECT thread_updated, user_id, admin_id_added_by, thread_id ";
					$sql .= " FROM " . $table_prefix . "forum ";
					$sql .= " WHERE forum_id=" . $db->tosql($new_forum_id, INTEGER);
					$sql .= " ORDER BY thread_updated DESC ";
					$db->RecordsPerPage = 1; $db->PageNumber = 1;
					$db->query($sql);
					if ($db->next_record()) {
						$last_post_added = $db->f("thread_updated", DATETIME);
						$last_post_user_id = $db->f("user_id");
						$last_post_admin_id = $db->f("admin_id_added_by");
						$last_post_thread_id = $db->f("thread_id");
					} else {
						$last_post_added = ""; $last_post_thread_id = 0;
						$last_post_user_id = 0; $last_post_admin_id = 0;
					}

					$sql  = " UPDATE ". $table_prefix . "forum_list ";
					$sql .= " SET threads_number=threads_number+1, ";
					$sql .= " messages_number=messages_number+" . $db->tosql($replies, INTEGER) . ", ";
					$sql .= " last_post_added=" . $db->tosql($last_post_added, DATETIME) . ", ";
					$sql .= " last_post_user_id=" . $db->tosql($last_post_user_id, INTEGER) . ", ";
					$sql .= " last_post_admin_id=" . $db->tosql($last_post_admin_id, INTEGER) . ", ";
					$sql .= " last_post_thread_id=" . $db->tosql($last_post_thread_id, INTEGER) . ", ";
					$sql .= " last_post_message_id=0 ";
					$sql .= " WHERE forum_id=" . $db->tosql($new_forum_id, INTEGER);
					$db->query($sql);

					// update forum_id for attachments
					$sql  = " UPDATE ". $table_prefix . "forum_attachments ";
					$sql .= " SET forum_id=" . $db->tosql($new_forum_id, INTEGER);
					$sql .= " WHERE forum_id=" . $db->tosql($old_forum_id, INTEGER);
					$db->query($sql);
				} 

			} else {
				set_friendly_url();
				if ($db_type == "postgre") {
					$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "forum') ";
					$thread_id = get_db_value($sql);
					$r->set_value("thread_id", $thread_id);
					$r->change_property("thread_id", USE_IN_INSERT, true);
				}
				$r->set_value("date_added", $date_added);
				if ($r->is_empty("views")) {
					$r->set_value("views", 0);
				}
				$r->set_value("replies", 0);

				$r->insert_record();

				if ($db_type == "mysql") {
					$sql = " SELECT LAST_INSERT_ID() ";
					$thread_id = get_db_value($sql);
				} else if ($db_type == "access") {
					$sql = " SELECT @@IDENTITY ";
					$thread_id = get_db_value($sql);
				}  else if ($db_type == "db2") {
					$thread_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "forum FROM " . $table_prefix . "forum");
				}

				// update forum information
				$sql  = " UPDATE " . $table_prefix . "forum_list ";
				$sql .= " SET last_post_added=" . $db->tosql($date_added, DATETIME) . ", threads_number=threads_number+1,";
				$sql .= " last_post_admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER) . ", ";
				$sql .= " last_post_thread_id=" . $db->tosql($thread_id, INTEGER) . ", ";
				$sql .= " last_post_user_id=0, last_post_message_id=0 ";
				$sql .= " WHERE forum_id=" . $db->tosql($new_forum_id, INTEGER);
				$db->query($sql);

				// check attachments
				$attachments = array();
				$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "forum_attachments ";
				$sql .= " WHERE forum_id=" . $db->tosql($new_forum_id, INTEGER);
				$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$sql .= " AND thread_id=0 ";
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);
				while ($db->next_record()) {
					$filename = $db->f("file_name");
					$filepath = $db->f("file_path");
					$attachments[] = array($filename, $filepath);
				}
    
				$sql  = " UPDATE " . $table_prefix . "forum_attachments ";
				$sql .= " SET thread_id=" . $db->tosql($thread_id, INTEGER);
				$sql .= " , attachment_status=1 ";
				$sql .= " WHERE forum_id=" . $db->tosql($new_forum_id, INTEGER);
				$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$sql .= " AND message_id=0 ";
				$sql .= " AND thread_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}	else if(strlen($r->get_value("thread_id"))) {
		$r->get_db_values();
	} else {
		// new item (set default values)
		$r->set_value("priority_id", $default_priority_id);
	}


	$r->set_parameters();

	// check attachments
	if (!$thread_id) {
		$attachments_files = "";
		$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "forum_attachments ";
		$sql .= " WHERE forum_id=" . $db->tosql($r->get_value("forum_id"), INTEGER);
		$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$sql .= " AND thread_id=0 ";
		$sql .= " AND message_id=0 ";
		$sql .= " AND attachment_status=0 ";
		$db->query($sql);
		while ($db->next_record()) {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$is_file_exists = file_exists($filepath);
			if (!$is_file_exists && file_exists("../" . $filepath)) {
				$filepath = "../" . $filepath;
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
	}

	// prepare icons to replace in the text
	prepare_icons($icons, $icons_codes, $icons_tags);

	// parse icons
	if ($icons_enable) {
		parse_icons("icons", $icons_cols, $icons_limit);
	}
	
	if(strlen($thread_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");

?>