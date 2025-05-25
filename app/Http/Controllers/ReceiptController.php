<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReceiptController extends Controller
{
    public function uploadForm()
    {
        return view('receipt.form');
    }

    public function process(Request $request, ReceiptService $service): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,pdf',
        ]);
        try {
            [$extractedText, $parsedData] = $service->processReceipt($request->file('file'));

            return response()->json([
                'extracted_text' => $extractedText,
                'parsed_data' => $parsedData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
