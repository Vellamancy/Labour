$sql_fetch1 = "SELECT dsr_no FROM `$nit_no`";
                                            $result = $conn->query($sql_fetch1);
                                            
                                            // Check if rows exist
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $dsr_no = $row['dsr_no']; // Column 3
                                                    $rate = $row['Rate'];     // Column 7
                                                    
                                            
                                                    // Check if column 7 (rate) is NULL
                                                    if (is_null($rate)) {
                                                        // Insert the entire row into 2024_detail
                                                        echo "OKKKS";
                                                    } else {
                                                        echo "not good";
                                                    }
                                                }



                                            $query_detail = "SELECT Description_of_Item, MAmount FROM `$nit_no_detail` WHERE dsr_no = ? ";
                                            $stmt_detail = $conn->prepare($query_detail);
                                            //$dsr_no = '1154';
                                            $stmt_detail->bind_param("s", $dsr_no);
                                            $stmt_detail->execute();
                                            $result_detail = $stmt_detail->get_result();
                                            
                                            while ($row_detail = $result_detail->fetch_assoc()) {
                                                echo "Description: " . $row_detail['Description_of_Item'] . "<br>";
                                                echo "MAmount: " . $row_detail['MAmount'] . "<br>";
                                            }

                                            $totalAmount = 0; // Initialize total amount to 0
                                            $foundTotal = false;

                                            while ($row_detail = $result_detail->fetch_assoc()) {
                                                $description = $row_detail['Description_of_Item'];
                                                $amount = $row_detail['MAmount'];

                                                // Step 3: Check if we reached the 'TOTAL' description.
                                                if (strpos($description, 'TOTAL') !== false) {
                                                    $foundTotal = true;
                                                    break; // Exit loop once we hit TOTAL
                                                }

                                                // Step 4: Add the amount if it's a float (valid number).
                                                if (floatval($amount) > 0) {
                                                    $totalAmount += floatval($amount);
                                                }
                                            }

                                            if ($foundTotal) {
                                                // Output the total amount calculated
                                                echo "Total Amount: " . number_format($totalAmount, 2);
                                            } else {
                                                echo "TOTAL not found in the records.";
                                            }
                                        
                                    

-----------------------------------------------------------------------------------------------------

                                            // Calculate the 10% of the total amount
                                                        $WaterC = round($total_amount * 0.01,2);
                                                        $tot = round($total_amount+$WaterC,2);
                                                    
                                                        // Update the row immediately after the "TOTAL" row
                                                        $next_row_id1 = $total_row_id + 1; // Assuming ID increments sequentially
                                                        $next_row_id2 = $total_row_id + 2;

                                                        // Update the `MAmount` of the next row
                                                        $sql_update_next_row = "UPDATE `$nit_no_detail` 
                                                                                SET MAmount = ? 
                                                                                WHERE id = ?";
                                                        $stmt_update = $conn->prepare($sql_update_next_row);
                                                        $stmt_update->bind_param('di', $WaterC, $next_row_id1);
                                                        $stmt_update->bind_param('di', $tot, $next_row_id2);
                                                    
                                                        if ($stmt_update->execute()) {
                                                            echo "Successfully updated the next row with additional amount.";
                                                        } else {
                                                            echo "Error updating the next row: " . $conn->error;
                                                        }
                                                        $stmt_update->close();

                                                        
 -----------------------------------------------------------------------------------------------------

                                                        $sql_copy_rows = "
                                SELECT * FROM `dar2023` 
                                WHERE `id` >= ? 
                            ";
                            $stmt_copy = $conn->prepare($sql_copy_rows);

                            if ($stmt_copy) {
                                $start_id = $row_dar['id']; // Assuming `id` is the primary key of `dar2023`
                                $stmt_copy->bind_param('i', $start_id);
                                $stmt_copy->execute();
                                $result_copy = $stmt_copy->get_result();

                                // Loop through and insert rows into `nit_no_detailSub`
                                while ($row_copy = $result_copy->fetch_assoc()) {
                                    // Stop copying when `Description_of_Item` is exactly "Say"
                                    if (strcmp(trim($row_copy['Description_of_Item']), 'Say') === 0) {
                                        break;
                                    }
----------------------------------------------------------------------------------------------------


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
            
        <li><a href="myWork.php">Detail Estimate</a></li>
        <li><a href="justification.php">Justification</a></li>

        <li class="logout-item">
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </li>
    </ul>



    <div class="container mt-5">
        <h2>Select NIT Number</h2>
        <form action="analysis.php" method="GET">
            <div class="d-flex align-items-center mb-3">
                <label for="nit_no" class="form-label me-3">NIT No.</label>
                <select name="nit_no" id="nit_no" class="form-select me-3" style="width: 500px;" required>
                    <option value="" disabled selected>Select NIT No.</option>
                    <?php foreach ($nit_no as $nit): ?>
                        <option value="<?= htmlspecialchars($nit); ?>"><?= htmlspecialchars($nit); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Enter for Providing Market Rate</button>
            </div>
        </form>
    </div>

    

</body>
</html>


