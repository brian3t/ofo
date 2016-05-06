<?
mysql_connect("localhost", "root", "ifl@b") or die(mysql_error());

$db = "oilfiltersonline_test_store";

mysql_query("UPDATE oilfiltersonline_test_store.va_items SET disable_out_of_stock = 1, use_stock_level = 1, shipping_in_stock = 7, shipping_out_stock = 7, stock_level = 0");

$result = mysql_query("SELECT * from oilfiltersonline.apw_view where manufacturer_id is not null")
or die(mysql_error());

while ($rs=mysql_fetch_assoc($result)) {
    $parts[]=$rs;
  }

foreach($parts as $part) {
  if($part["apwstock"] > 0) {
    if($part["cost"] > 0) {
/*
      if($part["cost"] < 3) {
        $price = ceil($part["cost"] * 2) - .05;
      } else {
        $price = ceil($part["cost"] * 1.35) - .05;
      }
*/
      
       $price = number_format(($part["cost"]), 2, '.', '');
    }
    
    $result = mysql_query("update ".$db.".va_items set stock_level = ".$part["apwstock"].", shipping_in_stock = 2, shipping_out_stock = 2, use_stock_level = 1, disable_out_of_stock = 0, buying_price = " . $part["cost"] . ",  sales_price = " . $price . ", price = " . $price . " where item_code = '".$part["item"]."' and manufacturer_id = " . $part["manufacturer_id"]);
    
    //$result = mysql_query("update ".$db.".va_items set stock_level = ".$part["apwstock"].", shipping_in_stock = 2, shipping_out_stock = 2, use_stock_level = 1, disable_out_of_stock = 0, buying_price = " . $part["cost"] . " where item_code = '".$part["item"]."' and manufacturer_id = " . $part["manufacturer_id"]);
  }
  

  
/*
  else {
    $result = mysql_query("update ".$db.".va_items set stock_level = 0, shipping_in_stock = 7, shipping_out_stock = 7, use_stock_level = 1, disable_out_of_stock = 1 where item_code = '".$part["item"]."' and manufacturer_id = " . $part["manufacturer_id"]);
  }
*/
}
mysql_query("UPDATE oilfiltersonline_test_store.va_items SET disable_out_of_stock = 1, use_stock_level = 1, shipping_in_stock = 7, shipping_out_stock = 7 WHERE stock_level = 0");
mysql_close($con);
?>