<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_type.php                                   ***
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

	check_admin_security("support_static_data");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_type.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_type_href", "admin_support_type.php");
	$t->set_var("admin_support_types_href", "admin_support_types.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", TYPE_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "support_types");
	$r->return_page = "admin_support_types.php";

	$r->add_where("type_id", INTEGER);

	$r->add_textbox("type_name", TEXT, TYPE_NAME_MSG );
	$r->parameters["type_name"][REQUIRED] = true;
	$r->add_checkbox("show_for_user", INTEGER);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>