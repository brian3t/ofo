<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_article_items_related.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	@set_time_limit (900);
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	require_once($root_folder_path . "includes/ajax_list_tree.php");
	include_once("./admin_common.php");
	
	check_admin_security("articles");
	
	$permissions = get_permissions();
	$related_forums = get_setting_value($permissions, "forum", 0);

	$article_id = get_param("article_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	
	$sql  = " SELECT article_title FROM " . $table_prefix . "articles ";
	$sql .= " WHERE article_id=" . $db->tosql($article_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$article_title = get_translation($db->f("article_title"));
	} else {
		die(OBJECT_NO_EXISTS_MSG);
	}
	
	// init ajax tree list and set it as ajax requests listener
	$list = new VA_Ajax_List_Tree($settings["admin_templates_dir"], "ajax_list_tree.html");
	$list->set_branches('categories', 'category_id', 'category_name', 'parent_category_id');
	$list->set_leaves('items', 'item_id', 'item_name', 'items_categories');
	$list->set_actions('selected_related_ids', 'ul', 'leaftostock');
	$list->ajax_listen('products_ajax_tree', 'admin_article_items_related.php?article_id='.$article_id);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_article_items_related.html");
	
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_article_items_related_href", "admin_article_items_related.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_product_href", "admin_product.php");
	$t->set_var("related_items", "");
	$t->set_var("available_items", "");
	$t->set_var("article_title", $article_title);

	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "articles_categories", "tree", "");
	$tree->show($category_id);

	$operation = get_param("operation");
	$return_page = "admin_articles.php?category_id=" . $category_id;
	$errors = "";
	
	if ($operation == "cancel") {
		header("Location: " . $return_page);
		exit;
	} elseif ($operation == "save" || $operation == "apply") {
		$related_ids = get_param("related_ids");
		
		if (!strlen($errors))
		{
			$related_ids = split(",", $related_ids);
			$db->query("DELETE FROM " . $table_prefix . "articles_items_related WHERE article_id=" . $article_id);
			for ($i = 0; $i < sizeof($related_ids); $i++) {
				if (strlen($related_ids[$i])) {
					$related_order = $i + 1;
					$sql  = " INSERT INTO " . $table_prefix . "articles_items_related (article_id, item_id, item_order) VALUES (";
					$sql .= $article_id . "," . $db->tosql($related_ids[$i], INTEGER) . "," . $related_order . ")";
					$db->query($sql);
				}
			}
			if ($operation == "save") {
				header("Location: " . $return_page);
				exit;
			}
		}
	}
		
	
	$sql  = " SELECT i.item_id, i.item_name ";
	$sql .= " FROM " . $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "articles_items_related ir ON ir.item_id=i.item_id ";
	$sql .= " WHERE ir.article_id=" . $db->tosql($article_id, INTEGER);
	$sql .= " ORDER BY ir.item_order, i.item_name ";
	$db->query($sql);
	while ($db->next_record())
	{
		$row_item_id   = $db->f("item_id");
		$related_id    = $db->f("related_id");
		$related_name  = get_translation($db->f("item_name"));
		$t->set_var("related_id", $row_item_id);
		$t->set_var("related_name", str_replace("\"", "&quot;", $related_name));
		$t->parse("related_items", true);
	}
		
	if ($tab=="general") {
		$list->parse_root_tree('products_ajax_tree', 'admin_article_items_related.php?article_id='.$article_id, 0);
	} elseif ($tab == "full") {
		$list->parse_plain('products_ajax_tree');
	}
	
	
	// set tabs
	$tab_url = new VA_URL("admin_article_items_related.php", false);
	$tab_url->add_parameter("article_id", REQUEST, "article_id");
	$tab_url->add_parameter("category_id", CONSTANT, $category_id);
		
	$article_tab_url = new VA_URL("admin_article_related.php", false);
	$article_tab_url->add_parameter("article_id", REQUEST, "article_id");
	$article_tab_url->add_parameter("category_id", CONSTANT, $category_id);
		
	
	$tabs = array(
		"general" => array("title" => ARTICLE_RELATED_PRODUCTS_TITLE),
		"article_general" => array("title" => RELATED_ARTICLES_MSG, "url" => $article_tab_url->get_url())
	);
	
	if ($related_forums) {
		$forum_tab_url = new VA_URL("admin_article_forums_related.php", false);
		$forum_tab_url->add_parameter("article_id", REQUEST, "article_id");
		$forum_tab_url->add_parameter("category_id", CONSTANT, $category_id);
		$tabs["forum_general"] = array(
			"title" => RELATED_FORUMS_MSG,
			"url" => $forum_tab_url->get_url()
		);		
	}
	foreach ($tabs as $tab_name => $tab_vars) {
		$tab_title = $tab_vars["title"];
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);		 
		$t->set_var("tab_title", $tab_title);
		if (isset($tab_vars["url"])) {
			$t->set_var("tab_url", $tab_vars["url"]);
		} else {
			$tab_url->add_parameter("tab", CONSTANT, $tab_name);
			$t->set_var("tab_url", $tab_url->get_url());
		} 
		
		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		$t->parse("tabs", $tab_title);
	}
	
	if (strlen($errors))	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}	else {
		$t->set_var("errors", "");
	}

	$t->set_var("article_id", $article_id);
	$t->set_var("category_id", $category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>