<?php
function products_users_bought($block_name, $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $site_id, $db_type, $table_prefix;
	global $settings, $page_settings;
	global $language_code, $currency, $date_show_format, $datetime_show_format;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$user_info = get_session("session_user_info");
	$user_id = get_session("session_user_id");
	$user_type_id = get_session("session_user_type_id");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$ubi_recs = get_setting_value($page_settings, "users_bought_item_recs", 10);
	$ubi_cols = get_setting_value($page_settings, "users_bought_item_cols", 1);
	$ubi_days = get_setting_value($page_settings, "users_bought_item_days", 30);
	$ubi_status = get_setting_value($page_settings, "users_bought_status", "PAID");
	$ubi_type = get_setting_value($page_settings, "users_bought_type", "ALL");

	$fields = array(
		"fn" => array("show" => 0, "field_name" => "o.first_name"),
		"ln" => array("show" => 0, "field_name" => "o.last_name"),
		"name" => array("show" => 0, "field_name" => "o.name"),
		"nickname" => array("show" => 1, "field_name" => "u.nickname"),
		"cn" => array("show" => 0, "field_name" => "o.company_name"),
		"email" => array("show" => 0, "field_name" => "o.email"),
		"country" => array("show" => 1, "field_name" => "c.country_name"),
		"state" => array("show" => 1, "field_name" => "s.state_name"),
		"od" => array("show" => 1, "field_name" => "o.order_placed_date"),
	);

	foreach($fields as $key => $field_info) {
		$show_value = get_setting_value($page_settings, "users_bought_item_" . $key, $field_info["show"]);
		$fields[$key]["show"] = $show_value;
	}

	$ubi_time = mktime(0,0,0, date("m"), date("d") - intval($ubi_days), date("Y"));
	$order_placed_date = va_time($ubi_time);

	$item_id = get_param("item_id");
	$t->set_file("block_body", "block_products_users_bought.html");
	$t->set_var("product_details_href", "product_details.php");

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$product_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
		$product_page = "product_details.php";
	}

	$sql  = " SELECT COUNT(*) AS total ";
	$sql .= " FROM ";
	if ($ubi_type == "REG") {
		$sql .= " (";
	}
	$sql .= " ((" . $table_prefix . "orders_items oi ";
	$sql .= " INNER JOIN " . $table_prefix . "orders o ON o.order_id=oi.order_id) ";
	$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
	if ($ubi_type == "REG") {
		$sql .= " INNER JOIN " . $table_prefix . "users u ON oi.user_id=u.user_id) ";
	}
	$sql .= " WHERE o.order_placed_date >=" . $db->tosql($order_placed_date, DATETIME);
	if ($ubi_status == "PAID") {
		$sql .= " AND os.paid_status=1 ";
	} elseif (strlen($ubi_status) && $ubi_status != "ANY") {
		$sql .= " AND o.order_status=" . $db->tosql($ubi_status, INTEGER);
	}
	if ($ubi_type == "ALL") {
		// don't need where condition
	} else if ($ubi_type == "NON") {
		$sql .= " AND (o.user_id=0 OR o.user_id IS NULL) ";
	} else if ($ubi_type == "REG") {
		$sql .= " AND o.user_id>0 AND o.user_id IS NOT NULL ";
	} else if (strlen($ubi_type)) {
		$sql .= " AND o.user_type_id=" . $db->tosql($ubi_type, INTEGER);
	}
	$sql .= " AND oi.item_id=" . $db->tosql($item_id, INTEGER);
	$total_records = get_db_value($sql);

	if (!$total_records) {
		return;
	}

	// set titles
	$fields_colspan = 0;
	for ($c = 0; $c < $ubi_cols; $c++) {
		if ($c) {
			$fields_colspan++;
			$t->parse_to("title_separator", "ubi_titles");
		}
		foreach($fields as $field_name => $field_info) {
			if ($field_info["show"]) {
				$fields_colspan++;
				$t->parse_to($field_name."_title", "ubi_titles");
			}
		}
	}
	$t->set_var("fields_colspan", $fields_colspan);

	$pages_number = 5;
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $product_page);
	$page_number = $n->set_navigator("ubi_navigator", "ubi_page", SIMPLE, $pages_number, $ubi_recs, $total_records, false, $pass_parameters);
  
	$sql  = " SELECT u.user_id, u.login";
	foreach($fields as $key => $field_info) {
		if ($field_info["show"]) {
			$sql .= ", ".$field_info["field_name"];
		}
	}
	$sql .= " FROM ";
	if ($fields["country"]) { $sql .= "("; }
	if ($fields["state"]) { $sql .= "("; }
	$sql .= " (((" . $table_prefix . "orders_items oi ";
	$sql .= " INNER JOIN " . $table_prefix . "orders o ON o.order_id=oi.order_id) ";
	$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON oi.user_id=u.user_id) ";
	if ($fields["country"]) { 
		$sql .= " LEFT JOIN " . $table_prefix . "countries c ON c.country_id=o.country_id) ";
	}
	if ($fields["state"]) { 
		$sql .= " LEFT JOIN " . $table_prefix . "states s ON s.state_id=o.state_id) ";
	}
	$sql .= " WHERE o.order_placed_date >=" . $db->tosql($order_placed_date, DATETIME);
	if ($ubi_status == "PAID") {
		$sql .= " AND os.paid_status=1 ";
	} elseif (strlen($ubi_status) && $ubi_status != "ANY") {
		$sql .= " AND o.order_status=" . $db->tosql($ubi_status, INTEGER);
	}
	if ($ubi_type == "ALL") {
		// don't need where condition
	} else if ($ubi_type == "NON") {
		$sql .= " AND (o.user_id=0 OR o.user_id IS NULL) ";
	} else if ($ubi_type == "REG") {
		$sql .= " AND o.user_id>0 AND o.user_id IS NOT NULL ";
	} else if (strlen($ubi_type)) {
		$sql .= " AND o.user_type_id=" . $db->tosql($ubi_type, INTEGER);
	}
	$sql .= " AND oi.item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= " ORDER BY o.order_placed_date DESC,oi.order_item_id DESC ";
  
	$db->RecordsPerPage = $ubi_recs;
	$db->PageNumber     = $page_number;
	$db->query($sql);
	if ($db->next_record()) {
		$ubi_number = 0;
		$ubi_column = 0;

		do {
			$ubi_number++;
			$ubi_column++;
			$user_id = $db->f("user_id");
			$first_name = $db->f("first_name");
			$last_name = $db->f("last_name");
			$company_name = $db->f("company_name");
			$name = $db->f("name");
			$login = $db->f("login");
			$nickname = $db->f("nickname");
			$country_name = get_translation($db->f("country_name"));
			$state_name = get_translation($db->f("state_name"));
			$order_placed_date = $db->f("order_placed_date", DATETIME);
			if (!$nickname) { 
				if (preg_match(EMAIL_REGEXP, $login)) {
					$nickname = preg_replace("/@.*$/", "", $login);
				} else {
					$nickname = $login; 
				}
			}
			$email = $db->f("email");
  
			$t->set_var("user_id", $user_id);
			$t->set_var("first_name", $first_name);
			$t->set_var("last_name", $last_name);
			$t->set_var("name", $name);
			$t->set_var("nickname", $nickname);
			$t->set_var("email", $email);
			$t->set_var("country_name", $country_name);
			$t->set_var("company_name", $company_name);
			$t->set_var("state_name", $state_name);
			$t->set_var("order_placed_date", va_date($datetime_show_format, $order_placed_date));

			foreach($fields as $field_name => $field_info) {
				if ($field_info["show"]) {
					$t->parse_to($field_name."_block", "ubi_row");
				}
			}
			if ($ubi_cols > 1 && $ubi_cols != $ubi_column) {
				$t->parse_to("row_separator", "ubi_row");
			}

			if ($ubi_number % $ubi_cols == 0) {
				$ubi_column = 0;
				$t->parse("ubi_rows");
				$t->set_var("ubi_row", "");
			}
  
		} while ($db->next_record());

		if ($ubi_number % $ubi_cols != 0) {
			$t->parse("ubi_rows");
		}
	}
  
	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>