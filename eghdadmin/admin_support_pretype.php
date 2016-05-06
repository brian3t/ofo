<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_pretype.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_predefined_reply");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_support_pretype.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$confirm_delete = str_replace(array("{record_name}", "\'"), array(TYPE_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_prereplies_href", "admin_support_prereplies.php");
	$t->set_var("admin_support_pretype_href",  "admin_support_pretype.php");
	$t->set_var("admin_support_pretypes_href",  "admin_support_pretypes.php");

	$r = new VA_Record($table_prefix . "support_predefined_types");
	$r->return_page = "admin_support_pretypes.php";

	$r->add_where("type_id", INTEGER);

	$r->add_textbox("type_name", TEXT, TYPE_NAME_MSG);
	$r->change_property("type_name", REQUIRED, true);

	$r->add_hidden("page", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("s_ut", INTEGER);
	$r->add_hidden("s_g", INTEGER);

	$r->process();

	$t->pparse("main");

?>