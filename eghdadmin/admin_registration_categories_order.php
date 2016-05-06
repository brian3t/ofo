<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_categories_order.php                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
	
	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("edit_reg_categories");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_registration_categories_order.html");
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registration_products_href", "admin_registration_products.php");
	$t->set_var("admin_registration_categories_order_href", "admin_registration_categories_order.php");


	$parent_category_id = get_param("parent_category_id");
	if (!$parent_category_id) { $parent_category_id = 0; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "registration_categories", "tree");
	$tree->show($parent_category_id);
	$available_categories = array();
	$shown_categories = array();
	
	$operation = get_param("operation");
	$return_page = "admin_registration_products.php?category_id=" . $parent_category_id;

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		$available_list = get_param("available_list");
		$shown_list = get_param("shown_list");
		if ($available_list) {
			$available_array = split(",", $available_list);
			for($i = 0; $i < sizeof($available_array); $i++) {
				$available_categories[] = $available_array[$i];
			}
		}
		if ($shown_list) {
			$left_array = split(",", $shown_list);
			for($i = 0; $i < sizeof($left_array); $i++) {
				$shown_categories[] = $left_array[$i];
			}
		}

		if ($operation == "save")
		{
			for ($i = 0; $i < sizeof($shown_categories); $i++) {
				$sql  = " UPDATE " . $table_prefix . "registration_categories SET category_order=" . intval($i + 1);
				$sql .= " , show_for_user=1 ";
				$sql .= " WHERE category_id=" . $shown_categories[$i];
				$db->query($sql);
			}
			for ($j = 0; $j < sizeof($available_categories); $j++) {
				$sql  = " UPDATE " . $table_prefix . "registration_categories SET category_order=" . intval($i + $j + 1);
				$sql .= " , show_for_user=0 ";
				$sql .= " WHERE category_id=" . $available_categories[$j];
				$db->query($sql);
			}
			header("Location: " . $return_page);
			exit;
		}
	}
	else
	{
		$sql  = " SELECT category_id, category_name, show_for_user ";
		$sql .= " FROM " . $table_prefix . "registration_categories WHERE parent_category_id = " . $db->tosql($parent_category_id, INTEGER);
		$sql .= " ORDER BY category_order, category_id DESC ";
		$db->query($sql);
		while ($db->next_record())
		{
			$category_id = $db->f("category_id");
			$category_order = $db->f("category_order");
			$category_name = get_translation($db->f("category_name"), $language_code);
			$is_showing = $db->f("show_for_user");
			if ($is_showing) {
				$shown_categories[] = array($category_id, $category_name);
			} else {
				$available_categories[] = array($category_id, $category_name);
			}
		}
	}

	set_options($available_categories, "", "available_categories");
	set_options($shown_categories, "", "shown_categories");

	$t->set_var("errors", "");
	$t->set_var("parent_category_id", $parent_category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
?>