<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  hsbc_cpi_check.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * The Cardholder Payment Interface (CPI) within HSBC Secure ePayments (http://www.hsbc.com/) 
 * transaction handler by www.viart.com
 */
	$root_folder_path = "./";
	include_once($root_folder_path . "payments/hsbc_cpi_functions.php");

	checkOrder();
?>