<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  friendly_functions.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$friendly_tables = array(
		$table_prefix . "categories" => array("category_id", "category_name"), 
		$table_prefix . "items" => array("item_id", "item_name"), 
		$table_prefix . "manufacturers" => array("manufacturer_id", "manufacturer_name"),
		$table_prefix . "articles_categories" => array("category_id", "category_name"), 
		$table_prefix . "articles" => array("article_id", "article_title"),
		$table_prefix . "forum_categories" => array("category_id", "category_name"), 
		$table_prefix . "forum_list" => array("forum_id", "forum_name"), 
		$table_prefix . "forum" => array("thread_id", "topic"),
		$table_prefix . "ads_categories" => array("category_id", "category_name"), 
		$table_prefix . "ads_items" => array("item_id", "item_title"),
		$table_prefix . "users" => array("user_id", "login"),
		$table_prefix . "pages" => array("page_id", "page_title"),
		$table_prefix . "manuals_list" => array("manual_id", "manual_title"),
		$table_prefix . "manuals_categories" => array("category_id", "category_name"),
		$table_prefix . "manuals_articles" => array("article_id", "article_title"),
		$table_prefix . "friendly_urls" => array("friendly_id", "script_name"),
		
	);

function set_friendly_url($parameters = array())
{
	global $db, $table_prefix, $settings, $r, $eg, $friendly_tables;

	$is_grid = isset($parameters["is_grid"]) ? $parameters["is_grid"] : false;
	$friendly_auto = get_setting_value($settings, "friendly_auto", 0);

	if ($is_grid) {
		$friendly_url = $eg->record->get_value("friendly_url");
		$field_updatable = ($eg->record->get_property_value("friendly_url", USE_IN_INSERT) || $eg->record->get_property_value("friendly_url", USE_IN_UPDATE));
	} else {
		$friendly_url = $r->get_value("friendly_url");
		$field_updatable = ($r->get_property_value("friendly_url", USE_IN_INSERT) || $r->get_property_value("friendly_url", USE_IN_UPDATE));
	}
	if ($field_updatable && ($friendly_auto == 1 || (!strlen($friendly_url) && $friendly_auto == 2))) {
		if ($is_grid) {
			$record_table = $eg->record->table_name;
		} else {
			$record_table = $r->table_name;
		}
		$table_info = $friendly_tables[$record_table];
		$title_field = $table_info[1];
		if ($is_grid) {
			$title_value = $eg->record->get_value($title_field);
		} else {
			$title_value = $r->get_value($title_field);
		}
		if ($is_grid) {
			$excluding_where = $eg->record->check_where() ? $eg->record->get_where(false) : "";
		} else {
			$excluding_where = $r->check_where() ? $r->get_where(false) : "";
		}
		if (strlen($excluding_where)) {
			$excluding_where = " AND NOT (" . $excluding_where . ")";
		}

		$friendly_url = generate_friendly_url($title_value, $record_table, $excluding_where);
		if ($is_grid) {
			$eg->record->set_value("friendly_url", $friendly_url);
		} else {
			$r->set_value("friendly_url", $friendly_url);
		}
	}

}

function validate_friendly_url($parameters, $generate_error = true)
{
	global $db, $table_prefix, $r, $eg, $friendly_tables;

	$eol = get_eol();
	$is_grid = isset($parameters["is_grid"]) ? $parameters["is_grid"] : false;

	if ($is_grid) {
		$friendly_url = $eg->record->get_value("friendly_url");
	} else {
		$friendly_url = $r->get_value("friendly_url");
	}

	$is_unique = true;
	if (strlen($friendly_url)) {
		foreach ($friendly_tables as $check_table => $table_info) {
			$sql  = " SELECT friendly_url FROM " . $check_table;
			$sql .= " WHERE friendly_url=" . $db->tosql($friendly_url, TEXT);
			if ($is_grid) {
				$record_table = $eg->record->table_name;
			} else {
				$record_table = $r->table_name;
			}
			if ($record_table == $check_table) {
				if ($is_grid) {
					$excluding_where = $eg->record->check_where() ? $eg->record->get_where(false) : "";
				} else {
					$excluding_where = $r->check_where() ? $r->get_where(false) : "";
				}
				if (strlen($excluding_where)) {
					$sql .= " AND NOT (" . $excluding_where . ")";
				}
			}
			$db->query($sql);
			if ($db->next_record()) {
				$is_unique = false;
				if ($generate_error) {
					$error_message = str_replace("{field_name}", $r->parameters["friendly_url"][CONTROL_DESC], UNIQUE_MESSAGE);
					if ($is_grid) {
						$eg->record->errors .= $error_message . "<br>" . $eol;
					} else {
						$r->errors .= $error_message . "<br>" . $eol;
					}
				}
				break;
			}
		}
	}
	return $is_unique;
}

function generate_friendly_url($item_title, $record_table = "", $excluding_where = "")
{
	global $db, $table_prefix, $friendly_tables;

	$friendly_url = trim(get_translation($item_title));
	$friendly_url = str_replace("\""," inch ", $friendly_url); 
	$friendly_url = str_replace("&"," and ", $friendly_url);  
	$friendly_url = str_replace("+"," and ", $friendly_url);  
	$friendly_url = str_replace("@"," at ", $friendly_url);  
	$friendly_url = preg_replace("/[\s\.]+/", "-", $friendly_url);
	$friendly_url = preg_replace("/[^a-z\d\_\-\s]/i", "", $friendly_url);
	$friendly_url = preg_replace("/_*\-+_*/", "-", $friendly_url);
	$initial_friendly_url = $friendly_url;

	if (strlen($friendly_url)) {
		$index = 0;
		do {
			if ($index) {
				$friendly_url = $initial_friendly_url . "_" . $index;
			}
			$is_exists = false;
			foreach ($friendly_tables as $check_table => $table_info) {
				$sql  = " SELECT friendly_url FROM " . $check_table;
				$sql .= " WHERE friendly_url=" . $db->tosql($friendly_url, TEXT);
				if ($check_table == $record_table) {
					$sql .= $excluding_where;
				}
				$db->query($sql);
				if ($db->next_record()) {
					$is_exists = true;
				}
			} 
			$index++;
		} while ($is_exists); 
	}

	return $friendly_url;
}

?>