<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_banners_group.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("banners");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_banners_group.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_banners_href", "admin_banners.php");
	$t->set_var("admin_banners_groups_href", "admin_banners_groups.php");
	$t->set_var("admin_banners_group_href", "admin_banners_group.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", BANNERS_GROUPS_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "banners_groups");
	$r->return_page = "admin_banners_groups.php";

	$r->add_where("group_id", INTEGER);
	$r->add_checkbox("is_active", INTEGER, IS_ACTIVE_MSG);
	$r->add_textbox("group_name", TEXT, GROUP_NAME_MSG);
	$r->change_property("group_name", REQUIRED, true);
	$r->add_textbox("group_desc", TEXT);
	$r->add_hidden("page", INTEGER);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("sort_ord", TEXT);

	$r->events[BEFORE_INSERT] = "set_group_id";
	$r->events[AFTER_INSERT] = "update_group_banners";
	$r->events[AFTER_UPDATE] = "update_group_banners";
	$r->events[AFTER_DELETE] = "delete_group_banners";

	$r->process();

	$t->set_var("banners", "");
	$t->set_var("selected_banners", "");

	$sql  = " SELECT b.banner_id, b.banner_title ";
	$sql .= " FROM (" . $table_prefix . "banners b ";
	$sql .= " LEFT JOIN " . $table_prefix . "banners_assigned ba ON b.banner_id=ba.banner_id) ";
	$sql .= " WHERE (b.is_active=1 ";
  $sql .= " AND (b.max_impressions=0 OR b.max_impressions>b.total_impressions) ";
  $sql .= " AND (b.max_clicks=0 OR b.max_clicks>b.total_clicks) ";
  $sql .= " AND (b.expiry_date IS NULL OR b.expiry_date>=" . $db->tosql(va_time(), DATETIME). ")) ";
	if ($r->get_value("group_id")) {
		$sql .= " OR ba.group_id=" . $db->tosql($r->get_value("group_id"), INTEGER);
	}
	$sql .= " GROUP BY b.banner_id, b.banner_rank, b.banner_title ";
	$sql .= " ORDER BY b.banner_rank, b.banner_title ";
	$db->query($sql);
	while($db->next_record())
	{
		$t->set_var("banner_id", $db->f("banner_id"));
		$t->set_var("banner_title", str_replace("\"", "\\\"", $db->f("banner_title")));
		$t->parse("banners");
	}

	$operation = get_param("operation");
	if ($operation == "save") {
		$banners = get_param("banners");
		if($banners) {
			$selected_banners = split(",", $banners);
			for($i = 0; $i < sizeof($selected_banners); $i++) {
				$t->set_var("banner_id", strtoupper($selected_banners[$i]));
				$t->parse("selected_banners");
			}
		}
	} else if($r->get_value("group_id")) {
		$sql = " SELECT banner_id FROM " . $table_prefix . "banners_assigned WHERE group_id=" . $db->tosql($r->get_value("group_id"), INTEGER);
		$db->query($sql);
		while($db->next_record())
		{
			$t->set_var("banner_id", $db->f("banner_id"));
			$t->parse("selected_banners");
		}
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_group_id()  {
		global $db, $table_prefix, $r;
		$sql = "SELECT MAX(group_id) FROM " . $table_prefix . "banners_groups ";
		$db->query($sql);
		if($db->next_record()) {
			$group_id = $db->f(0) + 1;
			$r->change_property("group_id", USE_IN_INSERT, true);
			$r->set_value("group_id", $group_id);
		}	
	}

	function update_group_banners()  {
		global $db, $table_prefix, $r;

		$group_id = $r->get_value("group_id");
		$db->query("DELETE FROM " . $table_prefix . "banners_assigned WHERE group_id=" . $db->tosql($group_id, INTEGER));

		$banners = get_param("banners");
		if (strlen($banners)) {
			$selected_banners = split(",", $banners);
			for($i = 0; $i < sizeof($selected_banners); $i++) {
				$db->query("INSERT INTO " . $table_prefix . "banners_assigned (banner_id, group_id) VALUES (" . $db->tosql($selected_banners[$i], INTEGER) . "," . $db->tosql($group_id, INTEGER) . ")");
			}
		}
	}

	function delete_group_banners()  {
		global $db, $table_prefix, $r;
		$group_id = $r->get_value("group_id");
		$db->query("DELETE FROM " . $table_prefix . "banners_assigned WHERE group_id=" . $db->tosql($group_id, INTEGER));
	}

?>