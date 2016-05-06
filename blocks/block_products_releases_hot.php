<?php

function products_releases_hot($block_name, $item_id = "")
{
	global $t, $db, $table_prefix;
	global $page_settings;
	global $date_show_format;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body",   "block_products_releases.html");
	$t->set_var("releases_href", "changes_log.php");
	$t->set_var("releases_href", "releases.php");

	$sql  = " SELECT release_id, item_id, release_title, version, release_date, release_desc ";
	$sql .= " FROM " . $table_prefix . "releases ";
	$sql .= " WHERE is_showing=1 ";
	if (strlen($item_id)) {
		$sql .= " AND item_id=" . $db->tosql($item_id, INTEGER);
	} else {
		$sql .= " AND show_on_index=1 ";
	}
	$sql .= " ORDER BY release_date DESC ";

	$db->query($sql);
	if ($db->next_record())
	{
		do {
			$t->set_var("release_id", $db->f("release_id"));
			$t->set_var("item_id", $db->f("item_id"));
			$t->set_var("release_title", $db->f("release_title"));
			$t->set_var("version", $db->f("version"));
			$t->set_var("release_desc", $db->f("release_desc"));
			$release_date = $db->f("release_date", DATETIME);
			$t->set_var("release_date", va_date($date_show_format, $release_date));

			$t->parse("releases");
		} while ($db->next_record());              	
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
}

?>