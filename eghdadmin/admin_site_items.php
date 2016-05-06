<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_site_items.php                                     ***
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
	
	$permissions   = get_permissions();
	$update_sites  = get_setting_value($permissions, "update_sites", 0);	
	
	$param_site_id = get_param("param_site_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	$sites_all = false;
	$sql  = " SELECT site_name FROM " . $table_prefix . "sites ";
	$sql .= " WHERE site_id=" . $db->tosql($param_site_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$site_name = get_translation($db->f("site_name"));
	} else {
		$site_name = SITES_ALL_MSG;
		$sites_all = true;
		$update_sites = false;
	}
		
	// init ajax tree list and set it as ajax requests listener
	$list = new VA_Ajax_List_Tree($settings["admin_templates_dir"], "ajax_list_tree.html");
	$list->set_branches('categories', 'category_id', 'category_name', 'parent_category_id');
	$list->set_leaves('items', 'item_id', 'item_name', 'items_categories');
	$list->set_actions('selected_related_ids', 'ul', 'leaftostock');
	$list->ajax_listen('products_ajax_tree', 'admin_site_items.php?param_site_id='.$param_site_id);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_site_items.html");

	$t->set_var("admin_site_items_href", "admin_site_items.php");
	$t->set_var("admin_sites_href", "admin_sites.php");
	$t->set_var("admin_site_href", "admin_site.php");
	$t->set_var("related_items", "");
	$t->set_var("available_items", "");
	$t->set_var("param_site_id", $param_site_id);
	$t->set_var("site_name", $site_name);
	if ($update_sites) {
		$t->parse("update_sites_perm");
	} else {
		$t->parse("no_update_sites_perm");
	}
	$t->set_var("site_name", $site_name);

	$operation = get_param("operation");	
	$return_page = "admin_sites.php";
	$errors = "";
	
	if ($operation == "cancel") {
		header("Location: " . $return_page);
		exit;
	} elseif ($operation == "save" || $operation == "apply") {
		$related_ids = get_param("related_ids");
		
		if (!strlen($errors))
		{
			if ($sites_all) {
				$sql  = " UPDATE " . $table_prefix . "items ";
				$sql .= " SET sites_all=";
				$db->query($sql . $db->tosql(0, INTEGER, true, false));
				if ($related_ids) {
					$sql .= $db->tosql(1, INTEGER, true, false);
					$sql .= " WHERE item_id IN (" . $db->tosql($related_ids, INTEGERS_LIST) . ")";
					$db->query($sql);
				}
			} else {
				$related_ids = split(",", $related_ids);
				$db->query("DELETE FROM " . $table_prefix . "items_sites WHERE site_id=" . $param_site_id);
				for ($i = 0; $i < sizeof($related_ids); $i++) {
					if (strlen($related_ids[$i])) {
						$related_order = $i + 1;
						$sql  = " INSERT INTO " . $table_prefix . "items_sites (site_id, item_id) VALUES (";
						$sql .= $param_site_id . "," . $db->tosql($related_ids[$i], INTEGER) . ")";
						$db->query($sql);			
					}
				}
			}
			if ($operation == "save") {
				header("Location: " . $return_page);
				exit;
			}
		}
	}
	
	if ($sites_all) {
		$sql  = " SELECT i.item_id, i.item_name ";
		$sql .= " FROM " . $table_prefix . "items i ";
		$sql .= " WHERE i.sites_all = 1";
		$db->query($sql);
		while ($db->next_record()) {
			$row_item_id   = $db->f("item_id");
			$related_name  = get_translation($db->f("item_name"));
			
			$t->set_var("related_id", $row_item_id);
			$t->set_var("related_name", str_replace("\"", "&quot;", $related_name));
			$t->parse("related_item_button", false);
			$t->parse("related_items", true);
		}
		
	} else {	
		$sql  = " SELECT i.item_id, i.item_name, i.sites_all ";
		$sql .= " FROM (" . $table_prefix . "items i ";
		$sql .= " LEFT JOIN " . $table_prefix . "items_sites ir ON (ir.item_id=i.item_id AND i.sites_all = 0)) ";
		$sql .= " WHERE (ir.site_id=" . $db->tosql($param_site_id, INTEGER) . " OR i.sites_all = 1)";
		$db->query($sql);
		while ($db->next_record()) {
			$row_item_id   = $db->f("item_id");
			$related_name  = get_translation($db->f("item_name"));
			
			$t->set_var("related_id", $row_item_id);
			$t->set_var("related_name", str_replace("\"", "&quot;", $related_name));
			
			$t->set_var("related_item_button", "");
			$t->set_var("related_item_star", "");
			if ($db->f("sites_all")) {
				$t->parse("related_item_star");	
			} else {
				$t->parse("related_item_button");
			}
			$t->parse("related_items", true);
		}
		$t->parse("all_sites_note");
	}
		
	if ($tab=="general") {
		$list->parse_root_tree('products_ajax_tree', 'admin_site_items.php?param_site_id='.$param_site_id, 0);
	} elseif ($tab == "full") {
		$list->parse_plain('products_ajax_tree');
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>