<?php
session_start();
include("./database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    
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


<form action="" method="post">
    <div class="container mt-5">
        <div class="card shadow-lg p-4 border-0">
            <h2 class="text-center fw-bold" style="font-size: 3rem; letter-spacing: 2.5px; color: #007bff; text-decoration: underline;">
                <i class="fas fa-folder-open"></i> Add New Project
            </h2>

            <!-- Form Starts -->
            <form action="" method="post">
                <!-- Project Name -->
                <div class="mb-3">
                    <label for="Project_Name" class="form-label fw-bold">
                        <i class="fas fa-building"></i> Project Name:
                    </label>
                    <textarea class="form-control border-primary shadow-sm" 
                            name="Project_Name" 
                            id="Project_Name" 
                            placeholder="Enter Project Name" 
                            style="height: 120px; font-size: 16px; resize: none;" 
                            required></textarea>
                </div>

                <!-- NIT Number -->
                <div class="mb-3">
                    <label for="NIT" class="form-label fw-bold">
                        <i class="fas fa-file-alt"></i> NIT No.:
                    </label>
                    <input type="text" 
                        class="form-control border-primary shadow-sm" 
                        name="NIT" 
                        id="NIT" 
                        placeholder="Enter NIT No." 
                        style="font-size: 16px;" 
                        required>
                </div>

                <!-- Buttons -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow">
                        <i class="fas fa-plus-circle"></i> Add Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</form>

</body>
</html>


<?php

if (isset($_POST['Project_Name']) && isset($_POST['NIT'])) {
    $user_id = $_SESSION['user'];
    $projectName = ($_POST['Project_Name']);
    $nitNo = ($_POST['NIT']);
    
    
    // SQL statement to create a new table
    //$sql = "CREATE TABLE Project_Detail (
      //  Serial_Number INT(5)PRIMARY KEY,
        //User Varchar(30),
       //Project_Name VARCHAR(3000),
      // NIT_No VARCHAR(30) )";

    // Execute the SQL query
    // $conn->query($sql);


    $checkSql = "SELECT COUNT(*) FROM Project_Detail WHERE NIT_No = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $nitNo); // Bind the NIT number parameter
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();

    $checkStmt->close();

    // If the NIT number already exists
    if ($count > 0) {
        echo "NIT No. '$nitNo' already exists";
    } else {
        $sql = "INSERT INTO Project_Detail (User, Project_Name, NIT_No) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $user_id, $projectName, $nitNo);
    
        if ($stmt->execute()) {
            echo "Project added successfully! <br>";

            $createEstimateTable = "
                CREATE TABLE IF NOT EXISTS `" . $nitNo . "` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nit_no VARCHAR(50) NOT NULL,
                    dsr_no VARCHAR(50) NOT NULL,
                    Description_of_Item VARCHAR(5000),
                    quantity FLOAT,
                    Unit VARCHAR(50),
                    Rate FLOAT,
                    Amount FLOAT,
                    MRate FLOAT,
                    MAmount FLOAT
                );
            ";
            /*
            $createDetailTable = "
                CREATE TABLE IF NOT EXISTS `" . $nitNo . "_detail` (
                    `Detail_ID` INT(11) AUTO_INCREMENT PRIMARY KEY,
                    `User_ID` INT(11) NOT NULL,
                    `Task` TEXT NOT NULL,
                    `Start_Date` DATE,
                    `End_Date` DATE
                );
            ";*/

            // Execute dynamic table creation queries
            if ($conn->query($createEstimateTable) === TRUE /*&& $conn->query($createDetailTable) === TRUE*/) {
                
                echo " Proceed Further by clicking on Make Estimate";
            } else {
                echo "Error creating related tables: " . $conn->error;
            }



        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    
    }

    
   
    



} 


?>
