<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_footer.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$t->set_file("admin_footer", "admin_footer.html");

	$t->set_var("footer_html", get_setting_value($settings, "html_below_footer", ""));

	if ($va_license_code & 4) {
		$t->global_parse("support_link", false, false, true);
	}
	
	$t->parse("admin_footer");

?>