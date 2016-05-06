<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  access_table.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	class VA_Access_Table {
		var $t, $access_levels, $access_levels_keys;
		var $table_name, $user_types_table_name, $subscriptions_table_name, $field_name, $field_value, $recursion_field;
		
		var $user_types, $subscription_groups, $subscriptions;
		var $field_id = "subscriptions";
		
		var $all_selected_access_level, $guest_selected_access_level, $selected_user_access_levels, $selected_access_levels;
		
		function VA_Access_Table($template_path, $filename, $field_id = ""){
			$this->t = new VA_Template($template_path);
			$this->t->set_file("access_table", $filename);
			if ($field_id) $this->field_id = $field_id;
			$this->t->set_var("field_id", $this->field_id);		
		}
		
		function set_tables($table_name, $user_types_table_name, $subscriptions_table_name, $field_name, $recursion_field, $field_value, $sql = false) {
			global $db, $table_prefix;
			
			$this->table_name               = $table_name;
			$this->user_types_table_name    = $user_types_table_name;
			$this->subscriptions_table_name = $subscriptions_table_name;
			
			$this->field_name  = $field_name;
			$this->field_value = $field_value;
			$this->recursion_field = $recursion_field;
			
			$this->user_types = array();
			$this->subscription_groups = array();
			$this->subscriptions = array();
			
			if (!$sql) {
				$sql  = " SELECT type_id, type_name ";
				$sql .= " FROM " . $table_prefix . "user_types ";
				$sql .= " WHERE is_active=1";
			}
			$db->query($sql);
			if ($db->next_record()) {
				do {
					$type_id   = $db->f("type_id");
					$type_name = get_translation($db->f("type_name"));
					$this->user_types[$type_id] = $type_name;
				} while ($db->next_record());
			}
			
			$sql  = " SELECT group_id, group_name ";
			$sql .= " FROM " . $table_prefix . "subscriptions_groups ";
			$sql .= " WHERE is_active=1";
			$db->query($sql);
			if ($db->next_record()) {
				do {
					$group_id   = $db->f("group_id");
					$group_name = get_translation($db->f("group_name"));
					$this->subscription_groups[$group_id] = $group_name;
				} while ($db->next_record());
			}			
				
			$sql  = " SELECT user_type_id, group_id, subscription_id, subscription_name FROM " . $table_prefix . "subscriptions ";
			$sql .= " WHERE is_active=1 ";
			$db->query($sql);
			if ($db->next_record()) {
				do {
					$type_id  = $db->f("user_type_id");
					$group_id = $db->f("group_id");
					$subscription_id = $db->f("subscription_id");
					$subscription_name = get_translation($db->f("subscription_name"));
					if (!$type_id) $type_id = 0;
					if (!$group_id) $group_id = 0;
					if ($type_id) {
						$this->subscriptions[$type_id][$subscription_id] = $subscription_name;
					} else {
						$this->subscriptions[$type_id][$group_id][$subscription_id] = $subscription_name;
					}
				} while ($db->next_record());
			}
			
			if (isset($this->subscriptions[0])) {
				$this->user_types[0] = OTHER_SUBSCRIPTIONS_MSG;
			}			
			
			$operation = get_param("operation");
			
			$this->all_selected_access_level   = 0;
			$this->guest_selected_access_level = 0;
			$this->selected_user_access_levels = array();
			$this->selected_access_levels      = array();
			
			if (!count($this->user_types) && !count($this->subscriptions)) {
				$max_access_level = 0;
				foreach ($this->access_levels_keys AS $access_level_key) {
					$max_access_level += $access_level_key;
				}
				$this->all_selected_access_level   = $max_access_level;
				$this->guest_selected_access_level = $max_access_level;
				return false;
			}
			
			if ($operation) {
				foreach ($this->access_levels_keys AS $access_level_key) {
					$access_level = get_param($this->field_id . "_all_" . $access_level_key);
					if ($access_level) {
						$this->all_selected_access_level += $access_level_key;
					}
					$access_level = get_param($this->field_id . "_guest_" . $access_level_key);
					if ($access_level) {
						$this->guest_selected_access_level += $access_level_key;
					}
				}
					
				
				$this->selected_user_access_levels = array();
				foreach ($this->user_types AS $type_id => $type_name) {
					foreach ($this->access_levels_keys AS $access_level_key) {
						$access_level = get_param($this->field_id . "_t_" . $type_id . "_" . $access_level_key);
						if ($access_level) {
							if (isset($this->selected_user_access_levels[$type_id])) {
								$this->selected_user_access_levels[$type_id] += $access_level_key;
							} else {
								$this->selected_user_access_levels[$type_id] = $access_level_key;
							}
						}
					}
				}
			
				foreach ($this->user_types AS $type_id => $type_name) {
					if (isset($this->subscriptions[$type_id])) {
						if ($type_id) {
							foreach ($this->subscriptions[$type_id] AS $subscription_id => $subscription_name) {
								foreach ($this->access_levels_keys AS $access_level_key) {
									$access_level = get_param($this->field_id . "_s_" . $subscription_id . "_" . $access_level_key);
									if ($access_level) {
										if (isset($this->selected_access_levels[$subscription_id])) {
											$this->selected_access_levels[$subscription_id] += $access_level_key;
										} else {
											$this->selected_access_levels[$subscription_id] = $access_level_key;
										}
									}
								}
								
							}
						} else {
							foreach ($this->subscriptions[$type_id] AS $group_id => $group_subscriptions) {
								foreach ($group_subscriptions AS $subscription_id => $subscription_name) {
									foreach ($this->access_levels_keys AS $access_level_key) {
										$access_level = get_param($this->field_id . "_s_" . $subscription_id . "_" . $access_level_key);
										
										if ($access_level) {
											if (isset($this->selected_access_levels[$subscription_id])) {
												$this->selected_access_levels[$subscription_id] += $access_level_key;
											} else {
												$this->selected_access_levels[$subscription_id] = $access_level_key;
											}
										}
									}
								}								
							}
						}
					}
				}
				
			} else {
				$sql  = " SELECT user_type_id, access_level ";
				$sql .= " FROM " . $table_prefix . $this->user_types_table_name;
				$sql .= " WHERE " . $this->field_name . "=" . $db->tosql($this->field_value, INTEGER);			
				$db->query($sql);
				while ($db->next_record()) {
					$type_id         = $db->f("user_type_id");
					$access_level    = $db->f("access_level");
					$this->selected_user_access_levels[$type_id] = $access_level;
				};
				
				$sql  = " SELECT subscription_id, access_level ";
				$sql .= " FROM " . $table_prefix . $this->subscriptions_table_name;
				$sql .= " WHERE " . $this->field_name . "=" . $db->tosql($this->field_value, INTEGER);			
				$db->query($sql);
				while ($db->next_record()) {
					$subscription_id = $db->f("subscription_id");
					$access_level    = $db->f("access_level");
					$this->selected_access_levels[$subscription_id] = $access_level;
				};				
			}
		}
		function set_access_levels($access_levels) {
			$this->access_levels = $access_levels;
			$this->access_levels_keys = array_keys($this->access_levels);
		}
			
		function parse($var, $all_access_level = 255, $guest_access_level = 255){
			global $t, $db, $table_prefix, $eol;
			$eol = get_eol();

			$this->all_selected_access_level   = $all_access_level;
			$this->guest_selected_access_level = $guest_access_level;
			if (!count($this->user_types) && !count($this->subscriptions)) {
				$max_access_level = 0;
				foreach ($this->access_levels_keys AS $access_level_key) {
					$max_access_level += $access_level_key;
				}
				$this->all_selected_access_level   = $max_access_level;
				$this->guest_selected_access_level = $max_access_level;
				return false;
			}		
			
			foreach ($this->access_levels AS $level_value => $level) {
				list($level_name, $level_fullname) = $level;
				$this->t->set_var("level_value", $level_value);
				$this->t->set_var("level_name", $level_name);
				$this->t->set_var("level_fullname", $level_fullname);
				$this->t->parse("header_level_block");
				
				$checked = ($level_value&$all_access_level) ? "checked" : "";
				$this->t->set_var("checked", $checked);				
				$this->t->parse("all_level_block");

				$checked = ($level_value&$guest_access_level) ? "checked" : "";
				$this->t->set_var("checked", $checked);
				$this->t->parse("guest_level_block");			
			}			
			$this->t->parse("header_block");
			$this->t->parse("all_block");
			$this->t->parse("guest_block");
					
			$row_index = 0;	
			$js  = " var " . $this->field_id . "_ids = new Array(); " . $eol;
			$js .= $this->field_id . "_ids['t']  = new Array(); " . $eol;
			$js .= $this->field_id . "_ids['s']  = new Array(); " . $eol;
			$ti = 0;
			$si = 0;			
			foreach ($this->user_types AS $type_id => $type_name) {
				$js .= $this->field_id . "_ids['t'][$ti] = $type_id; " . $eol;
				$ti++;
				if (isset($this->selected_user_access_levels[$type_id])) {
					$acl = $this->selected_user_access_levels[$type_id];
				} else {
					$acl = "";
				}
				
				$this->t->set_var("type_id", $type_id);				
				$this->t->set_var("type_name", $type_name);					
				$this->t->set_var("subscription_block", "");
				$this->t->set_var("type_level_block", "");
				$this->t->set_var("type_header_block", "");	
									
				if ($type_id) {
					// normal user type
					foreach ($this->access_levels_keys AS $level_value) {
						$checked = ($level_value&$acl) ? "checked" : "";
						$this->t->set_var("checked", $checked);
						$this->t->set_var("level_value", $level_value);
						$this->t->parse("type_level_block");
					}
					$row_class = "row" . ($row_index % 2 ? "1" : "2");
					$this->t->set_var("row_class", $row_class);
					$row_index++;
					$this->t->parse("type_header_block");
					
					$js .= $this->field_id . "_ids[$type_id]  = new Array(); " . $eol;
					$tsi = 0;
					if (isset($this->subscriptions[$type_id])) {
						foreach ($this->subscriptions[$type_id] AS $subscription_id => $subscription_name) {
							$js .= $this->field_id . "_ids['s'][$si] = $subscription_id ; " . $eol;
							$js .= $this->field_id . "_ids[$type_id][$tsi]  = $subscription_id ; " . $eol;
							$si++; $tsi++;
							if (isset($this->selected_access_levels[$subscription_id])) {
								$acl = $this->selected_access_levels[$subscription_id];
							} else {
								$acl = "";
							}
							$row_class = "row" . ($row_index % 2 ? "1" : "2");
							$this->t->set_var("row_class", $row_class);
							$this->t->set_var("subscription_id", $subscription_id);
							$this->t->set_var("subscription_name", $subscription_name);
							$this->t->set_var("level_block", "");
							foreach ($this->access_levels_keys AS $level_value) {
								$checked = ($level_value&$acl) ? "checked" : "";
								$this->t->set_var("checked", $checked);
								$this->t->set_var("level_value", $level_value);
								$this->t->parse("level_block");
							}
							$this->t->parse("subscription_block");
							$row_index++;
						}
					}
					$this->t->parse("type_block");
				} else {
					// zero user type - subscriptions groups						
					foreach ($this->subscriptions[$type_id] AS $group_id => $group_subscriptions) {
						$this->t->set_var("type_level_block", "");
						$this->t->set_var("other_type_header_block", "");
						$this->t->set_var("other_type_level_block", "");
						$this->t->set_var("subscription_block", "");
						if (isset($this->subscription_groups[$group_id])) {
							$this->t->set_var("type_name", $this->subscription_groups[$group_id]);
						} else {
							$this->t->set_var("type_name", OTHER_SUBSCRIPTIONS_MSG);
						}
						foreach ($this->access_levels AS $level_value => $level_name) {
							$this->t->parse("other_type_level_block");
						}
						$row_class = "row" . ($row_index % 2 ? "1" : "2");
						$this->t->set_var("row_class", $row_class);
						$row_index++;
						$this->t->parse("other_type_header_block");
						foreach ($group_subscriptions AS $subscription_id => $subscription_name) {
							$js .= $this->field_id . "_ids['s'][$si] = $subscription_id ; " . $eol;
							$si++;
							if (isset($this->selected_access_levels[$subscription_id])) {
								$acl = $this->selected_access_levels[$subscription_id];
							} else {
								$acl = "";
							}
							$row_class = "row" . ($row_index % 2 ? "1" : "2");
							$this->t->set_var("row_class", $row_class);
							$this->t->set_var("subscription_id", $subscription_id);
							$this->t->set_var("subscription_name", $subscription_name);
							$this->t->set_var("level_block", "");
							foreach ($this->access_levels_keys AS $level_value) {
								$checked = ($level_value&$acl) ? "checked" : "";
								$this->t->set_var("checked", $checked);
								$this->t->set_var("level_value", $level_value);
								$this->t->parse("level_block");
							}
							$this->t->parse("subscription_block");
							$row_index++;
						}
						$this->t->parse("type_block");
					}
				}			
			}
			$this->t->set_var("js", $js);
			$this->t->parse("table", false);
			$t->set_var($var, $this->t->get_var("table"));
			$t->sparse($var . "_block", false);
			return true;
		}
		
		function save_values_recursive($field_value) {
			global $db, $table_prefix;			
			$nested = array();							
			$sql  = " SELECT " . $this->field_name;
			$sql .= " FROM " . $table_prefix . 	$this->table_name;
			$sql .= " WHERE " . $this->recursion_field . " LIKE '%," . $db->tosql($field_value, INTEGER, false, false) . ",%'";			
			$db->query($sql);
			while ($db->next_record()) {
				$nested[] = $db->f($this->field_name);
			}
			
			if ($nested) {
				$sql  = " UPDATE " . $table_prefix .  $this->table_name;
				$sql .= " SET access_level= " . $db->tosql($this->all_selected_access_level, INTEGER, true, false);
				$sql .= " , guest_access_level= " . $db->tosql($this->guest_selected_access_level, INTEGER, true, false);
				$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($nested, INTEGERS_LIST) . ")";			
				$db->query($sql);
					
				$sql  = " DELETE FROM " . $table_prefix . $this->user_types_table_name;
				$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($nested, INTEGERS_LIST) . ")";
				$db->query($sql);
				
				$sql  = " DELETE FROM " . $table_prefix . $this->subscriptions_table_name;
				$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($nested, INTEGERS_LIST) . ")";
				$db->query($sql);
			
				foreach ($nested AS $field_value) {				
					foreach ($this->selected_user_access_levels AS $type_id => $access_level) {
						$sql  = " INSERT INTO " . $table_prefix . $this->user_types_table_name;
						$sql .=  " (" . $this->field_name . ", user_type_id, access_level) VALUES (";
						$sql .= $db->tosql($field_value, INTEGER, true, false) . ", ";
						$sql .= $db->tosql($type_id, INTEGER) . ", ";
						$sql .= $db->tosql($access_level, INTEGER) . ") ";
						$db->query($sql);
					}
					foreach ($this->selected_access_levels AS $subscription_id => $access_level) {
						$sql  = " INSERT INTO " . $table_prefix . $this->subscriptions_table_name;
						$sql .=  " (" . $this->field_name . ", subscription_id, access_level) VALUES (";
						$sql .= $db->tosql($field_value, INTEGER, true, false) . ", ";
						$sql .= $db->tosql($subscription_id, INTEGER) . ", ";
						$sql .= $db->tosql($access_level, INTEGER) . ") ";
						$db->query($sql);
					}
				}
			}
		}
		
		function save_values($field_value = null, $recursive = false) {
			global $db, $table_prefix;
			
			if ($field_value) $this->field_value = $field_value;
			
			$sql  = " DELETE FROM " . $table_prefix . $this->user_types_table_name;
			$sql .= " WHERE " . $this->field_name . "=" . $db->tosql($this->field_value, INTEGER);
			$db->query($sql);
			
			foreach ($this->selected_user_access_levels AS $type_id => $access_level) {
				$sql  = " INSERT INTO " . $table_prefix . $this->user_types_table_name;
				$sql .=  " (" . $this->field_name . ", user_type_id, access_level) VALUES (";
				$sql .= $db->tosql($this->field_value, INTEGER, true, false) . ", ";
				$sql .= $db->tosql($type_id, INTEGER) . ", ";
				$sql .= $db->tosql($access_level, INTEGER) . ") ";
				$db->query($sql);
			}
			
			$sql  = " DELETE FROM " . $table_prefix . $this->subscriptions_table_name;
			$sql .= " WHERE " . $this->field_name . "=" . $db->tosql($this->field_value, INTEGER);
			$db->query($sql);
			foreach ($this->selected_access_levels AS $subscription_id => $access_level) {
				$sql  = " INSERT INTO " . $table_prefix . $this->subscriptions_table_name;
				$sql .=  " (" . $this->field_name . ", subscription_id, access_level) VALUES (";
				$sql .= $db->tosql($this->field_value, INTEGER, true, false) . ", ";
				$sql .= $db->tosql($subscription_id, INTEGER) . ", ";
				$sql .= $db->tosql($access_level, INTEGER) . ") ";
				$db->query($sql);
			}
			
			if ($recursive && $this->recursion_field) {
				$this->save_values_recursive($this->field_value);
			}
		}
		
		function save_array_values($array, $access_level = 1, $guest_access_level = 1) {
			global $db, $table_prefix;
			
			if (!$array) return false;
			
			$sql  = " UPDATE " . $table_prefix . $this->table_name;
			$sql .= " SET access_level = " . $db->tosql($access_level, INTEGER);
			$sql .= ", guest_access_level = " . $db->tosql($guest_access_level, INTEGER);
			$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($array, INTEGERS_LIST) . ")";
			$db->query($sql);
			
			$sql  = " DELETE FROM " . $table_prefix . $this->user_types_table_name;
			$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($array, INTEGERS_LIST) . ")";
			$db->query($sql);
						
			$sql  = " DELETE FROM " . $table_prefix . $this->subscriptions_table_name;
			$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($array, INTEGERS_LIST) . ")";
			$db->query($sql);
			
			foreach ($array AS $value) {
				foreach ($this->selected_user_access_levels AS $type_id => $access_level) {
					$sql  = " INSERT INTO " . $table_prefix . $this->user_types_table_name;
					$sql .=  " (" . $this->field_name . ", user_type_id, access_level) VALUES (";
					$sql .= $db->tosql($value, INTEGER, true, false) . ", ";
					$sql .= $db->tosql($type_id, INTEGER) . ", ";
					$sql .= $db->tosql($access_level, INTEGER) . ") ";
					$db->query($sql);
				}			
				foreach ($this->selected_access_levels AS $subscription_id => $access_level) {
					$sql  = " INSERT INTO " . $table_prefix . $this->subscriptions_table_name;
					$sql .=  " (" . $this->field_name . ", subscription_id, access_level) VALUES (";
					$sql .= $db->tosql($value, INTEGER, true, false) . ", ";
					$sql .= $db->tosql($subscription_id, INTEGER) . ", ";
					$sql .= $db->tosql($access_level, INTEGER) . ") ";
					$db->query($sql);
				}
			}			
		}
	}
?>