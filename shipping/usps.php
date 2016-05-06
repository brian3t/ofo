<?php

	if (!strlen($country_code)) {return;}
	if ($country_code == "US" && !strlen($postal_code)) {return;}
	
	preg_match_all("/(\d+)(-)?(\d+)?/", $postal_code, $postal_codes, PREG_SET_ORDER);
	if (isset($postal_codes[0][1])) {
		$postal_code = $postal_codes[0][1];
	}

	if (!$external_url) $external_url = "http://testing.shippingapis.com/ShippingAPITest.dll";
	//or live url - "http://production.shippingapis.com/ShippingAPI.dll"

	$usps_url = parse_url($external_url);

	$usps_server = $usps_url["host"];
	$usps_api_lib = $usps_url["path"];
	// Domestic Rate Request
	$usps_api_dom = "RateV2";
	// International Rate Request
	$usps_api_int = "IntlRate";
	
	// To know what tool to use - domestic or international
	if ($country_code == "US") {
		$usps_api_name = $usps_api_dom;
	} else {
		$usps_api_name = $usps_api_int;
	}
	
	$xml = prepare_rate_request($module_params);
	$result = "";

	if ($country_code && $postal_code) {
		$fp = fsockopen($usps_server, 80, $errno, $errstr, 30);
		if (!$fp) {
			$r->errors .= "An error occured while opening remote server: $errstr ($errno)<br />\n";
		} else {
			$out = "GET " . $usps_api_lib . "?API=" . $usps_api_name . "&XML=" . $xml . " HTTP/1.1\r\n";
			$out .= "Accept: text/xml\r\n";
			$out .= "Accept: charset=ISO-8859-1\r\n";
			//$out .= "User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n";
			$out .= "Host: $usps_server\r\n";
			$out .= "Connection: Close\r\n\r\n";
			
			fwrite($fp, $out);
			while (!feof($fp)) {
				$result .= fgets($fp, 4096);
			}
			fclose($fp);
		}

		if ($result) {
			//echo htmlspecialchars($xml) . "<br>";
			//echo htmlspecialchars($result) . "<br>";
			$result = str_replace("\r", "", $result);
			$result = str_replace("\n", "", $result);
			$pos = strpos($result, "<?xml");
			$result_xml = substr($result, $pos);
			$pos = strpos($result, "?>");
			//<?
			$result = trim(substr($result, $pos + 2));
			$pos = strpos($result, "<");
			$result = trim(substr($result, $pos ));
		
			$errors = check_errors($result);
			if (sizeof($errors) > 0) {
				foreach ($errors as $error) {
					$r->errors .= sprintf("U.S.P.S. Error occured: %s - %s \n<br>", $error["Number"], $error["Description"]);
				}
			}
			else {				
				$packages = fill_package($result);
				foreach ($module_shipping as $module) {
					$search = array("(", ")", ".", "\"");
					$replacement = array("\(", "\)", "\.", "\\\"");
					$module[1] = str_replace($search, $replacement, $module[1]);
					// Domestic shipping
					if ($usps_api_name == $usps_api_dom && isset($packages[0]) && is_array($packages[0]) && isset($packages[0]["Postages"]) && is_array($packages[0]["Postages"])) {
						foreach ($packages[0]["Postages"] as $postage) {
							if (preg_match("/^" . $module[1] . "$/i", $postage["MailService"])) {
								//$module[2] = $postage["MailService"];
								$module[3] += $postage["Rate"];
								$shipping_types[] = $module;
							}
						}
					}
					//International shipping
					else if ($usps_api_name == $usps_api_int && isset($packages[0]) && is_array($packages[0]) && isset($packages[0]["Services"]) && is_array($packages[0]["Services"])) {
						$subcode = 0;
						foreach ($packages[0]["Services"] as $service) {
							if (preg_match("/" . $module[1] . "/i", $service["SvcDescription"])) {
								//$module[2] = $service["SvcDescription"];
								$module[3] += $service["Postage"];
								$shipping_types[] = $module;
							}
						}
					}
				}
			}
		}
		else {
			$r->errors .= "USPS server doesn't answer.<br>\r\n";
		}
	}

	function prepare_rate_request($module_params)
	{
		global $r, $db, $table_prefix, $shipping_weight, $state_code, $country_code, $postal_code, $usps_api_name, $usps_api_dom, $usps_api_int;
			
		$xml = "<" . $usps_api_name . "Request";
		// USERID - required, provided during registration
		if (isset($module_params["USERID"]) && strlen($module_params["USERID"])) {
			$xml .= ' USERID="' . $module_params["USERID"] . '"';
		} else {
			$r->errors .= "USERID is required.<br>\r\n";
		}
		if (isset($module_params["PASSWORD"]) && strlen($module_params["PASSWORD"])) {
			$xml .= ' PASSWORD="' . $module_params["PASSWORD"] . '"';
		}
		$xml .= ">";

		// Number of package - optional
		$xml .= '<Package ID="0">';

		if ($usps_api_name == $usps_api_dom) 
		{
			// For test purposes
			//$xml .= "<Service>Priority</Service>";
			// Service - required, one of the following: Express, First Class, Priority, Parcel, BPM, Library, Media, All
			$xml .= "<Service>All</Service>";

			// ZipOrigination - required, valid ZIP code with maximum length of 5 characters
			if (isset($module_params["ZipOrigination"]) && strlen($module_params["ZipOrigination"])) {
				$xml .= "<ZipOrigination>" . $module_params["ZipOrigination"] . "</ZipOrigination>";
			} else {
				$r->errors .= "ZipOrigination is required.<br>\r\n";
			}

			// ZipDestination - required, valid ZIP code with maximum length of 5 characters
			if (isset($postal_code) && strlen($postal_code)) {
				$xml .= "<ZipDestination>" . $postal_code . "</ZipDestination>";
			} else {
				$r->errors .= "ZipDestination is required.<br>\r\n";
			}
		}

		// Pounds, Ounces - required
		$pounds = floor($shipping_weight);
		$ounces = round(($shipping_weight - $pounds) * 16);
		if (!$pounds && !$ounces) {
			$pounds = 0;
			$ounces = 1;
		}
		$xml .= "<Pounds>" . $pounds . "</Pounds><Ounces>" . $ounces . "</Ounces>";
		//For test purposes
		//$xml .= "<Pounds>10</Pounds><Ounces>5</Ounces>";

		if ($usps_api_name == $usps_api_dom)
		{
			// Container - optional, tag is only applicable for Express Mail and Priority Mail. The tag will be ignored if specified with any other service type. When used, the <Container> field must contain one of the following valid packaging type names: Flate Rate Envelope, Flate Rate Box
			if (isset($module_params["Container"]) && strlen($module_params["Container"])) {
				$xml .= "<Container>" . $module_params["Container"] . "</Container>";
			}
	
			//Size - required, must be one of the following: Regular, Large, Oversize
			if (isset($module_params["Size"]) && strlen($module_params["Size"])) {
				$xml .= "<Size>" . $module_params["Size"] . "</Size>";
			}
			else {
				$r->errors .= "Size is required.<br>\r\n";
			}

			//Machinable - optional, applies only to Parcel Post
			if (isset($module_params["Machinable"]) && strlen($module_params["Machinable"])) {
				$xml .= "<Machinable>" . $module_params["Machinable"] . "</Machinable>";
			}
		}
		if ($usps_api_name == $usps_api_int)
		{
			//MailType - required, must be one of the following: Package, Postcards or Aerogrammes, Matter for blind, Envelope
			if (isset($module_params["MailType"]) && strlen($module_params["MailType"])) {
				$xml .= "<MailType>" . $module_params["MailType"] . "</MailType>";
			}
			else {
				$r->errors .= "MailType is required.<br>\r\n";
			}

			//Country - required, must be from USPS country list
			$country_name = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_code = " . $db->tosql($country_code, TEXT));
			if ($country_name) {
				$xml .= "<Country>" . $country_name . "</Country>";
			}
			else {
				$r->errors .= "Country is required.<br>\r\n";
			}
		}
		
		$xml .= '</Package></' . $usps_api_name . 'Request>';

		$xml = str_replace(" ", "%20", $xml);
		return $xml;
	}

	function fill_package($xml_string)
	{
		global $usps_api_name, $usps_api_dom, $usps_api_int;

		$packages = array();
		preg_match_all("/<Package ID=\"(.*)\">(.*)\<\/Package>/Ui", $xml_string, $packages_raw, PREG_SET_ORDER);
		for($i = 0; $i < sizeof($packages_raw); $i++) {
			if ($usps_api_name == $usps_api_dom)
			{
				// Parse postages
				$postages = array();
				preg_match_all("/<Postage>(.*)\<\/Postage>/Ui", trim($packages_raw[$i][2]), $postages_raw, PREG_SET_ORDER);
				for($j = 0; $j < sizeof($postages_raw); $j++) {
					preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($postages_raw[$j][1]), $matches, PREG_SET_ORDER);
					for($k = 0; $k < sizeof($matches); $k++) {
						$postages[$j][$matches[$k][1]] = ($matches[$k][2]);
					}
				}
				$packages_raw[$i][2] = preg_replace("/<Postage>.*\<\/Postage>/i", "", $packages_raw[$i][2]);
				$packages[$packages_raw[$i][1]]["Postages"] = $postages;
				// convert xml into array
				preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($packages_raw[$i][2]), $matches, PREG_SET_ORDER);
				for($j = 0; $j < sizeof($matches); $j++) {
					$packages[$packages_raw[$i][1]][$matches[$j][1]] = ($matches[$j][2]);
				}
			}
			else if ($usps_api_name == $usps_api_int)
			{
				// Parse services
				$services = array();
				preg_match_all("/<Service ID=\"(.*)\">(.*)\<\/Service>/Ui", trim($packages_raw[$i][2]), $services_raw, PREG_SET_ORDER);
				for($j = 0; $j < sizeof($services_raw); $j++) {
					preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($services_raw[$j][2]), $matches, PREG_SET_ORDER);
					for($k = 0; $k < sizeof($matches); $k++) {
						$services[$services_raw[$j][1]][$matches[$k][1]] = ($matches[$k][2]);
					}
				}
				$services_raw[$i][2] = preg_replace("/<Service ID=\".*\">.*\<\/Service>/i", "", $services_raw[$i][2]);
				$packages[$packages_raw[$i][1]]["Services"] = $services;
				// convert xml into array
				preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($packages_raw[$i][2]), $matches, PREG_SET_ORDER);
				for($j = 0; $j < sizeof($matches); $j++) {
					$packages[$packages_raw[$i][1]][$matches[$j][1]] = ($matches[$j][2]);
				}
			}
		}
		return $packages;
	}
	
	function check_errors($xml_string)
	{
		$errors = array();
		preg_match_all("/<Error>(.*)\<\/Error>/Ui", $xml_string, $errors_raw, PREG_SET_ORDER);
		for($i = 0; $i < sizeof($errors_raw); $i++) {
			// convert xml into array
			preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($errors_raw[$i][1]), $matches, PREG_SET_ORDER);
			for($j = 0; $j < sizeof($matches); $j++) {
				$errors[$i][$matches[$j][1]] = ($matches[$j][2]);
			}
		}
		return $errors;
	}

?>