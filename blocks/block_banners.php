<?php

function banners_group($block_name, $group_id, $bg_limit = 0, $params = "")
{
	global $t;
	global $db, $db_type, $table_prefix, $site_id;
	global $category_id;
	global $page_settings;
	
	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if (!$bg_limit && !$params) {
		$bg_limit = get_setting_value($page_settings, "bg_limit_" . $group_id, 0);
		$params = get_setting_value($page_settings, "bg_params_" . $group_id, "");
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

	$t->set_file("block_body",    "block_banners.html");
	$t->set_var("MORE_MSG",       MORE_MSG);
	$t->set_var("READ_MORE_MSG",  READ_MORE_MSG);
	$t->set_var("CLICK_HERE_MSG", CLICK_HERE_MSG);
	$t->set_var("banners", "");

	$banners_ids = "";
	$sql  = " SELECT b.*, bg.* FROM ((";
	if (isset($site_id)) {
		$sql .= "(";
	}
	$sql .= $table_prefix . "banners b ";
	$sql .= " INNER JOIN " . $table_prefix . "banners_assigned ba ON b.banner_id=ba.banner_id) ";
	$sql .= " INNER JOIN " . $table_prefix . "banners_groups bg ON ba.group_id=bg.group_id) ";
	if (isset($site_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "banners_sites bs ON bs.banner_id=b.banner_id) ";
	}
	$sql .= " WHERE bg.group_id=" . $db->tosql($group_id, INTEGER);
	$sql .= " AND bg.is_active=1 ";
	$sql .= " AND b.is_active=1 ";
	$sql .= " AND (b.max_impressions=0 OR b.max_impressions>b.total_impressions) ";
	$sql .= " AND (b.max_clicks=0 OR b.max_clicks>b.total_clicks) ";
	$sql .= " AND (b.expiry_date IS NULL OR b.expiry_date>=" . $db->tosql(va_time(), DATETIME). ") ";
	if (strtolower(get_var("HTTPS")) == "on") {
	  $sql .= " AND b.show_on_ssl=1 ";
	}
	if (isset($site_id)) {
		$sql .= " AND (b.sites_all=1 OR bs.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
	} else {
		$sql .= " AND b.sites_all=1 ";
	}
	
	$sql .= " ORDER BY b.banner_rank ";
	if ($db_type == "mysql") {
		$sql .= " , RAND() ";
	}
	if ($bg_limit > 0) {
		$db->RecordsPerPage = $bg_limit;
		$db->PageNumber = 1;
	}
	$db->query($sql);
	while ($db->next_record()) {
		$banner_id = $db->f("banner_id");
		if (strlen($banners_ids)) { $banners_ids .= ","; }
		$banners_ids .= $banner_id;
		$bc_url = "bc.php?b=" . $banner_id;

		$banner_title = get_translation($db->f("banner_title"));
		$show_title = $db->f("show_title");
		$image_src = $db->f("image_src");
		$image_alt= $db->f("image_alt");
		$target = ($db->f("is_new_window") == 1) ? "_blank" : "_top";
		if (!strlen($image_alt)) { $image_alt = $banner_title; }
		$html_text = get_translation($db->f("html_text"));


		$t->set_var("banner_id", $banner_id);
		$t->set_var("bc_url", $bc_url);
		$t->set_var("target", $target);

  
		if (strlen($image_src)) {
			if (preg_match("/^http\:\/\//", $image_src)) {
				$image_size = "";
			} else {
				$image_size = @GetImageSize($image_src);
			}
			$t->set_var("alt", htmlspecialchars($image_alt));
			$t->set_var("src", htmlspecialchars($image_src));
			if(is_array($image_size)) {
				$t->set_var("image_size", $image_size[3]);
			} else {
				$t->set_var("image_size", "");
			}
			$t->parse("banner_image", false);
		} else {
			$t->set_var("banner_image", "");
		}
  
		if ($show_title) {
			$t->set_var("banner_title", $banner_title);
			$t->parse("title_block", false);
		} else {
			$t->set_var("title_block", "");
		}
		$t->set_block("html_text", $html_text);
		$t->parse("html_text", false);

		$t->parse("banners", true);
	}

	if (strlen($banners_ids)) {

		$t->parse("block_body", false);
		$t->parse($block_name, true);
  
		// add one impression
		$sql  = " UPDATE " . $table_prefix . "banners ";
		$sql .= " SET total_impressions=total_impressions+1 ";
		$sql .= " WHERE banner_id IN (" . $db->tosql($banners_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
	}

}

?>