<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  dankort_credit_card_info.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * DanDomain PayNet (http://www.dandomain.dk/) transaction handler by www.viart.com
 */

 	$is_admin_path = true;
	$root_folder_path = "../";

	if(isset($_GET['SessionID'])) {
		session_id($_GET['SessionID']);
	}elseif($HTTP_GET_VARS['SessionID']){
		session_id($HTTP_GET_VARS['SessionID']);
	}
	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."includes/parameters.php");
	include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");

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

	$t = new VA_Template('.'.$settings["templates_dir"]);
	$t->set_file("main","dankort_credit_card_info.html");
	
	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	if ($is_ssl) {
		$absolute_url = $secure_url;
	} else {
		$absolute_url = $site_url;
	}
	$css_file = "";
	if (strlen(get_setting_value($settings, "style_name", ""))) {
		$css_file  = $absolute_url;
		$css_file .= "styles/" . get_setting_value($settings, "style_name");
		if (strlen(get_setting_value($settings, "scheme_name", ""))) {
			$css_file .= "_" . get_setting_value($settings, "scheme_name");
		}
		$css_file .= ".css";
	}
	$t->set_var("order_total", $variables['order_total']);
//	$t->set_var("absolute_url", $absolute_url);
//	$t->set_var("css_file", $css_file);

	$payment_name = 'DanDomain PayNet';
	$goto_payment_message = str_replace("{payment_system}", $payment_name, GOTO_PAYMENT_MSG);
	$goto_payment_message = str_replace("{button_name}", CONTINUE_BUTTON, $goto_payment_message);
	$t->set_var("GOTO_PAYMENT_MSG", $goto_payment_message);
	$t->set_var("payment_url",$payment_parameters['Secure_Capture']);

	if (isset($payment_parameters['MerchantNumber'])){
		$t->set_var("parameter_name", 'MerchantNumber');
		$t->set_var("parameter_value", $payment_parameters['MerchantNumber']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['Amount'])){
		$payment_parameters['Amount'] = str_replace('.', ',', $payment_parameters['Amount']);
		$t->set_var("parameter_name", 'Amount');
		$t->set_var("parameter_value", $payment_parameters['Amount']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['CurrencyID'])){
		$t->set_var("parameter_name", 'CurrencyID');
		$t->set_var("parameter_value", $payment_parameters['CurrencyID']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['OrderID'])){
		$t->set_var("parameter_name", 'OrderID');
		$t->set_var("parameter_value", $payment_parameters['OrderID']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['OKURL'])){
		$t->set_var("parameter_name", 'OKURL');
		$t->set_var("parameter_value", $payment_parameters['OKURL']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['FAILURL'])){
		$t->set_var("parameter_name", 'FAILURL');
		$t->set_var("parameter_value", $payment_parameters['FAILURL']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['ReferenceText'])){
		$t->set_var("parameter_name", 'ReferenceText');
		$t->set_var("parameter_value", $payment_parameters['ReferenceText']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['PayType'])){
		$t->set_var("parameter_name", 'PayType');
		$t->set_var("parameter_value", $payment_parameters['PayType']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['InstantCapture'])){
		$t->set_var("parameter_name", 'InstantCapture');
		$t->set_var("parameter_value", $payment_parameters['InstantCapture']);
		$t->parse("parameters", true);
	}
	if (isset($payment_parameters['TestMode'])){
		$t->set_var("parameter_name", 'TestMode');
		$t->set_var("parameter_value", $payment_parameters['TestMode']);
		$t->parse("parameters", true);
	}

	$cc_expiry_months = array(
		array('01', JANUARY),
		array('02', FEBRUARY),
		array('03', MARCH),
		array('04', APRIL),
		array('05', MAY),
		array('06', JUNE),
		array('07', JULY),
		array('08', AUGUST),
		array('09', SEPTEMBER),
		array('10', OCTOBER),
		array('11', NOVEMBER),
		array('12', DECEMBER)
	);
	$cc_months = array_merge (array(array("", MONTH_MSG)), $cc_expiry_months);
	set_options($cc_months, "", "cc_expiry_month");

	$cc_expiry_years = get_db_values("SELECT expiry_year AS year_value, expiry_year AS year_description FROM " . $table_prefix . "cc_expiry_years", array(array("", YEAR_MSG)));
	if (sizeof($cc_expiry_years) < 2) {
		$current_date = va_time();
		$cc_expiry_years = array(array("", YEAR_MSG));
		for($y = 0; $y <= 7; $y++) {
			$cc_expiry_years[] = array($current_date[YEAR] + $y, $current_date[YEAR] + $y);
		}
	}
	for ($i = 1; $i < sizeof($cc_expiry_years); $i++){
		$cc_expiry_years[$i][0] = substr($cc_expiry_years[$i][0], 2, 2);
	}

	set_options($cc_expiry_years, "", "cc_expiry_year");

	$t->sparse("submit_payment", false);
	$t->pparse("main");
		
	exit;
?>