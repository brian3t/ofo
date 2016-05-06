<?

ini_set('display_errors',1);
error_reporting(E_ALL);


$fp = fopen (dirname(__FILE__) . '/apw.csv', 'w+');

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_URL, 'ftp://ftp.apwks.com/APW_Knoxseeman.csv'); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY); 
curl_setopt($ch, CURLOPT_USERPWD, 'apwfil:fil0106');

curl_exec($ch); 
curl_close($ch);


//csv_file_to_mysql_table('apw.csv', 'apw');

function csv_file_to_mysql_table($source_file, $target_table, $max_line_length=10000) {
	
	$link = mysql_connect("localhost", "root", "ifl@b") or die(mysql_error());
	$db = mysql_select_db('oilfiltersonline', $link);
	mysql_query("TRUNCATE TABLE $target_table") or die(mysql_error());
	
    if (($handle = fopen("$source_file", "r")) !== FALSE) { 
        $columns = fgetcsv($handle, $max_line_length, ","); 
        foreach ($columns as &$column) { 
            $column = str_replace(" ","",$column); 
        } 
        $insert_query_prefix = "INSERT INTO $target_table (".join(",",$columns).")\nVALUES"; 
        while (($data = fgetcsv($handle, $max_line_length, ",")) !== FALSE) { 
            while (count($data)<count($columns)) 
                array_push($data, NULL); 
            $query = "$insert_query_prefix (".join(",",quote_all_array($data)).");";
            //echo $query;
            mysql_query($query); 
        } 
        fclose($handle);
    } 
}
function quote_all_array($values) { 
    foreach ($values as $key=>$value) 
        if (is_array($value)) 
            $values[$key] = quote_all_array($value); 
        else 
            $values[$key] = quote_all($value); 
    return $values; 
} 
	
function quote_all($value) { 
    if (is_null($value)) 
        return "NULL"; 

    $value = "'" . $value . "'"; 
    return $value; 
}

?>
