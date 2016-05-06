<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  dankort_validate.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * DanDomain PayNet (http://www.dandomain.dk/) transaction handler by www.viart.com
 */

	$va_status = get_param("va_status"); //success failed
	$transact  = get_param("transact");
	$orderid   = get_param("OrderID");
	$attempts  = get_param("Attempts");
	$errorcode = get_param("errorcode");

	$transaction_id = (strlen($transact))? $transact: $orderid;
	$error_message = (strlen($errorcode))? $errorcode: "";
	
	if(!strlen($transaction_id) && !strlen($error_message) && !strlen($va_status)){
		$pending_message = CHECKOUT_PENDING_MSG;
	}else{
		if($va_status != 'success'){
			$error_message = (strlen($error_message))? $error_message: TRANSACTION_DECLINED_MSG;
		}
	}
?>
