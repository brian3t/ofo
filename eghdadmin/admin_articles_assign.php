<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_assign.php                                ***
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

	$article_id = get_param("article_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_articles_assign.html");

	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_articles_assign_href", "admin_articles_assign.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_article_href",  "admin_article.php");

	$sql  = " SELECT article_title FROM " . $table_prefix . "articles ";
	$sql .= " WHERE article_id = " . $db->tosql($article_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("article_title", get_translation($db->f("article_title")));
	} else {
		die(OBJECT_NO_EXISTS_MSG);
	}

	$category_id = get_param("category_id");
	$top_id = 0;
	if (!strlen($category_id)) {
		header("Location: admin.php");
		exit;
	} else {
		$sql  = " SELECT category_path,parent_category_id ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";
		$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$parent_category_id = $db->f("parent_category_id");
			$category_path = $db->f("category_path");
			if ($parent_category_id == 0) {
				$top_id = $category_id;
			} else {
				$categories_ids = explode(",", $category_path);
				$top_id = $categories_ids[1];
			}
		}
	}


	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "articles_categories", "tree", "");
	$tree->show($category_id);

	$operation = get_param("operation");
	$return_page = "admin_articles.php?category_id=" . $category_id;
	$errors = "";

	if($operation == "cancel")
	{
		header("Location: " . $return_page);
		exit;
	}
	elseif ($operation == "save")
	{
		$categories = get_param("categories");
		if (!strlen($categories)) {
			$errors .= NO_CATEGORIES_SELECTED_MSG . "<br>";
		}
		if (!strlen($errors))
		{
			$categories = split(",", $categories);
			$sql  = " DELETE FROM " . $table_prefix . "articles_assigned ";
			$sql .= " WHERE article_id = " . $db->tosql($article_id, INTEGER);
			if ($categories) {
				$sql .= " AND category_id NOT IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
			}
			$db->query($sql);
			
			if ($categories) {
				$sql  = " SELECT category_id FROM " . $table_prefix . "articles_assigned ";
				$sql .= " WHERE article_id = " . $db->tosql($article_id, INTEGER);
				$sql .= " AND category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
				$db->query($sql);
				
				$existing_categories = array();
				while ($db->next_record()) {
					$existing_categories[] = $db->f("category_id");					
				}
				
				$categories = array_values(array_diff($categories, $existing_categories));
				if ($categories) {
					for ($i = 0; $i < sizeof($categories); $i++) {
						$sql  = " SELECT MAX(article_order) FROM " . $table_prefix . "articles_assigned ";
						$sql .= " WHERE category_id=" . $db->tosql($categories[$i], INTEGER);						
						$order = get_db_value($sql) + 1;
						
						$sql  = " INSERT INTO " . $table_prefix . "articles_assigned ";
						$sql .= " (article_id, category_id, article_order) VALUES (";
						$sql .= $db->tosql($article_id, INTEGER) . ",";
						$sql .= $db->tosql($categories[$i], INTEGER) . ",";
						$sql .= $db->tosql($order, INTEGER) . ")";
						$db->query($sql);
					}
				}
			}
			header("Location: " . $return_page);
			exit;
		}
	}

	$sql  = " SELECT category_id, parent_category_id, category_name ";
	$sql .= " FROM " . $table_prefix . "articles_categories ";
	$sql .= " WHERE (category_id=" . $db->tosql($top_id, INTEGER);
	$sql .= " OR category_path LIKE '0," . $db->tosql($top_id, INTEGER) . ",%') ";
	$sql .= " ORDER BY category_path";
	$db->query($sql);
	while ($db->next_record())
	{
		$t->set_var("category_id", $db->f("category_id"));
		$t->set_var("parent_category_id", $db->f("parent_category_id"));
		$t->set_var("category_name", str_replace("\"", "\\\"", get_translation($db->f("category_name"))));
		$t->parse("categories");
	}

	$sql = " SELECT category_id FROM " . $table_prefix . "articles_assigned WHERE article_id = " . $article_id;
	$db->query($sql);
	while ($db->next_record())
	{
		$t->set_var("category_id", $db->f("category_id"));
		$t->parse("selected_categories");
	}

	if (strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	} else {
		$t->set_var("errors", "");
	}

	$t->set_var("article_id", $article_id);
	$t->set_var("category_id", $category_id);
	$t->set_var("top_id", $top_id);
	$t->set_var("selected_name", "selected[]");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>