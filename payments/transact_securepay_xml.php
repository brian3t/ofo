<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  transact_securepay_xml.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Transact SecurePay (www.transact-gmbh.de) handler by ViArt Ltd (http://www.viart.com/)
 */

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");
	
	$order_errors = check_order($order_id, $vc);
	if ($order_errors){
		echo $order_errors;
		exit;
	} else {
		
		$post_parameters = ""; 
		$payment_params = array(); 
		$pass_parameters = array(); 
		$pass_data = array(); 
		$variables = array();
		get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
				
		$payment_parameters = $payment_params;
		
		$tid      = isset($payment_parameters["TID"]) ? $payment_parameters["TID"] : "93999999";
		$userid    = isset($payment_parameters["Userid"]) ? $payment_parameters["Userid"] : "123456789012345678901234567890";
		$password   = isset($payment_parameters["Password"]) ? $payment_parameters["Password"] : "transactdemotransactdemotransact";

		$transid = isset($payment_parameters["TransID"]) ? $payment_parameters["TransID"] : "";
		$amount = $variables["order_total"] * $variables["currency_rate"] * 100;
		$currency = $variables["currency_value"];
		$advanced_url = $payment_parameters["advenced_url"] ? $payment_parameters["advenced_url"] : "https://caesar-ips.transact-gmbh.de/xml";
		$function = $payment_parameters["function"];
		$validmonth = $payment_parameters["VALIDMONTH"];
		$validyear = $payment_parameters["VALIDYEAR"];
		$VERFALLDATUM = substr($validyear,2).$validmonth;
		$cvc = $payment_parameters["card_security_code"];
		$cc_name = $payment_parameters["card_name"];
		$cc_number = $payment_parameters["card_number"];
		$cc_type = $payment_parameters["card_type"];
		
		if ($function == 97){
			$xml  = "<COMMAND>\n";
			$xml .= "	<TERMINALID>".$tid."</TERMINALID>\n";
			$xml .= "	<USERLOGIN>".$userid."</USERLOGIN>\n";
			$xml .= "	<PASSWORD>".$password."</PASSWORD>\n";
			$xml .= "	<FUNKTION>".$function."</FUNKTION>\n";
			$xml .= "</COMMAND>\n";
		} else if ($function == 1 && $function == 2 && $function == 4 ){
			$xml  = "<COMMAND>\n";
			$xml .= "	<TERMINALID>".$tid."</TERMINALID>\n";
			$xml .= "	<USERLOGIN>".$userid."</USERLOGIN>\n";
			$xml .= "	<PASSWORD>".$password."</PASSWORD>\n";
			$xml .= "	<FUNKTION>".$function."</FUNKTION>\n";
			$xml .= "	<TRACE>".$TRACE."</TRACE>\n";
			$xml .= "	<BETRAG>".$amount."</BETRAG>\n";
			$xml .= "	<VALUTA>".$currency."</VALUTA>\n";
			$xml .= "</COMMAND>\n";
		} else if ($function == 3){
			$xml  = "<COMMAND>\n";
			$xml .= "	<TERMINALID>".$tid."</TERMINALID>\n";
			$xml .= "	<USERLOGIN>".$userid."</USERLOGIN>\n";
			$xml .= "	<PASSWORD>".$password."</PASSWORD>\n";
			$xml .= "	<FUNKTION>".$function."</FUNKTION>\n";
			$xml .= "	<BETRAG>".$amount."</BETRAG>\n";
			$xml .= "	<VALUTA>".$currency."</VALUTA>\n";
			$xml .= "	<PAN>".$cc_number."</PAN>\n";
			$xml .= "</COMMAND>\n";
		} else {
			$xml  = "<COMMAND>\n";
			$xml .= "	<TERMINALID>".$tid."</TERMINALID>\n";
			$xml .= "	<USERLOGIN>".$userid."</USERLOGIN>\n";
			$xml .= "	<PASSWORD>".$password."</PASSWORD>\n";
			$xml .= "	<FUNKTION>0</FUNKTION>\n";
			$xml .= "	<BETRAG>".$amount."</BETRAG>\n";
			$xml .= "	<VALUTA>".$currency."</VALUTA>\n";
			$xml .= "	<PAN>".$cc_number."</PAN>\n";
			$xml .= "	<VERFALLDATUM>".$VERFALLDATUM."</VERFALLDATUM>\n";
			$xml .= "	<CVC>".$cvc."</CVC>\n";
			$xml .= "</COMMAND>\n";
		}
		
		$ch = curl_init();

		$advanced_url = "https://caesar-ips.transact-gmbh.de/xml";
		$d[] = "Destination: CWXMLGate";
		if ($ch) {
			curl_setopt($ch, CURLOPT_URL, $advanced_url);
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			//curl_setopt($ch, CURLOPT_PROXY, "proxy:port");
			//curl_setopt($ch, CURLOPT_PROXYUSERPWD, "login:password");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $d);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "xml=" . $xml);
			curl_setopt($ch, CURLOPT_USERAGENT, "ViArt SHOP Cybersource payment module");
						
			$answer = curl_exec($ch);
			
			curl_close($ch);
		} else {
			$error_message = "Can't initialize cURL.";
		}
		$answer_words = array(
			"AID","BELEG-NR","BETRAG","CVC","DATUM","FEHLERCODE","FEHLERTEXT","FUNKTION","KARTENART","KARTENTYP","PAN",
			"PASSWORD","TERMINAL-ID","TRACE-NR","TRANSAKTION","UHRZEIT","USERLOGIN","VALUTA","VERFALL","VERSION","VU-NR",
			"ZEILE1","ZEILE2","ZEILE3","ZEILE4","ZEILE5");

		$err["0000"] = "Transaction successful";
		$err["0085"] = "Denial from credit card acquirer";
		$err["1004"] = "Internal connection error";
		$err["1101"] = "Referred transaction not found";
		$err["1103"] = "Transaction already reversed";
		$err["1106"] = "Card data defective";
		$err["1107"] = "Invalid card (expired)";
		$err["110B"] = "Unknown function";
		$err["110C"] = "Unknown card, please execute diagnostics";
		$err["110E"] = "Request  defective";
		$err["1113"] = "please execute diagnostics first";
		
		for ($i = 0; $i < count($answer_words); $i++){
			$answer_text[$answer_words[$i]] = get_text_xml($answer,$answer_words[$i]);
		}
		
		if ($answer_text["FEHLERCODE"] == "0000"){
		} else if ($answer_text["FEHLERCODE"] == "0085" || $answer_text["FEHLERCODE"] == "1004"|| $answer_text["FEHLERCODE"] == "1101" ||$answer_text["FEHLERCODE"] == "1103" || $answer_text["FEHLERCODE"] == "1106" || $answer_text["FEHLERCODE"] == "1107" || $answer_text["FEHLERCODE"] == "110B"|| $answer_text["FEHLERCODE"] == "110C" ||$answer_text["FEHLERCODE"] == "110E" || $answer_text["FEHLERCODE"] == "1113") {
			$error_message = $err[$answer_text["FEHLERCODE"]];
		} else if ($answer_text["FEHLERCODE"] == ""){
			$error_message = "Transaction failed";
		} else {
			$error_message = $answer_text["FEHLERTEXT"];
		}
		
		if (!strlen($answer_text["TRACE-NR"]) || !strlen($answer_text["BELEG-NR"]) || !strlen($answer_text["AID"]) || !strlen($answer_text["VU-NR"])){
			$error_message = $answer_text["FEHLERTEXT"];
		}
		
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET transaction_id=" . $db->tosql($answer_text["TRACE-NR"], TEXT) ;
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
	}
	
	function get_text_xml($text,$word){
		preg_match("/<".$word.">(.*)<\/".$word.">/i",$text,$match);
		if (isset($match[1])){
			return $match[1];
		} else {
			return "";
		}
	}

?>