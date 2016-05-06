<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_bookmarks.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/sorter.php");

	check_admin_security();
		
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_bookmarks.html");
	$t->set_var("admin_bookmarks", "");
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_bookmarks.php");
	$s->set_sorter(ID_MSG, "sorter_admin_bookmark_id", "1", "bookmark_id");
	$s->set_sorter(ADMIN_TITLE_MSG, "sorter_admin_title", "2", "title");
	$s->set_sorter(ADMIN_URL_SHORT_MSG, "sorter_admin_url", "3", "url");
	$s->set_sorter(IS_START_PAGE_MSG, "sorter_is_start_page", "4", "is_start_page");
	//$s->set_sorter("Default", "sorter_is_default", "4", "is_default");
	
	$admin_id = get_session("session_admin_id");
	$bookmarks = array();
	$sql  = " SELECT bookmark_id, title, url,notes,is_popup, is_start_page, image_path";
	$sql .= " FROM " . $table_prefix . "bookmarks ";
	$sql .= " WHERE admin_id=" . $db->tosql($admin_id, INTEGER);
	$sql .= $s->order_by;
	$db->query($sql);
		while ($db->next_record()) 
		{
			$admin_bookmark_id = $db->f("bookmark_id");
			$admin_bookmark_values = array("bookmark_id" => $admin_bookmark_id, "url" => $db->f("url"), "title" => $db->f("title"), "notes" => $db->f("notes"), "is_popup" => $db->f("is_popup"), "is_start_page" => $db->f("is_start_page"), "image_icon" => $db->f("image_path"));
			$admin_bookmark[$admin_bookmark_id][] = $admin_bookmark_values;
		}
		
	if(!empty($admin_bookmark))
	{
		foreach ($admin_bookmark as $admin_bookmark_id => $admin_book) 
		{
			for ($m = 0; $m < sizeof($admin_book); $m++) 
			{
				$admin_bookmark_id = $admin_book[$m]["bookmark_id"];
				$admin_url = $admin_book[$m]["url"];
				$admin_title = $admin_book[$m]["title"];
				$admin_notes = $admin_book[$m]["notes"];
				$admin_popup = $admin_book[$m]["is_popup"];
				$admin_src = $admin_book[$m]["image_icon"];
				$start_page = $admin_book[$m]["is_start_page"];
				if ($start_page == 1) 
					{
						$t->set_var("admin_start_page", YES_MSG); 
					}
					else 
					{
						$t->set_var("admin_start_page", NO_MSG);	  
					}
				$t->set_var("admin_bookmark_id",  $admin_bookmark_id);		  
  				$t->set_var("admin_url",  $admin_url);
		   	 	$t->set_var("admin_title", $admin_title);
   	 			$t->set_var("admin_notes", $admin_notes);
				$t->parse("admin_bookmarks", true);
				

			}
		}
		$t->parse("bookmarks_table", true);
	} else {
		$t->set_var("error", NO_RECORDS);
		$t->parse("errors");
	}
				
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
?>