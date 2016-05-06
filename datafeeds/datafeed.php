<?
mysql_connect('localhost:3306','root','ifl@b') or die(mysql_error());
mysql_select_db('oilfiltersonline_test_store') or die(mysql_error());

// CSV Format
$datafeedRs = mysql_query("SELECT va_items.item_name as name, va_items.item_code as mfrpartnbr, va_items.sales_price as price, va_items.big_image as largeimg, va_items.small_image as smallimg, va_manufacturers.manufacturer_name as manufacturer from va_items join va_manufacturers on va_items.manufacturer_id = va_manufacturers.manufacturer_id where va_items.is_approved = 1 and va_items.is_sales = 1 order by va_items.item_code asc") or die(mysql_error());

$delimiter = ",";

$content = "\"Name\"".$delimiter."\"Manufacturer\"".$delimiter."\"PartNumber\"".$delimiter."\"Price\"".$delimiter."\"URL\"".$delimiter."\"ThumbnailImage\"".$delimiter."\"FullImage\"\n";

while ($rs=mysql_fetch_assoc($datafeedRs)) {
	$content .=  "\"".trim($rs['name'])."\"".$delimiter."\"".$rs['manufacturer']."\"".$delimiter."\"".$rs['mfrpartnbr']."\"".$delimiter.$rs['price'].$delimiter."\"http://www.oilfiltersonline.com/product_details.php?item_code=".$rs['mfrpartnbr']."\"".$delimiter."\"http://www.oilfiltersonline.com/".$rs['largeimg']."\"".$delimiter."\"http://www.oilfiltersonline.com/".$rs['smallimg']."\"\n";
}
$datafeed = fopen("d:\inetpub\viarttest\datafeeds\datafeed.csv","w");

fwrite($datafeed, $content);

// Tab Delimited Format
$datafeedRs = mysql_query("SELECT va_items.item_name as name, va_items.item_code as mfrpartnbr, va_items.sales_price as price, va_items.big_image as largeimg, va_items.small_image as smallimg, va_manufacturers.manufacturer_name as manufacturer from va_items join va_manufacturers on va_items.manufacturer_id = va_manufacturers.manufacturer_id where va_items.is_approved = 1 and va_items.is_sales = 1 order by va_items.item_code asc") or die(mysql_error());

$delimiter = "\t";

$content = "\"Name\"".$delimiter."\"Manufacturer\"".$delimiter."\"PartNumber\"".$delimiter."\"Price\"".$delimiter."\"URL\"".$delimiter."\"ThumbnailImage\"".$delimiter."\"FullImage\"\n";

while ($rs=mysql_fetch_assoc($datafeedRs)) {
	$content .=  "\"".trim($rs['name'])."\"".$delimiter."\"".$rs['manufacturer']."\"".$delimiter."\"".$rs['mfrpartnbr']."\"".$delimiter.$rs['price'].$delimiter."\"http://www.oilfiltersonline.com/product_details.php?item_code=".$rs['mfrpartnbr']."\"".$delimiter."\"http://www.oilfiltersonline.com/".$rs['largeimg']."\"".$delimiter."\"http://www.oilfiltersonline.com/".$rs['smallimg']."\"\n";
}
$datafeed = fopen("d:\inetpub\viarttest\datafeeds\datafeed.txt","w");

fwrite($datafeed, $content);

?>