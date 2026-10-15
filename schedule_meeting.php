
<?php

include "../database/connection.php";

date_default_timezone_set("Asia/Kolkata");

$message = "";


/* =====================================================
   FORM SUBMIT
===================================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");

    $date = $_POST["date"] ?? "";

    $start_time = $_POST["start_time"] ?? "";

    $end_time = $_POST["end_time"] ?? "";

    $description = trim($_POST["description"] ?? "");


    /* =================================================
       REQUIRED FIELD CHECK
    ================================================= */

    if (
        $title == "" ||
        $date == "" ||
        $start_time == "" ||
        $end_time == ""
    ) {

        $message =
            "Please fill all required fields.";

    }


    /* =================================================
       CHECK PAST DATE
    ================================================= */

    elseif ($date < date("Y-m-d")) {

        $message =
            "Past date cannot be scheduled.";

    }


    /* =================================================
       CHECK TODAY'S PAST TIME
    ================================================= */

    elseif (
        $date == date("Y-m-d") &&
        $start_time <= date("H:i")
    ) {
        
        $message =
            "This meeting time has already passed. Please select a future time.";

    }


    /* =================================================
       CHECK START AND END TIME
    ================================================= */

    elseif ($end_time <= $start_time) {

        $message =
            "End time must be later than start time.";

    }


    /* =================================================
       SAVE MEETING
    ================================================= */

    else {

        $stmt = mysqli_prepare(
            $conn,

            "INSERT INTO meetings
            (
                title,
                meeting_date,
                start_time,
                end_time,
                description
            )
            VALUES
            (?, ?, ?, ?, ?)"
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $title,
                $date,
                $start_time,
                $end_time,
                $description
            );


            /* =========================================
               EXECUTE INSERT
            ========================================= */

            if (mysqli_stmt_execute($stmt)) {

                $meeting_id =
                    mysqli_insert_id($conn);

                mysqli_stmt_close($stmt);

                /* =====================================
                   AUTOMATED REMINDERS SCHEDULER
                   Schedule Windows Tasks for 1 Hour &
                   10 Minutes before meeting start time.
                ===================================== */
                require_once dirname(__DIR__) . "/create_meeting_tasks.php";
                scheduleMeetingReminderTasks($meeting_id, $conn);


                /* =====================================
                   SUCCESS MESSAGE
                   
                   OK CLICK செய்த பிறகு தான்
                   Employee Availability page வரும்.
                ===================================== */

                echo "<script>

                    alert('Meeting Scheduled Successfully and Automated Reminders Configured!');

                    window.location.href =
                        'employee_availability.php?meeting_id=$meeting_id';

                </script>";

                exit;

            } else {

                $message =
                    "Meeting save failed: " .
                    mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);

            }

        } else {

            $message =
                "Database error: " .
                mysqli_error($conn);

        }

    }

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Schedule Meeting</title>


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    padding: 0;

    font-family: Arial, sans-serif;

    background: #f4f6f9;

}


.container {

    width: 600px;

    max-width: 92%;

    margin: 50px auto;

    background: white;

    padding: 30px;

    border-radius: 12px;

    box-shadow:
        0 4px 15px
        rgba(0, 0, 0, 0.12);

}


h2 {

    text-align: center;

    margin-bottom: 25px;

    color: #333;

}


.form-group {

    margin-bottom: 18px;

}


label {

    display: block;

    margin-bottom: 7px;

    font-weight: bold;

    color: #444;

}


input,
textarea {

    width: 100%;

    padding: 11px;

    border: 1px solid #ccc;

    border-radius: 6px;

    font-size: 15px;

}


textarea {

    min-height: 110px;

    resize: vertical;

}


input:focus,
textarea:focus {

    outline: none;

    border-color: #007bff;

}


button {

    width: 100%;

    padding: 12px;

    border: none;

    border-radius: 6px;

    background: #007bff;

    color: white;

    font-size: 16px;

    font-weight: bold;

    cursor: pointer;

}


button:hover {

    background: #0056b3;

}


.error {

    background: #ffe5e5;

    color: #c00;

    padding: 10px;

    margin-bottom: 18px;

    border-radius: 6px;

    text-align: center;

}

</style>

</head>


<body>


<div class="container">


<h2>Schedule Meeting</h2>


<?php if ($message != "") { ?>

    <div class="error">

        <?php echo htmlspecialchars($message); ?>

    </div>

<?php } ?>


<form
    method="POST"
    action=""
>


<!-- Meeting Title -->

<div class="form-group">

    <label for="title">
        Meeting Title
    </label>

    <input
        type="text"
        id="title"
        name="title"
        placeholder="Enter meeting title"
        value="<?php echo htmlspecialchars($title ?? ''); ?>"
        required
    >

</div>


<!-- Meeting Date -->

<div class="form-group">

    <label for="date">
        Meeting Date
    </label>

    <input
        type="date"
        id="date"
        name="date"
        min="<?php echo date('Y-m-d'); ?>"
        value="<?php echo htmlspecialchars($date ?? ''); ?>"
        required
    >

</div>


<!-- Start Time -->

<div class="form-group">

    <label for="start_time">
        Start Time
    </label>

    <input
        type="time"
        id="start_time"
        name="start_time"
        value="<?php echo htmlspecialchars($start_time ?? ''); ?>"
        required

    >

</div>


<!-- End Time -->

<div class="form-group">

    <label for="end_time">
        End Time
    </label>

    <input
        type="time"
        id="end_time"
        name="end_time"
        value="<?php echo htmlspecialchars($end_time ?? ''); ?>"
        required
    >

</div>


<!-- Description -->

<div class="form-group">

    <label for="description">
        Description
    </label>

    <textarea
        id="description"
        name="description"
        placeholder="Enter meeting description"
    ><?php echo htmlspecialchars($description ?? ''); ?></textarea>

</div>


<!-- Submit -->

<button type="submit">

    Schedule Meeting

</button>


</form>


</div>


</body>

</html>

