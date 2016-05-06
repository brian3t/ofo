<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_table_emails.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$table_name = $table_prefix . "newsletters_users";
	$table_alias = "e";
	$table_pk = "email_id";
	$table_title = EMAILS_MSG;
	$min_column_allowed = 1;

	$db_columns = array(
		"email_id" => array(EMAIL_ID_MSG, INTEGER, 1, false),
		"email" => array(EMAIL_FIELD, TEXT, 2, false),
		"date_added" => array(DATE_ADDED_MSG, DATETIME, 2, false),
	);

	$db_aliases["id"] = "email_id";

?>