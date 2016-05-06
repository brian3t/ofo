<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_hot_day.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("ads");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_ads_hot_day.html");

	$t->set_var("admin_href",           "admin.php");
	$t->set_var("admin_ads_href",       "admin_ads.php");
	$t->set_var("admin_ads_hot_days_href", "admin_ads_hot_days.php");
	$t->set_var("admin_ads_hot_day_href",  "admin_ads_hot_day.php");

	$r = new VA_Record($table_prefix . "ads_hot_days");
	$r->return_page = "admin_ads_hot_days.php";
	
	$r->add_where("days_id", INTEGER);

	$r->add_textbox("days_number", INTEGER, DAYS_NUMBER_MSG);
	$r->change_property("days_number", REQUIRED, true);
	$r->add_textbox("days_title", TEXT, DAYS_TITLE_MSG);
	$r->add_textbox("publish_price", NUMBER, ADS_PUBLISH_PRICE_MSG);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
