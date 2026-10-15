<?php
session_start();

include "../database/connection.php";

$employee_id = $_SESSION['employee_id'];


/* Get Employee Meetings */

$query = "SELECT
meeting_participants.id AS participant_id,
meetings.title,
meetings.meeting_date,
meetings.start_time,
meetings.end_time,
meetings.description

FROM meeting_participants

INNER JOIN meetings 
ON meeting_participants.meeting_id = meetings.id

WHERE meeting_participants.employee_id = '$employee_id'

ORDER BY meetings.id DESC";


$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html>

<head>

    <title>My Meetings</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<style>

/* ================================
   PAGE
================================ */

*{
    box-sizing: border-box;
}

body{
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f7fb;
    color: #1f2937;
}

.page-container{
    width: 100%;
    max-width: 1200px;
    margin: auto;
    padding: 40px 25px;
}


/* ================================
   HEADER
================================ */

.page-header{
    background: white;
    padding: 28px 32px;
    border-radius: 18px;
    margin-bottom: 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    border: 1px solid #e8edf5;
}

.page-header h1{
    margin: 0;
    font-size: 30px;
    color: #172033;
}

.page-header p{
    margin: 8px 0 0;
    color: #6b7280;
    font-size: 15px;
}


/* ================================
   MEETING GRID
================================ */

.meeting-container{
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}


/* ================================
   MEETING CARD
================================ */

.meeting-card{
    background: white;
    border-radius: 18px;
    padding: 25px;
    border: 1px solid #e5eaf1;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    transition: 0.3s ease;
}

.meeting-card:hover{
    transform: translateY(-5px);
    box-shadow: 0 14px 32px rgba(0,0,0,0.10);
}


/* ================================
   CARD HEADER
================================ */

.meeting-header{
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding-bottom: 18px;
    border-bottom: 1px solid #edf0f4;
}

.meeting-header h2{
    margin: 0;
    font-size: 20px;
    color: #111827;
    flex: 1;
}


/* ================================
   DETAILS
================================ */

.meeting-details{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 20px;
}

.detail-item{
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    padding: 14px;
    border-radius: 12px;
}

.detail-icon{
    font-size: 22px;
}

.detail-item small{
    display: block;
    color: #8a94a6;
    font-size: 11px;
    margin-bottom: 4px;
    text-transform: uppercase;
}

.detail-item strong{
    font-size: 13px;
    color: #263143;
}


/* ================================
   DESCRIPTION
================================ */

.description-section{
    margin-top: 20px;
    padding: 16px;
    background: #fafbfc;
    border-radius: 12px;
}

.description-section h4{
    margin: 0 0 8px;
    font-size: 14px;
    color: #374151;
}

.description-section p{
    margin: 0;
    color: #6b7280;
    font-size: 13px;
    line-height: 1.6;
}


/* ================================
   EMPTY STATE
================================ */

.empty-state{
    grid-column: 1 / -1;
    background: white;
    padding: 70px 20px;
    text-align: center;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
}

.empty-icon{
    font-size: 55px;
    margin-bottom: 15px;
}

.empty-state h2{
    margin: 0;
    color: #374151;
}

.empty-state p{
    color: #6b7280;
}


/* ================================
   MOBILE
================================ */

@media(max-width: 800px){

    .meeting-container{
        grid-template-columns: 1fr;
    }

}

@media(max-width: 550px){

    .page-container{
        padding: 20px 15px;
    }

    .page-header{
        padding: 22px;
    }

    .page-header h1{
        font-size: 25px;
    }

    .meeting-card{
        padding: 20px;
    }

    .meeting-details{
        grid-template-columns: 1fr;
    }

}

</style>

</head>


<body>


<div class="page-container">


    <div class="page-header">

        <div>

            <h1>📅 My Meetings</h1>

            <p>View your scheduled meetings and meeting details</p>

        </div>

    </div>


    <div class="meeting-container">


<?php

if(mysqli_num_rows($result) > 0)
{

    while($row = mysqli_fetch_assoc($result))
    {

?>

        <div class="meeting-card">


            <div class="meeting-header">

                <h2>
                    <?php echo htmlspecialchars($row['title']); ?>
                </h2>

            </div>


            <div class="meeting-details">


                <div class="detail-item">

                    <span class="detail-icon">📅</span>

                    <div>

                        <small>Date</small>

                        <strong>
                            <?php echo htmlspecialchars($row['meeting_date']); ?>
                        </strong>

                    </div>

                </div>



                <div class="detail-item">

                    <span class="detail-icon">⏰</span>

                    <div>

                        <small>Time</small>

                        <strong>

                            <?php echo htmlspecialchars($row['start_time']); ?>

                            -

                            <?php echo htmlspecialchars($row['end_time']); ?>

                        </strong>

                    </div>

                </div>


            </div>


            <div class="description-section">

                <h4>📝 Description</h4>

                <p>

                    <?php

                    if(!empty($row['description']))
                    {

                        echo nl2br(
                            htmlspecialchars($row['description'])
                        );

                    }
                    else
                    {

                        echo "No description provided.";

                    }

                    ?>

                </p>

            </div>


        </div>


<?php

    }

}
else
{

?>

        <div class="empty-state">

            <div class="empty-icon">📅</div>

            <h2>No Meetings Found</h2>

            <p>You currently have no scheduled meetings.</p>

        </div>

<?php

}

?>


    </div>


</div>


</body>

</html>


