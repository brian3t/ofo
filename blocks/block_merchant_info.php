<?php

function merchant_info($block_name, $merchant_name, $merchant_info)
{
	global $t, $db, $table_prefix, $language_code;
	global $category_id, $restrict_categories_images;
	global $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if (strlen($merchant_info)) {

	  $t->set_file("block_body", "block_merchant_info.html");
	
		$t->set_var("merchant_name", $merchant_name);
		$t->set_var("merchant_info", $merchant_info);
	
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>