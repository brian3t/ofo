<?php
// APW SOAP Integration
// Version 1.0   

/*ini_set('display_errors',1);
error_reporting(E_ALL);*/

ini_set("soap.wsdl_cache_enabled", "0");
$path_to_wsdl = "apw.wsdl";
$client = new SoapClient($path_to_wsdl, array('trace' => 1));

$orderNum = $_GET['order'];
$db = "oilfiltersonline_test_store";
$server = "localhost";
$dbuser = "root";
$dbpass = "rTrapok)1";
$con = mysql_connect($server, $dbuser, $dbpass);
$apwUser = '80174';
/*$apwPass = 'TestAPW'; //** Test Password*/
$apwPass = 'HelloAPW';

function printRequestResponse($client) {
  echo '<h2>Transaction processed successfully.</h2>'. "\n"; 
  echo '<h2>Request</h2>' . "\n";
  echo '<pre>' . htmlspecialchars($client->__getLastRequest()). '</pre>';  
  echo "\n";
   
  echo '<h2>Response</h2>'. "\n";
  echo '<pre>' . htmlspecialchars($client->__getLastResponse()). '</pre>';
  echo "\n";
}

function printFault($exception, $client) {
    echo '<h2>Fault</h2>' . "\n";                        
    echo "<b>Code:</b>{$exception->faultcode}<br>\n";
    echo "<b>String:</b>{$exception->faultstring}<br>\n";
}

function addNote($orderNumber, $title, $details) {
	global $client, $con, $db, $apwUser, $apwPass;
	mysql_select_db($db, $con);
	$result = mysql_query(sprintf("SELECT count(*) as count from va_orders_notes where order_id = %s", $orderNumber));
	$notes = mysql_fetch_array($result);
	if($notes['count'] == 0) {
		mysql_query(sprintf("insert into va_orders_notes (order_id, note_title, note_details, show_for_user) values (%s, '%s', '%s', 0)", $orderNumber, $title, $details));
	} else {
		mysql_query(sprintf("update va_orders_notes set note_title = '%s', note_details = CONCAT_WS('\n', '%s', note_details) where order_id = %s", $title, $details, $orderNumber));
	}	
}

function submitOrder($orderNumber) {
	global $client, $con, $db, $apwUser, $apwPass;
	mysql_select_db($db, $con);
	
	$result = mysql_query(sprintf("SELECT * from va_orders where order_id = %s limit 1", $orderNumber));  
	
	$customer = mysql_fetch_array($result);
	
	$result = mysql_query(sprintf("SELECT items.manufacturer_code as code, 
									items.quantity as quantity, 
									manf.manufacturer_name as manufacturer
									from va_orders_items items 
									inner join va_items sku on (items.item_id = sku.item_id) 
									inner join va_manufacturers manf on (sku.manufacturer_id = manf.manufacturer_id)
									where items.order_id = %s", $orderNumber));
	
	while ($rs=mysql_fetch_assoc($result)) {
		$items[]=$rs;
	}
	
	foreach($items as $item) {
		$Part[] = array('OrderItemId' => $i,
						'Sku' => $item['code'],
						'Brand' => strtoupper($item['manufacturer']),
						'Qty' => $item['quantity']
						);
		$i++;
	}

	$request['UserData'] = array('CustomerAccountNumber' => $apwUser, 'CustomerPassword' => $apwPass);
	$request['OrderInfo'] = array('Order' => 
								  array('PONumber' => $orderNumber,
										'Delivery_Name' => $customer['delivery_name'],
										'Delivery_Address1' => $customer['delivery_address1'],
										'Delivery_Address2' => $customer['delivery_address2'],
										'Delivery_City' => $customer['delivery_city'],
										'Delivery_State' => $customer['delivery_state_code'],
										'Delivery_Postcode' => $customer['delivery_zip'],
										'Delivery_EmailAddress' => 'support@oilfiltersonline.com',
										'Delivery_Telephone' => $customer['daytime_phone'],
										'ShippingMethod' => $customer['shipping_type_code']
										),
									'Parts' => array('Part' => $Part)
									);
	$status = '<h2>Order #'.$orderNumber.'</h2>';

	try 
	{
		$response = $client ->PlaceOrder($request);
		//printRequestResponse($client);
		if($response->PlaceOrderResult->OrderInfo->Order->SupplierOrderId == ''){
			foreach($response->PlaceOrderResult->OrderInfo->Parts->Part as $part) {
				$status .= $part->Sku.': '.$part->Status.'<br>';
			}
			//addNote($orderNumber, 'APW Order Error', $status);
			echo $status.'APW Order Error<br>'.$status;
		} else {
			if(is_array($response->PlaceOrderResult->OrderInfo->Parts->Part)) {
				foreach($response->PlaceOrderResult->OrderInfo->Parts->Part as $part) {
					$details .= $part->Brand.' '.$part->Sku.': ('.$part->Qty.') '.$part->Status.'\n';
				}
			} else {
				$details = $response->PlaceOrderResult->OrderInfo->Parts->Part->Brand.' '.$response->PlaceOrderResult->OrderInfo->Parts->Part->Sku.': ('.$response->PlaceOrderResult->OrderInfo->Parts->Part->Qty.') '.$response->PlaceOrderResult->OrderInfo->Parts->Part->Status.'\n';
			}

			addNote($orderNumber, 'APW '.$response->PlaceOrderResult->OrderInfo->Order->SupplierOrderId, $details);
			echo $status.'APW '.$response->PlaceOrderResult->OrderInfo->Order->SupplierOrderId;
		}
		
	} catch (SoapFault $exception) {
		//addNote($orderNumber, 'APW SOAP Error', 'Code: '.$exception->faultcode.'\nString: '.$exception->faultstring.'\n\n');
		//printFault($exception, $client);
		echo 'APW SOAP Error<br>', 'Code: '.$exception->faultcode.'<br>String: '.$exception->faultstring;
	}
	
}

function checkInventory($orderNumber) {
	global $client, $con, $db, $apwUser, $apwPass;
	mysql_select_db($db, $con);
	
	$result = mysql_query(sprintf("SELECT * from va_orders where order_id = %s limit 1", $orderNumber));  
	
	$customer = mysql_fetch_array($result);
	
	$result = mysql_query(sprintf("SELECT items.manufacturer_code as code, 
									items.quantity as quantity, 
									manf.manufacturer_name as manufacturer
									from va_orders_items items 
									inner join va_items sku on (items.item_id = sku.item_id) 
									inner join va_manufacturers manf on (sku.manufacturer_id = manf.manufacturer_id)
									where items.order_id = %s", $orderNumber));
	
	while ($rs=mysql_fetch_assoc($result)) {
		$items[]=$rs;
	}
	//print_r($items);
	
	foreach($items as $item) {
		$Part[] = array('OrderItemId' => $i,
						'Sku' => $item['code'],
						'Brand' => strtoupper($item['manufacturer']),
						'Qty' => $item['quantity']
						);
		$i++;
	}

	$request['UserData'] = array('CustomerAccountNumber' => $apwUser, 'CustomerPassword' => $apwPass);
	$request['OrderInfo'] = array('ShippingMethod' => $customer['shipping_type_code'], 'Parts' => array('Part' => $Part));
	$status = '<h2>Order #'.$orderNumber.'</h2>';
	
	try 
	{
		$response = $client ->CheckInventory($request);
		if(is_array($response->CheckInventoryResult->OrderInfo->Parts->Part)) {
			foreach($response->CheckInventoryResult->OrderInfo->Parts->Part as $part) {
					$status .= $part->Sku.' ('.$part->Stock.") ".$part->Status.' $'.$part->VendorUnitPrice.'<br />';
				}
		} else {
			$status .= $response->CheckInventoryResult->OrderInfo->Parts->Part->Sku.' ('.$response->CheckInventoryResult->OrderInfo->Parts->Part->Stock.") ".$response->CheckInventoryResult->OrderInfo->Parts->Part->Status.' $'.$part->VendorUnitPrice.'<br />';
		}
		echo $status;
		//printRequestResponse($client);		
	} catch (SoapFault $exception) {
		printFault($exception, $client);
	}
	
}

function checkStatus($orderNumber) {
	global $client, $con, $db, $apwUser, $apwPass;

	$request['UserData'] = array('CustomerAccountNumber' => $apwUser, 'CustomerPassword' => $apwPass);
	$request['OrderInfo'] = array('Order' => array('PONumber' => $orderNumber));
	$status = '<h2>Order #'.$orderNumber.'</h2>';

	try 
	{
		$response = $client->OrderStatus($request);
		$status .= "Status: ".$response->OrderStatusResult->Order->Status."<br />";
		if($response->OrderStatusResult->Order->ShipUnits->ShipUnit->TrackingNumber) {
			$status .= "Tracking #: ".$response->OrderStatusResult->Order->ShipUnits->ShipUnit->TrackingNumber."<br />";
		}
		echo $status;
	} catch (SoapFault $exception) {
		printFault($exception, $client);
	}
	
}

function updateTracking() {
	global $client, $con, $db, $apwUser, $apwPass;
	mysql_select_db($db, $con);
	$result = mysql_query("SELECT order_id from va_orders where order_status = 4");
	while ($rs=mysql_fetch_assoc($result)) {
		$orders[]=$rs;
	}
	
	foreach($orders as $order) {
		//echo $order["order_id"];
		$request['UserData'] = array('CustomerAccountNumber' => $apwUser, 'CustomerPassword' => $apwPass);
		$request['OrderInfo'] = array('Order' => array('PONumber' => $order["order_id"]));
		try 
		{
			$response = $client->OrderStatus($request);
			//printRequestResponse($client);
		} catch (SoapFault $exception) {
			printFault($exception, $client);
		}

		if($response->OrderStatusResult->Order->Status != "Order Not Found" && $response->OrderStatusResult->Order->Status != "Open") {
			if($response->OrderStatusResult->Order->ShipUnits->ShipUnit->TrackingNumber) {
				$tracking_number = $response->OrderStatusResult->Order->ShipUnits->ShipUnit->TrackingNumber;
				mysql_query("UPDATE va_orders set shipping_tracking_id = '" . $tracking_number . "' where order_id = " . $order["order_id"]);
			}
		}
	
	}
		
}

if($_GET["mode"] == "submitOrder") {
	submitOrder($orderNum);
} elseif($_GET["mode"] == "checkInventory") {
	checkInventory($orderNum);
} elseif($_GET["mode"] == "checkStatus") {
	checkStatus($orderNum);
} elseif($_GET["mode"] == "tracking") {
	updateTracking();
}
?>