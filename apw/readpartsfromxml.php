<?php
/*Import .xml part data into oilfiltersonline.com VIART database.
Insert/replace new records into aaia and aaia_parts tables
Usage: At command line:
 php readpartsfromxml.php --file="/Users/tri/bench_mac/ofo/fgto.xml" --nodepath="App"
Check log file log_read_parts_.txt in the same folder for inserted records and/or errors encountered while executing script.
*/

//error_reporting(4);
ini_set('max_execution_time', 10800);
if ($_SERVER['PWD'] == '/home/tri' || strpos('localhost', $_SERVER['SERVER_NAME']) != false) {
    define('IS_LOCAL', true);
} else {
    define('IS_LOCAL', false);
}

if (IS_LOCAL) {
    $root_pw = "";
} else {
    $root_pw = "ifl@b";
}

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
    if (! $con->select_db(AAIA_VCDB)) {
        $log .= "Unable to select AAIA_VCDB: " . mysqli_error($con);
        exit;
    }

    $sql = "SELECT *
	FROM   Make
	WHERE  1";

    $result = $con->query($sql);

    if (! $result) {
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

    if (! $result) {
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

    if (! $result) {
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

    if (! $con->select_db(AAIA_PCDB)) {
        $log .= "Unable to select AAIA_PCDB: " . mysqli_error($con);
        exit;
    }

    $sql = "SELECT *
	FROM   Parts
	WHERE  1";

    $result = $con->query($sql);

    if (! $result) {
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

//returns an array of array of aaia ids, e.g.
//array (size=6)
// 0 => 
//        array (size=1)
//          0 => string '1503797' (length=7)
function lookupAaia($app)
{
    $result = false;
    global $makes, $logFile, $log, $enginebases, $engineDesignations, $fuelDeliveryTypes, $con;

    if (! $con->select_db(VIART_DB)) {
        $log .= "Unable to select VIART_DB: " . mysqli_error($con);
        exit;
    }
    if (!property_exists($app, 'Years')){
        $log .= "No year for this app";
        return null;
    }
    $appYear = $app->Years->attributes();
    $yearFrom = (string) $appYear['from'];
    $yearTo = (string) $appYear['to'];
    $appMake = $app->Make->attributes();
    $makeName = $makes[(string) $appMake['id']];
    $appModel = $app->Model->attributes();
    $appModelID = (string) $appModel['id'];
    $con->select_db(AAIA_VCDB);
    $sql = "SELECT ModelName FROM Model where modelid = " . $appModelID . ";";
    $result = $con->query($sql);
    if ($result->num_rows == 0) {
        $log .= "Model not found, nothing to print so am exiting";

        return false;
    }
    if ($result->num_rows != 1) {
        $log .= "More than 1 modelname found";

        return false;
    }
    $modelName = $result->fetch_row();
    $modelName = trim($modelName[0]);

    //get cylinders, liter from enginebase
    $appEngineBase = (property_exists($app, 'EngineBase') && method_exists($app->EngineBase, 'attributes')) ? $app->EngineBase->attributes() : null;
    if (isset($appEngineBase['id'])) {
        $engineBaseId = (string) $appEngineBase['id'];

        $sql = "SELECT * FROM EngineBase WHERE EngineBaseID = " . $engineBaseId . " limit 1;";
        $result = $con->query($sql);
        if ($result->num_rows == 0) {
            $log .= "No enginebase found, ";

            return false;
        }
        $engineBase = $result->fetch_assoc();//Cylinders Liter
        $cylinders = $engineBase['BlockType'] . $engineBase['Cylinders'];
        $liter = $engineBase['Liter'];
    }

    $appEngineDesignation = (property_exists($app, 'EngineDesignation') && method_exists($app->EngineDesignation, 'attributes')) ? $app->EngineDesignation->attributes() : null;
    if (isset($appEngineDesignation['id'])) {
        $engineDesignationId = (string) $appEngineDesignation['id'];
        $engineVIN = trim($engineDesignations[$engineDesignationId]);
    }
    $appFuelDev = (property_exists($app, 'FuelDeliveryType') && method_exists($app->FuelDeliveryType, 'attributes')) ? $app->FuelDeliveryType->attributes() : null;
    if (isset($appFuelDev['id'])) {
        $fuelDevId = (string) $appFuelDev['id'];
        $injectionType = $fuelDeliveryTypes[$fuelDevId];
    }

    $con->select_db(VIART_DB);

    $aaiaIds = array();
    for ($year = $yearFrom;$year <= $yearTo;$year ++) {
        $sql = "SELECT id FROM aaia where year = '$year' and make = '$makeName' and model = '$modelName' ";
        if (! empty($injectionType)) {
            $sql .= "AND injectionType = '$injectionType'  ";
        }
        if (! empty($cylinders)) {
            $sql .= "AND cylinders = '$cylinders' ";
        }
        if (! empty($liter)) {
            $sql .= "AND liters = '$liter' ";
        }
        if (! empty($engineVIN)) {
            $sql .= "AND engineVIN = '$engineVIN' ";
        }
        $result = $con->query($sql);
        if (! $result) {
            $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
            return false;
        }

        if ($result->num_rows == 0) {
            $sql = "REPLACE INTO aaia (year,make,model,cylinders,liters,fuelType,injectionType,engineVIN) values('$year', '$makeName', '$modelName', '$cylinders', '$liter','GAS', '$injectionType', '$engineVIN');";
            $result = $con->query($sql);
            if (! $result) {
                $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
                exit;
            }
            array_push($aaiaIds, $con->insert_id);
            $log .= "Inserted aaia id: $year $makeName $modelName. \r\n";

        } else {
            $row = $result->fetch_row();
            array_push($aaiaIds, $row[0]);
        }
    }

    if (is_object($result)) {
        $result->free_result();
    }
    fwrite($logFile, $log);
    $log = "";

    return $aaiaIds;
}


/*
@$z: partInfo:
array (size=6)
  'part' => string 'TG7317' (length=6)
  'description' => string 'Spin-On Full Flow' (length=17)
  'notes' => string '' (length=0)
  'aaia' =>
    array (size=6)
      0 =>
        array (size=1)
          0 => string '1503797' (length=7)
      1 =>
        array (size=1)
          0 => string '1503798' (length=7)
      2 =>
        array (size=1)
          0 => string '1503799' (length=7)
      3 =>
        array (size=1)
          0 => string '1503800' (length=7)
      4 =>
        array (size=1)
          0 => string '1503801' (length=7)
      5 =>
        array (size=1)
          0 => string '1503802' (length=7)
  'type' => string 'Engine Oil Filter' (length=17)
  'manufacturer' => string 'FramGroup' (length=9)
*/
function insertPartToOFO($z)
{
    $result = false;
    global $log, $logFile, $con;

    if (! $con->select_db(VIART_DB)) {
        $log .= "Unable to select VIART_DB: " . mysqli_error($con);
        exit;
    }
    foreach ($z['aaia'] as $key => $aaiaId) {

        //check if part already exists
        $sql = "SELECT id FROM aaia_parts WHERE `part` = '$z[part]' and `description` = '$z[description]' " .
               " AND `notes` = '$z[notes]' AND `aaia` = '$aaiaId' AND `type` = '$z[type]' AND `manufacturer` = '$z[manufacturer]' ;";
        if (($con->query($sql)->num_rows) > 0) {
            continue;
        };

        $sql = "REPLACE INTO aaia_parts (`part`, `description`, `notes`, `aaia`, `type`, `manufacturer`) " .
               "VALUES('$z[part]', '$z[description]', '$z[notes]', '$aaiaId', '$z[type]', '$z[manufacturer]') ; ";

        $result = $con->query($sql);

        if (! $result) {
            $log .= "Could not successfully run query ($sql) from DB: " . mysqli_error($con);
            exit;
        }

        $log .= "Inserted part id: " . $con->insert_id . "\r\n";
        $result = true;

    }
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
    $log .= ("Needs 2 arguments!!!");
    die(- 1);
}
// var_dump($options);

$xml = simplexml_load_file($options['file']);
if ($xml === false) {
    echo "Can not read file " . $options['file'] . "\n";
    die(- 1);
};
$apps = $xml->children();
//prepare lookups
initVars();

$z = array();

$appNodes = $apps->$options['nodepath'];
$k = 0;
for ($k = 0;$k <= sizeof($appNodes);$k ++) {

//    if (IS_LOCAL && $k > 1){
//        break;
//    }
    $app = $appNodes[$k];
    $z['part'] = (string) $app->Part;
    $z['description'] = (string) $app->MfrLabel;
    $z['notes'] = "";
    if (property_exists($app, 'Note')) {
        foreach ($app->Note as $note) {
            $z['notes'] .= ". " . (string) $note;
        }
    }
    $z['aaia'] = lookupAaia($app);
    $appPartType = $app->PartType->attributes();
    $appPartType = (string) $appPartType['id'];
    $z['type'] = $partTypes[$appPartType];
    $z['manufacturer'] = (string) $xml->Header->Company[0];//
    if ($z['manufacturer'] == "FramGroup") {
        $z['manufacturer'] = "Fram";
    }
    // var_dump($z);
    insertPartToOFO($z);
}

$con->close();
unset($makes, $enginebases, $fuelDeliveryTypes, $engineDesignations, $partTypes);
fwrite($logFile, $log . "\nStop");
fclose($logFile);