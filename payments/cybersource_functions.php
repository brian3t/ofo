<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  cybersource_functions.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Cybersource (www.cybersource.com) transaction handler functions by ViArt Ltd. (www.viart.com)
 */

	function php_hmacsha1($data, $key) {
		$klen = strlen($key);
		$blen = 64;
		$ipad = str_pad("", $blen, chr(0x36));
		$opad = str_pad("", $blen, chr(0x5c));

		if ($klen <= $blen) {
			while (strlen($key) < $blen) {
				$key .= "\0";
			}				#zero-fill to blocksize
		} else {
			$key = cybs_sha1($key);	#if longer, pre-hash key
		}
		$key = str_pad($key, strlen($ipad) + strlen($data), "\0");
		return cybs_sha1(($key ^ $opad) . cybs_sha1($key ^ $ipad . $data));
	}

	# calculates SHA-1 digest of the input string
	# cleaned up from John Allen's "SHA in 8 lines of perl5"
	# at http://www.cypherspace.org/~adam/rsa/sha.html
	#
	# returns the hash in a (binary) string

	function cybs_sha1($in) {
		/*
		Due to problems with standard hash calculation.
		Visit this Cybersource Knowledge Base article for details.
		http://www.cybersource.com/cgi-bin/kb/kbanswersdetail.cgi?solution_id=1155&category=Known%20Issues&sub_category=All
		*/
		return pack("H*", sha1($in));
		//return mhash(MHASH_SHA1, $in);

		$indx = 0;
		$chunk = "";

		$A = array(1732584193, 4023233417, 2562383102,  271733878, 3285377520);
		$K = array(1518500249, 1859775393, 2400959708, 3395469782);
		$a = $b = $c = $d = $e = 0;
		$l = $p = $r = $t = 0;

		do{
			$chunk = substr($in, $l, 64);
			$r = strlen($chunk);
			$l += $r;

			if ($r<64 && !$p++) {
				$r++;
				$chunk .= "\x80";
			}
			$chunk .= "\0\0\0\0";
			while (strlen($chunk) % 4 > 0) {
				$chunk .= "\0";
			}
			$len = strlen($chunk) / 4;
			if ($len > 16) $len = 16;
			$fmt = "N" . $len;
			$W = array_values(unpack($fmt, $chunk));
			if ($r < 57 ) {
				while (count($W) < 15) {
					array_push($W, "\0");
				}
				$W[15] = $l*8;
			}

			for ($i = 16; $i <= 79; $i++) {
				$v1 = d($W, $i-3);
				$v2 = d($W, $i-8);
				$v3 = d($W, $i-14);
				$v4 = d($W, $i-16);
				array_push($W, L($v1 ^ $v2 ^ $v3 ^ $v4, 1));
			}

			list($a,$b,$c,$d,$e)=$A;

			for ($i = 0; $i<=79; $i++) {
				$t0 = 0;
				switch(intval($i/20)) {
					case 1:
					case 3:
						$t0 = F1($b, $c, $d);
						break;
					case 2:
						$t0 = F2($b, $c, $d);
						break;
					default:
						$t0 = F0($b, $c, $d);
						break;
				}
				$t = M($t0 + $e  + d($W, $i) + d($K, $i/20) + L($a, 5));
				$e = $d;
				$d = $c;
				$c = L($b,30);
				$b = $a;
				$a = $t;
			}

			$A[0] = M($A[0] + $a);
			$A[1] = M($A[1] + $b);
			$A[2] = M($A[2] + $c);
			$A[3] = M($A[3] + $d);
			$A[4] = M($A[4] + $e);

		}while ($r>56);
		$v = pack("N*", $A[0], $A[1], $A[2], $A[3], $A[4]);
		return $v;
	}

	#### Ancillary routines used by sha1

	function dd($x) {
		if (defined($x)) return $x;
		return 0;
	}

	function d($arr, $x) {
		if ($x < count($arr)) return $arr[$x];
		return 0;
	}

	function F0($b, $c, $d) {
		return $b & ($c ^ $d) ^ $d;
	}

	function F1($b, $c, $d) {
		return $b ^ $c ^ $d;
	}

	function F2($b, $c, $d) {
		return ($b | $c) & $d | $b & $c;
	}

	# ($num)
	function M($x) {
		$m = 1+~0;
		if ($m == 0) return $x;
		return($x - $m * intval($x/$m));
	}

	# ($string, $count)
	function L($x, $n) {
		return ( ($x<<$n) | ((pow(2, $n) - 1) & ($x>>(32-$n))) );
	}

	####
	#### end of HMAC SHA1 implementation #####


	####
	#### HOP functions
	#### Copyright 2003, CyberSource Corporation.  All rights reserved.
	####

	function getmicrotime(){
		list($usec, $sec) = explode(" ",microtime());
		$usec = (int)((float)$usec * 1000);
		while (strlen($usec) < 3) { $usec = "0" . $usec; }
		return $sec . $usec;
	}


	function hopHash($data, $key) {
		return base64_encode(php_hmacsha1($data, $key));
	}

	function VerifySignature($data, $signature, $pub) {
		$pub_digest = hopHash($data, $pub);
		return strcmp($pub_digest, $signature) == 0;
	}

	####
	#### end of HOP functions

	// begin ViArt functions for Cybersource

?>