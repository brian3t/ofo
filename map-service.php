<?php
/*
ini_set('display_errors',1);
error_reporting(E_ALL);*/

if($_GET['action'] == 'savepoint') {

	$address = $_GET['address'];
	getLocations($address);
	
	exit;
}
	
// retrieve names 
function get_name($file){ 
    $h1tags = preg_match_all("/(<h2.*>)(\w.*)(<\/h2>)/ismU",$file,$patterns); 
    $res = array(); 
    array_push($res,$patterns[2]); 
    array_push($res,count($patterns[2])); 
    return $res; 
} 

// retrieve address1
function get_address1($file){ 
    $h1tags = preg_match_all("/<p\sclass=\"address1\">(.*)<\/p>/siU", $file, $patterns); 
    $res = array(); 
    array_push($res,$patterns[1]); 
    array_push($res,count($patterns[1])); 
    return $res; 
}

// retrieve address3
function get_address3($file){ 
    $h1tags = preg_match_all("/<p\sclass=\"address3\">(.*)<\/p>/siU", $file, $patterns); 
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

function getLocations($zipcode) {

	// Set up URL and IP Addresses

	$cityzip = urlencode($zipcode);
	$ippool = array("69.63.195.114","69.63.195.115","69.63.195.116","69.63.195.117","69.63.195.118","69.63.195.119","69.63.195.120","69.63.195.121","69.63.195.122","69.63.195.123","69.63.195.124","69.63.195.125");

	$ip = $ippool[mt_rand(0, count($ippool)-1)];    // Get random IP address

	// Set up the CURL object

	$url = "http://search.earth911.com/?what=oil&where=$cityzip&max_distance=100";
		
	$str = file_get_contents($url);
	
	/*
$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);                                 // Tell CURL which URL to get
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                         // Tell CURL to return the results as a string return value from curl_exec()
	curl_setopt($ch, CURLOPT_INTERFACE, $ip);							 // Assign random IP address to use
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );	 // Pass user agent	

	$str = curl_exec($ch);                                       
	curl_close( $ch );
*/

	preg_match( "/\<ul class=\"result-list\"\>(.*?)\<\/ul\>/is",$str, $byname1 );
		
    // Extract fields

	$file = $byname1[0];
	$name = get_name($file);
	$address1 = get_address1($file);
	$address3 = get_address3($file);
	$phone = get_phone($file);

	mysql_connect('localhost', 'root', 'ifl@b')
		OR die(fail('Could not connect to database.'));
		
	mysql_select_db('oilfiltersonline');
	$i = 0;
	$points = array();
	foreach($phone[0] as $value) {
		
		$loc = preg_replace( "/<(.*?)>/", "", $name[0][$i]);
		$loc = preg_replace("/Program Information/","",$loc);
		$phn = trim(preg_replace( "/<(.*?)>/", "", $value));
		$addr1 = trim(preg_replace( "/<(.*?)>/", "", $address1[0][$i]));
		$addr3 = trim(preg_replace( "/<(.*?)>/", "", $address3[0][$i]));
		if ($addr1 != null) {
			$addr1 = preg_replace("/[^a-zA-Z0-9\s]/", "", $addr1);
		}
		$addr3= trim(html_entity_decode($addr3));
		$pieces = explode(",", $addr3);
		$city = trim($pieces[0]);
		$pieces = explode(" ", trim($pieces[1]));
		$state = trim($pieces[0]);
		$zip = trim($pieces[1]);

		array_push($points, array('name' => $loc, 'address' => $addr1, 'city' => $city, 'state' => $state, 'zip' => $zip, 'phone' => $phn));	
		
		mysql_query(sprintf("insert into locations (name, phone, address1, city, state, zip) VALUES('%s','%s','%s','%s','%s','%s') ON DUPLICATE KEY UPDATE name = '%s'", $loc, $phn, $addr1, $city, $state, $zip, $loc));
		$i++ ;
	}
	
	$points = array_slice($points, 0, 10);
	echo json_encode(array("Locations" => $points));
}
?>