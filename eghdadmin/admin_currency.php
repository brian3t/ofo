<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_currency.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("static_tables");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_currency.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_currencies_href", "admin_currencies.php");
	$t->set_var("admin_currency_href", "admin_currency.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CURRENCY_TITLE, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "currencies");
	$r->return_page  = "admin_currencies.php";

	$yes_no = 
		array( 
			array(1, YES_MSG), array(0, NO_MSG)
		);

	$r->add_where("currency_id", INTEGER);
	$r->add_checkbox("is_default", INTEGER);
	$r->add_checkbox("recalculate_prices", INTEGER);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->add_checkbox("is_default_show", INTEGER);
	$r->change_property("recalculate_prices", USE_IN_SELECT, false);
	$r->change_property("recalculate_prices", USE_IN_INSERT, false);
	$r->change_property("recalculate_prices", USE_IN_UPDATE, false);

	$r->add_textbox("currency_code", TEXT, CURRENCY_CODE_MSG);
	$r->change_property("currency_code", REQUIRED, true);
	$r->change_property("currency_code", UNIQUE, true);
	$r->change_property("currency_code", MIN_LENGTH, 3);
	$r->change_property("currency_code", MAX_LENGTH, 3);
	$r->add_textbox("currency_value", TEXT);
	$r->add_textbox("currency_title", TEXT, CURRENCY_TITLE_MSG);
	$r->change_property("currency_title", REQUIRED, true);
	$r->add_textbox("currency_image", TEXT, CURRENCY_IMAGE_MSG);
	$r->add_textbox("currency_image_active", TEXT, CURRENCY_IMAGE_ACTIVE_MSG);
	$r->add_textbox("exchange_rate", NUMBER, EXCHANGE_RATE_MSG);
	$r->change_property("exchange_rate", REQUIRED, true);
	$r->change_property("exchange_rate", DEFAULT_VALUE, 1);
	$r->add_textbox("symbol_left", TEXT);
	$r->add_textbox("symbol_right", TEXT);
	$r->add_textbox("decimals_number", INTEGER, NUMBER_OF_DECIMALS_MSG);
	$r->change_property("decimals_number", MIN_VALUE, 0);
	$r->add_textbox("decimal_point", TEXT, DECIMAL_POINT_MSG);
	$r->change_property("decimal_point", MAX_LENGTH, 1);
	$r->add_textbox("thousands_separator", TEXT, THOUSANDS_SEPARATOR_MSG);
	$r->change_property("thousands_separator", MAX_LENGTH, 1);

	$r->events[BEFORE_INSERT] = "recalculate_rates";
	$r->events[BEFORE_UPDATE] = "recalculate_rates";
	$r->process();

	$t->set_var("date_added_format", join("", $date_edit_format));
	$t->pparse("main");

	function recalculate_rates()
	{
		global $r, $db, $table_prefix;
		$exchange_rate = $r->get_value("exchange_rate");
		if ($r->get_value("is_default") == 1) 
		{
			if ($exchange_rate != 1) {
				if ($r->get_value("recalculate_prices") == 1) {
					$sql  = " UPDATE " . $table_prefix . "items SET ";
					$sql .= " buying_price=buying_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " sales_price=sales_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " trade_sales=trade_sales*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " price=price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " trade_price=trade_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " properties_price=properties_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " trade_properties_price=trade_properties_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " shipping_cost=shipping_cost*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " shipping_trade_cost=shipping_trade_cost*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$db->query($sql);
		  
					$sql  = " UPDATE " . $table_prefix . "items_properties SET ";
					$sql .= " additional_price=additional_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " trade_additional_price=trade_additional_price*" . $db->tosql($exchange_rate, NUMBER);
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "items_properties_values SET ";
					$sql .= " buying_price=buying_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " additional_price=additional_price*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " trade_additional_price=trade_additional_price*" . $db->tosql($exchange_rate, NUMBER);
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "items_prices SET ";
					$sql .= " price=price*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "prices SET ";
					$sql .= " price_amount=price_amount*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "shipping_types SET ";
					$sql .= " min_goods_cost=min_goods_cost*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " max_goods_cost=max_goods_cost*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " cost_per_order=cost_per_order*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " cost_per_product=cost_per_product*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " cost_per_weight=cost_per_weight*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "saved_carts SET ";
					$sql .= " cart_total=cart_total*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "saved_items SET ";
					$sql .= " price=price*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "ads_items SET ";
					$sql .= " price=price*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$db->query($sql);

					$sql  = " UPDATE " . $table_prefix . "coupons SET ";
					$sql .= " discount_amount=discount_amount*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " minimum_amount=minimum_amount*" . $db->tosql($exchange_rate, NUMBER) . ", ";
					$sql .= " maximum_amount=maximum_amount*" . $db->tosql($exchange_rate, NUMBER) . " ";
					$sql .= " WHERE discount_type IN (2,4,5) ";
					$db->query($sql);
				}
		  
				$sql  = " UPDATE " . $table_prefix . "currencies SET exchange_rate=exchange_rate/" . $db->tosql($exchange_rate, NUMBER);
				$db->query($sql);
				$r->set_value("exchange_rate", 1);
			}

			$sql  = " UPDATE " . $table_prefix . "currencies SET is_default=0 ";
			$db->query($sql);
			set_session("session_currency", "");
		}
		if ($r->get_value("is_default_show") == 1) 
		{
			$sql  = " UPDATE " . $table_prefix . "currencies SET is_default_show=0 ";
			$db->query($sql);
			set_session("session_currency", "");
		}
	}

?>