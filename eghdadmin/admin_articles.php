<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");

	check_admin_security("articles");

	$permissions = get_permissions();
	$related_forums = get_setting_value($permissions, "forum", 0);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_articles.html");

	// set files names
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_article_href", "admin_article.php");
	$t->set_var("admin_article_items_related_href", "admin_article_items_related.php");
	$t->set_var("admin_article_forums_related_href", "admin_article_forums_related.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_article_category_items_related_href", "admin_article_category_items_related.php");
	$t->set_var("admin_articles_category_href", "admin_articles_category.php");
	$t->set_var("admin_layout_page_href", "admin_layout_page.php");
	$t->set_var("admin_articles_reviews_href", "admin_articles_reviews.php");
	$t->set_var("admin_tell_friend_href", "admin_tell_friend.php");
	$t->set_var("admin_articles_assign_href", "admin_articles_assign.php");
	$t->set_var("admin_articles_categories_href", "admin_articles_categories.php");
	$t->set_var("admin_article_related_href", "admin_article_related.php");
	$t->set_var("admin_articles_order_href",  "admin_articles_order.php");
	$t->set_var("admin_articles_assign_href", "admin_articles_assign.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$top_category_id = 0;
	$category_id = get_param("category_id");
	$shown_fields = "";

	$sql  = " SELECT article_list_fields,article_details_fields,article_required_fields ";
	$sql .= " FROM " . $table_prefix . "articles_categories ";
	$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER, true, false);
	$db->query($sql);
	
	if ($db->next_record()) {
		$shown_fields = ",," . $db->f("article_list_fields") . ",,";
	}

	// get search parameters
	$s = trim(get_param("s"));
	$sc = get_param("sc");
	$search = strlen($s) ? true : false;
	if ($sc) { $category_id = $sc; }
	$sa = "";

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
				$top_category_id = $category_id;
			} else {
				$categories_ids = explode(",", $category_path);
				$top_category_id = $categories_ids[1];
			}
		}
	}

	$articles_order_column = ""; $articles_order_direction = ""; $top_category_name = "";
	$sql  = " SELECT category_name, articles_order_column,articles_order_direction ";
	$sql .= " FROM " . $table_prefix . "articles_categories ";
	$sql .= " WHERE category_id=" . $db->tosql($top_category_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$top_category_name = get_translation($db->f("category_name"));
		$articles_order_column = $db->f("articles_order_column");
		$articles_order_direction = $db->f("articles_order_direction");
	}
	$t->set_var("top_category_name", $top_category_name);
	$t->set_var("top_category_id", $top_category_id);
	$t->set_var("category_id", $category_id);

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "articles_categories", "tree", "");
	$tree->show($category_id);

	$sql  = " SELECT full_description FROM " . $table_prefix . "articles_categories WHERE category_id = " . $db->tosql($category_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("full_description", $db->f("full_description"));
	} else {
		$t->set_var("full_description", "");
	}

	$sql  = " SELECT category_id, category_name ";
	$sql .= " FROM " . $table_prefix . "articles_categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$db->query($sql);
	if ($db->next_record())
	{
		$t->parse("categories_order_link", false);
		$t->set_var("no_categories", "");
		do
		{
			$t->set_var("row_category_id", $db->f("category_id"));
			$t->set_var("category_name", htmlspecialchars(get_translation($db->f("category_name"))));
			$t->parse("articles_categories");
		} while ($db->next_record());
		$t->parse("categories_header", false);
	}
	else
	{
		$t->set_var("articles_categories", "");
		$t->set_var("categories_order_link", "");
		$t->parse("no_categories");
	}

	$sql  = " SELECT a.article_id, a.article_title, a.article_date, aa.category_id, st.status_name, st.allowed_view ";
	$sql .= " FROM (((" . $table_prefix . "articles a ";
	$sql .= " INNER JOIN " . $table_prefix . "articles_assigned aa ON a.article_id=aa.article_id) ";
	$sql .= " INNER JOIN " . $table_prefix . "articles_categories ac ON ac.category_id = aa.category_id)";
	$sql .= " LEFT JOIN " . $table_prefix . "articles_statuses st ON a.status_id=st.status_id) ";
	if ($search) {
		$sql .= " WHERE (aa.category_id = " . $db->tosql($category_id, INTEGER);
		$sql .= " OR ac.category_path LIKE '" . $db->tosql($tree->get_path($category_id), TEXT, false) . "%')";
	} elseif (!$search) {
		$sql .= " WHERE aa.category_id = " . $db->tosql($category_id, INTEGER);
	}
	if ($search) {
		$sa = split(" ", $s);
		for ($si = 0; $si < sizeof($sa); $si++) {
			$sql .= " AND a.article_title LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
		}
	}
	$sql .= " GROUP BY a.article_id, a.article_date, st.status_name, st.allowed_view, a.date_end, a.article_title, a.author_name, aa.category_id, a.article_order, a.date_added, a.date_updated ";
	if ($articles_order_column) {
		if ($articles_order_column != "article_order") {
			$sql .= " ORDER BY a." . $articles_order_column . " " . $articles_order_direction;
		} else {
			$sql .= " ORDER BY aa." . $articles_order_column . " " . $articles_order_direction;
		}
	} else {
		$sql .= " ORDER BY aa.article_order, a.article_order ";
	}

	$db->query($sql);
	$is_date_column = strpos($shown_fields, ",article_date,");
	if ($is_date_column) {
		$t->parse("article_date_header_column", false);
	}
	if ($db->next_record())
	{
		$t->parse("articles_order_link", false);
		$t->set_var("no_items", "");
		do
		{
			$article_id = $db->f("article_id");
			$article_title = get_translation($db->f("article_title"));
			if ($is_date_column) {
				$article_date = $db->f("article_date", DATETIME);
				$article_date = va_date($datetime_show_format, $article_date);
				$t->set_var("article_date", $article_date);
				$t->parse("article_date_column", false);
			}
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


			$t->set_var("article_category_id", $db->f("category_id"));
			$t->set_var("article_id", $db->f("article_id"));
			if ($search) {
				for ($si = 0; $si < sizeof($sa); $si++) {
					$article_title = preg_replace("/(" . $sa[$si] . ")/i", "<font color=\"blue\">\\1</font>", $article_title);
				}
			}
			
			if ($related_forums) {
				$t->parse("related_forums_priv", false);				
			}
			$t->set_var("article_title", $article_title);
			$t->set_var("article_status", $article_status);
			$t->parse("items_list");
		} while ($db->next_record());
		$t->parse("items_header", false);
	}
	else
	{
		$t->set_var("articles_order_link", "");
		$t->set_var("items_list", "");
		$t->parse("no_items");
	}

	// set up search form parameters
	$values_before[] = array($top_category_id, SEARCH_IN_ALL_MSG);
	if ($top_category_id != $category_id) {
		$values_before[] = array($category_id, SEARCH_IN_CURRENT_MSG);
	}

	$sql  = " SELECT category_id, category_name ";
	$sql .= " FROM " . $table_prefix . "articles_categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$sc_values = get_db_values($sql, $values_before);

	set_options($sc_values, $sc, "sc");
	$t->set_var("s", $s);
	if ($search) {
		$t->parse("s_d", false);
	}

	$t->parse("items_block", false);
	$t->pparse("main");

?>