<?php

function custom_block($block_name, $block_number)
{
	global $t;
	global $db, $table_prefix;
	global $category_id;
	global $settings, $page_settings, $currency;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$css_class = get_setting_value($page_settings, "cb_css_class_" . $block_number, "");
	$user_type = get_setting_value($page_settings, "cb_user_type_" . $block_number, "");
	$admin_type = get_setting_value($page_settings, "cb_admin_type_" . $block_number, "");
	$params = get_setting_value($page_settings, "cb_params_" . $block_number, "");

	$user_check = true;
	if (strlen($user_type)) {
		if (strtoupper($user_type) == "NON") {
			if (strlen(get_session("session_user_id"))) {
				$user_check = false;
			}
		} else if (strtoupper($user_type) == "ANY") {
			if (!strlen(get_session("session_user_id"))) {
				$user_check = false;
			}
		} else {
			if ($user_type != get_session("session_user_type_id")) {
				$user_check = false;
			}
		}
	}

	if (!$user_check && !strlen($admin_type)) {
		return;
	}

	$admin_check = true;
	if (strlen($admin_type)) {
		if (strtoupper($admin_type) == "ANY") {
			if (!strlen(get_session("session_admin_id"))) {
				$admin_check = false;
			}
		} else {
			if ($admin_type != get_session("session_admin_privilege_id")) {
				$admin_check = false;
			}
		}
	}

	if (!$admin_check && (!$user_check || !strlen($user_type))) {
		return;
	}


	if (strlen($params)) {
		$pairs = explode(";", $params);
		for ($i = 0; $i < sizeof($pairs); $i++) {
			$pair = explode("=", $pairs[$i], 2);
			if (sizeof($pair) == 2) {
				list($param_name, $param_value) = $pair;
				if ($param_name == "category" || $param_name == "category_id") {
					$current_value = get_param("category_id");
					if (!strlen($current_value)) {
						$current_value = "0";
					}
				} else if ($param_name == "item" || $param_name == "product" || $param_name == "product_id") {
					$current_value = get_param("item_id");
				} else if ($param_name == "user" || $param_name == "user_id") {
					$current_value = get_session("session_user_id");
				} else {
					$current_value = get_param($param_name);
				}
				$param_values = explode(",", $param_value);
				if (!in_array($current_value, $param_values)) {
					return;
				}
			}
		}
	}


  $sql  = " SELECT block_title, block_path, block_desc FROM " . $table_prefix . "custom_blocks ";
  $sql .= " WHERE block_id=" . intval($block_number);
	$db->query($sql);
	if($db->next_record()) {
		$custom_title = get_translation($db->f("block_title"));
		$custom_title = get_currency_message($custom_title, $currency);
		$block_path = $db->f("block_path");
		if ($block_path) {
			$custom_body = join("", file($block_path));
		} else {
			$custom_body = get_translation($db->f("block_desc"));
		}
		$custom_body = get_translation($custom_body);
		$custom_body = get_currency_message($custom_body, $currency);
		if (get_setting_value($settings, "php_in_custom_blocks", 0)) {
			eval_php_code($custom_body);
		}
	} else {
		return;
	}
	
	if(!strlen($custom_body) && !strlen($custom_title)) {
		return;
	}
	if(strlen($custom_title)) {
		if (!$css_class) { $css_class = "block-custom"; }
		$t->set_file("block_body", "block_custom.html");
	} else {
		if (!$css_class) { $css_class = "block-simple"; }
		$t->set_file("block_body", "block_simple.html");
	}


	$t->set_var("css_class", $css_class);
	$t->set_block("custom_title", $custom_title);
	$t->parse("custom_title", false);
	$t->set_block("custom_body", $custom_body);
	$t->parse("custom_body", false);

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>