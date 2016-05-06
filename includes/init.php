<?php
	// Connect to the database
	mysql_connect('localhost:3306','root','ifl@b') or die(mysql_error());
	mysql_select_db('oilfiltersonlinestore') or die(mysql_error());

	// Backward compatibility with old variable names
	if(isset($currentDate))
		$cdate = $currentDate;
	if(isset($currentTime))
		$ctime = $currentTime;
	if(isset($imageNum))
		$cimg = $imageNum;

	// Set default values if needed
	if(!isset($cdate))
		$cdate = date('Ymd');     // $cdate = today's date
	if(!isset($cimg))
		$cimg = 0;    // $cimg = 0

	if(isset($userid))
	{
		// Get user info
		$result = mysql_query('SELECT * FROM va_orders WHERE order_id="'.$order_id.'" LIMIT 1');
		if($row = mysql_fetch_array($result))
		{
			$name = $row['name'];
			$email = $row['email'];
			
		}
	}
?>