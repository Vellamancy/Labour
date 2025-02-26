<?php
// Start the session and include necessary files
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
    die("NIT number is required to export market rates.");
}

// Table name based on NIT number
$nit_no_MR = "{$nit_no}_MR";

// Include PHPSpreadsheet library
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add "Market Rates" title above the table
$sheet->setCellValue('A1', 'Market Rates');
$sheet->mergeCells('A1:F1'); // Merge cells for better visibility
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16); // Bold and large font
$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center horizontally
$sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Center vertically

// Set the headers for the market rates table
$headers = ['Sl. No.', 'Code No', 'Description of Item', 'Unit', 'Basic Rate', 'Market Rate'];
$sheet->fromArray($headers, NULL, 'A2');

// Fetch data for market rates
$sql_market_rates = "SELECT * FROM `$nit_no_MR`";
$result_market_rates = $conn->query($sql_market_rates);

if ($result_market_rates->num_rows > 0) {
    $rowIndex = 3;

    while ($row = $result_market_rates->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['id']);
        $sheet->setCellValue('B' . $rowIndex, $row['code_no']);
        $sheet->setCellValue('C' . $rowIndex, $row['item_description']);
        $sheet->setCellValue('D' . $rowIndex, $row['unit']);
        $sheet->setCellValue('E' . $rowIndex, $row['basic_rate']);
        $sheet->setCellValue('F' . $rowIndex, $row['Market_rate']);
        $rowIndex++;
    }
} else {
    $sheet->setCellValue('A3', 'No data found for market rates');
}


$sheet->getColumnDimension('C')->setWidth(60); // Set the width of the "Description of Item" column (you can adjust the width as needed)
$sheet->getStyle('C')->getAlignment()->setWrapText(true); // Enable text wrapping in the "Description of Item" column

// Make A2 to I2 bold
$sheet->getStyle('A2:F2')->getFont()->setBold(true); // Make A2 to I2 bold

// Generate the Excel file
$filename = "market_rates_export_NIT_$nit_no.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Close the database connection
$conn->close();
exit();
?>
