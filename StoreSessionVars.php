<?php 

//  Need to convert session vars to standard vars
$email = $_SESSION["email"];
$make = $_SESSION["make"];
$model = $_SESSION["model"];
$year = $_SESSION["year"];
$engine = $_SESSION["engine"];

// Execute query to insert session vars
// No need to instantiate DB as Viart has already done so  - be sure to include DB name if not Viart store DB
mysql_query("INSERT INTO oilfiltersonline.customer_vehicle (email, make, model, year, engine) VALUES ('$email', '$make', '$model', '$year', '$engine')");

?>