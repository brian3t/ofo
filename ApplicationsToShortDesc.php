<?
	global $item_code, $item_id, $output;
	$auto_db = 'automotive_parts';
	$heavy_db = 'heavy_duty_parts';
	$motorcycle_db = 'motorcycle_parts';
	
	$getItems = mysql_query("SELECT item_code FROM oilfiltersonline_test_store.va_items");
	$rs=mysql_fetch_assoc($getItems);
	$item_code = $rs["item_code"];
	
	function getApplications($db,$item) {
		
		global $output;
		$partsRs = mysql_query(sprintf("SELECT make, model, year, engine FROM oilfiltersonline.%s WHERE part = '%s' order by make",$db,$item));
		$num_rows = mysql_num_rows($partsRs);
		if ($num_rows > 0){
			//$output .= '<div class="subTitleBar">'.$description.'</div>';
			//$output .= '<div><table class="applications">';
			//$output .= '<tr><td><b><u>Make</u></b></td><td><b><u>Model</u></b></td><td><b><u>Year</u></b></td><td><b><u>Engine</u></b></td><td></tr>';
			while ($rs=mysql_fetch_assoc($partsRs)) {
				$output .= $rs["make"].' '.$rs["model"].' '.$rs["year"].' '.$rs["engine"].' \n';
				}
			//$output .= '</table></div>';
		}
		return $output;
	}
	foreach($item_code as $item)
		getApplications($auto_db,$item);
		getApplications($heavy_db,$item);
		getApplications($motorcycle_db,$item);
		if($output != '') {
			echo $output;
		}
	}
	
?>