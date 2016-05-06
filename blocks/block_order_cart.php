<?php

function order_cart($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $current_page;
	global $months, $parameters, $cc_parameters, $additional_parameters;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_order_cart.html");

	if ($current_page == "order_final.php") {
		$order_id = get_order_id();
		$vc = get_session("session_vc");
		$order_errors = check_order($order_id, "", true);
	} else {
		$order_id = get_param("order_id");
		$vc = get_param("vc");
		if (!strlen($order_id)) { $order_id = get_session("session_order_id"); }
		if (!strlen($vc)) { $vc = get_session("session_vc"); }
		$order_errors = check_order($order_id, $vc);
	}

	if (!$order_errors) {
		// show cart always as for payment details page
		$items_text = show_order_items($order_id, true, "cc_info");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>