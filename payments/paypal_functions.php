<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_functions.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * PayPal functions by ViArt Ltd - http://www.viart.com/
 */

	function paypal_direct_payment($params)
	{
		global $token, $variables;

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
		if(isset($params["Subject"]) && strlen($params["Subject"])) {
			$soap .= "<Subject>" . xml_escape_string($params["subject"]) . "</Subject>";
		} else {
			$soap .= "<Subject/>";
		}
		$soap .= '
         </Credentials>
      </RequesterCredentials>
		</SOAP-ENV:Header>
		<SOAP-ENV:Body>
		<DoDirectPaymentReq xmlns="urn:ebay:api:PayPalAPI">
			<DoDirectPaymentRequest xmlns="urn:ebay:api:PayPalAPI">
				<Version xmlns="urn:ebay:apis:eBLBaseComponents">1.0</Version>
					<DoDirectPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">';
		if(isset($params["paymentaction"]) && strlen($params["paymentaction"])) {
			$soap .= "<PaymentAction>" . xml_escape_string($params["paymentaction"]) . "</PaymentAction>";
		} else {
			$soap .= "<PaymentAction>Sale</PaymentAction>";
		}

		$currencyID = (isset($params["currencyid"]) && strlen($params["currencyid"])) ? $params["currencyid"] : "USD";

		$soap .= "<PaymentDetails>";
		if(isset($params["ordertotal"]) && strlen($params["ordertotal"])) {
			$soap .= '<OrderTotal currencyID="' . xml_escape_string($currencyID) . '">' . xml_escape_string(number_format($params["ordertotal"], 2, ".", "")) . '</OrderTotal>';
		}
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
		if(isset($params["custom"]) && strlen($params["custom"])) {
			$soap .= "<Custom>" . xml_escape_string($params["custom"]) . "</Custom>\r\n";
		}
		if(isset($params["invoiceid"]) && strlen($params["invoiceid"])) {
			$soap .= "<InvoiceID>" . xml_escape_string($params["invoiceid"]) . "</InvoiceID>\r\n";
		}
		if(isset($params["buttonsource"]) && strlen($params["buttonsource"])) {
			$soap .= "<ButtonSource>" . xml_escape_string($params["buttonsource"]) . "</ButtonSource>\r\n";
		}

		$shipname = (isset($params["shipname"])) ? $params["shipname"] : "";
		$shipstreet1 = (isset($params["shipstreet1"])) ? $params["shipstreet1"] : "";
		$shipstreet2 = (isset($params["shipstreet2"])) ? $params["shipstreet2"] : "";
		$shipcityname = (isset($params["shipcityname"])) ? $params["shipcityname"] : "";
		$shipstateorprovince = (isset($params["shipstateorprovince"])) ? $params["shipstateorprovince"] : "";
		$shipcountry = (isset($params["shipcountry"])) ? $params["shipcountry"] : "";
		$shippostalcode = (isset($params["shippostalcode"])) ? $params["shippostalcode"] : "";
		if (strlen($shipname) || strlen($shipstreet1) || strlen($shipstreet2) || strlen($shipcityname) || strlen($shipstateorprovince) || strlen($shipcountry) || strlen($shippostalcode)) {
			$soap .= "<ShipToAddress>\r\n";
			if (!strlen($shipname)) {
				$ship_delivery_name = $variables["delivery_name"];
				$ship_delivery_first_name = $variables["delivery_first_name"];
				$ship_delivery_last_name = $variables["delivery_last_name"];
				if (strlen($ship_delivery_name)) {
					$shipname = $ship_delivery_name;
				} else if (strlen($ship_delivery_first_name) || strlen($ship_delivery_last_name)) {
					$shipname = trim($ship_delivery_first_name . " " . $ship_delivery_last_name);
				}
			}
			if (strlen($shipname)) { $soap .= "<Name>" . xml_escape_string($shipname) . "</Name>\r\n"; }
			if (strlen($shipstreet1)) { $soap .= "<Street1>" . xml_escape_string($shipstreet1) . "</Street1>\r\n"; }
			if (strlen($shipstreet2)) { $soap .= "<Street2>" . xml_escape_string($shipstreet2) . "</Street2>\r\n"; }
			if (strlen($shipcityname)) { $soap .= "<CityName>" . xml_escape_string($shipcityname) . "</CityName>\r\n"; }
			if (!strlen($shipstateorprovince) && (strtoupper($shipcountry) == "GB" || strtoupper($shipcountry) == "CA")) {
				$shipstateorprovince = "N/A";
			}
			if (strlen($shipstateorprovince)) { $soap .= "<StateOrProvince>" . xml_escape_string($shipstateorprovince) . "</StateOrProvince>\r\n"; }
			if (strlen($shipcountry)) { $soap .= "<Country>" . xml_escape_string($shipcountry) . "</Country>\r\n"; }
			if (strlen($shippostalcode)) { $soap .= "<PostalCode>" . xml_escape_string($shippostalcode) . "</PostalCode>\r\n"; }
			$soap .= "</ShipToAddress>\r\n";
		}

		/* information about ordering items
			$soap .= "<PaymentItem>";
			$soap .= "<Name>" . $item_name . "</Name>";
			$soap .= "<Number>" . $item_id . "</Number>";
			$soap .= "<Quantity>" . $quantity . "</Quantity>";
			$soap .= '<SalesTax currencyID="' . $currencyID . '">' . $sales_tax . "</SalesTax>\r\n";
			$soap .= '<Amount currencyID="' . $currencyID . '">' . $amount . "</Amount>\r\n";
			$soap .= "</PaymentItem>";
		//*/

		$soap .= "</PaymentDetails>";
		$soap .= "<CreditCard>";
		if(isset($params["creditcardtype"]) && strlen($params["creditcardtype"])) {
			$cc_type = $params["creditcardtype"];
			$cc_types = array ("visa" => "Visa", "mc" => "MasterCard", "mastercard" => "MasterCard", "discover" => "Discover",
			"amex" => "Amex", "american express" => "Amex", "americanexpress" => "Amex", "switch" => "Switch", "solo" => "Solo");
			if (isset($cc_types[strtolower($cc_type)])) {
				$cc_type = $cc_types[strtolower($cc_type)];
			}
			$soap .= "<CreditCardType>" . xml_escape_string($cc_type) . "</CreditCardType>\r\n";
		}

		if(isset($params["creditcardnumber"]) && strlen($params["creditcardnumber"])) {
			$soap .= "<CreditCardNumber>" . xml_escape_string($params["creditcardnumber"]) . "</CreditCardNumber>\r\n";
		}
		if(isset($params["expmonth"]) && strlen($params["expmonth"])) {
			$soap .= "<ExpMonth>" . xml_escape_string($params["expmonth"]) . "</ExpMonth>\r\n";
		}
		if(isset($params["expyear"]) && strlen($params["expyear"])) {
			$soap .= "<ExpYear>" . xml_escape_string($params["expyear"]) . "</ExpYear>\r\n";
		}
		$soap .= "<CardOwner>";
		if(isset($params["payer"]) && strlen($params["payer"])) {
			$soap .= "<Payer>" . xml_escape_string($params["payer"]) . "</Payer>\r\n";
		}

		$soap .= "<PayerName>";
		if(isset($params["ccsalutation"]) && strlen($params["ccsalutation"])) {
			$soap .= "<Salutation>" . xml_escape_string($params["ccsalutation"]) . "</Salutation>\r\n";
		}
		if(isset($params["ccfirstname"]) && strlen($params["ccfirstname"])) {
			$soap .= "<FirstName>" . xml_escape_string($params["ccfirstname"]) . "</FirstName>\r\n";
		}
		if(isset($params["ccmiddlename"]) && strlen($params["ccmiddlename"])) {
			$soap .= "<MiddleName>" . xml_escape_string($params["ccmiddlename"]) . "</MiddleName>\r\n";
		}
		if(isset($params["cclastname"]) && strlen($params["cclastname"])) {
			$soap .= "<LastName>" . xml_escape_string($params["cclastname"]) . "</LastName>\r\n";
		}
		if(isset($params["ccsuffix"]) && strlen($params["ccsuffix"])) {
			$soap .= "<Suffix>" . xml_escape_string($params["ccsuffix"]) . "</Suffix>\r\n";
		}
		$soap .= "</PayerName>";

		$name = (isset($params["name"])) ? $params["name"] : "";
		$street1 = (isset($params["street1"])) ? $params["street1"] : "";
		$street2 = (isset($params["street2"])) ? $params["street2"] : "";
		$cityname = (isset($params["cityname"])) ? $params["cityname"] : "";
		$stateorprovince = (isset($params["stateorprovince"])) ? $params["stateorprovince"] : "";
		$country = (isset($params["country"])) ? $params["country"] : "";
		$postalcode = (isset($params["postalcode"])) ? $params["postalcode"] : "";
		if (strlen($name) || strlen($street1) || strlen($street2) || strlen($cityname) || strlen($stateorprovince) || strlen($country) || strlen($postalcode)) {
			$soap .= "<Address>\r\n";
			if (strlen($name)) { $soap .= "<Name>" . xml_escape_string($name) . "</Name>\r\n"; }
			if (strlen($street1)) { $soap .= "<Street1>" . xml_escape_string($street1) . "</Street1>\r\n"; }
			if (strlen($street2)) { $soap .= "<Street2>" . xml_escape_string($street2) . "</Street2>\r\n"; }
			if (strlen($cityname)) { $soap .= "<CityName>" . xml_escape_string($cityname) . "</CityName>\r\n"; }
			if (strlen($stateorprovince)) { $soap .= "<StateOrProvince>" . xml_escape_string($stateorprovince) . "</StateOrProvince>\r\n"; }
			if (strlen($country)) { $soap .= "<Country>" . xml_escape_string($country) . "</Country>\r\n"; }
			if (strlen($postalcode)) { $soap .= "<PostalCode>" . xml_escape_string($postalcode) . "</PostalCode>\r\n"; }
			$soap .= "</Address>\r\n";
		}

		$soap .= "</CardOwner>";
		if(isset($params["cvv2"]) && strlen($params["cvv2"])) {
			$soap .= "<CVV2>" . xml_escape_string($params["cvv2"]) . "</CVV2>\r\n";
		}
		if(isset($params["startmonth"]) && strlen($params["startmonth"])) {
			$soap .= "<StartMonth>" . xml_escape_string($params["startmonth"]) . "</StartMonth>\r\n";
		}
		if(isset($params["startyear"]) && strlen($params["startyear"])) {
			$soap .= "<StartYear>" . xml_escape_string($params["startyear"]) . "</StartYear>\r\n";
		}
		if(isset($params["issuenumber"]) && strlen($params["issuenumber"])) {
			$soap .= "<IssueNumber>" . xml_escape_string($params["issuenumber"]) . "</IssueNumber>\r\n";
		}

		$soap .= "</CreditCard>";

		if(isset($params["ipaddress"]) && strlen($params["ipaddress"])) {
			$soap .= "<IPAddress>" . xml_escape_string($params["ipaddress"]) . "</IPAddress>\r\n";
		} else {
			$soap .= "<IPAddress>" . get_ip() . "</IPAddress>\r\n";
		}
		$soap .= "<MerchantSessionId>" . session_id() . "</MerchantSessionId>\r\n";

		$soap .= "
				</DoDirectPaymentRequestDetails>
			</DoDirectPaymentRequest>
		</DoDirectPaymentReq>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>";

		return $soap;
	}

?>