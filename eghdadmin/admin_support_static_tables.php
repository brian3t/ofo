<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_static_tables.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_static_tables.html");

	$t->set_var("admin_href",                   "admin.php");
	$t->set_var("admin_support_href",           "admin_support.php");
	$t->set_var("admin_support_types_href",     "admin_support_types.php");
	$t->set_var("admin_support_products_href",  "admin_admins.php");
	$t->set_var("admin_support_priorities_href","admin_support_priorities.php");
	$t->set_var("admin_support_statuses_href",  "admin_support_statuses.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>