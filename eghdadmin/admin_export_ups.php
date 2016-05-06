<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_export_ups.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");

	include_once("./admin_common.php");

	check_admin_security();

	$eol = get_eol();
	$id = get_param("id");
	$ids = get_param("ids");
	$s_on = get_param("s_on");
	$s_ne = get_param("s_ne");
	$s_pn = get_param("s_pn");
	$s_sd = get_param("s_sd");
	$s_ed = get_param("s_ed");
	$s_os = get_param("s_os");
	$s_cc = get_param("s_cc");
	$s_sc = get_param("s_sc");
	$s_ex = get_param("s_ex");

	$sql  = "SELECT smp.parameter_source ";
	$sql .= "FROM ". $table_prefix ."shipping_modules sm ";
	$sql .= "LEFT JOIN ". $table_prefix ."shipping_modules_parameters smp ON sm.shipping_module_id = smp.shipping_module_id ";
	$sql .= "WHERE sm.shipping_module_name LIKE '%UPS%' AND smp.parameter_name = 'PackagingType'";
	$db->query($sql);
	if ($db->next_record()) {
		switch ($db->f("parameter_source")){
		case "01":
			$service_level="EE";
			break;
		case "02":
			$service_level="CP";
			break;
		case "03":
			$service_level="TB";
			break;
		case "04":
			$service_level="PK";
			break;
		case "21":
			$service_level="EB";
			break;
		case "24":
			$service_level="25";
			break;
		case "25":
			$service_level="10";
			break;
		default:
			$service_level="";
			break;
		}

	} else {
		$service_level="";
	}
	$sql_where = "";

if (strlen($id)) {
	$sql_where .= " AND o.order_id>" . $db->tosql($id, INTEGER);
} else if (strlen($ids)) {
	$sql_where .= " AND o.order_id IN (" . $db->tosql($ids, TEXT, false) . ")";
} else {

	if (preg_match("/^(\d+)(,\d+)*$/", $s_on))	{
		$sql_where .= " AND (o.order_id IN (" . $db->tosql($s_on, TEXT, false) . ") ";
		$sql_where .= " OR o.transaction_id=" . $db->tosql($s_on, TEXT) . ") ";
	} else {
		$sql_where .= " AND o.transaction_id=" . $db->tosql($s_on, TEXT) . " ";
	}
	  
	if(strlen($s_ne)) {
		$s_ne_sql = $db->tosql($s_ne, TEXT, false);
		$sql_where .= " AND (o.email LIKE '%" . $s_ne_sql . "%'";
		$sql_where .= " OR o.name LIKE '%" . $s_ne_sql . "%'";
		$sql_where .= " OR o.first_name LIKE '%" . $s_ne_sql . "%'";
		$sql_where .= " OR o.last_name LIKE '%" . $s_ne_sql . "%')";
	}
	  
	if(strlen($s_pn)) {
		$sql_where .= " AND (oi.item_name LIKE '%" . $db->tosql($s_pn, TEXT, false) . "%'";
		$sql_where .= " OR oi.item_properties LIKE '%" . $db->tosql($s_pn, TEXT, false) . "%')";
	}
	  
	if(strlen($s_sd)) {
		$s_sd_value = parse_date($s_sd, $date_edit_format, $date_errors);
		$sql_where .= " AND o.order_placed_date>=" . $db->tosql($s_sd_value, DATE);
	}
	  
	if(strlen($s_ed)) {
		$end_date = parse_date($s_ed, $date_edit_format, $date_errors);
		$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
		$sql_where .= " AND o.order_placed_date<" . $db->tosql($day_after_end, DATE);
	}
	  
	if(strlen($s_os)) {
		$sql_where .= " AND o.order_status=" . $db->tosql($s_os, INTEGER);
	}

	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_info' ";
	//$sql .= " AND setting_name LIKE '%country_code%'";
	$db->query($sql);
	while($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}
	  
	if(strlen($s_cc)) {
		if ($order_info["show_delivery_country_code"] == 1) {
			$sql_where .= " AND o.delivery_country_code=" . $db->tosql($s_cc, TEXT);
		} else if ($order_info["show_country_code"] == 1) {
			$sql_where .= " AND o.country_code=" . $db->tosql($s_cc, TEXT);
		} 
	}

	if(strlen($s_sc)) {
		if ($order_info["show_delivery_state_code"] == 1) {
			$sql_where .= " AND o.delivery_state_code=" . $db->tosql($s_sc, TEXT);
		} else if ($order_info["show_state_code"] == 1) {
			$sql_where .= " AND o.state_code=" . $db->tosql($s_sc, TEXT);
		} 
	}

	if (strlen($s_ex)) {
		$sql_where .= ($s_ex == 1) ? " AND o.is_exported=1 " : " AND (o.is_exported<>1 OR o.is_exported IS NULL) ";
	}
}
		$separator=";";
	$data="";
	$field = array();
	$order_id = array();

	$exported_fields  = "o.order_id, o.name, o.first_name, o.last_name, o.address1, o.address2, o.city, o.zip, o.email, ";
	$exported_fields .= "o.delivery_first_name, o.delivery_last_name, o.delivery_address1, o.delivery_address2, o.delivery_city, ";
	$exported_fields .= "o.delivery_zip, o.delivery_country_code, o.delivery_state_code, o.delivery_email, ";
	$exported_fields .= "o.delivery_daytime_phone, o.daytime_phone, ";
	$exported_fields .= "oi.item_id, oi.discount_amount, oi.weight, oi.quantity, oi.item_name";

	$sql  = "SELECT o.order_id, o.name, o.first_name, o.last_name, o.address1, o.address2, o.city, o.zip, o.email, ";
	$sql .= "o.delivery_first_name, o.delivery_last_name, o.delivery_address1, o.delivery_address2, o.delivery_city, ";
	$sql .= "o.delivery_zip, o.delivery_country_code, o.delivery_state_code, o.delivery_email, ";
	$sql .= "o.delivery_daytime_phone, o.daytime_phone, ";
	$sql .= "oi.item_id, oi.discount_amount, oi.weight, oi.quantity, oi.item_name ";
	$sql .= "FROM ". $table_prefix ."orders o, ". $table_prefix ."orders_items oi ";
	$sql .= "WHERE oi.order_id=o.order_id ";
	$sql .= $sql_where;

	$db->query($sql);
	while ($db->next_record()) {
		if (strlen($db->f("delivery_first_name")) || strlen($db->f("delivery_last_name"))) {
			$name=$db->f("delivery_first_name")." ".$db->f("delivery_last_name");
		} else if (strlen($db->f("first_name")) || strlen($db->f("last_name"))) {
			$name=$db->f("first_name")." ".$db->f("last_name");
		} else if (strlen($db->f("name"))) {
			$name=$db->f("name");
		} else {
			$name="";
		}
		$field[0]=$name;
		$field[1]=$name;
		$field[2]=$db->f("delivery_address1");
		$field[3]=$db->f("delivery_address2");
		$field[4]="";
		$field[5]=$db->f("delivery_city");
		$field[6]=$db->f("delivery_country_code");
		$field[7]=$db->f("delivery_zip");
		if ($db->f("delivery_country_code")=="US") {
			$field[8]=$db->f("delivery_state_code");
		}
		if (strlen($db->f("delivery_daytime_phone"))) {
			$daytime_phone=$db->f("delivery_daytime_phone");
		} else if (strlen($db->f("daytime_phone"))) {
			$daytime_phone=$db->f("daytime_phone");
		} else {
			$daytime_phone="";
		}
		$field[9]=$daytime_phone;
		$field[10]="";
		$field[11]=$service_level;
		$field[12]="1";
		if (is_numeric($db->f("weight")) && $db->f("weight")>0.1) {
			if (is_numeric($db->f("quantity"))) {
				$weight=$db->f("weight")*$db->f("quantity");
			} else {
				$weight=$db->f("weight");
			}
		} else {
			$weight=0.1;
		}
		$field[13]=$weight;
		$field[14]=get_top_category($db->f("item_id"));
		$field[15]=$db->f("order_id");
		$field[16]="";
		$field[17]="CP";
		$field[18]="DP";
		$field[19]="1";
		if (strlen($db->f("delivery_email"))) {
			$email=$db->f("delivery_email");
		} else if (strlen($db->f("email"))) {
			$email=$db->f("email");
		} else {
			$email="";
		}
		$field[20]=$email;
		$field[21]=$name;

		$result_line="";
		foreach ($field as $value) {
			$result_line .= $value . $separator;
		}
		$data.=$result_line.$eol;
		$order_id[]=$db->f("order_id");
	}
	if (strlen($data)) {

		$sql = " UPDATE " . $table_prefix . "admins SET exported_order_fields=" . $db->tosql($exported_fields, TEXT);
		$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$db->query($sql);

		$max_id = 0;
		$ids = "";
		foreach ($order_id as $value) {
			if ($value > $max_id) { $max_id = $value; }
			$ids .= $value . ",";
		}
		$ids = substr($ids, 0, -1);
		$db->query("UPDATE " . $table_prefix . "orders SET is_exported=1 WHERE order_id IN (" . $db->tosql($ids, TEXT, false) . ")");

		$sql = " UPDATE " . $table_prefix . "admins SET exported_order_id=" . $db->tosql($max_id, INTEGER);
		$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$db->query($sql);

		$filename = "export_ups_" . date("Y-m-d-h-i-s") . ".csv";
		header("Pragma: private");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream"); 
		header("Content-Length: " . strlen($data));
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Transfer-Encoding: binary");

		echo $data;
		exit;
	}
	
function get_top_category($item_id)
{
	global $db, $table_prefix;

	$dbd = new VA_SQL();
	$dbd->DBType       = $db->DBType;
	$dbd->DBDatabase   = $db->DBDatabase;
	$dbd->DBUser       = $db->DBUser;
	$dbd->DBPassword   = $db->DBPassword;
	$dbd->DBHost       = $db->DBHost;
	$dbd->DBPort       = $db->DBPort;
	$dbd->DBPersistent = $db->DBPersistent;

	$sql  = "SELECT ic.item_id, c.category_id, c.parent_category_id, c.category_path, c.category_name ";
	$sql .= "FROM (".$table_prefix."categories c ";
	$sql .= "LEFT JOIN ".$table_prefix."items_categories ic ON ic.category_id=c.category_id) ";
	$sql .= "WHERE ic.item_id = ".$db->tosql($item_id, INTEGER);
	$dbd->query($sql);
	if ($dbd->next_record()) {
		$parent_category_id = $dbd->f("parent_category_id");
		$category_path = $dbd->f("category_path");
		if($parent_category_id == 0) {
			return $dbd->f("category_name");
		} else {
			$categories_ids = explode(",", $category_path);
			$sql  = "SELECT category_name ";
			$sql .= "FROM ".$table_prefix."categories ";
			$sql .= "WHERE category_id = ".$categories_ids[1];
			$dbd->query($sql);
			if ($dbd->next_record()) {
				return $dbd->f("category_name");
			} else {
				return "Top";
			}
		}
	} else {
		return "Top";
	}
}
?>