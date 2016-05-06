<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  user_order_payment.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/order_items.php");
	include_once("./includes/parameters.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","payment.html");

	$vc = get_param("vc");
	$order_id = get_param("order_id");

	$order_errors = check_order($order_id, $vc);
	if ($order_errors) {
		$t->set_var("errors_list", $order_errors);
		$t->parse("errors");
	} else {
		$sql  = " SELECT payment_id FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$payment_id = get_db_value($sql);
	
		set_session("session_vc", $vc);
		set_session("session_order_id", $order_id);
		set_session("session_user_order_id", $order_id);
		set_session("session_payment_id", $payment_id);

		header("Location: payment.php");
		exit;
	}
	$t->pparse("main");

?>
