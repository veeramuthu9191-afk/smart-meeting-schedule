<?php

include "../database/connection.php";

date_default_timezone_set('Asia/Kolkata');

$message = "";


/* =====================================================
   SELECTED MEETING ID
===================================================== */

$selected_meeting_id = 0;

if (isset($_GET["meeting_id"])) {

    $selected_meeting_id =
        intval($_GET["meeting_id"]);

}


/* =====================================================
   TASK ASSIGN
===================================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $meeting_id =
        intval($_POST["meeting_id"]);

    $employee_id =
        intval($_POST["employee_id"]);

    $task_title =
        trim($_POST["task_title"]);

    $assigned_date =
        $_POST["assigned_date"];

    $due_date =
        $_POST["due_date"];


    /*
    After POST also keep selected meeting
    */

    $selected_meeting_id =
        $meeting_id;


    /* =================================================
       CHECK EMPTY TASK
    ================================================= */

    if ($task_title == "") {

        $message =
            "Please enter task.";

    } else {


        /* =============================================
           CHECK DUPLICATE TASK
           SAME MEETING + SAME EMPLOYEE + SAME TASK
        ============================================= */

        $check_sql = "

            SELECT id

            FROM tasks

            WHERE meeting_id = ?

            AND employee_id = ?

            AND task_title = ?

            LIMIT 1

        ";


        $check_stmt =
            mysqli_prepare(
                $conn,
                $check_sql
            );


        mysqli_stmt_bind_param(
            $check_stmt,
            "iis",
            $meeting_id,
            $employee_id,
            $task_title
        );


        mysqli_stmt_execute(
            $check_stmt
        );


        $check_result =
            mysqli_stmt_get_result(
                $check_stmt
            );


        $duplicate =
            mysqli_num_rows(
                $check_result
            );


        mysqli_stmt_close(
            $check_stmt
        );


        /* =============================================
           DUPLICATE FOUND
        ============================================= */

        if ($duplicate > 0) {

            $message =
                "This task is already assigned to this employee for this meeting.";

        } else {


            /* =========================================
               GET MEETING TITLE

               MEETING TITLE = PROJECT NAME
            ========================================= */

            $project_name = "";


            $project_sql = "

                SELECT title

                FROM meetings

                WHERE id = ?

                LIMIT 1

            ";


            $project_stmt =
                mysqli_prepare(
                    $conn,
                    $project_sql
                );


            mysqli_stmt_bind_param(
                $project_stmt,
                "i",
                $meeting_id
            );


            mysqli_stmt_execute(
                $project_stmt
            );


            $project_result =
                mysqli_stmt_get_result(
                    $project_stmt
                );


            $project_row =
                mysqli_fetch_assoc(
                    $project_result
                );


            if ($project_row) {

                /*
                Meeting title is used
                as Project Name
                */

                $project_name =
                    $project_row["title"];

            }


            mysqli_stmt_close(
                $project_stmt
            );


            /* =========================================
               CHECK PROJECT NAME
            ========================================= */

            if ($project_name == "") {

                $message =
                    "Meeting title not found.";

            } else {


                /* =====================================
                   INSERT TASK

                   STATUS = Pending
                   PROGRESS = 0

                   Automatically
                ===================================== */

                $sql = "

                    INSERT INTO tasks
                    (
                        meeting_id,
                        employee_id,
                        project_name,
                        task_title,
                        status,
                        progress,
                        assigned_date,
                        due_date
                    )

                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        'Pending',
                        0,
                        ?,
                        ?
                    )

                ";


                $stmt =
                    mysqli_prepare(
                        $conn,
                        $sql
                    );


                if ($stmt) {


                    mysqli_stmt_bind_param(
                        $stmt,
                        "iissss",
                        $meeting_id,
                        $employee_id,
                        $project_name,
                        $task_title,
                        $assigned_date,
                        $due_date
                    );


                    if (
                        mysqli_stmt_execute(
                            $stmt
                        )
                    ) {

                        $message =
                            "Task assigned successfully!";

                    } else {

                        $message =
                            "Error assigning task: " .
                            mysqli_error($conn);

                    }


                    mysqli_stmt_close(
                        $stmt
                    );


                } else {

                    $message =
                        "Database error: " .
                        mysqli_error($conn);

                }

            }

        }

    }

}


/* =====================================================
   SELECTED MEETING
===================================================== */

$selected_meeting = null;


if ($selected_meeting_id > 0) {


    $sql = "

        SELECT
            id,
            title,
            meeting_date,
            start_time,
            end_time

        FROM meetings

        WHERE id = ?

    ";


    $stmt =
        mysqli_prepare(
            $conn,
            $sql
        );


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $selected_meeting_id
    );


    mysqli_stmt_execute(
        $stmt
    );


    $result =
        mysqli_stmt_get_result(
            $stmt
        );


    $selected_meeting =
        mysqli_fetch_assoc(
            $result
        );


    mysqli_stmt_close(
        $stmt
    );

}


/* =====================================================
   TODAY'S MEETINGS
===================================================== */

$today =
    date("Y-m-d");


$meetings = [];


$sql = "

    SELECT
        id,
        title,
        meeting_date,
        start_time,
        end_time

    FROM meetings

    WHERE meeting_date = ?

    ORDER BY start_time ASC

";


$stmt =
    mysqli_prepare(
        $conn,
        $sql
    );


mysqli_stmt_bind_param(
    $stmt,
    "s",
    $today
);


mysqli_stmt_execute(
    $stmt
);


$result =
    mysqli_stmt_get_result(
        $stmt
    );


while (
    $row =
    mysqli_fetch_assoc(
        $result
    )
) {

    $meetings[] =
        $row;

}


mysqli_stmt_close(
    $stmt
);


/* =====================================================
   GET EMPLOYEES FOR SELECTED MEETING
===================================================== */

$employees = [];


if ($selected_meeting) {


    $sql = "

        SELECT DISTINCT

            e.id,
            e.full_name,
            e.email

        FROM meeting_participants mp

        INNER JOIN employee e

            ON mp.employee_id = e.id

        WHERE mp.meeting_id = ?

        ORDER BY e.full_name ASC

    ";


    $stmt =
        mysqli_prepare(
            $conn,
            $sql
        );


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $selected_meeting_id
    );


    mysqli_stmt_execute(
        $stmt
    );


    $result =
        mysqli_stmt_get_result(
            $stmt
        );


    while (
        $row =
        mysqli_fetch_assoc(
            $result
        )
    ) {

        $employees[] =
            $row;

    }


    mysqli_stmt_close(
        $stmt
    );

}


/* =====================================================
   CHECK ALREADY ASSIGNED EMPLOYEES
===================================================== */

foreach (
    $employees as &$employee
) {


    $employee["has_task"] =
        false;


    $task_check_sql = "

        SELECT id

        FROM tasks

        WHERE meeting_id = ?

        AND employee_id = ?

        LIMIT 1

    ";


    $task_check_stmt =
        mysqli_prepare(
            $conn,
            $task_check_sql
        );


    mysqli_stmt_bind_param(
        $task_check_stmt,
        "ii",
        $selected_meeting_id,
        $employee["id"]
    );


    mysqli_stmt_execute(
        $task_check_stmt
    );


    $task_check_result =
        mysqli_stmt_get_result(
            $task_check_stmt
        );


    if (
        mysqli_num_rows(
            $task_check_result
        ) > 0
    ) {

        $employee["has_task"] =
            true;

    }


    mysqli_stmt_close(
        $task_check_stmt
    );

}


unset($employee);

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Create Task</title>


<style>

* {

    box-sizing: border-box;

    margin: 0;

    padding: 0;

}


body {

    font-family: Arial, sans-serif;

    background: #f4f6f9;

}


.container {

    width: 90%;

    max-width: 900px;

    margin: 50px auto;

}


h1 {

    text-align: center;

    margin-bottom: 10px;

    color: #333;

}


.date-title {

    text-align: center;

    margin-bottom: 30px;

    color: #666;

    font-size: 18px;

}


.box {

    background: white;

    padding: 30px;

    border-radius: 12px;

    margin-bottom: 25px;

    box-shadow:
        0 4px 15px
        rgba(0,0,0,0.10);

}


/* =========================
   MESSAGE
========================= */

.message {

    background: white;

    padding: 15px;

    border-radius: 8px;

    text-align: center;

    color: green;

    font-weight: bold;

    margin-bottom: 20px;

}


/* =========================
   MEETING
========================= */

.meeting-title {

    font-size: 25px;

    font-weight: bold;

    margin-bottom: 18px;

}


.meeting-info {

    line-height: 1.9;

    color: #444;

}


.open-button {

    display: inline-block;

    margin-top: 20px;

    padding: 12px 25px;

    background: #333;

    color: white;

    text-decoration: none;

    border-radius: 7px;

}


.open-button:hover {

    opacity: 0.85;

}


/* =========================
   EMPLOYEE
========================= */

.employee-box {

    margin-top: 20px;

    border-top: 4px solid #333;

}


.employee-name {

    font-size: 22px;

    font-weight: bold;

    margin-bottom: 15px;

}


.employee-info {

    line-height: 1.8;

    margin-bottom: 25px;

}


/* =========================
   ALREADY ASSIGNED
========================= */

.already-assigned {

    background: #f1f1f1;

    padding: 15px;

    border-radius: 7px;

    text-align: center;

    font-weight: bold;

    color: #666;

}


/* =========================
   FORM
========================= */

.form-group {

    margin-bottom: 18px;

}


label {

    display: block;

    font-weight: bold;

    margin-bottom: 7px;

}


input {

    width: 100%;

    padding: 12px;

    border: 1px solid #ccc;

    border-radius: 7px;

    font-size: 15px;

}


.assign-button {

    display: block;

    width: 180px;

    margin: 25px auto 0;

    padding: 12px;

    border: none;

    border-radius: 7px;

    background: #333;

    color: white;

    font-size: 16px;

    cursor: pointer;

}


.assign-button:hover {

    opacity: 0.85;

}


/* =========================
   NO DATA
========================= */

.no-data {

    text-align: center;

    padding: 30px;

    color: #666;

}

</style>

</head>


<body>


<div class="container">


<h1>

Create Task

</h1>


<div class="date-title">

Today's Meetings -

<?php

echo date("d-m-Y");

?>

</div>


<?php if ($message != "") { ?>


<div class="message">

<?php

echo htmlspecialchars(
    $message
);

?>

</div>


<?php } ?>


<?php if (!$selected_meeting) { ?>


<!-- =================================================
     TODAY'S MEETINGS
================================================= -->


<?php if (count($meetings) > 0) { ?>


<?php foreach (
    $meetings as $meeting
) { ?>


<div class="box">


<div class="meeting-title">

<?php

echo htmlspecialchars(
    $meeting["title"]
);

?>

</div>


<div class="meeting-info">


<p>

<strong>
Date:
</strong>

<?php

echo htmlspecialchars(
    $meeting["meeting_date"]
);

?>

</p>


<p>

<strong>
Starting Time:
</strong>

<?php

echo htmlspecialchars(
    $meeting["start_time"]
);

?>

</p>


<p>

<strong>
Ending Time:
</strong>

<?php

echo htmlspecialchars(
    $meeting["end_time"]
);

?>

</p>


</div>


<a
    href="create_task.php?meeting_id=<?php
        echo $meeting["id"];
    ?>"
    class="open-button"
>

Open Meeting

</a>


</div>


<?php } ?>


<?php } else { ?>


<div class="box no-data">

No meetings found for today.

</div>


<?php } ?>


<?php } else { ?>


<!-- =================================================
     SELECTED MEETING
================================================= -->


<div class="box">


<div class="meeting-title">

<?php

echo htmlspecialchars(
    $selected_meeting["title"]
);

?>

</div>


<div class="meeting-info">


<p>

<strong>
Project Name:
</strong>

<?php

/*
Meeting title is Project Name
*/

echo htmlspecialchars(
    $selected_meeting["title"]
);

?>

</p>


<p>

<strong>
Date:
</strong>

<?php

echo htmlspecialchars(
    $selected_meeting["meeting_date"]
);

?>

</p>


<p>

<strong>
Starting Time:
</strong>

<?php

echo htmlspecialchars(
    $selected_meeting["start_time"]
);

?>

</p>


<p>

<strong>
Ending Time:
</strong>

<?php

echo htmlspecialchars(
    $selected_meeting["end_time"]
);

?>

</p>


</div>


</div>


<!-- =================================================
     EMPLOYEES
================================================= -->


<?php if (
    count($employees) > 0
) { ?>


<?php foreach (
    $employees as $employee
) { ?>


<div class="box employee-box">


<div class="employee-name">

Employee:

<?php

echo htmlspecialchars(
    $employee["full_name"]
);

?>

</div>


<div class="employee-info">


<p>

<strong>
Project Name:
</strong>

<?php

echo htmlspecialchars(
    $selected_meeting["title"]
);

?>

</p>


<p>

<strong>
Email:
</strong>

<?php

echo htmlspecialchars(
    $employee["email"]
);

?>

</p>


</div>


<?php if (
    $employee["has_task"]
) { ?>


<!-- =================================================
     ALREADY ASSIGNED
================================================= -->


<div class="already-assigned">

Task already assigned for this employee in this meeting.

</div>


<?php } else { ?>


<!-- =================================================
     TASK FORM
================================================= -->


<form method="POST">


<input
    type="hidden"
    name="meeting_id"
    value="<?php

        echo $selected_meeting_id;

    ?>"
>


<input
    type="hidden"
    name="employee_id"
    value="<?php

        echo $employee["id"];

    ?>"
>


<div class="form-group">

<label>

Task

</label>


<input
    type="text"
    name="task_title"
    placeholder="Enter task"
    required
>

</div>


<div class="form-group">

<label>

Assigned Date

</label>


<input
    type="date"
    name="assigned_date"
    value="<?php

        echo date("Y-m-d");

    ?>"
    required
>

</div>


<div class="form-group">

<label>

Due Date

</label>


<input
    type="date"
    name="due_date"
    required
>

</div>


<button
    type="submit"
    name="assign_task"
    class="assign-button"
>

Assign Task

</button>


</form>


<?php } ?>


</div>


<?php } ?>


<?php } else { ?>


<div class="box no-data">

No employees selected for this meeting.

</div>


<?php } ?>


<?php } ?>


</div>


</body>

</html>
