<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_message.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_message.html");

	$admin_support_url = new VA_URL("admin_support.php", false);
	$admin_support_url->add_parameter("s_w", REQUEST, "s_w");
	$admin_support_url->add_parameter("s_s", REQUEST, "s_s");
	$admin_support_url->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_support_url->add_parameter("sort_dir", REQUEST, "sort_dir");
	$t->set_var("admin_support_url", $admin_support_url->get_url());
	$admin_support_url->add_parameter("support_id", REQUEST, "support_id");
	$return_page = $admin_support_url->get_url("admin_support_reply.php");
	$t->set_var("admin_support_reply_url", $return_page);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", MESSAGE_MSG, CONFIRM_DELETE_MSG));

	$operation = get_param("operation");
	$message_id = get_param("message_id");
	$support_id = get_param("support_id");
	if($operation != "delete") {
		$return_page .= "#" . $message_id;
	}

	$r = new VA_Record($table_prefix . "support_messages");
	$r->return_page = $return_page;

	$r->add_where("message_id", INTEGER);
	$r->add_where("support_id", INTEGER);

	$r->add_hidden("s_w", TEXT);
	$r->add_hidden("s_s", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);

	$support_statuses = get_db_values("SELECT * FROM " . $table_prefix . "support_statuses", "");
	$r->add_checkbox("internal", INTEGER);
	$r->add_select("support_status_id", INTEGER, $support_statuses, STATUS_MSG);
	$r->parameters["support_status_id"][REQUIRED] = true;
	$r->add_textbox("message_text", TEXT, MESSAGE_MSG);
	$r->parameters["message_text"][REQUIRED] = true;

	// editing information
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_IN_INSERT, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->change_property("date_modified", USE_IN_INSERT, false);
	$r->events[BEFORE_UPDATE] = "set_message_fields";
	$r->events[BEFORE_DELETE] = "remove_message_attachments";
	

/*
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_viewed", DATETIME);
	$r->change_property("date_viewed", USE_IN_UPDATE, false);
*/

	$r->process();

	$sql = " SELECT summary FROM " . $table_prefix . "support WHERE support_id=" . $db->tosql($support_id, INTEGER);
	$summary = get_db_value($sql);
	$t->set_var("support_request", htmlspecialchars($summary));
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_reply_href", "admin_support_reply.php");
	$t->set_var("admin_support_message_href", "admin_support_message.php");
	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

	function remove_message_attachments()
	{
		global $db, $table_prefix, $r;

		$sql = "SELECT file_path FROM " . $table_prefix . "support_attachments WHERE message_id=" . $db->tosql($r->get_value("message_id"), INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$file_path = $db->f("file_path");
			@unlink($file_path);
		}

		$sql = "DELETE FROM " . $table_prefix . "support_attachments WHERE message_id=" . $db->tosql($r->get_value("message_id"), INTEGER);
		$db->query($sql);
	}

	function set_message_fields()
	{
		global $r;

		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_modified", va_time());
	}

?>