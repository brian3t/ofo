<?php

function poll_form($block_name)
{
	global $t, $db, $table_prefix, $language_code;
	global $page_settings, $date_show_format;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$polls = array();
	$sql  = " SELECT * FROM " . $table_prefix . "polls ";
	$sql .= " WHERE is_active=1 ";
	$sql .= " ORDER BY date_added DESC ";
	$db->query($sql);
	while ($db->next_record()) {
		$poll_id = $db->f("poll_id");
		$poll_type = $db->f("poll_type");
		$question = get_translation($db->f("question"), $language_code);
		$poll_date = $db->f("date_added", DATETIME);
		$polls[]  = array($poll_id, $poll_type, $question, $poll_date);
	}

	if (sizeof($polls) > 0) {
		$t->set_file("block_body", "block_poll.html");
  
		$t->set_var("poll_vote_href", "poll_vote.php");
		$t->set_var("polls_href", "polls.php");
		$t->set_var("POLL_TITLE",  POLL_TITLE);
		$t->set_var("VOTE_BUTTON", VOTE_BUTTON);
		$t->set_var("VIEW_RESULTS_MSG", VIEW_RESULTS_MSG);
		$t->set_var("PREVIOUS_POLLS_MSG", PREVIOUS_POLLS_MSG);


		for($i = 0; $i < sizeof($polls); $i++) {
			list($poll_id, $poll_type, $question, $poll_date) = $polls[$i];
  
			$poll_control = ($poll_type == 1) ? "radio" : "checkbox";
			$t->set_var("poll_id", $poll_id);
			$t->set_var("question", $question);
			$t->set_var("poll_date", va_date($date_show_format, $poll_date));
			$t->set_var("poll_control", $poll_control);
  
			$option_number = 0;
			$t->set_var("poll_options", "");
			$sql  = " SELECT * FROM " . $table_prefix . "polls_options ";
			$sql .= " WHERE poll_id=" . $db->tosql($poll_id, INTEGER);
			$db->query($sql);
			while($db->next_record()) {
				$option_number++;
				$is_default_value = $db->f("is_default_value");
				$option_checked = ($is_default_value == 1) ? "checked" : "";
				$option_name = ($poll_type == 1) ? "option_value" : "option_value_" . $option_number;
				$t->set_var("poll_option_id",     $db->f("poll_option_id"));
				$t->set_var("option_name",        $option_name);
				$t->set_var("option_checked",     $option_checked);
				$t->set_var("option_description", get_translation($db->f("option_description"), $language_code));
				$t->parse("poll_options", true);
			}
  
			$t->parse("block_body", false);
			$t->parse($block_name, true);
  
		} 
	}
	
}

?>