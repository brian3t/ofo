<?php

function filter_block($block_name, $filter_id, $page_friendly_url, $page_friendly_params, $show_sub_products, $category_path)
{
	global $t, $db, $table_prefix, $filter_properties;
	global $settings, $page_settings, $current_page, $friendly_urls, $currency;
	
	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$sql  = " SELECT filter_type FROM " . $table_prefix . "filters ";
	$sql .= " WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$filter_type = $db->f("filter_type");
	} else {
		return;
	}

	$t->set_file("block_body", "block_filter.html");

	$filter_properties = array();
	$sql  = " SELECT * FROM " . $table_prefix . "filters_properties ";
	$sql .= " WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
	$sql .= " ORDER BY property_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$property_id = $db->f("property_id");
		$property_name = $db->f("property_name");
		$property_value = $db->f("property_value");
		$property_type = $db->f("property_type");
		$filter_from_sql = $db->f("filter_from_sql");
		$filter_join_sql = $db->f("filter_join_sql");
		$filter_where_sql = $db->f("filter_where_sql");
		$list_table = $db->f("list_table");
		$list_field_id = $db->f("list_field_id");
		$list_field_title = $db->f("list_field_title");
		$list_field_total = $db->f("list_field_total");
		$list_sql = $db->f("list_sql");
		$list_group_fields = $db->f("list_group_fields");
		$list_group_where = $db->f("list_group_where");
		if ($property_type == "manufacturer") {
			$list_group_fields = "i.manufacturer_id";
		} else if ($property_type == "property_type") {
			$list_group_fields = "i.item_type_id";
		} else if ($property_type == "product_option") {
			$list_group_fields = "fip_$property_id.property_description, fipv_$property_id.property_value";
			$list_group_where = " fip_".$property_id.".property_name=" . $db->tosql($property_value, TEXT);
		} else if ($property_type == "product_specification") {
			$list_group_fields = "ff_$property_id.feature_value";
			$list_group_where = " ff_".$property_id.".feature_name=" . $db->tosql($property_value, TEXT);
		}

		$filter_property = array(
			"property_name" => $property_name,
			"property_value" => $property_value,
			"filter_from_sql" => $filter_from_sql,
			"filter_join_sql" => $filter_join_sql,
			"filter_where_sql" => $filter_where_sql,
			"list_group_fields" => $list_group_fields,
			"list_group_where" => $list_group_where,
			"list_table" => $list_table,
			"list_field_id" => $list_field_id,
			"list_field_title" => $list_field_title,
			"list_field_total" => $list_field_total,
			"list_sql" => $list_sql,
		);
		$filter_properties[$property_id] = $filter_property;
	}

	// check values for filter properties
	foreach($filter_properties as $property_id => $filter_property) {
		$filter_values = array();
		$list_sql = $filter_property["list_sql"];
		$list_table = $filter_property["list_table"];
		$list_field_id = $filter_property["list_field_id"];
		$list_field_title = $filter_property["list_field_title"];
		$list_field_total = $filter_property["list_field_total"];
		// check predefined values
		$sql  = " SELECT value_id,list_value_id,list_value_title,filter_where_sql ";
		$sql .= " FROM " . $table_prefix . "filters_properties_values ";
		$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
		$sql .= " ORDER BY value_order ";
		$db->query($sql);
		while($db->next_record()) {
			$value_id = $db->f("value_id");
			$list_id = $db->f("list_value_id");
			$value_title = $db->f("list_value_title");
			$where = $db->f("filter_where_sql");
			if ($list_id) {
				$value_key = "fl" . $list_id;
			} else {
				$value_key = "fd" . $value_id;
			}
			$filter_values[$value_key] = array(
				"value_id" => $value_id, "list_id" => $list_id, "title" => $value_title, "total" => "", "where" => $where);
		}

		// check data from SQL queries if there is no predefined values
		if (sizeof($filter_values) == 0) {
			if ($list_sql) {
				$db->query($list_sql);
				while($db->next_record()) {
					$list_id = $db->f($list_field_id);
					if ($list_field_total) {
						$value_title = $db->f($list_field_title);
					} else {
						$value_title = $list_id;
					}
					if ($list_field_total) {
						$value_total = $db->f($list_field_total);
					} else {
						$value_total = "";
					}
					$filter_values["fl" . $list_id] = array(
						"value_id" => "", "list_id" => $list_id, "title" => $value_title, "total" => $value_total, "where" => "");
				}
			} else if ($list_table) {
				$sql = " SELECT " . $list_field_id;	
				if ($list_field_title) { $sql .= "," . $list_field_title;	}
				if ($list_field_total) { $sql .= "," . $list_field_total;	}
				$sql .= " FROM " . $list_table;	
				$db->query($sql);
				while($db->next_record()) {
					$list_id = $db->f($list_field_id);
					if ($list_field_title) {
						$value_title = $db->f($list_field_title);
					} else {
						$value_title = $list_id;
					}
					if ($list_field_total) {
						$value_total = $db->f($list_field_total);
					} else {
						$value_total = "";
					}
					$filter_values["fl" . $list_id] = array(
						"value_id" => "", "list_id" => $list_id, "title" => $value_title, "total" => $value_total, "where" => "");
				}
			}
		}

		// calculate total records for filter values
		$filter_from_sql = $filter_property["filter_from_sql"];
		$filter_join_sql = $filter_property["filter_join_sql"];
		$filter_where_sql = $filter_property["filter_where_sql"];
		$list_group_fields = $filter_property["list_group_fields"];
		$list_group_where = $filter_property["list_group_where"];
		if ($list_group_fields) {
			$values_total = array();
			$group_fields = explode(",", $list_group_fields);
			for ($f = 0; $f < sizeof($group_fields); $f++) {
				$list_group_field = trim($group_fields[$f]);
				$list_field_alias = preg_replace("/^[\w\d_]+\./i", "", $list_group_field);
				$sql = get_filter_sql($filter_type, $filter_from_sql, $filter_join_sql, $list_group_where, $list_group_field, $show_sub_products, $category_path);
				$db->query($sql);
				while ($db->next_record()) {
					$value_id = $db->f($list_field_alias);
					$value_total = $db->f("total");
					if ($value_id && $value_total) {
						if (isset($values_total["fl".$value_id])) {
							$values_total["fl".$value_id] += $value_total;
						} else {
							$values_total["fl".$value_id] = $value_total;
						}
					}
				}
			}
			foreach ($filter_values as $id => $filter_value) {
				if (isset($values_total[$id])) {
					$total = $values_total[$id];
				} else {
					$total = 0;
				}
				if ($total) {
					$filter_values[$id]["total"] = $total;
				} else {
					unset($filter_values[$id]);
				}
			}
		} else {
			foreach ($filter_values as $id => $filter_value) {
				$total = $filter_value["total"];
				if (!strlen($total)) {
					$list_id = $filter_value["list_id"];
					$where = $filter_value["where"];
					if (!$where) {
						$where = $filter_where_sql;
					}
					$where = str_replace("{value_id}", $list_id, $where);
					$where = str_replace("{table_value}", $list_id, $where);
					$sql  = get_filter_sql($filter_type, $filter_from_sql, $filter_join_sql, $where, "", $show_sub_products, $category_path);
					if ($sql && $where) {
						$total = get_db_value($sql);
					}
				}
				if ($total) {
					$filter_values[$id]["total"] = $total;
				} else {
					unset($filter_values[$id]);
				}
			}
		}

    $filter_properties[$property_id]["values"] = $filter_values;
	}

	if ($friendly_urls && $page_friendly_url) {
		$current_page = $page_friendly_url . $friendly_extension;
		$page_friendly_params[] = "filter";
		$transfer_query = transfer_params($page_friendly_params);
	} else {
		$transfer_query = transfer_params(array("filter"));
	}
	if (strlen($transfer_query)) {
		$filter_query = "&filter=";
	} else {
		$filter_query = "?filter=";
	}
	$filter_url = $current_page . $transfer_query;

	// check selected filters
	$filter = get_param("filter");
	$filters = explode("&", $filter);
	for ($f = 0; $f < sizeof($filters); $f++) {
		$filter_params = $filters[$f];
		$filter_value_id = "";
		if (preg_match("/^fl(\d+)=(.+)$/", $filter_params, $matches)) {
			$filter_property_id = $matches[1];
			$filter_list_id = $matches[2];
			if (isset($filter_properties[$filter_property_id]["values"]["fl" . $filter_list_id])) {
				$filter_properties[$filter_property_id]["selected"] = "fl" . $filter_list_id;
				$filter_properties[$filter_property_id]["values"]["fl" . $filter_list_id]["selected"] = true;
			}
		} else if (preg_match("/^fd(\d+)=(.+)$/", $filter_params, $matches)) {
			$filter_property_id = $matches[1];
			$filter_db_id = $matches[2];
			if (isset($filter_properties[$filter_property_id]["values"]["fd" . $filter_db_id])) {
				$filter_properties[$filter_property_id]["selected"] = "fd" . $filter_db_id;
				$filter_properties[$filter_property_id]["values"]["fd" . $filter_db_id]["selected"] = true;
			}
		}
	}

	// parse filters
	$t->set_var("filter_properties_cols", "");
	$t->set_var("filter_properties_rows", "");
	$properties_number = 0;
	$filter_values_limit = get_setting_value($page_settings, "filter_values_limit_" . $filter_id, 10);

	foreach($filter_properties as $property_id => $filter_property) {
		$t->set_var("filter_values", "");
		$t->set_var("filter_more_values", "");
		$t->set_var("filter_more_link", "");
		$t->set_var("filter_selected", "");
		// check if property has any values
		if (is_array($filter_property["values"]) && sizeof($filter_property["values"])) {
			$properties_number++;
			$values_number = 0;
			$t->set_var("property_name", $filter_property["property_name"]);
			$filter_selected = isset($filter_property["selected"]) ? $filter_property["selected"] : "";
			if ($filter_selected) {
	  
				$filter_value = $filter_property["values"][$filter_selected];
				if ($filter_value["list_id"]) {
					$remove_param = "&fl" . $property_id . "=" . $filter_value["list_id"];
				} else {
					$remove_param = "&fd" . $property_id . "=" . $filter_value["value_id"];
				}
				$filter_removed = str_replace($remove_param, "", $filter);
				if ($filter_removed) {
					$value_url = $filter_url . $filter_query . urlencode($filter_removed);
				} else {
					$value_url = $filter_url;
				}
	  
				$t->set_var("value_title", $filter_value["title"]);
				$t->set_var("filter_url", $value_url);
	  
				$t->parse("filter_selected", true);
			} else {
        $filter_values = $filter_property["values"];
				$filter_values_total = sizeof($filter_values);
				if ($filter_values_total == ($filter_values_limit + 1)) {
					$current_values_limit = $filter_values_limit + 1;
				} else {
					$current_values_limit = $filter_values_limit;
				}
				foreach ($filter_values as $id => $filter_value) {
					$values_number++;
					$value_url = $filter_url.$filter_query.urlencode($filter);
					if ($filter_value["list_id"]) {
						$value_url .= urlencode("&fl" . $property_id . "=" . $filter_value["list_id"]);
					} else {
						$value_url .= urlencode("&fd" . $property_id . "=" . $filter_value["value_id"]);
					}
					$t->set_var("value_title", $filter_value["title"]);
					$t->set_var("value_total", $filter_value["total"]);
					$t->set_var("filter_url", $value_url);
	    
					if ($values_number > $current_values_limit) {
						$t->parse("filter_more_values", true);
					} else {
						$t->parse("filter_values", true);
					}
				}
				if ($values_number > $current_values_limit) {
					$t->set_var("property_id", $property_id);
					$t->parse("filter_more_link", false);
				}
			}
			$t->parse("filter_properties_cols", true);
		}
	}
	$t->parse("filter_properties_rows", false);
	
	if ($properties_number) {
		// parse block if any filters properties is available
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}


function get_filter_sql($sql_type, $filter_from_sql, $filter_join_sql, $filter_where_sql, $list_group_field, $show_sub_products, $category_path)
{
	global $db, $table_prefix, $currency;
	global $display_products, $language_code, $site_id;

	$access_level = VIEW_CATEGORIES_ITEMS_PERM;
						
	$user_id         = get_session("session_user_id");
	$user_type_id    = get_session("session_user_type_id");
	$subscription_id = get_session("session_subscription_id");
			
	$sql = "";
	if ($sql_type == "products") {
		$category_id = get_param("category_id");
		$search_category_id = get_param("search_category_id");
		$search_string = trim(get_param("search_string"));
		$pq = get_param("pq");
		$fq = get_param("fq");
		$s_tit = get_param("s_tit");
		$s_cod = get_param("s_cod");
		$s_sds = get_param("s_sds");
		$s_fds = get_param("s_fds");
		$manf = get_param("manf");
		$user = get_param("user");
		if ($display_products != 2 || strlen($user_id)) {
			$lprice = get_param("lprice");
			$hprice = get_param("hprice");
		} else {
			$lprice = ""; $hprice = "";
		}
		$lweight = get_param("lweight");
		$hweight = get_param("hweight");
		$is_search = (strlen($search_string) || ($pq > 0) || ($fq > 0) || strlen($lprice) || strlen($hprice) || strlen($lweight) || strlen($hweight));
  	$is_manufacturer = strlen($manf);
		$is_user = strlen($user);
		if (strlen($search_category_id)) {
			$category_id = $search_category_id;
		}
		if (!strlen($category_id)) $category_id = "0";

		$price_type = get_session("session_price_type");
		if ($price_type == 1) {
			$price_field = "trade_price";
			$sales_field = "trade_sales";
			$properties_field = "trade_properties_price";
		} else {
			$price_field = "price";
			$sales_field = "sales_price";
			$properties_field = "properties_price";
		}

		$pr_where = ""; $pr_brackets = ""; $pr_join = "";
		if ($pq > 0) {
			for ($pi = 1; $pi <= $pq; $pi++) {
				$property_name = get_param("pn_" . $pi);
				$property_value = get_param("pv_" . $pi);
				if (strlen($property_name) && strlen($property_value)) {
					$pr_where .= " AND ip_".$pi.".property_name=" . $db->tosql($property_name, TEXT);
					$pr_where .= " AND (ip_".$pi.".property_description LIKE '%" . $db->tosql($property_value, TEXT, false) . "%' ";
					$pr_where .= " OR ipv_".$pi.".property_value LIKE '%" . $db->tosql($property_value, TEXT, false) . "%') ";
					$pr_brackets .= "((";
					$pr_join  .= " LEFT JOIN " . $table_prefix . "items_properties ip_".$pi." ON i.item_id = ip_".$pi.".item_id) ";
					$pr_join  .= " LEFT JOIN " . $table_prefix . "items_properties_values ipv_".$pi." ON ipv_".$pi.".property_id= ip_".$pi.".property_id) ";
				}
			}
		}
		if ($fq > 0) {
			for ($fi = 1; $fi <= $fq; $fi++) {
				$feature_name = get_param("fn_" . $fi);
				$feature_value = get_param("fv_" . $fi);
				if (strlen($feature_name) && strlen($feature_value)) {
					$pr_where .= " AND f_".$fi.".feature_name=" . $db->tosql($feature_name, TEXT);
					$pr_where .= " AND f_".$fi.".feature_value LIKE '%" . $db->tosql($feature_value, TEXT, false) . "%' ";
					$pr_brackets .= "(";
					$pr_join  .= " LEFT JOIN " . $table_prefix . "features f_".$fi." ON i.item_id = f_".$fi.".item_id) ";
				}
			}
		}

		filter_sqls($pr_brackets, $pr_join, $pr_where);
		// add join sqls for current filter if they don't already present in it
		if ($filter_join_sql && strpos($pr_join, $filter_join_sql) === false) {
			$pr_brackets = $filter_from_sql . $pr_brackets;
			$pr_join .= $filter_join_sql;
		} else if (!$filter_join_sql && $filter_from_sql && strpos($pr_brackets, $filter_from_sql) === false) {
			$pr_brackets = $filter_from_sql . $pr_brackets;
		}

		if ($db->DBType == "access") {
			$sql  = " SELECT COUNT(*) AS total ";
			if ($list_group_field) {
				$sql .= ", " . $list_group_field;
			}
			$sql .= " FROM (SELECT DISTINCT i.item_id ";
		} else {
			$sql  = " SELECT COUNT(DISTINCT i.item_id) AS total ";
		}
		if ($list_group_field) {
			$sql .= ", " . $list_group_field;
		}
		$sql .= " FROM " . $pr_brackets . "((";
		if (isset($site_id)) {
			$sql .= "(";
		}
		if (strlen($user_id)) {
			$sql .= "(";
		}
		if (strlen($subscription_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "items i ";
		$sql .= " INNER JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
		if (($is_search || $is_manufacturer || $show_sub_products) && $category_id != 0)	{
			$sql .= "INNER JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id)";
		} else {
			$sql .= ")";
		}
		if (isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "items_sites AS s ON s.item_id=i.item_id)";
		}			
		if (strlen($user_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "items_user_types AS ut ON ut.item_id=i.item_id)";
		}			
		if (strlen($subscription_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "items_subscriptions AS sb ON sb.item_id=i.item_id)";
		}
		$sql .= $pr_join;
		$sql_where  = " WHERE i.is_showing=1 AND i.is_approved=1 ";
		$sql_where .= " AND ((i.hide_out_of_stock=1 AND i.stock_level > 0) OR i.hide_out_of_stock=0 OR i.hide_out_of_stock IS NULL)";
		$sql_where .= " AND (i.language_code IS NULL OR i.language_code='' OR i.language_code=" . $db->tosql($language_code, TEXT) . ")";
		
		if (isset($site_id)) {
			$sql_where .= " AND (i.sites_all=1 OR s.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
		} else {
			$sql_where .= " AND i.sites_all=1 ";
		}
		if (strlen($user_id) && strlen($subscription_id)) {
			$sql_where .= " AND (" . format_binary_for_sql("i.access_level", $access_level);
			$sql_where .= " OR ( " . format_binary_for_sql("ut.access_level", $access_level) . "  AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") ";
			$sql_where .= " OR ( " . format_binary_for_sql("sb.access_level", $access_level) . "  AND sb.subscription_id=". $db->tosql($subscription_id, INTEGER, true, false) . ") )";
		} elseif (strlen($user_id)) {
			$sql_where .= " AND (" . format_binary_for_sql("i.access_level", $access_level);
			$sql_where .= " OR ( " . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") )";
		} else {
			$sql_where .= " AND " . format_binary_for_sql("i.guest_access_level", $access_level);
		}
			
		if (($is_search || $is_manufacturer || $show_sub_products) && $category_id != 0)	{
			$sql_where .= " AND (ic.category_id = " . $db->tosql($category_id, INTEGER);
			$sql_where .= " OR c.category_path LIKE '" . $db->tosql($category_path, TEXT, false) . "%')";
		} elseif (!$is_search && !$is_manufacturer && !$is_user) {
			$sql_where .= " AND ic.category_id = " . $db->tosql($category_id, INTEGER);
		}
		if (strlen($manf)) {
			$sql_where .= " AND i.manufacturer_id= " . $db->tosql($manf, INTEGER);
		}
		if (strlen($user)) {
			$sql_where .= " AND i.user_id= " . $db->tosql($user, INTEGER);
		}
		if (strlen($lprice)) {
			$conv_price = $lprice / $currency["rate"];
			$sql_where .= " AND ( ";
			$sql_where .= " (i.is_sales=1 AND (i." . $sales_field . "+i.".$properties_field.")>=" . $db->tosql($conv_price, NUMBER) . ") ";
			$sql_where .= " OR ((i.is_sales<>1 OR i.is_sales IS NULL) AND (i." . $price_field . "+i.".$properties_field.")>= " . $db->tosql($conv_price, NUMBER) . ") ";
			$sql_where .= ") ";
		}
		if (strlen($hprice)) {
			$conv_price = $hprice / $currency["rate"];
			$sql_where .= " AND ( ";
			$sql_where .= " (i.is_sales=1 AND (i." . $sales_field . "+i.".$properties_field.")<=" . $db->tosql($conv_price, NUMBER) . ") ";
			$sql_where .= " OR ((i.is_sales<>1 OR i.is_sales IS NULL) AND (i." . $price_field . "+i.".$properties_field.")<= " . $db->tosql($conv_price, NUMBER) . ") ";
			$sql_where .= ") ";
		}
		if (strlen($lweight)) {
			$sql_where .= " AND i.weight>=" . $db->tosql($lweight, NUMBER);
		}
		if (strlen($hweight)) {
			$sql_where .= " AND i.weight<=" . $db->tosql($hweight, NUMBER);
		}
		if (strlen($search_string)) {
			$search_values = split(" ", $search_string);
			for ($si = 0; $si < sizeof($search_values); $si++) {
				$s_fields = 0;
				$sql_where .= " AND ( ";
				if ($s_sds == 1) {
					$s_fields++;
					$sql_where .= " i.short_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
				}
				if ($s_fds == 1) {
					if ($s_fields > 0) {$sql_where .= " OR ";}
					$s_fields++;
					$sql_where .= " i.full_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
				}
				if ($s_tit == 1) {
					if ($s_fields > 0) {$sql_where .= " OR ";}
					$s_fields++;
					$sql_where .= " i.item_name LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
				}
				if ($s_cod == 1) {
					if ($s_fields > 0) {$sql_where .= " OR ";}
					$s_fields++;
					$sql_where .= " i.item_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
				}
				if ($s_fields == 0) {
					$sql_where .= " i.item_name LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR i.item_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
				}
				$sql_where .= " ) ";
			}
		}
		$sql_where .= $pr_where;
		$sql .= $sql_where;
		if ($filter_where_sql) {
			$sql .= " AND (" . $filter_where_sql . ") ";
		}
		if ($list_group_field) {
			if ($db->DBType == "access") {
				$sql .= " ) GROUP BY " . $list_group_field;
			} else {
				$sql .= " GROUP BY " . $list_group_field;
			}
		} else if ($db->DBType == "access") {
			$sql .= " ) ";
		}
	}

	return $sql;
}
?>