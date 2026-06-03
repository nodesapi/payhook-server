<?php

namespace App\Services;

class QrisService
{
    /**
     * Inject nominal into QRIS string
     * Method: Insert field 54 (amount) between field 53 (currency) and field 58 (country)
     */
    public function injectAmount(string $qrisString, float $amount): string
    {
        try {
            // Format amount as integer string
            $amountStr = (string)intval($amount);
            $amountLength = str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT);
            $newAmountField = '54' . $amountLength . $amountStr;

            // Find field 53 (currency) position
            $pos = strpos($qrisString, '5303');
            if ($pos === false) {
                throw new \Exception('Currency field (53) not found');
            }

            // Read field 53 length and find its end position
            $f53Length = intval(substr($qrisString, $pos + 2, 2));
            $f53End = $pos + 4 + $f53Length;

            // Check if field 54 already exists
            if (substr($qrisString, $f53End, 2) === '54') {
                // Field 54 exists - replace it
                $f54Length = intval(substr($qrisString, $f53End + 2, 2));
                $f54End = $f53End + 4 + $f54Length;
                
                $before = substr($qrisString, 0, $f53End);
                $after = substr($qrisString, $f54End);
            } else {
                // Field 54 doesn't exist - insert it before field 58 (country code)
                $before = substr($qrisString, 0, $f53End);
                $after = substr($qrisString, $f53End);
            }

            // Generate random transaction reference in field 62
            $after = preg_replace_callback('/(\d{2}10)14\d{11}/', function($matches) {
                $randomRef = mt_rand(10000000000, 99999999999);
                return $matches[1] . '14' . $randomRef;
            }, $after);

            // Reconstruct QRIS
            $qrWithAmount = $before . $newAmountField . $after;

            // Remove CRC and recalculate
            $qrWithoutCrc = substr($qrWithAmount, 0, -4);
            $newChecksum = $this->calculateCRC16($qrWithoutCrc);

            return $qrWithoutCrc . $newChecksum;

        } catch (\Exception $e) {
            throw new \Exception('Amount injection failed: ' . $e->getMessage());
        }
    }

    /**
     * Calculate CRC-16-CCITT for QRIS
     */
    protected function calculateCRC16(string $data): string
    {
        $crc = 0xFFFF;
        $length = strlen($data);

        for ($i = 0; $i < $length; $i++) {
            $crc ^= ord($data[$i]) << 8;
            
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
                $crc &= 0xFFFF; // Keep it 16-bit
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Generate QR code image from QRIS string
     */
    public function generateQRCode(string $qrisString): string
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        
        $writer = new \BaconQrCode\Writer($renderer);
        $svg = $writer->writeString($qrisString);

        return $svg;
    }
}
