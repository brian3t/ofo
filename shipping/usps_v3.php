<?php

	define("USPS_MAX_WEIGHT", 70);

	if (!strlen($country_code)) { return; }
	if (strtoupper($country_code) == "US" && !strlen($postal_code)) { return; }
	if ($shipping_weight > USPS_MAX_WEIGHT) { return; }
	global $usps_countries;

	// USPS country names
	$usps_countries = array(
		"AD" => "Andorra",
		"AE" => "United Arab Emirates",
		"AF" => "Afghanistan",
		"AG" => "Antigua and Barbuda",
		"AI" => "Anguilla",
		"AL" => "Albania",
		"AM" => "Armenia",
		"AO" => "Angola",
		"AR" => "Argentina",
		"AT" => "Austria",
		"AU" => "Australia",
		"AW" => "Aruba",
		"AZ" => "Azerbaijan",
		"BB" => "Barbados",
		"BD" => "Bangladesh",
		"BE" => "Belgium",
		"BF" => "Burkina Faso",
		"BG" => "Bulgaria",
		"BH" => "Bahrain",
		"BI" => "Burundi",
		"BJ" => "Benin",
		"BM" => "Bermuda",
		"BN" => "Brunei Darussalam",
		"BO" => "Bolivia",
		"BR" => "Brazil",
		"BS" => "Bahamas",
		"BT" => "Bhutan",
		"BW" => "Botswana",
		"BY" => "Belarus",
		"BZ" => "Belize",
		"CA" => "Canada",
		"CH" => "Switzerland",
		"CK" => "Cook Islands (New Zealand)",
		"CL" => "Chile",
		"CM" => "Cameroon",
		"CN" => "China",
		"CO" => "Colombia",
		"CR" => "Costa Rica",
		"CU" => "Cuba",
		"CV" => "Cape Verde",
		"CY" => "Cyprus",
		"CZ" => "Czech Republic",
		"DE" => "Germany",
		"DJ" => "Djibouti",
		"DK" => "Denmark",
		"DZ" => "Algeria",
		"EC" => "Ecuador",
		"EE" => "Estonia",
		"EG" => "Egypt",
		"ER" => "Eritrea",
		"ES" => "Spain",
		"ET" => "Ethiopia",
		"FI" => "Finland",
		"FJ" => "Fiji",
		"FK" => "Falkland Islands",
		"FM" => "Micronesia, Federated States of",
		"FO" => "Faroe Islands",
		"FR" => "France",
		"FX" => "France",
		"GA" => "Gabon",
		"GB" => "United Kingdom (Great Britain)",
		"GD" => "Grenada",
		"GE" => "Georgia, Republic of",
		"GF" => "French Guiana",
		"GG" => "Guernsey, Channel Islands (Great Britain)",
		"GH" => "Ghana",
		"GI" => "Gibraltar",
		"GL" => "Greenland",
		"GM" => "Gambia",
		"GP" => "Guadeloupe",
		"GQ" => "Equatorial Guinea",
		"GR" => "Greece",
		"GT" => "Guatemala",
		"GU" => "Guam (U.S. Possession) See DMM",
		"GW" => "Guinea",
		"GY" => "Guyana",
		"HK" => "Hong Kong",
		"HN" => "Honduras",
		"HR" => "Croatia",
		"HT" => "Haiti",
		"HU" => "Hungary",
		"ID" => "Indonesia",
		"IE" => "Ireland",
		"IL" => "Israel",
		"IM" => "Isle of Man (Great Britain)",
		"IN" => "India",
		"IQ" => "Iraq",
		"IR" => "Iran",
		"IS" => "Iceland",
		"IT" => "Italy",
		"JE" => "Jersey (Channel Islands) (Great Britain)",
		"JM" => "Jamaica",
		"JO" => "Jordan",
		"JP" => "Japan",
		"KE" => "Kenya",
		"KG" => "Kyrgyzstan",
		"KH" => "Cambodia",
		"KI" => "Kiribati",
		"KM" => "Comoros",
		"KR" => "Korea, Republic of (South Korea)",
		"KW" => "Kuwait",
		"KY" => "Cayman Islands",
		"KZ" => "Kazakhstan",
		"LB" => "Lebanon",
		"LC" => "Saint Lucia",
		"LI" => "Liechtenstein",
		"LK" => "Sri Lanka",
		"LR" => "Liberia",
		"LS" => "Lesotho",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"LV" => "Latvia",
		"LY" => "Libya",
		"MA" => "Morocco",
		"MC" => "Monaco (France)",
		"MD" => "Moldova",
		"MG" => "Madagascar",
		"MH" => "Marshall Islands, Republic of the",
		"MK" => "Macedonia",
		"ML" => "Mali",
		"MM" => "Myanmar (Burma)",
		"MN" => "Mongolia",
		"MO" => "Macau (Macao)",
		"MP" => "Northern Mariana Islands, Commonwealth of See DMM",
		"MQ" => "Martinique",
		"MR" => "Mauritania",
		"MS" => "Montserrat",
		"MT" => "Malta",
		"MU" => "Mauritius",
		"MV" => "Maldives",
		"MW" => "Malawi",
		"MX" => "Mexico",
		"MY" => "Malaysia",
		"MZ" => "Mozambique",
		"NA" => "Namibia",
		"NC" => "New Caledonia",
		"NF" => "Norfolk Island (Australia)",
		"NI" => "Nicaragua",
		"NL" => "Netherlands",
		"NO" => "Norway",
		"NP" => "Nepal",
		"NR" => "Nauru",
		"NU" => "Niue (New Zealand)",
		"NZ" => "New Zealand",
		"OM" => "Oman",
		"PA" => "Panama",
		"PE" => "Peru",
		"PF" => "French Polynesia",
		"PG" => "Papua New Guinea",
		"PH" => "Philippines",
		"PK" => "Pakistan",
		"PL" => "Poland",
		"PN" => "Pitcairn Island",
		"PR" => "Puerto Rico See DMM",
		"PT" => "Portugal",
		"PW" => "Palau See DMM",
		"PY" => "Paraguay",
		"QA" => "Qatar",
		"RE" => "Reunion",
		"RO" => "Romania",
		"RU" => "Russia",
		"RW" => "Rwanda",
		"SA" => "Saudi Arabia",
		"SB" => "Solomon Islands",
		"SC" => "Seychelles",
		"SD" => "Sudan",
		"SE" => "Sweden",
		"SG" => "Singapore",
		"SI" => "Slovenia",
		"SL" => "Sierra Leone",
		"SM" => "San Marino",
		"SN" => "Senegal",
		"SO" => "Somalia",
		"SR" => "Suriname",
		"ST" => "Sao Tome and Principe",
		"SV" => "El Salvador",
		"SY" => "Syrian Arab Republic",
		"SZ" => "Swaziland",
		"TC" => "Turks and Caicos Islands",
		"TD" => "Chad",
		"TG" => "Togo",
		"TH" => "Thailand",
		"TJ" => "Tajikistan",
		"TK" => "Tokelau (Union) Group (Western Samoa)",
		"TM" => "Turkmenistan",
		"TN" => "Tunisia",
		"TR" => "Turkey",
		"TT" => "Trinidad and Tobago",
		"TV" => "Tuvalu",
		"TW" => "Taiwan",
		"TZ" => "Tanzania",
		"UA" => "Ukraine",
		"UG" => "Uganda",
		"UY" => "Uruguay",
		"UZ" => "Uzbekistan",
		"VA" => "Vatican City",
		"VC" => "Saint Vincent and the Grenadines",
		"VE" => "Venezuela",
		"VN" => "Vietnam",
		"VU" => "Vanuatu",
		"WS" => "Samoa, American (U.S. Possession) See DMM",
		"YE" => "Yemen",
		"YT" => "Mayotte (France)",
		"ZA" => "South Africa",
		"ZM" => "Zambia",
		"ZW" => "Zimbabwe",
		"AN" => "Netherlands Antilles",
		"AQ" => "Antarctica",
		"AS" => "American Samoa",
		"AX" => "Aland Island (Finland)",
		"BA" => "Bosnia–Herzegovina",
		"BV" => "Bouvet Island",
		"CC" => "Cocos Island (Australia)",
		"CD" => "Congo, Democratic Republic of th",
		"CF" => "Central African Rep.",
		"CG" => "Congo, Republic of the (Brazzaville) ",
		"CI" => "Cote D'Ivoire",
		"CS" => "Serbia–Montenegro",
		"CX" => "Christmas Island",
		"DM" => "Dominica",
		"DO" => "Dominican Republic",
		"EH" => "Western Sahara",
		"GN" => "Guinea",
		"GS" => "South Georgia (Falkland Islands)",
		"HM" => "Heard and Mc Donald Islands",
		"IO" => "British Indian Ocean Territory",
		"KN" => "Saint Kitts (St. Christopher and Nevis)",
		"KP" => "Korea, Democratic People’s Republic of (North Korea)",
		"LA" => "Lao People's Democratic Republic",
		"NE" => "Niger",
		"NG" => "Nigeria",
		"PM" => "Saint Pierre and Miquelon",
		"PS" => "Palestinian Territory, Occupied",
		"SH" => "Saint Helena",
		"SJ" => "Svalbard and Jan Mayen Islands",
		"SK" => "Slovakia (Slovak Republic) EU",
		"TF" => "French Southern Territories",
		"TL" => "Timor-Leste",
		"TO" => "Tonga",
		"UM" => "United States Minor Outlying Islands",
		"US" => "United States",
		"VG" => "British Virgin Islands",
		"VI" => "Virgin Islands (U.S.)",
		"WF" => "Wallis And Futuna Islands"
	);

	preg_match_all("/(\d+)(-)?(\d+)?/", $postal_code, $postal_codes, PREG_SET_ORDER);
	if (isset($postal_codes[0][1])) {
		$postal_code = $postal_codes[0][1];
	}

	if (!$external_url) $external_url = "http://production.shippingapis.com/ShippingAPI.dll";

	$usps_url = parse_url($external_url);
	$usps_server = $usps_url["host"];
	$usps_api_lib = $usps_url["path"];
	
	// Domestic Rate Request
	if (strpos(strtolower($usps_server), "testing") === false && strpos(strtolower($usps_api_lib), "test") === false) {
		define("USPS_API_DOM", "RateV3");
	} else {
		define("USPS_API_DOM", "RateV2");
	}
	// International Rate Request
	define("USPS_API_INT", "IntlRate");

	// To know what tool to use - domestic or international
	if (strtoupper($country_code) == "US") {
		$usps_api_name = USPS_API_DOM;
	} else {
		$usps_api_name = USPS_API_INT;
	}

	$xml = usps_prepare_rate_request($module_params, $usps_api_name);
	$result = "";

	$fp = fsockopen($usps_server, 80, $errno, $errstr, 30);
	if (!$fp) {
		$r->errors .= "An error occurred while opening remote server: $errstr ($errno)<br />\n";
	} else {
		$out = "GET " . $usps_api_lib . "?API=" . $usps_api_name . "&XML=" . $xml . " HTTP/1.1\n";
		$out .= "Accept: text/xml\n";
		$out .= "Accept: charset=ISO-8859-1\n";
		$out .= "User-agent: ViArt Shop; USPS rates request tool\n";
		$out .= "Host: $usps_server\n";
		$out .= "Connection: Close\n\n";

		fwrite($fp, $out);
		while (!feof($fp)) {
			$result .= fgets($fp, 4096);
		}
		fclose($fp);
	}

	if ($result) {
		$result = str_replace("\r", "", $result);
		$result = str_replace("\n", "", $result);
		$pos = strpos($result, "<?xml");
		$result_xml = substr($result, $pos);
		$pos = strpos($result, "?>"); //<?
		$result = trim(substr($result, $pos + 2));
		$pos = strpos($result, "<");
		$result = trim(substr($result, $pos ));

		$errors = usps_check_errors($result);
		if (sizeof($errors) > 0)
		{
			foreach ($errors as $error) {
				$r->errors .= sprintf("U.S.P.S. Error occurred: %s - %s \n<br>", $error["Number"], $error["Description"]);
			}
		}
		else
		{
			$packages = usps_fill_package($result, $usps_api_name);
			$rated_shipment=array();
			$i = 0;
			if ($usps_api_name == USPS_API_DOM)
			{
				foreach ($packages as $package) {
					foreach ($package["Postages"] as $postage) {
						$service_code = $postage["MailService"];
						$monetary_value = $postage["Rate"];
						if($i != 0){
							foreach ($rated_shipment as $key => $rated) {
								if($rated[0] == $service_code){
									$rated_shipment[$key][1] += $monetary_value;
								}
							}
						}else{
							$rated_shipment[] = array($service_code,$monetary_value);
						}
					}
					$i++;
				}
			}
			// International shipping
			elseif ($usps_api_name == USPS_API_INT)
			{
				foreach ($packages as $package) {
					foreach ($package["Services"] as $service) {
						$service_code = $service["SvcDescription"];
						$monetary_value = $service["Postage"];
						if($i != 0){
							foreach ($rated_shipment as $key => $rated) {
								if($rated[0] == $service_code){
									$rated_shipment[$key][1] += $monetary_value;
								}
							}
						}else{
							$rated_shipment[] = array($service_code,$monetary_value);
						}
					}
					$i++;
				}
			}

			foreach ($module_shipping as $module) {
				$search = array("(", ")", ".", "\"");
				$replacement = array("\(", "\)", "\.", "\\\"");
				$module[1] = str_replace($search, $replacement, $module[1]);
				$module[1] = str_replace("&amp;lt;sup&amp;gt;&amp;amp;reg;&amp;lt;/sup&amp;gt;", "", $module[1]);
				// Domestic shipping
				foreach ($rated_shipment as $rated) {
					$rated[0] = str_replace("&amp;lt;sup&amp;gt;&amp;amp;reg;&amp;lt;/sup&amp;gt;", "", $rated[0]);
					if (preg_match("/^" . preg_quote($module[1], "/") . "$/Uis", $rated[0])) {
						$module[3] += $rated[1];
						$shipping_types[] = $module;
					}
				}
			}
		}
	}
	else
	{
		$r->errors .= "USPS server returned no answer.<br>\n";
	}

	function usps_prepare_rate_request($module_params, $usps_api_name)
	{
		global $r, $db, $table_prefix, $shipping_weight, $state_code, $country_code, $postal_code, $usps_countries, $shipping_packages;

		$xml = "<" . $usps_api_name . "Request";
		// USERID - required, provided during registration
		if (isset($module_params["USERID"]) && strlen($module_params["USERID"])) {
			$xml .= ' USERID="' . $module_params["USERID"] . '"';
		} else {
			$r->errors .= "USPS module error: USERID is required.<br>\n";
		}
		if (isset($module_params["PASSWORD"]) && strlen($module_params["PASSWORD"])) {
			$xml .= ' PASSWORD="' . $module_params["PASSWORD"] . '"';
		}
		$xml .= ">";

		foreach($shipping_packages as $package_index => $package) {
			// Number of package - optional
			$xml .= '<Package ID="'.$package_index.'">';

			if ($usps_api_name == USPS_API_DOM)
			{
				// Service - required, one of the following: Express, First Class, Priority, Parcel, BPM, Library, Media, All
				$xml .= "<Service>All</Service>";

				// ZipOrigination - required, valid ZIP code with maximum length of 5 characters
				if (isset($module_params["ZipOrigination"]) && strlen($module_params["ZipOrigination"])) {
					$xml .= "<ZipOrigination>" . $module_params["ZipOrigination"] . "</ZipOrigination>";
				} else {
					$r->errors .= "USPS module error: ZipOrigination is required.<br>\n";
				}

				// ZipDestination - required, valid ZIP code with maximum length of 5 characters
				if (strlen($postal_code)) {
					$xml .= "<ZipDestination>" . $postal_code . "</ZipDestination>";
				} else {
					$r->errors .= "USPS module error: ZipDestination is required.<br>\n";
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

			if ($usps_api_name == USPS_API_DOM)
			{
				/*
				Container - optional, tag is only applicable for Express Mail and Priority Mail.
				The tag will be ignored if specified with any other service type.
				When used, the <Container> field must contain one of the following valid packaging type names:
				Flate Rate Envelope, Flate Rate Box, RECTANGULAR, NONRECTANGULAR
				*/
				if (isset($module_params["Container"]) && strlen($module_params["Container"])) {
					$xml .= "<Container>" . $module_params["Container"] . "</Container>";
				}

				// Size - required, must be one of the following: Regular, Large, Oversize
				if (isset($module_params["Size"]) && strlen($module_params["Size"])) {
					$xml .= "<Size>" . $module_params["Size"] . "</Size>";
				} else {
					$r->errors .= "USPS module error: Size is required.<br>\n";
				}

				/*
				To capture the dimensional weight for Large Priority Mail pieces, RateV3 will require
				three new dimension tags for rectangular Priority Mail pieces: Length, Width, and Height;
				and four new dimension tags for non-rectangular pieces: Length, Width, Height, and Girth.
				Shippers will specify in the existing Container tag whether a Large Priority Mail piece
				is rectangular or non-rectangular.
				*/
				if ($package["width"]) {
					$xml .= "<Width>" . $package["width"] . "</Width>";
				} else {
					if (isset($module_params["Width"]) && strlen($module_params["Width"])) {
						$xml .= "<Width>" . $module_params["Width"] . "</Width>";
					}
				}
				if ($package["length"]) {
					$xml .= "<Length>" . $package["length"] . "</Length>";
				} else {
					if (isset($module_params["Length"]) && strlen($module_params["Length"])) {
						$xml .= "<Length>" . $module_params["Length"] . "</Length>";
					}
				}
				if ($package["height"]) {
					$xml .= "<Height>" . $package["height"] . "</Height>";
				} else {
					if (isset($module_params["Length"]) && strlen($module_params["Length"])) {
						$xml .= "<Height>" . $module_params["Length"] . "</Height>";
					}
				}

				if (isset($module_params["Girth"]) && intval($module_params["Girth"]) > 0) {
					$xml .= "<Girth>" . intval($module_params["Girth"]) . "</Girth>";
				}

				// Machinable - optional, applies only to Parcel Post
				if (isset($module_params["Machinable"]) && strlen($module_params["Machinable"])) {
					$xml .= "<Machinable>" . $module_params["Machinable"] . "</Machinable>";
				}
			}
			if ($usps_api_name == USPS_API_INT)
			{
				// MailType - required, must be one of the following: Package, Postcards or Aerogrammes, Matter for blind, Envelope
				if (isset($module_params["MailType"]) && strlen($module_params["MailType"])) {
					$xml .= "<MailType>" . $module_params["MailType"] . "</MailType>";
				} else {
					$r->errors .= "USPS module error: MailType is required.<br>\n";
				}

				// Country - required, must be from USPS country list
				if (isset($usps_countries[$country_code])) {
					$country_name = $usps_countries[$country_code];
				} else {
					$sql = "SELECT country_name FROM " . $table_prefix . "countries WHERE country_code = " . $db->tosql($country_code, TEXT);
					$country_name = get_db_value($sql);
				}

				if ($country_name) {
					$xml .= "<Country>" . $country_name . "</Country>";
				} else {
					$r->errors .= "USPS module error: Country is required.<br>\n";
				}
			}
			$xml .= '</Package>';
		}

		$xml .= '</' . $usps_api_name . 'Request>';

		$xml = str_replace(" ", "%20", $xml);
		return $xml;
	}

	function usps_fill_package($xml_string, $usps_api_name)
	{
		$packages = array();
		preg_match_all("/<Package ID=\"(.*)\">(.*)\<\/Package>/Ui", $xml_string, $packages_raw, PREG_SET_ORDER);
		for ($i = 0; $i < sizeof($packages_raw); $i++) {
			if ($usps_api_name == USPS_API_DOM)
			{
				// Parse postages
				$postages = array();
				preg_match_all("/<Postage CLASSID=\"\d+\">(.*)\<\/Postage>/Ui", trim($packages_raw[$i][2]), $postages_raw, PREG_SET_ORDER);
				for ($j = 0; $j < sizeof($postages_raw); $j++) {
					preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($postages_raw[$j][1]), $matches, PREG_SET_ORDER);
					for ($k = 0; $k < sizeof($matches); $k++) {
						$postages[$j][$matches[$k][1]] = ($matches[$k][2]);
					}
				}
				$packages_raw[$i][2] = preg_replace("/<Postage>.*\<\/Postage>/i", "", $packages_raw[$i][2]);
				$packages[$packages_raw[$i][1]]["Postages"] = $postages;
				// convert xml into array
				preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($packages_raw[$i][2]), $matches, PREG_SET_ORDER);
				for ($j = 0; $j < sizeof($matches); $j++) {
					$packages[$packages_raw[$i][1]][$matches[$j][1]] = ($matches[$j][2]);
				}
			}
			elseif ($usps_api_name == USPS_API_INT)
			{
				// Parse services
				$services = array();
				preg_match_all("/<Service ID=\"(.*)\">(.*)\<\/Service>/Ui", trim($packages_raw[$i][2]), $services_raw, PREG_SET_ORDER);
				for ($j = 0; $j < sizeof($services_raw); $j++) {
					preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($services_raw[$j][2]), $matches, PREG_SET_ORDER);
					for ($k = 0; $k < sizeof($matches); $k++) {
						$services[$services_raw[$j][1]][$matches[$k][1]] = ($matches[$k][2]);
					}
				}
				$services_raw[$i][2] = preg_replace("/<Service ID=\".*\">.*\<\/Service>/i", "", $services_raw[$i][2]);
				$packages[$packages_raw[$i][1]]["Services"] = $services;
				// convert xml into array
				preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($packages_raw[$i][2]), $matches, PREG_SET_ORDER);
				for ($j = 0; $j < sizeof($matches); $j++) {
					$packages[$packages_raw[$i][1]][$matches[$j][1]] = ($matches[$j][2]);
				}
			}
		}
		return $packages;
	}

	function usps_check_errors($xml_string)
	{
		$errors = array();
		preg_match_all("/<Error>(.*)\<\/Error>/Ui", $xml_string, $errors_raw, PREG_SET_ORDER);
		for ($i = 0; $i < sizeof($errors_raw); $i++) {
			// convert xml into array
			preg_match_all("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", trim($errors_raw[$i][1]), $matches, PREG_SET_ORDER);
			for ($j = 0; $j < sizeof($matches); $j++) {
				$errors[$i][$matches[$j][1]] = ($matches[$j][2]);
			}
		}
		return $errors;
	}

?>