<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_products_categories.php               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	@set_time_limit(300);
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("edit_reg_products");

	$item_id = get_param("item_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "browse"; }

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_registration_products_categories.html");

	$t->set_var("admin_registration_products_categories_href", "admin_registration_products_categories.php");
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registration_products_href", "admin_registration_products.php");
	$t->set_var("admin_registration_product_href", "admin_registration_product.php");

	$sql  = " SELECT item_name FROM " . $table_prefix . "registration_items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("item_name", get_translation($db->f("item_name")));
	} else {
		die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));
	}

	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "registration_categories", "tree");
	$tree->show($category_id);

	$operation = get_param("operation");
	$return_page = "admin_registration_products.php?category_id=" . $category_id;
	$errors = "";

	if ($operation == "cancel")
	{
		header("Location: " . $return_page);
		exit;
	}
	elseif ($operation == "save")
	{
		$categories_ids = get_param("categories_ids");
		if (!strlen($categories_ids)) {
			$errors .= NO_CATEGORIES_SELECTED_MSG . "<br>";
		}

		if (!strlen($errors))
		{
			$recent_index = 0;
			$recent_max_number = 20;
			$recently_used_categories = array();
			$session_used_categories = get_session("session_recently_used_registration_categories");
			$categories_ids = explode(",", $categories_ids);
			$db->query("DELETE FROM " . $table_prefix . "registration_items_assigned WHERE item_id=" . $item_id);
			for ($i = 0; $i < sizeof($categories_ids); $i++) {
				$db->query("INSERT INTO " . $table_prefix . "registration_items_assigned (item_id, category_id) VALUES (" . $item_id . "," . $db->tosql($categories_ids[$i], INTEGER) . ")");
				if ($recent_index <= $recent_max_number) {
					$recent_index++;
					$recently_used_categories[] = $categories_ids[$i];
				}
			}
			if (is_array($session_used_categories)) {
				for ($i = 0; $i < sizeof($session_used_categories); $i++) {
					if ($recent_index <= $recent_max_number) {
						$recent_index++;
						$recently_used_categories[] = $session_used_categories[$i];
					} else {
						break;
					}
				}
			}
			set_session("session_recently_used_registration_categories", $recently_used_categories);

			header("Location: " . $return_page);
			exit;
		}
	}

	$categories = array();
	$sub_categories = array();
	$session_used_categories = get_session("session_recently_used_registration_categories");
	if ($tab == "all") {
		$t->set_var("categories_class", "selectAllCategories");

		// add top category
		$t->set_var("category_id", 0);
		$t->set_var("category_name_option", PRODUCTS_TITLE . " [" . TOP_CATEGORY_MSG . "]");
		$t->parse("parent_categories", true);

		// get all categories
		$sql  = " SELECT category_id, parent_category_id, category_name, category_path ";
		$sql .= " FROM " . $table_prefix . "registration_categories ";
		$sql .= " ORDER BY category_path, category_order";
		$db->query($sql);
		while ($db->next_record())
		{
			$row_category_id = $db->f("category_id");
			$row_category_name = get_translation($db->f("category_name"));
			$parent_category_id = $db->f("parent_category_id");
			$category_path = $db->f("category_path");
			$categories[$row_category_id] = array($row_category_name, $parent_category_id, $category_path);
			$sub_categories[$parent_category_id][] = $row_category_id;
		}

		// parse all categories
		set_select_categories(0);

	} elseif ($tab == "recent") {
		$t->set_var("categories_class", "selectAllCategories");

		if (is_array($session_used_categories) && sizeof($session_used_categories) > 0) {
			$used_categories_ids = implode(",", $session_used_categories);
			$sql  = " SELECT category_id, parent_category_id, category_name, category_path ";
			$sql .= " FROM " . $table_prefix . "registration_categories ";
			$sql .= " WHERE category_id IN (" . $used_categories_ids . ") ";
			$sql .= " ORDER BY category_path, category_order";
			$db->query($sql);
			while ($db->next_record())
			{
				$row_category_id = $db->f("category_id");
				$row_category_name = get_translation($db->f("category_name"));
				$parent_category_id = $db->f("parent_category_id");
				$category_path = $db->f("category_path");
				$categories[$row_category_id] = array($row_category_name, $parent_category_id, $category_path);
			}

			foreach ($categories as $row_category_id => $category_info)
			{
				list($row_category_name, $parent_category_id, $category_path) = $category_info;

				$category_name_option = "";
				$path_ids = explode(",", $category_path);
				for ($i = 0; $i < sizeof($path_ids); $i++) {
					$path_id = $path_ids[$i];
					if ($path_id) {
						if (!isset($categories[$path_id])) {
							$sql  = " SELECT category_name, parent_category_id, category_path ";
							$sql .= " FROM " . $table_prefix . "registration_categories ";
							$sql .= " WHERE category_id=" . $db->tosql($path_id, INTEGER);
							$db->query($sql);
							if ($db->next_record()) {
								$path_category_name = get_translation($db->f("category_name"));
								$path_parent_id = $db->f("parent_category_id");
								$path_category_path = $db->f("category_path");

								$categories[$path_id] = array($path_category_name, $path_parent_id, $path_category_path);
								$t->set_var("category_id", $path_id);
								$t->set_var("parent_category_id", $path_parent_id);
								$t->set_var("category_name_js", str_replace("\"", "\\\"", $path_category_name));
								$t->set_var("subcategories_number", 0);
								$t->parse("parent_categories_js", true);
							}
						}
						$category_name_option .= $categories[$path_id][0] . " > ";
					}
				}
				$category_name_option .= $row_category_name;

				$t->set_var("category_id", $row_category_id);
				$t->set_var("parent_category_id", $parent_category_id);

				$t->set_var("category_name", $row_category_name);
				$t->set_var("category_name_option", $category_name_option);
				$t->set_var("category_name_js", str_replace("\"", "\\\"", $row_category_name));
				$t->set_var("subcategories_number", 0);

				$t->parse("parent_categories", true);
				$t->parse("parent_categories_js", true);
			}
		}
	} else {
		$t->set_var("categories_class", "selectCategories");

		// add top category
		$t->set_var("category_id", 0);
		$t->set_var("category_name_option", PRODUCTS_TITLE . " [" . TOP_CATEGORY_MSG . "]");
		$t->parse("parent_categories", true);

		// parse top categories
		$sql  = " SELECT c.category_id, c.category_name, COUNT(sc.category_id) as subcategories ";
		$sql .= " FROM (" . $table_prefix . "registration_categories c ";
		$sql .= " LEFT JOIN " . $table_prefix . "registration_categories sc ON c.category_id=sc.parent_category_id) ";
		$sql .= " WHERE c.parent_category_id=0 ";
		if ($db_type == "access" || $db_type == "db2" || $db_type == "postgre") {
			$sql .= " GROUP BY c.category_id, c.category_name, c.category_order ";
		} else {
			$sql .= " GROUP BY c.category_id, c.category_name ";	
		}
		$sql .= " ORDER BY c.category_order, c.category_id ";
		$db->query($sql);
		if ($db->next_record()) {

			do {
				$row_category_id = $db->f("category_id");
				$row_category_name = get_translation($db->f("category_name"));
				$subcategories = $db->f("subcategories");
				$categories[$row_category_id] = array($row_category_name, 0, 0);

				if ($subcategories > 0) {
					$category_name_option = $row_category_name . " > ";
				} else {
					$category_name_option = $row_category_name;
				}
				$t->set_var("category_id", $row_category_id);
				$t->set_var("parent_category_id", 0);
				$t->set_var("category_name", $row_category_name);
				$t->set_var("category_name_option", $category_name_option);
				$t->set_var("category_name_js", str_replace("\"", "\\\"", $row_category_name));
				$t->set_var("subcategories_number", $subcategories);

				$t->parse("parent_categories", true);
				$t->parse("parent_categories_js", true);

			} while ($db->next_record());
		}
	}

	// get selected categories
	$categories_ids = "";
	$selected_categories = array();
	$sql  = " SELECT ic.category_id, c.category_name, c.parent_category_id, c.category_path ";
	$sql .= " FROM (" . $table_prefix . "registration_items_assigned ic ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_categories c ON c.category_id=ic.category_id) ";
	$sql .= " WHERE ic.item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= " ORDER BY c.category_order ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$row_category_id = $db->f("category_id");
			if (strlen($categories_ids)) { $categories_ids .= ","; }
			$categories_ids .= $row_category_id;
			$parent_category_id = $db->f("parent_category_id");
			$category_path = $db->f("category_path");

			if ($row_category_id) {
				$row_category_name = get_translation($db->f("category_name"));
			} else {
				$row_category_name = PRODUCTS_TITLE . " [" . TOP_CATEGORY_MSG . "]";
			}
			if (!$parent_category_id) { $parent_category_id = 0; }
			if (!$category_path) { $category_path = 0; }

			if (!isset($categories[$row_category_id]) && $row_category_id) {
				$categories[$row_category_id] = array($row_category_name, $parent_category_id, $category_path);
				$t->set_var("category_id", $row_category_id);
				$t->set_var("parent_category_id", $parent_category_id);
				$t->set_var("category_name_js", str_replace("\"", "\\\"", $row_category_name));
				$t->set_var("subcategories_number", 0);
				$t->parse("parent_categories_js", true);
			}

			$selected_categories[$row_category_id] = array($row_category_name, $category_path);
		} while ($db->next_record());
	}


	foreach ($selected_categories as $row_category_id => $category_info)
	{
		$selected_category_path = "";
		$path_ids = explode(",", $category_info[1]);
		for ($i = 0; $i < sizeof($path_ids); $i++) {
			$path_id = $path_ids[$i];
			if ($path_id) {
				if (!isset($categories[$path_id])) {
					$sql  = " SELECT category_name, parent_category_id, category_path ";
					$sql .= " FROM " . $table_prefix . "registration_categories ";
					$sql .= " WHERE category_id=" . $db->tosql($path_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$row_category_name = get_translation($db->f("category_name"));
						$parent_category_id = $db->f("parent_category_id");
						$category_path = $db->f("category_path");

						$categories[$path_id] = array($row_category_name, $parent_category_id, $category_path);
						$t->set_var("category_id", $path_id);
						$t->set_var("parent_category_id", $parent_category_id);
						$t->set_var("category_name_js", str_replace("\"", "\\\"", $row_category_name));
						$t->set_var("subcategories_number", 0);
						$t->parse("parent_categories_js", true);
					}
				}
				$selected_category_path .= $categories[$path_id][0] . " > ";
			}
		}
		$selected_category_path .= $category_info[0];

		$t->set_var("category_id", $row_category_id);
		$t->set_var("selected_category_path", $selected_category_path);
		$t->parse("selected_categories", true);
	}


	if (strlen($errors)) {
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	} else {
		$t->set_var("errors", "");
	}

	// parse tabs
	$tab_url = new VA_URL("admin_registration_products_categories.php", false);
	$tab_url->add_parameter("item_id", REQUEST, "item_id");
	$tab_url->add_parameter("category_id", REQUEST, "category_id");

	$tabs = array("browse" => BROWSE_CATEGORIES_MSG, "all" => SEARCH_IN_ALL_MSG);
	if (is_array($session_used_categories) && sizeof($session_used_categories) > 0) {
		$tabs["recent"] = RECENTLY_USED_CATEGORIES_MSG;
	}
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);
		$tab_url->add_parameter("tab", CONSTANT, $tab_name);
		$t->set_var("tab_url", $tab_url->get_url());

		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
		} else {
			$t->set_var("tab_class", "adminTab");
		}
		$t->parse("tabs", $tab_title);
	}

	$t->set_var("tab", $tab);
	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);
	$t->set_var("categories_ids", $categories_ids);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_select_categories($top_id)
	{
		global $t, $categories, $sub_categories;

		$categories_ids = $sub_categories[$top_id];
		for ($ci = 0; $ci < sizeof($categories_ids); $ci++) {
			$row_category_id = $categories_ids[$ci];
			$category_info = $categories[$row_category_id];
			list($row_category_name, $parent_category_id, $category_path) = $category_info;

			$category_name_option = "";
			$path_ids = explode(",", $category_path);
			for ($pi = 0; $pi < sizeof($path_ids); $pi++) {
				$path_id = $path_ids[$pi];
				if ($path_id) {
					$category_name_option .= $categories[$path_id][0] . " > ";
				}
			}
			$category_name_option .= $row_category_name;

			$t->set_var("category_id", $row_category_id);
			$t->set_var("parent_category_id", $parent_category_id);

			$t->set_var("category_name", $row_category_name);
			$t->set_var("category_name_option", $category_name_option);
			$t->set_var("category_name_js", str_replace("\"", "\\\"", $row_category_name));
			$t->set_var("subcategories_number", 0);

			$t->parse("parent_categories", true);
			$t->parse("parent_categories_js", true);
			if (isset($sub_categories[$row_category_id])) {
				set_select_categories($row_category_id);
			}
		}
	}

?>