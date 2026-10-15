<?php

include "../database/connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';


$meeting_id = $_POST['meeting_id'];


if (isset($_POST['employee']))
{
    $employee = $_POST['employee'];

    foreach ($employee as $id)
    {
        // Get employee details
        $query = "SELECT full_name, email
                  FROM employee
                  WHERE id='$id'";

        $result = mysqli_query($conn, $query);

        $row = mysqli_fetch_assoc($result);

        if (!$row)
        {
            echo "Employee not found.";
            exit;
        }

        $employee_name = $row['full_name'];
        $employee_email = $row['email'];


        // Save employee invite
        $insert = "INSERT INTO meeting_participants
                   (meeting_id, employee_id, status)
                   VALUES
                   ('$meeting_id', '$id', 'Pending')";


        if (mysqli_query($conn, $insert))
        {
            // Database saved successfully


            // =========================
            // SEND EMAIL
            // =========================

            $mail = new PHPMailer(true);

            try
            {
                // SMTP Settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;

                // YOUR GMAIL
                $mail->Username   = 'veeramuthu9191@gmail.com';

                // YOUR GMAIL APP PASSWORD
                $mail->Password   ='ppbalqkxvzqjffpk';


                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;


                // Sender
                $mail->setFrom(
                    'veeramuthu9191@gmail.com',
                    'Smart Meeting System'
                );


                // Employee email
                $mail->addAddress(
                    $employee_email,
                    $employee_name
                );


                // Email content
                $mail->isHTML(true);

                $mail->Subject = 'Meeting Invitation';

                $mail->Body = "
                    <h2>Meeting Invitation</h2>

                    <p>Hello <b>$employee_name</b>,</p>

                    <p>You have been invited to a meeting.</p>

                    <p>
                        Please login to the Smart Meeting System
                        to view the meeting details.
                    </p>

                    <p>Thank you.</p>

                    <br>

                    <b>Smart Meeting System</b>
                ";


                // Send mail
                $mail->send();
            }
            catch (Exception $e)
            {
                echo "Mail Error: " . $mail->ErrorInfo;
                exit;
            }
        }
        else
        {
            echo "Database Error: " . mysqli_error($conn);
            exit;
        }
    }

    // Automatically ensure Windows reminder tasks are registered for 1 Hour & 10 Minutes
    require_once dirname(__DIR__) . "/create_meeting_tasks.php";
    scheduleMeetingReminderTasks($meeting_id, $conn);

    echo "Employees saved, invitation mail sent, and automated reminders (1 Hour & 10 Minutes before meeting) scheduled successfully.";
}
else
{
    echo "No Employee Selected";
}

?>
