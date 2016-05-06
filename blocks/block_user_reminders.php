<?php

	check_user_session();

	$monthes= array( 
		array(1, JANUARY),	
		array(2, FEBRUARY),
		array(3, MARCH),
		array(4, APRIL),
		array(5, MAY),
		array(6, JUNE),
		array(7, JULY),
		array(8, AUGUST),
		array(9, SEPTEMBER),
		array(10,OCTOBER),
		array(11,NOVEMBER),
		array(12,DECEMBER)
	);
	
	$weekdays_values = array(
		array(1, MONDAY_SHORT),
		array(2, TUESDAY_SHORT),
		array(4, WEDNESDAY_SHORT),
		array(8, THURSDAY_SHORT),
		array(16, FRIDAY_SHORT),
		array(32, SATURDAY_SHORT),
		array(64, SUNDAY_SHORT)
	);

	$user_id=get_session("session_user_id");
	
	$t->set_file("block_body","block_user_reminders.html");
	$t->set_var("user_reminders_href",  "user_reminders.php");
	$t->set_var("user_reminder_href",   "user_reminder.php");
	$t->set_var("user_home_href", "user_home.php");

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", "user_reminders.php");
	$s->set_default_sorting(1, "desc");
	$s->set_sorter("ID", "sorter_id", "1", "reminder_id");
	$s->set_sorter("Title", "sorter_title", "2", "reminder_title");
	$s->set_sorter("Start Date", "sorter_start", "3", "start_date");
	$s->set_sorter("End Date", "sorter_end", "3", "end_date");
	$s->set_sorter("Reminder date", "sorter_reminder_date", "3", "reminder_date");
	$s->set_sorter("Weekdays", "sorter_weekdays", "3", "reminder_weekdays");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", "user_reminders.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "reminders WHERE user_id=" . $db->tosql($user_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
		
	$sql = "SELECT *,CONCAT(reminder_day,'-',reminder_month,'-',reminder_year) as reminder_date FROM  " . $table_prefix . "reminders WHERE user_id=".$db->tosql($user_id,INTEGER);
	$db->query($sql . $s->order_by);
	if ($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do 
		{
			$reminder_id = $db->f("reminder_id");
			$reminder_title = $db->f("reminder_title");
			$reminder_notes = $db->f("reminder_notes");
			$start_date = $db->f("start_date", DATETIME);
			$end_date = $db->f("end_date", DATETIME);
			$reminder_date = "";
			$reminder_year = $db->f("reminder_year");	
			$reminder_month = $db->f("reminder_month");	
			$reminder_day = $db->f("reminder_day");	
			if ($reminder_day) {
				$reminder_date .= $reminder_day;
			}
			if ($reminder_month) {
				$reminder_date .= " " . get_array_value($reminder_month, $months);
			}
			if ($reminder_date && $reminder_year) {
				$reminder_date .= ", " . $reminder_year;
			}

			$reminder_weekdays = $db->f("reminder_weekdays");	
			
			$weekdays_text = "";
			for ($i=1; $i < 65; $i=$i*2) {
				if ($reminder_weekdays&$i) {
					if ($weekdays_text) { $weekdays_text .= ", "; }
					$weekdays_text .= get_array_value($i, $weekdays_values);
				}
			}
			$t->set_var("reminder_id",$reminder_id);
			$t->set_var("reminder_title",$reminder_title);
			$t->set_var("reminder_notes",$reminder_notes);
			if (sizeof($start_date) > 1)
				$t->set_var("start_date",va_date($date_show_format,$start_date));
			else 
				$t->set_var("start_date","");
			if (sizeof($end_date) > 1)	
				$t->set_var("end_date",va_date($date_show_format,$end_date));
			else 
				$t->set_var("end_date","");
			$t->set_var("reminder_year",$reminder_year);
			if ($reminder_month>=1) {
				$t->set_var("reminder_month",$monthes[$reminder_month-1][1]);
			} else {
				$t->set_var("reminder_month","");
			}
			$t->set_var("reminder_day", $reminder_day);
			$t->set_var("reminder_date", $reminder_date);
			$t->set_var("reminder_weekdays", $weekdays_text);
			$t->parse("records",true);
		
		}
		while($db->next_record());
		
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>