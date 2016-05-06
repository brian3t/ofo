<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  nochex_response.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
 
/*
 * NOCHEX (www.nochex.com) transaction handler by ViArt Ltd. (www.viart.com)
 */
	chdir ('../');
	include_once("./includes/common.php");
	include_once("./includes/record.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/order_items.php");
	include_once("./includes/order_links.php");
	include_once("./includes/parameters.php");
	include_once("./messages/".$language_code."/cart_messages.php");
			
	$t = new VA_Template($settings["templates_dir"]);
	
	$transaction_id   = get_param("transaction_id");
	$transaction_date = get_param("transaction_date");
	$order_id         = get_param("order_id");
	$amount           = get_param("amount");
	$from_email       = get_param("from_email");
	$to_email         = get_param("to_email");
	$security_key     = get_param("security_key");
	$status           = get_param("status");
	
	$post_parameters = ""; 
	$payment_params  = array(); 
	$pass_parameters = array(); 
	$pass_data       = array(); 
	$variables       = array();
	get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
	$errors_message = "";
	if ($variables) {	
		if ( true || ($variables["email"] == $from_email) && ( $variables["order_total"] == $amount )
			&& ( ($payment_params["test_transaction"] == "100" && $status == "test") || ($status == "live") ) 
			&& ( $payment_params["merchant_id"] == $to_email )
		) {
			$success_status_id = $variables["success_status_id"];
			$pending_status_id = $variables["pending_status_id"];
			$failure_status_id = $variables["failure_status_id"];
				
			$params  = "transaction_id=$transaction_id";
			$params .= "&transaction_date=$transaction_date";
			$params .= "&order_id=$order_id";
			$params .= "&amount=$amount";
			$params .= "&from_email=$from_email";
			$params .= "&to_email=$to_email";
			$params .= "&security_key=$security_key";
			if ($status) {
				$params .= "&status=$status";
			}
			
			$ch = curl_init ();
			curl_setopt ($ch, CURLOPT_URL, "https://www.nochex.com/nochex.dll/apc/apc");
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt ($ch, CURLOPT_ENCODING, "x-www-form-urlencoded");
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$nochex_response = curl_exec ($ch);
			if (curl_errno($ch))
				echo curl_errno($ch)." - ".curl_error($ch);
			curl_close($ch);
			
			if ($nochex_response == "AUTHORISED") {		
				update_order_status($order_id, $success_status_id, true, "", $status_error);							
			} elseif ($nochex_response == "DECLINED")  {
				update_order_status($order_id, $failure_status_id , true, "", $status_error);	
				$errors_message = "NOCHEX: Order declined response";
			} else {
				update_order_status($order_id, $pending_status_id , true, "", $status_error);	
				$errors_message = "NOCHEX: Order unknown response";
			}
		} else {
			$errors_message = "NOCHEX: Order request error";
		}
			
	} else {
		$error_message = "NOCHEX: No order found";
	}
	error_log($errors_message);
	
?>