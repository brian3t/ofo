<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_currencies.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("static_tables");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_currencies.html");

	$t->set_var("admin_currency_href", "admin_currency.php");
	$t->set_var("admin_currencies_href", "admin_currencies.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_currencies.php");
	$s->set_sorter(ID_MSG, "sorter_currency_id", "1", "currency_id");
	$s->set_sorter(CURRENCY_TITLE_MSG, "sorter_currency_title", "2", "currency_title");
	$s->set_sorter(CODE_MSG, "sorter_currency_code", "3", "currency_code");
	$s->set_sorter(EXCHANGE_RATE_MSG, "sorter_exchange_rate", "4", "exchange_rate");
	$s->set_sorter(DEFAULT_MSG, "sorter_is_default", "5", "is_default");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_currencies.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");

	$error_message = ""; $success_message = "";
	if ($operation == "currencysource") {
		include_once("./admin_currencysource.php");
	}

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "currencies ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "currencies " . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("currency_id", $db->f("currency_id"));
			$t->set_var("currency_title", htmlspecialchars($db->f("currency_title")));
			$t->set_var("currency_code", htmlspecialchars($db->f("currency_code")));
			$t->set_var("exchange_rate", $db->f("exchange_rate"));

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

	if ($error_message) {
		$t->set_var("errors_list", $error_message);
		$t->sparse("errors", false);
	} 

	if ($success_message) {
		$t->set_var("success_message", $success_message);
		$t->sparse("success", false);
	} 

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");
?>
