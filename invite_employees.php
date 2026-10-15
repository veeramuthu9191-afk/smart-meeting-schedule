<?php

include "../database/connection.php";


if(isset($_POST['employees']))
{

    $meeting_id = $_POST['meeting_id'];

    foreach($_POST['employees'] as $employee_id)
    {

        $query = "INSERT INTO meeting_participants(meeting_id, employee_id)
                  VALUES('$meeting_id','$employee_id')";

        mysqli_query($conn,$query);

    }


    echo "<script>
    alert('Employees Invited Successfully');
    window.location.href='dashboard.php';
    </script>";

}

else
{

    echo "<script>
    alert('Please select employee');
    history.back();
    </script>";

}

?>