<?php

function site_search_results($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $date_show_format;
	global $language_code;
	global $currency;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_site_search_results.html");
	$t->set_var("ss_items", "");

	$site_url = get_setting_value($settings, "site_url", "");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$records_per_page = get_setting_value($page_settings, "ss_recs", 10);

	$user_id = get_session("session_user_id");		
	$user_type_id = get_session("session_user_type_id");
	$q = get_param("q");

	$tables = array(
		"items" => array("ID" => "item_id", "TITLE" => "item_name", "SHORT" => "short_description", "FULL" => "full_description", "URL" => "product_details.php?item_id="), 
		"articles" => array("ID" => "article_id", "TITLE" => "article_title", "SHORT" => "short_description", "FULL" => "full_description", "URL" => "article.php?article_id="), 
		"pages" => array("ID" => "page_code", "TITLE" => "page_title", "SHORT" => "", "FULL" => "page_body", "URL" => "page.php?page="), 
		"manuals" => array("ID" => "article_id", "TITLE" => "article_title", "SHORT" => "short_description", "FULL" => "full_description", "URL" => "manuals_article_details.php?article_id="), 
	);

	// count the total found records for all tables
	$total_records = 0;
	foreach ($tables as $table_name => $table_info) {
		if ($table_name == "items") {
			$sql  = " SELECT COUNT(*) ";
			$sql .= " FROM " . $table_prefix . "items i ";
			$sql_where  = " WHERE i.is_showing=1 AND i.is_approved=1 ";
			$sql_where .= " AND ((i.hide_out_of_stock=1 AND i.stock_level > 0) OR i.hide_out_of_stock=0 OR i.hide_out_of_stock IS NULL)";
			$sql_where .= " AND (i.language_code IS NULL OR i.language_code='' OR i.language_code=" . $db->tosql($language_code, TEXT) . ")";
			if (strlen($q)) {
				$search_values = split(" ", $q);
				for ($si = 0; $si < sizeof($search_values); $si++) {
					$sql_where .= " AND ( ";
					$sql_where .= " i.item_name LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR i.item_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR i.full_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR i.short_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " ) ";
				}
			}
		} else if ($table_name == "articles") {
			$sql  = " SELECT COUNT(*) ";
			$sql .= " FROM  (" . $table_prefix . "articles a ";
			$sql .= " INNER JOIN " . $table_prefix . "articles_statuses st ON a.status_id=st.status_id) ";
			$sql_where  = " WHERE st.allowed_view=1 ";
			if (strlen($q)) {
				$search_values = split(" ", $q);
				for ($si = 0; $si < sizeof($search_values); $si++) {
					$sql_where .= " AND ( ";
					$sql_where .= " a.article_title LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR a.short_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR a.full_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " ) ";
				}
			}
		} else if ($table_name == "pages") {
			$sql  = " SELECT COUNT(*) ";
			$sql .= " FROM " . $table_prefix . "pages p ";
			$sql_where  = " WHERE p.is_showing=1 ";
			if (strlen($q)) {
				$search_values = split(" ", $q);
				for ($si = 0; $si < sizeof($search_values); $si++) {
					$sql_where .= " AND ( ";
					$sql_where .= " p.page_title LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR p.page_body LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR p.page_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " ) ";
				}
			}
		} else if ($table_name == "manuals") {
			$sql  = " SELECT COUNT(*) ";
			$sql .= " FROM " . $table_prefix . "manuals_articles ma ";
			$sql_where  = " WHERE ma.allowed_view=1 ";
			if (strlen($q)) {
				$search_values = split(" ", $q);
				for ($si = 0; $si < sizeof($search_values); $si++) {
					$sql_where .= " AND ( ";
					$sql_where .= " ma.article_title LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR ma.short_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " OR ma.full_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
					$sql_where .= " ) ";
				}
			}
		}
		$table_records = get_db_value($sql . $sql_where);
		$tables[$table_name]["TOTAL"] = $table_records;
		$tables[$table_name]["WHERE"] = $sql_where;
		$total_records += $table_records;
	}


	if ($total_records > 0) {
		$sv_regexp = "";
		$sv = split(" ", $q);
		if (is_array($sv)) {
			for ($si = 0; $si < sizeof($sv); $si++) {
				if (strlen($sv_regexp)) { $sv_regexp .= "|"; }
				$sv_regexp .= preg_quote($sv[$si], "/");
			}
		}

		// set up variables for navigator
		$n = new VA_Navigator($settings["templates_dir"], "navigator.html", "site_search.php");
  
		$nav_type = 1; $nav_pages = 10; 
		$nav_first_last = true; $nav_prev_next = true; $inactive_links = false;
		$n->set_parameters($nav_first_last, $nav_prev_next, $inactive_links);
		$page_number = $n->set_navigator("navigator", "page", 2, $nav_pages, $records_per_page, $total_records, false);
		$first_record = (($page_number - 1) * $records_per_page) + 1;
		$last_record = $page_number * $records_per_page;

		$shown_records = 0; $tables_total = 0;
		foreach ($tables as $table_name => $table_info) {
			if ($table_name == "items") {
				$sql  = " SELECT i.item_id, i.friendly_url, i.item_name, i.short_description, i.full_description ";
				$sql .= " FROM " . $table_prefix . "items i ";
				$order_by = " ORDER BY i.item_order, i.item_id ";
			} else if ($table_name == "articles") {
				$sql  = " SELECT a.article_id, a.friendly_url, a.article_title, a.short_description, a.full_description ";
				$sql .= " FROM  (" . $table_prefix . "articles a ";
				$sql .= " INNER JOIN " . $table_prefix . "articles_statuses st ON a.status_id=st.status_id) ";
				$order_by = " ORDER BY a.article_order, a.article_id ";
			} else if ($table_name == "pages") {
				$sql  = " SELECT p.page_code, p.friendly_url, p.page_title, p.page_body ";
				$sql .= " FROM " . $table_prefix . "pages p ";
				$order_by = " ORDER BY p.page_id ";
			} else if ($table_name == "manuals") {
				$sql  = " SELECT ma.article_id, ma.friendly_url, ma.article_title, ma.short_description, ma.full_description ";
				$sql .= " FROM " . $table_prefix . "manuals_articles ma ";
				$order_by = " ORDER BY ma.article_order, ma.article_id ";
			}
			// check the correct offset based on tables total and already shown records
			$table_records = $table_info["TOTAL"];
			$prev_tables_total = $tables_total;
			$tables_total += $table_records;
			if ($first_record <= $tables_total && $shown_records < $records_per_page) {
				$run_query = true;
				if ($page_number == 1 || $shown_records > 0) {
					$db->Offset = 0;
				} else {
					$db->Offset = ($page_number - 1) * $records_per_page - $prev_tables_total;
				}
				$db->RecordsPerPage = $records_per_page - $shown_records;
			} else {
				$run_query = false;
			}

			if ($run_query) {
				$db->query($sql . $table_info["WHERE"] . $order_by);
				while ($db->next_record())
				{
					$shown_records++;
					$id = $db->f($table_info["ID"]);
					$title = get_translation($db->f($table_info["TITLE"]));
					$title = htmlspecialchars($title);
					$friendly_url = $db->f("friendly_url");
					$desc = "";
					if ($table_info["SHORT"]) {
						$desc = strip_tags($db->f($table_info["SHORT"]));	
					}
					if (!$desc && $table_info["FULL"]) {
						$desc = strip_tags($db->f($table_info["FULL"]));	
					}
					$desc = get_translation($desc);
					$desc = get_currency_message($desc, $currency);
					if (strlen($desc) > 300) {
						$desc = substr($desc, 0, 300) . "...";
					}
					if ($friendly_urls && $friendly_url) {
						$details_url = $friendly_url . $friendly_extension;
					} else {
						$details_url = $table_info["URL"] . $id;
					}
					$ss_url = $site_url . $details_url;
		  
					if (strlen($sv_regexp)) {
						$title = preg_replace ("/(" . $sv_regexp . ")/i", "<b>\\1</b>", $title);
						$desc = preg_replace ("/(" . $sv_regexp . ")/i", "<b>\\1</b>", $desc);
						$ss_url = preg_replace ("/(" . $sv_regexp . ")/i", "<b>\\1</b>", $ss_url);
					}
		  
					$t->set_var("ss_title", $title);
					$t->set_var("ss_desciption", $desc);
					$t->set_var("ss_url", $ss_url);
					$t->set_var("details_url", $details_url);
					
					$t->parse("ss_rows");
				} 
			}

		}
			
		$t->parse("ss_items", false);

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	} else if ($q) {
		$t->set_var("query", htmlspecialchars($q));
		$t->parse("no_results", false);

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>