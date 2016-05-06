<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  sms_functions.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


function sms_send($recipient, $message, $originator)
{
	if (!$recipient || !$message) {
		return false;
	}
	
	/*
	 *	ADD CODE FOR SMS SENDING HERE
	 */

	// return true or SMS id in case of success
	return true; 
}

?>