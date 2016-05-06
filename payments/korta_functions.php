<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  korta_functions.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	/*
	* KORTA in Iceland transaction handler by www.viart.com
	*/


	function get_korta_crypt($params)
	{

		if (isset($params["amount"]) && strlen($params["amount"])) {
			$amount = $params["amount"];
		} else {
			$amount = "0.00";
		}
		if (isset($params["currency"]) && strlen($params["currency"])) {
			$currency = $params["currency"];
		} else {
			$currency = "usd";
		}
		if (isset($params["merchant"]) && strlen($params["merchant"])) {
			$merchant = $params["merchant"];
		} else {
			$merchant = "";
		}
		if (isset($params["terminal"]) && strlen($params["terminal"])) {
			$terminal = $params["terminal"];
		} else {
			$terminal = "";
		}
		if (isset($params["description"]) && strlen($params["description"])) {
			$description = $params["description"];
		} else {
			$description = "";
		}
		if (isset($params["secretcode"]) && strlen($params["secretcode"])) {
			$secretcode = $params["secretcode"];
		} else {
			$secretcode = "";
		}

		$checkvaluemd5 = htmlentities($amount . $currency . $merchant . $terminal . htmlspecialchars($description) . $secretcode);
		if (isset($params["is_test"]) && $params["is_test"]) {
			$checkvaluemd5 .= "TEST";
		}
		$checkvaluemd5 = md5($checkvaluemd5);

		return $checkvaluemd5;
	}


    function korta_set_error($order_id, $error_message){
    	global $db, $table_prefix;
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET error_message=" . $db->tosql($error_message, TEXT) ;
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
		$db->query($sql);
	}

?>
