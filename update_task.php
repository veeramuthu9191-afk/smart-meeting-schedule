<?php

session_start();

include "../database/connection.php";

if (!isset($_SESSION['employee_id'])) {
    die("Employee not logged in");
}

$employee_id = $_SESSION['employee_id'];

if (!isset($_GET['task_id'])) {
    die("Task not found");
}

$task_id = intval($_GET['task_id']);

$message = "";


/* =========================
   SAVE TASK UPDATE
========================= */

if (isset($_POST['update_task'])) {

    $status = $_POST['status'];
    $progress = intval($_POST['progress']);


    /* =========================
       SAVE UPDATE HISTORY
    ========================= */

    $sql = "
        INSERT INTO task_updates
        (
            task_id,
            employee_id,
            status,
            progress
        )
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iisi",
        $task_id,
        $employee_id,
        $status,
        $progress
    );


    if ($stmt->execute()) {

        /* =========================
           UPDATE CURRENT TASK
        ========================= */

        $update_task_sql = "
            UPDATE tasks
            SET
                status = ?,
                progress = ?
            WHERE id = ?
            AND employee_id = ?
        ";

        $update_task_stmt =
            $conn->prepare($update_task_sql);

        $update_task_stmt->bind_param(
            "siii",
            $status,
            $progress,
            $task_id,
            $employee_id
        );

        $update_task_stmt->execute();

        $update_task_stmt->close();


        /* =========================
           EMPLOYEE AVAILABILITY

           COMPLETED + 100%
           = AVAILABLE
        ========================= */

        if (
            $status == "Completed"
            &&
            $progress == 100
        ) {

            $availability_sql = "
                UPDATE employee
                SET availability = 'Available'
                WHERE id = ?
            ";

            $availability_stmt =
                $conn->prepare(
                    $availability_sql
                );

            $availability_stmt->bind_param(
                "i",
                $employee_id
            );

            $availability_stmt->execute();

            $availability_stmt->close();

        }


        $message =
            "Task update saved successfully!";

    } else {

        $message =
            "Failed to save task update.";

    }

    $stmt->close();
}


/* =========================
   GET TASK WITH PROJECT NAME
========================= */

$sql = "
    SELECT
        task_title,
        project_name,
        description,
        status,
        progress,
        assigned_date,
        due_date
    FROM tasks
    WHERE id = ?
    AND employee_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $task_id,
    $employee_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows == 0) {

    die("Task not found");

}

$task = $result->fetch_assoc();

$stmt->close();


/* =========================
   GET UPDATE HISTORY
========================= */

$history_sql = "
    SELECT
        status,
        progress,
        updated_at
    FROM task_updates
    WHERE task_id = ?
    AND employee_id = ?
    ORDER BY id DESC
";

$history_stmt =
    $conn->prepare($history_sql);

$history_stmt->bind_param(
    "ii",
    $task_id,
    $employee_id
);

$history_stmt->execute();

$history_result =
    $history_stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

<title>Update Task</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
}

.container {
    width: 90%;
    max-width: 750px;
    margin: 40px auto;
}

.box {
    background: white;
    padding: 30px;
    margin-bottom: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.12);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
}

.task-title {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 20px;
}

.task-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 7px;
    margin-bottom: 25px;
}

.task-info p {
    margin: 8px 0;
}

label {
    display: block;
    margin-top: 18px;
    margin-bottom: 7px;
    font-weight: bold;
}

select,
input {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

.submit-button {
    width: 100%;
    margin-top: 25px;
    padding: 12px;
    border: none;
    border-radius: 6px;
    background: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

.submit-button:hover {
    background: #0056b3;
}

.message {
    text-align: center;
    color: green;
    font-weight: bold;
    margin-bottom: 20px;
}

.history-item {
    background: #f8f9fa;
    padding: 18px;
    margin-bottom: 15px;
    border-radius: 7px;
}

.history-item p {
    margin: 7px 0;
}

.back-button {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #007bff;
}

</style>

</head>

<body>

<div class="container">


<!-- =========================
     TASK
========================= -->

<div class="box">

<h2>Update Task</h2>


<?php if ($message != "") { ?>

<div class="message">

<?php
echo htmlspecialchars($message);
?>

</div>

<?php } ?>


<div class="task-title">

<?php
echo htmlspecialchars(
    $task['task_title']
);
?>

</div>


<div class="task-info">

<p>

<strong>Project Name:</strong>

<?php
echo htmlspecialchars(
    $task['project_name']
);
?>

</p>


<p>

<strong>Assigned Date:</strong>

<?php
echo htmlspecialchars(
    $task['assigned_date']
);
?>

</p>


<p>

<strong>Due Date:</strong>

<?php
echo htmlspecialchars(
    $task['due_date']
);
?>

</p>


<p>

<strong>Current Status:</strong>

<?php
echo htmlspecialchars(
    $task['status']
);
?>

</p>


<p>

<strong>Current Progress:</strong>

<?php
echo htmlspecialchars(
    $task['progress']
);
?>%

</p>

</div>


<!-- =========================
     UPDATE FORM
========================= -->

<form method="POST">


<label>
Status
</label>

<select name="status" required>

<option
    value="Pending"
    <?php
    echo ($task['status'] == 'Pending')
        ? 'selected'
        : '';
    ?>
>
Pending
</option>


<option
    value="In Progress"
    <?php
    echo ($task['status'] == 'In Progress')
        ? 'selected'
        : '';
    ?>
>
In Progress
</option>


<option
    value="Completed"
    <?php
    echo ($task['status'] == 'Completed')
        ? 'selected'
        : '';
    ?>
>
Completed
</option>

</select>


<label>
Progress (%)
</label>

<input
    type="number"
    name="progress"
    min="0"
    max="100"
    value="<?php
    echo htmlspecialchars(
        $task['progress']
    );
    ?>"
    required
>


<button
    type="submit"
    name="update_task"
    class="submit-button"
>
Update Task
</button>

</form>


<a
    href="my_tasks.php"
    class="back-button"
>
← Back to My Tasks
</a>

</div>


<!-- =========================
     UPDATE HISTORY
========================= -->

<div class="box">

<h2>
Update History
</h2>


<?php if ($history_result->num_rows > 0) { ?>


<?php while (
    $history =
    $history_result->fetch_assoc()
) { ?>


<div class="history-item">


<p>

<strong>Project Name:</strong>

<?php
echo htmlspecialchars(
    $task['project_name']
);
?>

</p>


<p>

<strong>Status:</strong>

<?php
echo htmlspecialchars(
    $history['status']
);
?>

</p>


<p>

<strong>Progress:</strong>

<?php
echo htmlspecialchars(
    $history['progress']
);
?>%

</p>


<p>

<strong>Updated At:</strong>

<?php
echo htmlspecialchars(
    $history['updated_at']
);
?>

</p>

</div>


<?php } ?>


<?php } else { ?>

<p>
No updates yet.
</p>

<?php } ?>


</div>


</div>

</body>

</html>