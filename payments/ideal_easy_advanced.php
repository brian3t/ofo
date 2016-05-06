<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ideal_easy_advanced.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$is_admin_path = true;
	$root_folder_path = "../";

	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path ."includes/parameters.php");

	
	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");
	
	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}	
	
	$post_parameters = ""; 
	$payment_params = array(); 
	$pass_parameters = array(); 
	$pass_data = array(); 
	$variables = array();
	get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
	
	$transaction_id = 1;
	$tv = date("Y") % 10;
	$transaction_id += $tv;
	$tv = (date("M") * 31) . time();
	$transaction_id .=  (($tv < 10) ? '0' : '') . (($tv < 100) ? '0' : '') . $tv;
	$tv = (date("h") * 3600) + (date("i") * 60) + date("s");
	$transaction_id .= (($tv < 10) ? '0' : '') . (($tv < 100) ? '0' : '') . (($tv < 1000) ? '0' : ''). (($tv < 10000) ? '0' : '') . $tv;
	$tvplus = round(rand() * 9);
	$transaction_id .= (($tvplus + 1) % 10);
	
	$sql  = " UPDATE " . $table_prefix . "orders SET transaction_id=" . $db->tosql($transaction_id, INTEGER);
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql);
	

	$get = "https://internetkassa.abnamro.nl/ncol/prod/orderstandard.asp";
	$get .= "?PSPID=" . $payment_params["PSPID"];
	$get .= "&PM=" . $payment_params["PM"];
	$get .= "&orderID=" . $transaction_id;
	$get .= "&CN=" . $pass_data["name"];
	$get .= "&COM=" . urlencode(substr($pass_data["description"], 0, 90));
	$get .= "&EMAIL=" . urlencode($pass_data["email"]);
		
	$get .= "&owneraddress=" . $pass_data["owneraddress"];
	$get .= "&ownertown=" . $pass_data["ownertown"];
	$get .= "&ownerzip=" . $pass_data["ownerzip"];
	$get .= "&ownercty=" . $pass_data["ownercty"];
	$get .= "&currency=" . $pass_data["currency"];
	$get .= "&amount=" . round($pass_data["order_total"] *100);
	$get .= "&language=" . $language_code . "_" . $language_code;
		
	header("Location: " . $get);
?>
