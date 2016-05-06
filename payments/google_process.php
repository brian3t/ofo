<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  google_process.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * Google Checkout (https://checkout.google.com/) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";

	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."includes/parameters.php");
	include_once ($root_folder_path ."includes/date_functions.php");
    include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path ."payments/google_functions.php");

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");

	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}
	
	$payment_parameters = array();
	$pass_parameters = array();
	$post_parameters = '';
	$pass_data = array();
	$variables = array();
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables);

	$xml  = '<?xml version="1.0" encoding="utf-8"?>'; //<?
	$xml .= '<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">';
	$xml .= '	<shopping-cart>';
	$xml .= '		<items>';
	foreach ($variables["items"] as $number => $item) {
		$xml .= '			<item>';
		$xml .= '				<item-name>'.xml_escape_string($payment_parameters['item-name'].' '.$item['item_name']).'</item-name>';
		$xml .= '				<item-description>'.xml_escape_string($item['item_name']).'</item-description>';
		$xml .= '				<unit-price currency="'.xml_escape_string($payment_parameters['currency']).'">'.xml_escape_string($item['price_excl_tax']).'</unit-price>';
		$xml .= '				<quantity>'.xml_escape_string($item['quantity']).'</quantity>';
		$xml .= '				<tax-table-selector>item_'.xml_escape_string($number).'</tax-table-selector>';
		$xml .= '			</item>';
	}

	foreach ($variables["properties"] as $number => $property) {
		$xml .= '			<item>';
		$xml .= '				<item-name>'.xml_escape_string($payment_parameters['item-name'].' '.$property['property_name']).'</item-name>';
		$xml .= '				<item-description>'.xml_escape_string($property['property_name']).'</item-description>';
		$xml .= '				<unit-price currency="'.xml_escape_string($payment_parameters['currency']).'">'.xml_escape_string($property['property_price_excl_tax']).'</unit-price>';
		$xml .= '				<quantity>1</quantity>';
		$xml .= '				<tax-table-selector>property_'.xml_escape_string($number).'</tax-table-selector>';
		$xml .= '			</item>';
	}

	if ($variables["total_discount"] != 0) {
		$xml .= '			<item>';
		$xml .= '				<item-name>'.xml_escape_string(TOTAL_DISCOUNT_MSG).'</item-name>';
		$xml .= '				<item-description>'.xml_escape_string(TOTAL_DISCOUNT_MSG).'</item-description>';
		$xml .= '				<unit-price currency="'.xml_escape_string($payment_parameters['currency']).'">-'.xml_escape_string($variables["total_discount_incl_tax"]).'</unit-price>';
		$xml .= '				<quantity>1</quantity>';
		$xml .= '				<tax-table-selector>discount</tax-table-selector>';
		$xml .= '			</item>';
	}
	if ($variables["processing_fee"] != 0) {
		$xml .= '			<item>';
		$xml .= '				<item-name>'.xml_escape_string(PROCESSING_FEE_MSG).'</item-name>';
		$xml .= '				<item-description>'.xml_escape_string(PROCESSING_FEE_MSG).'</item-description>';
		$xml .= '				<unit-price currency="'.xml_escape_string($payment_parameters['currency']).'">'.xml_escape_string($variables["processing_fee"]).'</unit-price>';
		$xml .= '				<quantity>1</quantity>';
		$xml .= '				<tax-table-selector>processing</tax-table-selector>';
		$xml .= '			</item>';
	}
	$xml .= '		</items>';
	$xml .= '		<merchant-private-data>';
	$xml .= '			<merchant-note>'.xml_escape_string($order_id).'</merchant-note>';
	$xml .= '		</merchant-private-data>';
	$xml .= '	</shopping-cart>';
	$xml .= '	<checkout-flow-support>';
	$xml .= '		<merchant-checkout-flow-support>';
	if ($variables["shipping_type_desc"]) {
		$xml .= '			<shipping-methods>';
		$xml .= '				<flat-rate-shipping name="'.xml_escape_string($variables["shipping_type_desc"]).'">';
		$xml .= '					<price currency="'.xml_escape_string($payment_parameters['currency']).'">'.xml_escape_string($variables["shipping_cost_excl_tax"]).'</price>';
		$xml .= '				</flat-rate-shipping>';
		$xml .= '			</shipping-methods>';
	}
	$xml .= '			<tax-tables>';
	if ($variables["shipping_type_desc"]){
		if ($variables["shipping_taxable"] !=0) {
			$xml .= '				<default-tax-table>';
			$xml .= '					<tax-rules>';
			$xml .= '						<default-tax-rule>';
			$xml .= '							<shipping-taxed>true</shipping-taxed>';
			$xml .= '							<rate>'.xml_escape_string(number_format($variables["tax_percent"]/100, 10)).'</rate>';
			$xml .= '							<tax-area>';
			$xml .= '								<world-area/>';
			$xml .= '							</tax-area>';
			$xml .= '						</default-tax-rule>';
			$xml .= '					</tax-rules>';
			$xml .= '				</default-tax-table>';
		}else{
			$xml .= '				<default-tax-table>';
			$xml .= '					<tax-rules>';
			$xml .= '						<default-tax-rule>';
			$xml .= '							<shipping-taxed>false</shipping-taxed>';
			$xml .= '							<rate>0</rate>';
			$xml .= '							<tax-area>';
			$xml .= '								<world-area/>';
			$xml .= '							</tax-area>';
			$xml .= '						</default-tax-rule>';
			$xml .= '					</tax-rules>';
			$xml .= '				</default-tax-table>';
		}
	}
	$xml .= '				<alternate-tax-tables>';
	foreach ($variables["items"] as $number => $item) {
		if ($item['tax_free'] == 0) {
			if($item['price_incl_tax'] != round($item['price_excl_tax']*(1 + $item['tax_percent']/100), 2) ){
				$item_tax_percent = ($item['price_incl_tax']/$item['price_excl_tax'] - 1)*100;
			}else{
				$item_tax_percent = $item['tax_percent'];
			}
			$xml .= '					<alternate-tax-table standalone="true" name="item_'.xml_escape_string($number).'">';
			$xml .= '						<alternate-tax-rules>';
			$xml .= '							<alternate-tax-rule>';
			$xml .= '								<rate>'.xml_escape_string(number_format($item_tax_percent/100, 4)).'</rate>';
			$xml .= '								<tax-area>';
			$xml .= '									<world-area/>';
			$xml .= '								</tax-area>';
			$xml .= '							</alternate-tax-rule>';
			$xml .= '						</alternate-tax-rules>';
			$xml .= '					</alternate-tax-table>';
		}else{
			$xml .= '					<alternate-tax-table standalone="false" name="item_'.xml_escape_string($number).'">';
			$xml .= '						<alternate-tax-rules>';
			$xml .= '							<alternate-tax-rule>';
			$xml .= '								<rate>0</rate>';
			$xml .= '								<tax-area>';
			$xml .= '									<world-area/>';
			$xml .= '								</tax-area>';
			$xml .= '							</alternate-tax-rule>';
			$xml .= '						</alternate-tax-rules>';
			$xml .= '					</alternate-tax-table>';
		}
	}
	foreach ($variables["properties"] as $number => $property) {
		if ($property['tax_free'] == 0) {
			if($property['property_price_incl_tax'] != round($property['property_price_excl_tax']*(1 + $property['property_tax_percent']/100), 2) ){
				$property_tax_percent = ($property['property_price_incl_tax']/$property['property_price_excl_tax'] - 1)*100;
			}else{
				$property_tax_percent = $property['property_tax_percent'];
			}
			$xml .= '					<alternate-tax-table standalone="true" name="property_'.xml_escape_string($number).'">';
			$xml .= '						<alternate-tax-rules>';
			$xml .= '							<alternate-tax-rule>';
			$xml .= '								<rate>'.xml_escape_string(number_format($property_tax_percent/100, 4)).'</rate>';
			$xml .= '								<tax-area>';
			$xml .= '									<world-area/>';
			$xml .= '								</tax-area>';
			$xml .= '							</alternate-tax-rule>';
			$xml .= '						</alternate-tax-rules>';
			$xml .= '					</alternate-tax-table>';
		}else{
			$xml .= '					<alternate-tax-table standalone="false" name="property_'.xml_escape_string($number).'">';
			$xml .= '						<alternate-tax-rules>';
			$xml .= '							<alternate-tax-rule>';
			$xml .= '								<rate>0</rate>';
			$xml .= '								<tax-area>';
			$xml .= '									<world-area/>';
			$xml .= '								</tax-area>';
			$xml .= '							</alternate-tax-rule>';
			$xml .= '						</alternate-tax-rules>';
			$xml .= '					</alternate-tax-table>';
		}
	}
	if ($variables["total_discount"] != 0) {
			$xml .= '					<alternate-tax-table standalone="false" name="discount">';
			$xml .= '						<alternate-tax-rules>';
			$xml .= '							<alternate-tax-rule>';
			$xml .= '								<rate>0</rate>';
			$xml .= '								<tax-area>';
			$xml .= '									<world-area/>';
			$xml .= '								</tax-area>';
			$xml .= '							</alternate-tax-rule>';
			$xml .= '						</alternate-tax-rules>';
			$xml .= '					</alternate-tax-table>';
	}
	if ($variables["processing_fee"] != 0) {
			$xml .= '					<alternate-tax-table standalone="false" name="processing">';
			$xml .= '						<alternate-tax-rules>';
			$xml .= '							<alternate-tax-rule>';
			$xml .= '								<rate>0</rate>';
			$xml .= '								<tax-area>';
			$xml .= '									<world-area/>';
			$xml .= '								</tax-area>';
			$xml .= '							</alternate-tax-rule>';
			$xml .= '						</alternate-tax-rules>';
			$xml .= '					</alternate-tax-table>';
	}
	$xml .= '				</alternate-tax-tables>';
	$xml .= '			</tax-tables>';
	$xml .= '			<rounding-policy>';
	$xml .= '				<mode>HALF_UP</mode>';
	$xml .= '				<rule>PER_LINE</rule>';
	$xml .= '			</rounding-policy>';
	$xml .= '		</merchant-checkout-flow-support>';
	$xml .= '	</checkout-flow-support>';
	$xml .= '</checkout-shopping-cart>';

	$cart = base64_encode($xml);
	$signature = base64_encode(g_c_CalcHmacSha1($xml,$payment_parameters['merchant_key']));

	$t = new VA_Template('.'.$settings["templates_dir"]);
	$t->set_file("main","payment.html");
	$payment_name = 'Google Checkout';
	$goto_payment_message = str_replace("{payment_system}", $payment_name, GOTO_PAYMENT_MSG);
	$goto_payment_message = str_replace("{button_name}", CONTINUE_BUTTON, $goto_payment_message);
	$t->set_var("GOTO_PAYMENT_MSG", $goto_payment_message);
	$t->set_var("payment_url",$payment_parameters['action_url']);
	$t->set_var("submit_method", "post");
	$t->set_var("parameter_name", "cart");
	$t->set_var("parameter_value", $cart);
	$t->parse("parameters", true);
	$t->set_var("parameter_name", "signature");
	$t->set_var("parameter_value", $signature);
	$t->parse("parameters", true);
	$t->sparse("submit_payment", false);
	$t->pparse("main");
		
	exit;
?>