<?php


class va_url
{

    var $page_name = "";
    var $query_string = "";
    var $parameters = "";

    function va_url( $page_name, $save_query_string = false, $remove_parameters = "", $query_string = "" )
    {
        $this->page_name = $page_name;
        if ( $save_query_string )
        {
            global $HTTP_GET_VARS;
            $vars = isset( $_GET ) ? $_GET : $HTTP_GET_VARS;
            $this->query_string = get_query_string( $vars, $remove_parameters, $query_string );
        }
    }

    function add_parameter( $parameter_name, $parameter_type, $parameter_source )
    {
        $this->parameters[$parameter_name] = array( $parameter_name, $parameter_type, $parameter_source );
    }

    function remove_parameter( $parameter_name )
    {
        if ( isset( $this->parameters[$parameter_name] ) )
        {
            unset( $this->parameters[$parameter_name] );
        }
    }

    function get_url( $page_name = "" )
    {
        if ( $page_name )
        {
            $this->page_name = $page_name;
        }
        $query_string = $this->query_string;
        if ( is_array( $this->parameters ) )
        {
            foreach ( $this->parameters as $parameter_name => $parameter )
            {
                $parameter_type = $parameter[1];
                $parameter_source = $parameter[2];
                if ( $parameter_type == CONSTANT )
                {
                    $parameter_value = $parameter_source;
                }
                else if ( $parameter_type == GET || $parameter_type == POST || $parameter_type == REQUEST )
                {
                    $parameter_value = get_param( $parameter_source );
                }
                else if ( $parameter_type == SESSION )
                {
                    $parameter_value = get_session( $parameter_source );
                }
                else if ( $parameter_type == COOKIE )
                {
                    $parameter_value = get_cookie( $parameter_source );
                }
                else if ( $parameter_type == APPLICATION )
                {
                    $parameter_value = get_session( $parameter_source );
                }
                else if ( $parameter_type == DB )
                {
                    global $db;
                    $parameter_value = $db->f( $parameter_source );
                }
                if ( !strlen( $parameter_value ) )
                {
                    continue;
                }
                else if ( strlen( $query_string ) )
                {
                    $query_string .= "&".$parameter_name."=".urlencode( $parameter_value );
                }
                else
                {
                    $query_string .= "?".$parameter_name."=".urlencode( $parameter_value );
                }
            }
        }
        return $this->page_name.$query_string;
    }

}

?>
