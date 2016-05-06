<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  vat_check.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	function vat_check($vat, $ms = "")
	{
		global $vat_remote_exception_countries;
		
		$result = "";
		$valid = false;
		$connected = true;

		$chars_remove = array(" ", "-", ",", ".", "/", "\\");
		$vat = strtoupper(str_replace($chars_remove, "", $vat));

		if (preg_match("/^(AT|BE|BG|CY|CZ|DE|DK|EE|EL|ES|FI|FR|GB|HU|IE|IT|LT|LU|LV|MT|NL|PL|PT|RO|SE|SI|SK)(.*)/i", $vat, $matches))
		{
			$ms = strtoupper($matches[1]);
			$vat = $matches[2];
		}
		if (!strlen($ms)) {
			return false;
		}
	
		$url = "http://ec.europa.eu/taxation_customs/vies/viesquer.do";
		$parsed_url = parse_url($url);
		$server = $parsed_url["host"];
		$path = $parsed_url["path"];
	
		if (is_array($vat_remote_exception_countries) && in_array($ms, $vat_remote_exception_countries)) {
			$connected = false;
		} else {
			$fp = @fsockopen($server, 80, $errno, $errstr, 5);
			if (!$fp) {
				$connected = false;
			} else {
				$out  = "GET " . $path . "?ms=" . $ms . "&vat=" . $vat . "&iso=" . $ms . "&BtnSubmitVat=Verify HTTP/1.1\r\n";
				$out .= "Accept: text/xml\r\n";
				$out .= "Accept: charset=ISO-8859-1\r\n";
				$out .= "User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n";
				$out .= "Host: $server\r\n";
				$out .= "Connection: Close\r\n\r\n";
				
				fwrite($fp, $out);
				while (!feof($fp)) {
					$result .= fgets($fp, 4096);
				}
				fclose($fp);
			}
		
			if ($result) {
				//echo htmlspecialchars($result) . "<hr>";
				$result = str_replace("\r", "", $result);
				$result = str_replace("\n", "", $result);
				if (preg_match("/Yes, valid VAT number/i", $result)) {
					$valid = true;
				}
				else if (preg_match("/No, invalid VAT number/i", $result)) {
					$valid = false;
				}
				else {
					$connected = false;
				}
			}
			else {
				$connected = false;
			}
		}

		// Call VAT verification
		if (!$connected) {
			$vat_regexp = array(
				"AT" => array("/^U(\d{8})$/"),					//** Austria
				"BE" => array("/^(\d{9}\d?)$/"),				//** Belgium 
				"BG" => array("/^(\d{9,10})$/"),				//** Bulgaria 
				"CY" => array("/^(\d{8}[A-Z])$/"),				// Cyprus 
				"CZ" => array("/^(\d{8,10})(\d{3})?$/"),		//** Czech Republic
				"DE" => array("/^(\d{9})$/"),					//** Germany 
				"DK" => array("/^(\d{8})$/"),					//** Denmark 
				"EE" => array("/^(\d{9})$/"),					//** Estonia 
				"EL" => array("/^(\d{8,9})$/"),					//** Greece 
				"ES" => array("/^([A-Z]\d{8})$/",				//** Spain (1)
							"/^(\d{8}[A-Z])$/",					// Spain (2)
							"/^([A-Z]\d{7}[A-Z])$/"),			//** Spain (3)
				"FI" => array("/^(\d{8})$/"),					//** Finland 
				"FR" => array("/^(\d{11})$/",					//** France (1)
							"/^[(A-H)|(J-N)|(P-Z)]\d{10}$/",	// France (2)
							"/^\d[(A-H)|(J-N)|(P-Z)]\d{9}$/",	// France (3)
							"/^[(A-H)|(J-N)|(P-Z)]{2}\d{9}$/"),	// France (4)
				"GB" => array("/^(\d{9})$/",					//** UK (1)
							"/^(\d{9})\d{3}$/",					//** UK (2)
							"/^GD\d{3}$/",						//** UK (3)
							"/^HA\d{3}$/"),						//** UK (4)
				"HU" => array("/^(\d{8})$/"),					//** Hungary 
				"IE" => array("/^(\d{7}[A-W])$/",				//** Ireland (1)
							"/^([7-9][A-Z]\d{5}[A-W])$/"),		//** Ireland (2)
				"IT" => array("/^(\d{11})$/"),					//** Italy 
				"LT" => array("/^(\d{9}|\d{12})$/"),			//** Lithunia
				"LU" => array("/^(\d{8})$/"),					//** Luxembourg 
				"LV" => array("/^(\d{11})$/"),					//** Latvia 
				"MT" => array("/^(\d{8})$/"),					//** Malta
				"NL" => array("/^(\d{9})B\d{2}$/"),				//** Netherlands
				"PL" => array("/^(\d{10})$/"),					//** Poland
				"PT" => array("/^(\d{9})$/"),					//** Portugal
				"RO" => array("/^(\d{2,13})$/"),				// Romania
				"SE" => array("/^(\d{10}\d[1-4])$/"),			//** Sweden
				"SI" => array("/^(\d{8})$/"),					//** Slovenia
				"SK" => array("/^(\d{9}|\d{10})$/")				// Slovakia Republic
			);

			// Get vatnumber
			$vatnumber = "";
			if (isset($vat_regexp[$ms])) {
				foreach ($vat_regexp[$ms] as $re) {
					if (preg_match($re, $vat, $matches)) {
						$vatnumber = $matches[1];
						break;
			    	}
				}
			}
			else {
				// no RegExp set for this country code
				return false;
			}
			if (strlen($vatnumber) == 0) return false;

			switch ($ms) {
				// Checks the check digits of an Austrian VAT number.
				case "AT":
					$total = 0;
					$multipliers = array(1,2,1,2,1,2,1);
					$temp = 0;
  
					// Extract the next digit and multiply by the appropriate multiplier.  
					for ($i = 0; $i < 7; $i++) 
					{
						$temp = intval($vatnumber[$i]) * $multipliers[$i];
						if ($temp > 9)
							$total = $total + floor($temp / 10) + $temp % 10;
						else
							$total = $total + $temp;
					}  
				  
					// Establish check digit.
					$total = 10 - ($total + 4) % 10; 
					if ($total == 10) $total = 0;
				  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 7, 1)) 
				    	$valid = true;
					else 
				    	$valid = false;
					break;

				// Checks the check digits of a Belgium VAT number.
				case "BE":
					// Nine digit numbers have a 0 inserted at the front.
					if (strlen($vatnumber) == 9) $vatnumber = "0" . $vatnumber;
					  
					if (97 - substr($vatnumber, 0, 8) % 97 == substr($vatnumber, 8, 2)) 
						$valid = true;
					else 
						$valid = false;
					break;

				// Checks the check digits of a Bulgaria VAT number.
				case "BG":
					if (preg_match("/^[23]/", $vatnumber) && substr($vatnumber, 1, 2) <> "22"){
						$valid = false;
						break;
					}
					$total = 0;
					$multipliers = array(4,3,2,7,6,5,4,3,2);
					
					// nine character numbers should be prefixed with an 0.
					$vatnumber = str_pad($vatnumber, 10, "0", STR_PAD_LEFT);
					  
					// Extract the digit and multiply by the counter.
					for ($i = 0; $i < 9; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digit.
					$total = 11 - $total % 11;
					if ($total == 11) $total = 0;
					if ($total == 10){
						$valid = false;
						break;
					}
					  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 9, 1)) 
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Cyprus VAT number.
				case "CY":
					if (!preg_match("/^[013459]/", $vatnumber)){
						$valid = false;
						break;
					}
					$total = 0;
					$substitutes = array(1,0,5,7,9,13,15,17,19,21);

					// Substitute digits and sum them up.
					for ($i = 0; $i < 8; $i++)
					{
						if ($i % 2 == 0)
							$total = $total + $substitutes[intval($vatnumber[$i])];
						else
							$total = $total + intval($vatnumber[$i]);
					}

					// Now calculate the check digit itself.
					$total = $total % 26;
					$total = chr($total + 65);
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 8, 1))
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Czech Republic VAT number.
				case "CZ":
					$total = 0;
					$multipliers = array(8,7,6,5,4,3,2);
					  
					// Only do check digit validation for standard VAT numbers
					if (strlen($vatnumber) != 8) 
					{
						$valid = true;
					}
					else 
					{
						// Extract the next digit and multiply by the counter.
						for ($i = 0; $i < 7; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
						  
						// Establish check digit.
						$total = 11 - $total % 11;
						if ($total == 10) $total = 0; 
						if ($total == 11) $total = 1; 
						  
						// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
						if ($total == substr($vatnumber, 7, 1)) 
							$valid = true;
						else 
							$valid = false;
					}
    				break;

    			// Checks the check digits of a German VAT number.
				case "DE":
					$product = 10;
					$sum = 0;
					$checkdigit = 0;
					for ($i = 0; $i < 8; $i++) 
					{
						// Extract the next digit and implement perculiar algorithm!.
						$sum = (intval($vatnumber[$i]) + $product) % 10;
						if ($sum == 0) $sum = 10;
						$product = (2 * $sum) % 11;
					}
					  
					// Establish check digit.  
					if (11 - $product == 10)
						$checkdigit = 0;
					else 
						$checkdigit = 11 - $product;
					  
					// Compare it with the last two characters of the VAT number. If the same, then it is a valid check digit.
					if ($checkdigit == substr($vatnumber, 8, 1))
						$valid = true;
					else 
						$valid = false;
					break;

				// Checks the check digits of a Danish VAT number.
				case "DK":
					$total = 0;
					$multipliers = array(2,7,6,5,4,3,2,1);
					  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 8; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digit.
					$total = $total % 11;
					  
					// The remainder should be 0 for it to be valid..
					if ($total == 0) 
						$valid = true;
					else 
						$valid = false;
					break;

				// Checks the check digits of an Estonian VAT number.
				case "EE":
					$total = 0;
					$multipliers = array(3,7,1,3,7,1,3,7);
					  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 8; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digits using modulus 10.
					$total = 10 - $total % 10;
					if ($total == 10) $total = 0;
					  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 8, 1))
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Greek VAT number.
				case "EL":
					$total = 0;
					$multipliers = array(256,128,64,32,16,8,4,2);
					  
					//eight character numbers should be prefixed with an 0.
					if (strlen($vatnumber) == 8) $vatnumber = "0" + $vatnumber;
					  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 8; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digit.
					$total = $total % 11;
					if ($total > 9) $total = 0;  
					  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 8, 1)) 
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Spanish VAT number.
				case "ES":
					$total = 0; 
					$temp = 0;
					$multipliers = array(2,1,2,1,2,1,2);
					$esexp = array("/^[A-H]\d{8}$/", "/^[N|P|Q|S]\d{7}[A-Z]$/");
					  
					// With profit companies
					if (preg_match($esexp[0], $vatnumber)) 
					{  
						// Extract the next digit and multiply by the counter.
						for ($i = 0; $i < 7; $i++) 
						{
							$temp = intval($vatnumber[$i+1]) * $multipliers[$i];
							if ($temp > 9) 
								$total = $total + floor($temp / 10) + $temp % 10;
							else
								$total = $total + $temp;
						}
					    
						// Now calculate the check digit itself. 
						$total = 10 - $total % 10;
						if ($total == 10) $total = 0;
					    
						// Compare it with the last character of the VAT number. If it is the same, 
						// then it's a valid check digit.
						if ($total == substr($vatnumber, 8, 1)) 
							$valid = true;
						else
							$valid = false;
					}
					  
					// Non-profit companies
					elseif (preg_match($esexp[1], $vatnumber))
					{  
					    // Extract the next digit and multiply by the counter.
						for ($i = 0; $i < 7; $i++)
						{
							$temp = intval($vatnumber[$i+1]) * $multipliers[$i];
							if ($temp > 9) 
								$total = $total + floor($temp / 10) + $temp % 10;
							else
								$total = $total + $temp;
						}
					    
						// Now calculate the check digit itself.
						$total = 10 - $total % 10;
						$total = chr($total + 64);
					    
						// Compare it with the last character of the VAT number. If it is the same, 
						// then it's a valid check digit.
						if ($total == substr($vatnumber, 8, 1)) 
							$valid = true;
						else 
							$valid = false;
					}
					else 
					{ 
						$valid = true;
					}
					break;

				// Checks the check digits of a Finnish VAT number.
				case "FI":
					$total = 0;
					$multipliers = array(7,9,10,5,8,4,2);
					  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 7; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digit.
					$total = 11 - $total % 11;
					if ($total > 9) $total = 0;  
					  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 7, 1)) 
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a French VAT number.
				case "FR":
					if (!preg_match("/^\d{11}$/", $vatnumber))
					{
						$valid = true;
					}
					else 
					{
						// Extract the last nine digits as an integer.
						$total = substr($vatnumber, 2); 
						  
						// Establish check digit.
						$total = ($total * 100 + 12) % 97;
						  
						// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
						if ($total == substr($vatnumber, 0, 2)) 
							$valid = true;
						else
							$valid = false;
					}
					break;

				// Checks the check digits of a UK VAT number.
				case "GB":
					// Only inspect check digit of 9 character numbers
					if (strlen($vatnumber) != 9) 
					{
						$valid = true;
					}
					else 
					{
						$multipliers = array(8,7,6,5,4,3,2);
						$total = 0;
					    
						// Extract the next digit and multiply by the counter.
						for ($i = 0; $i < 7; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
						// Establish check digits by subtracting 97 from total until negative.
						//while ($total > 0) $total = $total - 97;
						$total = ($total % 97) - 97;
					  
						// Get the absolute value and compare it with the last two characters of the VAT number. 
						// If the same, then it is a valid check digit.
						$total = abs($total);
						if ($total == substr($vatnumber, 7, 2)) 
							$valid = true;
						else  
							$valid = false;
					}
					break;

				// Checks the check digits of a Hungarian VAT number.
				case "HU":
					$total = 0;
					$multipliers = array(9,7,3,1,9,7,3);
					  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 7; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digit.
					$total = 10 - $total % 10; 
					if ($total == 10) $total = 0;
					  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 7, 1)) 
						$valid = true;
					else 
						$valid = false;
					break;

				// Checks the check digits of an Irish VAT number.
				case "IE":
					$total = 0;
					$multipliers = array(8,7,6,5,4,3,2);
					  
					// If the code is in the old format, we need to convert it to the new.
					if (preg_match("/^\d[A-Z]/", $vatnumber)) {
						$vatnumber = "0" . substr($vatnumber, 2, 5) . substr($vatnumber, 0, 1) . substr($vatnumber, 7, 1);
					}
					
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 7; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digit using modulus 23, and translate to char. equivalent.
					$total = $total % 23;
					if ($total == 0)
						$total = "W";
					else
						$total = chr($total + 64);
					  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 7, 1)) 
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of an Italian VAT number.
				case "IT":
					$total = 0;
					$multipliers = array(1,2,1,2,1,2,1,2,1,2);
					$temp;
					    
					// The last three digits are the issuing office, and cannot exceed more 201
					$temp = intval(substr($vatnumber, 0, 7));
					if ($temp == 0) 
					{
						$valid = false;
					}
					else 
					{
						$temp = intval(substr($vatnumber, 7, 3));
						if (($temp < 1) || ($temp > 201)) 
						{
							$valid = false;
						}
						else
						{
							// Extract the next digit and multiply by the appropriate  
							for ($i = 0; $i < 10; $i++) 
							{
								$temp = intval($vatnumber[$i]) * $multipliers[$i];
								if ($temp > 9) 
									$total = $total + floor($temp / 10) + $temp % 10;
								else
									$total = $total + $temp;
							}
					
							// Establish check digit.
							$total = 10 - $total % 10;
							if ($total > 9) $total = 0;
					  
							// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
							if ($total == substr($vatnumber, 10, 1)) 
								$valid = true;
							else
								$valid = false;
						}
					}
					break;

				// Checks the check digits of a Lithuanian VAT number.
				case "LT":
					// Only do check digit validation for standard VAT numbers
					if (strlen($vatnumber) != 9)
					{
						$valid = true;
					}
					else
					{
						// Extract the next digit and multiply by the counter+1.
						$total = 0;
						for ($i = 0; $i < 8; $i++) $total = $total + intval($vatnumber[$i]) * ($i+1);
					  
						// Can have a double check digit calculation!
						if ($total % 11 == 10) 
						{
							$multipliers = array(3,4,5,6,7,8,9,1);
							$total = 0;
							for ($i = 0; $i < 8; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
						}
					  
						// Establish check digit.
						$total = $total % 11;
						if ($total == 10) $total = 0;
					  
					  	// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
						if ($total == substr($vatnumber, 8, 1)) 
							$valid = true;
						else
							$valid = false;
					}
					break;

				// Checks the check digits of a Luxembourg VAT number.
				case "LU":
					if (substr($vatnumber, 0, 6) % 89 == substr($vatnumber, 6, 2)) 
						$valid = true;
					else
						$valid =  false;
					break;

				// Checks the check digits of a Latvian VAT number.
				case "LV":
					// Only check the legal bodies
					if (preg_match("/^[0-3]/", $vatnumber))
					{
						$valid = true;
					}
					else
					{
						$total = 0;
						$multipliers = array(9,1,4,8,3,10,2,5,7,6);
					  
						// Extract the next digit and multiply by the counter.
						for ($i = 0; $i < 10; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
						// Establish check digits by getting modulus 11.
						if ($total % 11 == 4 && $vatnumber[0] == 9) $total = $total - 45;
						if ($total % 11 == 4)
							$total = 4 - $total % 11;
						elseif ($total % 11 > 4) 
							$total = 14 - $total % 11;
						elseif ($total % 11 < 4) 
							$total = 3 - $total % 11;
					  
						// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
						if ($total == substr($vatnumber, 10, 1)) 
							$valid = true;
						else
							$valid = false;
					}
					break;

				// Checks the check digits of a Maltese VAT number.
				case "MT":
					$total = 0;
					$multipliers = array(3,4,6,7,8,9);
					  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 6; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digits by getting modulus 37.
					$total = 37 - $total % 37;
					  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 6, 2))
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Dutch VAT number.
				case "NL":
					$total = 0;
					$multipliers = array(9,8,7,6,5,4,3,2);
					
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 8; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digits by getting modulus 11.
					$total = $total % 11;
					if ($total > 9) $total = 0;
					
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 8, 1))
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Polish VAT number.
				case "PL":
					$total = 0;
					$multipliers = array(6,5,7,2,3,4,5,6,7);
					  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 9; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digits subtracting modulus 11 from 11.
					$total = $total % 11;
					if ($total > 9) $total = 0;

					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 9, 1))
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Portugese VAT number.
				case "PT":
					$total = 0;
					$multipliers = array(9,8,7,6,5,4,3,2);
  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 8; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digits subtracting modulus 11 from 11.
					$total = 11 - $total % 11;
					if ($total > 9) $total = 0;
  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 8, 1))
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Romanian VAT number.
				case "RO":
					if (strlen($vatnumber) > 10 && strlen($vatnumber) <= 13) // Natural persons
					{
						if (preg_match("/^[12346]/", $vatnumber)) {
							$valid = false;
							break;
						}

						$year = intval(substr($vatnumber, 1, 2));
						$month = intval(substr($vatnumber, 3, 2));
						$day = intval(substr($vatnumber, 5, 2));

						if ($day < 1 || $day > 31) {
							$valid = false;
							break;
						}
						if ($month < 1 || $month > 12) {
							$valid = false;
							break;
						}
						if ($month == 2) // February
						{
							if ($year % 4 > 0 && $day > 28){
								$valid = false;
								break;
							} elseif ($year % 4 == 0 && $day > 29){
								$valid = false;
								break;
							}
						}
						elseif ($month == 4 || $month == 6 || $month == 9 || $month == 11)
						{
							if ($day > 30) {
								$valid = false;
								break;
							}
						}
						
						$total = 0;
						$multipliers = array(2,7,9,1,4,6,3,5,8,2,7,9);

						// fill vatnumber with 0 at the beginning.
						$vatnumber = str_pad($vatnumber, 13, "0", STR_PAD_LEFT);

						// Extract the digit and multiply by the counter.
						for ($i = 0; $i < 12; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];

						// Establish check digit.
						$total = $total % 11;
						if ($total == 10) $total = 1;
						  
						// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
						if ($total == substr($vatnumber, 12, 1))
							$valid = true;
						else
							$valid = false;
							
					} 
					elseif (strlen($vatnumber) >= 2 && strlen($vatnumber) <= 10) // Legal persons
					{
						$total = 0;
						$multipliers = array(7,5,3,2,1,7,5,3,2);
							
						// fill vatnumber with 0 at the beginning.
						$vatnumber = str_pad($vatnumber, 10, "0", STR_PAD_LEFT);

						// Extract the digit and multiply by the counter.
						for ($i = 0; $i < 9; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
						  
						// Establish check digit.
						$total = ($total * 10) % 11;
						if ($total == 10) $total = 0;
						  
						// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
						if ($total == substr($vatnumber, 9, 1))
							$valid = true;
						else
							$valid = false;
					}
					
					break;

				// Checks the check digits of a Swedish VAT number.
				case "SE":
					$total = 0;
					$multipliers = array(2,1,2,1,2,1,2,1,2);
					$temp = 0;
					  
					// Extract the next digit and multiply by the appropriate multiplier.
					for ($i = 0; $i < 9; $i++) 
					{
						$temp = intval($vatnumber[$i]) * $multipliers[$i];
						if ($temp > 9)
							$total = $total + floor($temp / 10) + $temp % 10;
						else
							$total = $total + $temp;
					}
					  
					// Establish check digits by subtracting mod 10 of total from 10.
					$total = 10 - ($total % 10); 
					if ($total == 10) $total = 0;
  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 9, 1))
						$valid = true;
					else
						$valid = false;
					break;

				// Checks the check digits of a Slovenian VAT number.
				case "SI":
					$total = 0; 
					$multipliers = array(8,7,6,5,4,3,2);
  
					// Extract the next digit and multiply by the counter.
					for ($i = 0; $i < 7; $i++) $total = $total + intval($vatnumber[$i]) * $multipliers[$i];
					  
					// Establish check digits by subtracting 97 from total until negative.
					$total = 11 - $total % 11;
					if ($total > 9) $total = 0;
  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 7, 1))
						$valid = true;
					else
						$valid = false;
					break;
					
				// Checks the check digits of a Slovak VAT number.
				case "SK":
					$total = 0; 
					$multipliers = array(8,7,6,5,4,3,2);
  
					// Extract the next digit and multiply by the counter.
					for ($i = 3; $i < 9; $i++) {
						$total = $total + intval($vatnumber[$i]) * $multipliers[$i-3];
					}
					  
					// Establish check digits by getting modulus 11.
					$total = 11 - $total % 11;
					if ($total > 9) $total = $total - 10;  
  
					// Compare it with the last character of the VAT number. If it is the same, then it's a valid check digit.
					if ($total == substr($vatnumber, 9, 1))
						$valid = true;
					else
						$valid = false;
					break;
			}

		}
		
		return $valid;
	}

?>