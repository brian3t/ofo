<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_tax_rates.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("tax_rates");


	$operation = get_param("operation");
	if ($operation == "default") {
		$tax_id = get_param("tax_id");
		$sql  = " SELECT country_id, state_id ";
		$sql .= " FROM " . $table_prefix . "tax_rates ";
		$sql .= " WHERE tax_id=" . $db->tosql($tax_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$country_id = $db->f("country_id");
			$state_id = $db->f("state_id");

			$sql  = " UPDATE " . $table_prefix . "tax_rates SET is_default=0 ";
			$sql .= " WHERE is_default=1 ";
			$sql .= " AND (country_id<>" . $db->tosql($country_id, INTEGER);
			$sql .= " OR state_id<>" . $db->tosql($state_id, INTEGER) . ") ";
			$db->query($sql);

			$sql  = " UPDATE " . $table_prefix . "tax_rates SET is_default=1 ";
			$sql .= " WHERE is_default=0 ";
			$sql .= " AND country_id=" . $db->tosql($country_id, INTEGER);
			$sql .= " AND state_id=" . $db->tosql($state_id, INTEGER);
			$db->query($sql);
		}
	}

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_tax_rates.html");
	$t->set_var("admin_tax_rates_href", "admin_tax_rates.php");
	$t->set_var("admin_tax_rate_href", "admin_tax_rate.php");

	$admin_tax_rate= new VA_URL("admin_tax_rate.php", false);
	$admin_tax_rate->add_parameter("page", REQUEST, "page");
	$admin_tax_rate->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_tax_rate->add_parameter("sort_dir", REQUEST, "sort_dir");

	$t->set_var("admin_tax_rate_new_url", $admin_tax_rate->get_url());

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_tax_rates.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_tax_id", "1", "tax_id");
	$s->set_sorter(TAX_NAME_MSG, "sorter_tax_name", "6", "tax_name");
	$s->set_sorter(COUNTRY_FIELD, "sorter_country_name", "2", "country_name");
	$s->set_sorter(STATE_FIELD, "sorter_state_name", "3", "state_name");
	$s->set_sorter(TAX_PERCENT_MSG, "sorter_tax_percent", "4", "tax_percent");
	$s->set_sorter(DEFAULT_MSG, "sorter_is_default", "5", "is_default");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_tax_rates.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "tax_rates");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT tr.tax_id,tr.tax_name,c.country_name,s.state_name,tr.tax_percent,tr.is_default ";
	$sql .= " FROM ((" . $table_prefix . "tax_rates tr ";
	$sql .= " INNER JOIN " . $table_prefix . "countries c ON c.country_id=tr.country_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "states s ON s.state_id=tr.state_id) ";
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$admin_tax_rate_default = new VA_URL("admin_tax_rates.php", true);
		$admin_tax_rate_default->add_parameter("operation", CONSTANT, "default");
		$admin_tax_rate_default->add_parameter("tax_id", DB, "tax_id");

		$admin_tax_rate->add_parameter("tax_id", DB, "tax_id");

		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		do {
			$t->set_var("tax_id", $db->f("tax_id"));
			$t->set_var("tax_name", get_translation($db->f("tax_name")));
			$t->set_var("country_name", get_translation($db->f("country_name")));
			$t->set_var("state_name", $db->f("state_name"));
			$t->set_var("tax_percent", number_format($db->f("tax_percent"), 3));
			$t->set_var("admin_tax_rate_default_url", $admin_tax_rate_default->get_url());
			$t->set_var("admin_tax_rate_url", $admin_tax_rate->get_url());

			$is_default = $db->f("is_default");
			if ($is_default) {
				$is_default = "<font color=\"blue\"><b>" . YES_MSG . "</b></font>";
			} else  {
				$is_default = "<font color=\"silver\">" . NO_MSG . "</font>";
			} 

			$t->set_var("is_default", $is_default);

			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->pparse("main");

?>
