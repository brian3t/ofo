<?php

// Copyright 2008, FedEx Corporation. All rights reserved.
// Version 2.0.1   

require_once('fedex-common.php');

$newline = "<br />";
//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "AddressValidationService_v2.wsdl"; 

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); 

$request['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => 'vj7PSfJ8DpA5NiPs', 'Password' => 'oiTYAH71TMZQ6x6CU5C8oAva9')); // Replace with FedEx provided credentials
									  
$request['ClientDetail'] = array('AccountNumber' => '469460860', 'MeterNumber' => '100776231'); // Replace 'XXX' with your account and meter number
$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Address Validation Request v2 using PHP ***');
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
$request['AddressesToValidate'] = array(0 => array('AddressId' => 'WTC','Address' => array('StreetLines' => array('1718 Catalpa Road'),'PostalCode' => '92011')));
try 
{
    $response = $client ->addressValidation($request);

	if ($response -> AddressResults -> ProposedAddressDetails -> ResidentialStatus == 'RESIDENTIAL')
	{ 
		echo "RESIDENTIAL";
	}
	else
	{
		echo "COMMERCIAL";
	}
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
    {
        printRequestResponse($client);
    }
    else
    {
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