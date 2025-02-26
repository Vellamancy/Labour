<?php
session_start();
include("./database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();

}
if (isset($_SESSION['nit_no_ok'])) {
    //echo "Stored NIT No.: " . $_SESSION['nit_no_ok'];
}

$user_id = $_SESSION['user'];
//$nit_no = isset($_GET['nit_no']) ? $_GET['nit_no'] : '';
$nit_no = isset($_GET['nit_no']) ? $_GET['nit_no'] : '';


//echo "$nit_no";
$nit_no = "{$nit_no}";
$nit_no_detail = "{$nit_no}_detail";

$nit_no_MR = "{$nit_no}_MR";

// Fetch data from nit_no_detailSub table
$sql_nit_no = "SELECT * FROM `$nit_no`";
$result_nit_no = $conn->query($sql_nit_no);


// Fetch data from nit_no_detail table
$sql_detail = "SELECT * FROM `$nit_no_detail`";
$result_detail = $conn->query($sql_detail);

// Fetch data from nit_no_MR table
$sql_MR = "SELECT * FROM `$nit_no_MR`";
$result_MR = $conn->query($sql_MR);

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
    /* Default button style */
    .toggle-button {
        font-size: 16px;
        transition: all 0.9s ease;
        padding: 10px 20px;
    }

    /* Enlarged button */
    .toggle-button.enlarged {
        font-size: 30px;
        padding: 15px 25px;
    }

    /* Shrunk button */
    .toggle-button.shrunk {
        font-size: 14px;
        padding: 8px 15px;
        opacity: 0.5;
    }


    
    th {
        text-align: center; /* Center horizontally */
        vertical-align: middle; /* Center vertically */
    }
    td {
        text-align: justify; /* Justify text in table data cells */
    }
    
    </style>

    <script>
        function toggleAnalysis() {
            setActiveButton('toggle-analysis-button');
            document.getElementById('analysis-section').style.display = 'block';
            document.getElementById('abstract-section').style.display = 'none';
            document.getElementById('market-rates-section').style.display = 'none';
        }

        function toggleAbstract() {
            setActiveButton('toggle-abstract-button');
            document.getElementById('abstract-section').style.display = 'block';
            document.getElementById('analysis-section').style.display = 'none';
            document.getElementById('market-rates-section').style.display = 'none';
        }

        function toggleMarketRates() {
            setActiveButton('toggle-market-button');
            document.getElementById('market-rates-section').style.display = 'block';
            document.getElementById('abstract-section').style.display = 'none';
            document.getElementById('analysis-section').style.display = 'none';
        }

        function setActiveButton(activeButtonId) {
            const buttons = document.querySelectorAll('.toggle-button');
            buttons.forEach(button => {
                if (button.id === activeButtonId) {
                    button.classList.add('enlarged');
                    button.classList.remove('shrunk');
                } else {
                    button.classList.add('shrunk');
                    button.classList.remove('enlarged');
                }
            });
        }
    </script>

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



    
    <?php
    echo "<div style=' text-align: center; font-size: 24px; margin-top: 20px;'> NIT No.- $nit_no </div>";
    ?>

<div class="container mt-5">
    <div class="d-flex justify-content-center">
        <button id="toggle-abstract-button" class="btn btn-success toggle-button" style="margin-right: 20px;" onclick="toggleAbstract()">Show the Abstract</button>
        <button id="toggle-analysis-button" class="btn btn-primary toggle-button" style="margin-right: 20px;" onclick="toggleAnalysis()">Show Detail Analysis</button>
        <button id="toggle-market-button" class="btn btn-info toggle-button" onclick="toggleMarketRates()">Show Market Rates</button>
    </div>
</div>


    <!-- New Download Buttons -->
    <div class="d-flex justify-content-center mt-3">
        <a href="export_abstract.php?nit_no=<?= urlencode($nit_no); ?>" class="btn btn-success" style="margin-right: 20px;">Download the Abstract in Excel</a>
        <a href="export_analysis.php?nit_no=<?= urlencode($nit_no); ?>" class="btn btn-primary" style="margin-right: 20px;">Download Detail Analysis in Excel</a>
        <a href="export_market_rate.php?nit_no=<?= urlencode($nit_no); ?>" class="btn btn-info">Download Market Rates in Excel</a>
    </div>

</div>




<!-- Abstract Section -->
<div id="abstract-section" style="display: none;" class="container mt-5">
    <h2 class="text-center">Abstract</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
            <th style="width: 3%;">Sr. No.</th>
            <th style="width: 6%;">DSR Code</th>
            <th style="width: 40%;">Description of Item</th>
            <th style="width: 5%;">Quantity</th>
            <th style="width: 5%;">Unit</th>
            <th style="width: 10%;">DSR Rate</th>
            <th style="width: 10%;">DSR Amount</th>
            <th style="width: 10%;">Market Rate</th>
            <th style="width: 15%;">Market Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Assuming $result_nit_no contains the NIT No table data
            if ($result_nit_no->num_rows > 0) {
                $serial_number = 1; // Initialize serial number
                while ($row = $result_nit_no->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $serial_number++ . "</td>"; // Use serial number and increment
                    echo "<td>" . htmlspecialchars($row['dsr_no']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Description_of_Item']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Unit']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Rate']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Amount']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['MRate']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['MAmount']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>"; // Adjust colspan to match table columns
            }
            
            ?>
        </tbody>
    </table>
</div>

<!-- Analysis Section -->
<div id="analysis-section" style="display: none;">

    <div class="container mt-5">
        <h2 class="text-center">Analysis</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 5%;">DSR No</th>
                    <th style="width: 60%;">Description of Item</th>
                    <th style="width: 5%;">Quantity</th>
                    <th style="width: 5%;">Unit</th>
                    <th style="width: 10%;">Market Rate</th>
                    <th style="width: 10%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_detail->num_rows > 0) {
                    while ($row = $result_detail->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['dsr_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Description_of_Item']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Unit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['MRate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['MAmount']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    

</div>

<!-- Market Rates Section -->
<div id="market-rates-section" style="display: none;" class="container mt-5">
    <h2 class="text-center">Market Rates</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Code No</th>
                <th>Description of Item</th>
                <th>Unit</th>
                <th>Basic Rate</th>
                <th>Market Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_MR->num_rows > 0) {
                while ($row = $result_MR->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['code_no']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['item_description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['basic_rate']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Market_rate']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

