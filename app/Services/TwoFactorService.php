<?php

namespace App\Services;

class TwoFactorService
{
    private static $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random secret key.
     */
    public static function generateSecretKey($length = 16)
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::$base32chars[rand(0, 31)];
        }
        return $secret;
    }

    /**
     * Get the TOTP code for a secret at a specific time.
     */
    public static function getCode($secret, $timeSlice = null)
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }

        $secretkey = self::base32Decode($secret);

        // Pack time slice into binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N', $timeSlice);

        // Hash it using HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $secretkey, true);

        // Dynamic truncation
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashpart = substr($hmac, $offset, 4);

        // Unpack to get the integer code
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;

        $modulo = pow(10, 6);
        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify the entered code against the secret.
     */
    public static function verifyCode($secret, $code, $discrepancy = 1)
    {
        $currentTimeSlice = floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = self::getCode($secret, $currentTimeSlice + $i);
            if ($calculatedCode === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate QR code URL for Google Authenticator / Microsoft Authenticator.
     */
    public static function getQrCodeUrl($label, $secret, $issuer = 'Cekbayar')
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $label) . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer);
    }

    /**
     * Generate QR code SVG from otpauth URL.
     */
    public static function generateQrCodeSvg($qrCodeUrl)
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        
        $writer = new \BaconQrCode\Writer($renderer);
        return $writer->writeString($qrCodeUrl);
    }

    /**
     * Decode a base32 string.
     */
    private static function base32Decode($secret)
    {
        if (empty($secret)) {
            return '';
        }

        $secret = strtoupper($secret);
        $allowedValues = array_flip(str_split(self::$base32chars));

        $buf = '';
        $val = 0;
        $vLen = 0;

        for ($i = 0; $i < strlen($secret); $i++) {
            $c = $secret[$i];
            if ($c == '=') {
                break;
            }
            if (!isset($allowedValues[$c])) {
                continue;
            }
            $val = ($val << 5) | $allowedValues[$c];
            $vLen += 5;
            if ($vLen >= 8) {
                $buf .= chr(($val >> ($vLen - 8)) & 0xFF);
                $vLen -= 8;
            }
        }
        return $buf;
    }
}
