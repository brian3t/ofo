<?php 

	// Set up URL and IP Addresses

	$ippool = array("69.63.195.114","69.63.195.115","69.63.195.116","69.63.195.117","69.63.195.118","69.63.195.119","69.63.195.120","69.63.195.121","69.63.195.122","69.63.195.123","69.63.195.124","69.63.195.125","69.63.195.126");

	$ip = $ippool[mt_rand(0, count($ippool)-1)];    // Get random IP address

	// Set up the CURL object

	$url = "http://www.qoop.com/photobooks/ps_user/ps_login.php?ft=yes&user_token=9d0c81b2557e96e825aaeb3042873351&photosite_id=QPPS&extra=live||||qualityphotoprints|";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);                                 // Tell CURL which URL to get
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                         // Tell CURL to return the results as a string return value from curl_exec()
	curl_setopt($ch, CURLOPT_INTERFACE, $ip);							 // Assign random IP address to use
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );	 // Pass user agent	

	$str = curl_exec($ch);                                       
	curl_close( $ch );
	
	// Get just the images in the product selection content
	preg_match( "/\<div style='width: 650px;' name='product_selection_content' id='product_selection_content'\>(.*?)\<\/div\>/is",$str, $qoop );
	
	// Replace the relative paths with absolute paths
	$qoop = str_replace("..","http://www.qoop.com/photobooks",$qoop[0]);

$myFile = "test.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $qoop;
fwrite($fh, $stringData);
fclose($fh);



?>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<script language="JavaScript" type="text/javascript" src="http://www.qoop.com/photobooks/javascript/iframe_functions.js"></script>
<script language="javascript" type="text/javascript">

function switch_product_selection(page, photo_source, photosite_id, sub_account_num, pro_user, pro_shots_only)
{
	var parameters = new Array();
	parameters['page'] = page;
	parameters['source'] = photo_source;
	parameters['photosite_id'] = photosite_id;
	parameters['sub_account_num'] = sub_account_num;
	parameters['pro_user'] = pro_user;
	parameters['pro_shots_only'] = pro_shots_only;
	update_iframe_with_parameters(get_hidden_frame(), "http://www.qoop.com/photobooks/photofront/draw_product_selection.php", parameters);
}
</script>
</head>
<body>
	<table width="800" align="center" cellpadding="5" cellspacing="0">
	<tr>
		  <td><?  echo $qoop; ?> 
          <iframe id="hidden_frame" width="0" height="0" src="" name="hidden_frame" marginheight="0" marginwidth="0" border="0">
			<html>
				<head>
					<title/>
				</head>
			<body/>
			</html>
		  </iframe>
          </td>
	</tr>
 	</table>
</body>
</html>
