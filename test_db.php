<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Database Connection...\n";
    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Connection Status: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
    
    $tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
    echo "Table Count: " . count($tables) . "\n";
    echo "Tables:\n";
    foreach ($tables as $table) {
        foreach ($table as $key => $value) {
            echo "- $value\n";
        }
    }
    echo "Database check passed successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
