<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  shipping_functions.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


function get_shipping_types($delivery_country_id, $delivery_state_id, $delivery_site_id, $user_type_id, $delivery_items)
{
	global $db, $table_prefix, $country_code, $postal_code, $order_total, $state_code, $r, $errors;
	global $goods_total_full, $total_quantity, $weight_total;
	global $shipping_packages, $shipping_items_total, $shipping_weight, $shipping_quantity;

	$shipping_types = array(); // return this array with available delivery methods

	$shipping_packages = array(); 
	$goods_total_full = 0; $shipping_items_total = 0; $total_quantity = 0; $weight_total = 0; $shipping_weight = 0; $shipping_quantity = 0;
	foreach ($delivery_items as $id => $item) {
		if (isset($item["full_price"])) {
			$price = $item["full_price"];
		} else {
			$price = $item["price"];
		}
		$quantity = $item["quantity"];
		$packages_number = $item["packages_number"];
		if ($packages_number < 1) { $packages_number = 1; }
		if (isset($item["full_weight"])) {
			$weight = $item["full_weight"];
		} else {
			$weight = $item["weight"];
		}
		$width = $item["width"];
		$height = $item["height"];
		$length = $item["length"];
		$is_shipping_free = $item["is_shipping_free"];
		$shipping_cost = $item["shipping_cost"];

		$item_total = $price * $quantity;
		$weight_total += ($weight * $quantity * $packages_number);
		$total_quantity += $quantity;
		$goods_total_full += $item_total;
		if (!$is_shipping_free) {
			$shipping_quantity += $quantity;
			$shipping_items_total += ($shipping_cost * $quantity); 
			$shipping_weight += ($weight * $quantity * $packages_number);
			$shipping_packages[$id] = array(
				"price" => $price,
				"quantity" => $quantity,
				"packages" => $packages_number,
				"weight" => $weight,
				"width" => $item["width"],
				"height" => $item["height"],
				"length" => $item["length"],
			);
		}
	}

	$shipping_modules = array();
	$sql  = " SELECT * FROM " . $table_prefix . "shipping_modules ";
	$sql .= " WHERE is_active=1 ";
	$db->query($sql);
	while ($db->next_record()) {
		$shipping_module_id   = $db->f("shipping_module_id");
		$shipping_module_name = $db->f("shipping_module_name");
		$is_external          = $db->f("is_external");
		$php_external_lib     = $db->f("php_external_lib");
		$external_url         = $db->f("external_url");
		$cost_add_percent     = $db->f("cost_add_percent");
		$shipping_modules[]   = array($shipping_module_id, $shipping_module_name, $is_external, $php_external_lib, $external_url, $cost_add_percent);
	}

	for ($sm = 0; $sm < sizeof($shipping_modules); $sm++) {
		list ($shipping_module_id, $shipping_module_name, $is_external, $php_external_lib, $external_url, $cost_add_percent) = $shipping_modules[$sm];
		$module_shipping = array();
		$sql  = " SELECT st.shipping_type_id, st.shipping_type_code, st.shipping_type_desc, st.shipping_time, ";
		$sql .= " st.cost_per_order, st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable ";
		$sql .= " FROM ((((";
		$sql .= $table_prefix . "shipping_types st ";
		$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_countries stc ON st.shipping_type_id=stc.shipping_type_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_states stt ON st.shipping_type_id=stt.shipping_type_id) ";
		if ($delivery_site_id) {
			$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_sites s ON st.shipping_type_id=s.shipping_type_id) ";
		} else {
			$sql .= ")";
		}
		if (strlen($user_type_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_users ut ON st.shipping_type_id=ut.shipping_type_id) ";
		} else {
			$sql .= ")";
		}
		$sql .= " WHERE st.is_active=1 ";
		$sql .= " AND (st.countries_all=1 OR stc.country_id=" . $db->tosql($delivery_country_id, INTEGER, true, false) . ") ";
		$sql .= " AND (st.states_all=1 OR stt.state_id=" . $db->tosql($delivery_state_id, INTEGER, true, false) . ") ";
		$sql .= " AND (st.min_weight IS NULL OR st.min_weight<=" . $db->tosql($shipping_weight, NUMBER) . ") ";
		$sql .= " AND (st.max_weight IS NULL OR st.max_weight>=" . $db->tosql($shipping_weight, NUMBER) . ") ";
		$sql .= " AND (st.min_goods_cost IS NULL OR st.min_goods_cost<=" . $db->tosql($goods_total_full, NUMBER) . ") ";
		$sql .= " AND (st.max_goods_cost IS NULL OR st.max_goods_cost>=" . $db->tosql($goods_total_full, NUMBER) . ") ";
		$sql .= " AND (st.min_quantity IS NULL OR st.min_quantity<=" . $db->tosql($shipping_quantity, NUMBER) . ") ";
		$sql .= " AND (st.max_quantity IS NULL OR st.max_quantity>=" . $db->tosql($shipping_quantity, NUMBER) . ") ";
		$sql .= " AND st.shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER);
		if ($delivery_site_id) {
			$sql .= " AND (st.sites_all=1 OR s.site_id=" . $db->tosql($delivery_site_id, INTEGER, true, false) . ")";
		} else {
			$sql .= " AND st.sites_all=1 ";
		}
		if (strlen($user_type_id)) {
			$sql .= " AND (st.user_types_all = 1 OR ut.user_type_id=" . $db->tosql($user_type_id, INTEGER, true, false) . ")";
		} else {
			$sql .= " AND st.user_types_all = 1 ";
		}
		$sql .= " GROUP BY st.shipping_type_id, st.shipping_order, st.shipping_type_code, st.shipping_type_desc, st.shipping_time, ";
		$sql .= " st.cost_per_order, st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable ";
		$sql .= " ORDER BY st.shipping_order, st.shipping_type_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$row_shipping_type_id = $db->f("shipping_type_id");
			$row_shipping_type_code = $db->f("shipping_type_code");
			$row_shipping_type_desc = get_translation($db->f("shipping_type_desc"));
			$row_shipping_time = $db->f("shipping_time");
			$cost_per_order = $db->f("cost_per_order");
			$cost_per_product = $db->f("cost_per_product");
			$cost_per_weight = $db->f("cost_per_weight");
			$row_tare_weight = $db->f("tare_weight");
			$row_shipping_taxable = $db->f("is_taxable");
			$row_shipping_cost = ($shipping_items_total + $cost_per_order + ($cost_per_product * $shipping_quantity) + ($cost_per_weight * ($shipping_weight + $row_tare_weight)));					
			$shipping_type = array($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $row_shipping_cost, $row_tare_weight, $row_shipping_taxable, $row_shipping_time);
			$module_shipping[] = $shipping_type;
			if (!$is_external) {
				$shipping_types[] = $shipping_type;
			}
		}

		if ($is_external && strlen($php_external_lib) && sizeof($module_shipping) > 0) {
			$module_params = array();
			$sql  = " SELECT * FROM " . $table_prefix . "shipping_modules_parameters ";
			$sql .= " WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER);
			$sql .= " AND not_passed<>1 ";
			$db->query($sql);
			while ($db->next_record()) {
				$param_name = $db->f("parameter_name");
				$param_source = $db->f("parameter_source");
				$module_params[$param_name] = $param_source;
			}
			include($php_external_lib);
		}
		if ($cost_add_percent && $shipping_types) {
			for($i=0, $ic = count($shipping_types); $i<$ic; $i++) {
				$shipping_types[$i][3] = $shipping_types[$i][3] * (1 + $cost_add_percent/100);
			}
		}

	}

	// add default shipping type in case if there are no methods available
	if (sizeof($shipping_types) == 0 && $shipping_items_total > 0) {
		$shipping_types[] = array(0, "", PROD_SHIPPING_MSG, $shipping_items_total, 0, 1, 0);
	}

	return $shipping_types;
}


?>