<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_import_oscommerce.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	// importing categories
	$sql = " SELECT COUNT(*) FROM va_categories ";
	$dbi->query($sql);
	$dbi->next_record($sql);
	$total = $dbi->f(0); // check the total number of records
	
	$imported = 0;
	$sql = " SELECT * FROM va_categories ";
	$dbi->query($sql);
	while ($dbi->next_record()) {
		$imported++; // save number of imported records
		importing_data("categories", $imported, $total); // output importings results to the page
	}

	// importing products

	// importing users

	// importing orders

	// ...

?>