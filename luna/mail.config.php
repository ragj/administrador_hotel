<?php 

global $mail;
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->isHTML(true);
$mail->SMTPAuth = true;

$mail->SMTPSecure = 'ssl';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->Username = "lozano.travel.contacto@gmail.com";
$mail->Password = "wrVV4Jzd3qXK4G7B!*";
$mail->SetFrom("lozano.travel.contacto@gmail.com", "lozano.travel.contacto@gmail.com");



?>