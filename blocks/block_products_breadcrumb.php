<?php                           
include_once("./includes/products_functions.php");

function products_breadcrumb($block_name, $erase_tags = true)
{
	global $t, $db, $site_id, $table_prefix;
	global $settings, $page_settings;
	global $category_id, $is_reviews;


	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$user_id = get_session("session_user_id");		
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");
	
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$t->set_file("block_body", "block_products_breadcrumb.html");

	$manf = get_param("manf");
	$user = get_param("user");
	$item_id = get_param("item_id");
	$category_id = get_param("category_id");
	$search_category_id = get_param("search_category_id");
	
	$breadcrumbs_tree_array = array();
	
	if (strlen($search_category_id)) {
		$category_id = $search_category_id;
	}
	if (!strlen($category_id) && strlen($item_id)) {
		$category_id = VA_Products::get_category_id($item_id);
	}
	$t->set_var("index_href", get_custom_friendly_url("index.php"));

	if ($category_id) {
		$current_id = $category_id;
		$sql  = " SELECT c.category_id, c.category_name, c.friendly_url, c.parent_category_id  FROM ";
		if (isset($site_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "categories c";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "categories_sites cs ON cs.category_id = c.category_id) ";	
			$sql .= " WHERE (c.sites_all=1 OR cs.site_id=" . $db->tosql($site_id, INTEGER) . ")";
		} else {
			$sql .= " WHERE c.sites_all=1";
		}
		$sql .= " AND c.category_id=";
		while ($current_id)
		{
			$db->query($sql . $db->tosql($current_id, INTEGER));
			if ($db->next_record()) {
				$category_name = $db->f("category_name");
				$category_name = get_translation($category_name);
				$friendly_url = $db->f("friendly_url");
				if ($friendly_urls && $friendly_url) {
					$tree_url = $friendly_url . $friendly_extension;
				} else {
					$tree_url = get_custom_friendly_url("products.php") . "?category_id=". urlencode($current_id);
				}

				$tree_title = $category_name;
				if ($erase_tags) { $tree_title = strip_tags($tree_title); }
				array_unshift($breadcrumbs_tree_array, array ($tree_url, $tree_title));
				$current_id= $db->f("parent_category_id");
			} else {
				$current_id = "0";
			}
		}
	}

	$tree_title = PRODUCTS_TITLE;
	if ($erase_tags) { $tree_title = strip_tags($tree_title); }
	array_unshift($breadcrumbs_tree_array, array (get_custom_friendly_url("products.php"), $tree_title));

	if (strlen($manf)) {
		$sql = "SELECT manufacturer_name, friendly_url FROM " . $table_prefix . "manufacturers WHERE manufacturer_id=" . $db->tosql($manf, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$friendly_url = $db->f("friendly_url");
			if ($friendly_urls && $friendly_url) {
				$tree_url = $friendly_url . $friendly_extension;
				if ($category_id) {
					$tree_url .= "?category_id=" . urlencode($category_id);
				}
			} elseif ($category_id) {
				$tree_url = get_custom_friendly_url("products.php") . "?category_id=" . urlencode($category_id) . "&manf=" . urlencode($manf);
			} else {
				$tree_url = get_custom_friendly_url("products.php") . "?manf=" . urlencode($manf);
			}
			$tree_title = $db->f("manufacturer_name");
			if ($erase_tags) { $tree_title = strip_tags($tree_title); }

			$breadcrumbs_tree_array[] = array ($tree_url, $tree_title);
		}
	}

	if (strlen($user)) {
		$sql = "SELECT company_name, name, login, friendly_url FROM " . $table_prefix . "users WHERE user_id=" . $db->tosql($user, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$company_name = $db->f("company_name");
			if (!strlen($company_name)) {
				$company_name = $db->f("name");
			}
			if (!strlen($company_name)) {
				$company_name = $db->f("login");
			}
			$friendly_url = $db->f("friendly_url");
			if ($friendly_urls && $friendly_url) {
				$tree_url = $friendly_url . $friendly_extension;
			} else {
				$tree_url = get_custom_friendly_url("user_list.php") . "?user=" . urlencode($user);
			}

			$tree_title = $company_name;
			if ($erase_tags) { $tree_title = strip_tags($tree_title); }
			$breadcrumbs_tree_array[] = array ($tree_url, $tree_title);
		}
	}

	// check search
	$ps_parameters = array();
	$search_params = array(
		"search_string", "lprice", "hprice", "lweight", "hweight"
	);
	for ($si = 0; $si < sizeof($search_params); $si++) {
		$search_param = $search_params[$si];
		$param_value  = get_param($search_param);
		if (strlen($param_value)) {
			$ps_parameters[$search_param] = $param_value;
		}
	}

	$pq = get_param("pq");
	$fq = get_param("fq");
	if ($pq > 0) {
		for ($pi = 1; $pi <= $pq; $pi++) {
			$property_name = get_param("pn_" . $pi);
			$property_value = get_param("pv_" . $pi);
			if (strlen($property_name) && strlen($property_value)) {
				$ps_parameters["pq"] = $pq;
				$ps_parameters["pn_" . $pi] = $property_name;
				$ps_parameters["pv_" . $pi] = $property_value;
			}
		}
	}
	if ($fq > 0) {
		for ($fi = 1; $fi <= $fq; $fi++) {
			$feature_name = get_param("fn_" . $fi);
			$feature_value = get_param("fv_" . $fi);
			if (strlen($feature_name) && strlen($feature_value)) {
				$ps_parameters["fq"] = $fq;
				$ps_parameters["fn_" . $fi] = $feature_name;
				$ps_parameters["fv_" . $fi] = $feature_value;
			}
		}
	}

	// Proceed products search parameters
	if (sizeof($ps_parameters) > 0) {
		$ps_parameters["s_tit"] = get_param("s_tit");
		$ps_parameters["s_cod"] = get_param("s_cod");
		$ps_parameters["s_sds"] = get_param("s_sds");
		$ps_parameters["s_fds"] = get_param("s_fds");
		$ps_parameters["category_id"] = $category_id;
		$ps_parameters["manf"] = get_param("manf");
		$query_string = get_query_string($ps_parameters, "", "", false);
		$tree_url = get_custom_friendly_url("products_search.php") . $query_string;
		$tree_title = SEARCH_RESULTS_MSG;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array ($tree_url, $tree_title);
	}

	if (strlen($item_id)) {
		$ps_parameters["category_id"] = get_param("category_id");
		$sql  = "SELECT i.item_name, i.friendly_url FROM ";
		$sql .= $table_prefix . "items i";	
		$sql .= " WHERE i.item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$item_name = get_translation($db->f("item_name"));
			$friendly_url = $db->f("friendly_url");
			if ($friendly_urls && $friendly_url) {
				$query_string = get_query_string($ps_parameters, "", "", false);
				$tree_url = $friendly_url . $friendly_extension . $query_string;
			} else {
				$ps_parameters["item_id"] = $item_id;
				$query_string = get_query_string($ps_parameters, "", "", false);
				$tree_url = get_custom_friendly_url("product_details.php") . $query_string;
			}
			$tree_title = $item_name;
			if ($erase_tags) { $tree_title = strip_tags($tree_title); }

			$breadcrumbs_tree_array[] = array ($tree_url, $tree_title);
		}
	}
	if ($is_reviews) {
		$query_string = get_query_string($ps_parameters, "", "", false);
		$tree_url = get_custom_friendly_url("reviews.php") . $query_string;
		$tree_title = REVIEWS_MSG;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array ($tree_url, $tree_title);
	}
	
	$ic = count($breadcrumbs_tree_array) - 1;
	for ($i=0; $i<$ic; $i++) {
		$t->set_var("tree_url", $breadcrumbs_tree_array[$i][0]);
		$t->set_var("tree_title", $breadcrumbs_tree_array[$i][1]);
		$t->set_var("tree_class", "");
		$t->parse("tree", true);
	}
	if ($ic>=0) {
		$t->set_var("tree_url", $breadcrumbs_tree_array[$ic][0]);
		$t->set_var("tree_title", $breadcrumbs_tree_array[$ic][1]);
		$t->set_var("tree_class", "treeItemLast");
		$t->parse("tree", true);
	}
	
	if (isset($settings["is_rss"]) && $settings["is_rss"]){
		if ($category_id){
			$t->set_var("rss_url","products_rss.php?category_id=".$category_id);
			$t->parse("rss",false);
		} else {
			$t->set_var("rss_url","products_rss.php");
			$t->parse("rss",false);
		}
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>