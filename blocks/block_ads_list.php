<?php

function ads_list($block_name, $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $restrict_ads_images;
	global $category_id;
	global $sc_item_id, $sc_category_id, $item_added, $sc_errors;
	global $site_id, $db_type, $currency;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_ads_list.html");
		
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$ads_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
		$ads_page = "ads.php"; //"articles.php";
	}
	
	srand ((double) microtime() * 1000000);
	$random_value = rand();

	$category_id        = get_param("category_id");
	$search_category_id = get_param("search_category_id");
	$search_string = trim(get_param("search_string"));
	$pq = get_param("pq");
	$fq = get_param("fq");
	$s_tit  = get_param("s_tit");
	$s_sds  = get_param("s_sds");
	$s_fds  = get_param("s_fds");
	$user   = get_param("user");
	$lprice = get_param("lprice");
	$hprice = get_param("hprice");
	$country = get_param("country");
	$state = get_param("state");
	$zip = get_param("zip");
	$page = get_param("page");
	$is_search = (strlen($search_string) || ($pq > 0) || ($fq > 0) || strlen($lprice) || strlen($hprice) || $country || $state || strlen($zip));
	$is_user = strlen($user);

	$allowed_all_items = false;
	if($search_category_id) {
		$category_id = $search_category_id;
		if (!VA_Ads_Categories::check_permissions($search_category_id, VIEW_CATEGORIES_ITEMS_PERM)) {
			$category_id = 0;
		}
	} elseif ($category_id) {
		$allowed_all_items = VA_Ads_Categories::check_permissions($category_id, VIEW_ITEMS_PERM);
	} else {
		$category_id = 0;
	}

	$t->set_var("ads_href",         "ads.php");
	$t->set_var("ads_details_href", "ads_details.php");
	$t->set_var("compare_href",     "ads_compare.php");
	$t->set_var("cl", $currency["left"]);
	$t->set_var("cr", $currency["right"]);
		
	$sql_where = "";
	if (($is_search || $is_user) && $category_id != 0)	{
		$search_tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", ADS_TITLE);
		$sql_where .= " (c.category_id = " . $db->tosql($category_id, INTEGER);		
		$sql_where .= " OR c.category_path LIKE '" . $db->tosql($search_tree->get_path($category_id), TEXT, false) . "%')";
		$allowed_all_items = false;
	} else if(!$is_search && !$is_user) {
		$sql_where .= " c.category_id = " . $db->tosql($category_id, INTEGER);
	}
	$categories_ids = VA_Ads_Categories::find_all_ids($sql_where, VIEW_CATEGORIES_PERM);
	if (!$categories_ids) return;
	
	$sql  = " SELECT category_name FROM " . $table_prefix . "ads_categories ";
	$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
	$current_category_name = get_translation(get_db_value($sql));
	if (!$current_category_name) { 
		$current_category_name = ADS_TITLE; 
	}
	$t->set_var("current_category_name", htmlspecialchars($current_category_name));

	$pr_where = ""; $pr_brackets = ""; $pr_join = "";
	if ($pq > 0) {
		for($pi = 1; $pi <= $pq; $pi++) {
			$property_name = get_param("pn_" . $pi);
			$property_value = get_param("pv_" . $pi);
			if (strlen($property_name) && strlen($property_value)) {
				$pr_where .= " AND ap_".$pi.".property_name=" . $db->tosql($property_name, TEXT);
				$pr_where .= " AND ap_".$pi.".property_value LIKE '%" . $db->tosql($property_value, TEXT, false) . "%' ";
				$pr_brackets .= "(";
				$pr_join  .= " LEFT JOIN " . $table_prefix . "ads_properties ap_".$pi." ON i.item_id = ap_".$pi.".item_id) ";
			}
		}
	}
	if ($fq > 0) {
		for($fi = 1; $fi <= $fq; $fi++) {
			$feature_name = get_param("fn_" . $fi);
			$feature_value = get_param("fv_" . $fi);
			if (strlen($feature_name) && strlen($feature_value)) {
				$pr_where .= " AND f_".$fi.".feature_name=" . $db->tosql($feature_name, TEXT);
				$pr_where .= " AND f_".$fi.".feature_value LIKE '%" . $db->tosql($feature_value, TEXT, false) . "%' ";
				$pr_brackets .= "(";
				$pr_join  .= " LEFT JOIN " . $table_prefix . "ads_features f_".$fi." ON i.item_id = f_".$fi.".item_id) ";
			}
		}
	}

	$sql_join = $table_prefix . "ads_items i";
	$sql_join = " (" . $sql_join . " INNER JOIN " . $table_prefix . "ads_assigned c ON i.item_id=c.item_id) ";
		
	$sql_join = $pr_brackets . $sql_join . $pr_join;
	$sql_where  = " WHERE i.is_approved=1 ";
	$sql_where .= " AND i.date_start<=" . $db->tosql(va_time(), DATETIME);
	$sql_where .= " AND i.date_end>" . $db->tosql(va_time(), DATETIME);
	$sql_where .= " AND c.category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";
	if (strlen($user)) {
		$sql_where .= " AND i.user_id= " . $db->tosql($user, INTEGER);
	}
	if (strlen($country)) {
		$sql_where .= " AND i.location_country_id= " . $db->tosql($country, INTEGER);
	}
	if (strlen($state)) {
		$sql_where .= " AND i.location_state_id= " . $db->tosql($state, INTEGER);
	}
	if (strlen($zip)) {
		$sql_where .= " AND i.location_postcode LIKE '%" . $db->tosql($zip, TEXT, false) . "%' ";
	}
	if (strlen($lprice)) {	
		$conv_price = $lprice / $currency["rate"];
		$sql_where .= " AND i.price>=" . $db->tosql($conv_price, NUMBER);
	}
	if (strlen($hprice)) {	
		$conv_price = $hprice / $currency["rate"];
		$sql_where .= " AND i.price<=" . $db->tosql($conv_price, NUMBER);
	}
	if (strlen($search_string)) {
		$search_values = split(" ", $search_string);
		for($si = 0; $si < sizeof($search_values); $si++) {
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
			if ($s_tit == 1 || $s_fields == 0) {
				if ($s_fields > 0) {$sql_where .= " OR ";}
				$s_fields++;
				$sql_where .= " i.item_title LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
			}
			$sql_where .= " ) ";
		}
	}
	$sql  = " SELECT i.item_id FROM " . $sql_join . $sql_where . $pr_where . " GROUP BY i.item_id ";
	$db->query($sql);          
	$total_records = 0;
	$items_ids = array(); 
	while ($db->next_record()) {
		$total_records++;
		$items_ids[] .= $db->f("item_id");
	}
	// set up variables for navigator
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $ads_page);

	$records_per_page = get_setting_value($page_settings, "ads_list_per_page", 10);
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false, $pass_parameters);
	$total_pages = ceil($total_records / $records_per_page);

	// generate page link with query parameters
	$pass_parameters["page"] = $page;
	$query_string = get_query_string($pass_parameters, "", "", true);
	$rp  = $ads_page;
	$rp	.= $query_string;
	$cart_link  = $rp;
	$cart_link .= strlen($query_string) ? "&" : "?";
	$cart_link .= "rnd=" . $random_value . "&";

	$t->set_var("rnd", $random_value);
	$t->set_var("rp_url", urlencode($rp));
	$t->set_var("rp", htmlspecialchars($rp));
	$t->set_var("total_records", $total_records);
	$t->set_var("search_string", htmlspecialchars($search_string));

	if ($total_records>0) {			
		
		$items_categories = array();
		if ($is_search || ($is_user && !$category_id)) {
			$sql  = " SELECT ic.item_id, ic.category_id, c.category_name ";
			$sql .= " FROM (" . $table_prefix . "ads_assigned ic ";								
			$sql .= " LEFT JOIN " . $table_prefix . "ads_categories c ON c.category_id = ic.category_id) ";			
			$sql .= " WHERE ic.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
			$sql .= " AND ic.category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")";
			$db->query($sql);
			$categories_ids = array();
			$allowed_categories = array();
			while ($db->next_record()) {
				$item_id = $db->f("item_id");
				$ic_id   = $db->f("category_id");
				$ic_name = get_translation($db->f("category_name"));
				$ic_name = get_currency_message($ic_name, $currency);
				$categories_ids[] = $ic_id;
				$items_categories[$item_id][] = array($ic_id, $ic_name);
			}
			
			if ($categories_ids) {
				$allowed_categories = VA_Ads_Categories::find_all_ids("c.category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")", VIEW_CATEGORIES_ITEMS_PERM);
			}
			
			$allowed_items = array();
			foreach ($items_ids AS $item_id) {
				$allowed_items[$item_id] = VA_Ads::get_category_id($item_id, VIEW_ITEMS_PERM);
			}
		}		

		$db->RecordsPerPage = $records_per_page;
		$db->PageNumber = $page_number;
				
		$sql  = " SELECT i.item_id, it.type_name, i.item_title, i.friendly_url, i.short_description, i.image_small, i.price, i.quantity, i.availability, ";
		$sql .= " i.is_compared, i.user_id, ut.type_name AS user_type_name, ";	
		$sql .= " u.login, u.name, u.first_name, u.last_name ";
		$sql .= " FROM (((" . $table_prefix . "ads_items i ";
		$sql .= " INNER JOIN " . $table_prefix . "ads_types it ON i.type_id= it.type_id)";
		$sql .= " LEFT JOIN " . $table_prefix . "users u ON i.user_id=u.user_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "user_types ut ON ut.type_id=u.user_type_id) ";
		$sql .= " WHERE i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY date_start DESC, date_added DESC";
		
		$db->query($sql);
		if ($db->next_record()) {
			$columns = get_setting_value($page_settings, "ads_list_columns", 1);
			$t->set_var("item_column", (100 / $columns) . "%");
			$t->set_var("total_columns", $columns);
			$t->set_var("forms", "");
			$item_number = 0;
			do
			{
				$item_number++;
				$item_id = $db->f("item_id");
				$type_name = $db->f("type_name");
				$form_id = $category_id . "_" . $item_id;
				$item_title = get_translation($db->f("item_title"));
				$friendly_url = $db->f("friendly_url");
				$availability = get_translation($db->f("availability"));
				$price = $db->f("price");
				$quantity = $db->f("quantity");
				$is_compared = $db->f("is_compared");
				$user_id = $db->f("user_id");
				$user_type_name = $db->f("user_type_name");
				$name = $db->f("name");
				$login = $db->f("login");
				$first_name = $db->f("first_name");
				$last_name = $db->f("last_name");
				if (strlen($name)) {
					$user_name = $name;
				} else if (strlen($first_name) || strlen($last_name)) {
					$user_name = $first_name." ".$last_name;
				} else {
					$user_name = $login;
				}
				if ($is_search || ($is_user && !$category_id)) {
					$item_categories = $items_categories[$item_id];
					$total_categories = sizeof($item_categories);
					$t->set_var("found_categories", "");
					for ($ic = 0; $ic < $total_categories; $ic++) {
						$ic_separator = ($ic != ($total_categories - 1)) ? "," : "";
						$ic_id = $item_categories[$ic][0];
						$ic_name = $item_categories[$ic][1];
						$t->set_var("ic_id", $ic_id);
						$t->set_var("item_category", $ic_name);
						$t->set_var("ic_separator", $ic_separator);
						if (!$allowed_categories || !in_array($ic_id, $allowed_categories)) {
							$t->set_var("restricted_cat_class", " restrictedCategory");
							$t->sparse("restricted_cat_image", false);
						} else {
							$t->set_var("restricted_cat_class", "");
							$t->set_var("restricted_cat_image", "");
						}
						$t->sparse("found_categories", true);
					}
					if (!$allowed_items[$item_id]) {
						$t->set_var("restricted_class", " restrictedItem");
						$t->sparse("restricted_image", false);
					} else {
						$t->set_var("restricted_class", "");
						$t->set_var("restricted_image", "");
					}
					$t->sparse("found_in_category", false);
				} else {
					$t->set_var("found_in_category", "");
					if (!$allowed_all_items) {
						$t->set_var("restricted_class", " restrictedItem");
						$t->sparse("restricted_image", false);
					} else {
						$t->set_var("restricted_class", "");
						$t->set_var("restricted_image", "");
					}
				}
	
				$properties = show_ads_properties($item_id);
	
				$t->set_var("item_id", $item_id);
				$t->set_var("type_name", $type_name);
				$t->set_var("ic_id", $category_id);
				$t->set_var("form_id", $form_id);
				$t->set_var("item_title", $item_title);
				if ($friendly_urls && $friendly_url) {
					$t->set_var("ads_details_url", $friendly_url . $friendly_extension); // . "?category_id=". $category_id
				} else {
					$t->set_var("ads_details_url", "ads_details.php?category_id=". $category_id . "&item_id=" . $item_id);
				}
	
				if (strlen($user_id)) {
					$t->set_var("user_id", htmlspecialchars($user_id));
					$t->set_var("user_type_name", htmlspecialchars($user_type_name));
					$t->set_var("user_name", htmlspecialchars($user_name));
					$t->global_parse("user_block", false, false, true);
				} else {
					$t->set_var("user_block", "");
				}
				$t->set_var("price", currency_format($price));
				if (strlen($availability)) {
					$t->set_var("availability", htmlspecialchars($availability));
					$t->global_parse("availability_block", false, false, true);
				} else {
					$t->set_var("availability_block", "");
				}
				if (strlen($quantity)) {
					$t->set_var("quantity", htmlspecialchars($quantity));
					$t->global_parse("quantity_block", false, false, true);
				} else {
					$t->set_var("quantity_block", "");
				}
	
				$short_description = get_translation($db->f("short_description"));
				if($short_description) {
					$t->set_var("short_description", nl2br($short_description));
					$t->parse("description", false);
				} else {
					$t->set_var("description", "");
				}
				$small_image = $db->f("image_small");
				if($small_image) {
					if (preg_match("/^http\:\/\//", $small_image)) {
						$image_size = "";
					} else {
		        		$image_size = @GetImageSize($small_image);
						if (isset($restrict_ads_images) && $restrict_ads_images) { 
							$small_image = "image_show.php?ad_id=".$item_id."&type=small"; 
						}
					}
	        		$t->set_var("alt", htmlspecialchars($item_title));
	        		$t->set_var("src", htmlspecialchars($small_image));
					if(is_array($image_size)) {
						$t->set_var("width", "width=\"" . $image_size[0] . "\"");
						$t->set_var("height", "height=\"" . $image_size[1] . "\"");
					} else {
						$t->set_var("width", "");
						$t->set_var("height", "");
					}
					$t->parse("small_image", false);
				} else {
					$t->set_var("small_image", "");
				}
				
				if ($is_compared) {
					$t->global_parse("compare", false, false, true);
					$t->parse("forms", true);
				} else {
					$t->set_var("compare", "");
				}
	
	
				$t->parse("items_cols");
				$is_next_record = $db->next_record();
				if($item_number % $columns == 0)
				{
					if($is_next_record)
						$t->parse("delimiter", false);
					else 
						$t->set_var("delimiter", "");
					$t->parse("items_rows");
					$t->set_var("items_cols", "");
				}
			} while ($is_next_record);              	
			$t->set_var("delimiter", "");
	
			if($item_number % $columns != 0)
				$t->parse("items_rows");
			
			if ($total_pages > 1)
				$t->parse("search_and_navigation", false);
	
			$t->parse("block_body", false);
			$t->set_var("no_items", "");
		}
	}
	else
	{
		$t->set_var("forms", "");
		$t->set_var("items_rows", "");
		$t->parse("no_items", false);
		$t->parse("block_body", false);
	}
	
	// show search results information
	if($is_search) {
		$found_message = str_replace("{found_records}", $total_records, FOUND_ADS_MSG);
		$found_message = str_replace("{search_string}", htmlspecialchars($search_string), $found_message);
		$t->set_var("FOUND_ADS_MSG", $found_message);
		$t->parse("search_results", false);
		$t->parse("search_and_navigation", false);
		$t->parse("block_body", false);
	} 

	if ($total_records > 0 || $is_search) {
		$t->parse($block_name, true);
	}

}

?>