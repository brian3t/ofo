<?php

	$username = "type_here_your_user_name";
	$password = "type_here_your_password";

	// Send SMS to N numbers
	function sms_send($recipient, $message, $originator = "")
	{
		global $username, $password;

		$message = str_replace("\r", "", $message);
		$message = str_replace("\n", " ", $message);
		$message = str_replace("\t", " ", $message);

		if (!$recipient || !$message) {
			return false;
		}
		
		$url_send = "http://212.58.4.8/xmlSms/xmlSms.php";
		$url = parse_url($url_send);
		$server_name = $url["host"];
		$server_path = $url["path"];
		$xml = '<?xml version="1.0"?' . '><PACKET><USERNAME>' . $username . '</USERNAME><PASSWORD>' . $password . '</PASSWORD><HEADER>' . $originator . '</HEADER><STARTDATE></STARTDATE><EXPIREDATE></EXPIREDATE><PHONENUMBER>' . $recipient . '</PHONENUMBER><MESSAGE>' . $message . '</MESSAGE></PACKET>';
		$result = "";
	
		$fp = fsockopen($server_name, 80, $errno, $errstr, 30);
		if (!$fp) {
			echo "An error occured while opening remote server: $errstr ($errno)<br />\n";
			return false;
		} else {
			$out = "POST " . $server_path . " HTTP/1.0\r\n";
			$out .= "Host: $server_name\r\n";
			$out .= "User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Type: text/xml; charset=ISO-8859-1\r\n";
			$out .= "Content-Length: " . strlen($xml) . "\r\n\r\n";
			$out .= $xml;
			
			fwrite($fp, $out);
			while (!feof($fp)) {
				$result .= fgets($fp, 4096);
			}
			fclose($fp);
		}
	
		if ($result) {
			//echo nl2br(htmlspecialchars($out)) . "<hr>";
			//echo nl2br(htmlspecialchars($result)) . "<hr>";
			$result = strip_tags(substr($result, strpos($result, "\r\n\r\n") + strlen("\r\n\r\n")));
			// get SMS ID
			$result_old = $result;
			$result = substr($result, strpos($result, "10-") + strlen("10-"));
			if ($result == $result_old) {
				return false;
			}
		} else {
			return false;
		}
	
		// return SMS ID in case of success
		return $result;
	}

	// Get delivery report of SMS message
	function sms_get_status($sms_id) 
	{
		global $username, $password;

		if (!is_numeric($sms_id)) {
			return false;
		}
		
		$url_report = "http://212.58.4.8/xmlSms/xmlReport.php";
		$url = parse_url($url_report);
		$server_name = $url["host"];
		$server_path = $url["path"];
		$xml = '<?xml version="1.0"' . chr(63) . '><PACKET><USERNAME>' . $username . '</USERNAME><PASSWORD>' . $password . '</PASSWORD><SMSID>' . $sms_id . '</SMSID><ACTION>0</ACTION></PACKET>';

		$result = "";
	
		$fp = fsockopen($server_name, 80, $errno, $errstr, 30);
		if (!$fp) {
			echo "An error occured while opening remote server: $errstr ($errno)<br />\n";
			return false;
		} else {
			$out = "POST " . $server_path . " HTTP/1.0\r\n";
			$out .= "Host: $server_name\r\n";
			$out .= "User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Type: text/xml; charset=ISO-8859-1\r\n";
			$out .= "Content-Length: " . strlen($xml) . "\r\n\r\n";
			$out .= $xml;
			
			fwrite($fp, $out);
			while (!feof($fp)) {
				$result .= fgets($fp, 4096);
			}
			fclose($fp);
		}
	
		if ($result) {
			$result = strip_tags(substr($result, strpos($result, "\r\n\r\n") + strlen("\r\n\r\n")));
		} else {
			return false;
		}
	
		// return SMS status in case of success
		return $result;
	}

	// Ask the user's available credits on server
	function sms_get_balance() 
	{
		global $username, $password;
	
		$url_balance = "http://212.58.4.8/xmlSms/getCredit.php";
		$url = parse_url($url_balance);
		$server_name = $url["host"];
		$server_path = $url["path"];
		$xml = '<?xml version="1.0"' . chr(63) . '><PACKET><USERNAME>' . $username . '</USERNAME><PASSWORD>' . $password . '</PASSWORD></PACKET>';

		$result = "";
	
		$fp = fsockopen($server_name, 80, $errno, $errstr, 30);
		if (!$fp) {
			echo "An error occured while opening remote server: $errstr ($errno)<br />\n";
			return false;
		} else {
			$out = "POST " . $server_path . " HTTP/1.0\r\n";
			$out .= "Host: $server_name\r\n";
			$out .= "User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Type: text/xml; charset=ISO-8859-1\r\n";
			$out .= "Content-Length: " . strlen($xml) . "\r\n\r\n";
			$out .= $xml;
			
			fwrite($fp, $out);
			while (!feof($fp)) {
				$result .= fgets($fp, 4096);
			}
			fclose($fp);
		}
	
		if ($result) {
			$result = strip_tags(substr($result, strpos($result, "\r\n\r\n") + strlen("\r\n\r\n")));
		} else {
			return false;
		}
	
		// return SMS status in case of success
		return $result;
	}

	/* For future use

	$url_private = "http://212.58.4.8/xmlSms/xmlSmsPrivate.php";
	$url_originator = "http://212.58.4.8/xmlSms/addOriginator.php";
	
	// Send more than one messages to more than number
	$xml_private = '<?xml version="1.0"' . chr(63) . '><PACKET><USERNAME>' . $username . '</USERNAME><PASSWORD>' . $password . '</PASSWORD><HEADER>' . $originator . '</HEADER><STARTDATE></STARTDATE><EXPIREDATE></EXPIREDATE><PHONENUMBER>' . $recipient . '</PHONENUMBER><MESSAGE><![CDATA[TEST1@@@TEST2]]></MESSAGE></PACKET>';
	// Define ORIGINATOR 
	$xml_origanator = '<?xml version="1.0"' . chr(63) . '><PACKET><USERNAME>' . $username . '</USERNAME><PASSWORD>' . $password . '</PASSWORD><ORIGINATOR>' . $originator . '</ORIGINATOR></PACKET>';
	
	*/

?>