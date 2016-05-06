<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  payson_check.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
/* * Payson (https://www.payson.se/) transaction handler by www.viart.com */	$okurl        = get_param("OkURL");	$refnr        = get_param("RefNr");	$paysonref    = get_param("Paysonref");	$md5_hash     = get_param("MD5");	if(!strlen($refnr) && !strlen($md5_hash)){		$pending_message = CHECKOUT_PENDING_MSG;	}elseif($refnr != $order_id){		$transaction_id = "RefNr(" . $refnr . ") " . $paysonref;		$pending_message = CHECKOUT_PENDING_MSG . " Parameters 'RefNr' and 'order_id' have a various values. ";	}else{		$transaction_id = "RefNr(" . $refnr . ") " . $paysonref;		if($md5_hash != md5($okurl . $paysonref . $payment_parameters['secretkey'])){			$error_message = "'HASH' have a wrong value.";		}	}?>