<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_users_report.php                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_users_stats");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_users_report.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_users_report_href", "admin_support_users_report.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_admins.php");
	$s->set_sorter(ID_MSG, "sorter_admin_id", "1", "admin_id");
	$s->set_sorter(USER_NAME_MSG, "sorter_admin_name", "2", "admin_name");
	$s->set_sorter(LOGIN_BUTTON, "sorter_admin_login", "3", "admin_name");
	$s->set_sorter(PRIVILEGE_MSG, "sorter_admin_privilege", "4", "admin_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_admins.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// prepare dates for stats
	$periods = array(array("", ""), array("1", TODAY_MSG), array("2", YESTERDAY_MSG), array("3", LAST_7DAYS_MSG), array("4", THIS_MONTH_MSG), array("5", LAST_MONTH_MSG), array("6", THIS_QUARTER_MSG), array("7", THIS_YEAR_MSG));

	$current_date = va_time();
	$cyear = $current_date[YEAR]; 
	$cmonth = $current_date[MONTH]; 
	$cday = $current_date[DAY]; 
	$today_ts = mktime (0, 0, 0, $cmonth, $cday, $cyear);
	$tomorrow_ts = mktime (0, 0, 0, $cmonth, $cday + 1, $cyear);
	$yesterday_ts = mktime (0, 0, 0, $cmonth, $cday - 1, $cyear);
	$week_ts = mktime (0, 0, 0, $cmonth, $cday - 6, $cyear);
	$month_ts = mktime (0, 0, 0, $cmonth, 1, $cyear);
	$last_month_start_ts = mktime (0, 0, 0, $cmonth - 1, 1, $cyear);
	$last_month_end_ts = mktime (0, 0, 0, $cmonth, 0, $cyear);
	$quarter_ts = mktime (0, 0, 0, intval(($cmonth - 1) / 3) * 3 + 1, 1, $cyear);
	$year_ts = mktime (0, 0, 0, 1, 1, $cyear);
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

	$r = new VA_Record("");
	$r->add_hidden("s_form", INTEGER);
	$r->add_select("s_tp", INTEGER, $periods);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->get_form_parameters();
	$r->validate();

	if (!($r->get_value("s_form")) && $r->is_empty("s_sd") && $r->is_empty("s_ed")) {
		$r->set_value("s_tp", 1);
		$r->set_value("s_sd", va_time($today_ts));
		$r->set_value("s_ed", va_time($today_ts));
	}
	$r->set_form_parameters();
	
	$dates = array(); //array of start and end dates of time periods
	$where = ""; 
	if(!$r->errors) {

		if(!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " m.date_added >= " . $db->tosql($r->get_value("s_sd"), DATE);
		}

		if(!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " m.date_added < " . $db->tosql($day_after_end, DATE);
		}
	}

	if(strlen($where)) {
		$where = " AND " . $where;
	}

	if (strlen(get_param("filter")) && !$r->errors && !($r->is_empty("s_sd")) && !($r->is_empty("s_ed")))
	{
	// -- generating statuses list
		$sql  = " SELECT status_id, status_name FROM ";
		$sql .=   $table_prefix . "support_statuses " ;
		$sql .= " ORDER BY status_id ASC" ;
		$db->query($sql);
		if($db->next_record()) {
			do {
				$t->set_var("status_name", $db->f("status_name")); 
				$status_ar[$db->f("status_id")] = $db->f("status_name") ;
				$t->parse("statuses",true);
			} while($db->next_record());
			$t->set_var("status_name", "<font style='color:blue;'>".TOTAL_MSG."</font>"); 
			$t->parse("statuses",true);
			$t->set_var("status_name", "<font style='color:darkblue;'>".UNIQUE_MSG."</font>"); 
			$t->parse("statuses",true);
		}
		else {
			$t->set_var("statuses","");
			$err = EMPTY_STATUS_DATA_MSG;
		}
	 
	// -- counting messages 
		$stats=array(); $user_ar=array(); $users=array(); $total_replies=0; $unique =array();

		$sql  = " SELECT a.admin_id as user_id,a.admin_name as user_name, ";
		$sql .= " m.support_id, m.support_status_id as status_id ";
		$sql .= "	FROM " . $table_prefix . "admins a, " . $table_prefix . "support_messages m ";
		$sql .= " WHERE a.admin_id=m.admin_id_assign_by ";
		$sql .= 	$where;
		$sql .= " ORDER BY a.admin_name" ;
		$db->query($sql);

		if ($db->next_record())
		{ 
			$last_user = $db->Record["user_name"];
			$last_user_id = $db->Record["user_id"];

			do	{
				$user = $db->Record["user_name"];
				$user_id = $db->Record["user_id"];  
				$users[$user] = $user_id;
				$support_id = $db->Record["support_id"];  
				$status_id = $db->Record["status_id"];
				if ($last_user != $user)	{		//-- new record
				  $stats["total"] = $total_replies; 
					$stats["unique"] = sizeof($unique);
					$total_replies = 0;
					$user_ar[$last_user] = $stats;
					$last_user = $user;
					$last_user_id = $user_id;
					$stats = array();
					$unique = array();
				}

				$total_replies += 1; 
				$unique[$support_id] = 1;
				if (isset($stats[$status_id])) $stats[$status_id] += 1;
				else $stats[$status_id] = 1;				

			}	while ($db->next_record());               

			$stats["total"] = $total_replies; 
			$stats["unique"] = sizeof($unique);
			$user_ar[$user] = $stats;
		}
	}       

	if (isset($user_ar) && sizeof($user_ar)>0) {
		$row_number =0;
		foreach ($user_ar as $user_name=>$stats) {
		  $row_number +=1;
			$messages ="";
 			$t->set_var("user_name",$user_name);
			$user_id = $users[$user_name];
			foreach ($status_ar as $status_id=>$status_name){
			  if (isset($stats[$status_id])) {
			  	$stat_messages = $stats[$status_id];
			  }
			  else  {
					$stat_messages ="0";
				}
			  if ($stat_messages)  $messages .= "<td align=center><b>".$stat_messages."</b></td>";		
			  else $messages .= "<td align=center>0</td>";		
			}
			$messages .= "<td align=center style='color: blue;'><b>".$stats["total"]."</b></td>";
			$messages .= "<td align=center style='color: darkblue;'><b>".$stats["unique"]."</b></td>";

			$t->set_var("messages",$messages);
			$t->set_var("user_id",$user_id); 			

			$row_style = ($row_number % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			$t->parse("records",true);
		}
		$t->set_var("no_records","");
		$t->parse("stats",false);
  }	else if (isset($user_ar) && sizeof($user_ar)==0) {
		$t->parse("no_records",false);
		$t->set_var("records","");
		$t->parse("stats",false); 	
	} 

	$t->set_var("admin_href", "admin.php");
  $t->pparse("main");

?>
