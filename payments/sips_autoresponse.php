<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  sips_autoresponse.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * SIPS (www.sips.atosorigin.com) transaction handler by ViArt Ltd. (www.viart.com)
 */
	chdir ('../');
	include_once("./includes/common.php");
	include_once("./includes/record.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/order_items.php");
	include_once("./includes/order_links.php");
	include_once("./includes/parameters.php");
	include_once("./messages/".$language_code."/cart_messages.php");
	
	define("SIPS_DEBUG", true);
	if (SIPS_DEBUG) {
		$fp = fopen("sips.log", "a+");
		fwrite($fp, date("Y-m-d H:i:s") . "\n");
		fclose($fp);
	}
	$error_message = "";
	if (isset($_POST['DATA'])) {
		
		$sql  = " SELECT payment_id ";
		$sql .= " FROM " . $table_prefix . "payment_parameters ";
		$sql .= " WHERE parameter_name='automatic_response_url' AND parameter_source LIKE '%sips_autoresponse.php%' ";
		$payment_id = get_db_value($sql);
		
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "payment_systems ";
		$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$success_status_id = $db->f('success_status_id');	
			$failure_status_id = $db->f('failure_status_ide');			
		} else {
			$error_message = "SIPS Error: No data found";
			error_log($error_message);
			exit;
		}
		
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "payment_parameters ";
		$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$payment_params = array();
		$db->query($sql);
		while ($db->next_record()) {
			$parameter_name   = $db->f('parameter_name');	
			$parameter_source = $db->f('parameter_source');			
			$payment_params[$parameter_name] = $parameter_source;
		}
								
		$params  = " message=" . $_POST['DATA'];
		$params .= " pathfile=" .  $payment_params['pathfile'];
		exec($payment_params['path_bin_resp'] . $params, $result);
		list(
			$empty, 
			$code,//0
			$error, 
			$merchant_id, //014022286611111
			$merchant_country, //fr
			$amount, //5250
			$transaction_id, //102421
			$payment_means, //VISA
			$transmission_date, //20080117082421
			$payment_time, //092809
			$payment_date, //20080117
			$response_code, //00
			$payment_certificate, //1200558489
			$authorisation_id, //558489
			$currency_code, //978
			$card_number, //4974.00
			$cvv_flag, //1
			$cvv_response_code, //4D
			$bank_response_code, //00
			$complementary_code, 
			$complementary_info, 
			$return_context, 
			$caddie, 
			$receipt_complement, 
			$merchant_language, //fr
			$language, //en
			$customer_id, //9102421
			$order_id, //56
			$customer_email, 
			$customer_ip_address, 
			$capture_day, //0
			$capture_mode, //"AUTHOR_CAPTURE"
			$data
		) = explode ("!", $result[0]);

		if (SIPS_DEBUG) {
			$fp = fopen("sips.log", "a+");
			fwrite($fp, $result[0] . "\n");
			fclose($fp);
		}
		
		$sql  = " SELECT * FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			if (($payment_id == $db->f('payment_id')) 
				&& ($customer_id == $db->f('user_id'))
				&& ($customer_email == $db->f('email'))) {
				$sql  = " SELECT credit_card_id FROM  " . $table_prefix . "credit_cards";
				$sql .= " WHERE UPPER(credit_card_code)=" . $db->tosql($payment_means, TEXT);
				$payment_means = get_db_value($sql);

				$t = new VA_Template("");				
				if ($code == 0 && $bank_response_code == "00") {
					if ($customer_ip_address !== $db->f('remote_address')) {
						$success_message = "Payment IP Address = " . $db->tosql($customer_ip_address, TEXT);
					}
					$sql  = " UPDATE " . $table_prefix . "orders ";
					$sql .= " SET transaction_id=" . $db->tosql($transaction_id, TEXT);
					$sql .= ", authorization_code=" . $db->tosql($authorisation_id, TEXT);
					$sql .= ", error_message=" . $db->tosql($error, TEXT);
					$sql .= ", cvv2_match=" . $db->tosql($cvv_flag, TEXT);
					$sql .= ", cc_number=" . $db->tosql($card_number, TEXT);
					$sql .= ", cc_type=" . $db->tosql($payment_means, INTEGER);	
					$sql .= ", success_message=" . $db->tosql($success_message, TEXT);	
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);
					update_order_status($order_id, $success_status_id, true, "", $status_error);	
				} else {
					$sql  = " UPDATE " . $table_prefix . "orders ";
					$sql .= " SET transaction_id=" . $db->tosql($transaction_id, TEXT);
					$sql .= ", authorization_code=" . $db->tosql($authorisation_id, TEXT);
					$sql .= ", error_message=" . $db->tosql($error, TEXT);
					$sql .= ", cvv2_match=" . $db->tosql($cvv_flag, TEXT);
					$sql .= ", cc_number=" . $db->tosql($card_number, TEXT);
					$sql .= ", cc_type=" . $db->tosql($payment_means, INTEGER); 
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);
					update_order_status($order_id, $failure_status_id, true, "", $status_error);					
					$error_message = "SIPS Error: " .$error;
					error_log($error_message);
				}						
			} else {
				$error_message = "SIPS Error: Order parameters is not valid.";
				error_log($error_message);
			}			
		} else {
			$error_message = "SIPS Error: Order id is not valid.";
			error_log($error_message);
		}				
	} else {
		$error_message = "SIPS Error: There are no answer from payment gateway.";
		error_log($error_message);
	}
	
	if (SIPS_DEBUG && $error_message) {
		$fp = fopen("sips.log", "a+");
		fwrite($fp, $error_message . "\n");
		fclose($fp);
	}
?>