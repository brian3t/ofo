<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  cybersource_check.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Cybersource (www.cybersource.com) SOP response handler by ViArt Ltd. (www.viart.com)
 */

	$root_folder_path = "./";
	include_once($root_folder_path . "payments/cybersource_functions.php");

	$decision = get_param("decision", POST);
	$decision_publicSignature = get_param("decision_publicSignature", POST);
	$orderAmount = get_param("orderAmount", POST);
	$orderAmount_publicSignature = get_param("orderAmount_publicSignature", POST);
	$orderNumber = get_param("orderNumber", POST);
	$orderNumber_publicSignature = get_param("orderNumber_publicSignature", POST);
	$orderCurrency = get_param("orderCurrency", POST);
	$orderCurrency_publicSignature = get_param("orderCurrency_publicSignature", POST);

	$pub = isset($payment_parameters["PublicKey"]) ? $payment_parameters["PublicKey"] : "";

	$success_message = "";
	if (strlen($pub))
	{
		if (VerifySignature($decision, $decision_publicSignature, $pub)
			&& VerifySignature($orderAmount, $orderAmount_publicSignature, $pub)
			&& VerifySignature($orderNumber, $orderNumber_publicSignature, $pub)
			&& VerifySignature($orderCurrency, $orderCurrency_publicSignature, $pub))
		{
			if ($decision == "ACCEPT") {
				$success_message = "Your order has been accepted.";
			} elseif ($decision == "REVIEW") {
				$pending_message = "Your order will be reviewed.";
			} else {
				$error_message .= " Your order has been rejected.";
			}
		}
		else
		{
			if (!VerifySignature($decision, $decision_publicSignature, $pub)) {
				$error_message .= " Order decision is not valid.";
			}
			if (!VerifySignature($orderAmount, $orderAmount_publicSignature, $pub))	{
				$error_message .=  " Order amount is not valid.";
			}
			if (!VerifySignature($orderNumber, $orderNumber_publicSignature, $pub))	{
				$error_message .= " Order number is not valid.";
			}
			if (!VerifySignature($orderCurrency, $orderCurrency_publicSignature, $pub)) {
				$error_message .= " Order currency is not valid.";
			}
		}
	}
	else
	{
		$error_message .= " Public Key is empty.";
	}

?>