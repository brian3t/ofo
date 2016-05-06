<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  pgp_functions.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/**
 * PGP extension
 * @version 1.0
 * Howto test
 * 1) create your key pair
 * gpg --gen-key
 * 2) view key list
 * gpg --list-keys
 * 3) send your key to keyserver
 * gpg --keyserver "YOUR_KEYSERVER" --send-keys "YOUR_UID"'
 * example
 * gpg --keyserver "keyserver.ubuntu.com" --keyserver-options http-proxy="http://ksenya:pRk-l=pDb@viart.com.ua:8080"  --send-keys "59B1998B"
 */

function pgp_test() {
	global $settings;
	$pgp_binary = get_setting_value($settings, "pgp_binary"); 
	
	$command_line = '"' . $pgp_binary . '" --help';
	exec($command_line, $output);
	if (!$output) {
		echo "GnuPG Binary isn`t availiable<br/>";
		echo "Current Binary Path <b>" . $pgp_binary . "</b>";
		return false;
	} else {
		return true;
	}	
}
function pgp_encrypt($data, $email) {	
	global $settings;
	$pgp_binary = get_setting_value($settings, "pgp_binary",0); 
	$pgp_home   = get_setting_value($settings, "pgp_home",0); 
	$pgp_tmp    = get_setting_value($settings, "pgp_tmp",0); 
	$pgp_proxy  = get_setting_value($settings, "pgp_proxy",0); 
	$pgp_ascii  = get_setting_value($settings, "pgp_ascii",0); 
	$pgp_keyserver = get_setting_value($settings, "pgp_keyserver",0); 
	
	
	$file_name = $pgp_tmp .'\\'. md5(time());	
	$fp = fopen($file_name,'w');
	fwrite($fp,$data);
	fclose($fp);	
	
	$command_line  = " call \"$pgp_binary\" ";
	if ($pgp_keyserver) {
		$command_line .= "--keyserver $pgp_keyserver";
	}
	if ($pgp_proxy) {
		$command_line .= " --keyserver-options http-proxy=$pgp_proxy ";
	}
	if ($pgp_home) {
		$command_line .= " --homedir \"$pgp_home\" ";
	}
	if ($pgp_ascii) {
		$command_line .= " -a ";		
	}
	$command_line .= "   --batch --always-trust -e -r  $email \"$file_name\" ";
	exec($command_line,$output);
	if ( $pgp_ascii ) {
		$new_file_name = $file_name . '.asc';
	} else {
		$new_file_name = $file_name . '.gpg';
	}
	unlink($file_name);
	if( file_exists($new_file_name)) {
		$fp = fopen($new_file_name, 'r');
		$encrypted = fread($fp, filesize($new_file_name));
		fclose($fp);	 
		unlink($new_file_name);
		return  $encrypted;
	} else {
		echo "Cant encrypt mail<br/> Check out your PGP settings";
		return false;
	}
}