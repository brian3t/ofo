<?php

class va_editgrid
{

    var $classname = "VA_EditGrid";
    var $table_name;
    var $record;
    var $block_name;
    var $record_number;
    var $values = array();
    var $errors = array();
    var $events = array();
    var $events_parameters = array();

    function va_editgrid($record, $grid_name)
    {
        $this->record = $record;
        $this->block_name = $grid_name;
    }

    function set_event($event_name, $event_function, $event_parameters = "")
    {
        $this->events[$event_name] = $event_function;
        if(is_array( $event_parameters ))
        {
            $this->events[$event_name."_params"] = $event_parameters;
        }
    }

    function get_form_values($number_records = 0)
    {
        $number_records = $number_records ? $number_records : get_param("number_".$this->block_name);
        $i = 1;
        for (; $i <= $number_records; ++$i)
        {
            foreach ($this->record->parameters as $parameter_name => $parameter)
            {
                $form_value = get_param($this->record->parameters[$parameter_name][CONTROL_NAME]."_".$i);
                if($this->record->parameters[$parameter_name][CONTROL_TYPE] == CHECKBOX && !strlen($form_value))
                {
                    $form_value = 0;
                }
                $this->values[$i][$parameter_name] = $form_value;
            }
        }
    }

    function validate($number_records = 0)
    {
        $number_records = $number_records ? $number_records : get_param("number_".$this->block_name);
        $is_valid = true;
        $i = 1;
        for ( ;$i <= $number_records; ++$i )
        {
            $non_empty = $this->set_record($i);
            if (!get_param($this->block_name."_delete_".$i) && $non_empty)
            {
                $is_valid = $this->record->validate() && $is_valid;
                $this->errors[$i] = $this->record->errors;
            }
        }
        return $is_valid;
    }

    function set_values( $parameter_name, $parameter_value )
    {
        $i = 1;
        for ( ; $i <= sizeof( $this->values ); ++$i )
        {
            $this->values[$i][$parameter_name] = $parameter_value;
        }
    }

    function set_value( $parameter_name, $parameter_value )
    {
        $this->record->parameters[$parameter_name][CONTROL_VALUE] = $parameter_value;
        if ( $this->record_number )
        {
            $this->values[$this->record_number][$parameter_name] = $parameter_value;
        }
    }

    function get_value( $parameter_name )
    {
        $value = "";
        if ( $this->record_number )
        {
            $value = $this->values[$this->record_number][$parameter_name];
        }
        return $value;
    }

    function set_parameters_all( $number_records = 0 )
    {
        global $t;
        $number_records = $number_records ? $number_records : get_param( "number_".$this->block_name );
        $i = 1;
        for ( ; $i <= $number_records; ++$i )
        {
            $delete_checkbox = get_param( $this->block_name."_delete_".$i ) ? "checked" : "";
            $t->set_var( $this->block_name."_delete", $delete_checkbox );
            $t->set_var( "row_number", $i );
            $t->set_var( $this->block_name."_number", $i );
            $this->set_record( $i );
            call_event( $this->events, BEFORE_SHOW );
            $this->record->set_parameters( );
            call_event( $this->events, AFTER_SHOW );
            $t->parse( $this->block_name, true );
        }
    }

    function set_parameters( $i )
    {
        global $t;
        $t->set_var( "row_number", $i );
        $t->set_var( $this->block_name."_number", $i );
        $this->set_record( $i );
        $this->record->set_parameters( );
        $t->parse( $this->block_name, true );
    }

    function set_record( $record_number )
    {
        $this->record_number = $record_number;
        $this->record->errors = isset( $this->errors[$record_number] ) ? $this->errors[$record_number] : "";
        foreach ( $this->record->parameters as $key => $parameter )
        {
            $control_value = isset( $this->values[$record_number][$key] ) ? $this->values[$record_number][$key] : "";
            if ( is_array( $this->record->parameters[$key][VALUE_MASK] ) )
            {
                switch ( $this->record->parameters[$key][VALUE_TYPE] )
                {
                    case DATETIME :
                    case DATE :
                    case TIME :
                    case TIMESTAMP :
                        $date_value = parse_date( $control_value, $this->record->parameters[$key][VALUE_MASK], $date_errors, $this->record->parameters[$key][CONTROL_DESC] );
                        if ( is_array( $date_value ) )
                        {
                            $control_value = $date_value;
                        }
                }
            }
            else if ( $this->record->parameters[$key][CONTROL_TYPE] == CHECKBOX && !strlen( $control_value ) )
            {
                $control_value = 0;
            }
            $this->record->parameters[$key][CONTROL_VALUE] = $control_value;
            $this->values[$record_number][$key] = $control_value;
        }
        $non_empty = false;
        $checked_index = $this->record->check_where( ) ? USE_IN_UPDATE : USE_IN_INSERT;
        foreach ( $this->record->parameters as $key => $parameter )
        {
            if ( $this->record->parameters[$key][CONTROL_TYPE] == CHECKBOX )
            {
                if ( $this->values[$record_number][$key] == 1 )
                {
                    $non_empty = true;
                }
            }
            else if ( $this->record->parameters[$key][$checked_index] && ( is_array( $this->values[$record_number][$key] ) || strlen( $this->values[$record_number][$key] ) ) && $this->record->parameters[$key][CONTROL_TYPE] != CHECKBOX && $this->record->parameters[$key][CONTROL_TYPE] != HIDDEN )
            {
                $non_empty = true;
            }
        }
        return $non_empty;
    }

    function insert_record( )
    {
        $this->record->insert_record( );
    }

    function insert_all( $number_records = 0 )
    {
        $number_records = $number_records ? $number_records : get_param( "number_".$this->block_name );
        $i = 1;
        for ( ; $i <= $number_records; ++$i )
        {
            if ( $this->set_record( $i ) )
            {
                call_event( $this->events, BEFORE_INSERT );
                $this->record->insert_record( );
                call_event( $this->events, AFTER_INSERT );
            }
        }
    }

    function change_property( $parameter_name, $property_name, $property_value )
    {
        $this->record->change_property( $parameter_name, $property_name, $property_value );
    }

    function update_record()
    {
        $this->record->update_record();
    }

    function update_all($number_records = 0)
    {
        $number_records = $number_records ? $number_records : get_param("number_".$this->block_name);
        $i = 1;
        for ( ; $i <= $number_records; ++$i )
        {
            $non_empty = $this->set_record( $i );
            $is_all_where = $this->record->check_where( );
            if ( get_param( $this->block_name."_delete_".$i ) )
            {
                if ( $is_all_where )
                {
                    call_event( $this->events, BEFORE_DELETE );
                    $this->record->delete_record( );
                    call_event( $this->events, AFTER_DELETE );
                }
            }
            else if ( $is_all_where )
            {
                call_event( $this->events, BEFORE_UPDATE );
                $this->record->update_record( );
                call_event( $this->events, AFTER_UPDATE );
            }
            else if ( $non_empty )
            {
                call_event( $this->events, BEFORE_INSERT );
                $this->record->insert_record( );
                call_event( $this->events, AFTER_INSERT );
            }
        }
    }

	function get_db_values()
	{
		global $db;
		$number_records = 0;
		$sql = $this->record->get_sql(SELECT_SQL);
		$db->query($sql);
		while($db->next_record())
		{
			++$number_records;
			$parameters = $this->record->parameters;
			reset($parameters);
			while(list($key, $value) = each($parameters))
			{
				if($parameters[$key][USE_IN_SELECT])
				{
					$this->values[$number_records][$key] = $db->f($parameters[$key][COLUMN_NAME], $parameters[$key][VALUE_TYPE]);
					continue;
				}
			}
		}
	
		return $number_records;
	}

}

?>
