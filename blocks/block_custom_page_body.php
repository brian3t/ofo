<?php

function custom_page_body($block_name, $page_title, $page_body)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $currency;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_custom_page_body.html");

	$t->set_block("page_title", $page_title);
	$t->parse("page_title", false);
	$t->set_block("page_body", $page_body);
	$t->parse("page_body", false);

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>