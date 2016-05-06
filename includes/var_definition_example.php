<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  var_definition_example.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	define("INSTALLED", true); // set to false if you want run install.php
	define("DEBUG",     true); // debug mode - set false on live site

	// database parameters
	$db_lib        = "mysql"; // mysql | postgre | odbc
	$db_type       = "mysql"; // mysql | postgre | access | db2
	$db_name       = "viartshop";
	$db_host       = "localhost";
	$db_port       = "";
	$db_user       = "root";
	$db_password   = "";
	$db_persistent = false;

	$table_prefix = "va_";

	$default_language = "en";

	// date parameters
	$datetime_show_format = array("M", "/", "D", "/", "YY", " ", "h", ":", "mm", " ", "AM");
	$date_show_format     = array("D", " ", "MMM", " ", "YYYY");
	$datetime_edit_format = array("M", "/", "D", "/", "YY", " ", "H", ":", "mm");
	$date_edit_format     = array("DD", ".", "MM", ".", "YYYY");
	// save new date with time shift in seconds (3600 - 1 hour)
	//$va_time_shift = 0; 

	// session settings
	$session_prefix = "viartshop";

	// if you use multi-site functionality uncomment the following line and specify appropriate id for current site
	//$site_id = 1;

	// if you use VAT validation uncomment the following line
	//$vat_validation = true;
	// array of country codes for which VAT check is obligatory
	//$vat_obligatory_countries = array("GB");
	// array of country codes for which remote VAT check won't be run
	//$vat_remote_exception_countries = array("NL");

?>