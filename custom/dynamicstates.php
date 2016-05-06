<?
//  Dynamic States Functionality for Viart 3.6
//  Created by Egghead Ventures, LLC - Tom Morris
//  6/7/09

require('js/xajax_core/xajax.inc.php');
$xajax = new xajax();
//$xajax->configure('debug',true); // Uncomment to debug
 
class myXajaxResponse extends xajaxResponse {
 
  function addCreateOptions($sSelectId, $options) {
    $this->script("document.getElementById('".$sSelectId."').length=0"); //Be sure to add ID tags in the block_order_info.html for state_ID and delivery_state_ID
	if($sSelectId == 'state_id') {
		$this->script("document.getElementById('delivery_country_id').selectedIndex = document.getElementById('country_id').selectedIndex"); //Update the selected delivery country if the personal is updated
	}
    if (sizeof($options) >0) {
       foreach ($options as $option) {
         $this->script("addOption('".$sSelectId."','".
$option['txt']."','".$option['val']."');");
       }
     }
  }
}

function addstates($selectId,$country) { // Dynamically updates states based on country
	global $dynStates, $db, $table_prefix, $settings;  //Borrow some of the global vars used by VIART
	$objResponse = new myXajaxResponse();
	$sql = "SELECT state_id,state_name FROM ".$table_prefix."states WHERE show_for_user=1 and country_id=".$country." order by state_name";
	$db->query($sql);
	$dynStates[]=array("txt"=>"Select State", "val"=>"");
	while ($db->next_record()) {
		$dynStates[]=array("txt"=>$db->f("state_name"), "val"=>$db->f("state_id"));
		}
	if($selectId == 'state_id') {
		$objResponse->addCreateOptions('state_id', $dynStates); //Change both personal and delivery state options
	}
	$objResponse->addCreateOptions('delivery_state_id', $dynStates);
	
	return $objResponse;
}

$xajax->register(XAJAX_FUNCTION,'addstates');

$xajax->processRequest();

$xajax->printJavascript("js/");

?>

<script type="text/javascript">
  function addOption(selectId, txt, val) {
    var objOption = new Option(txt, val);
	
     	document.getElementById(selectId).options.add(objOption);
   }
</script>