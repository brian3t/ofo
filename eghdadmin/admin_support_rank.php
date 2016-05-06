<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_rank.php                                   ***
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

	check_admin_security("support_users_priorities");

	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_rank.html");

	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("date_format_msg", $date_format_msg);

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_rank_href",  "admin_support_rank.php");
	$t->set_var("admin_support_ranks_href", "admin_support_ranks.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", RANK_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "support_users_priorities");
	$r->return_page = "admin_support_ranks.php";

	$r->add_where("user_priority_id", INTEGER);

	$r->add_textbox("user_id", INTEGER, CUSTOMER_ID_MSG);
	$r->add_textbox("user_email", TEXT, CUSTOMER_EMAIL_MSG);
	$r->change_property("user_email", USE_SQL_NULL, false);
	$r->change_property("user_email", REGEXP_MASK, EMAIL_REGEXP);
	$support_priorities = get_db_values("SELECT priority_id, priority_name FROM " . $table_prefix . "support_priorities", array(array("", PRIORITY_MSG)));
	$r->add_select("priority_id", INTEGER, $support_priorities, PRIORITY_MSG);
	$r->change_property("priority_id", REQUIRED, true);
	$r->add_textbox("priority_expiry", DATETIME, EXPIRY_DATE_MSG);
	$r->change_property("priority_expiry", VALUE_MASK, $date_edit_format);

	// editing information
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_IN_INSERT, false);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->change_property("date_modified", USE_IN_INSERT, false);
	$r->add_hidden("page", TEXT);
	$r->add_hidden("s_ne", TEXT);
	$r->add_hidden("s_pt", TEXT);
	$r->add_hidden("s_ed", TEXT);

	$r->set_event(BEFORE_VALIDATE, "user_priority_validation");
	$r->set_event(BEFORE_INSERT, "user_priority_update");
	$r->set_event(BEFORE_UPDATE, "user_priority_update");
	$r->set_event(AFTER_SELECT, "user_priority_select");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

	function user_priority_validation()
	{
		global $r, $db, $table_prefix;
		if ($r->is_empty("user_id") && $r->is_empty("user_email")) {
			$r->change_property("user_email", REQUIRED, true);
		}
	}

	function user_priority_update()
	{
		global $r, $db, $table_prefix;

		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());

		if ($r->is_empty("user_id")) {
			$r->set_value("user_id", "0");
		}
	}

	function user_priority_select()
	{
		global $r, $db, $table_prefix;

		if ($r->get_value("user_id") == "0") {
			$r->set_value("user_id", "");
		}
	}

?>