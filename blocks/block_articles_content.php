<?php

function articles_content($block_name, $top_id, $current_category_id, $current_category_name, $articles_order_column, $articles_order_direction, $page_friendly_url, $page_friendly_params)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$search_string = get_param("search_string");
	if (strlen($search_string)) {
		return;
	}

	$friendly_urls      = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$records_per_page   = get_setting_value($page_settings, "a_list_recs_" . $top_id, 10);
	$current_page       = get_param("page");
	if (!$current_page) 
		$current_page = 1;
	
	
	if ($friendly_urls && $page_friendly_url) {
		$articles_page = $page_friendly_url . $friendly_extension . "?";
	} else {
		$articles_page = "articles.php?category_id=" . $current_category_id . "&";
	}
	
	if (strlen($articles_order_column)) {
		$articles_order = " ORDER BY a." . $articles_order_column . " " . $articles_order_direction;
	} else {
		$articles_order_column = "article_order";
		$articles_order = " ORDER BY a.article_order ";
	}

	$t->set_file("block_body", "block_content.html");
	$t->set_var("category_name", $current_category_name);
	$t->set_var("CONTENT_TITLE", CONTENT_TITLE);

	$sql  = " SELECT a.article_id, a.article_title ";
	$sql .= " FROM " . $table_prefix . "articles a ";
	$sql .= " , " . $table_prefix . "articles_statuses st ";
	$sql .= " , " . $table_prefix . "articles_assigned aa ";
	$sql .= " WHERE a.status_id=st.status_id AND a.article_id=aa.article_id ";
	$sql .= " AND aa.category_id=" . $db->tosql($current_category_id, INTEGER);
	$sql .= " AND st.allowed_view=1 ";
	$sql .= " GROUP BY a.article_id, a.article_title ";
	if ($articles_order_column && $articles_order_column != "article_title") {
		$sql .= ", a." . $articles_order_column;
		$sql .= $articles_order;
	}

	$db->query($sql);
	if($db->next_record())
	{
		$item_number = 0;
		$page_number = 1;
		do
		{
			$item_number++;
			$article_id = $db->f("article_id");
			$a_name = "#a".$article_id;
			if (!($current_page == $page_number)) {
				$a_name = $articles_page . "page=" . $page_number . $a_name;
			} else {
				$a_name = get_request_uri() . $a_name;
			}
			$t->set_var("a_name", $a_name);
			$t->set_var("content_item_name", get_translation($db->f("article_title")));

			$t->parse("content_items", true);
			if (($item_number % $records_per_page) == 0) {
				$page_number++;
			}
		} while ($db->next_record());              	
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>