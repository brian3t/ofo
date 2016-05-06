<?php
function products_releases($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $date_show_format;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_products_releases.html");

	$t->set_var("changes_log_href", "changes_log.php");

	$user_id = get_session("session_user_id");
	$item_id = get_param("item_id");
	$order_item_id = get_param("order_item_id");
	$ordered_product = false;
	if ($order_item_id) {
		$sql  = " SELECT i.item_id, i.item_name, i.download_period, o.order_status, o.order_placed_date ";
		$sql .= " FROM " . $table_prefix . "items i, " . $table_prefix . "orders_items oi, " . $table_prefix . "orders o ";
		$sql .= " WHERE oi.item_id=i.item_id ";
		$sql .= " AND o.order_id=oi.order_id ";
		$sql .= " AND oi.order_item_id=" . $db->tosql($order_item_id, INTEGER);
		$sql .= " AND o.user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$item_id = $db->f("item_id");
			$item_name = $db->f("item_name");
			$download_period = $db->f("download_period");
			$order_status = $db->f("order_status");
			$order_placed_date = $db->f("order_placed_date", DATETIME);
			$expiry_date = mktime (0,0,0, $order_placed_date[MONTH], $order_placed_date[DAY] + $download_period, $order_placed_date[YEAR]);
			$current_date = mktime(0,0,0, date("m"), date("d"), date("Y"));
			$sql  = " SELECT download_activation FROM " . $table_prefix . "order_statuses ";
			$sql .= " WHERE status_id=" . $db->tosql($order_status, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$download_activation = $db->f("download_activation");
			} else {
				$download_activation = 0;
			}
			if ($expiry_date >= $current_date && $download_activation == 1) {
				$ordered_product = true;
			}
		} else {
			header("Location: user_login.php");
			exit;
		}
	} else {
		$sql  = " SELECT item_name ";
		$sql .= " FROM " . $table_prefix . "items ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$item_name = $db->f("item_name");
		} else {
			return;
		}
	}

	$prod_releases_message = str_replace("{product_name}", $item_name, PROD_RELEASES_MSG);
	$t->set_var("PROD_RELEASES_MSG", $prod_releases_message);
	$t->set_var("item_name", $item_name);

	$sql  = " SELECT r.release_id, r.release_date, r.release_title, ";
	$sql .= " r.version, r.download_type, r.path_to_file, r.release_desc ";
	$sql .= " FROM " . $table_prefix . "releases r ";
	$sql .= " WHERE r.item_id=". $db->tosql($item_id, INTEGER);
	$sql .= " AND r.is_showing=1 ";
	$sql .= " ORDER BY r.release_date DESC ";
	$db->query($sql);
	if ($db->next_record())
	{
		$latest_version = $db->f("version");
		do {
			$release_id = $db->f("release_id");
			$t->set_var("release_id", $release_id);
			$release_date = $db->f("release_date", DATETIME);
			$t->set_var("release_date", va_date($date_show_format, $release_date));
			$t->set_var("release_title", $db->f("release_title"));
			$t->set_var("release_desc", $db->f("release_desc"));
			$t->set_var("version", $db->f("version"));
			$download_type = $db->f("download_type");
			$path_to_file = $db->f("path_to_file");
			$t->set_var("download", "");
			if(strlen($path_to_file) && 
				($download_type == 1 || ($download_type == 2 && $user_id) || ($download_type == 3 && $ordered_product))
			) {
				$paths = split(";", $path_to_file);
				for($pi = 0; $pi < sizeof($paths); $pi++) {
					$sub_path = $paths[$pi];
					if($sub_path) {
						$download_url = $settings["site_url"] . "download.php?release_id=" . $release_id . "&path_id=" . ($pi + 1);
						if($download_type == 3) {
							$download_url .= "&order_item_id=" . $order_item_id;
						}
						$t->set_var("download_url", $download_url);
						$t->set_var("filename", basename($sub_path));
						$t->parse("download", true);
					}
				}
			} 
			$t->parse("releases", true);
		} while ($db->next_record());


	} else {
		$t->parse("no_releases", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>