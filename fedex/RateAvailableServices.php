<?php

// Copyright 2008, FedEx Corporation. All rights reserved.
// Version 6.0.0

require_once('fedex-common.php');

$newline = "<br />";
//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "RateService_v6.wsdl";

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

$request['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => 'q7UiIn0sHq7ivgfr', 'Password' => 'h0NMCAM1nJB7kE2tzLDOaQpXh')); // Replace with FedEx credentials 
 
$request['ClientDetail'] = array('AccountNumber' => '319793534', 'MeterNumber' => '103966874'); // Replace 'XXX' with your account and meter number
$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v6 using PHP ***');
$request['Version'] = array('ServiceId' => 'crs', 'Major' => '6', 'Intermediate' => '0', 'Minor' => '0');
$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
$request['RequestedShipment']['ShipTimestamp'] = date('c');
// Service Type and Packaging Type are not passed in the request
$request['RequestedShipment']['Shipper'] = array('Address' => array(
                                          'StreetLines' => array(''), // Origin details
                                          'City' => 'Berlin',
                                          'StateOrProvinceCode' => 'GE',
                                          'PostalCode' => '16341',
                                          'CountryCode' => 'US'));
$request['RequestedShipment']['Recipient'] = array('Address' => array (
                                               'StreetLines' => array('1718 Catalpa Road'), // Destination details
                                               'City' => 'Carlsbad',
                                               'StateOrProvinceCode' => 'CA',
                                               'PostalCode' => '92011',
                                               'CountryCode' => 'US'));
$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                        'Payor' => array('AccountNumber' => '319793534', // Replace 'XXX' with payor's account number
                                                                     'CountryCode' => 'US'));
$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
$request['RequestedShipment']['PackageCount'] = '1';
$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
$request['RequestedShipment']['RequestedPackages'] = array('0' => array('SequenceNumber' => '1',
                                                                  'InsuredValue' => array('Amount' => 0.0,
                                                                                          'Currency' => 'USD'),
                                                                  'ItemDescription' => 'College Transcripts',
                                                                  'Weight' => array('Value' => 1.0,
                                                                                    'Units' => 'LB'),
                                                                  'Dimensions' => array('Length' => 12,
                                                                                        'Width' => 4,
                                                                                        'Height' => 4,
                                                                                        'Units' => 'IN'),
                                                                  'CustomerReferences' => array('CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                                                                                                 'Value' => 'Undergraduate application')));

try 
{
    $response = $client ->getRates($request);
        
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
    {
        echo 'Rates for following service type(s) were returned.'. $newline. $newline; 
        foreach ($response -> RateReplyDetails as $rateReply)
        {           
           echo $rateReply -> ServiceType;
           echo $notification . $newline;  
           //echo $rateReply -> Message . $newline;
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