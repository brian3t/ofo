<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  hsbc_cpi.php                                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * The Cardholder Payment Interface (CPI) within HSBC Secure ePayments (http://www.hsbc.com/) 
 * transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";

	include_once ($root_folder_path . "payments/hsbc_cpi_functions.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/shopping_cart.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "includes/parameters.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	    
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

	if (isset($payment_params["URL"])) {
		$payment_url = $payment_params["URL"];
	} else {
		$payment_url = "https://www.cpi.hsbc.com/servlet";
	}


	$post_fields = createFields($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, $storefront_id , $cpi_hash_key);	
	$post_fields["OrderHash"] = getHash($cpi_hash_key, $post_fields, $payment_params);
	
	$post_fields_inline = "";
	foreach ($post_fields AS $key => $value) {
		if (trim($value)){
			$post_fields_inline .= "\n<input type='hidden' NAME='" . $key . "' value='" . trim($value) . "' />";
		}
	}
?>
	<html>		
	<body onload="document.getElementById('form1').submit();">
		<form action="<?php echo $payment_url; ?>" method="POST" name="form1" id="form1">
			<?php echo $post_fields_inline; ?>
			<button class="HSBC" type="submit" name="submit1" value="submit">
				<img src="https://www.cpi.hsbc.com/images/logo.gif">
			</button>
		</form>		
	</body>
	</html>