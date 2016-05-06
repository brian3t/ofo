<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_top.php                                   ***
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

	check_admin_security("articles");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_articles_top.html");

	$t->set_var("admin_href",                   "admin.php");
	$t->set_var("admin_articles_href",          "admin_articles.php");
	$t->set_var("admin_article_category_items_related_href", "admin_article_category_items_related.php");
	$t->set_var("admin_articles_category_href", "admin_articles_category.php");
	$t->set_var("admin_layout_page_href",       "admin_layout_page.php");
	$t->set_var("admin_reviews_href",           "admin_reviews.php");


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_articles_top.php");
	$s->set_sorter(ID_MSG, "sorter_category_id", "1", "category_id");
	$s->set_sorter(ARTICLES_TYPE_MSG, "sorter_category_name", "2", "category_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_articles_top.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "articles_categories WHERE parent_category_id=0 ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT ac.category_id, ac.category_name ";
	$sql .= " FROM " . $table_prefix . "articles_categories ac ";
	$sql .= " WHERE ac.parent_category_id=0 ";
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$t->set_var("category_id", $db->f("category_id"));
			$t->set_var("category_name", get_translation($db->f("category_name")));
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>
