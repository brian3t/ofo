<?
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	global $item_code, $item_id, $output;
	
	$getCode = mysql_query(sprintf("SELECT item_code, manufacturer_id FROM oilfiltersonline_test_store.va_items WHERE item_id = '%s' limit 1", $item_id));
	$rs=mysql_fetch_assoc($getCode);
	$manufacturer_id = $rs['manufacturer_id'];
	$item_code = $rs["item_code"];
	
	$auto_db = 'automotive_parts';
	$heavy_db = 'heavy_duty_parts';
	$motorcycle_db = 'motorcycle_parts';
	

	
	function getApplications($db,$item,$description) {
		
		global $output;
		$partsRs = mysql_query(sprintf("SELECT make, model, year, engine FROM oilfiltersonline.%s WHERE part = '%s' order by make",$db,$item));
		$num_rows = mysql_num_rows($partsRs);
		if ($num_rows > 0){
			$output .= '<div class="subTitleBar">'.$description.'</div>';
			$output .= '<div><table class="applications">';
			$output .= '<tr><td><b><u>Make</u></b></td><td><b><u>Model</u></b></td><td><b><u>Year</u></b></td><td><b><u>Engine</u></b></td><td></tr>';
			while ($rs=mysql_fetch_assoc($partsRs)) {
				$output .='<tr><td>'.$rs["make"].'</td><td>'.$rs["model"].'</td><td>'.$rs["year"].'</td><td>'.$rs["engine"].'</td><td></tr>';
				}
			$output .= '</table></div>';
		}
		return $output;
	}
	
	function getAAIAApplications($item) {
		
		global $output;
		
		$partsRs = mysql_query(sprintf("SELECT make, model, year, concat_ws(' ', liters, cylinders) as engine FROM oilfiltersonline.aaia_parts left join oilfiltersonline.aaia on (aaia_parts.aaia = aaia.id) WHERE aaia_parts.part = '%s' order by make, model, year",$item));

		$num_rows = mysql_num_rows($partsRs);
		if ($num_rows > 0){
			$output .= '<div><table class="applications">';
			$output .= '<tr><td><b><u>Make</u></b></td><td><b><u>Model</u></b></td><td><b><u>Year</u></b></td><td><b><u>Engine</u></b></td><td></tr>';
			while ($rs=mysql_fetch_assoc($partsRs)) {
				$output .='<tr><td>'.$rs["make"].'</td><td>'.$rs["model"].'</td><td>'.$rs["year"].'</td><td>'.$rs["engine"].'</td><td></tr>';
				}
			$output .= '</table></div>';
		}
		return $output;
	}
	
	if($manufacturer_id == 3) {
		getApplications($auto_db,$item_code,'Automotive Applications');
		getApplications($heavy_db,$item_code,'Heavy Duty Applications');
		getApplications($motorcycle_db,$item_code,'Motorcycle Applications');
	} else {
		getAAIAApplications($item_code);
	}
	if($output != '') {
		echo '<div id="willfit_data">';
		echo '<div class="titleBar">Includes all automotive, heavy duty, and motorcycle applications</div>';
		echo '<div id="applications">';
		echo $output;
		echo '</div></div>';
	} else {
		echo '<div id="willfit_data"></div>';
	}
	
?>