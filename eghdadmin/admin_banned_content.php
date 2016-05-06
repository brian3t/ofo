<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_banned_content.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("banned_contents");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_banned_content.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_banned_contents_href", "admin_banned_contents.php");
	$t->set_var("admin_banned_content_href", "admin_banned_content.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", BANNED_CONTENT, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "banned_contents");
	$r->return_page = "admin_banned_contents.php";
	$r->add_where("content_id", INTEGER);

	$r->add_textbox("content_text", TEXT, NOTES_MSG);
	$r->change_property("content_text", REQUIRED, true);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
