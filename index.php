<?php
// require 'vendor/autoload.php';

// use Dompdf\Dompdf;
// use Dompdf\Options;

// // 1. Database Connection
// $host = 'db';
// $db   = 'employees';
// $user = 'root';
// $pass = 'root_password';

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
// } catch (PDOException $e) {
//     die("Connection failed: " . $e->getMessage());
// }

// // 2. Settings
// $dept_name = 'Development'; // Change this to the target department
// $output_dir = "payslips_" . strtolower($dept_name) . "_" . date('Y-m');

// if (!is_dir($output_dir)) {
//     mkdir($output_dir, 0777, true);
// }

// // 3. Query all current employees in the specific department
// $query = "SELECT e.emp_no, e.first_name, e.last_name, s.salary, d.dept_name 
//           FROM employees e
//           JOIN dept_emp de ON e.emp_no = de.emp_no
//           JOIN departments d ON de.dept_no = d.dept_no
//           JOIN salaries s ON e.emp_no = s.emp_no
//           WHERE d.dept_name = :dept_name 
//           AND de.to_date = '9999-01-01' 
//           AND s.to_date = '9999-01-01'"; // Limit added for safety during testing

// $stmt = $pdo->prepare($query);
// $stmt->execute(['dept_name' => $dept_name]);

// // 4. Loop and Generate
// $options = new Options();
// $options->set('isHtml5ParserEnabled', true);
// $dompdf = new Dompdf($options);

// while ($employee = $stmt->fetch(PDO::FETCH_ASSOC)) {
//     # code...
//     $html = "
//     <style>
//         body { font-family: DejaVu Sans, sans-serif; }
//         .box { border: 2px solid #333; padding: 20px; width: 500px; margin: auto; }
//         .header { text-align: center; background: #f4f4f4; padding: 10px; }
//         .data-row { margin: 10px 0; border-bottom: 1px dotted #ccc; }
//         .bold { font-weight: bold; }
//     </style>
//     <div class='box'>
//         <div class='header'>
//             <h1>COMPANY NAME</h1>
//             <p>Official Payslip - " . date('M Y') . "</p>
//         </div>
//         <div class='data-row'><span class='bold'>Department:</span> {$employee['dept_name']}</div>
//         <div class='data-row'><span class='bold'>Employee:</span> {$employee['first_name']} {$employee['last_name']} ({$employee['emp_no']})</div>
//         <div class='data-row'><span class='bold'>Gross Salary:</span> $" . number_format($employee['salary'], 2) . "</div>
//         <div class='data-row' style='margin-top:20px; font-size: 1.2em;'>
//             <span class='bold'>Net Pay:</span> $" . number_format($employee['salary'], 2) . "
//         </div>
//     </div>";

//     $dompdf->loadHtml($html);
//     $dompdf->setPaper('A4', 'portrait');
//     $dompdf->render();

//     // Save to file instead of streaming
//     $filename = $output_dir . "/payslip_" . $employee['emp_no'] . ".pdf";
//     file_put_contents($filename, $dompdf->output());
    
//     // Clear Dompdf for the next iteration to save memory
//     $dompdf = new Dompdf($options); 
//     echo "Generated: " . $filename . "<br>";
//     flush();
// }

// echo "Done! Check the folder: " . $output_dir;