<?php
$db = mysqli_connect("localhost", "root", "rTrapok)1") or die($db->error);

$db->query("UPDATE oilfiltersonline_test_store.va_items SET disable_out_of_stock = 1, use_stock_level = 1, shipping_in_stock = 7, shipping_out_stock = 7, stock_level = 0");

$result = $db->query("SELECT * FROM oilfiltersonline.apw_view WHERE manufacturer_id IS NOT NULL")
or die($db->error);

while ($rs = $result->fetch_assoc()) {
    $parts[] = $rs;
}

foreach ($parts as $part){
    if ($part["apwstock"] > 0){
        if ($part["cost"] > 0){
            /*
                  if($part["cost"] < 3) {
                    $price = ceil($part["cost"] * 2) - .05;
                  } else {
                    $price = ceil($part["cost"] * 1.35) - .05;
                  }
            */

            $price = number_format(($part["cost"]), 2, '.', '');
        }

        $result = $db->query("UPDATE oilfiltersonline_test_store.va_items SET stock_level = " . $part["apwstock"] . ", shipping_in_stock = 2, shipping_out_stock = 2, use_stock_level = 1, disable_out_of_stock = 0, buying_price = " . $part["cost"] . ",  sales_price = " . $price . ", price = " . $price . " WHERE item_code = '" . $part["item"] . "' AND manufacturer_id = " . $part["manufacturer_id"]);

        //$result = mysql_query("update ".$db.".va_items set stock_level = ".$part["apwstock"].", shipping_in_stock = 2, shipping_out_stock = 2, use_stock_level = 1, disable_out_of_stock = 0, buying_price = " . $part["cost"] . " where item_code = '".$part["item"]."' and manufacturer_id = " . $part["manufacturer_id"]);
    }

    /*
      else {
        $result = mysql_query("update ".$db.".va_items set stock_level = 0, shipping_in_stock = 7, shipping_out_stock = 7, use_stock_level = 1, disable_out_of_stock = 1 where item_code = '".$part["item"]."' and manufacturer_id = " . $part["manufacturer_id"]);
      }
    */
}
$db->query("UPDATE oilfiltersonline_test_store.va_items SET disable_out_of_stock = 1, use_stock_level = 1, shipping_in_stock = 7, shipping_out_stock = 7 WHERE stock_level = 0");
$db->close();