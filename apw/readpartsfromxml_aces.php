<?php
/*Import .xml part data into oilfiltersonline.com VIART database.
Insert/replace new records into aaia and aaia_parts tables
Usage: At command line:
 php readpartsfromxml.php --file="/Users/tri/bench_mac/ofo/fgto.xml" --nodepath="App"
NOTE the capitazlied App
Check log file log_read_parts_.txt in the same folder for inserted records and/or errors encountered while executing script.
*/

//error_reporting(4);
ini_set('max_execution_time', 10800);
if (php_sapi_name() == 'cli' || strpos('ofo', $_SERVER['SERVER_NAME']) !== false) {
    define('IS_LOCAL', true);
} else {
    define('IS_LOCAL', false);
}

if (IS_LOCAL) {
    $root_pw = "rTrapok)1";
} else {
    $root_pw = "ifl@b";
}

define('IS_DEBUG', true);
//define('IS_DEBUG', false);

$con = new mysqli("localhost", "root", $root_pw);
/* check connection */
if ($con->connect_errno) {
    die("Connect failed: %s\n" . $con->connect_error);
}

define('AAIA_PCDB', 'aaia_pcdb');
define('AAIA_VCDB', 'aaia_vcdb');
define('VIART_DB', 'oilfiltersonline');
$makes = array();
$enginebases = array();
$fuelDeliveryTypes = array();
$engineDesignations = array();
$partTypes = array();
chdir(dirname(__FILE__));
$logFile = __DIR__ . '/log_read_parts_' . date("m_d_h_i_s") . ".txt";
$logFile = fopen($logFile, 'w+');
fwrite($logFile, "Start");
$log = "";

//init vars. Note: make sure $con is ready
function initVars()
{
    global $makes, $enginebases, $fuelDeliveryTypes, $engineDesignations, $partTypes, $log, $con, $logFile;
    if (!$con->select_db(AAIA_VCDB)) {
        $log .= "Unable to select AAIA_VCDB: " . mysqli_error($con);
        exit;
    }

    $sql = "SELECT *
	FROM   Make
	WHERE  1";

    $result = $con->query($sql);

    if (!$result) {
        $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
        exit;
    }

    if ($result->num_rows == 0) {
        $log .= "No rows found, nothing to print so exiting";
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $makes[$row['MakeID']] = strtoupper(trim($row['MakeName']));
    }

    $sql = "SELECT *
	FROM   EngineBase
	WHERE  1";

    $result = $con->query($sql);

    if (!$result) {
        $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
        exit;
    }

    if ($result->num_rows == 0) {
        $log .= "No rows found, nothing to print so am exiting";
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        array_push($enginebases, $row);
    }

    $sql = "SELECT *
	FROM   EngineDesignation
	WHERE  1";

    $result = $con->query($sql);

    if (!$result) {
        $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
        exit;
    }

    if ($result->num_rows == 0) {
        $log .= "No rows found, nothing to print so am exiting";
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $engineDesignations[$row['EngineDesignationID']] = $row['EngineDesignationName'];
    }

    $fuelDeliveryTypes['5'] = "FI";
    $fuelDeliveryTypes['6'] = "CARB";

    if (!$con->select_db(AAIA_PCDB)) {
        $log .= "Unable to select AAIA_PCDB: " . mysqli_error($con);
        exit;
    }

    $sql = "SELECT *
	FROM   Parts
	WHERE  1";

    $result = $con->query($sql);

    if (!$result) {
        $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
        exit;
    }

    if ($result->num_rows == 0) {
        $log .= "No rows found, nothing to print so am exiting";
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $partTypes[$row['PartTerminologyID']] = trim($row['PartTerminologyName']);
    }
    $result->free_result();
    fwrite($logFile, $log);
    $log = "";
}

/*
 * Input : 		<BaseVehicle id="1"/>
		<EngineBase id="13"/>
		<EngineVIN id="7"/>
		<Qty>1</Qty>
		<PartType id="6192"/>
 */
//returns an aaid ; creates an aaia if missing one
// populates $z with year make model cylinders, liter, engineVIN, parttype
function lookupAaia($z)
{
    $result = false;
    global $makes, $logFile, $log, $enginebases, $engineDesignations, $fuelDeliveryTypes, $con;

    if (!$con->select_db(VIART_DB)) {
        $log .= "Unable to select VIART_DB: " . mysqli_error($con);
        exit;
    }

    $con->select_db(AAIA_VCDB);
    $sql = "SELECT YearID, MAKE.MakeName AS make, MODEL.ModelName AS model FROM BaseVehicle AS BV JOIN Make AS MAKE ON BV.MakeID = MAKE.MakeID JOIN Model AS MODEL
  ON BV.ModelID = MODEL.ModelID
  WHERE BaseVehicleID = " . $z['basevehicle'] . ";";
    $result = $con->query($sql);
    if ($result->num_rows == 0) {
        $log .= "Model not found, nothing to print so exiting";
        return false;
    }
    if ($result->num_rows != 1) {
        $log .= "More than 1 basevehicle found";
        return false;
    }
    $year_make_model = $result->fetch_assoc();
    $year_id = trim($year_make_model['YearID']);
    $make = trim($year_make_model['make']);
    $model = trim($year_make_model['model']);

    //get cylinders, liter from enginebase
    if (isset($z['enginebase'])) {
        $sql = "SELECT * FROM EngineBase WHERE EngineBaseID = " . $z['enginebase'] . " LIMIT 1;";
        $result = $con->query($sql);
        if ($result->num_rows == 0) {
            $log .= "No enginebase found, ";

            return false;
        }
        $engineBase = $result->fetch_assoc();//Cylinders Liter
        $cylinders = $engineBase['Cylinders'];
        $liter = $engineBase['Liter'];
    }

    if (isset($z['enginevin']) && !empty($z['enginevin'])) {
        $sql = "SELECT * FROM EngineVIN WHERE EngineVINID = " . $z['enginevin'] . " LIMIT 1;";
        $result = $con->query($sql);
        if ($result->num_rows == 0) {
            $log .= "No engine vin found, ";
            return false;
        }
        $engineVIN = $result->fetch_assoc();//Cylinders Liter
        $z['enginevin'] = $engineVIN['EngineVINName'];
    }

    if (isset($z['parttype'])) {
        $sql = "SELECT * FROM aaia_pcdb.Parts WHERE PartTerminologyID = " . $z['parttype'] . " LIMIT 1;";
        $result = $con->query($sql);
        if ($result->num_rows == 0) {
            $log .= "No parttype found, ";
            return false;
        }
        $parttype = $result->fetch_assoc();//Cylinders Liter
        $z['parttype'] = $parttype['PartTerminologyName'];
    }

    $con->select_db(VIART_DB);

    $aaiaIds = array();
    $sql = "SELECT id FROM aaia where year = $year_id and make = '$make' and model = '$model' ";
    if (!empty($cylinders)) {
        $sql .= "AND cylinders LIKE '%$cylinders%' ";
    }
    if (!empty($liter)) {
        $sql .= "AND liters = '$liter' ";
    }
    $result = $con->query($sql);
    if (!$result) {
        $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
        return false;
    }

    if ($result->num_rows == 0) {
        $sql = "REPLACE INTO aaia (year,make,model,cylinders,liters,engineVIN) values('$year_id', '$make', '$model', '$cylinders', '$liter', '$engineVIN');";
        $result = $con->query($sql);
        if (!$result) {
            $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
            exit;
        }
        $aaiaId = $con->insert_id;
        $log .= "Inserted aaia id: $year_id $make $model. \r\n";

    } else {
        $row = $result->fetch_row();
        $aaiaId = $row[0];
    }
    
    if (is_object($result)) {
        $result->free_result();
    }
    fwrite($logFile, $log);
    $log = "";

    return $aaiaId;
}


/*
@$z: partInfo:

		<BaseVehicle id="1"/>
		<EngineBase id="13"/>
		<EngineVIN id="7"/>
		<Qty>1</Qty>
		<PartType id="6192"/>
		<Part>PAB9588</Part>

type
manufacturer
*/
function insertPartToOFO($z)
{
    $result = false;
    global $log, $logFile, $con;

    if (!$con->select_db(VIART_DB)) {
        $log .= "Unable to select VIART_DB: " . mysqli_error($con);
        exit;
    }

    $aaiaId = $z['aaia'];

    //check if part already exists
    $sql = "SELECT id FROM aaia_parts WHERE `part` = '$z[part]' AND `aaia` = '$aaiaId' AND `type` = '$z[parttype]' AND `manufacturer` = '$z[manufacturer]' ;";
    if (($con->query($sql)->num_rows) > 0) {
        //part exists
        $log .= "Part already exists ". json_encode($z) . "\n";
    };

    $pentius_desc = "Pentius UltraFlow Filters Feature:

*Advanced filtration technology
*Superior quality for today\'s high tech vehicles
*Are highly efficient in providing greater protection
*Prevent harmful contaminants from causing premature wear and/or damage
";

    $sql = "REPLACE INTO aaia_parts (`part`, `description`, `aaia`, `type`, `manufacturer`) " .
        "VALUES('$z[part]', '$pentius_desc', '$aaiaId', '$z[parttype]', '$z[manufacturer]') ; ";

    $result = $con->query($sql);

    if (!$result) {
        $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
        exit;
    }

    $log .= "Inserted part id: " . $con->insert_id . "\r\n";
    $result = true;


    fwrite($logFile, $log);
    $log = "";

    return $result;
}

$longopts = array(
    "file:",     // Required value
    "nodepath:"    // Optional value
);
$options = getopt(null, $longopts);
//DEBUG
//  $options['file']="/Users/tri/bench_mac/ofo/fgto.xml";
//$options['file']="\\Users\\tri\\bench_mac\\ofo\\fgto.xml";
//  $options['nodepath'] = "App";
//END DEBUG
if (sizeof($options) !== 2) {
    if (!isset($options['file'])) {
        $options['file'] = "data/pentius.xml";
    }

    if (!isset($options['nodepath'])) {
        $options['nodepath'] = 'App';
    }
}
// var_dump($options);

$xml = simplexml_load_file($options['file']);
if ($xml === false) {
    echo "Can not read file " . $options['file'] . "\n";
    die(-1);
};
$apps = $xml->children();
//prepare lookups
initVars();

$z = array();

$appNodes = $apps->$options['nodepath'];
$k = 0;
for ($k = 0;$k <= sizeof($appNodes);$k++) {

   if (IS_DEBUG && $k > 1){
       break;
   }
    $app = $appNodes[$k];
    /*
     * 		<BaseVehicle id="1"/>
		<EngineBase id="13"/>
		<EngineVIN id="7"/>
		<Qty>1</Qty>
		<PartType id="6192"/>
		<Part>PAB9588</Part>
     */
    $z['basevehicle'] = (string)$app->BaseVehicle->attributes();
    $z['enginebase'] = (string)$app->EngineBase->attributes();
    $z['enginevin'] = (string)$app->EngineVIN->attributes();
    $z['qty'] = (string)$app->Qty;
    $z['parttype'] = (string)$app->PartType->attributes();
    $z['part'] = (string)$app->Part;

    $z['type'] = $z['parttype'];
    $z['manufacturer'] = (string)$xml->Header->Company[0];//
    if ($z['manufacturer'] == "Pentius USA, Inc") {
        $z['manufacturer'] = "Pentius";
    }
    $z['aaia'] = lookupAaia($z);
    // var_dump($z);
    insertPartToOFO($z);
}

$con->close();
unset($makes, $enginebases, $fuelDeliveryTypes, $engineDesignations, $partTypes);
fwrite($logFile, $log . "\nStop");
fclose($logFile);