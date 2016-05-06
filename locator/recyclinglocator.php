<?php 

// retrieve names 
function get_name($file){ 
    $h1tags = preg_match_all("/(<h3.*>)(\w.*)(<\/h3>)/ismU",$file,$patterns); 
    $res = array(); 
    array_push($res,$patterns[2]); 
    array_push($res,count($patterns[2])); 
    return $res; 
} 

// retrieve distances
function get_distance($file){ 
    $h1tags = preg_match_all('/(<span class="distance".*>)(\w.*)(<\/span>)/ismU',$file,$patterns); 
    $res = array(); 
    array_push($res,$patterns[2]); 
    array_push($res,count($patterns[2])); 
    return $res; 
} 

// retrieve address
function get_address($file){ 
    $h1tags = preg_match_all("/<p\sclass=\"addr\">(.*)<\/p>/siU", $file, $patterns); 
    $res = array(); 
    array_push($res,$patterns[1]); 
    array_push($res,count($patterns[1])); 
    return $res; 
}

// retrieve phone 
function get_phone($file){ 
    $h1tags = preg_match_all('/(<p class="phone".*>)(\w.*)(<\/p>)/ismU',$file,$patterns); 
    $res = array(); 
    array_push($res,$patterns[1]); 
    array_push($res,count($patterns[1])); 
    return $res;
} 

?> 
<html>
<style type="text/css">
<!--

body {
	font-size: 8pt;
	font-family: Arial, Helvetica, sans-serif;
}
.locations {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8pt;
}
-->
</style>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA5ZySsfbOdrtpzhRp2ghGTBQ9_My28vhZKTB0Cm2JOOIcKoNUthTHa__lfO0U4pgpQR_wmfEZn3-__A" type="text/javascript"></script>
<script type="text/javascript">

    var map = null;
    var geocoder = null;

    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
		map.setCenter(new GLatLng(37.300275,-98.173828), 4);
		map.addControl(new GSmallMapControl());
		geocoder = new GClientGeocoder();
	
      }
    }

    function showAddress(address,name) {
	
      if (geocoder) {
        geocoder.getLatLng(
          address,
          function(point) {
            if (!point) {
              alert(address + " not found");
            } else {
              map.setCenter(point, 12) ;
              var marker = new GMarker(point);
	      var html = "<b>" + name + "</b><br>" + address;
			GEvent.addListener(marker, "click", function() {
            		marker.openInfoWindowHtml(html);
          		});
              map.addOverlay(marker);
              marker.openInfoWindowHtml(html);
            }
          }
        );
      }
    }
    
</script>

<table width="820" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <td><div align="center"><img src="/images/oil_filters/RecyclingLocations.jpg" width="804" height="57"></div></td>
  </tr>
  <tr>
    <td><div align="center" class="locations">Recycling your used oil is very important! Be sure to dispose of your oil properly by transporting it to a recylcling center near you. When transporting your used oil, make sure you have a safe container. You can find containers in the Oil Supplies section of our store. It is usually a good idea to call the recycling center before you go.</div></td>
  </tr>
  <tr>
    <td>
    	<form action="" method="post" enctype="application/x-www-form-urlencoded" name="form1"> 
		  <div align="center" class="locations">
		    Enter your zip code or city, state
            <input name="cityzip" type="text" size="40"/>
		    <input name="submit" type="submit" value="Submit" />
		    <br>
		  </div>
    	</form>
    </td>
  </tr>
</table>



<?php 


if (isset ($_POST['Submit']) or isset ($_POST['cityzip'])) {

	// Set up URL and IP Addresses

	$cityzip = urlencode($_POST['cityzip']);
	$ippool = array("69.63.195.114","69.63.195.115","69.63.195.116","69.63.195.117","69.63.195.118","69.63.195.119","69.63.195.120","69.63.195.121","69.63.195.122","69.63.195.123","69.63.195.124","69.63.195.125");

	$ip = $ippool[mt_rand(0, count($ippool)-1)];    // Get random IP address

	// Set up the CURL object

	$url = "http://search.earth911.com/?what=oil&where=$cityzip&max_distance=100<br>";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);                                 // Tell CURL which URL to get
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                         // Tell CURL to return the results as a string return value from curl_exec()
	curl_setopt($ch, CURLOPT_INTERFACE, $ip);							 // Assign random IP address to use
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );	 // Pass user agent	

	$str = curl_exec($ch);                                       
	curl_close( $ch );
	
	// Get just the list sorted by name
	preg_match( "/\<ul ID=\"recycleListings\"\>(.*?)\<\/ul\>/is",$str, $byname );

      // Extract fields

	$file = $byname[0];
	$name = get_name($file);
	$distance = get_distance($file);
	$address = get_address($file);
	$phone = get_phone($file);


?>
<body onLoad="initialize()" onUnload="GUnload()">
	<table width="800" align="center" cellpadding="5" cellspacing="0" class="locations" id="centers">
<tr>
		  <td colspan="4" align="center" valign="top"><div class="locations" id="map_canvas" style="display: block; width: 780px; height: 250px; text-align: center;" ></div></td>
    	</tr>
<?
	// Print Locations

	for( $i = 0; $i < 5; $i++ ) {
	
		echo "<tr><td>";
		$loc = preg_replace( "/<.*?>/", "", $name[0][$i]);
		$loc = preg_replace("/Program Information/","",$loc);
		echo "<b><u>$loc</u></b>" ; 
		echo "<br><b>Distance: </b>";
		echo html_entity_decode($distance[0][$i]) ;
		echo "<br>";
		$addr = preg_replace( "/<.*?>/", "", $address[0][$i]);
		echo trim(html_entity_decode($addr)) ;
		echo "<br>";
		$phn = preg_replace( "/<.*?>/", "", $phone[0][$i]);
		echo trim(html_entity_decode($phn)) ;
?>
		</td><td>
		<form action="#" onSubmit="showAddress(this.address.value,this.name.value); return false">
      			<input type="hidden" size="60" name="address" value="<? echo $addr ?>" />
				<input type="hidden" size="60" name="name" value="<? echo $loc ?>" />
        		<input type="submit" value="Map It!" />
		</form>	
		</td>
        <td>
<?       
        $loc = preg_replace( "/<.*?>/", "", $name[0][$i+5]);
		$loc = preg_replace("/Program Information/","",$loc);
		echo "<b><u>$loc</u></b>" ; 
		echo "<br><b>Distance: </b>";
		echo html_entity_decode($distance[0][$i]) ;
		echo "<br>";
		$addr = preg_replace( "/<.*?>/", "", $address[0][$i+5]);
		echo trim(html_entity_decode($addr)) ;
		echo "<br>";
		$phn = preg_replace( "/<.*?>/", "", $phone[0][$i+5]);
		echo trim(html_entity_decode($phn)) ;
?>
		</td><td>
		<form action="#" onSubmit="showAddress(this.address.value,this.name.value); return false">
      			<input type="hidden" size="60" name="address" value="<? echo $addr ?>" />
				<input type="hidden" size="60" name="name" value="<? echo $loc ?>" />
        		<input type="submit" value="Map It!" />
		</form>		</td>
        </tr>

<? } 
}
?>     
</table>
	
</body>
</html>
