<?
require('js/xajax_core/xajax.inc.php');
$xajax = new xajax();
// $xajax->configure('debug',true);
 
class myXajaxResponse extends xajaxResponse {
 
  function addCreateOptions($sSelectId, $options) {
    $this->script("document.getElementById('".$sSelectId."').length=0");
    if (sizeof($options) >0) {
       foreach ($options as $option) {
         $this->script("addOption('".$sSelectId."','".
$option['txt']."','".$option['val']."');");
       }
     }
  }
}
 
$db=mysql_connect("localhost", "root", "rTrapok)1") or die(mysql_error());
mysql_select_db("oilfiltersonline") or die(mysql_error());
$makesRs = mysql_query("SELECT DISTINCT(make) from part_lookup order by make asc") or die(mysql_error());
while ($rs=mysql_fetch_assoc($makesRs)) {
	$makes[]=$rs["make"];
}
 
      // adds an option to the select 

      function addmodels($selectId, $make) {
		global $models;
        	$objResponse = new myXajaxResponse();
        	$modelsRs = mysql_query(sprintf(
	"SELECT DISTINCT(model) FROM part_lookup WHERE make = '%s' order by model asc", $make));
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

      function addyears($selectId, $models, $make) {
        	global $years;
        	$objResponse = new myXajaxResponse();
		$yearsRs = mysql_query(sprintf
	("SELECT DISTINCT(year) FROM part_lookup WHERE model = '%s' and make = '%s' order by year desc", $models, $make));
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

      function addengines($selectId, $models, $make, $year) {
        	global $engines;
        	$objResponse = new myXajaxResponse();
		$enginesRs = mysql_query(sprintf
	("SELECT part,engine FROM part_lookup WHERE model = '%s' and make = '%s' and year = '%s' order by engine desc", $models, $make, $year));
		$engines[]=array("txt"=>"--Select Engine--", "val"=>"");
		while ($rs=mysql_fetch_assoc($enginesRs)) {
	  		$engines[]=array("txt"=>$rs["engine"]."  [".$rs["part"]."]", "val"=>$rs["engine"]);
			}
		$objResponse->assign("enginediv", "style.display", "block");
		$objResponse->assign("submitdiv", "style.display", "none");
      	$objResponse->addCreateOptions($selectId, $engines);
		return $objResponse;
      }

	function showdiv($divid) {
		$objResponse = new myXajaxResponse();
		$objResponse->assign($divid, "style.display", "block");
		return $objResponse;
	}


      $xajax->registerFunction("addmodels");
      $xajax->registerFunction("addyears");
      $xajax->registerFunction("addengines");	
      $xajax->registerFunction("showdiv");
      $xajax->processRequest();
      ?>
      <?
      if (isset($_POST['Submit'])) {
	  	header( 'Location: http://www.oilfiltersonline.com/product_details.php?category_id=0&item_code=PH7317' ) ;
        print_r($_POST);
      }
      ?>
<html>
<head>
<title>OilFiltersOnline.com Vehicle Selector</title>
      <?
      $xajax->printJavascript("js/");
      ?>
<script type="text/javascript">
  function addOption(selectId, txt, val) {
    var objOption = new Option(txt, val);
	
     	document.getElementById(selectId).options.add(objOption);
   }

</script>
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
}
-->
</style>
</head>
<body><table width="475" border="0" align="center" cellpadding="1" cellspacing="5">
	  <tr>
    	<td><div align="center"><img src="/images/fashion/FilterFinder.jpg"></div></td>
      </tr>
	  <tr>
	    <td><div align="center" class="style1">Select your car using our quick and easy Filter Finder and we'll take you right to the correct part for your vehicle!</div></td>
  </tr>
  <tr>
    <td><table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    	 <form name="form1" method="POST" action="">
      <b>Make :</b><br> 
      <select style="width: 350px" name="make" id="make" onChange="xajax_addmodels('models', document.form1.make.value)">
        <option value="">--Select Make--</option>
        <? foreach ($makes as $mod) { ?>
        <option value="<?= $mod?>"><?= $mod?></option>
        <? } ?>
      </select><br>
	<div name="modeldiv" id="modeldiv" style="display: none">
      <b>Model :</b><br>
      <select style="width: 350px" name="models" id="models" onChange="xajax_addyears('years', document.form1.models.value, document.form1.make.value)">
		<option value="">--Select Model--</option>
      	</select><br>
	</div>
	<div name="yeardiv" id="yeardiv" style="display: none">	
      <b>Year :</b><br>
      <select style="width: 350px" name="years" id="years" onChange="xajax_addengines('engines', document.form1.models.value, document.form1.make.value, document.form1.years.value)">
		<option value="">--Select Year--</option>
      	</select><br>
	</div>
	<div name="enginediv" id="enginediv" style="display: none">
	<b>Engine :</b><br>
	<select style="width: 350px" name="engines" id="engines" onChange="xajax_showdiv('submitdiv')">
		<option value="">--Select Engine--</option>
		</select><br>
	</div>
	<div align="center" name="submitdiv" id="submitdiv" style="display: none">
	<br>
      <input type="submit" value="Find My Filter!" name="Submit" id="Submit">
	</div>
      </form>
    </td>
  </tr>
</table>

      </td>
  </tr>
</table>

	
</body>
</html>