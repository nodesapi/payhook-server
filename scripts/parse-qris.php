<?php

$qris = '00020101021240540011ID.DANA.WWW01189360091531469500590213WAHYU SUHANDI5204482953033605405130005802ID5913WAHYU SUHANDI6015Kota Tangerang 61051279062460804DMCT993400020001242810120120251014594827996304BC96';

echo "=== Parse QRIS DANA Field by Field ===\n\n";

$offset = 0;
$length = strlen($qris);

while ($offset < $length - 4) {
    $tag = substr($qris, $offset, 2);
    $len = substr($qris, $offset + 2, 2);
    $lenInt = intval($len);
    
    if ($offset + 4 + $lenInt > $length) {
        break;
    }
    
    $value = substr($qris, $offset + 4, $lenInt);
    
    $description = '';
    switch($tag) {
        case '00': $description = 'Payload Format Indicator'; break;
        case '01': $description = 'Point of Initiation Method'; break;
        case '40': $description = 'Merchant Account Information'; break;
        case '52': $description = 'Merchant Category Code'; break;
        case '53': $description = 'Transaction Currency'; break;
        case '54': $description = 'Transaction Amount'; break;
        case '58': $description = 'Country Code'; break;
        case '59': $description = 'Merchant Name'; break;
        case '60': $description = 'Merchant City'; break;
        case '61': $description = 'Postal Code'; break;
        case '62': $description = 'Additional Data'; break;
        case '63': $description = 'CRC'; break;
        default: $description = 'Unknown'; break;
    }
    
    echo "Tag {$tag} ({$description}): Length={$len}, Value={$value}\n";
    
    $offset += 4 + $lenInt;
}

echo "\n=== Field 54 Analysis ===\n";
if (preg_match('/54(\d{2})([^5]+)/', $qris, $match)) {
    echo "Found field 54: 54{$match[1]}{$match[2]}\n";
    echo "Length: {$match[1]}\n";
    echo "Value: {$match[2]}\n";
}
