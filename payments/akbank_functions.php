<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  akbank_functions.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Akbank functions by ViArt Ltd (www.viart.com)
 */

	function akbank_cc5_request($pdata)
	{
		$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-9\"?>"; //<?
		$xml .= "<CC5Request>";

		// Common fields
		if (isset($pdata["name"]))
			$xml .= "<Name>" . xml_escape_string($pdata["name"]) . "</Name>";
		if (isset($pdata["password"]))
			$xml .= "<Password>" . xml_escape_string($pdata["password"]) . "</Password>";
		if (isset($pdata["clientid"]))
			$xml .= "<ClientId>" . xml_escape_string($pdata["clientid"]) . "</ClientId>";
		$remote_address = get_ip();
		if (strlen($remote_address))
			$xml .= "<IPAddress>" . $remote_address . "</IPAddress>";
		if (isset($pdata["email"]))
			$xml .= "<Email>" . xml_escape_string($pdata["email"]) . "</Email>";
		if (isset($pdata["mode"]))
			$xml .= "<Mode>" . xml_escape_string($pdata["mode"]) . "</Mode>";
		if (isset($pdata["orderid"]))
			$xml .= "<OrderId>" . xml_escape_string($pdata["orderid"]) . "</OrderId>";
		if (isset($pdata["groupid"]))
			$xml .= "<GroupId>" . xml_escape_string($pdata["groupid"]) . "</GroupId>";
		if (isset($pdata["userid"]))
			$xml .= "<UserId>" . xml_escape_string($pdata["userid"]) . "</UserId>";
		if (isset($pdata["type"]))
			$xml .= "<Type>" . xml_escape_string($pdata["type"]) . "</Type>";
		if (isset($pdata["number"]))
			$xml .= "<Number>" . xml_escape_string($pdata["number"]) . "</Number>";
		if (isset($pdata["expires"]))
			$xml .= "<Expires>" . xml_escape_string(str_replace(" ", "", $pdata["expires"])) . "</Expires>";
		if (isset($pdata["cvv2val"]))
			$xml .= "<Cvv2Val>" . xml_escape_string($pdata["cvv2val"]) . "</Cvv2Val>";
		if (isset($pdata["total"]))
			$xml .= "<Total>" . xml_escape_string($pdata["total"]) . "</Total>";
		if (isset($pdata["currency"]))
			$xml .= "<Currency>" . xml_escape_string($pdata["currency"]) . "</Currency>";
		if (isset($pdata["taksit"]))
			$xml .= "<Taksit>" . xml_escape_string($pdata["taksit"]) . "</Taksit>";

		// BillTo node
		$xml .= "<BillTo>";
		if (isset($pdata["billtoname"]))
			$xml .= "<Name>" . xml_escape_string($pdata["billtoname"]) . "</Name>";
		if (isset($pdata["billtostreet1"]))
			$xml .= "<Street1>" . xml_escape_string($pdata["billtostreet1"]) . "</Street1>";
		if (isset($pdata["billtostreet2"]))
			$xml .= "<Street2>" . xml_escape_string($pdata["billtostreet2"]) . "</Street2>";
		if (isset($pdata["billtostreet3"]))
			$xml .= "<Street3>" . xml_escape_string($pdata["billtostreet3"]) . "</Street3>";
		if (isset($pdata["billtocity"]))
			$xml .= "<City>" . xml_escape_string($pdata["billtocity"]) . "</City>";
		if (isset($pdata["billtostateprov"]))
			$xml .= "<StateProv>" . xml_escape_string($pdata["billtostateprov"]) . "</StateProv>";
		if (isset($pdata["billtopostalcode"]))
			$xml .= "<PostalCode>" . xml_escape_string($pdata["billtopostalcode"]) . "</PostalCode>";
		if (isset($pdata["billtocountry"]))
			$xml .= "<Country>" . xml_escape_string($pdata["billtocountry"]) . "</Country>";
		if (isset($pdata["billtocompany"]))
			$xml .= "<Company>" . xml_escape_string($pdata["billtocompany"]) . "</Company>";
		if (isset($pdata["billtotelvoice"]))
			$xml .= "<TelVoice>" . xml_escape_string($pdata["billtotelvoice"]) . "</TelVoice>";
		$xml .= "</BillTo>";

		// ShipTo node
		$xml .= "<ShipTo>";
		if (isset($pdata["shiptoname"]))
			$xml .= "<Name>" . xml_escape_string($pdata["shiptoname"]) . "</Name>";
		if (isset($pdata["shiptostreet1"]))
			$xml .= "<Street1>" . xml_escape_string($pdata["shiptostreet1"]) . "</Street1>";
		if (isset($pdata["shiptostreet2"]))
			$xml .= "<Street2>" . xml_escape_string($pdata["shiptostreet2"]) . "</Street2>";
		if (isset($pdata["shiptostreet3"]))
			$xml .= "<Street3>" . xml_escape_string($pdata["shiptostreet3"]) . "</Street3>";
		if (isset($pdata["shiptocity"]))
			$xml .= "<City>" . xml_escape_string($pdata["shiptocity"]) . "</City>";
		if (isset($pdata["shiptostateprov"]))
			$xml .= "<StateProv>" . xml_escape_string($pdata["shiptostateprov"]) . "</StateProv>";
		if (isset($pdata["shiptopostalcode"]))
			$xml .= "<PostalCode>" . xml_escape_string($pdata["shiptopostalcode"]) . "</PostalCode>";
		if (isset($pdata["shiptocountry"]))
			$xml .= "<Country>" . xml_escape_string($pdata["shiptocountry"]) . "</Country>";
		$xml .= "</ShipTo>";

		// Extra info
		$xml .= "<Extra></Extra>";
		$xml .= "</CC5Request>";

		return $xml;
	}

?>