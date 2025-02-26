<?php
session_start();
include("./database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user'];
//$nit_no = isset($_GET['nit_no']) ? $_GET['nit_no'] : '';



$stmt = $conn->prepare("SELECT NIT_No FROM `Project_Detail` WHERE `User` = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$nit_no = [];
while ($row = $result->fetch_assoc()) {
    $nit_no[] = $row['NIT_No'];
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



    <div class="container mt-5">
        <h2>Select NIT Number for Justification</h2>
        <form action="justiMR.php" method="GET">
            <div class="d-flex align-items-center mb-3">
                <label for="nit_no" class="form-label me-3">NIT No.</label>
                <select name="nit_no" id="nit_no" class="form-select me-3" style="width: 500px;" required>
                    <option value="" disabled selected>Select NIT No.</option>
                    <?php 
                        // Reverse the array to display options from bottom to top
                        $reversedNitNo = array_reverse($nit_no); 
                        foreach ($reversedNitNo as $nit): 
                    ?>
                        <option value="<?= htmlspecialchars($nit); ?>"><?= htmlspecialchars($nit); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Enter for Providing Market Rate</button>
            </div>
        </form>
    </div>


    

</body>
</html>

