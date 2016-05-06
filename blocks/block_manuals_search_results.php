<?php
include_once("./includes/manuals_functions.php");

function manuals_search_results($block_name) {
	global $t, $db, $table_prefix;
	global $settings;

	// Global friendly url settings
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$search_string = get_param("manuals_search");
	$search_type   = get_param("manuals_search_type");
	$manual_id     = intval(get_param("manual_id"));
	$t->set_file("block_body", "block_manuals_search_results.html");		
	
	$manuals_search_href  = "manuals_search.php";
	$article_details_href = "manuals_article_details.php";
	
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $manuals_search_href);
	$n->set_parameters(false, true, false);
	
	if ($search_string != "") {
		
		$where = "";
		if ($manual_id) {
			$where = "ml.manual_id=" . $db->tosql($manual_id, INTEGER);
		}
		$manuals_ids = VA_Manuals::find_all_ids($where, VIEW_CATEGORIES_ITEMS_PERM);		
		if (!$manuals_ids) return;
				
		$allowed_manuals_ids = VA_Manuals::find_all_ids($where, VIEW_ITEMS_PERM);
		
		$sql_where = " WHERE manual_id IN (" . $db->tosql($manuals_ids, INTEGERS_LIST) . ")";
		$search_strings = explode(" ", $search_string);
		$ic = count($search_strings);
		if (!($ic > 1)) {
			$search_type = 0;
		}
		if ($search_type == 1 || $search_type == 2) {
			$search_string_sql = "'%".$db->tosql($search_strings[0], TEXT, false) ."%'";
			$sql_where .= " AND ( article_title LIKE " . $search_string_sql ;
			$sql_where .= " OR short_description LIKE " . $search_string_sql ;
			$sql_where .= " OR full_description LIKE " . $search_string_sql ;
			$sql_where .= " ) ";
			for ($i = 1; $i < $ic; $i++) {	
				if ($search_type == 1 ) {
					$sql_where .= " OR ";
				} else {
					$sql_where .= " AND ";
				}
				$search_string_sql = "'%".$db->tosql($search_strings[$i], TEXT, false) ."%'";
				$sql_where .= " ( article_title LIKE " . $search_string_sql ;
				$sql_where .= " OR short_description LIKE " . $search_string_sql ;
				$sql_where .= " OR full_description LIKE " . $search_string_sql ;
				$sql_where .= " ) ";					
			}
			$sql_where .= " ) ";
		} else {
			$search_string_sql = "'%".$db->tosql($search_string, TEXT, false) ."%'";
			$sql_where .= " AND ( article_title LIKE " . $search_string_sql ;
			$sql_where .= " OR short_description LIKE " . $search_string_sql ;
			$sql_where .= " OR full_description LIKE " . $search_string_sql ;
			$sql_where .= " ) ";
		}
		
		$sql  = " SELECT COUNT(*) ";
		$sql .= " FROM " . $table_prefix . "manuals_articles ";
		$sql .= $sql_where;
		$counter = get_db_value($sql);
		
		$pass_parameters["manuals_search"] = $search_string;
		if ($manual_id) {
			$pass_parameters["manual_id"] = $manual_id;
		}
		$total_records = $db->f(0);
		$records_per_page = 25;
		$pages_number = 10;
		$page_number = $n->set_navigator("navigator", "page", CENTERED, $pages_number, $records_per_page, $total_records, false, $pass_parameters);
		
		$sql  = " SELECT article_id, article_title, manual_id, short_description, full_description, friendly_url, section_number";
		$sql .= " FROM " . $table_prefix . "manuals_articles ";
		$sql .= $sql_where;

		function bi($matches) { 
		  	return str_replace($matches[1], "<b>" . $matches[1] . "</b>", $matches[0]);
		}

		$db->RecordsPerPage = $records_per_page;
		$db->PageNumber = $page_number;
		$db->query($sql);
		if ($db->next_record()) {
			//$counter = 0;
			do {
				//$counter++;
				$article_id     = $db->f("article_id");
				$manual_id      = $db->f("manual_id");
				$section_number = $db->f("section_number");
				$article_title  = $db->f("article_title");
				$short_description = $db->f("short_description");
				$full_description  = $db->f("full_description");
				$t->set_var("article_title", $article_title);				
				
				if ($search_type == 1 || $search_type == 2) {
					for ($i = 0; $i < $ic; $i++) {
						$short_description = preg_replace_callback("/[^a-zA-Z]($search_strings[$i])[^a-zA-Z]/i", "bi", $short_description);
					}
				} else {
					$short_description = preg_replace_callback("/[^a-zA-Z]($search_string)[^a-zA-Z]/i", "bi", $short_description);
				}
				
				$t->set_var("short_description", $short_description);				
				$friendly_url = $db->f("friendly_url");

				if ($friendly_urls && $friendly_url != "") {
					$article_href = $friendly_url . $friendly_extension;
				} else {
					$article_href = $article_details_href."?article_id=".$article_id;
				}
				
				if (!$allowed_manuals_ids || !in_array($manual_id, $allowed_manuals_ids)) {
					$t->set_var("restricted_class", " restrictedItem");
					$t->sparse("restricted_image", false);
				} else {
					$t->set_var("restricted_class", "");
					$t->set_var("restricted_image", "");
				}

				//$article_href .= "&highlight=".$search_string;
				$t->set_var("section_number", $section_number);
				$t->set_var("article_href", $article_href);
				//$t->set_var("counter", ($page_number - 1)*$records_per_page + $counter);
				$t->parse("record", true);
			} while ($db->next_record());
			$found_message = str_replace("{results_number}", $counter, MANUALS_SEARCH_RESULTS_INFO);
			$found_message = str_replace("{search_string}", htmlspecialchars($search_string), $found_message);

			$t->set_var("found_message", $found_message);
			$t->set_var("search_string", htmlspecialchars($search_string));
			$t->parse("results_number_block", false);
		} else {
			$not_found_message = str_replace("{search_string}", htmlspecialchars($search_string), MANUALS_NOT_FOUND_ANYTHING);

			$t->set_var("not_found_message", $not_found_message);
			$t->set_var("results_number_block", "");
			$t->set_var("search_string", htmlspecialchars($search_string));
			$t->parse("no_results", false);
		}
	}
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);
}
?> 