<?php


function va_release_date( )
{
    $release_date = 1229420000;
    return $release_date;
}

function get_var( $var_name )
{
    global $HTTP_SERVER_VARS;
    global $_SERVER;
    global $_ENV;
    $var_value = getenv( $var_name );
    if ( !strlen( $var_value ) )
    {
        if ( isset( $_SERVER[$var_name] ) )
        {
            $var_value = $_SERVER[$var_name];
        }
        else if ( isset( $_ENV[$var_name] ) )
        {
            $var_value = $_ENV[$var_name];
        }
        else if ( isset( $HTTP_SERVER_VARS[$var_name] ) )
        {
            $var_value = $HTTP_SERVER_VARS[$var_name];
        }
    }
    return $var_value;
}

function get_translation( $message, $translation_code = "" )
{
    global $language_code;
    if ( $translation_code == "" )
    {
        $translation_code = $language_code;
    }
    $message = preg_replace( "/\\[".$translation_code."\\]/si", "", $message );
    $message = preg_replace( "/\\[\\/".$translation_code."\\]/si", "", $message );
    $message = preg_replace( "/\\[\\w\\w\\].*\\[\\/\\w\\w\\]/sU", "", $message );
    if ( preg_match( "/^\\w+$/", $message ) && defined( $message ) )
    {
        $message = constant( $message );
    }
    return $message;
}

function get_currency_message( $message, $currency )
{
    $message = preg_replace( "/\\[price\\]\\s*([\\d\\.]+)\\s*\\[\\/price\\]/ie", "currency_format((float)\\1, \$currency)", $message );
    $message = preg_replace( "/\\[price\\]\\s*(\\-[\\d\\.]+)\\s*\\[\\/price\\]/ie", "\"- \" . currency_format(abs((float)\\1), \$currency)", $message );
    $message = preg_replace( "/\\[".$currency['code']."\\]/si", "", $message );
    $message = preg_replace( "/\\[\\/".$currency['code']."\\]/si", "", $message );
    $message = preg_replace( "/\\[\\w\\w\\w\\].*\\[\\/\\w\\w\\w\\]/sU", "", $message );
    return $message;
}

function get_language( $message_file )
{
    global $default_language;
    global $is_admin_path;
    global $admin_language;
    if ( isset( $is_admin_path ) && $is_admin_path )
    {
        $root_folder_path = "../";
        if ( isset( $admin_language ) && $admin_language !== "" )
        {
            $default_language = $admin_language;
        }
        $cookie_lang_name = "cookie_admin_language";
        $sess_lang_name = "session_admin_language";
    }
    else
    {
        $is_admin_path = false;
        $root_folder_path = "./";
        $cookie_lang_name = "cookie_lang";
        $sess_lang_name = "session_language";
    }
    $sess_lang = get_session( $sess_lang_name );
    $param_lang = get_param( "language_code" );
    $cookie_lang = get_cookie( $cookie_lang_name );
    if ( strlen( $param_lang ) == 2 && file_exists( $root_folder_path."messages/".$param_lang."/".$message_file ) )
    {
        $lang_code = $param_lang;
        set_session( $sess_lang_name, $param_lang );
        setcookie( $cookie_lang_name, $param_lang, time( ) + 31622400 );
    }
    else if ( strlen( $cookie_lang ) == 2 && file_exists( $root_folder_path."messages/".$cookie_lang."/".$message_file ) )
    {
        $lang_code = $cookie_lang;
    }
    else if ( strlen( $sess_lang ) == 2 && file_exists( $root_folder_path."messages/".$sess_lang."/".$message_file ) )
    {
        $lang_code = $sess_lang;
    }
    else if ( strlen( $default_language ) == 2 && file_exists( $root_folder_path."messages/".$default_language."/".$message_file ) )
    {
        $lang_code = $default_language;
    }
    else
    {
        $lang_code = "en";
    }
    return $lang_code;
}

function check_admin_security( $block_name = "" )
{
    global $db;
    global $table_prefix;
    global $settings;
    global $argc;
    global $argv;
    $allow_access = true;
    if ( !strlen( get_session( "session_admin_id" ) ) )
    {
        $login = "";
        $password = "";
        if ( isset( $argc ) && 1 < $argc )
        {
            $i = 1;
            for ( ; $i < $argc; ++$i )
            {
                if ( ( $argv[$i] == "-l" || $argv[$i] == "-u" ) && isset( $argv[$i + 1] ) && substr( $argv[$i + 1], 0, 1 ) != "-" )
                {
                    ++$i;
                    $login = $argv[$i];
                }
                else if ( $argv[$i] == "-p" && isset( $argv[$i + 1] ) && substr( $argv[$i + 1], 0, 1 ) != "-" )
                {
                    ++$i;
                    $password = $argv[$i];
                }
            }
        }
        if ( strlen( $login ) && strlen( $password ) )
        {
            $password_encrypt = get_setting_value( $settings, "password_encrypt", 0 );
            $admin_password_encrypt = get_setting_value( $settings, "admin_password_encrypt", $password_encrypt );
            if ( $admin_password_encrypt == 1 )
            {
                $password_match = md5( $password );
            }
            else
            {
                $password_match = $password;
            }
            $sql = " SELECT * FROM ".$table_prefix."admins WHERE ";
            $sql .= " login=".$db->tosql( $login, TEXT );
            $sql .= " AND password=".$db->tosql( $password_match, TEXT );
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                set_session( "session_admin_id", $db->f( "admin_id" ) );
                set_session( "session_admin_privilege_id", $db->f( "privilege_id" ) );
            }
        }
    }
    if ( !strlen( get_session( "session_admin_id" ) ) || !strlen( get_session( "session_admin_privilege_id" ) ) )
    {
        $allow_access = false;
        $type_error = 1;
    }
    else if ( strlen( $block_name ) )
    {
        $sql = " SELECT permission FROM ".$table_prefix."admin_privileges_settings ";
        $sql .= " WHERE privilege_id=".$db->tosql( get_session( "session_admin_privilege_id" ), INTEGER );
        $sql .= " AND block_name=".$db->tosql( $block_name, TEXT );
        $allow_access = get_db_value( $sql ) ? true : false;
        if ( !$allow_access )
        {
            $type_error = 2;
        }
    }
    if ( !$allow_access )
    {
        $request_uri = get_request_uri( );
        if ( $type_error == 1 && 0 < sizeof( $_POST ) )
        {
            global $root_folder_path;
            global $admin_site_url;
            global $admin_secure_url;
            global $language_code;
            $_GET['return_page'] = $request_uri;
            $_GET['type_error'] = 1;
            include( "admin_login.php" );
            exit( );
        }
        else
        {
            header( "Location: admin_login.php?return_page=".urlencode( $request_uri )."&type_error=".$type_error );
            exit( );
        }
    }
    return $allow_access;
}

function va_settings( $auto_user_login = true )
{
    global $db;
    global $db_type;
    global $settings;
    global $table_prefix;
    global $tracking_ignore;
    global $is_admin_path;
    global $site_id;
    global $root_site_id;
    global $multisites_version;
    if ( $is_admin_path && function_exists( "comp_vers" ) )
    {
        if ( comp_vers( va_version( ), "3.3.3" ) == 1 )
        {
            $multisites_version = true;
        }
        else
        {
            $multisites_version = false;
        }
        if ( isset( $site_id ) )
        {
            $root_site_id = $site_id;
        }
        else
        {
            $root_site_id = 1;
        }
        $param_site_id = get_param( "param_site_id" );
        if ( !$param_site_id )
        {
            $param_site_id = get_session( "session_site_id" );
        }
        if ( !$param_site_id )
        {
            $param_site_id = $root_site_id;
        }
        set_session( "session_site_id", $param_site_id );
    }
    else
    {
        $multisites_version = true;
    }
    $settings = DEBUG ? "" : get_session( "session_settings" );
    $update_layout = false;
    if ( !is_array( $settings ) )
    {
        $update_layout = true;
        $sql = "SELECT setting_name,setting_value FROM ".$table_prefix."global_settings ";
        $sql .= "WHERE (setting_type='global' OR setting_type='products') ";
        if ( $multisites_version )
        {
            if ( isset( $site_id ) && 1 < $site_id )
            {
                $sql .= "AND ( site_id=1 OR site_id = ".$db->tosql( $site_id, INTEGER )." ) ";
                $sql .= "ORDER BY site_id ASC ";
            }
            else
            {
                $sql .= "AND site_id=1 ";
            }
        }
        $db->query( $sql );
        while ( $db->next_record( ) )
        {
            $settings[$db->f( "setting_name" )] = $db->f( "setting_value" );
        }
        if ( $multisites_version )
        {
            $sql = " SELECT * FROM ".$table_prefix."sites ";
            if ( isset( $site_id ) )
            {
                $sql .= " WHERE site_id=".$db->tosql( $site_id, INTEGER );
            }
            else
            {
                $sql .= " WHERE site_id=1 ";
            }
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                $settings['site_name'] = $db->f( "site_name" );
            }
        }
    }
    $user_id = get_session( "session_user_id" );
    if ( $auto_user_login && !$user_id )
    {
        auto_user_login( );
    }
    if ( $user_id )
    {
        $last_visit_page = get_request_uri( );
        if ( 255 < strlen( $last_visit_page ) )
        {
            $last_visit_page = substr( $last_visit_page, 0, 255 );
        }
        $sql = " UPDATE ".$table_prefix."users SET ";
        $sql .= " last_visit_date=".$db->tosql( va_time( ), DATETIME );
        $sql .= ", last_visit_ip=".$db->tosql( get_ip( ), TEXT );
        $sql .= ", last_visit_page=".$db->tosql( $last_visit_page, TEXT );
        $sql .= " WHERE user_id=".$db->tosql( $user_id, INTEGER );
        $db->query( $sql );
    }
    $param_layout_id = get_param( "set_layout_id" );
    if ( $update_layout || $param_layout_id )
    {
        $layout_id = "";
        $layout_data = "";
        $user_id = get_session( "session_user_id" );
        if ( $param_layout_id )
        {
            if ( $multisites_version )
            {
                $sql = " SELECT * FROM ".$table_prefix."layouts AS lt ";
                if ( isset( $site_id ) )
                {
                    $sql .= " LEFT JOIN ".$table_prefix."layouts_sites AS ls ON ls.layout_id=lt.layout_id";
                    $sql .= " WHERE (lt.sites_all=1 OR ls.site_id=".$db->tosql( $site_id, INTEGER, true, false ).") ";
                }
                else
                {
                    $sql .= " WHERE lt.sites_all=1 ";
                }
                $sql .= " AND lt.layout_id=".$db->tosql( $param_layout_id, INTEGER );
                $sql .= " AND lt.show_for_user=1 ";
            }
            else
            {
                $sql = " SELECT * FROM ".$table_prefix."layouts ";
                $sql .= " WHERE layout_id=".$db->tosql( $param_layout_id, INTEGER );
                $sql .= " AND show_for_user=1 ";
            }
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                $layout_id = $param_layout_id;
                $layout_data = $db->Record;
                set_session( "session_layout_id", $layout_id );
                if ( $user_id )
                {
                    $sql = " UPDATE ".$table_prefix."users SET layout_id=".$db->tosql( $layout_id, INTEGER );
                    $sql .= " WHERE user_id=".$db->tosql( $user_id, INTEGER );
                    $db->query( $sql );
                }
            }
        }
        $session_layout_id = get_session( "session_layout_id" );
        if ( !$layout_id && $session_layout_id )
        {
            if ( $multisites_version )
            {
                $sql = " SELECT * FROM ".$table_prefix."layouts AS lt ";
                if ( isset( $site_id ) )
                {
                    $sql .= " LEFT JOIN ".$table_prefix."layouts_sites AS ls ON ls.layout_id=lt.layout_id";
                    $sql .= " WHERE (lt.sites_all=1 OR ls.site_id=".$db->tosql( $site_id, INTEGER, true, false ).") ";
                }
                else
                {
                    $sql .= " WHERE lt.sites_all=1 ";
                }
                $sql .= " AND lt.layout_id=".$db->tosql( $session_layout_id, INTEGER );
                $sql .= " AND lt.show_for_user=1 ";
            }
            else
            {
                $sql = " SELECT * FROM ".$table_prefix."layouts ";
                $sql .= " WHERE layout_id=".$db->tosql( $session_layout_id, INTEGER );
                $sql .= " AND show_for_user=1 ";
            }
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                $layout_id = $session_layout_id;
                $layout_data = $db->Record;
            }
        }
        if ( !$layout_id && $user_id )
        {
            $user_info = get_session( "session_user_info" );
            $user_layout_id = get_setting_value( $user_info, "layout_id", "" );
            if ( $user_layout_id )
            {
                if ( $multisites_version )
                {
                    $sql = " SELECT * FROM ".$table_prefix."layouts AS lt ";
                    if ( isset( $site_id ) )
                    {
                        $sql .= " LEFT JOIN ".$table_prefix."layouts_sites AS ls ON ls.layout_id=lt.layout_id";
                        $sql .= " WHERE (lt.sites_all=1 OR ls.site_id=".$db->tosql( $site_id, INTEGER, true, false ).") ";
                    }
                    else
                    {
                        $sql .= " WHERE lt.sites_all=1 ";
                    }
                    $sql .= " AND lt.layout_id=".$db->tosql( $user_layout_id, INTEGER );
                    $sql .= " AND lt.show_for_user=1 ";
                }
                else
                {
                    $sql = " SELECT * FROM ".$table_prefix."layouts ";
                    $sql .= " WHERE layout_id=".$db->tosql( $user_layout_id, INTEGER );
                    $sql .= " AND show_for_user=1 ";
                }
                $db->query( $sql );
                if ( $db->next_record( ) )
                {
                    $layout_id = $user_layout_id;
                    $layout_data = $db->Record;
                }
                else
                {
                    $sql = " UPDATE ".$table_prefix."users SET layout_id=NULL ";
                    $sql .= " WHERE user_id=".$db->tosql( $user_id, INTEGER );
                    $db->query( $sql );
                }
            }
        }
        if ( !$layout_id )
        {
            $default_layout_id = get_setting_value( $settings, "layout_id", "" );
            if ( $default_layout_id )
            {
                $sql = " SELECT * FROM ".$table_prefix."layouts ".( $sql = " WHERE layout_id=".$db->tosql( $default_layout_id, INTEGER ) );
                $db->query( $sql );
                if ( $db->next_record( ) )
                {
                    $layout_id = $default_layout_id;
                    $layout_data = $db->Record;
                }
            }
        }
        if ( !$layout_id )
        {
            $layout_data['templates_dir'] = "./templates/user";
            $layout_data['admin_templates_dir'] = "../templates/admin";
            $layout_data['top_menu_type'] = 1;
            $layout_data['style_name'] = "default";
            $layout_data['scheme_name'] = "";
        }
        foreach ( $layout_data as $setting_name => $setting_value )
        {
            $settings[$setting_name] = $setting_value;
        }
        $settings['layout_id'] = $layout_id;
        set_session( "session_settings", $settings );
    }
    if ( isset( $tracking_ignore ) && $tracking_ignore )
    {
        $is_tracking = false;
    }
    else
    {
        $is_tracking = true;
    }
    $va_check = get_param( "va_check" );
    if ( $va_check )
    {
        va_license_check( true );
    }
    $af = get_param( "af" );
    $kw = get_param( "kw" );
    $friend_code = get_param( "friend" );
    $session_start = get_session( "session_start" );
    if ( $session_start && ( strlen( $af ) || strlen( $friend_code ) ) )
    {
        $af_session = get_session( "session_af" );
        $kw_session = get_session( "session_kw" );
        $friend_session = get_session( "session_friend" );
        if ( $af != $af_session )
        {
            $session_start = false;
        }
        if ( $friend_code != $friend_session )
        {
            $session_start = false;
            set_session( "session_friend_id", "" );
        }
    }
    if ( $is_tracking && !$session_start )
    {
        $user_ip = get_ip( );
        $user_agent = get_var( "HTTP_USER_AGENT" );
        $referer = get_var( "HTTP_REFERER" );
        $referer_host = "";
        if ( $referer )
        {
            $parsed_url = parse_url( $referer );
            $referer_host = $parsed_url['host'];
        }
        set_session( "session_start", 1 );
        set_session( "session_referer", $referer );
        set_session( "session_initial_ip", $user_ip );
        $cookie_visit = get_cookie( "cookie_visit" );
        $cookie_ip = "";
        $visit_number = 0;
        $parent_visit_id = 0;
        $cookie_visit = va_decrypt( $cookie_visit, "cookie" );
        $visit_info = explode( "|", $cookie_visit );
        $cookie_ip = "";
        $visit_number = 0;
        $parent_visit_id = 0;
        if ( !$cookie_ip )
        {
            $cookie_ip = $user_ip;
        }
        ++$visit_number;
        $new_cookie_visit = va_encrypt( $cookie_ip."|".$visit_number, "cookie" );
        setcookie( "cookie_visit", $new_cookie_visit, va_timestamp( ) + 31622400 );
        set_session( "session_cookie_ip", $cookie_ip );
        set_session( "session_visit_number", $visit_number );
        if ( !strlen( $af ) )
        {
            $af = get_cookie( "cookie_af" );
        }
        else
        {
            $affiliate_expire = get_setting_value( $settings, "affiliate_cookie_expire", 60 );
            if ( !$affiliate_expire )
            {
                $affiliate_expire = 60;
            }
            setcookie( "cookie_af", $af, va_timestamp( ) + 86400 * $affiliate_expire );
        }
        set_session( "session_af", $af );
        set_session( "session_kw", $kw );
        if ( !strlen( $friend_code ) )
        {
            $friend_code = get_cookie( "cookie_friend" );
        }
        else
        {
            $friend_expire = get_setting_value( $settings, "friend_cookie_expire", 120 );
            if ( !$friend_expire )
            {
                $friend_expire = 120;
            }
            setcookie( "cookie_friend", $friend_code, va_timestamp( ) + 86400 * $friend_expire );
        }
        set_session( "session_friend", $friend_code );
        $visit_id = 0;
        if ( isset( $settings['tracking_visits'] ) && $settings['tracking_visits'] == 1 )
        {
            $referer_engine_id = 0;
            $robot_engine_id = 0;
            $keywords_parameter = "";
            if ( $user_agent || $referer )
            {
                $sql = " SELECT * FROM ".$table_prefix."search_engines ";
                $db->query( $sql );
                do
                {
                    if ( !( $db->next_record( ) && !$referer_engine_id && !$robot_engine_id ) )
                    {
                        break;
                    }
                    else
                    {
                        $engine_id = $db->f( "engine_id" );
                        $engine_parameter = $db->f( "keywords_parameter" );
                        $referer_regexp = $db->f( "referer_regexp" );
                        $user_agent_regexp = $db->f( "user_agent_regexp" );
                        $ip_regexp = $db->f( "ip_regexp" );
                    }
                    if ( $referer && $referer_regexp && preg_match( $referer_regexp, $referer ) )
                    {
                        $referer_engine_id = $engine_id;
                        $keywords_parameter = $engine_parameter;
                    }
                    if ( $user_agent && $user_agent_regexp && preg_match( $user_agent_regexp, $user_agent ) )
                    {
                        $robot_engine_id = $engine_id;
                    }
                    if ( $user_ip && $ip_regexp && preg_match( $ip_regexp, $user_ip ) )
                    {
                        $robot_engine_id = $engine_id;
                    }
                } while ( 1 );
            }
            if ( $keywords_parameter && preg_match( "/[\\?\\&]".$keywords_parameter."=([^&]+)/i", $referer, $matches ) )
            {
                $kw = urldecode( $matches[1] );
                if ( CHARSET != "utf-8" && is_utf8( $kw ) )
                {
                    $kw = charset_decode_utf8( $kw );
                }
                set_session( "session_kw", $kw );
            }
            $request_uri = get_request_uri( );
            $request_page = get_request_page( );
            $date_added = va_time( );
            $week_added = get_yearweek( $date_added );
            if ( $db_type == "postgre" )
            {
                $sql = " SELECT NEXTVAL('seq_".$table_prefix."tracking_visits') ";
                $db->query( $sql );
                $db->next_record( $sql );
                $visit_id = $db->f( 0 );
            }
            $sql = " INSERT INTO ".$table_prefix."tracking_visits (";
            if ( $db_type == "postgre" )
            {
                $sql .= "visit_id, ";
            }
            $sql .= " parent_visit_id, visit_number, ";
            $sql .= " ip_long, ip_text, forwarded_ips, ";
            $sql .= " affiliate_code, keywords, user_agent, request_uri, request_page, ";
            $sql .= " referer, referer_host, referer_engine_id, robot_engine_id, ";
            $sql .= " date_added, year_added, month_added, week_added, day_added, hour_added, ";
            $sql .= " site_id) VALUES (";
            if ( $db_type == "postgre" )
            {
                $sql .= $db->tosql( $visit_id, INTEGER ).", ";
            }
            $sql .= $db->tosql( $parent_visit_id, INTEGER, true, false ).", ".$db->tosql( $visit_number, INTEGER ).", ";
            $sql .= $db->tosql( ip2long( $user_ip ), INTEGER, true, false ).", ".$db->tosql( $user_ip, TEXT, true, false ).", ";
            $sql .= $db->tosql( get_var( "HTTP_X_FORWARDED_FOR" ), TEXT ).", ";
            $sql .= $db->tosql( $af, TEXT, true, false ).", ".$db->tosql( $kw, TEXT, true, false ).", ".$db->tosql( $user_agent, TEXT, true, false ).", ";
            $sql .= $db->tosql( $request_uri, TEXT, true, false ).", ".$db->tosql( $request_page, TEXT, true, false ).", ";
            $sql .= $db->tosql( $referer, TEXT, true, false ).", ".$db->tosql( $referer_host, TEXT, true, false ).", ";
            $sql .= $db->tosql( $referer_engine_id, INTEGER, true, false ).", ".$db->tosql( $robot_engine_id, INTEGER, true, false ).", ";
            $sql .= $db->tosql( $date_added, DATETIME, true, false ).", ".$db->tosql( $date_added[YEAR], INTEGER, true, false ).", ";
            $sql .= $db->tosql( $date_added[MONTH], INTEGER, true, false ).", ";
            $sql .= $db->tosql( $week_added, INTEGER, true, false ).", ";
            $sql .= $db->tosql( $date_added[DAY], INTEGER, true, false ).", ";
            $sql .= $db->tosql( $date_added[HOUR], INTEGER, true, false ).", ";
            if ( isset( $site_id ) )
            {
                $sql .= $db->tosql( $site_id, INTEGER, true, false ).") ";
            }
            else
            {
                $sql .= $db->tosql( 1, INTEGER, true, false ).") ";
            }
            $db->query( $sql );
            if ( $db_type == "mysql" )
            {
                $sql = " SELECT LAST_INSERT_ID() ";
                $db->query( $sql );
                $db->next_record( $sql );
                $visit_id = $db->f( 0 );
            }
            else if ( $db_type == "access" )
            {
                $sql = " SELECT @@IDENTITY ";
                $db->query( $sql );
                $db->next_record( $sql );
                $visit_id = $db->f( 0 );
            }
            else if ( $db_type == "db2" )
            {
                $sql = " SELECT PREVVAL FOR seq_".$table_prefix."tracking_visits FROM ".$table_prefix."tracking_visits";
                $db->query( $sql );
                $db->next_record( $sql );
                $visit_id = $db->f( 0 );
            }
        }
        if ( !$parent_visit_id )
        {
            $parent_visit_id = $visit_id;
        }
        $new_cookie_visit = va_encrypt( $cookie_ip."|".$visit_number."|".$parent_visit_id, "cookie" );
        setcookie( "cookie_visit", $new_cookie_visit, va_timestamp( ) + 31622400 );
        set_session( "session_visit_id", $visit_id );
    }
    if ( $is_tracking && isset( $settings['tracking_pages'] ) && $settings['tracking_pages'] == 1 )
    {
        $visit_id = get_session( "session_visit_id" );
        $user_ip = get_ip( );
        $request_uri = get_request_uri( );
        if ( 255 < strlen( $request_uri ) )
        {
            $request_uri = substr( $request_uri, 0, 255 );
        }
        $request_page = get_request_page( );
        $date_added = va_time( );
        $sql = " INSERT INTO ".$table_prefix."tracking_pages (";
        $sql .= " visit_id,  ";
        $sql .= " ip_long, ip_text, forwarded_ips, ";
        $sql .= " request_uri, request_page, ";
        $sql .= " date_added, year_added, month_added, day_added, hour_added, ";
        $sql .= " site_id) VALUES (";
        $sql .= $db->tosql( $visit_id, INTEGER, true, false ).", ";
        $sql .= $db->tosql( ip2long( $user_ip ), INTEGER, true, false ).", ".$db->tosql( $user_ip, TEXT, true, false ).", ";
        $sql .= $db->tosql( get_var( "HTTP_X_FORWARDED_FOR" ), TEXT ).", ";
        $sql .= $db->tosql( $request_uri, TEXT, true, false ).", ".$db->tosql( $request_page, TEXT, true, false ).", ";
        $sql .= $db->tosql( $date_added, DATETIME, true, false ).", ".$db->tosql( $date_added[YEAR], INTEGER, true, false ).", ";
        $sql .= $db->tosql( $date_added[MONTH], INTEGER, true, false ).", ".$db->tosql( $date_added[DAY], INTEGER, true, false ).", ";
        $sql .= $db->tosql( $date_added[HOUR], INTEGER, true, false ).", ";
        if ( isset( $site_id ) )
        {
            $sql .= $db->tosql( $site_id, INTEGER, true, false ).") ";
        }
        else
        {
            $sql .= $db->tosql( 1, INTEGER, true, false ).") ";
        }
        $db->query( $sql );
    }
    return $settings;
}

function va_page_settings( $page_name, $layout_id )
{
    global $db;
    global $table_prefix;
    global $settings;
    global $site_id;
    if ( !strlen( $page_name ) )
    {
        return "";
    }
    $page_settings = DEBUG ? "" : get_session( "session_".$page_name."_settings" );
    if ( !is_array( $page_settings ) )
    {
        if ( !$layout_id )
        {
            $layout_id = 0;
        }
        $sql = " SELECT setting_name,setting_value FROM ".$table_prefix."page_settings ";
        $sql .= " WHERE page_name=".$db->tosql( $page_name, TEXT );
        $sql .= " AND layout_id=".$db->tosql( $layout_id, INTEGER );
        if ( isset( $site_id ) && 1 < $site_id )
        {
            $sql2 = " AND site_id=".$db->tosql( $site_id, INTEGER );
            $sql2 .= " ORDER BY setting_order ";
        }
        else
        {
            $sql2 = " AND site_id=1 ";
            $sql2 .= " ORDER BY setting_order ";
        }
        $db->query( $sql.$sql2 );
        if ( $db->next_record( ) )
        {
            $page_settings[$db->f( "setting_name" )] = $db->f( "setting_value" );
            while ( $db->next_record( ) )
            {
                $page_settings[$db->f( "setting_name" )] = $db->f( "setting_value" );
            }
        }
        else if ( isset( $site_id ) && 1 < $site_id )
        {
            $sql2 = " AND site_id=1 ";
            $sql2 .= " ORDER BY setting_order ";
            $db->query( $sql.$sql2 );
            while ( $db->next_record( ) )
            {
                $page_settings[$db->f( "setting_name" )] = $db->f( "setting_value" );
            }
        }
        set_session( "session_".$page_name."_settings", $page_settings );
    }
    return $page_settings;
}

function va_encrypt( $str, $user_key = "" )
{
    global $va_encrypt_key;
    $result = "";
    $str127 = "";
    $our_key = "Ai5MkLgF9eBE3JNasR10wZbC2fuT4qWrPz7UYhxoSvO8dpDIHlycG6jXVntQmK_";
    if ( strlen( $user_key ) )
    {
        $key = $our_key.$user_key;
    }
    else if ( isset( $va_encrypt_key ) )
    {
        $key = $our_key.$va_encrypt_key;
    }
    else
    {
        $key = $our_key;
    }
    $i = 0;
    for ( ; $i < strlen( $str ); ++$i )
    {
        if ( 127 < ord( $str[$i] ) )
        {
            $str127 .= chr( 128 ).chr( ord( $str[$i] ) - 128 );
        }
        else
        {
            $str127 .= $str[$i];
        }
    }
    $i = 0;
    for ( ; $i < strlen( $str127 ); ++$i )
    {
        $char = substr( $str127, $i, 1 );
        $key_pos = ( $i + ord( $key[$i % strlen( $key )] ) + ord( $key[strlen( $str127 ) % strlen( $key )] ) ) % strlen( $key );
        $keychar = $key[$key_pos];
        $keycode = ord( $keychar );
        if ( 127 < $keycode )
        {
            $keycode = intval( $keycode / 2 );
        }
        $char = chr( ord( $char ) + $keycode );
        $result .= $char;
    }
    return base64_encode( $result );
}

function va_decrypt( $str, $user_key = "" )
{
    global $va_encrypt_key;
    if ( preg_match( "/^[\\d\\*]+$/", $str ) )
    {
        return $str;
    }
    $result = "";
    $str127 = "";
    $str = base64_decode( $str );
    $our_key = "Ai5MkLgF9eBE3JNasR10wZbC2fuT4qWrPz7UYhxoSvO8dpDIHlycG6jXVntQmK_";
    if ( strlen( $user_key ) )
    {
        $key = $our_key.$user_key;
    }
    else if ( isset( $va_encrypt_key ) )
    {
        $key = $our_key.$va_encrypt_key;
    }
    else
    {
        $key = $our_key;
    }
    $i = 0;
    for ( ; $i < strlen( $str ); ++$i )
    {
        $char = substr( $str, $i, 1 );
        $key_pos = ( $i + ord( $key[$i % strlen( $key )] ) + ord( $key[strlen( $str ) % strlen( $key )] ) ) % strlen( $key );
        $keychar = $key[$key_pos];
        $keycode = ord( $keychar );
        if ( 127 < $keycode )
        {
            $keycode = intval( $keycode / 2 );
        }
        $char = chr( ord( $char ) - $keycode );
        $str127 .= $char;
    }
    $i = 0;
    for ( ; $i < strlen( $str127 ); ++$i )
    {
        if ( ord( $str127[$i] ) == 128 )
        {
            $result .= chr( 128 + ord( $str127[$i + 1] ) );
            ++$i;
        }
        else
        {
            $result .= $str127[$i];
        }
    }
    return $result;
}

function va_license_code( )
{
    global $va_code;
    global $va_name;
    global $va_type;
    global $version_name;
    global $version_type;
    if ( isset( $va_name ) )
    {
        $version_name = $va_name;
    }
    else if ( defined( "VA_PRODUCT" ) )
    {
        $version_name = VA_PRODUCT;
    }
    if ( isset( $va_type ) )
    {
        $version_type = $va_type;
    }
    else if ( defined( "VA_TYPE" ) )
    {
        $version_type = VA_TYPE;
    }
    if ( !isset( $va_code ) )
    {
        $va_code = 0;
        if ( $version_name == "shop" )
        {
            if ( $version_type == "enterprise" )
            {
                $va_code = 63;
            }
            else
            {
                if ( $version_type == "standard" )
                {
                    $va_code = 19;
                }
                else
                {
                    $va_code = 17;
                }
            }
        }
        else if ( $version_name == "cms" )
        {
            $va_code = 26;
        }
        else if ( $version_name == "helpdesk" )
        {
            $va_code = 46;
        }
    }
    return $va_code;
}

function va_license_check( $debug = false )
{
    global $va_domains;
    global $va_server_ips;
    global $va_code;
    global $va_name;
    global $va_type;
    global $va_key;
    global $va_purchased;
    global $va_upgrade_period;
    $va_code = va_license_code( );
    if ( !isset( $va_upgrade_period ) || !$va_upgrade_period )
    {
        if ( $va_code == 1 )
        {
            $va_upgrade_period = 15984000;
        }
        else
        {
            $va_upgrade_period = 31968000;
        }
    }
    $host_code = 0;
    $ip_code = 0;
    $hosts = "";
    $ips = "";
    $license_expired = 0;
    $check_key = "";
    $expiration_date = 0;
    if ( is_array( $va_domains ) )
    {
        $hosts = join( "", $va_domains );
        $host_name = get_var( "HTTP_HOST" );
        $host_name = preg_replace( "/^www\\./i", "", $host_name );
        $host_name = preg_replace( "/:\\d+$/i", "", $host_name );
        if ( in_array( $host_name, $va_domains ) )
        {
            $host_code = 1;
        }
        else
        {
            $host_code = 0;
        }
    }
    else
    {
        $host_code = 3;
    }
    if ( is_array( $va_server_ips ) )
    {
        $ips = join( "", $va_server_ips );
        $server_ip = get_var( "SERVER_ADDR" );
        if ( in_array( $server_ip, $va_server_ips ) )
        {
            $ip_code = 1;
        }
        else
        {
            $ip_code = 0;
        }
    }
    else
    {
        $ip_code = 3;
    }
    $host_valid = false;
    if ( ( $host_code & $ip_code ) == 1 )
    {
        $old_check_key = md5( $hosts.$ips.$va_name.$va_type.$va_purchased );
        $new_check_key = md5( $hosts.$ips.md5( $va_code ).$va_name.$va_type.md5( $va_purchased ).$va_upgrade_period );
        if ( $va_key == $old_check_key || $va_key == $new_check_key )
        {
            $host_valid = true;
        }
    }
    $release_date = va_release_date( );
    if ( $host_valid )
    {
        $expiration_date = $va_purchased + $va_upgrade_period;
        if ( $expiration_date < $release_date )
        {
            $license_expired = $va_purchased + $va_upgrade_period;
        }
    }
    if ( $debug )
    {
        echo "Owned By: <b><a href=\"http://www.viart.com/\">ViArt Ltd</a></b><br>";
        echo "Release:<b> ".VA_RELEASE."</b><br>";
        echo "Release Date:<b> ".date( "j M Y", va_release_date( ) )."</b><br>";
        if ( $va_name && $va_type && $va_purchased )
        {
            echo "Product Name:<b> ".$va_name."</b><br>";
            echo "Version Type:<b> ".$va_type."</b><br>";
            echo "Version Code:<b> ".$va_code."</b><br>";
            if ( is_array( $va_domains ) )
            {
                echo "Current Domain: <b>".$host_name."</b><br>";
                echo "Licensed Domains:<b> ".join( ", ", $va_domains )."</b><br>";
            }
            if ( is_array( $va_server_ips ) )
            {
                echo "Server Address: <b>".$server_ip."</b><br>";
                echo "Licensed IPs:<b> ".join( ", ", $va_server_ips )."</b><br>";
            }
            if ( $va_purchased )
            {
                echo "Purchased:<b> ".date( "j M Y", $va_purchased )."</b><br>";
                echo "Upgrade Period:<b> ".round( $va_upgrade_period / 86400 )." days</b><br>";
            }
            if ( $expiration_date )
            {
                echo "Expiration Date:<b>".date( "j M Y", $expiration_date )."</b>.<br> ";
            }
            echo "License Key:<b> ".$va_key."</b><br>";
            echo "Check Key #1:<b> ".$old_check_key."</b><br>";
            echo "Check Key #2:<b> ".$new_check_key."</b><br>";
        }
    }
	$host_valid = true;
    return array( $host_valid, $license_expired, $va_code );
}

function va_license_message( )
{
    global $custom_friendly_urls;
    $va_pages = array( "products.php" => 1, "product_details.php" => 1, "basket.php" => 1, "products_search.php" => 1, "articles.php" => 2, "article.php" => 2, "article_print.php" => 2, "support.php" => 4, "user_support.php" => 4, "forum.php" => 8, "forums.php" => 8, "forum_topic.php" => 8, "forum_topic_new.php" => 8, "order_info.php" => -1, "credit_card_info.php" => -1, "order_confirmation.php" => -1, "payment.php" => -1, "order_final.php" => -1, "cc_security_code_help.php" => -1, "install.php" => -1, "select_date_format.php" => -1, "ads.php" => 16, "ads_details.php" => 16, "ads_print.php" => 16, "manuals.php" => 32, "manuals_article_details.php" => 32, "manuals_articles.php" => 32, "manuals_search.php" => 32 );
    list( $host_valid, $license_expired, $va_code ) = va_license_check( );
    $script_name = get_script_name( );
    $page_type = 0;
    if ( isset( $va_pages[$script_name] ) )
    {
        $page_type = $va_pages[$script_name];
    }
    else if ( is_array( $custom_friendly_urls ) )
    {
        foreach ( $va_pages as $page_name => $page_code )
        {
            if ( isset( $custom_friendly_urls[$page_name] ) && $custom_friendly_urls[$page_name] == $script_name )
            {
                $page_type = $page_code;
            }
            else
            {
                break;
            }
        }
    }
    $ad = "";
    $license_valid = false;
    if ( $page_type == -1 )
    {
        $license_valid = true;
        $license_expired = 0;
    }
    else if ( $host_valid && ( !$page_type || $va_code & $page_type ) )
    {
        $license_valid = true;
    }
    if ( !$license_valid || $license_expired )
    {
        $host_name = get_var( "HTTP_HOST" );
        $host_length = strlen( $host_name );
        if ( $host_length % 4 == 3 )
        {
            $msg_sufix = "Free PHP ";
        }
        else if ( $host_length % 4 == 2 )
        {
            $msg_sufix = "Free ";
        }
        else if ( $host_length % 4 == 1 )
        {
            $msg_sufix = "PHP ";
        }
        else
        {
            $msg_sufix = "";
        }
        if ( $page_type == 1 )
        {
            $free_msg = $msg_sufix."Shopping Cart";
        }
        else if ( $page_type == 2 )
        {
            $free_msg = $msg_sufix."CMS";
        }
        else if ( $page_type == 4 )
        {
            $free_msg = $msg_sufix."HelpDesk";
        }
        else if ( $page_type == 8 )
        {
            $free_msg = $msg_sufix."Forum";
        }
        else if ( $page_type == 16 )
        {
            $free_msg = $msg_sufix."Classified Ads";
        }
        else if ( $page_type == 32 )
        {
            $free_msg = $msg_sufix."Manual Software";
        }
        else if ( $va_code & 1 )
        {
            $free_msg = $msg_sufix."Shopping Cart";
        }
        else if ( $va_code == 10 )
        {
            $free_msg = $msg_sufix."CMS";
        }
        else if ( $va_code == 46 )
        {
            $free_msg = $msg_sufix."HelpDesk";
        }
        else
        {
            $free_msg = $msg_sufix."Ecommerce Solutions";
        }
        $ad = "<center><div style='visibility:hidden'>--></div><div style='color:silver;visibility:visible;'>";
        $ad .= "<a style='color:silver;text-decoration:none;' href='http://www.viart.com/'>".$free_msg."</a>";
        $ad .= " by ViArt Ltd";
        if ( $license_valid && $license_expired )
        {
            $ad .= "<br>Your license has been expired (".date( "j M Y", $license_expired - 1 ).").";
            $ad .= "<br>To renew your license please visit ";
            $ad .= "<a style='color:silver;' href='http://www.viart.com/'>http://www.viart.com/<img border=0 src='images/fwd-arrow_small_white.gif'></a>";
        }
        $ad .= "</div></center>&nbsp;";
    }
    return $ad;
}

function va_mail( $mail_to, $mail_subject, $mail_body, $mail_headers = "", $attachments = "" )
{
    global $settings;
    if ( !strlen( $mail_to ) )
    {
        return false;
    }
    $mail_type = get_setting_value( $mail_headers, "mail_type", 0 );
    $mail_from = get_setting_value( $mail_headers, "from", $settings['admin_email'] );
    $email_additional_headers = get_setting_value( $settings, "email_additional_headers", "" );
    $email_additional_parameters = get_setting_value( $settings, "email_additional_parameters", "" );
    $eol = get_eol( );
    $add_mail_headers = preg_split( "/[\r\n]+/", $email_additional_headers, -1, PREG_SPLIT_NO_EMPTY );
    foreach ( $add_mail_headers as $header )
    {
        $header = explode( ":", $header );
        if ( sizeof( $header ) == 2 )
        {
            $mail_headers = array_merge( array( trim( $header[0] ) => trim( $header[1] ) ), $mail_headers );
        }
    }
    if ( is_array( $attachments ) && 0 < sizeof( $attachments ) )
    {
        $boundary = "--va_".md5( va_timestamp( ) )."_".va_timestamp( );
        $mail_headers['Content-Type'] = "multipart/mixed; boundary=\"".$boundary."\"";
        if ( isset( $mail_headers['mail_type'] ) )
        {
            unset( $mail_headers['mail_type'] );
        }
        $original_body = $mail_body;
        $mail_body = "This is a multi-part message in MIME format.".$eol.$eol;
        $mail_body .= "--".$boundary.$eol;
        if ( $mail_type )
        {
            $mail_body .= "Content-Type: text/html;".$eol;
        }
        else
        {
            $mail_body .= "Content-Type: text/plain;".$eol;
        }
        $mail_body .= "\tcharset=\"".CHARSET."\"".$eol;
        $mail_body .= "Content-Transfer-Encoding: 7bit".$eol;
        $mail_body .= $eol;
        $mail_body .= $original_body;
        $mail_body .= $eol.$eol;
        $at = 0;
        for ( ; $at < sizeof( $attachments ); ++$at )
        {
            $attachment_info = $attachments[$at];
            if ( !is_array( $attachment_info ) )
            {
                $filepath = $attachment_info;
                $attachment_info = array( basename( $filepath ), $filepath, "" );
            }
            else if ( sizeof( $attachment_info ) == 1 )
            {
                $filepath = $attachment_info[0];
                $attachment_info = array( basename( $filepath ), $filepath, "" );
            }
            $filename = $attachment_info[0];
            if ( !$filename )
            {
                $filename = basename( $filepath );
            }
            if ( !$filename )
            {
                $filename = "noname.txt";
            }
            $filepath = $attachment_info[1];
            $filetype = isset( $attachment_info[2] ) ? $attachment_info[2] : "";
            if ( preg_match( "/^(http|https|ftp|ftps):\\/\\//", $filepath ) )
            {
                $is_remote_file = true;
            }
            else
            {
                $is_remote_file = false;
            }
            $filebody = "";
            if ( $filetype == "pdf" )
            {
                $filebody = $pdf->get_buffer( );
            }
            else if ( $filetype == "buffer" )
            {
                $filebody = $filepath;
            }
            else if ( $filetype == "fp" )
            {
                while ( !feof( $fp ) )
                {
                    $filebody .= fread( $fp, 8192 );
                }
            }
            else if ( @( $is_remote_file || file_exists( @$filepath ) && !is_dir( @$filepath ) ) )
            {
                $fp = fopen( $filepath, "rb" );
                while ( !feof( $fp ) )
                {
                    $filebody .= fread( $fp, 8192 );
                }
                fclose( $fp );
            }
            if ( $filebody )
            {
                $file_base64 = chunk_split( base64_encode( $filebody ) );
                $mail_body .= "--".$boundary.$eol;
                if ( preg_match( "/\\.gif$/", $filename ) )
                {
                    $mail_body .= "Content-Type: image/gif;".$eol;
                }
                else if ( preg_match( "/\\.pdf$/", $filename ) )
                {
                    $mail_body .= "Content-Type: application/pdf;".$eol;
                }
                else
                {
                    $mail_body .= "Content-Type: application/octet-stream;".$eol;
                }
                $mail_body .= "\tname=\"".$filename."\"".$eol;
                $mail_body .= "Content-Transfer-Encoding: base64".$eol;
                $mail_body .= "Content-Disposition: attachment;".$eol;
                $mail_body .= "\tfilename=\"".$filename."\"".$eol;
                $mail_body .= $eol;
                $mail_body .= $file_base64;
                $mail_body .= $eol.$eol;
            }
        }
        $mail_body .= "--".$boundary."--".$eol;
        $mail_body .= $eol;
    }
    else
    {
        $mail_headers['mail_type'] = $mail_type;
    }
    $smtp_mail = get_setting_value( $settings, "smtp_mail", 0 );
    if ( $smtp_mail )
    {
        $smtp_host = get_setting_value( $settings, "smtp_host", "127.0.0.1" );
        $smtp_port = get_setting_value( $settings, "smtp_port", 25 );
        $smtp_timeout = get_setting_value( $settings, "smtp_timeout", 30 );
        $smtp_username = get_setting_value( $settings, "smtp_username", "" );
        $smtp_password = get_setting_value( $settings, "smtp_password", "" );
        $errors = "";
        $socket = @fsockopen( @$smtp_host, @$smtp_port, @$errno, @$error, @$smtp_timeout );
        if ( !$socket )
        {
            $errors = $error;
            return false;
        }
        $response = smtp_check_response( $socket, 220, $error );
        if ( $error )
        {
            $errors = $error;
            return false;
        }
        $smtp_username = get_setting_value( $settings, "smtp_username", "" );
        $smtp_password = get_setting_value( $settings, "smtp_password", "" );
        if ( strlen( $smtp_username ) && strlen( $smtp_password ) )
        {
            fputs( $socket, "EHLO ".$smtp_host."\r\n" );
            smtp_check_response( $socket, "250", $error );
            $errors .= $error;
            fputs( $socket, "AUTH LOGIN\r\n" );
            smtp_check_response( $socket, "334", $error );
            $errors .= $error;
            fputs( $socket, base64_encode( $smtp_username )."\r\n" );
            smtp_check_response( $socket, "334", $error );
            $errors .= $error;
            fputs( $socket, base64_encode( $smtp_password )."\r\n" );
            smtp_check_response( $socket, "235", $error );
            $errors .= $error;
        }
        else
        {
            fputs( $socket, "HELO ".$smtp_host."\r\n" );
            smtp_check_response( $socket, "250", $error );
            $errors .= $error;
        }
        if ( $errors )
        {
            return false;
        }
        fputs( $socket, "MAIL FROM: <".$mail_from.">\r\n" );
        smtp_check_response( $socket, "250", $error );
        if ( $error )
        {
            $errors = $error;
            return false;
        }
        if ( !isset( $mail_headers['to'] ) )
        {
            $mail_headers['to'] = $mail_to;
        }
        $header_names = array( "to", "cc", "bcc" );
        $hf = 0;
        for ( ; $hf < sizeof( $header_names ); ++$hf )
        {
            $recipients_string = get_setting_value( $mail_headers, $header_names[$hf], "" );
            $recipients_string = str_replace( ";", ",", $recipients_string );
            if ( $recipients_string )
            {
                $recipients_values = explode( ",", $recipients_string );
                $i = 0;
                for ( ; $i < sizeof( $recipients_values ); ++$i )
                {
                    $recipient_email = "";
                    $recipient_value = $recipients_values[$i];
                    if ( preg_match( "/<([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)>/i", $recipient_value, $match ) )
                    {
                        $recipient_email = $match[1];
                    }
                    else if ( preg_match( "/\\s*([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)\\s*/i", $recipient_value, $match ) )
                    {
                        $recipient_email = trim( $match[1] );
                    }
                    if ( $recipient_email )
                    {
                        fputs( $socket, "RCPT TO: <".$recipient_email.">\r\n" );
                        smtp_check_response( $socket, "250", $error );
                        $errors .= $error;
                    }
                }
            }
        }
        if ( $errors )
        {
            return false;
        }
        fputs( $socket, "DATA\r\n" );
        smtp_check_response( $socket, "354", $error );
        if ( $error )
        {
            $errors = $error;
            return false;
        }
        fputs( $socket, "Subject: ".$mail_subject."\r\n" );
        $headers_string = email_headers_string( $mail_headers, "\r\n" );
        fputs( $socket, $headers_string."\r\n\r\n" );
        fputs( $socket, $mail_body."\r\n.\r\n" );
        smtp_check_response( $socket, "250", $error );
        if ( $error )
        {
            $errors = $error;
            return false;
        }
        fputs( $socket, "QUIT\r\n" );
        fclose( $socket );
        return true;
    }
    else
    {
        $headers_string = email_headers_string( $mail_headers );
        $safe_mode = strtolower( ini_get( "safe_mode" ) ) == "on" || intval( ini_get( "safe_mode" ) ) == 1 ? true : false;
        if ( $safe_mode )
        {
            return mail( @$mail_to, @$mail_subject, @$mail_body, @$headers_string );
        }
        else
        {
            return mail( @$mail_to, @$mail_subject, @$mail_body, @$headers_string, @$email_additional_parameters );
        }
    }
}

function smtp_check_response( $socket, $check_code, &$error )
{
    $response = "";
    $response_code = "";
    do
    {
        $line = fgets( $socket, 512 );
        if ( preg_match( "/^(\\d{3})\\s/", $line, $matches ) )
        {
            $response_code = $matches[1];
        }
        $response .= $line;
    } while ( $line !== false && !$response_code );
    if ( $check_code == $response_code )
    {
        return $response;
    }
    else
    {
        if ( $response )
        {
            $error = "Error while sending email. Server response: ".$response."\n";
        }
        else
        {
            $error = "No response from mail server.\n";
        }
        return false;
    }
}

function va_version( )
{
    global $table_prefix;
    $sql = "SELECT setting_value FROM ".$table_prefix."global_settings WHERE setting_type='version' AND setting_name='number'";
    $current_db_version = get_db_value( $sql );
    if ( strlen( $current_db_version ) )
    {
        $current_version = $current_db_version;
    }
    else if ( defined( "VA_RELEASE" ) )
    {
        $current_version = VA_RELEASE;
    }
    else
    {
        $current_version = "2.1";
    }
    return $current_version;
}

?>
