<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_payment.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * PayPal Express Checkout (www.paypal.com) handler by http://www.viart.com/
 */

	// get some variables from our payment settings
	$sandbox        = isset($payment_parameters["sandbox"]) ? $payment_parameters["sandbox"] : 0;
	$shorterror      = isset($payment_parameters["shorterror"]) ? $payment_parameters["shorterror"] : 0;
	$sslcert        = isset($payment_parameters["sslcert"]) ? $payment_parameters["sslcert"] : "";
	$token          = get_session("session_token");
	$payer_id       = get_session("session_payer_id");


	if ($sandbox == 1) {
		$api_url = "https://api-aa.sandbox.paypal.com/2.0/";
	} else {
		$api_url = "https://api-aa.paypal.com/2.0/";
	}

	$soap = getPaymentSOAP($payment_parameters);

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
			$error_message  = "Some errors occurred during handling your transaction:<br>";
			$error_message .= $faultcode . ": " . $faultstring;
			return;
		} 

		if (preg_match_all("/<Errors[^>]*>.*<\\/Errors>/Uis", $paypal_response, $matches)) {
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
				return;
			}
		} 

		if (preg_match("/<TransactionID[^>]*>(.*)<\/TransactionID>/i", $paypal_response, $match)) {
			$transaction_id = $match[1];

			$payment_status = ""; $pending_reason = "";
			if (preg_match("/<PaymentStatus[^>]*>(.*)<\/PaymentStatus>/i", $paypal_response, $match)) {
				$payment_status = $match[1];
			}
			if (preg_match("/<PendingReason[^>]*>(.*)<\/PendingReason>/i", $paypal_response, $match)) {
				$pending_reason = $match[1];
			}
			if (strtolower($payment_status) == "pending") {
				$pending_message = strlen($pending_reason) ? $pending_reason : "Pending";
			} else if (!strlen($payment_status)) {
				$error_message = "Can't obtain status for your order.";
			} else if (strtolower($payment_status) != "completed") {	// check the payment_status is Completed
				$error_message = "Your payment status is " . $payment_status;
			}

		} else {
			$error_message  = "Can't obtain transaction information from PayPal.";
			return;
		}

	} else {
		$error_message  = "Empty response from PayPal, please check your payment settings.";
		return;
	}


	function getPaymentSOAP($params)
	{
		global $token, $payer_id;

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
		<DoExpressCheckoutPaymentReq xmlns="urn:ebay:api:PayPalAPI">
			<DoExpressCheckoutPaymentRequest>
				<Version xmlns="urn:ebay:apis:eBLBaseComponents">1.0</Version>
					<DoExpressCheckoutPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">';
		if(isset($params["paymentaction"]) && strlen($params["paymentaction"])) {
			$soap .= "<PaymentAction>" . xml_escape_string($params["paymentaction"]) . "</PaymentAction>";
		} else {
			$soap .= "<PaymentAction>Sale</PaymentAction>";
		}
		$soap .= '<Token>' . $token . '</Token>';
		$soap .= '<PayerID>' . $payer_id . '</PayerID>';
		$soap .= '<PaymentDetails>';

		$currencyID = (isset($params["currencyid"]) && strlen($params["currencyid"])) ? $params["currencyid"] : "USD";
		$OrderTotal = (isset($params["ordertotal"]) && strlen($params["ordertotal"])) ? number_format($params["ordertotal"], 2, ".", "") : "0.00";

		$soap .= '<OrderTotal currencyID="' . xml_escape_string($currencyID) . '">' . xml_escape_string($OrderTotal) . "</OrderTotal>\r\n";
		if(isset($params["itemtotal"]) && strlen($params["itemtotal"])) {
			$soap .= '<ItemTotal currencyID="' . xml_escape_string($currencyID) . '">' . xml_escape_string(number_format($params["itemtotal"], 2, ".", "")) . '</ItemTotal>';
		}
		if(isset($params["shippingtotal"]) && strlen($params["shippingtotal"])) {
			$soap .= '<ShippingTotal currencyID="' . xml_escape_string($currencyID) . '">' . xml_escape_string(number_format($params["shippingtotal"], 2, ".", "")) . '</ShippingTotal>';
		}
		if(isset($params["handlingtotal"]) && strlen($params["handlingtotal"])) {
			$soap .= '<HandlingTotal currencyID="' . xml_escape_string($currencyID) . '">' . xml_escape_string(number_format($params["handlingtotal"], 2, ".", "")) . '</HandlingTotal>';
		}
		if(isset($params["taxtotal"]) && strlen($params["taxtotal"])) {
			$soap .= '<TaxTotal currencyID="' . xml_escape_string($currencyID) . '">' . xml_escape_string(number_format($params["taxtotal"], 2, ".", "")) . '</TaxTotal>';
		}
		if(isset($params["orderdescription"]) && strlen($params["orderdescription"])) {
			$soap .= "<OrderDescription>" . xml_escape_string($params["orderdescription"]) . "</OrderDescription>\r\n";
		}
		if(isset($params["buttonsource"]) && strlen($params["buttonsource"])) {
			$soap .= "<ButtonSource>" . xml_escape_string($params["buttonsource"]) . "</ButtonSource>\r\n";
		}

		$ShipName = (isset($params["shipname"])) ? $params["shipname"] : "";
		$ShipStreet1 = (isset($params["shipstreet1"])) ? $params["shipstreet1"] : "";
		$ShipStreet2 = (isset($params["shipstreet2"])) ? $params["shipstreet2"] : "";
		$ShipCityName = (isset($params["shipcityname"])) ? $params["shipcityname"] : "";
		$ShipStateOrProvince = (isset($params["shipstateorprovince"])) ? $params["shipstateorprovince"] : "";
		$ShipCountry = (isset($params["shipcountry"])) ? $params["shipcountry"] : "";
		$ShipPostalCode = (isset($params["shippostalcode"])) ? $params["shippostalcode"] : "";
		if (strlen($ShipName) || strlen($ShipStreet1) || strlen($ShipStreet2) || strlen($ShipCityName) || strlen($ShipStateOrProvince) || strlen($ShipCountry) || strlen($PostalCode)) {
			$soap .= "<ShipToAddress>\r\n";
			if (strlen($ShipName)) { $soap .= "<Name>" . xml_escape_string($ShipName) . "</Name>\r\n"; }
			if (strlen($ShipStreet1)) { $soap .= "<Street1>" . xml_escape_string($ShipStreet1) . "</Street1>\r\n"; }
			if (strlen($ShipStreet2)) { $soap .= "<Street2>" . xml_escape_string($ShipStreet2) . "</Street2>\r\n"; }
			if (strlen($ShipCityName)) { $soap .= "<CityName>" . xml_escape_string($ShipCityName) . "</CityName>\r\n"; }
			if (strlen($ShipStateOrProvince)) { $soap .= "<StateOrProvince>" . xml_escape_string($ShipStateOrProvince) . "</StateOrProvince>\r\n"; }
			if (strlen($ShipCountry)) { $soap .= "<Country>" . xml_escape_string($ShipCountry) . "</Country>\r\n"; }
			if (strlen($ShipPostalCode)) { $soap .= "<PostalCode>" . xml_escape_string($ShipPostalCode) . "</PostalCode>\r\n"; }
			$soap .= "</ShipToAddress>\r\n";
		}

			$soap .= '</PaymentDetails>
				</DoExpressCheckoutPaymentRequestDetails>
			</DoExpressCheckoutPaymentRequest>
		</DoExpressCheckoutPaymentReq>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

		return $soap;
	}

?>