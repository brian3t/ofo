<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_pages.php                                          ***
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

	check_admin_security("web_pages");
	
	$search_page = trim(get_param("search_page"));

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_pages.html");

  if (strlen($search_page) > 0)
	{
		$sql_where = " WHERE page_code LIKE '%$search_page%' OR page_title LIKE '%$search_page%' OR page_body LIKE '%$search_page%' ";
		$t->set_var("search_page",$search_page);
	}
	else 
	{
		$sql_where = "";
		$t->set_var("search_page","");
	}

  
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_page_href", "admin_page.php");
	$t->set_var("admin_layout_page_href", "admin_layout_page.php");
	$t->set_var("rp_url", "admin_pages.php");


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_pages.php");
	$s->set_sorter(ID_MSG, "sorter_page_id", "1", "page_id");
	$s->set_sorter(PAGE_NAME_MSG, "sorter_page_title", "2", "page_title");
	$s->set_sorter(PAGE_CODE_MSG, "sorter_page_code", "3", "page_code");
	$s->set_sorter(SHOW_ON_SITE_MSG, "sorter_is_showing", "4", "is_showing");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_pages.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "pages" . $sql_where);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT page_id,page_code,page_title,is_showing FROM " . $table_prefix . "pages " . $sql_where  . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("page_id", $db->f("page_id"));
			$t->set_var("page_title", get_translation($db->f("page_title")));
			$t->set_var("page_code", $db->f("page_code"));
			$is_showing = $db->f("is_showing") ? "Yes" : "No";
			$t->set_var("is_showing", $is_showing);
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