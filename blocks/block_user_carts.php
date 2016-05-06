<?php

	check_user_security("my_carts");

	$operation = get_param("operation");
	$cart_id = get_param("cart_id");
	$session_user_id = get_session("session_user_id");

	// delete user cart
	if ($operation == "delete" && strlen($cart_id)) {
		$sql  = " DELETE FROM " . $table_prefix . "saved_items ";
		$sql .= " WHERE cart_id=" . $db->tosql($cart_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql($session_user_id, INTEGER);
		$db->query($sql);

		$sql  = " DELETE FROM " . $table_prefix . "saved_carts ";
		$sql .= " WHERE cart_id=" . $db->tosql($cart_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql($session_user_id, INTEGER);
		$db->query($sql);
	}

  $t->set_file("block_body","block_user_carts.html");
	$t->set_var("user_carts_href", get_custom_friendly_url("user_carts.php"));
	$t->set_var("cart_retrieve_href", get_custom_friendly_url("cart_retrieve.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CART_TITLE, CONFIRM_DELETE_MSG));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_carts.php"));
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(4, "desc");
	$s->set_sorter(CART_NO_FIELD, "sorter_id", "1", "cart_id");
	$s->set_sorter(CART_NAME_FIELD, "sorter_cart_name", "2", "cart_name");
	$s->set_sorter(CART_TOTAL_COLUMN, "sorter_total", "3", "cart_total");
	$s->set_sorter(CART_SAVED_DATE_COLUMN, "sorter_date", "4", "cart_added");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_carts.php"));

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "saved_carts sc ";
	$sql .= " WHERE sc.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT sc.cart_id, sc.cart_name, sc.cart_total, sc.cart_added ";
	$sql .= " FROM " . $table_prefix . "saved_carts sc ";
	$sql .= " WHERE sc.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$cart_total = $db->f("cart_total");
			$cart_name = $db->f("cart_name");
			$t->set_var("cart_id", $db->f("cart_id"));

			$cart_added = $db->f("cart_added", DATETIME);
			$t->set_var("cart_added", va_date($datetime_show_format, $cart_added));

			$t->set_var("cart_name", htmlspecialchars($cart_name));
			$t->set_var("cart_total", currency_format($cart_total));

			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>