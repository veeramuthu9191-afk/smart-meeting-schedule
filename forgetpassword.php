<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? "");
    $newPassword = $_POST['newPassword'] ?? "";
    $confirmPassword = $_POST['confirmPassword'] ?? "";

    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {

        $message = "Please fill all fields.";

    } elseif ($newPassword !== $confirmPassword) {

        $message = "Password not matching.";

    } else {

        // Check employee email
        $stmt = mysqli_prepare($conn, "SELECT id FROM employee WHERE email = ?");

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = mysqli_prepare(
                $conn,
                "UPDATE employee SET password = ? WHERE email = ?"
            );

            mysqli_stmt_bind_param($update, "ss", $hashedPassword, $email);

            if (mysqli_stmt_execute($update)) {

                $message = "Password changed successfully.";

            } else {

                $message = "Failed to change password.";
            }

            mysqli_stmt_close($update);

        } else {

            $message = "Email not found.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password</title>

    <!-- Your CSS file -->
    <link rel="stylesheet" href="forgetpassword.css">
</head>

<body>

    <div class="forgot-container">

        <h2>Forgot Password</h2>

        <form method="POST" action="forgetpassword.php">

            <div class="form-group">
                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="form-group">
                <label>New Password</label>

                <input
                    type="password"
                    name="newPassword"
                    placeholder="Enter new password"
                    required
                >
            </div>

            <div class="form-group">
                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirmPassword"
                    placeholder="Confirm new password"
                    required
                >
            </div>

            <button type="submit">
                Change Password
            </button>

        </form>

        <?php if (!empty($message)): ?>

            <p class="message">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>

        <a href="login.php">Back to Login</a>

    </div>

    <!-- Your JS file -->
    <script src="forgetpassword.js"></script>

</body>

</html>