<?php

require_once __DIR__ . "/database/connection.php";

date_default_timezone_set("Asia/Kolkata");

/*
|--------------------------------------------------------------------------
| Get PHP Executable Path
|--------------------------------------------------------------------------
*/
function getPhpBinaryPath() {
    $candidates = [
        "C:\\xampp\\php\\php.exe",
        "C:\\Users\\ELCOT\\Desktop\\xampp\\php\\php.exe"
    ];

    foreach ($candidates as $candidate) {
        if (file_exists($candidate)) {
            return $candidate;
        }
    }

    return "C:\\xampp\\php\\php.exe";
}

/*
|--------------------------------------------------------------------------
| Get send_reminder.php Path
|--------------------------------------------------------------------------
*/
function getSendReminderPath() {
    $localFile = __DIR__ . DIRECTORY_SEPARATOR . "send_reminder.php";
    if (file_exists($localFile)) {
        return realpath($localFile);
    }

    $candidates = [
        "C:\\xampp\\htdocs\\smart_meeting_schedule\\send_reminder.php",
        "C:\\Users\\ELCOT\\Desktop\\xampp\\htdocs\\smart_meeting_schedule\\send_reminder.php"
    ];

    foreach ($candidates as $candidate) {
        if (file_exists($candidate)) {
            return realpath($candidate);
        }
    }

    return $localFile;
}

/*
|--------------------------------------------------------------------------
| Create Windows Task Scheduler Task via CLI
|--------------------------------------------------------------------------
*/
function createReminderTask(
    $taskName,
    $runDate,
    $runTime,
    $meetingId,
    $reminderType
) {
    $phpPath = getPhpBinaryPath();
    $reminderPath = getSendReminderPath();

    /*
    | Convert date YYYY-MM-DD -> DD-MM-YYYY for Windows locale (en-IN)
    */
    $dateParts = explode("-", $runDate);
    if (count($dateParts) === 3) {
        $windowsDate = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
    } else {
        $windowsDate = $runDate;
    }

    /*
    | Command that Task Scheduler executes:
    | "php.exe" "send_reminder.php" <meeting_id> "<reminder_type>"
    */
    $command = '\"' . $phpPath . '\" \"' . $reminderPath . '\" ' . intval($meetingId) . ' \"' . $reminderType . '\"';

    /*
    | Build schtasks command
    */
    $schtasksCommand =
        'schtasks /Create ' .
        '/TN "' . $taskName . '" ' .
        '/TR "' . $command . '" ' .
        '/SC ONCE ' .
        '/SD ' . $windowsDate . ' ' .
        '/ST ' . $runTime . ' ' .
        '/F';

    $output = [];
    $returnCode = 0;

    exec($schtasksCommand, $output, $returnCode);

    return [
        "success" => ($returnCode === 0),
        "command" => $schtasksCommand,
        "output" => $output,
        "code" => $returnCode
    ];
}

/*
|--------------------------------------------------------------------------
| Schedule Reminders for a Single Meeting (1 Hour & 10 Minutes)
|--------------------------------------------------------------------------
*/
function scheduleMeetingReminderTasks($meetingId, $conn) {
    $meetingId = intval($meetingId);
    $query = "SELECT id, title, meeting_date, start_time FROM meetings WHERE id = '$meetingId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        return [
            "success" => false,
            "error" => "Meeting not found (ID: $meetingId)"
        ];
    }

    $meeting = mysqli_fetch_assoc($result);
    $meetingTimestamp = strtotime($meeting['meeting_date'] . ' ' . $meeting['start_time']);
    $now = time();

    $tasksScheduled = [];

    // 1 Hour Reminder (3600 seconds before start)
    $oneHourTimestamp = $meetingTimestamp - 3600;
    if ($oneHourTimestamp > $now) {
        $taskName = "SmartMeeting_" . $meetingId . "_1Hour";
        $runDate = date("Y-m-d", $oneHourTimestamp);
        $runTime = date("H:i", $oneHourTimestamp);

        $res = createReminderTask(
            $taskName,
            $runDate,
            $runTime,
            $meetingId,
            "1Hour"
        );

        $tasksScheduled["1Hour"] = [
            "task_name" => $taskName,
            "date" => $runDate,
            "time" => $runTime,
            "result" => $res
        ];
    } else {
        $tasksScheduled["1Hour"] = [
            "skipped" => true,
            "reason" => "1-hour reminder time is already in the past"
        ];
    }

    // 10 Minutes Reminder (600 seconds before start)
    $tenMinuteTimestamp = $meetingTimestamp - 600;
    if ($tenMinuteTimestamp > $now) {
        $taskName = "SmartMeeting_" . $meetingId . "_10Minutes";
        $runDate = date("Y-m-d", $tenMinuteTimestamp);
        $runTime = date("H:i", $tenMinuteTimestamp);

        $res = createReminderTask(
            $taskName,
            $runDate,
            $runTime,
            $meetingId,
            "10Minutes"
        );

        $tasksScheduled["10Minutes"] = [
            "task_name" => $taskName,
            "date" => $runDate,
            "time" => $runTime,
            "result" => $res
        ];
    } else {
        $tasksScheduled["10Minutes"] = [
            "skipped" => true,
            "reason" => "10-minute reminder time is already in the past"
        ];
    }

    return [
        "success" => true,
        "meeting_id" => $meetingId,
        "title" => $meeting['title'],
        "tasks" => $tasksScheduled
    ];
}

/*
|--------------------------------------------------------------------------
| Schedule Reminders for All Upcoming Meetings
|--------------------------------------------------------------------------
*/
function scheduleAllUpcomingMeetings($conn) {
    $sql = "
        SELECT *
        FROM meetings
        WHERE TIMESTAMP(meeting_date, start_time) > NOW()
        ORDER BY meeting_date ASC, start_time ASC
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return [
            "success" => false,
            "error" => mysqli_error($conn)
        ];
    }

    if (mysqli_num_rows($result) === 0) {
        return [
            "success" => true,
            "message" => "No upcoming meetings found.",
            "data" => []
        ];
    }

    $allResults = [];
    while ($meeting = mysqli_fetch_assoc($result)) {
        $allResults[] = scheduleMeetingReminderTasks($meeting["id"], $conn);
    }

    return [
        "success" => true,
        "data" => $allResults
    ];
}

/*
|--------------------------------------------------------------------------
| Direct Execution Handler (CLI or Browser)
|--------------------------------------------------------------------------
*/
$isDirectExecution = false;

if (php_sapi_name() === 'cli') {
    if (isset($argv[0]) && realpath($argv[0]) === realpath(__FILE__)) {
        $isDirectExecution = true;
    }
} else {
    if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
        $isDirectExecution = true;
    }
}

if ($isDirectExecution) {
    $results = scheduleAllUpcomingMeetings($conn);

    if (php_sapi_name() === 'cli') {
        echo "=== Smart Meeting Reminders Scheduler ===\n";
        print_r($results);
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Meeting Reminders Task Scheduler</title>
            <style>
                body { font-family: Segoe UI, sans-serif; background: #f4f6f9; padding: 30px; margin: 0; }
                .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
                h2 { color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
                .card { background: #f8fafc; border-left: 4px solid #4f46e5; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
                .success { color: #16a34a; font-weight: bold; }
                .skipped { color: #9333ea; font-weight: bold; }
                .error { color: #dc2626; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>🔔 Meeting Automated Reminder Tasks (Windows Scheduler)</h2>
                <?php if (empty($results['data'])): ?>
                    <p>No upcoming meetings found to schedule.</p>
                <?php else: ?>
                    <?php foreach ($results['data'] as $m): ?>
                        <div class="card">
                            <h3>Meeting #<?= htmlspecialchars($m['meeting_id']) ?>: <?= htmlspecialchars($m['title']) ?></h3>
                            <?php foreach ($m['tasks'] as $type => $t): ?>
                                <p>
                                    <strong><?= htmlspecialchars($type) ?> Reminder:</strong>
                                    <?php if (!empty($t['skipped'])): ?>
                                        <span class="skipped">Skipped (<?= htmlspecialchars($t['reason']) ?>)</span>
                                    <?php elseif (!empty($t['result']['success'])): ?>
                                        <span class="success">SUCCESS</span> - Task: <code><?= htmlspecialchars($t['task_name']) ?></code> at <?= htmlspecialchars($t['date']) ?> <?= htmlspecialchars($t['time']) ?>
                                    <?php else: ?>
                                        <span class="error">FAILED</span> (Code: <?= htmlspecialchars($t['result']['code'] ?? '') ?>)
                                    <?php endif; ?>
                                </p>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
    }
}
