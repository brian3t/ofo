<?php

	check_user_security("add_ad");

	$t->set_file("block_body","block_user_ads.html");
	$t->set_var("user_ads_href",  get_custom_friendly_url("user_ads.php"));
	$t->set_var("user_ad_href",   get_custom_friendly_url("user_ad.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_ads.php"));
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ID_MSG, "sorter_id", "1", "i.item_id");
	$s->set_sorter(TITLE_MSG, "sorter_title", "2", "i.item_title");
	$s->set_sorter(PRICE_MSG, "sorter_price", "3", "i.price");
	$s->set_sorter(STATUS_MSG, "sorter_status", "4", "i.is_approved");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_ads.php"));

	// set up variables for navigator
	$sql  = " SELECT i.item_id FROM ((";
	if(isset($site_id)) {
			$sql .= "(";
	}
	$sql .= $table_prefix . "ads_items i";
	$sql .= " LEFT JOIN " . $table_prefix . "ads_assigned ac ON ac.item_id = i.item_id)";
	$sql .= " LEFT JOIN " . $table_prefix . "ads_categories c ON ac.category_id = c.category_id)";
	if(isset($site_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_sites s ON s.category_id=c.category_id)";
	}
	$sql .= " WHERE i.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	if(isset($site_id)) {
		$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
	} else {
		$sql .= " AND c.sites_all=1";
	}
	$sql .= " GROUP BY i.item_id ";
	$db->query($sql);
	$total_records = 0;
	while($db->next_record()) {
		$total_records++;
	}

	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT i.item_id, i.item_title, i.price, i.is_approved, i.date_start, i.date_end ";
	$sql .= " FROM ((";
	if(isset($site_id)) {
			$sql .= "(";
	}
	$sql .= $table_prefix . "ads_items i";
	$sql .= " LEFT JOIN " . $table_prefix . "ads_assigned ac ON ac.item_id = i.item_id)";
	$sql .= " LEFT JOIN " . $table_prefix . "ads_categories c ON ac.category_id = c.category_id)";
	if(isset($site_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_sites s ON s.category_id=c.category_id)";
	}
	$sql .= " WHERE i.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	if(isset($site_id)) {
		$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
	} else {
		$sql .= " AND c.sites_all=1";
	}
	if ($db_type == "access" || $db_type == "db2" || $db_type == "postgre") {
		$sql .= " GROUP BY i.item_id, i.item_title, i.price, i.is_approved, i.date_start, i.date_end  ";
	} else {
		$sql .= " GROUP BY i.item_id, i.item_title, i.price, i.is_approved, i.date_start, i.date_end  ";
	}
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$item_id = $db->f("item_id");
			$item_title = get_translation($db->f("item_title"));
			$price = $db->f("price");

			$t->set_var("item_id", $item_id);
			$t->set_var("item_title", $item_title);
			$t->set_var("price", currency_format($price));

			$is_approved = $db->f("is_approved");
			$date_start = $db->f("date_start", DATETIME);
			$date_end = $db->f("date_end", DATETIME);
			$date_start_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY], $date_start[YEAR]);
			$date_end_ts = mktime(0,0,0, $date_end[MONTH], $date_end[DAY], $date_end[YEAR]);
			$date_now_ts = va_timestamp();
			if ($is_approved != 1) {
				$status = "<font color=red>".AD_NOT_APPROVED_MSG."</font>";
			} else if ($date_now_ts >= $date_start_ts && $date_now_ts < $date_end_ts) {
				$status = "<font color=blue>".AD_RUNNING_MSG."</font>";
			} else if ($date_start_ts == $date_end_ts) {
				$status = "<font color=silver>".AD_CLOSED_MSG."</font>";
			} else if ($date_now_ts >= $date_end_ts) {
				$status = "<font color=silver>".EXPIRED_MSG."</font>";
			}	else if ($date_now_ts < $date_start_ts) {
				$status = AD_NOT_STARTED_MSG;
			}
			$t->set_var("status", $status);


			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$type_index = 0;
	$sql  = " SELECT type_id, type_name ";
	$sql .= " FROM " . $table_prefix . "ads_types ";
	$db->query($sql);
	while ($db->next_record()) {
		$type_index++;
		$type_id = $db->f("type_id");
		$type_name = get_translation($db->f("type_name"));
		$delimiter = ($type_index > 1) ? " | " : "";

		$t->set_var("type_id", $type_id);
		$t->set_var("type_name", $type_name);
		$t->set_var("delimiter", $delimiter);

		$t->parse("ads_types", true);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>