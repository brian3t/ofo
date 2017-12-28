<?php

ini_set('display_errors',1);

include_once("./includes/common.php");
include_once("./includes/navigator.php");
include_once("./includes/items_properties.php");
include_once("./messages/" . $language_code . "/cart_messages.php");
include_once("./messages/" . $language_code . "/download_messages.php");
include_once("./includes/products_functions.php");
include_once("./includes/shopping_cart.php");
include_once("./includes/ads_functions.php");
include_once("./includes/order_items.php");

if(isset($_POST["part"])) {
	$item_code = $_POST["part"];
	$type = "list";
	$cart = "ADD";

	$sql = " SELECT item_id FROM " . $table_prefix . "items WHERE item_code=" . $db->tosql($item_code, TEXT);
	$item_id = get_db_value($sql);
					
	$accessory_id = get_param("accessory_id");
	$sc_item_id = $accessory_id ? $accessory_id : $item_id;
	$sc_price = get_param("price");
	$sc_quantity = $_POST["quantity"];

	$type_param_value = get_param("type");
	if ($type_param_value) { $type = $type_param_value; }
	/* start of adding item to the cart */
	$item_added = add_to_cart($sc_item_id, $sc_price, $sc_quantity, $type, $cart, $new_cart_id, $second_page_options, $sc_errors);
	/* end of adding item to the cart */
	// check if any coupons can be added or removed
	check_coupons();
	//$referURL = "Location: ".$_SERVER['HTTP_REFERER'];
	header("Location: basket.php");
	exit;
}


/*$count = $_POST["count"];

for ($i = 1; $i <= $count; $i++) {

	$part_number = "part".$i;
	
	if (isset($_POST[$part_number])) {
		$item_code = $_POST[$part_number];
		$type = "list";
		$cart = "ADD";

		$sql = " SELECT item_id FROM " . $table_prefix . "items WHERE item_code=" . $db->tosql($item_code, TEXT);
		$item_id = get_db_value($sql);
						
		$accessory_id = get_param("accessory_id");
		$sc_item_id = $accessory_id ? $accessory_id : $item_id;
		$sc_price = get_param("price");
		$sc_quantity = get_param("quantity");
	
		$type_param_value = get_param("type");
		if ($type_param_value) { $type = $type_param_value; }
		$item_added = add_to_cart($sc_item_id, $sc_price, $sc_quantity, $type, $cart, $new_cart_id, $second_page_options, $sc_errors);
		check_coupons();
	}
}

header ("Location: basket.php");*/
?>