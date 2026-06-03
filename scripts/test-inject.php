<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$qrisService = new \App\Services\QrisService();

// Get DANA template
$template = DB::table('qris_templates')->where('id', 2)->first();

echo "=== Test QRIS Generation ===\n\n";

echo "Original QRIS:\n{$template->qris_string}\n\n";

// Test inject amount 10000
try {
    $modified = $qrisService->injectAmount($template->qris_string, 10000);
    
    echo "Modified QRIS (10000):\n{$modified}\n\n";
    
    // Check differences
    echo "Original length: " . strlen($template->qris_string) . "\n";
    echo "Modified length: " . strlen($modified) . "\n\n";
    
    // Extract amount field
    if (preg_match('/54(\d{2})(\d+)/', $template->qris_string, $origMatch)) {
        echo "Original amount field: 54{$origMatch[1]}{$origMatch[2]} (amount: {$origMatch[2]})\n";
    }
    
    if (preg_match('/54(\d{2})(\d+)/', $modified, $modMatch)) {
        echo "Modified amount field: 54{$modMatch[1]}{$modMatch[2]} (amount: {$modMatch[2]})\n";
    }
    
    // Check CRC
    $origCRC = substr($template->qris_string, -4);
    $modCRC = substr($modified, -4);
    
    echo "\nOriginal CRC: {$origCRC}\n";
    echo "Modified CRC: {$modCRC}\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
