<?php

	check_user_session();

	// get user type settings
	$eol = get_eol();
	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);
	$current_date = va_time();

	$t->set_file("block_body","block_user_reminder.html");
	$t->set_var("site_url",        $settings["site_url"]);
	$t->set_var("user_home_href",  "user_home.php");
	$t->set_var("user_reminders_href",  "user_reminders.php");
	$t->set_var("user_reminder_href",   "user_reminder.php");
	$t->set_var("user_upload_href","user_upload.php");
	$t->set_var("user_select_href","user_select.php");
	$t->set_var("date_edit_format",join("", $date_edit_format));
	$t->set_var("date_format_msg", $date_format_msg);

	$reminder_id= get_param("reminder_id");
	$month = get_param("month");
	$weekdays = array(MONDAY, TUESDAY, WEDNESDAY, THURSDAY, FRIDAY, SATURDAY, SUNDAY);

	$weekdays_values = array(
		array(1, MONDAY),
		array(2, TUESDAY),
		array(4, WEDNESDAY),
		array(8, THURSDAY),
		array(16, FRIDAY),
		array(32, SATURDAY),
		array(64, SUNDAY)
	);
	
	$monthes= array( 
		array(0, ""),	
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
	
	$t->set_var("reminder_id",$reminder_id);
	$t->set_var("weekdays_block","");
	$t->set_var("month_block","");
	
	$r = new VA_Record($table_prefix . "reminders");

	// set up html form parameters
	$r->add_where("reminder_id", INTEGER);
	$r->change_property("reminder_id", USE_IN_INSERT, true);
	$r->change_property("reminder_id", USE_IN_UPDATE, true);

	$r->add_where("user_id", INTEGER);
	$r->change_property("user_id", USE_IN_INSERT, true);
	$r->change_property("user_id", USE_IN_UPDATE, true);
	
	$r->add_textbox("end_date", DATETIME);
	$r->change_property("end_date", VALUE_MASK, $date_edit_format);
	$r->add_textbox("start_date", DATETIME);
	$r->change_property("start_date", VALUE_MASK, $date_edit_format);
	$r->add_textbox("date_added",   DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	
	$r->add_textbox("reminder_title",TEXT,"Reminder Title");
	$r->change_property("reminder_title",REQUIRED,true);
	$r->add_textbox("reminder_notes",TEXT);
	$r->add_textbox("reminder_year",INTEGER);
	$r->change_property("reminder_year", USE_SQL_NULL, false);
	$r->add_select("reminder_month", INTEGER, $monthes);
	$r->change_property("reminder_month", USE_SQL_NULL, false);
	$r->add_textbox("reminder_day",INTEGER);
	$r->change_property("reminder_day", USE_SQL_NULL, false);
	$r->add_textbox("reminder_weekdays",INTEGER);
	$r->change_property("reminder_weekdays", USE_SQL_NULL, false);
	
	$r->events[BEFORE_INSERT] = "insert_weekdays";
	$r->events[BEFORE_UPDATE] = "insert_weekdays";
	
	$r->add_checkboxlist("weekdays",INTEGER,$weekdays_values);
	$r->change_property("weekdays", USE_IN_SELECT, false);
	$r->change_property("weekdays", USE_IN_INSERT, false);
	$r->change_property("weekdays", USE_IN_UPDATE, false);

	$r->get_form_values();
	
	$r->set_value("user_id", get_session("session_user_id"));

	$reminder_id=get_param("reminder_id");
	$operation = get_param("operation");
	$return_page = "user_reminders.php";

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $reminder_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "reminders WHERE reminder_id=" . $db->tosql($reminder_id, INTEGER));
			header("Location: " . $return_page);
			exit;
		} else if ($operation == "save") {

			$is_valid = $r->validate();
			$reminder_month = $r->get_value("reminder_month");
			$reminder_day = $r->get_value("reminder_day");
			$reminder_year = $r->get_value("reminder_year");
				
			if ($reminder_month && $reminder_day && $reminder_year && !checkdate($reminder_month,$reminder_day,$reminder_year))
			{
				$r->errors .= str_replace("{field_name}", "Reminder Date", INCORRECT_DATE_MESSAGE);
			}

			if(!strlen($r->errors))
			{
				$start_date = $r->get_value("start_date");
				
				if (strlen($reminder_id)) {
					$r->set_value("date_modified", va_time());
					call_event($r->events, BEFORE_UPDATE);
					$record_updated = $r->update_record();
				} else {
					$r->set_value("date_added", va_time());
					$r->set_value("date_modified", va_time());
					$db->query("SELECT MAX(reminder_id) FROM " . $table_prefix . "reminders");
					$db->next_record();
					$reminder_id = $db->f(0) + 1;
					$r->set_value("reminder_id", $reminder_id);
					call_event($r->events, BEFORE_INSERT);
					$record_updated = $r->insert_record();				
				}
				header("Location: ".$return_page);
				exit;
			}
		}
	}
	else if(strlen($reminder_id))
	{
		$r->get_db_values();
		if ($r->get_value("reminder_month") == 0) {
			$r->set_value("reminder_month", "");
		}
		if ($r->get_value("reminder_day") == 0) {
			$r->set_value("reminder_day", "");
		}
		if ($r->get_value("reminder_year") == 0) {
			$r->set_value("reminder_year", "");
		}
		$reminder_weekdays = $r->get_value("reminder_weekdays");
		$reminder_month = $r->get_value("reminder_month");
		for ($i=1; $i < 65; $i=$i*2) {
			if ($i&$reminder_weekdays) {
				$r->set_value("weekdays", $i);
			}
		}
	}
	else // new record (set default values)
	{
	}
	
	$r->set_form_parameters();
	
	if(strlen($reminder_id)) {
		$t->set_var("save_button_title", UPDATE_BUTTON);
		$t->global_parse("save_button", false, false, true);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button_title", ADD_BUTTON);
		$t->global_parse("save_button", false, false, true);
		$t->set_var("delete", "");	
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
	
	function insert_weekdays() {
		global $r;
		if (is_array($r->get_value("weekdays"))) {
			$r->set_value("reminder_weekdays",array_sum($r->get_value("weekdays")));
		} else {
			$r->set_value("reminder_weekdays", "");
		}
	}

?>