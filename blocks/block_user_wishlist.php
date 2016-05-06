<?php

	check_user_security("my_wishlist");

	$operation = get_param("operation");
	if ($operation == "add") {
		$cart_item_id = get_param("cart_item_id");

		// retrieve cart
		$sql  = " SELECT * FROM " . $table_prefix . "saved_items ";
		$sql .= " WHERE cart_item_id=" . $db->tosql($cart_item_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		$sql .= " ORDER BY cart_item_id ";
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$sc_errors = "";
				$cart_item_id = $db->f("cart_item_id");
				$item_id = $db->f("item_id");
				$item_name = $db->f("item_name");
				$quantity = $db->f("quantity");
				$price = $db->f("price");

				// add to cart
				add_to_cart($item_id, $price, $quantity, "db", "ADD", $new_cart_id, $second_page_options, $sc_errors, $cart_item_id, $item_name);

			} while ($db->next_record());
		}
		// check if any coupons can be added or removed
		check_coupons();

		header("Location: " . get_custom_friendly_url("basket.php") . "?rp=" . urlencode(get_custom_friendly_url("user_wishlist.php")));
		exit;
	} else if ($operation == "delete") {
		// delete an item
		$cart_item_id = get_param("cart_item_id");
		$sql  = " DELETE FROM " . $table_prefix . "saved_items ";
		$sql .= " WHERE cart_item_id=" . $db->tosql($cart_item_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		$db->query($sql);
	}


  $t->set_file("block_body","block_user_wishlist.html");
	$t->set_var("user_wishlist_href", get_custom_friendly_url("user_wishlist.php"));
	$t->set_var("cart_retrieve_href", get_custom_friendly_url("cart_retrieve.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_wishlist.php"));
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(6, "desc");
	$s->set_sorter(PROD_NAME_MSG, "sorter_item_name", "1", "si.item_name");
	$s->set_sorter(PRICE_MSG, "sorter_price", "2", "si.price");
	$s->set_sorter(QTY_MSG, "sorter_quantity", "3", "si.quantity");
	$s->set_sorter("Bought", "sorter_quantity_bought", "4", "si.quantity_bought");
	$s->set_sorter(TYPE_MSG, "sorter_type", "5", "st.type_name");
	$s->set_sorter(CART_SAVED_DATE_COLUMN, "sorter_date", "6", "si.date_added");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_wishlist.php"));

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "saved_items si ";
	$sql .= " WHERE si.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND si.cart_id=0 ";
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT si.cart_item_id, si.item_id, si.item_name, si.price, st.type_name, si.quantity, si.quantity_bought, si.date_added ";
	$sql .= " FROM (" . $table_prefix . "saved_items si ";
	$sql .= " LEFT JOIN " . $table_prefix . "saved_types st ON st.type_id=si.type_id) ";
	$sql .= " WHERE si.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND si.cart_id=0 ";
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");

		$cart_url = new VA_URL("user_wishlist.php", false);
		$cart_url->add_parameter("cart_item_id", DB, "cart_item_id");
		$cart_url->add_parameter("operation", CONSTANT, "add");

		$delete_url = new VA_URL("user_wishlist.php", false);
		$delete_url->add_parameter("cart_item_id", DB, "cart_item_id");
		$delete_url->add_parameter("operation", CONSTANT, "delete");

		do
		{
			$cart_item_id = $db->f("cart_item_id");
			$price = $db->f("price");
			$quantity = $db->f("quantity");
			$quantity_bought = $db->f("quantity_bought");
			$item_name = $db->f("item_name");
			$type_name = $db->f("type_name");
			$date_added = $db->f("date_added", DATETIME);

			$t->set_var("cart_item_id", $db->f("cart_item_id"));
			$t->set_var("date_added", va_date($datetime_show_format, $date_added));

			$t->set_var("item_name", get_translation($db->f("item_name")));
			$t->set_var("type_name", get_translation($db->f("type_name")));
			$t->set_var("price", currency_format($price));
			$t->set_var("quantity", $quantity);
			$t->set_var("quantity_bought", $quantity_bought);

			$t->set_var("cart_url", $cart_url->get_url());
			$t->set_var("delete_url", $delete_url->get_url());

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