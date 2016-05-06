<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  sips_response.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * SIPS (www.sips.atosorigin.com) transaction handler by ViArt Ltd. (www.viart.com)
 */
	if (isset($_POST['DATA'])) {		
						
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
			$returned_order_id, //56
			$customer_email, 
			$customer_ip_address, 
			$capture_day, //0
			$capture_mode, //"AUTHOR_CAPTURE"
			$data
		) = explode ("!", $result[0]);

		if ($returned_order_id==$order_id) {
			$sql  = " SELECT * FROM " . $table_prefix . "orders ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				if (($payment_id == $db->f('payment_id'))
					&& ($customer_id == $db->f('user_id'))
					&& ($customer_email == $db->f('email')) ) {
						$sql  = " SELECT credit_card_id FROM  " . $table_prefix . "credit_cards";
						$sql .= " WHERE UPPER(credit_card_code)=" . $db->tosql($payment_means, TEXT);
						$payment_means = get_db_value($sql);
																		
					if ($code == 0 && $bank_response_code == "00") {
						$sql  = " UPDATE " . $table_prefix . "orders ";
						$sql .= " SET transaction_id=" . $db->tosql($transaction_id, TEXT);
						$sql .= ", authorization_code=" . $db->tosql($authorisation_id, TEXT);
						$sql .= ", error_message=" . $db->tosql($error, TEXT);
						$sql .= ", cvv2_match=" . $db->tosql($cvv_flag, TEXT);
						$sql .= ", cc_number=" . $db->tosql($card_number, TEXT);
						$sql .= ", cc_type=" . $db->tosql($payment_means, INTEGER);			  	
						$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);	
						$success_message = "Your order has been accepted.";								
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
						$error_message .= "SIPS Error: " .$error;
					}						
				} else {
					$error_message .= " Order parameters is not valid.";
				}				
			} else {
				$error_message .= " Order id is not valid.";
			}			
		} else {
			$error_message .= " Order id is not valid.";
		}						
	} else {
		$pending_message = "There are no answer from payment gateway.";
	}
?>