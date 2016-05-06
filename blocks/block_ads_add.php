<?php
function ads_add($block_name, $category_id) {
	global $db, $settings, $table_prefix, $t;
	
	$user_id = get_session("session_user_id");

	if (!strlen($user_id) || !$category_id) return;
			
	$sql  = " SELECT setting_value ";
	$sql .= " FROM " . $table_prefix . "user_types_settings ";
	$sql .= " WHERE type_id=" . $db->tosql(get_session("session_user_type_id"), INTEGER);
	$sql .= " AND setting_name=" . $db->tosql("add_ad", TEXT);
	$allow_access = get_db_value($sql);	
	if (!$allow_access) return;
	
	$categories_ids = VA_Ads_Categories::find_all_ids("c.category_id=" . $db->tosql($category_id, INTEGER), ADD_ITEMS_PERM);
	if (!$categories_ids ) return;
	
	$t->set_file("block_body", "block_ads_add.html");
	
	$t->set_var("user_ad_href",   get_custom_friendly_url("user_ad.php"));
	$t->set_var("category_id",    $category_id);
	
	$sql  = " SELECT type_id, type_name ";
	$sql .= " FROM " . $table_prefix . "ads_types ";
	$db->query($sql);
	while ($db->next_record()) {
		$type_id   = $db->f("type_id");
		$type_name = get_translation($db->f("type_name"));
		$t->set_var("type_id", $type_id);
		$t->set_var("type_name", $type_name);
		$t->parse("ads_types", true);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}
?>