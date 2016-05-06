<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  parameters.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$parameters = array(
		"name", "first_name", "last_name", "company_id", "company_name", "email",
		"address1", "address2", "city", "province", "state_id", "zip", "country_id",
		"phone", "daytime_phone", "evening_phone", "cell_phone", "fax"
	);

	$cc_parameters = array(
		"cc_name", "cc_first_name", "cc_last_name", "cc_number", "cc_start_date", "cc_expiry_date", 
		"cc_type", "cc_issue_number", "cc_security_code", "pay_without_cc"
	);

	$additional_parameters = array(
		"nickname", "friendly_url", "tax_id", "paypal_account", "msn_account", "icq_number", "user_site_url", "short_description", "full_description", "is_hidden"
	);

	$call_center_user_parameters = array(
		"user_id", "name", "first_name", "last_name", "company_id", "company_name", "email", 
		"address1", "address2", "city", "province", "state_id", "zip", "country_id", 
		"phone", "daytime_phone", "evening_phone", "cell_phone", "fax",
		"delivery_name", "delivery_first_name", "delivery_last_name", "delivery_company_id", 
		"delivery_company_name", "delivery_email", "delivery_address1", "delivery_address2", 
		"delivery_city", "delivery_province", "delivery_state_id", "delivery_zip", 
		"delivery_country_id", "delivery_phone", "delivery_daytime_phone", 
		"delivery_evening_phone", "delivery_cell_phone", "delivery_fax"
	);
	
?>