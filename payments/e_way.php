<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  e_way.php                                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * eWay (www.eway.com.au) transaction handler by http://www.viart.com/
 */
	$post_xml  = '<ewaygateway>';
	$post_xml .= ' <ewayCustomerID>'.(isset($payment_parameters['ewayCustomerID'])? xml_escape_string($payment_parameters['ewayCustomerID']):'').'</ewayCustomerID>';
	$post_xml .= ' <ewayTotalAmount>'.(isset($payment_parameters['ewayTotalAmount'])? xml_escape_string($payment_parameters['ewayTotalAmount']):'').'</ewayTotalAmount>';
	$post_xml .= ' <ewayCustomerFirstName>'.(isset($payment_parameters['ewayCustomerFirstName'])? xml_escape_string($payment_parameters['ewayCustomerFirstName']):'').'</ewayCustomerFirstName>';
	$post_xml .= ' <ewayCustomerLastName>'.(isset($payment_parameters['ewayCustomerLastName'])? xml_escape_string($payment_parameters['ewayCustomerLastName']):'').'</ewayCustomerLastName>';
	$post_xml .= ' <ewayCustomerEmail>'.(isset($payment_parameters['ewayCustomerEmail'])? xml_escape_string($payment_parameters['ewayCustomerEmail']):'').'</ewayCustomerEmail>';
	$post_xml .= ' <ewayCustomerAddress>'.(isset($payment_parameters['ewayCustomerAddress'])? xml_escape_string($payment_parameters['ewayCustomerAddress']):'').'</ewayCustomerAddress>';
	$post_xml .= ' <ewayCustomerPostcode>'.(isset($payment_parameters['ewayCustomerPostcode'])? xml_escape_string($payment_parameters['ewayCustomerPostcode']):'').'</ewayCustomerPostcode>';
	$post_xml .= ' <ewayCustomerInvoiceDescription>'.(isset($payment_parameters['ewayCustomerInvoiceDescription'])? xml_escape_string($payment_parameters['ewayCustomerInvoiceDescription']):'').'</ewayCustomerInvoiceDescription>';
	$post_xml .= ' <ewayCustomerInvoiceRef>'.(isset($payment_parameters['ewayCustomerInvoiceRef'])? xml_escape_string($payment_parameters['ewayCustomerInvoiceRef']):'').'</ewayCustomerInvoiceRef>';
	$post_xml .= ' <ewayCardHoldersName>'.(isset($payment_parameters['ewayCardHoldersName'])? xml_escape_string($payment_parameters['ewayCardHoldersName']):'').'</ewayCardHoldersName>';
	$post_xml .= ' <ewayCardNumber>'.(isset($payment_parameters['ewayCardNumber'])? xml_escape_string($payment_parameters['ewayCardNumber']):'').'</ewayCardNumber>';
	$post_xml .= ' <ewayCardExpiryMonth>'.(isset($payment_parameters['ewayCardExpiryMonth'])? xml_escape_string($payment_parameters['ewayCardExpiryMonth']):'').'</ewayCardExpiryMonth>';
	$post_xml .= ' <ewayCardExpiryYear>'.(isset($payment_parameters['ewayCardExpiryYear'])? xml_escape_string($payment_parameters['ewayCardExpiryYear']):'').'</ewayCardExpiryYear>';
	$post_xml .= ' <ewayTrxnNumber>'.(isset($payment_parameters['ewayTrxnNumber'])? xml_escape_string($payment_parameters['ewayTrxnNumber']):'').'</ewayTrxnNumber>';
	$post_xml .= ' <ewayCVN>'.(isset($payment_parameters['ewayCVN'])? xml_escape_string($payment_parameters['ewayCVN']):'').'</ewayCVN>';
	$post_xml .= ' <ewayOption1>'.(isset($payment_parameters['ewayOption1'])? xml_escape_string($payment_parameters['ewayOption1']):'').'</ewayOption1>';
	$post_xml .= ' <ewayOption2>'.(isset($payment_parameters['ewayOption2'])? xml_escape_string($payment_parameters['ewayOption2']):'').'</ewayOption2>';
	$post_xml .= ' <ewayOption3>'.(isset($payment_parameters['ewayOption3'])? xml_escape_string($payment_parameters['ewayOption3']):'').'</ewayOption3>';
	$post_xml .= '</ewaygateway>';

	$error_message = "";
	$transaction_id = "";

	$ch = curl_init();
	if ($ch){
		curl_setopt ($ch, CURLOPT_URL, $advanced_url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_xml);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_TIMEOUT,30);
		set_curl_options ($ch, $payment_parameters);
			
		$payment_response = curl_exec ($ch);
		if (curl_errno($ch)){
			$error_message .= curl_errno($ch)." - ".curl_error($ch);
			return;
		}
		curl_close($ch);
		
		if(strlen($payment_response)){
			$matches = array();
			if (preg_match('/\<ewayResponse\>(.*)\<\/ewayResponse\>/Uis', $payment_response, $matches)){
				$matches = array();
				if (preg_match('/\<ewayTrxnNumber\>(.*)\<\/ewayTrxnNumber\>/Uis', $payment_response, $matches)){
					$transaction_id = $matches[1];
				}
				$matches = array();
				if (preg_match('/\<ewayTrxnStatus\>(.*)\<\/ewayTrxnStatus\>/Uis', $payment_response, $matches)){
					$ewayTrxnStatus = $matches[1];
					if(strtoupper($ewayTrxnStatus) != 'TRUE'){
						$matches = array();
						if (preg_match('/\<ewayTrxnError\>(.*)\<\/ewayTrxnError\>/Uis', $payment_response, $matches)){
							$error_message .= $matches[1];
						}
						if(!strlen($error_message)){
							$error_message .= "Your transaction was declined!";
						}
					}
				}
				if(!strlen($error_message) && !strlen($transaction_id)){
					$error_message .= "Invalid response.";
				}
			}else{
				$error_message  = "Can't obtain transaction information from eWay.";
			}
		}else{
			$error_message .= "Can't obtain data for your transaction.";
		}
	}else{
		$error_message .= "Can't initialize cURL.";
	}

?>