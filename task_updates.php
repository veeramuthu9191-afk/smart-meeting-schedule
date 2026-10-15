<?php

include "../database/connection.php";


/* =====================================================
   IF EMPLOYEE IS SELECTED
===================================================== */

$selected_employee_id = 0;

if (isset($_GET['employee_id'])) {

    $selected_employee_id =
        intval($_GET['employee_id']);

}


/* =====================================================
   GET SELECTED EMPLOYEE
===================================================== */

$selected_employee = null;

if ($selected_employee_id > 0) {

    $employee_sql = "

        SELECT
            id,
            full_name

        FROM employee

        WHERE id = ?

        LIMIT 1

    ";

    $employee_stmt =
        $conn->prepare($employee_sql);

    $employee_stmt->bind_param(
        "i",
        $selected_employee_id
    );

    $employee_stmt->execute();

    $employee_result =
        $employee_stmt->get_result();

    if (
        $employee_result->num_rows > 0
    ) {

        $selected_employee =
            $employee_result->fetch_assoc();

    }

    $employee_stmt->close();

}


/* =====================================================
   GET EMPLOYEES WHO HAVE UPDATES
===================================================== */

$employees_sql = "

    SELECT DISTINCT

        e.id,
        e.full_name

    FROM task_updates tu

    INNER JOIN employee e
        ON tu.employee_id = e.id

    ORDER BY e.full_name ASC

";


$employees_result =
    mysqli_query(
        $conn,
        $employees_sql
    );


/* =====================================================
   GET SELECTED EMPLOYEE UPDATES
===================================================== */

$updates_result = null;

if ($selected_employee) {

    $updates_sql = "

        SELECT

            tu.id AS update_id,

            tu.status,
            tu.progress,
            tu.update_details,
            tu.updated_at,

            t.task_title,
            t.project_name,

            m.title AS meeting_title

        FROM task_updates tu

        INNER JOIN tasks t
            ON tu.task_id = t.id

        LEFT JOIN meetings m
            ON t.meeting_id = m.id

        WHERE tu.employee_id = ?

        ORDER BY tu.updated_at DESC

    ";

    $updates_stmt =
        $conn->prepare($updates_sql);

    $updates_stmt->bind_param(
        "i",
        $selected_employee_id
    );

    $updates_stmt->execute();

    $updates_result =
        $updates_stmt->get_result();

}

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Employee Task Updates</title>


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

    width: 90%;

    max-width: 900px;

    margin: 40px auto;

}


h1 {

    text-align: center;

    color: #333;

    margin-bottom: 30px;

}


/* =========================
   BACK BUTTON
========================= */

.back-button {

    display: inline-block;

    margin-bottom: 25px;

    padding: 10px 20px;

    background: #333;

    color: white;

    text-decoration: none;

    border-radius: 6px;

}


.back-button:hover {

    opacity: 0.85;

}


/* =========================
   EMPLOYEE CARD
========================= */

.employee-card {

    background: white;

    padding: 22px;

    margin-bottom: 15px;

    border-radius: 10px;

    box-shadow:
        0 3px 10px
        rgba(0,0,0,0.12);

    display: flex;

    justify-content: space-between;

    align-items: center;

}


.employee-name {

    font-size: 20px;

    font-weight: bold;

    color: #333;

}


.view-button {

    padding: 10px 18px;

    background: #007bff;

    color: white;

    text-decoration: none;

    border-radius: 6px;

}


.view-button:hover {

    background: #0056b3;

}


/* =========================
   SELECTED EMPLOYEE
========================= */

.employee-heading {

    background: white;

    padding: 20px;

    margin-bottom: 20px;

    border-radius: 10px;

    box-shadow:
        0 3px 10px
        rgba(0,0,0,0.12);

    font-size: 22px;

    font-weight: bold;

    color: #333;

}


/* =========================
   UPDATE BOX
========================= */

.update-box {

    background: white;

    padding: 25px;

    margin-bottom: 20px;

    border-radius: 10px;

    box-shadow:
        0 3px 10px
        rgba(0,0,0,0.12);

}


.update-id {

    color: #777;

    font-size: 13px;

    margin-bottom: 15px;

}


/* =========================
   PROJECT NAME
========================= */

.project-name {

    font-size: 21px;

    font-weight: bold;

    color: #0066ff;

    margin-bottom: 18px;

}


/* =========================
   INFORMATION
========================= */

.info {

    margin-bottom: 10px;

    color: #444;

    line-height: 1.5;

}


.info strong {

    color: #222;

}


.status {

    font-weight: bold;

}


.progress {

    font-weight: bold;

}


/* =========================
   UPDATE DETAILS
========================= */

.update-details {

    margin-top: 18px;

    padding: 15px;

    background: #f8f9fa;

    border-radius: 7px;

    line-height: 1.6;

}


/* =========================
   UPDATED TIME
========================= */

.updated-time {

    margin-top: 15px;

    color: #777;

    font-size: 14px;

}


/* =========================
   NO DATA
========================= */

.no-update {

    background: white;

    padding: 30px;

    text-align: center;

    border-radius: 10px;

    color: #777;

}


</style>

</head>


<body>


<div class="container">


<h1>

Employee Task Updates

</h1>


<?php

/* =====================================================
   NO EMPLOYEE SELECTED
===================================================== */

if (!$selected_employee) {

?>


<a
    href="task_tracking.php"
    class="back-button"
>

← Back to Task Tracking

</a>


<?php

if (
    $employees_result &&
    mysqli_num_rows($employees_result) > 0
) {


    while (
        $employee =
        mysqli_fetch_assoc($employees_result)
    ) {

?>


<div class="employee-card">


<div class="employee-name">

👤

<?php

echo htmlspecialchars(
    $employee['full_name']
);

?>

</div>


<a
    href="task_updates.php?employee_id=<?php
        echo intval($employee['id']);
    ?>"
    class="view-button"
>

View Updates →

</a>


</div>


<?php

    }


} else {

?>


<div class="no-update">

No employee task updates found.

</div>


<?php

}


}


/* =====================================================
   EMPLOYEE SELECTED
===================================================== */

else {

?>


<a
    href="task_updates.php"
    class="back-button"
>

← Back to Employees

</a>


<div class="employee-heading">

👤

<?php

echo htmlspecialchars(
    $selected_employee['full_name']
);

?>

</div>


<?php


if (
    $updates_result &&
    $updates_result->num_rows > 0
) {


    while (
        $row =
        $updates_result->fetch_assoc()
    ) {


?>


<div class="update-box">


<div class="update-id">

Update ID:

#<?php

echo htmlspecialchars(
    $row['update_id']
);

?>

</div>


<!-- PROJECT -->

<div class="project-name">

Project Name:

<?php

echo htmlspecialchars(
    $row['project_name']
);

?>

</div>


<!-- MEETING -->

<div class="info">

<strong>
Meeting:
</strong>

<?php

echo htmlspecialchars(
    $row['meeting_title'] ?? ''
);

?>

</div>


<!-- TASK -->

<div class="info">

<strong>
Task:
</strong>

<?php

echo htmlspecialchars(
    $row['task_title']
);

?>

</div>


<!-- STATUS -->

<div class="info">

<strong>
Status:
</strong>

<span class="status">

<?php

echo htmlspecialchars(
    $row['status']
);

?>

</span>

</div>


<!-- PROGRESS -->

<div class="info">

<strong>
Progress:
</strong>

<span class="progress">

<?php

echo htmlspecialchars(
    $row['progress']
);

?>%

</span>

</div>


<!-- EMPLOYEE UPDATE -->

<div class="update-details">

<strong>
Employee Update:
</strong>

<br>


<?php

if (
    !empty($row['update_details'])
) {

    echo nl2br(
        htmlspecialchars(
            $row['update_details']
        )
    );

} else {

    echo "No update details provided.";

}

?>

</div>


<!-- UPDATED TIME -->

<div class="updated-time">

Updated At:

<?php

echo htmlspecialchars(
    $row['updated_at']
);

?>

</div>


</div>


<?php

    }


} else {

?>


<div class="no-update">

No updates found for this employee.

</div>


<?php

}


}


?>


</div>


</body>

</html>