<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_coupon.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");

	$operation   = get_param("operation");
	$coupon_id   = get_param("coupon_id");
	
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	$order_id = get_param("order_id");

	if ($order_id > 0) {
		check_admin_security("order_vouchers");
	} else {
		check_admin_security("coupons");
	}
	
	$s = get_param("s");
	$s_a = get_param("s_a");
	$discount_type = get_param("discount_type");
	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);

	// NEW TYPES: 6 - buy one and get one free; 7 - buy one and get a % off another product
	// 8 - Amount per products quantities; don't need this one as we use discount quantity for this

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_coupon.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("date_format_msg", $date_format_msg);
	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("admin_coupon_href", "admin_coupon.php");
	$t->set_var("admin_users_select_href", "admin_users_select.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", COUPON_MSG, CONFIRM_DELETE_MSG));

	$friends_discount_types = array(
		array(0, NO_FRIENDS_DISCOUNT_MSG),
		array(1, FRIENDS_ORDERS_DISCOUNT_MSG),
		array(2, INVITED_FRIENDS_DISCOUNT_MSG),
	);

	$periods =
		array(
			array("", ""), array(1, DAY_MSG), array(2, WEEK_MSG), array(3, MONTH_MSG), array(4, YEAR_MSG)
		);


	$r = new VA_Record($table_prefix . "coupons");
	if ($order_id > 0) {
		$r->return_page  = "admin_order_vouchers.php?order_id=" . urlencode($order_id);
	} else {
		$r->return_page  = "admin_coupons.php?s=" . $s . "&s_a=" . $s_a;
	}

	$yes_no = 
		array( 
			array(1, YES_MSG), array(0, NO_MSG)
		);

	$r->add_where("coupon_id", INTEGER);
	$r->add_textbox("order_id", INTEGER);
	$r->change_property("order_id", DEFAULT_VALUE, $order_id);
	$r->change_property("order_id", USE_IN_UPDATE, false);
	$r->add_select("order_item_id", INTEGER, "");

	$r->add_radio("is_active", INTEGER, $yes_no);
	$r->change_property("is_active", REQUIRED, true);
	$r->change_property("is_active", DEFAULT_VALUE, 1);
	$r->add_radio("is_auto_apply", INTEGER, $yes_no); // new
	$r->change_property("is_auto_apply", REQUIRED, true);
	$r->change_property("is_auto_apply", DEFAULT_VALUE, 0);
	$r->add_textbox("apply_order", INTEGER); 
	$r->change_property("apply_order", REQUIRED, true);
	$r->change_property("apply_order", DEFAULT_VALUE, 1);

	$r->add_textbox("coupon_code", TEXT, COUPON_CODE_MSG);
	$r->change_property("coupon_code", REQUIRED, true);
	$r->change_property("coupon_code", UNIQUE, true);
	$r->change_property("coupon_code", TRIM, true);
	$r->change_property("coupon_code", MIN_LENGTH, 3);
	$r->change_property("coupon_code", MAX_LENGTH, 64);
	$r->change_property("coupon_code", DEFAULT_VALUE, strtoupper(substr(md5(va_timestamp()), 0, 8)));
	$r->add_textbox("coupon_title", TEXT, COUPON_TITLE_MSG);
	$r->change_property("coupon_title", REQUIRED, true);
	$r->add_radio("discount_type", INTEGER, "");
	$r->change_property("discount_type", REQUIRED, true);
	$r->change_property("discount_type", DEFAULT_VALUE, $discount_type);
	$r->add_textbox("discount_type_text", INTEGER);
	$r->change_property("discount_type_text", COLUMN_NAME, "discount_type");
	$r->change_property("discount_type_text", CONTROL_NAME, "discount_type");
	$r->change_property("discount_type_text", DEFAULT_VALUE, $discount_type);

	$r->add_textbox("discount_quantity", INTEGER, DISCOUNT_MULTIPLE_MSG); // new
	$r->change_property("discount_quantity", DEFAULT_VALUE, 1);
	$r->add_textbox("discount_amount", NUMBER, DISCOUNT_AMOUNT_MSG);
	$r->change_property("discount_amount", REQUIRED, true);
	$r->change_property("discount_amount", DEFAULT_VALUE, 0);

	$r->add_checkbox("free_postage", NUMBER);
	$r->add_checkbox("coupon_tax_free", NUMBER);
	$r->add_checkbox("order_tax_free", NUMBER);

	$r->add_textbox("start_date", DATETIME, START_DATE_MSG);
	$r->change_property("start_date", VALUE_MASK, $date_edit_format);
	$r->add_textbox("expiry_date", DATETIME, ADMIN_EXPIRY_DATE_MSG);
	$r->change_property("expiry_date", VALUE_MASK, $date_edit_format);
	if ($discount_type != 5) {
		$r->change_property("expiry_date", DEFAULT_VALUE, va_time(va_timestamp() + (60*60*24*366)));
	}

	$r->add_textbox("users_use_limit", INTEGER, USERS_USE_LIMIT_MSG);
	$r->add_textbox("quantity_limit", INTEGER, TIMES_COUPON_CAN_BE_USED);
	$r->change_property("quantity_limit", DEFAULT_VALUE, 1);
	$r->add_textbox("coupon_uses", INTEGER);
	$r->change_property("coupon_uses", DEFAULT_VALUE, 0);

	$r->add_textbox("min_quantity", NUMBER, MINIMUM_ITEMS_QTY_MSG); // new
	$r->add_textbox("max_quantity", NUMBER, MAXIMUM_ITEMS_QTY_MSG); // new
	$r->add_textbox("minimum_amount", NUMBER);
	$r->add_textbox("maximum_amount", NUMBER); // new
	$r->add_checkbox("is_exclusive", NUMBER);
	$r->change_property("is_exclusive", DEFAULT_VALUE, 1);

	// orders fields
	$r->add_select("orders_period", INTEGER, $periods, ORDERS_PERIOD_MSG);
	$r->add_textbox("orders_interval", INTEGER, ORDERS_INTERVAL_MSG);
	$r->add_textbox("orders_min_goods", NUMBER, MINIMUM_GOODS_COST_MSG);
	$r->add_textbox("orders_max_goods", NUMBER, MAXIMUM_GOODS_COST_MSG);

	// products fields
	$r->add_checkbox("items_all", INTEGER);
	$r->change_property("items_all", DEFAULT_VALUE, 1);
	$r->add_textbox("items_ids", TEXT);

	$r->add_checkbox("cart_items_all", INTEGER); // new
	$r->change_property("cart_items_all", DEFAULT_VALUE, 1); // new
	$r->add_textbox("cart_items_ids", TEXT); // new

	// user fields
	$r->add_checkbox("users_all", INTEGER);
	$r->change_property("users_all", DEFAULT_VALUE, 1);
	$r->add_textbox("users_ids", TEXT);

	// friends fields
	$r->add_radio("friends_discount_type", INTEGER, $friends_discount_types, FRIENDS_DISCOUNT_TYPE_MSG);
	$r->change_property("friends_discount_type", REQUIRED, true);
	$r->change_property("friends_discount_type", DEFAULT_VALUE, 0);
	$r->add_select("friends_period", INTEGER, $periods, FRIENDS_PERIOD_MSG);
	$r->add_textbox("friends_interval", INTEGER, FRIENDS_INTERVAL_MSG);
	$r->add_textbox("friends_min_goods", NUMBER, MINIMUM_GOODS_COST_MSG);
	$r->add_textbox("friends_max_goods", NUMBER, MAXIMUM_GOODS_COST_MSG);
	$r->add_checkbox("friends_all", INTEGER);
	$r->change_property("friends_all", DEFAULT_VALUE, 1);
	$r->add_textbox("friends_ids", TEXT);

	// sites list
	$r->add_checkbox("sites_all", INTEGER);
	$r->change_property("sites_all", DEFAULT_VALUE, 1);
	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($coupon_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "coupons_sites ";
			$sql .= " WHERE coupon_id=" . $db->tosql($coupon_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}
	
	// editing information
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_IN_INSERT, false);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->change_property("date_modified", USE_IN_INSERT, false);
	
	$r->events[BEFORE_SHOW] = "set_record_controls";
	$r->events[AFTER_REQUEST] = "set_coupon_data";
	$r->events[AFTER_VALIDATE] = "set_record_controls";	
	$r->events[BEFORE_INSERT] = "set_coupon_id";
	$r->events[BEFORE_UPDATE] = "set_admin_data";
	$r->events[AFTER_INSERT] = "update_coupon_data";
	$r->events[AFTER_UPDATE] = "update_coupon_data";
	$r->events[AFTER_DELETE] = "delete_coupon_data";
	
	$r->process();

	$t->set_var("s", $s);
	$t->set_var("s_a", $s_a);


	$t->set_var("date_added_format", join("", $date_edit_format));
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_coupons_href", "admin_coupons.php");
	$t->set_var("admin_orders_href",  "admin_orders.php");
	$t->set_var("admin_order_href",   $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_vouchers_href", "admin_order_vouchers.php");
	$t->set_var("admin_product_select_href", "admin_product_select.php");


	if ($order_id > 0) {
		$t->parse("orders_path", false);
	} else {
		$t->parse("coupons_path", false);
	}
	
	if ($sitelist) {
		$sites = array();
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
		$db->query($sql);
		while ($db->next_record())	{
			$site_id   = $db->f("site_id");
			$site_name = $db->f("site_name");
			$sites[$site_id] = $site_name;
			$t->set_var("site_id", $site_id);
			$t->set_var("site_name", $site_name);
			if (in_array($site_id, $selected_sites)) {
				$t->parse("selected_sites", true);
			} else {
				$t->parse("available_sites", true);
			}
		}
	}

	$discount_type = $r->get_value("discount_type");
	$products_tab = ($discount_type == 3 || $discount_type == 4);
	$friends_tab = ($discount_type <= 4);

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => EDIT_COUPON_MSG), 
		"restrictions" => array("title" => COUPON_RESTRICTIONS_MSG), 
		"users" => array("title" => USERS_MSG), 
		"products" => array("title" => PRODUCTS_MSG, "show" => $products_tab), 
		"friends" => array("title" => FRIENDS_VISITS_MSG, "show" => $friends_tab), 
		"sites" => array("title" => ADMIN_SITES_MSG, "show" => $sitelist),
	);

	$tabs_in_row = 6; 
	parse_admin_tabs($tabs, $tab, 6);

	if ($sitelist) {
		$t->parse("sitelist");
	}

	$t->pparse("main");
	
	function set_coupon_id()  {
		global $db, $table_prefix, $r;
		global $coupon_id;

		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());

		$sql = "SELECT MAX(coupon_id) FROM " . $table_prefix . "coupons";
		$db->query($sql);
		if($db->next_record()) {
			$coupon_id= $db->f(0) + 1;
			$r->change_property("coupon_id", USE_IN_INSERT, true);
			$r->set_value("coupon_id", $coupon_id);
		}	
	}

	function set_admin_data() {
		global $r;
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_modified", va_time());
	}

	function update_coupon_data()  {
		global $db, $table_prefix, $r;
		global $coupon_id;
		global $sitelist, $selected_sites;
					
		if ($sitelist) {
			$db->query("DELETE FROM " . $table_prefix . "coupons_sites WHERE coupon_id=" . $db->tosql($coupon_id, INTEGER));
			for ($st = 0; $st < sizeof($selected_sites); $st++) {
				$site_id = $selected_sites[$st];
				if (strlen($site_id)) {
					$sql  = " INSERT INTO " . $table_prefix . "coupons_sites (coupon_id, site_id) VALUES (";
					$sql .= $db->tosql($coupon_id, INTEGER) . ", ";
					$sql .= $db->tosql($site_id, INTEGER) . ") ";
					$db->query($sql);
				}
			}
		}

	}

	function delete_coupon_data()  {
		global $db, $table_prefix, $r;
		global $coupon_id;
		$db->query("DELETE FROM " . $table_prefix . "coupons_sites WHERE coupon_id=" . $db->tosql($coupon_id, INTEGER));
	}
	
	
	function set_record_controls()
	{
		global $t, $r, $db, $table_prefix;
		$discount_type = $r->get_value("discount_type");

		if ($r->get_value("order_id") < 1) {
			$r->set_value("order_id", 0);
			$r->set_value("order_item_id", 0);
			$r->change_property("order_item_id", SHOW, false);
		} else {
			$order_items = array();
			$sql  = " SELECT oi.order_item_id,oi.item_name ";
			$sql .= " FROM (" . $table_prefix . "orders_items oi ";
			$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON oi.item_type_id=it.item_type_id) ";
			$sql .= " WHERE oi.order_id=" . $db->tosql($r->get_value("order_id"), INTEGER);
			$sql .= " AND it.is_gift_voucher=1 ";
			$order_items = get_db_values($sql, array(array("", "")));
			$r->change_property("order_item_id", VALUES_LIST, $order_items);
		}
		if ($discount_type <= 2) {
			$discount_types = array( array(1, PERCENTAGE_PER_ORDER_MSG), array(2, AMOUNT_PER_ORDER_MSG) );
			$r->change_property("discount_type", VALUES_LIST, $discount_types);
			$r->change_property("items_ids", SHOW, false);
			$r->change_property("discount_type_text", SHOW, false);
			$r->change_property("discount_type_text", USE_IN_INSERT, false);
			$r->change_property("discount_type_text", USE_IN_UPDATE, false);
			$r->change_property("discount_quantity",SHOW, false);
			$t->set_var("minimum_amount_title", MINIMUM_PURCHASE_AMOUNT_MSG);
			$t->set_var("maximum_amount_title", MAXIMUM_PURCHASE_AMOUNT_MSG);
			$t->set_var("min_quantity_desc", MINIMUM_PURCHASE_AMOUNT_NOTE);
			$t->set_var("max_quantity_desc", MAXIMUM_PURCHASE_AMOUNT_NOTE);
		} else if ($discount_type == 5) {
			$r->change_property("items_ids",     SHOW, false);
			$r->change_property("free_postage",  SHOW, false);
			$r->change_property("coupon_tax_free",SHOW, false);
			$r->change_property("order_tax_free",SHOW, false);
			$r->change_property("discount_quantity",SHOW, false);
			$r->change_property("min_quantity",SHOW, false);
			$r->change_property("max_quantity",SHOW, false);
			$r->change_property("minimum_amount",SHOW, false);
			$r->change_property("maximum_amount",SHOW, false);
			$r->change_property("discount_type", SHOW, false);
			$r->change_property("is_exclusive",  SHOW, false);
			$r->change_property("users_use_limit",SHOW, false);
			$r->change_property("quantity_limit",SHOW, false);
			$r->set_value("quantity_limit", 0);
			$r->change_property("discount_type", USE_IN_INSERT, false);
			$r->change_property("discount_type", USE_IN_UPDATE, false);
		} else  {
			$discount_types = array( array(3, PERCENTAGE_PER_PRODUCT_MSG), array(4, AMOUNT_PER_PRODUCT_MSG));
			$r->change_property("discount_type", VALUES_LIST, $discount_types);
			$r->change_property("free_postage", SHOW, false);
			$r->change_property("coupon_tax_free",SHOW, false);
			$r->change_property("order_tax_free", SHOW, false);
			$r->change_property("discount_type_text", SHOW, false);
			$r->change_property("discount_type_text", USE_IN_INSERT, false);
			$r->change_property("discount_type_text", USE_IN_UPDATE, false);
			$t->set_var("minimum_amount_title", MINIMUM_PRICE_OF_PRODUCT_MSG);
			$t->set_var("maximum_amount_title", MAXIMUM_PRICE_OF_PRODUCT_MSG);
			$t->set_var("min_quantity_desc", MIN_QTY_SAME_PRODUCTS_MSG);
			$t->set_var("max_quantity_desc", MAX_QTY_SAME_PRODUCTS_MSG);

			$items_ids = $r->get_value("items_ids");
			if ($items_ids) {
				$sql  = " SELECT i.item_id, i.item_name ";
				$sql .= " FROM " . $table_prefix . "items i ";
				$sql .= " WHERE i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
				$sql .= " ORDER BY i.item_name ";
				$db->query($sql);
				while($db->next_record())
				{
					$row_item_id = $db->f("item_id");
					$item_name = $db->f("item_name");
		  
					$t->set_var("related_id", $row_item_id);
					$t->set_var("item_name", $item_name);
					$t->set_var("item_name_js", str_replace("\"", "&quot;", $item_name));
		  
					$t->parse("selected_items", true);
					$t->parse("selected_items_js", true);
				}
			}
		}

		if ($discount_type <= 4) {
			// get users ids
			$users_ids = $r->get_value("users_ids");
			if ($users_ids) {
				$sql  = " SELECT user_id, login, email, name, first_name, last_name, nickname, company_name ";
				$sql .= " FROM " . $table_prefix . "users u ";
				$sql .= " WHERE user_id IN (" . $db->tosql($users_ids, INTEGERS_LIST) . ") ";
				$sql .= " ORDER BY name ";
				$db->query($sql);
				while($db->next_record())
				{
					$row_user_id = $db->f("user_id");
					$user_name = $db->f("name");
					if (!strlen($user_name)) { $user_name = trim($db->f("first_name") . " " . $db->f("last_name")); }
					if (!strlen($user_name)) { $user_name = trim($db->f("nickname")); }
					if (!strlen($user_name)) { $user_name = $db->f("company_name"); }
		  
					$t->set_var("user_id", $row_user_id);
					$t->set_var("user_name", $user_name);
					$t->set_var("user_name_js", str_replace("\"", "&quot;", $user_name));
		  
					$t->parse("selected_users", true);
					$t->parse("selected_users_js", true);
				}
			}

			// get friends ids
			$friends_ids = $r->get_value("friends_ids");
			if ($friends_ids) {
				$sql  = " SELECT user_id, login, email, name, first_name, last_name, nickname, company_name ";
				$sql .= " FROM " . $table_prefix . "users u ";
				$sql .= " WHERE user_id IN (" . $db->tosql($friends_ids, INTEGERS_LIST) . ") ";
				$sql .= " ORDER BY name";
				$db->query($sql);
				while($db->next_record())
				{
					$row_user_id = $db->f("user_id");
					$user_name = $db->f("name");
					if (!strlen($user_name)) { $user_name = trim($db->f("first_name") . " " . $db->f("last_name")); }
					if (!strlen($user_name)) { $user_name = trim($db->f("nickname")); }
					if (!strlen($user_name)) { $user_name = $db->f("company_name"); }
		  
					$t->set_var("user_id", $row_user_id);
					$t->set_var("user_name", $user_name);
					$t->set_var("user_name_js", str_replace("\"", "&quot;", $user_name));
		  
					$t->parse("selected_friends", true);
					$t->parse("selected_friends_js", true);
				}
			}
		}

		$friend_controls = array();
		$friend_controls["friends_period"] = "disabled";
		$friend_controls["friends_interval"] = "disabled";
		$friend_controls["friends_min_goods"] = "disabled";
		$friend_controls["friends_max_goods"] = "disabled";
		$friend_controls["friends_all"] = "disabled";
		$friend_controls["friends_users"] = "none";

		if ($r->get_value("friends_discount_type") == 1) {
			$friend_controls["friends_period"] = "active";
			$friend_controls["friends_interval"] = "active";
			$friend_controls["friends_min_goods"] = "active";
			$friend_controls["friends_max_goods"] = "active";
		} else if ($r->get_value("friends_discount_type") == 2) {
			$friend_controls["friends_all"] = "active";
			$friend_controls["friends_users"] = "table-row";
		}
		foreach ($friend_controls as $control_name => $control_type) {
			if ($control_type == "active") {
				$t->set_var($control_name."_disabled", "");
			} else if ($control_type == "disabled") {
				$t->set_var($control_name."_disabled", "disabled");
			} else if ($control_type == "none") {
				$t->set_var($control_name."_style", "display:none;");
			} else if ($control_type == "table-row") {
				$t->set_var($control_name."_style", "display:table-row;");
			}
		}

	}

	function set_coupon_data()  
	{
		global $r, $sitelist;
		if (!$sitelist) {
			$r->set_value("sites_all", 1);
		}
	}

?>