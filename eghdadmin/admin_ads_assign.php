<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_assign.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("ads");

	$item_id = get_param("item_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_ads_assign.html");

	$t->set_var("admin_ads_assign_href", "admin_ads_assign.php");
	$t->set_var("admin_ads_href", "admin_ads.php");
	$t->set_var("admin_ads_edit_href", "admin_ads_edit.php");

	$sql  = " SELECT item_title FROM " . $table_prefix . "ads_items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("item_title", get_translation($db->f("item_title")));
	} else {
		die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));
	}

	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", "Ads");
	$tree->show($category_id);

	$operation = get_param("operation");
	$return_page = "admin_ads.php?category_id=" . $category_id;
	$errors = "";

	if ($operation == "cancel")
	{
		header("Location: " . $return_page);
		exit;
	}
	elseif ($operation == "save")
	{
		$categories = get_param("categories");
		if (!strlen($categories)) {
			$errors .= NO_CATEGORIES_SELECTED_MSG . "<br>";
		}
		
		if (!strlen($errors))
		{
			$categories = split(",", $categories);
			$db->query("DELETE FROM " . $table_prefix . "ads_assigned WHERE item_id=" . $item_id);
			for ($i = 0; $i < sizeof($categories); $i++) {
				$db->query("INSERT INTO " . $table_prefix . "ads_assigned (item_id, category_id) VALUES (" . $item_id . "," . $db->tosql($categories[$i], INTEGER) . ")");
			}
	
			header("Location: " . $return_page);
			exit;
		}
	}

	$sql = " SELECT category_id, parent_category_id, category_name FROM " . $table_prefix . "ads_categories ORDER BY category_path";
	$db->query($sql);
	while ($db->next_record())
	{
		$category_name = get_translation($db->f("category_name"));
		$t->set_var("category_id", $db->f("category_id"));
		$t->set_var("parent_category_id", $db->f("parent_category_id"));
		$t->set_var("category_name", str_replace("\"", "\\\"", $category_name));
		$t->parse("categories");
	}

	$sql = " SELECT category_id FROM " . $table_prefix . "ads_assigned WHERE item_id=" . $item_id;
	$db->query($sql);
	while ($db->next_record())
	{
		$t->set_var("category_id", $db->f("category_id"));
		$t->parse("selected_categories");
	}

	if (strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	} else {
		$t->set_var("errors", "");
	}

	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);
	$t->set_var("selected_name", "selected[]");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>