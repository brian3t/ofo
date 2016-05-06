<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_categories.php                            ***
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
	$t->set_file("main", "admin_articles_categories.html");

	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_articles_categories_href", "admin_articles_categories.php");

	$parent_category_id = get_param("parent_category_id");
	if (!$parent_category_id) { $parent_category_id = 0; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "articles_categories", "tree", "");
	$tree->show($parent_category_id);

	$categories = array();

	$operation = get_param("operation");
	$return_page = "admin_articles.php?category_id=" . $parent_category_id;

	if (strlen($operation))
	{
		if ($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		}
		if ($operation == "save") {
			$categories_list = get_param("categories_list");
			if ($categories_list) {
				$categories_ids = split(",", $categories_list);
				for ($i = 0; $i < sizeof($categories_ids); $i++) {
					$sql  = " UPDATE " . $table_prefix . "articles_categories SET category_order = " . intval($i + 1);
					$sql .= " WHERE category_id = " . $categories_ids[$i];
					$db->query($sql);
				}
			}
			header("Location: " . $return_page);
			exit;
		}
	} else {
		$sql  = " SELECT ac.category_id, ac.category_name ";
		$sql .= " FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " WHERE ac.parent_category_id = " . $db->tosql($parent_category_id, INTEGER);
		$sql .= " ORDER BY ac.category_order, ac.category_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			$category_order = $db->f("category_order");
			$category_name = get_translation($db->f("category_name"));
			$categories[] = array($category_id, $category_name);
		}
	}

	set_options($categories, "", "categories");

	$t->set_var("errors", "");
	$t->set_var("parent_category_id", $parent_category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>