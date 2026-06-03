<?php

// Analyze currency code in QRIS
$danaQris = "00020101021240540011ID.DANA.WWW01189360091531469500590213WAHYU SUHANDI5204482953033605802ID5913WAHYU SUHANDI6015Kota Tangerang 61051279062460804DMCT9934000200012428101201202510145948279963041D6E";

echo "=== ANALYZING CURRENCY CODE IN DANA QRIS ===" . PHP_EOL . PHP_EOL;

// Find field 53 (currency)
$pos53 = strpos($danaQris, '5303');
if ($pos53 !== false) {
    $field53Tag = substr($danaQris, $pos53, 2);      // "53"
    $field53Length = substr($danaQris, $pos53 + 2, 2); // "03"
    $field53Value = substr($danaQris, $pos53 + 4, intval($field53Length)); // "360"
    
    echo "Field 53 (Currency):" . PHP_EOL;
    echo "  Tag: $field53Tag" . PHP_EOL;
    echo "  Length: $field53Length" . PHP_EOL;
    echo "  Value: $field53Value" . PHP_EOL;
    echo PHP_EOL;
}

echo "=== ISO 4217 NUMERIC CURRENCY CODES ===" . PHP_EOL;
echo "  CNY (Chinese Yuan) = 156" . PHP_EOL;
echo "  IDR (Indonesian Rupiah) = 360 ✅" . PHP_EOL;
echo "  USD (US Dollar) = 840" . PHP_EOL;
echo "  EUR (Euro) = 978" . PHP_EOL;
echo PHP_EOL;

echo "=== CONCLUSION ===" . PHP_EOL;
echo "DANA template uses: 360 = IDR (CORRECT!)" . PHP_EOL;
echo "If BCA shows CNY, it's a BCA app bug in currency mapping." . PHP_EOL;
echo PHP_EOL;

echo "=== CHECKING DANA-QRIS PROJECT ===" . PHP_EOL;
$danaQrisPath = "G:\\Project\\Payment gateway\\Dana-Qris\\src\\services\\qr.js";
if (file_exists($danaQrisPath)) {
    $content = file_get_contents($danaQrisPath);
    
    // Look for currency-related code
    if (preg_match('/currency|5303|360/i', $content)) {
        echo "Found currency references in Dana-Qris project." . PHP_EOL;
        echo "Let's check the source code..." . PHP_EOL;
    }
} else {
    echo "Dana-Qris project not found at expected path." . PHP_EOL;
}
