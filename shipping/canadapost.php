<?php

	define("CANADAPOST_MAX_WEIGHT", 65);

	if (!strlen($country_code) || !strlen($postal_code)) { return; }
	if ($shipping_weight > CANADAPOST_MAX_WEIGHT) { return; }

	if (!$external_url) $external_url = "http://sellonline.canadapost.ca:30000";

	$module_params["price"] = $order_total;
	$module_params['quantity'] = '1';

	$sql = "SELECT country_name FROM " . $table_prefix . "countries WHERE country_code = " . $db->tosql($country_code, TEXT);
	$country = get_db_value($sql);
	$sql = "SELECT state_name FROM " . $table_prefix . "states WHERE state_code = " . $db->tosql($state_code, TEXT);
	$state = get_db_value($sql);

	$xml = canadapost_prepare_rate_request($module_params);

	$ch = @curl_init();
	if ($ch) {
		curl_setopt($ch, CURLOPT_URL, $external_url); // set url to post to
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_PROXY, "proxy_server:port");
		//curl_setopt($ch, CURLOPT_PROXYUSERPWD, "login:password");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, "XMLRequest=" . $xml); // add POST fields
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		set_curl_options($ch, $module_params);
		$response = curl_exec($ch); // run the whole process
		curl_close($ch);

		$response = trim($response);
		if (strlen ($response)) {
			$statusMessage = substr($response, strpos($response, "<statusMessage>")+strlen("<statusMessage>"), strpos($response, "</statusMessage>")-strlen("<statusMessage>")-strpos($response, "<statusMessage>"));
			$statusCode = substr($response, strpos($response, "<statusCode>")+strlen("<statusCode>"), strpos($response, "</statusCode>")-strlen("<statusCode>")-strpos($response, "<statusCode>"));
			$requestID = substr($response, strpos($response, "<requestID>")+strlen("<requestID>"), strpos($response, "</requestID>")-strlen("<requestID>")-strpos($response, "<requestID>"));

			if ($statusMessage == 'OK') {
				$strProduct = substr($response, strpos($response, "<product id=")+strlen("<product id=>"), strpos($response, "</product>")-strlen("<product id=>")-strpos($response, "<product id="));
				$index = 0;
				$aryProducts = false;
				while (strpos($response, "</product>")) {

					$aryProducts[$index]['id'] = substr($response, strpos($response, "<product id=")+strlen("<product id=\""),
					strpos($response, "\" ")-strlen("<product id=")-strpos($response, "<product id=")-1);

					$aryProducts[$index]['name'] = substr($response, strpos($response, "<name>")+strlen("<name>"), strpos($response, "</name>")-strlen("<name>")-strpos($response, "<name>"));
					$aryProducts[$index]['rate'] = substr($response, strpos($response, "<rate>")+strlen("<rate>"), strpos($response, "</rate>")-strlen("<rate>")-strpos($response, "<rate>"));
					$aryProducts[$index]['shippingDate'] = substr($response, strpos($response, "<shippingDate>")+strlen("<shippingDate>"), strpos($response, "</shippingDate>")-strlen("<shippingDate>")-strpos($response, "<shippingDate>"));
					$aryProducts[$index]['deliveryDate'] = substr($response, strpos($response, "<deliveryDate>")+strlen("<deliveryDate>"), strpos($response, "</deliveryDate>")-strlen("<deliveryDate>")-strpos($response, "<deliveryDate>"));
					$aryProducts[$index]['deliveryDayOfWeek'] = substr($response, strpos($response, "<deliveryDayOfWeek>")+strlen("<deliveryDayOfWeek>"), strpos($response, "</deliveryDayOfWeek>")-strlen("<deliveryDayOfWeek>")-strpos($response, "<deliveryDayOfWeek>"));
					$aryProducts[$index]['nextDayAM'] = substr($response, strpos($response, "<nextDayAM>")+strlen("<nextDayAM>"), strpos($response, "</nextDayAM>")-strlen("<nextDayAM>")-strpos($response, "<nextDayAM>"));
					$aryProducts[$index]['packingID'] = substr($response, strpos($response, "<packingID>")+strlen("<packingID>"), strpos($response, "</packingID>")-strlen("<packingID>")-strpos($response, "<packingID>"));
					$index++;
					$response = substr($response, strpos($response, "</product>") + strlen("</product>"));
				}

				for ($i=0; $i<$index; $i++){
					for ($ms = 0; $ms < sizeof($module_shipping); $ms++) {
						list($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $cost, $row_tare_weight, $row_shipping_taxable) = $module_shipping[$ms];
						if (strtoupper($row_shipping_type_code) == strtoupper($aryProducts[$i]['id'])) {
							$shipping_types[] = array($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc . ': ' . $aryProducts[$i]['deliveryDate'], $aryProducts[$i]['rate'], $row_tare_weight, $row_shipping_taxable, $aryProducts[$i]['deliveryDate']);
							break;
						}
					}
				}
			}
			else
			{
				if (strpos($response, "<error>")) {
					$r->errors .= sprintf("Canada Post Error occured: %s - %s<br>\n", $statusCode, $statusMessage);
				}
			}
		} else {
			$r->errors .= "Canada Post module error: Empty response from remote server.<br>\n";
		}
	} else {
		$r->errors .= "Can't initialize cURL.<br>\n";
	}

	function canadapost_prepare_rate_request($module_params)
	{
		global $country, $state, $postal_code, $order_total, $shipping_weight, $shipping_packages, $city;

		$xml = "<?xml version=\"1.0\" ?>\n";

		$xml .= "<eparcel>\n";

		if (isset($module_params["language"])){
			$xml .= "	<language>" .  $module_params["language"] . "</language>\n";
		} else {
			$xml .= "	<language>en</language>\n";
		}
		$xml .= "	<ratesAndServicesRequest>\n";
		if (isset($module_params["CPCID"])) $xml .= "		<merchantCPCID>" . $module_params["CPCID"] . "</merchantCPCID>\n";
		if (isset($module_params["fromPostalCode"])) $xml .= "		<fromPostalCode>" . $module_params["fromPostalCode"] . "</fromPostalCode>\n";
		if (isset($module_params["AroundTime"])) $xml .= "		<turnAroundTime>" . $module_params["AroundTime"] . "</turnAroundTime>\n";
		if ($order_total > 0){$xml .= "		<itemsPrice>" . $order_total . "</itemsPrice>\n";}
		else if (isset($module_params["price"])) $xml .= "		<itemsPrice>" . $module_params["price"] . "</itemsPrice>\n";
		$xml .= "		<lineItems>\n";
		$j = 0;
		for ($i = 0; $i < count($shipping_packages); $i ++){
			if ($shipping_packages[$i]["width"] > 0 && $shipping_packages[$i]["height"] > 0 && $shipping_packages[$i]["length"] > 0){
				$j++;
				$xml .= "		<item>\n";
				$xml .= "			<quantity>" . $shipping_packages[$i]["quantity"] . "</quantity>\n";
				$xml .= "			<weight>" . $shipping_packages[$i]["weight"] . "</weight>\n";
				$xml .= "			<length>" . $shipping_packages[$i]["length"] . "</length>\n";
				$xml .= "			<width>" . $shipping_packages[$i]["width"] . "</width>\n";
				$xml .= "			<height>" . $shipping_packages[$i]["height"] . "</height>\n";
				$xml .= "			<description>Item No ".$j."</description>\n";
				$xml .= "			<readyToShip/>\n";
				$xml .= "		</item>\n";
			}
		}
		if ($j != 0){
			$xml .= "		<item>\n";
			$xml .= "			<quantity>" . $module_params['quantity'] . "</quantity>\n";
			$xml .= "			<weight>" . $module_params['weight'] . "</weight>\n";
			$xml .= "			<length>" . $module_params['length'] . "</length>\n";
			$xml .= "			<width>" . $module_params['width'] . "</width>\n";
			$xml .= "			<height>" . $module_params['height'] . "</height>\n";
			$xml .= "			<description>Items all</description>\n";
			$xml .= "			<readyToShip/>\n";
			$xml .= "		</item>\n";
		}
		$xml .= "		</lineItems>\n";
		$xml .= "		<city>" . $city . "</city>\n";
		$xml .= "		<provOrState>" . $state . "</provOrState>\n";
		$xml .= "		<country>" . $country . "</country>\n";
		$xml .= "		<postalCode>" . $postal_code . "</postalCode>\n";
		$xml .= "	</ratesAndServicesRequest>\n";
		$xml .= "</eparcel>\n";

		return $xml;
	}

?>