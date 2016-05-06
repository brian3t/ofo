<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_reviews.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path."includes/sorter.php");
	include_once($root_folder_path."includes/navigator.php");
	include_once($root_folder_path."includes/record.php");
	include_once($root_folder_path."includes/reviews_functions.php");
	include_once($root_folder_path."messages/".$language_code."/reviews_messages.php");

	include_once("./admin_common.php");

	check_admin_security("articles_reviews");

	// begin delete selected reviews
	$operation = get_param("operation");

	// update and remove operations
	$reviews_ids = get_param("reviews_ids");
	$articles_ids = get_param("articles_ids");
	$status_id = get_param("status_id");
	if (strlen($operation)) {
		if ($reviews_ids) {
			if ($operation == "remove_reviews") {
				$sql  = " DELETE FROM " . $table_prefix . "articles_reviews ";
				$sql .= " WHERE review_id IN (" . $db->tosql($reviews_ids, INTEGERS_LIST) . ")";
				$db->query($sql);
			} else if ($operation == "update_status" && strlen($status_id)) {
				$sql  = " UPDATE " . $table_prefix . "articles_reviews ";
				$sql .= " SET approved=" . $db->tosql($status_id, INTEGER);
				$sql .= " WHERE review_id IN (" . $db->tosql($reviews_ids, INTEGERS_LIST) . ")";
				$db->query($sql);
			}
		}
		if ($articles_ids) {
			update_article_rating($articles_ids);
		}
	}
	// end operations

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_articles_reviews.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_articles_reviews_href", "admin_articles_reviews.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");

	// prepare dates for stats
	$current_date = va_time();
	$cyear = $current_date[YEAR]; $cmonth = $current_date[MONTH]; $cday = $current_date[DAY];
	$today_ts = mktime (0, 0, 0, $cmonth, $cday, $cyear);
	$tomorrow_ts = mktime (0, 0, 0, $cmonth, $cday + 1, $cyear);
	$yesterday_ts = mktime (0, 0, 0, $cmonth, $cday - 1, $cyear);
	$week_ts = mktime (0, 0, 0, $cmonth, $cday - 6, $cyear);
	$month_ts = mktime (0, 0, 0, $cmonth, 1, $cyear);
	$last_month_ts = mktime (0, 0, 0, $cmonth - 1, 1, $cyear);
	$last_month_days = date("t", $last_month_ts);
	$last_month_end = mktime (0, 0, 0, $cmonth - 1, $last_month_days, $cyear);

	$t->set_var("date_edit_format", join("", $date_edit_format));

	// show reviews statistics
	$reviews_types = array(
		array("1", IS_APPROVED_MSG),
		array("0", NOT_APPROVED_MSG),
	);

	$stats = array(
		array("title" => TODAY_MSG, "date_start" => $today_ts, "date_end" => $today_ts),
		array("title" => YESTERDAY_MSG, "date_start" => $yesterday_ts, "date_end" => $yesterday_ts),
		array("title" => LAST_SEVEN_DAYS_MSG, "date_start" => $week_ts, "date_end" => $today_ts),
		array("title" => THIS_MONTH_MSG, "date_start" => $month_ts, "date_end" => $today_ts),
		array("title" => LAST_MONTH_MSG, "date_start" => $last_month_ts, "date_end" => $last_month_end),
	);

	$reviews_total_online = 0; 
	// get reviews stats
	for($i = 0; $i < sizeof($reviews_types); $i++) {
		// set general constants
		$type_id = $reviews_types[$i][0];
		$type_name = $reviews_types[$i][1];

		$t->set_var("type_id",   $type_id);
		$t->set_var("type_name", $type_name);

		// get registration stats
		$t->set_var("stats_periods", "");
		foreach($stats as $key => $stat_info) {
			$start_date = $stat_info["date_start"];
			$end_date = va_time($stat_info["date_end"]);
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews ";
			$sql .= " WHERE approved=" . $db->tosql($type_id, INTEGER);
			$sql .= " AND date_added>=" . $db->tosql($start_date, DATE);
			$sql .= " AND date_added<" . $db->tosql($day_after_end, DATE);
			$period_reviews = get_db_value($sql);
			if (isset($stats[$key]["total"])) {
				$stats[$key]["total"] += $period_reviews;
			} else {
				$stats[$key]["total"] = $period_reviews;
			}
			if($period_reviews > 0) {
				$period_reviews = "<a href=\"admin_articles_reviews.php?s_ap=".$type_id."&s_sd=".va_date($date_edit_format, $start_date)."&s_ed=".va_date($date_edit_format, $end_date)."\"><b>" . $period_reviews."</b></a>";
			}
			$t->set_var("period_reviews", $period_reviews);
			$t->parse("stats_periods", true);
		}

		$t->parse("types_stats", true);
	}

	foreach($stats as $key => $stat_info) {
		$t->set_var("start_date", va_date($date_edit_format, $stat_info["date_start"]));
		$t->set_var("end_date", va_date($date_edit_format, $stat_info["date_end"]));
		$t->set_var("stat_title", $stat_info["title"]);
		$t->set_var("period_total", $stat_info["total"]);
		$t->parse("stats_titles", true);
		$t->parse("stats_totals", true);
	}
	// end statistics

	// search form
	$approved_options = array(array("", ALL_MSG), array("1", IS_APPROVED_MSG), array("0", NOT_APPROVED_MSG));
	$rating_options = 
		array( 
			array("", ""), array(1, BAD_MSG), array(2, POOR_MSG), 
			array(3, AVERAGE_MSG), array(4, GOOD_MSG), array(5, EXCELLENT_MSG),
			);
	$recommended_options = 
		array( 
			array("", ALL_MSG), array(1, YES_MSG), array(-1, NO_MSG), 
			);

	$r = new VA_Record($table_prefix . "articles_reviews");
	$r->add_textbox("s_ne", TEXT);
	$r->change_property("s_ne", TRIM, true);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->change_property("s_sd", TRIM, true);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("s_ed", TRIM, true);
	$r->add_select("s_rt", INTEGER, $rating_options);
	$r->add_select("s_rc", INTEGER, $recommended_options);
	$r->add_select("s_ap", TEXT, $approved_options);
	$r->get_form_parameters();
	$r->validate();
	$approved_options = array(array("", ""), array("1", IS_APPROVED_MSG), array("0", NOT_APPROVED_MSG));
	$r->add_select("status_id", TEXT, $approved_options);
	$r->set_form_parameters();
	// end search form

	// build where condition
	$where = "";
	if (!$r->errors)
	{
		if (!$r->is_empty("s_ne")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_ne_sql = $db->tosql($r->get_value("s_ne"), TEXT, false);
			$where .= " (r.user_email LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR r.user_name LIKE '%" . $s_ne_sql . "%')";
		}

		if (!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " r.date_added>=" . $db->tosql($r->get_value("s_sd"), DATE);
		}

		if (!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " r.date_added<" . $db->tosql($day_after_end, DATE);
		}

		if (!$r->is_empty("s_rt")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " r.rating=" . $db->tosql($r->get_value("s_rt"), INTEGER);
		}

		if (!$r->is_empty("s_rc")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " r.recommended=" . $db->tosql($r->get_value("s_rc"), INTEGER);
		}

		if (!$r->is_empty("s_ap")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " r.approved=" . $db->tosql($r->get_value("s_ap"), INTEGER);
		}
	}
	$where_sql = ""; $where_and_sql = "";
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
		$where_and_sql = " AND " . $where;
	}

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_articles_reviews.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(3, "desc");
	$s->set_sorter(ID_MSG, "sorter_review_id", "1", "review_id");
	$s->set_sorter(USER_NAME_MSG, "sorter_user_name", "2", "review_name");
	$s->set_sorter(SUMMARY_MSG, "sorter_summary", "2", "summary");
	$s->set_sorter(RATING_MSG, "sorter_rating", "3", "approved");
	$s->set_sorter(REVIEW_ADDED_MSG, "sorter_date_added", "3", "date_added");
	$s->set_sorter(APPROVED_QST, "sorter_approved", "4", "approved");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_articles_reviews.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	
	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews r ". $where_sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "articles_reviews r " . $where_sql . $s->order_by);
	$review_index = 0;
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");

		$admin_article_review_url = new VA_URL("admin_article_review.php", false);
		$admin_article_review_url->add_parameter("s_ne", REQUEST, "s_ne");
		$admin_article_review_url->add_parameter("s_sd", REQUEST, "s_sd");
		$admin_article_review_url->add_parameter("s_ed", REQUEST, "s_ed");
		$admin_article_review_url->add_parameter("s_rt", REQUEST, "s_rt");
		$admin_article_review_url->add_parameter("s_rc", REQUEST, "s_rc");
		$admin_article_review_url->add_parameter("s_ap", REQUEST, "s_ap");
		$admin_article_review_url->add_parameter("page", REQUEST, "page");
		$admin_article_review_url->add_parameter("sort_ord", REQUEST, "sort_ord");
		$admin_article_review_url->add_parameter("sort_dir", REQUEST, "sort_dir");
		
		do
		{
			$review_index++;
			$t->set_var("review_index", $review_index);
			$review_id = $db->f("review_id");
			$article_id = $db->f("article_id");

			$admin_article_review_url->add_parameter("review_id", CONSTANT, $review_id);
			$t->set_var("admin_article_review_url", $admin_article_review_url->get_url());

			//$row_style = "rowWarn"; // to be used for IP addresses from black list
			$row_style = ($review_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			$t->set_var("review_id", $review_id);
			$t->set_var("article_id", $article_id);
			$t->set_var("user_name", htmlspecialchars($db->f("user_name")));
			$t->set_var("summary", htmlspecialchars($db->f("summary")));
			$t->set_var("rating", get_array_value($db->f("rating"), $rating_options));
			$date_added = $db->f("date_added", DATETIME);
			$t->set_var("date_added", va_date($date_show_format, $date_added));
			$approved = $db->f("approved");
			if ($approved) {
				$approved = "<font color=\"blue\"><b>" . YES_MSG . "</b></font>";
			} else  {
				$approved = "<font color=\"silver\">" . NO_MSG . "</font>";
			} 
			$t->set_var("approved", $approved);
			$t->parse("records", true);
		} while($db->next_record());
		$t->set_var("reviews_number", $review_index);
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->set_var("s_rt_search", $r->get_value("s_rt"));
	$t->set_var("s_rc_search", $r->get_value("s_rc"));
	$t->set_var("s_ap_search", $r->get_value("s_ap"));

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");
?>
