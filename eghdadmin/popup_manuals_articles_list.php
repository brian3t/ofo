<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  popup_manuals_articles_list.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/**
 * Display manuals allowed to view articles to fullfil alias article id field
 */

include_once ("./admin_config.php");
include_once ($root_folder_path . "includes/common.php");
include_once ($root_folder_path . "includes/record.php");
include_once ($root_folder_path . "includes/editgrid.php");
include_once("./admin_common.php");

check_admin_security("manual");

$t = new VA_Template($settings["admin_templates_dir"]);
$t->set_file("main","popup_manuals_articles_list.html");
// Get manuals
$manuals = array();
$sql = "SELECT * FROM ".$table_prefix."manuals_list ";
$sql .= "ORDER BY manual_title";
$db->query($sql);
if ($db->next_record()) {
	do {
		$manual_id = $db->f("manual_id");
		//$manual_title = $db->f("manual_title");
		$manuals[$manual_id] = $db->Record;
	} while($db->next_record());
}

// Get manual articles
$articles = array();

foreach ($manuals as $manual_id => $map) {
	$articles = array();
	$hierarchy = array();
	$t->set_var("article_block", "");
	
	$sql = "SELECT manual_id, article_id, parent_article_id, article_path, ";
	$sql .= "article_order, article_title, section_number ";
	$sql .= "FROM ".$table_prefix."manuals_articles ";
	$sql .= "WHERE manual_id = ".$db->tosql($manual_id, INTEGER);
	$sql .= " AND allowed_view = 1";
	$sql .= " ORDER BY article_path ASC, article_order ASC";
	
	$db->query($sql);
	
	if ($db->next_record()) {
		do {
			$article_id = $db->f("article_id");
			$parent_article_id = $db->f("parent_article_id");
			$hierarchy[$parent_article_id][] = $article_id;
			$article_path = $db->f("article_path");
			$articles[$article_id] = $db->Record;
			$article_level = strlen(preg_replace("/\d/", "", $article_path)) + 1;
			$articles[$article_id]["article_level"] = $article_level;
			$articles[$article_id]["manual_title"] = $map["manual_title"];
		} while ($db->next_record());
		
		build_articles_list(0);
		$t->set_var("no_articles", "");
	} else {
		$t->parse("no_articles", false);
	}
	
	$t->set_var("manual_id", $manual_id);
	$t->set_var("manual_title", $map["manual_title"]);
	$t->parse("manual_block", true);
}
$t->pparse("main");

/**
 * Show article of the same level. Recursive calling.
 *
 * @param integer $parent_id
 */
function build_articles_list($parent_id) {
	global $articles;
	global $hierarchy;
	global $t;
	
	if(isset($hierarchy[$parent_id])) {
		foreach ($hierarchy[$parent_id] as $article_id) {
			$article = $articles[$article_id];
			
			$t->set_var("manual_title", $article["manual_title"]);
			$t->set_var("alias_section_number", $article["section_number"]);
			$t->set_var("article_level", $article["article_level"]);
			$t->set_var("article_title", htmlentities($article["article_title"], ENT_QUOTES));
			$t->set_var("article_title_param", addslashes($article["article_title"]));
			$t->set_var("article_id", $article_id);
			$t->set_var("section_number", $article["section_number"]);
			$t->parse("article_block", true);
			if (isset($hierarchy[$article_id])) {
				build_articles_list($article_id);
			}
		}
	}
}
?>