<?php

include "db.php";


if(isset($_POST['submit']))
{
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];


    // Check password match
    if($password != $confirmPassword)
    {
        echo "Password not matching";
        exit();
    }


    // Password encrypt
    $password = password_hash($password, PASSWORD_DEFAULT);



    // Insert based on role

    if($role == "manager")
    {
        $query = "INSERT INTO manager(full_name,email,password,phone)
                  VALUES('$name','$email','$password','$phone')";

        $message = "Manager Signup Successful";
    }
    else
    {
        $query = "INSERT INTO employee(full_name,email,password,phone)
                  VALUES('$name','$email','$password','$phone')";

        $message = "Employee Signup Successful";
    }



    if(mysqli_query($conn,$query))
    {
        echo $message;
    }
    else
    {
        echo "Signup Failed: " . mysqli_error($conn);
    }
}


?>