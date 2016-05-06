<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_price_code.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	
	check_admin_security("static_prices");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_price_code.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_price_code_href", "admin_price_code.php");
	$t->set_var("admin_price_codes_href", "admin_price_codes.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", PRICE_CODE_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "prices");
	$r->return_page  = "admin_price_codes.php";

	$r->add_where("price_id", INTEGER);

	$r->add_textbox("price_title", TEXT, PRICE_CODE_TITLE_MSG);
	$r->change_property("price_title", REQUIRED, true);
	$r->change_property("price_title", UNIQUE, true);
	$r->add_textbox("price_amount", NUMBER);
	$r->add_textbox("price_amount", NUMBER, PRICE_AMOUNT_MSG);
	$r->change_property("price_amount", REQUIRED, true);
	$r->add_textbox("price_description", TEXT);

	$r->events[AFTER_UPDATE] = "update_prices";
	$r->events[BEFORE_DELETE] = "delete_prices";
	
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function update_prices()
	{
		global $r, $db, $table_prefix;
		$price_id = $db->tosql($r->get_value("price_id"), INTEGER, true, false);
		$price = $db->tosql($r->get_value("price_amount"), NUMBER, true, false) ;
		
		$sql = "UPDATE " . $table_prefix. "items SET price=" . $price . " WHERE price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET trade_price=" . $price . " WHERE trade_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET buying_price=" . $price . " WHERE buying_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET properties_price=" . $price . " WHERE properties_price_id=" . $price_id;
		$db->query($sql);

		$sql = "UPDATE " . $table_prefix. "items SET trade_properties_price=" . $price . " WHERE trade_properties_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET sales_price=" . $price . " WHERE sales_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET trade_sales=" . $price . " WHERE trade_sales_id=" . $price_id;
		$db->query($sql);
	}
	
	function delete_prices()
	{
		global $r, $db, $table_prefix;
		$price_id = $db->tosql($r->get_value("price_id"), INTEGER, true, false);
		
		$sql = "UPDATE " . $table_prefix. "items SET price_id=0 WHERE price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET trade_price_id=0 WHERE trade_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET buying_price_id=0 WHERE buying_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET properties_price_id=0 WHERE properties_price_id=" . $price_id;
		$db->query($sql);

		$sql = "UPDATE " . $table_prefix. "items SET trade_properties_price_id=0 WHERE trade_properties_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET sales_price_id=0 WHERE sales_price_id=" . $price_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET trade_sales_id=0 WHERE trade_sales_id=" . $price_id;
		$db->query($sql);
	}

?>