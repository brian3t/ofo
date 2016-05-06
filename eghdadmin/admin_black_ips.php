<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_black_ips.php                                      ***
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

	check_admin_security("black_ips");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_black_ips.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_black_ip_href", "admin_black_ip.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_black_ips.php");
	$s->set_sorter(IP_ADDRESS_MSG, "sorter_ip_address", 1, "ip_address");
	$s->set_sorter(ACTION_MSG, "sorter_address_action", 2, "address_action");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_black_ips.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "black_ips");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "black_ips" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$address_action = $db->f("address_action");
			if ($address_action == 1) {
				$address_action = BLOCK_ALL_ACTIVITIES_MSG;
			} else {
				$address_action = WARNING_MSG;
			}

			$t->set_var("ip_address", $db->f("ip_address"));
			$t->set_var("address_action", $address_action);

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
