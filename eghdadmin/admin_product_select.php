<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_product_select.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once ("../messages/".$language_code."/cart_messages.php");

	include_once("./admin_common.php");

	check_admin_security("products_categories");

	$sw = trim(get_param("sw"));
	$form_id = get_param("form_id");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_product_select.html");
	$t->set_var("admin_product_select_href", "admin_product_select.php");
	$t->set_var("sw", htmlspecialchars($sw));
	$t->set_var("form_id", htmlspecialchars($form_id));


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_product_select.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_item_id", "1", "item_id");
	$s->set_sorter(PROD_NAME_MSG, "sorter_item_name", "2", "item_name");
	$s->set_sorter(PROD_CODE_MSG, "sorter_item_code", "3", "item_code");
	$s->set_sorter(MANUFACTURER_CODE_MSG, "sorter_manufacturer_code", "4", "manufacturer_code");
	$s->set_sorter(PRICE_MSG, "sorter_price", "5", "price");

	$where = "";
	$sa = array();
	if ($sw) {
		$sa = split(" ", $sw);
		for($si = 0; $si < sizeof($sa); $si++) {
			if ($where) { $where .= " AND "; }
			else { $where .= " WHERE "; }
			$where .= " (item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%' ";
			$where .= " OR manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
		}
	}

	$sql = " SELECT COUNT(*) FROM " . $table_prefix . "items " . $where;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);

	// set up variables for navigator
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_product_select.php");
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT item_id, item_name, item_code, manufacturer_code, price, is_sales, sales_price ";
	$sql .= "	FROM " . $table_prefix . "items ";
	$sql .= $where;
	$sql .= $s->order_by;
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record()) {
		$t->parse("products_sorters", false);
		do {
			$item_id = $db->f("item_id");
			$item_name = $db->f("item_name");
			$item_code = $db->f("item_code");
			$manufacturer_code = $db->f("manufacturer_code");
			$price = $db->f("price");
			$is_sales = $db->f("is_sales");
			$sales_price = $db->f("sales_price");
			if ($is_sales && $sales_price > 0) {
				$price = $sales_price;
			}
			$item_name_js = str_replace("'", "\\'", htmlspecialchars($item_name));

			if(is_array($sa)) {
				for($si = 0; $si < sizeof($sa); $si++) {
					$item_code = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_code);					
					$item_name = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_name);					
					$manufacturer_code = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $manufacturer_code);
				}
			}

			$t->set_var("item_id", $item_id);
			$t->set_var("item_name", $item_name);
			$t->set_var("item_name_js", $item_name_js);

			$t->set_var("item_code", $item_code);
			$t->set_var("manufacturer_code", $manufacturer_code);
			$t->set_var("price", currency_format($price));
			$t->set_var("price_js", number_format($price, 2, 	".", ""));

			$t->parse("products", true);
		} while ($db->next_record());
	}

	if (strlen($sw)) {
		$found_message = str_replace("{found_records}", $total_records, FOUND_PRODUCTS_MSG);
		$found_message = str_replace("{search_string}", htmlspecialchars($sw), $found_message);
		$t->set_var("found_message", $found_message);
		$t->parse("search_results", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");


?>