<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_delete.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("products_categories");


	$operation = get_param("operation");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_products_delete.html");
	$t->set_var("admin_products_delete_href", "admin_products_delete.php");
	$t->set_var("admin_items_list_href",      "admin_items_list.php");

	$return_page = "admin_items_list.php";

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete")
		{
			// tables with item_id: items_downloads, orders_items, releases, reviews
			$products = array();
			$sql = " SELECT item_id FROM " . $table_prefix ."items ";
			$db->query($sql);
			while ($db->next_record()) {
				$products[] = $db->f("item_id");
			}

			for ($i = 0; $i < sizeof($products); $i++) {
				$item_id = $products[$i];

				$properties_ids = array();
				$sql = " SELECT property_id FROM " . $table_prefix ."items_properties WHERE item_id=" . $db->tosql($item_id, INTEGER);		
		  
				$db->query($sql);
				while ($db->next_record()) {
					$properties_ids[] = $db->f("property_id");
				}
				for($pi = 0; $pi < sizeof($properties_ids); $pi++) {
					$db->query("DELETE FROM " . $table_prefix . "items_properties_values WHERE property_id=" . $db->tosql($properties_ids[$pi], INTEGER));		
				}
				$db->query("DELETE FROM " . $table_prefix . "reviews WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "features WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "items_properties WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "items_categories WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "items_related WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "items_images WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "items_accessories WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($item_id, INTEGER));		

			}

			header("Location: " . $return_page);
			exit;
		}
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>