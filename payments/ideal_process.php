<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ideal_process.php                                        ***
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

	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/date_functions.php");
    include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "payments/ideal_functions.php");

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");

	$order_errors = check_order($order_id, $vc);
	if ($order_errors) 
	{
		echo $order_errors;
		exit;
	} 
	else 
	{
		$payment_parameters = array();
		$pass_parameters = array();
		$post_parameters = '';
		$pass_data = array();
		$variables = array();
		get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables);

		$order_total = $variables["order_total_100"];

		$timestamp = gmdate("Y") . "-" . gmdate("m") . "-" . gmdate("d") . "T" . gmdate("H") . ":" . gmdate("i") . ":" . gmdate("s") . ".000Z";
		$token = "";
		$tokenCode = "";

		$debug = (isset($payment_parameters["debug"])) ? $payment_parameters["debug"] : "";
		$cert_not_find = false;
		if (!file_exists($payment_parameters["Privatekey"])) {
			if ($debug) {
				echo "Can not find 'Privatekey'<br>\n";
			}
			$cert_not_find = true;
		}
		if (!file_exists($payment_parameters["Privatecert"])) {
			if ($debug) {
				echo "Can not find 'Privatecert'<br>\n";
			}
			$cert_not_find = true;
		}
		if (!file_exists($payment_parameters["Certificate0"])) {
			if ($debug) {
				echo "Can not find 'Certificate0'<br>\n";
			}
			$cert_not_find = true;
		}
		if ($cert_not_find) {
			if (!$debug) {
				echo "Some errors occurred while processing you request to iDEAL.<br>\n";
			}
			exit;
		}

		if ($payment_parameters["IssuerID"] == "") {
			$payment_parameters["IssuerID"] = get_param("issuer_id");
		}
		if ($payment_parameters["IssuerID"] == "") {
			if ("SHA1_RSA" == $payment_parameters["AuthenticationType"]) {
				$message = $timestamp . $payment_parameters["MerchantID"] . $payment_parameters["SubID"];
				$message = ideal_stripsimbls( $message );

				$token = ideal_createCertFingerprint($payment_parameters["Privatecert"]);

				$tokenCode = ideal_signMessage( $payment_parameters["Privatekey"], $payment_parameters["PrivatekeyPass"], $message );
				$tokenCode = base64_encode( $tokenCode );
			}
			$reqMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" //<?
				. "<DirectoryReq xmlns=\"http://www.idealdesk.com/Message\" version=\"1.1.0\">\n"
				. "<createDateTimeStamp>" . xml_escape_string($timestamp) . "</createDateTimeStamp>\n"
				. "<Merchant>\n"
				. "<merchantID>" . xml_escape_string($payment_parameters["MerchantID"]) . "</merchantID>\n"
				. "<subID>" . xml_escape_string($payment_parameters["SubID"]) . "</subID>\n"
				. "<authentication>" . xml_escape_string($payment_parameters["AuthenticationType"]) . "</authentication>\n"
				. "<token>" . xml_escape_string($token) . "</token>\n"
				. "<tokenCode>" . xml_escape_string($tokenCode) . "</tokenCode>\n"
				. "</Merchant>\n"
				. "</DirectoryReq>";

			$answer = ideal_PostToHost($payment_parameters["AcquirerURL"], $payment_parameters["AcquirerTimeout"], $reqMsg);

			$response_parameters = array();
			preg_match_all ("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $answer, $matches, PREG_SET_ORDER);
			for ($i = 0; $i < sizeof($matches); $i++) {
				if ($matches[$i][1]=="issuerID"){
					$response_parameters["IssuerID"][] = array($matches[$i][2],$matches[$i+1][2]);
				} else {
					$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
				}
			}
			if (isset($response_parameters["errorCode"])) {
				echo "errorCode - " . $response_parameters["errorCode"] . "<br>";
				echo "errorMessage - " . $response_parameters["errorMessage"] . "<br>";
				exit;
			}

			if (sizeof($response_parameters["IssuerID"])==1) {
				$payment_parameter["IssuerID"]=$response_parameters["IssuerID"][0][0];
			} else {
?>
<html>
<head>
<title>..: Select Issuer :..</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<body>

	<form action="ideal_process.php" method="post" name="OrderForm">
	<div align="center" style="font-family: tahoma, arial, sans-serif; color: navy; ">
	Please select Issuer:
	<select name="issuer_id">
<?php
				foreach ($response_parameters["IssuerID"] as $parameter_name => $parameter_value) {
					echo "<option  value=\"" . $response_parameters["IssuerID"][$parameter_name][0] . "\">"
						. $response_parameters["IssuerID"][$parameter_name][1];
				}
?>
	</select>
	<input type="submit" value="Continue" style="border: 1px solid gray; background-color: #e0e0e0; font-family: tahoma, arial, sans-serif; height: 20px; color: #333333; font-weight: bold;">
	</div>
	</form>

</body>
</html>
<?php
			exit;
			}
		}

        if ( "SHA1_RSA" == $payment_parameters["AuthenticationType"] ) {
			$message = $timestamp
				.$payment_parameters["IssuerID"]
				.$payment_parameters["MerchantID"]
				.$payment_parameters["SubID"]
				.$payment_parameters["MerchantReturnURL"]
				.$order_id
				.$order_total
				.$payment_parameters["Currency"]
				.$payment_parameters["Language"]
				.$payment_parameters["DESCRIPTION"]
				.$payment_parameters["EntranceCode"];
			$message = ideal_stripsimbls($message);

        	$token = ideal_createCertFingerprint($payment_parameters["Privatecert"]);

        	$tokenCode = ideal_signMessage( $payment_parameters["Privatekey"], $payment_parameters["PrivatekeyPass"], $message);
        	$tokenCode = base64_encode( $tokenCode );
		}
		$reqMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" //<?
                . "<AcquirerTrxReq xmlns=\"http://www.idealdesk.com/Message\" version=\"1.1.0\">\n"
                . "<createDateTimeStamp>" . xml_escape_string($timestamp) .  "</createDateTimeStamp>\n"
                . "<Issuer>" . "<issuerID>" . xml_escape_string($payment_parameters["IssuerID"]) . "</issuerID>\n"
                . "</Issuer>\n"
                . "<Merchant>" . "<merchantID>" . xml_escape_string($payment_parameters["MerchantID"]) . "</merchantID>\n"
                . "<subID>" . xml_escape_string($payment_parameters["SubID"]) . "</subID>\n"
                . "<authentication>" . xml_escape_string($payment_parameters["AuthenticationType"]) . "</authentication>\n"
                . "<token>" . xml_escape_string($token) . "</token>\n"
                . "<tokenCode>" . xml_escape_string($tokenCode) . "</tokenCode>\n"
                . "<merchantReturnURL>" . xml_escape_string($payment_parameters["MerchantReturnURL"]) . "</merchantReturnURL>\n"
                . "</Merchant>\n"
                . "<Transaction>" . "<purchaseID>" . xml_escape_string($order_id) . "</purchaseID>\n"
                . "<amount>" . xml_escape_string($order_total) . "</amount>\n"
                . "<currency>" . xml_escape_string($payment_parameters["Currency"]) . "</currency>\n"
                . "<expirationPeriod>" . xml_escape_string($payment_parameters["ExpirationPeriod"]) . "</expirationPeriod>\n"
                . "<language>" . xml_escape_string($payment_parameters["Language"]) . "</language>\n"
                . "<description>" . xml_escape_string($payment_parameters["DESCRIPTION"]) . "</description>\n"
                . "<entranceCode>" . xml_escape_string($payment_parameters["EntranceCode"]) . "</entranceCode>\n"
                . "</Transaction>" . "</AcquirerTrxReq>";
		$answer = ideal_PostToHost($payment_parameters["AcquirerURL"], $payment_parameters["AcquirerTimeout"], $reqMsg);
		
		$response_parameters = array();
		preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $answer, $matches, PREG_SET_ORDER);
		for ($i = 0; $i < sizeof($matches); $i++) {
			$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
		}
		if (isset($response_parameters["errorCode"])) {
			echo "errorCode - " . $response_parameters["errorCode"] . "<br>";
			echo "errorMessage - " . $response_parameters["errorMessage"] . "<br>";
			exit;
		}
		if ( isset($response_parameters["purchaseID"]) && $order_id == $response_parameters["purchaseID"]) {
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET transaction_id=" . $db->tosql($response_parameters["transactionID"], TEXT) ;
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
			$db->query($sql);
			header("Location: " . ideal_unhtmlentities($response_parameters["issuerAuthenticationURL"]));
		}
	}
	
?>