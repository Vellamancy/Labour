<?php
ob_start();
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
//$_SESSION['analysis_started'] = false;
//unset($_SESSION['nit_no']); // Remove old NIT selection


// ✅ **CLEAR nit_no WHEN PAGE LOADS**
if ($_SERVER["REQUEST_METHOD"] !== "POST") { 
    unset($_SESSION['nit_no']); 
    unset($_SESSION['nit_saved']); 
    $_SESSION['analysis_started'] = false;
}

// ✅ **SET nit_no ONLY AFTER Clicking "Start Analysis"**
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['start_analysis'])) {
    $_SESSION['nit_no'] = $_POST['nit_no']; // Store selected NIT No
    if (isset($_POST['nit_saved'])){
        $_SESSION['nit_saved'] = $_POST['nit_saved'];
    }else{
        $_SESSION['nit_saved'] = $_POST['nit_no'];
    }
     // Store Market Rate NIT (if any)
    $_SESSION['analysis_started'] = true; // Mark analysis as started
}

// ✅ **Retrieve Data for Display**
$analysis_started = $_SESSION['analysis_started'] ?? false;
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
        <li><a href="analysis.php">Justification</a></li>
        <li><a href="Billing.php">RA Bill</a></li>

        <li class="logout-item">
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </li>
    </ul>

    <div class="container mt-5">
        <h2>Justification</h2>

        <!-- First form: NIT Selection -->
        <form method="POST">
            <div class="mb-3">
                <label for="nit_no" class="form-label">Select NIT Number for Justification</label>
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
                <label for="nit_saved" class="form-label">Copy existing Market Rates from NIT No. (Optional)</label>
                <select name="nit_saved" id="nit_saved" class="form-select">
                    <option value="" disabled selected>Select NIT No. for coping (Optional)</option>
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
    
<?php endif; ?>
    

    <div class="table-container">
        <?php
        if ($analysis_started && !empty($nit_no)) {
            if (isset($user_id)) {
                //echo "$nit_no";

                /*if (!preg_match('/^[a-zA-Z0-9_]+$/', $nit_no)) {
                    die("Invalid NIT No. format.");
                }*/

                $nit_no = $conn->real_escape_string($nit_no);

                $table_detail = "{$nit_no}_detail";
                $table_MR = "{$nit_no}_MR";
                $table_MRSub = "{$nit_no}_MRSub";
                $table_detailSub = "{$nit_no}_detailSub";
                $table_MRsaved = "{$nit_no}_MRsaved";
                

                // Function to truncate the table if it exists
                function ensureFreshTable($conn, $table_name, $create_query) {
                    $check_table_query = "SHOW TABLES LIKE '$table_name'";
                    $result = $conn->query($check_table_query);

                    if ($result->num_rows > 0) {
                        // Table exists, truncate it
                        $truncate_query = "TRUNCATE TABLE `$table_name`";
                        if ($conn->query($truncate_query) !== TRUE) {
                            echo "Error truncating table `$table_name`: " . $conn->error . "<br>";
                        }
                    } else {
                        // Table does not exist, create it
                        if ($conn->query($create_query) !== TRUE) {
                            echo "Error creating table `$table_name`: " . $conn->error . "<br>";
                        }
                    }
                }

                // Table creation queries
                $sql_create_detail = "CREATE TABLE IF NOT EXISTS `$table_detail` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    dsr_no VARCHAR(50) NOT NULL,
                    Description_of_Item VARCHAR(5000),
                    quantity VARCHAR(50),
                    Unit VARCHAR(50),
                    Rate VARCHAR(50),
                    Amount VARCHAR(50),
                    MRate VARCHAR(50),
                    MAmount VARCHAR(50)
                )";

                $sql_create_MR = "CREATE TABLE IF NOT EXISTS `$table_MR` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    code_no VARCHAR(50),
                    item_description VARCHAR(5000),
                    unit VARCHAR(200),
                    basic_rate Float(10,2),
                    Market_rate Float (10,2)
                )";

                $sql_create_MRSub = "CREATE TABLE IF NOT EXISTS `$table_MRSub` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    dsr_no VARCHAR(50),
                    Market_rate Float (10,2)
                )";


                $sql_create_MRsaved = "CREATE TABLE IF NOT EXISTS `$table_MRsaved` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    code_no VARCHAR(50) UNIQUE,
                    Market_rate Float (10,2)
                )";

                if ($conn->query($sql_create_MRsaved) === TRUE) {
                    //echo "Table created successfully!";
                } else {
                    echo "Error creating table: " . $conn->error;
                }

                // Ensure fresh tables
                ensureFreshTable($conn, $table_detail, $sql_create_detail);
                ensureFreshTable($conn, $table_MR, $sql_create_MR);
                ensureFreshTable($conn, $table_MRSub, $sql_create_MRSub);

            }
        }
        ?>

        <?php
        if ($analysis_started && !empty($nit_no)) {
            //echo "$nit_no";
            $sql_fetch = "SELECT * FROM `$nit_no`";
            $result = $conn->query($sql_fetch);

            // Check if rows exist
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $dsr_no = $row['dsr_no']; // Column 3
                    $rate = $row['Rate'];     // Column 7

                    // Exclude certain keys
                    $row = array_filter($row, function($key) {
                        return $key !== 'nit_no' && $key !== 'id';
                    }, ARRAY_FILTER_USE_KEY);

                    // If Rate is NULL, insert into `_detail`
                    if (is_null($rate)) {
                        $columns = implode(", ", array_keys($row));
                        $values = implode(", ", array_map(function ($value) use ($conn) {
                            return is_null($value) ? "NULL" : "'" . $conn->real_escape_string($value) . "'";
                        }, $row));
                        $sql_insert = "INSERT INTO `" . $nit_no . "_detail` ($columns) VALUES ($values)";

                        if ($conn->query($sql_insert) !== TRUE) {
                            echo "Error copying data to 2024_detail: " . $conn->error . "<br>";
                        }
                    } else {
                        // Find the next id where dsr_no matches and Rate is NULL
                        $sql_find_valid_id = "SELECT id FROM `dar2023` WHERE `dsr_no` = '$dsr_no' AND (`Rate` IS NULL OR `Rate` = '') LIMIT 1";
                        $valid_id_result = $conn->query($sql_find_valid_id);

                        if ($valid_id_result->num_rows > 0) {
                            $valid_id_row = $valid_id_result->fetch_assoc();
                            $valid_start_id = $valid_id_row['id']; // Get the valid starting ID

                            // Fetch all rows starting from this valid ID
                            $sql_fetch_related = "SELECT * FROM `dar2023` WHERE `id` >= $valid_start_id ORDER BY id";
                            $related_result = $conn->query($sql_fetch_related);

                            if ($related_result->num_rows > 0) {
                                $print_rows = true;
                                while ($related_row = $related_result->fetch_assoc()) {
                                    if ($related_row['dsr_no'] === $dsr_no) {
                                        $print_rows = true;
                                    }

                                    if ($print_rows) {
                                        $filtered_data = [
                                            'dsr_no' => $related_row['dsr_no'],
                                            'Description_of_Item' => $related_row['Description_of_Item'],
                                            'quantity' => $related_row['quantity'],
                                            'Unit' => $related_row['Unit'],
                                            'Rate' => $related_row['Rate'],
                                            'Amount' => $related_row['Amount']
                                        ];

                                        $columns = implode(", ", array_keys($filtered_data));
                                        $values = implode(", ", array_map(function ($value) use ($conn) {
                                            return is_null($value) ? "NULL" : "'" . $conn->real_escape_string($value) . "'";
                                        }, $filtered_data));

                                        $sql_insert_related = "INSERT INTO `" . $nit_no . "_detail` ($columns) VALUES ($values)";

                                        if ($conn->query($sql_insert_related) !== TRUE) {
                                            echo "Error inserting data into `{$nit_no}_detail`: " . $conn->error . "<br>";
                                        }

                                        // Stop when "Say" is found
                                        if (trim($related_row['Description_of_Item']) === "Say") {
                                            break;
                                        }
                                    }
                                }
                            }
                        } else {
                            echo "No valid row found for DSR No: $dsr_no where Rate is NULL. Skipping...<br>";
                        }
                    }
                }
            }
        }
        
        ?>

        <?php
        if ($analysis_started && !empty($nit_no)) {
            // Table names
            $nit_no_detail = "{$nit_no}_detail";
            $nit_no_MR = "{$nit_no}_MR";
            $nit_no_MRSub = "{$nit_no}_MRSub";
            $nit_no_MRsaved = "{$nit_no}_MRsaved";
            $basic_rates_table = "Basic_rate";
            $nit_no_detailSub = "{$nit_no}_detailSub";
            $nit_no_saved = "{$nit_saved}_MRsaved";
            //echo "$nit_no";
            //---------------------------------------------
            do {
                $new_entries_found = false; // Reset flag at the beginning
            
                // Step 1: Fetch unique dsr_no values that haven't been processed
                $sql_fetch_dsr = "
                    SELECT DISTINCT dsr_no
                    FROM `$nit_no_detail`
                    WHERE dsr_no NOT IN (SELECT `Code No.` FROM `$basic_rates_table`)
                    AND dsr_no IS NOT NULL
                    AND dsr_no <> 'Code'
                    AND dsr_no NOT IN (SELECT dsr_no FROM `$nit_no`)
                    AND dsr_no NOT IN (SELECT dsr_no FROM `$nit_no_MRSub`);
                    -- Updated condition: Ensuring we only pick new entries
                ";
            
                $result_fetch_dsr = $conn->query($sql_fetch_dsr);
            
                if ($result_fetch_dsr && $result_fetch_dsr->num_rows > 0) {
                    while ($row = $result_fetch_dsr->fetch_assoc()) {
                        $dsr_no = $row['dsr_no']; // Fetch one `dsr_no` at a time
                        //echo "Processing DSR No: $dsr_no <br>";
                        
                        $sql_insert_dsr = "INSERT INTO `$nit_no_MRSub` (`dsr_no`) VALUES (?)";
                        $stmt_insert_dsr = $conn->prepare($sql_insert_dsr);

                        $sql_insert_dsr = "
                            INSERT INTO `$nit_no_MRSub` (`dsr_no`)
                            SELECT ? FROM DUAL WHERE NOT EXISTS (
                                SELECT 1 FROM `$nit_no_MRSub` WHERE `dsr_no` = ?
                            )";
                        $stmt_insert_dsr = $conn->prepare($sql_insert_dsr);
                        if ($stmt_insert_dsr) {
                            $stmt_insert_dsr->bind_param("ss", $dsr_no, $dsr_no);
                            $stmt_insert_dsr->execute();
                            $stmt_insert_dsr->close();
                        }

                        // Step 2: Perform operations on this `dsr_no`
                        $sql_search_dar = "SELECT * FROM `dar2023` WHERE `dsr_no` = ?";
                        $stmt_search = $conn->prepare($sql_search_dar);
            
                        if ($stmt_search) {
                            $stmt_search->bind_param('s', $dsr_no);
                            $stmt_search->execute();
                            $result_search = $stmt_search->get_result();
            
                            if ($result_search && $result_search->num_rows > 0) {
                                while ($row_dar = $result_search->fetch_assoc()) {
                                    $start_id = $row_dar['id'];
            
                                    // Check if Rate is NOT NULL or empty, skip copying if it has a value
                                    if (!is_null($row_dar['Rate']) && $row_dar['Rate'] !== '') {
                                        continue;
                                    }
            
                                    $sql_copy_rows = "SELECT * FROM `dar2023` WHERE `id` >= ?";
                                    $stmt_copy = $conn->prepare($sql_copy_rows);
            
                                    if ($stmt_copy) {
                                        $stmt_copy->bind_param('i', $start_id);
                                        $stmt_copy->execute();
                                        $result_copy = $stmt_copy->get_result();
            
                                        while ($row_copy = $result_copy->fetch_assoc()) {
                                            // Insert copied data into `nit_no_detail`
                                            $sql_insert = "
                                                INSERT INTO `$nit_no_detail` (`dsr_no`, `Description_of_Item`, `quantity`, `Unit`, `Rate`, `Amount`) 
                                                VALUES (?, ?, ?, ?, ?, ?)";
                                            $stmt_insert = $conn->prepare($sql_insert);
            
                                            if ($stmt_insert) {
                                                $dsr_no = $row_copy['dsr_no'];
                                                $Description_of_Item = $row_copy['Description_of_Item'];
                                                $quantity = $row_copy['quantity'];
                                                $Unit = $row_copy['Unit'];
                                                $Rate = $row_copy['Rate'];
                                                $Amount = $row_copy['Amount'];
            
                                                $stmt_insert->bind_param('ssssss', $dsr_no, $Description_of_Item, $quantity, $Unit, $Rate, $Amount);
                                                $stmt_insert->execute();
                                                $stmt_insert->close();
                                            }
            
                                            // Stop copying when `Description_of_Item` is exactly "Say"
                                            if (strcmp(trim($row_copy['Description_of_Item']), 'Say') === 0) {
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            $stmt_search->close();
                        }
                        
            
                        //echo "Completed processing for DSR No: $dsr_no <br>";
            
                        $new_entries_found = true; // New entries were processed, so continue looping
                    }
                }
            
            } while ($new_entries_found); // Keep looping if new entries appear
            

            //======================================================

            
            //--------------------------------------

            
            // Copy unique `dsr_no` values from `nit_no_details` to `nit_no_MR`
            // only if they exist in `Code No` column of `Basic_rates`
            $sql_copy_unique_serially = "
                INSERT INTO `$nit_no_MR` (`code_no`)
                SELECT DISTINCT d.`dsr_no`
                FROM `$basic_rates_table` AS b
                INNER JOIN `$nit_no_detail` AS d
                    ON d.`dsr_no` = b.`Code No.`
                WHERE d.`dsr_no` IS NOT NULL
                AND d.`dsr_no` <> '' -- Exclude blank values
                AND d.`dsr_no` NOT IN (
                    SELECT `code_no` FROM `$nit_no_MR`
                )
                ORDER BY d.`dsr_no`
            ";


            if ($conn->query($sql_copy_unique_serially) === TRUE) {
                //echo "Unique non-blank data copied serially to `$nit_no_MR`.";
            } else {
                echo "Error copying data: " . $conn->error;
            }

            $sql_update = "
                UPDATE `$nit_no_MR` AS m
                INNER JOIN `$basic_rates_table` AS n
                    ON m.`code_no` = n.`Code No.`
                SET m.`item_description` = n.`Description`, m.`unit` = n.`Unit`, m.`basic_rate` = n.`Basic Rate`
            ";

            if ($conn->query($sql_update) === TRUE) {
                //echo "Data successfully updated in `$nit_no_MR` based on `Basic_rate`.";
            } else {
                echo "Error updating data: " . $conn->error;
            }
            //$sql = "SELECT * FROM `$nit_no_MR`";
            //$result = $conn->query($sql);

            // Close the connection
            //header("Location: justification.php");
        }
        ?>


        <?php
        if ($analysis_started && !empty($nit_no)) {
            $market_rate_data = []; // Array to store market rates from $nit_no_saved
            $data_MR = []; // Array to store data from $nit_no_MR
            
            // Fetch data from $nit_no_MR table
            $query_MR = "SELECT code_no, item_description, unit, basic_rate FROM `$nit_no_MR`";
            $result_MR = $conn->query($query_MR);
            
            if ($result_MR && $result_MR->num_rows > 0) {
                while ($row = $result_MR->fetch_assoc()) {
                    $data_MR[] = $row;
                }
            }
            
            // Fetch data from $nit_no_saved table
            $query_saved = "SELECT code_no, market_rate FROM `$nit_no_saved`";
            $result_saved = $conn->query($query_saved);
            
            if ($result_saved && $result_saved->num_rows > 0) {
                while ($row = $result_saved->fetch_assoc()) {
                    $market_rate_data[$row['code_no']] = $row['market_rate']; // Store rates with code_no as key
                }
            }
        }
        ?>
    </div>
    <div style="border-top: 2px solid black; margin-top: 10px; margin-bottom: 10px;"></div>
    <?php if ($analysis_started && !empty($nit_no)): ?>
        
        <?php 
        echo "<div style=' text-align: center; font-size: 36px; margin-top: 20px; margin-bottom : -35px;'> NIT No.- $nit_no </div>";
        ?>
        <div class="container mt-5">
            <h2 style="margin-bottom: -45px;">Provide Market Rates</h2>
            <?php if (count($data_MR) > 0): ?>
                <form method="POST"> <!-- Form submission to save data -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="background-color: lightblue; font-weight: bold; width: 8%;">Code No</th>
                                <th style="background-color: lightblue; font-weight: bold; width: 50%;">Item Description</th>
                                <th style="background-color: lightblue; font-weight: bold; width: 8%;">Unit</th>
                                <th style="background-color: lightblue; font-weight: bold; width: 15%;">Basic Rate</th>
                                <th style="background-color: lightblue; font-weight: bold; width: 19%;">Market Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_MR as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['code_no']); ?></td>
                                    <td><?php echo htmlspecialchars($row['item_description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['unit']); ?></td>
                                    <td><?php echo htmlspecialchars($row['basic_rate']); ?></td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="market_rate[<?php echo htmlspecialchars($row['code_no']); ?>]" 
                                            value="<?php echo isset($market_rate_data[$row['code_no']]) ? htmlspecialchars($market_rate_data[$row['code_no']]) : ''; ?>" 
                                            placeholder="Enter Market Rate" 
                                            class="form-control"
                                        >
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" name="submit_market_rates" class="btn btn-primary">Submit</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
                                    <?php
                                        
                                        $nit_no_detail = "{$nit_no}_detail";
                                        $nit_no_MR = "{$nit_no}_MR";
                                        $nit_no_MRSub = "{$nit_no}_MRSub";
                                        $nit_no_MRsaved = "{$nit_no}_MRsaved";
                                        $basic_rates_table = "Basic_rate";
                                        $nit_no_saved = "{$nit_saved}_MRsaved";
                                        //echo "Before If: $nit_no"; // First echo, should print

                                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_market_rates'])) {
                                            //echo "OKKZ"; // Should print
                                            //echo "Inside If: $nit_no"; // Second echo, check if empty

                                            //var_dump($nit_no); // See what exactly is stored
                                        
                                            
                                            $marketRates = $_POST['market_rate'];
                                        
                                            // Prepare the SQL statement to update Market_rate
                                            $stmt = $conn->prepare("UPDATE `$nit_no_MR` SET Market_rate = ? WHERE code_no = ?");
                                            if ($stmt === false) {
                                                die("Error preparing statement: " . $conn->error);
                                            }
                                        
                                            // Iterate through each market rate and update the database
                                            foreach ($marketRates as $code_no => $market_rate) {
                                                // Skip empty market rates
                                                if (trim($market_rate) === "") {
                                                    continue;
                                                }
                                        
                                                // Bind parameters and execute the statement
                                                $stmt->bind_param("ds", $market_rate, $code_no); // "d" for float, "s" for string
                                                $stmt->execute();
                                            }
                                            

                                            

                                            $sqlsave = "
                                                INSERT INTO `$nit_no_MRsaved` (code_no, market_rate)
                                                SELECT DISTINCT code_no, market_rate
                                                FROM `$nit_no_MR`
                                                ON DUPLICATE KEY UPDATE
                                                    market_rate = VALUES(market_rate)
                                            ";


                                            if ($conn->query($sqlsave) === TRUE) {
                                                //echo "Data copied successfully!";
                                            } else {
                                                echo "Error: " . $conn->error;
                                            }

                                            // SQL to update `MRate` in both `nit_no_detail` and `nit_no_detailSub`
                                            $sql_update_rate_both_tables = "
                                            UPDATE `$nit_no_MR` AS m
                                            LEFT JOIN `$nit_no_detail` AS d ON d.dsr_no = m.code_no
                                            SET 
                                                d.MRate = CASE WHEN m.Market_rate IS NOT NULL THEN m.Market_rate ELSE d.MRate END
                                            WHERE m.Market_rate IS NOT NULL
                                            ";

                                            // Execute the query for both `nit_no_detail` and `nit_no_detailSub`
                                            if ($conn->query($sql_update_rate_both_tables) === TRUE) {
                                            //echo "Rate updated successfully in both `$nit_no_detail` and `$nit_no_detailSub` tables.<br>";
                                            } else {
                                            echo "Error updating Rate in both tables: " . $conn->error . "<br>";
                                            }



                                            // SQL to update `Amount` as the product of `quantity` and `Rate`
                                            $sql_update_amount = "
                                                UPDATE `$nit_no_detail`
                                                SET MAmount = 
                                                    ROUND(CAST(quantity AS DECIMAL(10, 4)) * CAST(MRate AS DECIMAL(10, 2)), 2)
                                                WHERE quantity REGEXP '^[0-9]+(\\.[0-9]+)?$'
                                                AND MRate REGEXP '^[0-9]+(\\.[0-9]+)?$';
                                            ";

                                            if ($conn->query($sql_update_amount) === TRUE) {
                                                //echo "Amount column updated successfully for valid rows.";
                                            } else {
                                                echo "Error: " . $conn->error;
                                            }
                                            

                                            //---------------------------------------------------------------------
                                            
                                            for ($i = 1; $i <= 3; $i++) {
                                                //echo "The value of i is: $i <br>";
                                                $sql_fetch = "SELECT dsr_no FROM `$nit_no_MRSub`";
                                                $result = $conn->query($sql_fetch);

                                                if ($result) {
                                                    //echo "OKKKKK";
                                                    while ($row = $result->fetch_assoc()) {
                                                        $dsr_no = $row['dsr_no']; // Fetch `dsr_no`
                                                        //echo "$dsr_no <br>";
                                                        // Search for the same `dsr_no` in the `nit_no_detailSub` table
                                                        $sql_search_detail = "SELECT * FROM `$nit_no_detail` WHERE dsr_no = ?";
                                                        $stmt = $conn->prepare($sql_search_detail);
                                                    
                                                        if ($stmt) {
                                                            $stmt->bind_param("s", $dsr_no);
                                                            $stmt->execute();
                                                            $result_detail = $stmt->get_result();
                                                    
                                                            // Process all rows with the same `dsr_no`
                                                            while ($row_detail = $result_detail->fetch_assoc()) {
                                                                $start_index = $row_detail['id'];
                                                                $quantity = $row_detail['quantity'];
                                                    
                                                                // Check if `quantity` is not empty or null
                                                                if (!empty($quantity)) {
                                                                    //echo "Found row with id $start_index and quantity $quantity for dsr_no: $dsr_no <br>";
                                                                    
                                                                    // Continue searching for the next row with the same `dsr_no`
                                                                    continue;
                                                                } else {
                                                                    //echo "Row with id $start_index has no quantity for dsr_no: $dsr_no <br>";
                                                                    break; // Stop further processing if quantity is missing
                                                                }
                                                            }
                                                        
                                                            
                                                            //echo "$start_index <br>";
                                                            // Fetch all rows from the starting `dsr_no` till finding 'TOTAL' in the Description_of_Item
                                                            $sql_get_amounts = "SELECT id, Description_of_Item, MAmount FROM `$nit_no_detail` WHERE id >= ? ORDER BY id ASC";
                                                            $stmt_amounts = $conn->prepare($sql_get_amounts);
                                                            $stmt_amounts->bind_param("i", $start_index);
                                                            $stmt_amounts->execute();
                                                            $result_amounts = $stmt_amounts->get_result();

                                                            $total_amount = 0;
                                                            $found_total = false;
                                                            
                                                            while ($row_amount = $result_amounts->fetch_assoc()) {
                                                                $description = $row_amount['Description_of_Item'];
                                                                $amount = round($row_amount['MAmount'],2);

                                                                // Stop when Description_of_Item is exactly "TOTAL"
                                                                if (trim($description) === 'TOTAL') {
                                                                    $found_total = true;
                                                                    $total_row_id = $row_amount['id']; // Save the ID of the 'TOTAL' row
                                                                    break;
                                                                }

                                                                // Add to the total only if MAmount is a valid float
                                                                if (is_numeric($amount)) {
                                                                    $total_amount += round((float)$amount,2);
                                                                }
                                                            }

                                                            // Update the 'TOTAL' row with the calculated amount
                                                            if ($found_total) {
                                                                $sql_update_total = "UPDATE `$nit_no_detail` SET MAmount = ? WHERE id = ?";
                                                                $stmt_update = $conn->prepare($sql_update_total);
                                                                $stmt_update->bind_param("di", $total_amount, $total_row_id);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Updated TOTAL for `dsr_no` $dsr_no with amount: $total_amount<br>";
                                                                } else {
                                                                    echo "Failed to update TOTAL for `dsr_no` $dsr_no: " . $conn->error . "<br>";
                                                                }
                                                            }
                                                            // Calculate the 10% of the total amount
                                                            $WaterC = round($total_amount * 0.01, 2);
                                                            $tot = round($total_amount + $WaterC, 2);
                                                            $GST = round($tot*0.2127,2);
                                                            $totg = round($tot+$GST,2);
                                                            $CPOH = round($totg*0.15,2);
                                                            $totCPOH = round($totg+$CPOH,2);
                                                            $cess = round($totCPOH*0.01,2);
                                                            $totf = round($totCPOH+$cess,2);

                                                            // Update the `MAmount` of the next row (next_row_id1)
                                                            $sql_update_next_row = "UPDATE `$nit_no_detail` SET MAmount = ? WHERE id = ?";
                                                            $stmt_update = $conn->prepare($sql_update_next_row);

                                                            $sql_update_next_row_des = "UPDATE `$nit_no_detail` SET Description_of_Item = ? WHERE id = ?";
                                                            $stmt_update2 = $conn->prepare($sql_update_next_row_des);


                                                            // Update the row immediately after the "TOTAL" row
                                                            $next_row_id1 = $total_row_id + 1; // Assuming ID increments sequentially
                                                            $next_row_id2 = $total_row_id + 2;
                                                            $next_row_id3 = $total_row_id + 3;
                                                            $next_row_id4 = $total_row_id + 4;
                                                            $next_row_id5 = $total_row_id + 5;
                                                            $next_row_id6 = $total_row_id + 6;
                                                            $next_row_id7 = $total_row_id + 7;
                                                            $next_row_id8 = $total_row_id + 8;
                                                            $next_row_id9 = $total_row_id + 9;
                                                            $next_row_id10 = $total_row_id + 10;

                                                            
                                                            

                                                            if ($stmt_update) {
                                                                // Update the first next row with $WaterC
                                                                $stmt_update->bind_param('di', $WaterC, $next_row_id1);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id1: " . $conn->error . "<br>";
                                                                }

                                                                // Update the second next row with $tot
                                                                $stmt_update->bind_param('di', $tot, $next_row_id2);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id2 with total amount ($tot).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id2: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $GST, $next_row_id3);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id3: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $totg, $next_row_id4);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id4: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $CPOH, $next_row_id5);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id5: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $totCPOH, $next_row_id6);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id6 with additional amount ($totCPOH).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id6: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $cess, $next_row_id7);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id7 with additional amount ($cess).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id7: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $totf, $next_row_id8);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id8: " . $conn->error . "<br>";
                                                                }


                                                                //------------------------------------------------------------------



                                                                
                                                                $sql_check_description = "SELECT Description_of_Item FROM `$nit_no_detail` WHERE id = ?";
                                                                $stmt_check = $conn->prepare($sql_check_description);

                                                                if ($stmt_check) {
                                                                    $stmt_check->bind_param('i', $next_row_id9); // Assuming `id` is an integer
                                                                    $stmt_check->execute();
                                                                    $stmt_check->bind_result($description_of_item); // Bind the result to a variable
                                                                    $stmt_check->fetch(); // Fetch the result
                                                                    $stmt_check->close();

                                                                    // Check if the fetched value matches "Say"
                                                                    if (trim($description_of_item) === 'Say') {
                                                                        //echo "Description_of_Item is 'Say'. Updating MAmount for id $next_row_id9.<br>";

                                                                        // Prepare the update query
                                                                        $sql_update_specific_row = "UPDATE `$nit_no_detail` SET MAmount = ? WHERE id = ?";
                                                                        $stmt_update = $conn->prepare($sql_update_specific_row);

                                                                        if ($stmt_update) {
                                                                            $stmt_update->bind_param('di', $totf, $next_row_id9); // Bind the calculated total
                                                                            if ($stmt_update->execute()) {
                                                                                //echo "Successfully updated row $next_row_id9 with MAmount ($totf).<br>";

                                                                                $sql_update_nitno = "UPDATE `$nit_no_MRSub` SET Market_rate = ? WHERE dsr_no = ?";
                                                                                $stmt_update_estimate = $conn->prepare($sql_update_nitno);
                                                                                $stmt_update_estimate->bind_param('ds', $total_amount, $dsr_no);
                                                                                $stmt_update_estimate->execute();

                                                                            } else {
                                                                                echo "Error updating row $next_row_id9: " . $conn->error . "<br>";
                                                                            }
                                                                            
                                                                        } else {
                                                                            echo "Error preparing the update statement: " . $conn->error . "<br>";
                                                                        }
                                                                    } else {
                                                                        // SQL query to fetch Amount for $next_row_id9
                                                                        $sql_fetch_amount = "SELECT Amount FROM `$nit_no_detail` WHERE id = ?";
                                                                        $stmt_fetch = $conn->prepare($sql_fetch_amount);

                                                                        if ($stmt_fetch) {
                                                                            // Fetch Amount for $next_row_id9
                                                                            $stmt_fetch->bind_param('i', $next_row_id8);
                                                                            $stmt_fetch->execute();
                                                                            $stmt_fetch->bind_result($amount_value8);
                                                                            $stmt_fetch->fetch();

                                                                            // Close the statement and prepare for the next fetch
                                                                            $stmt_fetch->close();

                                                                            // Fetch Amount for $next_row_id10
                                                                            $stmt_fetch = $conn->prepare($sql_fetch_amount); // Reuse the same query
                                                                            $stmt_fetch->bind_param('i', $next_row_id9);
                                                                            $stmt_fetch->execute();
                                                                            $stmt_fetch->bind_result($amount_value9);
                                                                            $stmt_fetch->fetch();

                                                                            // Close the statement after fetching
                                                                            $stmt_fetch->close();

                                                                            // Perform the division
                                                                            if ($amount_value9 != 0) { // Ensure no division by zero
                                                                                $multiplication = $amount_value8 / $amount_value9;
                                                                            }
                                                                        }

                                                                        $totFinal = round($totf/$multiplication,2);

                                                                        $stmt_update->bind_param('di', $totFinal, $next_row_id9);
                                                                        if ($stmt_update->execute()) {
                                                                            //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                        } else {
                                                                            echo "Error updating row $next_row_id9: " . $conn->error . "<br>";
                                                                        }

                                                                        $stmt_update->bind_param('di', $totFinal, $next_row_id10);
                                                                        if ($stmt_update->execute()) {
                                                                            //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                        } else {
                                                                            echo "Error updating row $next_row_id10: " . $conn->error . "<br>";
                                                                        }

                                                                        $subrate = round($total_amount / $multiplication, 2);
                                                                        $sql_update_nitno = "UPDATE `$nit_no_MRSub` SET Market_rate = ? WHERE dsr_no = ?";
                                                                        $stmt_update_estimate = $conn->prepare($sql_update_nitno);
                                                                        $stmt_update_estimate->bind_param('ds', $subrate, $dsr_no);
                                                                        $stmt_update_estimate->execute();
                                                                    }
                                                                    
                                                                    

                                                                

                                                                } else {
                                                                    echo "Error preparing the check statement: " . $conn->error . "<br>";
                                                                }
                                                            }

                                                        }
                                                        $description1 = 'Add 1 % Water charges';
                                                        $description3 = 'Add GST (multiplying factor 0.2127)';
                                                        $description5 = 'Add 15% CPOH';
                                                        $description7 = 'Add Cess @ 1%';

                                                        if ($stmt_update2) {
                                                                
                                                            // Update the first next row with $WaterC
                                                            $stmt_update2->bind_param('si', $description1, $next_row_id1);
                                                            if ($stmt_update2->execute()) {
                                                                //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                            } else {
                                                                echo "Error updating row $next_row_id1: " . $conn->error . "<br>";
                                                            }

                                                            $stmt_update2->bind_param('si', $description3, $next_row_id3);
                                                            if ($stmt_update2->execute()) {
                                                                //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                            } else {
                                                                echo "Error updating row $next_row_id3: " . $conn->error . "<br>";
                                                            }

                                                            $stmt_update2->bind_param('si', $description5, $next_row_id5);
                                                            if ($stmt_update2->execute()) {
                                                                //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                            } else {
                                                                echo "Error updating row $next_row_id5: " . $conn->error . "<br>";
                                                            }

                                                            $stmt_update2->bind_param('si', $description7, $next_row_id7);
                                                            if ($stmt_update2->execute()) {
                                                                //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                            } else {
                                                                echo "Error updating row $next_row_id7: " . $conn->error . "<br>";
                                                            }
                                                        }
                                                        // SQL to update `MRate` in both `nit_no_detail` and `nit_no_detailSub`
                                                        $sql_update_rate_for_Sub_item1 = "
                                                            UPDATE `$nit_no_MRSub` AS m
                                                            LEFT JOIN `$nit_no_detail` AS d ON d.dsr_no = m.dsr_no
                                                            
                                                            SET 
                                                                d.MRate = CASE 
                                                                            WHEN m.Market_rate IS NOT NULL AND (d.Rate IS NOT NULL AND d.Rate <> '') 
                                                                            THEN m.Market_rate 
                                                                            ELSE d.MRate 
                                                                        END
                                                            
                                                            WHERE m.Market_rate IS NOT NULL
                                                        ";


                                                        // Execute the query for both `nit_no_detail` and `nit_no_detailSub`
                                                        if ($conn->query($sql_update_rate_for_Sub_item1) === TRUE) {
                                                        //echo "Rate updated successfully in both `$nit_no_detail` and `$nit_no_detailSub` tables.<br>";
                                                        } else {
                                                        echo "Error updating Rate in both tables: " . $conn->error . "<br>";
                                                        }



                                                        // SQL to update `Amount` as the product of `quantity` and `Rate`
                                                        $sql_update_amount = "
                                                            UPDATE `$nit_no_detail`
                                                            SET MAmount = 
                                                                ROUND(CAST(quantity AS DECIMAL(10, 4)) * CAST(MRate AS DECIMAL(10, 2)), 2)
                                                            WHERE quantity REGEXP '^[0-9]+(\\.[0-9]+)?$'
                                                            AND MRate REGEXP '^[0-9]+(\\.[0-9]+)?$';
                                                        ";

                                                        if ($conn->query($sql_update_amount) === TRUE) {
                                                            //echo "Amount column updated successfully for valid rows.";
                                                        } else {
                                                            echo "Error: " . $conn->error;
                                                        }

                                                    }
                                                }
                                                

                                            }

                                        

                                            //================================================================================================================================
                                            // For adding the total in the MAmount Row of nit_no_detail
                                            
                                            $sql_fetch = "SELECT * FROM `$nit_no`";
                                            $result = $conn->query($sql_fetch);

                                            if ($result) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $dsr_no = $row['dsr_no']; // Fetch `dsr_no`
                                                    $rate = $row['Rate'];     // Fetch `Rate`

                                                    if (is_null($rate) || $rate === '' || strtolower($rate) === 'nil') {
                                                        // Skip processing if the rate is null, empty, or 'nil'
                                                        continue;
                                                    }

                                                    // Search for the same `dsr_no` in the `nit_no_detail` table
                                                    $sql_search_detail = "SELECT * FROM `$nit_no_detail` WHERE dsr_no = ?";
                                                    $stmt = $conn->prepare($sql_search_detail);
                                                    $stmt->bind_param("s", $dsr_no);
                                                    $stmt->execute();
                                                    $result_detail = $stmt->get_result();

                                                    if ($result_detail->num_rows > 0) {
                                                        $row_detail = $result_detail->fetch_assoc(); // Get the row matching the `dsr_no`
                                                        $start_index = $row_detail['id']; // Assuming there is an `id` column to determine the row order

                                                        // Fetch all rows from the starting `dsr_no` till finding 'TOTAL' in the Description_of_Item
                                                        $sql_get_amounts = "SELECT id, Description_of_Item, MAmount FROM `$nit_no_detail` WHERE id >= ? ORDER BY id ASC";
                                                        $stmt_amounts = $conn->prepare($sql_get_amounts);
                                                        $stmt_amounts->bind_param("i", $start_index);
                                                        $stmt_amounts->execute();
                                                        $result_amounts = $stmt_amounts->get_result();

                                                        $total_amount = 0;
                                                        $found_total = false;

                                                        while ($row_amount = $result_amounts->fetch_assoc()) {
                                                            $description = $row_amount['Description_of_Item'];
                                                            $amount = round($row_amount['MAmount'],2);

                                                            // Stop when Description_of_Item is exactly "TOTAL"
                                                            if (trim($description) === 'TOTAL') {
                                                                $found_total = true;
                                                                $total_row_id = $row_amount['id']; // Save the ID of the 'TOTAL' row
                                                                break;
                                                            }

                                                            // Add to the total only if MAmount is a valid float
                                                            if (is_numeric($amount)) {
                                                                $total_amount += (float)$amount;
                                                            }
                                                        }

                                                        // Update the 'TOTAL' row with the calculated amount
                                                        if ($found_total) {
                                                            $sql_update_total = "UPDATE `$nit_no_detail` SET MAmount = ? WHERE id = ?";
                                                            $stmt_update = $conn->prepare($sql_update_total);
                                                            $stmt_update->bind_param("di", $total_amount, $total_row_id);
                                                            if ($stmt_update->execute()) {
                                                                //echo "Updated TOTAL for `dsr_no` $dsr_no with amount: $total_amount<br>";
                                                            } else {
                                                                echo "Failed to update TOTAL for `dsr_no` $dsr_no: " . $conn->error . "<br>";
                                                            }



                                                            // Calculate the 10% of the total amount
                                                            $WaterC = round($total_amount * 0.01, 2);
                                                            $tot = round($total_amount + $WaterC, 2);
                                                            $GST = round($tot*0.2127,2);
                                                            $totg = round($tot+$GST,2);
                                                            $CPOH = round($totg*0.15,2);
                                                            $totCPOH = round($totg+$CPOH,2);
                                                            $cess = round($totCPOH*0.01,2);
                                                            $totf = round($totCPOH+$cess,2);

                                                            // Update the `MAmount` of the next row (next_row_id1)
                                                            $sql_update_next_row = "UPDATE `$nit_no_detail` SET MAmount = ? WHERE id = ?";
                                                            $stmt_update = $conn->prepare($sql_update_next_row);

                                                            $sql_update_next_row_des = "UPDATE `$nit_no_detail` SET Description_of_Item = ? WHERE id = ?";
                                                            $stmt_update1 = $conn->prepare($sql_update_next_row_des);


                                                            // Update the row immediately after the "TOTAL" row
                                                            $next_row_id1 = $total_row_id + 1; // Assuming ID increments sequentially
                                                            $next_row_id2 = $total_row_id + 2;
                                                            $next_row_id3 = $total_row_id + 3;
                                                            $next_row_id4 = $total_row_id + 4;
                                                            $next_row_id5 = $total_row_id + 5;
                                                            $next_row_id6 = $total_row_id + 6;
                                                            $next_row_id7 = $total_row_id + 7;
                                                            $next_row_id8 = $total_row_id + 8;
                                                            $next_row_id9 = $total_row_id + 9;
                                                            $next_row_id10 = $total_row_id + 10;

                                                            
                                                            

                                                            if ($stmt_update) {
                                                                // Update the first next row with $WaterC
                                                                $stmt_update->bind_param('di', $WaterC, $next_row_id1);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id1: " . $conn->error . "<br>";
                                                                }

                                                                // Update the second next row with $tot
                                                                $stmt_update->bind_param('di', $tot, $next_row_id2);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id2 with total amount ($tot).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id2: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $GST, $next_row_id3);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id3: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $totg, $next_row_id4);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id4: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $CPOH, $next_row_id5);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id5: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $totCPOH, $next_row_id6);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id6 with additional amount ($totCPOH).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id6: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $cess, $next_row_id7);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id7 with additional amount ($cess).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id7: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update->bind_param('di', $totf, $next_row_id8);
                                                                if ($stmt_update->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id8: " . $conn->error . "<br>";
                                                                }


                                                                $sql_check_description = "SELECT Description_of_Item FROM `$nit_no_detail` WHERE id = ?";
                                                                $stmt_check = $conn->prepare($sql_check_description);

                                                                if ($stmt_check) {
                                                                    $stmt_check->bind_param('i', $next_row_id9); // Assuming `id` is an integer
                                                                    $stmt_check->execute();
                                                                    $stmt_check->bind_result($description_of_item); // Bind the result to a variable
                                                                    $stmt_check->fetch(); // Fetch the result
                                                                    $stmt_check->close();

                                                                    // Check if the fetched value matches "Say"
                                                                    if (trim($description_of_item) === 'Say') {
                                                                        //echo "Description_of_Item is 'Say'. Updating MAmount for id $next_row_id9.<br>";

                                                                        // Prepare the update query
                                                                        $sql_update_specific_row = "UPDATE `$nit_no_detail` SET MAmount = ? WHERE id = ?";
                                                                        $stmt_update = $conn->prepare($sql_update_specific_row);

                                                                        if ($stmt_update) {
                                                                            $stmt_update->bind_param('di', $totf, $next_row_id9); // Bind the calculated total
                                                                            if ($stmt_update->execute()) {
                                                                                //echo "Successfully updated row $next_row_id9 with MAmount ($totf).<br>";

                                                                                $sql_update_nitno = "UPDATE `$nit_no` SET MRate = ? WHERE dsr_no = ?";
                                                                                $stmt_update_estimate = $conn->prepare($sql_update_nitno);
                                                                                $stmt_update_estimate->bind_param('ds', $totf, $dsr_no);
                                                                                $stmt_update_estimate->execute();

                                                                            } else {
                                                                                echo "Error updating row $next_row_id9: " . $conn->error . "<br>";
                                                                            }
                                                                            
                                                                        } else {
                                                                            echo "Error preparing the update statement: " . $conn->error . "<br>";
                                                                        }
                                                                    } else {
                                                                        // SQL query to fetch Amount for $next_row_id9
                                                                        $sql_fetch_amount = "SELECT Amount FROM `$nit_no_detail` WHERE id = ?";
                                                                        $stmt_fetch = $conn->prepare($sql_fetch_amount);

                                                                        if ($stmt_fetch) {
                                                                            // Fetch Amount for $next_row_id9
                                                                            $stmt_fetch->bind_param('i', $next_row_id8);
                                                                            $stmt_fetch->execute();
                                                                            $stmt_fetch->bind_result($amount_value8);
                                                                            $stmt_fetch->fetch();

                                                                            // Close the statement and prepare for the next fetch
                                                                            $stmt_fetch->close();

                                                                            // Fetch Amount for $next_row_id10
                                                                            $stmt_fetch = $conn->prepare($sql_fetch_amount); // Reuse the same query
                                                                            $stmt_fetch->bind_param('i', $next_row_id9);
                                                                            $stmt_fetch->execute();
                                                                            $stmt_fetch->bind_result($amount_value9);
                                                                            $stmt_fetch->fetch();

                                                                            // Close the statement after fetching
                                                                            $stmt_fetch->close();

                                                                            // Perform the division
                                                                            if ($amount_value9 != 0) { // Ensure no division by zero
                                                                                $multiplication = $amount_value8 / $amount_value9;
                                                                                

                                                                            }
                                                                        }

                                                                        $totFinal = round($totf/$multiplication,2);

                                                                        $stmt_update->bind_param('di', $totFinal, $next_row_id9);
                                                                        if ($stmt_update->execute()) {
                                                                            //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                        } else {
                                                                            echo "Error updating row $next_row_id9: " . $conn->error . "<br>";
                                                                        }

                                                                        $stmt_update->bind_param('di', $totFinal, $next_row_id10);
                                                                        if ($stmt_update->execute()) {
                                                                            //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                        } else {
                                                                            echo "Error updating row $next_row_id10: " . $conn->error . "<br>";
                                                                        }


                                                                        $sql_update_nitno = "UPDATE `$nit_no` SET MRate = ? WHERE dsr_no = ?";
                                                                        $stmt_update_estimate = $conn->prepare($sql_update_nitno);
                                                                        $stmt_update_estimate->bind_param('ds', $totFinal, $dsr_no);
                                                                        $stmt_update_estimate->execute();
                                                                    }

                                                                    $amountQuery = "
                                                                        UPDATE login_register.`$nit_no`
                                                                        SET MAmount = quantity * CAST(MRate AS FLOAT)
                                                                        WHERE dsr_no = ? AND Rate IS NOT NULL AND Rate != 0;
                                                                    ";
                                    
                                                                    $amountStmt = $conn->prepare($amountQuery);
                                                                    $amountStmt->bind_param("s", $dsr_no);
                                                                    $amountStmt->execute();
                                                                    

                                                                } else {
                                                                    echo "Error preparing the check statement: " . $conn->error . "<br>";
                                                                }
                                                            


                                                                $stmt_update->close();
                                                            } else {
                                                                echo "Error preparing the statement: " . $conn->error . "<br>";
                                                            }
                                                            
                                                            $description1 = 'Add 1 % Water charges';
                                                            $description3 = 'Add GST (multiplying factor 0.2127)';
                                                            $description5 = 'Add 15% CPOH';
                                                            $description7 = 'Add Cess @ 1%';

                                                            if ($stmt_update1) {
                                                                
                                                                // Update the first next row with $WaterC
                                                                $stmt_update1->bind_param('si', $description1, $next_row_id1);
                                                                if ($stmt_update1->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id1: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update1->bind_param('si', $description3, $next_row_id3);
                                                                if ($stmt_update1->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id3: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update1->bind_param('si', $description5, $next_row_id5);
                                                                if ($stmt_update1->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id5: " . $conn->error . "<br>";
                                                                }

                                                                $stmt_update1->bind_param('si', $description7, $next_row_id7);
                                                                if ($stmt_update1->execute()) {
                                                                    //echo "Successfully updated row $next_row_id1 with additional amount ($WaterC).<br>";
                                                                } else {
                                                                    echo "Error updating row $next_row_id7: " . $conn->error . "<br>";
                                                                }
                                                            }

                                                        }
                                                    }

                                                // $stmt->close();
                                                }
                                            } else {
                                                echo "Error fetching data from `$nit_no`: " . $conn->error;
                                            }
                                        

                                            // Close the statement and connection
                                            $stmt->close();
                                            $conn->close();
                                        
                                            // Redirect back or show a success message
                                            header('Location: detailJustification.php?nit_no=' . urlencode($nit_no)); //Replace with your success page
                                            //header('Location: analysis1.php?nit_no=' . urlencode($nit_no));
                                            exit;
                                            ob_end_flush();
                                        }
                                    ?>
                                    
                                </form>
                             
                            
        </div>
</body>
</html>
