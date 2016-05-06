<?php

function merchants($block_name)
{
	global $t, $db, $site_id, $db_type, $table_prefix;
	global $settings, $page_settings, $language_code;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls       = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension  = get_setting_value($settings, "friendly_extension", "");
	$merchants_selection = get_setting_value($page_settings, "merchants_selection", 1);
	
	$user = get_param("user");

	$t->set_file("block_body",    "block_merchants.html");
	$t->set_var("user_list_href", "user_list.php");

	$sql_params = array();
	$sql_params["brackets"] = "((";	
	if (isset($site_id)) {
		$sql_params["brackets"] .= "(";
	}
	$sql_params["join"]  = " INNER JOIN " . $table_prefix . "users u ON i.user_id=u.user_id) ";
	$sql_params["join"] .= " INNER JOIN " . $table_prefix . "user_types t ON t.type_id=u.user_type_id) ";
	if (isset($site_id)) {
		$sql_params["join"] .=	" LEFT JOIN " . $table_prefix . "user_types_sites uts ON (uts.type_id=t.type_id AND t.sites_all=0)) ";
	}	
	if (isset($site_id)) {
		$sql_params["where"] = " ( t.sites_all=1 OR uts.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
	}	
	$sql_params["order"] = " GROUP BY u.user_id ";
	if ($db_type == "access" || $db_type == "db2" || $db_type == "postgre") {
		$sql_params["order"] = ", u.company_name, u.name, u.login, u.friendly_url ";
	}
	$sql_params["order"] .= " ORDER BY u.company_name, u.name, u.login ";
	
	$merchants = VA_Products::find_all(
		"u.user_id",
		array("u.company_name", "u.name", "u.login", "u.friendly_url", "COUNT(*)"),
		$sql_params, VIEW_CATEGORIES_ITEMS_PERM
	);
	if (!$merchants) return;
	
	foreach ($merchants AS $merchant_id => $merchant) {
		$merchant_name = $merchant["u.company_name"];
		if (!strlen($merchant_name)) {
			$merchant_name = $merchant["u.name"];	
		}
		if (!strlen($merchant_name)) {
			$merchant_name = $merchant["u.login"];
		}
		if ($friendly_urls && $merchant["u.friendly_url"]) {
			$merchant_url = $merchant["u.friendly_url"] . $friendly_extension;
		} else {
			$merchant_url = "user_list.php?user=" . $merchant_id;
		}
		
		$merchant_selected = ($user == $merchant_id) ? "selected" : "";

		$merchant_products = $merchant["COUNT(*)"];
		$merchant_products = count(VA_Products::find_all_ids("i.user_id=" . $merchant_id));
		
		$t->set_var("merchant_id",   $merchant_id);
		$t->set_var("merchant_name", $merchant_name);
		$t->set_var("merchant_url",  $merchant_url);
		$t->set_var("merchant_selected", $merchant_selected);
		$t->set_var("merchant_products", $merchant_products);

		$t->sparse("merchants", true);
		$t->sparse("merchants_options", true);		
	}
	
	if ($merchants_selection == 2) {
		$t->sparse("merchants_select", false);
	} else {
		$t->sparse("merchants_list", false);
	}
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>