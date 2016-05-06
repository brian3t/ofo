<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_custom_blocks.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	check_admin_security("custom_blocks");
	$search_block = trim(get_param("search_block"));

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_custom_blocks.html");

	$t->set_var("admin_href", "admin.php");
	
	if (strlen($search_block) > 0)
	{
		$sql_where = " WHERE block_name LIKE '%$search_block%' OR block_title LIKE '%$search_block%' OR block_desc LIKE '%$search_block%' ";
		$t->set_var("search_block",$search_block);
	}
	else 
	{
		$sql_where = "";
		$t->set_var("search_block","");
	}

	$admin_custom_block_url = new VA_URL("admin_custom_block.php", true);
	$t->set_var("admin_custom_block_new_url", $admin_custom_block_url->get_url());

	$admin_custom_block_url->add_parameter("block_id", DB, "block_id");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_custom_blocks.php");
	$s->set_sorter(ID_MSG, "sorter_block_id", "1", "block_id");
	$s->set_sorter(BLOCK_NAME, "sorter_block_name", "2", "block_name");
	$s->set_sorter(BLOCK_NOTES_MSG, "sorter_block_notes", "3", "block_notes");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_custom_blocks.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "custom_blocks" . $sql_where);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "custom_blocks " . $sql_where . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("block_id", $db->f("block_id"));
		
			$block_name = get_translation($db->f("block_name"));

			$block_notes = get_translation($db->f("block_notes"));
			if (!$block_notes) {
				$block_notes = strip_tags(get_translation($db->f("block_desc")));
			}
			$words = explode(" ", $block_notes);
			if(sizeof($words) > 9) {
				$block_notes = "";
				for ($i = 0; $i < 9; $i++) {
					$block_notes .= $words[$i] . " ";
				}
				$block_notes .= " ...";
			} 

			$t->set_var("block_name",  $block_name);
			$t->set_var("block_notes", $block_notes);

			$t->set_var("admin_custom_block_url", $admin_custom_block_url->get_url());


			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>