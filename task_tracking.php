<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Task Tracking</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 60px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
        }

        .options {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .option-box {
            width: 350px;
            background: white;
            padding: 35px;
            border-radius: 12px;
            text-align: center;
            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.10);
        }

        .option-box h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .option-box p {
            color: #666;
            margin-bottom: 25px;
        }

        .button {
            display: inline-block;
            padding: 12px 25px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }

        .button:hover {
            opacity: 0.85;
        }

    </style>

</head>


<body>


<div class="container">


    <h1>
        Task Tracking
    </h1>


    <div class="options">


        <!-- CREATE TASK -->

        <div class="option-box">

            <h2>
                Create Task
            </h2>

            <p>
                Create and assign tasks
                to employees.
            </p>

            <a
                href="create_task.php"
                class="button"
            >
                Create Task
            </a>

        </div>


        <!-- TASK UPDATE -->

        <div class="option-box">

            <h2>
                Task Update
            </h2>

            <p>
                View employee task
                updates and progress.
            </p>

            <a
                href="task_updates.php"
                class="button"
            >
                Task Update
            </a>

        </div>


    </div>


</div>


</body>

</html>