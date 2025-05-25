<?php

namespace App\Agents;

use Illuminate\Support\Facades\Http;

class ReceiptAgent
{
    protected string $ollamaUrl;
    protected string $ollamaModel;

    public function __construct()
    {
        $this->ollamaUrl = config('services.ollama.url');
        $this->ollamaModel = config('services.ollama.model');
    }

    public function systemPrompt(): string
    {
        return <<<PROMPT
        You are an assistant specialized in reading bank receipts.

        Extract the following information from the text below and respond ONLY with the JSON, without any additional text, using the following format:

        {
        "payer": {
            "name": "",
            "institution": "",
            "agency": "",
            "account": "",
            "document": ""
        },
        "payee": {
            "name": "",
            "institution": "",
            "agency": "",
            "account": "",
            "document": ""
        },
        "amount": "",
        "date": ""
        }

        Rules:
        1. The "payer" is the one found in sections like "Origin", "Sender", "From", or "Payer".
        2. The "payee" is the one found in sections like "Destination", "To", "Recipient", or "Payee".
        3. The "document" field must capture **only CPF or CNPJ numbers**, depending on which one is present in the text. 
        - CPF has 11 digits, formatted as `000.000.000-00` or unformatted as `00000000000`.
        - CNPJ has 14 digits, formatted as `00.000.000/0000-00` or unformatted as `00000000000000`.
        - Do NOT capture transaction IDs, authentication codes, hashes, or any other number that is not a CPF or CNPJ.
        4. If the CPF or CNPJ is not present in the text, leave the "document" field as an empty string "".
        5. If any other field does not exist in the text, leave it as an empty string "".
        6. Correctly identify the amount and date in their exact formats (amount as "1000.00" and date as "YYYY-MM-DD HH:MM:SS").
        7. Respond ONLY with the JSON, with no comments or any extra text.

        Now analyze the following text:
        PROMPT;

    }

    public function ask(string $input): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $this->systemPrompt() . "\n" . $input,
            ],
        ];

        $response = Http::timeout(600)->post("{$this->ollamaUrl}/api/chat", [
            'model' => $this->ollamaModel,
            'messages' => $messages,
            'stream' => false,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to connect to Ollama: ' . $response->body());
        }

        $data = $response->json();

        return $data['message']['content'] ?? '';
    }
}
