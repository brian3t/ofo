<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_lost.php                                  ***
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
	include_once($root_folder_path . "includes/articles_functions.php");

	check_admin_security("articles_lost");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_articles_lost.html");	
	$t->set_var("articles_lost", "admin_articles_lost.php");
	
	$parent_category_id = get_param("parent_category_id");
	$operation          = get_param("operation");
	$articles_ids       = get_param("articles_ids");
	if ($operation == "delete") {
		VA_Articles::delete($articles_ids);		
	} elseif ($operation == "move") {
		if ($articles_ids && $parent_category_id) {
			$articles_ids = explode(",", $articles_ids);
			$sql  = " SELECT MAX(article_order) FROM ". $table_prefix . "articles_assigned";
			$sql .= " WHERE category_id=" . $db->tosql($parent_category_id, INTEGER);			
			$article_order = get_db_value($sql);
			foreach ($articles_ids AS $article_id) {
				$article_order++;
				$sql  = " INSERT INTO " . $table_prefix . "articles_assigned";
				$sql .= " (article_id, category_id, article_order) VALUES (";
				$sql .= $db->tosql($article_id, INTEGER) . ",";
				$sql .= $db->tosql($parent_category_id, INTEGER) . ",";
				$sql .= $db->tosql($article_order, INTEGER) . ")";
				$db->query($sql);				
			}
		}
	}
	
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_articles_lost.php");
	$s->set_sorter(ID_MSG, "sorter_article_id", "1", "article_id");
	$s->set_sorter(ARTICLE_TITLE_MSG, "sorter_article_title", "2", "article_title");
	$s->set_sorter(STATUS_MSG, "sorter_status", "3", "status_name");
	$s->set_sorter(DATE_ADDED_MSG, "sorter_date_added", "4", "date_added");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_articles_lost.php");
	
	
	$sql  = " SELECT COUNT(a.article_id) ";
	$sql .= " FROM (" . $table_prefix . "articles a ";
	$sql .= " LEFT OUTER JOIN " . $table_prefix . "articles_assigned aa ON a.article_id = aa.article_id) ";	
	$sql .= " WHERE aa.category_id IS NULL ";
	$total_records = get_db_value($sql);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	$sql  = " SELECT a.article_id, a.article_title, a.date_added, st.status_name, st.allowed_view ";
	$sql .= " FROM ((" . $table_prefix . "articles a ";
	$sql .= " LEFT OUTER JOIN " . $table_prefix . "articles_assigned aa ON a.article_id = aa.article_id) ";	
	$sql .= " LEFT JOIN " . $table_prefix . "articles_statuses st ON a.status_id=st.status_id) ";
	$sql .= " WHERE aa.category_id IS NULL ";	
	$db->query($sql . $s->order_by);
	
	$articles_ids = array();
	if ($db->next_record()) {
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		$index = 0;
		do {
			$index++;
			$t->set_var("index", $index);
			$t->set_var("row_class", $index % 2 + 1);
			$article_id = $db->f("article_id");
			$articles_ids[] = $article_id;
			$t->set_var("article_id", $article_id);
			$t->set_var("article_title", get_translation($db->f("article_title")));
			
			$article_status = $db->f("status_name");
			$allowed_view = $db->f("allowed_view");
			if ($allowed_view == 0) {
				$status_color = "silver";
			} elseif ($allowed_view == 1) {
				$status_color = "blue";
			} else {
				$status_color = "black";
			}
			$article_status = "<font color=\"" . $status_color . "\">" . $article_status . "</font>";
			$t->set_var("article_status", $article_status);
			
			$date_added = $db->f("date_added", DATETIME);
			$date_added = va_date($datetime_show_format, $date_added);
			$t->set_var("date_added", $date_added);
						
			$t->parse("records");			
		} while ($db->next_record());	
		
		$sql  = " SELECT category_id, category_name FROM " . $table_prefix . "articles_categories ";
		$sql .= " WHERE parent_category_id = 0";
		$sql .= " ORDER BY category_order";
		$parent_categories = get_db_values($sql, null);
	
		if ($parent_categories) {
			set_options($parent_categories, $parent_category_id, "parent_category_id");
			$t->parse("move_articles_block");
		}
		$t->set_var("articles_ids", implode(",", $articles_ids));
		$t->set_var("articles_number", count($articles_ids));
	} else {
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	$t->pparse("main");
?>