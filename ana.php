<?php
session_start();
include("./database.php");

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user'];

// Fetch available NIT numbers
$stmt = $conn->prepare("SELECT NIT_No FROM `Project_Detail` WHERE `User` = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$nit_list = [];
while ($row = $result->fetch_assoc()) {
    $nit_list[] = $row['NIT_No'];
}

// Reverse the NIT order for latest first
$nit_list = array_reverse($nit_list);

// Reset session variables on page load (so "Start Analysis" button is always visible)
$_SESSION['analysis_started'] = false;
unset($_SESSION['nit_no']); // Remove old NIT selection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['start_analysis'])) {
    $_SESSION['nit_no'] = $_POST['nit_no']; // Store selected NIT No
    $_SESSION['nit_saved'] = $_POST['nit_saved']; // Store Market Rate NIT (if any)
    $_SESSION['analysis_started'] = true; // Mark analysis as started
}

// Variables to track form state
$analysis_started = $_SESSION['analysis_started'];
$nit_no = $_SESSION['nit_no'] ?? '';
$nit_saved = $_SESSION['nit_saved'] ?? '';



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
            width: 100%;
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


        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column; /* Make the page elements stack vertically */
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            margin: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }
                        h2 {
                            text-align: center;
                            color: #333;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                        }
                        th, td {
                            padding: 12px;
                            text-align: left;
                            border: 1px solid #ddd;
                        }
                        th {
                            background-color: #4CAF50;
                            color: white;
                        }
                        tr:nth-child(even) {
                            background-color: #f2f2f2;
                        }
                        tr:hover {
                            background-color: #ddd;
                        }
                        form {
                            display: flex;
                            flex-direction: column;
                            margin-top: 30px;
                        }
                        label {
                            margin: 10px 0 5px;
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
        <li><a href="Billing.php">RA Bill</a></li>

        <li class="logout-item">
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </li>
    </ul>

    <div class="container mt-5">
        <h2>NIT Analysis Selection</h2>

        <!-- First form: NIT Selection -->
        <form method="POST">
            <div class="mb-3">
                <label for="nit_no" class="form-label">Select NIT No. (For Analysis)</label>
                <select name="nit_no" id="nit_no" class="form-select" required>
                    <option value="" disabled selected>Select NIT No.</option>
                    <?php foreach ($nit_list as $nit): ?>
                        <option value="<?= htmlspecialchars($nit); ?>">
                            <?= htmlspecialchars($nit); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="nit_saved" class="form-label">Choose Existing NIT No. (For Market Rates)</label>
                <select name="nit_saved" id="nit_saved" class="form-select">
                    <option value="" disabled selected>Select Existing NIT No. (Optional)</option>
                    <?php foreach ($nit_list as $nit): ?>
                        <option value="<?= htmlspecialchars($nit); ?>">
                            <?= htmlspecialchars($nit); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- "Start Analysis" button should always be visible -->
            <button type="submit" name="start_analysis" class="btn btn-success">Start Analysis</button>
        </form>
    </div>

<?php if ($analysis_started): ?>
    <!-- Below section to continue work after selection -->
    <div class="container mt-5">
        <h3>Selected NIT No.: <span class="text-primary"><?= htmlspecialchars($nit_no) ?></span></h3>
        <h4>Using Market Rates from: <span class="text-secondary"><?= htmlspecialchars($nit_saved) ?: "None" ?></span></h4>

        <!-- Additional Work Section -->
        <form method="POST" action="process_data.php">
            <input type="hidden" name="nit_no" value="<?= htmlspecialchars($nit_no) ?>">
            <input type="hidden" name="nit_saved" value="<?= htmlspecialchars($nit_saved) ?>">
            <button type="submit" name="submit_work" class="btn btn-primary">Submit Work</button>
        </form>

        <form method="POST" action="finalize.php">
            <input type="hidden" name="nit_no" value="<?= htmlspecialchars($nit_no) ?>">
            <button type="submit" name="final_submit" class="btn btn-danger mt-3">Finalize</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>


