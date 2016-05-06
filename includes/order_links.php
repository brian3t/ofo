<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  order_links.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	function get_order_links($order_id)
	{
	 	global $db, $settings, $table_prefix;
		global $datetime_show_format;
		
		$eol = get_eol();

		$links = array();
		$links["html"] = "";
		$links["text"] = "";

		// check if we have downloadable product
		$sql = "SELECT COUNT(*) FROM " . $table_prefix . "items_downloads WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$links["items"] = get_db_value($sql);

		if($links["items"] < 1) {
			return $links;
		}

		$site_url = $settings["site_url"];

		// preparing download links
		$sql  = " SELECT id.download_id, id.order_item_id, id.download_added, id.download_path, ";
		$sql .= " oi.item_name, i.download_path AS product_path ";
		$sql .= " FROM ((" . $table_prefix . "items_downloads id ";
		$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON id.order_item_id=oi.order_item_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "items i ON id.item_id=i.item_id) ";
		$sql .= " WHERE id.order_item_id=oi.order_item_id ";
		$sql .= " AND id.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			do {
				$order_item_id = get_translation($db->f("order_item_id"));
				$item_name = get_translation($db->f("item_name"));
				$product_path = trim($db->f("product_path"));
				$path_to_file = trim($db->f("download_path"));
				if (!$path_to_file) {
					$path_to_file = $product_path;
				} 
				$paths = split(";", $path_to_file);
				$download_path = $paths[0];
		    $file_size = @filesize ($download_path);
				$file_name = basename($download_path);
				if ($file_size > 1048576) {
					$file_name .= " (" . number_format($file_size / (1024 * 1024), 1) . " Mb)";
				} else if ($file_size > 1024) {
					$file_name .= " (" . number_format($file_size / 1024) . " Kb)";
				}	else if ($file_size) {
					$file_name .= " (" . number_format($file_size) . " bytes)";
				}

				$download_id = $db->f("download_id");
				$download_added = $db->f("download_added", DATETIME);
				$item_download_url  = $site_url . "download.php?download_id=" . $download_id;
				$vc = md5($download_id . $download_added[3].$download_added[4].$download_added[5]);
				$item_download_url .= "&vc=" . urlencode($vc);
				// prepare links in HTML format
				$links["html"] .= htmlspecialchars($item_name) . " - ";
				$links["html"] .= "<a href=\"" . $item_download_url . "\">" . $file_name . "</a><br>";
				// prepare links in Text mode
				$links["text"] .= $item_name . $eol;
				$links["text"] .= $file_name . $eol . $item_download_url . $eol;
				// save information per order item
				if (!isset($links["html_" . $order_item_id])) {
					$links["html_" . $order_item_id] = "";
					$links["text_" . $order_item_id] = "";
				}
				$links["html_" . $order_item_id] .= "<a href=\"" . $item_download_url . "\">" . $file_name . "</a><br>";
				$links["text_" . $order_item_id] .= $file_name . $eol . $item_download_url . $eol;
				
			} while ($db->next_record());
		} 

		return $links;
	}


	function get_serial_numbers($order_id)
	{
	 	global $db, $settings, $table_prefix;
		
		$eol = get_eol();

 		$serials = array();
		$serials["html"] = "";
		$serials["text"] = "";

		// check if we have serial numbers 
		$sql = "SELECT COUNT(*) FROM " . $table_prefix . "orders_items_serials WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$serials["total"] = get_db_value($sql);

		if($serials["total"] < 1) {
			return $serials;
		}

		// preparing serial numbers 
		$sql  = " SELECT ois.serial_id, ois.serial_number, ois.order_item_id, oi.item_name ";
		$sql .= " FROM (" . $table_prefix . "orders_items_serials ois ";
		$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON ois.order_item_id=oi.order_item_id) ";
		$sql .= " WHERE ois.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			do {
				$order_item_id = get_translation($db->f("order_item_id"));
				$item_name = get_translation($db->f("item_name"));
				$serial_number = $db->f("serial_number");

				// prepare serial numbers in HTML format
				$serials["text"] .= $item_name . " - '" . $serial_number . "'" . $eol;
				// prepare serial numbers in Text mode
				$serials["html"] .= $item_name . " - '" . $serial_number . "'<br>";

				// save information per order item
				if (!isset($serials["html_" . $order_item_id])) {
					$serials["text_" . $order_item_id] = "";
					$serials["html_" . $order_item_id] = "";
				}
				$serials["text_" . $order_item_id] .= $serial_number . $eol;
				$serials["html_" . $order_item_id] .= $serial_number . "<br>";
				
			} while ($db->next_record());
		} 

		return $serials;
	}

	function get_gift_vouchers($order_id)
	{
	 	global $db, $settings, $table_prefix;
		
		$eol = get_eol();

 		$vouchers = array();
		$vouchers["html"] = "";
		$vouchers["text"] = "";

		// check if we have serial numbers 
		$sql = "SELECT COUNT(*) FROM " . $table_prefix . "coupons WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$vouchers["total"] = get_db_value($sql);

		if($vouchers["total"] < 1) {
			return $vouchers;
		}

		// preparing gift vouchers 
		$sql  = " SELECT c.coupon_id,c.coupon_title,c.is_active,c.coupon_code,c.order_item_id ";
		$sql .= " FROM " . $table_prefix . "coupons c ";
		$sql .= " WHERE c.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			do {
				$order_item_id = get_translation($db->f("order_item_id"));
				$coupon_code = $db->f("coupon_code");
				$coupon_title = get_translation($db->f("coupon_title"));

				// prepare gift vouchers in HTML format
				$vouchers["text"] .= $coupon_title . " - '" . $coupon_code  . "'" . $eol;
				// prepare gift vouchers in Text mode
				$vouchers["html"] .= $coupon_title . " - '" . $coupon_code . "'<br>";

				// save information per order item
				if (!isset($vouchers["html_" . $order_item_id])) {
					$vouchers["text_" . $order_item_id] = "";
					$vouchers["html_" . $order_item_id] = "";
				}
				$vouchers["text_" . $order_item_id] .= $coupon_code . $eol;
				$vouchers["html_" . $order_item_id] .= $coupon_code . "<br>";

				
			} while ($db->next_record());
		} 

		return $vouchers;
	}

?>