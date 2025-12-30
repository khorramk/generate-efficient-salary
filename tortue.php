<?php
// Prevent the browser/Apache from timing out too early for the test
set_time_limit(0); 

echo "Creating 100,000 records in memory...<br>";
// flush();

// 1. Create a large array to eat some initial RAM
$data = [];
for ($i = 0; $i < 100000; $i++) {
    $data[] = [
        'id' => $i,
        'name' => "Practitioner_" . $i,
        'payload' => str_repeat("DUMMY_DATA_", 10) // Adding weight to each row
    ];
}

$initialMem = round(memory_get_usage() / 1024 / 1024, 2);
echo "Array created. Memory used: " . $initialMem . " MB. Starting loop...<br>";
error_log("--- Test Started: Memory at {$initialMem}MB ---");
// flush();

// 2. The Infinite Loop (or 100k loop)
foreach ($data as $row) {
    // Simulate heavy work (MD5 hashing 1000 times is CPU intensive)
    for ($j = 0; $j < 1000; $j++) {
        $task = md5($row['name'] . $j);
    }

    // Every 1000 records, output status
    if ($row['id'] % 1000 === 0) {
        $currentMem = round(memory_get_usage() / 1024 / 1024, 2);
        echo "Processed " . $row['id'] . " rows... (RAM: {$currentMem}MB)<br>";
        error_log("Progress: " . $row['id'] . " | RAM: {$currentMem}MB");
        
        // The Heartbeat - send to browser to prevent 504
        echo " "; 
        // if (ob_get_level() > 0) ob_flush();
        // flush();
    }
}

echo "Finished!";