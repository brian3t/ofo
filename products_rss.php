<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  products_rss.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	
	$user_id = get_session("session_user_id");	
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");

	$currency = get_currency();
	$category_id = get_param("category_id");
	if (!strlen($category_id)){
		$category_id = 0;
	}
	$eol = get_eol();
	
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	
	$sql = " SELECT friendly_url, category_name, show_sub_products FROM " . $table_prefix . "categories WHERE category_id = ".$db->tosql($category_id, INTEGER);
	$db->query($sql);
	$show_sub_products = 0;
	if ($friendly_urls && $category_id != 0){
		if ($db->next_record()){
			$show_sub_products = $db->f("show_sub_products");
			$category_friendly_url = $db->f("friendly_url");
			$category_name = $db->f("category_name");
		} else {
			$category_friendly_url = "products.php";
			$category_name = "All products";
			$category_id = 0;
			$show_sub_products = 1;
		}
	} else if ($friendly_urls && $category_id == 0){
		$category_friendly_url = "products.php";
		$category_name = "All products";
		$show_sub_products = 0;
	} else if ($category_id == 0){
		$category_name = "All products";
		$show_sub_products = 0;
	} else {
		if ($db->next_record()){
			$show_sub_products = $db->f("show_sub_products");
			$category_friendly_url = $db->f("friendly_url");
			$category_name = $db->f("category_name");
		}
	}
	
	$category_ids = "";
	if ($show_sub_products == 1){
		if ($category_id == 0){
			$sql = "SELECT category_id, friendly_url FROM " . $table_prefix . "categories WHERE category_path like '%".$db->tosql($category_id,INTEGER).",%' AND is_showing = 1 ";
		} else {
			$sql = "SELECT category_id, friendly_url FROM " . $table_prefix . "categories WHERE category_path like '%,".$db->tosql($category_id,INTEGER).",%' AND is_showing = 1 ";
		}
		$db->query($sql);
		$category_ids = intval($category_id);
		$sub_category_friendly_url[$category_id] = $category_friendly_url;
		if ($db->next_record()){
			do {
				$category_ids .= ",".$db->f("category_id");
				$sub_category_friendly_url[$db->f("category_id")] = $db->f("friendly_url");
			} while ($db->next_record());
		}
	} else {
		$sub_category_friendly_url[$category_id] = $category_friendly_url;
	}
	
	$is_xml = false;
	
	$sql  = " SELECT i.item_id, i.item_type_id, i.item_code, i.item_name, i.friendly_url, i.short_description, ";
	$sql .= " i.small_image, i.small_image_alt, i.big_image, i.big_image_alt, i.price, i.is_sales, i.sales_price, ";
	$sql .= " i.is_points_price, i.points_price, ";
	$sql .= " i.buy_link, i.is_sales, i.full_description, ";
	$sql .= " i.manufacturer_code, ";
	$sql .= " i.issue_date, ";
	// new product db
	$sql .= " ic.category_id, c.category_name, c.short_description AS category_short_description, c.full_description AS category_full_description ";
	$sql .= " FROM ((";
	$sql .= $table_prefix . "items i ";
	if ($show_sub_products == 1){
		$sql .= " INNER JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id AND ic.category_id IN (".$db->tosql($category_ids, INTEGERS_LIST).")) ";
		$sql .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id )";
	} else {
		$sql .= " INNER JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id AND ic.category_id = ".$db->tosql($category_id,INTEGER).") ";
		$sql .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id ) ";
	}
	$sql .= " WHERE i.is_showing = 1 ";
	$sql .= " GROUP BY i.item_id ";
	$sql .= " ORDER BY i.item_order, i.item_id ";
	$db->query($sql);
	if ($db->next_record()){
		$c = 0;
		do{
			$products_rss[$c]["item_id"] = $db->f("item_id");
			$products_rss[$c]["item_name"] = $db->f("item_name");
			$products_rss[$c]["small_image"] = $db->f("small_image");
			$products_rss[$c]["category_id"] = $db->f("category_id");
			$products_rss[$c]["category_name"] = $db->f("category_name");
			$products_rss[$c]["price"] = $db->f("price");
			$products_rss[$c]["is_sales"] = $db->f("is_sales");
			$products_rss[$c]["sales_price"] = $db->f("sales_price");
			$products_rss[$c]["small_image_alt"] = $db->f("small_image_alt");
			$products_rss[$c]["short_description"] = $db->f("short_description");
			$products_rss[$c]["full_description"] = $db->f("full_description");
			$products_rss[$c]["friendly_url"] = $db->f("friendly_url");
			$c++;
			$is_xml = true;
		} while ($db->next_record());

		if ($friendly_urls){
			if ($category_id == 0){
				$category_link = $settings["site_url"].$category_friendly_url;
			} else {
				if (strlen($category_friendly_url)){
					$category_link = $settings["site_url"].$category_friendly_url.$friendly_extension;
				} else {
					$category_link = $settings["site_url"]."products.php?category_id=".$category_id;
				}
			}
		} else {
			if ($category_id == 0){
				$category_link = $settings["site_url"]."products.php";
			} else {
				$category_link = $settings["site_url"]."products.php?category_id=".$category_id;
			}
		}
		
		$xml  = "<?xml version=\"1.0\" encoding=\"".CHARSET."\" ".chr(63).">" . $eol;
		$xml .= "<rss version=\"2.0\">" . $eol;
		$xml .= "<channel>" . $eol;
		$xml .= "<title>".xml_get_translation($category_name)."</title>" . $eol;
		$xml .= "<link><![CDATA[".xml_entities($category_link)."]]></link>" . $eol;
		$xml .= "<description><![CDATA[".xml_get_translation($category_name)."]]></description>" . $eol;
		$xml .= "<language>".$language_code."</language>" . $eol;
		$xml .= "<copyright>Copyright 2008</copyright>" . $eol;
		
		for ($i = 0; $i < $c; $i++){
			
			if ($friendly_urls){
				if ($products_rss[$i]["category_id"] == 0){
					$sub_category_link = $settings["site_url"].$category_friendly_url;
				} else {
					if (strlen($sub_category_friendly_url[$products_rss[$i]["category_id"]])){
						$sub_category_link = $settings["site_url"].$sub_category_friendly_url[$products_rss[$i]["category_id"]].$friendly_extension;
					} else {
						$sub_category_link = $settings["site_url"]."products.php?category_id=".$products_rss[$i]["category_id"];
					}
				}
			} else {
				if ($products_rss[$i]["category_id"] == 0){
					$sub_category_link = $settings["site_url"]."products.php";
				} else {
					$sub_category_link = $settings["site_url"]."products.php?category_id=".$products_rss[$i]["category_id"];
				}
			}
			
			$link = "";
			if ($friendly_urls){
				if (strlen($products_rss[$i]["friendly_url"])){
					$link = $settings["site_url"].$products_rss[$i]["friendly_url"].$friendly_extension;
				} else {
					$link = $settings["site_url"]."product_details.php?category_id=".$products_rss[$i]["category_id"]."&item_id=".$products_rss[$i]["item_id"];
				}
			} else {
				$link = $settings["site_url"]."product_details.php?category_id=".$products_rss[$i]["category_id"]."&item_id=".$products_rss[$i]["item_id"];
			}
			
			if ($category_id == $products_rss[$i]["category_id"]){
				$sub_category_name = $category_name;
			} else {
				$sub_category_name = $products_rss[$i]["category_name"];
			}
			
			if ($products_rss[$i]["is_sales"] == 0){
				$price = $currency["left"].$products_rss[$i]["price"].$currency["right"];
			} else {
				if ($products_rss[$i]["sales_price"] > 0){
					$price = $currency["left"].$products_rss[$i]["sales_price"].$currency["right"];
				} else {
					$price = $currency["left"].$products_rss[$i]["price"].$currency["right"];
				}
			}
			
			$xml .= "<item>" . $eol;
		    $xml .= "<title><![CDATA[".xml_get_translation($products_rss[$i]["item_name"])."]]></title>" . $eol;
		    $xml .= "<description><![CDATA[<img align=\"left\" vspace=\"5\" hspace=\"10\" ";
			if (preg_match("/http:\/\//",$products_rss[$i]["small_image"])){
				$xml .= "src=\"".xml_entities($products_rss[$i]["small_image"]);
			} else {
				$xml .= "src=\"".xml_entities($settings["site_url"].$products_rss[$i]["small_image"]);
			}
			if (strlen($products_rss[$i]["short_description"])){
				$xml .= "\" alt = \"".xml_get_translation($products_rss[$i]["small_image_alt"])."\"> Price: ".$price." - <a href=\"".$settings["site_url"]."product_details.php?item_id=".$products_rss[$i]["item_id"]."&cart=ADD\">Buy</a> ".xml_get_translation($products_rss[$i]["short_description"])." <br clear=\"all\">]]></description>" . $eol;
			} else {
				$xml .= "\">  Price: ".$price." - <a href=\"".$settings["site_url"]."product_details.php?item_id=".$products_rss[$i]["item_id"]."&cart=ADD\">Buy</a> ".xml_get_translation($products_rss[$i]["full_description"])." <br clear=\"all\">]]></description>" . $eol;
			}
		    $xml .= "<pubDate><![CDATA[".va_date()."]]></pubDate>" . $eol;
			$xml .= "<category domain=\"".xml_entities($sub_category_link)."\">".xml_get_translation($sub_category_name)."</category>" . $eol;
			$xml .= "<link>".xml_entities($link)."</link>" . $eol;
		    $xml .= "<source url=\"".xml_entities($settings["site_url"])."\">".xml_get_translation($sub_category_name)."</source>" . $eol;
			$xml .= "</item>" . $eol;
		}
		$xml .= "\t</channel>" . $eol;
		$xml .= "</rss>" . $eol;
		
	} else {
		
	}
	
	if ($is_xml) {
		header("Content-Type: text/xml");
		header("Pragma: no-cache");
		echo $xml;
	} else {
		echo "";
	}
	
	function xml_get_translation($string) {
		return xml_entities(get_translation($string));
	}

	function xml_entities($string) {
		return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
	}
	
?>