<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_filters.php                                        ***
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

	check_admin_security("filters");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_filters.html");

	$t->set_var("admin_href", "admin.php");

	$admin_filter_url = new VA_URL("admin_filter.php", true);
	$t->set_var("admin_filter_new_url", $admin_filter_url->get_url());

	$admin_filter_url->add_parameter("filter_id", DB, "filter_id");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_filters.php");
	$s->set_sorter(ID_MSG, "sorter_filter_id", "1", "filter_id");
	$s->set_sorter(FILTER_NAME_MSG, "sorter_filter_name", "2", "filter_name");
	$s->set_sorter(TYPE_MSG, "sorter_filter_type", "3", "filter_type");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_filters.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "filters");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "filters " . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", "");
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("filter_id", $db->f("filter_id"));
		
			$filter_name = get_translation($db->f("filter_name"));
			$filter_type = $db->f("filter_type");

			$filter_desc = get_translation($db->f("filter_desc"));
			if (!$filter_desc) {
				$filter_desc = strip_tags(get_translation($db->f("block_desc")));
			}
			$words = explode(" ", $filter_desc);
			if(sizeof($words) > 9) {
				$filter_desc = "";
				for ($i = 0; $i < 9; $i++) {
					$filter_desc .= $words[$i] . " ";
				}
				$filter_desc .= " ...";
			} 

			$t->set_var("filter_name", $filter_name);
			$t->set_var("filter_type", $filter_type);
			$t->set_var("filter_desc", $filter_desc);

			$t->set_var("admin_filter_url", $admin_filter_url->get_url("admin_filter.php"));
			$t->set_var("admin_filter_properties_url", $admin_filter_url->get_url("admin_filter_properties.php"));


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