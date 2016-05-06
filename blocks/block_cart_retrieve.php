<?php                           

function cart_retrieve($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_cart_retrieve.html");

	$current_page = "cart_save.php";


	$shopping_cart = get_session("shopping_cart");

	$t->set_var("basket_href",   get_custom_friendly_url("basket.php"));
	$t->set_var("current_href",  get_custom_friendly_url("cart_retrieve.php"));
	$t->set_var("checkout_href", get_custom_friendly_url("checkout.php"));
	$t->set_var("products_href", get_custom_friendly_url("products.php"));
	$t->set_var("cart_retrieve_href",get_custom_friendly_url("cart_retrieve.php"));

	// set up return page
	$rp = get_param("rp");
	if(!$rp) { $rp = get_custom_friendly_url("products.php"); }
	$t->set_var("rp", htmlspecialchars($rp));

	$operation = get_param("operation");
	$user_id = get_session("session_user_id");

	$r = new VA_Record($table_prefix . "saved_carts");
	$r->add_textbox("cart_id", INTEGER, CART_NO_FIELD);
	$r->change_property("cart_id", REQUIRED, true);
	$r->add_textbox("cart_name", TEXT, CART_NAME_FIELD);
	if (!$user_id) {
		$r->change_property("cart_name", REQUIRED, true);
	}

	if(strlen($operation)) 
	{
		if ($operation == "cancel") {
			header("Location: " . get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp));
			exit;
		} 
		$r->get_form_values();

		$is_valid = $r->validate();
		if ($is_valid) {
			$sql  = " SELECT cart_id FROM " . $table_prefix . "saved_carts ";
			$sql .= " WHERE cart_id=" . $db->tosql($r->get_value("cart_id"), INTEGER);
			$sql .= " AND (cart_name=" . $db->tosql($r->get_value("cart_name"), TEXT, true, false);
			if ($user_id) {
				$sql .= " OR user_id=" . $db->tosql($user_id, INTEGER);
			}
			$sql .= ")";
			$db->query($sql);
			if(!$db->next_record()) {
				$is_valid = false;
				$r->errors = RETRIEVE_CART_ERROR;
			}
		}

		if ($is_valid) {
			// clear current cart
			set_session("shopping_cart", "");
			set_session("session_coupons", "");

			// Database Initialize
			$dbi = new VA_SQL();
			$dbi->DBType      = $db->DBType;
			$dbi->DBDatabase  = $db->DBDatabase;
			$dbi->DBHost      = $db->DBHost;
			$dbi->DBPort      = $db->DBPort;
			$dbi->DBUser      = $db->DBUser;
			$dbi->DBPassword  = $db->DBPassword;
			$dbi->DBPersistent= $db->DBPersistent;

			// retrieve cart
			$sql  = " SELECT * FROM " . $table_prefix . "saved_items ";
			$sql .= " WHERE cart_id=" . $db->tosql($r->get_value("cart_id"), INTEGER);
			$sql .= " ORDER BY cart_item_id ";
			$dbi->query($sql);
			if ($dbi->next_record()) {
				do {
					$sc_errors = "";
					$cart_item_id = $dbi->f("cart_item_id");
					$item_id = $dbi->f("item_id");
					$item_name = $dbi->f("item_name");
					$quantity = $dbi->f("quantity");
					$price = $dbi->f("price");

					// add to cart
					add_to_cart($item_id, $price, $quantity, "db", "ADD", $new_cart_id, $second_page_options, $sc_errors, $cart_item_id, $item_name);

				} while ($dbi->next_record());
			}
			// check if any coupons can be added or removed
			check_coupons();

			header("Location: " . get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp));
			exit;
		}
			
	}

	$r->set_parameters();

	$t->set_var("rp", htmlspecialchars($rp));

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>