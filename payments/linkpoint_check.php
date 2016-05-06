<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  linkpoint_check.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Linkpoint (www.linkpoint.com) transaction handler by www.viart.com
 */

	// get parameters from linkpoint response
	$status         = get_param("status", POST); // status
	$order_id       = get_param("oid", POST); // our order id 
	$approval_code  = get_param("approval_code", POST); // get approval code
	$fail_reason    = get_param("failReason", POST); // get fail reason
	$merchant       = get_param("merchant", POST); 
	$merchantphone  = get_param("merchantphone", POST); 
	$merchantemail  = get_param("merchantemail", POST); 
	$chargetotal    = get_param("chargetotal", POST); 

	$codes = array(); $tran_status = "";
	if (strlen($approval_code)) {
		$codes = explode(":", $approval_code);
		if (sizeof($codes) > 3) {
			$tran_status = $codes[0];
			$transaction_id = $codes[3];
			$avs_codes = $codes[2];
			if (strlen($avs_codes) == 4) {
				$variables["avs_response_code"] = substr($avs_codes, 0, 3);
				$variables["avs_address_match"] = $avs_codes[0];
				$variables["avs_zip_match"] = $avs_codes[1];
				$variables["cvv2_match"] = $avs_codes[3];
			}
		}
	}

	// check parameters
	if (!strlen($status)) {
		$error_message = "Can't obtain transaction status.";
	} elseif (!strlen($order_id)) {
		$error_message = "Can't obtain order number parameter.";
	} elseif (strlen($fail_reason)) {
		$error_message = $fail_reason . " (" . $status . ")";
	} elseif ($status != "APPROVED" || $tran_status != "Y") {
		$error_message = "Your transaction has been declined. (" . $status . ")";
	} elseif (sizeof($codes) < 4) {
		$error_message = "Approval code has wrong value.";
	} else {
		//check amount and order id
		$error_message = check_payment($order_id, $chargetotal, "");
	}

?>