<?php

	global $cart_items;

	$rated_shipment = array();
	$item_count = 0;
	foreach($cart_items as $item_in_cart){
		$item_in_cart_total_weight = $item_in_cart['full_weight'];
		$xml = ups_prepare_rate_request($module_params);
		$response = '';
		if (strlen($xml)) {
			$ch = curl_init ();
			if (!$ch) {
				$r->errors .= CURL_INIT_ERROR_MSG . "<br>\n";
				return;
			}
			curl_setopt($ch, CURLOPT_URL, $external_url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			set_curl_options($ch, $module_params);
	
			$response = curl_exec($ch);

			curl_close($ch);
		} else {
			return;
		}
		$response = trim($response);
		$orderlog = 'test.log';
		$fp = fopen($orderlog, 'a');
		fwrite($fp, $response);
		if (strlen($response)) {
			$response_parameters = array();
			preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $response, $matches, PREG_SET_ORDER);
			for($i = 0; $i < sizeof($matches); $i++) {
				$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
			}
			if (isset($response_parameters["ErrorCode"])) {
				if (strtoupper($response_parameters["ErrorSeverity"]) != 'WARNING'){
					$r->errors .= sprintf("UPS. Error occured: %s - %s <br>", $response_parameters["ErrorCode"], $response_parameters["ErrorDescription"]);
				}
			}
			$rated_shipment_match=array();
			preg_match_all("/<RatedShipment>(.*)\<\/RatedShipment>/Uis", $response, $rated_raw, PREG_SET_ORDER);
			for($i = 0; $i < sizeof($rated_raw); $i++) {
				preg_match("/<Service>\s*<Code>(.*)<\/Code>\s*<\/Service>.*<TotalCharges>.*<CurrencyCode>(.*)<\/CurrencyCode>.*<MonetaryValue>(.*)\<\/MonetaryValue>.*<\/TotalCharges>.*<RatedPackage>(.*)<\/RatedPackage>/Uis", $rated_raw[$i][1], $total_raw);
				$service_code = $total_raw[1];
				$monetary_value = $total_raw[3];
				$rated_shipment_match[] = array($service_code,$monetary_value);
			}
			if($item_count){
				foreach ($rated_shipment as $index_rated => $rated) {
					$found_rated_code = false;
					foreach ($rated_shipment_match as $rated_match) {
						if($rated[0] == $rated_match[0]){
							$found_rated_code = true;
							$rated_shipment[$index_rated][1] += $rated_match[1];
						}
					}
					if(!$found_rated_code){
						unset($rated_shipment[$index_rated]);
					}
				}
			}else{
				$rated_shipment = $rated_shipment_match;
			}
			$item_count ++;
		}
		else {
			$r->errors .= "UPS server doesn't answer.<br>\r\n";
		}
	}
	foreach ($module_shipping as $module) {
		foreach ($rated_shipment as $rated) {
			if ($module[1] == $rated[0]) {
				$module[3] += $rated[1];
				$shipping_types[] = $module;
			}
		}
	}

	function ups_prepare_rate_request($module_params)
	{
		global $r, $shipping_weight, $state_code, $country_code, $postal_code, $item_in_cart_total_weight;
		$xml = '';
		if (!strlen($country_code) || !strlen($postal_code)) {
			return $xml;
		}

		$xml  = '<?xml version="1.0"?>';
		$xml .= '<AccessRequest xml:lang="en-US">';
		if (isset($module_params["access_license_number"])) {
			$xml .= '	<AccessLicenseNumber>' . $module_params["access_license_number"] . '</AccessLicenseNumber>';
		} else {
			$r->errors .= str_replace("{param_name}", "AccessLicenseNumber", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["user_id"])) {
			$xml .= '	<UserId>' . $module_params["user_id"] . '</UserId>';
		} else {
			$r->errors .= str_replace("{param_name}", "UserId", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["user_id"]) && isset($module_params["password"]) && isset($module_params["access_license_number"])) {
			$xml .= '	<Password>' . $module_params["password"] . '</Password>';
		} else {
			$r->errors .= str_replace("{param_name}", "Password", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '</AccessRequest>';

		$xml .= '<?xml version="1.0"?>';
		$xml .= '<RatingServiceSelectionRequest xml:lang="en-US">';
		$xml .= '	<Request>';
		$xml .= '		<TransactionReference>';
		$xml .= '			<CustomerContext>Bare Bones Rate Request</CustomerContext>';
		$xml .= '			<XpciVersion>1.0</XpciVersion>';
		$xml .= '		</TransactionReference>';
		$xml .= '		<RequestAction>Rate</RequestAction>';
		$xml .= '		<RequestOption>shop</RequestOption>';
		$xml .= '	</Request>';
		$xml .= '<PickupType>';
		if (isset($module_params["PickupType"]) && strlen($module_params["PickupType"])) {
			$xml .= '	<Code>' . $module_params["PickupType"] . '</Code>';
		} else {
			$r->errors .= str_replace("{param_name}", "PickupType", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '</PickupType>';
		if (isset($module_params["CustomerClassification"]) && strlen($module_params["CustomerClassification"])) {
			$xml .= '<CustomerClassification>' . $module_params["CustomerClassification"] . '</CustomerClassification>';
		}
		$xml .= '<Shipment>';
		$xml .= '	<Shipper>';
		$xml .= '		<Address>';
		if (isset($module_params["ShipperNumber"]) && strlen($module_params["ShipperNumber"])) {
			$xml .= '			<ShipperNumber>' . $module_params["ShipperNumber"] . '</ShipperNumber>';
		}
		if (isset($module_params["ShipperCity"]) && strlen($module_params["ShipperCity"])) {
			$xml .= '			<City>' . $module_params["ShipperCity"] . '</City>';
		}
		if (isset($module_params["ShipperStateProvinceCode"]) && strlen($module_params["ShipperStateProvinceCode"])) {
			$xml .= '			<StateProvinceCode>' . $module_params["ShipperStateProvinceCode"] . '</StateProvinceCode>';
		}
		if (isset($module_params["ShipperPostalCode"]) && strlen($module_params["ShipperPostalCode"])) {
			$xml .= '			<PostalCode>' . $module_params["ShipperPostalCode"] . '</PostalCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipperPostalCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["ShipperCountryCode"]) && strlen($module_params["ShipperCountryCode"])) {
			$xml .= '			<CountryCode>' . $module_params["ShipperCountryCode"] . '</CountryCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipperCountryCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '		</Address>';
		$xml .= '	</Shipper>';
		$xml .= '	<ShipTo>';
		$xml .= '		<Address>';
		if (isset($ShipToCity) && strlen($ShipToCity)) {
			$xml .= '			<City>' . $ShipToCity . '</City>';
		}
		if (isset($state_code) && strlen($state_code)) {
			$xml .= '			<StateProvinceCode>' . $state_code . '</StateProvinceCode>';
		}
		$xml .= '			<PostalCode>' . $postal_code . '</PostalCode>';
		$xml .= '			<CountryCode>' . $country_code . '</CountryCode>';
		$xml .= '			<ResidentialAddressIndicator>' . $ShipperResidentialAddressIndicator . '</ResidentialAddressIndicator>';
		$xml .= '		</Address>';
		$xml .= '	</ShipTo>';
		$xml .= '	<ShipFrom>';
		$xml .= '		<Address>';
		if (isset($module_params["ShipFromCity"]) && strlen($module_params["ShipFromCity"])) {
			$xml .= '			<City>' . $module_params["ShipFromCity"] . '</City>';
		}
		if (isset($module_params["ShipFromStateProvinceCode"]) && strlen($module_params["ShipFromStateProvinceCode"])) {
			$xml .= '			<StateProvinceCode>' . $module_params["ShipFromStateProvinceCode"] . '</StateProvinceCode>';
		}
		if (isset($module_params["ShipFromPostalCode"]) && strlen($module_params["ShipFromPostalCode"])) {
			$xml .= '			<PostalCode>' . $module_params["ShipFromPostalCode"] . '</PostalCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipFromPostalCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["ShipFromCountryCode"]) && strlen($module_params["ShipFromCountryCode"])) {
			$xml .= '			<CountryCode>' . $module_params["ShipFromCountryCode"] . '</CountryCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipFromCountryCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '		</Address>';
		$xml .= '	</ShipFrom>';
		$xml .= '	<Package>';
		$xml .= '		<PackagingType>';
		if (isset($module_params["PackagingType"]) && strlen($module_params["PackagingType"])) {
		$xml .= '			<Code>' . $module_params["PackagingType"] . '</Code>';
		} else {
			$r->errors .= str_replace("{param_name}", "PackagingType", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '		</PackagingType>';
		if  (isset($module_params["PackagingType"])
				&& ($module_params["PackagingType"]=='02' || $module_params["PackagingType"]=='21')
			)
		{
			$PackageLength = isset($module_params["PackageLength"]) ? $module_params["PackageLength"] : "";
			$PackageWidth  = isset($module_params["PackageWidth"]) ? $module_params["PackageWidth"] : "";
			$PackageHeight = isset($module_params["PackageHeight"]) ? $module_params["PackageHeight"] : "";
			if (strlen($PackageLength)
				|| strlen($PackageWidth)
				|| strlen($PackageHeight)) {
				$xml .= '		<Dimensions>';
				$xml .= '			<UnitOfMeasurement>';
				if (isset($module_params["PackagingTypeUnitOfMeasurement"]) && strlen($module_params["PackagingTypeUnitOfMeasurement"])) {
					$xml .= '				<Code>' . $module_params["PackagingTypeUnitOfMeasurement"] . '</Code>';
				} else {
					$r->errors .= str_replace("{param_name}", "PackagingTypeUnitOfMeasurement", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
				}
				$xml .= '			</UnitOfMeasurement>';
				if (isset($module_params["PackageLength"]) && strlen($module_params["PackageLength"])) {
					$xml .= '			<Length>' . $module_params["PackageLength"] . '</Length>';
				} else {
					$r->errors .= str_replace("{param_name}", "PackageLength", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
				}
				if (isset($module_params["PackageWidth"]) && strlen($module_params["PackageWidth"])) {
				$xml .= '			<Width>' . $module_params["PackageWidth"] . '</Width>';
				} else {
					$r->errors .= str_replace("{param_name}", "PackageWidth", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
				}
				if (isset($module_params["PackageHeight"]) && strlen($module_params["PackageHeight"])) {
				$xml .= '			<Height>' . $module_params["PackageHeight"] . '</Height>';
				} else {
					$r->errors .= str_replace("{param_name}", "PackageHeight", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
				}
				$xml .= '		</Dimensions>';
			}
		}
		$xml .= '		<PackageWeight>';
		$xml .= '			<UnitOfMeasurement>';
		if (isset($module_params["PackageWeightUnitOfMeasurement"]) && strlen($module_params["PackageWeightUnitOfMeasurement"])) {
			$xml .= '				<Code>' . $module_params["PackageWeightUnitOfMeasurement"] . '</Code>';
		} else {
			$r->errors .= str_replace("{param_name}", "PackageWeightUnitOfMeasurement", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '			</UnitOfMeasurement>';
		if (!$item_in_cart_total_weight) {$item_in_cart_total_weight='0.1';}
		$xml .= '			<Weight>' . $item_in_cart_total_weight . '</Weight>';
		$xml .= '		</PackageWeight>';
		$xml .= '	</Package>';
		$xml .= '</Shipment>';
		$xml .= '</RatingServiceSelectionRequest>';

		// Write XML and response to log file
		$orderlog = 'test.log';
		$fp = fopen($orderlog, 'a');
		fwrite($fp, $xml);


		return $xml;


	}

?>