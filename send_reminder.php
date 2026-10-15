<?php

require_once __DIR__ . "/database/connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

date_default_timezone_set("Asia/Kolkata");

/*
|--------------------------------------------------------------------------
| Logger Helper
|--------------------------------------------------------------------------
*/
function logReminderMessage($message) {
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logFile = $logDir . '/reminder.log';
    $entry = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
    @file_put_contents($logFile, $entry, FILE_APPEND);
}

// Get values from CLI ($argv) or URL query parameters ($_GET)
$meeting_id   = $argv[1] ?? ($_GET['meeting_id'] ?? '');
$reminderType = $argv[2] ?? ($_GET['type'] ?? '');

if (empty($meeting_id) || empty($reminderType)) {
    logReminderMessage("Execution failed: Missing meeting ID or reminder type.");
    exit("Invalid reminder details. Usage: send_reminder.php <meeting_id> <1Hour|10Minutes>\n");
}

$meeting_id = intval($meeting_id);

// Normalize reminder type
$typeClean = strtolower(str_replace([' ', '_', '-'], '', $reminderType));

if ($typeClean === '1hour' || $typeClean === '1h') {
    $reminderLabel = "1 Hour";
    $countdownText = "starts in 1 hour";
    $badgeColor = "#d97706"; // Amber
    $badgeBg = "#fef3c7";
} elseif ($typeClean === '10minutes' || $typeClean === '10mins' || $typeClean === '10min' || $typeClean === '10m') {
    $reminderLabel = "10 Minutes";
    $countdownText = "starts in 10 minutes";
    $badgeColor = "#dc2626"; // Red
    $badgeBg = "#fee2e2";
} else {
    logReminderMessage("Invalid reminder type: $reminderType for meeting ID: $meeting_id");
    exit("Invalid reminder type: $reminderType\n");
}

/*
|--------------------------------------------------------------------------
| Get Meeting Details
|--------------------------------------------------------------------------
*/
$meeting_query = "
    SELECT id, title, meeting_date, start_time, end_time, description
    FROM meetings
    WHERE id = '$meeting_id'
    LIMIT 1
";

$meeting_result = mysqli_query($conn, $meeting_query);

if (!$meeting_result || mysqli_num_rows($meeting_result) == 0) {
    logReminderMessage("Meeting not found: ID $meeting_id");
    exit("Meeting not found.\n");
}

$meeting = mysqli_fetch_assoc($meeting_result);
$meeting_title = $meeting['title'];
$meeting_date  = $meeting['meeting_date'];
$start_time    = $meeting['start_time'];
$end_time      = $meeting['end_time'];
$description   = $meeting['description'] ?? 'No description provided.';

/*
|--------------------------------------------------------------------------
| Get Participants Invited to this Meeting
|--------------------------------------------------------------------------
*/
$part_query = "
    SELECT e.full_name, e.email
    FROM meeting_participants mp
    INNER JOIN employee e
        ON mp.employee_id = e.id
    WHERE mp.meeting_id = '$meeting_id'
";

$part_result = mysqli_query($conn, $part_query);

if (!$part_result) {
    logReminderMessage("Participant query failed for meeting ID: $meeting_id: " . mysqli_error($conn));
    exit("Database query failed.\n");
}

if (mysqli_num_rows($part_result) == 0) {
    logReminderMessage("No participants found for meeting ID: $meeting_id ($meeting_title)");
    exit("No participants assigned to this meeting.\n");
}

$subject = "🔔 [REMINDER: " . $reminderLabel . "] Upcoming Meeting: " . $meeting_title;
$sentCount = 0;
$failCount = 0;

/*
|--------------------------------------------------------------------------
| Send Reminder Email to Each Participant
|--------------------------------------------------------------------------
*/
while ($employee = mysqli_fetch_assoc($part_result)) {
    $employee_name  = $employee['full_name'];
    $employee_email = $employee['email'];

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->SMTPDebug  = 0;

        // Credentials
        $mail->Username   = 'veeramuthu9191@gmail.com';
        $mail->Password   = 'ppbalqkxvzqjffpk';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender
        $mail->setFrom('veeramuthu9191@gmail.com', 'Smart Meeting System');

        // Recipient
        $mail->addAddress($employee_email, $employee_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;

        $mail->Body = "
        <div style='font-family: Segoe UI, Roboto, Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06);'>
            <div style='background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 25px; color: #ffffff; text-align: center;'>
                <h1 style='margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;'>Smart Meeting System</h1>
                <p style='margin: 8px 0 0; font-size: 15px; opacity: 0.9;'>Automated Meeting Reminder</p>
            </div>

            <div style='padding: 30px;'>
                <div style='text-align: center; margin-bottom: 25px;'>
                    <span style='display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; color: {$badgeColor}; background: {$badgeBg}; border: 1px solid {$badgeColor};'>
                        ⏰ Meeting {$countdownText}
                    </span>
                </div>

                <p style='font-size: 16px; color: #334155; margin-top: 0;'>
                    Hello <strong>" . htmlspecialchars($employee_name) . "</strong>,
                </p>

                <p style='font-size: 15px; color: #475569; line-height: 1.6;'>
                    This is a reminder that your scheduled meeting <strong>\"" . htmlspecialchars($meeting_title) . "\"</strong> is starting soon.
                </p>

                <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin: 25px 0;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px 0; color: #64748b; font-size: 14px; width: 35%;'><strong>Meeting Title:</strong></td>
                            <td style='padding: 8px 0; color: #1e293b; font-size: 15px; font-weight: 600;'>" . htmlspecialchars($meeting_title) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #64748b; font-size: 14px;'><strong>Date:</strong></td>
                            <td style='padding: 8px 0; color: #1e293b; font-size: 14px;'>" . htmlspecialchars($meeting_date) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #64748b; font-size: 14px;'><strong>Time:</strong></td>
                            <td style='padding: 8px 0; color: #1e293b; font-size: 14px;'>" . htmlspecialchars($start_time) . " - " . htmlspecialchars($end_time) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #64748b; font-size: 14px; vertical-align: top;'><strong>Description:</strong></td>
                            <td style='padding: 8px 0; color: #475569; font-size: 14px; line-height: 1.5;'>" . nl2br(htmlspecialchars($description)) . "</td>
                        </tr>
                    </table>
                </div>

                <p style='font-size: 14px; color: #64748b; text-align: center; margin: 25px 0 10px;'>
                    Please be prepared and join on time.
                </p>
            </div>

            <div style='background: #f1f5f9; padding: 15px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;'>
                &copy; " . date('Y') . " Smart Meeting System. All rights reserved.
            </div>
        </div>
        ";

        $mail->send();
        $sentCount++;
        logReminderMessage("SUCCESS: Sent $reminderLabel reminder for meeting ID $meeting_id to $employee_email ($employee_name)");

    } catch (Exception $e) {
        $failCount++;
        logReminderMessage("FAILED: Mail error for meeting ID $meeting_id to $employee_email: " . $mail->ErrorInfo);
    }
}

$responseMessage = "Reminder ($reminderLabel) for Meeting '$meeting_title' (ID: $meeting_id): Sent to $sentCount employees, Failed: $failCount.\n";
logReminderMessage($responseMessage);
echo $responseMessage;

?>
