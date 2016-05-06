<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_status.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("articles_statuses");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_articles_status.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_articles_statuses_href", "admin_articles_statuses.php");
	$t->set_var("admin_articles_status_href", "admin_articles_statuses.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", STATUS_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "articles_statuses");
	$r->return_page = "admin_articles_statuses.php";

	$r->add_where("status_id", INTEGER);

	$r->add_checkbox("is_shown", INTEGER);
	$r->parameters["is_shown"][DEFAULT_VALUE] = 1;
	$r->add_textbox("status_name", TEXT, STATUS_NAME_MSG);
	$r->parameters["status_name"][REQUIRED] = true;
	$r->add_textbox("status_description", TEXT, STATUS_DESCRIPTION_MSG);

	$allowed_values = array(array("0", NOBODY_MSG), array("1", FOR_ALL_USERS_MSG), array("2", ONLY_REGISTERED_USERS_MSG));
	$allowed_values = array(array("0", NOBODY_MSG), array("1", FOR_ALL_USERS_MSG));
	$r->add_radio("allowed_view", INTEGER, $allowed_values, ALLOW_VIEW_MSG);
	$r->parameters["allowed_view"][REQUIRED] = true;
	$r->parameters["allowed_view"][DEFAULT_VALUE] = 1;

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
