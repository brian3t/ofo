<?php	
	function site_map($block_name)
	{
		global $t, $db, $site_id, $table_prefix, $language_code;
		global $page_settings, $current_page;
		global $categories, $settings, $sitemap_settings;
		global $currency;
				
		include_once("./messages/" . $language_code . "/manuals_messages.php");
		include_once("./includes/products_functions.php");
		include_once("./includes/articles_functions.php");
		include_once("./includes/forums_functions.php");
		include_once("./includes/ads_functions.php");
		include_once("./includes/manuals_functions.php");
		
		$user_id = get_session("session_user_id");
		$user_type_id = get_session("session_user_type_id");
		
		$sitemap_settings = get_settings("site_map");
		$site_map_custom_pages      = get_setting_value($sitemap_settings, "site_map_custom_pages");
		$site_map_categories        = get_setting_value($sitemap_settings, "site_map_categories");
		$site_map_forums            = get_setting_value($sitemap_settings, "site_map_forums");
		$site_map_ad_categories     = get_setting_value($sitemap_settings, "site_map_ad_categories");
		$site_map_manual_categories = get_setting_value($sitemap_settings, "site_map_manual_categories");
	
		$t->set_file("block_body", "block_site_map.html");

		$t->set_var("item", "");
		$t->set_var("items_rows", "");
		$t->set_var("navigator_block", "");

		$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $current_page);
		
		$show_map = false;

		$current_record = 0;
		$first_record_on_page = 0;
		$last_record_on_page = 0;
		$total_records = 0;

		$friendly_urls = 0;
		if (isset($settings["friendly_urls"])) {
			$friendly_urls = $settings["friendly_urls"];
		}
		$friendly_extension = "";
		if (isset($settings["friendly_extension"])) {
			$friendly_extension = $settings["friendly_extension"];
		}

		// begin custom pages
		if ($site_map_custom_pages) {
			$sql  = " SELECT p.page_code, p.page_title, p.page_url FROM ";
			if (isset($site_id)) {
				$sql .= "(";
			}
			if (strlen($user_id)) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "pages p ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "pages_sites s ON (s.page_id=p.page_id AND p.sites_all=0)) ";
			}
			if (strlen($user_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "pages_user_types ut ON (ut.page_id=p.page_id AND p.user_types_all=0)) ";
			}
			$sql .= " WHERE p.is_showing=1 AND p.is_site_map=1 ";
			if (isset($site_id)) {
				$sql .= " AND (p.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " AND p.sites_all=1";
			}
			if (strlen($user_id)) {
				$sql .= " AND (p.user_types_all=1 OR ut.user_type_id=". $db->tosql($user_type_id, INTEGER) . ") ";
			} else {
				$sql .= " AND p.user_types_all=1 ";
			}
			$sql .= " ORDER BY p.page_order, p.page_title ";
			$db->query($sql);
			$total_records = $db->num_rows();
		}
		// end custom pages

		// begin products
		$products_total_records = 0;
		if ($site_map_categories) {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = PRODUCTS_TITLE;
			$categories[0]["parent_id"] = -1;
			$products_categories = VA_Categories::find_all("c.category_id", 
				array("c.category_name", "c.parent_category_id"),
				array("order" => " ORDER BY c.category_order, c.category_name")
			);
			if ($products_categories) {
				foreach ($products_categories AS $cur_category_id => $cur_category){
					$category_name = get_translation($cur_category["c.category_name"], $language_code);
					$category_name = get_currency_message($category_name, $currency);
					$parent_category_id = $cur_category["c.parent_category_id"];
					$categories[$cur_category_id]["parent_id"] = $parent_category_id;
					$categories[$cur_category_id]["category_name"] = $category_name;
					$categories[$parent_category_id]["subs"][] = $cur_category_id;
				}
			}
			if (sizeof($categories) > 0) {
				count_show_map_items(-1, 0, -1, 'item', $products_total_records);
			}
		} else {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = PRODUCTS_TITLE;
			$categories[0]["parent_id"] = -1;
			count_show_map_items(-1, 0, -1, 'item', $products_total_records);
		}
		$total_records += $products_total_records;
		// end products

		// begin articles
		$articles_total_records	= array();
		$articles_top_categories_ids = VA_Articles_Categories::find_all_ids(array(
				"where" => " c.parent_category_id=0 ",
				"order" => " ORDER BY c.category_order, c.category_name"
			)
		);
		if ($articles_top_categories_ids) {
			foreach ($articles_top_categories_ids AS $article_top_category_id) {		
				$is_allowed = get_setting_value($sitemap_settings,"site_map_articles_categories_" . $article_top_category_id);
				$is_allowed_articles = get_setting_value($sitemap_settings, "site_map_articles_" . $article_top_category_id);
				
				$categories = array();
				$params = array();
				$params["order"] = " ORDER BY c.category_order, c.category_name";
				if ($is_allowed) {
					$params["where"] = " (c.category_path LIKE '%" . $article_top_category_id . ",%' OR c.category_id=" . $article_top_category_id.") ";
				} else {
					$params["where"] = " c.category_id=" . $article_top_category_id." ";
				}
				
				$articles_categories = VA_Articles_Categories::find_all("c.category_id", array("c.category_name", "c.parent_category_id"), $params);
				if ($articles_categories) {
					foreach ($articles_categories AS $cur_category_id => $cur_category){
						$site_map_articles_categories = get_setting_value($page_settings, "site_map_articles_categories");
						$category_name = get_translation($cur_category["c.category_name"], $language_code);
						$category_name = get_currency_message($category_name, $currency);
						$parent_category_id = $cur_category["c.parent_category_id"];
						$categories[$cur_category_id]["parent_id"] = $parent_category_id;
						$categories[$cur_category_id]["category_name"] = $category_name;
						$categories[$parent_category_id]["subs"][] = $cur_category_id;
						$categories[$cur_category_id]["allowed_view"] = $is_allowed;
						$categories[$cur_category_id]["show_only_articles"] = (!$is_allowed && $is_allowed_articles);
					}
				}
				$articles_total_records[$article_top_category_id] = 0;
				count_show_map_items(0, 0, 0, 'article', $articles_total_records[$article_top_category_id]);
				$total_records += $articles_total_records[$article_top_category_id];
			}
		}
		// end articles
		
		// begin forum
		$forum_total_records = 0;
		if ($site_map_forums) {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = FORUM_TITLE;
			$categories[0]["parent_id"] = -1;
			$sql  = " SELECT c.category_id, c.category_name FROM ";
			if (isset($site_id)) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "forum_categories c ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "forum_categories_sites s ON (s.category_id=c.category_id AND c.sites_all=0)) ";
			}
			$sql .= " WHERE c.allowed_view=1";
			if (isset($site_id)) {
				$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " AND c.sites_all=1";
			}
			$sql .= " ORDER BY c.category_order, c.category_name ";
			$db->query($sql);
			while ($db->next_record()) {
				$cur_category_id = $db->f("category_id");
				$category_name = get_translation($db->f("category_name"), $language_code);
				$category_name = get_currency_message($category_name, $currency);
				$categories[$cur_category_id]["parent_id"] = 0;
				$categories[$cur_category_id]["category_name"] = $category_name;
				$categories[0]["subs"][] = $cur_category_id;
			}
				
			if (sizeof($categories) > 0) {
				count_show_map_items(-1, 0, -1, 'forum', $forum_total_records);
			}
		} else {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = FORUM_TITLE;
			$categories[0]["parent_id"] = -1;
			count_show_map_items(-1, 0, -1, 'forum', $forum_total_records);
		}
		$total_records += $forum_total_records;
		// end forum
		
		// begin ads
		$ads_total_records = 0;
		if ($site_map_ad_categories) {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = ADS_TITLE;
			$categories[0]["parent_id"] = -1;
			$ads_categories = VA_Ads_Categories::find_all("c.category_id", 
				array("c.category_name", "c.parent_category_id"),
				array("order" => " ORDER BY c.category_order, c.category_name")
			);
			if ($ads_categories) {
				foreach ($ads_categories AS $cur_category_id => $cur_category){
					$category_name = get_translation($cur_category["c.category_name"], $language_code);
					$category_name = get_currency_message($category_name, $currency);
					$parent_category_id = $cur_category["c.parent_category_id"];
					$categories[$cur_category_id]["parent_id"] = $parent_category_id;
					$categories[$cur_category_id]["category_name"] = $category_name;
					$categories[$parent_category_id]["subs"][] = $cur_category_id;
				}
			}
			if (sizeof($categories) > 0) {
				count_show_map_items(-1, 0, -1, 'ads', $ads_total_records);
			}
		} else {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = ADS_TITLE;
			$categories[0]["parent_id"] = -1;
			count_show_map_items(-1, 0, -1, 'ads', $ads_total_records);
		}
		$total_records += $ads_total_records;
		// end ads

		// begin manual
		$manual_total_records = 0;
		if ($site_map_manual_categories) {
			$manual_articles = array();
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = MANUALS_TITLE;
			$categories[0]["parent_id"] = -1;
			$manuals_categories = VA_Manuals_Categories::find_all("c.category_id", 
				array("c.category_name"),
				array("order" => " ORDER BY c.category_order, c.category_name")
			);
			if ($manuals_categories) {
				foreach ($manuals_categories AS $cur_category_id => $cur_category){
					$category_name = get_translation($cur_category["c.category_name"], $language_code);
					$category_name = get_currency_message($category_name, $currency);
					$categories[$cur_category_id]["parent_id"] = 0;
					$categories[$cur_category_id]["category_name"] = $category_name;
					$categories[0]["subs"][] = $cur_category_id;
				}
			}				
			if (sizeof($categories) > 0) {
				count_show_map_items(-1, 0, -1, 'manual', $manual_total_records);
			}
		} else {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = MANUALS_TITLE;
			$categories[0]["parent_id"] = -1;
			count_show_map_items(-1, 0, -1, 'manual', $manual_total_records);
		}
		$total_records += $manual_total_records;
		// end manual

		$pages_number = 1;
		$records_per_page = get_setting_value($sitemap_settings, "site_map_records_per_page", 25);
		if ($records_per_page) {
			$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
			$first_record_on_page = ($page_number-1)*$records_per_page;
			$last_record_on_page = $page_number*$records_per_page;
		}

		// top image to show in tree
		$top_tw = 14; $top_th = 20;
		$image_path = "images/" . $settings["style_name"] . "/tree_top.gif";
		if (file_exists($image_path)) {
			$image_tree_top = $image_path;
		} else {
			$image_tree_top = "images/tree_top.gif";
		}
		$top_image_size = @getimagesize($image_tree_top);
		if (is_array($top_image_size)) {
			$top_tw = $top_image_size[0];
			$top_th = $top_image_size[1];
		} else {
			$image_tree_top = "images/tr.gif";
		}
			
		// begin custom pages
		if ($site_map_custom_pages) 
		{
			$sql  = " SELECT p.page_code, p.page_title, p.page_url, p.friendly_url FROM ";
			if (isset($site_id)) {
				$sql .= "(";
			}
			if (strlen($user_id)) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "pages p ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "pages_sites s ON (s.page_id=p.page_id AND p.sites_all=0)) ";
			}
			if (strlen($user_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "pages_user_types ut ON (ut.page_id=p.page_id AND p.user_types_all=0)) ";
			}
			$sql .= " WHERE p.is_showing=1 AND p.is_site_map=1 ";
			if (isset($site_id)) {
				$sql .= " AND (p.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " AND p.sites_all=1";
			}
			if (strlen($user_id)) {
				$sql .= " AND (p.user_types_all=1 OR ut.user_type_id=". $db->tosql($user_type_id, INTEGER) . ") ";
			} else {
				$sql .= " AND p.user_types_all=1 ";
			}
			$sql .= " ORDER BY p.page_order, p.page_title ";			
			$db->query($sql);
			while ($db->next_record()) {
				$current_record++;
				$item_name = get_translation($db->f('page_title'), $language_code);
				$item_name = get_currency_message($item_name, $currency);
				if ($db->f("friendly_url") && $friendly_urls) {
					$item_url = $settings["site_url"] . $db->f("friendly_url") . $friendly_extension;
				} elseif ($db->f('page_url')) {
					$item_url = $settings["site_url"] . $db->f("page_url");
				} else {
					$item_url = $settings["site_url"]."page.php?page=" . $db->f("page_code");
				}
	
				if ($records_per_page) {
					if (($current_record > $first_record_on_page) && ($current_record <= $last_record_on_page))
					{	
						$t->set_var("item_tr_class", "topCategory");
						$t->set_var("item_url", $item_url);
						$t->set_var("item_name", $item_name);

						$t->set_var("src", $image_tree_top);
						$t->set_var("alt", $item_name);
						$t->set_var("width", $top_tw);
						$t->set_var("height", $top_th);
						$t->parse("category_image");

						$t->parse("item");
						$t->parse("items_rows");
						$t->set_var("category_image", "");
						$t->set_var("item", "");
					}
				} else {
					$t->set_var("item_tr_class", "topCategory");
					$t->set_var("item_url", $item_url);
					$t->set_var("item_name", $item_name);
					
					$t->set_var("src", $image_tree_top);
					$t->set_var("alt", $item_name);
					$t->set_var("width", $top_tw);
					$t->set_var("height", $top_th);
					$t->parse("category_image");
						
					$t->parse("item");
					$t->parse("items_rows");
					$t->set_var("category_image", "");
					$t->set_var("item", "");
					$show_map = true;
				}
			}
		}
		// end custom pages
		
		// begin products				
		if ($site_map_categories && $products_total_records) {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = PRODUCTS_TITLE;
			$categories[0]["parent_id"] = -1;
			
			if ($products_categories) {
				foreach ($products_categories AS $cur_category_id => $cur_category){
					$category_name = get_translation($cur_category["c.category_name"], $language_code);
					$category_name = get_currency_message($category_name, $currency);
					$parent_category_id = $cur_category["c.parent_category_id"];
					$categories[$cur_category_id]["parent_id"] = $parent_category_id;
					$categories[$cur_category_id]["category_name"] = $category_name;
					$categories[$parent_category_id]["subs"][] = $cur_category_id;
				}
			}
						
			if (sizeof($categories) > 0) {
				show_map(-1, 0, -1, "item", $first_record_on_page, $last_record_on_page, $current_record);
				$show_map = true;
			}
		} elseif ($products_total_records) {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = PRODUCTS_TITLE;
			$categories[0]["parent_id"] = -1;
			show_map(-1, 0, -1, "item", $first_record_on_page, $last_record_on_page, $current_record);
			$show_map = true;
		}
		// end products
		
		// begin articles
		$is_show = false;
		if ($articles_top_categories_ids) {
			foreach ($articles_top_categories_ids AS $article_top_category_id) {
				if ($articles_total_records[$article_top_category_id]) {					
					$is_allowed = get_setting_value($sitemap_settings, "site_map_articles_categories_" . $article_top_category_id);
					$is_allowed_articles = get_setting_value($sitemap_settings, "site_map_articles_" . $article_top_category_id);
					$categories = array();
					$params = array();
					$params["order"] = " ORDER BY c.category_order, c.category_name";
					if ($is_allowed) {
						$params["where"] = " (c.category_path LIKE '%" . $article_top_category_id . ",%' OR c.category_id=" . $article_top_category_id.") ";
					} else {
						$params["where"] = " c.category_id=" . $article_top_category_id." ";
					}
					
					$articles_categories = VA_Articles_Categories::find_all("c.category_id", array("c.category_name", "c.parent_category_id"), $params);
					if ($articles_categories) {
						foreach ($articles_categories AS $cur_category_id => $cur_category){
							$site_map_articles_categories = get_setting_value($page_settings, "site_map_articles_categories");
							$category_name = get_translation($cur_category["c.category_name"], $language_code);
							$category_name = get_currency_message($category_name, $currency);
							$parent_category_id = $cur_category["c.parent_category_id"];
							$categories[$cur_category_id]["parent_id"] = $parent_category_id;
							$categories[$cur_category_id]["category_name"] = $category_name;
							$categories[$parent_category_id]["subs"][] = $cur_category_id;
							$categories[$cur_category_id]["allowed_view"] = $is_allowed;
							$categories[$cur_category_id]["show_only_articles"] = (!$is_allowed && $is_allowed_articles);
						}
					}
					show_map(0, 0, 0, "article", $first_record_on_page, $last_record_on_page, $current_record);
					$is_show = true;
				}
			}
		}
	
		if ($is_show) {
			$show_map = true;
		}
		// end articles
	
		// begin forum
		if ($site_map_forums && $forum_total_records) {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = FORUM_TITLE;
			$categories[0]["parent_id"] = -1;
			$sql  = " SELECT c.category_id, c.category_name FROM ";
			if (isset($site_id)) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "forum_categories c ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "forum_categories_sites s ON (s.category_id=c.category_id AND c.sites_all=0)) ";
			}
			$sql .= " WHERE c.allowed_view=1";
			if (isset($site_id)) {
				$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " AND c.sites_all=1";
			}
			$sql .= " ORDER BY c.category_order, c.category_name ";
			$db->query($sql);
			while ($db->next_record()) {
				$cur_category_id = $db->f("category_id");
				$category_name = get_translation($db->f("category_name"), $language_code);
				$category_name = get_currency_message($category_name, $currency);
				$categories[$cur_category_id]["parent_id"] = 0;
				$categories[$cur_category_id]["category_name"] = $category_name;
				$categories[0]["subs"][] = $cur_category_id;
			}
				
			if (sizeof($categories) > 0) {
				show_map(-1, 0, -1, 'forum', $first_record_on_page, $last_record_on_page, $current_record);
				$show_map = true;
			}
		} elseif ($forum_total_records) {
			$categories = array();
			$categories[-1]["subs"][] = 0;
			$categories[0]["category_name"] = FORUM_TITLE;
			$categories[0]["parent_id"] = -1;
			show_map(-1, 0, -1, "forum", $first_record_on_page, $last_record_on_page, $current_record);
			$show_map = true;
		}
		// end forum
	
		// begin ads
		if ($ads_total_records) {
			if ($site_map_ad_categories) {
				$categories = array();
				$categories[-1]["subs"][] = 0;
				$categories[0]["category_name"] = ADS_TITLE;
				$categories[0]["parent_id"] = -1;
				if ($ads_categories) {
					foreach ($ads_categories AS $cur_category_id => $cur_category){
						$category_name = get_translation($cur_category["c.category_name"], $language_code);
						$category_name = get_currency_message($category_name, $currency);
						$parent_category_id = $cur_category["c.parent_category_id"];
						$categories[$cur_category_id]["parent_id"] = $parent_category_id;
						$categories[$cur_category_id]["category_name"] = $category_name;
						$categories[$parent_category_id]["subs"][] = $cur_category_id;
					}
				}
				if (sizeof($categories) > 0) {
					show_map(-1, 0, -1, 'ads', $first_record_on_page, $last_record_on_page, $current_record);
					$show_map = true;
				}
			} else {
				$categories = array();
				$categories[-1]["subs"][] = 0;
				$categories[0]["category_name"] = ADS_TITLE;
				$categories[0]["parent_id"] = -1;
				show_map(-1, 0, -1, "ads", $first_record_on_page, $last_record_on_page, $current_record);
				$show_map = true;				
			}
		}
		// end ads
	
		// begin manual	
		if ($manual_total_records) {
			if ($site_map_manual_categories) {				
				$categories = array();
				$categories[-1]["subs"][] = 0;
				$categories[0]["category_name"] = MANUALS_TITLE;
				$categories[0]["parent_id"] = -1;
				if ($manuals_categories) {
					foreach ($manuals_categories AS $cur_category_id => $cur_category){
						$category_name = get_translation($cur_category["c.category_name"], $language_code);
						$category_name = get_currency_message($category_name, $currency);
						$categories[$cur_category_id]["parent_id"] = 0;
						$categories[$cur_category_id]["category_name"] = $category_name;
						$categories[0]["subs"][] = $cur_category_id;
					}
				}				
				if (sizeof($categories) > 0) {
					show_map(-1, 0, -1, 'manual', $first_record_on_page, $last_record_on_page, $current_record);
					$show_map = true;
				}
			} else {
				$categories = array();
				$categories[-1]["subs"][] = 0;
				$categories[0]["category_name"] = MANUALS_TITLE;
				$categories[0]["parent_id"] = -1;
				show_map(-1, 0, -1, "manual", $first_record_on_page, $last_record_on_page, $current_record);
				$show_map = true;
			}		
		}
		// end manual
	
		if ($show_map) {
			$t->parse("block_body", false);
			$t->parse($block_name, true);
		}
	}
	
	// $item_type 'item', 'article', 'forum', 'ads', 'manual'
	function count_show_map_items($parent_id, $level, $top_id, $item_type, &$total_records)
	{	
		global $t, $categories, $settings, $sitemap_settings;
		global $db, $table_prefix, $language_code;
			
		$current_total_records = 0;

		$site_map_manuals           = get_setting_value($sitemap_settings, "site_map_manuals");
		$site_map_manual_articles   = get_setting_value($sitemap_settings, "site_map_manual_articles");
	
		$site_map_categories        = get_setting_value($sitemap_settings, "site_map_categories");
		$site_map_forums            = get_setting_value($sitemap_settings, "site_map_forums");
		$site_map_ad_categories     = get_setting_value($sitemap_settings, "site_map_ad_categories");
		$site_map_manual_categories = get_setting_value($sitemap_settings, "site_map_manual_categories");

		$subs = isset($categories[$parent_id]) ? $categories[$parent_id]["subs"] : array();
		for ($i = 0; $i < sizeof($subs); $i++)
		{

			$show_category_id = $subs[$i];
			if(($item_type == "item" && $site_map_categories) 
				|| ($item_type == "forum" && $site_map_forums) 
				|| ($item_type == "ads" && $site_map_ad_categories) 
				|| ($item_type == "manual" && $site_map_manual_categories) 
				|| ($item_type == "article" && !isset($categories[$show_category_id]["show_only_articles"])
			)) {
				$current_total_records++;
			}
	
			// begin items
			$all_items = get_all_items($item_type, $show_category_id);
			if ($all_items) {
				foreach ($all_items AS $item_id => $values) {
					if (!($item_type == "manual" && !$site_map_manuals && $site_map_manual_articles)) {
						$current_total_records++;
					}
					if ($item_type == "manual" && $site_map_manual_articles) 
					{
						$db_manual = new VA_SQL();
						$db_manual->DBType       = $db->DBType;
						$db_manual->DBDatabase   = $db->DBDatabase;
						$db_manual->DBUser       = $db->DBUser;
						$db_manual->DBPassword   = $db->DBPassword;
						$db_manual->DBHost       = $db->DBHost;
						$db_manual->DBPort       = $db->DBPort;
						$db_manual->DBPersistent = $db->DBPersistent;

						$sql_manual  = " SELECT article_id, article_title, parent_article_id ";
						$sql_manual .= " FROM " . $table_prefix . "manuals_articles ";
						$sql_manual .= " WHERE allowed_view=1";
						$sql_manual .= " AND manual_id=" . $item_id;
						$sql_manual .= " ORDER BY article_order, article_title ";
						$db_manual->query($sql_manual);
						while ($db_manual->next_record()) {
							$current_total_records++;
						}
					}
				}
			}
			// end items
			if (isset($categories[$show_category_id]["subs"]) && is_array($categories[$show_category_id]["subs"])) {
				count_show_map_items($show_category_id, $level + 1, $top_id, $item_type, $total_records);
			}
		}
		$total_records += $current_total_records;
	}

	// $item_type 'item', 'article', 'forum', 'ads', 'manual'
	function show_map($parent_id, $level, $top_id, $item_type, $first_record_on_page, $last_record_on_page, &$current_record)
	{
		global $t, $categories, $manual_articles, $settings, $sitemap_settings;
		global $db, $table_prefix, $language_code, $currency;
		
		$friendly_urls = 0;
		if (isset($settings['friendly_urls'])) {
			$friendly_urls = $settings['friendly_urls'];
		}
		$friendly_extension = "";
		if (isset($settings['friendly_extension'])) {
			$friendly_extension = $settings['friendly_extension'];
		}
		
		$site_map_items             = get_setting_value($sitemap_settings, "site_map_items");
		$site_map_forum_categories  = get_setting_value($sitemap_settings, "site_map_forum_categories");
		$site_map_ads               = get_setting_value($sitemap_settings, "site_map_ads");
		$site_map_manuals           = get_setting_value($sitemap_settings, "site_map_manuals");
		$site_map_manual_articles   = get_setting_value($sitemap_settings, "site_map_manual_articles");
		$records_per_page           = get_setting_value($sitemap_settings, "site_map_records_per_page", 25);

		$site_map_categories        = get_setting_value($sitemap_settings, "site_map_categories");
		$site_map_forums            = get_setting_value($sitemap_settings, "site_map_forums");
		$site_map_ad_categories     = get_setting_value($sitemap_settings, "site_map_ad_categories");
		$site_map_manual_categories = get_setting_value($sitemap_settings, "site_map_manual_categories");
		
		$space_tw = 14; $space_th = 20;
		$line_tw = 14; $line_th = 20;
		$begin_tw = 14; $begin_th = 20;
		$end_tw = 14; $end_th = 20;
		$top_tw = 14; $top_th = 20;
		
		// path and size for the default images
		$image_path = "images/" . $settings["style_name"] . "/tree_space.gif";
		if (file_exists($image_path)) {
			$image_tree_space = $image_path;
		} else {
			$image_tree_space = "images/tree_space.gif";
		}
		$space_image_size = @GetImageSize($image_tree_space);
		if (is_array($space_image_size)) {
			$space_tw = $space_image_size[0];
			$space_th = $space_image_size[1];
		} else {
			$image_tree_space = "images/tr.gif";
		}
		
		$image_path = "images/" . $settings["style_name"] . "/tree_line.gif";
		if (file_exists($image_path)) {
			$image_tree_line = $image_path;
		} else {
			$image_tree_line = "images/tree_line.gif";
		}
		$line_image_size = @GetImageSize($image_tree_line);
		if (is_array($line_image_size)) {
			$line_tw = $line_image_size[0];
			$line_th = $line_image_size[1];
		} else {
			$image_tree_line = "images/tr.gif";
		}
		
		$image_path = "images/" . $settings["style_name"] . "/tree_begin.gif";
		if (file_exists($image_path)) {
			$image_tree_begin = $image_path;
		} else {
			$image_tree_begin = "images/tree_begin.gif";
		}
		$begin_image_size = @GetImageSize($image_tree_begin);
		if (is_array($begin_image_size)) {
			$begin_tw = $begin_image_size[0];
			$begin_th = $begin_image_size[1];
		} else {
			$image_tree_begin = "images/tr.gif";
		}
		
		$image_path = "images/" . $settings["style_name"] . "/tree_end.gif";
		if (file_exists($image_path)) {
			$image_tree_end = $image_path;
		} else {
			$image_tree_end = "images/tree_end.gif";
		}
		$end_image_size = @GetImageSize($image_tree_end);
		if (is_array($end_image_size)) {
			$end_tw = $end_image_size[0];
			$end_th = $end_image_size[1];
		} else {
			$image_tree_end = "images/tr.gif";
		}

		$image_path = "images/" . $settings["style_name"] . "/tree_top.gif";
		if (file_exists($image_path)) {
			$image_tree_top = $image_path;
		} else {
			$image_tree_top = "images/tree_top.gif";
		}
		$top_image_size = @GetImageSize($image_tree_top);
		if (is_array($top_image_size)) {
			$top_tw = $top_image_size[0];
			$top_th = $top_image_size[1];
		} else {
			$image_tree_top = "images/tr.gif";
		}
	
		$subs = isset($categories[$parent_id]) ? $categories[$parent_id]["subs"] : array();
		for ($i = 0; $i < sizeof($subs); $i++)
		{
			$show_category_id = $subs[$i];
			$category_name  = $categories[$show_category_id]["category_name"];
			if ($item_type == "article") {
				$sql  = " SELECT friendly_url ";
				$sql .= " FROM " . $table_prefix . "articles_categories ";
				$sql .= " WHERE category_id=" . $db->tosql($show_category_id, INTEGER);
				$db->query($sql);
				if ($db->next_record() && strlen($db->f("friendly_url")) && $friendly_urls) {
					$item_url = $settings["site_url"] . $db->f("friendly_url") . $friendly_extension;
				} else {
					$item_url = $settings["site_url"] . "articles.php?category_id=" . $show_category_id;
				}
			}
			if ($item_type == "item") {
				$sql  = " SELECT friendly_url ";
				$sql .= " FROM " . $table_prefix . "categories ";
				$sql .= " WHERE category_id=" . $db->tosql($show_category_id, INTEGER);
				$db->query($sql);
				if ($db->next_record() && strlen($db->f("friendly_url")) && $friendly_urls) {
					$item_url = $settings["site_url"] . $db->f("friendly_url") . $friendly_extension;
				} else {
					$item_url = $settings["site_url"] . "products.php?category_id=" . $show_category_id;
				}
			}
			if ($item_type == "forum") {
				$sql  = " SELECT friendly_url ";
				$sql .= " FROM " . $table_prefix . "forum_categories ";
				$sql .= " WHERE category_id=" . $db->tosql($show_category_id, INTEGER);
				$db->query($sql);
				if ($db->next_record() && strlen($db->f("friendly_url")) && $friendly_urls) {
					$item_url = $settings["site_url"] . $db->f("friendly_url") . $friendly_extension;
				} else {
					$item_url = $settings["site_url"] . "forums.php?category_id=" . $show_category_id;
				}
			}
			if ($item_type == "ads") {
				$sql  = " SELECT friendly_url ";
				$sql .= " FROM ".$table_prefix."ads_categories ";
				$sql .= " WHERE category_id=" . $db->tosql($show_category_id, INTEGER);
				$db->query($sql);
				if ($db->next_record() && strlen($db->f("friendly_url")) && $friendly_urls) {
					$item_url = $settings["site_url"] . $db->f("friendly_url") . $friendly_extension;
				} else {
					$item_url = $settings["site_url"] . "ads.php?category_id=" . $show_category_id;
				}
			}
			if ($item_type == "manual") {
				$sql  = " SELECT friendly_url ";
				$sql .= " FROM ".$table_prefix."manuals_categories ";
				$sql .= " WHERE category_id=" . $db->tosql($show_category_id, INTEGER);
				$db->query($sql);
				if ($db->next_record() && strlen($db->f("friendly_url")) && $friendly_urls) {
					$item_url = $settings["site_url"] . $db->f("friendly_url") . $friendly_extension;
				} else {
					$item_url = $settings["site_url"] . "manuals.php?category_id=" . $show_category_id;
				}
			}
			$t->set_var("item_url", $item_url);
			$category_image = $image_tree_top;
			if ($i == sizeof($subs) - 1) {
				$categories[$show_category_id]["last"] = true;
			} else {
				$categories[$show_category_id]["last"] = false;
			}
	
			$t->set_var("category_id", $show_category_id);
	
			$tree_images = "";
			if ($parent_id != $top_id) {
				$tree_id = $parent_id; 
				if (isset($categories[$tree_id]["parent_id"])) {
					while ($categories[$tree_id]["parent_id"] != $top_id) {
						if ($categories[$tree_id]["last"]) {
							$tree_image = $image_tree_space;
							$tw = $space_tw;
							$th = $space_th;
						} else {
							$tree_image = $image_tree_line;
							$tw = $line_tw;
							$th = $line_th;
						}
						$tree_images = "<img border=\"0\" align=\"left\" src=\"$tree_image\" hspace=\"0\" width=\"$tw\" height=\"$th\">" . $tree_images;
						$tree_id = $categories[$tree_id]["parent_id"];
					}
				}
			}
			if ($level > 0) {
				if ($categories[$show_category_id]["last"]) {
					$tree_images .= "<img border=\"0\" align=\"left\" src=\"$image_tree_end\" hspace=\"0\" width=\"$end_tw\" height=\"$end_th\">";
				} else {
					$tree_images .= "<img border=\"0\" align=\"left\" src=\"$image_tree_begin\" hspace=\"0\" width=\"$begin_tw\" height=\"$begin_th\">";
				}
				$t->set_var("item_tr_class", "subCategory");
			} else {
				$t->set_var("item_tr_class", "topCategory");
			}

			$current_record++;
			if ($records_per_page) {
				if (($current_record > $first_record_on_page) && ($current_record <= $last_record_on_page)) {
					$t->set_var("item_name", $tree_images . $category_name);
					if (0 == $level) {
						$t->set_var("src", $image_tree_top);
						$t->set_var("alt", $category_name);
						$t->set_var("width", $top_tw);
						$t->set_var("height", $top_th);
						$t->parse("category_image");
					}
					$t->parse("item");
					$t->parse("items_rows");
					$t->set_var("category_image", "");
					$t->set_var("item", "");
				}
			} else {
					$t->set_var("item_name", $tree_images . $category_name);
					if (0 == $level) {
						$t->set_var("src", $image_tree_top);
						$t->set_var("alt", $category_name);
						$t->set_var("width", $top_tw);
						$t->set_var("height", $top_th);
						$t->parse("category_image");
					}
					$t->parse("item");
					$t->parse("items_rows");
					$t->set_var("category_image", "");
					$t->set_var("item", "");
			}
			
			// begin items			
			$all_items = get_all_items($item_type, $show_category_id);			
			if ($all_items) {
				$item_rows_count   = 0;
				$items_tree_images = "";
				$item_rows = count($all_items);
				
				if ($level > 0) {
					$t->set_var("item_tr_class", "subCategory");
				}
				if (isset($categories[$show_category_id]["subs"])) {
					$items_tree_images = "<img border=\"0\" align=\"left\" src=\"$image_tree_line\" hspace=\"0\" width=\"$line_tw\" height=\"$line_th\">" . $items_tree_images;
				}
				
				if (isset($categories[$show_category_id]["parent_id"])) {
					$item_parent_id = $categories[$show_category_id]["parent_id"];
					$child_id = $show_category_id;
					while ($item_parent_id != $top_id) {
						$subs_t = isset($categories[$item_parent_id]) ? $categories[$item_parent_id]["subs"] : array();
						if (isset($subs_t[sizeof($subs_t)-1])) {
							if ($child_id == $subs_t[sizeof($subs_t)-1]) {
								$last = true;
							} else {
								$last = false;
							}
						}

						if ($last) {
							$items_tree_images = "<img border=\"0\" align=\"left\" src=\"$image_tree_space\" hspace=\"0\" width=\"$space_tw\" height=\"$space_th\">" . $items_tree_images;
						} else {
							$items_tree_images = "<img border=\"0\" align=\"left\" src=\"$image_tree_line\" hspace=\"0\" width=\"$line_tw\" height=\"$line_th\">" . $items_tree_images;
						}
						$child_id = $item_parent_id;
						if (isset($categories[$item_parent_id]["parent_id"])) {
							$item_parent_id = $categories[$item_parent_id]["parent_id"];
						} else {
							$item_parent_id = $top_id;
						}
					}
				}
				
				foreach ($all_items AS $item_id => $values) {
					list($item_name, $friendly_url) = array_values($values);
					$item_rows_count++;
					$start_tree_image = "";
					$t->set_var("item_tr_class", "subCategory");

					if ($item_rows == $item_rows_count) {
						$tree_image_item = $items_tree_images . "<img border=\"0\" align=\"left\" src=\"$image_tree_space\" hspace=\"0\" width=\"$space_tw\" height=\"$space_th\"><img border=\"0\" align=\"left\" src=\"$image_tree_end\" hspace=\"0\" width=\"$end_tw\" height=\"$end_th\">";
						$start_tree_image = $items_tree_images . "<img border=\"0\" align=\"left\" src=\"$image_tree_space\" hspace=\"0\" width=\"$space_tw\" height=\"$space_th\"><img border=\"0\" align=\"left\" src=\"$image_tree_space\" hspace=\"0\" width=\"$space_tw\" height=\"$space_th\">";
					} else {
						$tree_image_item = $items_tree_images . "<img border=\"0\" align=\"left\" src=\"$image_tree_space\" hspace=\"0\" width=\"$space_tw\" height=\"$space_th\"><img border=\"0\" align=\"left\" src=\"$image_tree_begin\" hspace=\"0\" width=\"$begin_tw\" height=\"$begin_th\">";
						$start_tree_image = $items_tree_images . "<img border=\"0\" align=\"left\" src=\"$image_tree_space\" hspace=\"0\" width=\"$space_tw\" height=\"$space_th\"><img border=\"0\" align=\"left\" src=\"$image_tree_line\" hspace=\"0\" width=\"$line_tw\" height=\"$line_th\">";
					}
					if ($item_type == "article" && isset($categories[$show_category_id]["allowed_view"]) && $categories[$show_category_id]["allowed_view"]) {
						$item_url = $settings["site_url"] . "article.php?category_id=" . $show_category_id . "&article_id=" . $item_id;
					}
					if ($item_type == "article" && isset($categories[$show_category_id]["show_only_articles"]) && $categories[$show_category_id]["show_only_articles"]) {
						$item_url = $settings["site_url"] . "article.php?article_id=" . $item_id;
					}
					if ($item_type == "item" && $site_map_items) {
						$item_url = $settings["site_url"] . "product_details.php?category_id=" . $show_category_id . "&item_id=" . $item_id;
					}
					if ($item_type == "forum" && $site_map_forum_categories) {
						$item_url = $settings["site_url"] . "forum.php?forum_id=" . $item_id;
					}
					if ($item_type == "ads" && $site_map_ads) {
						$item_url = $settings["site_url"] . "ads_details.php?category_id=" . $show_category_id . "&item_id=" . $item_id;
					}
					if ($item_type == "manual" && $site_map_manuals) {
						$item_url = $settings["site_url"] . "manuals_articles.php?manual_id=" . $item_id;
					}
					if (strlen($db->f("friendly_url")) && $friendly_urls) {
						$item_url = $settings["site_url"] . $friendly_url . $friendly_extension;
					}
					$current_record++;
					if (!($item_type == "manual" && !$site_map_manuals && $site_map_manual_articles)) {
						if ($records_per_page) {							
							if (($current_record > $first_record_on_page) && ($current_record <= $last_record_on_page)) {								
								$t->set_var("item_url", $item_url);
								$item_name = get_translation($item_name, $language_code);
								$item_name = get_currency_message($item_name, $currency);
								$t->set_var("item_name", $tree_image_item . $item_name);
								$t->parse("item");
								$t->parse("items_rows");
								$t->set_var("item", "");
							}
						} else {
								$t->set_var("item_url", $item_url);
								$item_name = get_translation($db->f("item_name"), $language_code);
								$item_name = get_currency_message($item_name, $currency);
								$t->set_var("item_name", $tree_image_item . $item_name);
								$t->parse("item");
								$t->parse("items_rows");
								$t->set_var("item", "");
						}
					} elseif (!$site_map_manual_categories) {
						$start_tree_image = '';
					}
					if ($item_type == "manual" && $site_map_manual_articles) {

						$db_manual = new VA_SQL();
						$db_manual->DBType       = $db->DBType;
						$db_manual->DBDatabase   = $db->DBDatabase;
						$db_manual->DBUser       = $db->DBUser;
						$db_manual->DBPassword   = $db->DBPassword;
						$db_manual->DBHost       = $db->DBHost;
						$db_manual->DBPort       = $db->DBPort;
						$db_manual->DBPersistent = $db->DBPersistent;

						$manual_articles = array();
						$manual_articles[-1]["subs"][] = 0;
						$manual_articles[0]["category_name"] = PRODUCTS_TITLE;
						$manual_articles[0]["parent_id"] = -1;
						$sql_manual  = " SELECT article_id, article_title, parent_article_id ";
						$sql_manual .= " FROM " . $table_prefix . "manuals_articles ";
						$sql_manual .= " WHERE allowed_view=1";
						$sql_manual .= " AND manual_id=" . $item_id;
						$sql_manual .= " ORDER BY article_order, article_title ";
						$db_manual->query($sql_manual);
						while ($db_manual->next_record()) {
							$cur_article_id = $db_manual->f("article_id");
							$article_title = get_translation($db_manual->f("article_title"), $language_code);
							$article_title = get_currency_message($article_title, $currency);
							$parent_article_id = $db_manual->f("parent_article_id");
							$manual_articles[$cur_article_id]["parent_id"] = $parent_article_id;
							$manual_articles[$cur_article_id]["category_name"] = $article_title;
							$manual_articles[$parent_article_id]["subs"][] = $cur_article_id;
						}
						if (sizeof($manual_articles) > 0) {
							show_map_articles(0, 1, -1, "manual_articles", $first_record_on_page, $last_record_on_page, $current_record, $start_tree_image);
						}
					}
				}
			}
			// end items

			if (isset($categories[$show_category_id]["subs"]) && is_array($categories[$show_category_id]["subs"])) {
				show_map($show_category_id, $level + 1, $top_id, $item_type, $first_record_on_page, $last_record_on_page, $current_record);
			}
	
		}
	}

	function show_map_articles($parent_id, $level, $top_id, $item_type, $first_record_on_page, $last_record_on_page, &$current_record, $start_tree_images = "")
	{
		global $t, $manual_articles, $settings, $sitemap_settings;
		global $db, $table_prefix, $language_code, $currency;
	
		$friendly_urls = 0;
		if (isset($settings['friendly_urls'])) {
			$friendly_urls = $settings['friendly_urls'];
		}
		$friendly_extension = "";
		if (isset($settings['friendly_extension'])) {
			$friendly_extension = $settings['friendly_extension'];
		}
		
		$records_per_page = get_setting_value($sitemap_settings, "site_map_records_per_page", 25);
		
		$space_tw = 14; $space_th = 20;
		$line_tw = 14; $line_th = 20;
		$begin_tw = 14; $begin_th = 20;
		$end_tw = 14; $end_th = 20;
		$top_tw = 14; $top_th = 20;
		
		// path and size for the default images
		$image_path = "images/" . $settings["style_name"] . "/tree_space.gif";
		if (file_exists($image_path)) {
			$image_tree_space = $image_path;
		} else {
			$image_tree_space = "images/tree_space.gif";
		}
		$space_image_size = @GetImageSize($image_tree_space);
		if (is_array($space_image_size)) {
			$space_tw = $space_image_size[0];
			$space_th = $space_image_size[1];
		} else {
			$image_tree_space = "images/tr.gif";
		}
		
		$image_path = "images/" . $settings["style_name"] . "/tree_line.gif";
		if (file_exists($image_path)) {
			$image_tree_line = $image_path;
		} else {
			$image_tree_line = "images/tree_line.gif";
		}
		$line_image_size = @GetImageSize($image_tree_line);
		if (is_array($line_image_size)) {
			$line_tw = $line_image_size[0];
			$line_th = $line_image_size[1];
		} else {
			$image_tree_line = "images/tr.gif";
		}
		
		$image_path = "images/" . $settings["style_name"] . "/tree_begin.gif";
		if (file_exists($image_path)) {
			$image_tree_begin = $image_path;
		} else {
			$image_tree_begin = "images/tree_begin.gif";
		}
		$begin_image_size = @GetImageSize($image_tree_begin);
		if (is_array($begin_image_size)) {
			$begin_tw = $begin_image_size[0];
			$begin_th = $begin_image_size[1];
		} else {
			$image_tree_begin = "images/tr.gif";
		}
		
		$image_path = "images/" . $settings["style_name"] . "/tree_end.gif";
		if (file_exists($image_path)) {
			$image_tree_end = $image_path;
		} else {
			$image_tree_end = "images/tree_end.gif";
		}
		$end_image_size = @GetImageSize($image_tree_end);
		if (is_array($end_image_size)) {
			$end_tw = $end_image_size[0];
			$end_th = $end_image_size[1];
		} else {
			$image_tree_end = "images/tr.gif";
		}

		$image_path = "images/" . $settings["style_name"] . "/tree_top.gif";
		if (file_exists($image_path)) {
			$image_tree_top = $image_path;
		} else {
			$image_tree_top = "images/tree_top.gif";
		}
		$top_image_size = @GetImageSize($image_tree_top);
		if (is_array($top_image_size)) {
			$top_tw = $top_image_size[0];
			$top_th = $top_image_size[1];
		} else {
			$image_tree_top = "images/tr.gif";
		}
		$subs = isset($manual_articles[$parent_id]["subs"]) ? $manual_articles[$parent_id]["subs"] : array();
		for ($i = 0; $i < sizeof($subs); $i++)
		{
			$show_category_id = $subs[$i];
			$category_name  = $manual_articles[$show_category_id]["category_name"];
			if ($item_type == "manual_articles") {

				$db_manual = new VA_SQL();
				$db_manual->DBType       = $db->DBType;
				$db_manual->DBDatabase   = $db->DBDatabase;
				$db_manual->DBUser       = $db->DBUser;
				$db_manual->DBPassword   = $db->DBPassword;
				$db_manual->DBHost       = $db->DBHost;
				$db_manual->DBPort       = $db->DBPort;
				$db_manual->DBPersistent = $db->DBPersistent;

				$sql_manual  = " SELECT friendly_url ";
				$sql_manual .= " FROM " . $table_prefix . "manuals_articles ";
				$sql_manual .= " WHERE article_id=" . $db->tosql($show_category_id, INTEGER);
				$db_manual->query($sql_manual);
				if ($db_manual->next_record() && strlen($db_manual->f("friendly_url")) && $friendly_urls) {
					$item_url = $settings["site_url"] . $db_manual->f("friendly_url") . $friendly_extension;
				} else {
					$item_url = $settings["site_url"] . "manuals_article_details.php?article_id=" . $show_category_id;
				}
			}
			$t->set_var("item_url", $item_url);
			$category_image = $image_tree_top;
			if ($i == sizeof($subs) - 1) {
				$manual_articles[$show_category_id]["last"] = true;
			} else {
				$manual_articles[$show_category_id]["last"] = false;
			}
	
			$t->set_var("category_id", $show_category_id);
	
			$tree_images = "";
			if ($parent_id != $top_id) {
				$tree_id = $parent_id; 
				if (isset($manual_articles[$tree_id]["parent_id"])) {
					while ($manual_articles[$tree_id]["parent_id"] != $top_id) {
						if ($manual_articles[$tree_id]["last"]) {
							$tree_image = $image_tree_space;
							$tw = $space_tw;
							$th = $space_th;
						} else {
							$tree_image = $image_tree_line;
							$tw = $line_tw;
							$th = $line_th;
						}
						$tree_images = "<img border=\"0\" align=\"left\" src=\"$tree_image\" hspace=\"0\" width=\"$tw\" height=\"$th\">" . $tree_images;
						$tree_id = $manual_articles[$tree_id]["parent_id"];
					}
				}
			}
			$tree_images = $start_tree_images.$tree_images;
			if ($level > 0) {
				if ($manual_articles[$show_category_id]["last"]) {
					$tree_images .= "<img border=\"0\" align=\"left\" src=\"$image_tree_end\" hspace=\"0\" width=\"$end_tw\" height=\"$end_th\">";
				} else {
					$tree_images .= "<img border=\"0\" align=\"left\" src=\"$image_tree_begin\" hspace=\"0\" width=\"$begin_tw\" height=\"$begin_th\">";
				}
				$t->set_var("item_tr_class", "subCategory");
			} else {
				$t->set_var("item_tr_class", "topCategory");
			}
	
			$current_record++;
			if ($records_per_page) {
				if (($current_record > $first_record_on_page) && ($current_record <= $last_record_on_page)) {
					$t->set_var("item_name", $tree_images . $category_name);
					if (0 == $level) {
						$t->set_var("src", $image_tree_top);
						$t->set_var("alt", $category_name);
						$t->set_var("width", $top_tw);
						$t->set_var("height", $top_th);
						$t->parse("category_image");
					}
					$t->parse("item");
					$t->parse("items_rows");
					$t->set_var("category_image", "");
					$t->set_var("item", "");
				}
			} else {
					$t->set_var("item_name", $tree_images . $category_name);
					if (0 == $level) {
						$t->set_var("src", $image_tree_top);
						$t->set_var("alt", $category_name);
						$t->set_var("width", $top_tw);
						$t->set_var("height", $top_th);
						$t->parse("category_image");
					}
					$t->parse("item");
					$t->parse("items_rows");
					$t->set_var("category_image", "");
					$t->set_var("item", "");
			}

			if (isset($manual_articles[$show_category_id]["subs"]) && is_array($manual_articles[$show_category_id]["subs"])) {
				show_map_articles($show_category_id, $level + 1, $top_id, $item_type, $first_record_on_page, $last_record_on_page, $current_record, $start_tree_images);
			}
	
		}
	}
	
	function get_all_items($item_type, $show_category_id) {
		global $table_prefix, $db, $categories, $sitemap_settings;
		
		$site_map_items             = get_setting_value($sitemap_settings, "site_map_items");
		$site_map_forum_categories  = get_setting_value($sitemap_settings, "site_map_forum_categories");
		$site_map_ads               = get_setting_value($sitemap_settings, "site_map_ads");
		$site_map_manuals           = get_setting_value($sitemap_settings, "site_map_manuals");
		$site_map_manual_articles   = get_setting_value($sitemap_settings, "site_map_manual_articles");
	
		$site_map_categories        = get_setting_value($sitemap_settings, "site_map_categories");
		$site_map_forums            = get_setting_value($sitemap_settings, "site_map_forums");
		$site_map_ad_categories     = get_setting_value($sitemap_settings, "site_map_ad_categories");
		$site_map_manual_categories = get_setting_value($sitemap_settings, "site_map_manual_categories");
		
		$all_items = array();
		if ($item_type == "item" && $site_map_items) {
			$params = array();
			$params["order"] = " ORDER BY i.item_order, i.item_name";
			if ($site_map_categories) {
				$params["brackets"] = "(";
				$params["join"]  = " LEFT JOIN " . $table_prefix . "items_categories ic ON ic.item_id=i.item_id)";
				$params["where"] = " ic.category_id=" . $db->tosql($show_category_id, INTEGER);
			}
			$all_items = VA_Products::find_all("i.item_id", 
				array("i.item_name", "i.friendly_url"),
				$params
			);
		}
				
		if ($item_type == "article") {	
			if (isset($categories[$show_category_id]["show_only_articles"]) && $categories[$show_category_id]["show_only_articles"]) {
				$params = array();
				$params["order"] = " ORDER BY a.article_order, a.article_id";
				$params["where"] = " c.category_path LIKE '%," . $db->tosql($show_category_id, INTEGER) . ",%'";
				$all_items = VA_Articles::find_all("a.article_id",
					array("a.article_title", "a.friendly_url"),
					$params
				);
			} elseif (isset($categories[$show_category_id]["allowed_view"]) && $categories[$show_category_id]["allowed_view"]) {
				$params = array();
				$params["order"] = " ORDER BY a.article_order, a.article_id";
				$params["where"] = " ac.category_id =" . $db->tosql($show_category_id, INTEGER);
				$all_items = VA_Articles::find_all("a.article_id", 
					array("a.article_title", "a.friendly_url"),
					$params
				);
			}
		}			
			
		if ($item_type == "forum" && $site_map_forum_categories) {
			$params = array();
			$params["order"] = " ORDER BY fl.forum_order, fl.forum_name";
			if ($site_map_forums) {
				$params["where"] = " fl.category_id=" . $db->tosql($show_category_id, INTEGER);
			}
			$all_items = VA_Forums::find_all("fl.forum_id", 
				array("fl.forum_name", "fl.friendly_url"),
				$params
			);
		}
			
		if ($item_type == "ads" && $site_map_ads) {
			$params = array();
			$params["order"] = " ORDER BY i.item_order, i.item_title";
			if ($site_map_ad_categories) {
				$params["where"] = " c.category_id=" . $db->tosql($show_category_id, INTEGER);
			}
			$all_items = VA_Ads::find_all("i.item_id", 
				array("i.item_title", "i.friendly_url"),
				$params
			);
		}
		
		if ($item_type == "manual" && $site_map_manuals) {
			$params = array();
			$params["order"] = " ORDER BY ml.manual_order, ml.manual_title";
			if ($site_map_manual_categories) {
				$params["where"] = " c.category_id=" . $db->tosql($show_category_id, INTEGER);
			}
			$all_items = VA_Manuals::find_all("ml.manual_id", 
				array("ml.manual_title", "ml.friendly_url"),
				$params
			);
		}
		
		return $all_items;
		
	}

?>