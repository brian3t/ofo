<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_message.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");
	include_once("./admin_common.php");

	check_admin_security("forum");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_forum_message.html");

	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_thread_href", "admin_forum_thread.php");
	$t->set_var("admin_forum_message_href", "admin_forum_message.php");
	$t->set_var("admin_href", "admin.php");

	$r = new VA_Record($table_prefix . "forum_messages");

	$r->add_where("message_id", INTEGER);
	$r->add_textbox("date_modified", DATETIME);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->add_textbox("user_name", TEXT, NICKNAME_FIELD);
	$r->change_property("user_name", REQUIRED, true);
	$r->add_textbox("user_email", TEXT, EMAIL_FIELD);
	$r->change_property("user_email", REQUIRED, true);
	$r->add_textbox("remote_address", TEXT);
	$r->change_property("remote_address", USE_IN_UPDATE, false);
	$r->add_textbox("message_text", TEXT, MESSAGE_MSG);
	$r->change_property("message_text", REQUIRED, true);
	$r->get_form_parameters();

	$operation = get_param("operation");
	$message_id = get_param("message_id");
	$thread_id = "";
	$forum_id = "";
	if ($message_id) {
		$sql = "SELECT fm.thread_id, f.topic, f.forum_id, fl.forum_name, fl.category_id, fc.category_name ";
		$sql .= " FROM " . $table_prefix . "forum_messages fm, " . $table_prefix . "forum f, ";
		$sql .= $table_prefix . "forum_list fl, " . $table_prefix . "forum_categories fc ";
		$sql .= " WHERE fm.message_id = " . $db->tosql($message_id, INTEGER);
		$sql .= " AND fm.thread_id = f.thread_id AND f.forum_id = fl.forum_id AND fl.category_id = fc.category_id ";
		$db->query($sql);
		if ($db->next_record()) {
			$thread_id = $db->f("thread_id");
			$forum_id = $db->f("forum_id");
			$t->set_var("category_id", $db->f("category_id"));
			$t->set_var("current_category", $db->f("category_name"));
			$t->set_var("forum_id", $forum_id);
			$t->set_var("current_forum", $db->f("forum_name"));
			$t->set_var("thread_id", $thread_id);
			$t->set_var("topic", htmlspecialchars($db->f("topic")));
		}
	}
	$return_page = "admin_forum_thread.php?thread_id=" . $thread_id . "#m" . $message_id;
	$return_page_del = "admin_forum_thread.php?thread_id=" . $thread_id;

	if(strlen($operation)) {
		if($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		}	else if($operation == "delete" && $message_id) {
			$db->query("DELETE FROM " . $table_prefix . "forum_messages WHERE message_id = " . $db->tosql($message_id, INTEGER));
			// Update thread info
			$sql  = " UPDATE " . $table_prefix . "forum SET replies=replies-1 ";
			$sql .= " WHERE thread_id = " . $db->tosql($thread_id, INTEGER);
			$db->query($sql);

			// Update forum info
			$sql  = " UPDATE " . $table_prefix . "forum_list SET messages_number=messages_number-1 ";
			$sql .= " WHERE forum_id = " . $db->tosql($forum_id, INTEGER);
			$db->query($sql);

			// delete attachments
			$sql = "SELECT file_path FROM " . $table_prefix . "forum_attachments WHERE message_id=" . $db->tosql($r->get_value("message_id"), INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$file_path = $db->f("file_path");
				$is_file_exists = file_exists($file_path);
				if (!$is_file_exists && file_exists("../" . $file_path)) {
					$file_path = "../" . $file_path;
				}
				@unlink($file_path);
			}
	  
			$sql = "DELETE FROM " . $table_prefix . "forum_attachments WHERE message_id=" . $db->tosql($r->get_value("message_id"), INTEGER);
			$db->query($sql);

			header("Location: " . $return_page_del);
			exit;
		}

		$is_valid = $r->validate();

		if($is_valid && $message_id)
		{
			$date_modified = va_time();
			$admin_id = get_session("session_admin_id");
			$r->set_value("date_modified", $date_modified);
			$r->set_value("admin_id_modified_by", $admin_id);
			$r->update_record();
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($r->get_value("message_id")))
	{
		$r->get_db_values();
	}

	$r->set_form_parameters();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>