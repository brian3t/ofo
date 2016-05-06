<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_reply.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/support_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");
	
	// connection for support attachemnts 
	$dba = new VA_SQL();
	$dba->DBType       = $db->DBType;
	$dba->DBDatabase   = $db->DBDatabase;
	$dba->DBUser       = $db->DBUser;
	$dba->DBPassword   = $db->DBPassword;
	$dba->DBHost       = $db->DBHost;
	$dba->DBPort       = $db->DBPort;
	$dba->DBPersistent = $db->DBPersistent;

	$dbd = new VA_SQL();
	$dbd->DBType       = $db->DBType;
	$dbd->DBDatabase   = $db->DBDatabase;
	$dbd->DBUser       = $db->DBUser;
	$dbd->DBPassword   = $db->DBPassword;
	$dbd->DBHost       = $db->DBHost;
	$dbd->DBPort       = $db->DBPort;
	$dbd->DBPersistent = $db->DBPersistent;
            
	$eol = get_eol();

	// get permissions
	$permissions = get_permissions();
	$allow_edit  = get_setting_value($permissions, "support_ticket_edit", 0);
	$allow_close = get_setting_value($permissions, "support_ticket_close", 0); 
	$allow_reply = get_setting_value($permissions, "support_ticket_reply", 0); 
	$ticket_errors = "";

	//$close_id   = get_param("close_id");
	$support_id = get_param("support_id");
	$operation  = get_param("operation");
	$rnd        = get_param("rnd");

	$admin_support_url = new VA_URL("admin_support.php", true, array("support_id", "operation"));

	$close_status_id = "";
	$sql  = "SELECT status_id FROM " . $table_prefix . "support_statuses WHERE is_closed=1";
	$db->query($sql);
	if ($db->next_record()) {
		$close_status_id = $db->f("status_id");
	}

	if ($operation == "close") {
		if ($allow_close) {
			if ($close_status_id) {
				$sql  = " UPDATE " . $table_prefix . "support SET support_status_id=" . $db->tosql($close_status_id, INTEGER);
				$sql .= " , date_modified=" . $db->tosql(va_time(), DATETIME);
				$sql .= " , admin_id_assign_by = 0 ";
				$sql .= " , admin_id_assign_to = 0 ";
				$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
				$db->query($sql);
			}
			$url = $admin_support_url->get_url();
			header("Location: " . $url);
			exit;
		} else {
			$ticket_errors = CLOSE_TICKET_NOT_ALLOWED_MSG;
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_reply.html");

	$admin_support_url = new VA_URL("admin_support.php", false);
	$admin_support_url->add_parameter("s_w", REQUEST, "s_w");
	$admin_support_url->add_parameter("s_s", REQUEST, "s_s");
	$admin_support_url->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_support_url->add_parameter("sort_dir", REQUEST, "sort_dir");
	$t->set_var("admin_support_url", $admin_support_url->get_url());

	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_TICKET_MSG, CONFIRM_DELETE_MSG));

	$admin_support_url->add_parameter("support_id", REQUEST, "support_id");
	$return_page = $admin_support_url->get_url("admin_support_reply.php");

	$admin_support_url->add_parameter("rp", CONSTANT, $return_page);
	$admin_support_request_url = $admin_support_url->get_url("admin_support_request.php");
	$t->set_var("admin_support_request_url", $admin_support_request_url);

	$admin_support_url->remove_parameter("rp");
	$admin_support_url->add_parameter("operation", CONSTANT, "delete");
	$admin_request_delete_url = $admin_support_url->get_url("admin_support_request.php");
	$t->set_var("admin_request_delete_url", $admin_request_delete_url);

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_reply_href", "admin_support_reply.php");
	$t->set_var("admin_support_message_href", "admin_support_message.php");
	$t->set_var("admin_support_request_href", "admin_support_request.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("site_url", $settings["site_url"]);

	$t->set_var("rnd", va_timestamp());

	// signature
	$session_admin_id = get_session("session_admin_id");
	$user_signature = get_db_value(" SELECT signature FROM ". $table_prefix . "admins WHERE admin_id=" .$db->tosql($session_admin_id,INTEGER));
	$user_signature = str_replace("\r","\\r", $user_signature);
	$user_signature = str_replace("\n","\\n", $user_signature);
	$user_signature = str_replace("\"","\\\"", $user_signature);
	$t->set_var("user_signature", $user_signature);

	// update request viewed information
	$sql  = " UPDATE " . $table_prefix . "support SET date_viewed=" . $db->tosql(va_time(), DATETIME);
	$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
	$sql .= " AND date_viewed IS NULL ";
	$db->query($sql);

	$sql  = " SELECT s.dep_id, s.support_id , s.user_id,s.user_name, s.user_email, s.mail_cc, s.mail_bcc, ";
	$sql .= " s.remote_address, s.identifier, s.mail_headers, s.mail_body_html, s.mail_body_text, ";
	$sql .= " s.environment, p.product_name, st.type_name, s.summary, s.description, ";
	$sql .= " ss.status_name, ss.status_id, sp.priority_name, s.date_added, s.date_modified, ";
	$sql .= " aa.admin_name as assign_to, s.date_viewed ";
	if ($sitelist) {
		$sql .= ", sti.site_name ";
	}
	$sql .= " FROM ((((((" . $table_prefix . "support s ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_products p ON p.product_id=s.support_product_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id=s.support_status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id=s.support_type_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id=s.support_priority_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins aa ON aa.admin_id=s.admin_id_assign_to) ";
	if ($sitelist) {
		$sql .= " LEFT JOIN " . $table_prefix . "sites sti ON sti.site_id=s.site_id) ";
	} else {
		$sql .= " ) ";
	}
	$sql .= " WHERE s.support_id=" . $db->tosql($support_id, INTEGER);
	$db->query($sql);
	if ($db->next_record())
	{
		$dep_id = $db->f("dep_id");
		$user_id = $db->f("user_id");
		$t->set_var("user_id", $user_id);
		$user_name = $db->f("user_name");
		$t->set_var("user_name", htmlspecialchars($user_name));
		$user_email = $db->f("user_email");
		$user_cc = $db->f("mail_cc");
		$user_bcc = $db->f("mail_bcc");
		$initial_mail_headers = $db->f("mail_headers");
		$initial_mail_body_html = $db->f("mail_body_html");
		$initial_mail_body_text = $db->f("mail_body_text");
		$identifier = $db->f("identifier");
		$environment = $db->f("environment");
		$site_name = $db->f("site_name");
		if ($sitelist) {
			$t->set_var("site_name", $site_name);
			$t->parse("site_name_block");
		}
			

		//---------------------------------------------------------------
		
		$support_properties = array();
		$sql  = " SELECT op.property_id, ocp.property_name, op.property_value, ";
		$sql .= " ocp.control_type ";
		$sql .= " FROM (" . $table_prefix . "support_properties op ";
		$sql .= " INNER JOIN " . $table_prefix . "support_custom_properties ocp ON op.property_id=ocp.property_id)";
		$sql .= " WHERE op.support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " ORDER BY ocp.property_order, op.property_id ";
		$dba->query($sql);
		while ($dba->next_record()) {
			$property_id   = $dba->f("property_id");
			$property_name = $dba->f("property_name");
			$property_value = $dba->f("property_value");
			$control_type = $dba->f("control_type");
			// check value description
			if(($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && is_numeric($property_value)) {
				$sql  = " SELECT property_value FROM " . $table_prefix . "support_custom_values ";
				$sql .= " WHERE property_value_id=" . $db->tosql($property_value, INTEGER);
				$dbd->query($sql);
				if ($dbd->next_record()) {
					$property_value = $dbd->f("property_value");
				}
			}
			if (isset($support_properties[$property_id])) {
				$support_properties[$property_id]["value"] .= "; " . $property_value;
			} else {
				$support_properties[$property_id] = array(
					"name" => $property_name, "value" => $property_value,
				);
			}
		}
		foreach ($support_properties as $property_id => $property_values) {
			$property_name = $property_values["name"];
			$property_value = $property_values["value"];
			$t->set_var("property_name", $property_name);
			$t->set_var("property_value", $property_value);
			$t->sparse("custom_properties", true);
		}
		
		//---------------------------------------------------------------
		$t->set_var("user_email", $user_email);
		$t->set_var("mail_cc", $user_cc);
		$t->set_var("environment", htmlspecialchars($db->f("environment")));
		$t->set_var("remote_address", $db->f("remote_address"));
		$product_name = $db->f("product_name");
		$t->set_var("product_name", $product_name);
		$t->set_var("product", $product_name);

		$t->set_var("assign_to", $db->f("assign_to"));
		$t->set_var("type", get_translation($db->f("type_name")));
		$current_status = strlen($db->f("status_name")) ? $db->f("status_name") : "";
		$t->set_var("current_status", $current_status);
		
		$current_status_id = $db->f("status_id");
		$summary = $db->f("summary");
		$t->set_var("summary", htmlspecialchars($summary));
		
		$priority = strlen($db->f("priority_name")) ? $db->f("priority_name") : "";
		$t->set_var("priority", $priority);
		$date_modified = $db->f("date_modified", DATETIME);
		$date_modified_string = va_date($datetime_show_format, $date_modified);
		$t->set_var("date_modified", $date_modified_string);

		$date_added = $db->f("date_added", DATETIME);
		$request_added_string = va_date($datetime_show_format, $date_added);
		$date_added_string = $request_added_string;

		$request_viewed = $db->f("date_viewed", DATETIME);
		$request_viewed_string = va_date($datetime_show_format, $request_viewed);

		$t->set_var("request_added", $date_added_string);
		$description = $db->f("description");
		$t->set_var("request_description", nl2br(htmlspecialchars($description)));
		$last_message = $description;

		$identifier_html = htmlspecialchars($identifier);
		if ($identifier) {
			$sql  = " SELECT order_id FROM " . $table_prefix . "orders ";
			$sql .= " WHERE order_id=" . $db->tosql($identifier, INTEGER);
			$sql .= " OR transaction_id=" . $db->tosql($identifier, TEXT);
			$sql .= " OR invoice_number=" . $db->tosql($identifier, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$order_id = $db->f("order_id");
				$identifier_html = "<a href=\"" . $order_details_site_url . "admin_order.php?order_id=" . $order_id ."\">" . htmlspecialchars($identifier) . "</a>";
			} else {
				$identifier_html = htmlspecialchars($identifier) . APPROPRIATE_CODE_ERROR_MSG;
			}
		}
		$t->set_var("identifier", htmlspecialchars($identifier));
		$t->set_var("identifier_html", $identifier_html);

		// update request viewed information
		$sql  = " UPDATE " . $table_prefix . "support_messages SET date_viewed=" . $db->tosql(va_time(), DATETIME);
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " AND date_viewed IS NULL ";
		$sql .= " AND (admin_id_assign_by IS NULL OR admin_id_assign_by=0 ";
		$sql .= " OR (internal=1 AND (admin_id_assign_to IS NULL OR admin_id_assign_to=0 OR admin_id_assign_to=" . $db->tosql(get_session("session_admin_id"), INTEGER) . "))) ";
		$db->query($sql);

		$vc = md5($support_id . $date_added[3].$date_added[4].$date_added[5]);

		if ($allow_edit) {
			$t->parse("edit_ticket", false);
		}

		$admin_support_url = new VA_URL("admin_support_reply.php", true, array("operation"));
		$admin_support_url->add_parameter("operation", CONSTANT, "close");
		if ($allow_close && $db->f("status_id") != $close_status_id) {
			if ($allow_edit) {
				$t->set_var("links_separator", "|");
			}
			$t->set_var("close_ticket_url", $admin_support_url->get_url());
			$t->parse("close_ticket", false);
		} 
	} else {
		header("Location: admin_support.php");
		exit;
	}

	$t->set_var("admin_support_attachments_url", "admin_support_attachments.php?support_id=" . urlencode($support_id) . "&dep_id=" . urlencode($dep_id));

	// get department information
	$full_title = "";
	$sql = "SELECT * FROM " . $table_prefix . "support_departments WHERE dep_id=" . $db->tosql($dep_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$short_title = $db->f("short_title");
		$full_title = $db->f("full_title");
		$dep_signature = $db->f("signature");
		$incoming_account = $db->f("incoming_account");
		$outgoing_account = $db->f("outgoing_account");
	}
	$t->set_var("department_title", $full_title);

	// get global helpdesk settings
	$support_settings = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='support'";
	if ($multisites_version) {
		$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
		$sql .= "ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$support_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$r = new VA_Record($table_prefix . "support_messages");
	$r->add_where("message_id", INTEGER);
	$r->errors = $ticket_errors;
	
	// begin changes related to knowledge base settings
	$sql  = " SELECT category_id, category_name, category_path, parent_category_id ";
	$sql .= " FROM " . $table_prefix . "articles_categories";
	$sql .= " ORDER BY parent_category_id, category_order ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$row_category_id = $db->f("category_id");
			$row_category_name = $db->f("category_name");
			$row_category_path = $db->f("category_path");
			$row_parent_category_id = $db->f("parent_category_id");
			$array_knowledge_cat[] = array($row_category_id, $row_category_name, $row_category_path, $row_parent_category_id);
 		} while ($db->next_record());
	}

	$array_almost_complete_cat = array();

	function order_cat($p, &$k)
	{
		global $array_knowledge_cat;
		global $array_almost_complete_cat;
		for ($i=0; $i < sizeof($array_knowledge_cat); $i++) {
			$cat_name = $array_knowledge_cat[$i];
			if ($cat_name[3] == $p) {
				$array_almost_complete_cat[$k] = $cat_name;
				$k++;
				order_cat($cat_name[0], $k);
			}
		}
	}


	$knowledge_category_id = get_setting_value($support_settings, "knowledge_category", ""); 
	$knowledge_article_status = get_setting_value($support_settings, "knowledge_article_status", ""); 
	
	if (strlen($knowledge_category_id)) {
		$k = 0;
		order_cat($knowledge_category_id, $k);

		$sql  = "SELECT category_path, category_name FROM " . $table_prefix . "articles_categories ";
		$sql .= "WHERE category_id=" . $db->tosql($knowledge_category_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$parent_path = $db->f("category_path");
			$array_path = explode(",", $parent_path);
			$path_count = sizeof($array_path)-1;
			$parent_category_name = $db->f("category_name");

			$array_complete_cat = array();
			$k = 0;
			$array_complete_cat[$k][0] = "";
			$array_complete_cat[$k][1] = "";
			$k++;
			$array_complete_cat[$k][0] = $knowledge_category_id;
			$array_complete_cat[$k][1] = $parent_category_name;
			$k++;
	  
			foreach($array_almost_complete_cat as $cat_name) {
				$array_cat = explode(",", $cat_name[2]);
				for ($i = 1; $i < count($array_cat); $i++) {
					if (!isset($array_complete_cat[$k][1])) $array_complete_cat[$k][1] = "";
					if ($i > $path_count)
					$array_complete_cat[$k][1] .= " - ";
				}
	  
				if (!isset($array_complete_cat[$k][0])) $array_complete_cat[$k][0] = "";
				$array_complete_cat[$k][0] .= $cat_name[0];
				$array_complete_cat[$k][1] .= $cat_name[1];
				$k++;
			}
	    
			$t->set_var("has_knowledge_base", "1");
		} else {
			$knowledge_category_id = "";
			$t->set_var("has_knowledge_base", "");
		}
	} else {
		$t->set_var("has_knowledge_base", "");
	}
	// end changes related to knowledge base settings
	

	$sql  = " SELECT status_id, status_name, is_internal FROM " . $table_prefix . "support_statuses ";
	$sql .= " WHERE is_operation=1 ";
	if (!$allow_close) {
		$sql .= " AND (is_closed IS NULL OR is_closed<>1)";
	}
	$sql .= " ORDER BY status_name ASC";
	$support_statuses = array(array("", SUPPORT_SELECT_STATUS_MSG));
	$db->query($sql);
	while ($db->next_record()) {
		if ($db->f("is_internal") == "1") {
			$support_statuses[] = array($db->f("status_id"), $db->f("status_name") . " (Internal)");
		} else {
			$support_statuses[] = array($db->f("status_id"), $db->f("status_name"));
		}
	}

	$sql  = " SELECT a.admin_id,a.admin_name FROM (" . $table_prefix . "admins a ";
	$sql .= " INNER JOIN " . $table_prefix . "support_users_departments sud ON a.admin_id=sud.admin_id) ";
	$sql .= " WHERE sud.dep_id=" . $db->tosql($dep_id, INTEGER);
	$sql .= " ORDER BY a.admin_name ";
	$admins = get_db_values($sql, array(array("", "")));

	$r->add_textbox("support_id", INTEGER);

	$r->add_hidden("s_w", TEXT);
	$r->add_hidden("s_s", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);

	$r->add_textbox("dep_id", INTEGER);
	$r->add_textbox("internal", INTEGER);
	$r->add_select("admin_id_assign_to", INTEGER, $admins, ASSIGN_TO_MSG);
	$r->change_property("admin_id_assign_to", USE_SQL_NULL, false);	
	if ($operation == "assign" || $operation == "reply_to_admin") {
		$r->change_property("admin_id_assign_to", REQUIRED, true);
	}
	$r->add_hidden("last_admin_id_assign_by", INTEGER);
	$r->add_select("support_status_id", INTEGER, $support_statuses, STATUS_MSG);
	$r->change_property("support_status_id", PARSE_NAME, "response_status");
	$r->parameters["support_status_id"][REQUIRED] = true;
	$r->add_textbox("admin_id_assign_by", INTEGER);
	$r->change_property("admin_id_assign_by", USE_SQL_NULL, false);
	$r->add_textbox("message_text", TEXT, MESSAGE_MSG);
	$r->change_property("message_text", PARSE_NAME, "response_message");
	$r->change_property("message_text", TRIM, true);
	$r->parameters["message_text"][REQUIRED] = true;
	$r->add_textbox("date_added", DATETIME);
	
	if (strlen($knowledge_category_id)) {
		$r->add_select("knowledge_category", INTEGER, $array_complete_cat, KNOWLEDGE_CATEGORY_MSG);
		$r->change_property("knowledge_category", USE_IN_INSERT, false);
		$r->add_textbox("knowledge_title", TEXT, KNOWLEDGE_TITLE_MSG);
		$r->change_property("knowledge_title", USE_IN_INSERT, false);
		if ($operation == "knowledge") {
			$r->change_property("knowledge_category", REQUIRED, true);
			$r->change_property("knowledge_title", REQUIRED, true);
		}
	}

	$r->get_form_values();
	if ($operation == "reply_to_admin") {
		$r->set_value("admin_id_assign_to", $r->get_value("last_admin_id_assign_by"));
	}
	$errors = "";

	$session_rnd = get_session("session_rnd");
	$operation = get_param("operation");
	$rnd = get_param("rnd");

	if ($operation && $rnd != $session_rnd) {
		$new_status_id = $current_status_id;
		$update_ticket = false;
		if ($operation == "assign" || $operation == "reply_to_admin") {
			$sql  = "SELECT status_id FROM " . $table_prefix . "support_statuses WHERE is_reassign=1 ";
			$reassign_status_id = get_db_value($sql);
			$r->set_value("support_status_id", $reassign_status_id);
			$new_status_id = $reassign_status_id;
			$update_ticket = true;
		} elseif ($operation == "knowledge") {
			$sql  = "SELECT status_id FROM " . $table_prefix . "support_statuses WHERE is_add_knowledge=1 ";
			$knowledge_status_id = get_db_value($sql);
			$r->set_value("support_status_id", $knowledge_status_id);
		} else {
			$new_status_id = $r->get_value("support_status_id");
			$update_ticket = true;
		}

		if ($allow_reply) {
			$is_valid = $r->validate();
		} else {
			$is_valid = false;
			$r->errors = REPLY_TICKET_NOT_ALLOWED_MSG;
		}

		if ($is_valid) {
			$is_internal = 0;
			$sql = "SELECT is_internal FROM " . $table_prefix . "support_statuses WHERE status_id=" . $db->tosql($r->get_value("support_status_id"), INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$is_internal = $db->f("is_internal");
			}

			$date_added = va_time();
			$r->set_value("dep_id", $dep_id);
			$r->set_value("internal", intval($is_internal));
			$r->set_value("date_added", $date_added);
			$r->set_value("admin_id_assign_by", get_session("session_admin_id"));
			if ($db_type == "postgre") {
				$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "support_messages') ";
				$new_message_id = get_db_value($sql);
				$r->set_value("message_id", $new_message_id);
				$r->change_property("message_id", USE_IN_INSERT, true);
			}
				
			$has_knowledge_base = get_param("has_knowledge_base");
			if (strlen($has_knowledge_base)) {
				$knowledge_category_id = $r->get_value("knowledge_category");
			} else {
				$knowledge_category_id = 0;
			}
    
			// begin add message to knowledge base
			if ($knowledge_category_id > 0) {
				$article_title = $r->get_value("knowledge_title");
				$article_text = $r->get_value("message_text");
				if (!$knowledge_article_status) {
					$knowledge_article_status = 1;
				}
				$sql  = " SELECT MAX(a.article_order) FROM " . $table_prefix . "articles a, " . $table_prefix . "articles_assigned aa ";
				$sql .= " WHERE a.article_id = aa.article_id ";
				$sql .= " AND aa.category_id = " . $db->tosql($knowledge_category_id, INTEGER);
				$article_order = get_db_value($sql) + 1;
        
				$sql  = "INSERT INTO " . $table_prefix . "articles (article_order, article_date, article_title, status_id, date_added, full_description) VALUES (";
				$sql .= $db->tosql($article_order, INTEGER) . ",";
				$sql .= $db->tosql($date_added, DATETIME) . ",";
				$sql .= $db->tosql($article_title, TEXT) . ",";
				$sql .= $db->tosql($knowledge_article_status, INTEGER) . ",";
				$sql .= $db->tosql($date_added, DATETIME) . ",";
				$sql .= $db->tosql($article_text, TEXT) . ")";
				$db->query($sql);
        
				if ($db_type == "mysql") {
					$sql = "SELECT LAST_INSERT_ID()";
				} else {
					$sql = "SELECT MAX(article_id) FROM " . $table_prefix . "articles ";
				}
				$article_id = get_db_value($sql);
        
				$sql  = "INSERT INTO " . $table_prefix . "articles_assigned (article_id, category_id) VALUES (";
				$sql .= $db->tosql($article_id, INTEGER) . ",";
				$sql .= $db->tosql($knowledge_category_id, INTEGER) . ")";
				$db->query($sql);
			}
			// end add message to knowledge base
    
			if ($r->insert_record())
			{
				// get added new message_id
				if ($db_type == "mysql") {
					$sql = " SELECT LAST_INSERT_ID() ";
					$new_message_id = get_db_value($sql);
				} else if ($db_type == "access") {
					$sql = " SELECT @@IDENTITY ";
					$new_message_id = get_db_value($sql);
				} else if ($db_type == "db2") {
					$new_message_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "support_messages FROM " . $table_prefix . "support_messages");
				}
				$r->set_value("message_id", $new_message_id);
    
				$date_added_string = va_date($datetime_show_format, $date_added);
				$support_url = $settings["site_url"] . "support_messages.php?support_id=" . $support_id . "&vc=" . $vc;
    
				if (intval($r->get_value("admin_id_assign_to")) > 0) {
					$sql = " SELECT admin_name FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql($r->get_value("admin_id_assign_to"), INTEGER);
					$assign_to = get_db_value($sql);
					$t->set_var("assign_to", $assign_to);
				}
    
				$current_status = get_array_value($r->get_value("support_status_id"), $support_statuses);
    
				$t->set_var("status", $current_status);
				$t->set_var("current_status", $current_status);
				$t->set_var("vc", $vc);
				$t->set_var("support_url", $support_url);
				$t->set_var("message_added", $date_added_string);
				$t->set_var("date_modified", $date_added_string);

				// check attachments
				$attachments = array();
				$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "support_attachments ";
				$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
				$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);
				while ($db->next_record()) {
					$filename = $db->f("file_name");
					$filepath = $db->f("file_path");
					$attachments[] = array($filename, $filepath);
				}
    
				$sql  = " UPDATE " . $table_prefix . "support_attachments ";
				$sql .= " SET message_id=" . $db->tosql($new_message_id, INTEGER);
				$sql .= " , attachment_status=1 ";
				$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
				$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);
    
				// send email notification to admin
				if ($support_settings["admin_notification"] && intval($r->get_value("admin_id_assign_to")) > 0)
				{
					$t->set_block("admin_subject", $support_settings["admin_subject"]);
					$t->set_block("admin_message", $support_settings["admin_message"]);
    
					$admin_email = get_db_value("SELECT email FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql($r->get_value("admin_id_assign_to"), INTEGER));
    
					$r->set_parameters();
					if (strlen($outgoing_account)) {
						$mail_from = $outgoing_account;
					} else {
						$mail_from = get_setting_value($support_settings, "admin_mail_from", $settings["admin_email"]);
					}
					$email_headers = array();
					$email_headers["from"] = $mail_from;
					$email_headers["cc"] = get_setting_value($support_settings, "cc_emails");
					$email_headers["bcc"] = get_setting_value($support_settings, "admin_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($support_settings, "admin_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($support_settings, "admin_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($support_settings, "admin_message_type");
    
					$t->set_var("summary", $summary);
					$t->set_var("description", $description);
					$t->set_var("message_text", $r->get_value("message_text"));
					$t->set_var("user_name", $user_name);
					$t->set_var("identifier", $identifier);
					$t->set_var("environment", $environment);
					$t->parse("admin_subject", false);
					if ($email_headers["mail_type"]) {
						$t->set_var("summary", htmlspecialchars($summary));
						$t->set_var("description", nl2br(htmlspecialchars($description)));
						$t->set_var("message_text", nl2br(htmlspecialchars($r->get_value("message_text"))));
						$t->set_var("user_name", htmlspecialchars($user_name));
						$t->set_var("identifier", htmlspecialchars($identifier));
						$t->set_var("environment", htmlspecialchars($environment));
					}
					$t->parse("admin_message", false);
    
					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					va_mail($admin_email, $t->get_var("admin_subject"), $admin_message, $email_headers, $attachments);
				}
    
    
				if (!$r->get_value("internal"))
				{
					// send email notification to user
					$t->set_block("user_subject", $support_settings["user_subject"]);
					$t->set_block("user_message", $support_settings["user_message"]);
        
					$r->set_parameters();
        
					if (strlen($outgoing_account)) {
						$mail_from = $outgoing_account;
					} else {
						$mail_from = get_setting_value($support_settings, "user_mail_from", $settings["admin_email"]);
					}
					$mail_cc = get_setting_value($support_settings, "user_mail_cc");
					if ($user_cc) {
						if ($mail_cc) { $mail_cc .= ", "; }
						$mail_cc .= $user_cc;
					}
					$mail_bcc = get_setting_value($support_settings, "user_mail_bcc");
					if ($user_bcc) {
						if ($mail_bcc) { $mail_bcc .= ", "; }
						$mail_bcc .= $user_bcc;
					}
					$email_headers = array();
					$email_headers["from"] = $mail_from;
					$email_headers["cc"] = $mail_cc;
					$email_headers["bcc"] = $mail_bcc;
					$email_headers["reply_to"] = get_setting_value($support_settings, "user_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($support_settings, "user_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($support_settings, "user_message_type");
        
					$t->set_var("summary", $summary);
					$t->set_var("description", $description);
					$t->set_var("message_text", $r->get_value("message_text"));
					$t->set_var("user_name", $user_name);
					$t->set_var("identifier", $identifier);
					$t->set_var("environment", $environment);
					$t->parse("user_subject", false);
					if ($email_headers["mail_type"]) {
						$t->set_var("summary", htmlspecialchars($summary));
						$t->set_var("description", nl2br(htmlspecialchars($description)));
						$t->set_var("message_text", nl2br(htmlspecialchars($r->get_value("message_text"))));
						$t->set_var("user_name", htmlspecialchars($user_name));
						$t->set_var("identifier", htmlspecialchars($identifier));
						$t->set_var("environment", htmlspecialchars($environment));
					}
					$t->parse("user_message", false);
        
					$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
					va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers, $attachments);
				}
    
				// update support ticket info
				if ($update_ticket) {
					$sql  = " UPDATE " . $table_prefix . "support SET support_status_id=" . $new_status_id;
					$sql .= " , admin_id_assign_by=" . $db->tosql($r->get_value("admin_id_assign_by"), INTEGER, true, false);
					$sql .= " , admin_id_assign_to=" . $db->tosql($r->get_value("admin_id_assign_to"), INTEGER, true, false);
					$sql .= " , date_modified=" . $db->tosql(va_time(), DATETIME);
					$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
					$db->query($sql);
				} elseif ($operation == "assign") {
					// update assigned persons information and date
					$sql  = " UPDATE " . $table_prefix . "support SET ";
					$sql .= " admin_id_assign_by=" . $db->tosql($r->get_value("admin_id_assign_by"), INTEGER, true, false);
					$sql .= " , admin_id_assign_to=" . $db->tosql($r->get_value("admin_id_assign_to"), INTEGER, true, false);
					$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
					$db->query($sql);
				}
			}
        
			header("Location: " . $return_page);
			exit;
		}
		else
		{
			//$errors .= "Please provide information in the sections with red, italicized headings, then click 'Submit'.<br>";
			set_session("session_rnd", "");
		}
	}
	else // new message (set default values)
	{

	}

	// set ticket information
	$t->set_var("summary", htmlspecialchars($summary));
	$t->set_var("description", nl2br(htmlspecialchars($description)));
	$t->set_var("user_name", htmlspecialchars($user_name));
	$t->set_var("identifier", htmlspecialchars($identifier));
	$t->set_var("environment", htmlspecialchars($environment));

	// show ticket statistics
	$currency = get_currency();
	$orders_stats = false; $orders_name_stats = false;
	$sql  = " SELECT COUNT(*) AS orders_number, SUM(order_total) AS orders_total, o.order_status, os.status_name ";
	$sql .= " FROM (" . $table_prefix . "orders o ";
	$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id)";
	if ($user_id > 0) {
		$where = " WHERE (user_id=" . $db->tosql($user_id, INTEGER) . " OR email=" . $db->tosql($user_email, TEXT) . ") ";
	} else {
		$where = " WHERE email=" . $db->tosql($user_email, TEXT);
	}
	$group_by = " GROUP BY o.order_status, os.status_name ";
	$db->query($sql.$where.$group_by);
	if ($db->next_record()) {
		$orders_stats = true;
	} else {
		$where = " WHERE name=" . $db->tosql($user_name, TEXT);
		$name_parts = explode(" ", $user_name, 2);
		if (sizeof($name_parts) == 1) {
			$where .= " OR first_name=" . $db->tosql($name_parts[0], TEXT);
		} else {
			$where .= " OR (first_name=" . $db->tosql($name_parts[0], TEXT);
			$where .= " AND last_name=" . $db->tosql($name_parts[1], TEXT) . ") ";
		}
		$db->query($sql.$where.$group_by);
		if ($db->next_record()) {
			$orders_name_stats = true;
		}
	}

	if ($orders_stats || $orders_name_stats) {
		$orders_number_sum = 0; $orders_total_sum = 0;
		$admin_orders_url = new VA_URL("admin_orders.php", false);
		if ($user_id > 0) {
			$admin_orders_url->add_parameter("user_id", CONSTANT, $user_id);
		}
		if ($orders_name_stats) {
			$admin_orders_url->add_parameter("s_ne", CONSTANT, $user_name);
		} else {
			$admin_orders_url->add_parameter("s_ne", CONSTANT, $user_email);
		}

		do {
			$order_status = $db->f("status_name");
			$order_status_id = $db->f("order_status");
			if (!$order_status) { $order_status = $order_status_id; }
			$orders_number = $db->f("orders_number");
			$orders_total = $db->f("orders_total");
			$orders_number_sum += $orders_number; 
			$orders_total_sum += $orders_total;
			$admin_orders_url->add_parameter("s_os", CONSTANT, $order_status_id);

			$t->set_var("order_status", $order_status);
			$t->set_var("orders_number", $orders_number);
			$t->set_var("admin_orders_url", $admin_orders_url->get_url());
			$t->set_var("orders_total", $currency["left"] . number_format($orders_total * $currency["rate"], 2) . $currency["right"]);
			$t->sparse("orders_statuses", true);
		} while ($db->next_record());

		$admin_orders_url->add_parameter("s_os", CONSTANT, "");
		$t->set_var("admin_orders_url", $admin_orders_url->get_url());
		$t->set_var("orders_number_sum", $orders_number_sum);
		$t->set_var("orders_total_sum", $currency["left"] . number_format($orders_total_sum * $currency["rate"], 2) . $currency["right"]);
		if ($orders_name_stats) {
			$t->sparse("orders_name_stats", false);
		} else {
			$t->sparse("orders_stats", false);
		}
	}

	$sql  = " SELECT COUNT(*) AS tickets_number, s.support_status_id, ss.status_name ";
	$sql .= " FROM (" . $table_prefix . "support s ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON s.support_status_id=ss.status_id)";
	if ($user_id > 0) {
		$sql .= " WHERE (user_id=" . $db->tosql($user_id, INTEGER) . " OR user_email=" . $db->tosql($user_email, TEXT) . ") ";
	} else {
		$sql .= " WHERE user_email=" . $db->tosql($user_email, TEXT);
	}
	$sql .= " GROUP BY s.support_status_id, ss.status_name ";
	$db->query($sql);
	if ($db->next_record()) {
		$tickets_number_sum = 0;
		$admin_tickets_url= new VA_URL("admin_support.php", false);
		if ($user_id > 0) {
			$admin_tickets_url->add_parameter("user_id", CONSTANT, $user_id);
		}
		$admin_tickets_url->add_parameter("s_ne", CONSTANT, $user_email);

		do {
			$ticket_status = $db->f("status_name");
			$ticket_status_id = $db->f("support_status_id");
			if (!$ticket_status) { $ticket_status = $ticket_status_id; }
			$tickets_number = $db->f("tickets_number");
			$tickets_number_sum += $tickets_number; 
			$admin_tickets_url->add_parameter("status_id", CONSTANT, $ticket_status_id);

			$t->set_var("ticket_status", $ticket_status);
			$t->set_var("tickets_number", $tickets_number);
			$t->set_var("admin_tickets_url", $admin_tickets_url->get_url());
			$t->sparse("tickets_statuses", true);
		} while ($db->next_record());

		$admin_tickets_url->add_parameter("status_id", CONSTANT, "");
		$admin_tickets_url->add_parameter("s_in", CONSTANT, 2);
		$t->set_var("admin_tickets_url", $admin_tickets_url->get_url());
		$t->set_var("tickets_number_sum", $tickets_number_sum);
		$t->sparse("tickets_stats", false);
	}


	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_reply.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "support_messages WHERE support_id=" . $db->tosql($support_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "mes_page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT sm.admin_id_assign_by,sm.admin_id_assign_to,sm.message_id,a.admin_name,ss.status_name, ";
	$sql .= " sm.message_text, sm.date_added, sm.date_viewed, sm.internal, aa.admin_name as assign_to, ";
	$sql .= " sm.mail_headers, sm.mail_body_html, sm.mail_body_text, sm.reply_from, sm.reply_to ";
	$sql .= " FROM (((" . $table_prefix . "support_messages sm ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id=sm.support_status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id=sm.admin_id_assign_by) ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins aa ON aa.admin_id=sm.admin_id_assign_to) ";
	$sql .= " WHERE sm.support_id=" . $db->tosql($support_id, INTEGER);
	$sql .= " ORDER BY sm.date_added DESC ";
	$db->query($sql);
	if ($db->next_record())
	{
		$admin_support_url->remove_parameter("rp");
		$admin_support_url->add_parameter("message_id", DB, "message_id");
		$admin_support_url->remove_parameter("operation");
		$last_message = $db->f("message_text");

		$t->set_var("support_id", $support_id);

		do
		{
			$message_id = $db->f("message_id");
			$mail_headers = $db->f("mail_headers");
			$mail_body_html = $db->f("mail_body_html");
			$mail_body_text = $db->f("mail_body_text");
			$reply_from = $db->f("reply_from");

			$t->set_var("admin_support_message_url", $admin_support_url->get_url("admin_support_message.php"));
			$t->parse("edit_link", false);

			$status = strlen($db->f("status_name")) ? $db->f("status_name") : NEW_MSG;
			$internal_message = $db->f("internal");
			$assign_to = $db->f("assign_to");

			$t->set_var("status", $status);
			$t->set_var("message_id", $message_id);
			if ($internal_message) {
				$posted_by = $db->f("admin_name");
				if (!$posted_by) {
					$posted_by = "ID: " . $db->f("admin_id_assign_by");
				}
				$viewed_by = strlen($assign_to) ? $assign_to : ADMIN_TITLE_MSG;
			} elseif ($db->f("admin_id_assign_by")) {
				$posted_by = $db->f("admin_name");
				if (!$posted_by) {
					$posted_by = "ID: " . $db->f("admin_id_assign_by");
				}
				$viewed_by = "Customer";
			} else {
				if ($reply_from && $reply_from != $user_email) {
					$posted_by = $reply_from;
				} else {
					$posted_by = strlen($user_name) ? $user_name . " <" . $user_email . ">" : $user_email;
				}
				$viewed_by = ADMIN_TITLE_MSG;
			}
			if ($db->f("internal") == 1) {
				$t->parse("internal_block", false);
				$t->set_var("style_am","internal_message");
			} else {
				$t->set_var("internal_block", "");
				$t->set_var("style_am","usual_message");
			}
			if (strlen($db->f("assign_to"))) {
				$t->set_var("message_assign_to", $db->f("assign_to"));
				$t->parse("assign_to_block", false);
			} else {
				$t->set_var("assign_to_block", "");
			}

			$t->set_var("posted_by", htmlspecialchars($posted_by));
			$t->set_var("viewed_by", $viewed_by);

			$date_added = $db->f("date_added", DATETIME);
			$date_added_string = va_date($datetime_show_format, $date_added);
			$t->set_var("date_added", $date_added_string);

			$date_viewed = $db->f("date_viewed", DATETIME);
			if (is_array($date_viewed)) {
				$date_viewed_string = va_date($datetime_show_format, $date_viewed);
				$t->set_var("date_viewed", "<font color=\"blue\">" . $date_viewed_string . "</font>");
			} else {
				$t->set_var("date_viewed", "<font color=\"red\">".SUPPORT_NOT_VIEWED_MSG."</font>");
			}

			// check for mail data
			if ($mail_headers || $mail_body_html || $mail_body_text) {
				if ($mail_headers) {
					$t->set_var("admin_support_mail_data_url", "admin_support_mail_data.php?type=header&message_id=". $message_id);
					$t->parse("mail_headers", false);
				} else {
					$t->set_var("mail_headers", "");
				}
				if ($mail_body_html) {
					$t->set_var("admin_support_mail_data_url", "admin_support_mail_data.php?type=html&message_id=". $message_id);
					$t->parse("mail_body_html", false);
				} else {
					$t->set_var("mail_body_html", "");
				}
				if ($mail_body_text) {
					$t->set_var("admin_support_mail_data_url", "admin_support_mail_data.php?type=text&message_id=". $message_id);
					$t->parse("mail_body_text", false);
				} else {
					$t->set_var("mail_body_text", "");
				}
				$t->parse("mail_data", false);
			} else {
				$t->set_var("mail_data", "");
			}

			// check for attachments
			$sql  = " SELECT * FROM " . $table_prefix . "support_attachments ";
			$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
			$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER);
			$sql .= " AND attachment_status=1 ";
			$dba->query($sql);
			if ($dba->next_record()) {
				$attach_no = 1;
				$attachments_files = ""; 
				do {
					$attachment_id = $dba->Record["attachment_id"];
					$file_name     = $dba->Record["file_name"];
					$file_path     = $dba->Record["file_path"];
					$size	         = get_nice_bytes(filesize($file_path));
					$attachments_files  .= $attach_no . ". <a target=\"_blank\" href=\"admin_support_attachment.php?atid=" . $attachment_id . "\">" . $file_name . "</a> (" . $size . ")&nbsp;&nbsp;";
					$attach_no++;
				} while ($dba->next_record());
				$t->set_var("attachments_files", $attachments_files);
				$t->parse("attachments_block",false);
			} else { 
				$t->set_var("attachments_block","");
			}

			$message_text = $db->f("message_text");
			$message_text = process_message($message_text);
			$t->set_var("message_text", $message_text);

			$t->parse("records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
	}

	// parse initial request on the last page
	if ($page_number == ceil($total_records / $records_per_page)) {
		$t->set_var("edit_link", "");
		$t->set_var("internal_block", "");
		$t->set_var("assign_to_block", "");
		$t->set_var("style_am","usual_message");
		$t->set_var("mail_data", "");
		$t->set_var("mail_headers", "");
		$t->set_var("mail_body_html", "");
		$t->set_var("mail_body_text", "");
  
		$posted_by = strlen($user_name) ? $user_name . " <" . $user_email . ">" : $user_email;
		$t->set_var("status", NEW_MSG);
		$t->set_var("posted_by", htmlspecialchars($posted_by));
		$t->set_var("date_added", $request_added_string);
		$t->set_var("viewed_by", ADMIN_TITLE_MSG);
		if (is_array($request_viewed)) {
			$t->set_var("date_viewed", "<font color=\"blue\">" . $request_viewed_string. "</font>");
		} else {
			$t->set_var("date_viewed", "<font color=\"red\">".SUPPORT_NOT_VIEWED_MSG."</font>");
		}

		if ($initial_mail_headers || $initial_mail_body_html || $initial_mail_body_text) {
			if ($initial_mail_headers) {
				$t->set_var("admin_support_mail_data_url", "admin_support_mail_data.php?type=header&support_id=". $support_id);
				$t->parse("mail_headers", false);
			}
			if ($initial_mail_body_html) {
				$t->set_var("admin_support_mail_data_url", "admin_support_mail_data.php?type=html&support_id=". $support_id);
				$t->parse("mail_body_html", false);
			}
			if ($initial_mail_body_text) {
				$t->set_var("admin_support_mail_data_url", "admin_support_mail_data.php?type=text&support_id=". $support_id);
				$t->parse("mail_body_text", false);
			}
			$t->parse("mail_data", false);
		}

		// check for attachments
		$sql  = " SELECT * FROM " . $table_prefix . "support_attachments ";
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " AND message_id=0 AND attachment_status=1 ";
		$db->query($sql);
		if ($db->next_record()) {
			$attach_no = 1;
			$attachments_files = ""; 
			do {
				$attachment_id = $db->Record["attachment_id"];
				$file_name     = $db->Record["file_name"];
				$file_path     = $db->Record["file_path"];
				$size	         = get_nice_bytes(filesize($file_path));
				$attachments_files  .= $attach_no . ". <a target=\"_blank\" href=\"admin_support_attachment.php?atid=" . $attachment_id . "\">" . $file_name . "</a> (" . $size . ")&nbsp;&nbsp;";
				$attach_no++;
			} while ($db->next_record());
			$t->set_var("attachments_files", $attachments_files);
			$t->parse("attachments_block",false);
		} else { 
			$t->set_var("attachments_block","");
		}
  
		$description = process_message($description);

		$t->set_var("message_text", $description);
  
		$t->parse("initial_block", false);
		$t->parse("records", true);
	}


	if (!strlen($operation)) // (set default message text for reply)
	{
		$last_message = ">" . str_replace("\n", "\n>", $last_message);
		// add department signature
		$last_message .= $eol . $eol . $dep_signature;
		$r->set_value("message_text", $last_message);
	}

	// check attachments
	$attachments_files = "";
	$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "support_attachments ";
	$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
	$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
	$sql .= " AND message_id=0 ";
	$sql .= " AND attachment_status=0 ";
	$db->query($sql);
	while ($db->next_record()) {
		$attachment_id = $db->f("attachment_id");
		$filename = $db->f("file_name");
		$filepath = $db->f("file_path");
		$filesize = filesize($filepath);
		if ($attachments_files) { $attachments_files .= "; "; }
		$attachments_files .= "<a href=\"admin_support_attachment.php?atid=" .$attachment_id. "\" target=\"_blank\">" . $filename . "</a> (" . get_nice_bytes($filesize) . ")";
	}
	if ($attachments_files) {
		$t->set_var("attached_files", $attachments_files);
		$t->set_var("attachments_class", "display: block;");
	} else {
		$t->set_var("attachments_class", "display: none;");
	}
	$r->set_parameters();

	$tabs = array(
		"reply" => array("title" => REPLY_TO_CUSTOMER_MSG), 
		"assign" => array("title" => REASSIGN_TICKET_MSG), 
		"reply_to_admin" => array("title" => REPLY_TO_NAME_MSG, "show" => false), 
		"knowledge" => array("title" => ADD_KNOWLEDGE_BASE_MSG, "show" => false), 
	);

	// check last message
	$sql  = " SELECT sm.admin_id_assign_by,sm.admin_id_assign_to,a.admin_name ";
	$sql .= " FROM (" . $table_prefix . "support_messages sm ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id=sm.admin_id_assign_by) ";
	$sql .= " WHERE sm.support_id=" . $db->tosql($support_id, INTEGER);
	$sql .= " ORDER BY sm.date_added DESC ";
	$db->RecordsPerPage = 1;
	$db->PageNumber = 1;
	$db->query($sql);
	if ($db->next_record()) {
		$admin_id_assign_by = $db->f("admin_id_assign_by");
		$admin_id_assign_to = $db->f("admin_id_assign_to");
		$admin_assign_by = $db->f("admin_name");
		$session_admin_id;
		if ($admin_id_assign_by != $session_admin_id && $admin_id_assign_to == $session_admin_id) {
			$reply_to_name = str_replace("{name}", $admin_assign_by, REPLY_TO_NAME_MSG);
			$tabs["reply_to_admin"]["title"] = $reply_to_name;
			$tabs["reply_to_admin"]["show"] = true;
			$t->set_var("reply_to_admin", $admin_assign_by);
			$t->set_var("last_admin_id_assign_by", $admin_id_assign_by);
		}
	}

	// check Knowledge Base
	if (strlen($knowledge_category_id)) {
		$tabs["knowledge"]["show"] = true;
	}

	if (!$operation) { $operation = "reply"; }
	parse_admin_tabs($tabs, $operation);

	$t->set_var("operation", $operation);
	$t->set_var("button_name", $tabs[$operation]["title"]);
	$t->set_var("page", $page_number);
	$t->set_var("rp", urlencode($return_page));
	if ($allow_reply) {
		$t->parse("reply_button", false);
	}
	
	$t->pparse("main");

?>