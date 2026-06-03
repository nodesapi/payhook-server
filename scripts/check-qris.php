<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$templates = DB::table('qris_templates')->get();

echo "=== QRIS Templates ===\n\n";

foreach ($templates as $template) {
    echo "ID: {$template->id}\n";
    echo "Name: {$template->name}\n";
    echo "Type: {$template->type}\n";
    echo "QRIS Length: " . strlen($template->qris_string) . " chars\n";
    echo "Account: {$template->account_name} ({$template->account_number})\n";
    echo "Active: " . ($template->is_active ? 'YES' : 'NO') . "\n";
    echo "\n---\n\n";
}

if ($templates->isEmpty()) {
    echo "No templates found!\n";
}
