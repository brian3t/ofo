<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ogone_direct.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * oGone (http://ogone.com) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	define("INSTALLED", true);
	include_once($root_folder_path . "includes/common.php");	
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path . "payments/ogone_functions.php");
	
	$order_id = get_param("orderID");
	
	if ($order_id) {	
		$sql  = " SELECT success_message, error_message FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$success_message = $db->f("success_message");
			$error_message = $db->f("error_message");
			
			$post_parameters = ""; 
			$payment_params = array(); 
			$pass_parameters = array(); 
			$pass_data = array(); 
			$variables = array();
			get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
			
			checkOrder();
			$t = new VA_Template($settings["templates_dir"]);
					
			if ($order_id && $variables) {
				$success_status_id = $variables["success_status_id"];
				$pending_status_id = $variables["pending_status_id"];
				$failure_status_id = $variables["failure_status_id"];	
				if (strlen($error_message)) {
					update_order_status($order_id, $failure_status_id , true, "", $error_message);
				} elseif (strlen($success_message)) {
					update_order_status($order_id, $success_status_id, true, "", "");
				} else {
					update_order_status($order_id, $pending_status_id , true, "", "");
				}
			}
		}
	}
?>