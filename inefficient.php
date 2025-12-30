<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
// 1. Fetch ALL departments (The "1")
$host = 'db';
$db   = 'employees';
$user = 'root';
$pass = 'root_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
$deptStmt = $pdo->query("SELECT dept_no, dept_name FROM departments");
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

$finalReport = [];
// 2. Settings
$dept_name = 'Development'; // Change this to the target department
$output_dir = "payslips_" . strtolower($dept_name) . "_" . date('Y-m');

if (!is_dir($output_dir)) {
    mkdir($output_dir, 0777, true);
}
// 4. Loop and Generate
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

foreach ($departments as $dept) {
    // 2. For EACH department, fetch its employee links (The "N")
    echo "Processing Department: " . $dept['dept_name'] . "<br>";
    flush();
    $deStmt = $pdo->prepare("SELECT emp_no FROM dept_emp WHERE dept_no = :dept_no AND to_date = '9999-01-01'");
    $deStmt->execute(['dept_no' => $dept['dept_no']]);
    // $empLinks = $deStmt->fetchAll(PDO::FETCH_ASSOC);

    while ($empLinks = $deStmt->fetch(PDO::FETCH_ASSOC)) {
        $emp_no = $empLinks['emp_no'];

        // 3. For EACH employee link, fetch personal details (The "N x M")
        $eStmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE emp_no = :emp_no");
        $eStmt->execute(['emp_no' => $emp_no]);
        $details = $eStmt->fetch(PDO::FETCH_ASSOC);

        // 4. For EACH employee, fetch their salary (The "N x M" again)
        $sStmt = $pdo->prepare("SELECT salary FROM salaries WHERE emp_no = :emp_no AND to_date = '9999-01-01'");
        $sStmt->execute(['emp_no' => $emp_no]);
        $salary = $sStmt->fetch(PDO::FETCH_ASSOC);

        // Manually stitching data together in PHP
        $finalReport[] = [
            'dept_name'  => $dept['dept_name'],
            'first_name' => $details['first_name'],
            'last_name'  => $details['last_name'],
            'salary'     => $salary['salary'] ?? 0
        ];

        $html = "
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .box { border: 2px solid #333; padding: 20px; width: 500px; margin: auto; }
        .header { text-align: center; background: #f4f4f4; padding: 10px; }
        .data-row { margin: 10px 0; border-bottom: 1px dotted #ccc; }
        .bold { font-weight: bold; }
    </style>
    <div class='box'>
        <div class='header'>
            <h1>COMPANY NAME</h1>
            <p>Official Payslip - " . date('M Y') . "</p>
        </div>
        <div class='data-row'><span class='bold'>Department:</span> {$dept['dept_name']}</div>
        <div class='data-row'><span class='bold'>Employee:</span> {$details['first_name']} {$details['last_name']} ({$emp_no})</div>
        <div class='data-row'><span class='bold'>Gross Salary:</span> $" . number_format($salary['salary'], 2) . "</div>
        <div class='data-row' style='margin-top:20px; font-size: 1.2em;'>
            <span class='bold'>Net Pay:</span> $" . number_format($salary['salary'], 2) . "
        </div>
    </div>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Save to file instead of streaming
    $filename = $output_dir . "/payslip_" . $emp_no  . ".pdf";
    file_put_contents($filename, $dompdf->output());
    
    // Clear Dompdf for the next iteration to save memory
    $dompdf = new Dompdf($options); 
    echo "Generated: " . $filename . "<br>";
    flush();
    }
}