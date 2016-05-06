<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  articles_rss.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/articles_functions.php");
	
	$currency = get_currency();
	$category_id = get_param("category_id");

	if (VA_Articles_Categories::check_exists($category_id)) {
		if (!VA_Articles_Categories::check_permissions($category_id, VIEW_CATEGORIES_ITEMS_PERM)) {
			header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
			exit;
		}
	} else {
		echo NO_RECORDS_MSG;
		exit;
	}

	$eol  = get_eol();
	$sql  = " SELECT category_id, parent_category_id, category_path, category_name, article_list_fields,";
 	$sql .= " short_description, full_description, image_small, image_large, articles_order_column, articles_order_direction,";
 	$sql .= " is_rss, rss_limit ";
 	$sql .= " FROM " . $table_prefix . "articles_categories ";	
	$sql .= " WHERE category_id = " . $db->tosql($category_id, INTEGER);	
	$db->query($sql);
	
	$articles_setting = array();
	while ($db->next_record()) {
		$articles_setting[$db->f("category_id")]["category_name"] = $db->f("category_name");
		$articles_setting[$db->f("category_id")]["is_rss"] = $db->f("is_rss");
		if ($db->f("rss_limit")) {
			$articles_setting[$db->f("category_id")]["rss_limit"] = $db->f("rss_limit");
		} else {
			$articles_setting[$db->f("category_id")]["rss_limit"] = 10;
		}
		if (strlen($db->f("short_description"))) {
			$articles_setting[$db->f("category_id")]["description"] = $db->f("short_description");
		} else if (strlen($db->f("full_description"))) {
			$articles_setting[$db->f("category_id")]["description"] = $db->f("full_description");
		} else {
			$articles_setting[$db->f("category_id")]["description"] = $db->f("category_name");
		}
		if (strlen($db->f("image_small"))) {
			$articles_setting[$db->f("category_id")]["image"] = $db->f("image_small");
		} else if (strlen($db->f("image_large"))) {
			$articles_setting[$db->f("category_id")]["image"] = $db->f("image_large");
		}
		if($db->f("parent_category_id") == 0) {
			$articles_setting[$db->f("category_id")]["article_list_fields"] = $db->f("article_list_fields");
			if (strlen($db->f("articles_order_column"))) {
				$articles_setting[$db->f("category_id")]["order"] = "ORDER BY ".$db->f("articles_order_column")." "
				.$db->f("articles_order_direction");
			} else {
				$articles_setting[$db->f("category_id")]["order"] = "";
			}
		} else {
			$tpm_category_id=$db->f("category_id");
			$categories_ids = explode(",", $db->f("category_path"));
			$top_id = $categories_ids[1];
			$sql  = " SELECT category_id, parent_category_id, category_path, category_name, article_list_fields,";
		 	$sql .= " short_description, full_description, image_small, image_large, articles_order_column, articles_order_direction";
			$sql .= " FROM " . $table_prefix . "articles_categories ";
			$sql .= " WHERE category_id=" . $db->tosql($top_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$articles_setting[$tpm_category_id]["article_list_fields"] = $db->f("article_list_fields");
				if (strlen($db->f("articles_order_column"))) {
					$articles_setting[$tpm_category_id]["order"] = "ORDER BY ".$db->f("articles_order_column")." "
					.$db->f("articles_order_direction");
				} else {
					$articles_setting[$tpm_category_id]["order"] = "";
				}
			}
		}
	}

	if (isset($articles_setting[$category_id]["category_name"]) && isset($articles_setting[$category_id]["is_rss"]) && $articles_setting[$category_id]["is_rss"]==1){
		$is_xml = false;
		if (strlen($articles_setting[$category_id]["category_name"])) {
			$db->RecordsPerPage = $articles_setting[$category_id]["rss_limit"];
			$db->PageNumber = 1;
			$sql  = " SELECT aa.article_id FROM (".$table_prefix."articles_assigned aa ";
			$sql .= " LEFT JOIN ".$table_prefix."articles_categories ac ON aa.category_id=ac.category_id) ";
			$sql .= " WHERE ac.category_id =".$category_id." ORDER BY aa.article_id DESC";
			$db->query($sql);
			$articles_ids = "";
			while ($db->next_record()) {
				if (strlen($articles_ids)) {
					$articles_ids .= ",".$db->f("article_id");
				} else {
					$articles_ids = $db->f("article_id");
				}
			}

			$xml  = "<?xml version=\"1.0\" encoding=\"".CHARSET."\" ".chr(63).">" . $eol;
			$xml .= "<rss version=\"2.0\">" . $eol;
			$xml .= "\t<channel>" . $eol;
			$xml .= "\t\t<title>".xml_get_translation($articles_setting[$category_id]["category_name"])."</title>" . $eol;
			$xml .= "\t\t<link>".xml_entities($settings["site_url"]."articles.php?category_id=".$category_id)."</link>" . $eol;
			$xml .= "\t\t<description>".xml_get_translation($articles_setting[$category_id]["description"])."</description>" . $eol;

			if (strlen($articles_ids) && isset($articles_setting[$category_id]["article_list_fields"]) && strlen($articles_setting[$category_id]["article_list_fields"])) {
				$sql  = "SELECT article_id, ".$articles_setting[$category_id]["article_list_fields"];
				$sql .= " FROM (".$table_prefix."articles a ";
				$sql .= " INNER JOIN " . $table_prefix . "articles_statuses st ON a.status_id=st.status_id) ";
				$sql .= " WHERE article_id in (".$db->tosql($articles_ids, INTEGERS_LIST).") AND st.allowed_view=1 ";
				$sql .= $articles_setting[$category_id]["order"];
				$db->query($sql);
				if ($db->next_record()) {
					do {
						$title = "";
						$link = "";
						$description = "";
						$author = "";
						$pubDate = "";
						if (preg_match("/article_title/i", $articles_setting[$category_id]["article_list_fields"]) && strlen($db->f("article_title"))) {
							$title = $db->f("article_title");
						}
						if (strlen($db->f("article_id"))) {
							$link = $settings["site_url"]."article.php?article_id=".$db->f("article_id");
						}
						if (preg_match("/short_description/i", $articles_setting[$category_id]["article_list_fields"]) && strlen($db->f("short_description"))) {
							$description = $db->f("short_description");
						} else if (preg_match("/full_description/i", $articles_setting[$category_id]["article_list_fields"]) && strlen($db->f("full_description"))) {
							$description = $db->f("full_description");
						}
						if (preg_match("/author_email/i", $articles_setting[$category_id]["article_list_fields"]) && strlen($db->f("author_email"))) {
							$author = $db->f("author_email");
						}
						if (preg_match("/author_name/i", $articles_setting[$category_id]["article_list_fields"]) && strlen($db->f("author_name"))) {
							if (strlen($author)) {
								$author = $author." (".$db->f("author_name").")";
							} else {
								$author = $db->f("author_name");
							}
						}
						if (preg_match("/article_date/i", $articles_setting[$category_id]["article_list_fields"]) && is_array($db->f("article_date", DATETIME))) {
							$tpubdate = $db->f("article_date", DATETIME);
							$tdate=mktime($tpubdate[HOUR],$tpubdate[MINUTE],$tpubdate[SECOND],$tpubdate[MONTH],$tpubdate[DAY],$tpubdate[YEAR]);
							$pubdate = date("D, d M Y H:i:s O", $tdate);
						}
						
						$is_xml_item = false;
						if (strlen($title) || strlen($description)) {
							$xml_item  = "\t\t<item>" . $eol;
							if (strlen($title)) {
								$is_xml_item = true;
								$xml_item .= "\t\t\t<title>".xml_get_translation($title)."</title>" . $eol;
							}
							if (strlen($link)) {
								$xml_item .= "\t\t\t<link>".xml_get_translation($link)."</link>" . $eol;
							}
							if (strlen($description)) {
								$is_xml_item = true;
								$xml_item .= "\t\t\t<description>".xml_get_translation($description)."</description>" . $eol;
							}
							if (strlen($author)) {
								$xml_item .= "\t\t\t<author>".xml_get_translation($author)."</author>" . $eol;
							}
							if (isset($pubdate) && strlen($pubdate)) {
								$xml_item .= "\t\t\t<pubDate>".xml_get_translation($pubdate)."</pubDate>" . $eol;
							}
							$xml_item .= "\t\t</item>" . $eol;
						}
						if ($is_xml_item) {
							$xml .= $xml_item;
							$is_xml = true;
						}
					} while ($db->next_record());
				}
			}

			$xml .= "\t</channel>" . $eol;
			$xml .= "</rss>" . $eol;
		}
		if ($is_xml) {
			header("Content-Type: text/xml");
			header("Pragma: no-cache");
			echo $xml;
		} else {
			echo "";
		}
	}
	
	function xml_get_translation($string) {
		return xml_entities(get_translation($string));
	}

	function xml_entities($string) {
		return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
	}
	
?>