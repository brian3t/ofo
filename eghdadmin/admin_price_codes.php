<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_price_codes.php                                    ***
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

	check_admin_security("static_prices");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_price_codes.html");

	$t->set_var("admin_price_code_href", "admin_price_code.php");
	$t->set_var("admin_price_codes_href", "admin_price_codes.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_price_codes.php");
	$s->set_sorter(ID_MSG, "sorter_price_id", "1", "price_id");
	$s->set_sorter(TITLE_MSG, "sorter_price_title", "2", "price_title");
	$s->set_sorter(AMOUNT_MSG, "sorter_price_amount", "3", "price_amount");
	$s->set_sorter(DESCRIPTION_MSG, "sorter_price_description", "4", "price_description");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_price_codes.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");

	$error_message = ""; $success_message = "";

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "prices ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "prices " . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("price_id", $db->f("price_id"));
			$t->set_var("price_title", htmlspecialchars($db->f("price_title")));
			$t->set_var("price_amount", htmlspecialchars($db->f("price_amount")));
			$t->set_var("price_description", $db->f("price_description"));
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
