<?

$fp = fopen (dirname(__FILE__) . '/apw.csv', 'w+');//This is the file where we save the information
$ch = curl_init('http://www.apwks.com/oilfilteronline/APW_Knoxseeman_033110.csv');//Here is the file we are downloading
curl_setopt($ch, CURLOPT_TIMEOUT, 50);
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);
fclose($fp);
?>