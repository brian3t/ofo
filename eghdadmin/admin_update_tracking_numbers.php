<?
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");
	
	if(isset($_GET['state'])) {
		$sql = "UPDATE " . $table_prefix . "orders SET keywords = " . $_GET['state'] . " WHERE order_id = " . $_GET['order_id'];
		//echo $sql;
	}	
	
	if(isset($_GET['tracking_id'])) {
		$sql = "UPDATE " . $table_prefix . "orders SET shipping_tracking_id = '" . $_GET['tracking_id'] . "' WHERE order_id = " . $_GET['order_id'];
		//echo $sql;
	}
	//echo 'test: '. $_GET['order_id'];
	$db->query($sql);
	
?>