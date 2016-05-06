<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  user_invoice_pdf.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$includes_path = "./includes/";
	include_once("./includes/common.php");
	include_once("./includes/record.php");
	include_once("./includes/order_items.php");
	include_once("./includes/parameters.php");
	include_once("./includes/pdflib.php");
	include_once("./includes/pdf.php");
	include_once("./includes/invoice_functions.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");
	include_once("./messages/" . $language_code . "/admin_messages.php");

	check_user_security("my_orders");


	$user_id = get_session("session_user_id");
	$ids = get_param("ids");
	$order_id = get_param("order_id");
	if ($order_id) {
		$ids = $order_id;
	}

	$order_ids = explode(",", $ids);
	for ($i = 0; $i < sizeof($order_ids); $i++)
	{
		$id = $order_ids[$i];
		$sql  = " SELECT os.user_invoice_activation ";
		$sql .= " FROM (" . $table_prefix . "orders o ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
		$sql .= " WHERE o.order_id=" . $db->tosql($id, INTEGER);
		$sql .= " AND o.user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$user_invoice_activation = $db->f("user_invoice_activation");
			// check if user can access the invoice
			if (!$user_invoice_activation) {
				header("Location: user_orders.php");
				exit;
			}
		} else {
			// can find order for user
			header("Location: user_orders.php");
			exit;
		}
	}

	$buffer = pdf_invoice($ids);
	$length = strlen($buffer);

	$pdf_filename = "invoice_" . str_replace(",", "_", $ids) . ".pdf";
	header("Pragma: private");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Length: " . $length);
	header("Content-Disposition: attachment; filename=" . $pdf_filename);
	header("Content-Transfer-Encoding: binary");

	echo $buffer;

?>