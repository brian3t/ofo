<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  previews_functions.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
	
	include_once($root_folder_path . "includes/sql_functions.php");

	class VA_Previews extends VA_Model{
		
		var $__tablename = "items_files";
		/**
		 * primary key
		 *
		 * @var integer
		 */		
		var $file_id;
		
		/**
		 * external key
		 *
		 * @var integer
		 */		
		var $item_id;
		
		/**
		 * preview title
		 *
		 * @var string
		 */
		var $preview_title;
		
		/**
		 * preview link
		 *
		 * @var string
		 */
		var $preview_path;
		
		/**
		 * preview image link
		 *
		 * @var string
		 */
		var $preview_image;
		
		/**
		 * 0 - not availiable / hidden
		 * 1 - as downloadable
		 * 2 - with player
		 * 
		 * @var integer
		 */
		var $preview_type;
		
		/**
		 * 0 - not availiable / hidden
		 * 1 - in separate section
		 * 2 - under large image on product details
		 * 3 - under large image on products list
		 * 
		 * @var integer
		 */
		var $preview_position;
		
		var $file_extension;
		var $preview_width;
		var $preview_height;
		var $preview_external;
		
		
		function __onInit() {			
			$this->preview_width  = 200;
			$this->preview_height = 200;
		}
		function __findSQL() {
			global $table_prefix, $db;
			$sql  = " SELECT item_id, file_id, preview_type, preview_title, preview_path, preview_image, preview_position ";
			$sql .= " FROM " . $table_prefix . "items_files ";
			$where = "";
			if (strlen($this->file_id)) {
				if ($where) $where .= " AND ";
				$where .= " file_id=" . $db->tosql($this->file_id, INTEGER);
			}
			if (strlen($this->item_id)) {
				if ($where) $where .= " AND ";
				$where .= " item_id=" . $db->tosql($this->item_id, INTEGER);
			}
			if (is_array($this->preview_type) && count($this->preview_type)) {
				if ($where) $where .= " AND ";
				$where .= " preview_type IN (" . $db->tosql($this->preview_type, INTEGERS_LIST) . ")";			
			} elseif (strlen($this->preview_type)) {
				if ($where) $where .= " AND ";
				$where .= " preview_type=" . $db->tosql($this->preview_type, INTEGER);
			}
			if (strlen($this->preview_title)) {
				if ($where) $where .= " AND ";
				$where .= " preview_title=" . $db->tosql($this->preview_title, TEXT);
			}
			if (strlen($this->preview_path)) {
				if ($where) $where .= " AND ";
				$where .= " preview_path=" . $db->tosql($this->preview_path, TEXT);
			}
			if (strlen($this->preview_image)) {
				if ($where) $where .= " AND ";
				$where .= " preview_image=" . $db->tosql($this->preview_image, TEXT);
			}
			if (strlen($this->preview_position)) {
				if ($where) $where .= " AND ";
				$where .= " preview_position=" . $db->tosql($this->preview_position, INTEGER);
			}
			if (strlen($where)) {
				$sql .= " WHERE " . $where;
			}			
			return $sql;
		}
		function __onGet(&$preview) {
			if (!$this->file_extension) {
				$tmp = explode(".", $preview->preview_path);
				$preview->file_extension = strtolower(array_pop($tmp));	
			} else {
				$preview->file_extension = $this->file_extension;
			}			
			$preview->preview_width    = $this->preview_width;
			$preview->preview_height   = $this->preview_height;
			if (!preg_match("/^http(s)?:\/\//", $preview->preview_path)) {
				$preview_size = @getimagesize($preview->preview_path);
				if (is_array($preview_size)) {
					$preview->preview_width  = $preview_size[0];
					$preview->preview_height = $preview_size[1];
				}
				$preview->preview_external = false;
			} else {
				$preview->preview_external = true;
			}
		}
		function showOne($block_name, $preview_index = 0) {
			global $t;
			$t->set_file("preview_body", "previews.html");
						
			$previews_type_block = "preview_simple_link";
			$preview_path        = $this->preview_path;
			$preview_image       = $this->preview_image;
			$preview_title       = get_translation($this->preview_title);
			$preview_width       = $this->preview_width;
			$preview_height      = $this->preview_height;
			if (strlen($this->preview_title)) {
				$preview_title = $this->preview_title;
			} else {
				$preview_title = $this->preview_path;
			}
			if ($this->preview_type == 2) {
				switch ($this->file_extension) {
					case "swf":
						$previews_type_block = "preview_swf";
					break;
					case "mp3":
						$preview_height = 15;
						$previews_type_block = "preview_swf";
						$preview_path = "swf/xspf_player_slim.swf?song_url=" . $preview_path
							. "&song_title=" . $preview_title . "&player_title=" . $preview_title;
					break;
					case "xspf":
						$previews_type_block = "preview_swf";
						$preview_path = "swf/xspf_player.swf?playlist_url=" . $preview_path 
							. "&playlist_title=" . $preview_title . "&player_title=" . $preview_title;
					break;
					case "flv":						
						if (!$this->preview_external) {
							 $preview_path = "../" .  $preview_path;
						}
						/*$preview_path = "swf/flvplayer.swf?theFile=" . $preview_path
							. "&defaultImage=" . $preview_image . "&startPlayingOnload=false";*/
						$preview_path = "swf/player.swf?file=" .  $preview_path . "&image=" . $preview_image;
						$previews_type_block = "preview_swf";
					break;
					case "avi":case "mpg": case "mpeg": case "wmv":
						$previews_type_block = "preview_avi";
					break;
					
				}
			}
			
			$t->set_var("file_id",        $this->file_id);
			$t->set_var("item_id",        $this->item_id);
			$t->set_var("preview_title",  $preview_title);			
			$t->set_var("preview_path",   $preview_path);
			$t->set_var("preview_image",  $preview_image);
			$t->set_var("preview_width",  $preview_width);	
			$t->set_var("preview_height", $preview_height);	
								
			$t->parse_to($previews_type_block, $block_name, true);		
		}
	}
?>