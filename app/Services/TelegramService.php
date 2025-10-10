<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function sendMessage($message)
    {
        try {
            $botToken = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');

            // Send message with Markdown formatting and no link preview
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown', // enables bold text & clickable links
                'disable_web_page_preview' => true,
            ]);

            // Log response for debugging
            Log::info('Telegram API response:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram send error: ' . $e->getMessage());
            return false;
        }
    }
}
