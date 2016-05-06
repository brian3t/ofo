<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ideal_confirm.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * iDEAL (www.ing-ideal.nl) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once($root_folder_path . "includes/var_definition.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/date_functions.php");
	include_once($root_folder_path . "includes/common_functions.php");
	include_once($root_folder_path . "includes/va_functions.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path . "payments/ideal_functions.php");

	check_admin_security("update_orders");

	$t = new VA_Template("");

	$sql  = " SELECT payment_id FROM " . $table_prefix . "payment_systems";
	$sql .= " WHERE payment_name LIKE '%iDEAL%'";
	$db->query($sql);
	if ($db->num_rows() == 1) {
		$db->next_record();
		$payment_id = $db->f("payment_id");
	} else {
		exit;
	}

	$order_ids = array();
	$sql  = " SELECT order_id, order_placed_date, transaction_id, order_status, payment_id FROM " . $table_prefix . "orders";
	$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER) . " AND order_status=1 ";
	$sql .= " AND order_placed_date <=" . $db->tosql(time() - 600, DATETIME);
	$sql .= " AND order_placed_date >=" . $db->tosql(time() - 172800, DATETIME);
	$db->query($sql);
	if ($db->num_rows() != 0){
		while ($db->next_record()) {
			$order_ids[] = array($db->f("order_id"), $db->f("transaction_id"));
		}
	} else {
		exit;
	}

	$order_final = array();
	$setting_type = "order_final_" . $payment_id;
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	$db->query($sql);
	while ($db->next_record()) {
		$order_final[$db->f("setting_name")] = $db->f("setting_value");
	}
	$success_status_id = get_setting_value($order_final, "success_status_id", "");
	$failure_status_id = get_setting_value($order_final, "failure_status_id", "");
	$pending_status_id = get_setting_value($order_final, "pending_status_id", "");

	$payment_params = array(); $pass_parameters = array();
	$sql = " SELECT parameter_name, parameter_source FROM " . $table_prefix . "payment_parameters WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		$parameter_name = trim($db->f("parameter_name"));
		$parameter_source = trim($db->f("parameter_source"));
		$payment_params[$parameter_name] = $parameter_source;
	}

	$error_message = "";
	$success_message = "";

	foreach ($order_ids as $value) {
		$transaction_id = $value[1];
		$order_id = $value[0];
		if (!strlen($transaction_id)) {
			$error_message = "Transaction ID is absent.";
			echo "| " . $order_id . " | " . $error_message . " | null | status - failure<hr>\n";
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET order_status=" . $db->tosql($failure_status_id, INTEGER);
			$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			send_mail();
			$error_message = "";
		} else {
			set_status();
			if (strlen($error_message)) {
				echo "| " . $order_id . " | " . $error_message . " | " . $transaction_id . " | status - failure<hr>\n";
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET order_status=" . $db->tosql($failure_status_id, INTEGER);
				$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
				send_mail();
				$error_message = "";
			}
			if (strlen($success_message)) {
				echo "| " . $order_id . " | " . $success_message . " | " . $transaction_id . " | status - success<hr>\n";
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET order_status=" . $db->tosql($success_status_id, INTEGER);
				$sql .= " , success_message=" . $db->tosql($success_message, TEXT);
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
				send_mail();
				$success_message = "";
			}
		}
	}

	function send_mail()
	{
		global $error_message, $success_message, $order_final, $order_id, $table_prefix, $db;
		global $currency_left, $currency_rate, $currency_right, $datetime_show_format;
		global $settings, $cart_items, $total_items, $t;
		global $cart_properties, $personal_properties, $delivery_properties, $payment_properties;

		$pending_message="";

		// get download links
		$links = get_order_links($order_id);
		$t->set_var("links", $links["html"]);

		// get serial numbers
		$order_serials = get_serial_numbers($order_id);
		$t->set_var("serials", $order_serials["html"]);
		$t->set_var("serial_numbers", $order_serials["html"]);

		// get gift vouchers
		$order_vouchers = get_gift_vouchers($order_id);
		$t->set_var("vouchers", $order_vouchers["html"]);
		$t->set_var("gift_vouchers", $order_vouchers["html"]);

		$is_failed = false; $is_pending = false; $is_success = false;
		if (strlen($error_message)) {
			$is_failed = true;
			$message_type = "failure";
			$final_title   = CHECKOUT_ERROR_TITLE;
			$final_message = get_setting_value($order_final, "failure_message", CHECKOUT_ERROR_MSG);
			$t->set_var("error_desc", $error_message);
			$t->set_var("error_message", $error_message);
		} else if (strlen($pending_message)) {
			$is_pending = true;
			$message_type = "pending";
			$final_title   = CHECKOUT_PENDING_TITLE;
			$final_message = get_setting_value($order_final, "pending_message", CHECKOUT_PENDING_MSG);
			$t->set_var("pending_desc", $pending_message);
			$t->set_var("pending_message", $pending_message);
		} else {
			$is_success = true;
			$message_type = "success";
			$final_title   = CHECKOUT_SUCCESS_TITLE;
			$final_message = get_setting_value($order_final, "success_message", CHECKOUT_SUCCESS_MSG);
		}


		// set orders variables
		$sql = "SELECT * FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			// email variables
			$email = $db->f("email");
			$delivery_email = $db->f("delivery_email");
			// shopping variables
			$goods_total = $db->f("goods_total");
			$total_discount = $db->f("total_discount");
			$goods_with_discount = $goods_total - $total_discount;
			$shipping_cost = $db->f("shipping_cost");
			$tax_percent = $db->f("tax_percent");
			$tax_total = $db->f("tax_total");
			$order_total = $db->f("order_total");
			$order_placed_date = $db->f("order_placed_date", DATETIME);
			$cc_start_date = $db->f("cc_start_date", DATETIME);
			$cc_expiry_date = $db->f("cc_expiry_date", DATETIME);
			$cc_type = $db->f("cc_type");

			// info variables
			$company_id = $db->f("company_id");
			$state_code = $db->f("state_code");
			$country_code = $db->f("country_code");
			$delivery_company_id = $db->f("delivery_company_id");
			$delivery_state_code = $db->f("delivery_state_code");
			$delivery_country_code = $db->f("delivery_country_code");
			$t->set_vars($db->Record);

			$t->set_var("goods_total", $currency_left . number_format($goods_total * $currency_rate, 2) . $currency_right);
			$t->set_var("total_discount", $currency_left . number_format($total_discount * $currency_rate, 2) . $currency_right);
			$t->set_var("goods_with_discount", $currency_left . number_format($goods_with_discount * $currency_rate, 2) . $currency_right);
			$t->set_var("shipping_cost", $currency_left . number_format($shipping_cost * $currency_rate, 2) . $currency_right);
			$t->set_var("tax_percent", number_format($tax_percent, 2) . "%");
			$t->set_var("tax_total", $currency_left . number_format($tax_total * $currency_rate, 2) . $currency_right);
			$t->set_var("tax_cost", $currency_left . number_format($tax_total * $currency_rate, 2) . $currency_right);
			$t->set_var("order_total", $currency_left . number_format($order_total * $currency_rate, 2) . $currency_right);
			$t->set_var("order_placed_date", va_date($datetime_show_format, $order_placed_date));
			$t->set_var("cc_start_date", va_date(array("MM", " / ", "YYYY"), $cc_start_date));
			$t->set_var("cc_expiry_date", va_date(array("MM", " / ", "YYYY"), $cc_expiry_date));

			if ($cc_type) {
				$t->set_var("cc_type", get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($cc_type, INTEGER)));
			}
			if ($company_id) {
				$t->set_var("company_select", get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($company_id, INTEGER)));
			}
			if ($state_code) {
				$t->set_var("state", get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_code=" . $db->tosql($state_code, TEXT)));
			}
			if ($country_code) {
				$t->set_var("country", get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_code=" . $db->tosql($country_code, TEXT)));
			}
			if ($delivery_company_id) {
				$t->set_var("delivery_company_select", get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($delivery_company_id, INTEGER)));
			}
			if ($delivery_state_code) {
				$t->set_var("delivery_state", get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_code=" . $db->tosql($delivery_state_code, TEXT)));
			}
			if ($delivery_country_code) {
				$t->set_var("delivery_country", get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_code=" . $db->tosql($delivery_country_code, TEXT)));
			}
		}

		// get admin notify
		$admin_notification   = get_setting_value($order_final, "admin_notification",   0);
		$admin_pending_notify = get_setting_value($order_final, "admin_pending_notify", 0);
		$admin_failure_notify = get_setting_value($order_final, "admin_failure_notify", 0);

		// get user notify
		$user_notification   = get_setting_value($order_final, "user_notification",   0);
		$user_pending_notify = get_setting_value($order_final, "user_pending_notify", 0);
		$user_failure_notify = get_setting_value($order_final, "user_failure_notify", 0);

		if (($is_success && $admin_notification) || ($is_pending && $admin_pending_notify) || ($is_failed && $admin_failure_notify))
		{
			$admin_subject = get_final_message($order_final["admin_subject"], $message_type);
			$admin_message = get_final_message($order_final["admin_message"], $message_type);
			$t->set_block("admin_subject", $admin_subject);
			$t->set_block("admin_message", $admin_message);

			$items_text = show_order_items($order_id, true);

			$mail_to = get_setting_value($order_final, "admin_email", $settings["admin_email"]);
			$mail_to = str_replace(";", ",", $mail_to);
			$email_headers = array();
			$email_headers["from"] = get_setting_value($order_final, "admin_mail_from", $settings["admin_email"]);
			$email_headers["cc"] = get_setting_value($order_final, "cc_emails");
			$email_headers["bcc"] = get_setting_value($order_final, "admin_mail_bcc");
			$email_headers["reply_to"] = get_setting_value($order_final, "admin_mail_reply_to");
			$email_headers["return_path"] = get_setting_value($order_final, "admin_mail_return_path");
			$email_headers["mail_type"] = get_setting_value($order_final, "admin_message_type");

			if (!$email_headers["mail_type"]) {
				$t->set_var("basket", $items_text);
				$t->set_var("links",  $links["text"]);
				$t->set_var("serials", $order_serials["text"]);
				$t->set_var("serial_numbers", $order_serials["text"]);
				$t->set_var("vouchers", $order_vouchers["text"]);
				$t->set_var("gift_vouchers", $order_vouchers["text"]);
			}

			$t->parse("admin_subject", false);
			$t->parse("admin_message", false);
			$admin_message = str_replace("\r", "", $t->get_var("admin_message"));
			va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
		}

		if (($is_success && $user_notification) || ($is_pending && $user_pending_notify) || ($is_failed && $user_failure_notify))
		{
			$user_subject = get_final_message($order_final["user_subject"], $message_type);
			$user_message = get_final_message($order_final["user_message"], $message_type);
			$t->set_block("user_subject", $user_subject);
			$t->set_block("user_message", $user_message);

			$items_text = show_order_items($order_id, true);

			$email_headers = array();
			$email_headers["from"] = get_setting_value($order_final, "user_mail_from", $settings["admin_email"]);
			$email_headers["cc"] = get_setting_value($order_final, "user_mail_cc");
			$email_headers["bcc"] = get_setting_value($order_final, "user_mail_bcc");
			$email_headers["reply_to"] = get_setting_value($order_final, "user_mail_reply_to");
			$email_headers["return_path"] = get_setting_value($order_final, "user_mail_return_path");
			$email_headers["mail_type"] = get_setting_value($order_final, "user_message_type");

			if (!$email_headers["mail_type"]) {
				$t->set_var("basket", $items_text);
				$t->set_var("links",  $links["text"]);
				$t->set_var("serials", $order_serials["text"]);
				$t->set_var("serial_numbers", $order_serials["text"]);
				$t->set_var("vouchers", $order_vouchers["text"]);
				$t->set_var("gift_vouchers", $order_vouchers["text"]);
			}

			$t->parse("user_subject", false);
			$t->parse("user_message", false);
			$user_email = strlen($email) ? $email : $delivery_email;
			$user_message = str_replace("\r", "", $t->get_var("user_message"));
			va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers);
		}
	}

	function set_status()
	{
		global $payment_params, $transaction_id, $error_message, $success_message;

		$timestamp = gmdate("Y") . "-" . gmdate("m") . "-" . gmdate("d") . "T" . gmdate("H") . ":" . gmdate("i") . ":" . gmdate("s") . ".000Z";
		$token = "";
		$tokenCode = "";
		if ("SHA1_RSA" == $payment_params["AuthenticationType"]) {
			$message = $timestamp . $payment_params["MerchantID"] . $payment_params["SubID"] . $transaction_id;
			$message = ideal_stripsimbls( $message );

			$token = ideal_createCertFingerprint($payment_params["Privatecert"]);
			$tokenCode = ideal_signMessage( $payment_params["Privatekey"], $payment_params["PrivatekeyPass"], $message );
			$tokenCode = base64_encode( $tokenCode );
		}
		$reqMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
		. "<AcquirerStatusReq xmlns=\"http://www.idealdesk.com/Message\" version=\"1.1.0\">\n"
		. "<createDateTimeStamp>" . xml_escape_string($timestamp) . "</createDateTimeStamp>\n"
		. "<Merchant>" . "<merchantID>" . xml_escape_string($payment_params["MerchantID"]) . "</merchantID>\n"
		. "<subID>" . xml_escape_string($payment_params["SubID"]) . "</subID>\n"
		. "<authentication>" . xml_escape_string($payment_params["AuthenticationType"]) . "</authentication>\n"
		. "<token>" . xml_escape_string($token) . "</token>\n"
		. "<tokenCode>" . xml_escape_string($tokenCode) . "</tokenCode>\n"
		. "</Merchant>\n"
		. "<Transaction>" . "<transactionID>" . xml_escape_string($transaction_id) . "</transactionID>\n"
		. "</Transaction>" . "</AcquirerStatusReq>";

		$answer = ideal_PostToHost($payment_params["AcquirerURL"], $payment_params["AcquirerTimeout"], $reqMsg);

		$response_parameters = array();
		preg_match_all ("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $answer, $matches, PREG_SET_ORDER);
		for ($i = 0; $i < sizeof($matches); $i++) {
			$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
		}

		if (isset($response_parameters["errorCode"])) {
			if (!(isset($response_parameters["errorMessage"])) || !(strlen($response_parameters["errorMessage"]))){
				$error_message = "Your transaction has been declined.";
			} else {
				$error_message = $response_parameters["errorMessage"];
			}
			return;
		}

		if (!(isset($response_parameters["status"]))) {
			$error_message = "Your transaction has been declined.";
			return;
		}

		if ($response_parameters["status"] != "Success") {
			$error_message = $response_parameters["status"];
			return;
		}

		if (!(isset($response_parameters["createDateTimeStamp"]))
			|| !(isset($response_parameters["consumerAccountNumber"]))
			|| !(isset($response_parameters["signatureValue"])))
		{
			$error_message = "Your transaction has been declined.";
			return;
		}

		$message = $response_parameters["createDateTimeStamp"] . $transaction_id . $response_parameters["status"] . $response_parameters["consumerAccountNumber"];
		$message = ideal_stripsimbls( $message );

		$sig = base64_decode($response_parameters["signatureValue"]);

		$valid = ideal_verifyMessage($payment_params["Certificate0"], $message, $sig );

		if ($valid != 1) {
			$error_message = "Bad signature!";
		} else {
			if (strlen($response_parameters["status"])) {
				$success_message = $response_parameters["status"];
			}
		}
	}

?>