<?php
include_once($root_folder_path . "includes/forums_functions.php");
	
function forum_search($block_name) 
{
	global $t, $category_id, $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_var("forum_href",   "forum.php");
	$t->set_file("block_body", "block_forum_search.html");
	$t->set_var("SEARCH_TITLE",  SEARCH_TITLE);
	$t->set_var("SEARCH_BUTTON", SEARCH_BUTTON);
	$t->set_var("current_category_id", htmlspecialchars($category_id));
	
	$forums = VA_Forums::find_all( 
		"fl.forum_id",
		array(
			"c.category_id", "c.category_name", "fl.forum_name"
		), 
		array (
			"order" => "ORDER BY c.category_order, c.category_id, fl.forum_order"
		),
		VIEW_TOPICS_PERM
	);
	
	$search_categories[] = array("", SEARCH_IN_ALL_MSG);

	if ($forums) {
		$last_category_id = "";
		foreach ($forums AS $forum_id => $forum_values) {
			$category_id   = $forum_values["c.category_id"];
			$category_name = get_translation($forum_values["c.category_name"]);
			$forum_name    = get_translation($forum_values["fl.forum_name"]);
			if ($last_category_id != $category_id) {
				$search_categories[] = array("c" . $category_id, $category_name);
			}
			$search_categories[] = array("f" . $forum_id, " -- " . $forum_name);
			$last_category_id = $category_id;
		}
	}

	$sf = get_param("sf");
	$sw = get_param("sw");
	if (sizeof($search_categories) > 1) {
		set_options($search_categories, $sf, "sf");
		$t->global_parse("search_categories", false, false, true);
	} else {
		$t->set_var("search_categories", "");
	}
	$t->set_var("sw", htmlspecialchars($sw));

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}
?>