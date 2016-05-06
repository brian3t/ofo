<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ogone_check.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * oGone (http://ogone.com) transaction handler by www.viart.com
 */
	$root_folder_path = "./";
	include_once ($root_folder_path ."payments/ogone_functions.php");

	if ($order_id == get_param("orderID")) {
		checkOrder();
	} else {
		$error_message .= "oGone: No order found";
	}
	
?>