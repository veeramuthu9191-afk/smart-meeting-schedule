<?php

session_start();

include "../database/connection.php";


/* =========================
   EMPLOYEE LOGIN CHECK
========================= */

if (!isset($_SESSION['employee_id'])) {

    die("Employee not logged in");

}

$employee_id = $_SESSION['employee_id'];


/* =========================
   GET EMPLOYEE TASKS
========================= */

$sql = "
    SELECT
        t.id,
        t.task_title,
        t.status,
        t.progress,
        t.assigned_date,
        t.due_date,

        m.title AS meeting_title,

        e.project_name

    FROM tasks t

    INNER JOIN meetings m
        ON t.meeting_id = m.id

    INNER JOIN employee e
        ON t.employee_id = e.id

    WHERE t.employee_id = ?

    AND NOT (
        t.status = 'Completed'
        AND t.progress = 100
    )

    ORDER BY t.id DESC
";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $employee_id
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

    <title>My Tasks</title>


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

            max-width: 1000px;

            margin: 40px auto;

        }


        h2 {

            text-align: center;

            color: #333;

            margin-bottom: 30px;

        }


        .task-box {

            background: white;

            padding: 25px;

            margin-bottom: 20px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0, 0, 0, 0.12);

        }


        .project-title {

            font-size: 18px;

            font-weight: bold;

            color: #333;

            margin-bottom: 12px;

        }


        .meeting-title {

            color: #666;

            margin-bottom: 15px;

        }


        .task-title {

            font-size: 22px;

            font-weight: bold;

            color: #222;

            margin-bottom: 20px;

        }


        .task-info {

            display: flex;

            gap: 30px;

            flex-wrap: wrap;

            margin-bottom: 20px;

        }


        .info-item {

            color: #444;

        }


        .status {

            font-weight: bold;

        }


        .progress {

            font-weight: bold;

        }


        .update-button {

            display: inline-block;

            padding: 10px 20px;

            background: #007bff;

            color: white;

            text-decoration: none;

            border-radius: 6px;

        }


        .update-button:hover {

            background: #0056b3;

        }


        .no-task {

            text-align: center;

            background: white;

            padding: 30px;

            border-radius: 10px;

            color: #777;

            font-size: 17px;

        }

    </style>

</head>


<body>


<div class="container">


    <h2>

        My Tasks

    </h2>


    <?php

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

    ?>


        <div class="task-box">


            <!-- PROJECT TITLE -->

            <div class="project-title">

                Project Title:

                <?php

                echo htmlspecialchars(
                    $row['project_name']
                );

                ?>

            </div>


            <!-- MEETING -->

            <div class="meeting-title">

                Meeting:

                <?php

                echo htmlspecialchars(
                    $row['meeting_title']
                );

                ?>

            </div>


            <!-- TASK -->

            <div class="task-title">

                Task:

                <?php

                echo htmlspecialchars(
                    $row['task_title']
                );

                ?>

            </div>


            <!-- TASK INFORMATION -->

            <div class="task-info">


                <div class="info-item">

                    <strong>
                        Assigned Date:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $row['assigned_date']
                    );

                    ?>

                </div>


                <div class="info-item">

                    <strong>
                        Due Date:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $row['due_date']
                    );

                    ?>

                </div>


                <div class="info-item">

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


                <div class="info-item">

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


            </div>


            <!-- UPDATE TASK -->

            <a
                href="update_task.php?task_id=<?php
                    echo $row['id'];
                ?>"
                class="update-button"
            >

                Update Task

            </a>


        </div>


    <?php

        }

    } else {

    ?>


        <div class="no-task">

            No Tasks Assigned

        </div>


    <?php

    }

    ?>


</div>


</body>

</html>