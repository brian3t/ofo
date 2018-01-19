<?php
require_once dirname(__DIR__) . "/common/Product.php";

function products_list($block_name, $list_template, $current_category_name, $page_friendly_url, $page_friendly_params, $show_sub_products, $category_path)
{
    global $t, $db, $db_type, $site_id, $table_prefix;
    global $settings, $page_settings;
    global $category_id, $language_code;
    global $sc_item_id, $sc_category_id, $item_added, $sc_errors;
    global $currency, $filter_properties;

    if (get_setting_value($page_settings, $block_name . "_column_hide", 0)){
        return;
    }

    $shopping_cart = get_session("shopping_cart");
    $records_per_page = get_setting_value($page_settings, "products_per_page", 10);
    $columns = get_setting_value($page_settings, "products_columns", 1);
    $products_default_view = get_setting_value($page_settings, "products_default_view", "list");
    $products_group_by_cats = get_setting_value($page_settings, "products_group_by_cats", 0);
    $products_sortings = get_setting_value($page_settings, "products_sortings", 1);
    if ($products_default_view == "table"){
        $list_template = "block_products_table_view.html";
        $columns = 1;
    } elseif (!strlen($list_template)) {
        $list_template = "block_products_list.html";
    }

    $t->set_file("block_body", $list_template);
    $t->set_var("items_cols", "");
    $t->set_var("items_rows", "");
    $t->set_var("PRODUCT_OUT_STOCK_MSG", htmlspecialchars(PRODUCT_OUT_STOCK_MSG));
    $t->set_var("out_stock_alert", str_replace("'", "\\'", htmlspecialchars(PRODUCT_OUT_STOCK_MSG)));

    $user_info = get_session("session_user_info");
    $user_tax_free = get_setting_value($user_info, "tax_free", 0);
    $discount_type = get_setting_value($user_info, "discount_type", "");
    $discount_amount = get_setting_value($user_info, "discount_amount", "");

    $quantity_control = get_setting_value($settings, "quantity_control_list", "");
    $tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
    $display_products = get_setting_value($settings, "display_products", 0);
    $show_item_code = get_setting_value($settings, "item_code_list", 0);
    $show_manufacturer_code = get_setting_value($settings, "manufacturer_code_list", 0);
    $php_in_short_desc = get_setting_value($settings, "php_in_products_short_desc", 0);
    $php_in_features = get_setting_value($settings, "php_in_products_features", 0);

    $shop_hide_add_list = get_setting_value($settings, "hide_add_list", 0);
    $shop_hide_view_list = get_setting_value($settings, "hide_view_list", 0);
    $shop_hide_checkout_list = get_setting_value($settings, "hide_checkout_list", 0);
    $shop_hide_wishlist_list = get_setting_value($settings, "hide_wishlist_list", 0);
    $weight_measure = get_setting_value($settings, "weight_measure", "");
    $friendly_urls = get_setting_value($settings, "friendly_urls", 0);
    $friendly_extension = get_setting_value($settings, "friendly_extension", "");
    $stock_level_list = get_setting_value($settings, "stock_level_list", 0);

    $points_system = get_setting_value($settings, "points_system", 0);
    $points_conversion_rate = get_setting_value($settings, "points_conversion_rate", 1);
    $points_decimals = get_setting_value($settings, "points_decimals", 0);
    $points_price_list = get_setting_value($settings, "points_price_list", 0);
    $reward_points_list = get_setting_value($settings, "reward_points_list", 0);
    $points_prices = get_setting_value($settings, "points_prices", 0);

    // credit settings
    $credit_system = get_setting_value($settings, "credit_system", 0);
    $reward_credits_users = get_setting_value($settings, "reward_credits_users", 0);
    $reward_credits_list = get_setting_value($settings, "reward_credits_list", 0);

    // new product settings
    $new_product_enable = get_setting_value($settings, "new_product_enable", 0);
    $new_product_order = get_setting_value($settings, "new_product_order", 0);

    // get products reviews settings
    $reviews_settings = get_settings("products_reviews");
    $reviews_allowed_view = get_setting_value($reviews_settings, "allowed_view", 0);
    $reviews_allowed_post = get_setting_value($reviews_settings, "allowed_post", 0);

    $product_params = prepare_product_params();

    $user_id = get_session("session_user_id");
    $user_type_id = get_session("session_user_type_id");
    $price_type = get_session("session_price_type");
    if ($price_type == 1){
        $price_field = "trade_price";
        $sales_field = "trade_sales";
        $properties_field = "trade_properties_price";
    } else {
        $price_field = "price";
        $sales_field = "sales_price";
        $properties_field = "properties_price";
    }

    $watermark = false;
    $restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
    if ($products_default_view == "table"){
        $price_matrix_list = false;
        $product_no_image = get_setting_value($settings, "product_no_image_tiny", "");
        $image_field = "tiny_image";
        $image_field_alt = "tiny_image_alt";
        $watermark = get_setting_value($settings, "watermark_tiny_image", 0);
        $image_type_name = "tiny";
    } else {
        $price_matrix_list = get_setting_value($settings, "price_matrix_list", 0);
        $product_no_image = get_setting_value($settings, "product_no_image", "");
        $image_field = "small_image";
        $image_field_alt = "small_image_alt";
        $watermark = get_setting_value($settings, "watermark_small_image", 0);
        $image_type_name = "small";
    }

    srand((double)microtime() * 1000000);
    $random_value = rand();
    $current_ts = va_timestamp();

    $category_id = get_param("category_id");
    $search_category_id = get_param("search_category_id");
    $search_string = trim(get_param("search_string"));
    $pq = get_param("pq");
    $fq = get_param("fq");
    $s_tit = get_param("s_tit");
    $s_cod = get_param("s_cod");
    $s_sds = get_param("s_sds");
    $s_fds = get_param("s_fds");
    $manf = get_param("manf");
    $user = get_param("user");
    if ($display_products != 2 || strlen($user_id)){
        $lprice = get_param("lprice");
        $hprice = get_param("hprice");
    } else {
        $lprice = "";
        $hprice = "";
    }
    $lweight = get_param("lweight");
    $hweight = get_param("hweight");
    $page = get_param("page");
    $is_search = (strlen($search_string) || ($pq > 0) || ($fq > 0) || strlen($lprice) || strlen($hprice) || strlen($lweight) || strlen($hweight));
    $is_manufacturer = strlen($manf);
    $is_user = strlen($user);
    $sort_ord = get_param("sort_ord");
    $sort_dir = get_param("sort_dir");
    $filter = get_param("filter");

    if (strlen($search_category_id)){
        $category_id = $search_category_id;
    }
    if (!strlen($category_id)) $category_id = "0";

    if ($friendly_urls && $page_friendly_url){
        $products_page = $page_friendly_url . $friendly_extension;
    } elseif ($is_search) {
        $products_page = get_custom_friendly_url("products_search.php");
    } else {
        $products_page = get_custom_friendly_url("products.php");
    }
    if ($is_search){
        $products_form_url = "products_search.php";
    } else {
        $products_form_url = "products.php";
    }
    $t->set_var("products_href", $products_page);
    $t->set_var("products_form_url", $products_form_url);
    $t->set_var("product_details_href", get_custom_friendly_url("product_details.php"));
    $t->set_var("basket_href", get_custom_friendly_url("basket.php"));
    $t->set_var("checkout_href", get_custom_friendly_url("checkout.php"));
    $t->set_var("reviews_href", get_custom_friendly_url("reviews.php"));
    $t->set_var("compare_href", get_custom_friendly_url("compare.php"));
    $t->set_var("cl", $currency["left"]);
    $t->set_var("cr", $currency["right"]);
    $t->set_var("category_id", htmlspecialchars($category_id));
    $t->set_var("tax_prices_type", $tax_prices_type);

    $pass_parameters = array(
        "category_id" => $category_id, "search_string" => $search_string,
        "search_category_id" => $search_category_id, "pq" => $pq, "fq" => $fq,
        "s_tit" => $s_tit, "s_cod" => $s_cod, "s_sds" => $s_sds, "s_fds" => $s_fds,
        "manf" => $manf, "user" => $user, "lprice" => $lprice, "hprice" => $hprice,
        "lweight" => $lweight, "hweight" => $hweight,
        "sort_ord" => $sort_ord, "sort_dir" => $sort_dir, "filter" => $filter,
    );

    $t->set_var("current_category_name", $current_category_name);

    $pr_where = "";
    $pr_brackets = "";
    $pr_join = "";
    if ($pq > 0){
        for ($pi = 1;$pi <= $pq;$pi++){
            $property_name = get_param("pn_" . $pi);
            $property_value = get_param("pv_" . $pi);
            if (strlen($property_name) && strlen($property_value)){
                $pass_parameters["pn_" . $pi] = $property_name;
                $pass_parameters["pv_" . $pi] = $property_value;

                $pr_where .= " AND ip_" . $pi . ".property_name=" . $db->tosql($property_name, TEXT);
                $pr_where .= " AND (ip_" . $pi . ".property_description LIKE '%" . $db->tosql($property_value, TEXT, false) . "%' ";
                $pr_where .= " OR ipv_" . $pi . ".property_value LIKE '%" . $db->tosql($property_value, TEXT, false) . "%') ";
                $pr_brackets .= "((";
                $pr_join .= " LEFT JOIN " . $table_prefix . "items_properties ip_" . $pi . " ON i.item_id = ip_" . $pi . ".item_id) ";
                $pr_join .= " LEFT JOIN " . $table_prefix . "items_properties_values ipv_" . $pi . " ON ipv_" . $pi . ".property_id= ip_" . $pi . ".property_id) ";
            }
        }
    }
    if ($fq > 0){
        for ($fi = 1;$fi <= $fq;$fi++){
            $feature_name = get_param("fn_" . $fi);
            $feature_value = get_param("fv_" . $fi);
            if (strlen($feature_name) && strlen($feature_value)){
                $pass_parameters["fn_" . $fi] = $feature_name;
                $pass_parameters["fv_" . $fi] = $feature_value;

                $pr_where .= " AND f_" . $fi . ".feature_name=" . $db->tosql($feature_name, TEXT);
                $pr_where .= " AND f_" . $fi . ".feature_value LIKE '%" . $db->tosql($feature_value, TEXT, false) . "%' ";
                $pr_brackets .= "(";
                $pr_join .= " LEFT JOIN " . $table_prefix . "features f_" . $fi . " ON i.item_id = f_" . $fi . ".item_id) ";
            }
        }
    }
    filter_sqls($pr_brackets, $pr_join, $pr_where);

    $sql_params = array();
    $sql_params["brackets"] = $pr_brackets . "((";
    $sql_params["join"] = " INNER JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
    if (($is_search || $is_manufacturer || $show_sub_products) && $category_id != 0){
        $sql_params["join"] .= "INNER JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id)";
    } else {
        $sql_params["join"] .= ")";
    }
    $sql_params["join"] .= $pr_join;

    $sql_where = "";
    if (($is_search || $is_manufacturer || $show_sub_products) && $category_id != 0){
        if (strlen($sql_where)) $sql_where .= " AND ";
        $sql_where .= " (ic.category_id = " . $db->tosql($category_id, INTEGER);
        $sql_where .= " OR c.category_path LIKE '" . $db->tosql($category_path, TEXT, false) . "%')";
    } elseif (!$is_search && !$is_manufacturer && !$is_user) {
        if (strlen($sql_where)) $sql_where .= " AND ";
        $sql_where .= " ic.category_id = " . $db->tosql($category_id, INTEGER);
    }
    if (strlen($manf)){
        if (strlen($sql_where)) $sql_where .= " AND ";
        $sql_where .= " i.manufacturer_id= " . $db->tosql($manf, INTEGER);
    }
    if (strlen($user)){
        if (strlen($sql_where)) $sql_where .= " AND ";
        $sql_where .= " i.user_id= " . $db->tosql($user, INTEGER);
    }
    if (strlen($lprice)){

        if (strlen($sql_where)) $sql_where .= " AND ";
        $conv_price = $lprice / $currency["rate"];
        $sql_where .= " ( ";
        $sql_where .= " (i.is_sales=1 AND (i." . $sales_field . "+i." . $properties_field . ")>=" . $db->tosql($conv_price, NUMBER) . ") ";
        $sql_where .= " OR ((i.is_sales<>1 OR i.is_sales IS NULL) AND (i." . $price_field . "+i." . $properties_field . ")>= " . $db->tosql($conv_price, NUMBER) . ") ";
        $sql_where .= ") ";
    }
    if (strlen($hprice)){

        if (strlen($sql_where)) $sql_where .= " AND ";
        $conv_price = $hprice / $currency["rate"];
        $sql_where .= " ( ";
        $sql_where .= " (i.is_sales=1 AND (i." . $sales_field . "+i." . $properties_field . ")<=" . $db->tosql($conv_price, NUMBER) . ") ";
        $sql_where .= " OR ((i.is_sales<>1 OR i.is_sales IS NULL) AND (i." . $price_field . "+i." . $properties_field . ")<= " . $db->tosql($conv_price, NUMBER) . ") ";
        $sql_where .= ") ";
    }
    if (strlen($lweight)){
        if (strlen($sql_where)) $sql_where .= " AND ";
        $sql_where .= " i.weight>=" . $db->tosql($lweight, NUMBER);
    }
    if (strlen($hweight)){
        if (strlen($sql_where)) $sql_where .= " AND ";
        $sql_where .= " i.weight<=" . $db->tosql($hweight, NUMBER);
    }
    if (strlen($search_string)){
        $search_values = split(" ", $search_string);
        for ($si = 0;$si < sizeof($search_values);$si++){
            $s_fields = 0;
            if (strlen($sql_where)) $sql_where .= " AND ";
            $sql_where .= " ( ";
            if ($s_sds == 1){
                $s_fields++;
                $sql_where .= " i.short_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
            }
            if ($s_fds == 1){
                if ($s_fields > 0){
                    $sql_where .= " OR ";
                }
                $s_fields++;
                $sql_where .= " i.full_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
            }
            if ($s_tit == 1){
                if ($s_fields > 0){
                    $sql_where .= " OR ";
                }
                $s_fields++;
                $sql_where .= " i.item_name LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
            }
            if ($s_cod == 1){
                if ($s_fields > 0){
                    $sql_where .= " OR ";
                }
                $s_fields++;
                $sql_where .= " i.item_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
                $sql_where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
            }
            if ($s_fields == 0){
                $sql_where .= " i.item_name LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
                $sql_where .= " OR i.item_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
                $sql_where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
                $sql_where .= " OR i.short_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
                $sql_where .= " OR i.full_description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'";
            }
            $sql_where .= " ) ";
        }
    }
    $sql_where .= $pr_where;
    $sql_params["where"] = $sql_where;
    if ($products_group_by_cats){
        $sql_params["distinct"] = " ic.category_id, i.item_id";
    } else {
        $sql_params["distinct"] = " i.item_id";
    }

    $total_records = VA_Products::count($sql_params, VIEW_CATEGORIES_ITEMS_PERM);
    $sql_params["distinct"] = "";

    $details_parameters = $pass_parameters; // use all parameters for details page
    if ($friendly_urls && $page_friendly_url){
        for ($fp = 0;$fp < sizeof($page_friendly_params);$fp++){
            unset($pass_parameters[$page_friendly_params[$fp]]);
        }
    }

    $s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", $products_page, "sort", "", $pass_parameters);
    // use products order for category only if results grouped by categories or it is only one category products available
    $category_order = ($products_group_by_cats || (!$show_sub_products && ($category_id || (!$is_search && !$is_manufacturer && !$is_user))));
    if ($products_sortings){
        $s->set_parameters(false, true, true, false);
        $s->set_default_sorting(1, "asc");
        if ($category_order){
            $s->set_sorter(PROD_SORT_DEFAULT_MSG, "sorter_default", "1", "ic.item_order, i.item_order, i.item_id", "ic.item_order, i.item_order, i.item_id", "ic.item_order DESC, i.item_order, i.item_id");
        } else {
            $s->set_sorter(PROD_SORT_DEFAULT_MSG, "sorter_default", "1", "i.item_order, i.item_id", "i.item_order, i.item_id", "i.item_order DESC, i.item_id");
        }
        if ($db_type == "mysql"){
            $s->set_sorter(PRICE_MSG, "sorter_price", "2", "i.price", "IF(i.is_sales=1, i.sales_price + COALESCE(i.properties_price,0), i.price + COALESCE(i.properties_price,0) )", "IF(i.is_sales=1, i.sales_price + COALESCE(i.properties_price,0), i.price + COALESCE(i.properties_price,0) ) DESC");
            //$s->set_sorter(PRICE_MSG, "sorter_price", "2", "i.price", "i.price", "i.price");
        } elseif ($db_type == "access") {
            $s->set_sorter(PRICE_MSG, "sorter_price", "2", "i.price", "IIF(i.is_sales=1, (i.sales_price + IIF(ISNULL(i.properties_price),0,i.properties_price)), (i.price + IIF(ISNULL(i.properties_price),0,i.properties_price)) )", "IIF(i.is_sales=1, (i.sales_price + IIF(ISNULL(i.properties_price),0,i.properties_price)), (i.price + IIF(ISNULL(i.properties_price),0,i.properties_price)) ) DESC");
        } elseif ($db_type == "postgre") {
            $s->set_sorter(PRICE_MSG, "sorter_price", "2", "i.price", "(CASE WHEN i.is_sales=1 THEN i.sales_price + COALESCE(i.properties_price,0) ELSE i.price + COALESCE(i.properties_price,0) END)", "(CASE WHEN i.is_sales=1 THEN i.sales_price + COALESCE(i.properties_price,0) ELSE i.price + COALESCE(i.properties_price,0) END) DESC");
        }
        $s->set_sorter(PROD_SORT_MANUFACTURER_MSG, "sorter_manufacturer", "3", "m.manufacturer_name, i.item_id", "m.manufacturer_name, i.item_id", "m.manufacturer_name DESC, i.item_id");
        $s->set_sorter(NAME_MSG, "sorter_name", "4", "i.item_name, i.item_id", "i.item_name, i.item_id", "i.item_name DESC, i.item_id");
        if ($show_manufacturer_code){
            $s->set_sorter(PROD_SORT_CODE_MSG, "sorter_code", "5", "i.manufacturer_code, i.item_id", "i.manufacturer_code, i.item_id", "i.manufacturer_code DESC, i.item_id");
        } else {
            $s->set_sorter(PROD_SORT_CODE_MSG, "sorter_code", "5", "i.item_code, i.item_id", "i.item_code, i.item_id", "i.item_code DESC, i.item_id");
        }
        $t->sparse("products_sortings", false);
    } else {
        if ($category_order){
            $s->order_by = " ORDER BY ic.item_order, i.item_order ";
        } else {
            $s->order_by = " ORDER BY i.item_order ";
        }
    }
    if ($products_group_by_cats){
        // when we are grouping by categories we should always have order by categories first
        if (($is_search || $is_manufacturer || $show_sub_products) && $category_id != 0){
            $s->order_by = str_replace("ORDER BY", "ORDER BY c.category_order, ic.category_id,", $s->order_by);
        } else {
            $s->order_by = str_replace("ORDER BY", "ORDER BY ic.category_id,", $s->order_by);
        }
    }


    // set up variables for navigator
    $n = new VA_Navigator($settings["templates_dir"], "navigator.html", $products_page);

    $products_nav_type = get_setting_value($page_settings, "products_nav_type", 1);
    $products_nav_pages = get_setting_value($page_settings, "products_nav_pages", 5);
    $products_nav_first_last = get_setting_value($page_settings, "products_nav_first_last", 0);
    $products_nav_prev_next = get_setting_value($page_settings, "products_nav_prev_next", 1);
    $inactive_links = false;

    $n->set_parameters($products_nav_first_last, $products_nav_prev_next, $inactive_links);
    $page_number = $n->set_navigator("navigator", "page", $products_nav_type, $products_nav_pages, $records_per_page, $total_records, false, $pass_parameters);
    $total_pages = ceil($total_records / $records_per_page);

    // generate page link with query parameters
    $pass_parameters["page"] = $page;
    $query_string = get_query_string($pass_parameters, "", "", false);
    $rp = $products_page;
    $rp .= $query_string;
    $cart_link = $rp;
    $cart_link .= strlen($query_string) ? "&" : "?";
    $cart_link .= "rnd=" . $random_value . "&";

    // set hidden parameter with category_id parameter
    $hidden_parameters = $pass_parameters;
    $hidden_parameters["category_id"] = $category_id;
    get_query_string($hidden_parameters, "", "", true);

    // remove page and sorting parameters from url
    $details_query = get_query_string($details_parameters, array("page", "sort_ord", "sort_dir"), "", false); //** EGGHEAD ADD category_id to remove from friendly URL (removed for now)
    $product_link = get_custom_friendly_url("product_details.php") . $details_query;
    $product_link .= strlen($details_query) ? "&" : "?";
    $product_link .= "item_id=";
    $reviews_link = get_custom_friendly_url("reviews.php") . $details_query;
    $reviews_link .= strlen($details_query) ? "&" : "?";
    $reviews_link .= "item_id=";

    $t->set_var("rnd", $random_value);
    $t->set_var("rp_url", urlencode($rp));
    $t->set_var("rp", htmlspecialchars($rp));
    $t->set_var("total_records", $total_records);
    $t->set_var("search_string", htmlspecialchars($search_string));

    if ($total_records){

        $order_columns = $s->order_columns;
        if ($products_group_by_cats){

            if ($order_columns){
                $group_by = "ic.category_id, i.item_id, " . $order_columns;
            } else {
                $group_by = "ic.category_id, i.item_id";
            }
            if (($is_search || $is_manufacturer || $show_sub_products) && $category_id != 0){
                $group_by .= ", c.category_order";
            }

            $sql_params["select"] = " i.item_id, ic.category_id";
            $sql_params["group"] = $group_by;
            $sql_params["order"] = $s->order_by;
        } else {

            if ($order_columns && $sort_ord != 2){
                $group_by = $order_columns;
            } else {
                $group_by = "i.item_id";
            }
            $sql_params["select"] = " i.item_id ";
            $sql_params["group"] = $group_by;
            $sql_params["order"] = $s->order_by;
        }
        if (preg_match("/m\.manufacturer_name/", $s->order_by)){

            // join manufacturer table to order by manufacturer_name
            $sql_params["brackets"] .= "(";
            $sql_params["join"] .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
        }

        $ids = VA_Products::data($sql_params, VIEW_CATEGORIES_ITEMS_PERM, $records_per_page, $page_number);

        $items_where = "";
        $items_ids = array();
        for ($id = 0;$id < sizeof($ids);$id++){
            $items_ids[] = $ids[$id]["item_id"];
            if ($products_group_by_cats){
                if ($items_where){
                    $items_where .= " OR ";
                }
                $items_where .= "(ic.item_id=" . $db->tosql($ids[$id]["item_id"], INTEGER);
                $items_where .= " AND ic.category_id=" . $db->tosql($ids[$id]["category_id"], INTEGER);
                $items_where .= ")";
            }
        }

        $allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);

        $items_categories = array();
        if ($is_search || $is_manufacturer){
            $sql = " SELECT ic.item_id, ic.category_id, c.category_name ";
            $sql .= " FROM (" . $table_prefix . "items_categories ic ";
            $sql .= " LEFT JOIN " . $table_prefix . "categories c ON ic.category_id=c.category_id) ";
            $sql .= " WHERE ic.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
            $db->query($sql);
            while ($db->next_record()) {
                $item_id = $db->f("item_id");
                $ic_id = $db->f("category_id");
                $ic_name = get_translation($db->f("category_name"));
                if (!strlen($ic_name)){
                    $ic_name = PRODUCTS_TITLE;
                }
                $items_categories[$item_id][$ic_id] = $ic_name;
            }
        }

        $sql = " SELECT i.item_id, i.item_type_id, i.item_code, i.item_name, i.friendly_url, i.short_description, i.features, i.is_compared, ";
        $sql .= " i.tiny_image, i.tiny_image_alt, i.small_image, i.small_image_alt, i.big_image, i.big_image_alt, ";
        $sql .= " i.buying_price, i." . $price_field . ", i.is_price_edit, i." . $sales_field . ", i.discount_percent, ";
        $sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
        $sql .= " i.tax_free, i.weight, i.buy_link, i.total_views, i.votes, i.points, i.is_sales, ";
        $sql .= " i.manufacturer_code, i.manufacturer_id, m.manufacturer_name, m.affiliate_code, ";
        $sql .= " i.issue_date, i.stock_level, i.use_stock_level, i.disable_out_of_stock, i.min_quantity, i.max_quantity, quantity_increment, ";
        $sql .= " i.hide_out_of_stock, i.hide_add_list, ";
        $sql .= " st_in.shipping_time_desc AS in_stock_message, st_out.shipping_time_desc AS out_stock_message ";
        // new product db
        if ($new_product_enable){
            switch ($new_product_order) {
                case 0:
                    $sql .= ", i.issue_date AS new_product_date ";
                    break;
                case 1:
                    $sql .= ", i.date_added AS new_product_date ";
                    break;
                case 2:
                    $sql .= ", i.date_modified AS new_product_date ";
                    break;
            }
        }
        if ($products_group_by_cats){
            $sql .= " , ic.category_id, c.category_name, c.short_description AS category_short_description, c.full_description AS category_full_description ";
        }
        $sql .= " FROM (((";
        if ($products_group_by_cats){
            $sql .= "((";
        } else if ($category_order){
            $sql .= "(";
        }
        $sql .= $table_prefix . "items i ";
        if ($products_group_by_cats){
            $sql .= " INNER JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
            $sql .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id) ";
        } else if ($category_order){
            $sql .= " INNER JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
        }
        $sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
        $sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
        $sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
        if ($items_where){
            $sql .= " WHERE (" . $items_where . ") ";
        } else {
            $sql .= " WHERE i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
        }
        if (!$products_group_by_cats && $category_order){
            // if products should be shown from one category
            $sql .= " AND ic.category_id=" . $db->tosql($category_id, INTEGER);
        }
        $sql .= $s->order_by;

        $t->set_var("category_id", htmlspecialchars($category_id));
        $db->query($sql);
        if ($db->next_record()){
            $last_category_id = $db->f("category_id");
            $last_category_name = $db->f("category_name");
            $t->set_var("item_column", (100 / $columns) . "%");
            $t->set_var("total_columns", $columns);
            $t->set_var("forms", "");
            $item_number = 0;

            // item previews
            $previews = new VA_Previews();
            $previews->preview_type = array(1, 2);
            $previews->preview_position = 3;
            do {
                $item_number++;
                $item_id = $db->f("item_id");
                $item_category_id = $db->f("category_id");
                $item_category_name = get_translation($db->f("category_name"));
                $category_short_description = get_translation($db->f("category_short_description"));
                $category_full_description = get_translation($db->f("category_full_description"));
                if (strval($item_category_name) == ""){
                    $item_category_name = PRODUCTS_TITLE;
                }

                $item_type_id = $db->f("item_type_id");
                $item_code = $db->f("item_code");
                if ($products_group_by_cats){
                    $form_id = $item_category_id . "_" . $item_id;
                } else {
                    $form_id = $item_id;
                }
                $product_params["form_id"] = $form_id;
                $item_name = get_translation($db->f("item_name"));
                $product_params["item_name"] = strip_tags($item_name);
                $highlights = get_translation($db->f("features"));
                if ($php_in_features){
                    eval_php_code($highlights);
                }
                $friendly_url = $db->f("friendly_url");
                $is_compared = $db->f("is_compared");
                $manufacturer_code = $db->f("manufacturer_code");
                $manufacturer_name = $db->f("manufacturer_name");
                $issue_date_ts = 0;
                $issue_date = $db->f("issue_date", DATETIME);
                if (is_array($issue_date)){
                    $issue_date_ts = va_timestamp($issue_date);
                }

                $price = $db->f($price_field);
                $is_price_edit = $db->f("is_price_edit");
                $is_sales = $db->f("is_sales");
                $sales_price = $db->f($sales_field);
                $min_quantity = $db->f("min_quantity");
                $max_quantity = $db->f("max_quantity");
                $quantity_increment = $db->f("quantity_increment");

                // special prices
                $user_price = false;
                $user_price_action = 0;
                $initial_quantity = ($min_quantity) ? $min_quantity : 1;
                $q_prices = get_quantity_price($item_id, 1);
                if ($q_prices){
                    $user_price = $q_prices [0];
                    $user_price_action = $q_prices [2];
                }

                $buying_price = $db->f("buying_price");
                // points data
                $is_points_price = $db->f("is_points_price");
                $points_price = $db->f("points_price");
                $reward_type = $db->f("reward_type");
                $reward_amount = $db->f("reward_amount");
                $credit_reward_type = $db->f("credit_reward_type");
                $credit_reward_amount = $db->f("credit_reward_amount");
                if (!strlen($is_points_price)){
                    $is_points_price = $points_prices;
                }

                $weight = $db->f("weight");
                $total_views = $db->f("total_views");
                $tax_free = $db->f("tax_free");
                if ($user_tax_free){
                    $tax_free = $user_tax_free;
                }
                $stock_level = $db->f("stock_level");
                $use_stock_level = $db->f("use_stock_level");
                $disable_out_of_stock = $db->f("disable_out_of_stock");
                $hide_out_of_stock = $db->f("hide_out_of_stock");
                $hide_add_list = $db->f("hide_add_list");
                $quantity_limit = ($use_stock_level && ($disable_out_of_stock || $hide_out_of_stock));

                if ($new_product_enable){
                    $new_product_date = $db->f("new_product_date");
                    $is_new_product = is_new_product($new_product_date);
                } else {
                    $is_new_product = false;
                }
                if ($is_new_product){
                    $t->set_var("product_new_class", " newProduct");
                    $t->sparse("product_new_image", false);
                } else {
                    $t->set_var("product_new_class", "");
                    $t->set_var("product_new_image", "");
                }
                if (!$allowed_items_ids || !in_array($item_id, $allowed_items_ids)){
                    $t->set_var("restricted_class", " restrictedItem");
                    $t->sparse("restricted_image", false);
                    $hide_add_list = true;
                } else {
                    $t->set_var("restricted_class", "");
                    $t->set_var("restricted_image", "");
                }

                // calcalutate price
                if ($user_price > 0 && ($user_price_action > 0 || !$discount_type)){
                    if ($is_sales){
                        $sales_price = $user_price;
                    } else {
                        $price = $user_price;
                    }
                }

                if ($user_price_action != 1){
                    if ($discount_type == 1 || $discount_type == 3){
                        $price -= round(($price * $discount_amount) / 100, 2);
                        $sales_price -= round(($sales_price * $discount_amount) / 100, 2);
                    } elseif ($discount_type == 2) {
                        $price -= round($discount_amount, 2);
                        $sales_price -= round($discount_amount, 2);
                    } elseif ($discount_type == 4) {
                        $price -= round((($price - $buying_price) * $discount_amount) / 100, 2);
                        $sales_price -= round((($sales_price - $buying_price) * $discount_amount) / 100, 2);
                    }
                }
                $item_price = calculate_price($price, $is_sales, $sales_price);

                $properties = show_items_properties($form_id, $item_id, $item_type_id, $item_price, $tax_free, "list", $product_params, $price_matrix_list);
                $is_properties = $properties["is_any"];
                $properties_ids = $properties["ids"];
                $selected_price = $properties["price"];
                $components_price = $properties["components_price"];
                $components_tax_price = $properties["components_tax_price"];
                $components_points_price = $properties["components_points_price"];
                $components_reward_points = $properties["components_reward_points"];
                $components_reward_credits = $properties["components_reward_credits"];

                $t->set_var("item_id", $item_id);
                if ($friendly_urls && strlen($friendly_url)){
                    $t->set_var("product_details_url", $friendly_url . $friendly_extension . $details_query);
                } else {
                    $t->set_var("product_details_url", $product_link . $item_id);
                }
                $t->set_var("reviews_url", $reviews_link . $item_id);
                if (($is_search || $is_manufacturer) && isset($items_categories[$item_id]) && $items_categories[$item_id]){
                    $item_categories = $items_categories[$item_id];
                    $total_categories = sizeof($item_categories);
                    $t->set_var("found_categories", "");
                    $i = 0;
                    $ic_separator = ",";
                    foreach ($item_categories AS $ic_id => $ic_name){
                        if ($i == $total_categories - 1)
                            $ic_separator = "";
                        $t->set_var("ic_id", $ic_id);
                        $t->set_var("item_category", $ic_name);
                        $t->set_var("ic_separator", $ic_separator);
                        $t->sparse("found_categories", true);
                        $i++;
                    }
                    $t->global_parse("found_in_category", false, false, true);
                } else {
                    $t->set_var("found_in_category", "");
                }
                $t->set_var("form_id", $form_id);
                $t->set_var("item_name", $item_name);
                $t->set_var("highlights", $highlights);
                $t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
                $t->set_var("manufacturer_name", htmlspecialchars($manufacturer_name));
                $t->set_var("total_views", $total_views);

                $t->set_var("tax_price", "");
                $t->set_var("tax_sales", "");
                // show item code
                if ($show_item_code && $item_code){
                    $t->set_var("item_code", htmlspecialchars($item_code));
                    $t->sparse("item_code_block", false);
                } else {
                    $t->set_var("item_code_block", "");
                }
                // show manufacturer code
                if ($show_manufacturer_code && $manufacturer_code){
                    $t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
                    $t->sparse("manufacturer_code_block", false);
                } else {
                    $t->set_var("manufacturer_code_block", "");
                    $t->set_var("product_code", "");
                }

                $t->set_var("item_added", "");
                $t->set_var("sc_errors", "");
                if ($item_id == $sc_item_id){
                    if ($sc_errors){
                        $t->set_var("errors_list", $sc_errors);
                        $t->parse("sc_errors", false);
                    } elseif ($item_added) {
                        $cart = get_param("cart");
                        if ($cart == "WISHLIST"){
                            $added_message = str_replace("{product_name}", $item_name, "{product_name} was added to your Wishlist.");
                        } else {
                            $added_message = str_replace("{product_name}", $item_name, ADDED_PRODUCT_MSG);
                        }
                        $t->set_var("added_message", $added_message);
                        $t->parse("item_added", false);
                    }
                }

                if (!$use_stock_level || $stock_level > 0){
                    $shipping_time_desc = $db->f("in_stock_message");
                } else {
                    $shipping_time_desc = $db->f("out_stock_message");
                }
                if (strlen($shipping_time_desc)){
                    $t->set_var("shipping_time_desc", get_translation($shipping_time_desc));
                    $t->sparse("availability", false);
                } else {
                    $t->set_var("availability", "");
                }
                if ($stock_level_list && $use_stock_level){
                    $t->set_var("stock_level", $stock_level);
                    $t->sparse("stock_level_block", false);
                } else {
                    $t->set_var("stock_level_block", "");
                }

                $small_image = $db->f($image_field);
                $small_image_alt = get_translation($db->f($image_field_alt));
                $prod = new Product(['item_code' => $db->f('item_code'), 'manufacturer_id' => $db->f('manufacturer_id')]);
                if (!$small_image){
                    $small_image = $prod->default_img();
                }
                $image_exists = true;

                if (strlen($small_image)){
                    if (preg_match("/^http(s)?:\/\//", $small_image)){
                        $image_size = "";
                    } else {
                        $image_size = @getimagesize($small_image);
                        if ($image_exists && ($watermark || $restrict_products_images)){
                            $small_image = "image_show.php?item_id=" . $item_id . "&type=" . $image_type_name . "&vc=" . md5($small_image);
                        }
                    }
                    if (!strlen($small_image_alt)){
                        $small_image_alt = $item_name;
                    }
                    $t->set_var("alt", htmlspecialchars($small_image_alt));
                    $t->set_var("src", htmlspecialchars($small_image));
                    if (is_array($image_size)){
                        $t->set_var("width", "width=\"" . $image_size[0] . "\"");
                        $t->set_var("height", "height=\"" . $image_size[1] . "\"");
                    } else {
                        $t->set_var("width", "");
                        $t->set_var("height", "");
                    }
                    $t->parse("small_image", false);
                } else {
                    $t->set_var("small_image", "");
                }

                $short_description = get_translation($db->f("short_description"));
                if ($php_in_short_desc){
                    eval_php_code($short_description);
                }

                $t->set_var("short_description", $short_description);
                $t->sparse("description", false);

                if ($weight > 0){
                    if (strpos($weight, ".") !== false){
                        while (substr($weight, strlen($weight) - 1) == "0")
                            $weight = substr($weight, 0, strlen($weight) - 1);
                    }
                    if (substr($weight, strlen($weight) - 1) == ".")
                        $weight = substr($weight, 0, strlen($weight) - 1);
                    $t->set_var("weight", $weight . " " . $weight_measure);
                    $t->global_parse("weight_block", false, false, true);
                }

                if ($is_compared){
                    $t->global_parse("compare", false, false, true);
                    $t->parse("forms", true);
                } else {
                    $t->set_var("compare", "");
                }

                // show products previews
                $previews->item_id = $item_id;
                $previews->showAll("product_previews");

                // show points price
                if ($points_system && $points_price_list){
                    if ($points_price <= 0){
                        $points_price = $item_price * $points_conversion_rate;
                    }
                    $points_price += $components_points_price;
                    $selected_points_price = $selected_price * $points_conversion_rate;
                    $product_params["base_points_price"] = $points_price;
                    if ($is_points_price){
                        $t->set_var("points_rate", $points_conversion_rate);
                        $t->set_var("points_decimals", $points_decimals);
                        $t->set_var("points_price", number_format($points_price + $selected_points_price, $points_decimals));
                        $t->sparse("points_price_block", false);
                    } else {
                        $t->set_var("points_price_block", "");
                    }
                }

                // show reward points
                if ($points_system && $reward_points_list){
                    $reward_points = calculate_reward_points($reward_type, $reward_amount, $item_price, $buying_price, $points_conversion_rate, $points_decimals);
                    $reward_points += $components_reward_points;

                    $product_params["base_reward_points"] = $reward_points;
                    if ($reward_type){
                        $t->set_var("reward_points", number_format($reward_points, $points_decimals));
                        $t->sparse("reward_points_block", false);
                    } else {
                        $t->set_var("reward_points_block", "");
                    }
                }

                // show reward credits
                if ($credit_system && $reward_credits_list && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $user_id))){
                    $reward_credits = calculate_reward_credits($credit_reward_type, $credit_reward_amount, $item_price, $buying_price);
                    $reward_credits += $components_reward_credits;

                    $product_params["base_reward_credits"] = $reward_credits;
                    if ($credit_reward_type){
                        $t->set_var("reward_credits", currency_format($reward_credits));
                        $t->sparse("reward_credits_block", false);
                    } else {
                        $t->set_var("reward_credits_block", "");
                    }
                }

                $product_params["pe"] = 0;
                if ($display_products != 2 || strlen($user_id)){
                    set_quantity_control($quantity_limit, $stock_level, $quantity_control, $min_quantity, $max_quantity, $quantity_increment);

                    $base_price = calculate_price($price, $is_sales, $sales_price);
                    $product_params["base_price"] = $base_price;
                    if ($is_price_edit){
                        $product_params["pe"] = 1;
                        $t->set_var("price_block_class", "priceBlockEdit");
                        if ($price > 0){
                            $control_price = number_format($price, 2);
                        } else {
                            $control_price = "";
                        }

                        $t->set_var("price", $control_price);
                        $t->set_var("price_control", "<input name=\"price\" type=\"text\" class=\"price\" value=\"" . $control_price . "\">");
                        $t->sparse("price_block", false);
                        $t->set_var("sales", "");
                        $t->set_var("save", "");
                    } elseif ($sales_price != $price && $is_sales) {
                        $discount_percent = round($db->f("discount_percent"), 0);
                        if (!$discount_percent && $price > 0){
                            $discount_percent = round(($price - $sales_price) / ($price / 100), 0);
                        }

                        $t->set_var("discount_percent", $discount_percent);
                        set_tax_price($item_id, $item_type_id, $price, $sales_price + $selected_price, $tax_free, "price", "sales_price", "tax_sales", true, $components_price, $components_tax_price);

                        $t->sparse("price_block", false);
                        $t->sparse("sales", false);
                        $t->sparse("save", false);
                    } else {
                        $product_params["pe"] = 0;
                        set_tax_price($item_id, $item_type_id, $price + $selected_price, 0, $tax_free, "price", "", "tax_price", true, $components_price, $components_tax_price);

                        $t->sparse("price_block", false);
                        $t->set_var("sales", "");
                        $t->set_var("save", "");
                    }


                    $buy_link = $db->f("buy_link");
                    if ($buy_link){
                        $t->set_var("buy_href", $db->f("buy_link") . $db->f("affiliate_code"));
                    } elseif ($is_properties || $quantity_control == "LISTBOX" || $quantity_control == "TEXTBOX" || $is_price_edit) {
                        $t->set_var("buy_href", "javascript:document.form_" . $form_id . ".submit();");
                        $t->set_var("wishlist_href", "javascript:document.form_" . $form_id . ".submit();");
                    } else {
                        $t->set_var("buy_href", $cart_link . "cart=ADD&add_id=" . $item_id . "&rp=" . urlencode($rp) . "#p" . $item_id);
                        $t->set_var("wishlist_href", $cart_link . "cart=WISHLIST&add_id=" . $item_id . "&rp=" . urlencode($rp) . "#p" . $item_id);
                    }

                    if ($hide_add_list || $shop_hide_add_list){
                        $t->set_var("add_button_disabled", "");
                        $t->set_var("add_button", "");
                    } else {
                        if ($use_stock_level && $stock_level < 1 && $disable_out_of_stock){
                            $t->set_var("add_button", "");
                            $t->sparse("add_button_disabled", false);
                        } else {
                            $t->set_var("add_button_disabled", "");
                            if (($use_stock_level && $stock_level < 1) || $issue_date_ts > $current_ts){
                                $t->set_var("ADD_TO_CART_MSG", PRE_ORDER_MSG);
                            } else {
                                $t->set_var("ADD_TO_CART_MSG", ADD_TO_CART_MSG);
                            }
                            $t->sparse("add_button", false);
                        }
                    }
                    if ($shop_hide_view_list){
                        $t->set_var("view_button", "");
                    } else {
                        $t->sparse("view_button", false);
                    }
                    if ($shop_hide_checkout_list || !is_array($shopping_cart)){
                        $t->set_var("checkout_button", "");
                    } else {
                        $t->sparse("checkout_button", false);
                    }
                    if (!$user_id || $buy_link || $shop_hide_wishlist_list){
                        $t->set_var("wishlist_button", "");
                    } else {
                        $t->sparse("wishlist_button", false);
                    }
                }
                set_product_params($product_params);


                if ($reviews_allowed_view == 1 || ($reviews_allowed_view == 2 && strlen($user_id))
                    || $reviews_allowed_post == 1 || ($reviews_allowed_post == 2 && strlen($user_id))){
                    $votes = $db->f("votes");
                    $points = $db->f("points");

                    $rating_float = $votes ? round($points / $votes, 2) : 0;
                    $rating_int = round($rating_float, 0);
                    if ($rating_int){
                        $rating_alt = $rating_float;
                        $rating_image = "rating-" . $rating_int;
                    } else {
                        $rating_alt = RATE_IT_BUTTON;
                        $rating_image = "not-rated";
                    }

                    $t->set_var("rating_image", $rating_image);
                    $t->set_var("rating_alt", $rating_alt);
                    $t->sparse("reviews", false);
                }

                $is_next_record = $db->next_record();
                if ($item_number % $columns == 0 || (!$is_next_record && $item_number < $columns)){
                    $t->set_var("class_item_td", "");
                } else {
                    $t->set_var("class_item_td", "vDelimiter");
                }
                $t->parse("items_cols");

                if ($is_next_record){
                    $new_category_id = $db->f("category_id");
                } else {
                    $new_category_id = "";
                }
                if ($item_number % $columns == 0){
                    if ($is_next_record && $item_category_id == $new_category_id){
                        $t->parse("delimiter", false);
                    } else {
                        $t->set_var("delimiter", "");
                    }
                    $t->parse("items_rows");
                    $t->set_var("items_cols", "");
                }

                if ($is_next_record && $products_group_by_cats){
                    if ($item_category_id != $new_category_id){
                        if ($item_number % $columns != 0){
                            $t->parse("items_rows");
                        }
                        $t->set_var("category_name", $item_category_name);
                        $t->set_var("category_short_description", $category_short_description);
                        $t->set_var("category_full_description", $category_full_description);

                        $t->parse("items_category_name", false);
                        $t->parse("category_items", true);
                        $t->set_var("items_rows", "");
                        $t->set_var("items_cols", "");
                        $item_number = 0; // start from zero for new category
                    }
                }
            } while ($is_next_record);
            $t->set_var("delimiter", "");

            if ($item_number % $columns != 0){
                $t->parse("items_rows");
            }
            if ($products_group_by_cats){
                $t->set_var("category_name", $item_category_name);
                $t->parse("items_category_name", false);
            }
            $t->parse("category_items", true);

            if ($total_pages > 1){
                $t->parse("search_and_navigation", false);
            }

            $t->parse("block_body", false);
            $t->set_var("no_items", "");
        }
    } else {
        $t->set_var("forms", "");
        $t->set_var("items_rows", "");
        $t->parse("no_items", false);
        $t->parse("block_body", false);
    }

    // show search results information
    if ($is_search){
        $found_message = str_replace("{found_records}", $total_records, FOUND_PRODUCTS_MSG);
        $found_message = str_replace("{search_string}", htmlspecialchars($search_string), $found_message);
        $t->set_var("FOUND_PRODUCTS_MSG", $found_message);
        $t->parse("search_results", false);
        $t->parse("search_and_navigation", false);
        $t->parse("block_body", false);
    }

    if ($total_records > 0 || $is_search){
        $t->parse($block_name, true);
    }

}

?>