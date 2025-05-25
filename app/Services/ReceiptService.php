<?php

namespace App\Services;

use App\Agents\ReceiptAgent;
use Illuminate\Http\UploadedFile;

class ReceiptService
{
    public function processReceipt(UploadedFile $file): array
    {
        $path = $file->store('receipts');
        $fullPath = storage_path("app/private/{$path}");

        $imagePath = $this->convertIfPdf($file, $fullPath);

        $ocrText = $this->runOcr($imagePath);
        
        if (!$this->isValidReceipt($ocrText)) {
            throw new \Exception('The uploaded file does not appear to be a valid receipt.');
        }

        $agent = new ReceiptAgent();
        $response = $agent->ask($ocrText);

        $parsedData = [$ocrText, json_decode($response, true) ?? $response];

        $this->deleteFiles([$fullPath, $imagePath]);

        return $parsedData;
    }

    public function convertIfPdf(UploadedFile $file, string $fullPath): string
    {
        if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
            return $fullPath;
        }

        $imagePath = preg_replace('/\.pdf$/i', '.png', $fullPath);

        $magick = env('IMAGEMAGICK_PATH', 'magick');
        $magick = "\"{$magick}\"";

        $cmd = "{$magick} -density 300 " . escapeshellarg($fullPath) . " -quality 100 " . escapeshellarg($imagePath) . " 2>&1";

        exec($cmd, $output, $returnVar);

        $outputClean = array_map(function ($line) {
            return mb_convert_encoding($line, 'UTF-8', 'UTF-8');
        }, $output);

        if ($returnVar !== 0 || !file_exists($imagePath)) {
            throw new \Exception("Error converting PDF to image. Command: {$cmd}. Output: " . implode("\n", $outputClean));
        }

        return $imagePath;
    }

    private function runOcr(string $imagePath): string
    {
        putenv('TESSDATA_PREFIX=' . env('TESSDATA_PREFIX'));

        $outputTxt = tempnam(sys_get_temp_dir(), 'ocr_');
        exec("tesseract \"{$imagePath}\" \"{$outputTxt}\" -l por+eng 2>&1", $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception("Error running Tesseract: " . implode("\n", $output));
        }

        $txtFile = "{$outputTxt}.txt";

        if (!file_exists($txtFile)) {
            throw new \Exception("OCR output file not found.");
        }

        $ocrText = file_get_contents($txtFile);

        unlink($txtFile);
        unlink($outputTxt);

        return $ocrText;
    }

    private function deleteFiles(array $files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    private function isValidReceipt(string $text): bool
    {
        $requiredPatterns = [
            // words related to date, transaction, etc.
            '/(data|transferência|ID da transação|tipo de transferência|Pix)/i',

            // words related to bank data and personal information
            '/(banco|instituição|agência|conta|cpf|cnpj|nome)/i',
        ];

        foreach ($requiredPatterns as $pattern) {
            if (!preg_match($pattern, $text)) {
                return false;
            }
        }

        return true;
    }
}
