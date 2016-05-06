<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_products.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

		
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");
	
	check_admin_security("admin_registration");
		
	$permissions = get_permissions();
	$edit_reg_list_priv = get_setting_value($permissions, "edit_reg_list", 0);
	$edit_reg_categories_priv = get_setting_value($permissions, "edit_reg_categories", 0);
	$edit_reg_products_priv = get_setting_value($permissions, "edit_reg_products", 0);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_registration_products.html");	
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registration_products_href", "admin_registration_products.php");
	$t->set_var("admin_registration_settings_href", "admin_registration_settings.php");
	$t->set_var("admin_category_edit_href", "admin_registration_category.php");
	$t->set_var("admin_categories_order_href", "admin_registration_categories_order.php");	
	$t->set_var("admin_product_href", "admin_registration_product.php");
	$t->set_var("admin_products_order_href", "admin_registration_products_order.php");
	$t->set_var("admin_product_categories_href", "admin_registration_products_categories.php");	
	$t->set_var("admin_registrations_href", "admin_registrations.php");	
	$t->set_var("admin_registration_edit_href", "admin_registration_edit.php");	
		
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	$category_id = get_param("category_id");
	if (!strlen($category_id))  { $category_id = "0"; }
	
	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "registration_categories", "tree");
	$tree->show($category_id);
	
	$t->set_var("parent_category_id", $category_id);
	$sql  = " SELECT category_id,category_name ";
	$sql .= " FROM " . $table_prefix . "registration_categories ";
	$sql .= " WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$db->query($sql);

	
	if ($db->next_record())
	{
		$t->set_var("no_categories", "");
		do {
			$row_category_id = $db->f("category_id");
			$row_category_name = $db->f("category_name");
			$row_category_name = get_translation($row_category_name, $language_code);
			$t->set_var("category_id", $row_category_id);
			$t->set_var("category_name", htmlspecialchars($row_category_name));
			if ($edit_reg_categories_priv) {
				$t->parse("categories_edit_list_priv", false);
			}
			$t->parse("categories");
		} while ($db->next_record());
		if ($edit_reg_categories_priv) {
			$t->parse("categories_edit_header_priv", false);
		}
		$t->parse("categories_header", false);
		$t->parse("categories_order_priv", false);	
	}
	else
	{
		$t->set_var("categories", "");
		$t->parse("no_categories");
		$t->set_var("categories_order_priv", "");
	}
	if ($edit_reg_categories_priv) {
		$t->parse("categories_edit_block", false);
	}

	$where = " ic.category_id = " . $db->tosql($category_id, INTEGER);
	
	$sorter = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_registration_products.php");
	$sorter->set_parameters(false, true, true, false);
	$sorter->set_default_sorting(10, "asc");
	$sorter->set_sorter(REGISTRATION_PROD_TITLE_COLUMN, "sorter_item_name", 1, "i.item_name");
	$sorter->set_sorter(REGISTRATION_PROD_CODE_COLUMN, "sorter_item_code", 2, "i.item_code");
	$sorter->set_sorter(REGISTRATION_TOTAL_COLUMN, "sorter_registered", 3, "i.registered");
	$sorter->set_sorter(ADMIN_ORDER_MSG, "sorter_order", 10, "i.item_order");
	
	if (strtolower($db_type) == "mysql") {
		$sql  = " SELECT COUNT(DISTINCT i.item_id) ";
	} else {
		$sql  = " SELECT COUNT(*) ";
	}
	$sql .= " FROM ((" . $table_prefix . "registration_items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_items_assigned ic ON i.item_id=ic.item_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_categories c ON c.category_id = ic.category_id)";
	if ($where) {
		$sql .= " WHERE " . $where;
	}
	$total_records = 0;
	if (strtolower($db_type) == "mysql") {
		$db->query($sql);
		$db->next_record();
		$total_records = $db->f(0);
	} else {
		$sql .= " GROUP BY i.item_id";
		$db->query($sql);
		while ($db->next_record()) {
			$total_records++;
		}
	}
	
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_registration_products.php");
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);

	
	$sql  = " SELECT i.item_id ";
	$sql .= " FROM ((" . $table_prefix . "registration_items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_items_assigned ic ON i.item_id=ic.item_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_categories c ON c.category_id = ic.category_id)";
	if ($where) {
		$sql .= " WHERE " . $where;
	}
	$sql .= " GROUP BY i.item_id, i.item_name, i.item_order ";
	
	$items_ids = "";
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record()) {
		$items_ids .= $db->f("item_id");
		while ($db->next_record()) {
			$items_ids .= "," . $db->f("item_id");
		}
	} else {
		$total_records = 0;
	}                    

	$item_index = 0;
	if ($total_records > 0) {
		$sql  = " SELECT i.item_id, i.item_code, i.item_name, COUNT(rl.registration_id) AS registered";
		$sql .= " FROM (" . $table_prefix . "registration_items i ";
		$sql .= " LEFT JOIN " . $table_prefix . "registration_list rl ON rl.item_id = i.item_id)";
		$sql .= " WHERE i.item_id IN (" . $items_ids . ") ";
		$sql .= " GROUP BY i.item_id, i.item_code, i.item_name, i.item_order ";
		$sql .= $sorter->order_by;
		$db->query($sql);
		if ($db->next_record())
		{
			$t->set_var("category_id", $category_id);
			$t->set_var("no_items", "");
			do {
				$item_index++;
				$item_id = $db->f("item_id");
				$product_category_id = $db->f("category_id");
				$item_code = $db->f("item_code");
				$item_name = get_translation($db->f("item_name"));
				$registered = $db->f("registered");
				
				$t->set_var("item_id", $item_id);
				$t->set_var("item_index", $item_index);
				$t->set_var("product_category_id", $product_category_id);
				$t->set_var("item_code", htmlspecialchars($item_code));
				$t->set_var("registered", $registered);

				$item_name = htmlspecialchars($item_name);
				$t->set_var("item_name", $item_name);
				
				
				if ($edit_reg_list_priv) {
					$t->parse("update_list_priv", false);
				} else {
					$t->set_var("update_list_priv", "");
				}
				if ($registered) {
					$t->parse("view_list_priv", false);
				} else {
					$t->set_var("view_list_priv", "0");
				}
				
				if ($edit_reg_products_priv) {
					$t->parse("update_products_priv", false);
					$t->set_var("read_only_products_priv", "");
				} else {
					$t->parse("read_only_products_priv", false);
					$t->set_var("update_products_priv", "");
				}
				
								
				$row_style = ($item_index % 2 == 0) ? "row1" : "row2";
				$t->set_var("row_style", $row_style);
				$t->parse("items_list");
			} while ($db->next_record());
			$t->parse("items_header", false);
		}
		$t->parse("products_order_priv");
	} else {
		$t->set_var("products_order_priv", "");
		$t->set_var("items_list", "");
		$t->parse("no_items");
	}
	
	$t->set_var("items_number", $item_index);
	
	if ($edit_reg_products_priv) {
		$t->parse("products_edit_block", false);
	}
	$t->parse("items_block", false);
	
	$t->pparse("main");
?>
	