<?php

class va_navigator
{

    var $t;
    var $order_by;
    var $is_first_last;
    var $is_prev_next;
    var $inactive_links;
    var $navigator_page;
    var $records_left;

    function va_navigator( $template_path, $filename, $navigator_page )
    {
        $this->t = new va_template( $template_path );
        $this->t->set_file( "navigator", $filename );
        $this->t->set_var( "NEXT_PAGE_MSG", NEXT_PAGE_MSG );
        $this->t->set_var( "PREV_PAGE_MSG", PREV_PAGE_MSG );
        $this->t->set_var( "FIRST_PAGE_MSG", FIRST_PAGE_MSG );
        $this->t->set_var( "LAST_PAGE_MSG", LAST_PAGE_MSG );
        $this->t->set_var( "OF_PAGE_MSG", OF_PAGE_MSG );
        $this->is_first_last = false;
        $this->is_prev_next = true;
        $this->inactive_links = false;
        $this->navigator_page = $navigator_page;
    }

    function set_parameters( $is_first_last, $is_prev_next, $inactive_links )
    {
        $this->is_first_last = $is_first_last;
        $this->is_prev_next = $is_prev_next;
        $this->inactive_links = $inactive_links;
    }

    function set_navigator( $navigator_name, $navigator_parameter, $navigator_type, $pages_number, $records_per_page, $total_records, $empty_navigator, $pass_parameters = "", $remove_parameters = array( ), $suffix = "" )
    {
        global $t;
        global $db;
        global $HTTP_GET_VARS;
        $get_vars = isset( $_GET ) ? $_GET : $HTTP_GET_VARS;
        $page = $this->navigator_page;
        $page_number = intval( get_param( $navigator_parameter ) );
        if ( $page_number < 1 )
        {
            $page_number = 1;
        }
        if ( is_array( $pass_parameters ) )
        {
            $remove_parameters[] = $navigator_parameter;
            $query_string = get_query_string( $pass_parameters, $remove_parameters, "", false );
        }
        else
        {
            $remove_parameters[] = $navigator_parameter;
            $query_string = get_query_string( $get_vars, $remove_parameters, "", false );
        }
        $query_string .= strlen( $query_string ) ? "&" : "?";
        $page .= $query_string.$navigator_parameter."=";
        $total_pages = ceil( $total_records / $records_per_page );
        if ( $total_pages < $page_number )
        {
            $page_number = $total_pages;
        }
        $this->t->set_var( "current_page", $page_number );
        $this->t->set_var( "total_pages", $total_pages );
        $this->t->set_var( "total_records", $total_records );
        $this->t->set_var( "first_on", "" );
        $this->t->set_var( "prev_on", "" );
        $this->t->set_var( "first_off", "" );
        $this->t->set_var( "prev_off", "" );
        $this->t->set_var( "last_on", "" );
        $this->t->set_var( "next_on", "" );
        $this->t->set_var( "last_off", "" );
        $this->t->set_var( "next_off", "" );
        if ( 1 < $page_number )
        {
            if ( $this->is_first_last )
            {
                $this->t->set_var( "navigating_href", $page."1".$suffix );
                $this->t->set_var( "page", 1 );
                $this->t->parse( "first_on", false );
            }
            if ( $this->is_prev_next )
            {
                $this->t->set_var( "navigating_href", $page.( $page_number - 1 ).$suffix );
                $this->t->set_var( "page", $page_number - 1 );
                $this->t->parse( "prev_on", false );
            }
        }
        else
        {
            if ( $this->inactive_links && $this->is_first_last )
            {
                $this->t->parse( "first_off", false );
            }
            if ( $this->inactive_links && $this->is_prev_next )
            {
                $this->t->parse( "prev_off", false );
            }
        }
        if ( $navigator_type == CENTERED )
        {
            $start_page = $page_number - intval( ( $pages_number - 1 ) / 2 );
            if ( $start_page < 1 )
            {
                $start_page = 1;
            }
            $end_page = $start_page + $pages_number - 1;
            if ( $total_pages < $end_page )
            {
                $start_page = $start_page - $end_page + $total_pages;
                if ( $start_page < 1 )
                {
                    $start_page = 1;
                }
                $end_page = $total_pages;
            }
            $this->parse_pages( $navigator_type, $start_page, $page_number, $end_page, $page, $records_per_page, $total_records );
        }
        else if ( $navigator_type == MOVING )
        {
            $pages_group = ceil( $page_number / $pages_number );
            $start_page = 1 + $pages_number * ( $pages_group - 1 );
            $end_page = $pages_number * $pages_group;
            if ( $start_page < 1 )
            {
                $start_page = 1;
            }
            if ( $total_pages < $end_page )
            {
                $end_page = $total_pages;
            }
            $this->parse_pages( $navigator_type, $start_page, $page_number, $end_page, $page, $records_per_page, $total_records );
        }
        else if ( $navigator_type == ALL_PAGES )
        {
            $this->parse_pages( $navigator_type, 1, $page_number, $total_pages, $page, $records_per_page, $total_records );
        }
        else
        {
            $this->parse_pages( $navigator_type, $page_number, $page_number, $page_number, $page, $records_per_page, $total_records );
        }
        if ( $page_number < $total_pages )
        {
            if ( $this->is_first_last )
            {
                $this->t->set_var( "navigating_href", $page.$total_pages.$suffix );
                $this->t->set_var( "page", $total_pages );
                $this->t->parse( "last_on", false );
            }
            if ( $this->is_prev_next )
            {
                $this->t->set_var( "navigating_href", $page.( $page_number + 1 ).$suffix );
                $this->t->set_var( "page", $page_number + 1 );
                $this->t->parse( "next_on", false );
            }
        }
        else
        {
            if ( $this->inactive_links && $this->is_first_last )
            {
                $this->t->parse( "last_off", false );
            }
            if ( $this->inactive_links && $this->is_prev_next )
            {
                $this->t->parse( "next_off", false );
            }
        }
        if ( $total_records <= $records_per_page && $empty_navigator == false )
        {
            $t->set_var( $navigator_name, "" );
            $t->set_var( $navigator_name."_block", "" );
        }
        else
        {
            $this->t->parse( "navigator", false );
            $t->set_var( $navigator_name, $this->t->get_var( "navigator" ) );
            $t->sparse( $navigator_name."_block", false );
        }
        return $page_number;
    }

    function parse_pages( $navigator_type, $start_page, $current_page_number, $end_page, $page, $records_per_page, $total_records )
    {
        $this->t->set_var( "page_before", "" );
        $this->t->set_var( "page_after", "" );
        if ( $navigator_type == MOVING && 1 < $start_page )
        {
            $first_record = ( $start_page - 2 ) * $records_per_page + 1;
            $last_record = ( $start_page - 1 ) * $records_per_page;
            $this->t->set_var( "navigating_href", $page.( $start_page - 1 ) );
            $this->t->set_var( "page", $start_page - 1 );
            $this->t->set_var( "page_number", "&lt; ".( $start_page - 1 ) );
            $this->t->set_var( "first_record", "&lt; ".$first_record );
            $this->t->set_var( "last_record", $last_record );
            $this->t->parse( "page_before", true );
        }
        $i = $start_page;
        for ( ; $i <= $end_page; ++$i )
        {
            $first_record = ( $i - 1 ) * $records_per_page + 1;
            $last_record = $i * $records_per_page;
            if ( $total_records < $last_record )
            {
                $last_record = $total_records;
            }
            $this->t->set_var( "navigating_href", $page.$i );
            $this->t->set_var( "page", $i );
            $this->t->set_var( "page_number", $i );
            $this->t->set_var( "first_record", $first_record );
            $this->t->set_var( "last_record", $last_record );
            if ( $i < $current_page_number )
            {
                $this->t->parse( "page_before", true );
            }
            else if ( $current_page_number < $i )
            {
                $this->t->parse( "page_after", true );
            }
            else
            {
                $this->t->set_var( "current_first_record", $first_record );
                $this->t->set_var( "current_last_record", $last_record );
            }
        }
        $total_pages = ceil( $total_records / $records_per_page );
        if ( $navigator_type == MOVING && $end_page < $total_pages )
        {
            $first_record = $end_page * $records_per_page + 1;
            $last_record = ( $end_page + 1 ) * $records_per_page;
            if ( $total_records < $last_record )
            {
                $last_record = $total_records;
            }
            $this->t->set_var( "navigating_href", $page.( $end_page + 1 ) );
            $this->t->set_var( "page_number", ( $end_page + 1 )." &gt;" );
            $this->t->set_var( "page", $end_page + 1 );
            $this->t->set_var( "first_record", $first_record );
            $this->t->set_var( "last_record", $last_record." &gt;" );
            $this->t->parse( "page_after", true );
        }
    }

}

define( "SIMPLE", 1 );
define( "CENTERED", 2 );
define( "MOVING", 3 );
define( "ALL_PAGES", 4 );
?>
