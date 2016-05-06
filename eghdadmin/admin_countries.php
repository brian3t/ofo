<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_countries.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once("./admin_common.php");

	check_admin_security("static_tables");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_countries.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_country_href", "admin_country.php");
	$t->set_var("admin_settings_list", "admin_settings_list.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_countries.php");
	$s->set_default_sorting("1", "asc");
	$s->set_sorter(ADMIN_ORDER_MSG, "sorter_country_order", 1, "country_order");
	$s->set_sorter(COUNTRY_NAME_MSG, "sorter_country_name", 2, "country_name");
	$s->set_sorter(ISO_NUMBER_MSG, "sorter_country_iso_number", 3, "country_iso_number");
	$s->set_sorter(COUNTRY_CODE_MSG, "sorter_country_code", 4, "country_code");
	$s->set_sorter(COUNTRY_CODE_ALPHA3_MSG, "sorter_country_code_alpha3", 5, "country_code_alpha3");
	$s->set_sorter(SHOW_FOR_USER_MSG, "sorter_show_for_user", 6, "show_for_user");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_countries.php");

	$sp = trim(get_param("sp")); 
	$where = "";
	if (strlen($sp)) {
		$where  = " WHERE country_name LIKE '%" . $db->tosql($sp, TEXT, false) . "%'";
		$where .= " OR country_code=" . $db->tosql($sp, TEXT);
		$where .= " OR country_iso_number=" . $db->tosql($sp, TEXT);
	}

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "countries " . $where);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$t->set_var("page", $page_number);

	$t->set_var("sort_ord", get_param("sort_ord"));
	$t->set_var("sort_dir", get_param("sort_dir"));
	$t->set_var("page", get_param("page"));
	$t->set_var("sp", htmlspecialchars($sp));
	$t->set_var("sp_url", urlencode($sp));

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "countries " . $where . $s->order_by);
	if ($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("country_id", $db->f("country_id"));
			$t->set_var("country_name", get_translation($db->f("country_name")));
			$t->set_var("country_order", $db->f("country_order"));
			$t->set_var("country_iso_number", $db->f("country_iso_number"));
			$t->set_var("country_code", $db->f("country_code"));
			$t->set_var("country_code_alpha3", $db->f("country_code_alpha3"));
			$show_for_user = $db->f("show_for_user");
			if ($show_for_user) {
				$show_for_user = "<font color=\"blue\"><b>" . YES_MSG . "</b></font>";
			} else {
				$show_for_user = "<font color=\"silver\">" . NO_MSG . "</font>";
			} 
			$t->set_var("show_for_user", $show_for_user);

			$t->parse("records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>