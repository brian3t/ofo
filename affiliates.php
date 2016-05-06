<?php 
$block_body .= "<br /><br />
We are happy to announce that we will be launching a very generous affiliate program in the comming weeks.  Please check back with us soon...
<table border=\"0\" align=\"center\" cellpadding=\"5\" cellspacing=\"0\">
  <tr>
    <td>
    	<form action=\"\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" name=\"form1\"> 
		  <div align=\"center\">
		    Enter your affiliate id here
            <input name=\"affiliateID\" type=\"text\" size=\"40\"/>
		    <input name=\"submit\" type=\"submit\" value=\"Submit\" />
		    <br>
		  </div>
    	</form>
    </td>
  </tr>
</table>
";

if (isset ($_POST['Submit']) or isset ($_POST['affiliateID'])) {

	// Set up URL and IP Addresses

	$affiliateID = urlencode($_POST['affiliateID']);
	
	// Set up the CURL object

	$url = "http://www.oilfiltersonline.com/index.php?affiliateID=$affiliateID<br>";
	echo $url;
	}

?>
