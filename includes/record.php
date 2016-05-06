<?php

class va_record
{

    var $table_name;
    var $errors_block;
    var $errors;
    var $success_block;
    var $success_message;
    var $buttons_block;
    var $record_name;
    var $return_page;
    var $record_show = true;
    var $redirect = true;
    var $where_set = false;
    var $data_valid = true;
    var $required_symbol = "*";
    var $default_class = "usual";
    var $errors_class = "error";
    var $operation;
    var $operations = array( );
    var $operation_name;
    var $errors_messages = array( );
    var $success_messages = array( );
    var $parameters = array( );
    var $matched_parameters = array( );
    var $events = array( );
    var $events_parameters = array( );

    function va_record( $table_name, $record_name = "" )
    {
        $this->table_name = $table_name;
        $this->set_record_name( $record_name );
        $this->errors = "";
        $this->set_default_messages( );
        $this->operations = array( INSERT_ALLOWED => true, UPDATE_ALLOWED => true, DELETE_ALLOWED => true, SELECT_ALLOWED => true );
    }

    function set_record_name( $record_name = "" )
    {
        if ( strlen( $record_name ) )
        {
            $this->record_name = $record_name;
            $this->errors_block = $record_name."_errors";
            $this->success_block = $record_name."_success";
            $this->buttons_block = $record_name."_buttons";
            $this->sections_block = $record_name."_sections";
            $this->operation_name = $record_name."_operation";
        }
        else
        {
            $this->record_name = "record";
            $this->errors_block = "errors";
            $this->buttons_block = "buttons";
            $this->success_block = "success";
            $this->sections_block = "sections";
            $this->operation_name = "operation";
        }
    }

    function set_default_messages( )
    {
        $this->errors_messages = array( INSERT_ALLOWED => INSERT_ALLOWED_ERROR, UPDATE_ALLOWED => UPDATE_ALLOWED_ERROR, DELETE_ALLOWED => DELETE_ALLOWED_ERROR );
        $this->success_messages = array( INSERT_SUCCESS => RECORD_ADDED_MSG, UPDATE_SUCCESS => RECORD_UPDATED_MSG, DELETE_SUCCESS => RECORD_DELETED_MSG );
    }

    function set_event( $event_name, $event_function, $event_parameters = "" )
    {
        $this->events[$event_name] = $event_function;
        if ( is_array( $event_parameters ) )
        {
            $this->events[$event_name."_params"] = $event_parameters;
        }
    }

    function set_control_event( $parameter_name, $event_name, $event_function, $event_parameters = "" )
    {
        if ( isset( $this->parameters[$parameter_name] ) )
        {
            $this->parameters[$parameter_name][$event_name] = $event_function;
            if ( is_array( $event_parameters ) )
            {
                $this->parameters[$parameter_name][$event_name."_params"] = $event_parameters;
            }
        }
    }

    function process( )
    {
        global $t;
        call_event( $this->events, BEFORE_PROCESS );
        $this->sort_parameters( );
        $this->operation = get_param( $this->operation_name );
        if ( strlen( $this->operation ) )
        {
            call_event( $this->events, BEFORE_REQUEST );
            $this->get_form_parameters( );
            call_event( $this->events, AFTER_REQUEST );
            $rnd = get_param( "rnd" );
            $session_rnd = get_session( "session_rnd" );
            if ( $rnd && $rnd == $session_rnd )
            {
                call_event( $this->events, ON_DOUBLE_SAVE );
            }
            if ( $this->operation == "cancel" )
            {
                call_event( $this->events, ON_CANCEL_OPERATION );
                if ( $this->redirect )
                {
                    header( "Location: ".$this->get_return_url( ) );
                    exit( );
                }
            }
            else
            {
                if ( $this->operation == "delete" )
                {
                    call_event( $this->events, BEFORE_DELETE );
                    if ( $this->where_set )
                    {
                        if ( $this->operations[DELETE_ALLOWED] )
                        {
                            $record_deleted = $this->delete_record( );
                            call_event( $this->events, AFTER_DELETE );
                            if ( $record_deleted )
                            {
                                $this->success_message = $this->success_messages[DELETE_SUCCESS];
                                $this->update_related( DELETE_SQL );
                                if ( $this->redirect )
                                {
                                    header( "Location: ".$this->get_return_url( ) );
                                    exit( );
                                }
                            }
                        }
                        else
                        {
                            $this->errors = $this->errors_messages[DELETE_ALLOWED]."<br>";
                        }
                    }
                }
                else
                {
                    if ( $this->operation == "save" )
                    {
                        call_event( $this->events, BEFORE_VALIDATE );
                        $this->data_valid = $this->validate( );
                        call_event( $this->events, AFTER_VALIDATE );
                        if ( $this->data_valid )
                        {
                            $record_updated = false;
                            if ( $this->where_set )
                            {
                                call_event( $this->events, BEFORE_UPDATE );
                                if ( $this->operations[UPDATE_ALLOWED] )
                                {
                                    $record_updated = $this->update_record( );
                                    call_event( $this->events, AFTER_UPDATE );
                                    if ( $record_updated )
                                    {
                                        set_session( "session_rnd", $rnd );
                                        $this->success_message = $this->success_messages[UPDATE_SUCCESS];
                                        $this->update_related( UPDATE_SQL );
                                    }
                                }
                                else
                                {
                                    $this->errors = $this->errors_messages[UPDATE_ALLOWED]."<br>";
                                }
                            }
                            else
                            {
                                call_event( $this->events, BEFORE_INSERT );
                                if ( $this->operations[INSERT_ALLOWED] )
                                {
                                    $record_updated = $this->insert_record( );
                                    call_event( $this->events, AFTER_INSERT );
                                    if ( $record_updated )
                                    {
                                        set_session( "session_rnd", $rnd );
                                        $this->success_message = $this->success_messages[INSERT_SUCCESS];
                                        $this->update_related( INSERT_SQL );
                                    }
                                }
                                else
                                {
                                    $this->errors = $this->errors_messages[INSERT_ALLOWED]."<br>";
                                }
                            }
                            if ( $record_updated && $this->redirect )
                            {
                                header( "Location: ".$this->get_return_url( ) );
                                exit( );
                            }
                        }
                        else
                        {
                            set_session( "session_rnd", "" );
                        }
                    }
                    else
                    {
                        call_event( $this->events, ON_CUSTOM_OPERATION, array( "operation" => $this->operation ) );
                        if ( $this->redirect )
                        {
                            header( "Location: ".$this->get_return_url( ) );
                            exit( );
                        }
                    }
                }
            }
        }
        else if ( $this->get_where_parameters( ) )
        {
            call_event( $this->events, BEFORE_SELECT );
            if ( $this->operations[SELECT_ALLOWED] )
            {
                $this->get_db_values( );
                call_event( $this->events, AFTER_SELECT );
            }
        }
        else
        {
            call_event( $this->events, BEFORE_DEFAULT );
            $this->set_default_values( );
            call_event( $this->events, AFTER_DEFAULT );
        }
        call_event( $this->events, BEFORE_SHOW );
        $this->set_form_parameters( );
        $t->set_var( "delete", "" );
        $t->set_var( "add_button", "" );
        $t->set_var( "update_button", "" );
        $t->set_var( "delete_button", "" );
        if ( $this->where_set )
        {
            $t->set_var( "save_button", UPDATE_BUTTON );
            if ( $t->block_exists( "update_button" ) && $this->operations[UPDATE_ALLOWED] )
            {
                $t->parse( "update_button", false );
            }
            if ( $t->block_exists( "delete" ) && $this->operations[DELETE_ALLOWED] )
            {
                $t->parse( "delete", false );
            }
        }
        else
        {
            $t->set_var( "save_button", ADD_BUTTON );
            if ( $t->block_exists( "add_button" ) && $this->operations[INSERT_ALLOWED] )
            {
                $t->parse( "add_button", false );
            }
        }
        if ( $t->block_exists( $this->buttons_block ) )
        {
            $t->parse( $this->buttons_block, false );
        }
        $t->set_var( "rnd", va_timestamp( ) );
        if ( $t->block_exists( $this->record_name ) )
        {
            if ( $this->record_show )
            {
                $t->parse( $this->record_name, false );
            }
            else
            {
                $t->set_var( $this->record_name, "" );
            }
        }
        call_event( $this->events, AFTER_SHOW );
        call_event( $this->events, AFTER_PROCESS );
    }

    function get_return_url( )
    {
        $query_string = "";
        foreach ( $this->parameters as $key => $parameter )
        {
            if ( isset( $parameter[TRANSFER] ) && $parameter[TRANSFER] )
            {
                $control_value = $parameter[CONTROL_VALUE];
                if ( strlen( $control_value ) )
                {
                    $query_string .= $query_string ? "&" : "?";
                    $query_string .= $key."=".urlencode( $control_value );
                }
            }
        }
        return $this->return_page.$query_string;
    }

    function parameter_exists( $parameter_name )
    {
        return isset( $this->parameters[$parameter_name] );
    }

    function add_parameter( $parameter_name, $parameter_desc, $control_type, $value_type, $values_list, $is_select, $is_insert, $is_update, $is_where )
    {
        $parameter_desc = $parameter_desc === "" ? $parameter_name : $parameter_desc;
        $this->parameters[$parameter_name] = array( $parameter_name, $parameter_desc, $control_type, "", $value_type, "", $values_list, $parameter_name, $parameter_name, false, true, $is_select, $is_insert, $is_update, $is_where, true, true );
        $this->parameters[$parameter_name][USE_IN_ORDER] = false;
        $this->parameters[$parameter_name][REQUIRED] = false;
        $this->parameters[$parameter_name][IS_VALID] = true;
    }

    function remove_parameter( $parameter_name )
    {
        if ( isset( $this->parameters[$parameter_name] ) )
        {
            unset( $this->parameters[$parameter_name] );
        }
    }

    function sort_parameters( )
    {
        $param_index = 0;
        $controls_order = array( );
        foreach ( $this->parameters as $param_name => $param_info )
        {
            ++$param_index;
            $control_order = isset( $param_info[CONTROL_ORDER] ) && strlen( $param_info[CONTROL_ORDER] ) ? $param_info[CONTROL_ORDER] : $param_index;
            $controls_order[$param_name] = $control_order;
        }
        array_multisort( $controls_order, $this->parameters );
    }

    function add_hidden( $parameter_name, $parameter_type )
    {
        $this->add_parameter( $parameter_name, $parameter_name, HIDDEN, $parameter_type, "", false, false, false, false );
        $this->parameters[$parameter_name][TRANSFER] = true;
    }

    function add_textbox( $parameter_name, $parameter_type, $parameter_desc = "" )
    {
        $this->add_parameter( $parameter_name, $parameter_desc, TEXTBOX, $parameter_type, "", true, true, true, false );
    }

    function add_checkbox( $parameter_name, $parameter_type, $parameter_desc = "" )
    {
        $this->add_parameter( $parameter_name, $parameter_desc, CHECKBOX, $parameter_type, "", true, true, true, false );
    }

    function add_select( $parameter_name, $parameter_type, $values_list, $parameter_desc = "" )
    {
        $this->add_parameter( $parameter_name, $parameter_desc, LISTBOX, $parameter_type, $values_list, true, true, true, false );
    }

    function add_radio( $parameter_name, $parameter_type, $values_list, $parameter_desc = "" )
    {
        $this->add_parameter( $parameter_name, $parameter_desc, RADIOBUTTON, $parameter_type, $values_list, true, true, true, false );
    }

    function add_checkboxlist( $parameter_name, $parameter_type, $values_list, $parameter_desc = "" )
    {
        $this->add_parameter( $parameter_name, $parameter_desc, CHECKBOXLIST, $parameter_type, $values_list, false, false, false, false );
    }

    function add_where( $parameter_name, $parameter_type, $parameter_desc = "" )
    {
        if ( isset( $this->parameters[$parameter_name] ) )
        {
            $this->parameters[$parameter_name][USE_IN_WHERE] = true;
        }
        else
        {
            $this->add_parameter( $parameter_name, $parameter_desc, HIDDEN, $parameter_type, "", false, false, false, true );
        }
    }

    function change_property( $parameter_name, $property_index, $property_value, $property_parameters = "" )
    {
        if ( isset( $this->parameters[$parameter_name] ) )
        {
            $this->parameters[$parameter_name][$property_index] = $property_value;
            if ( is_array( $property_parameters ) )
            {
                $this->parameters[$parameter_name][$property_index."_params"] = $property_parameters;
            }
        }
    }

    function get_property_value( $parameter_name, $property_index )
    {
        $property_value = "";
        if(isset($this->parameters[$parameter_name][$property_index]))
        {
            $property_value = $this->parameters[$parameter_name][$property_index];
        }
        return $property_value;
    }

    function remove_property( $parameter_name, $property_index )
    {
        if ( isset( $this->parameters[$parameter_name] ) )
        {
            unset( $this->parameters[$parameter_name][$property_index] );
        }
    }

    function is_empty( $parameter_name )
    {
        $value = isset( $this->parameters[$parameter_name][CONTROL_VALUE] ) ? $this->parameters[$parameter_name][CONTROL_VALUE] : "";
        if ( is_array( $value ) || strlen( $value ) )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function set_value( $parameter_name, $parameter_value )
    {
        $eol = get_eol( );
        if ( isset( $this->parameters[$parameter_name] ) )
        {
            $parameter = $this->parameters[$parameter_name];
            $type = $parameter[VALUE_TYPE];
            if ( ( $type == DATETIME || $type == DATE || $type == TIMESTAMP || $type == TIME ) && !is_array( $parameter_value ) && is_array( $parameter[VALUE_MASK] ) )
            {
                $date_value = parse_date( $parameter_value, $parameter[VALUE_MASK], $date_errors, $parameter[CONTROL_DESC] );
                if ( is_array( $date_value ) )
                {
                    $parameter_value = $date_value;
                }
            }
            if ( $parameter[CONTROL_TYPE] == CHECKBOXLIST )
            {
                $this->parameters[$parameter_name][CONTROL_VALUE][] = $parameter_value;
            }
            else
            {
                $this->parameters[$parameter_name][CONTROL_VALUE] = $parameter_value;
            }
        }
        else
        {
            echo str_replace( "{parameter_name}", $parameter_name, UNDEFINED_RECORD_PARAMETER_MSG )."<br>".$eol;
        }
    }

    function get_value( $parameter_name )
    {
        return $this->parameters[$parameter_name][CONTROL_VALUE];
    }

    function get_value_desc( $parameter_name )
    {
        $parameter = $this->parameters[$parameter_name];
        $control_type = $parameter[CONTROL_TYPE];
        if ( $control_type == CHECKBOX )
        {
            if ( $parameter[CONTROL_VALUE] == 1 )
            {
                $value = YES_MSG;
            }
            else
            {
                $value = NO_MSG;
            }
        }
        else if ( $control_type == RADIOBUTTON || $control_type == LISTBOX || $control_type == CHECKBOXLIST )
        {
            $value = get_array_value( $parameter[CONTROL_VALUE], $parameter[VALUES_LIST], "; " );
        }
        else
        {
            $value = $parameter[CONTROL_VALUE];
            if ( is_array( $value ) )
            {
                $value = va_date( $parameter[VALUE_MASK], $parameter[CONTROL_VALUE] );
            }
        }
        return $value;
    }

    function get_where_parameters( )
    {
        $where_params = 0;
        $this->where_set = true;
        foreach ( $this->parameters as $key => $parameter )
        {
            if ( $parameter[USE_IN_WHERE] )
            {
                ++$where_params;
                $control_value = get_param( $parameter[CONTROL_NAME] );
                $this->parameters[$key][CONTROL_VALUE] = $control_value;
                if ( !strlen( $control_value ) )
                {
                    $this->where_set = false;
                }
            }
            else if ( $parameter[CONTROL_TYPE] == HIDDEN )
            {
                $control_value = get_param( $parameter[CONTROL_NAME] );
                $this->parameters[$key][CONTROL_VALUE] = $control_value;
            }
        }
        if ( !$where_params )
        {
            $this->where_set = false;
        }
        return $this->where_set;
    }

    function get_form_values( )
    {
        $this->get_form_parameters( );
    }

    function get_form_parameters( )
    {
        $where_params = 0;
        $this->where_set = true;
        foreach ( $this->parameters as $key => $parameter )
        {
            call_event( $parameter, BEFORE_REQUEST );
            $control_value = get_param( $parameter[CONTROL_NAME] );
            if ( isset( $parameter[TRIM] ) && $parameter[TRIM] )
            {
                $control_value = trim( $control_value );
            }
            else
            {
                if ( isset( $parameter[LTRIM] ) && $parameter[LTRIM] )
                {
                    $control_value = ltrim( $control_value );
                }
                if ( isset( $parameter[RTRIM] ) && $parameter[RTRIM] )
                {
                    $control_value = rtrim( $control_value );
                }
            }
            if ( isset( $parameter[UCASE] ) && $parameter[UCASE] )
            {
                $control_value = strtoupper( $control_value );
            }
            else if ( isset( $parameter[LCASE] ) && $parameter[LCASE] )
            {
                $control_value = strtolower( $control_value );
            }
            else if ( isset( $parameter[UCWORDS] ) && $parameter[UCWORDS] )
            {
                $control_value = ucwords( $control_value );
            }
            if ( $parameter[CONTROL_TYPE] == CHECKBOXLIST )
            {
                $last_index = intval( $control_value );
                $i = 1;
                for ( ; $i <= $last_index; ++$i )
                {
                    $control_value = get_param( $parameter[CONTROL_NAME]."_".$i );
                    if ( strlen( $control_value ) )
                    {
                        $this->parameters[$key][CONTROL_VALUE][] = $control_value;
                    }
                }
                continue;
            }
            else
            {
                if ( $parameter[USE_IN_WHERE] )
                {
                    ++$where_params;
                    if ( !strlen( $control_value ) )
                    {
                        $this->where_set = false;
                    }
                }
                if ( is_array( $parameter[VALUE_MASK] ) )
                {
                    switch ( $parameter[VALUE_TYPE] )
                    {
                        case DATETIME :
                        case DATE :
                        case TIME :
                        case TIMESTAMP :
                            $date_value = parse_date( $control_value, $parameter[VALUE_MASK], $date_errors, $parameter[CONTROL_DESC] );
                            if ( is_array( $date_value ) )
                            {
                                $control_value = $date_value;
                            }
                    }
                }
                else if ( $parameter[CONTROL_TYPE] == CHECKBOX && !strlen( $control_value ) )
                {
                    $control_value = 0;
                }
                $this->parameters[$key][CONTROL_VALUE] = $control_value;
            }
            call_event( $parameter, AFTER_REQUEST );
        }
        if ( !$where_params )
        {
            $this->where_set = false;
        }
    }

    function validate()
    {
        global $db;
        $eol = get_eol();
        foreach ($this->parameters as $key => $parameter)
        {
            call_event( $parameter, BEFORE_VALIDATE, $this->parameters[$key] );
            $control_value = $this->parameters[$key][CONTROL_VALUE];
            $control_value_exists = is_array( $control_value ) || strlen( $control_value );
            $is_valid = true;
            $error_message = "";
            if ( $parameter[CONTROL_TYPE] == CHECKBOXLIST )
            {
                if ( $parameter[SHOW] && $parameter[REQUIRED] && !is_array( $control_value ) )
                {
                    $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], REQUIRED_MESSAGE );
                    $is_valid = false;
                }
            }
            else if ( $parameter[CONTROL_TYPE] == CHECKBOX )
            {
                if ( $parameter[SHOW] && $parameter[REQUIRED] && $control_value != 1 )
                {
                    $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], REQUIRED_MESSAGE );
                    $is_valid = false;
                }
            }
            else if ( $parameter[SHOW] && $parameter[REQUIRED] && !$control_value_exists )
            {
                $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], REQUIRED_MESSAGE );
                $is_valid = false;
            }
            else if ( $control_value_exists )
            {
                switch ( $parameter[VALUE_TYPE] )
                {
                    case INTEGER :
                    case FLOAT :
                    case NUMBER :
                        if ( !is_numeric( $control_value ) )
                        {
                            $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], INCORRECT_VALUE_MESSAGE );
                            $is_valid = false;
                            break;
                        }
                        break;
                    case DATETIME :
                    case DATE :
                    case TIME :
                    case TIMESTAMP :
                        $date_errors = "";
                        $date_value = parse_date( $control_value, $parameter[VALUE_MASK], $date_errors, $parameter[CONTROL_DESC] );
                        if ( $date_errors )
                        {
                            $error_message = $date_errors;
                            $is_valid = false;
                        }
                        else
                        {
                            $this->parameters[$key][CONTROL_VALUE] = $date_value;
                            break;
                        }
                }
            }
            if ( $is_valid && $parameter[SHOW] && $control_value_exists && $parameter[CONTROL_TYPE] != CHECKBOXLIST )
            {
                $min_length = $this->get_property_value( $key, MIN_LENGTH );
                $max_length = $this->get_property_value( $key, MAX_LENGTH );
                $min_value = $this->get_property_value( $key, MIN_VALUE );
                $max_value = $this->get_property_value( $key, MAX_VALUE );
                $regexp_mask = $this->get_property_value( $key, REGEXP_MASK );
                $regexp_error = $this->get_property_value( $key, REGEXP_ERROR );
                $unique = $this->get_property_value( $key, UNIQUE );
                if ( $min_length && strlen( $control_value ) < $min_length )
                {
                    $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], MIN_LENGTH_MESSAGE );
                    $error_message = str_replace( "{min_length}", $min_length, $error_message );
                    $is_valid = false;
                }
                else if ( $max_length && $max_length < strlen( $control_value ) )
                {
                    $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], MAX_LENGTH_MESSAGE );
                    $error_message = str_replace( "{max_length}", $max_length, $error_message );
                    $is_valid = false;
                }
                else if ( $regexp_mask && !preg_match( $regexp_mask, $control_value ) )
                {
                    $error_message = $regexp_error ? $regexp_error : INCORRECT_VALUE_MESSAGE;
                    $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], $error_message );
                    $is_valid = false;
                }
                else if ( strlen( $min_value ) && $control_value < $min_value )
                {
                    $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], MIN_VALUE_MESSAGE );
                    $error_message = str_replace( "{min_value}", $min_value, $error_message );
                    $is_valid = false;
                }
                else if ( strlen( $max_value ) && $max_value < $control_value )
                {
                    $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], MAX_VALUE_MESSAGE );
                    $error_message = str_replace( "{max_value}", $max_value, $error_message );
                    $is_valid = false;
                }
                else if ( $unique )
                {
                    $excluding_where = $this->where_set ? $this->get_where( false ) : "";
                    $where = " WHERE ".$parameter[COLUMN_NAME]."=".$db->tosql( $control_value, $parameter[VALUE_TYPE], $parameter[SQL_DELIMITERS], $parameter[USE_SQL_NULL] );
                    if ( strlen( $excluding_where ) )
                    {
                        $where .= " AND NOT (".$excluding_where.")";
                    }
                    $sql = " SELECT COUNT(*) FROM ".$this->table_name.$where;
                    $records_number = get_db_value( $sql );
                    if ( 0 < $records_number )
                    {
                        $error_message = str_replace( "{field_name}", $parameter[CONTROL_DESC], UNIQUE_MESSAGE );
                        $is_valid = false;
                    }
                }
                else if ( isset( $parameter[MATCHED] ) && $control_value != $this->parameters[$parameter[MATCHED]][CONTROL_VALUE] )
                {
                    $error_message = str_replace( "{field_one}", $parameter[CONTROL_DESC], MATCHED_MESSAGE );
                    $error_message = str_replace( "{field_two}", $this->parameters[$parameter[MATCHED]][CONTROL_DESC], $error_message );
                    $is_valid = false;
                }
            }
            if ( !$is_valid )
            {
                $this->parameters[$key][IS_VALID] = $is_valid;
                $this->parameters[$key][ERROR_DESC] = $error_message;
            }
            call_event( $parameter, AFTER_VALIDATE, $this->parameters[$key] );
        }
        foreach ( $this->parameters as $key => $parameter )
        {
            if ( !$this->parameters[$key][IS_VALID] )
            {
                $this->errors .= $this->parameters[$key][ERROR_DESC]."<br>".$eol;
            }
        }
        return strlen( $this->errors ) ? false : true;
    }

    function empty_values( )
    {
        foreach ( $this->parameters as $key => $parameter )
        {
            $this->parameters[$key][CONTROL_VALUE] = "";
        }
    }

    function check_where( )
    {
        $is_all_where = true;
        foreach ( $this->parameters as $key => $parameter )
        {
            if ( $parameter[USE_IN_WHERE] && !strlen( $parameter[CONTROL_VALUE] ) )
            {
                $is_all_where = false;
                break;
            }
        }
        return $is_all_where;
    }

    function insert_record( )
    {
        global $db;
        global $table_prefix;
        $record_inserted = false;
        $allow_insert = true;
        list( $host_valid, $license_expired, $va_code ) = va_license_check( );
        $license_valid = $host_valid && !$license_expired;
        $max_records = 0;
        if ( $this->table_name == $table_prefix."categories" )
        {
            if ( !$license_valid || !( $va_code & 1 ) )
            {
                $max_records = 10;
                $records_name = "categories";
            }
        }
        else if ( $this->table_name == $table_prefix."items" )
        {
            if ( !$license_valid || !( $va_code & 1 ) )
            {
                $max_records = 50;
                $records_name = "products";
            }
        }
        else if ( $this->table_name == $table_prefix."support" )
        {
            if ( !$license_valid || !( $va_code & 4 ) )
            {
                $max_records = 200;
                $records_name = "requests";
            }
        }
        else if ( $this->table_name == $table_prefix."support_departments" )
        {
            if ( !$license_valid || !( $va_code & 4 ) )
            {
                $max_records = 2;
                $records_name = "departments";
            }
        }
        else if ( $this->table_name == $table_prefix."articles_categories" )
        {
            if ( !$license_valid || !( $va_code & 2 ) )
            {
                $max_records = 20;
                $records_name = "categories";
            }
        }
        else if ( $this->table_name == $table_prefix."articles" )
        {
            if ( !$license_valid || !( $va_code & 2 ) )
            {
                $max_records = 200;
                $records_name = "articles";
            }
        }
        else if ( $this->table_name == $table_prefix."admins" )
        {
            if ( !$license_valid )
            {
                $max_records = 10;
                $records_name = "administrators";
            }
        }
        else if ( $this->table_name == $table_prefix."ads_categories" )
        {
            if ( !$license_valid || !( $va_code & 16 ) )
            {
                $max_records = 20;
                $records_name = "categories";
            }
        }
        else if ( $this->table_name == $table_prefix."ads_items" && ( !$license_valid || !( $va_code & 16 ) ) )
        {
            $max_records = 200;
            $records_name = "ads";
        }
        if ( 0 < $max_records )
        {
            $sql = "SELECT COUNT(*) FROM ".$this->table_name;
            $total_records = get_db_value( $sql );
            if ( $max_records <= $total_records )
            {
                $this->errors = str_replace( "{max_records}", $max_records, str_replace( "{records_name}", $records_name, MAX_RECORDS_LIMITATION_MSG ) );
                if ( $host_valid )
                {
                    $this->errors .= "<br>";
                }
                else
                {
                    $this->errors .= " (FREE version)<br>";
                }
                $this->errors .= str_replace( "{records_name}", $records_name, DELETE_RECORDS_BEFORE_PROCEED_MSG );
                $allow_insert = false;
            }
        }
        if ( $allow_insert )
        {
            $sql = $this->get_sql( INSERT_SQL );
            $record_inserted = $db->query( $sql );
        }
        return $record_inserted;
    }

    function update_record( )
    {
        global $db;
        $sql = $this->get_sql( UPDATE_SQL );
        return $db->query( $sql );
    }

    function delete_record( )
    {
        global $db;
        $sql = $this->get_sql( DELETE_SQL );
        return $db->query( $sql );
    }

    function update_related( $sql_type )
    {
        global $db;
        foreach ( $this->parameters as $parameter_name => $parameter )
        {
            if ( isset( $parameter[RELATED_TABLE] ) && strlen( $parameter[RELATED_TABLE] ) )
            {
                if ( $sql_type == UPDATE_SQL || $sql_type == DELETE_SQL )
                {
                    $sql = $this->get_related_sql( DELETE_SQL, $parameter_name );
                    if ( $sql )
                    {
                        $db->query( $sql );
                    }
                }
                if ( $sql_type == INSERT_SQL || $sql_type == UPDATE_SQL )
                {
                    $sql = $this->get_related_sql( INSERT_SQL, $parameter_name );
                    $i = 0;
                    for ( ; $i < sizeof( $sql ); ++$i )
                    {
                        $db->query( $sql[$i] );
                    }
                }
            }
        }
    }

    function get_order( $include_order = true )
    {
        global $db;
        $order_list = "";
        $order_parameters = 0;
        foreach ( $this->parameters as $key => $parameter )
        {
            if ( $parameter[USE_IN_ORDER] )
            {
                ++$order_parameters;
                if ( $order_parameters == 1 && $include_order )
                {
                    $order_list .= " ORDER BY ";
                }
                else if ( 1 < $order_parameters )
                {
                    $order_list .= ", ";
                }
                if ( $parameter[USE_IN_ORDER] == ORDER_ASC )
                {
                    $order = " ASC";
                }
                else if ( $parameter[USE_IN_ORDER] == ORDER_DESC )
                {
                    $order = " DESC";
                }
                else
                {
                    $order = "";
                }
                $order_list .= $parameter[COLUMN_NAME].$order;
            }
        }
        return $order_list;
    }

    function get_where( $include_where = true )
    {
        global $db;
        $where_list = "";
        $where_parameters = 0;
        foreach ( $this->parameters as $key => $parameter )
        {
            if ( $parameter[USE_IN_WHERE] )
            {
                ++$where_parameters;
                if ( $where_parameters == 1 && $include_where )
                {
                    $where_list .= " WHERE ";
                }
                else if ( 1 < $where_parameters )
                {
                    $where_list .= " AND ";
                }
                $where_list .= $parameter[COLUMN_NAME]."=".$db->tosql( $parameter[CONTROL_VALUE], $parameter[VALUE_TYPE], $parameter[SQL_DELIMITERS], $parameter[USE_SQL_NULL] );
            }
        }
        return $where_list;
    }

    function get_sql( $sql_type )
    {
        global $db;
        $sql = "";
        switch ( $sql_type )
        {
            case SELECT_SQL :
                $select_parameters = 0;
                $select_list = "";
                foreach ( $this->parameters as $key => $parameter )
                {
                    if ( $parameter[USE_IN_SELECT] )
                    {
                        ++$select_parameters;
                        if ( 1 < $select_parameters )
                        {
                            $select_list .= ", ";
                        }
                        $select_list .= $parameter[COLUMN_NAME];
                    }
                }
                $sql = " SELECT ".$select_list." FROM ".$this->table_name.$this->get_where( ).$this->get_order( );
                break;
            case INSERT_SQL :
                $insert_parameters = 0;
                $columns_list = "";
                $values_list = "";
                foreach ( $this->parameters as $key => $parameter )
                {
                    if ( $parameter[USE_IN_INSERT] )
                    {
                        ++$insert_parameters;
                        if ( 1 < $insert_parameters )
                        {
                            $columns_list .= ",";
                            $values_list .= ",";
                        }
                        $columns_list .= $parameter[COLUMN_NAME];
                        $values_list .= $db->tosql( $parameter[CONTROL_VALUE], $parameter[VALUE_TYPE], $parameter[SQL_DELIMITERS], $parameter[USE_SQL_NULL] );
                    }
                }
                $sql = " INSERT INTO ".$this->table_name." (".$columns_list.") VALUES (".$values_list.")";
                break;
            case UPDATE_SQL :
                $update_parameters = 0;
                $where_parameters = 0;
                $update_list = "";
                foreach ( $this->parameters as $key => $parameter )
                {
                    if ( $parameter[USE_IN_UPDATE] )
                    {
                        ++$update_parameters;
                        if ( 1 < $update_parameters )
                        {
                            $update_list .= ",";
                        }
                        $update_list .= $parameter[COLUMN_NAME]."=";
                        $update_list .= $db->tosql( $parameter[CONTROL_VALUE], $parameter[VALUE_TYPE], $parameter[SQL_DELIMITERS], $parameter[USE_SQL_NULL] );
                    }
                }
                $sql = " UPDATE ".$this->table_name." SET ".$update_list.$this->get_where( );
                break;
            case DELETE_SQL :
                $sql = " DELETE FROM ".$this->table_name.$this->get_where( );
        }
        return $sql;
    }

    function get_related_sql( $sql_type, $parameter_name )
    {
        global $db;
        $sql = "";
        $where = "";
        $columns_list = "";
        $where_values_list = "";
        if ( isset( $this->parameters[$parameter_name][RELATED_TABLE] ) && strlen( $this->parameters[$parameter_name][RELATED_TABLE] ) )
        {
            $parameter = $this->parameters[$parameter_name];
            $is_where_set = true;
            if ( isset( $this->parameters[$parameter_name][RELATED_WHERE] ) && is_array( $this->parameters[$parameter_name][RELATED_WHERE] ) )
            {
                $related_where = $this->parameters[$parameter_name][RELATED_WHERE];
                $i = 0;
                for ( ; $i < sizeof( $related_where ); ++$i )
                {
                    $where_parameter = $this->parameters[$related_where[$i][1]];
                    if ( strlen( $where_parameter[CONTROL_VALUE] ) )
                    {
                        if ( $i == 0 )
                        {
                            $where .= " WHERE ";
                        }
                        else
                        {
                            $where .= " AND ";
                            $columns_list .= ", ";
                            $where_values_list .= ", ";
                        }
                        $where .= $related_where[$i][0]."=".$db->tosql( $where_parameter[CONTROL_VALUE], $where_parameter[VALUE_TYPE], $where_parameter[SQL_DELIMITERS], $where_parameter[USE_SQL_NULL] );
                        $columns_list .= $related_where[$i][0];
                        $where_values_list .= $db->tosql( $where_parameter[CONTROL_VALUE], $where_parameter[VALUE_TYPE], $where_parameter[SQL_DELIMITERS], $where_parameter[USE_SQL_NULL] );
                    }
                    else
                    {
                        $is_where_set = false;
                        break;
                    }
                }
            }
            if ( $is_where_set )
            {
                $related_table = $this->parameters[$parameter_name][RELATED_TABLE];
                switch ( $sql_type )
                {
                    case SELECT_SQL :
                        $sql = " SELECT ".$parameter[COLUMN_NAME]." FROM ".$related_table.$where;
                        break;
                    case INSERT_SQL :
                        if ( $columns_list )
                        {
                            $columns_list .= ", ";
                        }
                        $columns_list .= $parameter[COLUMN_NAME];
                        $control_values = $parameter[CONTROL_VALUE];
                        if ( !is_array( $control_values ) && strlen( $control_values ) )
                        {
                            $control_values[0] = $control_values;
                        }
                        $i = 0;
                        for ( ; $i < sizeof( $control_values ); ++$i )
                        {
                            $column_value = $db->tosql( $control_values[$i], $parameter[VALUE_TYPE], $where_parameter[SQL_DELIMITERS] );
                            $values_list = strlen( $where_values_list ) ? $where_values_list.", ".$column_value : $column_value;
                            $sql[] = " INSERT INTO ".$related_table." (".$columns_list.") VALUES (".$values_list.")";
                        }
                        continue;
                        break;
                    case DELETE_SQL :
                        $sql = " DELETE FROM ".$related_table.$where;
                }
            }
        }
        return $sql;
    }

    function get_db_values( )
    {
        global $db;
        $record_returned = false;
        foreach ( $this->parameters as $parameter_name => $parameter )
        {
            if ( isset( $parameter[TRANSFER] ) && $parameter[TRANSFER] )
            {
                $this->parameters[$parameter_name][CONTROL_VALUE] = get_param( $parameter[CONTROL_NAME] );
            }
        }
        $sql = $this->get_sql( SELECT_SQL );
        $db->query( $sql );
        if ( $db->next_record( ) )
        {
            $record_returned = true;
            foreach ( $this->parameters as $key => $parameter )
            {
                if ( $parameter[USE_IN_SELECT] )
                {
                    $this->parameters[$key][CONTROL_VALUE] = $db->f( $parameter[COLUMN_NAME], $parameter[VALUE_TYPE] );
                }
            }
        }
        foreach ( $this->parameters as $parameter_name => $parameter )
        {
            if ( isset( $parameter[RELATED_TABLE] ) && strlen( $parameter[RELATED_TABLE] ) )
            {
                $sql = $this->get_related_sql( SELECT_SQL, $parameter_name );
                if ( $sql )
                {
                    $db->query( $sql );
                    $values = "";
                    while ( $db->next_record( ) )
                    {
                        $values[] = $db->f( $parameter[COLUMN_NAME], $parameter[VALUE_TYPE] );
                    }
                    $this->parameters[$key][CONTROL_VALUE] = is_array( $values ) && sizeof( $values ) == 1 ? $values[0] : $values;
                }
            }
        }
        return $record_returned;
    }

    function set_default_values( )
    {
        foreach ( $this->parameters as $parameter_name => $parameter )
        {
            if ( isset( $this->parameters[$parameter_name][DEFAULT_VALUE] ) )
            {
                $this->parameters[$parameter_name][CONTROL_VALUE] = $this->parameters[$parameter_name][DEFAULT_VALUE];
            }
            else if ( isset( $parameter[TRANSFER] ) && $parameter[TRANSFER] )
            {
                $this->parameters[$parameter_name][CONTROL_VALUE] = get_param( $parameter[CONTROL_NAME] );
            }
        }
    }

    function set_parameters( )
    {
        $this->set_form_parameters( );
    }

    function set_form_parameters( )
    {
        global $t;
        if ( strlen( $this->errors ) )
        {
            $t->set_var( "errors_list", $this->errors );
            $t->parse( $this->errors_block, false );
        }
        else
        {
            $t->set_var( $this->errors_block, "" );
        }
        if ( strlen( $this->success_message ) )
        {
            $t->set_var( "success_message", $this->success_message );
            $t->parse( $this->success_block, false );
        }
        else
        {
            $t->set_var( $this->success_block, "" );
        }
        $is_sections_block = $t->block_exists( $this->sections_block );
        foreach ( $this->parameters as $key => $parameter )
        {
            call_event( $this->parameters[$key], BEFORE_SHOW, $this->parameters[$key] );
            if ( $this->parameters[$key][SHOW] )
            {
                $control_type = $this->parameters[$key][CONTROL_TYPE];
                if ( $this->parameters[$key][REQUIRED] )
                {
                    $t->set_var( $this->parameters[$key][PARSE_NAME]."_required", $this->required_symbol );
                }
                else
                {
                    $t->set_var( $this->parameters[$key][PARSE_NAME]."_required", "" );
                }
                if ( $control_type == TEXTBOX || $control_type == TEXTAREA || $control_type == HIDDEN )
                {
                    $value = $this->parameters[$key][CONTROL_VALUE];
                    if ( is_array( $value ) )
                    {
                        $value = va_date( $this->parameters[$key][VALUE_MASK], $this->parameters[$key][CONTROL_VALUE] );
                    }
                    $t->set_var( $this->parameters[$key][PARSE_NAME], htmlspecialchars( $value ) );
                }
                else if ( $control_type == CHECKBOX )
                {
                    if ( $parameter[CONTROL_VALUE] == 1 )
                    {
                        $t->set_var( $this->parameters[$key][PARSE_NAME], "checked" );
                    }
                    else
                    {
                        $t->set_var( $this->parameters[$key][PARSE_NAME], "" );
                    }
                }
                else if ( $control_type == RADIOBUTTON || $control_type == LISTBOX || $control_type == CHECKBOXLIST )
                {
                    $t->set_var( $this->parameters[$key][PARSE_NAME]."_size", sizeof( $this->parameters[$key][VALUES_LIST] ) );
                    set_options( $this->parameters[$key][VALUES_LIST], $this->parameters[$key][CONTROL_VALUE], $this->parameters[$key][PARSE_NAME], $this->parameters[$key] );
                }
                if ( $this->parameters[$key][IS_VALID] )
                {
                    $t->set_var( $this->parameters[$key][PARSE_NAME]."_class", $this->default_class );
                }
                else
                {
                    $t->set_var( $this->parameters[$key][PARSE_NAME]."_class", $this->errors_class );
                }
                if ( $t->block_exists( $this->parameters[$key][PARSE_NAME]."_block" ) )
                {
                    if ( $is_sections_block )
                    {
                        $t->parse_to( $this->parameters[$key][PARSE_NAME]."_block", $this->sections_block );
                    }
                    else
                    {
                        $t->parse( $this->parameters[$key][PARSE_NAME]."_block", false );
                    }
                }
            }
            else
            {
                $t->set_var( $this->parameters[$key][PARSE_NAME]."_block", "" );
            }
            call_event( $this->parameters[$key], AFTER_SHOW, $this->parameters[$key] );
        }
    }

}

?>
