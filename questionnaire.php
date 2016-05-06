<?php

// ------------- CONFIGURABLE SECTION ------------------------

// $mailto - set to the email address you want the form
// sent to, eg
//$mailto		= "youremailaddress@example.com" ;

$mailto = 'esupport@oilfiltersonline.com' ;

// $subject - set to the Subject line of the email, eg
//$subject	= "Feedback Form" ;

$subject = 'Questionnaire' ;

// the pages to be displayed, eg
//$formurl		= "http://www.example.com/feedback.html" ;
//$errorurl		= "http://www.example.com/error.html" ;
//$thankyouurl	= "http://www.example.com/thankyou.html" ;

$formurl = "http://www.oilfiltersonline.com/questionnaire.html" ;
$errorurl = "http://www.oilfiltersonline.com/formerror.html" ;
$thankyouurl = "http://www.oilfiltersonline.com/thankyouquestionnaire.html" ;

$email_is_required = 0;
$name_is_required = 0;
$uself = 0;
$use_envsender = 0;
$use_utf8 = 1;

// -------------------- END OF CONFIGURABLE SECTION ---------------

$headersep = (!isset( $uself ) || ($uself == 0)) ? "\r\n" : "\n" ;
$content_type = (!isset( $use_utf8 ) || ($use_utf8 == 0)) ? 'Content-Type: text/plain; charset="iso-8859-1"' : 'Content-Type: text/plain; charset="utf-8"' ;
if (!isset( $use_envsender )) { $use_envsender = 0 ; }
$envsender = "-f$mailto" ;
$name = 'Questionnaire' ;
$email = $mailto ;
$comments = $_POST['filter_found']."\n".$_POST['desired_type']."\n".$_POST['likely_purchase']."\n".$_POST['visit_reason']."\n".$_POST['buy_reason'] ;
$http_referrer = getenv( "HTTP_REFERER" );


$messageproper =
	"------------------------- Questionnaire -------------------------\n\n" .
	$comments .
	"\n\n------------------------------------------------------------\n" ;

$headers =
	"From: \"$name\" <$email>" . $headersep . "Reply-To: \"$name\" <$email>" . $headersep . "X-Mailer: chfeedback.php 2.12.0" .
	$headersep . 'MIME-Version: 1.0' . $headersep . $content_type ;


mail($mailto, $subject, $messageproper, $headers );

header( "Location: $thankyouurl" );
exit ;

?>

