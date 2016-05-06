<?php


class va_tree
{

    var $db_category_id;
    var $db_parent_id;
    var $db_category_name;
    var $db_table;
    var $tree_name;
    var $top_name;
    var $erase_tags;

    function va_tree( $db_category_id, $db_category_name, $db_parent_id, $db_table, $tree_name, $top_name = "Top", $erase_tags = true )
    {
        $this->db_category_id = $db_category_id ? $db_category_id : "category_id";
        $this->db_category_name = $db_category_name ? $db_category_name : "category_name";
        $this->db_parent_id = $db_parent_id ? $db_parent_id : "parent_category_id";
        $this->db_table = $db_table ? $db_table : "categories";
        $this->tree_name = $tree_name ? $tree_name : "tree";
        $this->top_name = $top_name;
        $this->erase_tags = $erase_tags;
    }

    function get_path( $category_id )
    {
        global $db;
        $path = "";
        if ( $category_id )
        {
            $sql = " SELECT ".$this->db_category_id.",";
            $sql .= $this->db_parent_id." FROM ".$this->db_table;
            $sql .= " WHERE ".$this->db_category_id."=";
            while ( $category_id )
            {
                $db->query( $sql.$db->tosql( $category_id, INTEGER ) );
                if ( $db->next_record( ) )
                {
                    $path = $category_id.",".$path;
                    $category_id = $db->f( $this->db_parent_id );
                }
                else
                {
                    $category_id = "0";
                    $path = "";
                }
            }
            $path = "0,".$path;
        }
        else
        {
            $path = "0,";
        }
        return $path;
    }

    function show( $category_id )
    {
        global $db;
        global $t;
        global $language_code;
        $tree_name = $this->tree_name;
        if ( !$category_id )
        {
            if ( strlen( $this->top_name ) )
            {
                $current_id = "0";
                $t->set_var( $tree_name."_current_name", $this->top_name );
                $t->set_var( $tree_name."_current_id", "0" );
                $t->set_var( $tree_name, "" );
            }
        }
        else
        {
            $current_id = $category_id;
            $sql = " SELECT ".$this->db_category_id.",".$this->db_category_name.",";
            $sql .= $this->db_parent_id." FROM ".$this->db_table;
            $sql .= " WHERE ".$this->db_category_id."=";
            while ( $category_id )
            {
                $db->query( $sql.$db->tosql( $category_id, INTEGER ) );
                if ( $db->next_record( ) )
                {
                    $category_name = $db->f( $this->db_category_name );
                    $category_name = get_translation( $category_name, $language_code );
                    if ( $this->erase_tags )
                    {
                        $category_name = strip_tags( $category_name );
                    }
                    if ( $current_id != $category_id )
                    {
                        $t->set_var( $tree_name."_cat_id", $category_id );
                        $t->set_var( $tree_name."_cat_name", $category_name );
                        $t->rparse( $tree_name );
                    }
                    else
                    {
                        $t->set_var( $tree_name."_current_name", $category_name );
                        $t->set_var( $tree_name."_current_id", $category_id );
                    }
                    $category_id = $db->f( $this->db_parent_id );
                }
                else
                {
                    $t->set_var( $tree_name."_current_name", "NO CATEGORY" );
                    $category_id = "0";
                }
            }
            if ( strlen( $this->top_name ) )
            {
                $t->set_var( $tree_name."_cat_id", "0" );
                $t->set_var( $tree_name."_cat_name", $this->top_name );
                $t->rparse( $tree_name );
            }
        }
    }

}

?>
