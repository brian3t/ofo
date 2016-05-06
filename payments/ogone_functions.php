<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ogone_functions.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * oGone (http://ogone.com) transaction handler by www.viart.com
 */
function checkOrder() {
	global $table_prefix, $db;
	global $order_id, $success_message, $error_message, $pending_message;
	global $post_parameters, $payment_params, $pass_parameters, $pass_data, $variables;
		
	if ($order_id) {
		$currency           = get_param("currency");
		$amount             = get_param("amount");
		$payment_method     = get_param("PM");
		$acceptance_code    = get_param("ACCEPTANCE");
		$transaction_status = get_param("STATUS");
		$masked_card_number = get_param("CARDNO");
		$payment_reference  = get_param("PAYID");
		$error_code         = get_param("NCERROR");
		$card_brand         = get_param("BRAND");
		$send_hash          = get_param("SHASIGN");		
		
		$hash_fields  = $order_id;
		$hash_fields .= $currency;
		$hash_fields .= $amount;
		$hash_fields .= $payment_method;
		$hash_fields .= $acceptance_code;
		$hash_fields .= $transaction_status;
		$hash_fields .= $masked_card_number;
		$hash_fields .= $payment_reference;
		$hash_fields .= $error_code;
		$hash_fields .= $card_brand;
		$hash_fields .= $payment_params["SHAfeedback_signature"];
		
		$send_hash_check = strtoupper(sha1($hash_fields));	

		if ($send_hash_check != $send_hash) {			
			$error_message .=  " Order hash is not valid.";
		} elseif ( ($variables["order_total"] != $amount) || ($variables["currency_code"] != $currency)) {
			$error_message .=  " Order total is not valid.";
		} else {	
			// other params
			$IPCTY         = get_param("IPCTY");
			$CCCTY         = get_param("CCCTY");
			$ECI           = get_param("ECI");
			$CVCCheck      = get_param("CVCCheck");
			$AAVCheck      = get_param("AAVCheck");
			$VC            = get_param("VC");
			$IP            = get_param("IP");
			$NCERRORPLUS   = get_param("NCERRORPLUS");
			
			switch ($transaction_status) {
				case 2:
					$error_message .= "The authorization has been declined by the financial institution.";
				break;
				case 5: 
					$success_message = "The authorization has been accepted.";
				break;
				case 9: 
					$success_message = "The payment has been accepted.";
				break;
				case 51:
					$pending_message .= "The authorization will be processed offline.";
				break;
				case 91:
					$pending_message .= "The data capture will be processed offline.";
				break;
				case 92: case 52:
					$error_message .= "A technical problem arose during the authorization/payment process, giving an unpredictable result.";
				break;
				case 93:
					$error_message .= "A technical problem arose.";
				break;
				case 0: default:
					$error_message .= "At least one of the payment data fields is invalid or missing (" . $error_code . $NCERRORPLUS . ")";
				break;			
			}
			
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET transaction_id=" . $db->tosql($payment_reference, TEXT) . ", ";
			$sql .= " cc_number=" . $db->tosql($masked_card_number, TEXT);			
			$db->query($sql);			
		}		
	} else {
		$error_message .= "oGone: No order found";
	}
}
?>