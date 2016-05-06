<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_footer_links.php                                   ***
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

	check_admin_security("footer_links");

	$search_block = trim(get_param("search_block"));

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_footer_links.html");

	$t->set_var("admin_href", "admin.php");
	
	$admin_footer_link_url = new VA_URL("admin_footer_link.php", true);
	$t->set_var("admin_footer_link_new_url", $admin_footer_link_url->get_url());

	$admin_footer_link_url->add_parameter("menu_id", DB, "menu_id");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_footer_links.php");
	$s->set_sorter(ID_MSG, "sorter_menu_id", "1", "menu_id");
	$s->set_sorter(MENU_TITLE_MSG, "sorter_menu_title", "2", "menu_title");
	$s->set_sorter(ADMIN_URL_SHORT_MSG, "sorter_menu_url", "3", "menu_url");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_footer_links.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "footer_links ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "footer_links " . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("menu_id", $db->f("menu_id"));
		
			$menu_title = get_translation($db->f("menu_title"));
			$menu_url = $db->f("menu_url");

			$t->set_var("menu_title",  $menu_title);
			$t->set_var("menu_url", $menu_url);

			$t->set_var("admin_footer_link_url", $admin_footer_link_url->get_url());


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