<?php

session_start();

include "../db.php";

$employee_id = $_SESSION['employee_id'];

$query = "SELECT * FROM employee WHERE id='$employee_id'";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f7fb;
            color: #263238;
        }

        /* Main Wrapper */

        .profile-wrapper {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        /* Main Profile Card */

        .profile-card {
            background: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        /* Profile Header */

        .profile-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 40px;
            color: white;

            display: flex;
            align-items: center;
            gap: 25px;
        }

        /* Avatar */

        .profile-avatar {
            width: 105px;
            height: 105px;

            border-radius: 50%;

            background: white;
            color: #5b21b6;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 42px;
            font-weight: 700;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        /* Header Text */

        .profile-header h2 {
            margin: 0;

            font-size: 30px;
            font-weight: 700;
        }

        .profile-header p {
            margin: 7px 0 0;

            opacity: 0.9;

            font-size: 16px;
        }

        /* Content */

        .profile-content {
            padding: 35px;
        }

        /* Section Title */

        .section-title {
            font-size: 20px;
            font-weight: 700;

            margin-bottom: 25px;

            color: #202938;
        }

        /* Information Grid */

        .info-grid {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;
        }

        /* Information Box */

        .info-box {
            border: 1px solid #e8ecf3;

            border-radius: 15px;

            padding: 20px;

            display: flex;

            align-items: center;

            gap: 16px;

            transition: 0.3s;

            background: #fbfcff;
        }

        .info-box:hover {

            transform: translateY(-3px);

            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.07);

            background: white;
        }

        /* Icon */

        .info-icon {
            width: 48px;
            height: 48px;

            border-radius: 12px;

            background: #eef2ff;

            color: #4f46e5;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;

            flex-shrink: 0;
        }

        /* Information Text */

        .info-text small {

            display: block;

            color: #8a94a6;

            font-size: 13px;

            margin-bottom: 4px;
        }

        .info-text strong {

            font-size: 15px;

            color: #263238;

            word-break: break-word;
        }

        /* Availability Badge */

        .status-badge {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 6px 12px;

            border-radius: 20px;

            background: #dcfce7;

            color: #15803d;

            font-size: 13px;

            font-weight: 600;
        }

        .status-dot {

            width: 8px;
            height: 8px;

            background: #22c55e;

            border-radius: 50%;
        }

        /* Footer */

        .profile-footer {

            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #edf0f5;

            display: flex;

            justify-content: flex-end;
        }

        /* Edit Button */

        .edit-btn {

            border: none;

            padding: 12px 22px;

            border-radius: 10px;

            background: #4f46e5;

            color: white;

            font-weight: 600;

            text-decoration: none;

            transition: 0.3s;
        }

        .edit-btn:hover {

            background: #4338ca;

            transform: translateY(-2px);
        }

        /* Responsive */

        @media (max-width: 700px) {

            .profile-wrapper {

                margin: 20px auto;
            }

            .profile-header {

                padding: 30px 20px;

                flex-direction: column;

                text-align: center;
            }

            .profile-content {

                padding: 25px 20px;
            }

            .info-grid {

                grid-template-columns: 1fr;
            }

            .profile-header h2 {

                font-size: 24px;
            }

        }

    </style>

</head>


<body>


<div class="profile-wrapper">

    <div class="profile-card">


        <!-- Profile Header -->

        <div class="profile-header">


            <div class="profile-avatar">

                <?php

                echo strtoupper(
                    substr($row['full_name'], 0, 1)
                );

                ?>

            </div>


            <div>

                <h2>

                    <?php

                    echo htmlspecialchars(
                        $row['full_name']
                    );

                    ?>

                </h2>


                <p>

                    <i class="bi bi-person-badge"></i>

                    Employee Profile

                </p>

            </div>


        </div>



        <!-- Profile Content -->

        <div class="profile-content">


            <div class="section-title">

                Personal Information

            </div>



            <div class="info-grid">


                <!-- Full Name -->

                <div class="info-box">

                    <div class="info-icon">

                        <i class="bi bi-person"></i>

                    </div>


                    <div class="info-text">

                        <small>
                            Full Name
                        </small>


                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $row['full_name']
                            );

                            ?>

                        </strong>

                    </div>

                </div>



                <!-- Email -->

                <div class="info-box">

                    <div class="info-icon">

                        <i class="bi bi-envelope"></i>

                    </div>


                    <div class="info-text">

                        <small>
                            Email Address
                        </small>


                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $row['email']
                            );

                            ?>

                        </strong>

                    </div>

                </div>



                <!-- Phone -->

                <div class="info-box">

                    <div class="info-icon">

                        <i class="bi bi-telephone"></i>

                    </div>


                    <div class="info-text">

                        <small>
                            Phone Number
                        </small>


                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $row['phone']
                            );

                            ?>

                        </strong>

                    </div>

                </div>



                <!-- Availability -->

                <div class="info-box">

                    <div class="info-icon">

                        <i class="bi bi-circle-fill"></i>

                    </div>


                    <div class="info-text">

                        <small>
                            Current Availability
                        </small>


                        <strong>

                            <span class="status-badge">

                                <span class="status-dot"></span>

                                <?php

                                echo htmlspecialchars(
                                    $row['availability']
                                );

                                ?>

                            </span>

                        </strong>

                    </div>

                </div>


            </div>



            <!-- Footer -->

            <div class="profile-footer">


                <a href="#" class="edit-btn">

                    <i class="bi bi-pencil-square"></i>

                    Edit Profile

                </a>


            </div>


        </div>


    </div>

</div>


</body>

</html>

