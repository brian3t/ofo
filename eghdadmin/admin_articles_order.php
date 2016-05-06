<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_order.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("articles");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_articles_order.html");

	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_articles_order_href", "admin_articles_order.php");

	$parent_category_id = get_param("parent_category_id");
	if(!$parent_category_id) $parent_category_id = 0;

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "articles_categories", "tree", "");
	$tree->show($parent_category_id);

	$articles = array();

	$operation = get_param("operation");
	$return_page = "admin_articles.php?category_id=" . $parent_category_id;

	if (strlen($operation))
	{
		if ($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		}
		if ($operation == "save") {
			$articles_list = get_param("articles_list");
			if ($articles_list) {
				$articles_ids = split(",", $articles_list);
				for ($i = 0; $i < sizeof($articles_ids); $i++) {
					$sql  = " UPDATE " . $table_prefix . "articles_assigned ";
					$sql .= " SET article_order = " . intval($i + 1);
					$sql .= " WHERE category_id = " . $db->tosql($parent_category_id, INTEGER);
					$sql .= " AND article_id = " . $articles_ids[$i];
					$db->query($sql);
				}
			}
			header("Location: " . $return_page);
			exit;
		}
	} else {
		$sql  = " SELECT a.article_id, a.article_title ";
		$sql .= " FROM (" . $table_prefix . "articles a ";
		$sql .= " LEFT JOIN " . $table_prefix . "articles_assigned aa ON a.article_id=aa.article_id)";
		$sql .= " WHERE aa.category_id = " . $db->tosql($parent_category_id, INTEGER);
		$sql .= " ORDER BY aa.article_order, a.article_order, a.article_id DESC ";
		$db->query($sql);
		while($db->next_record()) {
			$article_id = $db->f("article_id");
			$article_order = $db->f("article_order");
			$article_title = get_translation($db->f("article_title"));
			$articles[] = array($article_id, $article_title);
		}
	}

	set_options($articles, "", "articles");

	$t->set_var("errors", "");
	$t->set_var("parent_category_id", $parent_category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>