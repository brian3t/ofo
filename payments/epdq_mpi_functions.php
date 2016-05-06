<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  epdq_mpi_functions.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * ePDQ MPI (www.tele-pro.co.uk/epdq/) transaction handler by http://www.viart.com/
 */

	function epdq_build_xml($params)
	{

		$xml = '
<?xml version="1.0" encoding="UTF-8"?>
<EngineDocList>
	<DocVersion>1.0</DocVersion>
	<EngineDoc>
		<ContentType>OrderFormDoc</ContentType>
		<User>';

		$username = (isset($params["username"])) ? xml_escape_string($params["username"]) : "";
		$password = (isset($params["password"])) ? xml_escape_string($params["password"]) : "";
		$clientid = (isset($params["clientid"])) ? xml_escape_string($params["clientid"]) : "";
		$xml .= "<Name>" . $username . "</Name>\r\n";
		$xml .= "<Password>" . $password . "</Password>\r\n";
		$xml .= "<ClientId DataType=\"S32\">" . $clientid . "</ClientId>";
		$xml .= '
		</User>
		<Instructions>
			<Pipeline>PaymentNoFraud</Pipeline>
		</Instructions>';

		$xml .= "<OrderFormDoc>";
		$mode = (isset($params["mode"])) ? xml_escape_string($params["mode"]) : "";
		$xml .= "<Mode>" . $mode . "</Mode>\r\n";
		$xml .= "
			<Comments/>
			<Consumer>\r\n";
				if(isset($params["email"]) && strlen($params["email"])) {
					$xml .= "<Email>" . xml_escape_string($params["email"]) . "</Email>\r\n";
				} else {
					$xml .= "<Email/>\r\n";
				}
		$xml .= "
				<PaymentMech>
					<CreditCard>";
		$ccnumber = (isset($params["ccnumber"])) ? xml_escape_string($params["ccnumber"]) : "";
		$ccexpires = (isset($params["ccexpires"])) ? xml_escape_string($params["ccexpires"]) : "";
		$cvv2val = (isset($params["cvv2val"])) ? xml_escape_string($params["cvv2val"]) : "";
		$xml .= "<Number>" . $ccnumber . "</Number>\r\n";
		$xml .= "<Expires DataType=\"ExpirationDate\" Locale=\"840\">" . $ccexpires . "</Expires>\r\n";
		if(isset($params["cvv2val"]) && strlen($params["cvv2val"])) {
			$xml .= "<Cvv2Val>" . xml_escape_string($params["cvv2val"]) . "</Cvv2Val>\r\n";
			$xml .= "<Cvv2Indicator>1</Cvv2Indicator>\r\n";
		}
		if(isset($params["ccissuenum"]) && strlen($params["ccissuenum"])) {
			$xml .= "<IssueNum>" . xml_escape_string($params["ccissuenum"]) . "</IssueNum>\r\n";
		}
		if(isset($params["ccstartdate"]) && preg_match("/^\d{2}\/\d{2}$/", $params["ccstartdate"])) {
			$xml .= "<StartDate>" . xml_escape_string($params["ccstartdate"]) . "</StartDate>\r\n";
		}
		if(isset($params["cctype"]) && strlen($params["cctype"])) {
			$cc_type = xml_escape_string($params["cctype"]);
			$cc_types = array ("visa" => "1", "mc" => "2", "mastercard" => "2", "discover" => "3",
				"diners" => "4", "dinersclub" => "4", "carteblanche" => "5", "jcb" => "6", "enroute" => "7",
				"amex" => "8", "american express" => "8", "americanexpress" => "8",
				"solo" => "9", "switch" => "10", "electron" => "11"
			);
			if (isset($cc_types[strtolower($cc_type)])) {
				$cc_type = $cc_types[strtolower($cc_type)];
			}
			$xml .= "<Type>" . xml_escape_string($cc_type) . "</Type>\r\n";
		}

		$xml .= '
					</CreditCard>
				</PaymentMech>';

		$xml .= '
				<BillTo>
					<Location>';
		if(isset($params["billemail"]) && strlen($params["billemail"])) {
			$xml .= "<Email>" . xml_escape_string($params["billemail"]) . "</Email>\r\n";
		}
		if(isset($params["billtelfax"]) && strlen($params["billtelfax"])) {
			$billtelfax = preg_replace("/[^\d]/", "", $params["billtelfax"]);
			$xml .= "<TelFax>" . xml_escape_string($billtelfax) . "</TelFax>\r\n";
		}
		if(isset($params["billtelvoice"]) && strlen($params["billtelvoice"])) {
			$billtelvoice = preg_replace("/[^\d]/", "", $params["billtelvoice"]);
			$xml .= "<TelVoice>" . xml_escape_string($billtelvoice) . "</TelVoice>\r\n";
		}
		$xml .= '<Address>';
		if(isset($params["billname"]) && strlen($params["billname"])) {
			$xml .= "<Name>" . xml_escape_string($params["billname"]) . "</Name>\r\n";
		}
		if(isset($params["billfirstname"]) && strlen($params["billfirstname"])) {
			$xml .= "<FirstName>" . xml_escape_string($params["billfirstname"]) . "</FirstName>\r\n";
		}
		if(isset($params["billlastname"]) && strlen($params["billlastname"])) {
			$xml .= "<LastName>" . xml_escape_string($params["billlastname"]) . "</LastName>\r\n";
		}
		if(isset($params["billstreet1"]) && strlen($params["billstreet1"])) {
			$xml .= "<Street1>" . xml_escape_string($params["billstreet1"]) . "</Street1>\r\n";
		}
		if(isset($params["billstreet2"]) && strlen($params["billstreet2"])) {
			$xml .= "<Street2>" . xml_escape_string($params["billstreet2"]) . "</Street2>\r\n";
		}
		if(isset($params["billcity"]) && strlen($params["billcity"])) {
			$xml .= "<City>" . xml_escape_string($params["billcity"]) . "</City>\r\n";
		}
		if(isset($params["billstateprov"]) && strlen($params["billstateprov"])) {
			$xml .= "<StateProv>" . xml_escape_string($params["billstateprov"]) . "</StateProv>\r\n";
		}
		if(isset($params["billpostalcode"]) && strlen($params["billpostalcode"])) {
			$xml .= "<PostalCode>" . xml_escape_string($params["billpostalcode"]) . "</PostalCode>\r\n";
		}
		if(isset($params["billcountry"]) && strlen($params["billcountry"])) {
			$xml .= "<Country>" . xml_escape_string($params["billcountry"]) . "</Country>\r\n";
		}
		if(isset($params["billcompany"]) && strlen($params["billcompany"])) {
			$xml .= "<Company>" . xml_escape_string($params["billcompany"]) . "</Company>\r\n";
		}
		$xml .= '
						</Address>
					</Location>
				</BillTo>';

		$shipname = (isset($params["shipname"])) ? $params["shipname"] : "";
		$shipfirstname = (isset($params["shipfirstname"])) ? $params["shipfirstname"] : "";
		$shiplastname = (isset($params["shiplastname"])) ? $params["shiplastname"] : "";
		$shipstreet1 = (isset($params["shipstreet1"])) ? $params["shipstreet1"] : "";
		$shipstreet2 = (isset($params["shipstreet2"])) ? $params["shipstreet2"] : "";
		$shipcity = (isset($params["shipcity"])) ? $params["shipcity"] : "";
		$shipstateprov = (isset($params["shipstateprov"])) ? $params["shipstateprov"] : "";
		$shippostalcode = (isset($params["shippostalcode"])) ? $params["shippostalcode"] : "";
		$shipcountry = (isset($params["shipcountry"])) ? $params["shipcountry"] : "";
		$shipcompany = (isset($params["shipcompany"])) ? $params["shipcompany"] : "";
		if (strlen($shipname) || strlen($shipfirstname) || strlen($shiplastname) || strlen($shipstreet1) || strlen($shipstreet2) || strlen($shipcity) || strlen($shipstateprov) || strlen($shipcountry) || strlen($shippostalcode) || strlen($shipcompany)) {
			$xml .= '
				<ShipTo>
					<Location>';
			if(isset($params["shipemail"]) && strlen($params["shipemail"])) {
				$xml .= "<Email>" . xml_escape_string($params["shipemail"]) . "</Email>\r\n";
			}
			if(isset($params["shiptelfax"]) && strlen($params["shiptelfax"])) {
				$shiptelfax = preg_replace("/[^\d]/", "", $params["shiptelfax"]);
				$xml .= "<TelFax>" . xml_escape_string($shiptelfax) . "</TelFax>\r\n";
			}
			if(isset($params["shiptelvoice"]) && strlen($params["shiptelvoice"])) {
				$shiptelvoice = preg_replace("/[^\d]/", "", $params["shiptelvoice"]);
				$xml .= "<TelVoice>" . xml_escape_string($shiptelvoice) . "</TelVoice>\r\n";
			}
			$xml .= '<Address>';
			if(strlen($shipname)) {
				$xml .= "<Name>" . xml_escape_string($shipname) . "</Name>\r\n";
			}
			if(strlen($shipfirstname)) {
				$xml .= "<FirstName>" . xml_escape_string($shipfirstname) . "</FirstName>\r\n";
			}
			if(strlen($shiplastname)) {
				$xml .= "<LastName>" . xml_escape_string($shiplasttname) . "</LastName>\r\n";
			}
			if(strlen($shipstreet1)) {
				$xml .= "<Street1>" . xml_escape_string($shipstreet1) . "</Street1>\r\n";
			}
			if(strlen($shipstreet2)) {
				$xml .= "<Street2>" . xml_escape_string($shipstreet2) . "</Street2>\r\n";
			}
			if(strlen($shipcity)) {
				$xml .= "<City>" . xml_escape_string($shipcity) . "</City>\r\n";
			}
			if(strlen($shipstateprov)) {
				$xml .= "<StateProv>" . xml_escape_string($shipstateprov) . "</StateProv>\r\n";
			}
			if(strlen($shippostalcode)) {
				$xml .= "<PostalCode>" . xml_escape_string($shippostalcode) . "</PostalCode>\r\n";
			}
			if(strlen($shipcountry)) {
				$xml .= "<Country>" . xml_escape_string($shipcountry) . "</Country>\r\n";
			}
			if(strlen($shipcompany)) {
				$xml .= "<Company>" . xml_escape_string($shipcompany) . "</Company>\r\n";
			}
			$xml .= '
						</Address>
					</Location>
				</ShipTo>';
		}
		$xml .= '
			</Consumer>
			<Transaction>';

		$type = (isset($params["type"])) ? $params["type"] : "";
		$xml .= "<Type>" . xml_escape_string($type) . "</Type>\r\n";
		if (isset($params["ponumber"]) && strlen($params["ponumber"])) {
			$xml .= "<PoNumber>" . xml_escape_string($params["ponumber"]) . "</PoNumber>\r\n";
		}
		// authentication parameters
		if (isset($params["cardholderpresentcode"]) && strlen($params["cardholderpresentcode"])) {
			$xml .= "<CardholderPresentCode>" . xml_escape_string($params["cardholderpresentcode"]) . "</CardholderPresentCode>\r\n";
		}
		if (isset($params["payersecuritylevel"]) && strlen($params["payersecuritylevel"])) {
			$xml .= "<PayerSecurityLevel>" . xml_escape_string($params["payersecuritylevel"]) . "</PayerSecurityLevel>\r\n";
		}
		if (isset($params["payerauthenticationcode"]) && strlen($params["payerauthenticationcode"])) {
			$xml .= "<PayerAuthenticationCode>" . epdq_encode($params["payerauthenticationcode"]) . "</PayerAuthenticationCode>\r\n";
		}
		if (isset($params["payertxnid"]) && strlen($params["payertxnid"])) {
			$xml .= "<PayerTxnId>" . epdq_encode($params["payertxnid"]) . "</PayerTxnId>\r\n";
		}

		$xml .= '
				<CurrentTotals>
					<Totals>';
		$currency = (isset($params["currency"])) ? $params["currency"] : "";
		$ordertotal = (isset($params["ordertotal"])) ? $params["ordertotal"] : "";
		$xml .= "<Total DataType=\"Money\" Currency=\"" . xml_escape_string($currency) . "\">" . xml_escape_string($ordertotal) . "</Total>";
		$xml .= '
					</Totals>
				</CurrentTotals>
			</Transaction>
		</OrderFormDoc>
	</EngineDoc>
</EngineDocList>';

		return $xml;
	}

	function epdq_encode($value) 
	{
		$value = str_replace("+", "%2B", $value);
		return $value;
	}

	function epdq_void_xml($params)
	{
		$xml = '
<?xml version="1.0" encoding="UTF-8"?>
<EngineDocList>
	<DocVersion>1.0</DocVersion>
	<EngineDoc>
		<ContentType>OrderFormDoc</ContentType>
		<User>';

		$username = (isset($params["username"])) ? $params["username"] : "";
		$password = (isset($params["password"])) ? $params["password"] : "";
		$clientid = (isset($params["clientid"])) ? $params["clientid"] : "";
		$xml .= "<Name>" . xml_escape_string($username) . "</Name>\r\n";
		$xml .= "<Password>" . xml_escape_string($password) . "</Password>\r\n";
		$xml .= "<ClientId DataType=\"S32\">" . xml_escape_string($clientid) . "</ClientId>\r\n";
		$xml .= "</User>\r\n";

		$xml .= "<OrderFormDoc>";
		$xml .= "<Mode>P</Mode>\r\n";
		$transaction_id = (isset($params["transaction_id"])) ? $params["transaction_id"] : "";
		$xml .= "<Id>" . xml_escape_string($transaction_id) . "</Id>\r\n";
		$xml .= "<Comments/>\r\n";
		$xml .= "<Consumer>\r\n";
		if(isset($params["email"]) && strlen($params["email"])) {
			$xml .= "<Email>" . xml_escape_string($params["email"]) . "</Email>\r\n";
		} else {
			$xml .= "<Email/>\r\n";
		}
		$xml .= "</Consumer>\r\n";

		$xml .= "<Transaction>\r\n";
		$xml .= "<Type>Void</Type>\r\n";
		$xml .= '
			</Transaction>
		</OrderFormDoc>
	</EngineDoc>
</EngineDocList>';

		return $xml;
	}

	function epdq_credit_xml($params)
	{
		$xml = '
<?xml version="1.0" encoding="UTF-8"?>
<EngineDocList>
	<DocVersion>1.0</DocVersion>
	<EngineDoc>
		<ContentType>OrderFormDoc</ContentType>
		<User>';

		$username = (isset($params["username"])) ? $params["username"] : "";
		$password = (isset($params["password"])) ? $params["password"] : "";
		$clientid = (isset($params["clientid"])) ? $params["clientid"] : "";
		$xml .= "<Name>" . xml_escape_string($username) . "</Name>\r\n";
		$xml .= "<Password>" . xml_escape_string($password) . "</Password>\r\n";
		$xml .= "<ClientId DataType=\"S32\">" . xml_escape_string($clientid) . "</ClientId>\r\n";
		$xml .= "</User>";

		$xml .= "<OrderFormDoc>";
		$xml .= "<Mode>P</Mode>\r\n";
		$transaction_id = (isset($params["transaction_id"])) ? $params["transaction_id"] : "";
		$xml .= "<Id>" . xml_escape_string($transaction_id) . "</Id>\r\n";
		$xml .= "<Comments/>\r\n";
		$xml .= "<Consumer>\r\n";
		if(isset($params["email"]) && strlen($params["email"])) {
			$xml .= "<Email>" . xml_escape_string($params["email"]) . "</Email>\r\n";
		} else {
			$xml .= "<Email/>\r\n";
		}
		$xml .= "</Consumer>\r\n";

		$xml .= "<Transaction>\r\n";
		$xml .= "<Type>Credit</Type>\r\n";
		$xml .= '
			</Transaction>
		</OrderFormDoc>
	</EngineDoc>
</EngineDocList>';

		return $xml;
	}

?>