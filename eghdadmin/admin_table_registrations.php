<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_table_registrations.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$table_name = $table_prefix . "registration_list";
	$table_alias = "reg";
	$table_pk = "registration_id";
	$table_title = PRODUCTS_REGISTRATIONS_MSG;
	$min_column_allowed = 5;

	$db_columns = array(
		"registration_id" => array(REGISTRATION_NUMBER_MSG, INTEGER, 1, false),
		
		"user_id" => array(USER_ID_MSG, INTEGER, 2, 0),
		
		"is_approved" => array(IS_APPROVED_MSG, INTEGER, 2, false),
		
		"category_id" => array(CAT_ID_MSG, INTEGER, 2, 0),
		"item_id" => array(PRODUCT_ID_MSG, INTEGER, 2, 0),
		
		"item_code" => array(PROD_CODE_MSG, TEXT, 2, false),
		"item_name" => array(PROD_NAME_MSG, TEXT, 2, false),
		"serial_number" => array(SERIAL_NUMBER_MSG, TEXT, 2, false),
		"invoice_number" => array(INVOICE_NUMBER_MSG, TEXT, 2, false),
		"store_name" => array(STORE_NAME_MSG, TEXT, 2, false),
		
		"purchased_day" => array(DAY_OF_PURCHASE_MSG, INTEGER, 2, 0),
		"purchased_month" => array(MONTH_OF_PURCHASE_MSG, INTEGER, 2, 0),
		"purchased_year" => array(YEAR_OF_PURCHASE_MSG, INTEGER, 2, 0),
		
		"admin_id_added_by" => array(ADMIN_ID_ADDED_BY_MSG, INTEGER, 2, 0),
		"admin_id_modified_by" => array(ADMIN_ID_MODIFIED_BY_MSG, INTEGER, 2, 0),
		
		"date_added" => array(DATE_ADDED_MSG, DATETIME, 2, true),
		"date_modified" => array(MODIFIED_DATE_MSG, DATETIME, 2, true)
	);
	
	$sql = " SELECT property_id, property_name, control_type FROM " . $table_prefix . "registration_custom_properties ";
	$db->query($sql);
	while ($db->next_record()) {
		$property_id   = $db->f("property_id");
		$property_name = $db->f("property_name");
		$control_type  = $db->f("control_type");
		$db_columns["registration_property_" . $property_id] = array(get_translation($property_name), TEXT, 2, false);
		$db_columns["registration_property_" . $property_id]["control_type"] = $control_type;
	}

	$db_aliases["id"] = "registration_id";
?>