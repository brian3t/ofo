<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_article_forums_related.php                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
	
	@set_time_limit (900);
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	require_once($root_folder_path . "includes/ajax_list_tree.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");
	
	check_admin_security("articles");
	check_admin_security("forum");
	
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
	$list->set_branches('forum_list', 'forum_id', 'forum_name');	
	$list->set_topbranches('forum_categories', 'category_id', 'category_name');	
	$list->set_leaves('forum', 'thread_id', 'topic');
	$list->set_actions('selected_related_ids', 'ul', 'leaftostock');
	$list->ajax_listen('products_ajax_tree', 'admin_article_forums_related.php?article_id='.$article_id);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_article_forums_related.html");
	
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_article_items_related_href", "admin_article_items_related.php");
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
			$db->query("DELETE FROM " . $table_prefix . "articles_forum_topics WHERE article_id=" . $article_id);
			for ($i = 0; $i < sizeof($related_ids); $i++) {
				if (strlen($related_ids[$i])) {
					$related_order = $i + 1;
					$sql  = " INSERT INTO " . $table_prefix . "articles_forum_topics (article_id, thread_id, thread_order) VALUES (";
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
		
	
	$sql  = " SELECT f.thread_id, f.topic ";
	$sql .= " FROM " . $table_prefix . "forum f ";
	$sql .= " LEFT JOIN " . $table_prefix . "articles_forum_topics fr ON fr.thread_id=f.thread_id ";
	$sql .= " WHERE fr.article_id=" . $db->tosql($article_id, INTEGER);
	$sql .= " ORDER BY fr.thread_order, f.topic ";
	$db->query($sql);
	while ($db->next_record()) {
		$related_name  = get_translation($db->f("topic"));
		$t->set_var("related_id", $db->f("thread_id"));
		$t->set_var("related_name", str_replace("\"", "&quot;", $related_name));
		$t->parse("related_items", true);
	}
		
	if ($tab=="general") {
		$list->parse_root_tree('products_ajax_tree', 'admin_article_forums_related.php?article_id='.$article_id, 0);
	} elseif ($tab == "full") {
		$list->parse_plain('products_ajax_tree');
	}
	
	
	// set tabs
	$tab_url = new VA_URL("admin_article_forums_related.php", false);
	$tab_url->add_parameter("article_id", REQUEST, "article_id");
	$tab_url->add_parameter("category_id", CONSTANT, $category_id);
	
	$item_tab_url = new VA_URL("admin_article_items_related.php", false);
	$item_tab_url->add_parameter("article_id", REQUEST, "article_id");
	$item_tab_url->add_parameter("category_id", CONSTANT, $category_id);
	
	$article_tab_url = new VA_URL("admin_article_related.php", false);
	$article_tab_url->add_parameter("article_id", REQUEST, "article_id");
	$article_tab_url->add_parameter("category_id", CONSTANT, $category_id);
	
	$tabs = array(
		"item_general" => array("title" => ARTICLE_RELATED_PRODUCTS_TITLE, "url" => $item_tab_url->get_url()),
		"article_general" => array("title" => RELATED_ARTICLES_MSG, "url" => $article_tab_url->get_url()),
		"general" => array("title" => RELATED_FORUMS_MSG)
	);
	
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