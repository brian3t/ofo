<?php

	$xml = ups_prepare_rate_request($module_params);
	$answer = '';
	if (strlen($xml)) {
		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $external_url);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt ($ch, CURLOPT_POST, 1); 
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
		$answer = curl_exec($ch);
		curl_close($ch);
	} else {
		return;
	}
	$answer = trim($answer);
	if (strlen($answer)) {
		$response_parameters = array();
		preg_match_all ("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $answer, $matches, PREG_SET_ORDER);
		for($i = 0; $i < sizeof($matches); $i++) {
			$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
		}
		if (isset($response_parameters["ErrorCode"])) {
			if (strtoupper($response_parameters["ErrorSeverity"]) != 'WARNING'){
				$r->errors .= sprintf("UPS. Error occured: %s - %s <br>", $response_parameters["ErrorCode"], $response_parameters["ErrorDescription"]);
			}
		}
		$rated_shipment=array();
		preg_match_all("/<RatedShipment>(.*)\<\/RatedShipment>/Uis", $answer, $rated_raw, PREG_SET_ORDER);
		for($i = 0; $i < sizeof($rated_raw); $i++) {
			preg_match("/<Service>\s*<Code>(.*)<\/Code>\s*<\/Service>.*<TotalCharges>.*<CurrencyCode>(.*)<\/CurrencyCode>.*<MonetaryValue>(.*)\<\/MonetaryValue>.*<\/TotalCharges>.*<RatedPackage>(.*)<\/RatedPackage>/Uis", $rated_raw[$i][1], $total_raw);
			$service_code = $total_raw[1];
			$monetary_value = $total_raw[3];
			$rated_shipment[] = array($service_code,$monetary_value);
		}
		foreach ($module_shipping as $module) {
			foreach ($rated_shipment as $rated) {
				if ($module[1] == $rated[0]) {
					$module[3] += $rated[1];
					$shipping_types[] = $module;
				}
			}
		}
	}
	else {
		$r->errors .= "UPS server doesn't answer.<br>\r\n";
	}

	function ups_prepare_rate_request($module_params)
	{
		global $r, $state_code, $country_code, $postal_code, $shipping_packages;
		$xml = '';
		if (!strlen($country_code) || !strlen($postal_code)) {
			return $xml;
		}

		$xml  = '<?xml version="1.0"?>';
		$xml .= '<AccessRequest xml:lang="en-US">';
		if (isset($module_params["access_license_number"])) {
			$xml .= '	<AccessLicenseNumber>'.$module_params["access_license_number"].'</AccessLicenseNumber>';
		} else {
			$r->errors .= str_replace("{param_name}", "AccessLicenseNumber", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["user_id"])) {
			$xml .= '	<UserId>'.$module_params["user_id"].'</UserId>';
		} else {
			$r->errors .= str_replace("{param_name}", "UserId", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["user_id"]) && isset($module_params["password"]) && isset($module_params["access_license_number"])) {
			$xml .= '	<Password>'.$module_params["password"].'</Password>';
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
			$xml .= '	<Code>'.$module_params["PickupType"].'</Code>';
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
			$xml .= '			<ShipperNumber>'.$module_params["ShipperNumber"].'</ShipperNumber>';
		}
		if (isset($module_params["ShipperCity"]) && strlen($module_params["ShipperCity"])) {
			$xml .= '			<City>'.$module_params["ShipperCity"].'</City>';
		}
		if (isset($module_params["ShipperStateProvinceCode"]) && strlen($module_params["ShipperStateProvinceCode"])) {
			$xml .= '			<StateProvinceCode>'.$module_params["ShipperStateProvinceCode"].'</StateProvinceCode>';
		}
		if (isset($module_params["ShipperPostalCode"]) && strlen($module_params["ShipperPostalCode"])) {
			$xml .= '			<PostalCode>'.$module_params["ShipperPostalCode"].'</PostalCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipperPostalCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["ShipperCountryCode"]) && strlen($module_params["ShipperCountryCode"])) {
			$xml .= '			<CountryCode>'.$module_params["ShipperCountryCode"].'</CountryCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipperCountryCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '		</Address>';
		$xml .= '	</Shipper>';
		$xml .= '	<ShipTo>';
		$xml .= '		<Address>';
		if (isset($ShipToCity) && strlen($ShipToCity)) {
			$xml .= '			<City>'.$ShipToCity.'</City>';
		}
		if (isset($state_code) && strlen($state_code)) {
			$xml .= '			<StateProvinceCode>'.$state_code.'</StateProvinceCode>';
		}
		$xml .= '			<PostalCode>'.$postal_code.'</PostalCode>';
		$xml .= '			<CountryCode>'.$country_code.'</CountryCode>';
		if (isset($ShipperResidentialAddressIndicator) && strlen($ShipperResidentialAddressIndicator)) {
			$xml .= '			<ResidentialAddressIndicator>'.$ShipperResidentialAddressIndicator.'</ResidentialAddressIndicator>';
		}
		$xml .= '		</Address>';
		$xml .= '	</ShipTo>';
		$xml .= '	<ShipFrom>';
		$xml .= '		<Address>';
		if (isset($module_params["ShipFromCity"]) && strlen($module_params["ShipFromCity"])) {
			$xml .= '			<City>'.$module_params["ShipFromCity"].'</City>';
		}
		if (isset($module_params["ShipFromStateProvinceCode"]) && strlen($module_params["ShipFromStateProvinceCode"])) {
			$xml .= '			<StateProvinceCode>'.$module_params["ShipFromStateProvinceCode"].'</StateProvinceCode>';
		}
		if (isset($module_params["ShipFromPostalCode"]) && strlen($module_params["ShipFromPostalCode"])) {
			$xml .= '			<PostalCode>'.$module_params["ShipFromPostalCode"].'</PostalCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipFromPostalCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		if (isset($module_params["ShipFromCountryCode"]) && strlen($module_params["ShipFromCountryCode"])) {
			$xml .= '			<CountryCode>'.$module_params["ShipFromCountryCode"].'</CountryCode>';
		} else {
			$r->errors .= str_replace("{param_name}", "ShipFromCountryCode", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
		}
		$xml .= '		</Address>';
		$xml .= '	</ShipFrom>';
		foreach($shipping_packages as $package_index => $package) {
			for($package_number=1; $package_number<=intval($package['quantity']*$package['packages']); $package_number++){
				$xml .= '	<Package>';
				$xml .= '		<PackagingType>';
				if (isset($module_params["PackagingType"]) && strlen($module_params["PackagingType"])) {
				$xml .= '			<Code>'.$module_params["PackagingType"].'</Code>';
				} else {
					$r->errors .= str_replace("{param_name}", "PackagingType", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
				}
				$xml .= '		</PackagingType>';
				if (isset($module_params["PackagingType"]) && ($module_params["PackagingType"]=='02' || $module_params["PackagingType"]=='21')) {
					$xml .= '		<Dimensions>';
					$xml .= '			<UnitOfMeasurement>';
					if (isset($module_params["PackagingTypeUnitOfMeasurement"]) && strlen($module_params["PackagingTypeUnitOfMeasurement"])) {
						$xml .= '				<Code>'.$module_params["PackagingTypeUnitOfMeasurement"].'</Code>';
					} else {
						$r->errors .= str_replace("{param_name}", "PackagingTypeUnitOfMeasurement", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
					}
					$xml .= '			</UnitOfMeasurement>';
					if ($package["length"]) {
						$xml .= '			<Length>'.round($package["length"],2).'</Length>';
					} else {
						if (isset($module_params["PackageLength"]) && strlen($module_params["PackageLength"])) {
							$xml .= '			<Length>'.round($module_params["PackageLength"],2).'</Length>';
						} else {
							$r->errors .= str_replace("{param_name}", "PackageLength", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
						}
					}
					if ($package["width"]) {
						$xml .= '			<Width>'.round($package["width"],2).'</Width>';
					} else {
						if (isset($module_params["PackageWidth"]) && strlen($module_params["PackageWidth"])) {
						$xml .= '			<Width>'.round($module_params["PackageWidth"],2).'</Width>';
						} else {
							$r->errors .= str_replace("{param_name}", "PackageWidth", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
						}
					}
					if ($package["height"]) {
						$xml .= '			<Height>'.round($package["height"],2).'</Height>';
					} else {
						if (isset($module_params["PackageHeight"]) && strlen($module_params["PackageHeight"])) {
						$xml .= '			<Height>'.round($module_params["PackageHeight"],2).'</Height>';
						} else {
							$r->errors .= str_replace("{param_name}", "PackageHeight", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
						}
					}
					$xml .= '		</Dimensions>';
				} elseif(isset($module_params["PackagingTypeUnitOfMeasurement"]) && strlen($module_params["PackagingTypeUnitOfMeasurement"]) &&
					$package["length"]>0 && $package["width"] && $package["height"]) {
					$xml .= '		<Dimensions>';
					$xml .= '			<UnitOfMeasurement>';
					$xml .= '				<Code>'.$module_params["PackagingTypeUnitOfMeasurement"].'</Code>';
					$xml .= '			</UnitOfMeasurement>';
					$xml .= '			<Length>'.round($package["length"],2).'</Length>';
					$xml .= '			<Width>'.round($package["width"],2).'</Width>';
					$xml .= '			<Height>'.round($package["height"],2).'</Height>';
					$xml .= '		</Dimensions>';
				}
				$xml .= '		<PackageWeight>';
				$xml .= '			<UnitOfMeasurement>';
				if (isset($module_params["PackageWeightUnitOfMeasurement"]) && strlen($module_params["PackageWeightUnitOfMeasurement"])) {
					$xml .= '				<Code>'.$module_params["PackageWeightUnitOfMeasurement"].'</Code>';
				} else {
					$r->errors .= str_replace("{param_name}", "PackageWeightUnitOfMeasurement", UPS_PARAMETER_REQUIRED_MSG) . "<br>\n";
				}
				$xml .= '			</UnitOfMeasurement>';
				$package_weight = ($package["weight"])? $package["weight"]: 0.1;
				$xml .= '			<Weight>'.$package_weight.'</Weight>'; 
				$xml .= '		</PackageWeight>';
				$xml .= '	</Package>';
			}
		}
		$xml .= '</Shipment>';
		$xml .= '</RatingServiceSelectionRequest>';

		return $xml;

	}

?>