<?php

session_start();

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $role = $_POST['role'] ?? "";

    // Check all fields
    if (empty($email) || empty($password) || empty($role)) {
        echo "Please fill all fields.";
        exit();
    }


    /* =====================================================
       MANAGER LOGIN
    ===================================================== */

    if ($role === "manager") {

        $query = "SELECT * FROM manager WHERE email='$email'";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "Database Error: " . mysqli_error($conn);
            exit();
        }

        if (mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {

                $_SESSION['manager_id'] = $row['id'];

                header("Location: manager/dashboard.php");
                exit();

            } else {

                echo "Invalid Password.";
                exit();
            }

        } else {

            echo "This email is not registered as Manager.";
            exit();
        }
    }


    /* =====================================================
       EMPLOYEE LOGIN
    ===================================================== */

    elseif ($role === "employee") {

        $query = "SELECT * FROM employee WHERE email='$email'";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "Database Error: " . mysqli_error($conn);
            exit();
        }

        if (mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {

                $_SESSION['employee_id'] = $row['id'];

                header("Location: employee/dashboard.php");
                exit();

            } else {

                echo "Invalid Password.";
                exit();
            }

        } else {

            echo "This email is not registered as Employee.";
            exit();
        }
    }


    /* =====================================================
       INVALID ROLE
    ===================================================== */

    else {

        echo "Invalid Role.";
        exit();
    }

}

?>
