<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_details.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * PayPal Express Checkout (www.paypal.com) transaction handler by http://www.viart.com/
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "includes/order_links.php");
	include_once ($root_folder_path . "includes/date_functions.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path ."includes/parameters.php");

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");
	$token = get_param("token");
	set_session("session_token", $token);

	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}

	// get payment data
	$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables);

	// get some variables from our payment settings
	$sandbox        = isset($payment_parameters["sandbox"]) ? $payment_parameters["sandbox"] : 0;
	$sslcert        = isset($payment_parameters["sslcert"]) ? $payment_parameters["sslcert"] : "";


	if ($sandbox == 1) {
		$api_url = "https://api-aa.sandbox.paypal.com/2.0/";
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$api_url = "https://api-aa.paypal.com/2.0/";
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}

	$soap = getDetailsSOAP($payment_parameters);

	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $api_url);
	curl_setopt ($ch, CURLOPT_POST, 1);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $soap);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_SSLCERT, $sslcert);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	set_curl_options ($ch, $payment_parameters);

	$paypal_response = curl_exec ($ch);
	curl_close ($ch);
	if (strlen($paypal_response)) {

		preg_match("/<Payer [^>]*>(.*)<\/Payer>/i", $paypal_response, $match);
		$email = (isset($match[1]))?($match[1]):'';
		$delivery_email = $email;
		preg_match("/<FirstName[^>]*>(.*)<\/FirstName>/i", $paypal_response, $match);
		$first_name = (isset($match[1]))?($match[1]):'';
		$delivery_first_name = $first_name;
		preg_match("/<LastName[^>]*>(.*)<\/LastName>/i", $paypal_response, $match);
		$last_name = (isset($match[1]))?($match[1]):'';
		$delivery_last_name = $last_name;
		preg_match("/<Name[^>]*>(.*)<\/Name>/i", $paypal_response, $match);
		$name = (isset($match[1]))?($match[1]):'';
		$delivery_name = $name;
		preg_match("/<Street1[^>]*>(.*)<\/Street1>/i", $paypal_response, $match);
		$address1 = (isset($match[1]))?($match[1]):'';
		$delivery_address1 = $address1;
		preg_match("/<Street2[^>]*>(.*)<\/Street2>/i", $paypal_response, $match);
		$address2 = (isset($match[1]))?($match[1]):'';
		$delivery_address2 = $address2;
		preg_match("/<CityName[^>]*>(.*)<\/CityName>/i", $paypal_response, $match);
		$city = (isset($match[1]))?($match[1]):'';
		$delivery_city = $city;
		preg_match("/<StateOrProvince[^>]*>(.*)<\/StateOrProvince>/i", $paypal_response, $match);
		$province = (isset($match[1]))?($match[1]):'';
		$delivery_province = $province;
		preg_match("/<Country[^>]*>(.*)<\/Country>/i", $paypal_response, $match);
		$country_code = (isset($match[1]))?($match[1]):'';
		$delivery_country_code = $country_code;
		$sql = "SELECT country_id FROM " . $table_prefix . "countries WHERE country_code=".$db->tosql($country_code, TEXT);
		$country_id = get_db_value($sql);
		$delivery_country_id = $country_id;
		preg_match("/<PostalCode[^>]*>(.*)<\/PostalCode>/i", $paypal_response, $match);
		$zip = (isset($match[1]))?($match[1]):'';
		$delivery_zip = $zip;
		if(strtoupper($country_code) == "US"){
			$state_code = $province;
			$delivery_state_code = $province;
			$sql = "SELECT state_id FROM " . $table_prefix . "states WHERE state_code=".$db->tosql($state_code, TEXT);
			$state_id = get_db_value($sql);
			$delivery_state_id = $state_id;
		}else{
			$state_code = "";
			$delivery_state_code = "";
			$state_id = 0;
			$delivery_state_id = 0;
		}

		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET name=" . $db->tosql($name, TEXT) ;
		$sql .= ", first_name=" . $db->tosql($first_name, TEXT) ;
		$sql .= ", last_name=" . $db->tosql($last_name, TEXT) ;
		$sql .= ", email=" . $db->tosql($email, TEXT) ;
		$sql .= ", address1=" . $db->tosql($address1, TEXT) ;
		$sql .= ", address2=" . $db->tosql($address2, TEXT) ;
		$sql .= ", city=" . $db->tosql($city, TEXT) ;
		$sql .= ", province=" . $db->tosql($province, TEXT) ;
		$sql .= ", state_code=" . $db->tosql($state_code, TEXT) ;
		$sql .= ", state_id=" . $db->tosql($state_id, INTEGER) ;
		$sql .= ", zip=" . $db->tosql($zip, TEXT) ;
		$sql .= ", country_code=" . $db->tosql($country_code, TEXT) ;
		$sql .= ", country_id=" . $db->tosql($country_id, INTEGER) ;
		$sql .= ", delivery_name=" . $db->tosql($delivery_name, TEXT) ;
		$sql .= ", delivery_first_name=" . $db->tosql($delivery_first_name, TEXT) ;
		$sql .= ", delivery_last_name=" . $db->tosql($delivery_last_name, TEXT) ;
		$sql .= ", delivery_email=" . $db->tosql($delivery_email, TEXT) ;
		$sql .= ", delivery_address1=" . $db->tosql($delivery_address1, TEXT) ;
		$sql .= ", delivery_address2=" . $db->tosql($delivery_address2, TEXT) ;
		$sql .= ", delivery_city=" . $db->tosql($delivery_city, TEXT) ;
		$sql .= ", delivery_province=" . $db->tosql($delivery_province, TEXT) ;
		$sql .= ", delivery_state_code=" . $db->tosql($delivery_state_code, TEXT) ;
		$sql .= ", delivery_state_id=" . $db->tosql($delivery_state_id, INTEGER) ;
		$sql .= ", delivery_zip=" . $db->tosql($delivery_zip, TEXT) ;
		$sql .= ", delivery_country_code=" . $db->tosql($delivery_country_code, TEXT) ;
		$sql .= ", delivery_country_id=" . $db->tosql($delivery_country_id, INTEGER) ;
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
		$db->query($sql);

		if (preg_match("/<SOAP-ENV:Fault>/i", $paypal_response)) {
			$faultcode = ""; $faultstring = "";
			if (preg_match("/<faultcode>(.*)<\/faultcode>/i", $paypal_response, $match)) {
				$faultcode = $match[1];
			}
			if (preg_match("/<faultstring>(.*)<\/faultstring>/i", $paypal_response, $match)) {
				$faultstring = $match[1];
			}
			echo "Some errors occurred during handling your transaction:<br>";
			echo $faultcode . ": " . $faultstring;
			exit;
		} else if (preg_match("/<Errors/i", $paypal_response)) {
			$errorcode = ""; $shortmessage = ""; $longmessage = "";
			if (preg_match("/<ErrorCode[^>]*>(.*)<\/ErrorCode>/i", $paypal_response, $match)) {
				$errorcode = $match[1];
			}
			if (preg_match("/<ShortMessage[^>]*>(.*)<\/ShortMessage>/i", $paypal_response, $match)) {
				$shortmessage = $match[1];
			}
			if (preg_match("/<LongMessage[^>]*>(.*)<\/LongMessage>/i", $paypal_response, $match)) {
				$longmessage = $match[1];
			}
			echo "Some errors occurred during handling your transaction:<br>";
			echo $errorcode . ": " . $shortmessage . "<br>";
			echo $longmessage;
			exit;
		} else if (preg_match("/<PayerID[^>]*>(.*)<\/PayerID>/i", $paypal_response, $match)) {
			$payer_id = $match[1];
			set_session("session_payer_id", $payer_id);

			$confirmation_url = "../order_confirmation.php";
			header("Location: " . $confirmation_url);
			exit;
		} else {
			echo "Can't obtain Payer information from PayPal.";
			exit;
		}

	} else {
		echo "Empty response from PayPal, please check your payment settings.";
		exit;
	}


	function getDetailsSOAP($params)
	{
		global $token;

		$soap  = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
   xmlns:xsi="http://www.w3.org/1999/XMLSchema-instance"
   xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
   xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
   xmlns:xsd="http://www.w3.org/1999/XMLSchema"
   SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">';
		$soap .= '
    <SOAP-ENV:Header>
      <RequesterCredentials
        xmlns="urn:ebay:api:PayPalAPI"
        SOAP-ENV:mustUnderstand="1">
         <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
';
		if(isset($params["username"]) && strlen($params["username"])) {
			$soap .= "<Username>" . xml_escape_string($params["username"]) . "</Username>\r\n";
		} else {
			$soap .= "<Username/>\r\n";
		}
		if(isset($params["password"]) && strlen($params["password"])) {
			$soap .= "<Password>" . xml_escape_string($params["password"]) . "</Password>\r\n";
		} else {
			$soap .= "<Password/>\r\n";
		}
		if(isset($params["subject"]) && strlen($params["subject"])) {
			$soap .= "<Subject>" . xml_escape_string($params["subject"]) . "</Subject>";
		} else {
			$soap .= "<Subject/>";
		}
		$soap .= '
         </Credentials>
      </RequesterCredentials>
		</SOAP-ENV:Header>
		<SOAP-ENV:Body>
		<GetExpressCheckoutDetailsReq xmlns="urn:ebay:api:PayPalAPI">
			<GetExpressCheckoutDetailsRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>';
		$soap .= '<Token>' . $token . '</Token>';
		$soap .= '</GetExpressCheckoutDetailsRequest>
    </GetExpressCheckoutDetailsReq>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

		return $soap;
	}

?>