<?php
	// Connect to the database
	mysql_connect('localhost:3306','root','rTrapok)1') or die(mysql_error());
	mysql_select_db('oilfiltersonline_test_store') or die(mysql_error());

	if(isset($_GET['order_id']))
	{

		// Get order info
		$result = mysql_query("SELECT DATE_FORMAT(order_placed_date, '%H') as PHour, DATE_FORMAT(order_placed_date, '%i') as PMinute, DATE_FORMAT(order_placed_date, '%S') as PSecond FROM va_orders where order_id = ".$_GET['order_id']." LIMIT 1");
		if($row = mysql_fetch_array($result))
		{
			$hour = $row['PHour'];
			$minute = $row['PMinute'];
			$second = $row['PSecond'];
		}
	}
	
$order_str = $_GET['order_id'].$hour.$minute.$second;

header( 'Location: https://www.oilfiltersonline.com/credit_card_info.php?order_id='.$_GET['order_id'].'&vc='.md5($order_str) ) ;
?>
