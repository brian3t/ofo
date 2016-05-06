<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_edit.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once($root_folder_path . "messages/".$language_code."/download_messages.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("update_products");

	$currency = get_currency();
	$html_editor = get_setting_value($settings, "html_editor", 1);

	$operation = get_param("operation");
	$page_show = get_param("page_show");
	$category_id = get_param("category_id");
	$items_ids = get_param("items_ids");
	$total_columns = get_param("total_columns");
	if(!strlen($category_id)) $category_id = "0";

	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_items_list.php?category_id=" . urlencode($category_id);
	$errors = "";

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_products_edit.html");

	$t->set_var("admin_products_edit_href", "admin_products_edit.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");

	$t->set_var("ADD_TO_CART_MSG", ADD_TO_CART_MSG);

	$t->set_var("items_ids", $items_ids);
	$t->set_var("category_id", $category_id);
	$t->set_var("total_columns", $total_columns);

  $t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("html_editor", $html_editor);

	$t->set_var("currency_left", $currency["left"]);
	$t->set_var("currency_right", $currency["right"]);
	$t->set_var("currency_rate", $currency["rate"]);

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

  $table_alias = "i";

	function set_db_column($column_name, $column_title)
	{
		    global $t, $db, $total_columns, $table_alias, $checked_columns;
    		$total_columns++;
    		$column_checked = in_array($table_alias.".".$column_name, $checked_columns) ? " checked " : "";
		    $t->set_var("col", $total_columns);
		    $t->set_var("column_name", htmlspecialchars($column_name));
		    $t->set_var("column_title", htmlspecialchars($column_title));
		    $t->set_var("column_checked", $column_checked);
		    $t->parse("rows", true);
		    if($total_columns % 2 == 0)
        {
			     $t->parse("columns", true);
			     $t->set_var("rows", "");
		    }

	}

		$db_columns = array(
		"is_showing" => FOR_SALES_MSG,
		"is_approved" => IS_APPROVED_MSG,
    "item_order" => PROD_ORDER_MSG,
		"item_type_id" => PROD_TYPE_MSG,
		"item_code" => PROD_CODE_MSG,
		"item_name" => PROD_NAME_MSG,
		"friendly_url" => FRIENDLY_URL_MSG,
		"manufacturer_id" => MANUFACTURER_NAME_MSG,
		"manufacturer_code" =>MANUFACTURER_CODE_MSG,
    "weight" => WEIGHT_MSG,
    "issue_date" => PROD_ISSUE_DATE_MSG,
    "is_compared" => ALLOW_PRODUCT_COMPARISON_MSG,
		"tax_free" => PROD_TAX_FREE_MSG,
		"language_code" => LANGUAGE_MSG,
		"price" => PRICE_MSG,
		"buying_price" => PROD_BUYING_PRICE_MSG,
		"properties_price" => PROD_OPTIONS_PRICE_MSG,
		"trade_properties_price" => OPTIONS_TRADE_PRICE_MSG,
		"is_sales" => PROD_ACTIVATE_DISCOUNT_MSG,
		"sales_price" => PROD_DISCOUNT_PRICE_MSG,
		"trade_price" => PROD_TRADE_PRICE_MSG,
		"trade_sales" => PROD_DISCOUNT_TRADE_MSG,
		"discount_percent" => PROD_DISCOUNT_PERCENT_MSG,
		"short_description" => SHORT_DESCRIPTION_MSG,
		"features" => ADMIN_FEATURES_MSG,
		"full_desc_type" => FULL_DESCRIPTION_TYPE_MSG,
		"full_description" => FULL_DESCRIPTION_MSG,
		"meta_title" => META_TITLE_MSG,
		"meta_keywords" => META_KEYWORDS_MSG,
		"meta_description" => META_DESCRIPTION_MSG,
		"is_special_offer" => IS_SPECIAL_OFFER_MSG,
		"special_offer" => SPECIAL_OFFER_MSG,
		"tiny_image" => IMAGE_TINY_MSG,
		"tiny_image_alt" => IMAGE_TINY_ALT_MSG,
		"small_image" => IMAGE_SMALL_MSG,
		"small_image_alt" => IMAGE_SMALL_ALT_MSG,
		"big_image" => IMAGE_LARGE_MSG,
		"big_image_alt" => IMAGE_LARGE_ALT_MSG,
		"super_image" => IMAGE_SUPER_MSG,
		"template_name" => CUSTOM_TEMPLATE_MSG,
		"hide_add_list" => HIDE_BUTTON_PROD_LIST_MSG,
		"hide_add_details" => HIDE_BUTTON_PROD_DETAILS_MSG,
		"use_stock_level" => USE_STOCK_MSG,
		"stock_level" => STOCK_QUANTITY_MSG,
		"hide_out_of_stock" => HIDE_OFF_LIMITS_PRODUCTS_MSG,
		"disable_out_of_stock" => DISABLE_OUT_STOCK_MSG,
		"downloadable" => IS_DOWNLOADABLE_MSG,
		"download_period" => DOWNLOAD_PERIOD_MSG,
		"download_path" => ADMIN_DOWNLOAD_PATH_MSG,
		"generate_serial" => SERIAL_GENERATE_MSG,
		"serial_period" => SERIAL_PERIOD_MSG,
		"activations_number" => ACTIVATION_MAX_NUMBER_MSG,
		"shipping_in_stock" => IN_STOCK_AVAILABILITY_MSG,
		"shipping_out_stock" => OUT_STOCK_AVAILABILITY_MSG,
		"shipping_rule_id" => SHIPPING_RESTRICTIONS_MSG,
		"votes" => TOTAL_VOTES_MSG,
		"points" => TOTAL_POINTS_MSG,
		"notes" => NOTES_MSG,
		"buy_link" => DIRECT_BUY_LINK_MSG
	);

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

  if (strlen($page_show))
  {
      $total_columns = 0;
      $default_columns = "";
      $sql  = " SELECT edited_item_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		  $default_columns = get_db_value($sql);
	    $checked_columns = explode(",", $default_columns);

	   foreach($db_columns as $column_name => $column_info)
	      set_db_column($column_name, $column_info);
	   if($total_columns % 2 != 0)
	      $t->parse("columns", true);
	   $t->set_var("total_columns", $total_columns);
	   $t->parse("fields", false);
     $t->pparse("main");
	   exit;
  }
  else
  {
      if(!strlen($operation))
      {
			   // generate selected columns
			   $columns_selected = 0;
			   $edit_fields  = "";
			   for($col = 1; $col <= $total_columns; $col++) {
				    $column_name = get_param("db_column_" . $col);
				    $column_title = get_param("column_title_" . $col);
				    if($column_name) {
					     $columns_selected++;
					     if($columns_selected > 1) {
						      $edit_fields .= ",";
					     }
					     $edit_fields .= $table_alias . "." . $column_name;
					     $columns[] = $column_name;
				    }
			   }
			   // update default columns list
			   $sql = " UPDATE " . $table_prefix . "admins SET edited_item_fields=" . $db->tosql($edit_fields, TEXT);
			   $sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
			   $db->query($sql);
     }
     else
     {
        $sql  = " SELECT edited_item_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		    $default_columns = get_db_value($sql);
	      $checked_columns = explode(",", $default_columns);
	      $db_column = array();
        $ii = 1;
	      foreach($db_columns as $column_name => $column_info)
	      {
           if (in_array($table_alias.".".$column_name, $checked_columns))
           {
              $db_column[$ii] = $column_name;
              $ii++;
           }
        }
        $total_columns = $ii - 1;
     }
  }

	$r = new VA_Record($table_prefix . "items", "items");

 for ($i = 1; $i <= $total_columns; $i++)
{
   if (isset($db_column[$i])) {
      $column_name = $db_column[$i];
   }
   else {
      $column_name = get_param("db_column_$i");
   }
   if (strlen($column_name))
   {
      switch ($column_name)
      {
         case "is_showing" :
            $r->add_checkbox("is_showing", INTEGER, FOR_SALES_MSG);
            break;
         case "is_approved" :
         	  $approve_values = array(array(1, YES_MSG), array(0, NO_MSG));
	          $r->add_radio("is_approved", INTEGER, $approve_values, IS_APPROVED_MSG);
            break;
         case "item_order" :
          	$r->add_textbox("item_order", INTEGER, PROD_ORDER_MSG);
	          $r->parameters["item_order"][REQUIRED] = true;
            break;
         case "item_type_id" :
	          $item_types = get_db_values("SELECT item_type_id, item_type_name FROM " . $table_prefix . "item_types", array(array("", "")));
	          $r->add_select("item_type_id", INTEGER, $item_types, PROD_TYPE_MSG);
	          $r->parameters["item_type_id"][REQUIRED] = true;
            break;
         case "item_code" :
            $item_included = true;
	          $r->add_textbox("item_code", TEXT, PROD_CODE_MSG);
	          $r->parameters["item_code"][REQUIRED] = true;
            break;
         case "item_name" :
            $item_included = true;
	          $r->add_textbox("item_name", TEXT, PROD_NAME_MSG);
	          $r->parameters["item_name"][REQUIRED] = true;
            break;
         case "friendly_url" :
	          $r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
						$r->change_property("friendly_url", USE_SQL_NULL, false);
 						$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url", array("is_grid" => true));
						$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
						$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
            break;
         case "manufacturer_id" :
	          $manufacturers = get_db_values("SELECT * FROM " . $table_prefix . "manufacturers", array(array("", NONE_MSG)));
	          $r->add_select("manufacturer_id", INTEGER, $manufacturers, MANUFACTURER_NAME_MSG);
            break;
         case "manufacturer_code" :
	          $r->add_textbox("manufacturer_code", TEXT, MANUFACTURER_CODE_MSG);
            break;
         case "weight" :
          	$r->add_textbox("weight", NUMBER, WEIGHT_MSG);
            break;
         case "issue_date" :
	          $r->add_textbox("issue_date", DATETIME, PROD_ISSUE_DATE_MSG);
	          $r->change_property("issue_date", VALUE_MASK, $date_edit_format);
            break;
         case "is_compared" :
	          $r->add_checkbox("is_compared", INTEGER, ALLOW_PRODUCT_COMPARISON_MSG);
            break;
         case "tax_free" :
	          $r->add_checkbox("tax_free", INTEGER, PROD_TAX_FREE_MSG);
            break;
         case "language_code" :
	          $languages = get_db_values("SELECT language_code, language_name FROM " . $table_prefix . "languages", array(array("", ALL_MSG)));
	          $r->add_select("language_code", TEXT, $languages, LANGUAGE_MSG);
						$r->change_property("language_code", USE_SQL_NULL, false);
            break;
         case "price" :
	          $r->add_textbox("price", NUMBER, PRICE_MSG);
	          $r->parameters["price"][REQUIRED] = true;
            break;
         case "buying_price" :
            $r->add_textbox("buying_price", NUMBER, PROD_BUYING_PRICE_MSG);
            break;
         case "properties_price" :
           	$r->add_textbox("properties_price", NUMBER, PROD_OPTIONS_PRICE_MSG);
            break;
         case "trade_properties_price" :
           	$r->add_textbox("trade_properties_price", NUMBER, OPTIONS_TRADE_PRICE_MSG);
            break;
         case "is_sales" :
          	$r->add_checkbox("is_sales", INTEGER, PROD_ACTIVATE_DISCOUNT_MSG);
         case "sales_price" :
            $r->add_textbox("sales_price", NUMBER, PROD_DISCOUNT_PRICE_MSG);
            break;
         case "trade_price" :
            $r->add_textbox("trade_price", NUMBER, PROD_TRADE_PRICE_MSG);
            break;
         case "trade_sales" :
            $r->add_textbox("trade_sales", NUMBER, PROD_DISCOUNT_TRADE_MSG);
            break;
         case "discount_percent" :
            $r->add_textbox("discount_percent", NUMBER, PROD_DISCOUNT_PERCENT_MSG);
            break;
         case "short_description" :
            $r->add_textbox("short_description", TEXT, SHORT_DESCRIPTION_MSG);
            break;
         case "features" :
            $r->add_textbox("features", TEXT, ADMIN_FEATURES_MSG);
            break;
         case "full_desc_type" :
         	  $content_types =
		        array
            (
			         array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		        );
            $r->add_radio("full_desc_type", INTEGER, $content_types, FULL_DESCRIPTION_TYPE_MSG);
            break;
         case "full_description" :
            $r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);
            break;
         case "meta_title" :
            $r->add_textbox("meta_title", TEXT, META_TITLE_MSG);
            break;
         case "meta_keywords" :
            $r->add_textbox("meta_keywords", TEXT, META_KEYWORDS_MSG);
            break;
         case "meta_description" :
            $r->add_textbox("meta_description", TEXT, META_DESCRIPTION_MSG);
            break;
         case "is_special_offer" :
            $r->add_checkbox("is_special_offer", INTEGER, IS_SPECIAL_OFFER_MSG);
            break;
         case "special_offer" :
            $r->add_textbox("special_offer", TEXT, SPECIAL_OFFER_TITLE);
            break;
         case "tiny_image" :
            $r->add_textbox("tiny_image", TEXT, IMAGE_TINY_MSG);
            break;
         case "tiny_image_alt" :
            $r->add_textbox("tiny_image_alt", TEXT, IMAGE_TINY_ALT_MSG);
            break;
         case "small_image" :
            $r->add_textbox("small_image", TEXT, IMAGE_SMALL_MSG);
            break;
         case "small_image_alt" :
            $r->add_textbox("small_image_alt", TEXT, IMAGE_SMALL_ALT_MSG);
            break;
         case "big_image" :
            $r->add_textbox("big_image", TEXT, IMAGE_LARGE_MSG);
            break;
         case "big_image_alt" :
            $r->add_textbox("big_image_alt", TEXT, IMAGE_LARGE_ALT_MSG);
            break;
         case "super_image" :
            $r->add_textbox("super_image", TEXT, IMAGE_SUPER_MSG);
            break;
         case "template_name" :
            $r->add_textbox("template_name", TEXT, CUSTOM_TEMPLATE_MSG);
            break;
         case "hide_add_list" :
            $r->add_checkbox("hide_add_list", INTEGER, HIDE_BUTTON_PROD_LIST_MSG);
            break;
         case "hide_add_details" :
            $r->add_checkbox("hide_add_details", INTEGER, HIDE_BUTTON_PROD_DETAILS_MSG);
            break;
         case "use_stock_level" :
            $r->add_checkbox("use_stock_level", INTEGER, USE_STOCK_MSG);
            break;
         case "stock_level" :
            $r->add_textbox("stock_level", NUMBER, STOCK_QUANTITY_MSG);
            break;
         case "hide_out_of_stock" :
            $r->add_checkbox("hide_out_of_stock", INTEGER, HIDE_OFF_LIMITS_PRODUCTS_MSG);
            break;
         case "disable_out_of_stock" :
            $r->add_checkbox("disable_out_of_stock", INTEGER, DISABLE_OUT_STOCK_MSG);
            break;
         case "downloadable" :
            $r->add_checkbox("downloadable", INTEGER, "Is Downloadable");
            break;
         case "download_period" :
            $r->add_textbox("download_period", INTEGER, DOWNLOAD_PERIOD_MSG);
            break;
         case "download_path" :
            $r->add_textbox("download_path", TEXT, DOWNLOAD_PATH_MSG);
            break;
         case "generate_serial" :
            $r->add_checkbox("generate_serial", INTEGER, SERIAL_GENERATE_MSG);
            break;
         case "serial_period" :
            $r->add_textbox("serial_period", INTEGER, DOWNLOAD_PERIOD_MSG);
            break;
         case "activations_number" :
            $r->add_textbox("activations_number", INTEGER, ACTIVATION_MAX_NUMBER_MSG);
            break;
         case "shipping_in_stock" :
            $times = get_db_values("SELECT * FROM " . $table_prefix . "shipping_times", array(array("", NONE_MSG)), 90);
	          $r->add_select("shipping_in_stock", INTEGER, $times, IN_STOCK_AVAILABILITY_MSG);
            break;
         case "shipping_out_stock" :
            $times = get_db_values("SELECT * FROM " . $table_prefix . "shipping_times", array(array("", NONE_MSG)), 90);
	          $r->add_select("shipping_out_stock", INTEGER, $times, OUT_STOCK_AVAILABILITY_MSG);
            break;
         case "shipping_rule_id" :
            $rules = get_db_values("SELECT * FROM " . $table_prefix . "shipping_rules", array(array("", NONE_MSG)), 90);
	          $r->add_select("shipping_rule_id", INTEGER, $rules, SHIPPING_RESTRICTIONS_MSG);
            break;
         case "votes" :
            $r->add_textbox("votes", INTEGER, TOTAL_VOTES_MSG);
            break;
         case "points" :
            $r->add_textbox("points", INTEGER, TOTAL_POINTS_MSG);
            break;
         case "notes" :
            $r->add_textbox("notes", TEXT, NOTES_MSG);
            break;
         case "buy_link" :
            $r->add_textbox("buy_link", TEXT, DIRECT_BUY_LINK_MSG);
            break;

      }

   }

}


    	$r->add_hidden("item_id", INTEGER);
	    $r->change_property("item_id", USE_IN_WHERE, true);
      if (!isset($item_included))
         $r->add_hidden("item_name_hid", TEXT);


	    $number_items = get_param("number_items");
    	$eg = new VA_EditGrid($r, "items");

		  for($i = 1; $i <= $number_items; $i++)
		  {
			   foreach ($eg->record->parameters as $parameter_name => $parameter)
			   {
				    $form_value = get_param($eg->record->parameters[$parameter_name][CONTROL_NAME] . "_" . $i);
    				if($eg->record->parameters[$parameter_name][CONTROL_TYPE] == CHECKBOX && !strlen($form_value)) {
  					   $form_value = 0;
  			    }
      			$eg->values[$i][$parameter_name] = $form_value;
			   }
		  }

if(strlen($operation))
{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate();

		if($is_valid)
		{
		  $number_items = $number_items ? $number_items : get_param("number_" . $eg->block_name);
		  for($i = 1; $i <= $number_items; $i++)
		  {
			  $non_empty = $eg->set_record($i);
			  $is_all_where = $eg->record->check_where();
        if($is_all_where)
        {
				    $eg->record->update_record();
			  }
        else if($non_empty)
        {
				    $eg->record->insert_record();
			  }
		  }
			header("Location: " . $return_page);
			exit;
		}
}
else
{


  		// manually get items
	  	$number_items = 0;
		  $sql  = " SELECT *";
		  $sql .= " FROM " . $table_prefix . "items ";
		  $sql .= " WHERE item_id IN (" . $db->tosql($items_ids, TEXT, false) . ")";

  		$db->query($sql);
	  	while($db->next_record())
		  {
			  $number_items++;
			  $eg->values[$number_items]["item_id"] = $db->f("item_id");
		    $eg->values[$number_items]["is_showing"] = $db->f("is_showing");
		    $eg->values[$number_items]["is_approved"] = $db->f("is_approved");
			  $eg->values[$number_items]["item_order"] = $db->f("item_order");
			  $eg->values[$number_items]["item_type_id"] = $db->f("item_type_id");
			  $eg->values[$number_items]["item_name_hid"] = $db->f("item_name");
			  $eg->values[$number_items]["item_code"] = $db->f("item_code");
			  $eg->values[$number_items]["item_name"] = $db->f("item_name");
			  $eg->values[$number_items]["friendly_url"] = $db->f("friendly_url");
			  $eg->values[$number_items]["manufacturer_id"] = $db->f("manufacturer_id");
			  $eg->values[$number_items]["manufacturer_code"] = $db->f("manufacturer_code");
			  $eg->values[$number_items]["issue_date"] = $db->f("issue_date", DATETIME);
			  $eg->values[$number_items]["weight"] = $db->f("weight");
			  $eg->values[$number_items]["is_compared"] = $db->f("is_compared");
			  $eg->values[$number_items]["tax_free"] = $db->f("tax_free");
			  $eg->values[$number_items]["language_code"] = $db->f("language_code");
			  $eg->values[$number_items]["price"] = $db->f("price");
			  $eg->values[$number_items]["buying_price"] = $db->f("buying_price");
			  $eg->values[$number_items]["properties_price"] = $db->f("properties_price");
			  $eg->values[$number_items]["trade_properties_price"] = $db->f("trade_properties_price");
			  $eg->values[$number_items]["is_sales"] = $db->f("is_sales");
			  $eg->values[$number_items]["sales_price"] = $db->f("sales_price");
			  $eg->values[$number_items]["trade_price"] = $db->f("trade_price");
			  $eg->values[$number_items]["trade_sales"] = $db->f("trade_sales");
			  $eg->values[$number_items]["discount_percent"] = $db->f("discount_percent");
			  $eg->values[$number_items]["short_description"] = $db->f("short_description");
			  $eg->values[$number_items]["features"] = $db->f("features");
			  $eg->values[$number_items]["full_desc_type"] = $db->f("full_desc_type");
			  $eg->values[$number_items]["full_description"] = $db->f("full_description");
			  $eg->values[$number_items]["meta_title"] = $db->f("meta_title");
			  $eg->values[$number_items]["meta_keywords"] = $db->f("meta_keywords");
			  $eg->values[$number_items]["meta_description"] = $db->f("meta_description");
			  $eg->values[$number_items]["is_special_offer"] = $db->f("is_special_offer");
			  $eg->values[$number_items]["special_offer"] = $db->f("special_offer");
			  $eg->values[$number_items]["tiny_image"] = $db->f("tiny_image");
			  $eg->values[$number_items]["tiny_image_alt"] = $db->f("tiny_image_alt");
			  $eg->values[$number_items]["small_image"] = $db->f("small_image");
			  $eg->values[$number_items]["small_image_alt"] = $db->f("small_image_alt");
			  $eg->values[$number_items]["big_image"] = $db->f("big_image");
			  $eg->values[$number_items]["big_image_alt"] = $db->f("big_image_alt");
			  $eg->values[$number_items]["super_image"] = $db->f("super_image");
			  $eg->values[$number_items]["template_name"] = $db->f("template_name");
			  $eg->values[$number_items]["hide_add_list"] = $db->f("hide_add_list");
			  $eg->values[$number_items]["hide_add_details"] = $db->f("hide_add_details");
			  $eg->values[$number_items]["use_stock_level"] = $db->f("use_stock_level");
			  $eg->values[$number_items]["stock_level"] = $db->f("stock_level");
			  $eg->values[$number_items]["hide_out_of_stock"] = $db->f("hide_out_of_stock");
			  $eg->values[$number_items]["disable_out_of_stock"] = $db->f("disable_out_of_stock");
			  $eg->values[$number_items]["downloadable"] = $db->f("downloadable");
			  $eg->values[$number_items]["download_period"] = $db->f("download_period");
			  $eg->values[$number_items]["download_path"] = $db->f("download_path");
			  $eg->values[$number_items]["generate_serial"] = $db->f("generate_serial");
			  $eg->values[$number_items]["serial_period"] = $db->f("serial_period");
			  $eg->values[$number_items]["activations_number"] = $db->f("activations_number");
			  $eg->values[$number_items]["shipping_in_stock"] = $db->f("shipping_in_stock");
			  $eg->values[$number_items]["shipping_out_stock"] = $db->f("shipping_out_stock");
			  $eg->values[$number_items]["shipping_rule_id"] = $db->f("shipping_rule_id");
			  $eg->values[$number_items]["votes"] = $db->f("votes");
			  $eg->values[$number_items]["points"] = $db->f("points");
			  $eg->values[$number_items]["notes"] = $db->f("notes");
			  $eg->values[$number_items]["buy_link"] = $db->f("buy_link");
			}

}


	$t->set_var("number_items", $number_items);
	if (!isset($is_valid))
     $is_valid = true;
if (!isset($item_included) && $is_valid)
{
		for($i = 1; $i <= $number_items; $i++)
		{
			$t->set_var("item_name", $eg->values[$i]["item_name"]);
			$t->set_var("item_name_hid", $eg->values[$i]["item_name"]);
			$t->set_var($eg->block_name . "_number", $i);
			$eg->set_record($i);
			$eg->record->set_parameters();
			$t->parse($eg->block_name, true);
		}
}
else if (!$is_valid)
{
		for($i = 1; $i <= $number_items; $i++)
		{
			$t->set_var($eg->block_name . "_number", $i);
			$eg->set_record($i);
			$eg->record->set_parameters();
      if (!isset($item_included))
      {
         $t->set_var("item_name", $eg->values[$i]["item_name_hid"]);
         $t->set_var("item_name", $eg->values[$i]["item_name_hid"]);
      }
      $errors_list = $eg->errors[$i];
			if ($errors_list) {
	      $t->set_var("errors_list", $errors_list);
  	    $t->parse("items_errors", false);
			} else {
  	    $t->set_var("items_errors", "");
			}
			$t->parse($eg->block_name, true);
		}
}
else
  $eg->set_parameters_all($number_items);

  $t->set_var("rp", htmlspecialchars($return_page));

  $t->parse("items_rows", false);
	$t->pparse("main");

?>
