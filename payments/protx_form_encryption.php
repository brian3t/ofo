<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  protx_form_encryption.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Protx (www.protx.com) transaction handler by www.viart.com
 */

	function simple_xor ($InString, $Key)
	{
		// Initialise key array
		$KeyList = array();
		// Initialise out variable
		$output = "";

		// Convert $Key into array of ASCII values
		for($i = 0; $i < strlen($Key); $i++){
			$KeyList[$i] = ord(substr($Key, $i, 1));
		}

		// Step through string a character at a time
		for($i = 0; $i < strlen($InString); $i++) {
			// Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
			// % is MOD (modulus), ^ is XOR
			$output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
		}

		// Return the result
		return $output;
	}

	function get_protx_crypt($params)
	{
		$crypt_url = "";
		$EncryptionPassword = $params["EncryptionPassword"];

		//** Build the crypt string plaintext **
		$crypt_url .= "VendorTxCode=" . $params["VendorTxCode"];
		$crypt_url .= "&Amount=" . $params["Amount"];
		$crypt_url .= "&Currency=" . $params["Currency"];
		$crypt_url .= "&Description=" . $params["Description"];
		$crypt_url .= "&SuccessURL=" . $params["SuccessURL"];
		$crypt_url .= "&FailureURL=" . $params["FailureURL"];

		if (isset($params["CustomerEmail"]) && strlen($params["CustomerEmail"])) {
			$crypt_url .= "&CustomerEmail=" . $params["CustomerEmail"];
		}
		if (isset($params["VendorEmail"]) && strlen($params["VendorEmail"])) {
			$crypt_url .= "&VendorEmail=" . $params["VendorEmail"];
		}
		if (isset($params["CustomerName"]) && strlen($params["CustomerName"])) {
			$crypt_url .= "&CustomerName=" . $params["CustomerName"];
		}
		if (isset($params["DeliveryAddress"]) && strlen(trim($params["DeliveryAddress"]))) {
			$crypt_url .= "&DeliveryAddress=" . $params["DeliveryAddress"];
		}
		if (isset($params["DeliveryPostCode"]) && strlen($params["DeliveryPostCode"])) {
			$crypt_url .= "&DeliveryPostCode=" . $params["DeliveryPostCode"];
		}
		if (isset($params["BillingAddress"]) && strlen(trim($params["BillingAddress"]))) {
			$crypt_url .= "&BillingAddress=" . $params["BillingAddress"];
		}
		if (isset($params["BillingPostCode"]) && strlen($params["BillingPostCode"])) {
			$crypt_url .= "&BillingPostCode=" . $params["BillingPostCode"];
		}
		// new 2.22 fields
		if (isset($params["ContactNumber"]) && strlen($params["ContactNumber"])) {
			$crypt_url .= "&ContactNumber=" . $params["ContactNumber"];
		}
		if (isset($params["ContactFax"]) && strlen($params["ContactFax"])) {
			$crypt_url .= "&ContactFax=" . $params["ContactFax"];
		}
		if (isset($params["AllowGiftAid"]) && strlen($params["AllowGiftAid"])) {
			$crypt_url .= "&AllowGiftAid=" . $params["AllowGiftAid"];
		}
		if (isset($params["ApplyAVSCV2"]) && strlen($params["ApplyAVSCV2"])) {
			$crypt_url .= "&ApplyAVSCV2=" . $params["ApplyAVSCV2"];
		}
		if (isset($params["Apply3DSecure"]) && strlen($params["Apply3DSecure"])) {
			$crypt_url .= "&Apply3DSecure=" . $params["Apply3DSecure"];
		}
		if (isset($params["Basket"]) && strlen($params["Basket"])) {
			$crypt_url .= "&Basket=" . $params["Basket"];
		}
		if (isset($params["EMailMessage"]) && strlen($params["EMailMessage"])) {
			$crypt_url .= "&EMailMessage=" . $params["EMailMessage"];
		}

		$crypt = base64_encode(simple_xor($crypt_url, $EncryptionPassword));

		return $crypt;
	}

?>