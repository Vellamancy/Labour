<?php
session_start(); // Start the session to manage session variables
require 'vendor/autoload.php'; // Include PhpSpreadsheet library
include("./database.php"); // Include database connection

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ensure the user is authenticated
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

// Fetch NIT No. from URL
$nit_no = isset($_GET['nit_no']) ? $_GET['nit_no'] : '';
if (!$nit_no) {
    die("Invalid NIT No.");
}

$nit_no_table = "{$nit_no}"; // Replace this with your table name logic

// Fetch data from the database
$sql = "SELECT * FROM `$nit_no_table`";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add a title for the document
$sheet->setCellValue('A1', 'Market Rate Abstract');
$sheet->mergeCells('A1:I1'); // Merge across all columns
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16); // Bold and large font
$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center horizontally
$sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Center vertically

// Set header row starting from row 2 (since row 1 is for the title)
$sheet->setCellValue('A2', 'Sr. No.');
$sheet->setCellValue('B2', 'DSR Code');
$sheet->setCellValue('C2', 'Description of Item');
$sheet->setCellValue('D2', 'Quantity');
$sheet->setCellValue('E2', 'Unit');
$sheet->setCellValue('F2', 'DSR Rate');
$sheet->setCellValue('G2', 'DSR Amount');
$sheet->setCellValue('H2', 'Market Rate');
$sheet->setCellValue('I2', 'Market Amount');

// Make the "Description of Item" column wider and wrap text
$sheet->getColumnDimension('C')->setWidth(60); // Set the width of the "Description of Item" column (you can adjust the width as needed)
$sheet->getStyle('C')->getAlignment()->setWrapText(true); // Enable text wrapping in the "Description of Item" column

// Make A2 to I2 bold
$sheet->getStyle('A2:I2')->getFont()->setBold(true); // Make A2 to I2 bold

// Add data rows
$rowIndex = 3; // Start from the third row
$serialNumber = 1; // Initialize the serial number

while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowIndex, $serialNumber++); // Use the serial number
    $sheet->setCellValue('B' . $rowIndex, $row['dsr_no']);
    $sheet->setCellValue('C' . $rowIndex, $row['Description_of_Item']);
    $sheet->setCellValue('D' . $rowIndex, $row['quantity']);
    $sheet->setCellValue('E' . $rowIndex, $row['Unit']);
    $sheet->setCellValue('F' . $rowIndex, $row['Rate']);
    $sheet->setCellValue('G' . $rowIndex, $row['Amount']);
    $sheet->setCellValue('H' . $rowIndex, $row['MRate']);
    $sheet->setCellValue('I' . $rowIndex, $row['MAmount']);
    $rowIndex++;
}

// Set headers for download
$filename = "Justified Abstract_NIT_$nit_no.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Write to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
