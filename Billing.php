
<?php
session_start();
include("./database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user'];
$nit_no = isset($_GET['nit_no']) ? $_GET['nit_no'] : '';



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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


        .left-box {
            width: 20%;
            height: 70%;
            margin-top: 75px;
            margin-left: 10px;
            background-color: #faf3e0;
            padding: 20px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            position: fixed;
            z-index: 1;
        }
        .left-box label {
            font-weight: bold;
            margin-top: -10px;
            margin-bottom: -10px;
        }
        .left-box input {
            margin-bottom: 5px;
        }
        .table-container {
            margin-left: 26%; /* Adjust to avoid overlapping with the left box */
            padding: 20px;
        }

        .table-container {
            margin-left: 21%; /* Adjust to avoid overlapping with the left box */
            padding: 20px;
        }

        .table-bordered {
            border: 1px solid #333; /* Make the table border more prominent */
            border-radius: 8px; /* Rounded corners for the whole table */
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #333; /* Thicker borders for table cells */
            padding: 10px; /* Increase padding inside cells for better readability */
            box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2); /* Light shadow effect for the cells */
            text-align: justify; /* Justify align the content for a cleaner look */
        }

        .table-dark {
            background-color: #343a40; /* Darker background for the table header */
            color: white; /* White text for the header */
        }

        .dropdown {
        position: relative; /* Create a stacking context */
        z-index: 10; /* Make sure the dropdown appears above the left box */
        }

    </style>
    <style>
        th {
            
            vertical-align: middle; /* Center vertically */
        }
        td {
            text-align: justify; /* Justify text in table data cells */
        }
    
    </style>
    <link rel="stylesheet" href="dropdown.css">

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

    
    <!-- Left-side box -->
    <div class="left-box">
    <h3>NIT No.- <?= htmlspecialchars($nit_no); ?></h3>
        <form method="post" action="">
            <div class="mb-3">
                <label for="dsr_no" style= " "class="form-label">DSR Item No.</label>
                <input type="text" class="form-control" id="dsr_no" name="dsr_no" placeholder="Enter DSR No." required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="Float" class="form-control" id="quantity" name="quantity" placeholder="Enter Quantity" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_item">Add Item</button>
            
        </form>
        <br>
        <br>
        <br>
        <form method="post" action="">
            <div class="mb-3">
                <label for="delete_dsr_no" class="form-label">DSR Item No. to be Deleted</label>
                <input type="text" class="form-control" id="delete_dsr_no" name="delete_dsr_no" placeholder="Enter DSR No. to be Deleted" required>
            </div>
            <button type="submit" class="btn btn-danger" name="delete_item">Delete Item</button>
        </form>

    </div>

    <!-- Right-side table -->
    <div class="table-container">
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h1 style="margin-bottom: 20px;">NIT No.- <?= htmlspecialchars($nit_no); ?></h1>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h1 style="margin: 0; flex: 1; text-align: center;">Detail Estimate</h1>
                <a href="download_excel.php?nit_no=<?= urlencode($nit_no); ?>" class="btn btn-success" style="margin-left: 10px;">Download in Excel</a>
            </div>

        </div>

        <table class="table table-bordered table-striped" style =  "align-items: center">
            
            <thead class="table-dark" > 
                <tr>
                    <th>Sr. No.</th>
                    <th>DSR Code</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
                        // Get the input value
                        $dsr_no = $_POST['dsr_no'];
                        $quantity = $_POST['quantity'];
                        
                    
                        if (!empty($dsr_no)) {
                            // Build the query to fetch related rows

                            $query1 = "SELECT dsr_no, Rate FROM `$nit_no` WHERE dsr_no = ?";
                            $stmt1 = $conn->prepare($query1);
                            if ($stmt1) {
                                // Bind the input parameter
                                $stmt1->bind_param("s", $dsr_no);
                    
                                // Execute the query
                                $stmt1->execute();
                    
                                // Fetch the result
                                $result1 = $stmt1->get_result();
                                $row1 = $result1->fetch_assoc();
                            
                                if ($row1) {
                                    if (is_null($row1['Rate']) || $row1['Rate'] === '') {
                                        echo "<p style='color: red; font-size: 20px; text-align: center;'> DSR No. $dsr_no is not a valid end Item. Do check item no. </p>";
                                    } else {
                                        // Row exists and Rate is valid
                                        echo "<p style='color: red; text-align: center; font-size: 20px;'>Item already added. If required change the Quantity. </p>";
                                    }
                                } else {


                                    $query = "
                                        SELECT Rate 
                                        FROM Data.DSR2023  
                                        WHERE `DSR_Code` = ?
                                    ";
    
                                    $stmt = $conn->prepare($query);
                                    if ($stmt) {
                                        $stmt->bind_param("s", $dsr_no);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $row = $result->fetch_assoc();
                                    }
                                    
                                    if ($row) {  // Check if row exists
                                        if (is_null($row['Rate']) || $row['Rate'] === '' || $row['Rate'] == 0) {
                                            echo "<p style='color: red; font-size: 20px; text-align: center;'>
                                                DSR No. $dsr_no is not a valid end Item. Do check item no.
                                            </p>";
                                        } else {
    
                                            $likeConditions = [];
                                            $parts = explode('.', $dsr_no);
                                    
                                            // Create conditions for all parent levels
                                            for ($i = 2; $i <= count($parts); $i++) {
                                                $likeConditions[] = "`DSR_Code` = '" . implode('.', array_slice($parts, 0, $i)) . "'";
                                            }
                                            //$likeConditions = array_reverse($likeConditions);
                                            $queryCondition = implode(' OR ', $likeConditions);
                                            $fetchQuery = "SELECT * FROM Data.DSR2023 WHERE $queryCondition";
                                    
                                            // Execute the query
                                            $result = $conn->query($fetchQuery);
                                    
                                            if ($result && $result->num_rows > 0) {
                                                // Insert into the `$nit_no` table
                                                while ($row = $result->fetch_assoc()) {
                                                    $rowQuantity = ($row['DSR_Code'] === $dsr_no) ? $quantity : NULL;
                                                    $insertQuery = "
                                                        INSERT INTO `$nit_no` (nit_no, dsr_no, Description_of_Item, Unit, Rate, quantity)
                                                        VALUES (?, ?, ?, ?, ?, ?)
                                                    ";
                                                    $insertStmt = $conn->prepare($insertQuery);
                                    
                                                    if ($insertStmt) {
                                                        $rate = !empty($row['Rate']) ? $row['Rate'] : null;
                                                        $insertStmt->bind_param(
                                                            "sssssd",
                                                            $nit_no,
                                                            $row['DSR_Code'],
                                                            $row['Description_of_Items'],
                                                            $row['Unit'],
                                                            $rate,
                                                            $rowQuantity
                                                        );
                                    
                                                        if ($insertStmt->execute()) {
                                                            
                                                            echo "<p style='color: green; font-size: 24px; font-weight: bold; text-align: center; margin-top: -20px;'>Item No. " . $row['DSR_Code'] . " added successfully. </p> ";
                                                        } else {
                                                            echo "Error inserting row: " . $insertStmt->error;
                                                        }
                                                    } else {
                                                        echo "Error preparing insert query: " . $conn->error;
                                                    }
                                                }
                                            } else {
                                                echo "<p style='color: red; font-size: 32px; font-weight: bold; text-align: center; margin-top: 20px;'>DSR Item No. $dsr_no does not Exist</p>";
                                            }
                                        }
                                    
                                    } else {
                                        echo "<p style='color: red; font-size: 32px; font-weight: bold; text-align: center; margin-top: 20px;'>DSR Item No. $dsr_no does not Exist</p>";
                                    }
                                    $amountQuery = "
                                        UPDATE login_register.`$nit_no`
                                        SET Amount = quantity * CAST(Rate AS FLOAT)
                                        WHERE dsr_no = ? AND Rate IS NOT NULL AND Rate != 0;
                                    ";
    
                                    $amountStmt = $conn->prepare($amountQuery);
                                    $amountStmt->bind_param("s", $dsr_no);
                                    $amountStmt->execute();
                                }
                            } 
                        }
                    }
                
                    
                    
            
                ?>
                <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
                        $delete_dsr_no = $_POST['delete_dsr_no'];

                        if (!empty($delete_dsr_no)) {
                            // Step 1: Find the row where `dsr_no = $delete_dsr_no`
                            $checkQuery = "SELECT id, Quantity FROM `$nit_no` WHERE dsr_no = ? LIMIT 1";
                            $checkStmt = $conn->prepare($checkQuery);

                            if ($checkStmt) {
                                $checkStmt->bind_param("s", $delete_dsr_no);
                                $checkStmt->execute();
                                $checkResult = $checkStmt->get_result();
                                $row = $checkResult->fetch_assoc();

                                if ($row) {
                                    $row_id = $row['id'];
                                    $quantity = $row['Quantity'];

                                    // Step 2: If the `Quantity` column is empty, do not allow deletion
                                    if (is_null($quantity) || trim($quantity) === '') {
                                        echo "<p style='color: red; font-size: 20px; text-align: center;'>
                                            DSR Item No. $delete_dsr_no is not a valid item for deletion (Quantity is empty).
                                        </p>";
                                    } else {
                                        // Step 3: Find all rows above this row (including itself) until a row with a non-empty Quantity is found
                                        $deleteIds = [$row_id]; // Start with the found row

                                        $fetchAboveRowsQuery = "SELECT id, Quantity FROM `$nit_no` WHERE id < ? ORDER BY id DESC";
                                        $fetchAboveStmt = $conn->prepare($fetchAboveRowsQuery);

                                        if ($fetchAboveStmt) {
                                            $fetchAboveStmt->bind_param("i", $row_id);
                                            $fetchAboveStmt->execute();
                                            $resultAbove = $fetchAboveStmt->get_result();

                                            while ($aboveRow = $resultAbove->fetch_assoc()) {
                                                if (!empty($aboveRow['Quantity'])) {
                                                    break; // Stop when a row with a non-empty Quantity is found
                                                }
                                                $deleteIds[] = $aboveRow['id']; // Collect IDs to delete
                                            }

                                            // Step 4: Convert IDs to a string and delete them
                                            if (!empty($deleteIds)) {
                                                $idsToDelete = implode(",", $deleteIds);
                                                $deleteQuery = "DELETE FROM `$nit_no` WHERE id IN ($idsToDelete)";

                                                if ($conn->query($deleteQuery) === TRUE) {
                                                    echo "<p style='color: green; font-size: 20px; text-align: center;'>
                                                        DSR Item No. $delete_dsr_no and related items deleted successfully.
                                                    </p>";
                                                } else {
                                                    echo "<p style='color: red; font-size: 20px; text-align: center;'>
                                                        Error deleting rows: " . $conn->error . "
                                                    </p>";
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    // If the DSR No. does not exist
                                    echo "<p style='color: red; font-size: 20px; text-align: center;'>
                                        DSR Item No. $delete_dsr_no not found in the estimate.
                                    </p>";
                                }
                            } else {
                                echo "<p style='color: red; font-size: 20px; text-align: center;'>
                                    Error preparing check query: " . $conn->error . "
                                </p>";
                            }
                        }
                    }
                ?>


                <?php
                    // Fetch data from the nit_no table
                    $estimateData = [];
                    if (!empty($nit_no)) {
                        $fetchQuery = "SELECT * FROM `$nit_no`";
                        $result = $conn->query($fetchQuery);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $estimateData[] = $row;
                            }
                        }
                    }
                ?>
                <?php if (!empty($estimateData)) : ?>
                        <?php $serial = 1; ?>
                        <?php foreach ($estimateData as $row) : ?>
                            <tr>
                                <td><?= $serial++; ?></td>
                                <td><?= htmlspecialchars($row['dsr_no']); ?></td>
                                <td><?= htmlspecialchars($row['Description_of_Item']); ?></td>
                                <td><?= htmlspecialchars($row['quantity']); ?></td>
                                <td><?= htmlspecialchars($row['Unit']); ?></td>
                                <td><?= htmlspecialchars($row['Rate']); ?></td>
                                <td><?= htmlspecialchars($row['Amount']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">No data available.</td>
                        </tr>
                    <?php endif; ?> 
            
                    
            </tbody>
        </table>
    </div>
    
    



</body>
</html>


