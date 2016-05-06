<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  pdflib.php                                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


class VA_PDFLib {

  var $pdf = "";     
	var $core_fonts = array('courier'=>'Courier','courierb'=>'Courier-Bold','courieri'=>'Courier-Oblique','courierbi'=>'Courier-BoldOblique',
		'helvetica'=>'Helvetica','helveticab'=>'Helvetica-Bold','helveticai'=>'Helvetica-Oblique','helveticabi'=>'Helvetica-BoldOblique',
		'times'=>'Times-Roman','timesb'=>'Times-Bold','timesi'=>'Times-Italic','timesbi'=>'Times-BoldItalic',
		'arial'=>'Arial', 'arialb'=>'Arial-Bold', 'arialbi'=>'Arial-BoldItalic',
		'symbol'=>'Symbol','zapfdingbats'=>'ZapfDingbats');

  function VA_PDFLib() {
		$this->pdf = pdf_new();
		pdf_open_file($this->pdf, "");
  }
	var $font;          // current font 
	var $font_encoding; // current encoding
	var $font_size;     // current font size 

	function set_title($title) {
		pdf_set_info($this->pdf, "Title", $title);
	}

	function set_subject($subject) {
		pdf_set_info($this->pdf, "Subject", $subject);
	}

	function set_author($author) {	
		pdf_set_info($this->pdf, "Author", $author);
	}

	function set_creator($creator) {	
		pdf_set_info($this->pdf, "Creator", $creator);
	}

	function set_producer($producer) {	
		pdf_set_info($this->pdf, "Producer", $producer);
	}

	function set_keywords($keywords) {	
		pdf_set_info($this->pdf, "Keywords", $keywords);
	}

	function set_creation_date($creation_date) {	
		pdf_set_info($this->pdf, "CreationDate", "D:".date("YmdHis",$creation_date));
	}

	function add_font($font_family, $style = "", $file = "")
	{
		$style = strtolower($style);
		$style = str_replace("u", "", $style);
		if ($style == "ib") { $style = "bi"; }
		if (!$file) { $file = $font_family.$style.".ttf"; }
		if (isset($this->core_fonts[$font_family.$style])) {
			$font_family = $this->core_fonts[$font_family.$style];
		}

		pdf_set_parameter($this->pdf, "FontOutline", $font_family."=" . $this->get_font_path() . $file);
	}

	function get_font_path()
	{
		if (defined("VA_PDF_FONTPATH")) {
			$font_path = constant("VA_PDF_FONTPATH");
		} else if (is_dir(dirname(__FILE__)."/font")) {
			$font_path = dirname(__FILE__)."/font/";
		} else {
			$font_path = "";
		}
		return $font_path;
	}

	function setfont($font_family, $style, $font_size)
	{
		$style = strtolower($style);
		if (strpos($style, "u") !== false) {
			pdf_set_parameter ($this->pdf, "underline", "true");
			$style=str_replace("u", "", $style);
		} else {
			pdf_set_parameter ($this->pdf, "underline", "false");
		}
		if ($style == "ib") {
			$style = "bi";
		}
		if (isset($this->core_fonts[$font_family.$style])) {
			$font_family = $this->core_fonts[$font_family.$style];
		}
		$font_encoding = $this->get_font_encoding();
		$font = pdf_findfont($this->pdf, $font_family, $font_encoding, 0);
		pdf_setfont($this->pdf, $font, $font_size);
		$this->font = $font;
		$this->font_size = $font_size;

		return $font;
	}

	function set_font_encoding($encoding) 
	{
		if ($encoding) {
			$encoding = preg_replace("/^windows\-/", "cp", $encoding);
		} else {
			$encoding = "host";
		}

		$this->font_encoding = $encoding;
	}

	function get_font_encoding() 
	{
		return $this->font_encoding;
	}

	function stringwidth($text, $font_family = "", $font_style = "", $font_size = 0)
	{
		if ($font_size < 1) {
			$font_size = $this->font_size;
		}
		if (!strlen($font_family)) {
			$font = $this->font;
		} else {
			$font = $this->setfont($font_family, $font_style, $font_size);
		}
		$length = pdf_stringwidth($this->pdf, $text, $font, $font_size); 

		return $length;
	}

	function prepare_text($text, $width)
	{
		$lines = array();
		$fontsize = pdf_get_value($this->pdf, "fontsize", 0); 
		$parts = explode("\n", $text);
		for ($p = 0; $p < sizeof($parts); $p++) {
			$part = trim($parts[$p]);
			$full_words = explode(" ", $part);
			// split long words onto smaller parts
			$words = array();
			for ($w = 0; $w < sizeof($full_words); $w++) {
				$full_word = $full_words[$w];
				while (strlen($full_word) > 0) {
					$word_length = strlen($full_word);
					$line_width = $this->stringwidth($full_word);
					while ($line_width > $width && $word_length > 1) {
						$word_length -= 1;
						$line_width = $this->stringwidth(substr($full_word, 0, $word_length));
					}
					$words[] = substr($full_word, 0, $word_length);
					$full_word = substr($full_word, $word_length);
				}
			}
			$line = "";
			$last_index = sizeof($words) - 1;
			for ($w = 0; $w <= $last_index; $w++) {
				$line .= $words[$w] . " ";
				$next_word = ($w == $last_index) ? "" : $words[$w + 1];
				$line_width = pdf_stringwidth($this->pdf, trim($line.$next_word), $this->font, $this->font_size);
				if ($w == $last_index || $line_width > $width) {
					$lines[] = trim($line);
					$line = "";
				}
			}
		}     
		return $lines;
	}

	function show_xy($text, $right, $top, $width = 0, $height = 0, $horizontal_mode = "left", $vertical_mode = "middle", $direction = "down")
	{
		$text_height = 0;
		$fontsize = pdf_get_value($this->pdf, "fontsize", 0); 
		if ($width > 0) {
			$lines = $this->prepare_text($text, $width);
			$text_height = sizeof($lines) * $fontsize;
			$top_indent = 0;
			if ($height > 0 && $height > $text_height) {
				if ($vertical_mode == "middle") {
					$top_indent = round(($height - $text_height) / 2);
				} else if ($vertical_mode == "bottom") {
					$top_indent = $height - $text_height;
				}
			}
			for ($l = 0; $l < sizeof($lines); $l++) {
				$line = $lines[$l];
				$top_indent += $fontsize;
				$line_width = pdf_stringwidth($this->pdf, trim($line), $this->font, $this->font_size);
				if ($horizontal_mode == "center") {
					$indent = round(($width - $line_width) / 2);
					pdf_show_xy ($this->pdf, trim($line), $right + $indent, $top - $top_indent);
				} else if ($horizontal_mode == "right") {
					$indent = $width - $line_width;
					pdf_show_xy ($this->pdf, trim($line), $right + $indent, $top - $top_indent);
				} else {
					pdf_show_xy ($this->pdf, trim($line), $right, $top - $top_indent);
				}
			}
		} else {
			$text_height = $fontsize;
			pdf_show_xy ($this->pdf, trim($text), $right, $top - $height);
		}
		return $text_height;
	}

	function setlinewidth($width)
	{
		pdf_setlinewidth($this->pdf, $width);
	}

	function rect($right, $top, $width, $height, $style = "")
	{
		pdf_rect ($this->pdf, $right, $top, $width, $height);
		pdf_stroke($this->pdf);
	}

	function line($x1, $y1, $x2, $y2)
	{
		pdf_moveto($this->pdf, $x1, $y1);
		pdf_lineto($this->pdf, $x2, $y2);
		pdf_stroke($this->pdf);
	}

	function get_buffer()
	{
		pdf_close($this->pdf);
		$buffer = pdf_get_buffer($this->pdf);
		pdf_delete($this->pdf);
		
		return $buffer;
	}

	function begin_page($width, $height)
	{
		pdf_begin_page($this->pdf, 595, 842);
	}

	function end_page()
	{
		pdf_end_page($this->pdf);
	}

	function place_image($image_file, $right, $top, $image_type = "")
	{
		$img = pdf_open_image_file($this->pdf, $image_type, $image_file, "", 0);
		if ($img) {
			pdf_place_image($this->pdf, $img, $right, $top, 1);
			pdf_close_image($this->pdf, $img);
		}
	}


}

?>