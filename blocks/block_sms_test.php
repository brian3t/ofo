<?php

function sms_test_form($block_name)
{
	global $t, $db, $table_prefix;
	global $category_id;
	global $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_sms_test.html");

	$error_desc   = "";
	$phone_number  = get_param("phone_number");
	$query_string = transfer_params("", true);
	
	$t->set_var("query_string", $query_string);
	$sms_test_desc = str_replace("SEND_BUTTON", SEND_BUTTON, SMS_TEST_DESC);
	$t->set_var("SMS_TEST_DESC", $sms_test_desc);

	if (strlen($phone_number)) {
		if(preg_match("/^\+?\d+$/", $phone_number)) {
			sms_send($phone_number, get_setting_value($page_settings, "sms_test_message", ""), get_setting_value($page_settings, "sms_originator", ""));
		} else {
			$error_desc = INVALID_CELL_PHONE;
		}
	}

	if ($error_desc) {
		$t->set_var("phone_number", htmlspecialchars($phone_number));
		$t->set_var("error_desc", $error_desc);
		$t->parse("sms_error", false);
	} else {
		$t->set_var("phone_number", "");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>