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

// retrieve any email 
function get_emails($file){ 
    $h1count = preg_match_all('/[a-zA-Z0-9_-]{1,}@[a-zA-Z0-9-_]{1,}\.[a-zA-Z]{1,4}/',$file,$patterns); 
    $res = array(); 
    array_push($res,$patterns[0]); 
    array_push($res,count($patterns[0])); 
    return $res; 
} 


?> 
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>Oil Recycling Locations</title> 

    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA5ZySsfbOdrtpzhRp2ghGTBQ9_My28vhZKTB0Cm2JOOIcKoNUthTHa__lfO0U4pgpQR_wmfEZn3-__A"
      type="text/javascript"></script>
    <script type="text/javascript">

	function show2() {
  
     		document.getElementById('map_canvas').style.display = 'block';

	}

    var map = null;
    var geocoder = null;

    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
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
  </head>

<body>

<form action="" method="post" enctype="application/x-www-form-urlencoded" name="form1"> 
<input name="cityzip" type="text" size="40"/>
<input name="submit" type="submit" value="submit" /><br>
Enter your zip code or city, state.
</form> 

<?php 


if (isset ($_POST['submit']) or isset ($_POST['cityzip'])) {

	$cityzip = urlencode($_POST['cityzip']);

	echo "http://earth911.org/search-recycle?what=oil&where=$cityzip&max_distance=25<br>";


?>


	    
<body onload="initialize()" onunload="GUnload()">
    
 


<?

// Set up the CURL object
$ch = curl_init( "http://www.oilfiltersonline.com/locator/earth911.htm" );

curl_setopt( $ch, CURLOPT_USERAGENT, "Internet Explorer" );

// Start the output buffering
ob_start();

// Get the HTML
curl_exec( $ch );
curl_close( $ch );

// Get the contents of the output buffer
$str = ob_get_contents();
ob_end_clean();

// Get just the list sorted by name
preg_match( "/\<ul ID=\"recycleListings\"\>(.*?)\<\/ul\>/is",$str, $byname );

    
// Extract fields

	$file = $byname[0];
	$name = get_name($file);
	$distance = get_distance($file);
	$address = get_address($file);
	$phone = get_phone($file);


?><table><tr><td>
<?
// Print Locations

	for( $i = 0; $i < count( $name[0] ); $i++ ) {
		$loc = preg_replace( "/<.*?>/", "", $name[0][$i]);
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



		<form action="#" onsubmit="showAddress(this.address.value,this.name.value); return false">
      			<input type="hidden" size="60" name="address" value="<? echo $addr ?>" />
			<input type="hidden" size="60" name="name" value="<? echo $loc ?>" />
        		<input type="submit" onClick="show2()" value="Map It!" />
    		</form>	
		
<?	
        } 

}
?>
</td>
<td valign="top"><div id="map_canvas" style="display: block; width: 600px; height: 500px"></div></td>
</tr></table>
</body> 
</html> 
