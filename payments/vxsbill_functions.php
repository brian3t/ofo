<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  vxsbill_functions.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * VXSBill functions by ViArt Ltd - www.viart.com
 */

	function vxsbill_payment_request($pdata)
	{
		global $table_prefix, $db;

		$request_string = "";
		foreach ($pdata as $key => $value) {
			if ($key == "price") {
				$value = str_replace(".", "", $value);
			} elseif ($key == "country_code") {
				$value = get_db_value("SELECT country_code FROM " . $table_prefix . "countries WHERE country_name = " . $db->tosql($value, TEXT));
			}
			if (strlen($request_string)) $request_string .= "&";
			$request_string .= $key . "=" . urlencode($value);
		}

		$remote_address = get_ip();
		if (strlen($remote_address)) {
			$request_string .= "&ip=" . urlencode($remote_address);
		}

		$request_string = "?" . $request_string;
		return $request_string;
	}

?>