<?php

include "database/connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

date_default_timezone_set("Asia/Kolkata");


/* =====================================================
   CURRENT TIME
===================================================== */

$currentTime = time();


/* =====================================================
   GET MEETINGS
===================================================== */

$sql = "
    SELECT
        id,
        title,
        meeting_date,
        start_time,
        end_time,
        description
    FROM meetings
";

$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "Meeting Query Error: " .
        mysqli_error($conn)
    );

}


/* =====================================================
   CHECK EACH MEETING
===================================================== */

while ($row = mysqli_fetch_assoc($result)) {


    $meeting_id =
        intval($row['id']);

    $meetingDateTime =
        $row['meeting_date'] .
        ' ' .
        $row['start_time'];


    $meetingTime =
        strtotime($meetingDateTime);


    $difference =
        $meetingTime - $currentTime;


    /* =================================================
       CHECK REMINDER TYPE
    ================================================= */

    $reminder_type = "";


    /*
    1 HOUR REMINDER
    59 minutes to 61 minutes
    */

    if (
        $difference >= 3540 &&
        $difference <= 3660
    ) {

        $reminder_type = "1 Hour";

    }


    /*
    10 MINUTES REMINDER
    9 minutes to 11 minutes
    */

    elseif (
        $difference >= 540 &&
        $difference <= 660
    ) {

        $reminder_type = "10 Minutes";

    }


    /*
    No reminder required
    */

    if ($reminder_type == "") {

        continue;

    }


    /* =================================================
       GET EMPLOYEES FOR THIS MEETING
    ================================================= */

    $employee_sql = "

        SELECT
            e.id,
            e.full_name,
            e.email

        FROM meeting_participants mp

        INNER JOIN employee e

            ON mp.employee_id = e.id

        WHERE mp.meeting_id = ?

    ";


    $employee_stmt =
        $conn->prepare(
            $employee_sql
        );


    $employee_stmt->bind_param(
        "i",
        $meeting_id
    );


    $employee_stmt->execute();


    $employee_result =
        $employee_stmt->get_result();


    /* =================================================
       CHECK EMPLOYEES
    ================================================= */

    if (
        $employee_result->num_rows == 0
    ) {

        echo
            "No employees assigned for meeting: "
            . htmlspecialchars(
                $row['title']
            )
            . "<br>";

        $employee_stmt->close();

        continue;

    }


    /* =================================================
       SEND MAIL TO EACH EMPLOYEE
    ================================================= */

    while (
        $employee =
        $employee_result->fetch_assoc()
    ) {


        $employee_name =
            $employee['full_name'];

        $employee_email =
            $employee['email'];


        /* =============================================
           CREATE PHPMailer
        ============================================= */

        $mail =
            new PHPMailer(true);


        try {


            /* =========================================
               SMTP
            ========================================= */

            $mail->isSMTP();

            $mail->Host =
                'smtp.gmail.com';

            $mail->SMTPAuth =
                true;


            /* =========================================
               GMAIL ACCOUNT
            ========================================= */

            $mail->Username =
                'Veeramuthu9191@gmail.com';


            /* =========================================
               GOOGLE APP PASSWORD
               
               IMPORTANT:
               Paste your NEW App Password here.
            ========================================= */

            $mail->Password =
                'ppbalqkxvzqjffpk';


            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port =
                587;


            /* =========================================
               SENDER
            ========================================= */

            $mail->setFrom(
           'Veeramuthu9191@gmail.com',
           'Smart Meeting System'
          );
            /* =========================================
               EMPLOYEE RECEIVER
            ========================================= */

            $mail->addAddress(
                $employee_email,
                $employee_name
            );


            /* =========================================
               HTML EMAIL
            ========================================= */

            $mail->isHTML(true);


            /* =========================================
               SUBJECT
            ========================================= */

            $mail->Subject =
                "Smart Meeting Reminder - " .
                $reminder_type;


            /* =========================================
               EMAIL BODY
            ========================================= */

            $mail->Body = "

                <div style='
                    font-family: Arial, sans-serif;
                    padding: 20px;
                '>

                    <h2>
                        🔔 Smart Meeting Reminder
                    </h2>

                    <p>
                        Hello
                        <strong>"
                        .
                        htmlspecialchars(
                            $employee_name
                        )
                        .
                        "</strong>,
                    </p>

                    <p>
                        Your meeting is starting in
                        <strong>"
                        .
                        htmlspecialchars(
                            $reminder_type
                        )
                        .
                        "</strong>.
                    </p>

                    <hr>

                    <p>
                        <strong>
                            Project / Meeting Title:
                        </strong>
                        "
                        .
                        htmlspecialchars(
                            $row['title']
                        )
                        .
                        "
                    </p>

                    <p>
                        <strong>
                            Meeting Date:
                        </strong>
                        "
                        .
                        htmlspecialchars(
                            $row['meeting_date']
                        )
                        .
                        "
                    </p>

                    <p>
                        <strong>
                            Start Time:
                        </strong>
                        "
                        .
                        htmlspecialchars(
                            $row['start_time']
                        )
                        .
                        "
                    </p>

                    <p>
                        <strong>
                            End Time:
                        </strong>
                        "
                        .
                        htmlspecialchars(
                            $row['end_time']
                        )
                        .
                        "
                    </p>

                    <p>
                        <strong>
                            Description:
                        </strong>
                        "
                        .
                        htmlspecialchars(
                            $row['description']
                        )
                        .
                        "
                    </p>

                    <hr>

                    <p>
                        Please be ready for the meeting.
                    </p>

                    <p>
                        <strong>
                            Smart Meeting System
                        </strong>
                    </p>

                </div>

            ";


            /* =========================================
               SEND
            ========================================= */

            $mail->send();


            echo
                "Reminder sent successfully to: "
                .
                htmlspecialchars(
                    $employee_email
                )
                .
                " - "
                .
                htmlspecialchars(
                    $reminder_type
                )
                .
                " - "
                .
                htmlspecialchars(
                    $row['title']
                )
                .
                "<br>";


        }


        catch (Exception $e) {


            echo
                "Mail Error for "
                .
                htmlspecialchars(
                    $employee_email
                )
                .
                " : "
                .
                htmlspecialchars(
                    $mail->ErrorInfo
                )
                .
                "<br>";

        }

    }


    $employee_stmt->close();

}

?>