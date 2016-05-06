<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_release_type.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_release_type.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_release_types_href", "admin_release_types.php");
	$t->set_var("admin_release_type_href", "admin_release_type.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", RELEASE_TYPE_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "release_types");
	$r->return_page = "admin_release_types.php";
	

	$r->add_where("type_id", INTEGER);

	$r->add_textbox("type_name", TEXT, TYPE_NAME_MSG );
	$r->change_property("type_name", REQUIRED, true);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
