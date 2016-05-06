<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  show_image.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("includes/common.php");

	$width_preview = get_param("width");
	$filepath = get_param("filepath");
	$filepath = str_replace('../','',$filepath); 
	$path_parts = pathinfo($filepath);
	$ext = strtolower($path_parts['extension']);
	
	if ($ext != 'gif' && $ext != 'jpg' && $ext != "jpeg" && $ext != 'png') {
		header("Content-type: image/png");
		$image_no = imagecreatetruecolor(120, 20);
		$bg = imagecolorallocate($image_no, 255, 255, 255);
		imagefilledrectangle($image_no, 0, 0, 120, 20, $bg);
		$color = imagecolorallocate($image_no, 255, 0, 0);
		imagestring($image_no, 10, 0, 0, "Not supported", $color);
		imagepng($image_no);
		imagedestrot($image_no);
		exit;
	}
		  	
	$size = @getimagesize($filepath);
	if (sizeof($size) > 2) {
		@list($width, $height) = $size;
		if ($width > $width_preview)
		{
			if ($height > $width_preview) {
				$ratio1 = $width_preview/$height;
				$ratio2 = $width_preview/$width;
				if ($ratio1 > $ratio2){
					$ratio = $ratio2;
				} else {
					$ratio = $ratio1;
				}
			} else {
				$ratio = $width_preview/$width;
			}
		} else {
			if ($height > $width_preview) {
				$ratio = $width_preview/$height;
			} else {
				$ratio = 1;
			}
		}
	
		$new_width = $width * $ratio;
		$new_height = $height * $ratio;
		
		if ($ext == 'gif') {
			header('Content-type: image/png');			
			if ($width != $new_width) {
				$gif = imageCreateFromGIF($filepath);
				$image = imageCreateTrueColor($new_width, $new_height);
				imageAlphaBlending($image, false);
				imageCopyResampled($image, $gif, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imageSaveAlpha($image, true);
				imagepng($image,null,100);
				imageDestroy($image);
			} else {
				$image = imagecreatefromgif($filepath);
				imagepng($image);
			}
			
		} elseif ($ext == 'jpg' || $ext == 'jpeg') {
			header('Content-type: image/jpeg');
			if ($width != $new_width){
			$image_p = imagecreatetruecolor($new_width, $new_height);
			$image = imagecreatefromjpeg($filepath);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagejpeg($image_p, null, 100);
			} else {
			  $image = imagecreatefromjpeg($filepath);
			  imagejpeg($image, null, 100);			  
			}
		} elseif ($ext == 'png') {
			header('Content-type: image/png');

			$image_p = imagecreate($new_width, $new_height);
			$image = imagecreatefrompng($filepath);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagepng($image_p);
		} else {
			//Never
		}
	}

?> 