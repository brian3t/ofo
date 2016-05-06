<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  image_functions.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


function gd_error(&$errors)
{
	if (!function_exists("gd_info")) {
		$errors .= "GD library not loaded.<br>\n";
		return true;
	} else {
		return false;
	}
}

function check_image_errors($filename, &$errors)
{
	if (gd_error($errors)) {
		return true;
	} else {
		if (!file_exists($filename)) {
			$errors .= "The file does not exist: " . $filename . "<br>\n";
			return true;
		} else {
			$file_info = @getimagesize($filename);
			if (!empty($file_info)) {
				return false;
			}	else {
				$errors .= "Image file error.<br>\n";
				return true;
			}
		}
	}
}

function image_supported($filename, &$errors) {
	if (check_image_errors($filename, $errors)) {
		return false;
	} else {
		$file_info = @getimagesize($filename);
		$format = $file_info[2];
		// 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel), 8 = TIFF(motorola), 9 = JPC, 10 = JP2, 11 = JPX.
		switch ($format) {
			case 1:
				if (imagetypes() & IMG_GIF) {
					return true;
				} else {
					$errors	.= "GIF image format is not supported.<br>\n";
					return false;
				}
				break;
			case 2:
				if (imagetypes() & IMG_JPG) {
					return true;
				} else {
					$errors	.= "JPG image format is not supported.<br>\n";
					return false;
				}
				break;
			case 3:
				if (imagetypes() & IMG_PNG) {
					return true;
				} else {
					$errors	.= "PNG image format is not supported.<br>\n";
					return false;
				}
				break;
			default :
				$errors	.= "Format is not supported.<br>\n";
				return false;
				break;
		}
	}
}

function image_watermark($original_image, $watermark_image, $image_position = "C", $image_pct = 50, $watermark_text = "", $text_size = 20, $text_color = "white", $text_angle = 0, $text_position = "C", $text_pct = 50, $output_image = "")
{
	global $settings;
	$font_type = 5; // 1,2,3,4,5 for built-in fonts in latin2 encoding where higher numbers corresponding to larger fonts
	$font_file = "./includes/font/comic.ttf"; // The name of the TrueType font file 

	$jpeg_quality = get_setting_value($settings, "jpeg_quality", 75);
	// get original image
	$original_id = "";
	$original_info = @getimagesize($original_image);
	$original_type = isset($original_info[2]) ? $original_info[2] : "";
	if ($original_type == 1) {
		$original_id = @imagecreatefromgif($original_image);
	} elseif ($original_type == 2) {
	  $original_id = @imagecreatefromjpeg($original_image); 
	} elseif ($original_type == 3) {
		$original_id = @imagecreatefrompng($original_image);
	}

	if (!$original_id) { /* if we can't open original image */
		$original_id = imagecreatetruecolor(60, 60); 
		$original_info = array(60, 60);
		$original_type = 3;
		$bgc = imagecolorallocate($original_id, 255, 255, 255);
		$tc  = imagecolorallocate($original_id, 0, 0, 0);
		imagefilledrectangle($original_id, 0, 0, 60, 60, $bgc);
		imagestring($original_id, 1, 5, 10, "Error", $tc);
		imagestring($original_id, 1, 5, 20, "Loading", $tc);
		imagestring($original_id, 1, 5, 30, "Image", $tc);
		imagestring($original_id, 1, 5, 40, basename($original_image), $tc);
	}

	if ($watermark_image) // apply image watermark
	{
		$watermark_id = "";
		$watermark_info = @getimagesize($watermark_image);
		$image_type = isset($watermark_info[2]) ? $watermark_info[2] : "";
		$watermark_is_transparent = get_setting_value($settings, "watermark_is_transparent", 0);
		// 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel), 8 = TIFF(motorola), 9 = JPC, 10 = JP2, 11 = JPX.
		if ($image_type == 1) {
			$watermark_id = @imagecreatefromgif($watermark_image);
		} elseif ($image_type == 2) {
	    $watermark_id = @imagecreatefromjpeg($watermark_image); 
		} elseif ($image_type == 3) {
			$watermark_id = @imagecreatefrompng($watermark_image);
		}

		if (!$watermark_id) { /* if we can't open image create error image */
			$watermark_id = imagecreatetruecolor(60, 60); 
			$watermark_info = array(60, 60);
			$bgc = imagecolorallocate($watermark_id, 255, 255, 255);
			$tc  = imagecolorallocate($watermark_id, 0, 0, 0);
			imagefilledrectangle($watermark_id, 0, 0, 60, 60, $bgc);
			imagestring($watermark_id, 1, 5, 10, "Error", $tc);
			imagestring($watermark_id, 1, 5, 20, "Loading", $tc);
			imagestring($watermark_id, 1, 5, 30, "Watermark", $tc);
		}
		
		$destination = watermark_position($original_info[0], $original_info[1], $watermark_info[0], $watermark_info[1], $image_position);
		
		if ($watermark_is_transparent) {
			imagealphablending($watermark_id, 1);
			imagecopy($original_id, $watermark_id, $destination[0], $destination[1], 0, 0, $watermark_info[0], $watermark_info[1]);
		} else {
			imagecopymerge($original_id, $watermark_id, $destination[0], $destination[1], 0, 0, $watermark_info[0], $watermark_info[1], $image_pct);
		}		
		imagedestroy($watermark_id);
	}

	$text_length = strlen($watermark_text);
	if ($text_length > 0) // create text watermark 
	{
		$text_size = (float)$text_size;
		if (!$text_size) {
			$text_size = 20;
		}
		$text_angle = (float)$text_angle;
		if ($text_angle > 360) {
			$text_angle = $text_angle % 360;
		} elseif ($text_angle < 0) {
			$text_angle = ($text_angle % 360) + 360;
		}
		// calculate text size and position
		$pos_x = 0; $pos_y = 0;
		if (function_exists("imagettftext")) {
			$box = imagettfbbox($text_size, $text_angle, $font_file, $watermark_text);
			if ($text_angle >= 0 && $text_angle <= 90) {
				$text_width = abs($box[6] - $box[2]);
				$text_height = abs($box[5] - $box[1]);
				$pos_x = abs($box[6] - $box[0]);
				$pos_y = $text_height;
			} elseif ($text_angle > 90 && $text_angle <= 180) {
				$text_width = abs($box[0] - $box[4]);
				$text_height = abs($box[7] - $box[3]);
				$pos_x = $text_width;
				$pos_y = abs($box[3] - $box[1]);
			} elseif ($text_angle > 180 && $text_angle <= 270) {
				$text_width = abs($box[6] - $box[2]);
				$text_height = abs($box[5] - $box[1]);
				$pos_x = abs($box[0] - $box[2]);
				$pos_y = 0;
			} elseif ($text_angle > 270 && $text_angle <= 360) {
				$text_width = abs($box[4] - $box[0]);
				$text_height = abs($box[3] - $box[7]);
				$pos_x = 0;
				$pos_y = abs($box[1] - $box[7]);
			}
		} else {
			$text_width = imagefontwidth($font_type) * $text_length;
			$text_height = imagefontheight($font_type);
		}

		$rgb = get_rgb_color($text_color);
		if (!is_array($rgb)) {
			$rgb = array(255, 255, 255); // use white by default
		}

		$watermark_id = @imagecreate ($text_width, $text_height);
		$watermark_info = array($text_width, $text_height);
		$background_color = imagecolorallocate ($watermark_id, $rgb[0], $rgb[1], $rgb[2]); // better use the same as text_color
		imagecolortransparent($watermark_id, $background_color);
		$text_color = imagecolorallocate ($watermark_id, $rgb[0], $rgb[1], $rgb[2]); // same color as transparent

		if (function_exists("imagettftext")) {
			imagettftext($watermark_id, $text_size, $text_angle, $pos_x, $pos_y, $text_color, $font_file, $watermark_text);
		} else {
			imagestring($watermark_id, $font_type, $pos_x, $pos_y, $watermark_text, $text_color);
		}

		$destination = watermark_position($original_info[0], $original_info[1], $watermark_info[0], $watermark_info[1], $text_position);
		imagecopymerge($original_id, $watermark_id, $destination[0], $destination[1], 0, 0, $watermark_info[0], $watermark_info[1], $text_pct);
		imagedestroy($watermark_id);
	}

	if ($output_image) {
		if ($original_type == 1) {
			imagegif($original_id, $output_image);
		} elseif ($original_type == 2) {
			imagejpeg($original_id, $output_image, $jpeg_quality);
		} elseif ($original_type == 3) {
			imagepng($original_id, $output_image);
		}
	} else {
		if ($original_type == 1) {
			header("Content-Type: image/gif");
			imagegif($original_id);
		} elseif ($original_type == 2) {
			header("Content-Type: image/jpeg");
			imagejpeg($original_id, "", $jpeg_quality);
		} elseif ($original_type == 3) {
			header("Content-Type: image/png");
			imagepng($original_id);
		}
	}
	imagedestroy($original_id);
}

function watermark_position($original_width, $original_height, $watermark_width, $watermark_height, $position)
{
	global $settings;

	$dest_x = 0; $dest_y = 0;
	$position = strtoupper($position);
	$jpeg_quality = get_setting_value($settings, "jpeg_quality", 75);

	if ($position == "TL") { // Top Left
		$dest_x = 0; $dest_y = 0;
	} elseif ($position == "TC") { // Top Center
		$dest_x = intval(($original_width - $watermark_width) / 2);
		$dest_y = 0;
	} elseif ($position == "TR") { // Top Right
		$dest_x = $original_width - $watermark_width;
		$dest_y = 0;
	} elseif ($position == "ML") { // Middle left
		$dest_x = 0;
		$dest_y = intval(($original_height - $watermark_height) / 2);
	} elseif ($position == "C") { // Center
		$dest_x = intval(($original_width - $watermark_width) / 2);
		$dest_y = intval(($original_height - $watermark_height) / 2);
	} elseif ($position == "MR") { // Middle Right
		$dest_x = $original_width - $watermark_width;
		$dest_y = intval(($original_height - $watermark_height) / 2);
	} elseif ($position == "BL") { // Bottom left
		$dest_x = 0;
		$dest_y = $original_height - $watermark_height;
	} elseif ($position == "BC") { // Bottom Center
		$dest_x = intval(($original_width - $watermark_width) / 2);
		$dest_y = $original_height - $watermark_height;
	} elseif ($position == "BR") { // Bottom Right
		$dest_x = $original_width - $watermark_width;
		$dest_y = $original_height - $watermark_height;
	} elseif ($position == "RND") { // Random Position
		$dest_x = rand(0, $original_width - $watermark_width);
		$dest_y = rand(0, $original_height - $watermark_height);
	} else {
		// be default show it by center
		$dest_x = intval(($original_width - $watermark_width) / 2);
		$dest_y = intval(($original_height - $watermark_height) / 2);
	}

	return array($dest_x, $dest_y);
}

function get_rgb_color($text_color) 
{
	$rgb = "";
	$text_color = trim($text_color);
	if ($text_color == "red") {
		$rgb = array(255, 0, 0);
	} elseif ($text_color == "green") {
		$rgb = array(0, 128, 0);
	} elseif ($text_color == "blue") {
		$rgb = array(0, 0, 255);
	} elseif ($text_color == "black") {
		$rgb = array(0, 0, 0);
	} elseif ($text_color == "white") {
		$rgb = array(255, 255, 255);
	} elseif ($text_color == "yellow") {
		$rgb = array(255, 255, 0);
	} elseif ($text_color == "silver") {
		$rgb = array(192, 192, 192);
	} elseif ($text_color == "gray") {
		$rgb = array(128, 128, 128);
	} elseif ($text_color == "fuchsia") {
		$rgb = array(255, 0, 255);
	} elseif ($text_color == "maroon") {
		$rgb = array(128, 0, 0);
	} elseif ($text_color == "lime") {
		$rgb = array(0, 255, 0);
	} elseif ($text_color == "olive") {
		$rgb = array(128, 128, 0);
	} elseif ($text_color == "purple") {
		$rgb = array(128, 0, 128);
	} elseif ($text_color == "aqua") {
		$rgb = array(0, 255, 255);
	} elseif ($text_color == "teal") {
		$rgb = array(0, 128, 128);
	} elseif ($text_color == "navy") {
		$rgb = array(0, 0, 128);
	} elseif (preg_match("/^\#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i", $text_color, $matches)) {
		$rgb = array(hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
	} elseif (preg_match("/^\#?([0-9a-f])([0-9a-f])([0-9a-f])$/i", $text_color, $matches)) {
		$rgb = array(hexdec($matches[1].$matches[1]), hexdec($matches[2].$matches[2]), hexdec($matches[3].$matches[3]));
	}

	return $rgb;
}

// generate new filename adding a number e.g. 'example_2.gif'
function get_new_file_name($filepaths, $filename)
{
	if (!is_array($filepaths)) {
		$filepaths = array($filepaths);
	}
	$current_index = 0;
	$filename = str_replace(" ", "_", $filename);
	$new_filename = $filename;
	do {
		$is_file_exists = false;
		for ($i = 0; $i < sizeof($filepaths); $i++) {
			$is_file_exists = ($is_file_exists || file_exists($filepaths[$i] . $new_filename));
		}
		if ($is_file_exists) {
			$current_index++;
			$delimiter_pos = strpos($filename, ".");
			if ($delimiter_pos) {
				$new_filename = substr($filename, 0, $delimiter_pos) . "_" . $current_index . substr($filename, $delimiter_pos);
			} else {
				$new_filename = $filename . "_" . $current_index;
			}
		}
	} while ($is_file_exists);
	
	return $new_filename;
}

function resize($filename, $origin, $dest, $width, $height, &$errors)
{
	global $settings;

	$jpeg_quality = get_setting_value($settings, "jpeg_quality", 75);

	if (empty($filename) || empty($origin) || empty($dest) || empty($width) || empty($height)) {
		$errors .= "Missing resizing parameter.<br>\n";
		return false;
		exit;
	}
	if (!image_supported($origin . $filename, $errors)) {
		return false;
		exit;
	}
	if (!is_dir($dest))	{
		$errors .= "The folder " . $dest . " doesn't exist.<br>\n";
		return false;
		exit;
	} elseif (!is_writable($dest)){
		$errors .= str_replace("{folder_name}", $dest, FOLDER_PERMISSION_MESSAGE) . "<br>\n";
		return false;
		exit;
	}

	list($width_orig, $height_orig, $format) = @getimagesize($origin . $filename);

	// Get new dimensions
	if ($width_orig < $height_orig) {
		if ($height < $height_orig) {
			$width = ($height / $height_orig) * $width_orig;
		} else {
			$width = $width_orig;
			$height = $height_orig;
		}
	}
	else {
		if ($width < $width_orig) {
			$height = ($width / $width_orig) * $height_orig;
		} else {
			$width = $width_orig;
			$height = $height_orig;
		}
	}

	$width = round($width);
	$height = round($height);

	if ($format == IMAGETYPE_GIF) {
		$image = imagecreatefromgif($origin.$filename);
	} elseif ($format == IMAGETYPE_JPEG) {
		$image = imagecreatefromjpeg($origin.$filename);
	} elseif ($format == IMAGETYPE_PNG) {
		$image = imagecreatefrompng($origin.$filename);
	}
        
	if ($format == IMAGETYPE_GIF) {
		// always use imagecreate function for gif images 
		$image_resized = imagecreate($width, $height);
	} else {
		$image_resized = imagecreatetruecolor($width, $height);
	}
                
	// check transparancy for gif and png images 
	if ($format == IMAGETYPE_GIF || $format == IMAGETYPE_PNG) {
		$transparent_index = imagecolortransparent($image);

		// if we have a specific transparent color
		if ($transparent_index >= 0) {
			// get the original image's transparent color's RGB values
			$transparent_color = imagecolorsforindex($image, $transparent_index);
			// allocate the same color in the new image resource
			$transparent_index = imagecolorallocate($image_resized, $transparent_color["red"], $transparent_color["green"], $transparent_color["blue"]);
			// completely fill the background of the new image with allocated color.
			imagefill($image_resized, 0, 0, $transparent_index);
			// set the background color for new image to transparent
			imagecolortransparent($image_resized, $transparent_index);
		}  elseif ($format == IMAGETYPE_PNG) { // Always make a transparent background color for PNGs that don't have one allocated already
			// turn off transparency blending (temporarily)
			imagealphablending($image_resized, false);
			// create a new transparent color for image
			$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
			// completely fill the background of the new image with allocated color.
			imagefill($image_resized, 0, 0, $color);
			// restore transparency blending
			imagesavealpha($image_resized, true);
		}
	}

	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	imagedestroy($image);

	if ($format == 1) {
		imagegif($image_resized, $dest.$filename);
	} elseif ($format == 2) {
		imagejpeg($image_resized, $dest.$filename, $jpeg_quality);
	} elseif ($format == 3) {
		imagepng($image_resized, $dest.$filename);
	}
	imagedestroy($image_resized);
	return true;
}

function image_resize($file, $destination, $width, $height, $resize = 1, $proportional = true, $output = "file", $delete_original = true)
{
	global $image_errors, $settings;

	$jpeg_quality = get_setting_value($settings, "jpeg_quality", 75);
	$images_errors = ""; 

	if (empty($file) || empty($width) || empty($height)) {
		$images_errors .= "Missing resizing parameter.<br>\n";
		return false;
	}
	if (!image_supported($file, $images_errors)) {
		return false;
	}

	$image = "";
	$info = getimagesize($file);
	list($width_old, $height_old, $image_type) = $info;

	// resize: 1 - reduce only, 2 - enlarge only, 3 - reduce or enlarge
	if ($resize == 1) { // reduce only
		if ($width_old < $width && $height_old < $height) {
			$width = $width_old; $height = $height_old;
		}
	} elseif ($resize == 2) { // enlarge only
		if ($width_old > $width && $height_old > $height) {
			$width = $width_old; $height = $height_old;
		}
	}

	// preserver original aspect ratio
	$final_width = 0; $final_height = 0;
	if ($proportional) {
		if ($width == 0) {
			$aspect_ratio = $height/$height_old;
		} elseif ($height == 0) {
			$aspect_ratio = $width/$width_old;
		} else {
			$aspect_ratio = min ($width / $width_old, $height / $height_old);   
		}
		$final_width = round ($width_old * $aspect_ratio);
		$final_height = round ($height_old * $aspect_ratio);
	} else {
		$final_width = ($width <= 0) ? $width_old : $width;
		$final_height = ($height <= 0) ? $height_old : $height;
	}

	if ($image_type == IMAGETYPE_GIF) {
		$image = imagecreatefromgif($file);
	} elseif ($image_type == IMAGETYPE_JPEG) {
		$image = imagecreatefromjpeg($file);
	} elseif ($image_type == IMAGETYPE_PNG) {
		$image = imagecreatefrompng($file);
	}
        
	if ($image_type == IMAGETYPE_GIF) {
		// always use imagecreate function for gif images 
		$image_resized = imagecreate($final_width, $final_height);
	} else {
		$image_resized = imagecreatetruecolor($final_width, $final_height);
	}
                
	// check transparancy for gif and png images 
	if ($image_type == IMAGETYPE_GIF || $image_type == IMAGETYPE_PNG) {
		$transparent_index = imagecolortransparent($image);

		// if we have a specific transparent color
		if ($transparent_index >= 0) {
			// get the original image's transparent color's RGB values
			$transparent_color = imagecolorsforindex($image, $transparent_index);
			// allocate the same color in the new image resource
			$transparent_index = imagecolorallocate($image_resized, $transparent_color["red"], $transparent_color["green"], $transparent_color["blue"]);
			// completely fill the background of the new image with allocated color.
			imagefill($image_resized, 0, 0, $transparent_index);
			// set the background color for new image to transparent
			imagecolortransparent($image_resized, $transparent_index);
		}  elseif ($image_type == IMAGETYPE_PNG) { // Always make a transparent background color for PNGs that don't have one allocated already
			// turn off transparency blending (temporarily)
			imagealphablending($image_resized, false);
			// create a new transparent color for image
			$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
			// completely fill the background of the new image with allocated color.
			imagefill($image_resized, 0, 0, $color);
			// restore transparency blending
			imagesavealpha($image_resized, true);
		}
	}

	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
	imagedestroy($image);
    
	if ($delete_original) {
		@unlink($file);
	}

	if ($output == "file" && $destination) {
		if ($image_type == 1) {
			imagegif($image_resized, $destination);
		} elseif ($image_type == 2) {
			imagejpeg($image_resized, $destination, $jpeg_quality);
		} elseif ($image_type == 3) {
			imagepng($image_resized, $destination);
		}
		imagedestroy($image_resized);
		return true;
	} elseif ($output == "return" || $output == "image") {
		return $image_resized;
	} else {
		if ($image_type== 1) {
			header("Content-Type: image/gif");
			imagegif($image_resized);
		} elseif ($image_type== 2) {
			header("Content-Type: image/jpeg");
			imagejpeg($image_resized, "", $jpeg_quality);
		} elseif ($image_type == 3) {
			header("Content-Type: image/png");
			imagepng($image_resized);
		}
		imagedestroy($image_resized);
		return true;
	}
}

function image_errors()
{
	global $image_errors;
	return $image_errors;
}

?>