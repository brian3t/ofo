<?php                           

function checkout_breadcrumb($block_name)
{
	global $t, $db, $site_id, $table_prefix;
	global $settings, $page_settings, $current_page;

	$t->set_file("block_body", "block_checkout_breadcrumb.html");

	if ($current_page == "order_info.php") {
		$t->set_var("step1_class", "active");
		$t->set_var("step2_class", "nonactive");
		$t->set_var("step3_class", "nonactive");
	} else if ($current_page == "credit_card_info.php") {
		$t->set_var("step1_class", "nonactive");
		$t->set_var("step2_class", "active");
		$t->set_var("step3_class", "nonactive");
	} else if ($current_page == "order_confirmation.php") {
		$t->set_var("step1_class", "nonactive");
		$t->set_var("step2_class", "nonactive");
		$t->set_var("step3_class", "active");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>