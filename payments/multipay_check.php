<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  multipay_check.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * Multipay (https://multipay.net) transaction handler by http://www.viart.com/
 */

function GetTcpinfo($psMsg,$IP, $Port, $psFront)
{
	sleep(2);
	$mysocket = fsockopen($IP, $Port, $errno, $errstr, 10);
	if(isset($mysocket)){
		fputs($mysocket,$psMsg."\n");
		$Ret = '';
		while(!feof($mysocket))
			$Ret = $Ret.fgets($mysocket,128);
		fclose($mysocket);
	}
	$pos = strpos($Ret,$psFront);
	if($pos>0)
		$Ret = trim(substr($Ret,$pos + strlen($psFront) +1));
	return $Ret;
}

	$mpCurrency = get_param("mpCurrency");
	$mpOrder_ID = get_param("mpOrder_ID");
	$mpMethod = get_param("mpMethod");
	$mpAmount = get_param("mpAmount");
	$mpVar1 = get_param("mpVar1");
	$mpVar2 = get_param("mpVar2");
	$mpStatus = get_param("mpStatus");

	$order_total = '';
	$payment_id = '';
	$seller_id = '';
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$order_total = $db->f("order_total");
		$payment_id = $db->f("payment_id");
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "payment_parameters ";
		$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$db->query($sql);
		$parametrs=array();
		while ($db->next_record()) {
			$parametrs[$db->f("parameter_name")]=$db->f("parameter_source");
		}
	}
	$pOrder_ID = str_replace('{order_id}',$order_id,$parametrs['mpOrder_ID']);

	if (strlen($mpOrder_ID) && $mpOrder_ID==$pOrder_ID){

		if (!isset($parametrs['mpSeller_ID'])) {
			$error_message = "Failed. mpSeller_ID not found ";
		} else if ($mpAmount==$order_total) {
			$sResult = GetTcpinfo("mpCheckTrans;.;".$parametrs['mpSeller_ID'].";.;".$mpOrder_ID.";.;","multipay.net", "2229","System ready.");
			$Items = split(";",$sResult);
			if (strtoupper($Items[0]) == "NOTFOUND"){
				$error_message = "Failed. Order not found ";
			}else if (strtoupper($Items[1]) == "F"){
				$error_message = "Failed. Transaction is failed ";
			}else if (strtoupper($Items[1]) == "B"){
				if((0+$Items[3]) != (0+$order_total)){
					$error_amount = "Amount";
				}
				if(strtoupper($Items[2]) != strtoupper($parametrs['mpCurrency'])){
					$error_currency = "Currency";
				}
				if (isset($error_amount) || isset($error_currency)) {
					$error_message = 'Failed. ';
					$error_message = (isset($error_amount))? $error_message.$error_amount : $error_message ;
					if (isset($error_currency)){
						$error_message = (isset($error_amount))? $error_message.' and '.$error_currency : $error_message.$error_currency ;
					}
					$error_message .= ' not check ';
				}

				$transaction_id = "AdminID: ".$Items[7]."; BankID: ".$Items[8];
			}else{
				$pending_message = "Pending. Transaction in process ";
			}
		} else {
			$error_message = "Failed. Amount not check ";
		}
	} else {
		$error_message = "Failed. Order not found ";
	}
?>