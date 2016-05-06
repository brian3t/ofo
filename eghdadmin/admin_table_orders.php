<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_table_orders.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/



	$table_name = $table_prefix . "orders";
	$table_alias = "o";
	$table_pk = "order_id";
	$table_title = ORDERS_MSG;
	$min_column_allowed = 5;

	$db_columns = array(
		"order_id" => array(ORDER_NUMBER_MSG, INTEGER, 1, false),
		"parent_order_id" => array(PARENT_ORDER_NUMBER_MSG, INTEGER, 2, false),
		
		"invoice_number" => array(INVOICE_NUMBER_MSG, TEXT, 2, false),
		"transaction_id" => array(TRANSACTION_ID_MSG, TEXT, 2, false),
		"authorization_code" => array(AUTHORIZATION_CODE_MSG, TEXT, 2, false),
		"user_id" => array(USER_ID_MSG, INTEGER, 2, false),
		"user_type_id" => array(USER_TYPE_ID_MSG, INTEGER, 2, false),
		"affiliate_code" => array(AFFILIATE_CODE_FIELD, TEXT, 4, false, ''),
		"affiliate_user_id" => array(AFFILIATE_USER_ID_MSG, INTEGER, 2, false),

		"payment_id" => array(PAYMENT_ID_MSG, INTEGER, 2, false),

		"success_message" => array(SUCCESS_MESSAGE_MSG, TEXT, 2, false),
		"pending_message" => array(PENDING_MESSAGE_MSG, TEXT, 2, false),
		"error_message" => array(ERROR_MESSAGE_MSG, TEXT, 2, false),
		"visit_id" => array(VISIT_ID_MSG, INTEGER, 2, false),
		"remote_address" => array(REMOTE_IP_MSG, TEXT, 2, false),
		"initial_ip" => array(INITIAL_IP_MSG, TEXT, 2, false),
		"cookie_ip" => array(COOKIE_IP_MSG, TEXT, 2, false),

		"keywords" => array(KEYWORDS_MSG, TEXT, 2, false),
		"coupons_ids" => array(COUPONS_NUMBER_MSG, TEXT, 2, false),
		"currency_code" => array(CURRENCY_CODE_MSG, TEXT, 2, false),
		"currency_rate" => array(CURRENCY_RATE_MSG, FLOAT, 2, false),

		"avs_response_code" => array(AVS_RESPONSE_CODE_MSG, TEXT, 2, false),
		"avs_message" => array(AVS_MESSAGE_MSG, TEXT, 2, false),
		"avs_address_match" => array(AVS_ADDRESS_MSG, TEXT, 2, false),
		"avs_zip_match" => array(AVS_ZIP_MATCH_MSG, TEXT, 2, false),
		"cvv2_match" => array(CVV2_MATCH_MSG, TEXT, 2, false),
		"secure_3d_check" => array(D_SECURE_CHECK_MSG, TEXT, 2, false),
		"secure_3d_status" => array(D_SECURE_STATUS_MSG, TEXT, 2, false),
		"secure_3d_md" => array(D_SECURE_MD_MSG, TEXT, 2, false),
		"secure_3d_eci" => array(D_SECURE_ECI_MSG, TEXT, 2, false),
		"secure_3d_cavv" => array(D_SECURE_CAVV_MSG, TEXT, 2, false),
		"secure_3d_xid" => array(D_SECURE_XID_MSG, TEXT, 2, false),

		"order_status" => array(STATUS_ID_MSG, INTEGER, 3, true),
		"total_buying" => array(TOTAL_BUYING_MSG, FLOAT, 2, true),
		"total_buying_tax" => array(TOTAL_BUYING_TAX_MSG, FLOAT, 2, true, 0), 
		"goods_total" => array(GOODS_TOTAL_MSG, FLOAT, 2, true),
		"goods_tax" => array(GOODS_TOTAL_TAX_MSG, FLOAT, 2, true, 0),
		"goods_points_amount" => array(GOODS_POINTS_COST_MSG, FLOAT, 2, true, 0),
		"total_quantity" => array(TOTAL_GOODS_QTY_MSG, INTEGER, 3, true),
		"weight_total" => array(WEIGHT_TOTAL_MSG, FLOAT, 2, false),
		"total_discount" => array(TOTAL_DISCOUNT_MSG, FLOAT, 2, false),
		"total_discount_tax" => array(TOTAL_DISCOUNT_TAX_MSG, FLOAT, 2, false),
		"shipping_type_id" => array(SHIPPING_ID_MSG, INTEGER, 2, false),
		"shipping_type_code" => array(SHIPPING_CODE_MSG, TEXT, 2, false),
		"shipping_type_desc" => array(SHIPPING_DESCRIPTION_MSG, TEXT, 2, false),
		"shipping_cost" => array(SHIPPING_COST_MSG, FLOAT, 2, false),
		"shipping_taxable" => array(IS_SHIPPINGTAXABLE_MSG, INTEGER, 2, false),
		"shipping_points_amount" => array(SHIPPING_POIUNTS_AMOUNT_MSG, FLOAT, 2, false),
		"shipping_tracking_id" => array(SHIPPING_TRACKING_MSG, TEXT, 2, false),
		"shipping_expecting_date" => array(SHIPPING_EXPECTING_MSG, DATETIME, 2, false),
		"properties_total" => array(PROPERTIES_TOTAL_MSG, FLOAT, 2, false),
		"properties_points_amount" => array(PROPERTIES_POINTS_COST_MSG, FLOAT, 2, false),
		"tax_name" => array(TAX_NAME_MSG, TEXT, 2, false),
		"tax_percent" => array(TAX_PERCENT_MSG, FLOAT, 2, false),
		"tax_total" => array(TAX_TOTAL_MSG, FLOAT, 2, false),
		"total_points_amount" => array(TOTAL_POINTS_COST_MSG, FLOAT, 2, false, 0),
		"total_reward_points" => array(TOTAL_REWARDS_POINTS_MSG, FLOAT, 2, false, 0),
		"total_reward_credits" => array(REWARD_CREDITS_TOTAL_MSG, FLOAT, 2, false, 0),
		"total_merchants_commission" => array(TOTAL_MERCHANT_COMMISSIONS_MSG, FLOAT, 2, true, 0),
		"total_affiliate_commission" => array(TOTAL_AFFILIATE_COMMISSION_MSG, FLOAT, 2, true, 0),
		"credit_amount" => array(CREDIT_AMOUNT_MSG, FLOAT, 2, false),
		"processing_fee" => array(PROCESSING_FEE_MSG, FLOAT, 2, false),
		"order_total" => array(ADMIN_ORDER_TOTAL_MSG, FLOAT, 2, true),

		"name" => array(NAME_MSG, TEXT, 2, false),
		"first_name" => array(FIRST_NAME_FIELD, TEXT, 2, false),
		"last_name" => array(LAST_NAME_FIELD, TEXT, 2, false),
		"company_id" => array(COMPANY_ID_MSG, INTEGER, 3, false),
		"company_name" => array(COMPANY_NAME_FIELD, TEXT, 2, false),
		"email" => array(EMAIL_FIELD, TEXT, 2, false, ''),
		"address1" => array(STREET_ADDRESS_FIRST_LINE_MSG, TEXT, 2, false),
		"address2" => array(STREET_ADDRESS_SECOND_LINE_MSG, TEXT, 2, false),
		"city" => array(CITY_FIELD, TEXT, 2, false),
		"province" => array(PROVINCE_FIELD, TEXT, 2, false),
		"state_id" => array(STATE_FIELD, INTEGER, 2, false, ''),
		"state_code" => array(STATE_CODE_MSG, TEXT, 2, false, ''),
		"zip" => array(ZIP_MSG, TEXT, 2, false),
		"country_id" => array(COUNTRY_FIELD, INTEGER, 2, false, ''),
		"country_code" => array(COUNTRY_CODE_MSG, TEXT, 2, false, ''),
		"phone" => array(PHONE_FIELD, TEXT, 2, false),
		"daytime_phone" => array(DAYTIME_PHONE_FIELD, TEXT, 2, false),
		"evening_phone" => array(EVENING_PHONE_FIELD, TEXT, 2, false),
		"cell_phone" => array(CELL_PHONE_FIELD, TEXT, 2, false),
		"fax" => array(FAX_FIELD, TEXT, 2, false),

		"delivery_name" => array(DELIVERY_NAME_MSG, TEXT, 2, false),
		"delivery_first_name" => array(DELIVERY_FIRST_NAME_MSG, TEXT, 2, false),
		"delivery_last_name" => array(DELIVERY_LAST_NAME_MSG, TEXT, 2, false),
		"delivery_company_id" => array(DELIVERY_COMPANY_ID_MSG, INTEGER, 3, false),
		"delivery_company_name" => array(DELIVERY_COMPANY_NAME_MSG, TEXT, 2, false),
		"delivery_email" => array(DELIVERY_EMAIL_MSG, TEXT, 2, false),
		"delivery_address1" => array(DELIVERY_ADDRESS_FIRST_MSG, TEXT, 2, false),
		"delivery_address2" => array(DELIVERY_ADDRESS_SEC_MSG, TEXT, 2, false),
		"delivery_city" => array(DELIVERY_CITY_MSG, TEXT, 2, false),
		"delivery_province" => array(DELIVERY_PROVINCE_MSG, TEXT, 2, false),
		"delivery_state_id" => array(DELIVERY_STATE_MSG, INTEGER, 2, false, ''),
		"delivery_state_code" => array(DELIVERY_STATE_CODE_MSG, TEXT, 2, false, ''),
		"delivery_zip" => array(DELIVERY_ZIP_MSG, TEXT, 2, false),
		"delivery_country_id" => array(DELIVERY_COUNTRY_MSG, INTEGER, 2, false, ''),
		"delivery_country_code" => array(DELIVERY_COUNTRY_CODE_MSG, TEXT, 2, false, ''),
		"delivery_phone" => array(DELIVERY_PHONE_MSG, TEXT, 2, false),
		"delivery_daytime_phone" => array(DELIVERY_DAY_PHONE_MSG, TEXT, 2, false),
		"delivery_evening_phone" => array(DELIVERY_EVENING_PHONE_MSG, TEXT, 2, false),
		"delivery_cell_phone" => array(DELIVERY_CELL_PHONE_MSG, TEXT, 2, false),
		"delivery_fax" => array(DELIVERY_FAX_MSG, TEXT, 2, false),

		"cc_name" => array(CC_NAME_FIELD, TEXT, 2, false),
		"cc_first_name" => array(FIRST_NAME_FIELD, TEXT, 2, false),
		"cc_last_name" => array(LAST_NAME_FIELD, TEXT, 2, false),
		"cc_number" => array(CC_NUMBER_FIELD, TEXT, 2, false),
		"cc_start_date" => array(CC_START_DATE_FIELD, DATETIME, 2, false),
		"cc_expiry_date" => array(EXPIRY_DATE_MSG, DATETIME, 2, false),
		"cc_type" => array(CARD_TYPE_ID, INTEGER, 2, false),
		"cc_issue_number" => array(CC_ISSUE_NUMBER_FIELD, INTEGER, 2, false),
		"cc_security_code" => array(CC_SECURITY_CODE_FIELD, TEXT, 2, false),
		"pay_without_cc" => array(PAY_WITHOUT_CREDIT_MSG, TEXT, 2, false),

		"order_placed_date" => array(ORDER_DATE_MSG, DATETIME, 2, true),
		"modified_date" => array(MODIFIED_DATE_MSG, DATETIME, 4, false),
		"is_placed" => array(IS_PLACED_MSG, INTEGER, 2, false),
		"is_exported" => array(IS_EXPORTED_MSG, INTEGER, 2, false),
		"is_call_center" => array(IS_CALLCENTER_MSG, INTEGER, 2, false),
		"is_recurring" => array(IS_RECURRING_MSG, INTEGER, 2, false),
	);
	$sql = " SELECT property_id, property_name FROM " . $table_prefix . "orders_properties GROUP BY property_id, property_name ";
	$db->query($sql);
	while ($db->next_record()) {
		$property_id = $db->f("property_id");
		$property_name = $db->f("property_name");
		$db_columns["order_property_" . $property_id] = array(get_translation($property_name), TEXT, 2, false);
	}

	$db_aliases["id"] = "order_id";

	$related_table = "orders_items";
	$related_table_name = $table_prefix . "orders_items ";
	$related_table_alias = "oi";
	$related_table_title = ORDERS_ITEMS_MSG;

	$related_columns = array(
		"order_item_id" => array(ORDER_PRODUCT_ID_MSG, INTEGER, HIDE_DB_FIELD, false),
		"item_id" => array(PRODUCT_ID_MSG, INTEGER, 2, false, ''),
		"item_code" => array(PROD_CODE_MSG, TEXT, 2, false),
		"manufacturer_code" => array(MANUFACTURER_CODE_MSG, TEXT, 2, false),
		"item_name" => array(PROD_NAME_MSG, TEXT, 2, false),
		"item_properties" => array(PRODUCT_OPTIONS_MSG, TEXT, 2, false)
	);
	
	$sql  = " SELECT property_id, property_name ";
	$sql .= " FROM " . $table_prefix . "orders_items_properties ";
	if (isset($is_export) && $is_export) {
		$sql .= " WHERE property_id IN (SELECT property_id FROM " . $table_prefix . "orders_items_properties ";
		if (get_param("ids")) {
			$sql .= " WHERE order_id IN (" . $db->tosql(get_param("ids"), INTEGERS_LIST) . ")";
		}
		$sql .= " GROUP BY property_id)";
		$sql .= " GROUP BY property_id, property_name  ";
	} else {
		$sql .= " GROUP BY property_name  ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$property_id = $db->f("property_id");
		$property_name = $db->f("property_name");
		$related_columns["order_item_property_" . $property_id] = array(PRODUCT_OPTION_MSG."(" . get_translation($property_name) . ")", TEXT, 2, false);
	}
	$related_columns["buying_price"] = array(PROD_BUYING_PRICE_MSG, NUMBER, 2, false);
	$related_columns["price"] = array(PRODUCT_SELLING_PRICE_MSG, NUMBER, 2, false);
	$related_columns["weight"] = array(PRODUCT_WEIGHT_MSG, NUMBER, 2, false);
	$related_columns["width"] = array(WIDTH_MSG, NUMBER, 2, false);
	$related_columns["height"] = array(HEIGHT_MSG, NUMBER, 2, false);
	$related_columns["length"] = array(LENGTH_MSG, NUMBER, 2, false);
	$related_columns["quantity"] = array(PRODUCT_QUANTITY_MSG, INTEGER, 2, false);
	$related_columns["downloadable"] = array(IS_PRODUCT_DOWNLOADABLE_MSG, INTEGER, 2, false);
	$related_columns["coupons_ids"] = array(PRODUCT__COUPONS_MSG, TEXT, 2, false);
	$related_columns["real_price"] = array(PRODUCT_REAL_PRICE_MSG, NUMBER, 2, false);
	$related_columns["discount_amount"] = array(PRODUCT_DISCOUNT_MSG, NUMBER, 2, false);
	$related_columns["is_shipping_free"] = array(FREE_SHIPPING_MSG, INTEGER, 2, false);
	$related_columns["shipping_cost"] = array(SHIPPING_COST_MSG, NUMBER, 2, false);

	$related_aliases["id"] = "order_item_id";

	$related_columns_from_main = array("user_id", "user_type_id");
?>