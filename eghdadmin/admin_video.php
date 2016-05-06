<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_video.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once("./admin_common.php");

	check_admin_security();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_video.html");

	$file = get_param("file");

	$t->set_var("stream_video",$file);
	
	if (preg_match("/.flv$/i", $file)){
		$t->parse("flash_player_block",false);
		$t->set_var("windows_media_block","");
	} else {
		$t->set_var("flash_player_block","");
		$t->parse("windows_media_block",false);
	}
	
	$t->pparse("main");

?>