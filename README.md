# MAX Messenger PHP Library

Библиотека для работы с API мессенджера MAX на PHP и Laravel.

## Установка

```bash
composer require max-messenger-php/max-messenger
```

## Конфигурация

### Laravel

Опубликуйте конфигурационный файл:

```bash
php artisan vendor:publish --provider="MaxMessenger\MaxMessengerServiceProvider"
```

Добавьте в `.env`:

```env
MAX_BOT_TOKEN=your_bot_token_here
MAX_API_BASE_URL=https://botapi.max.ru
```

### Обычный PHP

```php
use MaxMessenger\MaxMessenger;

$max = new MaxMessenger([
    'bot_token' => 'your_bot_token_here',
    'base_url' => 'https://botapi.max.ru'
]);
```

## Использование

### Получение информации о боте

```php
// Laravel
$botInfo = MaxMessenger::bot()->getInfo();

// Обычный PHP
$botInfo = $max->bot()->getInfo();
```

### Отправка сообщений

```php
// Простое текстовое сообщение
MaxMessenger::messages()->send([
    'chat_id' => $chatId,
    'text' => 'Привет!'
]);

// Сообщение с форматированием Markdown
MaxMessenger::messages()->send([
    'chat_id' => $chatId,
    'text' => '**Жирный текст** и *курсив*',
    'format' => 'markdown'
]);

// Сообщение с inline клавиатурой
MaxMessenger::messages()->send([
    'chat_id' => $chatId,
    'text' => 'Выберите действие:',
    'attachments' => [
        [
            'type' => 'inline_keyboard',
            'payload' => [
                'buttons' => [
                    [
                        [
                            'type' => 'callback',
                            'text' => 'Кнопка 1',
                            'payload' => 'button1'
                        ],
                        [
                            'type' => 'callback',
                            'text' => 'Кнопка 2',
                            'payload' => 'button2'
                        ]
                    ]
                ]
            ]
        ]
    ]
]);
```

### Работа с чатами

```php
// Получить список всех чатов
$chats = MaxMessenger::chats()->getAll();

// Получить информацию о конкретном чате
$chatInfo = MaxMessenger::chats()->getInfo($chatId);

// Изменить информацию о чате
MaxMessenger::chats()->update($chatId, [
    'title' => 'Новое название чата'
]);

// Удалить чат
MaxMessenger::chats()->delete($chatId);
```

### Работа с участниками

```php
// Получить список участников чата
$members = MaxMessenger::chats()->getMembers($chatId);

// Добавить участников в чат
MaxMessenger::chats()->addMembers($chatId, [$userId1, $userId2]);

// Удалить участника из чата
MaxMessenger::chats()->removeMember($chatId, $userId);
```

### Работа с администраторами

```php
// Получить список администраторов
$admins = MaxMessenger::chats()->getAdmins($chatId);

// Назначить администратора
MaxMessenger::chats()->addAdmin($chatId, $userId);

// Отменить права администратора
MaxMessenger::chats()->removeAdmin($chatId, $userId);
```

### Загрузка файлов

```php
// Получить URL для загрузки файла
$uploadUrl = MaxMessenger::upload()->getUrl();

// Отправить сообщение с файлом
MaxMessenger::messages()->send([
    'chat_id' => $chatId,
    'text' => 'Файл прикреплен',
    'attachments' => [
        [
            'type' => 'file',
            'payload' => [
                'file_id' => $fileId
            ]
        ]
    ]
]);
```

### Webhook и Long Polling

```php
// Получить обновления через Long Polling
$updates = MaxMessenger::subscriptions()->getUpdates();

// Подписаться на обновления
MaxMessenger::subscriptions()->subscribe($webhookUrl);

// Отписаться от обновлений
MaxMessenger::subscriptions()->unsubscribe();
```

## Обработка callback'ов

```php
// Обработка callback от кнопок
MaxMessenger::messages()->answerCallback($callbackId, 'Ответ на callback');
```

## Форматирование текста

### Markdown

```php
$text = "**Жирный текст**\n*Курсив*\n`код`\n~~зачеркнутый~~\n++подчеркнутый++";
```

### HTML

```php
$text = "<strong>Жирный текст</strong><br><em>Курсив</em><br><code>код</code>";
```

## Типы кнопок

- `callback` - отправляет callback событие
- `link` - открывает ссылку
- `request_contact` - запрашивает контакт
- `request_geo_location` - запрашивает местоположение
- `open_app` - открывает мини-приложение
- `message` - отправляет текстовое сообщение

## Обработка ошибок

```php
try {
    $result = MaxMessenger::messages()->send([
        'chat_id' => $chatId,
        'text' => 'Тестовое сообщение'
    ]);
} catch (MaxMessenger\Exceptions\MaxMessengerException $e) {
    // Обработка ошибок API
    Log::error('MAX API Error: ' . $e->getMessage());
}
```

## Тестирование

```bash
composer test
```

## Лицензия

MIT License

