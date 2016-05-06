<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_ipn.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * PayPal IPN (www.paypal.com) transaction handler by http://www.viart.com/
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/parameters.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "includes/order_links.php");
	include_once ($root_folder_path . "includes/date_functions.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");

	// initialize template object
	$t = new VA_Template(".");

	$order_id = get_param("invoice");
	
	$sql  = " SELECT o.payment_id, o.site_id ";
	$sql .= " FROM " . $table_prefix . "orders o ";
	$sql .= " WHERE o.order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_id    = $db->f("payment_id");
		$order_site_id = $db->f("site_id");
	} else {
		$error_message = APPROPRIATE_CODE_ERROR_MSG . $order_id . ".<br>";
		exit;
	}
		
	$payment_parameters = array();
	$pass_parameters    = array();
	$post_parameters    = array();
	$pass_data          = array();
	$variables          = array();
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables, "final");
	$t->set_vars($variables);
	
	$order_final = array();
	$setting_type = "order_final_" . $payment_id;
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	if ($order_site_id) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($order_site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_final[$db->f("setting_name")] = $db->f("setting_value");
	}
	
	// get some variables from our payment settings
	$business_email = isset($payment_parameters["business"]) ? $payment_parameters["business"] : "";
	$sandbox        = isset($payment_parameters["sandbox"]) ? $payment_parameters["sandbox"] : 0;
	$ssl            = isset($payment_parameters["ssl"]) ? $payment_parameters["ssl"] : 0;

	if ($sandbox == 1) {
		$paypal_url = "www.sandbox.paypal.com";
	} else {
		$paypal_url = "www.paypal.com";
	}

	// read the post from PayPal system and add 'cmd'
	$request_params = "cmd=_notify-validate";

	if (isset($_POST)) {
		foreach ($_POST as $key => $value) {
			$t->set_var($key, $value);
			$value = urlencode(stripslashes($value));
			$request_params .= "&$key=$value";
		}
	} else {
		foreach ($HTTP_POST_VARS as $key => $value) {
			$t->set_var($key, $value);
			$value = urlencode(stripslashes($value));
			$request_params .= "&$key=$value";
		}
	}

	// request header to PayPal system to validate
	$request_header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$request_header .= "Host: " . $paypal_url . "\r\n";
	$request_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$request_header .= "Content-Length: " . strlen($request_params) . "\r\n\r\n";

	if($ssl == 1) {
		// If possible, securely post back to paypal using HTTPS
		// Your PHP server will need to be SSL enabled
		$fp = fsockopen ("ssl://" . $paypal_url, 443, $errno, $errstr, 30);
	} else {
		$fp = fsockopen ($paypal_url, 80, $errno, $errstr, 30);
	}


// assign posted variables to local variables
/*
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payer_email = $_POST['payer_email'];
*/

	if (!$fp) {
		// HTTP ERROR
		$error_message = "Can't connect to PayPal.";
	} else {
		fputs ($fp, $request_header . $request_params);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED") == 0) {
				// check the payment_status is Completed
				// check that txn_id has not been previously processed
				// check that receiver_email is your Primary PayPal email
				// check that payment_amount/payment_currency are correct
				// process payment
				$transaction_id   = get_param("txn_id");
				$payment_status   = get_param("payment_status");
				$payment_currency = get_param("mc_currency");
				$payment_amount   = get_param("mc_gross");
        		$receiver_email   = get_param("receiver_email");
				$pending_reason   = get_param("pending_reason");

				if (strtolower($payment_status) == "pending") {
					$pending_message = strlen($pending_reason) ? $pending_reason : "Pending";
				}

				if (strtolower($payment_status) != "completed" && strtolower($payment_status) != "pending") {	// check the payment_status is Completed
					if (strlen($payment_status)) {
						$error_message = "Your payment status is " . $payment_status;
					} else {
						$error_message = "Unknown payment status";
					}
				} else if (!strlen(trim($business_email))) {	// check that business parameter was set
					$error_message = "Please specify the 'business' parameter in your payment parameters list.";
				} else if (strtolower(trim($business_email)) != strtolower(trim($receiver_email))) {	// check that receiver_email is your Primary PayPal email
					$error_message = "Wrong receiver email - " . $receiver_email;
				} else {
					// check that payment_amount/payment_currency are correct
					$error_message = check_payment($order_id, $payment_amount, $payment_currency);
				}

				// get statuses
				$success_status_id = get_setting_value($order_final, "success_status_id", "");
				$pending_status_id = get_setting_value($order_final, "pending_status_id", "");
				$failure_status_id = get_setting_value($order_final, "failure_status_id", "");

				// update order status
				$is_failed = false; $is_pending = false; $is_success = false;
				if (strlen($error_message)) {
					$is_failed = true;
					$message_type = "failure";
					$t->set_var("error_desc", $error_message);
					$t->set_var("error_message", $error_message);
					$order_status = $failure_status_id;
					if (strtolower($payment_status) == "refunded") {
						$sql  = " SELECT status_id FROM " . $table_prefix . "order_statuses ";
						$sql .= " WHERE status_type='REFUNDED' ";
						$db->query($sql);
						if ($db->next_record()) {
							$order_status = $db->f("status_id");
						}
					}
					$sql  = " UPDATE " . $table_prefix . "orders ";
					$sql .= " SET error_message=" . $db->tosql($error_message, TEXT);
					$sql .= " , transaction_id=" . $db->tosql($transaction_id, TEXT);
					$sql .= " , is_placed=1 ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;

				} else if (strlen($pending_message)) {
					$is_pending = true;
					$message_type = "pending";
					$t->set_var("pending_desc", $pending_message);
					$t->set_var("pending_message", $pending_message);
					$order_status = $pending_status_id;
					$sql  = " UPDATE " . $table_prefix . "orders ";
					$sql .= " SET pending_message=" . $db->tosql($pending_message, TEXT);
					$sql .= " , transaction_id=" . $db->tosql($transaction_id, TEXT);
					$sql .= " , is_placed=1 ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
				} else {
					$is_success = true;
					$message_type = "success";
					$order_status = $success_status_id;
					$sql  = " UPDATE " . $table_prefix . "orders ";
					$sql .= " SET error_message=''";
					$sql .= " , pending_message=''";
					$sql .= " , transaction_id=" . $db->tosql($transaction_id, TEXT);
					$sql .= " , is_placed=1 ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
				}
				$db->query($sql);

				// update order status
				update_order_status($order_id, $order_status, true, "", $status_error);
				
				// send emails
				if (!$is_placed && $current_status != $order_status) // check if order wasn't placed and status was changed
				{
					$t->set_var("request_params", $request_params);					
					
					// get admin notify
					$admin_notification   = get_setting_value($order_final, "admin_notification",   0);
					$admin_pending_notify = get_setting_value($order_final, "admin_pending_notify", 0);
					$admin_failure_notify = get_setting_value($order_final, "admin_failure_notify", 0);

					// get user notify
					$user_notification   = get_setting_value($order_final, "user_notification",   0);
					$user_pending_notify = get_setting_value($order_final, "user_pending_notify", 0);
					$user_failure_notify = get_setting_value($order_final, "user_failure_notify", 0);

					
					$admin_notify = (($is_success && $admin_notification) || ($is_pending && $admin_pending_notify) || ($is_failed && $admin_failure_notify));
					if (isset($order_final["admin_message"])){
						$admin_message = get_final_message($order_final["admin_message"], $message_type);
					} else {
						$admin_message = "";
					}
					$admin_mail_type = get_setting_value($order_final, "admin_message_type");
					$user_notify = (($is_success && $user_notification) || ($is_pending && $user_pending_notify) || ($is_failed && $user_failure_notify));
					if (isset($order_final["user_message"])){
						$user_message = get_final_message($order_final["user_message"], $message_type);
					} else {
						$user_message = "";
					}
					$user_mail_type = get_setting_value($order_final, "user_message_type");
		
					// parse basket template if tag used in notification
					if (($admin_notify && $admin_mail_type && strpos($admin_message, "{basket}") !== false)
						|| ($user_notify && $user_mail_type && strpos($user_message, "{basket}") !== false))
					{
						$t->set_file("basket_html", "email_basket.html");
						$items_text = show_order_items($order_id, true, "");
						$t->parse("basket_html", false);
					}
					if (($admin_notify && !$admin_mail_type && strpos($admin_message, "{basket}") !== false) 
						|| ($user_notify && !$user_mail_type && strpos($user_message, "{basket}") !== false) )
					{
						$t->set_file("basket_text", "email_basket.txt");
						$items_text = show_order_items($order_id, true, "");
						$t->parse("basket_text", false);
					}
		
					// get download links
					$links = get_order_links($order_id);
					$t->set_var("links",      $links["html"]);
					$t->set_var("links_html", $links["html"]);
					$t->set_var("links_txt",  $links["text"]);

					// get serial numbers
					$order_serials = get_serial_numbers($order_id);
					$t->set_var("serials", $order_serials["html"]);
					$t->set_var("serial_numbers", $order_serials["html"]);

					// get gift vouchers
					$order_vouchers = get_gift_vouchers($order_id);
					$t->set_var("vouchers", $order_vouchers["html"]);
					$t->set_var("gift_vouchers", $order_vouchers["html"]);

					$order_admin_email = (isset($order_final["admin_email"]) && $order_final["admin_email"]) ? $order_final["admin_email"] : $settings["admin_email"];

					if(($is_success && $admin_notification) || ($is_pending && $admin_pending_notify) || ($is_failed && $admin_failure_notify))
					{
						$admin_subject = get_final_message($order_final["admin_subject"], $message_type);
						$admin_message = get_final_message($order_final["admin_message"], $message_type);
						$t->set_block("admin_subject", $admin_subject);
						$t->set_block("admin_message", $admin_message);

						$items_text = show_order_items($order_id, true);

						$email_headers = array();
						$mail_to = get_setting_value($order_final, "admin_email", $settings["admin_email"]);
						$mail_to = str_replace(";", ",", $mail_to);
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
						} else {
							$t->set_var("basket", $t->get_var("basket_html"));
						}

						$t->parse("admin_subject", false);
						$t->parse("admin_message", false);
						$admin_message = str_replace("\r", "", $t->get_var("admin_message"));
						va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
					}

					if(($is_success && $user_notification) || ($is_pending && $user_pending_notify) || ($is_failed && $user_failure_notify))
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
						} else {
							$t->set_var("basket", $t->get_var("basket_html"));
						}

						$t->parse("user_subject", false);
						$t->parse("user_message", false);
						$user_email = strlen($variables["email"]) ? $variables["email"] : $variables["delivery_email"];
						$user_message = str_replace("\r", "", $t->get_var("user_message"));
						
						va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers);
					}

				}
			} else if (strcmp ($res, "INVALID") == 0) {
				// log for manual investigation
				// mail("to@youraddress", "Invalid PayPal IPN Request", $request_params)
			}
		}
		fclose ($fp);
	}

?>