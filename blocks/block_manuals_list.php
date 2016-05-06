<?php
include_once("./includes/manuals_functions.php");

	function manuals_list($block_name) {
		global $t, $db, $table_prefix, $settings, $datetime_show_format;
		global $html_title, $meta_keywords, $meta_description;
		
		$category_id = get_param("category_id");
	
		$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
		$friendly_extension = get_setting_value($settings, "friendly_extension", "");		
		
		$manual_categories_href = get_custom_friendly_url("manuals.php") . "?category_id=";
		$manual_articles_href   = get_custom_friendly_url("manuals_articles.php") . "?manual_id=";
		
		$t->set_file("block_body", "block_manuals_list.html");

		$where = "";
		if ($category_id) {
			$where = "c.category_id=" . $db->tosql($category_id, INTEGER); 
		}
		$manuals_ids = VA_Manuals::find_all_ids($where, VIEW_CATEGORIES_PERM);		
				
		if ($category_id) {
			$sql  = " SELECT category_name, meta_title, meta_keywords, meta_description ";
			$sql .= " FROM " . $table_prefix . "manuals_categories ";
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
				
			$db->query($sql);
			if ($db->next_record()) {
				$category_name = get_translation($db->f("category_name"));
				
				$meta_title = get_translation($db->f("meta_title"));
				if ($meta_title) {
					$html_title = $meta_title;
				} elseif ($category_name) {
					$html_title =  MANUALS_TITLE . " | " . $category_name;
				}
				$meta_keywords = get_translation($db->f("meta_keywords"));
				$meta_description = get_translation($db->f("meta_description"));				
			}
		}
		
		if ($manuals_ids) {
			$allowed_manuals_ids = VA_Manuals::find_all_ids($where, VIEW_CATEGORIES_ITEMS_PERM);
			
			$sql  = " SELECT ml.manual_id, ml.manual_title, ml.short_description, ml.friendly_url, mc.short_description, mc.category_id, mc.category_name, mc.friendly_url AS cat_friendly_url ";
			$sql .= " FROM (" . $table_prefix . "manuals_categories mc ";
			$sql .= " LEFT JOIN " . $table_prefix . "manuals_list ml ON mc.category_id = ml.category_id)";
			$sql .= " WHERE manual_id IN (" . $db->tosql($manuals_ids, INTEGERS_LIST) . ")";
			$sql .= " ORDER BY mc.category_order, ml.manual_order";
	
			$db->query($sql);
			$prev_category_id = 0;
			if ($db->next_record()) {
				do {
					$category_id = $db->f("category_id");
					
					if ($prev_category_id != $category_id) {					
						if ($prev_category_id != 0) {
							$t->parse("categories", true);
							$t->set_var("manuals", "");
						}
						
						$cat_friendly_url = $db->f("cat_friendly_url");
						$t->set_var("cat_name", $db->f("category_name"));
						if ($friendly_urls && $cat_friendly_url != "") {
							$category_href = $cat_friendly_url . $friendly_extension;
						} else {
							$category_href = $manual_categories_href . $category_id;
						}
						$t->set_var("category_href", $category_href);
					}										
					
					$manual_id = $db->f("manual_id");
					
					// Parse manual
					$t->set_var("manual_title", $db->f("manual_title"));
					$t->set_var("short_description", $db->f("short_description"));
					$friendly_url = $db->f("friendly_url");
					
					if ($friendly_urls && $friendly_url != "") {
						$manual_href = $friendly_url . $friendly_extension;
					} else {
						$manual_href = $manual_articles_href . $manual_id;
					}
					
					if (!$allowed_manuals_ids || !in_array($manual_id, $allowed_manuals_ids)) {
						$t->set_var("restricted_class", " restrictedItem");
						$t->sparse("restricted_image", false);
					} else {
						$t->set_var("restricted_class", "");
						$t->set_var("restricted_image", "");
					}
					
					$t->set_var("manual_href", $manual_href);
					$t->parse("manuals", true);
					
					$prev_category_id = $category_id;
				} while ($db->next_record());
				
				$t->parse("categories", true);
			}
			$t->set_var("no_manuals", "");
		} else {
			$t->parse("no_manuals", false);
			$t->set_var("manuals", "");
		}
		
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
?>