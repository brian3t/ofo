<?
require('js/xajax_core/xajax.inc.php');
if (isset($_GET["app"])){
    $url = "/?app=" . $_GET["app"];
} else {
    $url = "/";
}
$xajax = new xajax($url);
// $xajax->configure('debug',true);
global $current_db, $current_view, $effects_script;

class myXajaxResponse extends xajaxResponse
{

    function addCreateOptions($sSelectId, $options)
    {
        $this->script("document.getElementById('" . $sSelectId . "').length=0");
        if (sizeof($options) > 0){
            foreach ($options as $option){
                $this->script("addOption('" . $sSelectId . "','" . $option['txt'] . "','" . $option['val'] . "');");
            }
        }
    }
}

if ($_GET["app"] == "motorcycle"){
    $current_db = "motorcycle";
    $current_view = "motorcycle_view";
} else if ($_GET["app"] == "heavyduty"){
    $current_db = "heavy_duty";
    $current_view = "heavy_duty_view";
} else if ($_GET["app"] == "cross"){
} else {
    $current_db = "aaia";
    $current_view = "aaia_view";
}

$effects_script = 'imagePreview();';

if ($_GET["app"] == "heavyduty"){
    $makesRs = mysql_query("SELECT DISTINCT(make) FROM oilfiltersonline." . $current_db . " ORDER BY make ASC") or die(mysql_error());
    while ($rs = mysql_fetch_assoc($makesRs)) {
        $make_options .= '<option value="' . $rs["make"] . '">' . $rs["make"] . '</option>';
    }
} else {
    $yearRs = mysql_query("SELECT DISTINCT(year) FROM oilfiltersonline." . $current_db . " ORDER BY year DESC") or die(mysql_error());
    while ($rs = mysql_fetch_assoc($yearRs)) {
        $year_options .= '<option value="' . $rs["year"] . '">' . $rs["year"] . '</option>';
    }
}
$form_code = '<h2>Shop By Vehicle</h2>
        <form name="form1" id="form1" method="POST" action="sessionvars.php">
        <select name="app" id="app">
          <option value="aaia">Cars &amp; Trucks</option>';
if ($_GET["app"] == "motorcycle"){
    $form_code .= '  <option value="motorcycle" selected>Motorcycles</option>';
} else {
    $form_code .= '  <option value="motorcycle">Motorcycles</option>';
}
if ($_GET["app"] == "heavyduty"){
    $form_code .= '  <option value="heavyduty" selected>Heavy Duty</option>';
} else {
    $form_code .= '  <option value="heavyduty">Heavy Duty</option>';
}
$form_code .= '  </select>';
if ($_GET["app"] == "heavyduty"){
    $form_code .= '<select name="makeHD" id="makeHD">
            <option value="" selected>--Select Make--</option>' . $make_options . '
          </select>
          <select name="modelsHD" disabled="disabled" id="modelsHD">
            <option value="" selected>--Select Model--</option>
          </select>
          <select name="yearsHD" disabled="disabled" id="yearsHD">
            <option value="" selected>--Select Year--</option>
          </select>
          <select name="enginesHD" disabled="disabled" id="enginesHD">
            <option value="" selected>--Select Engine--</option>
          </select>';
} else {
    $form_code .= '<select name="years" id="years">
            <option value="" selected>--Select Year--</option>' . $year_options . '
          </select>
          <select name="make" id="make" disabled="disabled">
            <option value="" selected>--Select Make--</option>
          </select>';
    if ($_GET["app"] == "motorcycle"){
        $form_code .= '<select name="models" disabled="disabled" id="models" onChange="xajax_addengines_old(\'engines\', this.form.models.value, this.form.make.value, this.form.years.value)">
            <option value="" selected>--Select Model--</option>
          </select>';
    } else {
        $form_code .= '<select name="models" disabled="disabled" id="models" onChange="xajax_addengines(\'engines\', this.form.models.value, this.form.make.value, this.form.years.value)">
            <option value="" selected>--Select Model--</option>
          </select>';
    }

    $form_code .= '  <select name="engines" disabled="disabled" id="engines">
            <option value="" selected>--Select Engine--</option>
          </select>';
}
$form_code .= '</form>';

function addmodels($selectId, $year, $make)
{
    global $models, $current_db;
    $objResponse = new myXajaxResponse();
    $modelsRs = mysql_query(sprintf(
        "SELECT DISTINCT(model) FROM oilfiltersonline." . $current_db . " WHERE make = '%s' AND year = '%s' ORDER BY model ASC", $make, $year));
    $models[] = array("txt" => "--Select Model--", "val" => "");
    while ($rs = mysql_fetch_assoc($modelsRs)) {
        $models[] = array("txt" => $rs["model"], "val" => $rs["model"]);
    }
    $objResponse->script("$('#models option:contains(--)').attr('selected', true)");
    $objResponse->script("$('#models').removeAttr('disabled');");
    $objResponse->script("$('#engines').attr('disabled', 'disabled');");
    $objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
    $objResponse->addCreateOptions($selectId, $models);
    return $objResponse;
}

function addmodelsHD($selectId, $make)
{
    global $models, $current_db;
    $objResponse = new myXajaxResponse();
    $modelsRs = mysql_query(sprintf(
        "SELECT DISTINCT(model) FROM oilfiltersonline." . $current_db . " WHERE make = '%s' ORDER BY model ASC", $make));
    $models[] = array("txt" => "--Select Model--", "val" => "");
    while ($rs = mysql_fetch_assoc($modelsRs)) {
        $models[] = array("txt" => $rs["model"], "val" => $rs["model"]);
    }
    $objResponse->script("$('#modelsHD option:contains(--)').attr('selected', true)");
    $objResponse->script("$('#modelsHD').removeAttr('disabled');");
    $objResponse->script("$('#yearsHD').attr('disabled', 'disabled');");
    $objResponse->script("$('#yearsHD option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#enginesHD').attr('disabled', 'disabled');");
    $objResponse->script("$('#enginesHD option:contains(Select)').attr('selected', true)");
    $objResponse->addCreateOptions($selectId, $models);
    return $objResponse;
}

function addmakes($selectId, $year)
{
    global $makes, $current_db;
    $objResponse = new myXajaxResponse();
    $makesRs = mysql_query(sprintf
    ("SELECT DISTINCT(make) FROM oilfiltersonline." . $current_db . " WHERE year = '%s' ORDER BY make ASC", $year));
    $makes[] = array("txt" => "--Select Make--", "val" => "");
    while ($rs = mysql_fetch_assoc($makesRs)) {
        $makes[] = array("txt" => $rs["make"], "val" => $rs["make"]);
    }
    $objResponse->script("$('#models option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#make').removeAttr('disabled');");
    $objResponse->script("$('#models').attr('disabled', 'disabled');");
    $objResponse->script("$('#engines').attr('disabled', 'disabled');");
    $objResponse->addCreateOptions($selectId, $makes);
    return $objResponse;
}

function addyearsHD($selectId, $make, $model)
{
    global $years, $current_db;
    $objResponse = new myXajaxResponse();
    $yearsRs = mysql_query(sprintf
    ("SELECT DISTINCT(year) FROM oilfiltersonline." . $current_db . " WHERE model = '%s' AND make = '%s' ORDER BY year DESC", $model, $make));
    $years[] = array("txt" => "--Select Year--", "val" => "");
    while ($rs = mysql_fetch_assoc($yearsRs)) {
        $years[] = array("txt" => $rs["year"], "val" => $rs["year"]);
    }
    $objResponse->script("$('#yearsHD option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#yearsHD').removeAttr('disabled');");
    $objResponse->script("$('#enginesHD option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#enginesHD').attr('disabled', 'disabled');");
    $objResponse->addCreateOptions($selectId, $years);
    return $objResponse;
}

function addengines($selectId, $model, $make, $year)
{
    global $engines, $current_db;
    $objResponse = new myXajaxResponse();
    if ($current_db == "aaia"){
        $enginesRs = mysql_query(sprintf
        ("SELECT id, concat_ws('-',cylinders,concat(liters,'L'),engineVIN,injectionType,notes1,notes2,notes4,fuelType) AS engine FROM oilfiltersonline." . $current_db . " WHERE model = '%s' AND make = '%s' AND year = '%s' ORDER BY engine DESC", $model, $make, $year));
    } else {
        $enginesRs = mysql_query(sprintf
        ("SELECT DISTINCT(engine) FROM oilfiltersonline." . $current_db . " WHERE model = '%s' AND make = '%s' AND year = '%s' ORDER BY engine DESC", $model, $make, $year));
    }
    $engines[] = array("txt" => "--Select Engine--", "val" => "");
    while ($rs = mysql_fetch_assoc($enginesRs)) {
        $engines[] = array("txt" => $rs["engine"], "val" => $rs["id"]);
    }
    $objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#engines').removeAttr('disabled');");
    $objResponse->addCreateOptions($selectId, $engines);
    return $objResponse;
}

function addengines_old($selectId, $model, $make, $year)
{
    global $engines, $current_db;
    $objResponse = new myXajaxResponse();
    $enginesRs = mysql_query(sprintf
    ("SELECT DISTINCT(engine) FROM oilfiltersonline." . $current_db . " WHERE model = '%s' AND make = '%s' AND year = '%s' ORDER BY engine DESC", $model, $make, $year));
    $engines[] = array("txt" => "--Select Engine--", "val" => "");
    while ($rs = mysql_fetch_assoc($enginesRs)) {
        $engines[] = array("txt" => $rs["engine"], "val" => $rs["engine"]);
    }
    $objResponse->script("$('#engines option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#engines').removeAttr('disabled');");
    $objResponse->addCreateOptions($selectId, $engines);
    return $objResponse;
}

function addenginesHD($selectId, $model, $make, $year)
{
    global $engines, $current_db;
    $objResponse = new myXajaxResponse();
    $enginesRs = mysql_query(sprintf
    ("SELECT DISTINCT(engine) FROM oilfiltersonline." . $current_db . " WHERE model = '%s' AND make = '%s' AND year = '%s' ORDER BY engine DESC", $model, $make, $year));
    $engines[] = array("txt" => "--Select Engine--", "val" => "");
    while ($rs = mysql_fetch_assoc($enginesRs)) {
        $engines[] = array("txt" => $rs["engine"], "val" => $rs["engine"]);
    }
    $objResponse->script("$('#enginesHD option:contains(Select)').attr('selected', true)");
    $objResponse->script("$('#enginesHD').removeAttr('disabled');");
    $objResponse->addCreateOptions($selectId, $engines);
    return $objResponse;
}

function showdiv($model, $make, $year, $id, $engine)
{
    global $part, $current_view, $effects_script;
    $pcdb = new mysqli('localhost', 'root', 'rTrapok)1', 'aaia_pcdb');
    $objResponse = new myXajaxResponse();
    if ($current_view == "aaia_view"){
        $partRs = mysql_query(sprintf
        ("SELECT * FROM oilfiltersonline." . $current_view . " WHERE aaia = '%s' ORDER BY field(type,'Engine Oil Filter','Air Filter','Cabin Air Filter','Brake Pads','Suspension','Fuel Filter','Transmission Filters','Spark Plug','Oxygen Sensors','PCV Valves and Breather'), price ASC ", $id));
    } else {
        $partRs = mysql_query(sprintf
        ("SELECT * FROM oilfiltersonline." . $current_view . " WHERE model = '%s' AND make = '%s' AND year = '%s' AND engine = '%s' ", $model, $make, $year, $engine));
    }
    while ($rs = mysql_fetch_assoc($partRs)) {
        //trying to pull part type from AAIA PCDB
        $type_id = $rs['type'];
        if (is_numeric($type_id)){
            if ($query_result = $pcdb->query("SELECT PartTerminologyName FROM aaia_pcdb.Parts WHERE PartTerminologyID = '$type_id' LIMIT 1", MYSQLI_USE_RESULT)){
                $type_desc = $query_result->fetch_row();
                $type_desc = $type_desc[0];
                $query_result->close();
            }
            $rs['type'] = $type_desc;
        }
        $parts[] = $rs;
        $categories[] = $rs["type"];
    }
    $categories = array_unique($categories);
    $application_code = '<div class="finder-wrapper">';
    $application_code .= file_get_contents('./custom/5dollarShippingSmall.html');
    $application_code .= '<div id="searchVehicle">
              Search Results For:
              <span class="currentVehicle">' . $year . ' ' . $make . ' ' . $model . '</span>
              <a class="showHide button" id="hide">Hide Results</a>
             </div>
               <div id="categoryWrapper">
                <div id="selectCategory">
                  Select Your Category
                </div>
                <ul class="partCategories">';
    foreach ($categories as $cat){
        $application_code .= '    <li class="' . str_replace(" ", "", $cat) . '">' . $cat . '</li>';
    }
    $application_code .= '    </ul>
              </div>';
    $count = 1;
    $previous_part_group = "";
    foreach ($parts as $rs){
        if ($rs["use_stock_level"] == 1 && $rs["stock_level"] > 0){
            $availability = $rs["shipping_in_stock"];
        } else if ($use_stock_level != 1){
            $availability = $rs["shipping_out_stock"];
        } else {
            $availability = $rs["shipping_out_stock"];
        }
        $availDesc = mysql_query(sprintf("SELECT shipping_time_desc FROM oilfiltersonline_test_store.va_shipping_times WHERE shipping_time_id= %s LIMIT 1", $availability));
        list($availableDesc) = mysql_fetch_row($availDesc);
        $price = $rs["price"];
        $part = $rs["part"];
        $description = htmlentities($rs["description"], ENT_QUOTES);
        $manufacturer = $rs["manufacturer"];
        $friendly_url = $rs["friendly_url"];
        $content = $rs["content"];
        $retail = $rs["retail_price"];
        if ($rs["thumbnail"]){
            $thumbnail = $rs["thumbnail"];
        } else {
            //try pulling from manufacturer and part number
            $thumbnail_path = "/images/products/big/" . strtolower($rs['manufacturer']) . '/' . $rs['part'] . ".jpg";
            if (file_exists(dirname(__DIR__) . $thumbnail_path)){
                $thumbnail = $thumbnail_path;
            } else {
                $thumbnail = 'images/no_image_tiny.gif';
            }
        }
        if ($rs["bigimage"]){
            $bigimage = $rs["bigimage"];
        } else {
            $thumbnail_path = "/images/products/big/" . strtolower($rs['manufacturer']) . '/' . $rs['part'] . ".jpg";
            if (file_exists(dirname(__DIR__) . $thumbnail_path)){
                $bigimage = $thumbnail_path;
            } else {
                $bigimage = 'images/no_image_tiny.gif';
            }
        }
        $part_group = $rs["type"];

        if ($part_group != $previous_part_group){
            $application_code .= '<div class="finder_header_row">
                  <a class="left" name="' . str_replace(" ", "", $part_group) . '" id="' . str_replace(" ", "", $part_group) . '">' . $part_group . '</a>
                  <a class="backToTop right" ><span>Back To Top </span><img src="images/up.gif" /></a>
                  </div>';
        }

        if (is_null($rs["price"]) || $rs["Active"] == 0){
            $application_code .= '<div class="finder-row">
                  <div class="part-description">
                    <h3>
                      <img src="' . $thumbnail . '"  title="' . $bigimage . '" class="preview thumbnail" />
                      <span class="' . $manufacturer . '">' . $manufacturer . '</span> ' . $description . ' - ' . $part .
                '</h3>
                    <p>' . $content . '</p>
                  </div>
                  <div class="finder-contact"><a href="/articles.php?category_id=36">Contact Us For Availability</a></div>
                  </div>';
        } else {
            $application_code .= '<div class="finder-row">
                  <div class="part-description">
                    <h3>
                    <a class="parts-highlight" target="_parent" href="' . $friendly_url . '">
                      <img src="' . $thumbnail . '" title="' . $bigimage . '" class="preview thumbnail" />
                    <span class="' . $manufacturer . '">' . $manufacturer . '</span> ' . $description . ' - ' . $part . '</a>
                    </h3>
                    <p>' . $content . '</p>
                    <p class="availability"><strong>Availability: </strong>' . $availableDesc . '</p>
                  </div>
                  <div class="part-price">

                    <div class="salesBlock">Our Price:  $' . $price . '</div>

                    <form name="form' . $count . '" method="POST" action="sessionvars.php" class="addToCart">
                    <input type="hidden" value="' . $part . '" name="part" />
                    <input type="hidden" value="' . str_replace(" ", "", $part_group) . '" name="type" />
                    Quantity: <input name="quantity" value="1" size="4"/><input class="button finderButton" type="submit" value="Add To Cart"/>
                    </form>
                  </div>
                  </div>';
            $count++;
        }
        $previous_part_group = $rs["type"];
    }
    $application_code .= '</div>';
    $application_code = preg_replace('/\s\s+/', ' ', $application_code);
    // Store the vehicle and part results in case the user comes back to see their parts
    $_SESSION["application"] = $application_code;
    $_SESSION["year"] = $year;
    $_SESSION["make"] = $make;
    $_SESSION["model"] = $model;
    $_SESSION["engine"] = $engine;
    $_SESSION["search"] = '';
    $_SESSION["count"] = $count - 1;
    $objResponse->script('$(".finder-wrapper").remove();');
    $objResponse->script('$("#searchVehicle").remove();');
    $objResponse->script('$("#vehicleSelector").animate({width: "200px"}, 300);');
    $objResponse->script('$(".mainTableCellCenter").prepend(\'' . $application_code . '\');');
    $objResponse->assign("currentApp", "innerHTML", '<div class="application"><p><img src="images/delete.png" alt="Delete" title="Delete Vehicle" class="deleteVehicle" /><span class="currentVehicle">' . $_SESSION["year"] . ' ' . $_SESSION["make"] . ' ' . $_SESSION["model"] . ' </span><button class="showHide button">Hide</button></p></div>');
    $objResponse->assign("currentCross", "innerHTML", "");
    $objResponse->assign("count", "value", ($count - 1));
    $objResponse->script($effects_script);
    $pcdb->close();
    return $objResponse;
}

function getcross($partnumsubmit)
{
    global $effects_script;
    if ($partnumsubmit == ""){
        exit();
    } //Make sure they submitted something

    $objResponse = new xajaxResponse();
    $crossRs = mysql_query("SELECT * FROM oilfiltersonline.crossref_view WHERE C_part LIKE '%" . trim($partnumsubmit) . "%'");

    if (mysql_num_rows($crossRs) > 0){

        $application_code = '<div class="finder-wrapper">
               <div id="searchVehicle">
                Cross Reference Results For:
                <span class="currentVehicle">' . $partnumsubmit . '</span>
                <a class="showHide button" id="hide">Hide Results</a>
              </div>
               <table width="100%" border="0" cellspacing="0" cellpadding="0" class="finder_header_row">
                <tr>
                  <td width="20%">Manufacturer</td>
                  <td width="15%">MFR Part #</strong></td>
                  <td width="20%">FRAM Part #</td>
                  <td width="15%">Our Price</td>
                  <td width="30%"></td>
                </tr>
              </table>';
        $count = 1;
        $row = 1;
        $application_code .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="finder_row">';
        while ($rs = mysql_fetch_assoc($crossRs)) {
            if ($row & 1){
                $row_class = "odd";
            } else {
                $row_class = "even";
            }
            if ($rs["thumbnail"]){
                $thumbnail = $rs["thumbnail"];
            } else {
                $thumbnail_path = "/images/products/big/" . strtolower($rs['manufacturer']) . $rs['part'] . ".jpg";
                if (file_exists(dirname(__DIR__) . $thumbnail_path)){
                    $thumbnail = $thumbnail_path;
                } else {
                    $thumbnail = 'images/no_image_tiny.gif';
                }
            }
            if ($rs["bigimage"]){
                $bigimage = $rs["bigimage"];
            } else {
                if (file_exists(dirname(__DIR__) . $thumbnail_path)){
                    $bigimage = $thumbnail_path;
                } else {
                    $bigimage = 'images/no_image_tiny.gif';
                }
            }
            $application_code .= '<tr class="' . $row_class . '">
                  <td width="20%">' . $rs["Competitor"] . '</td>
                  <td width="15%">' . $rs["C_Part"] . '</td>
                  <td width="20%">
                    <img title="' . $bigimage . '" class="preview thumbnail" src="' . $thumbnail . '"/>
                    <a href="' . $rs["friendly_url"] . '">' . $rs["Part"] . '</a>
                  </td>';
            if (is_null($rs["price"])){
                $application_code .= '<td width="15%"></td>
                    <td width="30%">
                    <div><a href=\"articles.php?category_id=36\">Contact Us For Availability</a></div>
                    </td>
                </tr>';
            } else {
                $application_code .= '<td width="15%"><span class="ourPrice">$' . $rs["price"] . '</span></td>
                  <td width="30%">
                  <form name="form' . $count . '" method="POST" action="sessionvars.php" class="addToCart">
                    <input type="hidden" value="' . $rs["Part"] . '" name="part" />
                    Qty: <input name="quantity" value="1" size="4"/>&nbsp;&nbsp;<input class="button" type="submit" value="Add To Cart"/>
                  </form>
                  </td>
                </tr>';
                $count++;
            }
            $row++;
        }
        $application_code .= '</table></div>';
        $_SESSION["search"] = $partnumsubmit;
        $application_code = preg_replace('/\s\s+/', ' ', $application_code);
        $objResponse->assign("currentApp", "innerHTML", "");
        $objResponse->assign("currentCross", "innerHTML", '<div class="application"><p><img src="images/delete.png" alt="Delete" title="Delete Vehicle" class="deleteVehicle" /><span class="currentVehicle">' . $_SESSION["search"] . ' </span> <button class="showHide button">Hide</button></p></div>');
        $objResponse->script('$(".finder-wrapper").remove();');
        $objResponse->script('$("#searchVehicle").remove();');
        $objResponse->script('$("#vehicleSelector").animate({width: "200px"}, 300);');
        $objResponse->script('$(".mainTableCellCenter").prepend(\'' . $application_code . '\');');
    } else {
        $objResponse->assign("currentCross", "innerHTML", "Sorry, there were no matches found. Try your search again with only a segment of the part number.");
        $objResponse->script('$(".finder-wrapper").remove();');
        $objResponse->script('$("#searchVehicle").remove();');
    }
    $_SESSION["year"] = '';
    $_SESSION["make"] = '';
    $_SESSION["model"] = '';
    $_SESSION["engine"] = '';
    $_SESSION["application"] = $application_code;
    $_SESSION["count"] = $count - 1;
    $objResponse->script($effects_script);
    return $objResponse;
}

function destroySession()
{
    unset($_SESSION['application']);
    unset($_SESSION['make']);
    unset($_SESSION['model']);
    unset($_SESSION['year']);
    unset($_SESSION['engine']);
    session_destroy();
}

$xajax->registerFunction("destroySession");
$xajax->registerFunction("getcross");
$xajax->registerFunction("addmodels");
$xajax->registerFunction("addyearsHD");
$xajax->registerFunction("addmodelsHD");
$xajax->registerFunction("addmakes");
$xajax->registerFunction("addengines");
$xajax->registerFunction("addengines_old");
$xajax->registerFunction("addenginesHD");
$xajax->registerFunction("showdiv_old");
$xajax->registerFunction("showdiv");
$xajax->processRequest();
$xajax->printJavascript("js/");
?>
<script type="text/javascript">
    $(document).ready(function () {
        <? if ($_SESSION["application"]){
        //echo '$(".mainTableCellCenter").prepend(\''.$_SESSION["application"].'\');';
    } ?>
        $(".finder-wrapper").addClass("hide");

        var ShowHide = function () {
            if ($(".finder-wrapper").hasClass("hide")) {
                $(".finder-wrapper").removeClass("hide");
                $(".application").effect("transfer", {to: $(".finder-wrapper")}, 400);
                $(".showHide").text("Hide");
            } else {
                $(".finder-wrapper").effect("transfer", {to: $(".application")}, 400);
                $(".finder-wrapper").addClass("hide");
                $(".showHide").text("Show");
            }
        };

        var DeleteVehicle = function () {
            xajax_destroySession();
            $(".finder-wrapper").remove();
            $(".application").remove();
            $("#form1 select").each(function () {
                if ($(this).is("#app")) {
                } else {
                    $(this).val($("#" + $(this).attr("id") + " option:first").val());
                }
            });
            $("#make").attr("disabled", "disabled");
            $("#models").attr("disabled", "disabled");
            $("#engines").attr("disabled", "disabled");
            $("#yearsHD").attr("disabled", "disabled");
            $("#modelsHD").attr("disabled", "disabled");
            $("#enginesHD").attr("disabled", "disabled");
        };

        $(".showHide,.currentVehicle").live('click', ShowHide);
        $(".deleteVehicle").live('click', DeleteVehicle);

        $("#years").change(function () {
            xajax_addmakes("make", $("#years").val());
        });
        $("#make").change(function () {
            xajax_addmodels("models", $("#years").val(), $("#make").val());
        });
        $("#makeHD").change(function () {
            xajax_addmodelsHD("modelsHD", $("#makeHD").val());
        });
        $("#modelsHD").change(function () {
            xajax_addyearsHD("yearsHD", $("#makeHD").val(), $("#modelsHD").val());
        });
        $("#yearsHD").change(function () {
            xajax_addenginesHD("enginesHD", $("#modelsHD").val(), $("#makeHD").val(), $("#yearsHD").val());
        });
        $("#enginesHD").change(function () {
            xajax_showdiv($("#modelsHD").val(), $("#makeHD").val(), $("#yearsHD").val(), $("#enginesHD").val(), $("#enginesHD :selected").text());
        });
        $("#engines").change(function () {
            xajax_showdiv($("#models").val(), $("#make").val(), $("#years").val(), $("#engines").val(), $("#engines :selected").text());
        });
        $("#crossReferenceSubmit").click(function () {
            getResults($('#partnum').val());
        });
        $("#partnum").keypress(function (event) {
            disableEnterKey(event);
        });
        $("#app").change(function () {
            window.location = window.location.pathname + "?app=" + $("#app").val();
        });
        $(".backToTop").live('click', function () {
            $("html, body").animate({scrollTop: 0}, "slow");
        });
        $("ul.partCategories li").live('click', function () {
            $("html,body").animate({scrollTop: $("#" + $(this).attr("class")).offset().top}, "slow");
        });

        <? echo $effects_script; ?>

    });
</script>
<div id="vehicleSelector">
    <? echo $form_code; ?>
    <div id="currentApp">
        <?
        if ($_SESSION["make"]){
            echo '<div class="application"><p><img src="images/delete.png" alt="Delete" title="Delete Vehicle" class="deleteVehicle" /><span class="currentVehicle">' . $_SESSION["year"] . ' ' . $_SESSION["make"] . ' ' . $_SESSION["model"] . '  </span><button class="showHide button">Show</button></p></div>';
        } ?>
    </div>
    <div class="clear"></div>
</div>
<div id="crossReference">
    <h2>Cross Reference</h2>
    <b>Manufacturer Part Number:</b>
    <input name="partnum" id="partnum" type="text">
    <input id="crossReferenceSubmit" name="crossReferenceSubmit" type="submit" class="button" value="Find My Part!">
    <form name="form1" method="POST" action="sessionvars.php">
        <input id="partinput" name="partinput" type="hidden" value="">
    </form>
    <div id="currentCross">
        <? if ($_SESSION["search"]){
            echo '<div class="application"><p><img src="images/delete.png" alt="Delete" title="Delete Vehicle" class="deleteVehicle" /><span class="currentVehicle" href="#">' . $_SESSION["search"] . ' </span><button class="showHide button">Show</button></p></div>';
        } ?>
    </div>
    <div class="clear"></div>
</div>
