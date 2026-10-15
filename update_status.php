<?php

include "../db.php";


if(isset($_POST['status']))
{

    $status = $_POST['status'];

    // Temporary employee id
    $id = 1;


    $query = "UPDATE users SET availability='$status' WHERE id='$id'";


    if(mysqli_query($conn,$query))
    {
        header("Location: dashboard.php");
        exit();
    }
    else
    {
        echo "Status update failed";
    }

}

?>