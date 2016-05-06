<?php

	if (!strlen($external_url) || !strlen($country_code)) {
		return;
	}
	
	$domestic = (strtolower($country_code) == "us");
	
	if ($domestic && !strlen($postal_code)) { return; }

	$fedex_error = "";
	$ratetype = $module_params["RateType"];
	if (!preg_match("/".$ratetype."/"," PAYOR_LIST RATED_LIST RATED_ACCOUNT PAYOR_ACCOUNT")){
		$ratetype = "PAYOR_ACCOUNT";
	}

	$xml_request = fedex_prepare_rate_request($module_params);

	$ch = @curl_init();
	if ($ch){
		$header = array();
		$header[] = "POST /web-services HTTP/1.1";
		$header[] = "Host: gatewaybeta.fedex.com";
		$header[] = "Connection: Keep-Alive";
		$header[] = "User-Agent: PHP-SOAP/5.2.6";
		$header[] = "Content-Type: text/xml; charset=utf-8";
		$header[] = "SOAPAction: \"getRates\"";
		$header[] = "Content-Length: ".strlen($xml_request);
		
		curl_setopt($ch, CURLOPT_URL, $external_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		$fedex_response = curl_exec($ch);
		curl_close($ch);

		$tree = GetXMLTree($fedex_response);
		
		if (!isset($tree["SOAPENV:ENVELOPE"][0]["SOAPENV:BODY"])){
			return;
		}
		
		$error_code = $tree["SOAPENV:ENVELOPE"][0]["SOAPENV:BODY"][0]["RATE:RATEREPLY"][0]["RATE:NOTIFICATIONS"][0]["RATE:CODE"][0]["VALUE"];
		$error_message = $tree["SOAPENV:ENVELOPE"][0]["SOAPENV:BODY"][0]["RATE:RATEREPLY"][0]["RATE:NOTIFICATIONS"][0]["RATE:MESSAGE"][0]["VALUE"];
		$error_severity = $tree["SOAPENV:ENVELOPE"][0]["SOAPENV:BODY"][0]["RATE:RATEREPLY"][0]["RATE:HIGHESTSEVERITY"][0]["VALUE"];
		
		$fedex_error .= $error_code . " -  " . $error_severity . " : " . $error_message . "<br>\n";
		
		if ($error_code == 0 or $error_code == 834){
		
			$methods = $tree["SOAPENV:ENVELOPE"][0]["SOAPENV:BODY"][0]["RATE:RATEREPLY"][0]["RATE:RATEREPLYDETAILS"];
			for ($i=0;$i<count($methods);$i++){

				$ship_code = $methods[$i]["RATE:SERVICETYPE"][0]["VALUE"];
				$type_account = $methods[$i]["RATE:RATEDSHIPMENTDETAILS"];
				for ($j=0;$j<count($type_account);$j++){
					$RateTypeTemp = $type_account[$j]["RATE:SHIPMENTRATEDETAIL"][0]["RATE:RATETYPE"][0]["VALUE"];

					if ($RateTypeTemp == $ratetype){
						if (isset($type_account[$j]["RATE:RATEDPACKAGES"][0]["RATE:PACKAGERATEDETAIL"][0]["RATE:NETCHARGE"][0]["RATE:AMOUNT"][0]["VALUE"])) {
							$fedex_rate = $type_account[$j]["RATE:RATEDPACKAGES"][0]["RATE:PACKAGERATEDETAIL"][0]["RATE:NETCHARGE"][0]["RATE:AMOUNT"][0]["VALUE"];
						} else if (isset($type_account[$j]["RATE:SHIPMENTRATEDETAIL"][0]["RATE:TOTALNETCHARGE"][0]["RATE:AMOUNT"][0]["VALUE"])){
							$fedex_rate = $type_account[$j]["RATE:SHIPMENTRATEDETAIL"][0]["RATE:TOTALNETCHARGE"][0]["RATE:AMOUNT"][0]["VALUE"];
						} else {
							$fedex_rate = false;
						}
						
						if ($fedex_rate){
							for ($ms = 0; $ms < sizeof($module_shipping); $ms++) {
								list($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $cost, $row_tare_weight, $row_shipping_taxable) = $module_shipping[$ms];
								if ($row_shipping_type_code == $ship_code) {
									$shipping_types[] = array($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $fedex_rate, $row_tare_weight, $row_shipping_taxable, '');
									break;
								}
							}
						}
					}
				}
			}
			
		} else {
			$r->errors .= $fedex_error;
		}
	} else {
		return;
	}
	
	function fedex_prepare_rate_request($module_params, $domestic = false)
	{
		global $r, $shipping_weight, $state_code, $country_code, $postal_code, $shipping_city, $shipping_street;
		global $goods_total, $currency, $shipping_weight, $shipping_weight_measure, $city, $address1, $fedex_service;
		global $language_code, $shipping_packages;

		// define some parameters
		$errors = "";
		$packaging = isset($module_params["Packaging"]) ? $module_params["Packaging"] : "";
		$origin_state_code = isset($module_params["StateOrProvinceCode"]) ? $module_params["StateOrProvinceCode"] : "";
		$origin_postal_code = isset($module_params["PostalCode"]) ? $module_params["PostalCode"] : "";
		$origin_country_code = isset($module_params["CountryCode"]) ? $module_params["CountryCode"] : "";
		
		$week_day = date("w");
		if ($week_day == 0) {
			$days_off = 1;
		} elseif ($week_day == 6) {
			$days_off = 2;
		} else {
			$days_off = 0;
		}
		
		$ship_date = mktime (0, 0, 0, date("n"), date("j") + $days_off, date("Y"));
		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml.= "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ns1=\"http://fedex.com/ws/rate/v5\">\n";
		$xml.= "<SOAP-ENV:Body>\n";
		$xml.= "	<ns1:RateRequest>\n";
		$xml.= "	<ns1:WebAuthenticationDetail>\n";
		$xml.= "		<ns1:UserCredential>\n";
		$xml.= "			".add_line_fedex("Key",$module_params["Key"]);
		$xml.= "			".add_line_fedex("Password",$module_params["Password"]);
		$xml.= "		</ns1:UserCredential>\n";
		$xml.= "	</ns1:WebAuthenticationDetail>\n";
		$xml.= "	<ns1:ClientDetail>\n";
		$xml.= "		".add_line_fedex("AccountNumber",$module_params["AccountNumber"]);
		$xml.= "		".add_line_fedex("MeterNumber",$module_params["MeterNumber"]);
		$xml.= "	</ns1:ClientDetail>\n";
		if ($domestic){
			$xml.= "		<ns1:TransactionDetail>\n";
			$xml.= "			<ns1:CustomerTransactionId>ExpressUSBasicRate</ns1:CustomerTransactionId>\n";
			$xml.= "		</ns1:TransactionDetail>\n";
		} else {
			$xml.= "		<ns1:TransactionDetail>\n";
			$xml.= "			<ns1:CustomerTransactionId>ExpressIntlRate</ns1:CustomerTransactionId>\n";
			$xml.= "		</ns1:TransactionDetail>\n";
		}
		$xml.= "	<ns1:Version>\n";
		$xml.= "		".add_line_fedex("ServiceId",$module_params["ServiceId"]);
		$xml.= "		".add_line_fedex("Major",$module_params["Major"]);
		$xml.= "		".add_line_fedex("Intermediate",$module_params["Intermediate"]);
		$xml.= "		".add_line_fedex("Minor",$module_params["Minor"]);
		$xml.= "	</ns1:Version>\n";
		$xml.= "	<ns1:RequestedShipment>\n";
		$xml.= "		<ns1:ShipTimestamp>".date("Y-m-d", $ship_date)."T00:00:00-00:00</ns1:ShipTimestamp>\n";
		$xml.= "		<ns1:DropoffType>REGULAR_PICKUP</ns1:DropoffType>\n";
		$xml.= "		<ns1:Shipper>\n";
		$xml.= "			<ns1:Address>\n";
		$xml.= "				".add_line_fedex("StreetLines",$module_params["StreetLines"]);
		$xml.= "				".add_line_fedex("City",$module_params["City"]);
		$xml.= "				".add_line_fedex("StateOrProvinceCode",$module_params["StateOrProvinceCode"]);
		$xml.= "				".add_line_fedex("PostalCode",$module_params["PostalCode"]);
		$xml.= "				".add_line_fedex("CountryCode",$module_params["CountryCode"]);
		$xml.= "			</ns1:Address>\n";
		$xml.= "		</ns1:Shipper>\n";
		$xml.= "		<ns1:Recipient>\n";
		$xml.= "			<ns1:Address>\n";
		$xml.= "				".add_line_fedex("StreetLines",$address1);
		$xml.= "				".add_line_fedex("City",$city);
		$xml.= "				".add_line_fedex("StateOrProvinceCode",$state_code);
		$xml.= "				".add_line_fedex("PostalCode",$postal_code);
		$xml.= "				".add_line_fedex("CountryCode",$country_code);
		$xml.= "			</ns1:Address>\n";
		$xml.= "		</ns1:Recipient>\n";
		$xml.= "		<ns1:ShippingChargesPayment>\n";
		$xml.= "			<ns1:PaymentType>SENDER</ns1:PaymentType>\n";
		$xml.= "			<ns1:Payor>\n";
		$xml.= "				".add_line_fedex("AccountNumber",$module_params["AccountNumber"]);
		$xml.= "				".add_line_fedex("CountryCode",$module_params["CountryCode"]);
		$xml.= "			</ns1:Payor>\n";
		$xml.= "		</ns1:ShippingChargesPayment>\n";
		$xml.= "		<ns1:RateRequestTypes>LIST</ns1:RateRequestTypes>\n";
		
		$xml2 = "";
		$prod_costs = 0;
		
		$j = 0;
		
		if (!$module_params["WeightUnits"]){
			$module_params["WeightUnits"] = "LB";
		}
		
		if (!$module_params["DimensionsUnit"]){
			$module_params["DimensionsUnit"] = "IN";
		}
		
		for ($i = 0; $i < count($shipping_packages); $i ++){
			
			$j++;
				
			if ($shipping_packages[$i]["length"] > 0) {
				$length = $shipping_packages[$i]["length"];
			} else if ($module_params["Length"] > 0) {
				$length = $module_params["Length"];
			} else {
				$length = 1;
			}
			
			if ($shipping_packages[$i]["width"] > 0) {
				$width = $shipping_packages[$i]["width"];
			} else if ($module_params["Width"] > 0) {
				$width = $module_params["Width"];
			} else {
				$width = 1;
			}
			
			if ($shipping_packages[$i]["height"] > 0) {
				$height = $shipping_packages[$i]["height"];
			} else if ($module_params["Height"] > 0) {
				$height = $module_params["Height"];
			} else {
				$height = 1;
			}
			
			if ($shipping_packages[$i]["weight"] > 0) {
				$weight = $shipping_packages[$i]["weight"];
			} else if ($module_params["WeightValue"] > 0) {
				$weight = $module_params["WeightValue"];
			} else {
				$weight = 1;
			}
			
			$prod_cost = $shipping_packages[$i]["price"] / $shipping_packages[$i]["packages"];
			$quantity = $shipping_packages[$i]["quantity"] * $shipping_packages[$i]["packages"];
			
			$xml2.= "		<ns1:RequestedPackages>\n";
			$xml2.= "			<ns1:SequenceNumber>".$quantity."</ns1:SequenceNumber>\n";
			$xml2.= "			<ns1:InsuredValue>\n";
			$xml2.= "				".add_line_fedex("Currency",$currency["code"]);
			$xml2.= "				".add_line_fedex("Amount",$prod_cost);
			$xml2.= "			</ns1:InsuredValue>\n";
			$xml2.= "			<ns1:Weight>\n";
			$xml2.= "				".add_line_fedex("Units",$module_params["WeightUnits"]);
			$xml2.= "				".add_line_fedex("Value",$weight);
			$xml2.= "			</ns1:Weight>\n";
			$xml2.= "			<ns1:Dimensions>\n";
			$xml2.= "				".add_line_fedex("Length",$length,"ns1:","",INTEGER);
			$xml2.= "				".add_line_fedex("Width",$width,"ns1:","",INTEGER);
			$xml2.= "				".add_line_fedex("Height",$height,"ns1:","",INTEGER);
			$xml2.= "				".add_line_fedex("Units",$module_params["DimensionsUnit"]);
			$xml2.= "			</ns1:Dimensions>\n";
			$xml2.= "			<ns1:ItemDescription>Item #".$j."</ns1:ItemDescription>\n";
			$xml2.= "			<ns1:CustomerReferences>\n";
			$xml2.= "				<ns1:CustomerReferenceType>CUSTOMER_REFERENCE</ns1:CustomerReferenceType>\n";
			$xml2.= "				<ns1:Value>Undergraduate application</ns1:Value>\n";
			$xml2.= "			</ns1:CustomerReferences>\n";
			$xml2.= "		</ns1:RequestedPackages>\n";

		}
		
		$xml.= "		<ns1:PackageCount>".$j."</ns1:PackageCount>\n";
		$xml.= "		<ns1:PackageDetail>INDIVIDUAL_PACKAGES</ns1:PackageDetail>\n";
		
		$xml .= $xml2;
		
		$xml.= "	</ns1:RequestedShipment>\n";
		$xml.= "</ns1:RateRequest>\n";
		$xml.= "</SOAP-ENV:Body>\n";
		$xml.= "</SOAP-ENV:Envelope>\n";

		return $xml;
	}
	
	function add_line_fedex($parameter,$value,$v3="ns1:",$parameter2 = "",$int=""){
		if ($int == INTEGER){
			$value = intval($value);
		}
		if (strlen($parameter2)) {
			$xml_string = "<".$v3.$parameter." ".$parameter2.">".$value."</".$v3.$parameter.">\n";
		} else {
			$xml_string = "<".$v3.$parameter.">".$value."</".$v3.$parameter.">\n";
		}
		return $xml_string;
	}
	
	function GetNextValue($values, &$i) 
    {
        $next_value = array(); 
    
        if (isset($values[$i]['value'])) {
            $next_value['VALUE'] = $values[$i]['value']; 
		}
    
        while (++$i < count($values)) { 
            switch ($values[$i]['type']) {
                case 'cdata': 
                    if (isset($next_value['VALUE'])) {
                        $next_value['VALUE'] .= $values[$i]['value']; 
                    } else {
                        $next_value['VALUE'] = $values[$i]['value']; 
					}
                    break;
    
                case 'complete': 
                    if (isset($values[$i]['attributes'])) {
                        $next_value[$values[$i]['tag']][]['ATTRIBUTES'] = $values[$i]['attributes'];
                        $index = count($next_value[$values[$i]['tag']])-1;
    
                        if (isset($values[$i]['value'])) 
                            $next_value[$values[$i]['tag']][$index]['VALUE'] = $values[$i]['value']; 
                        else
                            $next_value[$values[$i]['tag']][$index]['VALUE'] = ''; 
                    } else {
                        if (isset($values[$i]['value'])) {
                            $next_value[$values[$i]['tag']][]['VALUE'] = $values[$i]['value']; 
                        } else {
                            $next_value[$values[$i]['tag']][]['VALUE'] = ''; 
						}
					}
                    break; 
    
                case 'open': 
                    if (isset($values[$i]['attributes'])) {
                        $next_value[$values[$i]['tag']][]['ATTRIBUTES'] = $values[$i]['attributes'];
                        $index = count($next_value[$values[$i]['tag']])-1;
                        $next_value[$values[$i]['tag']][$index] = array_merge($next_value[$values[$i]['tag']][$index],GetNextValue($values, $i));
                    } else {
                        $next_value[$values[$i]['tag']][] = GetNextValue($values, $i);
                    }
                    break; 
    
                case 'close': 
                    return $next_value; 
            } 
        } 
    } 

    function GetXMLTree($xml_parse) 
    { 
        $data = $xml_parse;
       
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
        xml_parse_into_struct($parser, $data, $values, $index); 
        xml_parser_free($parser);

        $tree = array(); 
        $i = 0; 

        if (isset($values[$i]['attributes'])) {
	    	$tree[$values[$i]['tag']][]['ATTRIBUTES'] = $values[$i]['attributes']; 
	    	$index = count($tree[$values[$i]['tag']])-1;
	    	$tree[$values[$i]['tag']][$index] = array_merge($tree[$values[$i]['tag']][$index], GetNextValue($values, $i));
        }
        else {
            $tree[$values[$i]['tag']][] = GetNextValue($values, $i); 
		}
        
        return $tree; 
    }
	
?>