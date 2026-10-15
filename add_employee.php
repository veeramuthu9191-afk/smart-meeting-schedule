<!DOCTYPE html>
<html>
<head>
    <title>Add Employee</title>
    <link rel="stylesheet" href="add_employee.css">
</head>

<body>

<h2 class="page-title">Add New Employee</h2>

<form method="POST" action="save_employee.php">
    <div class="employee-box">

    <label>Employee Name</label>
    <input type="text" name="name" required>
    <br><br>

    <label>Email</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Phone</label>
    <input type="text" name="phone" required>
    <br><br>

    <label>Department</label>
    <input type="text" name="department">
    <br><br>

    <label>Password</label>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Add Employee</button>

</form>

</body>
</html>