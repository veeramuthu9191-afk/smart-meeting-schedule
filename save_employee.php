<?php

include "../db.php";

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$department = $_POST['department'];
$password = $_POST['password'];

$query = "INSERT INTO employee 
(full_name, email, phone, department, password, availability)
VALUES
('$name', '$email', '$phone', '$department', '$password', 'Available')";

$result = mysqli_query($conn, $query);

if($result)
{
    echo "Employee Added Successfully";
}
else
{
    echo "Error: " . mysqli_error($conn);
}

?>