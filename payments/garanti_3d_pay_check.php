<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  garanti_3d_pay_check.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * Garanti 3D Pay (www.garanti.com.tr) transaction handler by ViArt Ltd. (www.viart.com)
 */

	$clientid       = get_param("clientid");
	$oid            = get_param("oid");
	$PAResSyntaxOK  = get_param("PAResSyntaxOK");
	$PAResVerified  = get_param("PAResVerified");
	$version        = get_param("version");
	$mercantID      = get_param("mercantID");
	$xid            = get_param("xid");
	$mdStatus       = get_param("mdStatus");
	$mdErrorMsg     = get_param("mdErrorMsg");
	$txstatus       = get_param("txstatus");
	$iReqCode       = get_param("iReqCode");
	$iReqDetail     = get_param("iReqDetail");
	$vendorCode     = get_param("vendorCode");
	$eci            = get_param("eci");
	$cavv           = get_param("cavv");
	$cavvAlgorithm  = get_param("cavvAlgorithm");
	$md             = get_param("md");
	$rnd            = get_param("rnd");
	$HASH           = get_param("HASH");
	$HASHPARAMS     = get_param("HASHPARAMS");
	$HASHPARAMSVAL  = get_param("HASHPARAMSVAL");
	$AuthCode       = get_param("AuthCode");
	$Response       = get_param("Response");
	$HostRefNum     = get_param("HostRefNum");
	$ProcReturnCode = get_param("ProcReturnCode");
	$transaction_id = get_param("TransId");
	$error_message  = get_param("ErrMsg");
	if($payment_parameters['oid'] != $oid){
		$pending_message = "The parameter 'oid' is not equal to value of database. ".CHECKOUT_PENDING_MSG;
		return;
	}
	if(strtolower($Response) != 'approved'){
		$error_message = "Transaction status is ".$Response.". ".$error_message;
		return;
	}
	if(strlen($error_message)){
		return;
	}
	if(!strlen($transaction_id)){
		$error_message  = "Can't obtain parameter 'TransId'.";
		return;
	}

	$paramsval="";
	$index1=0;
	$index2=0;
	while($index1 < strlen($HASHPARAMS))
	{
		$index2 = strpos($HASHPARAMS,":",$index1);
		$vl = get_param(substr($HASHPARAMS,$index1,$index2- $index1));
		if($vl == null)	$vl = "";
		$paramsval = $paramsval . $vl; 
		$index1 = $index2 + 1;
	}
	$hashval = $paramsval.$payment_parameters['storekey'];
	$hash = base64_encode(pack('H*',sha1($hashval)));
	if($paramsval != $HASHPARAMSVAL || $HASH != $hash) {
		$error_message = "Parameter 'HASH' or 'HASHPARAMSVAL' have a wrong value.";
	}

?>