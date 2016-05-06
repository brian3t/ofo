<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ideal_functions.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * iDEAL (www.ing-ideal.nl) transaction handler by www.viart.com
 */

	function ideal_unhtmlentities($string){
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);

		return strtr($string, $trans_tbl);
	}

	function ideal_stripsimbls( $message ){
		$message = str_replace( " ", "", $message );
		$message = str_replace( "\t", "", $message );
		$message = str_replace( "\n", "", $message );
        
		return $message;
	}

	function ideal_createCertFingerprint($filename){
		$fp = fopen($filename, "r");
		if (!$fp) {
			return false;
		}
		$cert = fread($fp, 8192);
		fclose($fp);

		$data = openssl_x509_read($cert);

		if (!openssl_x509_export($data, $data)) {
			return false;
		}

		$data = str_replace("-----BEGIN CERTIFICATE-----", "", $data);
		$data = str_replace("-----END CERTIFICATE-----", "", $data);
		$data = base64_decode($data);
		$fingerprint = sha1($data);
		$fingerprint = strtoupper($fingerprint);

		return $fingerprint;
	}

	function ideal_signMessage($priv_keyfile, $key_pass, $data){
		$fp = fopen($priv_keyfile , "r");
		$priv_key = fread($fp, 8192);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key, $key_pass);
		openssl_sign($data, $signature, $pkeyid);
		openssl_free_key($pkeyid);

		return $signature;
	}

	function ideal_PostToHost($url, $timeout, $data_to_send){
		$idx = strrpos($url, ":");
		$host = substr($url, 0, $idx);
		$url = substr($url, $idx + 1);
		$idx = strpos($url, "/");
		$port = substr($url, 0, $idx);
		$path = substr($url, $idx);
		if (!strlen($port)){
			$idx = strrpos($host, ":");
			$protocol = substr($host, 0, $idx);
			switch ($protocol) {
				case 'ssl':
					$port = 443;
					break;
				case 'https':
					$port = 443;
					break;
				case 'http':
					$port = 80;
					break;
			}
		}
		$fsp = fsockopen($host, $port, $errno, $errstr, $timeout);
		if ($fsp) {
			fputs($fsp, "POST $path HTTP/1.0\r\n");
			fputs($fsp, "Accept: text/html\r\n");
			fputs($fsp, "Accept: charset=ISO-8859-1\r\n");
			fputs($fsp, "Content-Length:".strlen($data_to_send)."\r\n");
			fputs($fsp, "Content-Type: text/html; charset=ISO-8859-1\r\n\r\n");
			fputs($fsp, $data_to_send, strlen($data_to_send));

			$res="";
			while(!feof($fsp)) {
				$res .= fgets($fsp, 128);
			}
			fclose($fsp);
			return $res;
		}else {
			return "Error: " . $errstr;
		}
	}

	function ideal_verifyMessage($certfile, $data, $signature){
		$ok=0;
		$fp = fopen( $certfile, "r");

		if(!$fp) {
			return false;
		}

		$cert = fread($fp, 8192);
		fclose($fp);
		$pubkeyid = openssl_get_publickey($cert);

		$ok = openssl_verify($data, $signature, $pubkeyid);

		openssl_free_key($pubkeyid);

		return $ok;
	}
?>