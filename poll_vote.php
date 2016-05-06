<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  poll_vote.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main", "poll_vote.html");
	$t->set_var("CHARSET", CHARSET);
	$t->set_var("VIEW_RESULTS_MSG",    VIEW_RESULTS_MSG);
	$t->set_var("CLOSE_WINDOW_MSG",    CLOSE_WINDOW_MSG);
	$t->set_var("VOTES_MSG",           VOTES_MSG);
	$t->set_var("TOTAL_MSG",           TOTAL_MSG);

	$css_file = "";
	if (isset($settings["style_name"]) && $settings["style_name"]) {
		$css_file = "styles/" . $settings["style_name"];
		if (isset($settings["scheme_name"]) && $settings["scheme_name"]) {
			$css_file .= "_" . $settings["scheme_name"];
		}
		$css_file .= ".css";
	}
	$t->set_var("css_file", $css_file);

	$poll_id = get_param("poll_id");
	if (strlen($poll_id)) {
		$sql_where = " WHERE poll_id=" . $db->tosql($poll_id, INTEGER);
	} else {
		$sql_where = " WHERE is_active=1 ";
	}
	$sql  = " SELECT * FROM " . $table_prefix . "polls ";
	$sql .= $sql_where;
	$db->query($sql);
	if ($db->next_record()) {
		$poll_id = $db->f("poll_id");
		$poll_type = $db->f("poll_type");
		$is_active = $db->f("is_active");
		$question = get_translation($db->f("question"), $language_code);
		$poll_date = $db->f("date_added", DATETIME);
		$total_votes = $db->f("total_votes");
		$t->set_var("poll_id", $poll_id);
		$t->set_var("question", $question);
		$t->set_var("poll_date", va_date($date_show_format, $poll_date));

		$poll_options = array(); 
		$sql  = " SELECT * FROM " . $table_prefix . "polls_options ";
		$sql .= " WHERE poll_id=" . $db->tosql($poll_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$poll_option_id = $db->f("poll_option_id");
			$option_description = get_translation($db->f("option_description"), $language_code);
			$poll_options[$poll_option_id] = $option_description;
		}
		$options_number = sizeof($poll_options);

		$operation = get_param("operation");
		if ($operation == "vote" && $is_active) {
			$remote_address = get_ip();
			$date_added = mktime(date("H"), date("i"), date("s"), date("m"), date("d") - 7, date("Y")); // allow another vote after 7 days
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "polls_votes ";
			$sql .= " WHERE poll_id=" . $db->tosql($poll_id, INTEGER) . " AND remote_address=" . $db->tosql($remote_address, TEXT);		
			$sql .= " AND date_added>" . $db->tosql($date_added, DATETIME);		
			$db->query($sql);
			$db->next_record();
			$user_votes = $db->f(0);
			if ($user_votes < 1) {
				$selected_options = array();
				if ($poll_type == 1) {
					$option_value = get_param("option_value");
					if (strlen($option_value) && isset($poll_options[$poll_option_id])) {
						$selected_options[] = $option_value;
					}
				} elseif ($poll_type == 2) {
					for ($i = 0; $i < $options_number; $i++) {
						$option_value = get_param("option_value_" . ($i + 1));
						if (strlen($option_value) && isset($poll_options[$poll_option_id])) {
							$selected_options[] = $option_value;
						}
					}
				}
				if (sizeof($selected_options) > 0) {
					$date_added = va_time();
					for ($i = 0; $i < sizeof($selected_options); $i++) {
						$poll_option_id = $selected_options[$i];
						$sql  = " INSERT INTO " . $table_prefix . "polls_votes (poll_id, poll_option_id, remote_address, date_added) VALUES (";
						$sql .= $db->tosql($poll_id, INTEGER) . ", ";
						$sql .= $db->tosql($poll_option_id, INTEGER) . ", ";
						$sql .= $db->tosql($remote_address, TEXT) . ", ";
						$sql .= $db->tosql($date_added, DATETIME) . ") ";
						$db->query($sql);
					}
					$sql  = " UPDATE " . $table_prefix . "polls SET total_votes=total_votes+1 ";
					$sql .= " WHERE poll_id=" . $db->tosql($poll_id, INTEGER);
					$db->query($sql);
					$total_votes++;
				}
			}
		}

		$t->set_var("total_votes", $total_votes);
		foreach ($poll_options as $poll_option_id => $option_description) {
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "polls_votes ";
			$sql .= " WHERE poll_option_id=" . $db->tosql($poll_option_id, INTEGER);
			$db->query($sql);
			$db->next_record();
			$option_votes = $db->f(0);
			if ($option_votes == 0) {
				$img_width = "1";
				$option_percent = "0%";
			} else {
				$img_width = round(($option_votes * 240) / $total_votes);
				$option_percent = round(($option_votes * 100) / $total_votes) . "%";
			}			
			$t->set_var("img_width", $img_width);
			$t->set_var("option_percent", $option_percent);
			$t->set_var("option_votes", $option_votes);
			$t->set_var("option_description", $option_description);
			$t->parse("poll_options", true);
		}
		
	} 

	$t->pparse("main");

?>