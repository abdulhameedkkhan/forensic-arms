<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the arm dealer relationship
try {
    $count = \App\Models\ArmDealer::count();
    echo "Total Arm Dealers: " . $count . "\n";
    
    if ($count > 0) {
        $dealers = \App\Models\ArmDealer::limit(5)->get();
        echo "Sample Arm Dealers:\n";
        foreach ($dealers as $dealer) {
            echo "- {$dealer->shop_name} ({$dealer->name})\n";
        }
    } else {
        echo "No arm dealers found in database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}