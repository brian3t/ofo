<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_prereply.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	// get permissions
	$permissions = get_permissions();
	$allow_predefined_edit  = get_setting_value($permissions, "support_predefined_reply", 0); 

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");
	$is_popup = get_param("is_popup");
	$operation = get_param("operation");
	$reply_id = get_param("reply_id");
	if ($operation == "use" && $reply_id) {
		$sql  = " SELECT total_uses, body FROM " . $table_prefix . "support_predefined ";
		$sql .= " WHERE reply_id=" . $db->tosql($reply_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$total_uses = $db->f("total_uses");
			$body = $db->f("body");

			$sql  = " UPDATE " . $table_prefix . "support_predefined ";
			if ($total_uses > 0) {
				$sql .= " SET total_uses=total_uses+1 ";
			} else {
				$sql .= " SET total_uses=1 ";
			}
			$sql .= " WHERE reply_id=" . $db->tosql($reply_id, INTEGER);
			$db->query($sql);

			echo $body;
		}
		// script run with AJAX so call exit
		exit;
	}


	$admin_support_prereplies = new VA_URL("admin_support_prereplies.php", false);
	$admin_support_prereplies->add_parameter("s_kw", REQUEST, "s_kw");
	$admin_support_prereplies->add_parameter("s_type", REQUEST, "s_type");
	$admin_support_prereplies->add_parameter("is_popup", REQUEST, "is_popup");
	$admin_support_prereplies->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_support_prereplies->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_support_prereplies->add_parameter("page", REQUEST, "page");

  $t = new VA_Template($settings["admin_templates_dir"]);
	if ($is_popup) {
	  $t->set_file("main","admin_support_prereply_popup.html");
	} else {
	  $t->set_file("main","admin_support_prereply.html");
	}

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_prereply_href", "admin_support_prereply.php");
	$t->set_var("admin_support_prereplies_href", "admin_support_prereplies.php");
	$t->set_var("admin_support_prereplies_url", $admin_support_prereplies->get_url());
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SUPPORT_REPLY_FORM_TITLE, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "support_predefined");
	$r->return_page = "admin_support_prereplies.php";
	$r->add_hidden("s_kw", TEXT);
	$r->add_hidden("s_type", TEXT);
	$r->add_hidden("is_popup", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("page", TEXT);

	$r->add_where("reply_id", INTEGER);
	$sql  = " SELECT type_id, type_name FROM " . $table_prefix . "support_predefined_types ";
	$sql .= " ORDER BY type_name ";
	$reply_types= get_db_values($sql, array(array("", SELECT_REPLY_TYPE_MSG)));
	$r->add_select("type_id", TEXT, $reply_types, TYPE_MSG);
	$r->parameters["type_id"][REQUIRED] = true;
	$r->add_textbox("subject", TEXT, ADMIN_TITLE_MSG);
	$r->parameters["subject"][REQUIRED] = true;
	$r->add_textbox("body", TEXT, BODY_MSG);
	$r->change_property("body", REQUIRED, true);

	$r->add_textbox("total_uses", INTEGER);
	$r->change_property("total_uses", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_IN_INSERT, false);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->change_property("date_modified", USE_IN_INSERT, false);
	$r->add_textbox("last_updated", DATETIME);

	$r->set_event(BEFORE_INSERT,   "set_admin_data");
	$r->set_event(BEFORE_UPDATE,   "set_admin_data");
	$r->set_event(BEFORE_DELETE,   "check_predefined_permissions");
	$r->set_event(BEFORE_VALIDATE, "check_predefined_permissions");


	$r->process();
	if ($allow_predefined_edit) {
		$t->parse("edit_button", false);
	} else {
		$t->set_var("delete", "");
	}
	$t->set_var("is_popup",   $is_popup);

	if (!$is_popup) { 						
		include_once("./admin_header.php");
		include_once("./admin_footer.php");
 	}	
	$t->set_var("admin_href", "admin.php");

	$t->pparse("main");

	function check_predefined_permissions()
	{
		global $allow_predefined_edit;
		if (!$allow_predefined_edit) {
			die(PREDEFINED_REPLIES_NOT_ALLOWED_MSG);
		}
	}

	function set_admin_data() {
		global $r;

		$r->set_value("total_uses", 0);
		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());
		$r->set_value("last_updated", va_time());
	}


?>