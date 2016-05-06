<?php                           
function subscriptions_breadcrumb($block_name)
{
	global $t, $settings, $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$t->set_file("block_body", "block_subscriptions_breadcrumb.html");

	$breadcrumbs_tree_array = array();
	
	$breadcrumbs_tree_array[] = array (get_custom_friendly_url("products.php"), PRODUCTS_TITLE);
	$breadcrumbs_tree_array[] = array (get_custom_friendly_url("subscriptions.php"), SUBSCRIPTIONS_MSG);
	
	$ic = count($breadcrumbs_tree_array) - 1;
	for ($i=0; $i<$ic; $i++) {
		$t->set_var("tree_url", $breadcrumbs_tree_array[$i][0]);
		$t->set_var("tree_title", $breadcrumbs_tree_array[$i][1]);
		$t->set_var("tree_class", "");
		$t->parse("tree", true);
	}
	
	if ($ic>=0) {
		$t->set_var("tree_url", $breadcrumbs_tree_array[$ic][0]);
		$t->set_var("tree_title", $breadcrumbs_tree_array[$ic][1]);
		$t->set_var("tree_class", "treeItemLast");
		$t->parse("tree", true);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>