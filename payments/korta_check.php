<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  korta_check.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
* KORTA in Iceland transaction handler by www.viart.com
*/


// secretcode gotten from KORTA
$secretcode = $payment_params["secretcode"];

// Values that are posted by KORTA Webpayment system
$korta_reference = get_param("reference");
$korta_checkvaluemd5 = get_param("checkvaluemd5");
$korta_downloadmd5 = get_param("downloadmd5");
$korta_time = get_param("time");
$korta_cardbrand = get_param("cardbrand");
$korta_card4 =  get_param("card4");

$my_downloadmd5 = htmlentities('2' . $korta_checkvaluemd5  . $korta_reference . $secretcode);
if (isset($payment_params["is_test"]) && $payment_params["is_test"]) {
	$my_downloadmd5 .= "TEST";
}
$my_downloadmd5 = md5($my_downloadmd5);

if ($my_downloadmd5 == $korta_downloadmd5)
{
	$success_message = 'Electronic signature correct!<br/>';
	$success_message .= '<strong>Payment accepted!</strong>' . '<br/>';
	$success_message .= 'Order           : ' . $korta_reference . '<br/>';
	$success_message .= 'Time of payment : ' . $korta_time . '<br/>';
	$success_message .= 'Card type       : ' . $korta_cardbrand . '<br/>';
	$success_message .= 'Last 4 in cardnumber : ' . $korta_card4 . '<br/>';
}
else
{
	$error_message = 'Electronic signature does not match, is someone hacking ? <br/>';
	$error_message .= 'Signature = [' . my_downloadmd5 . ']<br/>';
}


?>