<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_category_items.php                                 ***
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

	check_admin_security("products_categories");
	
	$category_id = get_param("category_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	$sql  = " SELECT category_name FROM " . $table_prefix . "categories ";
	$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$category_name = get_translation($db->f("category_name"));
	} else {
		die(OBJECT_NO_EXISTS_MSG);
	}
		
	// init ajax tree list and set it as ajax requests listener
	$list = new VA_Ajax_List_Tree($settings["admin_templates_dir"], "ajax_list_tree.html");
	$list->set_branches('categories', 'category_id', 'category_name', 'parent_category_id');
	$list->set_leaves('items', 'item_id', 'item_name', 'items_categories');
	$list->set_actions('selected_related_ids', 'ul', 'leaftostock');
	$list->ajax_listen('products_ajax_tree', 'admin_category_items.php?category_id='.$category_id);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_category_items.html");

	$t->set_var("admin_category_items_href", "admin_category_items.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("related_items", "");
	$t->set_var("available_items", "");
	$t->set_var("category_name", $category_name);

	$parent_category_id = get_param("parent_category_id");
	if (!strlen($parent_category_id)) { $parent_category_id = "0"; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($parent_category_id);

	$operation = get_param("operation");
	$return_page = "admin_items_list.php?category_id=" . $parent_category_id;
	$errors = "";
	
	if ($operation == "cancel") {
		header("Location: " . $return_page);
		exit;
	} elseif ($operation == "save" || $operation == "apply") {
		$related_ids = get_param("related_ids");
		
		if (!strlen($errors))
		{
			$related_ids = split(",", $related_ids);
						
			$db->query("DELETE FROM " . $table_prefix . "items_categories WHERE category_id=" . $category_id);
			$related_order = 0;
			for ($i = 0; $i < sizeof($related_ids); $i++) {
				if (strlen($related_ids[$i])) {
					$related_order++;
					$sql  = " INSERT INTO " . $table_prefix . "items_categories (category_id, item_id, item_order) VALUES (";
					$sql .= $category_id . ",";
					$sql .= $db->tosql($related_ids[$i], INTEGER) . ",";
					$sql .= $db->tosql($related_order, INTEGER) . ")";
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
	$sql .= " LEFT JOIN " . $table_prefix . "items_categories ir ON ir.item_id=i.item_id ";
	$sql .= " WHERE ir.category_id=" . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY ir.item_order ";
	$db->query($sql);
	while ($db->next_record())
	{
		$row_item_id   = $db->f("item_id");
		$related_name  = get_translation($db->f("item_name"));
		$t->set_var("related_id", $row_item_id);
		$t->set_var("related_name", str_replace("\"", "&quot;", $related_name));
		$t->parse("related_items", true);
	}
		
	if ($tab=="general") {
		$list->parse_root_tree('products_ajax_tree', 'admin_category_items.php?category_id='.$category_id, 0);
	} elseif ($tab == "full") {
		$list->parse_plain('products_ajax_tree');
	}

	$t->set_var("parent_category_id", $parent_category_id);
	$t->set_var("category_id", $category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>