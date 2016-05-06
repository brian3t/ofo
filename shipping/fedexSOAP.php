<?php
/*
  ***      FedEx Shipping Module - SOAP Version                            ***
  ***      Created By: Tom Morris - Egghead Ventures, LLC                  ***
  ***      5/17/2009			                                           ***
*/
global $r, $shipping_weight, $state_code, $country_code, $postal_code, $city, $address1, $address2, $goods_total, $currency;

if (!strlen($country_code) || !strlen($postal_code) || $state_code == "AP" || $state_code == "AE" || $state_code == "AA" ){
	return;
}

$path_to_validation_wsdl = "./shipping/AddressValidationService_v2.wsdl"; // Path from your root - make sure gateway is correct in WSDL (not beta)
$path_to_rate_wsdl = "./shipping/RateService_v6.wsdl"; // Path from your root - make sure gateway is correct in WSDL (not beta)

ini_set("soap.wsdl_cache_enabled", "0"); // Make sure you have all of the SOAP settings enabled in your PHP.ini also

$client = new SoapClient($path_to_validation_wsdl, array('trace' => 1)); 

$request['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => $module_params["Key"], 'Password' => $module_params["Password"])); 
									  
$request['ClientDetail'] = array('AccountNumber' => $module_params["AccountNumber"], 'MeterNumber' => $module_params["MeterNumber"]); 
$request['TransactionDetail'] = array('CustomerTransactionId' => 'Address Validation Request');
$request['Version'] = array('ServiceId' => 'aval', 'Major' => '2', 'Intermediate' => '0', 'Minor' => '0');
$request['RequestTimestamp'] = date('c');
$request['Options'] = array('CheckResidentialStatus' => 1,
                             'MaximumNumberOfMatches' => 5,
                             'StreetAccuracy' => 'LOOSE',
                             'DirectionalAccuracy' => 'LOOSE',
                             'CompanyNameAccuracy' => 'LOOSE',
                             'ConvertToUpperCase' => 1,
                             'RecognizeAlternateCityNames' => 1,
                             'ReturnParsedElements' => 1);
if($address2 !='') { // Fedex will throw an error if you pass it a blank value
	$request['AddressesToValidate'] = array(0 => array('Address' => array(
									'StreetLines' => array($address1,$address2),
									'PostalCode' => $postal_code))); //Only need street and zip
} else {
	$request['AddressesToValidate'] = array(0 => array('Address' => array(
									'StreetLines' => array($address1),
									'PostalCode' => $postal_code))); //Only need street and zip
}
try {
    $response = $client ->addressValidation($request);

	if ($response -> AddressResults -> ProposedAddressDetails -> ResidentialStatus == 'BUSINESS')
	{ 
		$residential = "BUSINESS";
	}
	else
	{
		$residential = "RESIDENTIAL";  // If it doesn't find a match for any reason (including an error) just return residential
	}    
} catch (SoapFault $exception) {
	//** If it throws an error we assume residential - code can be added here to do something else
}

// If Sunday or Saturday use next Monday for ShipDate - if you ship 7 days a week you will need to disable
$week_day = date("w");
if ($week_day == 0) {
	$days_off = 1;
} elseif ($week_day == 6) {
	$days_off = 2;
} else {
	$days_off = 0;
}
$ship_date = mktime (0, 0, 0, date("n"), date("j") + $days_off, date("Y"));

$shipping_weight = $shipping_weight + $module_params["PackagingTare"]; //Module tare weight because Viart's tare doesn't work for sending weights to FedEX

$client = new SoapClient($path_to_rate_wsdl, array('trace' => 1)); 

$request['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => $module_params["Key"], 'Password' => $module_params["Password"])); 
 
$request['ClientDetail'] = array('AccountNumber' => $module_params["AccountNumber"], 'MeterNumber' => $module_params["MeterNumber"]); 
$request['TransactionDetail'] = array('CustomerTransactionId' => 'Get FedEx Shipping Rates');
$request['Version'] = array('ServiceId' => 'crs', 'Major' => '6', 'Intermediate' => '0', 'Minor' => '0');
$request['RequestedShipment']['DropoffType'] = $module_params["DropoffType"]; 
$request['RequestedShipment']['ShipTimestamp'] = $ship_date;

$request['RequestedShipment']['Shipper'] = array('Address' => array( // Origin details
										  'StateOrProvinceCode' => $module_params["StateOrProvinceCode"],
										  'PostalCode' => $module_params["PostalCode"],
										  'CountryCode' => $module_params["CountryCode"]));
if($residential == "BUSINESS") {
	$request['RequestedShipment']['Recipient'] = array('Address' => array ( // Destination details
											   'StreetLines' => array($address1), 
											   'City' => $city,
											   'StateOrProvinceCode' => $state_code,
											   'PostalCode' => $postal_code,
											   'CountryCode' => $country_code));
} else {
	$request['RequestedShipment']['Recipient'] = array('Address' => array ( // Destination details
											   'StreetLines' => array($address1), 
											   'City' => $city,
											   'StateOrProvinceCode' => $state_code,
											   'PostalCode' => $postal_code,
											   'Residential' => 1,
											   'CountryCode' => $country_code));
}
$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; //This will return both account and list rates
$request['RequestedShipment']['PackageCount'] = '1'; // Assumes only one package
$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
$request['RequestedShipment']['RequestedPackages'] = array('0' => array('SequenceNumber' => '1',
                                                                  'Weight' => array('Value' => round($shipping_weight, 1),
                                                                                    'Units' => $module_params["WeightUnit"])));
try {
	
    $response = $client ->getRates($request);
	
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
    {
		$fedex_results = array();
		$i=0;
        foreach ($response -> RateReplyDetails as $rateReply)
        {           
			$method = $rateReply -> ServiceType;
			$rsd = $rateReply->RatedShipmentDetails;
			
			foreach ($rsd as $rateDetails)
			{
				if ($rateDetails->ShipmentRateDetail->RateType == "PAYOR_ACCOUNT") {
					$ACCOUNT = $rateDetails->ShipmentRateDetail->TotalNetFedExCharge->Amount;
				}
				if ($rateDetails->ShipmentRateDetail->RateType == "PAYOR_LIST") {
					$LIST = $rateDetails->ShipmentRateDetail->TotalNetFedExCharge->Amount;
				}
			}

			if ($module_params["CustomerPercentage"] != null && is_numeric($module_params["CustomerPercentage"]))
			{
				$amount = abs(round($ACCOUNT + (($LIST - $ACCOUNT) * $module_params["CustomerPercentage"]),2)); //Calc difference and multiply percent.  Return 2 decimals and positive #.
			} else {
				$amount = $LIST; // if no percentage factor just charge list
			}
			
			if ($method == "INTERNATIONAL_ECONOMY" || $method == "INTERNATIONAL_PRIORITY") {
				$amount = $ACCOUNT + $module_params["InternationalUpcharge"];
			}
			
			if($method == "FEDEX_GROUND" || $method == "GROUND_HOME_DELIVERY") {
				if($goods_total > $module_params["FreeShipping"]) { 
					$amount = 0;
				}
			}

			$fedex_results[$i] = array("service"=>$method, "amount"=>$amount);
			$i++;
        }
    } else {
        foreach ($response -> Notifications as $notification)
        {           
            if(is_array($response -> Notifications))
            {  
			    $r->errors = "FedEx Error: " . $notification -> Severity . " - " . $notification -> Message .  "<br>\r\n";       
            }
            else
            {
				$r->errors = "FedEx Error: " . $notification .  "<br>\r\n";
            }
        } 
    } 
    
} catch (SoapFault $exception) {

	$r->errors = "FedEx Error: " . $exception->faultcode .  " - ". $exception->faultstring . "<br>\r\n";
}

for ($i = 0; $i < sizeof($fedex_results); $i++) {
	$service = $fedex_results[$i]['service'];
	$net_charge = $fedex_results[$i]['amount'];
	for ($ms = 0; $ms < sizeof($module_shipping); $ms++) {
		list($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $row_shipping_cost, $row_tare_weight, $row_shipping_taxable) = $module_shipping[$ms];
		if (strtoupper($row_shipping_type_code) == strtoupper($service)) {
			if($service == "FEDEX_GROUND" || $service == "GROUND_HOME_DELIVERY") {
				if($goods_total > $module_params["FreeShipping"]) { 
					$shipping_types[] = array($row_shipping_type_id, $row_shipping_type_code, $module_params["FreeShippingDesc"], ($net_charge + $row_shipping_cost), $row_tare_weight, $row_shipping_taxable, $row_shipping_time);
				} else {
					$shipping_types[] = array($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, ($net_charge + $row_shipping_cost), $row_tare_weight, $row_shipping_taxable, $row_shipping_time);
				}
			} else {
				$shipping_types[] = array($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, ($net_charge + $row_shipping_cost), $row_tare_weight, $row_shipping_taxable, $row_shipping_time);
			}
			break;
		}
	}
}

function subval_sort($a,$subkey) {
	foreach($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
	}
	asort($b);
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
	}
	return $c;
}

$shipping_types = subval_sort($shipping_types, 0);
?>