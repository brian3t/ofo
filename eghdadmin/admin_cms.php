<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_cms.php                                            ***
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

	include_once("./admin_common.php");

	check_admin_security("cms_settings");

	$va_license_code = va_license_code();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_cms.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_layout_header_href", "admin_layout_header.php");
	$t->set_var("admin_layout_page_href", "admin_layout_page.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_layouts_url", urlencode("admin_layouts.php"));
	$t->set_var("admin_cms_url", urlencode("admin_cms.php"));

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_layouts.php");
	$s->set_sorter(ID_MSG, "sorter_layout_id", "1", "layout_id");
	$s->set_sorter(LAYOUT_NAME_MSG, "sorter_layout_name", "2", "layout_name");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_layouts.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$param_site_id = get_session("session_site_id");
	// parse articles categories
	if ($va_license_code & 2) {
		$t->set_var("articles", "");
		
		$sql  = " SELECT COUNT(ac.category_id) ";
		$sql .= " FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS st ON st.category_id = ac.category_id ";
		$sql .= " WHERE ac.parent_category_id=0 ";
		$sql .= " AND (ac.sites_all=1 OR st.site_id=".$db->tosql($param_site_id, INTEGER).") ";
		
		$db->query($sql);
		$db->next_record();
		$total_records = $db->f(0);
		$records_per_page = 24;
		$pages_number = 5;
		$page_number = $n->set_navigator("articles_navigator", "articles_page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
  
		$db->RecordsPerPage = $records_per_page;
		$db->PageNumber = $page_number;
		
		$sql  = " SELECT ac.category_id, ac.category_name ";
		$sql .= " FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS st ON st.category_id = ac.category_id ";
		$sql .= " WHERE ac.parent_category_id=0 ";
		$sql .= " AND (ac.sites_all=1 OR st.site_id=".$db->tosql($param_site_id, INTEGER).") ";
		$sql .= " GROUP BY  ac.category_id, ac.category_name ";
		$db->query($sql);
		
		if($db->next_record()) 
		{
			do
			{
				$art_cat_id = $db->f("category_id");
				$articles_category = get_translation($db->f("category_name"));
  
				$t->set_var("art_cat_id", $art_cat_id);
				$t->set_var("articles_category", $articles_category);
  
				$t->parse("articles", true);
			} while($db->next_record());
		} 
		$t->sparse("articles_pages", false);
	}

  // shop - 1, cms - 2, helpdesk - 4, forum - 8, ads - 16, manuals - 32
	if ($va_license_code & 1) {
		$t->sparse("products_pages", false);
		$t->sparse("users_pages", false);
	}
	if ($va_license_code & 4) {
		$t->sparse("helpdesk_pages", false);
	}
	if ($va_license_code & 8) {
		$t->sparse("forum_pages", false);
	}
	if ($va_license_code & 16) {
		$t->sparse("ads_pages", false);
	}
	if ($va_license_code & 32) {
		$t->sparse("manuals_pages", false);
	}

	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	$t->pparse("main");

?>