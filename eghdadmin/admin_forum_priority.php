<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_priority.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path."includes/record.php");
	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("forum");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_forum_priority.html");

	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_priority_href", "admin_forum_priority.php");
	$t->set_var("admin_forum_priorities_href", "admin_forum_priorities.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", PRIORITY_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "forum_priorities");
	$r->return_page = "admin_forum_priorities.php";

	$r->add_where("priority_id", INTEGER);

	$r->add_checkbox("is_default", INTEGER);
	$r->add_textbox("priority_rank", INTEGER, PRIORITY_RANK_MSG);
	$r->change_property("priority_rank", REQUIRED, true);
	$r->add_textbox("priority_name", TEXT, PRIORITY_NAME_MSG);
	$r->parameters["priority_name"][REQUIRED] = true;
	$r->add_textbox("html_before_title", TEXT, BEFORE_TITLE_HTML_MSG);
	$r->add_textbox("html_after_title", TEXT, AFTER_TITLE_HTML_MSG);
	$r->set_event(BEFORE_INSERT, "set_priority_fields");
	$r->set_event(BEFORE_UPDATE, "set_priority_fields");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

	function set_priority_fields()
	{
		global $r, $db, $table_prefix;

		if ($r->get_value("is_default") == 1) {
			$sql = " UPDATE " . $table_prefix . "forum_priorities SET is_default=0 ";
			$db->query($sql);
		}

	}

?>