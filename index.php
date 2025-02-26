<?php
session_start();
include("./database.php");

if (!isset($_SESSION["user"])) {
    header("location: index.php");
}
$user_id = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="dropdown.css">

    <style>
       /* Navigation Bar Styling */
       .nav {
            display: flex;
            /*justify-content: space-between;*/
            align-items: center;
            list-style: none;
            background-color: #343a40; /* Dark background */
            padding: 0;
            margin: 0;
        }

        .nav li {
            margin: 0;
        }

        .nav a {
            display: inline-block;
            color: white;
            text-decoration: none;
            padding: 15px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .nav a:hover {
            /*background-color: #495057; /* Slightly lighter background on hover */
            /*color: Black; /* Golden text color on hover */
        }

        .logout-item {
            margin-left: Auto; /* Push logout button to the right */
        }

        .logout-item .btn {
            color: #fff;
            background-color: #dc3545; /* Red background for logout */
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-item .btn:hover {
            background-color: #c82333; /* Darker red on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Add shadow effect */
        }

        
        .nav {
            position: sticky;
            top: 0;
            z-index: 1000;
        }
    </style>
</head>
<body>

   

    <ul class="nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="myWork.php">My Works</a></li>
        <li><a href="work_detail.php">Add New Work</a></li>
            
        <li><a href="DetailEstimate.php">Detail Estimate</a></li>
        <li><a href="analysis.php">Justification</a></li>
        <li><a href="Billing.php">RA Bill</a></li>

        <li class="logout-item">
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </li>
    </ul>

    <div class="container">
        <br>
        <h1>Welcome <?= htmlspecialchars($user_id); ?> </h1>
        <br><br>
    </div>

    <div class="table-container">
        
    </div>

</body>
</html>
