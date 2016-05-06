<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  dsp_process.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

        $is_admin_path = true;
        $root_folder_path = "../";

        include_once ($root_folder_path ."includes/common.php");
        include_once ($root_folder_path ."includes/order_items.php");
        include_once ($root_folder_path ."includes/date_functions.php");

        $vc = get_session("session_vc");
        $order_id = get_session("session_order_id");

        $order_errors = check_order($order_id, $vc);
        if($order_errors) {
                echo $order_errors;
                exit;
        }

        $sql  = " SELECT payment_id, order_total FROM " . $table_prefix . "orders ";
        $sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
        $db->query($sql);
        if ($db->next_record()) {
                $payment_id = $db->f("payment_id");
                $order_total = $db->f("order_total")*100;
                $order_total = round($order_total,0);
        }

        $payment_parameter = array();
        $sql  = " SELECT parameter_name, parameter_source, parameter_type FROM " . $table_prefix . "payment_parameters";
        $sql .= " WHERE payment_id=". $db->tosql($payment_id, INTEGER);
        $db->query($sql);
        while($db->next_record()) {
                $payment_parameter[$db->f("parameter_name")] = str_replace( "{site_url}", $settings["site_url"], $db->f("parameter_source"));
                if ( ($db->f("parameter_name")=="Amount") && ($db->f("parameter_type")== "CONSTANT") ){
                $order_total=$payment_parameter["Amount"];
                }
        }

        $SECURE_SECRET = $payment_parameter["Secure_Hash_Secret"];

        $vpcURL = $_POST["virtualPaymentClientURL"] . "?";

        unset($_POST["virtualPaymentClientURL"]);
        unset($_POST["SubButL"]);
        $_POST["vpc_Amount"] = $order_total;

        $md5HashData = $SECURE_SECRET;
        ksort ($_POST);

        $appendAmp = 0;

        foreach($_POST as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                $md5HashData .= $value;
            }
        }

        if (strlen($SECURE_SECRET) > 0) {
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($md5HashData));
        }

        header("Location: ".$vpcURL);
?>