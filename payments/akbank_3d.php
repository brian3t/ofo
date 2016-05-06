<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  akbank_3d.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Akbank (www.est.com.tr) transaction handler by www.viart.com
 */

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");

	$order_errors = check_order($order_id, $vc);
	if ($order_errors) {
		echo $order_errors;
		exit;
	}

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if (isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}

	$params ='';
	foreach ($pass_data as $k => $v) {
		if(strlen($params)) { $params .= '&'; }
		$params .= $k . '=' . urlencode($v);
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $advanced_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 90);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_ENCODING, "x-www-form-urlencoded");
	set_curl_options($ch, $payment_parameters);

	$result=curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if (curl_errno($ch)) {
		$error_message = curl_errno($ch) . " - " . curl_error($ch);
	}
	curl_close($ch);
	
	$MD = "";
	$mdStatus = "";
	$mdErrorMsg = "";
	preg_match_all("/<input(.*)\>/Uis", $result, $input, PREG_SET_ORDER);

	foreach ($input as $k => $v) {
		if (preg_match('/name="mdStatus"/Uis', $v[1], $value)) {
			preg_match('/value="(.*)\"/Uis', $v[1], $value);
			$mdStatus = intval($value[1]);
		}
		if (preg_match('/name="MD"/Us', $v[1], $value)) {
			preg_match('/value="(.*)\"/Uis', $v[1], $value);
			$MD = $value[1];
		}
		if (preg_match('/name="mdErrorMsg"/Uis', $v[1], $value)) {
			preg_match('/value="(.*)\"/Uis', $v[1], $value);
			$mdErrorMsg = $value[1];
		}
	}

	if ($mdStatus==1 && strlen($MD)) {
		$transaction_id = $MD;
	} elseif (($mdStatus>=2 && $mdStatus<=9) || $mdStatus==0) {
		if (strlen($MD)) {
			$transaction_id = $MD;
		}
		if (!strlen($mdErrorMsg)) {$mdErrorMsg = 'Transaction failed! ';}
		$error_message = 'mdStatus='.$mdStatus.' '.$mdErrorMsg;
	} else {
		if (strlen($MD)) {
			$transaction_id = $MD;
		}
		$error_message = "Unknown status! ";
	}
?>