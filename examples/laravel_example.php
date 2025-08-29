<?php

// Пример использования в Laravel контроллере
// app/Http/Controllers/MaxBotController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use MaxMessenger\Facades\MaxMessenger;
use MaxMessenger\Objects\Update;
use MaxMessenger\Exceptions\MaxMessengerException;
use Illuminate\Support\Facades\Log;

class MaxBotController extends Controller
{
    /**
     * Webhook для получения обновлений от MAX Messenger
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            // Получаем данные обновления
            $updateData = $request->all();
            
            // Создаем объект обновления
            $update = new Update($updateData);
            
            // Обрабатываем обновление
            $this->handleUpdate($update);
            
            return response()->json(['status' => 'ok']);
            
        } catch (\Exception $e) {
            Log::error('MAX Bot Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Обработка обновлений
     */
    private function handleUpdate(Update $update): void
    {
        if ($update->isMessage()) {
            $this->handleMessage($update->getMessage());
        } elseif ($update->isCallbackQuery()) {
            $this->handleCallbackQuery($update);
        }
    }

    /**
     * Обработка сообщений
     */
    private function handleMessage($message): void
    {
        $text = $message->getText();
        $chatId = $message->getChat()->getId();
        $userId = $message->getFrom()->getId();
        
        Log::info("Новое сообщение от пользователя {$userId} в чате {$chatId}: {$text}");
        
        // Обработка команд
        switch ($text) {
            case '/start':
                $this->sendWelcomeMessage($chatId);
                break;
                
            case '/help':
                $this->sendHelpMessage($chatId);
                break;
                
            case '/menu':
                $this->sendMainMenu($chatId);
                break;
                
            default:
                $this->sendEchoMessage($chatId, $text);
                break;
        }
    }

    /**
     * Обработка callback запросов
     */
    private function handleCallbackQuery(Update $update): void
    {
        $callbackData = $update->getCallbackData();
        $callbackId = $update->getCallbackId();
        $chatId = $update->getCallbackMessage()->getChat()->getId();
        
        Log::info("Callback: {$callbackData} от пользователя в чате {$chatId}");
        
        // Обработка различных действий
        switch ($callbackData) {
            case 'action1':
                $this->handleAction1($chatId);
                break;
                
            case 'action2':
                $this->handleAction2($chatId);
                break;
                
            case 'settings':
                $this->showSettings($chatId);
                break;
                
            default:
                // Ответ на callback
                MaxMessenger::messages()->answerCallback($callbackId, "Действие: {$callbackData}");
                break;
        }
    }

    /**
     * Отправка приветственного сообщения
     */
    private function sendWelcomeMessage(string $chatId): void
    {
        $message = "Привет! Я бот на MAX Messenger! 🚀\n\n";
        $message .= "Доступные команды:\n";
        $message .= "/start - Начать работу\n";
        $message .= "/help - Помощь\n";
        $message .= "/menu - Главное меню";
        
        MaxMessenger::messages()->sendText($chatId, $message);
    }

    /**
     * Отправка сообщения с помощью
     */
    private function sendHelpMessage(string $chatId): void
    {
        $message = "📚 Помощь по использованию бота:\n\n";
        $message .= "• Отправьте сообщение, и я отвечу на него\n";
        $message .= "• Используйте кнопки для навигации\n";
        $message .= "• Команда /menu открывает главное меню";
        
        MaxMessenger::messages()->sendText($chatId, $message);
    }

    /**
     * Отправка главного меню
     */
    private function sendMainMenu(string $chatId): void
    {
        $buttons = [
            [
                MaxMessenger::messages()->createCallbackButton('⚙️ Настройки', 'settings'),
                MaxMessenger::messages()->createCallbackButton('ℹ️ Информация', 'info')
            ],
            [
                MaxMessenger::messages()->createCallbackButton('🔗 Сайт', 'website'),
                MaxMessenger::messages()->createCallbackButton('📞 Поддержка', 'support')
            ]
        ];
        
        MaxMessenger::messages()->sendWithKeyboard(
            $chatId,
            "🎯 Главное меню\nВыберите действие:",
            $buttons
        );
    }

    /**
     * Отправка эхо-сообщения
     */
    private function sendEchoMessage(string $chatId, string $text): void
    {
        $response = "📝 Вы написали: {$text}";
        MaxMessenger::messages()->sendText($chatId, $response);
    }

    /**
     * Обработка действия 1
     */
    private function handleAction1(string $chatId): void
    {
        $message = "✅ Действие 1 выполнено!\n\n";
        $message .= "Это пример обработки callback кнопки.";
        
        MaxMessenger::messages()->sendText($chatId, $message);
    }

    /**
     * Обработка действия 2
     */
    private function handleAction2(string $chatId): void
    {
        $message = "✅ Действие 2 выполнено!\n\n";
        $message .= "Вы можете добавить любую логику сюда.";
        
        MaxMessenger::messages()->sendText($chatId, $message);
    }

    /**
     * Показать настройки
     */
    private function showSettings(string $chatId): void
    {
        $buttons = [
            [
                MaxMessenger::messages()->createCallbackButton('🔔 Уведомления', 'notifications'),
                MaxMessenger::messages()->createCallbackButton('🌍 Язык', 'language')
            ],
            [
                MaxMessenger::messages()->createCallbackButton('🔙 Назад', 'back_to_menu')
            ]
        ];
        
        MaxMessenger::messages()->sendWithKeyboard(
            $chatId,
            "⚙️ Настройки\nВыберите раздел:",
            $buttons
        );
    }

    /**
     * Отправить сообщение всем пользователям (для администратора)
     */
    public function broadcastMessage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:4096',
                'admin_token' => 'required|string'
            ]);
            
            // Проверка токена администратора
            if ($request->admin_token !== config('max-messenger.admin_token')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $message = $request->message;
            
            // Получаем список всех чатов
            $chats = MaxMessenger::chats()->getAll();
            
            $sentCount = 0;
            $errors = [];
            
            foreach ($chats['chats'] ?? [] as $chat) {
                try {
                    MaxMessenger::messages()->sendText($chat['chat_id'], $message);
                    $sentCount++;
                } catch (MaxMessengerException $e) {
                    $errors[] = "Чат {$chat['chat_id']}: " . $e->getMessage();
                }
            }
            
            return response()->json([
                'status' => 'success',
                'sent_count' => $sentCount,
                'total_chats' => count($chats['chats'] ?? []),
                'errors' => $errors
            ]);
            
        } catch (MaxMessengerException $e) {
            Log::error('MAX Bot Broadcast Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Получить статистику бота
     */
    public function getStats(): JsonResponse
    {
        try {
            $botInfo = MaxMessenger::bot()->getInfo();
            $chats = MaxMessenger::chats()->getAll();
            $updates = MaxMessenger::subscriptions()->getRecentUpdates(100);
            
            $stats = [
                'bot' => [
                    'id' => $botInfo['user_id'],
                    'name' => $botInfo['name'],
                    'username' => $botInfo['username'],
                    'last_activity' => $botInfo['last_activity_time']
                ],
                'chats' => [
                    'total' => count($chats['chats'] ?? []),
                    'types' => $this->getChatTypesStats($chats['chats'] ?? [])
                ],
                'updates' => [
                    'recent' => count($updates['updates'] ?? []),
                    'last_update_id' => $updates['updates'][0]['update_id'] ?? null
                ]
            ];
            
            return response()->json($stats);
            
        } catch (MaxMessengerException $e) {
            Log::error('MAX Bot Stats Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Получить статистику по типам чатов
     */
    private function getChatTypesStats(array $chats): array
    {
        $stats = [];
        
        foreach ($chats as $chat) {
            $type = $chat['type'] ?? 'unknown';
            $stats[$type] = ($stats[$type] ?? 0) + 1;
        }
        
        return $stats;
    }
}
