<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_articles_related.php                         ***
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

	check_admin_security("forum");
	check_admin_security("articles");
	
	$permissions = get_permissions();
	$products_categories = get_setting_value($permissions, "products_categories", 0);
	$product_related = get_setting_value($permissions, "product_related", 0);
	

	$thread_id  = get_param("thread_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	
	$sql  = " SELECT f.topic, f.forum_id, fl.forum_name, fl.category_id, fc.category_name ";
	$sql .= " FROM " . $table_prefix . "forum f, " . $table_prefix . "forum_list fl, " . $table_prefix . "forum_categories fc ";
	$sql .= " WHERE thread_id = " . $db->tosql($thread_id, INTEGER);
	$sql .= " AND f.forum_id = fl.forum_id AND fl.category_id = fc.category_id ";
	$db->query($sql);
	if($db->next_record()) {
		$topic = get_translation($db->f("topic"));
		$forum_id = $db->f("forum_id");
		$forum_name = get_translation($db->f("forum_name"));
		$category_id = $db->f("category_id");
		$category_name = get_translation($db->f("category_name"));
	}	else {		
		die(str_replace("{thread_id}", $thread_id, FORUM_ID_NO_LONGER_EXISTS_MSG));
	}
		
	// init ajax tree list and set it as ajax requests listener
	$list = new VA_Ajax_List_Tree($settings["admin_templates_dir"], "ajax_list_tree.html");
	$list->set_branches('articles_categories', 'category_id', 'category_name', 'parent_category_id');
	$list->set_leaves('articles', 'article_id', 'article_title', 'articles_assigned');
	$list->set_actions('selected_related_ids', 'ul', 'leaftostock');
	$list->ajax_listen('products_ajax_tree', 'admin_forum_articles_related.php?thread_id=' . $thread_id);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_forum_articles_related.html");
	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_thread_href", "admin_forum_thread.php");	
	$t->set_var("related_items", "");
	$t->set_var("available_items", "");
	$t->set_var("topic", $topic);	
	$t->set_var("forum_id", $forum_id);
	$t->set_var("current_forum", $forum_name);
	$t->set_var("category_id", $category_id);
	$t->set_var("current_category", $category_name);

	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$operation = get_param("operation");
	$return_page = "admin_forum.php?forum_id=" . $forum_id;
	$errors = "";
	
	if ($operation == "cancel") {
		header("Location: " . $return_page);
		exit;
	} elseif ($operation == "save" || $operation == "apply") {
		$related_ids = get_param("related_ids");
		
		if (!strlen($errors))
		{
			$related_ids = split(",", $related_ids);
			$db->query("DELETE FROM " . $table_prefix . "articles_forum_topics WHERE thread_id=" . $thread_id);
			for ($i = 0; $i < sizeof($related_ids); $i++) {
				if (strlen($related_ids[$i])) {
					$related_order = $i + 1;
					$sql  = " INSERT INTO " . $table_prefix . "articles_forum_topics (thread_id, article_id, article_order) VALUES (";
					$sql .= $db->tosql($thread_id, INTEGER) . "," . $db->tosql($related_ids[$i], INTEGER) . "," . $related_order . ")";
					$db->query($sql);
				}
			}
			if ($operation == "save") {
				header("Location: " . $return_page);
				exit;
			}
		}
	}
		
	
	$sql  = " SELECT a.article_id, a.article_title ";
	$sql .= " FROM (" . $table_prefix . "articles a ";
	$sql .= " LEFT JOIN " . $table_prefix . "articles_forum_topics ar ON ar.article_id=a.article_id) ";
	$sql .= " WHERE ar.thread_id=" . $db->tosql($thread_id, INTEGER);
	$sql .= " ORDER BY ar.article_order, a.article_title ";
	$db->query($sql);
	while ($db->next_record())
	{
		$related_name  = get_translation($db->f("article_title"));
		$t->set_var("related_id", $db->f("article_id"));
		$t->set_var("related_name", str_replace("\"", "&quot;", $related_name));
		$t->parse("related_items", true);
	}
		
	if ($tab=="general") {
		$list->parse_root_tree('products_ajax_tree', 'admin_forum_articles_related.php?thread_id=' . $thread_id, 0);
	} elseif ($tab == "full") {
		$list->parse_plain('products_ajax_tree', $thread_id);
	}
	
	
	// set tabs
	$tab_url = new VA_URL("admin_forum_articles_related.php", false);
	$tab_url->add_parameter("thread_id", REQUEST, "thread_id");
	
	$item_tab_url = new VA_URL("admin_forum_items_related.php", false);
	$item_tab_url->add_parameter("thread_id", REQUEST, "thread_id");
	
	$tabs = array();
	
	if ($products_categories && $product_related) {
		$tabs["item_general"] = array("title" => ADMIN_RELATED_PRODUCTS_TITLE, "url" => $item_tab_url->get_url());
	}
	$tabs["general"] = array("title" => RELATED_ARTICLES_MSG);
		
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

	$t->set_var("thread_id", $thread_id);
	$t->set_var("category_id", $category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>