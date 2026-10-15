<?php

date_default_timezone_set('Asia/Kolkata');


/*
|--------------------------------------------------------------------------
| Create Reminder Task
|--------------------------------------------------------------------------
*/

function createReminderTask(
    $taskName,
    $runDate,
    $runTime,
    $meetingId,
    $reminderType
) {

    $phpPath =
        "C:\\xampp\\php\\php.exe";

    $reminderPath =
        "C:\\xampp\\htdocs\\smart_meeting_schedule\\send_reminder.php";


    /*
    |--------------------------------------------------------------------------
    | Reminder Command
    |--------------------------------------------------------------------------
    */

    $command =
        '\"' . $phpPath . '\" \"' .
        $reminderPath . '\" ' .
        $meetingId . ' \"' .
        $reminderType . '\"';


    /*
    |--------------------------------------------------------------------------
    | Convert Date
    |--------------------------------------------------------------------------
    */

    $dateParts = explode('-', $runDate);

    $windowsDate =
        $dateParts[2] . '-' .
        $dateParts[1] . '-' .
        $dateParts[0];


    /*
    |--------------------------------------------------------------------------
    | Create Windows Task
    |--------------------------------------------------------------------------
    */

    $schtasksCommand =
        'schtasks /Create ' .
        '/TN "' . $taskName . '" ' .
        '/TR "' . $command . '" ' .
        '/SC ONCE ' .
        '/SD ' . $windowsDate . ' ' .
        '/ST ' . $runTime . ':00 ' .
        '/F';


    /*
    |--------------------------------------------------------------------------
    | Execute
    |--------------------------------------------------------------------------
    */

    exec(
        $schtasksCommand,
        $output,
        $returnCode
    );


    /*
    |--------------------------------------------------------------------------
    | Show Result
    |--------------------------------------------------------------------------
    */

    echo "<pre>";

    echo "Task Name:\n";
    echo $taskName . "\n\n";

    echo "Command:\n";
    echo $schtasksCommand . "\n\n";

    echo "Return Code:\n";
    echo $returnCode . "\n\n";

    echo "Task Scheduler Output:\n";

    print_r($output);

    echo "</pre>";
}


/*
|--------------------------------------------------------------------------
| TEST TASK
|--------------------------------------------------------------------------
*/

createReminderTask(
    "SmartMeeting_Function_Test_2",
    "2026-08-24",
    "09:30",
    55,
    "10 Minutes"
);

?>