<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  buckaroo_ssl_check.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Buckaroo (http://buckaroo.nl/) transaction handler by www.viart.com
 */

	$status         = get_param("status");
	$bpe_trx        = get_param("bpe_trx");
	$bpe_result     = get_param("bpe_result");
	$bpe_invoice    = get_param("bpe_invoice");
	$bpe_reference  = get_param("bpe_reference");
	$bpe_signature  = get_param("bpe_signature");
	$bpe_signature2 = get_param("bpe_signature2");
	$bpe_amount     = get_param("bpe_amount");
	$bpe_currency   = get_param("bpe_currency");
	$bpe_timestamp  = get_param("bpe_timestamp");

	$error_message = check_payment($order_id, round(($bpe_amount / 100), 2), $bpe_currency);

	$transaction_id = $bpe_trx;
	if($bpe_invoice != $order_id){
		$pending_message = CHECKOUT_PENDING_MSG;
	}elseif(strlen($error_message)){
		return;
	}else{
		if(
			$bpe_signature2 != md5($bpe_trx . $bpe_timestamp . $payment_parameters['BPE_Merchant'] . $bpe_invoice . $bpe_reference . $bpe_currency . $bpe_amount . $bpe_result . $payment_parameters['BPE_Mode'] . $payment_parameters['secretkey'])
			||
			$bpe_signature != md5($payment_parameters['BPE_Merchant'] . $bpe_invoice . $bpe_trx . $payment_parameters['secretkey'])
		){
			$error_message = "'HASH' have a wrong value.";
		}elseif($bpe_result == 100 || $bpe_result == 801){
			$success_message = "Result code (".$bpe_result.") Your payment status is " . $status;
		}else{
			$error_message = "Result code (".$bpe_result.") Your payment status is " . $status;
		}
	}
?>
