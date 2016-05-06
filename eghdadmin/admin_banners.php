<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_banners.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path . "includes/record.php");

	check_admin_security("banners");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_banners.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_banner_href", "admin_banner.php");
	$t->set_var("admin_banners_href", "admin_banners.php");
	$t->set_var("admin_layouts_href", "admin_layouts.php");
	$t->set_var("admin_banners_groups_href", "admin_banners_groups.php");


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_banners.php");
	//$s->set_default_sorting(1, "desc");
	$s->set_sorter(ID_MSG, "sorter_banner_id", "1", "banner_id");
	$s->set_sorter(TITLE_MSG, "sorter_banner_title", "2", "banner_title");
	$s->set_sorter(IMPRESSIONS_MSG, "sorter_total_impressions", "3", "total_impressions");
	$s->set_sorter(CLICKS_MSG, "sorter_total_clicks", "4", "total_clicks");
	$s->set_sorter(EXPIRY_DATE_MSG, "sorter_expiry_date", "5", "expiry_date");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_banners.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$sql  = "SELECT COUNT(*) FROM " . $table_prefix . "banners ";
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);

	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT * FROM " . $table_prefix . "banners ";
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$banner_index = 0;
		do {
			$is_active = $db->f("is_active");
			$active_status = ($is_active == 1) ? "<b>" . YES_MSG . "</b>": NO_MSG;
			$max_impressions = $db->f("max_impressions");
			$max_clicks = $db->f("max_clicks");
			$total_impressions = $db->f("total_impressions");
			$total_clicks= $db->f("total_clicks");

			$expiry_date = "";
			$is_expired = false;
			$expiry_date_db = $db->f("expiry_date", DATETIME);
			if(is_array($expiry_date_db)) {
				$expiry_date = va_date($date_show_format, $expiry_date_db);
				$expiry_date_ts = mktime (0,0,0, $expiry_date_db[MONTH], $expiry_date_db[DAY], $expiry_date_db[YEAR]);
				$current_date_ts = va_timestamp();
				if($current_date_ts > $expiry_date_ts) {
					$is_expired = true;
				}
			} 
			if (!$is_active) {
				$banner_status = "<font color=silver>" . INACTIVE_MSG . "</font>";
			} else if ($max_impressions > 0 && $max_impressions <= $total_impressions) {
				$banner_status = "<font color=green>" . MAX_CLICKS_MSG . "</font>";
			} else if ($max_clicks > 0 && $max_clicks <= $total_clicks) {
				$banner_status = "<font color=green>". MAX_IMPRESSIONS_MSG ."</font>";
			} else if ($is_expired) {
				$banner_status = "<font color=red>" . EXPIRED_MSG . "</font>";
			} else {
				$banner_status = "<font color=blue>" . ACTIVE_MSG . "</font>";
			}

			$banner_index++;
			$row_style = ($banner_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			$t->set_var("banner_index", $banner_index);
			$t->set_var("banner_id", $db->f("banner_id"));
			$t->set_var("banner_title", $db->f("banner_title"));
			$t->set_var("active_status", $active_status);
			$t->set_var("expiry_date", $expiry_date);
			$t->set_var("banner_status", $banner_status);
			$t->set_var("total_impressions", $db->f("total_impressions"));
			$t->set_var("total_clicks", $db->f("total_clicks"));
			$t->set_var("max_impressions", $db->f("max_impressions"));
			$t->set_var("max_clicks", $db->f("max_clicks"));

			$t->parse("records", true);
		} while($db->next_record());
		$t->parse("sorters", false);
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>