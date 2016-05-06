<?php

function products_changes_log($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $date_show_format;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_products_changes_log.html");

	$t->set_var("products_href", get_custom_friendly_url("products.php"));
	$t->set_var("current_href", get_custom_friendly_url("index.php"));

	$item_id = get_param("item_id");
	$release_id = get_param("release_id");

	if ($release_id) {
		$sql  = " SELECT i.item_name FROM " . $table_prefix . "items i, " . $table_prefix . "releases r ";
		$sql .= " WHERE r.item_id=i.item_id";
		$sql .= " AND r.release_id=" . $db->tosql($release_id, INTEGER);
	} else {
		$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	}
	$db->query($sql);
	if ($db->next_record()) {
		$item_name = $db->f("item_name");
		$prod_changes_message = str_replace("{product_name}", $item_name, PROD_CHANGES_LOG_MSG);
		$t->set_var("item_name", $item_name);
		$t->set_var("PROD_CHANGES_LOG_MSG", $prod_changes_message);
	} else {
		header("Location: " . get_custom_friendly_url("index.php"));
		exit;
	}


	$sql  = " SELECT r.release_id, r.release_title, r.version, r.release_date, rc.change_desc ";
	$sql .= " FROM " . $table_prefix . "releases r, " . $table_prefix . "release_changes rc ";
	$sql .= " WHERE r.release_id=rc.release_id ";
	if ($release_id) {
		$sql .= " AND r.release_id=". $db->tosql($release_id, INTEGER);
	} else {
		$sql .= " AND r.item_id=". $db->tosql($item_id, INTEGER);
	}
	$sql .= " AND r.is_showing=1 ";
	$sql .= " AND rc.is_showing=1 ";
	$sql .= " ORDER BY r.release_date DESC, rc.change_date ";
	$db->query($sql);
	if ($db->next_record())
	{
		$release_id = $db->f("release_id");
		do {
			$current_release_id = $db->f("release_id");
			if ($release_id != $current_release_id) {
				$t->set_var("version", $version);
				$t->set_var("release_title", $release_title);
				$t->set_var("release_date", va_date($date_show_format, $release_date));
				$t->parse("releases", true);
				$t->set_var("changes", "");
			}
			$t->set_var("change_desc", $db->f("change_desc"));
			$t->parse("changes", true);

			$release_id = $db->f("release_id");
			$release_title = $db->f("release_title");
			$release_date = $db->f("release_date", DATETIME);
			$version = $db->f("version");
		} while ($db->next_record());

		$t->set_var("release_title", $release_title);
		$t->set_var("release_date", va_date($date_show_format, $release_date));
		$t->set_var("version", $version);
		$t->parse("releases", true);

	} else {
		$t->parse("no_releases", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>