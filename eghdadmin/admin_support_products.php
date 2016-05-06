<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_products.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_static_data");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_products.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_product_href", "admin_support_product.php");
	$t->set_var("admin_support_products_href", "admin_support_products.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_products.php");
	$s->set_sorter(ID_MSG, "sorter_product_id", "1", "product_id");
	$s->set_sorter(PROD_NAME_MSG, "sorter_product_name", "2", "product_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_products.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "support_products");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "support_products" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		do {
			$t->set_var("product_id", $db->f("product_id"));
			$t->set_var("product_name", $db->f("product_name"));
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
