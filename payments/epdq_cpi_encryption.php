<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  epdq_cpi_encryption.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * ePDQ CPI (www.tele-pro.co.uk/epdq/) transaction handler by http://www.viart.com/
 */

function get_epdqdata($payment_parameters)
{
	// get payment parameters
	$clientid = isset($payment_parameters["clientid"]) ? $payment_parameters["clientid"] : "";
	$password = isset($payment_parameters["password"]) ? $payment_parameters["password"] : "";
	$oid = isset($payment_parameters["oid"]) ? $payment_parameters["oid"] : "";
	$chargetype = isset($payment_parameters["chargetype"]) ? $payment_parameters["chargetype"] : "";
	$currencycode = isset($payment_parameters["currencycode"]) ? $payment_parameters["currencycode"] : "";
	$total = isset($payment_parameters["total"]) ? $payment_parameters["total"] : "";

	// use this variable to set data
	$epdqdata = "";

	//define the remote cgi in readiness to call pullpage function
	$server="secure2.epdq.co.uk";
	$url="/cgi-bin/CcxBarclaysEpdqEncTool.e";

	//the following parameters have been obtained earlier in the merchant's webstore
	//clientid, passphrase, oid, currencycode, total
	$params  = "clientid=" . $clientid;
	$params .= "&password=" . $password;
	$params .= "&oid=" . $oid;
	$params .= "&chargetype=Auth";
	$params .= "&currencycode=" . $currencycode;
	$params .= "&total=" . $total;
	//echo $params;
	//perform the HTTP Post
	$response = pullpage($server, $url, $params);

	//split the response into separate lines
	$response_lines=explode("\n", $response);

	//for each line in the response check for the presence of the string 'epdqdata'
	//this line contains the encrypted string
	$response_line_count=count($response_lines);
	for ($i=0; $i < $response_line_count; $i++) {
		$response_line = $response_lines[$i];
  	if (preg_match("/epdqdata/i", $response_line)) {
			$epdqdata_field = $response_line;
			if (preg_match ("/value=\"?(\w+)\"?/i", $response_line, $match)) {
				$epdqdata = $match[1];
			}
    }
	}

	return $epdqdata;
}

function pullpage($host, $usepath, $postdata = "")
{
	// open socket to filehandle(epdq encryption cgi)
	$fp = fsockopen( $host, 80, $errno, $errstr, 60 );

	// prepare var for output
	$output = "";

	//check that the socket has been opened successfully
	if (!$fp) {
		print "$errstr ($errno)<br>\n";
	} else {

		//write the data to the encryption cgi
		fputs($fp, "POST $usepath HTTP/1.0\n");
		$strlength = strlen( $postdata );
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
		fputs($fp, "Content-length: ".$strlength."\n\n" );
		fputs($fp, $postdata."\n\n" );

    //read the response from the remote cgi
    //while content exists, keep retrieving document in 1K chunks
		while (!feof($fp)) {
			$output .= fgets( $fp, 1024);
		}

		//close the socket connection
		fclose($fp);
	}

	//return the response
	return $output;
}

?>