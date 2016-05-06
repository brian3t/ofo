<?php


class va_sorter
{

    var $t;
    var $order_by;
    var $order_columns;
    var $is_column;
    var $is_column_link;
    var $is_image;
    var $is_image_link;
    var $sorter_parameter;
    var $sorter_page;
    var $default_sorter_value;
    var $default_sorter_order;
    var $remove_parameters;
    var $pass_parameters;

    function va_sorter( $template_path, $filename, $sorter_page, $sorter_parameter = "sort", $remove_parameters = "", $pass_parameters = "" )
    {
        $this->t = new va_template( $template_path );
        $this->t->set_file( "sorter", $filename );
        $this->is_column = false;
        $this->is_column_link = true;
        $this->is_image = false;
        $this->is_image_link = true;
        $this->sorter_page = $sorter_page;
        $this->sorter_parameter = $sorter_parameter;
        $this->order_by = "";
        $this->order_columns = "";
        $this->default_sorter_value = "";
        $this->default_sorter_order = "";
        $this->remove_parameters = $remove_parameters;
        $this->pass_parameters = $pass_parameters;
    }

    function set_default_sorting( $default_sorter_value, $default_sorter_order )
    {
        $this->default_sorter_value = $default_sorter_value;
        $this->default_sorter_order = strtolower( $default_sorter_order );
    }

    function set_parameters( $is_column, $is_column_link, $is_image, $is_image_link )
    {
        $this->is_column = $is_column;
        $this->is_column_link = $is_column_link;
        $this->is_image = $is_image;
        $this->is_image_link = $is_image_link;
    }

    function set_sorter( $column_name, $sorter_name, $sorter_value, $table_column, $column_asc = "", $column_desc = "" )
    {
        global $t;
        global $HTTP_GET_VARS;
        if ( is_array( $this->pass_parameters ) )
        {
            $get_vars = $this->pass_parameters;
        }
        else
        {
            $get_vars = isset( $_GET ) ? $_GET : $HTTP_GET_VARS;
        }
        $remove_parameters = $this->remove_parameters;
        if ( !is_array( $remove_parameters ) )
        {
            $remove_parameters = array( );
        }
        $page = $this->sorter_page;
        $remove_parameters[] = $this->sorter_parameter."_ord";
        $remove_parameters[] = $this->sorter_parameter."_dir";
        $query_string = get_query_string( $get_vars, $remove_parameters, "", false );
        $query_string .= strlen( $query_string ) ? "&" : "?";
        $query_string .= $this->sorter_parameter."_ord=".urlencode( $sorter_value );
        $page_asc = $page.$query_string."&".$this->sorter_parameter."_dir=asc";
        $page_desc = $page.$query_string."&".$this->sorter_parameter."_dir=desc";
        $this->t->set_var( "column_name", $column_name );
        $this->t->set_var( "column_on", "" );
        $this->t->set_var( "column_off", "" );
        $this->t->set_var( "image_on_asc", "" );
        $this->t->set_var( "image_off_asc", "" );
        $this->t->set_var( "image_on_desc", "" );
        $this->t->set_var( "image_off_desc", "" );
        $sorted_value = get_param( $this->sorter_parameter."_ord" );
        if ( $sorted_value == $sorter_value || $this->default_sorter_value == $sorter_value && !strlen( $sorted_value ) )
        {
            $direction = strtolower( get_param( $this->sorter_parameter."_dir" ) );
            if ( !$direction )
            {
                $direction = $this->default_sorter_order;
            }
            if ( $this->is_column )
            {
                $this->t->parse( "column_off", false );
            }
            if ( $this->is_column_link )
            {
                if ( $direction == "asc" )
                {
                    $this->t->set_var( "sorting_href", $page_desc );
                }
                else
                {
                    $this->t->set_var( "sorting_href", $page_asc );
                }
                $this->t->parse( "column_on", false );
            }
            if ( $this->is_image_link )
            {
                if ( $direction == "asc" )
                {
                    $this->t->parse( "image_on_asc", false );
                    $this->t->parse( "image_off_desc", false );
                }
                else
                {
                    if ( $direction == "desc" )
                    {
                        $this->t->parse( "image_on_desc", false );
                        $this->t->parse( "image_off_asc", false );
                    }
                    else
                    {
                        $this->t->parse( "image_off_asc", false );
                        $this->t->parse( "image_off_desc", false );
                    }
                }
            }
            else if ( $this->is_image )
            {
                if ( $direction == "asc" )
                {
                    $this->t->parse( "image_on_asc", false );
                }
                else if ( $direction == "desc" )
                {
                    $this->t->parse( "image_on_desc", false );
                }
            }
            $this->order_by .= strlen( $this->order_by ) ? ", " : " ORDER BY ";
            if ( $this->order_columns )
            {
                $this->order_columns .= ", ";
            }
            $this->order_columns .= $table_column;
            if ( $direction == "desc" )
            {
                $this->order_by .= $column_desc ? $column_desc : $table_column." DESC";
            }
            else
            {
                $this->order_by .= $column_asc ? $column_asc : $table_column." ASC";
            }
        }
        else
        {
            $this->t->set_var( "sorting_href", $page_asc );
            if ( $this->is_column_link )
            {
                $this->t->parse( "column_on", false );
            }
            if ( $this->is_column )
            {
                $this->t->parse( "column_off", false );
            }
            if ( $this->is_image_link )
            {
                $this->t->parse( "image_off_asc", false );
                $this->t->set_var( "sorting_href", $page_desc );
                $this->t->parse( "image_off_desc", false );
            }
        }
        $this->t->parse( "sorter", false );
        $t->set_var( $sorter_name, $this->t->get_var( "sorter" ) );
    }

}

?>
