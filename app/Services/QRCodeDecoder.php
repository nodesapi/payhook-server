<?php

namespace App\Services;

use Exception;
use Zxing\QrReader;

/**
 * Service untuk decode QR Code image menjadi text/string.
 * Digunakan untuk extract QRIS string dari uploaded QR image.
 */
class QRCodeDecoder
{
    /**
     * Decode QR Code image dari file path atau URL.
     *
     * @param string $imagePath Path to image file
     * @return string|null Decoded QRIS string atau null jika gagal
     * @throws Exception
     */
    public function decode(string $imagePath): ?string
    {
        try {
            // Validate file exists
            if (!file_exists($imagePath)) {
                throw new Exception("Image file not found: {$imagePath}");
            }

            // Validate file is image
            $imageInfo = @getimagesize($imagePath);
            if ($imageInfo === false) {
                throw new Exception("Invalid image file");
            }

            // Decode QR code using QrReader
            $qrcode = new QrReader($imagePath);
            $text = $qrcode->text();

            if (empty($text)) {
                throw new Exception("No QR code found in image or QR code is unreadable");
            }

            return $text;
        } catch (Exception $e) {
            \Log::error('QR Code Decode Error: ' . $e->getMessage(), [
                'image_path' => $imagePath
            ]);
            throw $e;
        }
    }

    /**
     * Decode QR dari uploaded file (UploadedFile instance).
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|null
     * @throws Exception
     */
    public function decodeFromUpload($file): ?string
    {
        // Validate file is image
        if (!$file->isValid()) {
            throw new Exception("Invalid uploaded file");
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new Exception("Invalid image format. Allowed: JPG, PNG, WEBP");
        }

        // Get temporary path
        $tempPath = $file->getRealPath();

        return $this->decode($tempPath);
    }

    /**
     * Validate apakah string adalah QRIS valid.
     *
     * @param string $qrisString
     * @return bool
     */
    public function isValidQRIS(string $qrisString): bool
    {
        // QRIS harus dimulai dengan "000201" (EMV QR Code format)
        if (!str_starts_with($qrisString, '000201')) {
            return false;
        }

        // QRIS minimal 100 karakter
        if (strlen($qrisString) < 100) {
            return false;
        }

        // QRIS harus punya CRC field (6304)
        if (!str_contains($qrisString, '6304')) {
            return false;
        }

        return true;
    }

    /**
     * Extract merchant info dari QRIS string.
     *
     * @param string $qrisString
     * @return array
     */
    public function extractMerchantInfo(string $qrisString): array
    {
        $info = [
            'merchant_name' => null,
            'merchant_city' => null,
            'merchant_country' => 'ID',
            'currency' => '360', // IDR
        ];

        try {
            // Extract Merchant Name (field 59)
            if (preg_match('/59(\d{2})([^\d]{2,})/', $qrisString, $matches)) {
                $length = (int)$matches[1];
                $info['merchant_name'] = substr($matches[2], 0, $length);
            }

            // Extract Merchant City (field 60)
            if (preg_match('/60(\d{2})([^\d]{2,})/', $qrisString, $matches)) {
                $length = (int)$matches[1];
                $info['merchant_city'] = substr($matches[2], 0, $length);
            }

            // Extract Country Code (field 58)
            if (preg_match('/58(\d{2})(\w{2,})/', $qrisString, $matches)) {
                $length = (int)$matches[1];
                $info['merchant_country'] = substr($matches[2], 0, $length);
            }

            // Extract Currency (field 53)
            if (preg_match('/53(\d{2})(\d{2,})/', $qrisString, $matches)) {
                $length = (int)$matches[1];
                $info['currency'] = substr($matches[2], 0, $length);
            }
        } catch (Exception $e) {
            \Log::warning('Failed to extract merchant info from QRIS', [
                'error' => $e->getMessage()
            ]);
        }

        return $info;
    }

    /**
     * Detect QRIS type (Static vs Dynamic).
     *
     * @param string $qrisString
     * @return string 'static' or 'dynamic' or 'unknown'
     */
    public function detectQRISType(string $qrisString): string
    {
        // Static QRIS biasanya tidak ada field 54 (Transaction Amount)
        // Dynamic QRIS ada field 54

        if (preg_match('/54\d{2}\d+/', $qrisString)) {
            return 'dynamic'; // Ada amount embedded
        }

        // Check if it's merchant QR (field 26 or 51)
        if (preg_match('/26\d{2}|51\d{2}/', $qrisString)) {
            return 'static'; // Merchant static QR
        }

        return 'unknown';
    }
}
