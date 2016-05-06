<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_css_editor.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/shopping_cart.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "includes/order_links.php");
	include_once ($root_folder_path . "includes/parameters.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");
	include_once("./admin_css_editor_includes.php");
	include_once($root_folder_path."messages/".$language_code."/admin_messages.php");
	
	global $colors,$css_array;
	sort($colors);
	check_admin_security();
		
	$category_id = get_param("category_id");
	$subcategory_id = get_param("subcategory_id");
	$operation = get_param("operation");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_css_editor.html");
	$t->set_var("block_param_header", "");
	$t->set_var("display", "none");
	$t->set_var("img_more","images/plus.gif");
	$t->set_var("css_definition", "");
	$t->set_var("preview_block", "");
	$t->set_var("css_block", "");
	
	$styles[] = array();
	$sql = "SELECT setting_value FROM va_global_settings WHERE setting_name='layout_id'";
	$db->query($sql);
	$db->next_record();
	$layout_id = $db->f("setting_value");
		
	$sql = "SELECT templates_dir, style_name FROM va_layouts WHERE layout_id=".$db->tosql($layout_id, INTEGER);
	$db->query($sql);
	$db->next_record();
	$templates_dir = $db->f("templates_dir");
//	$style_name = $db->f("style_name");
	$style_name = "default_css";

	
	$t->set_var("style_name",$style_name);
	$stylesheet = trim(file_get_contents("../styles/".$style_name.".css"));
	$xml = trim(file_get_contents("html_codes.xml"));
	
	$f = fopen("../styles/".$style_name.".css", "w");
	$stylesheet = preg_replace("/(\r|\n|\t|\s)*\/\*[^*]*\*\//U","",$stylesheet);
	$stylesheet = preg_replace("/;(\r|\n|\t|\s)*(\w+)\:\s*;/i",";",$stylesheet);
	fwrite($f, $stylesheet);
	
	//make backup
	$f_bak = fopen("../styles/".$style_name.".css.bak", "w");
	fwrite($f_bak, $stylesheet);
	
	
	$categories = array();
	$subcategories = array();
	$content = array();
	$sub_description = "";
	preg_match_all("/\/\*\*+\s(.+?)\s\*\*+\/(.*?)\/\*\*+\s(.+?)\s\*\*+\//is", $stylesheet, $matches, PREG_SET_ORDER);
	
	preg_match_all("/<category name\=\"(.*)\"\>(.*)<\/category>/Uis", $xml, $category_xml, PREG_SET_ORDER);
	preg_match_all("/<description\>(.*)<\/description>/Uis", $xml, $descriptions, PREG_SET_ORDER);
	preg_match_all("/<html_code\>(.*)<\/html_code>/Uis", $xml, $html_codes, PREG_SET_ORDER);
	 
	// html code $html_codes[0][0]."<br>";
	// description $descriptions[0][0]."<br>";
	// category name $category_xml[0][1]."<br>";
	
	$contentArr ="";
	
	for ($i=0; $i<sizeof($matches); $i++)
	{
		$categories[$i] =  $matches[$i][1];
		preg_match_all("/(.*?)\{([^}]*?)\}/s", $matches[$i][2], $matches2, PREG_SET_ORDER);
		$contentArr .= "contentArr[$i] = new Array();\n";
		for ($j=0; $j<sizeof($matches2); $j++)
		{
			$subcategories[$i][$j] = preg_replace("/\/\*.*\*\//","",$matches2[$j][1]);
			$content[$i][$j] = preg_replace("/\/\*.*\*\//","",$matches2[$j][2]);
			$content_for_arr = str_replace("\r\n","",$content[$i][$j]);
			$content_for_arr = str_replace("'","\'",$content_for_arr);
			$contentArr .= "contentArr[$i][$j] = '".$content_for_arr."'\n";
		}
	}
	set_session("session_subcategories", $subcategories);
		if (strlen($category_id)!=0 && strlen($subcategory_id)!=0)
		{
			$info = -1;
		 	for ($k=0; $k<sizeof($category_xml); $k++)
		 	{
		 		if (trim($category_xml[$k][1]) == trim($categories[$category_id]))
		 		{
		 			$info = $k;
		 		}	
		 	}

	 	if ($info >=0)
	 	{
	 		$sub_preview = $html_codes[$info][1];
	 		$t->set_var("preview_load","onload=\"parent.frames['preview_frame'].window.location.reload();\"");
	 		$t->parse("preview_block");
	 	}
	 		else 
	 		{
	 			$sub_preview = "";
	 			$sub_description = "";
	 			$t->set_var("preview_load","");
	 		}
	 		$_SESSION['preview_html'] = $sub_preview;;
		}
		
	
	for ($i=0; $i<sizeof($categories); $i++)
	{
		$t->set_var("subcategory_block", "");
		if (isset($subcategories[$i]) && (trim($categories[$i]) != "User Home Icons"))
		{
			for ($j=0; $j<sizeof($subcategories[$i]); $j++)
			{
				$subcategory="";
				for ($k=0; $k<sizeof($category_xml); $k++)
		 		{
			 		if (trim($category_xml[$k][1]) == trim($subcategories[$i][$j]))
			 		{
			 			$subcategory = $descriptions[$k][0];
			 		}	
		 		}
				if (strlen($subcategory)!=0) {$t->set_var("subcategory",$subcategory);}
					else {$t->set_var("subcategory",$subcategories[$i][$j]);}
				$t->set_var("i", $i);
				$t->set_var("j", $j);
				$t->parse("subcategory_block", true);
			}
			if (strlen($category_id)!=0 && $category_id == $i)
			{
				$t->set_var("display", "block");
				$t->set_var("img_more","images/minus.gif");
			}
				else 
				{
					$t->set_var("display", "none");
					$t->set_var("img_more","images/plus.gif");
				}
			
				
			$t->set_var("category", $categories[$i]);
			$t->parse("category_block", true);
		}
	}

	$t->set_var("param_block", "");
	$t->set_var("param_block_end", "");
	if (strlen($category_id)==0 || strlen($subcategory_id)==0)
		$t->set_var("content", "");
	 if (strlen($category_id)!=0 && strlen($subcategory_id)!=0)
	 {
	 	$content_value = $content[$category_id][$subcategory_id];
	 	$content_ex = explode(";", $content_value);
	 	$t->set_var("content_ex",sizeof($content_ex)-1);
	 	for($i=0; $i<sizeof($content_ex)-1; $i++)
	 	{
	 		$t->set_var("input_block","");
	 		$t->set_var("select_block","");
	 		$t->set_var("special_block","");
			$t->set_var("special",0);
	 		$select=0;
	 		$t->set_var("j", $i);
	 		$t->set_var("content_array",trim($content_ex[$i]));
	 		$content_temp = explode (":",$content_ex[$i]);
	 		$t->set_var("param_name_main", trim($content_temp[0]));
	 		$t->set_var("param_value_main", trim($content_temp[1]));
		 		for ($l=0; $l<sizeof($css_array); $l++)
		 		{
		 			
		 			if($css_array[$l][0]==trim($content_temp[0]))
		 			{
		 				$t->set_var("select_row", "");
		 				$param_values=explode(",",$css_array[$l][1]);
		 				foreach($param_values as $param_value)
		 				{
		 					$t->set_var("param_value",$param_value);
		 					if ($param_value == trim($content_temp[1])) {$t->set_var("selected","selected");}
		 						else {$t->set_var("selected","");}
		 					$t->parse("select_row", true);
		 				}
		 				$t->set_var("param_name",trim($content_temp[0]));
		 				$t->set_var("special",0);
		 				$t->set_var("subparam",0);
		 				$t->parse("select_block", false);
		 				$select=1;
		 			}
		 		}
		 	if ($select!=1)
		 	{
		 		$param_name=trim($content_temp[0]);
		 		$param_arr = explode(" ",trim($content_temp[1]));
		 		//begin  for border
		 		if (strpos($param_name,"border")!==false  && (sizeof($param_arr)==3))
		 		{
		 			$param_values=explode(",",$css_array[3][1]);
		 			$special_value = "<div class=\"show_or_hide_div\" id=\"param_block_$i\" name=\"param_block_$i\" style=\"display:none;\">";
			 		$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">width</font></div>";
			 		$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_0\" id=\"".$param_name."_0\" value=\"".$param_arr[0]."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
			 		$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">style</font></div>";
			 		$special_value .= "<div class=\"desc_div\"><select name=\"".$param_name."_1\" id=\"".$param_name."_1\" onClick=\"document.getElementById('update_button').disabled=false;\">";
		 				foreach($param_values as $param_value)
		 				{
		 					
		 					if ($param_value == trim($param_arr[1])) {$this_selected="selected";}
		 						else {$this_selected="";}
		 						$special_value .= "<option value=\"".$param_value."\" ".$this_selected." >".$param_value."</option>";
		 				}
		 			$special_value .= "</select></div>";
			 		$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">color</font></div>";
			 		$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_2\" id=\"".$param_name."_2\" value=\"".$param_arr[2]."\" onClick=\"document.getElementById('update_button').disabled=false;\">";
			 		foreach($colors as $color)
			 		{
			 			if ($color==trim($param_arr[2])) 
			 			{
			 				$special_value .= "<img name=\"".$param_name."_2_img\" id=\"".$param_name."_2_img\" src=\"../images/tr.gif\" style=\"background:#".$color.";\" hspace=\"2px\" width=\"15px\" height=\"15px\" onclick=\"document.getElementById('color_change').value='".$param_name."_2';document.getElementById('div_colors').style.display=block;\">";
			 			}
			 		}
			 		
			 		$special_value .= "</div>";
			 		$special_value .= "</div>"; 
		 			$t->set_var("special","1");
		 			$t->set_var("subparam","3");
		 			$t->set_var("special_value",$special_value);	
		 			$t->set_var("param_name",$param_name);
		 			$t->parse("special_block", false);
		 		}
		 		//end for border
		 		//begin for margin and padding if 4 elements
		 		elseif ((($param_name=="padding") || ($param_name=="margin")) && (sizeof($param_arr)==4))
		 		{
		 				$special_value = "";
			 			$param_values = array("top","right","bottom","left");
			 			$special_value = "<div class=\"show_or_hide_div\" id=\"param_block_$i\" name=\"param_block_$i\" style=\"display:none;\">";
			 			for ($j=0;$j<4;$j++)
			 			{
			 				$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">".$param_name."-".$param_values[$j]."</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$param_arr[$j]."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
			 			}
			 			$special_value .= "</div>"; 
			 			$t->set_var("special","1");
			 			$t->set_var("subparam","4");
			 			
			 			$t->set_var("special_value",$special_value);	
			 			$t->set_var("param_name",$param_name);
			 			$t->parse("special_block", false);
		 		}
		 		//end for margin and padding
		 		//begin for margin and padding if 2 elements
		 		elseif ((($param_name=="padding") || ($param_name=="margin")) && (sizeof($param_arr)==2))
		 		{
			 			$param_values = array("top","right","bottom","left");
			 			$special_value = "<div class=\"show_or_hide_div\" id=\"param_block_$i\" name=\"param_block_$i\" style=\"display:none;\">";
			 			for ($j=0;$j<4;$j++)
			 			{
			 				
			 				$element  = $j%2;
			 				$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">".$param_name."-".$param_values[$j]."</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$param_arr[$element]."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
			 			}
			 			$special_value .= "</div>"; 
			 			$t->set_var("special","1");
			 			$t->set_var("subparam","4");
			 			
			 			$t->set_var("special_value",$special_value);	
			 			$t->set_var("param_name",$param_name);
			 			$t->parse("special_block", false);
		 		}
		 		//end for margin and padding
		 		//begin for font
		 		elseif ($param_name=="font")
		 		{
			 		$special_value = "<div class=\"show_or_hide_div\" id=\"param_block_$i\" name=\"param_block_$i\" style=\"display:none;\">";
			 			
		 			$font_value = trim(implode(" ",$param_arr));
					$tok = strtok($font_value, " ");
					$h=0;
					$font_family = "";
					$size_values = explode(",",$css_array[9][1]);
					$font_style = explode(",",$css_array[10][1]);
					$font_weight = explode(",",$css_array[5][1]);
					$font_variant = explode(",",$css_array[11][1]);
					
					while ($tok !== false) {
					    if (($tok == 'normal' && $h==0) || $tok == 'italic' || $tok == 'oblique')
						{
							$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">font-style</font></div>";
			 				$special_value .= "<div class=\"desc_div\">";
							$special_value .= "<select name=\"".$param_name."_".$h."\" id=\"".$param_name."_".$h."\" onChange=\"document.getElementById('update_button').disabled=false;\">";
			 				foreach($font_style as $temp_style)
			 				{
			 					if ($temp_style == $tok) $selected = "selected";
			 						else $selected = "";
			 					$special_value .= "<option value=\"".$temp_style."\"".$selected.">".$temp_style."</option>";
			 				}
			 				$special_value .= "</select></div>";
			 				$h++;
			 			}
						elseif (($tok == 'normal' && $h==1) || $tok == 'small-caps')
						{
							$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">font-variant</font></div>";
			 				$special_value .= "<div class=\"desc_div\">";
							$special_value .= "<select name=\"".$param_name."_".$h."\" id=\"".$param_name."_".$h."\" onChange=\"document.getElementById('update_button').disabled=false;\">";
			 				foreach($font_variant as $temp_variant)
			 				{
			 					if ($temp_variant == $tok) $selected = "selected";
			 						else $selected = "";
			 					$special_value .= "<option value=\"".$temp_variant."\" ".$selected.">".$temp_variant."</option>";
			 				}
			 				$special_value .= "</select></div>";
							$h++;
						}
						elseif (($tok == 'normal' && $h==2) || $tok == 'bold' || $tok == 'bolder' || $tok == 'lighter')
						{
							$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">font-weight</font></div>";
			 				$special_value .= "<div class=\"desc_div\">";
							$special_value .= "<select name=\"".$param_name."_".$h."\" id=\"".$param_name."_".$h."\" onChange=\"document.getElementById('update_button').disabled=false;\">";
			 				foreach($font_weight as $temp_weight)
			 				{
			 					if ($temp_weight == $tok) $selected = "selected";
			 						else $selected = "";
			 					$special_value .= "<option value=\"".$temp_weight."\" ".$selected.">".$temp_weight."</option>";
			 				}
			 				$special_value .= "</select></div>";
							$h++;
						}
						elseif (sizeof($sizes = explode("|",$tok)) > 1)
						{
							$hidden_value = $sizes[0]."|".$sizes[1];
							preg_match('/(\d+)(\D*)/',$sizes[0],$matches_size);
							$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">font-size</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"hidden\" name=\"".$param_name."_".$h."\" id=\"".$param_name."_".$h."\" value=\"".$hidden_value."\">";
			 				$special_value .= "<input type=\"text\" name=\"".$param_name."_".$h."_1\" id=\"".$param_name."_".$h."_1\" value=\"".$matches_size[1]."\" onClick=\"fontConstructor('$h','4');document.getElementById('update_button').disabled=false;\"></div>";
			 				$special_value .= "<div class=\"desc_div\">";
			 				$special_value .= "<select name=\"".$param_name."_".$h."_2\" id=\"".$param_name."_".$h."_2\" onChange=\"fontConstructor('$h','4');document.getElementById('update_button').disabled=false;\">";
			 				foreach($size_values as $size_value)
			 				{
			 					if ($size_value == $matches_size[2]) $selected = "selected";
			 						else $selected = "";
			 					$special_value .= "<option value=\"".$size_value."\" ".$selected.">".$size_value."</option>";
			 				}
			 				$special_value .= "</select></div>";
							preg_match('/(\d+)(\D*)/',$sizes[1],$matches_line);
							$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">line-height</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input class=\"digit_input\" type=\"text\" name=\"".$param_name."_".$h."_3\" id=\"".$param_name."_".$h."_3\" value=\"".$matches_line[1]."\" onClick=\"fontConstructor('$h','4');document.getElementById('update_button').disabled=false;\">";
			 				$special_value .= "";
			 				$special_value .= "<select name=\"".$param_name."_".$h."_4\" id=\"".$param_name."_".$h."_4\" onChange=\"fontConstructor('$h','4');document.getElementById('update_button').disabled=false;\">";
			 				foreach($size_values as $size_value)
			 				{
			 					if ($size_value == $matches_line[2]) $selected = "selected";
			 						else $selected = "";
			 					$special_value .= "<option value=\"".$size_value."\" ".$selected.">".$size_value."</option>";
			 				}
			 				$special_value .= "</select></div>";
						}
						elseif (preg_match('/(\d+)(\D*)/',$tok,$matches3))
						{
								$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">font-size</font></div>";
			 					$special_value .= "<div class=\"desc_div\"><input type=\"hidden\" name=\"".$param_name."_".$h."\" id=\"".$param_name."_".$h."\" value=\"".$tok."\">";
			 					$special_value .= "<input type=\"text\" name=\"".$param_name."_".$h."_1\" id=\"".$param_name."_".$h."_1\" value=\"".$matches3[1]."\" onClick=\"fontConstructor('$h','2');document.getElementById('update_button').disabled=false;\"></div>";
			 					$special_value .= "<div class=\"desc_div\">";
			 					$special_value .= "<select name=\"".$param_name."_".$h."_2\" id=\"".$param_name."_".$h."_2\" onChange=\"fontConstructor('$h','2');document.getElementById('update_button').disabled=false;\">";
			 					foreach($size_values as $size_value)
			 					{
			 						if ($size_value == $matches3[2]) $selected = "selected";
			 							else $selected = "";
			 						$special_value .= "<option value=\"".$size_value."\" ".$selected." >".$size_value."</option>";
			 					}
			 					$special_value .= "</select></div>";
						} 	
						else 
						{
							$font_family .= $tok." ";
						}
					    
					    $tok = strtok(" ");
					}
					
					if (strlen($font_family)>0)
					{
						$h++;
						$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">font-family</font></div>";
			 			$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$h."\" id=\"".$param_name."_".$h."\" value=\"".$font_family."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
					}
					
					$special_value .= "</div>"; 
			 		$t->set_var("special","1");
			 		$t->set_var("subparam",$h+1);
			 			
			 		$t->set_var("special_value",$special_value);	
			 		$t->set_var("param_name",$param_name);
			 		$t->parse("special_block", false);
					
		 		}

		 		//end for font
		 		//begin for background
		 		elseif ($param_name == "background")
		 		{
		 			$horizontal = 1;
		 			$special_value = "<div class=\"show_or_hide_div\" id=\"param_block_$i\" name=\"param_block_$i\" style=\"display:none;\">";
		 			for ($j=0; $j<sizeof($param_arr);$j++)
		 			{
		 				$subparam = $param_arr[$j];
		 				if ($subparam[0] == '#')
		 				{
		 					$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">color</font></div>";
			 				$special_value .= "<div class=\"desc_div\">";
			 				$special_value .= "<img name=\"".$param_name."_".$j."_img\" id=\"".$param_name."_".$j."_img\" src=\"../images/tr.gif\" style=\"background:".$subparam.";\" hspace=\"2px\" width=\"15px\" height=\"15px\" onclick=\"document.getElementById('color_change').value='".$param_name."_".$j."';document.getElementById('div_colors').style.display='block';\">";
			 				$special_value .= "<input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$subparam."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
		 					
		 				}
		 				elseif (strpos($subparam,"url")!==FALSE)
		 				{
		 					//echo "url - ".$subparam."<br>";
		 					$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">URL</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$subparam."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
		 					
		 				}
		 				elseif (strpos($subparam,"repeat")!==FALSE)
		 				{
		 					$repeat_values = explode(",",$css_array[2][1]);
		 					
		 					$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">repeat</font></div>";
			 				//$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$subparam."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
			 				$special_value .= "<div class=\"desc_div\"><select name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" onClick=\"document.getElementById('update_button').disabled=false;\">";
			 				foreach ($repeat_values as $repeat_value)
			 				{
			 					if ($repeat_value == $subparam) $selected = "selected";
			 					else $selected = "";
			 					$special_value .= "<option value='".$repeat_value."' ".$selected." >".$repeat_value."</option>";
			 				}
			 				$special_value .= "</select></div>";
		 					
		 				}
		 				elseif ($subparam == 'left' || $subparam == 'right')
		 				{
		 					//echo "horizontal - ".$subparam."<br>";
		 					$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">horizontal</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$subparam."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
		 					
		 				}
		 				elseif ($subparam == 'top' || $subparam == 'bottom')
		 				{
		 					//echo "vertical - ".$subparam."<br>";
		 					$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">vertical</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$subparam."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
		 					
		 				}
		 				elseif (($subparam == 'center' || preg_match('/\d+/',$subparam)) && ($j==sizeof($param_arr)-2 || $j==sizeof($param_arr)-1) && $horizontal==1)
		 				{
		 					//echo "horizontal - ".$subparam."<br>";
		 					$horizontal = 0;
		 					$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">horizontal</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$subparam."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
		 					
		 				}
		 				elseif (($subparam == 'center' || preg_match('/\d+/',$subparam)) && ($j==sizeof($param_arr)-1))
		 				{
		 					//echo "vertical - ".$subparam."<br>";
		 					$special_value .= "<div class=\"desc_title_div\"><font color=\"black\">vertical</font></div>";
			 				$special_value .= "<div class=\"desc_div\"><input type=\"text\" name=\"".$param_name."_".$j."\" id=\"".$param_name."_".$j."\" value=\"".$subparam."\" onClick=\"document.getElementById('update_button').disabled=false;\"></div>";
		 				}
		 			}
		 			
		 			$special_value .= "</div>"; 
			 		$t->set_var("special","1");
			 		$t->set_var("subparam",$j);
			 			
			 		$t->set_var("special_value",$special_value);	
			 		$t->set_var("param_name",$param_name);
			 		$t->parse("special_block", false);
			 			
		 		}
		 		//end for background
		 		else
		 		{
		 			if (strpos($param_name,"color")!==FALSE)
			 		{
			 			foreach($colors as $color)
			 			{
			 				if (strpos(trim($content_temp[1]),$color)!== FALSE)
			 				{
			 					$icon_color = "#".$color;
			 					break;
			 				}
			 					else $icon_color = trim($content_temp[1]);
			 			}
			 			$t->set_var("img_icon","<img name=\"".$param_name."_img\" id=\"".$param_name."_img\" src=\"../images/tr.gif\" style=\"background:".$icon_color.";\" hspace=\"2px\" width=\"15px\" height=\"15px\" onclick=\"document.getElementById('color_change').value='".$param_name."'; document.getElementById('div_colors').style.display='block';\">");	
			 		}
			 		else {$t->set_var("img_icon","");}
		 			$t->set_var("param_name", $param_name);
		 			$t->set_var("param_value", trim($content_temp[1]));
		 			$t->set_var("special",0);	
		 			$t->parse("input_block", false);
		 		}
		 	}
	 		$t->parse("param_block", true);
	 	//	$l++;
	 	}
	 	$t->parse("param_block_end", false);
	 	//	echo strlen($sub_description);
	 	
	 	$css_class_name = str_replace(",",",<br>",trim($subcategories[$category_id][$subcategory_id]));
	 	$css_class_value_arr = explode(";",trim($content_value));
	 	
	 	$css_class_value_arr_size = sizeof($css_class_value_arr);
	 	unset($css_class_value_arr[$css_class_value_arr_size-1]);
	 	
	 	sort($css_class_value_arr);
	 	//print_r($css_class_value_arr);
	 	$css_class_value = "";
	 	foreach($css_class_value_arr as $css_value)
	 	{
	 		$css_class_value .= "&nbsp;&nbsp;&nbsp;&nbsp;".trim($css_value).";<br>";
	 	}
	 
	 	$css_definition = $css_class_name."<br>{<br>".$css_class_value."}";
	 	
	 	if (strlen($css_definition)!=0)
	 	{
	 		$t->set_var("css_definition", $css_definition);
	 		$t->parse("css_block");
	 	}
		$t->set_var("category_name",$categories[$category_id]);
		if (strlen($sub_description)!=0) {$t->set_var("subcategory_name",$sub_description);}
			else {$t->set_var("subcategory_name",$subcategories[$category_id][$subcategory_id]);}
		$t->parse("block_param_header");
	 	$t->set_var("category_id",$category_id);
	 	$t->set_var("subcategory_id",$subcategory_id);	
	 }
	 else
	 {
	 	$t->set_var("block_param_header", "");
	 //	$t->set_var("display", "none");
	 }
	 
	 $t->set_var("div_colors","");
	 foreach($colors as $div_color)
	 {
	 		$t->set_var("div_color",$div_color);
	 		$t->parse("div_colors",true);
	 }
	 
	 if ($operation == "cancel")
	 {
	 	header("Location: admin_css_editor.php");
	 }
	 elseif ($operation == "save") 
	 {
	 	$replace_cat = $subcategories[$category_id][$subcategory_id];
	 	$first_s = strpos($stylesheet, $replace_cat);
	 	$last_s = strpos(strstr($stylesheet, $replace_cat), "}");
	 	$length_s = $first_s + $last_s;
	 	$array1 = array();
	 	$content_value = $content[$category_id][$subcategory_id];
	 	$content_ex = explode(";", $content_value);
		$replace_string = $replace_cat." {";
	 	$replace_css_value = str_replace(",",",<br>",trim($subcategories[$category_id][$subcategory_id]))."<br>{<br>";
	 	/*for($i=0; $i<sizeof($content_ex)-1; $i++)
	 	{
			//echo "1.<br>";
	 		$special = get_param("special_".$i);
	 		$subparam = get_param("subparam_".$i);
	 		$content_temp = explode (":",$content_ex[$i]);
	 		$array1[$i][0] = get_param(trim($content_temp[0]."_name"));//param_name
	 		//echo $array1[$i][0]."<br>";
	 			if ($special==1)
	 			{
	 				$temp_value="";
	 				for($s=0; $s<$subparam; $s++)
	 				{
	 					$temp_value .= get_param(trim($content_temp[0])."_".$s)." ";
	 				}
	 				$array1[$i][1] = $temp_value;
	 				//echo " temp - ".$temp_value." - get - ".trim($content_temp[0])."_".$s."<br>";
	 			}
	 			else 
	 			{
	 				$array1[$i][1] = get_param(trim($content_temp[0]));//param_value
	 				//echo $content_temp[0]." - ".$array1[$i][1]."<br>";
	 			}
	 			
	 		$replace_string .= $array1[$i][0].": ".$array1[$i][1].";";
	 	}*/
	 	$replace = get_param("replace");
	 	$replace_value_arr = explode(";",trim($replace));
	 	$replace_value_arr_size = sizeof($replace_value_arr);
	 	unset($replace_value_arr[$replace_value_arr_size-1]);
	 	sort($replace_value_arr);
	 	$replace_value = "";
	 	foreach($replace_value_arr as $replace_value_this)
	 	{
	 		$replace_value .= $replace_value_this."; ";
	 		$replace_css_value .= "&nbsp;&nbsp;&nbsp;&nbsp;".trim($replace_value_this).";<br>";
	 	}
	
	 	$replace_string .= $replace_value; 
	 	$replace_string .= "}\r";	
	 	$replace_css_value .= "<br>} ||";
	 	echo $replace_css_value;
		//echo "replacement - ".$replace_string."<br>";
		//echo "first - ".$first_s." last - ".$last_s;
	 	$stylesheet = substr_replace($stylesheet, $replace_string, $first_s, $last_s+2);
	 	$f = fopen("../styles/".$style_name.".css", "w");
	 	fwrite($f, $stylesheet);
	 	fclose($f);
	// header("Location: admin_css_editor.php?category_id=$category_id&subcategory_id=$subcategory_id");
	 }
	 
	include("./admin_header.php");
	include("./admin_footer.php");
	//$t1->pparse("main");
	$t->pparse("main");

?>