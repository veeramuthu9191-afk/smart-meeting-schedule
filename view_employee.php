<?php
include "../db.php";

$query = "SELECT * FROM employee";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>

<html>
<head>

<title>View Employees</title>

<style>

/* =========================
   RESET
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}


/* =========================
   BODY
========================= */

body{

    font-family:"Segoe UI", Arial, sans-serif;

    background:
    linear-gradient(135deg,#eef2ff 0%,#f8fafc 45%,#eef6ff 100%);

    min-height:100vh;

    color:#1e293b;

}


/* =========================
   MAIN CONTAINER
========================= */

.container{

    display:flex;

    gap:28px;

    padding:45px;

    max-width:1400px;

    margin:auto;

}


/* =========================
   ADD EMPLOYEE BOX
========================= */

.add-box{

    width:260px;

    min-width:260px;

    height:240px;

    padding:30px 25px;

    border-radius:22px;

    background:
    linear-gradient(
        145deg,
        #ffffff,
        #f1f3ff
    );

    border:1px solid rgba(99,102,241,0.15);

    box-shadow:
    0 10px 30px rgba(79,70,229,0.08),
    0 2px 8px rgba(0,0,0,0.04);

    text-align:center;

    position:relative;

    overflow:hidden;

    transition:all .35s ease;

}


/* Decorative Circle */

.add-box::before{

    content:"";

    position:absolute;

    width:120px;

    height:120px;

    border-radius:50%;

    background:#e0e7ff;

    top:-55px;

    right:-45px;

    opacity:.7;

}


.add-box::after{

    content:"";

    position:absolute;

    width:80px;

    height:80px;

    border-radius:50%;

    background:#ddd6fe;

    bottom:-40px;

    left:-30px;

    opacity:.5;

}


.add-box:hover{

    transform:translateY(-8px);

    box-shadow:
    0 20px 40px rgba(79,70,229,0.15);

}


/* =========================
   ADD ICON
========================= */

.add-icon{

    width:62px;

    height:62px;

    margin:0 auto 17px;

    border-radius:18px;

    background:
    linear-gradient(
        135deg,
        #6366f1,
        #8b5cf6
    );

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:28px;

    box-shadow:
    0 8px 18px rgba(99,102,241,.28);

    position:relative;

    z-index:2;

}


.add-box h3{

    font-size:19px;

    font-weight:700;

    color:#1e293b;

    margin-bottom:8px;

    position:relative;

    z-index:2;

}


.add-box p{

    font-size:13px;

    line-height:1.5;

    color:#64748b;

    margin-bottom:20px;

    position:relative;

    z-index:2;

}


/* =========================
   ADD BUTTON
========================= */

.add-btn{

    display:inline-block;

    padding:11px 22px;

    border-radius:10px;

    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    color:white;

    text-decoration:none;

    font-size:13px;

    font-weight:600;

    box-shadow:
    0 7px 18px rgba(79,70,229,.25);

    transition:.3s;

    position:relative;

    z-index:2;

}


.add-btn:hover{

    transform:translateY(-2px);

    box-shadow:
    0 10px 25px rgba(79,70,229,.35);

}


/* =========================
   EMPLOYEE GRID
========================= */

.employee-container{

    flex:1;

    display:grid;

    grid-template-columns:
    repeat(2,minmax(300px,1fr));

    gap:22px;

}


/* =========================
   EMPLOYEE CARD
========================= */

.employee-card{

    position:relative;

    background:
    linear-gradient(
        145deg,
        #ffffff 0%,
        #fbfcff 100%
    );

    border-radius:20px;

    padding:25px;

    border:1px solid #e6e9f2;

    box-shadow:
    0 8px 25px rgba(15,23,42,.06);

    overflow:hidden;

    transition:
    transform .35s ease,
    box-shadow .35s ease,
    border-color .35s ease;

}


/* Top Decorative Line */

.employee-card::before{

    content:"";

    position:absolute;

    top:0;

    left:0;

    width:100%;

    height:4px;

    background:
    linear-gradient(
        90deg,
        #4f46e5,
        #8b5cf6,
        #6366f1
    );

}


/* Soft Background Circle */

.employee-card::after{

    content:"";

    position:absolute;

    width:140px;

    height:140px;

    border-radius:50%;

    background:#f1f3ff;

    right:-75px;

    bottom:-75px;

    z-index:0;

}


.employee-card:hover{

    transform:translateY(-7px);

    border-color:#c7d2fe;

    box-shadow:
    0 18px 40px rgba(79,70,229,.12);

}


/* =========================
   EMPLOYEE TOP
========================= */

.employee-top{

    display:flex;

    align-items:center;

    gap:15px;

    padding-bottom:20px;

    margin-bottom:20px;

    border-bottom:1px solid #edf0f6;

    position:relative;

    z-index:1;

}


/* =========================
   PROFILE ICON
========================= */

.profile-icon{

    width:58px;

    height:58px;

    flex-shrink:0;

    border-radius:18px;

    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #8b5cf6
    );

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:22px;

    font-weight:700;

    box-shadow:
    0 8px 18px rgba(79,70,229,.25);

    position:relative;

}


/* =========================
   EMPLOYEE NAME
========================= */

.employee-name{

    font-size:18px;

    font-weight:700;

    color:#172033;

    margin-bottom:5px;

}


.employee-role{

    font-size:12px;

    color:#8a94a6;

    letter-spacing:.3px;

}


/* =========================
   DETAILS
========================= */

.employee-details{

    display:flex;

    flex-direction:column;

    gap:13px;

    position:relative;

    z-index:1;

}


.detail-row{

    display:flex;

    align-items:center;

    gap:13px;

    padding:9px 10px;

    border-radius:11px;

    transition:.25s;

}


.detail-row:hover{

    background:#f5f6ff;

}


/* =========================
   DETAIL ICON
========================= */

.detail-icon{

    width:36px;

    height:36px;

    flex-shrink:0;

    border-radius:10px;

    background:#f0f2ff;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:16px;

}


/* =========================
   DETAIL CONTENT
========================= */

.detail-content{

    min-width:0;

}


.detail-label{

    display:block;

    font-size:10px;

    font-weight:600;

    text-transform:uppercase;

    letter-spacing:.7px;

    color:#9aa3b2;

    margin-bottom:3px;

}


.detail-value{

    display:block;

    font-size:13px;

    font-weight:500;

    color:#475569;

    white-space:nowrap;

    overflow:hidden;

    text-overflow:ellipsis;

}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:1050px){

    .container{

        flex-direction:column;

        padding:30px;

    }


    .add-box{

        width:100%;

        height:auto;

    }


    .employee-container{

        grid-template-columns:
        repeat(2,minmax(280px,1fr));

    }

}


@media(max-width:700px){

    .container{

        padding:20px;

    }


    .employee-container{

        grid-template-columns:1fr;

    }

}

</style>

</head>

<body>

<div class="container">

<!-- =========================
     ADD EMPLOYEE
========================= -->

<div class="add-box">

<div class="add-icon">

＋

</div>

<h3>
Add Employee
</h3>

<p>
Create a new employee profile and add them to your team.
</p>

<a href="add_employee.php" class="add-btn">

Add Employee

</a>

</div>

<!-- =========================
     EMPLOYEE PROFILES
========================= -->

<div class="employee-container">

<?php

while($row = mysqli_fetch_assoc($result))
{

    $name = $row['full_name'];

    $first_letter =
        strtoupper(substr($name,0,1));

?>

<!-- EMPLOYEE CARD -->

<div class="employee-card">

<!-- TOP -->

<div class="employee-top">

<div class="profile-icon">

<?php echo $first_letter; ?>

</div>

<div>

<div class="employee-name">

<?php echo $row['full_name']; ?>

</div>

<div class="employee-role">

Employee Profile

</div>

</div>

</div>

<!-- DETAILS -->

<div class="employee-details">

<!-- EMAIL -->

<div class="detail-row">

<div class="detail-icon">

📧

</div>

<div class="detail-content">

<span class="detail-label">

Email

</span>

<span class="detail-value">

<?php echo $row['email']; ?>

</span>

</div>

</div>

<!-- PHONE -->

<div class="detail-row">

<div class="detail-icon">

📱

</div>

<div class="detail-content">

<span class="detail-label">

Phone

</span>

<span class="detail-value">

<?php echo $row['phone']; ?>

</span>

</div>

</div>

</div>

</div>

<?php

}

?>

</div>

</div>

</body>

</html>



