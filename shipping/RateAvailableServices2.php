<?php

// Copyright 2008, FedEx Corporation. All rights reserved.
// Version 6.0.0

require_once('fedex-common.php');

$newline = "<br />";

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

$path_to_wsdl = "RateService_v6.wsdl";

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); 

$request['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => 'vj7PSfJ8DpA5NiPs', 'Password' => 'oiTYAH71TMZQ6x6CU5C8oAva9')); 
 
$request['ClientDetail'] = array('AccountNumber' => '469460860', 'MeterNumber' => '100776231'); 
$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v6 using PHP ***');
$request['Version'] = array('ServiceId' => 'crs', 'Major' => '6', 'Intermediate' => '0', 'Minor' => '0');
$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; 
$request['RequestedShipment']['ShipTimestamp'] = $ship_date;

$request['RequestedShipment']['Recipient'] = array('Address' => array(
                                          'StreetLines' => array($_POST['street']), // Origin details
                                          'City' => $_POST['street'],
                                          'StateOrProvinceCode' => $_POST['state'],
                                          'PostalCode' => $_POST['zip'],
                                          'CountryCode' => 'US'));
$request['RequestedShipment']['Shipper'] = array('Address' => array (
                                               'StreetLines' => array('1340 Specialty Drive'), // Destination details
                                               'City' => 'Vista',
                                               'StateOrProvinceCode' => 'CA',
                                               'PostalCode' => '92081',
											   //'Residential' => '1',
                                               'CountryCode' => 'US'));
$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                        'Payor' => array('AccountNumber' => '469460860', 
                                                                     'CountryCode' => 'US'));
$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
$request['RequestedShipment']['PackageCount'] = '1';
$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
$request['RequestedShipment']['RequestedPackages'] = array('0' => array('SequenceNumber' => '1',
                                                                  'Weight' => array('Value' => $_POST['weight'],
                                                                                    'Units' => 'LB')));

try 
{
    $response = $client ->getRates($request);
	
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
    {
        echo 'Rates for following service type(s) were returned.'. $newline. $newline; 
        foreach ($response -> RateReplyDetails as $rateReply)
        {           
			echo $rateReply -> ServiceType."</br>";
			$rsd = $rateReply->RatedShipmentDetails;
			
			// For some reason Fedex returns duplicate responses for non ground shipments so we need to check if they did and grab the last response
			//if(is_array($rsd)) {
			//	$rsd = end($rateReply->RatedShipmentDetails);
			//}
			//print_r($rateReply);
			foreach ($rsd as $test)
			{
				//echo $test->ShipmentRateDetail->RateType;
				//echo $test->ShipmentRateDetail->TotalNetFedExCharge->Amount."</br>";
				if ($test->ShipmentRateDetail->RateType == "PAYOR_ACCOUNT") {
					echo "ACCOUNT: ";
					$ACCOUNT = $test->ShipmentRateDetail->TotalNetFedExCharge->Amount;
					echo $ACCOUNT."<br />";
				}
				if ($test->ShipmentRateDetail->RateType == "PAYOR_LIST") {
					echo "LIST: ";
					$LIST = $test->ShipmentRateDetail->TotalNetFedExCharge->Amount;
					echo $LIST."<br />";
				}
			}
			$amount = abs(round($ACCOUNT + (($LIST - $ACCOUNT)*.7),2)); // Find the difference and multiply by desired percentage.  Return 2 decimals and only positive number.
			echo "What we charge:".$amount;
			//print_r($rsd);
			echo $rsd->ShipmentRateDetail->TotalNetFedExCharge->Amount;  
			echo $notification . $newline;  
        } 

        printRequestResponse($client);
    }
    else
    {
        echo 'Error in processing transaction.'. $newline. $newline; 
        foreach ($response -> Notifications as $notification)
        {           
            if(is_array($response -> Notifications))
            {              
               echo $notification -> Severity;
               echo ': ';           
               echo $notification -> Message . $newline;
            }
            else
            {
                echo $notification . $newline;
            }
        } 
    } 
    
    writeToLog($client);    // Write to log file   

} catch (SoapFault $exception) {
   printFault($exception, $client);        
}

?>