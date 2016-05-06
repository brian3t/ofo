<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  filter_functions.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


function filter_sqls(&$from_sql, &$join_sql, &$where_sql)
{
	global $db, $table_prefix, $filter_properties;

	// prepare queries for filters
	$filter = get_param("filter");
	$filters = explode("&", $filter);
	for ($f = 0; $f < sizeof($filters); $f++) {
		$filter_params = $filters[$f];
		$filter_value_id = "";
		$filter_where_sql = "";
		if (preg_match("/^fl(\d+)=(.+)$/", $filter_params, $matches)) {
			$filter_property_id = $matches[1];
			$filter_value_id = $matches[2];
		} else if (preg_match("/^fd(\d+)=(.+)$/", $filter_params, $matches)) {
			$filter_property_id = $matches[1];
			$filter_db_id = $matches[2];
			$sql  = " SELECT list_value_id,filter_where_sql ";
			$sql .= " FROM " . $table_prefix . "filters_properties_values ";
			$sql .= " WHERE value_id=" . $db->tosql($filter_db_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$filter_value_id = $db->f("list_value_id");
				$filter_where_sql = $db->f("filter_where_sql");
			}
		}
		if ($filter_value_id || $filter_where_sql) {
			$filter_from_sql = ""; $filter_join_sql = "";
			if (is_array($filter_properties) && isset($filter_properties[$filter_property_id])) {
				// data available in the filter array
				if (!$filter_where_sql) {
					$filter_where_sql = $filter_properties[$filter_property_id]["filter_where_sql"];
				}
				$filter_from_sql = $filter_properties[$filter_property_id]["filter_from_sql"];
				$filter_join_sql = $filter_properties[$filter_property_id]["filter_join_sql"];
			} else {
				// get data from database
				$sql  = " SELECT filter_from_sql, filter_join_sql, filter_where_sql  ";
				$sql .= " FROM " . $table_prefix . "filters_properties ";
				$sql .= " WHERE property_id=" . $db->tosql($filter_property_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					if (!$filter_where_sql) {
						$filter_where_sql = $db->f("filter_where_sql"); 
					}
					$filter_from_sql = $db->f("filter_from_sql");
					$filter_join_sql = $db->f("filter_join_sql");
				}
			}
			$filter_where_sql = str_replace("{value_id}", $db->tosql($filter_value_id, TEXT, false), $filter_where_sql);
			$filter_where_sql = str_replace("{table_value}", $db->tosql($filter_value_id, TEXT, false), $filter_where_sql);

			if ($filter_where_sql) {
				// if correct data passed and where condition available
				$from_sql = $filter_from_sql . $from_sql;
				$join_sql .= $filter_join_sql;
				$where_sql .= " AND (" . $filter_where_sql . ") ";
			}
		}
	}
}

?>