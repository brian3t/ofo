<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  vcs_check.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * VCS (https://www.vcs.co.za) transaction handler by http://www.viart.com/
 */

	$p1 = get_param("p1");
	$p2 = get_param("p2");
	$p3 = get_param("p3");
	$p4 = get_param("p4");
	$p5 = get_param("p5");
	$p6 = get_param("p6");
	$p7 = get_param("p7");
	$p8 = get_param("p8");
	$p9 = get_param("p9");
	$p10 = get_param("p10");
	$pam = get_param("pam");

	$error_message = '';
	$pending_message = '';
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
	$pOrder_ID = str_replace('{order_id}',$order_id,$parametrs['p2']);

	$transaction_id = $p2;
	if (strlen($p2) && $p2==$pOrder_ID){
		if ($parametrs['pam']!=$pam) {
			$error_message .= "Failed. PAM incorrect! ";
		}
		if ($p6!=$order_total) {
			$error_message .= "Failed. Amount incorrect! ";
		}
		if(preg_match('/APPROVED/Uis', $p3, $value)){
		}else{
			$error_message .= $p3;
		}
	} else {
		$error_message = "Failed. Order not found ";
	}

?>