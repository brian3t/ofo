<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_days.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	check_admin_security("ads");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_ads_days.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_ads_href", "admin_ads.php");
	$t->set_var("admin_ads_day_href", "admin_ads_day.php");
	$t->set_var("admin_ads_features_default_href", "admin_ads_features_default.php");
	$t->set_var("admin_ads_properties_default_href", "admin_ads_properties_default.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_ads_days.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_days_id", "1", "days_id");
	$s->set_sorter(DAYS_NUMBER_MSG, "sorter_days_number", "2", "days_number");
	$s->set_sorter(DAYS_TITLE_MSG, "sorter_days_title", "3", "days_title");
	$s->set_sorter(DAYS_PRICE_MSG, "sorter_publish_price", "4", "publish_price");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_ads_days.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "ads_days");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "ads_days" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$days_title = get_translation($db->f("days_title"));
			$publish_price = $db->f("publish_price");
			$t->set_var("days_id", $db->f("days_id"));
			$t->set_var("days_number", $db->f("days_number"));
			$t->set_var("days_title", $days_title);
			if ($publish_price != 0) {
				$t->set_var("publish_price", currency_format($publish_price));
			} else {
				$t->set_var("publish_price", "");
			}
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>
