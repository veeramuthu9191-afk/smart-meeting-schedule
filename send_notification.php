<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../PHPMailer-master/src/Exception.php";
require "../PHPMailer-master/src/PHPMailer.php";
require "../PHPMailer-master/src/SMTP.php";


$mail = new PHPMailer(true);

try
{
    $mail->isSMTP();

    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;

    $mail->Username = "Veeramuthu9191@gmail.com";
    $mail->Password = "gomathi@9191";

    $mail->SMTPSecure = "tls";
    $mail->Port = 587;


    $mail->setFrom("yourgmail@gmail.com","Smart Meeting System");

    $mail->addAddress("employee@gmail.com");


    $mail->Subject = "Meeting Reminder";

    $mail->Body = "Your meeting reminder notification.";


    $mail->send();

    echo "Mail Sent Successfully";

}
catch(Exception $e)
{
    echo "Mail Error : ".$mail->ErrorInfo;
}

?>