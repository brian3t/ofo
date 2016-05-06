<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  echo_process.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * ECHO (www.echo-inc.com) transaction handler by ViArt Ltd. (www.viart.com)
 */
	$echo = array();
	$echo['order_type']       = "S";
	$echo['transaction_type'] = "EV";
	
	$echo['merchant_echo_id'] = $payment_parameters['merchant_echo_id'];
	$echo['merchant_pin']     = $payment_parameters['merchant_pin'];	
	
	$max_counter                = $payment_parameters['counter'];
	$echo['billing_ip_address'] = $_SERVER['REMOTE_ADDR'];
    
	$test_mode                  = isset($payment_parameters['test_mode'])&&$payment_parameters['test_mode'];
	
	if ($test_mode) {
		$echo['grand_total']      = "1.00";
		$echo['cc_number']        = "4005 5500 0000 0019";
		$echo['ccexp_month']      = 12;
		$echo['ccexp_year']       = 2004; 
		$echo['cnp_security']     = 3333;
		$echo['counter']          = time();	
		if($payment_parameters['order_number']) {
			$echo['order_number']         = $payment_parameters['order_number'];
		}	
	} else {
		$echo['grand_total']      = ($payment_parameters["grand_total"]) ? $payment_parameters["grand_total"] : "0.00";
		$echo['cc_number']        = $payment_parameters['cc_number'];
		$echo['ccexp_month']      = $payment_parameters['ccexp_month'];
		$echo['ccexp_year']       = $payment_parameters['ccexp_year'];
		$echo['cnp_security']     = $payment_parameters['cnp_security'];
		
		$where   = array();	
		$where[] = ' order_total=' . $db->tosql($echo['grand_total'], INTEGER);
		if($payment_parameters['order_number']) {
			$echo['order_number']         = $payment_parameters['order_number'];
		}
		if ($payment_parameters['sales_tax'])
			$echo['sales_tax']            = $payment_parameters['sales_tax'];
		if ($payment_parameters['billing_first_name'])
			$echo['billing_first_name']   = $payment_parameters['billing_first_name'];
		if ($payment_parameters['billing_last_name'])
			$echo['billing_last_name']    = $payment_parameters['billing_last_name'];
		if ($payment_parameters['billing_company_name'])
	   		$echo['billing_company_name'] = $payment_parameters['billing_company_name'];
	   	if ($payment_parameters['billing_address1'])
	    		$echo['billing_address1']     = $payment_parameters['billing_address1'];
		if ($payment_parameters['billing_address2'])
			$echo['billing_address2']     = $payment_parameters['billing_address2'];
		if ($payment_parameters['billing_city'])
			$echo['billing_city']         = $payment_parameters['billing_city'];
		if ($payment_parameters['billing_country'])
			$echo['billing_country']      = $payment_parameters['billing_country'];
		if ($payment_parameters['billing_zip'])
			$echo['billing_zip']          = $payment_parameters['billing_zip'];
		if ($payment_parameters['billing_state'])
	    		$echo['billing_state']        = $payment_parameters['billing_state'];
		if ($payment_parameters['billing_email'])
			$echo['billing_email']        = $payment_parameters['billing_email'];
		if ($payment_parameters['billing_phone'])
			$echo['billing_phone']        = $payment_parameters['billing_phone'];
		if (isset($payment_parameters['product_description']))
			$echo['product_description']      = $payment_parameters['product_description'];
		
		if($max_counter>1) {	
			$sql  = "SELECT COUNT(order_id) FROM " . $table_prefix . "orders ";	
			$sql .= ' WHERE order_placed_date>"' . date('Y-m-d H:i:s',time()-8*60*60) . '" ';
			if ($where) {
				$sql    .= ' AND ' . implode(' AND ',$where);	
			}	
		    $counter = get_db_value($sql);
		    if ($counter) {
			    if ($counter<$max_counter) {
			    	$echo['counter'] = $counter+1;
			    }
		    } else {
		    	$echo['counter'] = 1;
		    }
		} else {
			$echo['counter'] = 1;
		}
	}
	
	$tmp  = array();
	foreach ($echo AS $key=>$value){
		$tmp[] = "$key=$value";
	}
	$data = implode('&',$tmp);
	$ch = @curl_init();
	if ($ch) {
		curl_setopt ($ch, CURLOPT_URL, $advanced_url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);	
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_POST, $data);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt ($ch, CURLOPT_USERAGENT, "ViArt SHOP ECHO payment module");
		set_curl_options($ch, $payment_parameters);

		$payment_response = curl_exec($ch);
		curl_close($ch);
		
		if ($payment_response) {
			$startpos = strpos($payment_response, "<ECHOTYPE1>") + 11;
			$endpos = strpos($payment_response, "</ECHOTYPE1>");
			$echotype1 = substr($payment_response, $startpos, $endpos - $startpos);
	
			$startpos = strpos($payment_response, "<ECHOTYPE2>") + 11;
			$endpos = strpos($payment_response, "</ECHOTYPE2>");
			$echotype2 = substr($payment_response, $startpos, $endpos - $startpos);
	
			$startpos = strpos($payment_response, "<ECHOTYPE3>") + 11;
			$endpos = strpos($payment_response, "</ECHOTYPE3>");
			$echotype3 = substr($payment_response, $startpos, $endpos - $startpos);	
			
			if ($echotype2=="INVALID TERM ID     1013") { 
				echo $echotype2;
				exit;
			}
					
			$startpos = strpos($echotype3 , "<status>") + 8;
			$endpos = strpos($echotype3 , "</status>");
			$status = substr($echotype3, $startpos, $endpos - $startpos);
			if ($status !== "G") {
				$error_message = strip_tags($payment_response);
			}	
		} else {
			$error_message = "Bad response from gateway: " . $payment_response;
		}
	} else {
		$error_message .= "Can't initialize cURL.";
	}

?>