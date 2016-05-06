<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_order.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("products_order");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_products_order.html");
	$t->set_var("admin_products_order_href", "admin_products_order.php");

	$parent_category_id = get_param("parent_category_id");
	if(!$parent_category_id) $parent_category_id = 0;

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($parent_category_id);

	$available_products = array();
	$shown_products = array();

	$operation = get_param("operation");
	$return_page = "admin_items_list.php?category_id=" . $parent_category_id;

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		$available_list = get_param("available_list");
		$shown_list     = get_param("shown_list");
		$available_array = array(); $shown_array = array();
		if($available_list) {
			$available_array = split(",", $available_list);
		}
		if($shown_list) {
			$shown_array = split(",", $shown_list);
		}

		if($operation == "save")
		{			
			for($i = 0; $i < sizeof($shown_array); $i++) {
				$sql  = " UPDATE " . $table_prefix . "items_categories ";
				$sql .= " SET item_order=" . intval($i + 1);
				$sql .= " WHERE item_id=" . $db->tosql($shown_array[$i], INTEGER);
				$sql .= " AND category_id = " . $db->tosql($parent_category_id, INTEGER);
				$db->query($sql);
			}
			
			for($j = 0; $j < sizeof($available_array); $j++) {
				$sql  = " UPDATE " . $table_prefix . "items_categories ";
				$sql .= " SET item_order=" . intval($i + $j + 1);
				$sql .= " WHERE item_id=" .  $db->tosql($available_array[$j], INTEGER);
				$sql .= " AND category_id = " . $db->tosql($parent_category_id, INTEGER);
				$db->query($sql);
			}
			
			if ($shown_array) {
				$sql  = " UPDATE " . $table_prefix . "items SET is_showing=1 ";
				$sql .= " WHERE item_id IN (" . $db->tosql($shown_array, INTEGERS_LIST). ")";
				$db->query($sql);
			}			
			if ($available_array) {
				$sql  = " UPDATE " . $table_prefix . "items SET is_showing=0 ";
				$sql .= " WHERE item_id IN (" . $db->tosql($available_array, INTEGERS_LIST). ")";
				$db->query($sql);
			}
			
			header("Location: " . $return_page);
			exit;
		}
	}


	$sql  = " SELECT i.item_id, i.item_name, i.is_showing ";
	$sql .= " FROM (" . $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id ) ";
	$sql .= " WHERE ic.category_id = " . $db->tosql($parent_category_id, INTEGER);
	$sql .= " ORDER BY ic.item_order, i.item_order, i.item_id DESC ";
	$db->query($sql);
	while($db->next_record())
	{
		$item_id    = $db->f("item_id");
		$item_order = $db->f("item_order");
		$item_name  = get_translation($db->f("item_name"));
		$is_showing = $db->f("is_showing");
		if($is_showing) {
			$shown_products[] = array($item_id, $item_name);
		} else {
			$available_products[] = array($item_id, $item_name);
		}
	}

	set_options($available_products, "", "available_products");
	set_options($shown_products, "", "shown_products");

	$t->set_var("errors", "");
	$t->set_var("parent_category_id", $parent_category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>