<?php

function site_search_form($block_name)
{
	global $t, $db, $table_prefix, $language_code;
	global $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if($block_name) {
		$t->set_file("block_body", "block_site_search_form.html");
	}

	$t->set_var("search_href", get_custom_friendly_url("site_search.php"));

	$q = trim(get_param("q"));

	// set up search form parameters
	$t->set_var("q", htmlspecialchars($q));

	if($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>