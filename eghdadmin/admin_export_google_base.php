<?php
/*
*	Google Base Export
*	RSS 2.0 Formatted (Example http://base.google.com/base/products2.xml)
*/
	@set_time_limit (900);

	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/shopping_cart.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("import_export");
	check_admin_security("products_export_google_base");
	
	// settings
	$tax_rates       = get_tax_rates(true);	
	$country_id      = $settings["country_id"];
	$tax_percent     = isset($tax_rates[0]) ? $tax_rates[0] : 0;
	$tax_region      = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($country_id, INTEGER, true, false));
	$tax_prices_type = get_setting_value($settings, "tax_prices_type");
	
	$google_base_ftp_login    = get_setting_value($settings, "google_base_ftp_login");
	$google_base_ftp_password = get_setting_value($settings, "google_base_ftp_password");
	
	$google_base_filename     = get_setting_value($settings, "google_base_filename");
	$google_base_title        = get_setting_value($settings, "google_base_title");
	$google_base_description  = get_setting_value($settings, "google_base_description");
	$google_base_encoding     = get_setting_value($settings, "google_base_encoding", "UTF-8");
	
	$google_base_save_path    = get_setting_value($settings, "google_base_save_path", get_setting_value($settings, "tmp_dir", "../images/"));
	$google_base_export_type  = get_setting_value($settings, "google_base_export_type", 0);

	if (!$google_base_filename) {
		$google_base_filename = 'googlebase.xml';
	}
	$google_base_tax               = get_setting_value($settings, "google_base_tax", true);
	$google_base_days_expiry       = get_setting_value($settings, "google_base_days_expiry", 30);
	$google_base_product_condition = get_setting_value($settings, "google_base_product_condition", "new");
	$google_base_global_product_type_id = get_setting_value($settings, 'google_base_product_type_id', 0);
	
	$site_url = get_setting_value($settings, "site_url");
	$friendly_urls      = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$product_link       = $site_url . get_custom_friendly_url("product_details.php") . "?item_id=";
	
	$current_date              = getdate();
	$expiration_date           = mktime ($current_date["hours"], $current_date["minutes"], $current_date["seconds"], $current_date["mon"], $current_date["mday"] + $google_base_days_expiry, $current_date["year"]);
	$expiration_date_formatted = date("Y-m-d", $expiration_date);
	
	$dbd = new VA_SQL();
	$dbd->DBType       = $db->DBType;
	$dbd->DBDatabase   = $db->DBDatabase;
	$dbd->DBUser       = $db->DBUser;
	$dbd->DBPassword   = $db->DBPassword;
	$dbd->DBHost       = $db->DBHost;
	$dbd->DBPort       = $db->DBPort;
	$dbd->DBPersistent = $db->DBPersistent;	
	
	// write in file or output to the browser
	$write_to_file = false;
	if ($google_base_export_type==1 && $google_base_ftp_login && $google_base_ftp_password) {
		$fp = fopen($google_base_save_path . $google_base_filename, "w+");
		if (!$fp) {
			echo MODULE_COULDNT_WRITE_TO_MSG . $google_base_save_path . $google_base_filename . CHECK_PERMISSIONS_MSG . "<br/>";
			fclose($fp);
			exit;
		}
		$write_to_file = true;
	}
	
	// search items
	$s  = trim(get_param("s"));
	$sc = get_param("sc");
	$sl = get_param("sl");
	$ss = get_param("ss");
	$ap = get_param("ap");
	
	$search = (strlen($sc) || strlen($s) || strlen($sl) || strlen($ss) || strlen($ap)) ? true : false;

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	
	$sql  = " SELECT i.item_id, i.item_type_id, i.google_base_type_id, it.google_base_type_id AS item_type_gbt ";
	if (strlen($sc)) {
		$sql .= " FROM (((" . $table_prefix . "items i ";
		$sql .= " LEFT JOIN " . $table_prefix . "items_categories ic ON ic.item_id=i.item_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "categories c ON ic.category_id=c.category_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON i.item_type_id=it.item_type_id) ";
	} else {
		$sql .= " FROM (" . $table_prefix . "items i ";
		$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON i.item_type_id=it.item_type_id) ";
	}
	$where = " WHERE i.google_base_type_id>=0 ";
	if (!$search) {
		$where .= " AND i.is_showing=1 ";
		$where .= " AND ((i.hide_out_of_stock=1 AND i.stock_level > 0) OR i.hide_out_of_stock=0)";
	}
	if($search && $sc != 0) {
		$where .= " AND c.category_id = ic.category_id ";
		$where .= " AND (ic.category_id = " . $db->tosql($sc, INTEGER);
		$where .= " OR c.category_path LIKE '" . $db->tosql($tree->get_path($sc), TEXT, false) . "%')";
	} else if(strlen($sc)) {
		$where .= " AND ic.category_id = " . $db->tosql($sc, INTEGER);
	}
	if($s) {
		$sa = split(" ", $s);
		for($si = 0; $si < sizeof($sa); $si++) {
			$where .= " AND (i.item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR i.item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
		}
	}
	if(strlen($sl)) {
		if ($sl == 1) {
			$where .= " AND (i.stock_level>0 OR i.stock_level IS NULL) ";
		} else {
			$where .= " AND i.stock_level<1 ";
		}
	}
	if(strlen($ss)) {
		if ($ss == 1) {
			$where .= " AND i.is_showing=1 ";
		} else {
			$where .= " AND i.is_showing=0 ";
		}
	}
	if(strlen($ap)) {
		if ($ap == 1) {
			$where .= " AND i.is_approved=1 ";
		} else {
			$where .= " AND i.is_approved=0 ";
		}
	}
	$sql .= $where;
	$db->query($sql);
	
	$item_ids = array();
	$items    = array();
	while ($db->next_record()) {
		$item_id = $db->f("item_id");
		$google_base_type_id = $db->f("google_base_type_id");
		$item_type_gbt = $db->f("item_type_gbt");
		if ($google_base_type_id == 0) {
			$sql  = " SELECT MAX(google_base_type_id) ";
			$sql .= " FROM (" . $table_prefix . "items_categories ic ";
			$sql .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id=ic.category_id) ";
			$sql .= " WHERE ic.item_id=" . $db->tosql($item_id, INTEGER);	
			$dbd->query($sql);
			if ($dbd->next_record()) {	
				$google_base_type_id = $dbd->f(0);
			}
			if ($google_base_type_id == 0) {
				$google_base_type_id = $item_type_gbt;
			}
			if ($google_base_type_id == 0) {	
				$google_base_type_id = $google_base_global_product_type_id;
			}
		}
		if ($google_base_type_id<=0) {
			continue;			
		}
		
		$sql  = " SELECT type_name FROM " . $table_prefix . "google_base_types ";
		$sql .= " WHERE type_id=" . $db->tosql($google_base_type_id, INTEGER);
		$dbd->query($sql);
		if ($dbd->next_record()) {	
			$google_base_type_name = $dbd->f(0);
		} else {
			continue;
		}
		
		$item_ids[] = $item_id;
		$items[$item_id] = array (
			"google_base_type_id" => $google_base_type_id,
			"google_base_type_name" => $google_base_type_name
		);
	}
	
	if (!$item_ids) {
		echo NO_PRODUCTS_EXPORT_MSG;
		exit;
	}
	
	// get schema type of items
	$schema_type = 'g';		
	$sql  = " SELECT a.attribute_name, a.attribute_type, a.value_type, f.feature_value FROM (" . $table_prefix . "features f ";
	$sql .= " INNER JOIN " . $table_prefix . "google_base_attributes a ON a.attribute_id=f.google_base_attribute_id)";
	$sql .= " WHERE f.item_id IN ( " . $db->tosql($item_ids, INTEGERS_LIST) . ")";
	$sql .= " AND a.attribute_type = 'c' AND f.feature_value IS NOT NULL AND a.attribute_name IS NOT NULL ";
	$db->query($sql);
	if ($db->next_record()) {	
		$schema_type = 'c';
	}	
	
	// echo headers
	if (!$write_to_file) {
		header("Pragma: private");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=" . $google_base_filename);
		header("Content-Transfer-Encoding: binary");	
	}
	
	write_to(
		"<?xml version='1.0' encoding='" . $google_base_encoding . "'?>" . $eol
		 . "<rss version='2.0' xmlns:" . $schema_type . "='http://base.google.com/ns/1.0'>" . $eol
		 . "<channel>" . $eol
	);
	if(strlen($google_base_title))
		write_to("<title>" . $google_base_title . "</title>" . $eol);
	if(strlen($google_base_description))
		write_to("<description>" . $google_base_description . "</description> " . $eol);		
	write_to("<link>" . get_setting_value($settings, "site_url") . "</link>" . $eol);			
	
	// get items	
	$sql  = " SELECT i.item_id, i.item_type_id, i.item_code, i.item_name, i.full_description, i.big_image, i.meta_keywords, i.friendly_url, ";
	$sql .= " i.manufacturer_code, i.weight, i.issue_date, m.manufacturer_name, ";
	$sql .= " i.price, i.sales_price, i.is_sales, i.properties_price, i.tax_free, i.stock_level ";
	$sql .= " FROM (" . $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
	$sql .= " WHERE i.item_id IN ( " . $db->tosql($item_ids, INTEGERS_LIST) . ")";
	$db->query($sql);
	while ($db->next_record()) {

		$item_id      = $db->f("item_id");
		$item_type_id = $db->f("item_type_id");
		$item_name    = htmlspecialchars(trim(get_translation($db->f("item_name"))));
		$friendly_url = $db->f("friendly_url");
		
		if (isset($items[$item_id])) {
			$google_base_type_name = $items[$item_id]["google_base_type_name"];
			unset($items[$item_id]);
		} else {
			continue;
		}
		
		if ($friendly_urls && strlen($friendly_url)) {
			$product_details_url = $site_url . $friendly_url . $friendly_extension;
		} else {
			$product_details_url = $product_link . $item_id;
		}
		
		write_to("<item>" . $eol);		
		write_to("<ID>"   . $item_id . "</ID>" . $eol);
		// write_to("<guid>" . $item_id . "</guid>" . $eol);
		write_to("<title><![CDATA[" . xml_rep($item_name) . "]]></title>" . $eol);
			
		write_to("<link>". $product_details_url ."</link>" . $eol);
				
		/*$item_code    = $db->f("item_code");
		if ($item_code) {			
			if (strtolower($google_base_type_name) == "books") {
				write_to("<" . $schema_type . ":isbn>" . $item_code . "</" . $schema_type . ":isbn>" . $eol);
			} else {
				write_to("<" . $schema_type . ":upc>" . $item_code . "</" . $schema_type . ":upc>" . $eol);
			}
		}*/
				
		write_to("<" . $schema_type . ":product_type>" . $google_base_type_name . "</" . $schema_type . ":product_type>" . $eol);
		//write_to("<" . $schema_type . ":expiration_date>" . $expiration_date_formatted . "</" . $schema_type . ":expiration_date>" . $eol);
		write_to("<" . $schema_type . ":condition>" . $google_base_product_condition . "</" . $schema_type . ":condition>" . $eol);
				
		$description = rtrim(trim(get_translation($db->f("full_description"))));
		if (strlen($description )) {
			write_to("<description><![CDATA[" . xml_rep($description) . "]]></description>" . $eol);
		} else {
			$description = rtrim(trim(get_translation($db->f("short_description"))));
			write_to( "<description><![CDATA[" . xml_rep($description) . "]]></description>" . $eol);
		}
		
		$manufacturer_name = $db->f("manufacturer_name");
		if (strlen($manufacturer_name))
			write_to("<" . $schema_type . ":brand><![CDATA[" . xml_rep($manufacturer_name) . "]]></" . $schema_type . ":brand>" . $eol);
		
		$manufacturer_code = $db->f("manufacturer_code");
		if (strlen($manufacturer_code))
			write_to( "<" . $schema_type . ":mpn><![CDATA[" . xml_rep($manufacturer_code) . "]]></" . $schema_type . ":mpn>" . $eol);
			write_to( "<" . $schema_type . ":model_number><![CDATA[" . xml_rep($manufacturer_code) . "]]></" . $schema_type . ":model_number>" . $eol);
		
		$image_url = $db->f("big_image");
		if ($image_url && !preg_match("/^http\:\/\//", $image_url)) {
			$image_url = $settings["site_url"] . $image_url;
		}
		if (strlen($image_url))
			write_to( "<" . $schema_type . ":image_link>" . $image_url . "</" . $schema_type . ":image_link>" . $eol);
	  
		$price            = $db->f("price");
		$sales_price      = $db->f("sales_price");
		$is_sales         = $db->f("is_sales");
		$properties_price = $db->f("properties_price");
		$tax_free         = $db->f("tax_free");		
		if ($is_sales && $sales_price > 0) {
			$price = $sales_price;
		}
		$price += $properties_price;
		if ($google_base_tax) {			
			$price_tax = get_tax_amount($tax_rates, $item_type_id, $price, $tax_free, $tax_percent);			
			if ($tax_prices_type == 1) {
				$price_incl = $price;
				$price_excl = $price - $price_tax;
			} else {
				$price_incl = $price + $price_tax;
				$price_excl = $price;
			}
			$price = $price_incl;
			write_to( "<" . $schema_type . ":tax_percent>" . $tax_percent . "</" . $schema_type . ":tax_percent>" . $eol);	
			write_to( "<" . $schema_type . ":tax_region>" . $tax_region . "</" . $schema_type . ":tax_region>" . $eol);	
		}
		write_to( "<" . $schema_type . ":price>" . $price . "</" . $schema_type . ":price>" . $eol);			
		
		/*$stock_level = $db->f("stock_level");
		if ($stock_level>0) {
			write_to( "<" . $schema_type . ":pickup>true</" . $schema_type . ":pickup>" . $eol);
			write_to( "<" . $schema_type . ":quantity>" . $stock_level . "</" . $schema_type . ":quantity>" . $eol);	
		} else {
			write_to( "<" . $schema_type . ":pickup>false</" . $schema_type . ":pickup>" . $eol);
		}*/
		
		/*$weight = $db->f('weight');
		$weight_measure = get_setting_value($settings, "weight_measure", "");
		if ($weight) {
			if ($weight_measure) {
				write_to( "<" . $schema_type . ":weight>" . $weight . " " . $weight_measure . "</" . $schema_type . ":weight>" . $eol);		
			} else {
				write_to( "<" . $schema_type . ":weight>" . $weight . "</" . $schema_type . ":weight>" . $eol);
			}
		}*/
		
		$issue_date = $db->f('issue_date');
		if ($issue_date) {
			$tmp =  explode('-', $issue_date);
			if (strlen($tmp[0]))
				write_to( "<" . $schema_type . ":year>" . $tmp[0] . "</" . $schema_type . ":year>" . $eol);			
		}
		
		$sql  = " SELECT a.attribute_name, a.attribute_type, a.value_type, f.feature_value FROM (" . $table_prefix . "features f ";
		$sql .= " INNER JOIN " . $table_prefix . "google_base_attributes a ON a.attribute_id=f.google_base_attribute_id)";
		$sql .= " WHERE f.item_id=" . $db->tosql($item_id, INTEGER);
		$dbd->query($sql);
		while ($dbd->next_record()) {	
			$attribute_name = $dbd->f('attribute_name');
			$attribute_type = $dbd->f('attribute_type');
			$value_type     = $dbd->f('value_type');
			$feature_value  = $dbd->f('feature_value');
			if ($attribute_name && $attribute_type && $feature_value) {
				if ($attribute_type == 'g') {
					write_to( "<" . $schema_type . ":" . $attribute_name . "><![CDATA[" . xml_rep($feature_value) . "]]></" . $schema_type . ":" . $attribute_name . ">" . $eol);
				} else {
					write_to( "<" . $schema_type . ":" . $attribute_name . " type='" . $value_type . "'><![CDATA[" . xml_rep($feature_value) . "]]></" . $schema_type . ":" . $attribute_name . ">" . $eol);		
				}
			}
		}	
		
		write_to("</item>" . $eol);
	}

	
	
	write_to("</channel>" . $eol . "</rss>");
		
	if ($write_to_file) {
		fclose($fp);
		$conn_id = ftp_connect("uploads.google.com", 21, 5);
		if (!$conn_id) {
			echo COULDNOT_CONNECT_GOOGLE_MSG ."<br/>";
			echo DATA_FEED_SUBMIT_MSG_MSG . "<br/>";
			echo "<a href='" . $google_base_save_path . $google_base_filename . "'>" . DOWNLOAD_BUTTON  . $google_base_save_path . $google_base_filename . "</a>";
			exit;
		}
		
		$login_result = ftp_login($conn_id, $google_base_ftp_login, $google_base_ftp_password);
		if (!$login_result) {
			echo COULDNOT_CONNECT_GOOGLE_MSG ."<br/>";
			echo DATA_FEED_SUBMIT_MSG_MSG . "<br/>";
			echo "<a href='" . $google_base_save_path . $google_base_filename . "'>" . DOWNLOAD_BUTTON  . $google_base_save_path . $google_base_filename . "</a>";
			ftp_close($conn_id);
			exit;
		}
		ftp_pasv($conn_id, true);	
		
		$upload = ftp_put($conn_id, $google_base_filename, $google_base_save_path . $google_base_filename, FTP_BINARY);
		if (!$upload) {
			echo FTP_UPLOAD_FAILED_MSG ."<br/>";
			echo DATA_FEED_SUBMIT_MSG_MSG . "<br/>";
			echo "<a href='" . $google_base_save_path . $google_base_filename . "'>".DOWNLOAD_BUTTON . $google_base_save_path . $google_base_filename . "</a>";
		} else {
			echo FTP_UPLOAD_SUCCEED_MSG;
		}	
		ftp_close($conn_id);	
	}
	
	function xml_rep($string)
	{
		global $google_base_encoding;

		if (strtolower(CHARSET) == strtolower($google_base_encoding)) {			
			return $string;
		} elseif(function_exists("iconv")) {
			$string = iconv(CHARSET, $google_base_encoding, $string);			
			return $string;
		} else {
			$string = htmlentities($string, ENT_QUOTES, CHARSET);  
			$string = html_entity_decode($string, ENT_QUOTES, $google_base_encoding);
			return $string;
		}
	}
	
	function write_to($xml) {
		global $write_to_file, $fp;
		if ($write_to_file) {
			fwrite($fp, $xml);
		} else {
			echo $xml;
		}
	}
?>
	