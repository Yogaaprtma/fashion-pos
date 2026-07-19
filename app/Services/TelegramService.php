<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public static function sendMessage(string $message): bool
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            return false;
        }

        // Support multiple chat IDs separated by commas
        $chatIds = explode(',', $chatId);
        $success = true;

        foreach ($chatIds as $id) {
            $id = trim($id);
            if (empty($id)) continue;

            try {
                $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $id,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);

                if (!$response->successful()) {
                    $success = false;
                }
            } catch (\Exception $e) {
                Log::error("Telegram notification failed for chat {$id}: " . $e->getMessage());
                $success = false;
            }
        }

        return $success;
    }
}
