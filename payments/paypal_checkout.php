<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_checkout.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * PayPal Express Checkout(www.paypal.com) transaction handler by http://www.viart.com/
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/date_functions.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");


	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");

	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}

	// get payment data
	$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables);

	// get some variables from our payment settings
	$sandbox    = isset($payment_parameters["sandbox"]) ? $payment_parameters["sandbox"] : 0;
	$sslcert    = isset($payment_parameters["sslcert"]) ? $payment_parameters["sslcert"] : "";
	$shorterror  = isset($payment_parameters["shorterror"]) ? $payment_parameters["shorterror"] : 0;
	$payment_parameters["ButtonSource"] = "ViArt_ShoppingCart_EC";
	$payment_parameters["buttonsource"] = "ViArt_ShoppingCart_EC";

	if ($sandbox == 1) {
		$api_url = "https://api.sandbox.paypal.com/2.0/";
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$api_url = "https://api.paypal.com/2.0/";
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}

	$soap = getCheckoutSOAP($payment_parameters);

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
		} 

		if (preg_match_all("/<Errors[^>]*>.*<\\/Errors>/Uis", $paypal_response, $matches)) {
			$error_message = "";
			for($m = 0; $m < sizeof($matches[0]); $m++) {
				$errors_block = $matches[0][$m];
				$errorcode = ""; $shortmessage = ""; $longmessage = ""; $severitycode = "";
				if (preg_match("/<ErrorCode[^>]*>(.*)<\/ErrorCode>/i", $errors_block, $match)) {
					$errorcode = $match[1];
				}
				if (preg_match("/<ShortMessage[^>]*>(.*)<\/ShortMessage>/i", $errors_block, $match)) {
					$shortmessage = $match[1];
				}
				if (preg_match("/<LongMessage[^>]*>(.*)<\/LongMessage>/i", $errors_block, $match)) {
					$longmessage = $match[1];
				}
				if (preg_match("/<SeverityCode[^>]*>(.*)<\/SeverityCode>/i", $errors_block, $match)) {
					$severitycode = $match[1];
				}

				// show only errors 
				if (preg_match("/Error/i", $severitycode)) {
					$error_message .= $errorcode . ": ";
					if ($shorterror) {
						$error_message .= $shortmessage;
					} else {
						$error_message .= $longmessage;
					}
					$error_message .= "<br>";
				}
			}
			if ($error_message) {
				echo $error_message;
				exit;
			}
		} 

		if (preg_match("/<Token[^>]*>(.*)<\/Token>/i", $paypal_response, $match)) {
			$token = $match[1];
			$paypal_url .= "?cmd=_express-checkout&token=" . urlencode($token);
			header("Location: " . $paypal_url);
			exit;
		} else {
			echo "Can't obtain Token from PayPal.";
			exit;
		}

	} else {
		if (!$sslcert) {
			$errors = "SSLCert parameter is required for PayPal Express Checkout.";
		} else if (!file_exists($sslcert)) {
			$errors = "Can't find PayPal SSL certificate, please use absolute path like '/home/user_name/cert/cert_key_pem.txt' for SSLCert payment parameter.";
		} else if (!@fopen($sslcert, "r")) {
			$errors = "Can't read PayPal SSL certificate, please check read permissions to the file.";
		} else {
			$errors = "Empty response from PayPal, please check that your payment parameters Username, Password and SSLCert were set correctly.";
		}

		echo $errors;
		exit;
	}


	function getCheckoutSOAP($params)
	{
		global $variables;

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
      <SetExpressCheckoutReq xmlns="urn:ebay:api:PayPalAPI">
         <SetExpressCheckoutRequest xsi:type="ns:SetExpressCheckoutRequestType">
            <Version
              xmlns="urn:ebay:apis:eBLBaseComponents"
              xsi:type="xsd:string">64.0</Version>
            <SetExpressCheckoutRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
';
		$currencyID = (isset($params["currencyid"]) && strlen($params["currencyid"])) ? $params["currencyid"] : "USD";
		$OrderTotal = (isset($params["ordertotal"]) && strlen($params["ordertotal"])) ? number_format($params["ordertotal"], 2, ".", "") : "0.00";
		$soap .= '<OrderTotal xmlns="urn:ebay:apis:eBLBaseComponents" currencyID="' . xml_escape_string($currencyID) . '"
                 xsi:type="cc:BasicAmountType">' . xml_escape_string($OrderTotal) . "</OrderTotal>\r\n";
		if(isset($params["invoiceid"]) && strlen($params["invoiceid"])) {
			$soap .= "<InvoiceID>" . xml_escape_string($params["invoiceid"]) . "</InvoiceID>\r\n";
		}
		if(isset($params["buttonsource"]) && strlen($params["buttonsource"])) {
			$soap .= "<ButtonSource>" . xml_escape_string($params["buttonsource"]) . "</ButtonSource>\r\n";
		}
		if(isset($params["maxamount"]) && strlen($params["maxamount"])) {
			$soap .= "<MaxAmount>" . xml_escape_string(number_format($params["maxamount"], 2, ".", "")) . "</MaxAmount>";
		}
		if(isset($params["orderdescription"]) && strlen($params["orderdescription"])) {
			$soap .= "<OrderDescription>" . xml_escape_string($params["orderdescription"]) . "</OrderDescription>\r\n";
		}
		if(isset($params["returnurl"]) && strlen($params["returnurl"])) {
			$soap .= '<ReturnURL xsi:type="xsd:string">' . xml_escape_string($params["returnurl"]) . "</ReturnURL>\r\n";
		}
		if(isset($params["cancelurl"]) && strlen($params["cancelurl"])) {
			$soap .= '<CancelURL xsi:type="xsd:string">' . xml_escape_string($params["cancelurl"]) . "</CancelURL>\r\n";
		}
		
		if(isset($params["BrandName"]) && strlen($params["BrandName"])) {
			$soap .= "<BrandName>" . xml_escape_string($params["BrandName"]) . "</BrandName>\r\n";
		}
		
		if(isset($params["PageStyle"]) && strlen($params["PageStyle"])) {
			$soap .= "<PageStyle>" . xml_escape_string($params["PageStyle"]) . "</PageStyle>\r\n";
		}

		$ShipName = (isset($params["shipname"])) ? $params["shipname"] : "";
		$ShipStreet1 = (isset($params["shipstreet1"])) ? $params["shipstreet1"] : "";
		$ShipStreet2 = (isset($params["shipstreet2"])) ? $params["shipstreet2"] : "";
		$ShipCityName = (isset($params["shipcityname"])) ? $params["shipcityname"] : "";
		$ShipStateOrProvince = (isset($params["shipstateorprovince"])) ? $params["shipstateorprovince"] : "";
		$ShipCountry = (isset($params["shipcountry"])) ? $params["shipcountry"] : "";
		$ShipPostalCode = (isset($params["shippostalcode"])) ? $params["shippostalcode"] : "";
		if (strlen($ShipName) || strlen($ShipStreet1) || strlen($ShipStreet2) || strlen($ShipCityName) || strlen($ShipStateOrProvince) || strlen($ShipCountry) || strlen($ShipPostalCode)) {
			if (!strlen($ShipName)) {
				$ship_delivery_name = $variables["delivery_name"];
				$ship_delivery_first_name = $variables["delivery_first_name"];
				$ship_delivery_last_name = $variables["delivery_last_name"];
				if (strlen($ship_delivery_name)) {
					$ShipName = $ship_delivery_name;
				} else if (strlen($ship_delivery_first_name) || strlen($ship_delivery_last_name)) {
					$ShipName = trim($ship_delivery_first_name . " " . $ship_delivery_last_name);
				}
			}
			$soap .= "<Address>\r\n";
			if (strlen($ShipName)) { $soap .= "<Name>" . xml_escape_string($ShipName) . "</Name>\r\n"; }
			if (strlen($ShipStreet1)) { $soap .= "<Street1>" . xml_escape_string($ShipStreet1) . "</Street1>\r\n"; }
			if (strlen($ShipStreet2)) { $soap .= "<Street2>" . xml_escape_string($ShipStreet2) . "</Street2>\r\n"; }
			if (strlen($ShipCityName)) { $soap .= "<CityName>" . xml_escape_string($ShipCityName) . "</CityName>\r\n"; }
			if (strlen($ShipStateOrProvince)) { $soap .= "<StateOrProvince>" . xml_escape_string($ShipStateOrProvince) . "</StateOrProvince>\r\n"; }
			if (strlen($ShipCountry)) { $soap .= "<Country>" . xml_escape_string($ShipCountry) . "</Country>\r\n"; }
			if (strlen($ShipPostalCode)) { $soap .= "<PostalCode>" . xml_escape_string($ShipPostalCode) . "</PostalCode>\r\n"; }
			$soap .= "</Address>\r\n";
		}
		$soap .= '</SetExpressCheckoutRequestDetails>
         </SetExpressCheckoutRequest>
      </SetExpressCheckoutReq>
   </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

	$fp = fopen('d:/inetpub/customeuropeanplates/paypal.txt', 'w');
	fwrite($fp, $soap);
	fclose($fp);

		return $soap;
	}

?>