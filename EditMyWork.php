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
            align-items: center;
            /*justify-content: space-between;*/
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




        .nav {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Center the table and set width */
        .table-container {
            width: 80%;
            margin: 0 auto; /* Center the table horizontally */
            padding-top: 0px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa; /* Light background for headers */
        }

        /* Adjust column widths */
        .serial-col {
            width: 5%; /* Smaller for serial number */
        }

        .project-name-col {
            width: 40%; /* Larger for project name */
        }

        .nit-no-col {
            width: 25%; /* Medium size for NIT No. */
        }

        .action-col {
            width: 15%; /* Smaller for action buttons */
        }
    </style>
</head>
<body>

    

    <ul class="nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="myWork.php">My Works</a></li>
        <li><a href="work_detail.php">Add New Work</a></li>
            
        <li><a href="DetailEstimate.php">Detail Estimate</a></li>
        <li><a href="justification.php">Justification</a></li>

        <li class="logout-item">
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </li>
    </ul>

    <div class="container">
        <br>
        <h1>Estimate Details <a href="work_detail.php" class="btn btn-warning">Add New Work</a> </h1>
        
    </div>

    <div class="table-container">
        <?php
        if (isset($user_id)) {
            $stmt = $conn->prepare("SELECT * FROM `Project_Detail` WHERE `User` = ?");
            $stmt->bind_param("s", $user_id); // Bind the user_id parameter
            $stmt->execute();
            $result = $stmt->get_result(); // Fetch the result set
        
            $items = [];
            while ($item = $result->fetch_assoc()) {
                $items[] = $item;
            }
        
            $items = array_reverse($items); // Reverse the order of items
        
            echo "<table>"; // Start table
            echo "<tr>
                    <th class='serial-col'>Sr. No.</th>
                    <th class='project-name-col'>Project Name</th>
                    <th class='nit-no-col'>NIT No.</th>
                    <th class='action-col'>Action</th>
                </tr>";
        
            $serialNumber = 1; // Initialize serial number
            foreach ($items as $item) { // Loop through reversed rows
                echo "<tr>
                    <td>" . $serialNumber++ . "</td>
                    <td>" . htmlspecialchars($item['Project_Name']) . "</td>
                    <td>" . htmlspecialchars($item['NIT_No']) . "</td>
                    <td>
                    <a href='Estimate.php?nit_no=" . urlencode($item['NIT_No']) . "' class='btn btn-primary'>Edit Detail</a>
                    </td>
                </tr>";
            }
            echo "</table>"; // End table
        }
        
        ?>
    </div>

</body>
</html>
