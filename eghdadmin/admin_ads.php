<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
                           

	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/shopping_cart.php");

	check_admin_security("ads");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_ads.html");

	// set files names
	$t->set_var("admin_ads_href",          "admin_ads.php");
	$t->set_var("admin_layout_page_href",  "admin_layout_page.php");
	$t->set_var("admin_ads_category_href", "admin_ads_category.php");
	$t->set_var("admin_ads_edit_href",     "admin_ads_edit.php");
	$t->set_var("admin_ads_properties_href",   "admin_ads_properties.php");
	$t->set_var("admin_ads_assign_href",       "admin_ads_assign.php");
	$t->set_var("admin_ads_categories_href",   "admin_ads_categories.php");
	$t->set_var("admin_ads_order_href",        "admin_ads_order.php");
	$t->set_var("admin_ads_types_href",        "admin_ads_types.php");
	$t->set_var("admin_ads_features_groups_href", "admin_ads_features_groups.php");
	$t->set_var("admin_ads_features_href",        "admin_ads_features.php");
	$t->set_var("admin_ads_images_href",          "admin_ads_images.php");
	$t->set_var("admin_ads_images_settings_href", "admin_ads_images_settings.php");
	$t->set_var("admin_ads_notify_href",          "admin_ads_notify.php");
	$t->set_var("admin_ads_search_href",          "admin_ads_search.php");
	$t->set_var("admin_ads_request_href",         "admin_ads_request.php");
	$t->set_var("admin_tell_friend_href",         "admin_tell_friend.php");
	$t->set_var("admin_import_href", "admin_import.php");
	$t->set_var("admin_export_href", "admin_export.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }
	// get search parameters
	$s = trim(get_param("s"));
	$sc = get_param("sc");
	$aa = get_param("aa");
	$search = get_param("search");

	$search = (strlen($search)) ? true : false;
	if ($sc) { $category_id = $sc; }
	$sa = "";

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", "Ads");
	$tree->show($category_id);

	$t->set_var("parent_category_id", $category_id);
	$sql  = " SELECT category_id,category_name ";
	$sql .= " FROM " . $table_prefix . "ads_categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$db->query($sql);
	if ($db->next_record())
	{
		$t->parse("categories_order_link", false);
		$t->set_var("no_categories", "");
		$t->set_var("no_top_categories", "");
		do
		{
			$t->set_var("category_id", $db->f("category_id"));
			$t->set_var("category_name", htmlspecialchars(get_translation($db->f("category_name"))));
			$t->parse("categories");
		} while ($db->next_record());
		$t->parse("categories_header", false);
	}
	else
	{
		$t->set_var("categories", "");
		$t->set_var("categories_order_link", "");
		if ($category_id > 0) {
			$t->parse("no_categories");
		} else {
			$t->parse("no_parent_categories");
		}
	}

	$sql  = " SELECT a.item_id, a.item_title, aa.category_id, a.is_approved, a.date_start, a.date_end, ";
	$sql .= " a.price, a.quantity ";
	$sql .= " FROM ((" . $table_prefix . "ads_items a ";
	$sql .= " LEFT JOIN " . $table_prefix . "ads_assigned aa ON a.item_id=aa.item_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "ads_categories c ON c.category_id = aa.category_id)";
	$sql .= " WHERE 1=1 ";
	if ($search && $category_id != 0) {
		$sql .= " AND c.category_id = aa.category_id ";
		$sql .= " AND (aa.category_id = " . $db->tosql($category_id, INTEGER);
		$sql .= " OR c.category_path LIKE '" . $db->tosql($tree->get_path($category_id), TEXT, false) . "%')";
	} elseif (!$search) {
		$sql .= " AND aa.category_id = " . $db->tosql($category_id, INTEGER);
	}
	if ($s) {
		$sa = split(" ", $s);
		for ($si = 0; $si < sizeof($sa); $si++) {
			$sql .= " AND a.item_title LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
		}
	}
	if (strlen($aa)) {
		if ($aa == 1) {
			$sql .= " AND a.is_approved=1 ";
		} else {
			$sql .= " AND a.is_approved<>1 ";
		}
	}
	$sql .= " GROUP BY a.item_id, a.item_title, aa.category_id, a.item_order, a.price, a.quantity, a.is_approved, a.date_start, a.date_end, a.date_added, a.date_updated  ";
	$sql .= " ORDER BY a.date_updated DESC ";

	$db->query($sql);
	if ($db->next_record())
	{
		//$t->parse("ads_order_link", false);
		$t->set_var("ads_order_link", "");
		$t->set_var("category_id", $category_id);
		$t->set_var("no_items", "");
		do
		{
			$item_title = get_translation($db->f("item_title"));
			$price = $db->f("price");
			$quantity = $db->f("quantity");

			$is_approved = $db->f("is_approved");
			$date_start = $db->f("date_start", DATETIME);
			$date_end = $db->f("date_end", DATETIME);
			$date_start_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY], $date_start[YEAR]);
			$date_end_ts = mktime(0,0,0, $date_end[MONTH], $date_end[DAY], $date_end[YEAR]);
			$date_now_ts = va_timestamp();
			if ($is_approved != 1) {
				$status = "<font color=red>".NOT_APPROVED_MSG."</font>";
			} elseif ($date_now_ts >= $date_start_ts && $date_now_ts < $date_end_ts) {
				$status = "<font color=blue>".AD_RUNNING_MSG."</font>";
			} elseif ($date_start_ts == $date_end_ts) {
				$status = "<font color=silver>".AD_CLOSED_MSG."</font>";
			} elseif ($date_now_ts >= $date_end_ts) {
				$status = "<font color=silver>".EXPIRED_MSG."</font>";
			}	elseif ($date_now_ts < $date_start_ts) {
				$status = AD_NOT_STARTED_MSG;
			}

			$t->set_var("item_id", $db->f("item_id"));
			$t->set_var("ad_category_id", $db->f("category_id"));
			if (is_array($sa)) {
				for ($si = 0; $si < sizeof($sa); $si++) {
					$item_title = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_title);					
				}
			}
			$t->set_var("item_title", $item_title);
			$t->set_var("price", currency_format($price));
			if ($quantity < 0) {
				$quantity = "<font color=red>" . $quantity . "</font>";
			}
			$t->set_var("quantity", $quantity);
			$t->set_var("status", $status);

			$t->parse("items_list");
		} while ($db->next_record());
		$t->parse("items_header", false);
	}
	else
	{
		$t->set_var("ads_order_link", "");
		$t->set_var("items_list", "");
		$t->parse("no_items");
	}

	// set up search form parameters
	$approve_params = 
		array( 
			array("", ALL_ADS_MSG), array(0, NOT_APPROVED_MSG), array(1, IS_APPROVED_MSG)
		);
	set_options($approve_params, $aa, "aa");

	$values_before[] = array("", SEARCH_IN_ALL_MSG);
	if ($category_id != 0) {
		$values_before[] = array($category_id, SEARCH_IN_CURRENT_MSG);
	}

	$sql  = " SELECT category_id,category_name ";
	$sql .= " FROM " . $table_prefix . "ads_categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$sc_values = get_db_values($sql, $values_before);

	set_options($sc_values, $sc, "sc");
	$t->set_var("s", $s);
	if ($search) {
		$t->parse("s_d", false);
	}

	if ($search || $category_id > 0) { 
		$t->parse("items_block", false);
	} else {
		$t->set_var("items_block", "");
	}

	$t->pparse("main");

?>