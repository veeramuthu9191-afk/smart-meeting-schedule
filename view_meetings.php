<?php

include "../database/connection.php";


// Temporary employee id for testing
$employee_id = 1;


$query = "SELECT meetings.title,
                 meetings.meeting_date,
                 meetings.start_time,
                 meetings.end_time,
                 meetings.description
          FROM meetings
          INNER JOIN meeting_participants
          ON meetings.id = meeting_participants.meeting_id
          WHERE meeting_participants.employee_id='$employee_id'";


$result = mysqli_query($conn, $query);


?>


<!DOCTYPE html>
<html>
<head>

<title>My Meetings</title>

</head>

<body>


<h2>My Upcoming Meetings</h2>


<?php

if(mysqli_num_rows($result) > 0)
{

    while($row = mysqli_fetch_assoc($result))
    {

?>

<div>

<h3>
<?php echo $row['title']; ?>
</h3>

<p>
Date :
<?php echo $row['meeting_date']; ?>
</p>

<p>
Start Time :
<?php echo $row['start_time']; ?>
</p>

<p>
End Time :
<?php echo $row['end_time']; ?>
</p>

<p>
Description :
<?php echo $row['description']; ?>
</p>

<hr>

</div>


<?php

    }

}
else
{
    echo "No Meetings Found";
}

?>


</body>
</html>