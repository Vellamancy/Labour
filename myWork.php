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
        <li><a href="analysis.php">Justification</a></li>
        <li><a href="Billing.php">RA Bill</a></li>

        <li class="logout-item">
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </li>
    </ul>

    <div class="container">
        <br>
        <h1>My Works Detail <a href="work_detail.php" class="btn btn-warning">Add New Work</a> </h1>
        
    </div>

    <!-- Bootstrap Modal -->
    <!-- Bootstrap Modal -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Project Details</h2>
            <form id="editForm" action="myWork.php" method="POST">
                <label for="project_name">Project Name:</label>
                <textarea id="project_name" name="project_name" rows="10" required></textarea>

                <!-- Hidden NIT No. Field -->
                <input type="hidden" id="nit_no" name="nit_no">

                <button type="submit" class="btn btn-success">Update</button>
            </form>
        </div>
    </div>
    <style>
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            text-align: center;
        }
        textarea {
            width: 100%;
            resize: vertical;
            font-size: 16px;
            padding: 10px;
        }
        button {
            margin-top: 20px; /* Adds space above the Update button */
        }
    </style>



    <!-- Styles for Modal -->
    <style>
        .modal { position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); display: flex; justify-content: center; align-items: center; }
        .modal-content { background-color: white; padding: 20px; border-radius: 10px; width: 50%; text-align: center; }
        .close { position: absolute; right: 15px; font-size: 20px; cursor: pointer; }
    </style>


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
                    <button class='btn btn-primary edit-btn' 
                        data-nit='" . htmlspecialchars($item['NIT_No']) . "' 
                        data-name='" . htmlspecialchars($item['Project_Name']) . "'>
                        Edit Detail
                    </button>
                    </td>
                </tr>";
            }
            echo "</table>"; // End table
        }
        
        ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let modal = document.getElementById("editModal");
        let closeBtn = document.querySelector(".close");

        // Open modal when clicking Edit button
        document.querySelectorAll(".edit-btn").forEach(button => {
            button.addEventListener("click", function() {
                document.getElementById("project_name").value = this.dataset.name;
                document.getElementById("nit_no").value = this.dataset.nit;
                modal.style.display = "flex";
            });
        });

        // Close modal
        closeBtn.onclick = function() { modal.style.display = "none"; };
        window.onclick = function(event) { if (event.target == modal) modal.style.display = "none"; };

        // Auto-close alert after 5 seconds
        setTimeout(function() {
            let alertBox = document.getElementById("successAlert");
            if (alertBox) {
                alertBox.style.opacity = "0";
                setTimeout(() => { alertBox.style.display = "none"; }, 500); // Smooth fade-out
            }
        }, 5000);
    });
    </script>

    <div>
        <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $project_name = $_POST['project_name'];
                $nit_no = $_POST['nit_no'];

                if (!empty($project_name) && !empty($nit_no)) {
                    $updateQuery = "UPDATE `Project_Detail` SET `Project_Name` = ? WHERE `NIT_No` = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("ss", $project_name, $nit_no);
                    
                    if ($stmt->execute()) {
                        echo "<div id='successAlert' class='alert alert-success alert-dismissible fade show' role='alert' 
                                style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                                        padding: 20px 30px; border-radius: 10px; background-color: #d4edda; 
                                        color: #155724; font-size: 18px; font-weight: bold; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
                                        text-align: center;'>
                                ✅ Project details updated successfully!
                            </div>
                            <script>
                                setTimeout(() => window.location.href = 'myWork.php', 1500);
                            </script>";
                    } else {
                        echo "<div class='alert alert-danger' 
                                style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                                        padding: 20px 30px; border-radius: 10px; background-color: #f8d7da; 
                                        color: #721c24; font-size: 18px; font-weight: bold; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
                                        text-align: center;'>
                                ❌ Error: " . $conn->error . "
                            </div>";
                    }
                }
            }
        ?>
    </div>



</body>
</html>
