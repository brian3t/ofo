<?php

function coupon_form($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $current_page;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_coupon_form.html");
	$t->set_var("coupon_errors", "");
	$t->set_var("message_block", "");

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$query_string = transfer_params("", true);
	if ($is_ssl) {
		$current_url = $secure_url . $current_page . $query_string;
	} else {
		$current_url = $site_url . $current_page . $query_string;
	}

  $t->set_var("current_url", $current_url);

	$coupons_applied = 0;
	$coupon_code = get_param("coupon_code");
	$coupon_operation = get_param("coupon_operation");
	$coupon_errors = ""; 
	if($coupon_operation == "add")
	{
		
		if(!strlen($coupon_code)) {
			$error_message = str_replace("{field_name}", COUPON_CODE_FIELD, REQUIRED_MESSAGE);
			$coupon_errors .= $error_message . "<br>";
		}
	  
		if(!strlen($coupon_errors)) {
			$sql  = " SELECT c.* FROM (" . $table_prefix . "coupons c";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN  " . $table_prefix . "coupons_sites s ON (s.coupon_id=c.coupon_id AND c.sites_all=0))";
			} else {
				$sql .= ")";
			}
			$sql .= " WHERE c.coupon_code=" . $db->tosql($coupon_code, TEXT);
			if (isset($site_id)) {
				$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			} else {
				$sql .= " AND c.sites_all=1 ";
			}				
			$db->query($sql);
			if ($db->next_record()) {
				$coupons_applied = check_add_coupons(true, $coupon_code, $coupon_errors);
			} else {
				$coupon_errors = COUPON_NOT_FOUND_MSG;
			}
		}
	}


	if(strlen($coupon_errors))
	{
		$t->set_var("coupon_code", htmlspecialchars($coupon_code));
		$t->set_var("errors_list", $coupon_errors);
		$t->parse("coupon_errors", false);
	} else if ($coupons_applied) {
		$t->set_var("coupon_message", COUPON_ADDED_MSG);
		$t->parse("message_block", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>