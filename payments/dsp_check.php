<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  dsp_check.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

        $vc = get_session("session_vc");
        $order_id = get_session("session_order_id");

        $order_errors = check_order($order_id, $vc);
        if($order_errors) {
                echo $order_errors;
                exit;
        }

        $payment_parameter = array();
        $sql  = " SELECT parameter_name, parameter_source, parameter_type FROM " . $table_prefix . "payment_parameters p, " . $table_prefix . "orders o";
        $sql .= " WHERE p.payment_id=o.payment_id AND o.order_id=" . $db->tosql($order_id, INTEGER);
        $db->query($sql);
        while($db->next_record()) {
                $payment_parameter[$db->f("parameter_name")] = str_replace( "{site_url}", $settings["site_url"], $db->f("parameter_source"));
                if ( ($db->f("parameter_name")=="Amount") && ($db->f("parameter_type")== "CONSTANT") ){
                $order_total=$payment_parameter["Amount"];
                }
        }

        $SECURE_SECRET = $payment_parameter["Secure_Hash_Secret"];

        $vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
        unset($_GET["vpc_SecureHash"]);

        $errorExists = false;

        if (strlen($SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7" && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {

                $md5HashData = $SECURE_SECRET;

                foreach($_GET as $key => $value) {
                        if ($key != "vpc_SecureHash" or strlen($value) > 0) {
                                $md5HashData .= $value;
                        }
                }

                if (strtoupper($vpc_Txn_Secure_Hash) != strtoupper(md5($md5HashData))) {
                        $error_message = "INVALID HASH";
                        $errorExists = true;
                }
        } else {
                $error_message = "Not Calculated - No 'SECURE_SECRET' present.";
        }

        $title           = get_param("Title");

        $amount          = get_param("vpc_Amount");
        $locale          = get_param("vpc_Locale");
        $batchNo         = get_param("vpc_BatchNo");
        $command         = get_param("vpc_Command");
        $message         = get_param("vpc_Message");
        $version         = get_param("vpc_Version");
        $cardType        = get_param("vpc_Card");
        $orderInfo       = get_param("vpc_OrderInfo");
        $receiptNo       = get_param("vpc_ReceiptNo");
        $merchantID      = get_param("vpc_Merchant");
        $authorizeID     = get_param("vpc_AuthorizeId");
        $merchTxnRef     = get_param("vpc_MerchTxnRef");
        $transactionNo   = get_param("vpc_TransactionNo");
        $acqResponseCode = get_param("vpc_AcqResponseCode");
        $txnResponseCode = get_param("vpc_TxnResponseCode");

        $verType         = get_param("vpc_VerType");
        $verStatus       = get_param("vpc_VerStatus");
        $token           = get_param("vpc_VerToken");
        $verSecurLevel   = get_param("vpc_VerSecurityLevel");
        $enrolled        = get_param("vpc_3DSenrolled");
        $xid             = get_param("vpc_3DSXID");
        $acqECI          = get_param("vpc_3DSECI");
        $authStatus      = get_param("vpc_3DSstatus");

        if ($txnResponseCode != "0" && $verStatus != "Y") {
                if (!strlen($message)){
                        $error_message = "Your transaction has been declined.";
                } else {
                        $error_message = $message;
                }
        }

function getResponseDescription($responseCode) {

    switch ($responseCode) {
        case "0" : $result = "Transaction Successful"; break;
        case "?" : $result = "Transaction status is unknown"; break;
        case "1" : $result = "Unknown Error"; break;
        case "2" : $result = "Bank Declined Transaction"; break;
        case "3" : $result = "No Reply from Bank"; break;
        case "4" : $result = "Expired Card"; break;
        case "5" : $result = "Insufficient funds"; break;
        case "6" : $result = "Error Communicating with Bank"; break;
        case "7" : $result = "Payment Server System Error"; break;
        case "8" : $result = "Transaction Type Not Supported"; break;
        case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
        case "A" : $result = "Transaction Aborted"; break;
        case "C" : $result = "Transaction Cancelled"; break;
        case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
        case "F" : $result = "3D Secure Authentication failed"; break;
        case "I" : $result = "Card Security Code verification failed"; break;
        case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
        case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
        case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
        case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
        case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
        case "T" : $result = "Address Verification Failed"; break;
        case "U" : $result = "Card Security Code Failed"; break;
        case "V" : $result = "Address Verification and Card Security Code Failed"; break;
        default  : $result = "Unable to be determined";
    }
    return $result;
}


function getStatusDescription($statusResponse) {
    if ($statusResponse == "") {
        $result = "3DS not supported or there was no 3DS data provided";
    } else {
        switch ($statusResponse) {
            Case "Y"  : $result = "The cardholder was successfully authenticated."; break;
            Case "E"  : $result = "The cardholder is not enrolled."; break;
            Case "N"  : $result = "The cardholder was not verified."; break;
            Case "U"  : $result = "The cardholder's Issuer was unable to authenticate due to some system error at the Issuer."; break;
            Case "F"  : $result = "There was an error in the format of the request from the merchant."; break;
            Case "A"  : $result = "Authentication of your Merchant ID and Password to the ACS Directory Failed."; break;
            Case "D"  : $result = "Error communicating with the Directory Server."; break;
            Case "C"  : $result = "The card type is not supported for authentication."; break;
            Case "S"  : $result = "The signature on the response received from the Issuer could not be validated."; break;
            Case "P"  : $result = "Error parsing input from Issuer."; break;
            Case "I"  : $result = "Internal Payment Server system error."; break;
            default   : $result = "Unable to be determined"; break;
        }
    }
    return $result;
}

?>