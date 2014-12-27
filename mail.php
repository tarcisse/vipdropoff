<?php
require_once('web/PHPMailer_v5.1/class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail             = new PHPMailer();

$body             = $message;
//$body             = eregi_replace("[\]",'',$body);

$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = "mail.congomarket.fr"; // SMTP server
$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                          // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
$mail->Host       = "mail.congomarket.fr";      // sets GMAIL as the SMTP server
$mail->Port       = 587;                   // set the SMTP port for the GMAIL server
$mail->Username   = "Annonce@congomarket.fr";  // GMAIL username
$mail->Password   = "annoncepauline";            // GMAIL password

$mail->SetFrom('Annonce@congomarket.fr');

//$mail->AddReplyTo($adresse);

$mail->Subject    = $sujet;

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$address = $c;
$mail->AddAddress($address, "Agenda le pagne & vous");

//$mail->AddAttachment("images/phpmailer.gif");      // attachment
//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

if(!$mail->Send()) {
 // echo "Mailer Error: " . $mail->ErrorInfo;
 die ("une erreur inatendue s' est produite, verifier si un mail vous a été envoyez");
} else {
  
 //echo "Message sent!";
}
?>