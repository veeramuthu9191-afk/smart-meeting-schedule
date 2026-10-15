<?php

session_start();

include "../database/connection.php";

date_default_timezone_set("Asia/Kolkata");

$employee_id = $_SESSION['employee_id'];

$query = "SELECT 
            meetings.title,
            meetings.meeting_date,
            meetings.start_time,
            meetings.end_time,
            meetings.description
          FROM meeting_participants
          INNER JOIN meetings 
          ON meeting_participants.meeting_id = meetings.id
          WHERE meeting_participants.employee_id = '$employee_id'
          ORDER BY meetings.id DESC";

$result = mysqli_query($conn, $query);

$currentTime = time();

$hasNotification = false;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notifications</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f7fb;
            color: #263238;
        }

        .notification-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            padding: 25px 30px;
            border-radius: 18px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .page-header h2 {
            margin: 0;
            font-size: 28px;
            color: #202938;
        }

        .page-header p {
            margin: 8px 0 0;
            color: #7b8494;
        }

        .notification-card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.07);
            border-left: 5px solid #4f46e5;
            transition: 0.3s;
        }

        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.10);
        }

        .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .notification-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: #eef2ff;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .notification-title h3 {
            margin: 0;
            font-size: 18px;
            color: #202938;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-new {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-hour {
            background: #fff3cd;
            color: #856404;
        }

        .badge-ten {
            background: #fee2e2;
            color: #b91c1c;
        }

        .meeting-title {
            font-size: 21px;
            font-weight: 700;
            color: #4f46e5;
            margin: 15px 0;
        }

        .meeting-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 15px;
        }

        .detail {
            background: #f8fafc;
            padding: 12px 15px;
            border-radius: 10px;
        }

        .detail-label {
            display: block;
            font-size: 12px;
            color: #8a94a6;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #263238;
        }

        .description {
            margin-top: 15px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 10px;
            color: #596579;
            font-size: 14px;
            line-height: 1.5;
        }

        .no-notification {
            background: white;
            padding: 45px;
            text-align: center;
            border-radius: 18px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .no-notification-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .no-notification h3 {
            margin: 0 0 8px;
            color: #263238;
        }

        .no-notification p {
            margin: 0;
            color: #8a94a6;
        }

        @media (max-width: 650px) {

            .notification-container {
                margin: 20px auto;
                padding: 15px;
            }

            .page-header {
                padding: 20px;
            }

            .page-header h2 {
                font-size: 23px;
            }

            .notification-card {
                padding: 20px;
            }

            .notification-header {
                align-items: flex-start;
                gap: 10px;
            }

            .meeting-details {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="notification-container">

    <div class="page-header">

        <h2>🔔 Meeting Notifications</h2>

        <p>
            Stay updated with your scheduled meetings and reminders.
        </p>

    </div>


<?php

while ($row = mysqli_fetch_assoc($result)) {

    $meetingTime = strtotime(
        $row['meeting_date'] . ' ' . $row['start_time']
    );

    $difference = $meetingTime - $currentTime;


    /*
        NEW MEETING NOTIFICATION

        Meeting scheduled and more than
        10 minutes remaining.
    */

    if ($difference > 660) {

        $hasNotification = true;

?>

        <div class="notification-card">

            <div class="notification-header">

                <div class="notification-title">

                    <div class="notification-icon">
                        🔔
                    </div>

                    <h3>
                        New Meeting Scheduled
                    </h3>

                </div>

                <span class="badge badge-new">
                    NEW
                </span>

            </div>


            <div class="meeting-title">

                <?php
                echo htmlspecialchars($row['title']);
                ?>

            </div>


            <div class="meeting-details">

                <div class="detail">

                    <span class="detail-label">
                        📅 Date
                    </span>

                    <span class="detail-value">

                        <?php
                        echo htmlspecialchars($row['meeting_date']);
                        ?>

                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        🕐 Time
                    </span>

                    <span class="detail-value">

                        <?php
                        echo htmlspecialchars($row['start_time']);
                        ?>

                        -

                        <?php
                        echo htmlspecialchars($row['end_time']);
                        ?>

                    </span>

                </div>

            </div>


            <?php if (!empty($row['description'])) { ?>

                <div class="description">

                    <strong>Description:</strong><br>

                    <?php
                    echo htmlspecialchars($row['description']);
                    ?>

                </div>

            <?php } ?>


        </div>


<?php

    }


    /*
        1 HOUR REMINDER
    */

    elseif ($difference >= 3540 && $difference <= 3660) {

        $hasNotification = true;

?>

        <div class="notification-card"
             style="border-left-color:#f59e0b;">

            <div class="notification-header">

                <div class="notification-title">

                    <div class="notification-icon"
                         style="background:#fff7ed;color:#f59e0b;">

                        ⏰

                    </div>

                    <h3>
                        Meeting Reminder
                    </h3>

                </div>

                <span class="badge badge-hour">
                    1 HOUR
                </span>

            </div>


            <div class="meeting-title"
                 style="color:#d97706;">

                <?php
                echo htmlspecialchars($row['title']);
                ?>

            </div>


            <p>

                Your meeting starts in
                <strong>1 hour</strong>.

            </p>


            <div class="meeting-details">

                <div class="detail">

                    <span class="detail-label">
                        📅 Date
                    </span>

                    <span class="detail-value">

                        <?php
                        echo htmlspecialchars($row['meeting_date']);
                        ?>

                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        🕐 Time
                    </span>

                    <span class="detail-value">

                        <?php
                        echo htmlspecialchars($row['start_time']);
                        ?>

                        -

                        <?php
                        echo htmlspecialchars($row['end_time']);
                        ?>

                    </span>

                </div>

            </div>

        </div>


<?php

    }


    /*
        10 MINUTE REMINDER
    */

    elseif ($difference >= 540 && $difference <= 660) {

        $hasNotification = true;

?>

        <div class="notification-card"
             style="border-left-color:#dc2626;">

            <div class="notification-header">

                <div class="notification-title">

                    <div class="notification-icon"
                         style="background:#fef2f2;color:#dc2626;">

                        🔔

                    </div>

                    <h3>
                        Meeting Reminder
                    </h3>

                </div>

                <span class="badge badge-ten">
                    10 MINUTES
                </span>

            </div>


            <div class="meeting-title"
                 style="color:#dc2626;">

                <?php
                echo htmlspecialchars($row['title']);
                ?>

            </div>


            <p>

                Your meeting starts in
                <strong>10 minutes</strong>.

            </p>


            <div class="meeting-details">

                <div class="detail">

                    <span class="detail-label">
                        📅 Date
                    </span>

                    <span class="detail-value">

                        <?php
                        echo htmlspecialchars($row['meeting_date']);
                        ?>

                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        🕐 Time
                    </span>

                    <span class="detail-value">

                        <?php
                        echo htmlspecialchars($row['start_time']);
                        ?>

                        -

                        <?php
                        echo htmlspecialchars($row['end_time']);
                        ?>

                    </span>

                </div>

            </div>

        </div>


<?php

    }

}


if (!$hasNotification) {

?>

    <div class="no-notification">

        <div class="no-notification-icon">
            🔕
        </div>

        <h3>
            No New Notifications
        </h3>

        <p>
            You don't have any new meeting notifications right now.
        </p>

    </div>

<?php

}

?>

</div>

</body>

</html>



