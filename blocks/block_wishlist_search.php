<?php

function wishlist_search($block_name)
{
	global $t, $db, $table_prefix, $language_code;
	global $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if($block_name) {
		$t->set_file("block_body", "block_wishlist_search.html");
	}

	$t->set_var("search_href",   "wishlist.php");

	$se = trim(get_param("se"));

	// set up search form parameters
	$t->set_var("se", htmlspecialchars($se));

	if($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>