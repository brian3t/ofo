<?

mysql_connect('localhost:3306','root','rTrapok)1') or die(mysql_error());
mysql_select_db('oilfiltersonlinestore') or die(mysql_error());
$datafeedRs = mysql_query("SELECT item_code as mfrpartnbr, sales_price as price from va_items order by item_code asc") or die(mysql_error());

while ($rs=mysql_fetch_assoc($datafeedRs)) {
	$content .=  '"'.$rs("mfrpartnbr").'"\t"'.$rs("sales_price").'\n';
}
$datafeed = fopen("datafeed.txt", "a");

fwrite($datafeed, "test");

?>
	  