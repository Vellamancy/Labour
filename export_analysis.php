<?php
// Include necessary files and start the session
session_start();
include("./database.php");

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get NIT number from GET request
$nit_no = isset($_GET['nit_no']) ? $_GET['nit_no'] : '';

if (empty($nit_no)) {
    die("NIT number is required to export analysis.");
}

// Table names based on NIT number
$nit_no_detail = "{$nit_no}_detail";
$nit_no_detailSub = "{$nit_no}_detailSub";

// Include PHPSpreadsheet library
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add "Analysis" title above the table
$sheet->setCellValue('A1', 'Analysis');
$sheet->mergeCells('A1:F1'); // Merge cells for better visibility
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16); // Bold and large font
$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center horizontally
$sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Center vertically

// Set the headers for the main analysis table
$headers = ['DSR No', 'Description of Item', 'Quantity', 'Unit', 'Market Rate', 'Amount'];
$sheet->fromArray($headers, NULL, 'A2');

$sheet->getColumnDimension('B')->setWidth(60); // Set the width of the "Description of Item" column (you can adjust the width as needed)
$sheet->getStyle('B')->getAlignment()->setWrapText(true); // Enable text wrapping in the "Description of Item" column

// Make A2 to I2 bold
$sheet->getStyle('A2:F2')->getFont()->setBold(true); // Make A2 to I2 bold

// Fetch data for the main analysis
$sql_main_analysis = "SELECT * FROM `$nit_no_detail`";
$result_main_analysis = $conn->query($sql_main_analysis);

if ($result_main_analysis->num_rows > 0) {
    $rowIndex = 3;

    while ($row = $result_main_analysis->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['dsr_no']);
        $sheet->setCellValue('B' . $rowIndex, $row['Description_of_Item']);
        $sheet->setCellValue('C' . $rowIndex, $row['quantity']);
        $sheet->setCellValue('D' . $rowIndex, $row['Unit']);
        $sheet->setCellValue('E' . $rowIndex, $row['MRate']);
        $sheet->setCellValue('F' . $rowIndex, $row['MAmount']);
        $rowIndex += 1;
    }
} else {
    $sheet->setCellValue('A3', 'No data found for main analysis');
}
/*
// Add spacing before Sub-Analysis
$rowIndex += 2;  // Adding some space (2 rows) between the two sections

// Add "Sub-Analysis" title above the table
$sheet->setCellValue('A' . $rowIndex, 'Sub-Analysis');
$sheet->mergeCells('A' . $rowIndex . ':F' . $rowIndex); // Merge cells for better visibility
$sheet->getStyle('A' . $rowIndex)->getFont()->setBold(true)->setSize(14); // Bold and large font
$sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center horizontally
$sheet->getStyle('A' . $rowIndex)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Center vertically


$sheet->getColumnDimension('B')->setWidth(60); // Set the width of the "Description of Item" column (you can adjust the width as needed)
$sheet->getStyle('B')->getAlignment()->setWrapText(true); // Enable text wrapping in the "Description of Item" column

// Make A2 to I2 bold
$sheet->getStyle('A2:F2')->getFont()->setBold(true); // Make A2 to I2 bold

// Set the headers for the sub-analysis table
$headersSub = ['DSR No', 'Description of Item', 'Quantity', 'Unit', 'Market Rate', 'Amount'];
$rowIndex++;  // Move to the next row for the headers
$sheet->fromArray($headersSub, NULL, 'A' . $rowIndex);

// Fetch data for sub-analysis
$sql_sub_analysis = "SELECT * FROM `$nit_no_detailSub`";
$result_sub_analysis = $conn->query($sql_sub_analysis);

if ($result_sub_analysis->num_rows > 0) {
    $rowIndex += 1;  // Skip the header row and start with data

    while ($row = $result_sub_analysis->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['dsr_no']);
        $sheet->setCellValue('B' . $rowIndex, $row['Description_of_Item']);
        $sheet->setCellValue('C' . $rowIndex, $row['quantity']);
        $sheet->setCellValue('D' . $rowIndex, $row['Unit']);
        $sheet->setCellValue('E' . $rowIndex, $row['MRate']);
        $sheet->setCellValue('F' . $rowIndex, $row['MAmount']);
        $rowIndex += 1;
    }
} else {
    $sheet->setCellValue('A' . $rowIndex, 'No data found for sub-analysis');
}
*/
// Generate the Excel file
$filename = "analysis_export_NIT_$nit_no.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Close the database connection
$conn->close();
exit();
?>
