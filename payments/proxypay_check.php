<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  proxypay_check.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * ProxyPay (www.clear2pay.com) transaction handler by http://www.viart.com/
 */

	$sql  = " SELECT success_message FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$success_message = get_db_value($sql);

	if (!strlen($success_message)) {
		$pending_message = "There are no answer from payment gateway. This order will be reviewed manually.";
	}

?>