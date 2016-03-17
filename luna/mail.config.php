<?php 

global $mail;
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->isHTML(true);
$mail->SMTPAuth = true;

$mail->SMTPSecure = 'ssl';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->Username = "jzebadua@denumeris.com";
$mail->Password = "4dm1n4dm1n";
$mail->SetFrom("jzebadua@denumeris.com", "jzebadua@denumeris.com");



?>