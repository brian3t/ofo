<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_shipping_rules.php                                 ***
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

	check_admin_security("shipping_rules");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_shipping_rules.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_shipping_rule_href", "admin_shipping_rule.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_shipping_rules.php");
	$s->set_sorter(ID_MSG, "sorter_shipping_rule_id", "1", "shipping_rule_id");
	$s->set_sorter(SHIPPING_RULE_MSG, "sorter_shipping_rule_desc", "2", "shipping_rule_desc");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_shipping_rules.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "shipping_rules");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "shipping_rules" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("shipping_rule_id", $db->f("shipping_rule_id"));
			$t->set_var("shipping_rule_desc", get_translation($db->f("shipping_rule_desc")));
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
