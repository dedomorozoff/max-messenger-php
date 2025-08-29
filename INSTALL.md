# Инструкция по установке и настройке

## Требования

- PHP 8.0 или выше
- Composer
- Laravel 8+ (для интеграции с Laravel)

## Установка

### 1. Установка через Composer

```bash
composer require max-messenger-php/max-messenger
```

### 2. Получение токена бота

1. Найдите @BotFather в MAX Messenger
2. Отправьте команду `/newbot`
3. Следуйте инструкциям для создания бота
4. Сохраните полученный токен

## Настройка

### Для обычного PHP

```php
<?php

require_once 'vendor/autoload.php';

use MaxMessenger\MaxMessenger;

$bot = new MaxMessenger([
    'bot_token' => 'YOUR_BOT_TOKEN_HERE',
    'base_url' => 'https://botapi.max.ru',
    'timeout' => 30,
    'verify' => true,
]);
```

### Для Laravel

#### 1. Публикация конфигурации

```bash
php artisan vendor:publish --provider="MaxMessenger\MaxMessengerServiceProvider"
```

#### 2. Настройка переменных окружения

Добавьте в файл `.env`:

```env
MAX_BOT_TOKEN=your_bot_token_here
MAX_API_BASE_URL=https://botapi.max.ru
MAX_API_TIMEOUT=30
MAX_API_VERIFY_SSL=true
MAX_WEBHOOK_ENABLED=false
MAX_WEBHOOK_URL=https://yourdomain.com/max-bot/webhook
MAX_WEBHOOK_SECRET_TOKEN=your_secret_token
```

#### 3. Настройка конфигурации

Файл `config/max-messenger.php` будет создан автоматически. При необходимости отредактируйте его:

```php
<?php

return [
    'bot_token' => env('MAX_BOT_TOKEN'),
    'base_url' => env('MAX_API_BASE_URL', 'https://botapi.max.ru'),
    'timeout' => env('MAX_API_TIMEOUT', 30),
    'verify' => env('MAX_API_VERIFY_SSL', true),
    
    'webhook' => [
        'enabled' => env('MAX_WEBHOOK_ENABLED', false),
        'url' => env('MAX_WEBHOOK_URL'),
        'secret_token' => env('MAX_WEBHOOK_SECRET_TOKEN'),
    ],
    
    'rate_limit' => [
        'enabled' => env('MAX_RATE_LIMIT_ENABLED', true),
        'max_requests_per_minute' => env('MAX_RATE_LIMIT_PER_MINUTE', 30),
        'max_requests_per_hour' => env('MAX_RATE_LIMIT_PER_HOUR', 1000),
    ],
    
    'logging' => [
        'enabled' => env('MAX_LOGGING_ENABLED', true),
        'channel' => env('MAX_LOGGING_CHANNEL', 'daily'),
        'level' => env('MAX_LOGGING_LEVEL', 'info'),
    ],
    
    'cache' => [
        'enabled' => env('MAX_CACHE_ENABLED', true),
        'ttl' => env('MAX_CACHE_TTL', 300),
        'prefix' => env('MAX_CACHE_PREFIX', 'max_messenger'),
    ],
];
```

## Настройка Webhook

### 1. Создание маршрута

Добавьте в `routes/web.php` или `routes/api.php`:

```php
use App\Http\Controllers\MaxBotController;

Route::post('/max-bot/webhook', [MaxBotController::class, 'webhook']);
```

### 2. Создание контроллера

Создайте контроллер `app/Http/Controllers/MaxBotController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MaxMessenger\Facades\MaxMessenger;

class MaxBotController extends Controller
{
    public function webhook(Request $request)
    {
        // Обработка webhook'а
        $updateData = $request->all();
        
        // Ваша логика обработки обновлений
        
        return response()->json(['status' => 'ok']);
    }
}
```

### 3. Регистрация webhook'а

```php
// Подписаться на webhook
MaxMessenger::subscriptions()->subscribe('https://yourdomain.com/max-bot/webhook');

// Отписаться от webhook
MaxMessenger::subscriptions()->unsubscribe();
```

## Настройка SSL (для разработки)

Если у вас проблемы с SSL сертификатами в среде разработки:

```php
$bot = new MaxMessenger([
    'bot_token' => 'YOUR_BOT_TOKEN',
    'verify' => false, // Отключить проверку SSL
]);
```

**Внимание:** Не отключайте проверку SSL в продакшене!

## Настройка логирования

### Для Laravel

```php
// В config/logging.php добавьте канал для MAX Messenger
'channels' => [
    'max_messenger' => [
        'driver' => 'daily',
        'path' => storage_path('logs/max-messenger.log'),
        'level' => env('MAX_LOGGING_LEVEL', 'info'),
        'days' => 14,
    ],
],
```

### Для обычного PHP

```php
// Настройка логирования
if (config('max-messenger.logging.enabled')) {
    $logFile = 'logs/max-messenger.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Логирование ошибок
    set_error_handler(function($severity, $message, $file, $line) use ($logFile) {
        $logMessage = date('Y-m-d H:i:s') . " [{$severity}] {$message} in {$file}:{$line}\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    });
}
```

## Настройка кэширования

### Для Laravel

```php
// В config/cache.php добавьте store для MAX Messenger
'stores' => [
    'max_messenger' => [
        'driver' => 'redis',
        'connection' => 'default',
        'prefix' => config('max-messenger.cache.prefix', 'max_messenger'),
    ],
],
```

### Для обычного PHP

```php
// Простая реализация кэширования в файлах
class MaxMessengerCache
{
    private string $cacheDir;
    private int $ttl;
    
    public function __construct(string $cacheDir = 'cache', int $ttl = 300)
    {
        $this->cacheDir = $cacheDir;
        $this->ttl = $ttl;
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get(string $key)
    {
        $filename = $this->cacheDir . '/' . md5($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data['expires'] < time()) {
            unlink($filename);
            return null;
        }
        
        return $data['value'];
    }
    
    public function set(string $key, $value, int $ttl = null): bool
    {
        $filename = $this->cacheDir . '/' . md5($key);
        $ttl = $ttl ?? $this->ttl;
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        return file_put_contents($filename, serialize($data)) !== false;
    }
    
    public function delete(string $key): bool
    {
        $filename = $this->cacheDir . '/' . md5($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
}
```

## Настройка ограничения частоты запросов

### Для Laravel

```php
// В app/Http/Kernel.php добавьте middleware
protected $routeMiddleware = [
    'max.rate.limit' => \App\Http\Middleware\MaxRateLimitMiddleware::class,
];

// Создайте middleware app/Http/Middleware/MaxRateLimitMiddleware.php
class MaxRateLimitMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'max_messenger_rate_limit_' . $request->ip();
        $maxRequests = config('max-messenger.rate_limit.max_requests_per_minute', 30);
        
        if (Cache::has($key)) {
            $requests = Cache::get($key);
            if ($requests >= $maxRequests) {
                return response()->json(['error' => 'Rate limit exceeded'], 429);
            }
            Cache::increment($key);
        } else {
            Cache::put($key, 1, 60);
        }
        
        return $next($request);
    }
}
```

### Для обычного PHP

```php
class MaxRateLimiter
{
    private string $cacheDir;
    private int $maxRequests;
    private int $window;
    
    public function __construct(string $cacheDir = 'cache', int $maxRequests = 30, int $window = 60)
    {
        $this->cacheDir = $cacheDir;
        $this->maxRequests = $maxRequests;
        $this->window = $window;
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function check(string $identifier): bool
    {
        $filename = $this->cacheDir . '/rate_limit_' . md5($identifier);
        
        if (!file_exists($filename)) {
            $this->reset($filename);
            return true;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data['window_start'] + $this->window < time()) {
            $this->reset($filename);
            return true;
        }
        
        if ($data['requests'] >= $this->maxRequests) {
            return false;
        }
        
        $data['requests']++;
        file_put_contents($filename, serialize($data));
        
        return true;
    }
    
    private function reset(string $filename): void
    {
        $data = [
            'requests' => 1,
            'window_start' => time()
        ];
        
        file_put_contents($filename, serialize($data));
    }
}
```

## Проверка установки

### 1. Тест подключения

```php
try {
    $botInfo = MaxMessenger::bot()->getInfo();
    echo "Бот подключен успешно!\n";
    echo "ID: " . $botInfo['user_id'] . "\n";
    echo "Имя: " . $botInfo['name'] . "\n";
} catch (Exception $e) {
    echo "Ошибка подключения: " . $e->getMessage() . "\n";
}
```

### 2. Тест отправки сообщения

```php
try {
    $result = MaxMessenger::messages()->sendText(
        'CHAT_ID_HERE',
        'Тестовое сообщение'
    );
    echo "Сообщение отправлено! ID: " . $result['message_id'] . "\n";
} catch (Exception $e) {
    echo "Ошибка отправки: " . $e->getMessage() . "\n";
}
```

## Устранение неполадок

### Ошибка "Bot token is required"

- Проверьте, что токен указан в конфигурации
- Убедитесь, что переменная окружения `MAX_BOT_TOKEN` установлена

### Ошибка SSL

- В среде разработки: установите `verify => false`
- В продакшене: проверьте SSL сертификат

### Ошибка "Rate limit exceeded"

- Увеличьте лимиты в конфигурации
- Добавьте задержки между запросами
- Используйте кэширование

### Webhook не работает

- Проверьте доступность URL из интернета
- Убедитесь, что SSL сертификат валиден
- Проверьте логи сервера

## Дополнительные настройки

### Настройка прокси

```php
$bot = new MaxMessenger([
    'bot_token' => 'YOUR_BOT_TOKEN',
    'proxy' => [
        'http' => 'http://proxy.example.com:8080',
        'https' => 'http://proxy.example.com:8080'
    ]
]);
```

### Настройка пользовательских заголовков

```php
$bot = new MaxMessenger([
    'bot_token' => 'YOUR_BOT_TOKEN',
    'headers' => [
        'User-Agent' => 'MyBot/1.0',
        'X-Custom-Header' => 'CustomValue'
    ]
]);
```

### Настройка таймаутов

```php
$bot = new MaxMessenger([
    'bot_token' => 'YOUR_BOT_TOKEN',
    'timeout' => 60, // 60 секунд
    'connect_timeout' => 10, // 10 секунд на подключение
]);
```

## Безопасность

### 1. Защита webhook'а

```php
// Проверка IP адреса
$allowedIPs = ['IP_1', 'IP_2']; // IP адреса MAX Messenger
$clientIP = $request->ip();

if (!in_array($clientIP, $allowedIPs)) {
    return response()->json(['error' => 'Unauthorized'], 403);
}
```

### 2. Проверка токена

```php
// Проверка секретного токена
$secretToken = $request->header('X-Max-Signature');
$expectedToken = config('max-messenger.webhook.secret_token');

if ($secretToken !== $expectedToken) {
    return response()->json(['error' => 'Invalid signature'], 403);
}
```

### 3. Валидация данных

```php
// Валидация входящих данных
$request->validate([
    'update_id' => 'required|integer',
    'message' => 'sometimes|array',
    'callback_query' => 'sometimes|array',
]);
```

## Мониторинг и логирование

### 1. Логирование всех запросов

```php
// В сервисах добавьте логирование
Log::info('MAX API Request', [
    'endpoint' => $endpoint,
    'method' => $method,
    'params' => $params,
    'response' => $response
]);
```

### 2. Мониторинг ошибок

```php
// Отслеживание ошибок API
try {
    $result = $this->makeRequest($endpoint, $method, $params);
} catch (MaxMessengerException $e) {
    Log::error('MAX API Error', [
        'endpoint' => $endpoint,
        'error' => $e->getMessage(),
        'http_code' => $e->getHttpCode(),
        'api_error_code' => $e->getApiErrorCode()
    ]);
    
    // Уведомление администратора
    $this->notifyAdmin($e);
    
    throw $e;
}
```

### 3. Метрики производительности

```php
// Измерение времени выполнения запросов
$startTime = microtime(true);

try {
    $result = $this->makeRequest($endpoint, $method, $params);
    
    $executionTime = microtime(true) - $startTime;
    
    // Логирование метрик
    Log::info('MAX API Performance', [
        'endpoint' => $endpoint,
        'execution_time' => $executionTime,
        'success' => true
    ]);
    
    return $result;
} catch (Exception $e) {
    $executionTime = microtime(true) - $startTime;
    
    Log::error('MAX API Performance', [
        'endpoint' => $endpoint,
        'execution_time' => $executionTime,
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
    throw $e;
}
```
