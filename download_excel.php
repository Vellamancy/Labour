<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet via Composer
include("./database.php"); // Database connection

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['nit_no']) && !empty($_GET['nit_no'])) {
    $nit_no = $_GET['nit_no'];

    // Fetch data from the database
    $fetchQuery = "SELECT * FROM `$nit_no`";
    $result = $conn->query($fetchQuery);

    if ($result && $result->num_rows > 0) {
        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add table headers
        $headers = ['Sr. No.', 'DSR Code', 'Description', 'Quantity', 'Unit', 'Rate', 'Amount'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Add data rows
        $rowNumber = 2;
        $serial = 1;
        while ($row = $result->fetch_assoc()) {
            $sheet->setCellValue('A' . $rowNumber, $serial++);
            $sheet->setCellValue('B' . $rowNumber, $row['dsr_no']);
            $sheet->setCellValue('C' . $rowNumber, $row['Description_of_Item']);
            $sheet->setCellValue('D' . $rowNumber, $row['quantity']);
            $sheet->setCellValue('E' . $rowNumber, $row['Unit']);
            $sheet->setCellValue('F' . $rowNumber, $row['Rate']);
            $sheet->setCellValue('G' . $rowNumber, $row['Amount']);
            $rowNumber++;
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="estimate_data.xlsx"');
        header('Cache-Control: max-age=0');

        // Write the spreadsheet to output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    } else {
        echo "No data found for NIT No: $nit_no";
    }
} else {
    echo "Invalid NIT No.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Welcome <?= htmlspecialchars($user_id); ?></h1>
        <a href="logout.php" class="btn btn-warning">Logout</a>
        <br><br>

        <div class="table-container">
            <h1>Estimate Details</h1>
            <a href="download_excel.php?nit_no=<?= urlencode($nit_no); ?>" class="btn btn-success">Download Excel</a>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
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
                    <?php if (!empty($estimateData)) : ?>
                        <?php $serial = 1; ?>
                        <?php foreach ($estimateData as $row) : ?>
                            <tr>
                                <td><?= $serial++; ?></td>
                                <td><?= htmlspecialchars($row['dsr_no']); ?></td>
                                <td><?= htmlspecialchars($row['Description_of_Item']); ?></td>
                                <td><?= htmlspecialchars($row['quantity'] ?? ''); ?></td>
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
    </div>
</body>
</html>
