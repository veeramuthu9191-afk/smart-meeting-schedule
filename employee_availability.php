<?php

include "../database/connection.php";


/* =========================
   GET MEETING ID
========================= */

if (!isset($_GET['meeting_id'])) {

    die("Meeting not found");

}

$meeting_id = intval($_GET['meeting_id']);


/* =========================
   GET CURRENT MEETING DETAILS
========================= */

$meeting_query = "

    SELECT
        meeting_date,
        start_time,
        end_time

    FROM meetings

    WHERE id = ?

    LIMIT 1

";


$meeting_stmt = $conn->prepare($meeting_query);


if (!$meeting_stmt) {

    die(
        "Meeting query failed: "
        . $conn->error
    );

}


$meeting_stmt->bind_param(
    "i",
    $meeting_id
);


$meeting_stmt->execute();


$meeting_result =
    $meeting_stmt->get_result();


if ($meeting_result->num_rows == 0) {

    $meeting_stmt->close();

    die("Meeting not found");

}


$meeting = $meeting_result->fetch_assoc();


$current_date =
    $meeting['meeting_date'];

$current_start =
    $meeting['start_time'];

$current_end =
    $meeting['end_time'];


$meeting_stmt->close();

?>

<!DOCTYPE html>

<html>

<head>

<title>Employee Availability</title>

<link
    rel="stylesheet"
    href="employee_availability.css"
>

</head>


<body>


<h2>Employee Availability</h2>


<form
    action="send_invite.php"
    method="POST"
>


<input
    type="hidden"
    name="meeting_id"
    value="<?php echo $meeting_id; ?>"
>


<div class="employee-container">


<?php


/* =========================
   GET ALL EMPLOYEES
========================= */

$query = "

    SELECT
        id,
        full_name

    FROM employee

    ORDER BY full_name ASC

";


$result = mysqli_query(
    $conn,
    $query
);


if (!$result) {

    die(
        "Employee query failed: "
        . mysqli_error($conn)
    );

}


while (
    $row =
    mysqli_fetch_assoc($result)
) {


    $employee_id =
        intval($row['id']);


    /* =====================================================
       CHECK EXISTING MEETING CONFLICT
       
       Employee should be BUSY only if they already have
       another meeting at the same date/time.
    ===================================================== */

    $conflict_query = "

        SELECT
            mp.id

        FROM meeting_participants mp

        INNER JOIN meetings m
            ON mp.meeting_id = m.id

        WHERE mp.employee_id = ?

        AND mp.meeting_id != ?

        AND m.meeting_date = ?

        AND m.start_time < ?

        AND m.end_time > ?

        LIMIT 1

    ";


    $conflict_stmt =
        $conn->prepare(
            $conflict_query
        );


    if (!$conflict_stmt) {

        die(
            "Availability query failed: "
            . $conn->error
        );

    }


    $conflict_stmt->bind_param(
        "iisss",
        $employee_id,
        $meeting_id,
        $current_date,
        $current_end,
        $current_start
    );


    $conflict_stmt->execute();


    $conflict_result =
        $conflict_stmt->get_result();


    $is_busy = false;


    if (
        $conflict_result->num_rows > 0
    ) {

        $is_busy = true;

    }


    $conflict_stmt->close();

?>


<div class="employee-card">


<h3>

👤

<?php

echo htmlspecialchars(
    $row['full_name']
);

?>

</h3>


<?php

/* =========================
   BUSY
========================= */

if ($is_busy) {

?>


<p class="busy-status">

🔴 Busy

</p>


<?php

}


/* =========================
   AVAILABLE
========================= */

else {

?>


<p class="available-status">

🟢 Available

</p>


<div class="invite-area">


<label>

<input
    type="checkbox"
    name="employee[]"
    value="<?php

        echo $employee_id;

    ?>"
>

Invite Employee

</label>


</div>


<?php

}

?>


</div>


<?php

}

?>


</div>


<button
    type="submit"
    class="main-button"
>

Invite Selected Employees

</button>


</form>


</body>

</html>