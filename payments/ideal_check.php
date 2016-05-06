<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ideal_check.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * iDEAL (www.ing-ideal.nl) transaction handler by www.viart.com
 */

	include_once("./payments/ideal_functions.php");

    $transaction_id = get_param("trxid");
    $entrance_code = get_param("ec");

	$sql  = " SELECT payment_id, order_id FROM " . $table_prefix . "orders ";
	$sql .= " WHERE transaction_id=" . $db->tosql($transaction_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_id = $db->f("payment_id");
		$order_id = $db->f("order_id");
	}

	if (!(isset($payment_parameters["EntranceCode"])) || ($payment_parameters["EntranceCode"]!=$entrance_code)) {
		$error_message = "Incorrect Entrance Code";
		return;
	}

	$timestamp = gmdate("Y") . "-" . gmdate("m") . "-" . gmdate("d") . "T" . gmdate("H") . ":" . gmdate("i") . ":" . gmdate("s") . ".000Z";
	$token = "";
	$tokenCode = "";
	if ("SHA1_RSA" == $payment_parameters["AuthenticationType"]) {
		$message = $timestamp . $payment_parameters["MerchantID"] . $payment_parameters["SubID"] . $transaction_id;
		$message = ideal_stripsimbls( $message );

		$token = ideal_createCertFingerprint($payment_params["Privatecert"]);
		$tokenCode = ideal_signMessage( $payment_params["Privatekey"], $payment_params["PrivatekeyPass"], $message );
		$tokenCode = base64_encode( $tokenCode );
	}
	$reqMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" //<?
	. "<AcquirerStatusReq xmlns=\"http://www.idealdesk.com/Message\" version=\"1.1.0\">\n"
	. "<createDateTimeStamp>" . $timestamp . "</createDateTimeStamp>\n"
	. "<Merchant>" . "<merchantID>" . $payment_parameters["MerchantID"] . "</merchantID>\n"
	. "<subID>" . $payment_parameters["SubID"] . "</subID>\n"
	. "<authentication>" . $payment_parameters["AuthenticationType"] . "</authentication>\n"
	. "<token>" . $token . "</token>\n"
	. "<tokenCode>" . $tokenCode . "</tokenCode>\n"
	. "</Merchant>\n"
	. "<Transaction>" . "<transactionID>" . $transaction_id . "</transactionID>\n"
	. "</Transaction>" . "</AcquirerStatusReq>";

	$answer = ideal_PostToHost($payment_parameters["AcquirerURL"], $payment_parameters["AcquirerTimeout"], $reqMsg);

	$response_parameters = array();
	preg_match_all ("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $answer, $matches, PREG_SET_ORDER);
	for($i = 0; $i < sizeof($matches); $i++) {
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

	if (!(isset($response_parameters["status"]))){
		$error_message = "Your transaction has been declined.";
		return;
	}

	if ( $response_parameters["status"] != "Success" ){
		$error_message = $response_parameters["status"];
		return;
	}

	if ( !(isset($response_parameters["createDateTimeStamp"])) || !(isset($response_parameters["consumerAccountNumber"])) ||
		 !(isset($response_parameters["signatureValue"])) ) {
		$error_message = "Your transaction has been declined.";
		return;
	}

	$message = $response_parameters["createDateTimeStamp"] . $transaction_id . $response_parameters["status"] . $response_parameters["consumerAccountNumber"];
	$message = ideal_stripsimbls( $message );

	$sig = base64_decode($response_parameters["signatureValue"]);

	$valid = ideal_verifyMessage($payment_parameters["Certificate0"], $message, $sig );

	if( $valid != 1 ) {
		$error_message = "Bad signature!";
	}

?>