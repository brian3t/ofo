<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_item_accessories.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit(900);
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("product_accessories");

	$item_id = get_param("item_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_item_accessories.html");

	$t->set_var("admin_item_accessories_href", "admin_item_accessories.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_product_href", "admin_product.php");
	$t->set_var("accessories_items", "");
	$t->set_var("available_items", "");

	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$t->set_var("item_name", get_translation($db->f("item_name")));
	} else {
		die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));
	}

	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$operation = get_param("operation");
	$return_page = "admin_items_list.php?category_id=" . $category_id;
	$errors = "";

	if ($operation == "cancel")
	{
		header("Location: " . $return_page);
		exit;
	}
	elseif ($operation == "save")
	{
		$accessories_ids = get_param("accessories_ids");

		if (!strlen($errors))
		{
			$accessories_ids = explode(",", $accessories_ids);
			$db->query("DELETE FROM " . $table_prefix . "items_accessories WHERE item_id=" . $item_id);
			for ($i = 0; $i < sizeof($accessories_ids); $i++) {
				if (strlen($accessories_ids[$i])) {
					$accessory_order = $i + 1;
					$sql  = " INSERT INTO " . $table_prefix . "items_accessories (item_id, accessory_id, accessory_order) VALUES (";
					$sql .= $item_id . "," . $db->tosql($accessories_ids[$i], INTEGER) . "," . $accessory_order . ")";
					$db->query($sql);
				}
			}
			header("Location: " . $return_page);
			exit;
		}
	}

	$sql  = " SELECT ir.accessory_id, i.item_id, i.item_name ";
	$sql .= " FROM (" . $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "items_accessories ir ON (ir.accessory_id= i.item_id ";
	$sql .= " AND ir.item_id=" . $db->tosql($item_id, INTEGER) . ")) ";
	$sql .= " WHERE i.item_id<>" . $db->tosql($item_id, INTEGER);
	$sql .= " ORDER BY ir.accessory_order, i.item_name ";
	$db->query($sql);
	while ($db->next_record())
	{
		$row_item_id = $db->f("item_id");
		$accessory_id = $db->f("accessory_id");
		$accessory_name = $db->f("item_name");
		$category_name = $db->f("category_name");
		$t->set_var("accessory_id", $row_item_id);
		$t->set_var("accessory_name", str_replace("\"", "&quot;", $accessory_name));
		if ($row_item_id == $accessory_id) {
			$t->parse("accessories_items", true);
		} else {
			$t->parse("available_items", true);
		}
	}

	if(strlen($errors))	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	} else {
		$t->set_var("errors", "");
	}

	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>