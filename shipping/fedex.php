<?php

	if (!strlen($external_url) || !strlen($country_code)) {
		return;
	}
	$domestic = (strtolower($country_code) == "us");
	if ($domestic && !strlen($postal_code)) { return; }
	if (($domestic || strtolower($country_code) == "ca") && !strlen($state_code)) { return; }

	// Run world around request
	$xml = fedex_prepare_rate_request($module_params);
	if (strlen($xml))
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $external_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		set_curl_options($ch, $module_params);

		$fedex_response = curl_exec($ch);
		
		$fp = fopen('test.txt', 'a');
		fwrite($fp, $fedex_response);
		
		curl_close($ch);

		if (!strlen($fedex_response)) {
			$r->errors = "Empty response from FedEx.<br>\r\n";
		} elseif (preg_match("/<Error>\s*<Code>(.*)<\/Code>\s*<Message>(.*)<\/Message>\s*<\/Error>/si", $fedex_response, $matches)) {
			$error_code = $matches[1];
			$error_message = $matches[2];
			$r->errors = "FedEx Error occured: " . $error_message . " (ErrorCode: " . $error_code .  ")<br>\r\n";
		} elseif (preg_match("/<SoftError>.*<Code>(.*)<\/Code>\s*<Message>(.*)<\/Message>\s*<\/SoftError>/si", $fedex_response, $matches)) {
			$error_code = $matches[1];
			$error_message = $matches[2];
			$r->errors = "FedEx Error occured: " . $error_message . " (ErrorCode: " . $error_code .  ")<br>\r\n";
		} else {
			$entries = array();
			preg_match_all ("/<Entry>(.*)<\/Entry>/Usi", $fedex_response, $matches, PREG_SET_ORDER);
			for ($i = 0; $i < sizeof($matches); $i++) {
				$entries[] = $matches[$i][1];
			}

			for ($i = 0; $i < sizeof($entries); $i++) {
				$entry = $entries[$i];
				if (preg_match ("/<Service>([^<]*)<\/Service>.*<CurrencyCode>([^<]*)<\/CurrencyCode>.*<NetCharge>([^<]*)<\/NetCharge>/si", $entry, $matches)) {
					$service = $matches[1];
					$cur_code = $matches[2];
					$net_charge = $matches[3];
					for ($ms = 0; $ms < sizeof($module_shipping); $ms++) {
						list($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $row_shipping_cost, $row_tare_weight, $row_shipping_taxable) = $module_shipping[$ms];
						if (strtoupper($row_shipping_type_code) == strtoupper($service)) {
							$shipping_types[] = array($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, ($net_charge + $row_shipping_cost), $row_tare_weight, $row_shipping_taxable, $row_shipping_time);
							break;
						}
					}
				}
			}
		}
	}

	// Run domestic ground request
	if ($domestic) {
		$xml = fedex_prepare_rate_request($module_params, true);
		if (strlen($xml))
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $external_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			$fedex_response = curl_exec($ch);
			
			$fp = fopen('test.txt', 'a');
			fwrite($fp, $fedex_response);

			curl_close($ch);

			if (!strlen($fedex_response)) {
				$r->errors = "Empty response from FedEx.<br>\r\n";
			} elseif (preg_match("/<Error>\s*<Code>(.*)<\/Code>\s*<Message>(.*)<\/Message>\s*<\/Error>/si", $fedex_response, $matches)) {
				$error_code = $matches[1];
				$error_message = $matches[2];
				$r->errors = "FedEx Error occured: " . $error_message . " (ErrorCode: " . $error_code .  ")<br>\r\n";
			} elseif (preg_match("/<SoftError>.*<Code>(.*)<\/Code>\s*<Message>(.*)<\/Message>\s*<\/SoftError>/si", $fedex_response, $matches)) {
				$error_code = $matches[1];
				$error_message = $matches[2];
				$r->errors = "FedEx Error occured: " . $error_message . " (ErrorCode: " . $error_code .  ")<br>\r\n";
			} else {
				$entries = array();
				preg_match_all ("/<Entry>(.*)<\/Entry>/Usi", $fedex_response, $matches, PREG_SET_ORDER);
				for ($i = 0; $i < sizeof($matches); $i++) {
					$entries[] = $matches[$i][1];
				}

				for ($i = 0; $i < sizeof($entries); $i++) {
					$entry = $entries[$i];
					if (preg_match ("/<Service>([^<]*)<\/Service>.*<NetCharge>([^<]*)<\/NetCharge>/si", $entry, $matches)) {
						$service = $matches[1];
						$net_charge = $matches[2];
						for ($ms = 0; $ms < sizeof($module_shipping); $ms++) {
							list($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $row_shipping_cost, $row_tare_weight, $row_shipping_taxable) = $module_shipping[$ms];
							if (strtoupper($row_shipping_type_code) == strtoupper($service)) {
								$shipping_types[] = array($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, ($net_charge + $row_shipping_cost), $row_tare_weight, $row_shipping_taxable, $row_shipping_time);
								break;
							}
						}
					}
				}
			}
		}
	}

	function fedex_prepare_rate_request($module_params, $domestic = false)
	{
		global $r, $shipping_weight, $state_code, $country_code, $postal_code;
		global $goods_total, $currency;

		// define some parameters
		$errors = "";
		$packaging = isset($module_params["Packaging"]) ? $module_params["Packaging"] : "";
		$origin_state_code = isset($module_params["StateOrProvinceCode"]) ? $module_params["StateOrProvinceCode"] : "";
		$origin_postal_code = isset($module_params["PostalCode"]) ? $module_params["PostalCode"] : "";
		$origin_country_code = isset($module_params["CountryCode"]) ? $module_params["CountryCode"] : "";

		$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
		$xml .= "<FDXRateAvailableServicesRequest xmlns:api=\"http://www.fedex.com/fsmapi\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"FDXRateAvailableServicesRequest.xsd\">\r\n";

		$xml .= "<RequestHeader>\r\n";
		// CustomerTransactionIdentifier
		if (isset($module_params["CustomerTransactionIdentifier"]) && strlen($module_params["CustomerTransactionIdentifier"])) {
			$xml .= "<CustomerTransactionIdentifier>" . $module_params["CustomerTransactionIdentifier"] . "</CustomerTransactionIdentifier>\r\n";
		}
		// AccountNumber (required)
		if (isset($module_params["AccountNumber"]) && strlen($module_params["AccountNumber"])) {
			$xml .= "<AccountNumber>" . $module_params["AccountNumber"] . "</AccountNumber>\r\n";
		} else {
			$errors .= "FedEx parameter AccountNumber is required.<br>\r\n" ;
		}
		// MeterNumber (required)
		if (isset($module_params["MeterNumber"]) && strlen($module_params["MeterNumber"])) {
			$xml .= "<MeterNumber>" . $module_params["MeterNumber"] . "</MeterNumber>\r\n";
		} else {
			$errors .= "FedEx parameter MeterNumber is required.<br>\r\n" ;
		}
		// CarrierCode (required) - FDXE, FDXG
		/*
		if (isset($module_params["CarrierCode"]) && strlen($module_params["CarrierCode"])) {
			$xml .= "<CarrierCode>" . $module_params["CarrierCode"] . "</CarrierCode>\r\n";
		} else {
			$errors .= "FedEx parameter CarrierCode is required.<br>\r\n" ;
		}
		*/
		if ($domestic) {
			$xml .= "<CarrierCode>FDXG</CarrierCode>\r\n";
		} else {
			$xml .= "<CarrierCode>FDXE</CarrierCode>\r\n";
		}
		$xml .= "</RequestHeader>\r\n";

		// If Sunday or Saturday use next Monday for ShipDate
		$week_day = date("w");
		if ($week_day == 0) {
			$days_off = 1;
		} elseif ($week_day == 6) {
			$days_off = 2;
		} else {
			$days_off = 0;
		}
		$ship_date = mktime (0, 0, 0, date("n"), date("j") + $days_off, date("Y"));
		// ShipDate (required)
		$xml .= "<ShipDate>" . date("Y-m-d", $ship_date) . "</ShipDate>\r\n";

		// ReturnShipmentIndicator (required) - NONRETURN, PRINTRETURNLABEL, EMAILLABEL
		if (isset($module_params["ReturnShipmentIndicator"]) && strlen($module_params["ReturnShipmentIndicator"])) {
			$xml .= "<ReturnShipmentIndicator>" . $module_params["ReturnShipmentIndicator"] . "</ReturnShipmentIndicator>\r\n";
		}
		// DropoffType (required) - REGULARPICKUP, REQUESTCOURIER, DROPBOX, BUSINESSSERVICECENTER, STATION
		if (isset($module_params["DropoffType"]) && strlen($module_params["DropoffType"])) {
			$xml .= "<DropoffType>" . $module_params["DropoffType"] . "</DropoffType>\r\n";
		}

		// Service (optional) - PRIORITYOVERNIGHT, STANDARDOVERNIGHT, FIRSTOVERNIGHT, FEDEX2DAY, FEDEXEXPRESSSAVER,
		// INTERNATIONALPRIORITY, INTERNATIONALECONOMY, INTERNATIONALFIRST, FEDEX1DAYFREIGHT, FEDEX2DAYFREIGHT
		// FEDEX3DAYFREIGHT, FEDEXGROUND, GROUNDHOMEDELIVERY, INTERNATIONALPRIORITYFREIGHT, INTERNATIONALECONOMYFREIGHT, EUROPEFIRSTINTERNATIONALPRIORITY
		if (isset($module_params["Service"]) && strlen($module_params["Service"])) {
			$xml .= "<Service>" . $module_params["Service"] . "</Service>\r\n";
		}
		// Packaging (required) - FEDEXENVELOPE, FEDEXPAK, FEDEXBOX, FEDEXTUBE, FEDEX10KGBOX, FEDEX25KGBOX, YOURPACKAGING
		if (strlen($packaging)) {
			$xml .= "<Packaging>" . $packaging . "</Packaging>\r\n";
		} else {
			$errors .= "FedEx parameter Packaging is required.<br>\r\n" ;
		}
		// WeightUnits (required) - LBS, KGS
		if (isset($module_params["WeightUnits"]) && strlen($module_params["WeightUnits"])) {
			$xml .= "<WeightUnits>" . $module_params["WeightUnits"] . "</WeightUnits>\r\n";
		}
		if ($shipping_weight > 0) {
			$xml .= "<Weight>" . round($shipping_weight, 1) . "</Weight>\r\n";
		} else {
			$xml .= "<Weight>1.0</Weight>\r\n";
		}

		// ListRate (optional) - true, 1
		if (isset($module_params["ListRate"]) && strlen($module_params["ListRate"])) {
			$xml .= "<ListRate>" . $module_params["ListRate"] . "</ListRate>\r\n";
		}

		$xml .= "<OriginAddress>\r\n";
		if (strlen($origin_state_code)) {
			$xml .= "<StateOrProvinceCode>" . $origin_state_code . "</StateOrProvinceCode>\r\n";
		} elseif (strtolower($origin_country_code) == "us" || strtolower($origin_country_code) == "ca") {
			$errors .= "FedEx parameter StateOrProvinceCode is required.<br>\r\n" ;
		}
		if (strlen($origin_postal_code)) {
			$xml .= "<PostalCode>" . $origin_postal_code . "</PostalCode>\r\n";
		} elseif (strtolower($origin_country_code) == "us" || strtolower($origin_country_code) == "ca") {
			$errors .= "FedEx parameter PostalCode is required.<br>\r\n" ;
		}
		if (strlen($origin_country_code)) {
			$xml .= "<CountryCode>" . $origin_country_code . "</CountryCode>\r\n";
		} else {
			$errors .= "FedEx parameter CountryCode is required.<br>\r\n" ;
		}
		$xml .= "</OriginAddress>\r\n";

		$xml .= "<DestinationAddress>\r\n";
		if (strlen($state_code)) {
			$xml .= "<StateOrProvinceCode>" . $state_code . "</StateOrProvinceCode>\r\n";
		} elseif (strtolower($country_code) == "us" || strtolower($country_code) == "ca") {
			$errors .= "Destination StateOrProvinceCode is required.<br>\r\n" ;
		}
		if (strlen($postal_code)) {
			$xml .= "<PostalCode>" . $postal_code . "</PostalCode>\r\n";
		} elseif (strtolower($country_code) == "us" || strtolower($country_code) == "ca") {
			$errors .= "Destination PostalCode is required.<br>\r\n" ;
		}
		if (strlen($country_code)) {
			$xml .= "<CountryCode>" . $country_code . "</CountryCode>\r\n";
		} else {
			$errors .= "Destination CountryCode is required.<br>\r\n" ;
		}
		$xml .= "</DestinationAddress>\r\n";

		$xml .= "<Payment>\r\n";
		// PayorType (optional) - Defaults to SENDER
		if (isset($module_params["PayorType"]) && strlen($module_params["PayorType"])) {
			$xml .= "<PayorType>" . $module_params["PayorType"] . "</PayorType>\r\n";
		}
		$xml .= "</Payment>\r\n";

		if (strtoupper($packaging) == "YOURPACKAGING") {
			$xml .= "<Dimensions>\r\n"; // Only applicable if the package type is YOURPACKAGING.
			if (isset($module_params["Length"]) && strlen($module_params["Length"])) {
				$xml .= "<Length>" . $module_params["Length"] . "</Length>\r\n";
			}
			if (isset($module_params["Width"]) && strlen($module_params["Width"])) {
				$xml .= "<Width>" . $module_params["Width"] . "</Width>\r\n";
			}
			if (isset($module_params["Height"]) && strlen($module_params["Height"])) {
				$xml .= "<Height>" . $module_params["Height"] . "</Height>\r\n";
			}
			// Units - IN, CM
			if (isset($module_params["Units"]) && strlen($module_params["Units"])) {
				$xml .= "<Units>" . $module_params["Units"] . "</Units>\r\n";
			}
			$xml .= "</Dimensions>\r\n";
		}

		$xml .= "<DeclaredValue>\r\n";
		$xml .= "<Value>" . round($goods_total*$currency["rate"], 2) . "</Value>\r\n";
		$xml .= "<CurrencyCode>" . $currency["code"] . "</CurrencyCode>\r\n";
		$xml .= "</DeclaredValue>\r\n";

		if (isset($module_params["Alcohol"]) && strlen($module_params["Alcohol"])) {
			$xml .= "<Alcohol>" . $module_params["Alcohol"] . "</Alcohol>\r\n";
		}

		if (isset($module_params["PackageCount"]) && strlen($module_params["PackageCount"])) {
			$xml .= "<PackageCount>" . $module_params["PackageCount"] . "</PackageCount>\r\n";
		}

		$xml .= "</FDXRateAvailableServicesRequest>\r\n";

		if (strlen($errors)) {
			$xml = "";
			$r->errors = $errors;
		}

		return $xml;
	}

?>