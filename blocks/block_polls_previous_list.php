<?php
function polls_previous_list($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $date_show_format;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_polls_previous_list.html");


	$t->set_var("polls_href", "polls.php");
	$t->set_var("poll_vote_href", "poll_vote.php");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", "polls.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "polls WHERE is_active<>1  ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 20;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$t->set_var("page", $page_number);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT * FROM " . $table_prefix . "polls ";
	$sql .= " WHERE is_active<>1 ";
	$sql .= " ORDER BY date_added DESC ";
	$db->query($sql);
	if($db->next_record())
	{
		$meta_description = get_translation($db->f("question"), $language_code);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("poll_id", $db->f("poll_id"));
			$t->set_var("poll_question", get_translation($db->f("question"), $language_code));
			$poll_date = $db->f("date_added", DATETIME);
			$t->set_var("poll_date", va_date($date_show_format, $poll_date));

			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$meta_description = NO_POLLS_MSG;
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->set_var("meta_description", get_meta_desc($meta_description));

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>