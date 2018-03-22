<?php
require_once "Mail.php";
 
$from = "Test <niaga@mystellar.org>";  //Email yang anda buat di hostingnya
$to = "Nobody <amankharbanda007@gmail.com>";  //Email tujuan yang sudah anda miliki
$subject = "Test email using PHP SMTP\r\n\r\n";
$body = "This is a test email message";
 
$host = "srv19.niagahoster.com";  //SMTP server hosting anda
$username = "niaga@mystellar.org";  //SMTP akun Username
$password = "~)O~$0m!9tAi";  //Password SMTP anda
$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'auth' => true,
    'username' => $username,
    'password' => $password));
 
$mail = $smtp->send($to, $headers, $body);
 
if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
} else {
  echo("<p>Message successfully sent!</p>");
}
?>