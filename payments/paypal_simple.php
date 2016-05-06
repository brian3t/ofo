<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_simple.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * Website Payments Standard(www.paypal.com) transaction handler by http://www.viart.com/
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/date_functions.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");

	$eol = get_eol();

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");

	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}

	// get payment data
	$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables);
	
	$is_sandbox = isset($payment_parameters["sandbox"])? $payment_parameters["sandbox"] : 0;
	$is_at = isset($payment_parameters["at"])? $payment_parameters["at"] : 0;
	unset($payment_parameters["sandbox"]);
	unset($payment_parameters["at"]);
	
	$payment_url = "https://www.paypal.com/cgi-bin/webscr";
	$get_fields_inline = "upload=1";
	foreach ($payment_parameters AS $name => $value) {
		$get_fields_inline  .= "&" . $name . "=" . urlencode($value);
	}
	
	$items_text = show_order_items($order_id, false);
	$paypal_total = 0;	
	if ( $items_text_array && is_array($items_text_array) && count($items_text_array)) {
		$item_count = 0;
		foreach ($items_text_array AS $item) {
			$item_count++;
			$get_fields_inline  .= "&item_name_" . $item_count . "=" . urlencode($item["item_name"] . " " . $item["item_properties_text"]);
			$get_fields_inline  .= "&amount_" . $item_count . "=" . round($item["item_price_with_tax"], 2);
			$get_fields_inline  .= "&quantity_" . $item_count . "=" . $item["quantity"];
			$paypal_total +=  round($item["item_price_with_tax"], 2) * $item["quantity"];
		}
	}
	
	if ($variables["shipping_cost"]) {
		$item_count++;
		$shipping_cost     = $variables["shipping_cost"];
		$tax_percent       = $variables["tax_percent"];
		$shipping_taxable  = $variables["shipping_taxable"];
		$tax_prices_type   = $variables["tax_prices_type"];
		$shipping_tax_free = (!$shipping_taxable);
		
		$shipping_taxable_value = ($shipping_taxable == 1) ? $shipping_cost : 0;
		$shipping_tax_percent = $tax_percent;
		$shipping_tax = get_tax_amount("", 0, $shipping_cost, $shipping_tax_free, $shipping_tax_percent);
		if ($tax_prices_type == 1) {
			$shipping_cost_excl_tax = $shipping_cost - $shipping_tax;
			$shipping_cost_incl_tax = $shipping_cost;
		} else {
			$shipping_cost_excl_tax = $shipping_cost;
			$shipping_cost_incl_tax = $shipping_cost + $shipping_tax;
		}
		
		$paypal_total += $shipping_cost_incl_tax;
	        $paypal_minus = $paypal_total - $variables["order_total"];
	        if ($paypal_minus) {
	            $shipping_cost_incl_tax -= $paypal_minus;
	        }
		

		$get_fields_inline  .= "&item_name_" . $item_count . "=" . urlencode($variables["shipping_type_desc"]);
		$get_fields_inline  .= "&amount_" . $item_count . "=" . $shipping_cost_incl_tax;
	}
	
	 header("Location: ". $payment_url . "?" . $get_fields_inline);
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo CHECKOUT_PAYMENT_TITLE; ?></title></head>
<meta http-equiv="Content-Type" content="text/html; charset={CHARSET}">
<body class="commonbg" onload="document.getElementById('form1').submit();">	
		<form action="<?php echo $payment_url; ?>" method="POST" name="form1" id="form1">
			<input type="hidden" name="upload" value="1">
			<?php echo $post_fields_inline; ?>
			<div align="center" style="font-family: tahoma, arial, sans-serif; color: navy; ">
			Now you will be redirected to Paypal. If nothing happens please click 'Continue' button to proceed<br><br>
			<input type="submit" value="Continue" style="border: 1px solid gray; background-color: #e0e0e0; font-family: tahoma, arial, sans-serif; height: 20px; color: #333333; font-weight: bold;">
			</div>
		</form>		
	</body>
	</html>