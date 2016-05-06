<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  posnet_functions.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Posnet functions by ViArt Limited - [www.viart.com]
 */


	function posnet_payment_request($pdata)
	{
		$xml  = "xmldata=";
		$xml .=		"<posnetRequest>";
		if (isset($pdata["mid"])) {
		$xml .=			"<mid>".xml_escape_string($pdata["mid"])."</mid>";
		}
		if (isset($pdata["tid"])) {
		$xml .=			"<tid>".xml_escape_string($pdata["tid"])."</tid>";
		}
		if (isset($pdata["username"])) {
		$xml .=			"<username>".xml_escape_string($pdata["username"])."</username>";
		}
		if (isset($pdata["password"])) {
		$xml .=			"<password>".xml_escape_string($pdata["password"])."</password>";
		}
		$xml .=			"<sale>";
		if (isset($pdata["ccno"])) {
		$xml .=				"<ccno>".xml_escape_string($pdata["ccno"])."</ccno>";
		}
		if (isset($pdata["cardexpmonth"]) && isset($pdata["cardexpyear"])) {
		$xml .=				"<expDate>".xml_escape_string($pdata["cardexpyear"].$pdata["cardexpmonth"])."</expDate>";
		}
		if (isset($pdata["cvc"])) {
		$xml .=				"<cvc>".xml_escape_string($pdata["cvc"])."</cvc>";
		}
		if (isset($pdata["amount"])) {
		$xml .=				"<amount>".intval($pdata["amount"]*100)."</amount>";
		}
		if (isset($pdata["currencycode"])) {
		$xml .=				"<currencyCode>".xml_escape_string($pdata["currencycode"])."</currencyCode>";
		}
		if (isset($pdata["orderid"])) {
			$orderid = strval($pdata["orderid"]);
			for ($i = strlen($orderid); $i < 24; $i++) {
				$orderid = '0'.$orderid;
			}
		$xml .=				"<orderID>".xml_escape_string($orderid)."</orderID>";
		}
		if (isset($pdata["installment"])) {
		$xml .=				"<installment>".xml_escape_string($pdata["installment"])."</installment>";
		}
		if (isset($pdata["extrapoint"])) {
		$xml .=				"<extraPoint>".xml_escape_string($pdata["extrapoint"])."</extraPoint>";
		}
		if (isset($pdata["multiplepoint"])) {
		$xml .=				"<multiplePoint>".xml_escape_string($pdata["multiplepoint"])."</multiplePoint>";
		}
		$xml .=			"</sale>";
		$xml .=		"</posnetRequest>";
		return $xml;
	}

?>