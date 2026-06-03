<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$channels = \App\Models\PaymentChannel::all();
foreach ($channels as $c) {
    echo $c->id . ' = ' . $c->qr_code_path . "\n";
}
