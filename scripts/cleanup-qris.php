<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Delete empty templates
$deleted = DB::table('qris_templates')->where('qris_string', '')->delete();
$deleted += DB::table('qris_templates')->whereNull('qris_string')->delete();

echo "Deleted {$deleted} empty templates\n";

// Show remaining templates
$templates = DB::table('qris_templates')->get();

echo "\nRemaining templates:\n";
foreach ($templates as $t) {
    echo "- ID {$t->id}: {$t->name} (QRIS: " . strlen($t->qris_string) . " chars)\n";
}
