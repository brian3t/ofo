<?
require('js/xajax_core/xajax.inc.php');
$xajax = new xajax();
// $xajax->configure('debug',true);
global $current_db, $current_view;  //Have to declare these as global so they are available to all functions

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
	$current_db = "motorcycle_parts";
	$current_view = "motorcycle_parts_view";
	$current_description = "<h2>Motorcycles, Small Recreation Vehicles and Watercraft</h2>
        					Select your application below. Once your selection is complete, click on the part number to go the product page or add
        					the items directly to your shopping cart by checking the box next to the parts you want to purchase 
        					and clicking the <strong>ADD CHECKED ITEMS TO CART</strong> button. The system will remember your selections so you can 
							always come back this page to review or add parts to your shopping cart.";
} else if($_GET["app"] == "heavyduty") {
	$current_db = "heavy_duty_parts";
	$current_view = "heavy_duty_parts_view";
	$current_description = "<h2>Heavy Duty Applications</h2>
        					Select your application below. Once your selection is complete, click on the part number to go the product page or add
        					the items directly to your shopping cart by checking the box next to the parts you want to purchase 
        					and clicking the <strong>ADD CHECKED ITEMS TO CART</strong> button. The system will remember your selections so you can 
							always come back this page to review or add parts to your shopping cart.<br /><br />
							Please note that a lot of heavy duty applications have notes about the application.  To see any special notes, hold your 
							mouse over the <img src=\"images/info.gif\"/> next to the part description, if applicable.";
} else if($_GET["app"] == "cross") {
	$current_description = "<h2>Cross Reference</h2>
							Enter the part number you are currently using below and the <strong>Filter Finder&#8482;</strong> will show you the 
							<strong>FRAM&trade;</strong> equivalent! Please be specific. USE AS A GUIDE ONLY - We do our best to ensure this information 
							is accurate but you should always check to your application carefully. Add the items directly to your shopping cart by checking 
							the box next to the parts you want to purchase and clicking the <strong>ADD CHECKED ITEMS TO CART</strong> button. 
							Click on the FRAM part number to go to the product page.";
} else {
	$current_db = "distinct_vehicles";
	$current_view = "automotive_parts_view";
	$current_description = "<h2>Passenger Cars &amp; Small Trucks</h2>
        					Select your application below. Once your selection is complete, click on the part number to go the product page or add
        					the items directly to your shopping cart by checking the box next to the parts you want to purchase 
        					and clicking the <strong>ADD CHECKED ITEMS TO CART</strong> button. The system will remember your selections so you can 
							always come back this page to review or add parts to your shopping cart.";
}

if($_GET["app"] != "cross") { //Only show the make dropdown if it is not cross reference
	$makesRs = mysql_query("SELECT DISTINCT(make) from oilfiltersonline.".$current_db." order by make asc") or die(mysql_error());
	$make_options = '<option value="">--Select Make--</option>';
	while ($rs=mysql_fetch_assoc($makesRs)) {
		$make_options .= '<option value="'.$rs["make"].'">'.$rs["make"].'</option>';
	}
	$form_code =   '<form name="form1" method="POST" action="sessionvars.php">
					<input id="count" name="count" type="hidden" value="'.$_SESSION["count"].'">
					<div style="float:left">
						<div class="MMYE">Make :</div>
						<select class="dropdown" name="make" id="make" onChange="xajax_addmodels(\'models\', document.form1.make.value)">'.$make_options.'</select>
						<br />
						<div name="modeldiv" id="modeldiv" style="display: none">
							<div class="MMYE"><b>Model :</b></div>
							<select class="dropdown" name="models" id="models" 
							onChange="xajax_addyears(\'years\', document.form1.models.value, document.form1.make.value)">
							</select>
						</div>';
	$form_code .=  '	<div name="yeardiv" id="yeardiv" style="display: none">
							<div class="MMYE"><b>Year :</b></div>
							<select class="dropdown" name="years" id="years" 
							onChange="xajax_addengines(\'engines\', document.form1.models.value, document.form1.make.value, document.form1.years.value)">
							</select>
						</div>
						<div name="enginediv" id="enginediv" style="display: none">
							<div class="MMYE"><b>Engine :</b></div>
							<select class="dropdown" name="engines" id="engines" 
							onChange="xajax_showdiv(\'submitdiv\', document.form1.models.value, document.form1.make.value, document.form1.years.value, document.form1.engines.value)">
							</select>
						</div>
					</div>
					<div style="float: right; padding-right: 10px;">
						<img src="/images/oil-filters/Free-2Day-Upgrade.jpg" alt="Autolite Spark Plugs" 
						title="Now offering Autolite spark plugs!">
					</div>';
} else {
	$form_code =   '<div style="margin-bottom:10px;">
						<b>Manufacturer Part Number:</b>
						<input style="width: 310px" name="partnum" id="partnum" type="text" onKeyPress="return disableEnterKey(event)">
						<input id="placeholder" name="placeholder" type="image" src="/images/cross-reference-find-my-part.jpg"
						 onClick="getResults(document.getElementById(\'partnum\').value);" style="vertical-align: middle;">
					</div>
					<form name="form1" method="POST" action="sessionvars.php">
						<input id="partinput" name="partinput" type="hidden" value="">
						<input id="count" name="count" type="hidden" value="">';
}

function addmodels($selectId, $make) {
	global $models, $current_db;
	$objResponse = new myXajaxResponse();
	$modelsRs = mysql_query(sprintf(
	"SELECT DISTINCT(model) FROM oilfiltersonline.".$current_db." WHERE make = '%s' order by model asc", $make));
	$models[]=array("txt"=>"--Select Model--", "val"=>"");
	while ($rs=mysql_fetch_assoc($modelsRs)) {
		$models[]=array("txt"=>$rs["model"], "val"=>$rs["model"]);
		}
	$objResponse->assign("modeldiv", "style.display", "block");
	$objResponse->assign("yeardiv", "style.display", "none");
	$objResponse->assign("enginediv", "style.display", "none");
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
	$objResponse->assign("yeardiv", "style.display", "block");
	$objResponse->assign("enginediv", "style.display", "none");
	$objResponse->assign("submitdiv", "style.display", "none");
	$objResponse->addCreateOptions($selectId, $years);
	return $objResponse;
}

function addengines($selectId, $model, $make, $year) {
	global $engines, $current_db;
	$objResponse = new myXajaxResponse();
	$enginesRs = mysql_query(sprintf
	("SELECT DISTINCT(engine) FROM oilfiltersonline.".$current_db." WHERE model = '%s' and make = '%s' and year = '%s' order by engine desc", $model, $make, $year));
	$engines[]=array("txt"=>"--Select Engine--", "val"=>"");
	while ($rs=mysql_fetch_assoc($enginesRs)) {
		$engines[]=array("txt"=>$rs["engine"], "val"=>$rs["engine"]);
		}
	$objResponse->assign("enginediv", "style.display", "block");
	$objResponse->assign("submitdiv", "style.display", "none");
	$objResponse->addCreateOptions($selectId, $engines);
	return $objResponse;
}

function showdiv($divid, $model, $make, $year, $engine) {
	global $part, $current_view;
	$objResponse = new myXajaxResponse();
	$partRs = mysql_query(sprintf
	("SELECT * FROM oilfiltersonline.".$current_view." WHERE model = '%s' and make = '%s' and year = '%s' and engine = '%s' order by sort_order", $model, $make, $year, $engine));
	$application_code = '<div align="center">
						<input type="image" src="/images/cross-reference-add.jpg" name="cart" id="button" />
						</div>
						<table width="675" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;background-color: black;color: silver;">
							<tr>
								<td width="50%"><div align="left" style="padding-left:5px" class="style2"><strong>Part Type</strong></div></td>
								<td width="18%"><div align="left" class="style2"><strong>Part #</strong></div></td>
								<td width="17%"><div align="center" class="style2"><strong>Our Price</strong></div></td>
								<td width="15%"><div align="center" class="style2"><strong>Add to Cart</strong></div></td>
							</tr>
						</table>
						<div style="overflow:auto; text-align: center;vertical-align: middle;border: 1px solid black;background: #e8f4fe;">';
	$count = 1;
	$previous_part_group = "";
	while ($rs=mysql_fetch_assoc($partRs)) {
		$category = $rs["category"];
		$description = $rs["description"];
		$manufacturer = $rs["manufacturer"];
		$part_group = $rs["part_group"];
		if($part_group != $previous_part_group) {
			$application_code .= "<div class=\"finder_header_row\">".$part_group."</div>";
		}

		if(is_null($rs["price"]) || $rs["Active"] == 0) {
			$application_code .= "<table width=\"665\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" style=\"text-align:left\">
									<tr>
									<td width=\"50%\"><span class=\"".$manufacturer."\">".$manufacturer." </span><span class=\"style2\">".$category."</span>";
								if ($description) {
			$application_code .= "<img src=\"images/info.gif\" onmouseover=\"Tip('".$description."')\" onmouseout=\"UnTip()\">";
								}
			$application_code .= "</td>
									<td width=\"15%\"><span class=\"style2\">".$rs["part"]."</span></td>
									<td width=\"25%\">
										<div align=\"center\" class=\"style2\">
											<a href=\"articles.php?category_id=36\">Contact Us For Availability</a>
										</div>
									</td>
									<td width=\"10%\">&nbsp;</td>
									</tr>
								  </table>";
		}else{
			$application_code .= "<table width=\"665\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" style=\"text-align:left\">
									<tr>
									<td width=\"50%\"><span class=\"".$manufacturer."\">".$manufacturer." </span><span class=\"style2\">".$category."</span>";
			if ($description) {
			$application_code .= "<img src=\"images/info.gif\" onmouseover=\"Tip('".$description."')\" onmouseout=\"UnTip()\">";
								}
			$application_code .= " </td>
									<td width=\"15%\">
									<a href=\"product_details.php?item_code=".$rs["part"]."\" target=\"_parent\"><span class=\"style2\">".$rs["part"]."</span>
									</td>
									<td width=\"25%\"><div align=\"center\" class=\"style2\">$".$rs["price"]."</div></td>
									<td width=\"10%\">
									<div align=\"center\">
										<input type=\"checkbox\" name=\"part".$count."\" id=\"part".$count."\" value=\"".$rs["part"]."\"/>
									</div>
									</td>
									</tr>
								  </table>";
			$count++;
		}
		$previous_part_group = $rs["part_group"];
	}
	$application_code .= '</div>';
	// Store the vehicle and part results in case the user comes back to see their parts
	$_SESSION["application"] = $application_code;
	$_SESSION["search"] = "<b>Current Vehicle: </b>".$year." ".$make." ".$model." - ".$engine;
	$_SESSION["year"] = $year;
	$_SESSION["make"] = $make;
	$_SESSION["model"] = $model;
	$_SESSION["engine"] = $engine;
	$_SESSION["count"] = $count - 1;
	$objResponse->assign("submitdiv", "innerHTML", $application_code);
	$objResponse->assign($divid, "style.display", "block");
	$objResponse->assign("count", "value", ($count-1));
	return $objResponse;
}

function getcross($partnumsubmit) {
	if($partnumsubmit == "") {exit();} //Make sure they submitted something
	
	$objResponse = new xajaxResponse();
	$crossRs = mysql_query("SELECT * FROM oilfiltersonline.crossref WHERE C_part like '%".$partnumsubmit."%'");
	
	if (mysql_num_rows($crossRs) > 0){
		
			$application_code = '<div align="center">
								 	<input type="image" style="margin-top:5px" src="/images/cross-reference-add.jpg" name="cart" id="button"/>
								 </div>
								 <table width="675" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;background-color: black;color: silver;">
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
				$application_code .= '<table width="665" border="0" cellspacing="0" cellpadding="2" style="padding-top:5px">
										<tr>
											<td width="20%"><div align="left" style="padding-left:5px">'.$rs["Competitor"].'</div></td>
											<td width="15%"><div align="center">'.$rs["C_Part"].'</div></td>
											<td width="15%"><div align="center">
												<a href="#" onclick="window.open(\'product_details.php?item_code='.$rs["Part"].'\',\'_top\'); return false">
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
												<div align="center">
													<input type="checkbox" name="part'.$count.'" id="part'.$count.'" value="'.$rs["Part"].'"/>
												</div>
											</td>
										</tr>';
					$count++;
				}
				$application_code .='</table>';
			}
			$objResponse->assign("submitdiv", "innerHTML", $application_code);
			$objResponse->assign("count", "value", ($count-1));
	} else {
			$application_code = '<br />
								<div align="center">
									<span class="style2">Sorry, there were no matches found.  Try your search again with only a segment of the part number.</span>
								</div>';
			$objResponse->assign("submitdiv", "innerHTML", $application_code);
	}
$_SESSION["application"] = $application_code;
$_SESSION["search"] = "<b>Cross Reference Results For: </b>".$partnumsubmit;
$_SESSION["count"] = $count - 1;
return $objResponse;
}

$xajax->registerFunction("getcross");
$xajax->registerFunction("addmodels");
$xajax->registerFunction("addyears");
$xajax->registerFunction("addengines");	
$xajax->registerFunction("showdiv");
$xajax->processRequest();
$xajax->printJavascript("js/"); 
?>
<script type="text/javascript">
	function addOption(selectId, txt, val) {
		var objOption = new Option(txt, val);
		document.getElementById(selectId).options.add(objOption);
	}
	
	function disableEnterKey(e) {
		 var key;
	
		 if(window.event)
			  key = window.event.keyCode;     //IE
		 else
			  key = e.which;     //firefox
	
		 if(key == 13) {
			document.getElementById("submitdiv").innerHTML = '<div align="center"><img src="images/loading.gif" style="margin-top:50px;padding-bottom:10px"><br /><span class="style2">Please Wait While We Find Your Part</span></div>';
			 document.getElementById("placeholder").click();
		 }else{
			  return true;}
	}
	
	function getResults(comp) {
		document.getElementById("submitdiv").innerHTML = '<div align="center"><img src="images/loading.gif" style="margin-top:50px;padding-bottom:10px"><br /><span class="style2">Please Wait While We Find Your Part</span></div>';
		xajax_getcross(comp);
	}
</script>
<script type="text/javascript" src="js/wz_tooltip.js"></script>
<link href="/css/Fram-Filter-Finder.css" rel="stylesheet" type="text/css"/>
<a name="FF"><img src="/images/oil-filters/find-your-filters.jpg" style="padding-bottom:5px"/></a>
<div class="midnav">
    <ul class="tabs" id="nav-tabs">
        <li><a href="?app=auto" id="car-oil-filters" alt="Car & Auto Parts, Oil Filters, Air Filters, Spark Plugs" title="Find parts for your car or small truck including oil filters, air filters, fuel filters, and spark plugs"></a></li>
        <li><a href="?app=motorcycle" id="motorcycle-oil-filters" alt="Motorcycle Oil Filters" title="Find motorcycle oil filters"></a></li>
        <li><a href="?app=heavyduty" id="heavy-duty-filters" alt="Heavy Duty Filters" title="Find oil filters, air filters, fuel filters for your heavy duyt application"></a></li>
        <li><a href="?app=cross" id="oil-filter-cross-reference" "FRAM Cross Reference Tool" title="Cross reference your air filter, fuel filter and oil filter to a FRAM equivalent"></a></li>
        <li><a href="articles.php?category_id=36" id="ask-a-question" alt="Filter Questions" title="Ask us a question about your oil filter, fuel filter, air filter, or spark plug application"></a></li>
    </ul>
</div>
<hr>
<div align="left">
<? echo $current_description; ?>
</div>
<hr>
<? echo $form_code; ?>
<div align="center" name="submitdiv" id="submitdiv" style="clear:both">
<? if($_SESSION["application"]){ 
			$output = "<div style=\"border: #000000 solid 1px;background: #c8fc8a;\">".$_SESSION["search"]."</div><br />".$_SESSION["application"];
			echo $output;
		} 
 ?>
</form>
</div>
