<?php

  if (!IS_LOCAL && substr($_SERVER['HTTP_HOST'],0,3) != 'www') {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: http://www.'.$_SERVER['HTTP_HOST']
    .$_SERVER['REQUEST_URI']);
    }


  include_once("./blocks/block_login.php");

  $site_url = get_setting_value($settings, "site_url", "");
  $secure_url = get_setting_value($settings, "secure_url", "");
  if ($is_ssl) {
    $absolute_url = $secure_url;
    $google_analytics_js = "https://ssl.google-analytics.com/urchin.js";
    $sayu_landing_js = "https://www.sayutracking.co.uk/landing.js";
  } else {
    $absolute_url = $site_url;
    $google_analytics_js = "http://www.google-analytics.com/urchin.js";
    $sayu_landing_js = "http://www.sayutracking.co.uk/landing.js";
  }
  $parsed_url = parse_url($site_url);
  $site_path = isset($parsed_url["path"]) ? $parsed_url["path"] : "/";

  $layout_id = get_setting_value($settings, "layout_id", "");
  $css_file = "";
  if (strlen(get_setting_value($settings, "style_name", ""))) {
    $css_file  = $absolute_url;
    $css_file .= "styles/" . get_setting_value($settings, "style_name");
    if (strlen(get_setting_value($settings, "scheme_name", ""))) {
      $css_file .= "_" . get_setting_value($settings, "scheme_name");
    }
    $css_file .= ".css";
  }

  $t->set_file("header", "header.html");
  $t->set_var("CHARSET", CHARSET);
  $t->set_var("meta_language", $language_code);
  $t->set_var("site_url", $site_url);
  $t->set_var("secure_url", $secure_url);
  $t->set_var("absolute_url", $absolute_url);
  $t->set_var("google_analytics_js", $google_analytics_js);
  $t->set_var("sayu_landing_js", $sayu_landing_js);
  $t->set_var("css_file", $css_file);
  $t->set_var("menu", "");

  $request_uri_path = get_request_path();
  $request_uri_base = basename($request_uri_path);
  // set site logo
  $logo_image = get_translation(get_setting_value($settings, "logo_image", "images/tr.gif"));
  $logo_image_alt = get_translation(get_setting_value($settings, "logo_image_alt", HOME_PAGE_TITLE));
  $logo_width = get_setting_value($settings, "logo_image_width", "");
  $logo_height = get_setting_value($settings, "logo_image_height", "");
  $logo_size = "";
  if ($logo_width || $logo_height) {
    if ($logo_width) { $logo_size = "width=\"".$logo_width."\""; }
    if ($logo_height) { $logo_size .= " height=\"".$logo_height."\""; }
  } elseif ($logo_image && !preg_match("/^http\:\/\//", $logo_image)) {
    //$logo_image = $absolute_url . $logo_image;
    $image_size = @GetImageSize($logo_image);
    if (is_array($image_size)) {
      $logo_size = $image_size[3];
    }
  }

  $t->set_var("logo_alt", htmlspecialchars($logo_image_alt));
  $t->set_var("logo_src", htmlspecialchars($logo_image));
  $t->set_var("logo_size", $logo_size);


  $active_menu_id = ""; $active_submenu_id = ""; $submenu_style_name = "";
  if (!isset($show_last_active_menu)) { $show_last_active_menu = false; }
  $request_page = get_request_page();
  if (!isset($current_page)) { $current_page = $request_page; }

  // check secondary items
  $sql  = " SELECT hs.* FROM (" . $table_prefix . "header_submenus hs ";
  $sql .= " INNER JOIN " . $table_prefix . "header_links hl ON hs.menu_id=hl.menu_id) ";
  $sql .= " WHERE (hl.layout_id=0 OR hl.layout_id=" . $db->tosql($layout_id, INTEGER) . ") ";
  $sql .= " AND (submenu_page=" . $db->tosql($request_page, TEXT); // check default page link page.php
  if ($current_page != $request_page) {
    $sql .= " OR submenu_page=" . $db->tosql($current_page, TEXT);
  }
  if ($request_uri_path != $request_page) {
    $sql .= " OR submenu_page=" . $db->tosql($request_uri_path, TEXT); // check absolute links /viart/page.php
  }
  $sql .= ")";
  $sql .= " AND hs.match_type>0 ";
  $db->query($sql);
  while ($db->next_record()) {
    $secondary_menu_matched = true;
    $secondary_menu_url = get_custom_friendly_url($db->f("submenu_url"));
    $match_type = $db->f("match_type");
    if ($match_type == 2) {
      $secondary_menu_url = preg_replace("/\#.*$/", "", $secondary_menu_url);
      $secondary_menu_url = preg_replace("/^.*\?/", "", $secondary_menu_url);
      if ($secondary_menu_url) {
        $secondary_menu_params = explode("&", $secondary_menu_url);
        for($s = 0; $s < sizeof($secondary_menu_params); $s++) {
          if (preg_match("/^(.+)=(.+)$/", $secondary_menu_params[$s], $matches)) {
            $param_name = $matches[1];
            $secondary_menu_param_value = $matches[2];
            $request_param_value = get_param($param_name);
            if (strval($secondary_menu_param_value) != strval($request_param_value)) {
              $secondary_menu_matched = false;
            }
          }
        }
      }
    }
    if ($secondary_menu_matched) {
      $submenu_path = $db->f("submenu_path");
      if (preg_match("/^(\d+)/", $submenu_path, $matches)) {
        $active_submenu_id = $matches[1];
        $sql = " SELECT menu_id FROM " . $table_prefix . "header_submenus WHERE submenu_id=" . $db->tosql($active_submenu_id, INTEGER);
        $active_menu_id = get_db_value($sql);
      } else {
        $active_menu_id = $db->f("menu_id");
      }
      break;
    }
  }

  $menu = array();

  $top_menu_type = get_setting_value($settings, "top_menu_type", 1);
  if (!$settings["layout_id"]) { $settings["layout_id"] = 1; }
  $header_where = get_session("session_user_id") ? " show_logged=1 " : " show_non_logged=1 ";
  $sql  = " SELECT * FROM " . $table_prefix . "header_links ";
  $sql .= " WHERE " . $header_where;
  $sql .= " AND (layout_id=0 OR layout_id=" . $db->tosql($settings["layout_id"], INTEGER) . ") ";
  $sql .= " ORDER BY menu_order ";
  $db->query($sql);
  while ($db->next_record())
  {
    $menu_id = $db->f("menu_id");
    $menu_url = $db->f("menu_url");
    $menu_page = $db->f("menu_page");
    $menu_friendly_url = get_custom_friendly_url($menu_url);
    $parent_menu_id = $db->f("parent_menu_id");
    $menu_title = get_translation($db->f("menu_title"));
    $menu_image = $db->f("menu_image");
    $menu_image_active = $db->f("menu_image_active");
    $match_type = $db->f("match_type");

    if ($menu_id == $parent_menu_id) {
      $parent_menu_id = 0;
    }
    $menu_basename = basename($menu_friendly_url);
    if ($parent_menu_id == 0 && !$active_menu_id) {
      $url_matched = false;
      if ($match_type > 0) {
        if ($menu_page == $request_page || $menu_page == $current_page || $menu_page == $request_uri_path) {
          $url_matched = true;
        }
        if ($url_matched && $match_type == 2) {
          $menu_request_uri = preg_replace("/\#.*$/", "", $menu_url);
          $menu_request_uri = preg_replace("/^.*\?/", "", $menu_request_uri);
          if ($menu_request_uri) {
            $menu_params = explode("&", $menu_request_uri);
            for($s = 0; $s < sizeof($menu_params); $s++) {
              if (preg_match("/^(.+)=(.+)$/", $menu_params[$s], $matches)) {
                $param_name = $matches[1];
                $menu_param_value = $matches[2];
                $request_param_value = get_param($param_name);
                if (strval($menu_param_value) != strval($request_param_value)) {
                  $url_matched = false;
                }
              }
            }
          }
        }
      }
      if ($url_matched) {
        $active_menu_id = $menu_id;
      }
    }

    if ($menu_url == "index.php") {
      $menu_url = $site_url;
    } if (preg_match("/^\//", $menu_url)) {
      $menu_url = preg_replace("/^".preg_quote($site_path, "/")."/i", "", $menu_url);
      $menu_url = $site_url . get_custom_friendly_url($menu_url);
    } else if (!preg_match("/^http\:\/\//", $menu_url) && !preg_match("/^https\:\/\//", $menu_url) && !preg_match("/^javascript\:/", $menu_url)) {
      $menu_url = $site_url . $menu_friendly_url;
    }


    if (strlen($menu_title) || $menu_image || $menu_image_active) {
      $menu_values = array(
        "menu_id" => $menu_id, "menu_url" => $menu_url,
        "menu_title" => $menu_title, "menu_target" => $db->f("menu_target"),
        "menu_image" => $menu_image, "menu_image_active" => $menu_image_active, "menu_path" => $db->f("menu_path"),
        "submenu_style_name" => $db->f("submenu_style_name"),
      );
      $menu[$parent_menu_id][] = $menu_values;
    }

  }

  if (!$active_menu_id && $show_last_active_menu) { // if there is no active menu use value from session
    $active_menu_id = get_session("session_last_menu_id");
    $active_submenu_id = get_session("session_last_submenu_id");
  }

  foreach ($menu as $parent_menu_id => $menus)
  {
    for ($m = 0; $m < sizeof($menus); $m++) {

      $menu_id = $menus[$m]["menu_id"];
      $menu_url = $menus[$m]["menu_url"];
      $menu_target = $menus[$m]["menu_target"];
      $menu_title = $menus[$m]["menu_title"];
      $menu_image = $menus[$m]["menu_image"];
      $menu_image_active = $menus[$m]["menu_image_active"];
      $menu_submenu_style = $menus[$m]["submenu_style_name"];
      $menu_path = $menus[$m]["menu_path"];
      $menu_path_id = str_replace(",", "_", $menu_path) . $menu_id;

      $t->set_var("menu_path_id", $menu_path_id);

      $menu_style = "menu";

      if ($menu_id == $active_menu_id) {
        $submenu_style_name = $menu_submenu_style;
        $menu_style = "menuActive";
        if ($menu_image_active) {
          $menu_image = $menu_image_active;
        }
        $active_menu_id = $menu_id;
      }

      $t->set_var("menu_href",  htmlspecialchars($menu_url));
      $t->set_var("menu_target",  htmlspecialchars($menu_target));
      $t->set_var("menu_style", $menu_style);
      $t->set_var("menu_title", $menu_title);

      if ($top_menu_type) {
        if($menu_image && file_exists($menu_image) && ($top_menu_type != 2 || !strlen($menu_title)))
        {
          $is_menu_image = true;
          // check image translation
          $slash_pos = strrpos($menu_image, "/");
          if ($slash_pos === false) {
            $menu_image_translation = $language_code . "/" . $menu_image;
          } else {
            $menu_image_translation = substr($menu_image, 0, $slash_pos) . "/" . $language_code . substr($menu_image, $slash_pos);
          }
          if (file_exists($menu_image_translation)) {
            $menu_image = $menu_image_translation;
          }

          $image_size = @GetImageSize($menu_image);
          $t->set_var("alt", htmlspecialchars($menu_title));
          $t->set_var("src", htmlspecialchars($menu_image));
          if (is_array($image_size)) {
            $t->set_var("width", "width=\"" . $image_size[0] . "\"");
            $t->set_var("height", "height=\"" . $image_size[1] . "\"");
          } else {
            $t->set_var("width", "");
            $t->set_var("height", "");
          }
          $t->sparse("menu_image", false);
        } else {
          $is_menu_image = false;
          $t->set_var("menu_image", "");
        }

        if ($top_menu_type != 1 || !$is_menu_image) {
          $t->sparse("menu_text", false);
        } else {
          $t->set_var("menu_text", "");
        }
      } else {
        $t->set_var("menu_image", "");
        $t->set_var("menu_text", "");
      }

      if ($parent_menu_id) {
        $t->sparse("sub_menu", true);
      } else {
        $t->sparse("menu", true);
      }

    }

    // parse sub menus block
    if ($parent_menu_id > 0) {
      $menu_block_id = preg_replace("/,$/", "", $menu_path);
      $menu_block_id = "sm_" . str_replace(",", "_", $menu_block_id);

      $t->set_var("menu_block_id", $menu_block_id);
      $t->sparse("sub_menu_block", true);
      $t->set_var("sub_menu", "");
    }
  }

  // parse secondary menu if available
  if ($active_menu_id) {
    $submenu = array();
    $sql  = " SELECT * FROM " . $table_prefix . "header_submenus ";
    $sql .= " WHERE menu_id=" . $db->tosql($active_menu_id, INTEGER);
    $sql .= " AND (show_for_user=1 ";
    if (get_session("session_user_id")) {
      $sql .= " OR show_for_user=2 ";
    } else {
      $sql .= " OR show_for_user=3 ";
    }
    $sql .= " ) ";
    $sql .= " ORDER BY submenu_order, submenu_title ";
    $db->query($sql);
    while ($db->next_record())
    {
      $submenu_id = $db->f("submenu_id");
      $parent_submenu_id = $db->f("parent_submenu_id");
      $submenu_url = get_custom_friendly_url($db->f("submenu_url"));
      $submenu_title = get_translation($db->f("submenu_title"));
      $submenu_image = $db->f("submenu_image");
      $submenu_image_active = $db->f("submenu_image_active");

      if (strlen($submenu_title) || $submenu_image || $submenu_image_active) {

        $submenu_values = array(
          "submenu_id" => $submenu_id, "submenu_url" => $submenu_url,  "submenu_page" => $db->f("submenu_page"), "submenu_title" => $submenu_title, "submenu_target" => $db->f("submenu_target"),
          "submenu_image" => $submenu_image, "submenu_image_active" => $submenu_image_active, "submenu_path" => $db->f("submenu_path"),
          "match_type" => $db->f("match_type"),
        );

        $submenu[$parent_submenu_id][] = $submenu_values;
      }
    }

    set_session("session_last_menu_id", $active_menu_id);
    if (!$submenu_style_name) { $submenu_style_name = "secondary"; }

    if (sizeof($submenu) > 0) {
      // set styles
      $t->set_var("secondary_table_class", $submenu_style_name . "Menu");
      $t->set_var("secondary_begin_id", $submenu_style_name . "Begin");
      $t->set_var("secondary_end_id", $submenu_style_name . "End");

      foreach ($submenu as $parent_submenu_id => $submenus)
      {
        for ($m = 0; $m < sizeof($submenus); $m++) {

          $submenu_id = $submenus[$m]["submenu_id"];
          $submenu_url = $submenus[$m]["submenu_url"];
          $submenu_page = $submenus[$m]["submenu_page"];
          $submenu_target = $submenus[$m]["submenu_target"];
          $submenu_title = $submenus[$m]["submenu_title"];
          $submenu_image = $submenus[$m]["submenu_image"];
          $submenu_image_active = $submenus[$m]["submenu_image_active"];
          $submenu_path = $submenus[$m]["submenu_path"];
          $match_type = $submenus[$m]["match_type"];
          $submenu_path_id = str_replace(",", "_", $submenu_path) . $submenu_id;

          $t->set_var("submenu_path_id", $submenu_path_id);


          $submenu_active = false;
          if ($submenu_id == $active_submenu_id) {
            $submenu_active = true;
          } else if ($submenu_page == $request_page || $submenu_page == $current_page || $submenu_page == $request_uri_path) {
            if ($match_type == 1) {
              $submenu_active = true;
            } else if ($match_type == 2) {
              $submenu_active = true;
              $submenu_request_uri = preg_replace("/\#.*$/", "", $submenu_url);
              $submenu_request_uri = preg_replace("/^.*\?/", "", $submenu_request_uri);
              if ($submenu_request_uri) {
                $submenu_params = explode("&", $submenu_request_uri);
                for($s = 0; $s < sizeof($submenu_params); $s++) {
                  if (preg_match("/^(.+)=(.+)$/", $submenu_params[$s], $matches)) {
                    $param_name = $matches[1];
                    $submenu_param_value = $matches[2];
                    $request_param_value = get_param($param_name);
                    if (strval($submenu_param_value) != strval($request_param_value)) {
                      $submenu_active = false;
                    }
                  }
                }
              }
            }
          }

          $submenu_style = $submenu_style_name . "Menu";
          if ($submenu_active) {
            set_session("session_last_submenu_id", $submenu_id);
            $submenu_style = $submenu_style_name . "MenuActive";
            if ($submenu_image_active) { $submenu_image = $submenu_image_active; }
          }

          if (!preg_match("/^http\:\/\//", $submenu_url) && !preg_match("/^https\:\/\//", $submenu_url) && !preg_match("/^javascript\:/", $submenu_url)) {
            $submenu_url = $site_url . $submenu_url;
          }

          $t->set_var("secondary_menu_url", $submenu_url);
          $t->set_var("secondary_menu_title", $submenu_title);
          $t->set_var("secondary_menu_target", $submenu_target);
          $t->set_var("secondary_menu_style", $submenu_style);
          $t->set_var("secondary_menu_image", "");
          $t->set_var("secondary_menu_text", "");


          if ($submenu_image && file_exists($submenu_image))
          {
            // check image translation
            $slash_pos = strrpos($submenu_image, "/");
            if ($slash_pos === false) {
              $submenu_image_translation = $language_code . "/" . $submenu_image;
            } else {
              $submenu_image_translation = substr($submenu_image, 0, $slash_pos) . "/" . $language_code . substr($submenu_image, $slash_pos);
            }
            if (file_exists($submenu_image_translation)) {
              $submenu_image = $submenu_image_translation;
            }

            $image_size = @GetImageSize($submenu_image);
            $t->set_var("alt", htmlspecialchars($submenu_title));
            $t->set_var("src", htmlspecialchars($submenu_image));
            if (is_array($image_size)) {
              $t->set_var("width", "width=\"" . $image_size[0] . "\"");
              $t->set_var("height", "height=\"" . $image_size[1] . "\"");
            } else {
              $t->set_var("width", "");
              $t->set_var("height", "");
            }
            $t->sparse("secondary_menu_image", false);
          } else {
            $t->sparse("secondary_menu_text", false);
          }

          //$t->sparse("secondary_menu_items", true);

          if ($parent_submenu_id) {
            $t->sparse("secondary_dropdowns", true);
          } else {
            $t->sparse("secondary_menu_items", true);
          }


        }
        // parse sub menus block
        if ($parent_submenu_id > 0) {
          $submenu_block_id = preg_replace("/,$/", "", $submenu_path);
          $submenu_block_id = "secondary_ddm_" . str_replace(",", "_", $submenu_block_id);

          $t->set_var("submenu_block_id", $submenu_block_id);
          $t->sparse("secondary_dropdown_block", true);
          $t->set_var("secondary_dropdowns", "");
        }
      }

      $t->sparse("secondary_menu", false);
    }

  }


  $t->set_var("index_href", get_custom_friendly_url("index.php"));
  $t->set_var("products_href", get_custom_friendly_url("products.php"));
  $t->set_var("basket_href", get_custom_friendly_url("basket.php"));
  $t->set_var("user_profile_href", get_custom_friendly_url("user_profile.php"));
  $t->set_var("admin_href", "admin.php");
  $t->set_var("help_href", get_custom_friendly_url("page.php") . "?page=help");
  $t->set_var("about_href", get_custom_friendly_url("page.php") . "?page=about");

  // parse some common blocks if they existed in the header
  if ($t->block_exists("small_cart", "header")) {
    include_once("./blocks/block_cart.php");
    small_cart();
  }
  if ($t->block_exists("login_form", "header")) {
    login_form();
  }
  if ($t->block_exists("select_currencies", "header")) {
    include_once("./blocks/block_currency.php");
    currency_form("", 2);
  }
  if ($t->block_exists("currencies_images", "header")) {
    include_once("./blocks/block_currency.php");
    currency_form("", 1);
  }
  if ($t->block_exists("select_languages", "header")) {
    include_once("./blocks/block_language.php");
    language_form("", 2);
  }
  if ($t->block_exists("languages_images", "header")) {
    include_once("./blocks/block_language.php");
    language_form("", 1);
  }
  if ($t->block_exists("search_categories", "header")) {
    include_once("./blocks/block_search.php");
    search_form();
  }


  if (!isset($header_title)) { $header_title = ""; }
  $t->set_var("header_title", $header_title);

  $t->parse("header", false);
  $t->set_var("header", get_currency_message($t->get_var("header"), $currency));

?>
