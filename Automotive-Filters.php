<?
require('js/xajax_core/xajax.inc.php');
$xajax = new xajax();
// $xajax->configure('debug',true);
global $current_db, $current_view;  

class myXajaxResponse extends xajaxResponse {
 
  function addCreateOptions($sSelectId, $options) {
    $this->script("document.getElementById('".$sSelectId."').length=0");
    if (sizeof($options) >0) {
       foreach ($options as $option) {
         $this->script("addOption('".$sSelectId."','".$option['txt']."','".$option['val']."');");
       }
     }
  }
}

if ($_GET["app"] == "motorcycle") {
	$current_db = "motorcycle";
	$current_view = "motorcycle_view";
} else if($_GET["app"] == "heavyduty") {
	$current_db = "heavy_duty";
	$current_view = "heavy_duty_view";
} else if($_GET["app"] == "cross") {
} else {
	$current_db = "aaia";
	$current_view = "aaia_view";
}

$makesRs = mysql_query("SELECT DISTINCT(make) from oilfiltersonline.".$current_db." order by make asc") or die(mysql_error());
$make_options = '<option value="">--Select Make--</option>';
while ($rs=mysql_fetch_assoc($makesRs)) {
	$make_options .= '<option value="'.$rs["make"].'">'.$rs["make"].'</option>';
}
$form_code =   '<h2>Shop By Vehicle</h2>
				<form name="form1" method="POST" action="sessionvars.php">
				<input id="count" name="count" type="hidden" value="'.$_SESSION["count"].'">
				<select class="dropdown" name="app" id="app" onChange="window.location=\'/?app=\' + this.form.app.value">
					<option value="aaia">Cars &amp; Trucks</option>';
if($_GET["app"] == "motorcycle") {
	$form_code .= '	<option value="motorcycle" selected>Motorcycles</option>';
} else {
	$form_code .= '	<option value="motorcycle">Motorcycles</option>';
}
if($_GET["app"] == "heavyduty") {
	$form_code .= '	<option value="heavyduty" selected>Heavy Duty</option>';
} else {
	$form_code .= '	<option value="heavyduty">Heavy Duty</option>';
}
$form_code .= '	</select>
					<select class="dropdown" name="make" id="make" onChange="xajax_addmodels(\'models\', this.form.make.value)">'.		$make_options.'
				</select>
				<select class="dropdown" name="models" disabled="disabled" id="models" onChange="xajax_addyears(\'years\', this.form.models.value, this.form.make.value)">
					<option value="" selected>--Select Model--</option>
				</select>';
if($_GET["app"] == "motorcycle"  || $_GET["app"] == "heavyduty") {
	$form_code .= '<select class="dropdown" name="years" disabled="disabled" id="years" onChange="xajax_addengines_old(\'engines\', this.form.models.value, this.form.make.value, this.form.years.value)">
						<option value="" selected>--Select Year--</option>
					</select>';
} else {
	$form_code .=  '<select class="dropdown" name="years" disabled="disabled" id="years" onChange="xajax_addengines(\'engines\', this.form.models.value, this.form.make.value, this.form.years.value)">
						<option value="" selected>--Select Year--</option>
					</select>';
}
$form_code .= '	<select class="dropdown" name="engines" disabled="disabled" id="engines" onChange="xajax_showdiv(\'submitdiv\', this.form.models.value, this.form.make.value, this.form.years.value, this.form.engines.value, $(\'#engines :selected\').text())">
						<option value="" selected>--Select Engine--</option>
					</select>';
$form_code .= '</form>';

function addmodels($selectId, $make) {
	global $models, $current_db;
	$objResponse = new myXajaxResponse();
	$modelsRs = mysql_query(sprintf(
	"SELECT DISTINCT(model) FROM oilfiltersonline.".$current_db." WHERE make = '%s' order by model asc", $make));
	$models[]=array("txt"=>"--Select Model--", "val"=>"");
	while ($rs=mysql_fetch_assoc($modelsRs)) {
		$models[]=array("txt"=>$rs["model"], "val"=>$rs["model"]);
	}
	$objResponse->script("$('#models option:contains(--)').attr('selected', true)");
	$objResponse->script("$('#models').removeAttr('disabled');");
	$objResponse->script("$('#engines').attr('disabled', 'disabled');");
	$objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
	$objResponse->script("$('#years').attr('disabled', 'disabled');");
	$objResponse->script("$('#years option:contains(--)').attr('selected', true)");
	$objResponse->assign("submitdiv", "style.display", "none");
	$objResponse->addCreateOptions($selectId, $models);
	return $objResponse;
}

function addyears($selectId, $model, $make) {
	global $years, $current_db;
	$objResponse = new myXajaxResponse();
	$yearsRs = mysql_query(sprintf
	("SELECT DISTINCT(year) FROM oilfiltersonline.".$current_db." WHERE model = '%s' and make = '%s' order by year desc", $model, $make));
	$years[]=array("txt"=>"--Select Year--", "val"=>"");
	while ($rs=mysql_fetch_assoc($yearsRs)) {
		$years[]=array("txt"=>$rs["year"], "val"=>$rs["year"]);
	}
	$objResponse->script("$('#years option:contains(--)').attr('selected', true)");
	$objResponse->script("$('#years').removeAttr('disabled');");
	$objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
	$objResponse->script("$('#engines').attr('disabled', 'disabled');");
	$objResponse->assign("submitdiv", "style.display", "none");
	$objResponse->addCreateOptions($selectId, $years);
	return $objResponse;
}

function addengines($selectId, $model, $make, $year) {
	global $engines, $current_db;
	$objResponse = new myXajaxResponse();
	if($current_db == "aaia") {
		$enginesRs = mysql_query(sprintf
		("SELECT id, concat_ws('-',cylinders,concat(liters,'L'),engineVIN,injectionType,notes1,notes2,notes4,fuelType) as engine from oilfiltersonline.".$current_db." WHERE model = '%s' and make = '%s' and year = '%s' order by engine desc", $model, $make, $year));
	} else {
		$enginesRs = mysql_query(sprintf
	("SELECT DISTINCT(engine) FROM oilfiltersonline.".$current_db." WHERE model = '%s' and make = '%s' and year = '%s' order by engine desc", $model, $make, $year));
	}
	$engines[]=array("txt"=>"--Select Engine--", "val"=>"");
	while ($rs=mysql_fetch_assoc($enginesRs)) {
		$engines[]=array("txt"=>$rs["engine"], "val"=>$rs["id"]);
	}
	$objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
	$objResponse->script("$('#engines').removeAttr('disabled');");
	$objResponse->assign("submitdiv", "style.display", "none");
	$objResponse->addCreateOptions($selectId, $engines);
	return $objResponse;
}

function addengines_old($selectId, $model, $make, $year) {
	global $engines, $current_db;
	$objResponse = new myXajaxResponse();
	$enginesRs = mysql_query(sprintf
	("SELECT DISTINCT(engine) FROM oilfiltersonline.".$current_db." WHERE model = '%s' and make = '%s' and year = '%s' order by engine desc", $model, $make, $year));
	$engines[]=array("txt"=>"--Select Engine--", "val"=>"");
	while ($rs=mysql_fetch_assoc($enginesRs)) {
		$engines[]=array("txt"=>$rs["engine"], "val"=>$rs["engine"]);
		}
	$objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
	$objResponse->script("$('#engines').removeAttr('disabled');");
	$objResponse->assign("submitdiv", "style.display", "none");
	$objResponse->addCreateOptions($selectId, $engines);
	return $objResponse;
}

function showdiv($divid, $model, $make, $year, $id, $engine) {
	global $part, $current_view;
	$objResponse = new myXajaxResponse();
	if($current_view == "aaia_view") {
		$partRs = mysql_query(sprintf
		("SELECT * FROM oilfiltersonline.".$current_view." WHERE aaia = '%s' order by field(type,'Oil Filters','Air Filters','Cabin Air Filters','Brake Pads','Fuel Filters','Transmission Filters','Spark Plugs','Oxygen Sensors','PCV Valves and Breather'), price asc ", $id));
	} else {
		$partRs = mysql_query(sprintf
		("SELECT * FROM oilfiltersonline.".$current_view." WHERE model = '%s' and make = '%s' and year = '%s' and engine = '%s' ", $model, $make, $year, $engine));
	}
	while ($rs=mysql_fetch_assoc($partRs)) {
		$parts[] = $rs;
		$categories[] = $rs["type"];
	}
	$categories = array_unique($categories);
	$application_code = '<div class="finder-wrapper">
						 <div id="searchVehicle">
							Search Results For: 
							<span class="currentVehicle">'.$year.' '.$make.' '.$model.'</span>
							<a class="button" id="hide"><span>Hide Results</span></a>
						</div>
						
							 <div id="categoryWrapper">
								<div id="selectCategory">
									Select Your Category
								</div>
								<ul class="partCategories">';
	foreach ($categories as $cat) {
		$application_code .= '		<li onclick="goToByScroll(\\\''.str_replace(" ", "",$cat).'\\\');">'.$cat.'</li>';				
	}
	$application_code .= '		</ul>
							</div>';
	$count = 1;
	$previous_part_group = "";
	foreach ($parts as $rs) {
		$price = $rs["price"];
		$part = $rs["part"];
		$description = $rs["description"];
		$manufacturer = $rs["manufacturer"];
		$content = $rs["content"];
		$retail = $rs["retail_price"];
		if($rs["thumbnail"]) {
			$thumbnail = $rs["thumbnail"];
		} else {
			$thumbnail = 'images/no_image_tiny.gif';
		}
		if($rs["bigimage"]) {
			$bigimage = $rs["bigimage"];
		} else {
			$bigimage = 'images/no_image_tiny.gif';
		}
		$part_group = $rs["type"];
		if($part_group != $previous_part_group) {
			$application_code .= '<div class="finder_header_row"><a name="'.str_replace(" ", "",$part_group).'" id="'.str_replace(" ", "",$part_group).'">'.$part_group.'</a><a class="backToTop right" onclick="goToByScroll(\\\'#top\\\');"><span>Back To Top </span><img src="images/up.gif" /></a></div>';
		}

		if(is_null($rs["price"]) || $rs["Active"] == 0) {
			$application_code .= '<div class="finder-row">
									<div class="part-description">
										<h3>
											<img src="'.$thumbnail.'"  title="'.$bigimage.'" class="preview" />
											<span class="'.$manufacturer.'">'.$manufacturer.'</span> '.$description.' - '.$part.
										'</h3>
										<p>'.$content.'</p>
									</div>
									<div id="finder-contact"><a href="/articles.php?category_id=36">Contact Us For Availability</a></div>
									</div>';
		} else {
			$application_code .= '<div class="finder-row">
									<div class="part-description">
										<h3>
										<a class="parts-highlight" target="_parent" href="product_details.php?item_code='.$part.'">
											<img src="'.$thumbnail.'"  title="'.$bigimage.'" class="preview" />
										<span class="'.$manufacturer.'">'.$manufacturer.'</span> '.$description.' - '.$part.'</a>
										</h3>
										<p>'.$content.'</p>
									</div>
									<div class="part-price">
										<span class="priceNames">List Price: </span><span style="text-decoration: line-through;">$'.$retail.'</span><br />
										<span class="priceNames">Our Price:  </span><span class="ourPrice">$'.$price.'</span><br />
										<span class="priceNames">Savings: </span> $'.($retail - $price).'<br />
										<form name="form'.$count.'" method="POST" action="sessionvars.php" class="addToCart">
										<input type="hidden" value="'.$part.'" name="part" />
										<input type="hidden" value="'.str_replace(" ", "",$part_group).'" name="type" />
										Quantity: <input name="quantity" value="1" style="width: 25px"/><input id="addCart" type="image" src="/images/oil-filters/addCart.png" />
										</form>
									</div>
									</div>';
			$count++;
		}
		$previous_part_group = $rs["type"];
		$effects_script = '$(document).ready(function(){
								imagePreview();
								mouseHover();
							   $(".application,#hide").click(function() {
									if($(".finder-wrapper").hasClass("hide")) {
										$(".finder-wrapper").removeClass("hide");
										$(".application").effect("transfer",{ to: $(".finder-wrapper") }, 400);
									} else {
										$(".finder-wrapper").effect("transfer",{ to: $(".application") }, 400);
										$(".finder-wrapper").addClass("hide");
									}
								});
							});';
	}
	$application_code .= '</div>';
	$application_code = preg_replace('/\s\s+/', ' ', $application_code);
	// Store the vehicle and part results in case the user comes back to see their parts
	$_SESSION["application"] = $application_code;
	$_SESSION["year"] = $year;
	$_SESSION["make"] = $make;
	$_SESSION["model"] = $model;
	$_SESSION["engine"] = $engine;
	$_SESSION["search"] = '';
	$_SESSION["count"] = $count - 1;
	$objResponse->script('$(".finder-wrapper").remove();');
	$objResponse->script('$("#searchVehicle").remove();');
	$objResponse->script('$(".mainTableCellCenter").prepend(\''.$application_code.'\');');
	$objResponse->assign("currentApp", "innerHTML", '<div class="application"><p><strong>Current Vehicle:</strong></p><a title="Search Results For: '.$_SESSION["year"].' '.$_SESSION["make"].' '.$_SESSION["model"].' '.$_SESSION["engine"].'"class="currentVehicle" href="#">'. $_SESSION["year"] . ' ' . $_SESSION["make"]  . ' ' . $_SESSION["model"].' </a></div>');
	$objResponse->assign("currentCross", "innerHTML", "");
	$objResponse->assign("count", "value", ($count-1));
	$objResponse->script($effects_script);
	return $objResponse;
}

function getcross($partnumsubmit) {
	if($partnumsubmit == "") {exit();} //Make sure they submitted something
	
	$objResponse = new xajaxResponse();
	$crossRs = mysql_query("SELECT * FROM oilfiltersonline.crossref WHERE C_part like '%".$partnumsubmit."%'");
	
	if (mysql_num_rows($crossRs) > 0){
		
			$application_code = '<div class="finder-wrapper">
								 <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;background-color: black;color: silver;">
									<tr>
										<td width="20%"><div align="left" style="padding-left:5px"><strong>Manufacturer</strong></div></td>
										<td width="15%"><div align="center"><strong>MFR Part #</strong></div></td>
										<td width="15%"><div align="center"><strong>FRAM Part #</strong></div></td>
										<td width="20%"><div align="center"><strong>Our Price</strong></div></td>
										<td width="10%"><div align="center"><strong>Add To Cart</strong></div></td>
									</tr>
								</table>
								<div style="overflow:auto; text-align: center;vertical-align: middle;border: 1px solid black;background: #e8f4fe;">';
			$count = 1;
			while ($rs=mysql_fetch_assoc($crossRs)) {
				$application_code .= '<table width="100%" border="0" cellspacing="0" cellpadding="2" style="padding-top:5px">
										<tr>
											<td width="20%"><div align="left" style="padding-left:5px">'.$rs["Competitor"].'</div></td>
											<td width="15%"><div align="center">'.$rs["C_Part"].'</div></td>
											<td width="15%"><div align="center">
												<a href="product_details.php?item_code='.$rs["Part"].'">
													'.$rs["Part"].'
												</a>
												</div>
											</td>';
				if(is_null($rs["price"])) {
					$application_code .=   '<td width="20%">
											<div align=\"center\">
												<a href=\"articles.php?category_id=36\">Contact Us For Availability</a>
											</div>
											</td>
											<td width="10%"></td>
										</tr>';
				}else{
					$application_code .=   '<td width="20%">$'.$rs["price"].'</td>
										    <td width="10%">
											<form name="form'.$count.'" method="POST" action="sessionvars.php" class="addToCart">
												<input type="hidden" value="'.$rs["Part"].'" name="part" />
												<input type="hidden" value="'.str_replace(" ", "",$part_group).'" name="type" />
												Qty: <input name="quantity" value="1" style="width: 25px"/><input id="addCart" type="image" src="/images/oil-filters/addCart.png" />
										</form>
											</td>
										</tr>';
					$count++;
				}
				$application_code .='</table>';
			}
			$application_code .='</div></div>';
			$_SESSION["search"] = $partnumsubmit;
			$application_code = preg_replace('/\s\s+/', ' ', $application_code);
			$objResponse->assign("currentApp", "innerHTML", "");
			$objResponse->assign("currentCross", "innerHTML", '<div class="application"><p><strong>Current Cross Reference:</strong></p><a class="currentVehicle" href="#">'. $_SESSION["search"] .' </a></div>');
			$objResponse->assign("count", "value", ($count-1));
			$objResponse->script('$(".finder-wrapper").remove();');
			$objResponse->script('$("#searchVehicle").remove();');
			$objResponse->script('$(".mainTableCellCenter").prepend(\''.$application_code.'\');');
	} else {
			$application_code = '<div class="finder-wrapper"><span class="style2">Sorry, there were no matches found. Try your search again with only a segment of the part number.</span></div>';
			$objResponse->script('$(".finder-wrapper").remove();');
			$objResponse->script('$("#searchVehicle").remove();');
			$objResponse->script('$(".mainTableCellCenter").prepend(\''.$application_code.'\');');
	}
	$effects_script = '';
$_SESSION["year"] = '';
$_SESSION["make"] = '';
$_SESSION["model"] = '';
$_SESSION["engine"] = '';
$_SESSION["application"] = $application_code;
$_SESSION["count"] = $count - 1;
$objResponse->script($effects_script);
return $objResponse;
}

$xajax->registerFunction("getcross");
$xajax->registerFunction("addmodels");
$xajax->registerFunction("addyears");
$xajax->registerFunction("addengines");
$xajax->registerFunction("addengines_old");	
$xajax->registerFunction("showdiv_old");
$xajax->registerFunction("showdiv");
$xajax->processRequest();
$xajax->printJavascript("js/"); 
?>
<script type="text/javascript">
	$(document).ready(function(){
		<? if($_SESSION["application"]) {
			echo '$(".mainTableCellCenter").prepend(\''.$_SESSION["application"].'\');';
		}?>
		imagePreview();
		mouseHover();
		$(".finder-wrapper").addClass('hide');
		$(".application,#hide").click(function() {
				if($(".finder-wrapper").hasClass('hide')) {
					$(".finder-wrapper").removeClass('hide');
					$(".application").effect("transfer",{ to: $(".finder-wrapper") }, 400);
				} else {
					$(".finder-wrapper").effect("transfer",{ to: $(".application") }, 400);
					$(".finder-wrapper").addClass('hide');
				}
		});
	});
</script>
<link href="/css/Fram-Filter-Finder.css" rel="stylesheet" type="text/css">
<div id="vehicleSelector">
	<? echo $form_code; ?>
	<div id="currentApp">
	<?
	if($_SESSION["make"]){
		echo '<div class="application"><p><strong>Current Vehicle:</strong></p><a class="currentVehicle" href="#">'. $_SESSION["year"] . ' ' . $_SESSION["make"]  . ' ' . $_SESSION["model"].' </a></div>';
	} ?>
	</div>
<div class="clear"></div>
</div>
<div id="crossReference">
	<h2>Cross Reference</h2>
	<b>Manufacturer Part Number:</b>
	<input style="width: 177px;margin-bottom: 8px" name="partnum" id="partnum" type="text" onKeyPress="return disableEnterKey(event)">
	<span class="search">
		<input id="placeholder" name="placeholder" type="submit" class="button" value="Find My Part!" onClick="getResults(document.getElementById('partnum').value);" style="width:150px">
	</span>
	<form name="form1" method="POST" action="sessionvars.php">
		<input id="partinput" name="partinput" type="hidden" value="">
		<input id="count" name="count" type="hidden" value="">
	</form>
	<div id="currentCross">
	<? if($_SESSION["search"]) {
			echo '<div class="application"><p><strong>Current Cross Reference:</strong></p><a class="currentVehicle" href="#">'. $_SESSION["search"] .' </a></div>';
		}?>
	</div>
	<div class="clear"></div>
</div>

