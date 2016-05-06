<?php

// ------------- CONFIGURABLE SECTION ------------------------

// $mailto - set to the email address you want the form
// sent to, eg
//$mailto		= "youremailaddress@example.com" ;

$mailto = 'support@oilfiltersonline.com' ;

// $subject - set to the Subject line of the email, eg
//$subject	= "Feedback Form" ;

$subject = "Filter Finder Question" ;

// the pages to be displayed, eg
//$formurl		= "http://www.example.com/feedback.html" ;
//$errorurl		= "http://www.example.com/error.html" ;
//$thankyouurl	= "http://www.example.com/thankyou.html" ;

$formurl = "http://www.oilfiltersonline.com/searchmailform.html" ;
$errorurl = "http://www.oilfiltersonline.com/formerror.html" ;
$thankyouurl = "http://www.oilfiltersonline.com/thankyou.html" ;

$email_is_required = 1;
$name_is_required = 1;
$uself = 0;
$use_envsender = 0;
$use_utf8 = 1;

// -------------------- END OF CONFIGURABLE SECTION ---------------

$headersep = (!isset( $uself ) || ($uself == 0)) ? "\r\n" : "\n" ;
$content_type = (!isset( $use_utf8 ) || ($use_utf8 == 0)) ? 'Content-Type: text/plain; charset="iso-8859-1"' : 'Content-Type: text/plain; charset="utf-8"' ;
if (!isset( $use_envsender )) { $use_envsender = 0 ; }
$envsender = "-f$mailto" ;
$name = $_POST['name'] ;
$email = $_POST['email'] ;
$comments = $_POST['comments'] ;
$signup = $_POST['signup'] ;
$http_referrer = getenv( "HTTP_REFERER" );

if (!isset($_POST['email'])) {
	header( "Location: $formurl" );
	exit ;
}
if (($email_is_required && (empty($email) || !ereg("@", $email))) || ($name_is_required && empty($name))) {
	header( "Location: $errorurl" );
	exit ;
}
if ( ereg( "[\r\n]", $name ) || ereg( "[\r\n]", $email ) ) {
	header( "Location: $errorurl" );
	exit ;
}
if (empty($email)) {
	$email = $mailto ;
}

if (get_magic_quotes_gpc()) {
	$comments = stripslashes( $comments );
}

$messageproper =
	"This message was sent from:\n" .
	"$http_referrer\n" .
	"------------------------------------------------------------\n" .
	"Name of sender: $name\n" .
	"Email of sender: $email\n" .
	"------------------------- COMMENTS -------------------------\n\n" .
	$comments .
	"\n\n------------------------------------------------------------\n" .
	"Checkbox: " .
	$signup;

$headers =
	"From: \"$name\" <$email>" . $headersep . "Reply-To: \"$name\" <$email>" . $headersep . "X-Mailer: chfeedback.php 2.12.0" .
	$headersep . 'MIME-Version: 1.0' . $headersep . $content_type ;

if ($use_envsender) {
	mail($mailto, $subject, $messageproper, $headers, $envsender );
}
else {
	mail($mailto, $subject, $messageproper, $headers );
}
header( "Location: $thankyouurl" );
exit ;

?>

