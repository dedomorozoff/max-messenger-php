<?php

// Пример маршрутов для Laravel
// routes/web.php или routes/api.php

use App\Http\Controllers\MaxBotController;

// Webhook для получения обновлений от MAX Messenger
Route::post('/max-bot/webhook', [MaxBotController::class, 'webhook']);

// Маршруты для администратора (защищенные middleware)
Route::middleware(['auth', 'admin'])->group(function () {
    // Отправить сообщение всем пользователям
    Route::post('/max-bot/broadcast', [MaxBotController::class, 'broadcastMessage']);
    
    // Получить статистику бота
    Route::get('/max-bot/stats', [MaxBotController::class, 'getStats']);
});

// Альтернативный вариант с API маршрутами
Route::prefix('api/max-bot')->group(function () {
    Route::post('/webhook', [MaxBotController::class, 'webhook']);
    
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/broadcast', [MaxBotController::class, 'broadcastMessage']);
        Route::get('/stats', [MaxBotController::class, 'getStats']);
    });
});

// Пример middleware для проверки администратора
// app/Http/Middleware/AdminMiddleware.php
/*
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return $next($request);
    }
}
*/

// Пример команды Artisan для управления ботом
// app/Console/Commands/MaxBotCommand.php
/*
namespace App\Console\Commands;

use Illuminate\Console\Command;
use MaxMessenger\Facades\MaxMessenger;

class MaxBotCommand extends Command
{
    protected $signature = 'max-bot:send {message} {--chat-id=}';
    protected $description = 'Отправить сообщение через MAX Messenger бота';

    public function handle()
    {
        $message = $this->argument('message');
        $chatId = $this->option('chat-id');
        
        try {
            if ($chatId) {
                // Отправить в конкретный чат
                $result = MaxMessenger::messages()->sendText($chatId, $message);
                $this->info("Сообщение отправлено в чат {$chatId}");
            } else {
                // Отправить во все чаты
                $chats = MaxMessenger::chats()->getAll();
                $sentCount = 0;
                
                foreach ($chats['chats'] ?? [] as $chat) {
                    try {
                        MaxMessenger::messages()->sendText($chat['chat_id'], $message);
                        $sentCount++;
                    } catch (\Exception $e) {
                        $this->error("Ошибка отправки в чат {$chat['chat_id']}: " . $e->getMessage());
                    }
                }
                
                $this->info("Сообщение отправлено в {$sentCount} чатов");
            }
            
        } catch (\Exception $e) {
            $this->error("Ошибка: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
*/

// Пример планировщика задач
// app/Console/Kernel.php
/*
protected function schedule(Schedule $schedule)
{
    // Отправлять уведомления каждый день в 9:00
    $schedule->call(function () {
        $chats = MaxMessenger::chats()->getAll();
        $message = "🌅 Доброе утро! Не забудьте проверить ваши задачи на сегодня.";
        
        foreach ($chats['chats'] ?? [] as $chat) {
            try {
                MaxMessenger::messages()->sendText($chat['chat_id'], $message);
            } catch (\Exception $e) {
                Log::error("Ошибка отправки утреннего уведомления: " . $e->getMessage());
            }
        }
    })->dailyAt('09:00');
    
    // Отправлять еженедельный отчет по воскресеньям
    $schedule->call(function () {
        $chats = MaxMessenger::chats()->getAll();
        $message = "📊 Еженедельный отчет готов! Проверьте вашу статистику.";
        
        foreach ($chats['chats'] ?? [] as $chat) {
            try {
                MaxMessenger::messages()->sendText($chat['chat_id'], $message);
            } catch (\Exception $e) {
                Log::error("Ошибка отправки еженедельного отчета: " . $e->getMessage());
            }
        }
    })->weekly()->sundays()->at('18:00');
}
*/

// Пример валидации для webhook
// app/Http/Requests/MaxBotWebhookRequest.php
/*
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaxBotWebhookRequest extends FormRequest
{
    public function authorize()
    {
        // Здесь можно добавить дополнительную проверку
        // например, проверку IP адреса MAX Messenger
        return true;
    }

    public function rules()
    {
        return [
            'update_id' => 'required|integer',
            'message' => 'sometimes|array',
            'callback_query' => 'sometimes|array',
            'chat_member' => 'sometimes|array',
            'chat_join_request' => 'sometimes|array',
        ];
    }
}
*/

// Пример обработки ошибок
// app/Exceptions/Handler.php
/*
public function register()
{
    $this->reportable(function (MaxMessengerException $e) {
        // Логирование ошибок MAX Messenger
        Log::error('MAX Messenger Error: ' . $e->getMessage(), [
            'http_code' => $e->getHttpCode(),
            'api_error_code' => $e->getApiErrorCode(),
            'error_data' => $e->getErrorData(),
        ]);
    });
}
*/
