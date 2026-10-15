<!DOCTYPE html>
<html>

<head>
    <title>Smart Meeting System</title>
    <link rel="stylesheet" href="style.css">
</head>


<body>


<!-- Top Right Signup Button -->
<div class="top-bar">

    <a href="signup.html" class="signup-btn">
        Sign Up
    </a>

</div>



<div class="login-container">


<h1>Smart Meeting System</h1>

<p>Sign In to Continue</p>



<form id="loginForm" method="POST" action="signin.php">


    <label>Email Address</label>

    <input 
        type="email"
        name="email"
        placeholder="Enter your email"
        required
    >



    <label>Password</label>

    <input 
        type="password"
        name="password"
        placeholder="Enter your password"
        required
    >



    <div class="forgot-password">

        <a href="forgetpassword.php">
            Forgot Password?
        </a>

    </div>




    <label>Select Role</label>


    <select name="role" required>


        <option value="">
            -- Select Role --
        </option>


        <option value="manager">
            Manager
        </option>


        <option value="employee">
            Employee
        </option>


    </select>




    <button type="submit">
        Sign In
    </button>



</form>


</div>


</body>

</html>