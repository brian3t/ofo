<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_states.php                                         ***
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

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_states.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_state_href", "admin_state.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_states.php");
	$s->set_sorter(ID_MSG, "sorter_state_id", "1", "state_code");
	$s->set_sorter(STATE_CODE_MSG, "sorter_state_code", "2", "state_code");
	$s->set_sorter(STATE_NAME_MSG, "sorter_state_name", "3", "state_name");
	$s->set_sorter(COUNTRY_NAME_MSG, "sorter_country_name", "4", "country_name");
	$s->set_sorter(SHOW_FOR_USER_MSG, "sorter_show_for_user", "5", "show_for_user");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_states.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "states");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 26;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT s.state_id,s.state_code,s.state_name,c.country_name,s.show_for_user ";
	$sql .= " FROM (" . $table_prefix . "states s ";
	$sql .= " LEFT JOIN " . $table_prefix . "countries c ON s.country_id=c.country_id) ";
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("state_id", $db->f("state_id"));
			$t->set_var("state_code", $db->f("state_code"));
			$t->set_var("state_name", get_translation($db->f("state_name")));
			$t->set_var("country_name", get_translation($db->f("country_name")));
			$show_for_user = $db->f("show_for_user");
			if ($show_for_user) {
				$show_for_user = "<font color=\"blue\"><b>" . YES_MSG . "</b></font>";
			} else  {
				$show_for_user = "<font color=\"silver\">" . NO_MSG . "</font>";
			} 
			$t->set_var("show_for_user", $show_for_user);

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
