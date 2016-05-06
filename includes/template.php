<?php


class va_template
{

    var $_globals = array( );
    var $_blocks = array( );
    var $_template_path = "./";
    var $_parse_array = array( );
    var $_position = 0;
    var $_length = 0;
    var $_delimiter = "";
    var $_tag_sign = "";
    var $_begin_block = "";
    var $_end_block = "";
    var $show_tags = false;

    function va_template( $template_path )
    {
        $this->_template_path = $template_path;
        $this->_delimiter = chr( 27 );
        $this->_tag_sign = chr( 15 );
        $this->_begin_block = chr( 16 );
        $this->_end_block = chr( 17 );
    }

    function get_template_path( )
    {
        return $this->_template_path;
    }

    function set_template_path( $template_path )
    {
        $this->_template_path = $template_path;
    }

    function set_file( $block_name, $filename )
    {
        global $is_admin_path;
        $file_path = $this->_template_path."/".$filename;
        $is_file_exists = file_exists( $file_path );
        if ( !$is_file_exists )
        {
            if ( $is_admin_path )
            {
                $file_path = "../templates/user/".$filename;
            }
            else
            {
                $file_path = "./templates/user/".$filename;
            }
            $is_file_exists = file_exists( $file_path );
        }
        if ( $is_file_exists )
        {
            $file_content = join( "", file( $file_path ) );
            if ( $block_name == "main" )
            {
                $ad = va_license_message( );
                if ( $ad )
                {
                    if ( strpos( $file_content, "</body>" ) )
                    {
                        $file_content = str_replace( "</body>", $ad."</body>", $file_content );
                    }
                    else if ( strpos( $file_content, "</html>" ) )
                    {
                        $file_content = str_replace( "</html>", $ad."</html>", $file_content );
                    }
                    else
                    {
                        $file_content .= $ad;
                    }
                }
            }
            $this->set_block( $block_name, $file_content );
        }
        else
        {
            $file_path = $this->_template_path."/".$filename;
            echo FILE_DOESNT_EXIST_MSG."<b>".$file_path."</b>";
            exit( );
        }
    }

    function set_block( $block_name, $block_content )
    {
        $delimiter = $this->_delimiter;
        $tag_sign = $this->_tag_sign;
        $begin_block = $this->_begin_block;
        $end_block = $this->_end_block;
        $block_content = preg_replace( "/(<!\\-\\-\\s*begin\\s*(\\w+)\\s*\\-\\->)/is", $delimiter.$begin_block.$delimiter."\\2".$delimiter, $block_content );
        $block_content = preg_replace( "/(<!\\-\\-\\s*end\\s*(\\w+)\\s*\\-\\->)/is", $delimiter.$end_block.$delimiter."\\2".$delimiter, $block_content );
        $block_content = preg_replace( "/(\\{(\\w+)\\})/is", $delimiter.$tag_sign.$delimiter."\\2".$delimiter, $block_content );
        $this->_parse_array = explode( $delimiter, $block_content );
        $this->_position = 0;
        $this->_length = sizeof( $this->_parse_array );
        $this->_parse_block( $block_name, false );
    }

    function _parse_block( $block_name, $is_subblock = true )
    {
        $block_array = array( );
        $block_number = 0;
        $block_array[0] = 0;
        $tag_sign = $this->_tag_sign;
        $begin_block = $this->_begin_block;
        $end_block = $this->_end_block;
        while ( $this->_position < $this->_length )
        {
            $element_array = $this->_parse_array[$this->_position];
            if ( $element_array == $tag_sign )
            {
                ++$block_number;
                $block_array[$block_number] = $this->_parse_array[$this->_position + 1];
                $this->_position += 2;
            }
            else if ( $element_array == $begin_block )
            {
                ++$block_number;
                $block_array[$block_number] = $this->_parse_array[$this->_position + 1];
                $this->_position += 2;
                $this->_parse_block( $this->_parse_array[$this->_position - 1], true );
            }
            else if ( $element_array == $end_block && $is_subblock )
            {
                if ( $this->_parse_array[$this->_position + 1] == $block_name )
                {
                    $block_array[0] = $block_number;
                    $this->_position += 2;
                    $this->_blocks[$block_name] = $block_array;
                    return;
                }
                else
                {
                    echo PARSE_ERROR_IN_BLOCK_MSG.$block_name;
                    exit( );
                }
            }
            else
            {
                ++$block_number;
                $block_array[$block_number] = $block_name."#".$block_number;
                $this->_globals[$block_name."#".$block_number] = $element_array;
                ++$this->_position;
            }
        }
        $block_array[0] = $block_number;
        $this->_blocks[$block_name] = $block_array;
    }

    function set_var( $key, $value )
    {
        $this->_globals[$key] = $value;
    }

    function set_vars( $values )
    {
        if ( is_array( $values ) )
        {
            foreach ( $values as $key => $value )
            {
                $this->_globals[$key] = $value;
            }
        }
    }

    function get_var( $key )
    {
        return isset( $this->_globals[$key] ) ? $this->_globals[$key] : "";
    }

    function delete_var( $block_name )
    {
        if ( isset( $this->_globals[$block_name] ) )
        {
            unset( $this->_globals[$block_name] );
        }
    }

    function var_exists( $var_name )
    {
        return isset( $this->_globals[$var_name] );
    }

    function copy_var( $var_from, $var_to, $accumulate = true )
    {
        $var_value = $this->_globals[$var_from];
        $this->_globals[$var_to] = $accumulate && isset( $this->_globals[$var_to] ) ? $this->_globals[$var_to].$var_value : $var_value;
    }

    function block_exists( $block_name, $parent_block_name = "" )
    {
        $block_exists = false;
        if ( $parent_block_name === "" )
        {
            $block_exists = isset( $this->_blocks[$block_name] );
        }
        else if ( isset( $this->_blocks[$parent_block_name] ) )
        {
            $block_exists = in_array( $block_name, $this->_blocks[$parent_block_name] );
        }
        return $block_exists;
    }

    function parse( $block_name, $accumulate = true )
    {
        $this->global_parse( $block_name, $accumulate, false );
    }

    function rparse( $block_name, $accumulate = true )
    {
        $this->global_parse( $block_name, $accumulate, true, true );
    }

    function sparse( $block_name, $accumulate = true )
    {
        $this->global_parse( $block_name, $accumulate, false, true );
    }

    function parse_to( $block_name, $parse_to, $accumulate = true )
    {
        $this->global_parse( $block_name, $accumulate, false, true, $parse_to );
    }

    function global_parse( $block_name, $accumulate = true, $reverse_parse = false, $safe_parse = false, $parse_to = "" )
    {
        $block_value = "";
        if ( isset( $this->_blocks[$block_name] ) )
        {
            if ( !$parse_to )
            {
                $parse_to = $block_name;
            }
            $block_array = $this->_blocks[$block_name];
            $globals = $this->_globals;
            $array_size = $block_array[0];
            $i = 1;
            for ( ; $i <= $array_size; ++$i )
            {
                if ( isset( $globals[$block_array[$i]] ) )
                {
                    $array_value = $globals[$block_array[$i]];
                }
                else if ( defined( $block_array[$i] ) )
                {
                    $array_value = constant( $block_array[$i] );
                }
                else if ( $this->show_tags )
                {
                    $array_value = "{".$block_array[$i]."}";
                }
                else
                {
                    $array_value = "";
                }
                $block_value .= $array_value;
            }
            if ( $reverse_parse )
            {
                $this->_globals[$parse_to] = $accumulate && isset( $this->_globals[$parse_to] ) ? $block_value.$this->_globals[$parse_to] : $block_value;
            }
            else
            {
                $this->_globals[$parse_to] = $accumulate && isset( $this->_globals[$parse_to] ) ? $this->_globals[$parse_to].$block_value : $block_value;
            }
        }
        else if ( !$safe_parse )
        {
            echo BLOCK_DOENT_EXIST_MSG.$block_name;
            exit( );
        }
    }

    function pparse( $block_name, $accumulate = true )
    {
        $this->parse( $block_name, $accumulate );
        echo $this->_globals[$block_name];
    }

    function print_block( $block_name )
    {
        reset( $this->_blocks[$block_name] );
        echo "<table border=\"1\">";
        $value = each( $this->_blocks[$block_name][1] );
        $key = each( $this->_blocks[$block_name][0] );
        while ( each( $this->_blocks[$block_name] ) )
        {
            if ( $key != 0 )
            {
                echo "<tr><th valign=top>".$value."</th><td>".nl2br( htmlspecialchars( $this->_globals[$value] ) )."</td></tr>";
            }
            else
            {
                echo "<tr><th valign=top>".NUMBER_OF_ELEMENTS_MSG."</th><td>".$value."</td></tr>";
            }
        }
        echo "</table>";
    }

}

?>
