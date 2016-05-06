<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_visits_report.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
                                   	       

	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("visits_report"); 

	$date_show_format_custom = array("D", " ", "MMM", " ", "YYYY");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_visits_report.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// prepare list values 
	$groupby = array(array("1", YEAR_MSG), array("2", MONTH_MSG), array("3", WEEK_MSG), array("4", DAY_MSG));
	$periods = array(array("", ""), array("1", TODAY_MSG), array("2", YESTERDAY_MSG), array("3", LAST_7DAYS_MSG), array("4", THIS_MONTH_MSG), array("5", LAST_MONTH_MSG), array("6", THIS_QUARTER_MSG), array("7", THIS_YEAR_MSG));

	$search_engines = array();
	$sql = " SELECT engine_id,engine_name FROM " . $table_prefix . "search_engines ";
	$db->query($sql);
	while ($db->next_record()) {
		$search_engines[$db->f("engine_id")] = $db->f("engine_name");
	}

	$max_rec = get_param("max_rec");
	if (!$max_rec) { $max_rec = 20; }
	$referer_url = get_param("referer_url");
	$referer_keywords = get_param("referer_keywords");
	$referer_affiliate = get_param("referer_affiliate");
	$keywords_engine = get_param("keywords_engine");
	$referer_title = REFERRING_URLS_MSG;
	if ($referer_url) {
		$referer_title .= HOST_MSG . ":" .  $referer_url;
	}
	if ($referer_keywords) {
		$referer_title .= KEYWORDS_MSG . ":" .  $referer_keywords;
		if (isset($search_engines[$keywords_engine])) {
			$referer_title .= SEARCH_ENGINE_MSG . ":" . $search_engines[$keywords_engine];
		}
	}
	if ($referer_affiliate) {
		$referer_title .= AFFILIATE_MSG . ":" .  $referer_affiliate;
	}
	$keywords_title = SEARCH_ENGINE_KEYWORDS_MSG;
	if (isset($search_engines[$keywords_engine])) {
		$keywords_title = KEYWORDS_FOR_MSG . ":" . $search_engines[$keywords_engine];
	}

	$report_types = array(
		"user_agent" => USER_AGENTS_MSG,
		"referer_host" => REFERRING_HOSTS_MSG, "referer" => $referer_title,
		"referer_engine_id" => SEARCH_ENGINES_VISITS_MSG,
		"keywords" => $keywords_title,
		"request_page" => START_PAGES_MSG,
		"affiliate_code" => AFFILIATES_VISITS_MSG,
		"robot_engine_id" => ROBOTS_VISITS_MSG); 
	$monthes = array(JANUARY,FEBRUARY,MARCH,APRIL,MAY,JUNE,JULY,AUGUST,SEPTEMBER,OCTOBER,NOVEMBER,DECEMBER);

	// prepare dates for stats
	$current_date = va_time();
	$cyear = $current_date[YEAR]; 

	$cmonth = $current_date[MONTH]; 
	$cday = $current_date[DAY]; 
	$today_ts = mktime(0, 0, 0, $cmonth, $cday, $cyear);
	$tomorrow_ts = mktime(0, 0, 0, $cmonth, $cday + 1, $cyear);
	$yesterday_ts = mktime(0, 0, 0, $cmonth, $cday - 1, $cyear);
	$week_ts = mktime(0, 0, 0, $cmonth, $cday - 6, $cyear);
	$month_ts = mktime(0, 0, 0, $cmonth, 1, $cyear);
	$last_month_start_ts = mktime(0, 0, 0, $cmonth - 1, 1, $cyear);
	$last_month_end_ts = mktime(0, 0, 0, $cmonth, 0, $cyear);
	$quarter_ts = mktime(0, 0, 0, intval(($cmonth - 1) / 3) * 3 + 1, 1, $cyear);
	$year_ts = mktime(0, 0, 0, 1, 1, $cyear);
	$today_date = va_date($date_edit_format, $today_ts);
	$tomorrow_date = va_date($date_edit_format, $tomorrow_ts);
	$yesterday_date = va_date($date_edit_format, $yesterday_ts);
	$week_start_date = va_date($date_edit_format, $week_ts);
	$month_start_date = va_date($date_edit_format, $month_ts);
	$last_month_start_date = va_date($date_edit_format, $last_month_start_ts);
	$last_month_end_date = va_date($date_edit_format, $last_month_end_ts);
	$quarter_start_date = va_date($date_edit_format, $quarter_ts);
	$year_start_date = va_date($date_edit_format, $year_ts);

	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("today_date", $today_date);
	$t->set_var("yesterday_date", $yesterday_date);
	$t->set_var("week_start_date", $week_start_date);
	$t->set_var("month_start_date", $month_start_date);
	$t->set_var("last_month_start_date", $last_month_start_date);
	$t->set_var("last_month_end_date", $last_month_end_date);
	$t->set_var("quarter_start_date", $quarter_start_date);
	$t->set_var("year_start_date", $year_start_date);

	if ($sitelist) {
		$sites = get_db_values("SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ", array(array("", "")));
	}
	
	$r = new VA_Record("");
	$r->add_hidden("s_form", INTEGER);
	$r->add_select("s_gr", INTEGER, $groupby);
	$r->add_select("s_tp", INTEGER, $periods);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	if($sitelist) {
		$r->add_select("s_sti", TEXT, $sites);
	}
	foreach($report_types as $rep => $rep_title) {
 		$r->add_checkbox($rep, INTEGER);
	}
                      
	$r->get_form_parameters();
	$r->validate();

	if (!($r->get_value("s_form"))) {
		$r->set_value("s_gr", 2);
		if ($r->is_empty("s_sd") && $r->is_empty("s_ed")) {
			$r->set_value("s_tp", 7);
			$r->set_value("s_sd", va_time($year_ts));
			$r->set_value("s_ed", va_time($today_ts));
		}
	}
	$r->set_form_parameters();
	$s_gr = $r->get_value("s_gr");
	if (!strlen(get_param("filter")) && $r->get_value("s_form")) {
		$t->set_var("search_results", "");
		$t->pparse("main");
		exit;
	}
	$where = ""; 

	$admin_report_other_url = new VA_URL("admin_visits_report.php", true, array("max_rec"));

	$admin_report_url = new VA_URL("admin_visits_report.php", false);
	$admin_report_url->add_parameter("s_form", REQUEST, "s_form");
	$admin_report_url->add_parameter("s_gr", REQUEST, "s_gr");
	$admin_report_url->add_parameter("s_tp", REQUEST, "s_tp");
	$admin_report_url->add_parameter("s_sd", REQUEST, "s_sd");
	$admin_report_url->add_parameter("s_ed", REQUEST, "s_ed");
	$admin_report_url->add_parameter("filter", REQUEST, "filter");
	if(!$r->errors) {

		if(!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " date_added >= " . $db->tosql($r->get_value("s_sd"), DATE);
		}                                         
		if(!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " date_added < " . $db->tosql($day_after_end, DATE);
		}

		if(!$r->is_empty("s_sti")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_sti = $r->get_value("s_sti");
			$where .= " site_id=" . $db->tosql($r->get_value("s_sti"), INTEGER);
		} 
	}

	if(strlen($where)) {
		$where = " WHERE " . $where;
	}

	foreach ($report_types as $report_type => $report_title) {
		$admin_report_url->remove_parameter("keywords");
		$admin_report_url->remove_parameter("keywords_engine");
		$admin_report_url->remove_parameter("referer");
		$admin_report_url->remove_parameter("referer_url");
		$admin_report_url->remove_parameter("referer_keywords");
		$admin_report_url->remove_parameter("keywords_engine");
		$admin_report_url->remove_parameter("referer_affiliate");

		if ($r->get_value($report_type) > 0) {
		  $t->set_var("report_type", $report_title);

			if ($report_type == "user_agent") {
				$report_where = " user_agent<>'' AND user_agent IS NOT NULL ";
			} else if ($report_type == "referer_host") {
				$report_where = " referer_host<>'' AND referer_host IS NOT NULL ";
			} else if ($report_type == "referer") {
				$report_where = " referer<>'' AND referer IS NOT NULL ";
				if ($referer_url) {
					$report_where .= " AND referer_host=" . $db->tosql($referer_url, TEXT);
				}
				if ($referer_keywords) {
					$report_where .= " AND keywords=" . $db->tosql($referer_keywords, TEXT);
				}
				if ($referer_affiliate) {
					$report_where .= " AND affiliate_code=" . $db->tosql($referer_affiliate, TEXT);
				}
				if ($keywords_engine) {
					$report_where .= " AND referer_engine_id=" . $db->tosql($keywords_engine, INTEGER);
				}
			} else if ($report_type == "referer_engine_id") {
				$report_where = " referer_engine_id<>0 AND referer_engine_id IS NOT NULL ";
			} else if ($report_type == "robot_engine_id") {
				$report_where = " robot_engine_id<>0 AND robot_engine_id IS NOT NULL ";
			} else if ($report_type == "keywords") {
				$report_where = " keywords<>'' AND keywords IS NOT NULL ";
				if ($keywords_engine) {
					$report_where .= " AND referer_engine_id=" . $db->tosql($keywords_engine, INTEGER);
				}
			} else if ($report_type == "request_page") {
				$report_where = " robot_engine_id=0 AND request_page<>'' AND request_page IS NOT NULL ";
			} else if ($report_type == "affiliate_code") {
				$report_where = " affiliate_code<>'' AND affiliate_code IS NOT NULL ";
			} 
			if ($where) {
				$report_where = " AND " . $report_where;
			} else {
				$report_where = " WHERE " . $report_where;
			}

			if ($s_gr == 1) {
				$group_fields = " year_added ";
			} else if ($s_gr == 2) {
				$group_fields = " year_added, month_added ";
			} else if ($s_gr == 3) {
				$group_fields = " week_added ";
			} else {
				$group_fields = " year_added, month_added, day_added ";
			}
  
			$sql  = " SELECT COUNT(visit_id) AS quantity, ";
			$sql .= $report_type . " AS stat_title, ";
			$sql .= $group_fields;
			$sql .= " FROM " . $table_prefix . "tracking_visits ";
			$sql .= $where . $report_where;
			$sql .= " GROUP BY " . $report_type . ", " . $group_fields;
 	 	 	$sql .= " ORDER BY " . $group_fields; 
			if ($db_type == "access") {
				$sql .= ", COUNT(visit_id) DESC ";
			} else {
				$sql .= ", quantity DESC ";
			}
		  
			$db->query($sql);
			if ($db->next_record()) {
				$t->set_var("no_records", "");
				$stat_index = 0; $total_quantity = 0; $other_quantity = 0;	$date_period = "";
			  do {
					$qty = $db->f("quantity");
					$stat_title = $db->f("stat_title");       	  
					$year_added = intval($db->f("year_added")); 			                              
					$month_added = intval($db->f("month_added"));
					$week_added = intval($db->f("week_added")); 		                              
					$day_added = intval($db->f("day_added"));  
		  
					if ($s_gr == 1) {
						$current_period = $year_added;
					} else if ($s_gr == 2) {
						$current_period = $year_added.", ".$monthes[$month_added-1];
					} else if ($s_gr == 3) {
						$current_period = substr($week_added,0,4).", ".substr($week_added,4);
					} else {
						$stat_date = mktime(0,0,0, $month_added, $day_added, $year_added); 
						$current_period = va_date($date_show_format, $stat_date);
					}
					if ($date_period == "") { $date_period = $current_period; }
		  
 	 	 			if ($date_period != $current_period) {				
						$t->set_var("date_group_by", $date_period);
						$t->set_var("total_quantity", $total_quantity);
						if ($other_quantity) {
							$admin_report_other_url->add_parameter("max_rec", CONSTANT, ($max_rec + 20));
							$t->set_var("admin_report_other_url", $admin_report_other_url->get_url());
							$t->set_var("other_quantity", $other_quantity);
							$t->sparse("other_block", false);           
						} else {
							$t->set_var("other_block", "");           
						}
		  
						$t->parse("date_block", true);           
						$t->set_var("records", "");
		  
						$date_period = $current_period;
						$total_quantity = 0; $other_quantity = 0;
						$stat_index = 0;
					} 
		  
				  $t->set_var("quantity", $qty);
  
				  if (($report_type == "referer_engine_id") || ($report_type == "robot_engine_id")) {
						$engine_id = $stat_title;
						if (isset($search_engines[$engine_id])) {
							$stat_title = $search_engines[$engine_id];
						}
					  if ($report_type == "referer_engine_id") {
							$admin_report_url->add_parameter("keywords", CONSTANT, 1);
							$admin_report_url->add_parameter("keywords_engine", CONSTANT, $engine_id);
							$stat_title .= " &nbsp; <a href=\"" . $admin_report_url->get_url() . "\">".KEYWORDS_DETAILS_MSG."</a>";
						}
				  } else if ($report_type == "referer_host") {
						$admin_report_url->add_parameter("referer", CONSTANT, 1);
						$admin_report_url->add_parameter("referer_url", CONSTANT, $stat_title);
						$stat_title .= " &nbsp; <a href=\"" . $admin_report_url->get_url() . "\">".URLS_DETAILS."</a>";
				  } else if ($report_type == "keywords") {
						$admin_report_url->add_parameter("referer", CONSTANT, 1);
						$admin_report_url->add_parameter("referer_keywords", CONSTANT, $stat_title);
						$admin_report_url->add_parameter("keywords_engine", REQUEST, "keywords_engine");
						$stat_title .= " &nbsp; <a href=\"" . $admin_report_url->get_url() . "\">".URLS_DETAILS."</a>";
				  } else if ($report_type == "affiliate_code") {
						$admin_report_url->add_parameter("referer", CONSTANT, 1);
						$admin_report_url->add_parameter("referer_affiliate", CONSTANT, $stat_title);
						$stat_title .= " &nbsp; <a href=\"" . $admin_report_url->get_url() . "\">".REFERRING_URLS_MSG."</a>";
					}

					$t->set_var("stat_title", $stat_title); 
					$row_style = ($stat_index % 2 == 0) ? "row2" : "row1";
					$t->set_var("row_style", $row_style);
			  	$total_quantity += $qty;			
		  
					if ($stat_index <= $max_rec) {
						$t->parse("records", true);
						$stat_index++;				
					} else {
						$other_quantity += $qty;
					}
				} while ($db->next_record());
				// parse last period
				$t->set_var("date_group_by", $date_period);
				$t->set_var("total_quantity", $total_quantity);
				if ($other_quantity) {
					$admin_report_other_url->add_parameter("max_rec", CONSTANT, ($max_rec + 20));
					$t->set_var("admin_report_other_url", $admin_report_other_url->get_url());
					$t->set_var("other_quantity", $other_quantity);
					$t->sparse("other_block", false);           
				} else {
					$t->set_var("other_block", "");           
				}

				$t->parse("date_block", true);           
				
 	 		}	else	{
				$t->set_var("records", "");
				$t->parse("no_records", false);
			}
			$t->parse("search_results", true);
			$t->set_var("date_block", "");
			$t->set_var("records", ""); 
		}
	}

	if($sitelist) {
		$t->parse('sitelist');		
	}
	
	$t->pparse("main");

?>