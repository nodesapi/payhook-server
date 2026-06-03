<?php
require 'vendor/autoload.php';

$files = scandir('storage/app/public/qr-codes');
foreach ($files as $f) {
    if (pathinfo($f, PATHINFO_EXTENSION) == 'jpg' || pathinfo($f, PATHINFO_EXTENSION) == 'png' || pathinfo($f, PATHINFO_EXTENSION) == 'jpeg') {
        $qrcode = new \Zxing\QrReader('storage/app/public/qr-codes/' . $f);
        echo $f . " = " . $qrcode->text() . "\n";
    }
}
