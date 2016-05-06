<?php

	$is_admin_path = true;
	include_once ("../includes/common.php");
	include_once ("../includes/record.php");

	$operation = get_param("operation");
	$country_code = get_param("CountryCode");

	$r = new VA_Record("");

	// RequestHeader
	$r->add_hidden("shipping_module_id", INTEGER);
	$r->add_textbox("FedExURL", TEXT);
	$r->change_property("FedExURL", REQUIRED, true);
	$r->add_textbox("CustomerTransactionIdentifier", TEXT);
	$r->add_textbox("AccountNumber", TEXT, "Account Number");
	$r->change_property("AccountNumber", REQUIRED, true);
	$r->add_textbox("MeterNumber", TEXT, "Meter Number");

	// Contact
	$r->add_textbox("PersonName", TEXT, "Person Name");
	$r->change_property("PersonName", REQUIRED, true);
	$r->add_textbox("CompanyName", TEXT);
	$r->add_textbox("Department", TEXT);
	$r->add_textbox("PhoneNumber", TEXT, "Phone Number");
	$r->change_property("PhoneNumber", REQUIRED, true);
	$r->add_textbox("PagerNumber", TEXT);
	$r->add_textbox("FaxNumber", TEXT);
	$r->add_textbox("EMailAddress", TEXT);

	// Address
	$r->add_textbox("Line1", TEXT);
	$r->change_property("Line1", REQUIRED, true);
	$r->add_textbox("Line2", TEXT);
	$r->add_textbox("City", TEXT);
	$r->change_property("City", REQUIRED, true);
	$r->add_textbox("StateOrProvinceCode", TEXT, "State Or Province Code");
	if (strtoupper($country_code) == "US" || strtoupper($country_code) == "CA") {
		$r->change_property("StateOrProvinceCode", REQUIRED, true);
	}
	$r->add_textbox("PostalCode", TEXT);
	$r->change_property("PostalCode", REQUIRED, true);
	$r->add_textbox("CountryCode", TEXT);
	$r->change_property("CountryCode", REQUIRED, true);

	if(strlen($operation)) {

		$r->get_form_parameters();
		$r->validate();

		if(!strlen($r->errors)) {

			$shipping_module_id = $r->get_value("shipping_module_id");
			$fedex_url = $r->get_value("FedExURL");
	  
			foreach($r->parameters as $key => $value) {
				$module_params[$key] = $value[CONTROL_VALUE];
			}
	  
			$xml = prepare_subscription_request($module_params);
	  
			$ch = curl_init();
    
			curl_setopt ($ch, CURLOPT_URL, $fedex_url);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_TIMEOUT,30); 
			
			$fedex_response = curl_exec($ch);
    
			if (preg_match("/<MeterNumber>(.*)<\/MeterNumber>/i", $fedex_response, $matches)) {
				$meter_number = $matches[1];
				$r->errors = "<font color=\"blue\">Your MeterNumber is <b>" . $meter_number . "</b>.</font>";
				if (strlen($shipping_module_id)) {
					$module_params["MeterNumber"] = $meter_number;
					$sql  = " SELECT parameter_id FROM " . $table_prefix . "shipping_modules_parameters ";
					$sql .= " WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER);
					$sql .= " AND parameter_name=" . $db->tosql("MeterNumber", TEXT);
					$db->query($sql);
					if ($db->next_record()) {
						$parameter_id = $db->f("parameter_id");
						$sql  = " UPDATE " . $table_prefix . "shipping_modules_parameters ";
						$sql .= " SET parameter_source=" . $db->tosql($meter_number, TEXT);
						$sql .= " WHERE parameter_id=" . $db->tosql($parameter_id, INTEGER);
						$db->query($sql);
					} else {
						$sql  = " INSERT INTO " . $table_prefix . "shipping_modules_parameters ";
						$sql .= " (shipping_module_id, parameter_name, parameter_source, not_passed) VALUES (";
						$sql .= $db->tosql($shipping_module_id, INTEGER) . ", ";
						$sql .= $db->tosql("MeterNumber", TEXT) . ", ";
						$sql .= $db->tosql($meter_number, TEXT) . ", ";
						$sql .= "0) ";
						$db->query($sql);
					}
				}
			} else if (preg_match("/<Message>(.*)<\/Message>/i", $fedex_response, $matches)) {
				$r->errors = $matches[1];
			} else {
				$r->errors = "Can't obtain MeterNumber from FedEx.";
			}
		}

	} else {
		$sql = "SELECT * FROM " . $table_prefix . "shipping_modules WHERE shipping_module_name LIKE '%FedEx%'";
		$db->query($sql);
		if ($db->next_record()) {
			$shipping_module_id = $db->f("shipping_module_id");
			$external_url = $db->f("external_url");
			$r->set_value("shipping_module_id", $shipping_module_id);
			$r->set_value("FedExURL", $external_url);

			$module_params = array();
			$sql  = " SELECT * FROM " . $table_prefix . "shipping_modules_parameters ";
			$sql .= " WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER);
			$sql .= " AND not_passed<>1 ";
			$db->query($sql);
			while ($db->next_record()) {
				$param_name = $db->f("parameter_name");
				$param_source = $db->f("parameter_source");
				if (isset($r->parameters["$param_name"])) {
					$r->set_value($param_name, $param_source);
				}
			}

		}
	}


	$t = new VA_Template(".");
	$t->set_file("main", "fedex_subscription.html");
	$t->set_var("fedex_subscription_href", "fedex_subscription.php");

	$r->set_form_parameters();

	$t->pparse("main", false);

	function prepare_subscription_request($module_params)
	{
		$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		$xml .= "<FDXSubscriptionRequest xmlns:api=\"http://www.fedex.com/fsmapi\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"FDXSubscriptionRequest.xsd\">\r\n";

		$xml .= "<RequestHeader>\r\n";
		if (isset($module_params["CustomerTransactionIdentifier"]) && strlen($module_params["CustomerTransactionIdentifier"])) {
			$xml .= "<CustomerTransactionIdentifier>" . $module_params["CustomerTransactionIdentifier"] . "</CustomerTransactionIdentifier>\r\n";
		}
		if (isset($module_params["AccountNumber"])) {
			$xml .= "<AccountNumber>" . $module_params["AccountNumber"] . "</AccountNumber>\r\n";
		}
		if (isset($module_params["MeterNumber"]) && strlen($module_params["MeterNumber"])) {
			$xml .= "<MeterNumber>" . $module_params["MeterNumber"] . "</MeterNumber>\r\n";
		}
		$xml .= "</RequestHeader>\r\n";

		$xml .= "<Contact>\r\n";
		if (isset($module_params["PersonName"]) && strlen($module_params["PersonName"])) {
			$xml .= "<PersonName>" . $module_params["PersonName"] . "</PersonName>\r\n";
		}
		if (isset($module_params["CompanyName"]) && strlen($module_params["CompanyName"])) {
			$xml .= "<CompanyName>" . $module_params["CompanyName"] . "</CompanyName>\r\n";
		}
		if (isset($module_params["Department"]) && strlen($module_params["Department"])) {
			$xml .= "<Department>" . $module_params["Department"] . "</Department>\r\n";
		}
		if (isset($module_params["PhoneNumber"]) && strlen($module_params["PhoneNumber"])) {
			$xml .= "<PhoneNumber>" . $module_params["PhoneNumber"] . "</PhoneNumber>\r\n";
		}
		if (isset($module_params["PagerNumber"]) && strlen($module_params["PagerNumber"])) {
			$xml .= "<PagerNumber>" . $module_params["PagerNumber"] . "</PagerNumber>\r\n";
		}
		if (isset($module_params["FaxNumber"]) && strlen($module_params["FaxNumber"])) {
			$xml .= "<FaxNumber>" . $module_params["FaxNumber"] . "</FaxNumber>\r\n";
		}
		if (isset($module_params["EMailAddress"]) && strlen($module_params["EMailAddress"])) {
			$xml .= "<E-MailAddress>" . $module_params["EMailAddress"] . "</E-MailAddress>\r\n";
		}
		$xml .= "</Contact>\r\n";

		$xml .= "<Address>\r\n";
		if (isset($module_params["Line1"]) && strlen($module_params["Line1"])) {
			$xml .= "<Line1>" . $module_params["Line1"] . "</Line1>\r\n";
		}
		if (isset($module_params["Line2"]) && strlen($module_params["Line2"])) {
			$xml .= "<Line2>" . $module_params["Line2"] . "</Line2>\r\n";
		}
		if (isset($module_params["City"]) && strlen($module_params["City"])) {
			$xml .= "<City>" . $module_params["City"] . "</City>\r\n";
		}
		if (isset($module_params["StateOrProvinceCode"]) && strlen($module_params["StateOrProvinceCode"])) {
			$xml .= "<StateOrProvinceCode>" . $module_params["StateOrProvinceCode"] . "</StateOrProvinceCode>\r\n";
		}
		if (isset($module_params["PostalCode"]) && strlen($module_params["PostalCode"])) {
			$xml .= "<PostalCode>" . $module_params["PostalCode"] . "</PostalCode>\r\n";
		}
		if (isset($module_params["CountryCode"]) && strlen($module_params["CountryCode"])) {
			$xml .= "<CountryCode>" . $module_params["CountryCode"] . "</CountryCode>\r\n";
		}
		$xml .= "</Address>\r\n";

		$xml .= "</FDXSubscriptionRequest>\r\n";

		return $xml;
	}

?>