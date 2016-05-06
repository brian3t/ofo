<?php
include_once("./includes/manuals_functions.php");

/**
	 * Add manual article detail block
	 *
	 * @param string $block_name
	 */
function manuals_article_details($block_name) {
	global $t, $db, $table_prefix, $settings, $datetime_show_format;
	global $article;
	global $html_title, $meta_keywords, $meta_description;

	$article_id = get_param("article_id");
	$t->set_file("block_body", "block_manuals_article_details.html");


	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "manuals_articles ";
	$sql .= " WHERE article_id=".$db->tosql($article_id, INTEGER);			
		
	$db->query($sql);
	if ($db->next_record()) {
		$alias_article = array();
		$article_title = get_translation($db->f("article_title"));
		$article_order = $db->f("article_order");
		$section_number = $db->f("section_number");
		$parent_article_id = $db->f("parent_article_id");
		$short_description = $db->f("short_description");
		$article = $db->Record;

		$full_description = $db->f("full_description");
		$alias_article_id = $db->f("alias_article_id");
		
		$meta_title = get_translation($db->f("meta_title"));
		$meta_keywords = get_translation($db->f("meta_keywords"));
		$meta_description = get_translation($db->f("meta_description"));
		
		// if $full_description is empty and alias article specified
		// read alias article info
		if ($full_description == "" && $alias_article_id > 0) {
			$sql = "SELECT * FROM " . $table_prefix . "manuals_articles WHERE article_id=";
			$sql .= $db->tosql($alias_article_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$alias_article = $db->Record;
				$full_description = $db->f("full_description");
				$short_description = $db->f("short_description");
				$meta_title = get_translation($db->f("meta_title"));
				$meta_keywords = get_translation($db->f("meta_keywords"));
				$meta_description = get_translation($db->f("meta_description"));
			}
		}
		
		$manual_id = $db->f("manual_id");		
		if (!VA_Manuals::check_permissions($manual_id, VIEW_ITEMS_PERM)) {
			header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
			exit;
		}
		
		if ($meta_title) {
			$html_title = $meta_title;
		} else {
			$html_title = MANUALS_TITLE;
			$sql  = " SELECT ml.manual_title, mc.category_name ";
			$sql .= " FROM (" . $table_prefix . "manuals_list ml ";
			$sql .= " LEFT JOIN " . $table_prefix . "manuals_categories mc ON mc.category_id = ml.category_id )";
			$sql .= " WHERE ml.manual_id = ".$db->tosql($manual_id, INTEGER);			
			$db->query($sql);
			if ($db->next_record()) {
				$category_name = get_translation($db->f("category_name"));
				$manual_title  = get_translation($db->f("manual_title"));
				if ($category_name) {
					$html_title .= " | " . $category_name;
				}
				if ($manual_title) {
					$html_title .= " | " . $manual_title;
				}
			}
			if ($article_title) {			
				$html_title .= " | " . $article_title;
			}
		}	

		$t->set_var("article_title", $article_title);
		$t->set_var("section_number", $section_number);
		$t->set_var("short_description", $short_description);

		$level = count(explode(".", $section_number));
		$t->set_var("level", $level);
		$content = parse_special_tags($full_description);

		set_prev_article($article);
		set_index($article);
		set_next_article($article);

		if ($content == "") {
			$t->set_var("full_description", MANUAL_ARTICLE_NO_CONTENT_MSG);
		} else {
			$t->set_var("full_description", $content);
		}
		$t->parse("item", false);
		$t->set_var("no_item", "");
	} else {
		$t->parse("no_item", false);
		$t->set_var("item", "");
	}
	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

/**
	 * Parse special tags
	 *
	 * @param string $text
	 * @return string
	 */
function parse_special_tags($text) {
	global $article;
	global $table_prefix;
	global $db;
	global $settings;

	// Global friendly url settings
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$parsers = array();
	// Description
	$parsers[] = array(
	"regexp" => "/\[article\s+friendly_url\s{0,}=\s{0,}(.[^\]#]+)([0-9a-z_#]{0,})\s{0,}]/",
	"name" => "article"
	);
	$parsers[] = array(
	"regexp" => "/\[subsections]/",
	"name" => "subsections"
	);
	/*
	$text = "[article friendly_url=lalal_bla_bla_1#anchore]";
	$s = "/\[article\s+friendly_url\s{0,}=\s{0,}(.[^\]#]+)([0-9a-z_#]{0,})\s{0,}]/";
	$result = preg_match_all($s, $text, $matches);
	var_dump($matches);
	//*/
	foreach ($parsers as $parser_info) {
		$regexp = $parser_info["regexp"];
		$name = $parser_info["name"];

		$result = preg_match_all($regexp, $text, $matches);

		if ($result) {
			switch ($name) {
				// Tags proceessing
				case "article":

				foreach ($matches[1] as $key => $friendly_url) {
					$section_number = get_section_info($friendly_url);
					if (isset($matches[2]) && isset($matches[2][$key])) {
						$friendly_url .= $matches[2][$key];
						$friendly_url .= $friendly_extension;
					}
					if ($section_number != "") {
						$url = "<a href=\"".$friendly_url."\">see section ".$section_number."</a>";
						$text = str_replace($matches[0][$key], $url, $text);
					} else {
						$link_title = "see details";
						$url = "<a href=\"".$friendly_url."\">".$link_title."</a>";
						$text = str_replace($matches[0][$key], $url, $text);
					}
				}
				break;
				case "subsections":
				$article_id = get_param("article_id");
				$sql  = " SELECT section_number, article_id, friendly_url, article_title ";
				$sql .= " FROM ".$table_prefix."manuals_articles ";
				$sql .= " WHERE section_number LIKE ".$db->tosql($article["section_number"]. ".%", TEXT);
				$sql .= " AND manual_id = ".$db->tosql($article["manual_id"], INTEGER);
				$sql .= " ORDER BY section_number";
				$db->query($sql);

				$template = new VA_Template($settings["templates_dir"]);
				$template->set_file("subsections", "manuals_subsections_list.html");

				if ($db->next_record()) {
					do {
						$section_number = $db->f("section_number");
						$article_id = $db->f("article_id");
						$article_title = get_translation($db->f("article_title"));
						$friendly_url = $db->f("friendly_url");
						$level = count(explode(".", $section_number));
						if (!$friendly_urls || $friendly_url == "") {
							$src = "manuals_article_details.php?article_id=".$article_id;
						} else {
							$src = $friendly_url . $friendly_extension;
						}

						$template->set_var("level", $level);
						$template->set_var("section_href", $src);
						$template->set_var("name", $article_title);
						$template->set_var("section_number", $section_number);
						$template->parse("section", true);

					} while ($db->next_record());
					$template->parse("subsections", false);
					$subsections_list = $template->get_var("subsections");
					$text = str_replace("[subsections]", $subsections_list, $text);
				}
				break;
			}
		}
	}
	return $text;
}

/**
	 * Get section info by friendly_url
	 *
	 * @param string $friendly_url
	 * @return string
	 */
function get_section_info($friendly_url) {
	global $db;
	global $table_prefix;

	$section_number = "";
	$sql = "SELECT section_number FROM ".$table_prefix."manuals_articles WHERE friendly_url = ".$db->tosql($friendly_url, TEXT);
	$db->query($sql);
	if ($db->next_record()) {
		$section_number = $db->f("section_number");
	}
	return $section_number;
}

/**
	 * Parse block of previous article reference.
	 *
	 * @param array $article
	 */
function set_prev_article($article) {
	global $table_prefix;
	global $db;
	global $t;
	global $settings;

	// Global friendly url settings
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	// Count prev section
	$section_number = $article["section_number"];
	$decimals = explode(".", $section_number);
	$last_number = $decimals[count($decimals) - 1];
	if ($last_number == 1) {
		unset($decimals[count($decimals) - 1]);
	} else {
		$decimals[count($decimals) - 1]--;
	}
	$prev_section = implode(".", $decimals);

	$sql  = " SELECT section_number, article_id, friendly_url ";
	$sql .= " FROM ".$table_prefix."manuals_articles ";
	$sql .= " WHERE (section_number LIKE ".$db->tosql($prev_section, TEXT);
	$sql .= " OR section_number LIKE ". $db->tosql($prev_section. ".%", TEXT) . ")";
	$sql .= " AND manual_id = ".$db->tosql($article["manual_id"], INTEGER);
	$sql .= " AND allowed_view = 1";
	$sql .= " ORDER BY section_number";
	$db->query($sql);

	if ($db->next_record()) {
		$first_article_id = $db->f("article_id");
		if ($first_article_id == $article["article_id"]) {
			$t->set_var("prev_article", "");
		} else {
			$friendly_url = "";
			$search = true;
			do {
				$article_section_number = $db->f("section_number");

				if ($article_section_number == $section_number) {
					$prev_article_id = $article_id;
					$search = false;
				} else {
					$friendly_url = $db->f("friendly_url");
					$prev_article_id = $db->f("article_id");
				}
				$article_id = $db->f("article_id");
			} while ($search && $db->next_record());

			if ($friendly_urls && $friendly_url != "") {
				$src = $friendly_url . $friendly_extension;
			} else {
				$src = "manuals_article_details.php?article_id=".$prev_article_id;
			}
			$t->set_var("friendly_url", $src);
			$t->parse("prev_article", false);
		}
	}
}

/**
	 * Parse block of reference to next article
	 *
	 * @param array $article
	 */
function set_next_article($article) {
	global $table_prefix;
	global $db;
	global $t;
	global $settings;

	// Global friendly url settings
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	// Get next possible section numbers
	$section_number = $article["section_number"];
	$decimals = explode(".", $section_number);
	$sections = array();
	$sections[] = $section_number . ".1";

	$section = $decimals;

	foreach ($decimals as $decimal) {

		$section[count($section) - 1]++;
		$sections[] = implode(".", $section);
		unset($section[count($section) - 1]);
	}

	$sql  = " SELECT article_id, friendly_url, section_number ";
	$sql .= " FROM ".$table_prefix."manuals_articles ";
	$sql .= " WHERE  manual_id = ".$db->tosql($article["manual_id"], INTEGER);
	if (is_array($sections) && !empty($sections)) {
		foreach ($sections as $section) {
			$query_str[] = " section_number = " . $db->tosql($section, TEXT);
		}
		$sql .= " AND " . "(" . implode(" OR ", $query_str) . ") ";
	}
	$sql .= " AND allowed_view = 1";

	$db->query($sql);

	if ($db->next_record()) {
		$exist_sections = array();
		do {
			$section_number = $db->f("section_number");
			$exist_sections[$section_number] = array(
			"article_id" => $db->f("article_id"),
			"friendly_url" => $db->f("friendly_url")
			);
		} while($db->next_record());

		$not_found = true;
		$i = 0;
		do {
			$section = $sections[$i];
			if (isset($exist_sections[$section])) {
				$not_found = false;
				$article_id = $exist_sections[$section]["article_id"];
				$friendly_url = $exist_sections[$section]["friendly_url"];
			}
			// No next article
			if ($i == count($sections)) {
				$not_found = false;
			}
			$i++;
		} while ($not_found);

		if ($friendly_urls && $friendly_url != "") {
			$src = $friendly_url . $friendly_extension;
		} else {
			$src = "manuals_article_details.php?article_id=".$article_id;
		}

		$t->set_var("friendly_url", $src);
		$t->parse("next_article", false);

	} else {
		$t->set_var("next_article", "");
	}
}

/**
	 * Parse index block. Show link to articles index
	 *
	 * @param array $article
	 */
function set_index($article) {
	global $table_prefix, $db, $t;
	global $settings;

	// Global friendly url settings
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$sql = "SELECT manual_id, friendly_url, manual_title FROM ".$table_prefix."manuals_list";
	$sql .= " WHERE manual_id = ".$db->tosql($article["manual_id"], INTEGER);

	$db->query($sql);
	if ($db->next_record()) {
		$manual_id = $db->f("manual_id");
		$friendly_url = $db->f("friendly_url");

		$manual_title = $db->f("manual_title");
		if ($friendly_urls && $friendly_url != "") {
			$url = $friendly_url . $friendly_extension;
		} else {
			$url = "manuals_articles.php?manual_id=".intval($manual_id);
		}

		$t->set_var("friendly_url", $url);
		$t->set_var("manual_href", $url);
		$t->set_var("manual_title", $manual_title);
		$t->parse("index", false);
	}
}

/**
	 * Compare two sections. If second is before first return 1, if they are same
	 * return 0, else -1 
	 *
	 * @param string $section_1
	 * @param string $section_2
	 * @return integer
	 */
function compare_sections($section_1, $section_2) {
	$s1_decimals = explode(".", $section_1);
	$s2_decimals = explode(".", $section_2);
	// If one of section longer then other save what is longer
	if (count($s1_decimals) > count($s2_decimals)) {
		$result = 1;
	} elseif (count($s1_decimals) < count($s2_decimals)) {
		$result = -1;
	} else {
		$result = 0;
	}

	for ($i = 0; $i < min(count($s1_decimals), count($s2_decimals)); $i++) {
		if ($s1_decimals[$i] > $s2_decimals[$i]) {
			return 1;
		} elseif ($s1_decimals[$i] < $s2_decimals[$i]) {
			return -1;
		}
	}
	// Next return works if sections have same length or one is subsection of the
	// other
	return $result;
}

?>