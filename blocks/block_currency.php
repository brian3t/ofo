<?php

function currency_form($block_name, $currency_selection = "")
{
	global $t, $db, $table_prefix, $page_settings;

	if ($block_name) {
		$t->set_file("block_body", "block_currency.html");
		$t->set_var("currencies_images", "");
	}

	if (!$currency_selection) {
		$currency_selection = get_setting_value($page_settings, "currency_selection", 2);
	}

	$currency = get_currency();
	$currency_code = $currency["code"];

	$query_string = transfer_params("", true);

	$sql = " SELECT currency_code, currency_title, currency_image, currency_image_active FROM " . $table_prefix . "currencies ";
	$sql .= " WHERE show_for_user=1 ";
	$db->query($sql);
	while ($db->next_record()) 
	{
		$row_currency_code = $db->f("currency_code");
		$row_currency_title = $db->f("currency_title");
		$currency_image = $db->f("currency_image");
		$currency_image_active = $db->f("currency_image_active");
		$currency_selected = ($currency_code == $row_currency_code) ? "selected" : "";
		$t->set_var("currency_selected", $currency_selected);
		$t->set_var("currency_code", $row_currency_code);
		$t->set_var("currency_title", $row_currency_title);
		if ($currency_selection != 2 && $currency_image) {
			// If current row currency is a selected by user, make it "highlighted" use active image
			// If it's not empty
			if ($currency_code == $row_currency_code && $currency_image_active != "") {
				$currency_image = $currency_image_active;
			}
			$image_size = preg_match("/^http\:\/\//", $currency_image) ? "" : @getimagesize($currency_image);
			$t->set_var("src", htmlspecialchars($currency_image));
			if (is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}

			$currency_query = $query_string;
			if ($currency_query) {
				$currency_query .= "&";
			} else {
				$currency_query .= "?";
			}
 			$currency_query .= "currency_code=" . $row_currency_code; 
			$t->set_var("currency_query", $currency_query);

			$t->parse("currencies_images", true);
		} elseif ($currency_selection == 2) {
			$t->parse("currencies", true);
		}
	}

	if ($currency_selection == 2) {
		$t->set_var("currencies_images", "");
		$t->sparse("select_currencies", false);
	}

	if ($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>