<?php
session_start();
include("./database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user'];
$nit_no = isset($_GET['nit_no']) ? $_GET['nit_no'] : '';

// Store the selected NIT No. when POST is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nit_no_ok'])) {
    // Store the selected NIT No. in the session
    $_SESSION['nit_no_ok'] = $_POST['nit_no_ok'];
    // Redirect to analysis.php to prevent form resubmission
    header("Location: analysis.php?nit_no=" . urlencode($nit_no));
    exit();
}

$stmt = $conn->prepare("SELECT NIT_No FROM `Project_Detail` WHERE `User` = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$nit_no_saved = [];
while ($row = $result->fetch_assoc()) {
    $nit_no_saved[] = $row['NIT_No'];
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
        <h1>Justification in progress for NIT No.- <?php echo htmlspecialchars($nit_no); ?></h1> <br>
        <h2>Copy existing Market Rates from NIT No.</h2>
        <form method="POST" action=""> <!-- Use the same page for submission -->
            <div class="d-flex align-items-center mb-3">
                <select name="nit_no_ok" id="nit_no" class="form-select me-3" style="width: 500px;" required>
                    <option value="" disabled selected>Select NIT No.</option>
                    <?php 
                        // Reverse the array to display options from bottom to top
                        $reversedNitNoSaved = array_reverse($nit_no_saved); 
                        foreach ($reversedNitNoSaved as $nit): 
                    ?>
                        <option value="<?= htmlspecialchars($nit); ?>"><?= htmlspecialchars($nit); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Copy Market Rates of available items</button>
            </div>
        </form>
    </div>


</body>
</html>
