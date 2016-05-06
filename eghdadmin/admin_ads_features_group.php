<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_features_group.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("ads");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_ads_features_group.html");

	$t->set_var("admin_href",                 "admin.php");
	$t->set_var("admin_ads_href",             "admin_ads.php");
	$t->set_var("admin_ads_features_groups_href", "admin_ads_features_groups.php");
	$t->set_var("admin_ads_features_group_href",  "admin_ads_features_group.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SPECIFICATION_GROUP_MSG, CONFIRM_DELETE_MSG));

	$group_id = get_param("group_id");
	$operation = get_param("operation");
	$max_order = 1;
	if (!$operation && !$group_id) {
		$sql = " SELECT MAX(group_order) FROM " . $table_prefix . "ads_features_groups ";
		$db->query($sql);
		if ($db->next_record()) {
			$max_order = $db->f(0);
			$max_order++;
		}
	}

	$r = new VA_Record($table_prefix . "ads_features_groups");
	$r->return_page = "admin_ads_features_groups.php";
	
	$r->add_where("group_id", INTEGER);
	$r->add_textbox("group_order", INTEGER, GROUP_ORDER_MSG);
	$r->change_property("group_order", REQUIRED, true);
	$r->change_property("group_order", DEFAULT_VALUE, $max_order);
	$r->add_textbox("group_name", TEXT, GROUP_NAME_MSG);
	$r->change_property("group_name", REQUIRED, true);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
